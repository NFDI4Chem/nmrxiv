import OCL from "openchemlib";

/**
 * Make every implicit hydrogen explicit and lay out the full structure in 2D
 * space using OCL's CoordinateInventor so the editor's auto-fit can scale the
 * whole structure (heavy atoms + H's) to the available canvas width.
 *
 * @param {import("openchemlib").Molecule} molecule
 */
function expandHydrogens(molecule) {
    if (!molecule) {
        return;
    }
    molecule.addImplicitHydrogens();
    molecule.inventCoordinates({ keepHydrogens: true });
}

/**
 * Strip every explicit hydrogen from the molecule by round-tripping through
 * canonical SMILES (which omits explicit H by default), then regenerate 2D
 * coordinates for the heavy-atom skeleton.
 *
 * @param {import("openchemlib").Molecule} molecule
 * @returns {import("openchemlib").Molecule}
 */
function collapseHydrogens(molecule) {
    if (!molecule) {
        return molecule;
    }
    return OCL.Molecule.fromSmiles(molecule.toIsomericSmiles());
}

/**
 * Flag every stereo center whose configuration is not specified as
 * "configuration unknown" (parity `cAtomParityUnknown`). The editor renders
 * those atoms with a wavy stereo indicator. Stereo centers with an explicit
 * R/S parity, or that the user has explicitly declared unknown, are left
 * untouched.
 *
 * @param {import("openchemlib").Molecule} molecule
 */
function markUnassignedStereo(molecule) {
    if (!molecule) {
        return;
    }
    molecule.ensureHelperArrays(OCL.Molecule.cHelperParities);
    const total = molecule.getAllAtoms();
    for (let i = 0; i < total; i++) {
        if (
            molecule.isAtomStereoCenter(i) &&
            molecule.getAtomParity(i) === OCL.Molecule.cAtomParityNone
        ) {
            molecule.setAtomConfigurationUnknown(i, true);
        }
    }
}

/**
 * Assign a small superscript number (1..N) as a custom atom label on every
 * atom. Prefixing the label with `]` is OCL's marker for "render as
 * superscript", which makes the label appear as small text next to the
 * element symbol for heteroatoms and as a small standalone number for
 * carbons (which have no visible element label otherwise).
 *
 * @param {import("openchemlib").Molecule} molecule
 */
function applyAtomNumbering(molecule) {
    if (!molecule) {
        return;
    }
    const total = molecule.getAllAtoms();
    for (let i = 0; i < total; i++) {
        molecule.setAtomCustomLabel(i, `]${i + 1}`);
    }
}

/**
 * Build a small toggle overlay that sits inside the host element, anchored to
 * the top-right corner ABOVE the canvas. Returns the canvas container
 * element where the CanvasEditor should be mounted; that container fills the
 * entire host so the editor keeps its full intended dimensions.
 *
 * @param {HTMLElement} host
 * @param {{ expandHydrogens: boolean, showAtomNumbers: boolean }} flags
 * @param {{ onExpandHydrogensChange: (value: boolean) => void, onShowAtomNumbersChange: (value: boolean) => void }} handlers
 * @returns {HTMLElement}
 */
function buildToggleUI(host, flags, handlers) {
    host.innerHTML = "";
    if (!host.style.position || host.style.position === "static") {
        host.style.position = "relative";
    }
    // Clip the absolutely-positioned canvas to the host's border-radius so
    // wrappers with `rounded-*` classes still have rounded corners.
    host.style.overflow = "hidden";

    const canvasContainer = document.createElement("div");
    canvasContainer.style.cssText =
        "position:absolute;inset:0;width:100%;height:100%;";
    host.appendChild(canvasContainer);

    const bar = document.createElement("div");
    bar.style.cssText = [
        "position:absolute",
        "top:6px",
        "right:6px",
        "z-index:10",
        "display:flex",
        "gap:10px",
        "align-items:center",
        "padding:4px 8px",
        "border:1px solid #e5e7eb",
        "border-radius:6px",
        "background:rgba(255,255,255,0.95)",
        "box-shadow:0 1px 2px rgba(0,0,0,0.05)",
        "font-size:11px",
        "line-height:1",
        "color:#374151",
    ].join(";");

    const makeToggle = (labelText, checked, onChange) => {
        const label = document.createElement("label");
        label.style.cssText =
            "display:inline-flex;align-items:center;gap:4px;cursor:pointer;user-select:none;white-space:nowrap;";

        const input = document.createElement("input");
        input.type = "checkbox";
        input.checked = checked;
        input.style.cssText = "cursor:pointer;margin:0;";
        input.addEventListener("change", () => onChange(input.checked));

        const span = document.createElement("span");
        span.textContent = labelText;

        label.appendChild(input);
        label.appendChild(span);
        return label;
    };

    bar.appendChild(
        makeToggle(
            "Expand H",
            flags.expandHydrogens,
            handlers.onExpandHydrogensChange
        )
    );
    bar.appendChild(
        makeToggle(
            "Atom numbers",
            flags.showAtomNumbers,
            handlers.onShowAtomNumbersChange
        )
    );

    host.appendChild(bar);

    return canvasContainer;
}

