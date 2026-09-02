<template>
    <div class="flex h-full w-full flex-col">
        <div
            v-if="!molfile"
            class="flex h-full min-h-[16rem] flex-1 items-center justify-center rounded-md border border-dashed border-gray-200 bg-gray-50 px-3 py-8 text-center text-sm text-gray-500 dark:border-slate-600 dark:bg-slate-900/40 dark:text-slate-400"
        >
            No linked structure is available to display HiFSA assignments.
        </div>
        <div
            v-else
            ref="viewerHost"
            class="relative h-full min-h-[16rem] w-full overflow-hidden rounded-md bg-white dark:bg-slate-950"
            role="img"
            :aria-label="ariaLabel"
        >
            <div ref="viewerEl" class="absolute inset-0 h-full w-full"></div>
            <p
                v-if="loadError"
                class="absolute inset-0 flex items-center justify-center bg-white/90 px-3 text-center text-sm text-red-600 dark:bg-slate-950/90 dark:text-red-300"
            >
                {{ loadError }}
            </p>
            <p
                v-else-if="showInteractionHint"
                class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-white/95 to-transparent px-3 pb-3 pt-8 text-center text-xs text-gray-500 dark:from-slate-950/95 dark:text-slate-400"
            >
                {{ interactionHint }}
            </p>
        </div>
    </div>
</template>

<script>
import {
    buildCouplingOverlays,
    buildShiftOverlays,
} from "@/Utils/hifsaAtomLabels";

const HIGHLIGHT_COLOR = "#f59e0b";
const ARROW_HIGHLIGHT = "#f59e0b";
const LABEL_BG = "0x0f172a";
const LABEL_BG_LIGHT = "0xf8fafc";

