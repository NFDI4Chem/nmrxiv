<template>
    <section
        class="w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm ring-1 ring-gray-900/5 dark:border-gray-700 dark:bg-slate-800/90 dark:ring-white/5"
    >
        <button
            type="button"
            class="flex w-full items-center justify-between gap-3 border-b border-gray-100 px-3 py-3 text-left transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-500 dark:border-gray-700 dark:hover:bg-slate-800/80 sm:px-4"
            :aria-expanded="expanded"
            :aria-controls="panelControlId"
            @click="toggleExpanded"
        >
            <div class="flex min-w-0 flex-1 items-center gap-3">
                <h3
                    :id="headingControlId"
                    class="text-lg font-semibold tracking-tight text-gray-900 dark:text-slate-100"
                >
                    Chemical composition
                    <span class="text-red-500" aria-hidden="true">*</span>
                </h3>
                <span
                    v-if="!expanded && study"
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold tabular-nums text-gray-700 ring-1 ring-inset ring-gray-200/80 dark:bg-slate-700/80 dark:text-slate-200 dark:ring-gray-600"
                >
                    <img
                        src="https://upload.wikimedia.org/wikipedia/sco/3/35/ChEBI_logo.png"
                        alt=""
                        class="h-5 w-5 shrink-0 object-contain opacity-80 dark:opacity-90"
                        width="20"
                        height="20"
                        loading="lazy"
                        decoding="async"
                    />
                    {{ selectedStudyMoleculeCount }}
                    <span class="sr-only"
                        >{{ selectedStudyMoleculeCount }} molecule(s) in this
                        sample</span
                    >
                </span>
            </div>
            <ChevronRightIcon
                class="h-5 w-5 shrink-0 text-gray-400 transition-transform duration-200 dark:text-slate-500"
                :class="{ 'rotate-90': expanded }"
                aria-hidden="true"
            />
        </button>

        <div
            v-show="expanded"
            :id="panelControlId"
            class="grid min-h-0 gap-4 p-3 sm:p-4 lg:items-stretch lg:gap-6 lg:p-4"
            :class="canUpdateStudy ? 'lg:grid-cols-2' : 'lg:grid-cols-1'"
            role="region"
            :aria-labelledby="headingControlId"
        >
            <div
                class="flex h-full min-h-0 min-w-0 flex-col gap-3"
                :class="
                    canUpdateStudy
                        ? 'lg:border-r lg:border-gray-100 lg:pr-4 dark:lg:border-gray-700'
                        : ''
                "
            >
                <div v-if="selectedStudyMoleculeCount > 0" class="min-h-0">
                    <ul role="list" class="flex flex-col gap-3">
                        <li
                            v-for="molecule in study.sample.molecules"
                            :key="molecule.standard_inchi"
                            class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm ring-1 ring-gray-900/5 dark:border-gray-600 dark:bg-slate-900/50 dark:ring-white/5"
                        >
                            <div
                                class="border-b border-gray-100 bg-gray-50/90 px-3 py-2.5 dark:border-gray-700 dark:bg-slate-900/70"
                            >
                                <div
                                    class="flex items-start justify-between gap-2"
                                >
                                    <span
                                        class="inline-flex shrink-0 items-center rounded-md bg-teal-50 px-2 py-0.5 text-xs font-semibold tabular-nums text-teal-800 dark:bg-teal-900/50 dark:text-teal-200"
                                    >
                                        <template
                                            v-if="
                                                getMixtureComponentForMolecule(
                                                    molecule
                                                )
                                            "
                                        >
                                            {{
                                                formatMixtureValue(
                                                    getMixtureComponentForMolecule(
                                                        molecule
                                                    ).value
                                                )
                                            }}
                                            {{ mixtureBasisUnitLabel }}
                                        </template>
                                        <template v-else-if="molecule.pivot">
                                            <template
                                                v-if="
                                                    isCompositionPercentUnknown(
                                                        molecule.pivot
                                                            .percentage_composition
                                                    )
                                                "
                                            >
                                                Unknown
                                            </template>
                                            <template v-else>
                                                {{
                                                    formatCompositionPercent(
                                                        molecule.pivot
                                                            .percentage_composition
                                                    )
                                                }}%
                                            </template>
                                        </template>
                                        <template v-else>—</template>
                                    </span>
                                    <div
                                        v-if="canUpdateStudy"
                                        class="flex shrink-0 gap-0.5"
                                    >
                                        <button
                                            type="button"
                                            title="Edit compound"
                                            class="rounded-md p-1.5 text-gray-500 transition-colors hover:bg-white hover:text-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:hover:bg-slate-800 dark:hover:text-teal-400"
                                            @click="editMolecule(molecule)"
                                        >
                                            <PencilIcon
                                                class="h-4 w-4"
                                                aria-hidden="true"
                                            />
                                        </button>
                                        <button
                                            type="button"
                                            title="Remove compound"
                                            class="rounded-md p-1.5 text-gray-500 transition-colors hover:bg-white hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 dark:hover:bg-slate-800"
                                            @click="deleteMolecule(molecule)"
                                        >
                                            <TrashIcon
                                                class="h-4 w-4"
                                                aria-hidden="true"
                                            />
                                        </button>
                                    </div>
                                </div>
                                <p
                                    class="mt-2 min-w-0 break-all font-mono text-[11px] leading-snug text-gray-700 dark:text-slate-300"
                                >
                                    {{ molecule.standard_inchi }}
                                </p>
                            </div>
                            <div
                                v-if="molecule.canonical_smiles"
                                class="flex bg-white px-2 py-3 dark:bg-slate-900/40"
                            >
                                <Depictor
                                    class="width-full py-2"
                                    :model-value="molecule.canonical_smiles"
                                    :show-download="false"
                                ></Depictor>
                            </div>
                        </li>
                    </ul>
                </div>
                <div v-else class="flex min-h-0 flex-1 flex-col justify-center">
                    <div
                        class="flex flex-col items-center justify-center rounded-lg border border-dashed border-gray-200 bg-gray-50/80 px-4 py-8 text-center dark:border-gray-600 dark:bg-slate-900/35"
                    >
                        <div
                            class="rounded-full bg-gray-100 p-2 dark:bg-slate-800"
                        >
                            <svg
                                class="mx-auto h-8 w-8 text-gray-400 dark:text-slate-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    vector-effect="non-scaling-stroke"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                                />
                            </svg>
                        </div>
                        <p
                            class="mt-3 text-sm font-medium text-gray-900 dark:text-slate-200"
                        >
                            No compounds yet
                        </p>
                    </div>
                </div>
            </div>

            <div v-if="canUpdateStudy" class="flex min-w-0 flex-col gap-3">
                <div class="flex items-center justify-between gap-2">
                    <h4
                        class="text-sm font-semibold text-gray-900 dark:text-slate-200"
                    >
                        Add structure
                    </h4>
                    <a
                        href="https://docs.nmrxiv.org/submission-guides/editor.html"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-xs font-medium text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300"
                    >
                        Help
                    </a>
                </div>

                <div
                    class="relative flex h-[min(380px,52vh)] min-h-[280px] w-full flex-shrink-0 flex-col"
                >
                    <div
                        ref="editorHostRef"
                        class="relative min-h-0 flex-1 rounded-lg border border-gray-200 bg-gray-50 shadow-inner transition-colors dark:border-gray-600 dark:bg-slate-900/50"
                        :class="{
                            'border-teal-400 bg-teal-50/50 dark:bg-teal-950/20':
                                isDragging,
                        }"
                        tabindex="0"
                        @dragover.capture.prevent="handleDragOver"
                        @dragleave.capture.prevent="handleDragLeave"
                        @drop.capture.prevent="handleDrop"
                        @paste.capture="handleEditorPaste"
                    ></div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-slate-400">
                        Draw a structure, or paste / drop SMILES, MOL, SDF, or a
                        CAS number (e.g. 58-08-2).
                    </p>
                    <p
                        v-if="structureLoading"
                        class="mt-1 text-xs text-teal-600 dark:text-teal-400"
                    >
                        Loading structure…
                    </p>
                </div>

                <jet-input-error :message="errorMessage" class="mt-1.5" />

                <div
                    v-if="editorHasStructure"
                    class="mb-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm ring-1 ring-gray-900/5 dark:border-gray-600 dark:bg-slate-900/50 dark:ring-white/5"
                >
                    <div class="flex items-center justify-between gap-3">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400"
                        >
                            Composition share
                        </p>
                        <button
                            type="button"
                            class="inline-flex shrink-0 items-center gap-1 text-xs font-medium text-gray-500 transition-colors hover:text-teal-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 dark:text-slate-400 dark:hover:text-teal-400 dark:focus-visible:ring-offset-slate-900"
                            @click="showCompositionHelpModal = true"
                        >
                            Need help
                            <InformationCircleIcon
                                class="h-4 w-4"
                                aria-hidden="true"
                            />
                        </button>
                    </div>
                    <div
                        class="mt-3 flex flex-wrap rounded-lg bg-gray-100 p-1 dark:bg-slate-800/90"
                        role="radiogroup"
                        aria-label="Composition share type"
                    >
                        <button
                            type="button"
                            role="radio"
                            :disabled="compositionPureSampleDisabled"
                            :aria-checked="compositionSampleType === 'pure'"
                            :title="
                                compositionPureSampleDisabled
                                    ? 'Pure sample is only available before you add compounds'
                                    : undefined
                            "
                            :class="[
                                compositionPureSampleDisabled
                                    ? 'cursor-not-allowed opacity-60 text-gray-400 dark:text-slate-500'
                                    : compositionSampleType === 'pure'
                                    ? 'bg-white text-teal-800 shadow-sm ring-1 ring-gray-200/80 dark:bg-slate-800 dark:text-teal-200 dark:ring-gray-600'
                                    : 'text-gray-600 hover:text-gray-900 dark:text-slate-400 dark:hover:text-slate-200',
                                'min-w-0 flex-1 rounded-md px-3 py-2 text-center text-sm font-medium transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 disabled:pointer-events-none dark:focus-visible:ring-offset-slate-900',
                            ]"
                            @click="compositionSampleType = 'pure'"
                        >
                            Pure sample
                        </button>
                        <button
                            type="button"
                            role="radio"
                            :aria-checked="compositionSampleType === 'mixture'"
                            :class="[
                                compositionSampleType === 'mixture'
                                    ? 'bg-white text-teal-800 shadow-sm ring-1 ring-gray-200/80 dark:bg-slate-800 dark:text-teal-200 dark:ring-gray-600'
                                    : 'text-gray-600 hover:text-gray-900 dark:text-slate-400 dark:hover:text-slate-200',
                                'min-w-0 flex-1 rounded-md px-3 py-2 text-center text-sm font-medium transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900',
                            ]"
                            @click="compositionSampleType = 'mixture'"
                        >
                            Mixture
                        </button>
                        <button
                            type="button"
                            role="radio"
                            :aria-checked="compositionSampleType === 'unknown'"
                            :class="[
                                compositionSampleType === 'unknown'
                                    ? 'bg-white text-teal-800 shadow-sm ring-1 ring-gray-200/80 dark:bg-slate-800 dark:text-teal-200 dark:ring-gray-600'
                                    : 'text-gray-600 hover:text-gray-900 dark:text-slate-400 dark:hover:text-slate-200',
                                'min-w-0 flex-1 rounded-md px-3 py-2 text-center text-sm font-medium transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900',
                            ]"
                            @click="compositionSampleType = 'unknown'"
                        >
                            Unknown
                        </button>
                    </div>

                    <div
                        v-if="compositionSampleType === 'pure'"
                        class="mt-4 flex justify-end border-t border-gray-100 pt-4 dark:border-gray-700"
                    >
                        <span
                            class="text-2xl font-semibold tabular-nums tracking-tight text-teal-800 dark:text-teal-200"
                            aria-live="polite"
                            >{{ formatCompositionPercent(percentage) }}%</span
                        >
                    </div>

                    <div
                        v-show="compositionSampleType === 'mixture'"
                        class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-700"
                    >
                        <MixtureCompositionForm
                            ref="mixtureCompositionForm"
                            :composition="study?.sample?.mixture_composition"
                            @update:draft="mixtureDraft = $event"
                            @metadata-change="saveMixtureMetadata"
                        />
                    </div>
                </div>

                <button
                    v-if="editorHasStructure"
                    type="button"
                    class="inline-flex w-full items-center justify-center rounded-md border border-transparent bg-teal-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 dark:focus:ring-offset-slate-900 sm:w-auto"
                    :disabled="
                        compositionSampleType === 'mixture' &&
                        !canAddMixtureComponent
                    "
                    @click="saveMolecule()"
                >
                    Add compound
                </button>
            </div>
        </div>

        <MixtureCompositionHelpModal
            :show="showCompositionHelpModal"
            @close="showCompositionHelpModal = false"
        />
    </section>