/**
 * Thin compatibility adapter that exposes the old `StructureEditor.createSVGEditor()`
 * surface (`setSmiles`, `getSmiles`, `setMolFile`, `getMolFile`, `getMolecule`)
 * on top of openchemlib v9's canvas-based `CanvasEditor`.
 *
 * The legacy SVG editor was removed in openchemlib v9.0.0. This wrapper lets
 * existing call sites continue to work with minimal changes while we migrate
 * to the new canvas/Molecule-object API.
 *
 * Two opt-in toggles are rendered above the canvas:
 *   - "Expand H": expands all implicit hydrogens into explicit H atoms and
 *     re-lays out the structure so the editor's auto-fit can scale it to the
 *     available width.
 *   - "Atom numbers": labels every atom with `<element><index>` (e.g. `C1`).
 *
 * Stereo centers with no assigned configuration are always flagged so the
 * editor renders the wavy "unknown" stereo indicator.
 *
 * @param {string|HTMLElement} target - DOM element or its id attribute.
 * @param {import("openchemlib").CanvasEditorOptions & { expandHydrogens?: boolean, showAtomNumbers?: boolean }} [options]
 * @returns {{
 *   instance: import("openchemlib").CanvasEditor,
 *   setSmiles: (smiles: string) => void,
 *   getSmiles: () => string,
 *   setMolFile: (molfile: string) => void,
 *   getMolFile: () => string,
 *   getMolecule: () => import("openchemlib").Molecule,
 *   setMolecule: (molecule: import("openchemlib").Molecule) => void,
 *   setExpandHydrogens: (value: boolean) => void,
 *   setShowAtomNumbers: (value: boolean) => void,
 *   onChange: (callback: import("openchemlib").OnChangeListenerCallback) => void,
 *   destroy: () => void,
 * }}
 */
export function createStructureEditor(target, options = {}) {
    const host =
        typeof target === "string" ? document.getElementById(target) : target;

    if (!host) {
        throw new Error(
            typeof target === "string"
                ? `createStructureEditor: element with id "${target}" not found`
                : "createStructureEditor: element not provided"
        );
    }

    const {
        expandHydrogens: initialExpandH = false,
        showAtomNumbers: initialAtomNumbers = false,
        ...editorOptions
    } = options;

    const flags = {
        expandHydrogens: initialExpandH,
        showAtomNumbers: initialAtomNumbers,
    };

    const canvasContainer = buildToggleUI(host, flags, {
        onExpandHydrogensChange: (value) => {
            flags.expandHydrogens = value;
            rebuildFromCurrent();
        },
        onShowAtomNumbersChange: (value) => {
            flags.showAtomNumbers = value;
            rebuildFromCurrent();
        },
    });

    const editor = new OCL.CanvasEditor(canvasContainer, editorOptions);

    const emptyMolecule = () => new OCL.Molecule(32, 32);

    let userOnChange = null;
    let suppressOnChange = false;

    const decorate = (molecule) => {
        if (!molecule) {
            return molecule;
        }
        molecule.removeAtomCustomLabels();
        if (flags.expandHydrogens) {
            expandHydrogens(molecule);
        }
        markUnassignedStereo(molecule);
        if (flags.showAtomNumbers) {
            applyAtomNumbering(molecule);
        }
        return molecule;
    };

    const setDecorated = (molecule) => {
        suppressOnChange = true;
        editor.setMolecule(decorate(molecule));
        suppressOnChange = false;
    };

    /**
     * Re-render the editor's current molecule with the latest flag values.
     * Used when the user flips a toggle.
     *
     * Whenever explicit hydrogens need to be removed we round-trip the
     * molecule through canonical SMILES so the result is a fresh `Molecule`
     * instance — this guarantees `setMolecule()` triggers a redraw even
     * though the editor's internal molecule reference is the same one we'd
     * otherwise be mutating in place.
     */
    const rebuildFromCurrent = () => {
        const current = editor.getMolecule();
        if (!current) {
            return;
        }
        const base = flags.expandHydrogens
            ? current
            : collapseHydrogens(current);
        decorate(base);
        suppressOnChange = true;
        editor.setMolecule(base);
        editor.moleculeChanged();
        suppressOnChange = false;
    };

    // Re-apply the decoration pipeline after every user edit so newly-drawn
    // atoms get protons + labels too. The `isUserEvent` guard plus a manual
    // re-entry flag prevent a feedback loop with the `moleculeChanged()` call
    // we trigger ourselves.
    editor.setOnChangeListener((event) => {
        if (
            !suppressOnChange &&
            event.type === "molecule" &&
            event.isUserEvent
        ) {
            const current = editor.getMolecule();
            decorate(current);
            suppressOnChange = true;
            editor.moleculeChanged();
            suppressOnChange = false;
        }
        if (typeof userOnChange === "function") {
            userOnChange(event);
        }
    });

    return {
        instance: editor,

        setSmiles(smiles) {
            const molecule =
                smiles && smiles.trim() !== ""
                    ? OCL.Molecule.fromSmiles(smiles)
                    : emptyMolecule();
            setDecorated(molecule);
        },

        getSmiles() {
            return editor.getMolecule().toIsomericSmiles();
        },

        setMolFile(molfile) {
            const molecule =
                molfile && molfile.trim() !== ""
                    ? OCL.Molecule.fromMolfile(molfile)
                    : emptyMolecule();
            setDecorated(molecule);
        },

        getMolFile() {
            return editor.getMolecule().toMolfile();
        },

        getMolecule() {
            return editor.getMolecule();
        },

        setMolecule(molecule) {
            setDecorated(molecule);
        },

        setExpandHydrogens(value) {
            flags.expandHydrogens = Boolean(value);
            rebuildFromCurrent();
        },

        setShowAtomNumbers(value) {
            flags.showAtomNumbers = Boolean(value);
            rebuildFromCurrent();
        },

        onChange(callback) {
            userOnChange = callback;
        },

        destroy() {
            if (!editor.isDestroyed) {
                editor.destroy();
            }
        },
    };
}
