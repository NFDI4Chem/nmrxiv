import { moleculeFromMolfileOrText } from "@/Utils/molfileNormalize";

/** @type {import("openchemlib").default | null} */
let oclModule = null;

/** @type {Promise<import("openchemlib").default> | null} */
let oclLoadPromise = null;

/**
 * Load the full openchemlib ESM build (avoids Vite tree-shaking away CanvasEditor).
 *
 * @returns {Promise<import("openchemlib").default>}
 */
export async function loadOpenChemLib() {
    if (oclModule) {
        return oclModule;
    }

    if (!oclLoadPromise) {
        oclLoadPromise = (async () => {
            const imported = await import("openchemlib");
            const OCL = imported.default ?? imported;

            if (typeof OCL.CanvasEditor !== "function") {
                throw new Error(
                    "Structure editor requires openchemlib v9 or newer (CanvasEditor). Run npm install, then restart the Vite dev server."
                );
            }

            oclModule = OCL;

            return OCL;
        })();
    }

    return oclLoadPromise;
}

/**
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
 * @param {import("openchemlib").Molecule} molecule
 * @param {import("openchemlib").default} OCL
 * @returns {import("openchemlib").Molecule}
 */
function collapseHydrogens(molecule, OCL) {
    if (!molecule) {
        return molecule;
    }
    return OCL.Molecule.fromSmiles(molecule.toIsomericSmiles());
}

/**
 * @param {import("openchemlib").Molecule} molecule
 * @param {import("openchemlib").default} OCL
 */
function markUnassignedStereo(molecule, OCL) {
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

/** Preset for structure search modals: hides Expand H and atom number toggles. */
export const STRUCTURE_SEARCH_EDITOR_OPTIONS = {
    showExpandHydrogensToggle: false,
    showAtomNumbersToggle: false,
};

/**
 * Only apply percentage height when the host has no usable height from CSS.
 * Inline `height: 100%` overrides Tailwind classes (e.g. h-[360px]) and collapses
 * the canvas when ancestors use auto height (hero structure tab on Welcome).
 *
 * @param {HTMLElement} host
 */
function ensureHostHasRenderableHeight(host) {
    if (host.style.height) {
        return;
    }

    const { height, minHeight } = window.getComputedStyle(host);
    const heightPx = parseFloat(height);
    const minHeightPx = parseFloat(minHeight);

    if (Number.isFinite(heightPx) && heightPx > 0) {
        return;
    }

    if (Number.isFinite(minHeightPx) && minHeightPx > 0) {
        host.style.height = `${minHeightPx}px`;
        return;
    }

    host.style.height = "100%";
}

/**
 * @param {HTMLElement} host
 * @param {{ expandHydrogens: boolean, showAtomNumbers: boolean }} flags
 * @param {{
 *   showExpandHydrogensToggle: boolean,
 *   showAtomNumbersToggle: boolean,
 *   onExpandHydrogensChange: (value: boolean) => void,
 *   onShowAtomNumbersChange: (value: boolean) => void,
 * }} controls
 * @returns {HTMLElement}
 */
function buildToggleUI(host, flags, controls) {
    host.innerHTML = "";
    if (!host.style.position || host.style.position === "static") {
        host.style.position = "relative";
    }
    host.style.overflow = "hidden";
    host.style.minHeight = "0";
    ensureHostHasRenderableHeight(host);

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

    if (controls.showExpandHydrogensToggle) {
        bar.appendChild(
            makeToggle(
                "Expand H",
                flags.expandHydrogens,
                controls.onExpandHydrogensChange
            )
        );
    }

    if (controls.showAtomNumbersToggle) {
        bar.appendChild(
            makeToggle(
                "Atom numbers",
                flags.showAtomNumbers,
                controls.onShowAtomNumbersChange
            )
        );
    }

    if (bar.childElementCount > 0) {
        host.appendChild(bar);
    }

    return canvasContainer;
}

/**
 * @typedef {import("openchemlib").CanvasEditorOptions & {
 *   expandHydrogens?: boolean,
 *   showAtomNumbers?: boolean,
 *   showExpandHydrogensToggle?: boolean,
 *   showAtomNumbersToggle?: boolean,
 * }} StructureEditorOptions
 */

/**
 * @param {import("openchemlib").default} OCL
 * @param {string|HTMLElement} target
 * @param {StructureEditorOptions} [options]
 */
function createStructureEditorWithOcl(OCL, target, options = {}) {
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
        showExpandHydrogensToggle = true,
        showAtomNumbersToggle = true,
        ...editorOptions
    } = options;

    const flags = {
        expandHydrogens: showExpandHydrogensToggle ? initialExpandH : false,
        showAtomNumbers: showAtomNumbersToggle ? initialAtomNumbers : false,
    };

    const canvasContainer = buildToggleUI(host, flags, {
        showExpandHydrogensToggle,
        showAtomNumbersToggle,
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
        markUnassignedStereo(molecule, OCL);
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

    const rebuildFromCurrent = () => {
        const current = editor.getMolecule();
        if (!current) {
            return;
        }
        const base = flags.expandHydrogens
            ? current
            : collapseHydrogens(current, OCL);
        decorate(base);
        suppressOnChange = true;
        editor.setMolecule(base);
        editor.moleculeChanged();
        suppressOnChange = false;
    };

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
            if (!molfile || molfile.trim() === "") {
                setDecorated(emptyMolecule());
                return;
            }

            const { molecule } = moleculeFromMolfileOrText(molfile, OCL);

            setDecorated(
                molecule && molecule.getAtoms() > 0 ? molecule : emptyMolecule()
            );
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
            if (!showExpandHydrogensToggle) {
                return;
            }
            flags.expandHydrogens = Boolean(value);
            rebuildFromCurrent();
        },

        setShowAtomNumbers(value) {
            if (!showAtomNumbersToggle) {
                return;
            }
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

/**
 * @param {string|HTMLElement} target
 * @param {StructureEditorOptions} [options]
 */
export async function createStructureEditor(target, options = {}) {
    const OCL = await loadOpenChemLib();

    return createStructureEditorWithOcl(OCL, target, options);
}