</template>

<script>
import axios from "axios";
import OCL from "openchemlib";
import {
    TrashIcon,
    PencilIcon,
    InformationCircleIcon,
} from "@heroicons/vue/24/solid";
import { ChevronRightIcon } from "@heroicons/vue/24/outline";
import Depictor from "@/Shared/Depictor.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import MixtureCompositionForm from "@/Shared/MixtureCompositionForm.vue";
import MixtureCompositionHelpModal from "@/Shared/MixtureCompositionHelpModal.vue";
import {
    applySampleMoleculeResponse,
    basisUnitLabel,
    formatMixtureValue,
} from "@/Utils/mixtureComposition";
import {
    detectStructureInputFormat,
    detectStructureInputType,
    editorHasStructureContent,
    resolveStructureForEditorWithStandardize,
} from "@/Utils/structureImport";
import { createStructureEditor } from "@/Utils/structureEditor";

export default {
    components: {
        TrashIcon,
        PencilIcon,
        ChevronRightIcon,
        Depictor,
        JetInputError,
        MixtureCompositionForm,
        MixtureCompositionHelpModal,
        InformationCircleIcon,
    },
    props: {
        study: {
            type: Object,
            required: true,
        },
        canUpdateStudy: {
            type: Boolean,
            default: false,
        },
        chemistryStandardizeUrl: {
            type: String,
            required: true,
        },
        expanded: {
            type: Boolean,
            default: false,
        },
        idPrefix: {
            type: String,
            default: "chemical-composition",
        },
    },
    emits: ["update:expanded", "study-updated"],
    data() {
        return {
            isDragging: false,
            structureLoading: false,
            structureLoadCounter: 0,
            percentage: 99.99,
            compositionSampleType: "pure",
            mixtureDraft: null,
            showCompositionHelpModal: false,
            editor: null,
            editorHasStructure: false,
            errorMessage: "",
            percentageLabelId: `${this.idPrefix}-percentage-label`,
        };
    },
    computed: {
        panelControlId() {
            return `${this.idPrefix}-panel`;
        },
        headingControlId() {
            return `${this.idPrefix}-heading`;
        },
        selectedStudyMoleculeCount() {
            return this.study?.sample?.molecules?.length ?? 0;
        },
        compositionPureSampleDisabled() {
            return this.selectedStudyMoleculeCount > 0;
        },
        getMax() {
            if (!this.study?.sample?.molecules) {
                return 100;
            }
            let total = 0;
            this.study.sample.molecules.forEach((molecule) => {
                const value = parseFloat(
                    molecule?.pivot?.percentage_composition
                );
                total += Number.isFinite(value) ? value : 0;
            });
            const remaining = 100 - total;
            const epsilon = 1e-5;
            if (remaining <= epsilon) {
                return 0;
            }

            return remaining;
        },
        compositionSliderMax() {
            const max = this.getMax;

            return max < 0 ? 0 : max;
        },
        mixtureBasisUnitLabel() {
            return basisUnitLabel(
                this.study?.sample?.mixture_composition?.basis ??
                    this.$refs.mixtureCompositionForm?.localBasis ??
                    "mole_percent"
            );
        },
        canAddMixtureComponent() {
            return (
                this.$refs.mixtureCompositionForm?.canAddComponent?.() ?? false
            );
        },
    },
    watch: {
        canUpdateStudy(value) {
            if (value && !this.editor) {
                this.ensureStructureSearchEditor();
            }
        },
        getMax(newMax) {
            if (this.compositionSampleType === "pure") {
                const capped = Math.min(99.99, newMax);
                if (this.percentage !== capped) {
                    this.percentage = capped;
                }
                return;
            }
            if (this.compositionSampleType === "unknown") {
                return;
            }
            if (this.percentage > newMax) {
                this.percentage = newMax;
            }
        },
        compositionSampleType(mode) {
            if (mode === "pure") {
                this.percentage = Math.min(99.99, this.compositionSliderMax);
            } else if (mode === "mixture") {
                const max = this.compositionSliderMax;
                if (this.percentage > max) {
                    this.percentage = max;
                }
            }
        },
        compositionPureSampleDisabled(disabled) {
            if (disabled && this.compositionSampleType === "pure") {
                this.compositionSampleType = "mixture";
            }
        },
        expanded(isOpen) {
            if (isOpen) {
                this.ensureStructureSearchEditor();
            }
        },
        "study.id"() {
            this.initializeCompositionState();
        },
    },
    mounted() {
        this.initializeCompositionState();
        if (this.expanded) {
            this.ensureStructureSearchEditor();
        }
    },
    methods: {
        toggleExpanded() {
            this.$emit("update:expanded", !this.expanded);
        },
        initializeCompositionState() {
            this.errorMessage = "";
            this.structureLoadCounter = 0;
            this.syncCompositionSampleType(this.study?.sample);
            this.percentage = Math.min(99.99, this.compositionSliderMax);
            if (this.editor) {
                this.editor.setSmiles("");
                this.syncEditorHasStructure();
            } else {
                this.editorHasStructure = false;
            }
        },
        ensureStructureSearchEditor(onReady) {
            if (!this.canUpdateStudy || !this.expanded) {
                if (typeof onReady === "function") {
                    onReady();
                }
                return;
            }

            const run = (retriesLeft) => {
                this.$nextTick(() => {
                    requestAnimationFrame(async () => {
                        const host = this.$refs.editorHostRef;

                        if (!host || !host.isConnected) {
                            if (retriesLeft > 0) {
                                run(retriesLeft - 1);
                            } else if (typeof onReady === "function") {
                                onReady();
                            }
                            return;
                        }

                        const { width, height } = host.getBoundingClientRect();

                        if (width < 4 || height < 4) {
                            if (retriesLeft > 0) {
                                run(retriesLeft - 1);
                            } else if (typeof onReady === "function") {
                                onReady();
                            }
                            return;
                        }

                        if (!this.editor) {
                            this.editor = await createStructureEditor(host);
                            this.editor.onChange(() => {
                                this.syncEditorHasStructure();
                            });
                        }

                        this.syncEditorHasStructure();

                        if (typeof onReady === "function") {
                            onReady();
                        }
                    });
                });
            };

            run(20);
        },
        syncEditorHasStructure() {
            this.editorHasStructure = editorHasStructureContent(this.editor);
        },
        handleEditorPaste(event) {
            if (!this.expanded || !this.canUpdateStudy) {
                return;
            }

            const pastedText = event.clipboardData?.getData("text/plain") ?? "";

            if (!pastedText.trim()) {
                return;
            }

            const inputType = detectStructureInputType(pastedText);

            if (
                inputType === "CAS" ||
                inputType === "SMILES" ||
                inputType === "MOL/SDF"
            ) {
                event.preventDefault();
                event.stopPropagation();
                this.importStructureInput(pastedText);
            }
        },
        async importStructureInput(text, fileName = "") {
            const trimmed = text.trim();

            if (!trimmed) {
                return;
            }

            const inputType = detectStructureInputType(trimmed, fileName);
            this.errorMessage = "";

            if (inputType === "CAS") {
                await this.loadCasStructure(trimmed);
                return;
            }

            if (inputType === "SMILES" || inputType === "MOL/SDF") {
                await this.loadStructureIntoEditor(
                    trimmed,
                    fileName,
                    inputType
                );
                return;
            }

            this.errorMessage =
                "Unrecognized format. Paste or drop SMILES, MOL, SDF, or a CAS number.";
        },
        async loadStructureIntoEditor(text, fileName = "", format = null) {
            const loadId = ++this.structureLoadCounter;
            this.errorMessage = "";
            this.structureLoading = true;

            const resolvedFormat = format ?? detectStructureInputFormat(text);
            let structureToLoad = null;

            try {
                if (resolvedFormat === "SMILES") {
                    const smiles = text.trim();
                    OCL.Molecule.fromSmiles(smiles);
                    structureToLoad = { type: "smiles", value: smiles };
                } else if (resolvedFormat === "MOL/SDF") {
                    structureToLoad =
                        await resolveStructureForEditorWithStandardize(
                            text,
                            fileName,
                            (molfile) => this.standardizeMolecules(molfile)
                        );
                } else {
                    this.errorMessage =
                        "Unable to detect chemical format. Please check your input.";
                    return;
                }
            } catch {
                if (loadId !== this.structureLoadCounter) {
                    return;
                }
                this.errorMessage =
                    "Could not parse the structure. Check the file or pasted content.";
                return;
            } finally {
                if (loadId === this.structureLoadCounter) {
                    this.structureLoading = false;
                }
            }

            if (loadId !== this.structureLoadCounter || !structureToLoad) {
                return;
            }

            this.ensureStructureSearchEditor(() => {
                if (loadId !== this.structureLoadCounter || !this.editor) {
                    return;
                }

                try {
                    if (structureToLoad.type === "smiles") {
                        this.editor.setSmiles(structureToLoad.value);
                    } else {
                        this.editor.setMolFile(structureToLoad.value);
                    }
                    this.syncEditorHasStructure();
                } catch {
                    this.errorMessage =
                        "Could not load the structure into the editor.";
                }
            });
        },
        async fetchFromCAS(casNumber) {
            try {
                const response = await axios.get("/cas/detail", {
                    params: {
                        cas_rn: casNumber,
                    },
                    timeout: 30000,
                });

                return response.data;
            } catch (error) {
                const errorMessage =
                    error.response?.data?.error ||
                    error.response?.data?.message ||
                    "CAS API server error - please try again later";
                throw new Error(errorMessage);
            }
        },
        async loadCasStructure(casNumber) {
            const loadId = ++this.structureLoadCounter;
            this.errorMessage = "";
            this.structureLoading = true;

            try {
                const casData = await this.fetchFromCAS(casNumber);
                const smiles = casData.smile || casData.canonicalSmile;

                if (!smiles) {
                    this.errorMessage = `No structural data available for CAS ${casNumber}`;
                    return;
                }

                if (loadId !== this.structureLoadCounter) {
                    return;
                }

                this.ensureStructureSearchEditor(() => {
                    if (loadId !== this.structureLoadCounter || !this.editor) {
                        return;
                    }

                    this.editor.setSmiles(smiles);
                    this.syncEditorHasStructure();
                });
            } catch (error) {
                if (loadId === this.structureLoadCounter) {
                    this.errorMessage = error.message;
                }
            } finally {
                if (loadId === this.structureLoadCounter) {
                    this.structureLoading = false;
                }
            }
        },
        handleDragOver(event) {
            event.preventDefault();
            this.isDragging = true;
        },
        handleDragLeave(event) {
            event.preventDefault();
            this.isDragging = false;
        },
        handleDrop(event) {
            event.preventDefault();
            this.isDragging = false;

            const files = Array.from(event.dataTransfer?.files ?? []);

            if (files.length > 0) {
                this.processFiles(files);
                return;
            }

            const text = event.dataTransfer?.getData("text/plain") ?? "";

            if (text.trim()) {
                this.importStructureInput(text);
            }
        },
        processFiles(files) {
            if (files.length === 0) {
                return;
            }

            const validFiles = files.filter((file) => {
                const extension = file.name.toLowerCase().split(".").pop();
                return ["mol", "sdf", "sd"].includes(extension);
            });

            if (validFiles.length === 0) {
                this.errorMessage = "Please drop a valid MOL or SDF file.";
                return;
            }

            const file = validFiles[0];
            const reader = new FileReader();

            reader.onload = (loadEvent) => {
                this.importStructureInput(loadEvent.target.result, file.name);
            };

            reader.onerror = () => {
                this.errorMessage = "Error reading file. Please try again.";
            };

            reader.readAsText(file);

            if (validFiles.length > 1) {
                this.errorMessage = `Only the first file (${file.name}) was loaded.`;
            }
        },
        isCompositionPercentUnknown(value) {
            if (value === undefined || value === null) {
                return true;
            }
            const text = String(value).trim();
            if (text === "") {
                return true;
            }
            if (text.toLowerCase() === "unknown") {
                return true;
            }

            return false;
        },
        formatCompositionPercent(value) {
            const number = Number(value);
            if (!Number.isFinite(number)) {
                return value != null && value !== "" ? String(value) : "0";
            }
            if (Math.abs(number - Math.round(number)) < 1e-9) {
                return String(Math.round(number));
            }

            return number.toFixed(3).replace(/\.?0+$/, "");
        },
        deleteMolecule(molecule) {
            axios
                .delete(
                    "/dashboard/studies/" +
                        this.study.id +
                        "/molecule/" +
                        molecule.id
                )
                .then((response) => {
                    applySampleMoleculeResponse(
                        this.study.sample,
                        response.data
                    );
                    this.syncCompositionSampleType(this.study.sample);
                    this.$nextTick(() => {
                        this.percentage = Math.min(
                            99.99,
                            this.compositionSliderMax
                        );
                        this.$refs.mixtureCompositionForm?.resetDraft?.();
                    });
                    if (this.editor) {
                        this.editor.setSmiles("");
                        this.syncEditorHasStructure();
                    }
                    this.$emit("study-updated", this.study);
                });
        },
        editMolecule(molecule) {
            const mixtureComponent =
                this.getMixtureComponentForMolecule(molecule);
            if (mixtureComponent) {
                this.compositionSampleType = "mixture";
                this.$nextTick(() => {
                    this.$refs.mixtureCompositionForm?.resetDraft?.();
                    if (this.$refs.mixtureCompositionForm) {
                        this.$refs.mixtureCompositionForm.draft = {
                            value: mixtureComponent.value,
                            integrated_signal:
                                mixtureComponent.integrated_signal ?? "",
                            n_nuclei: mixtureComponent.n_nuclei ?? "",
                        };
                        if (
                            mixtureComponent.integrated_signal ||
                            mixtureComponent.n_nuclei
                        ) {
                            this.$refs.mixtureCompositionForm.expandDetails?.();
                        }
                    }
                });
            } else {
                const raw = molecule.pivot?.percentage_composition;
                if (this.isCompositionPercentUnknown(raw)) {
                    this.compositionSampleType = "unknown";
                } else {
                    this.compositionSampleType = "mixture";
                    this.percentage = parseFloat(raw) || 0;
                }
            }

            this.ensureStructureSearchEditor(() => {
                if (this.editor) {
                    this.editor.setSmiles(molecule.canonical_smiles);
                    this.syncEditorHasStructure();
                }
                axios
                    .delete(
                        "/dashboard/studies/" +
                            this.study.id +
                            "/molecule/" +
                            molecule.id
                    )
                    .then((response) => {
                        applySampleMoleculeResponse(
                            this.study.sample,
                            response.data
                        );
                        this.$emit("study-updated", this.study);
                    });
            });
        },
        saveMolecule() {
            this.ensureStructureSearchEditor(() => {
                if (!this.editor) {
                    return;
                }
                const molfile = this.editor.getMolFile();
                this.standardizeMolecules(molfile).then((response) => {
                    this.associateMoleculeToStudy(response.data);
                });
            });
        },
        standardizeMolecules(molfile) {
            return axios.post(this.chemistryStandardizeUrl, molfile);
        },
        associateMoleculeToStudy(molecule) {
            const payload = {
                InChI: molecule.inchi,
                InChIKey: molecule.inchikey,
                mol: molecule.standardized_mol,
                canonical_smiles: molecule.canonical_smiles,
                composition_mode: this.compositionSampleType,
            };

            if (this.compositionSampleType === "unknown") {
                payload.percentage = null;
            } else if (this.compositionSampleType === "mixture") {
                const mixturePayload =
                    this.$refs.mixtureCompositionForm?.mixturePayload?.();
                if (!mixturePayload?.basis || mixturePayload.value == null) {
                    return;
                }
                Object.assign(payload, mixturePayload);
            } else {
                payload.percentage = this.percentage;
            }

            axios
                .post(
                    "/dashboard/studies/" + this.study.id + "/molecule",
                    payload
                )
                .then((response) => {
                    applySampleMoleculeResponse(
                        this.study.sample,
                        response.data
                    );
                    this.syncCompositionSampleType(this.study.sample);
                    this.$nextTick(() => {
                        this.percentage = Math.min(
                            99.99,
                            this.compositionSliderMax
                        );
                        this.$refs.mixtureCompositionForm?.resetDraft?.();
                    });
                    if (this.editor) {
                        this.editor.setSmiles("");
                        this.syncEditorHasStructure();
                    }
                    this.$emit("study-updated", this.study);
                });
        },
        saveMixtureMetadata(metadata) {
            if (!this.study?.sample?.mixture_composition || !metadata?.basis) {
                return;
            }

            axios
                .put(
                    "/dashboard/studies/" +
                        this.study.id +
                        "/mixture-composition",
                    metadata
                )
                .then((response) => {
                    applySampleMoleculeResponse(
                        this.study.sample,
                        response.data
                    );
                    this.$emit("study-updated", this.study);
                });
        },
        getMixtureComponentForMolecule(molecule) {
            const components =
                this.study?.sample?.mixture_composition?.components ?? [];

            return components.find(
                (component) => component.molecule_id === molecule.id
            );
        },
        syncCompositionSampleType(sample) {
            const molecules = sample?.molecules ?? [];
            if (molecules.length === 0) {
                this.compositionSampleType = "pure";
                return;
            }

            if (sample?.mixture_composition) {
                this.compositionSampleType = "mixture";
                return;
            }

            const allUnknown = molecules.every((entry) =>
                this.isCompositionPercentUnknown(
                    entry.pivot?.percentage_composition
                )
            );
            this.compositionSampleType = allUnknown ? "unknown" : "mixture";
        },
        formatMixtureValue,
    },
};
</script>
