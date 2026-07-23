<template>
    <a>
        <div>
            <InertiaLink :href="resolvedHref">
                <div class="relative overflow-hidden rounded-t">
                    <Depictor2D
                        v-if="
                            molecule.canonical_smiles &&
                            String(molecule.canonical_smiles).trim() !== ''
                        "
                        class="border-b p-4"
                        :molecule="molecule.canonical_smiles"
                    ></Depictor2D>
                    <div
                        v-else
                        class="relative flex min-h-[180px] items-center justify-center border-b bg-gray-50 p-4"
                    >
                        <span class="text-center text-sm text-gray-500"
                            >No structure drawing available</span
                        >
                    </div>
                    <span
                        v-if="moleculeIdOverlayLabel"
                        class="pointer-events-none absolute bottom-2 left-2 z-10 inline-flex items-center rounded bg-white/90 px-1.5 py-0.5 font-mono text-[11px] font-semibold tabular-nums leading-none text-gray-700 shadow-sm ring-1 ring-black/5 dark:bg-gray-900/90 dark:text-gray-200 dark:ring-white/10"
                    >
                        {{ moleculeIdOverlayLabel }}
                    </span>
                </div>
                <div class="px-4 pb-4 pt-3">
                    <div
                        v-if="showAnnotationStars"
                        class="mb-2 flex items-center"
                    >
                        <div class="mb-1 flex items-center">
                            <svg
                                v-for="index in starCount"
                                :key="'y-' + index"
                                class="inline text-yellow-400 h-4 w-4 flex-shrink-0"
                                x-state:on="Active"
                                x-state:off="Inactive"
                                x-state-description='Active: "text-yellow-400", Inactive: "text-gray-200"'
                                x-description="Heroicon name: mini/star"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                            <svg
                                v-for="index in inactiveStarCount"
                                :key="'n-' + index"
                                class="inline text-gray-200 h-4 w-4 flex-shrink-0"
                                x-state-description='undefined: "text-yellow-400", undefined: "text-gray-200"'
                                x-description="Heroicon name: mini/star"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <template v-if="displayTitle">
                            <p
                                v-if="displayTitleIsSmiles"
                                class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500"
                            >
                                Structure
                            </p>
                            <h3
                                :class="[
                                    'leading-snug',
                                    displayTitleIsSmiles
                                        ? 'line-clamp-2 break-all font-mono text-xs font-normal text-gray-600 dark:text-gray-300'
                                        : 'line-clamp-3 text-[0.9375rem] font-semibold tracking-tight text-gray-900 dark:text-gray-50',
                                ]"
                                :title="displayTitle"
                            >
                                {{ displayTitle }}
                            </h3>
                        </template>
                        <p
                            v-else
                            class="text-sm text-gray-400 dark:text-gray-500"
                        >
                            —
                        </p>

                        <dl
                            v-if="hasCompoundMeta"
                            class="flex flex-wrap items-center gap-x-1.5 gap-y-0.5"
                        >
                            <template v-if="molecularFormula">
                                <dt class="sr-only">Molecular formula</dt>
                                <dd
                                    class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 font-mono text-[11px] font-medium text-gray-800 dark:bg-gray-800/80 dark:text-gray-200"
                                >
                                    <MolecularFormula
                                        :formula="molecularFormula"
                                    />
                                </dd>
                            </template>
                            <span
                                v-if="
                                    molecularFormula && formattedMolecularWeight
                                "
                                class="text-gray-300 dark:text-gray-600"
                                aria-hidden="true"
                                >·</span
                            >
                            <template v-if="formattedMolecularWeight">
                                <dt class="sr-only">Molecular weight</dt>
                                <dd
                                    class="text-[11px] font-medium tabular-nums text-gray-600 dark:text-gray-400"
                                >
                                    {{ formattedMolecularWeight }}
                                </dd>
                            </template>
                        </dl>
                    </div>
                    <div
                        v-if="showWorkspaceGrouping"
                        class="mt-3 overflow-hidden rounded-lg border border-gray-200/90 dark:border-gray-700"
                    >
                        <button
                            type="button"
                            class="flex w-full items-center justify-between gap-2 bg-gray-50/80 px-2 py-2 text-left transition hover:bg-gray-100/90 dark:bg-gray-900/40 dark:hover:bg-gray-800/60"
                            :aria-expanded="experimentTypesExpanded"
                            @click.stop.prevent="toggleExperimentTypes"
                        >
                            <span class="min-w-0 flex-1">
                                <span
                                    class="block text-[11px] font-medium text-gray-800 dark:text-gray-100"
                                    >{{ workspaceGroupingSummaryLine }}</span
                                >
                            </span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="h-4 w-4 shrink-0 text-gray-500 transition-transform duration-200 dark:text-gray-400"
                                :class="
                                    experimentTypesExpanded ? 'rotate-180' : ''
                                "
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                        <div
                            v-show="experimentTypesExpanded"
                            class="border-t border-gray-200/80 bg-white/60 px-2 py-1.5 dark:border-gray-700 dark:bg-gray-950/30"
                        >
                            <ul
                                class="max-h-36 space-y-1 overflow-y-auto pr-0.5"
                            >
                                <li
                                    v-if="workspaceSamplesNumber > 0"
                                    class="grid grid-cols-[minmax(0,1fr)_auto] gap-x-2 gap-y-0 border-b border-gray-200/90 pb-1.5 text-left text-[11px] leading-snug dark:border-gray-700"
                                    :class="{
                                        'mb-1': experimentTypeRows.length > 0,
                                    }"
                                >
                                    <span
                                        class="min-w-0 text-gray-800 dark:text-gray-100"
                                        >Samples</span
                                    >
                                    <span
                                        class="shrink-0 tabular-nums font-semibold text-gray-700 dark:text-gray-300"
                                        >{{ workspaceSamplesNumber }}</span
                                    >
                                </li>
                                <li
                                    v-for="row in experimentTypeRows"
                                    :key="row.label"
                                    class="grid grid-cols-[minmax(0,1fr)_auto] gap-x-2 gap-y-0 text-left text-[11px] leading-snug"
                                >
                                    <span
                                        class="min-w-0 break-words text-gray-800 dark:text-gray-100"
                                        :title="row.label"
                                        >{{ row.label }}</span
                                    >
                                    <span
                                        class="shrink-0 tabular-nums font-semibold text-indigo-700 dark:text-indigo-300"
                                        >{{ row.count }}</span
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div
                        v-if="caption"
                        class="mt-3 truncate border-t border-gray-100 pt-3 text-xs text-gray-500"
                    >
                        {{ caption }}
                    </div>
                </div>

                <!-- <div class="px-6 pb-2">
        <span
          class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2"
          >#photography</span
        >
        <span
          class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2"
          >#travel</span
        >
        <span
          class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2"
          >#winter</span
        >
      </div> -->
                <!-- </div> -->
            </InertiaLink>
        </div>
    </a>