export default {
    name: "HifsaMoleculeViewer",
    props: {
        molfile: {
            type: String,
            default: null,
        },
        /**
         * Cosmic Truth label → 1-based SDF atom index (from OUTPUT.json `o`+1).
         */
        atomMap: {
            type: Object,
            default: null,
        },
        mode: {
            type: String,
            default: "shifts",
            validator: (value) => ["shifts", "couplings"].includes(value),
        },
        rows: {
            type: Array,
            default: () => [],
        },
        activeRowIndex: {
            type: Number,
            default: null,
        },
        selectedRowIndex: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            viewer: null,
            model: null,
            atoms: [],
            loadError: null,
            resizeObserver: null,
            darkObserver: null,
            isDark: false,
            threeDmol: null,
        };
    },
    computed: {
        highlightedRowIndex() {
            if (Number.isFinite(this.activeRowIndex)) {
                return this.activeRowIndex;
            }

            if (Number.isFinite(this.selectedRowIndex)) {
                return this.selectedRowIndex;
            }

            return null;
        },
        overlays() {
            if (!this.assignmentsEnabled) {
                return [];
            }

            if (this.mode === "couplings") {
                return buildCouplingOverlays(this.rows, this.atomMap);
            }

            return buildShiftOverlays(this.rows, this.atomMap);
        },
        assignmentsEnabled() {
            return Boolean(
                this.molfile &&
                    this.atomMap &&
                    typeof this.atomMap === "object" &&
                    Object.keys(this.atomMap).length
            );
        },
        showInteractionHint() {
            return (
                this.assignmentsEnabled &&
                !this.loadError &&
                this.highlightedRowIndex == null
            );
        },
        interactionHint() {
            if (this.mode === "couplings") {
                return "Hover or click a coupling row to show J on the structure.";
            }

            return "Hover or click a chemical-shift row to show δ on the structure.";
        },
        ariaLabel() {
            if (this.mode === "couplings") {
                return "3D molecule with HiFSA coupling constant arrows";
            }

            return "3D molecule with HiFSA chemical shift labels";
        },
    },
    watch: {
        molfile() {
            this.rebuildViewer();
        },
        atomMap: {
            deep: true,
            handler() {
                this.redrawOverlays();
            },
        },
        mode() {
            this.redrawOverlays();
        },
        rows: {
            deep: true,
            handler() {
                this.redrawOverlays();
            },
        },
        highlightedRowIndex() {
            this.redrawOverlays();
        },
    },
    mounted() {
        this.isDark = this.detectDarkMode();
        this.observeDarkMode();
        this.rebuildViewer();
    },
    beforeUnmount() {
        this.teardownViewer();
        this.resizeObserver?.disconnect();
        this.darkObserver?.disconnect();
    },
    methods: {
        detectDarkMode() {
            if (typeof document === "undefined") {
                return false;
            }

            return (
                document.documentElement.classList.contains("dark") ||
                document.body?.classList?.contains("dark")
            );
        },
        observeDarkMode() {
            if (typeof MutationObserver === "undefined") {
                return;
            }

            this.darkObserver = new MutationObserver(() => {
                const next = this.detectDarkMode();

                if (next !== this.isDark) {
                    this.isDark = next;
                    this.applyBackground();
                    this.redrawOverlays();
                }
            });

            this.darkObserver.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ["class"],
            });

            if (document.body) {
                this.darkObserver.observe(document.body, {
                    attributes: true,
                    attributeFilter: ["class"],
                });
            }
        },
        async load3Dmol() {
            if (this.threeDmol) {
                return this.threeDmol;
            }

            const mod = await import("3dmol/build/3Dmol.es6.js");
            this.threeDmol = mod.default || mod;

            if (typeof this.threeDmol.createViewer !== "function") {
                throw new Error("3Dmol.js createViewer is unavailable");
            }

            return this.threeDmol;
        },
        async waitForViewerSize(el, attempts = 20) {
            for (let i = 0; i < attempts; i++) {
                const width = el.clientWidth;
                const height = el.clientHeight;

                if (width > 0 && height > 0) {
                    return true;
                }

                await new Promise((resolve) =>
                    requestAnimationFrame(() => resolve())
                );
            }

            return el.clientWidth > 0 && el.clientHeight > 0;
        },
        async rebuildViewer() {
            this.teardownViewer();
            this.loadError = null;

            if (!this.molfile || !String(this.molfile).trim()) {
                return;
            }

            await this.$nextTick();

            const host = this.$refs.viewerHost;
            const el = this.$refs.viewerEl;

            if (!el || !host) {
                return;
            }

            try {
                const sized = await this.waitForViewerSize(host);

                if (!sized) {
                    throw new Error("Viewer container has no layout size yet");
                }

                const threeDmol = await this.load3Dmol();

                this.viewer = threeDmol.createViewer(el, {
                    backgroundColor: this.isDark ? 0x020617 : 0xffffff,
                    antialias: true,
                });

                this.model = this.viewer.addModel(this.molfile, "sdf");
                this.viewer.setStyle(
                    {},
                    {
                        stick: { radius: 0.15 },
                        sphere: { scale: 0.25 },
                    }
                );
                this.atoms = this.viewer.selectedAtoms({}) || [];
                this.redrawOverlays();
                this.viewer.zoomTo();
                this.viewer.render();
                this.setupResizeObserver();
            } catch (error) {
                console.error(error);
                this.loadError =
                    "Could not render the molecular structure in 3D.";
                this.teardownViewer();
            }
        },
        setupResizeObserver() {
            this.resizeObserver?.disconnect();

            if (
                typeof ResizeObserver === "undefined" ||
                !this.$refs.viewerHost
            ) {
                return;
            }

            this.resizeObserver = new ResizeObserver(() => {
                if (!this.viewer) {
                    return;
                }

                this.viewer.resize();
                this.viewer.render();
            });

            this.resizeObserver.observe(this.$refs.viewerHost);
        },
        applyBackground() {
            if (!this.viewer) {
                return;
            }

            this.viewer.setBackgroundColor(this.isDark ? 0x020617 : 0xffffff);
            this.viewer.render();
        },
        teardownViewer() {
            if (this.viewer) {
                try {
                    this.viewer.clear();
                    this.viewer.removeAllModels?.();
                } catch {
                    // Viewer may already be disposed with the DOM node.
                }
            }

            this.viewer = null;
            this.model = null;
            this.atoms = [];

            const el = this.$refs.viewerEl;

            if (el) {
                el.innerHTML = "";
            }
        },
        redrawOverlays() {
            if (!this.viewer || !this.atoms.length) {
                return;
            }

            this.viewer.removeAllLabels();
            this.viewer.removeAllShapes();
            this.viewer.setStyle(
                {},
                {
                    stick: { radius: 0.15 },
                    sphere: { scale: 0.25 },
                }
            );

            if (this.mode === "couplings") {
                this.drawCouplings();
            } else {
                this.drawShifts();
            }

            this.viewer.render();
        },
        drawShifts() {
            // δ labels are hover/selection-driven to avoid clutter on large NP systems.
            if (this.highlightedRowIndex == null) {
                return;
            }

            for (const overlay of this.overlays) {
                if (overlay.rowIndex !== this.highlightedRowIndex) {
                    continue;
                }

                const resolved = overlay.atoms.flatMap((atom) =>
                    this.resolveAtoms(atom, { requireExactElement: true })
                );

                if (resolved.length !== overlay.atoms.length) {
                    continue;
                }

                for (const atom of resolved) {
                    this.viewer.addLabel(overlay.text, {
                        position: atom,
                        backgroundColor: this.isDark
                            ? LABEL_BG
                            : LABEL_BG_LIGHT,
                        backgroundOpacity: 0.95,
                        fontColor: this.isDark ? "white" : "#0f172a",
                        fontSize: 13,
                        borderThickness: 1.5,
                        borderColor: HIGHLIGHT_COLOR,
                        inFront: true,
                        showBackground: true,
                    });

                    this.viewer.setStyle(
                        { index: atom.index },
                        {
                            stick: {
                                radius: 0.22,
                                color: HIGHLIGHT_COLOR,
                            },
                            sphere: {
                                scale: 0.35,
                                color: HIGHLIGHT_COLOR,
                            },
                        }
                    );
                }
            }
        },
        drawCouplings() {
            // Coupling arrows/J labels are hover/selection-driven only.
            if (this.highlightedRowIndex == null) {
                return;
            }

            for (const overlay of this.overlays) {
                if (overlay.rowIndex !== this.highlightedRowIndex) {
                    continue;
                }

                for (const pair of overlay.pairs) {
                    const fromPoint = this.resolveCouplingPoint(pair.from);
                    const toPoint = this.resolveCouplingPoint(pair.to);

                    // Only draw between real SDF atoms — never invent coordinates.
                    if (!fromPoint || !toPoint) {
                        continue;
                    }

                    if (this.distance3(fromPoint, toPoint) < 0.05) {
                        continue;
                    }

                    const points = this.curvedArrowPoints(
                        fromPoint,
                        toPoint,
                        this.moleculeCentroid()
                    );
                    this.drawCurvedArrow(points);

                    this.viewer.addLabel(pair.from.raw, {
                        position: fromPoint,
                        backgroundColor: this.isDark
                            ? LABEL_BG
                            : LABEL_BG_LIGHT,
                        backgroundOpacity: 0.9,
                        fontColor: this.isDark ? "white" : "#0f172a",
                        fontSize: 11,
                        borderThickness: 1,
                        borderColor: ARROW_HIGHLIGHT,
                        inFront: true,
                        showBackground: true,
                    });

                    this.viewer.addLabel(pair.to.raw, {
                        position: toPoint,
                        backgroundColor: this.isDark
                            ? LABEL_BG
                            : LABEL_BG_LIGHT,
                        backgroundOpacity: 0.9,
                        fontColor: this.isDark ? "white" : "#0f172a",
                        fontSize: 11,
                        borderThickness: 1,
                        borderColor: ARROW_HIGHLIGHT,
                        inFront: true,
                        showBackground: true,
                    });

                    const labelAt = points[Math.floor(points.length / 2)];

                    this.viewer.addLabel(overlay.text, {
                        position: {
                            x: labelAt.x,
                            y: labelAt.y,
                            z: labelAt.z,
                        },
                        backgroundColor: this.isDark
                            ? LABEL_BG
                            : LABEL_BG_LIGHT,
                        backgroundOpacity: 0.95,
                        fontColor: this.isDark ? "white" : "#0f172a",
                        fontSize: 13,
                        borderThickness: 1.5,
                        borderColor: ARROW_HIGHLIGHT,
                        inFront: true,
                        showBackground: true,
                    });

                    for (const atom of [fromPoint.atom, toPoint.atom].filter(
                        Boolean
                    )) {
                        this.viewer.setStyle(
                            { index: atom.index },
                            {
                                stick: {
                                    radius: 0.22,
                                    color: HIGHLIGHT_COLOR,
                                },
                                sphere: {
                                    scale: 0.35,
                                    color: HIGHLIGHT_COLOR,
                                },
                            }
                        );
                    }
                }
            }
        },
        /**
         * Resolve a coupling endpoint to a real SDF atom only.
         * Cosmic Truth `Hn` means the hydrogen on heavy atom n when present;
         * if that hydrogen is missing from the structure, return null (skip).
         *
         * @param {{ element: string, serial: number, suffix: string|null, raw: string }} descriptor
         * @returns {{x:number,y:number,z:number,atom:object}|null}
         */
        resolveCouplingPoint(descriptor) {
            const atoms = this.resolveAtoms(descriptor, {
                requireExactElement: true,
            });
            const atom = atoms[0] || null;

            if (!atom) {
                return null;
            }

            return {
                x: atom.x,
                y: atom.y,
                z: atom.z,
                atom,
            };
        },
        /**
         * Draw a smooth quadratic-bezier arrow. Avoid 3Dmol addCurve+toArrow,
         * which peels too many spline points and leaves a broken shaft.
         *
         * @param {Array<{x:number,y:number,z:number}>} points
         */
        drawCurvedArrow(points) {
            if (!points || points.length < 3) {
                return;
            }

            const radius = 0.08;
            const color = 0xf59e0b;
            const tipLength = Math.min(
                0.6,
                Math.max(
                    0.4,
                    this.distance3(points[0], points[points.length - 1]) * 0.22
                )
            );

            let tipStart = points.length - 2;

            for (let i = points.length - 2; i >= 1; i--) {
                if (
                    this.distance3(points[i], points[points.length - 1]) >=
                    tipLength
                ) {
                    tipStart = i;
                    break;
                }
            }

            for (let i = 0; i < tipStart; i++) {
                this.viewer.addCylinder({
                    start: {
                        x: points[i].x,
                        y: points[i].y,
                        z: points[i].z,
                    },
                    end: {
                        x: points[i + 1].x,
                        y: points[i + 1].y,
                        z: points[i + 1].z,
                    },
                    radius,
                    color,
                    fromCap: i === 0,
                    toCap: false,
                });
            }

            this.viewer.addArrow({
                start: {
                    x: points[tipStart].x,
                    y: points[tipStart].y,
                    z: points[tipStart].z,
                },
                end: {
                    x: points[points.length - 1].x,
                    y: points[points.length - 1].y,
                    z: points[points.length - 1].z,
                },
                radius,
                radiusRatio: 2.4,
                mid: 0.01,
                color,
            });
        },
        distance3(a, b) {
            return Math.hypot(b.x - a.x, b.y - a.y, b.z - a.z);
        },
        /**
         * Average atom position — used to bend coupling arrows outward.
         *
         * @returns {{x:number,y:number,z:number}}
         */
        moleculeCentroid() {
            if (!this.atoms.length) {
                return { x: 0, y: 0, z: 0 };
            }

            let x = 0;
            let y = 0;
            let z = 0;

            for (const atom of this.atoms) {
                x += atom.x;
                y += atom.y;
                z += atom.z;
            }

            const n = this.atoms.length;

            return { x: x / n, y: y / n, z: z / n };
        },
        /**
         * Sample a quadratic Bezier from→to with the bulge forced outward
         * from the molecule centroid (never through the structure).
         *
         * @param {{x:number,y:number,z:number}} from
         * @param {{x:number,y:number,z:number}} to
         * @param {{x:number,y:number,z:number}} centroid
         * @returns {Array<{x:number,y:number,z:number}>}
         */
        curvedArrowPoints(from, to, centroid = { x: 0, y: 0, z: 0 }) {
            const dx = to.x - from.x;
            const dy = to.y - from.y;
            const dz = to.z - from.z;
            const length = Math.hypot(dx, dy, dz) || 1;
            const mid = {
                x: (from.x + to.x) / 2,
                y: (from.y + to.y) / 2,
                z: (from.z + to.z) / 2,
            };

            // Radial direction from molecule center toward the chord midpoint.
            let ox = mid.x - centroid.x;
            let oy = mid.y - centroid.y;
            let oz = mid.z - centroid.z;

            // Remove the component parallel to the arrow so the bulge is
            // perpendicular and still reads as from→to.
            const along = (ox * dx + oy * dy + oz * dz) / (length * length);
            ox -= along * dx;
            oy -= along * dy;
            oz -= along * dz;

            let outwardLength = Math.hypot(ox, oy, oz);

            if (outwardLength < 1e-6) {
                // Degenerate (mid near center): fall back to a world-up cross.
                ox = dy * 1 - dz * 0;
                oy = dz * 0 - dx * 1;
                oz = dx * 0 - dy * 0;
                outwardLength = Math.hypot(ox, oy, oz);

                if (outwardLength < 1e-6) {
                    ox = 0;
                    oy = 1;
                    oz = 0;
                    outwardLength = 1;
                }
            }

            const bulge = Math.min(1.8, Math.max(0.7, length * 0.45));
            const scale = bulge / outwardLength;
            const control = {
                x: mid.x + ox * scale,
                y: mid.y + oy * scale,
                z: mid.z + oz * scale,
            };

            const steps = 24;
            const points = [];

            for (let i = 0; i <= steps; i++) {
                const t = i / steps;
                const oneMinus = 1 - t;
                points.push({
                    x:
                        oneMinus * oneMinus * from.x +
                        2 * oneMinus * t * control.x +
                        t * t * to.x,
                    y:
                        oneMinus * oneMinus * from.y +
                        2 * oneMinus * t * control.y +
                        t * t * to.y,
                    z:
                        oneMinus * oneMinus * from.z +
                        2 * oneMinus * t * control.z +
                        t * t * to.z,
                });
            }

            return points;
        },
        /**
         * Map a Cosmic Truth atom descriptor onto 3Dmol atoms (1-based serial).
         *
         * @param {{ element: string, serial: number, suffix: string|null }} descriptor
         * @returns {object[]}
         */
        /**
         * Cosmic Truth uses 1-based SDF atom numbers. 3Dmol may expose those as
         * 1-based serials (V2000) or 0-based serials/index (V3000).
         */
        atomsForSerial(serial) {
            if (!Number.isFinite(serial) || serial < 1) {
                return [];
            }

            const serials = this.atoms
                .map((atom) => atom.serial)
                .filter((value) => typeof value === "number");
            const minSerial = serials.length > 0 ? Math.min(...serials) : 0;
            const zeroBased = minSerial === 0;
            const targetSerial = zeroBased ? serial - 1 : serial;

            let matches = this.atoms.filter(
                (atom) => atom.serial === targetSerial
            );

            if (!matches.length) {
                matches = this.atoms.filter(
                    (atom) => atom.index === serial - 1
                );
            }

            return matches;
        },
        resolveAtoms(descriptor, options = {}) {
            if (!descriptor || !this.atoms.length) {
                return [];
            }

            // CT labels are never SDF serials. Without a map entry, skip.
            const mappedSerial = this.mappedSerialFor(descriptor);

            if (mappedSerial == null) {
                return [];
            }

            const mapped = this.atomsForSerial(mappedSerial);
            const matched = mapped.find(
                (atom) => atom.elem === descriptor.element
            );

            if (matched) {
                return [matched];
            }

            // Missing exact element (e.g. H mapped onto heavy atom): do not
            // invent coordinates or fall back to CT serial numbers.
            if (options.requireExactElement) {
                return [];
            }

            return [];
        },
        /**
         * @param {{ raw?: string, element?: string, serial?: number }} descriptor
         * @returns {number|null}
         */
        mappedSerialFor(descriptor) {
            if (!this.atomMap || typeof this.atomMap !== "object") {
                return null;
            }

            const raw =
                typeof descriptor?.raw === "string"
                    ? descriptor.raw.trim()
                    : "";

            if (!raw) {
                return null;
            }

            const direct = this.atomMap[raw];

            if (direct != null) {
                const value = Number(direct);

                return Number.isFinite(value) && value >= 1 ? value : null;
            }

            // Case-insensitive lookup for map key mismatches.
            const lower = raw.toLowerCase();
            const key = Object.keys(this.atomMap).find(
                (candidate) => candidate.toLowerCase() === lower
            );

            if (!key) {
                return null;
            }

            const value = Number(this.atomMap[key]);

            return Number.isFinite(value) && value >= 1 ? value : null;
        },
        /**
         * Real hydrogens bonded to a heavy atom (bond table, else distance).
         * Kept for diagnostics; assignment overlays use atomMap only.
         *
         * @param {object} heavy
         * @returns {object[]}
         */
        hydrogensOnHeavy(heavy) {
            const fromBonds = (heavy.bonds || [])
                .map((bondIndex) => this.atoms[bondIndex])
                .filter((atom) => atom && atom.elem === "H");

            if (fromBonds.length) {
                return fromBonds;
            }

            return this.atoms.filter((atom) => {
                if (atom.elem !== "H" || atom === heavy) {
                    return false;
                }

                return this.distance3(heavy, atom) <= 1.25;
            });
        },
    },
};
</script>