</template>
<script>
import Depictor2D from "@/Shared/Depictor2D.vue";
import MolecularFormula from "@/Shared/MolecularFormula.vue";
import { Link as InertiaLink } from "@inertiajs/vue3";
export default {
    components: {
        Depictor2D,
        MolecularFormula,
        InertiaLink,
    },
    props: {
        molecule: {
            type: Object,
            required: true,
        },
        /**
         * When set, the card links here instead of the public spectra browser.
         */
        href: {
            type: String,
            default: null,
        },
        showAnnotationStars: {
            type: Boolean,
            default: true,
        },
        /**
         * Optional line shown under the compound title (e.g. study name on the dashboard).
         */
        caption: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            results: [],
            experimentTypesExpanded: false,
        };
    },
    computed: {
        resolvedHref() {
            if (this.href != null && String(this.href).trim() !== "") {
                return this.href;
            }

            return "/compound/M" + this.compoundNumericId;
        },
        compoundNumericId() {
            const raw = this.molecule?.identifier;
            if (raw === null || raw === undefined) {
                return "";
            }
            const s = String(raw).replace(/^NMRXIV:M/i, "");
            const lead = s.match(/^(\d+)/);

            return lead ? lead[1] : s.replace(/\D/g, "") || "";
        },
        moleculeIdOverlayLabel() {
            const n = this.compoundNumericId;

            return n !== "" ? `M${n}` : "";
        },
        workspaceSamplesNumber() {
            const raw = this.molecule?.workspace_samples_count;
            if (raw === undefined || raw === null) {
                return 0;
            }
            const n = Number(raw);

            return Number.isFinite(n) && n > 0 ? n : 0;
        },
        experimentTypeRows() {
            const raw =
                this.molecule?.workspace_experiment_type_counts ??
                this.molecule?.workspaceExperimentTypeCounts;
            if (raw == null || typeof raw !== "object" || Array.isArray(raw)) {
                return [];
            }

            return Object.entries(raw)
                .filter(([key]) => key != null && String(key).trim() !== "")
                .map(([label, count]) => ({
                    label: String(label),
                    count: Number(count) || 0,
                }))
                .filter((row) => row.count > 0)
                .sort(
                    (a, b) =>
                        b.count - a.count || a.label.localeCompare(b.label)
                );
        },
        showWorkspaceGrouping() {
            return (
                this.workspaceSamplesNumber > 0 ||
                this.experimentTypeRows.length > 0
            );
        },
        workspaceGroupingSummaryLine() {
            const parts = [];
            const samples = this.workspaceSamplesNumber;
            if (samples > 0) {
                parts.push(`${samples} sample${samples === 1 ? "" : "s"}`);
            }
            const rows = this.experimentTypeRows;
            if (rows.length > 0) {
                const kinds = rows.length;
                const total = rows.reduce((sum, r) => sum + r.count, 0);
                parts.push(
                    `${kinds} experiment type${
                        kinds === 1 ? "" : "s"
                    } · ${total} spectrum${total === 1 ? "" : "a"}`
                );
            }

            return parts.join(" · ");
        },
        starCount() {
            const n = Number(this.molecule?.annotation_level);
            if (!Number.isFinite(n) || n < 0) {
                return 0;
            }

            return Math.min(5, Math.floor(n));
        },
        inactiveStarCount() {
            return Math.max(0, 5 - this.starCount);
        },
        displayTitle() {
            const iupac = this.molecule?.iupac_name;
            if (iupac != null && String(iupac).trim() !== "") {
                return String(iupac).trim();
            }

            const name = this.molecule?.name;
            if (name != null && String(name).trim() !== "") {
                return String(name).trim();
            }

            const smiles = this.molecule?.canonical_smiles;
            if (smiles != null && String(smiles).trim() !== "") {
                return String(smiles).trim();
            }

            return null;
        },
        displayTitleIsSmiles() {
            const iupac = this.molecule?.iupac_name;
            if (iupac != null && String(iupac).trim() !== "") {
                return false;
            }

            const name = this.molecule?.name;
            if (name != null && String(name).trim() !== "") {
                return false;
            }

            return this.displayTitle != null;
        },
        molecularFormula() {
            const raw = this.molecule?.molecular_formula;
            if (raw == null) {
                return null;
            }
            const trimmed = String(raw).trim();

            return trimmed !== "" ? trimmed : null;
        },
        formattedMolecularWeight() {
            const raw = this.molecule?.molecular_weight;
            if (raw == null || raw === "") {
                return null;
            }
            const n = Number(raw);
            if (!Number.isFinite(n)) {
                const trimmed = String(raw).trim();

                return trimmed !== "" ? trimmed : null;
            }

            const formatted =
                Math.abs(n - Math.round(n)) < 0.001
                    ? String(Math.round(n))
                    : n.toFixed(2).replace(/\.?0+$/, "");

            return `${formatted} g/mol`;
        },
        hasCompoundMeta() {
            return (
                this.molecularFormula != null ||
                this.formattedMolecularWeight != null
            );
        },
    },
    mounted() {},
    methods: {
        toggleExperimentTypes() {
            this.experimentTypesExpanded = !this.experimentTypesExpanded;
        },
    },
};
</script>
