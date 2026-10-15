<template>
    <div class="bg-white">
        <Head title="NMR Prediction - nmrXiv"></Head>
        <FlashMessages />
        <main>
            <div class="relative">
                <PublicSiteHeader />

                <div
                    class="border-b border-amber-200 bg-amber-50"
                    role="status"
                >
                    <div
                        class="mx-auto flex max-w-7xl items-start gap-3 px-4 py-4 sm:px-6 lg:px-8"
                    >
                        <ExclamationTriangleIcon
                            class="mt-0.5 h-5 w-5 shrink-0 text-amber-600"
                            aria-hidden="true"
                        />
                        <p class="text-sm leading-relaxed text-amber-950">
                            <span class="font-semibold"
                                >Experimental feature.</span
                            >
                            Please use NMR Spectrum Prediction with caution. It
                            is still under development, and the results should
                            be verified independently before you rely on them.
                            If you need assistance, please contact us at
                            <a
                                href="mailto:info.nmrxiv@uni-jena.de?subject=NMR%20Spectrum%20Prediction"
                                class="font-medium underline decoration-amber-400 underline-offset-2 hover:text-amber-800"
                                >info.nmrxiv@uni-jena.de</a
                            >
                            or through the
                            <a
                                href="https://helpdesk.nfdi4chem.de/"
                                class="font-medium underline decoration-amber-400 underline-offset-2 hover:text-amber-800"
                                target="_blank"
                                rel="noopener noreferrer"
                                >NFDI4Chem Help Desk</a
                            >.
                        </p>
                    </div>
                </div>

                <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                    <div class="mb-12 text-center">
                        <h1
                            class="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl"
                        >
                            NMR Spectrum Prediction
                        </h1>
                        <p
                            class="mx-auto mt-3 max-w-md text-base text-gray-500 sm:text-lg md:mt-5 md:max-w-3xl md:text-xl"
                        >
                            Fast and accurate NMR spectra predictions from
                            chemical structures
                        </p>
                    </div>

                    <div class="mx-auto max-w-6xl">
                        <!-- Structure input (hidden while predicting / showing results) -->
                        <div
                            v-show="!isPredicting && !showResults"
                            class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-xl"
                        >
                            <div class="px-6 py-8 sm:p-10">
                                <div class="mb-6">
                                    <h2
                                        class="text-2xl font-bold text-gray-900"
                                    >
                                        Draw or Import Structure
                                    </h2>
                                    <p class="mt-2 text-gray-600">
                                        Draw, paste, or import a chemical
                                        structure for NMR prediction
                                    </p>
                                </div>

                                <div
                                    class="mb-6 grid grid-cols-1 gap-3 md:grid-cols-2"
                                >
                                    <label
                                        class="relative flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:border-gray-400 hover:bg-gray-50"
                                        :class="
                                            isDragging
                                                ? 'border-gray-900 bg-gray-50'
                                                : ''
                                        "
                                        @dragover.prevent="isDragging = true"
                                        @dragleave.prevent="isDragging = false"
                                        @drop.prevent="handleDrop"
                                    >
                                        <svg
                                            class="mr-2 h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                            />
                                        </svg>
                                        Drop or select MOL/SDF
                                        <input
                                            ref="fileInput"
                                            type="file"
                                            accept=".mol,.sdf,.sd"
                                            class="sr-only"
                                            @change="handleFileSelect"
                                        />
                                    </label>

                                    <button
                                        type="button"
                                        class="flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                                        @click="pasteFromClipboard"
                                    >
                                        <svg
                                            class="mr-2 h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                            />
                                        </svg>
                                        Paste from Clipboard
                                    </button>
                                </div>

                                <div
                                    id="predictionEditor"
                                    class="mb-6 w-full rounded-xl border border-gray-200 bg-white shadow-sm"
                                    style="height: 450px"
                                />

                                <div class="mb-8">
                                    <h3
                                        class="mb-4 text-lg font-semibold text-gray-900"
                                    >
                                        Prediction Type
                                    </h3>
                                    <div
                                        class="grid grid-cols-1 gap-4 md:grid-cols-3"
                                    >
                                        <label
                                            for="prediction-type-1h"
                                            class="relative flex cursor-pointer items-start rounded-xl border-2 border-gray-200 bg-white p-4 transition-all hover:border-gray-300"
                                            :class="
                                                predictionType === '1h'
                                                    ? 'border-gray-900 bg-gray-50'
                                                    : ''
                                            "
                                        >
                                            <input
                                                id="prediction-type-1h"
                                                v-model="predictionType"
                                                name="prediction-type"
                                                value="1h"
                                                type="radio"
                                                class="sr-only"
                                            />
                                            <div class="flex-1">
                                                <span
                                                    class="block text-sm font-semibold text-gray-900"
                                                >
                                                    <sup>1</sup>H NMR
                                                </span>
                                                <span
                                                    class="mt-1 block text-xs text-gray-500"
                                                >
                                                    Proton NMR prediction
                                                </span>
                                            </div>
                                        </label>

                                        <label
                                            for="prediction-type-13c"
                                            class="relative flex cursor-pointer items-start rounded-xl border-2 border-gray-200 bg-white p-4 transition-all hover:border-gray-300"
                                            :class="
                                                predictionType === '13c'
                                                    ? 'border-gray-900 bg-gray-50'
                                                    : ''
                                            "
                                        >
                                            <input
                                                id="prediction-type-13c"
                                                v-model="predictionType"
                                                name="prediction-type"
                                                value="13c"
                                                type="radio"
                                                class="sr-only"
                                            />
                                            <div class="flex-1">
                                                <span
                                                    class="block text-sm font-semibold text-gray-900"
                                                >
                                                    <sup>13</sup>C NMR
                                                </span>
                                                <span
                                                    class="mt-1 block text-xs text-gray-500"
                                                >
                                                    Carbon-13 NMR prediction
                                                </span>
                                            </div>
                                        </label>

                                        <label
                                            for="prediction-type-both"
                                            class="relative flex cursor-pointer items-start rounded-xl border-2 border-gray-200 bg-white p-4 transition-all hover:border-gray-300"
                                            :class="
                                                predictionType === 'both'
                                                    ? 'border-gray-900 bg-gray-50'
                                                    : ''
                                            "
                                        >
                                            <input
                                                id="prediction-type-both"
                                                v-model="predictionType"
                                                name="prediction-type"
                                                value="both"
                                                type="radio"
                                                class="sr-only"
                                            />
                                            <div class="flex-1">
                                                <span
                                                    class="block text-sm font-semibold text-gray-900"
                                                >
                                                    Both
                                                </span>
                                                <span
                                                    class="mt-1 block text-xs text-gray-500"
                                                >
                                                    <sup>1</sup>H and
                                                    <sup>13</sup>C NMR
                                                </span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex flex-col items-center gap-3">
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-lg bg-gray-900 px-8 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
                                        @click="predictSpectrum"
                                    >
                                        Predict Spectrum
                                    </button>
                                    <p
                                        v-if="errorMessage"
                                        class="text-center text-sm text-red-600"
                                        role="alert"
                                    >
                                        {{ errorMessage }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Loading state -->
                        <div
                            v-if="isPredicting"
                            class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-xl"
                            role="status"
                            aria-live="polite"
                        >
                            <div
                                class="flex flex-col items-center justify-center px-6 py-24 sm:py-32"
                            >
                                <svg
                                    class="h-10 w-10 animate-spin text-gray-900"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    />
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    />
                                </svg>
                                <p
                                    class="mt-4 text-sm font-medium text-gray-900"
                                >
                                    Predicting spectrum…
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    This usually takes a few seconds
                                </p>
                            </div>
                        </div>

                        <!-- Results: 2D structure (left) + NMRium (right) -->
                        <div
                            v-if="showResults && !isPredicting"
                            class="relative overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-xl"
                        >
                            <div
                                class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-6 py-4"
                            >
                                <div>
                                    <h2
                                        class="text-lg font-semibold text-gray-900"
                                    >
                                        Predicted Spectrum
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Explore the prediction in NMRium
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                                    @click="resetToEditor"
                                >
                                    Predict another
                                </button>
                            </div>
                            <div
                                class="grid grid-cols-1 items-start gap-4 p-4 pb-20 sm:p-6 sm:pb-20 lg:grid-cols-10"
                            >
                                <div class="flex flex-col gap-3 lg:col-span-3">
                                    <div
                                        class="flex min-h-[320px] flex-col rounded-md border border-gray-200 bg-white p-3"
                                    >
                                        <p
                                            class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500"
                                        >
                                            Structure
                                        </p>
                                        <div
                                            class="flex flex-1 items-center justify-center overflow-hidden"
                                        >
                                            <!-- OCL SVG is generated locally — do not run through sanitizeHtml (it strips <svg>). -->
                                            <div
                                                v-if="predictedStructureSvg"
                                                ref="structurePanel"
                                                class="predict-mol flex w-full max-w-full items-center justify-center [&_svg]:h-auto [&_svg]:max-w-full"
                                                @click="onStructureClick"
                                                v-html="predictedStructureSvg"
                                            />
                                            <p
                                                v-else
                                                class="text-sm text-gray-400"
                                            >
                                                Structure unavailable
                                            </p>
                                        </div>
                                        <p
                                            class="mt-2 text-center text-xs text-gray-500"
                                        >
                                            Click an atom to highlight its peak
                                        </p>
                                    </div>
                                    <div
                                        class="rounded-md border border-gray-200 bg-white p-3"
                                    >
                                        <p
                                            class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500"
                                        >
                                            Parameters
                                        </p>
                                        <dl class="space-y-1.5 text-xs">
                                            <div
                                                v-for="row in predictionParameterRows"
                                                :key="row.label"
                                                class="flex items-baseline justify-between gap-2"
                                            >
                                                <dt
                                                    class="shrink-0 text-gray-500"
                                                >
                                                    {{ row.label }}
                                                </dt>
                                                <dd
                                                    class="text-right font-medium text-gray-900"
                                                >
                                                    {{ row.value }}
                                                </dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>
                                <div class="min-w-0 lg:col-span-7">
                                    <div
                                        v-if="predictedNuclei.length > 1"
                                        class="mb-2 flex flex-wrap gap-2"
                                        role="tablist"
                                        aria-label="Predicted spectra"
                                    >
                                        <button
                                            v-for="nucleus in predictedNuclei"
                                            :key="nucleus"
                                            type="button"
                                            role="tab"
                                            class="inline-flex items-center rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors"
                                            :class="
                                                activePredictedTab === nucleus
                                                    ? 'border-gray-900 bg-gray-900 text-white'
                                                    : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'
                                            "
                                            :aria-selected="
                                                activePredictedTab === nucleus
                                            "
                                            @click="
                                                selectPredictedSpectrumTab(
                                                    nucleus
                                                )
                                            "
                                        >
                                            {{ formatNucleusLabel(nucleus) }}
                                        </button>
                                    </div>
                                    <iframe
                                        name="PredictionNMRiumIframe"
                                        frameborder="0"
                                        allowfullscreen
                                        class="w-full rounded-md border"
                                        style="height: 70vh; min-height: 480px"
                                        :src="nmriumIframeSrc"
                                        @load="onNmriumIframeLoad"
                                    />
                                </div>
                            </div>
                            <a
                                href="https://nmrshiftdb.nmr.uni-koeln.de/"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="absolute bottom-3 left-3 z-10 flex flex-col items-start gap-1 rounded-md bg-white px-2.5 py-1.5 no-underline"
                            >
                                <span
                                    class="text-[11px] font-medium uppercase tracking-wide text-gray-400"
                                >
                                    Powered by
                                </span>
                                <img
                                    src="/img/nmrshiftdb-logo.png"
                                    alt="NMRShiftDB"
                                    class="h-12 w-auto object-contain"
                                />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <Footer />
    </div>
</template>

<script>
import { Head } from "@inertiajs/vue3";
import { ExclamationTriangleIcon } from "@heroicons/vue/24/outline";
import { markRaw } from "vue";
import FlashMessages from "@/Shared/FlashMessages.vue";
import Footer from "@/Shared/Footer.vue";
import PublicSiteHeader from "@/Shared/PublicSiteHeader.vue";
import OCL from "openchemlib";
import { createStructureEditor } from "@/Utils/structureEditor";
import {
    postNmriumLoad,
    requestClearHighlight,
    requestHighlight,
    requestSelectTab,
    resolveNmriumTargetOrigin,
} from "@/Utils/nmriumTabPreference.js";
import {
    attachPredictionPeaks,
    interactiveStructureSvg,
} from "@/Utils/predictionHighlight.js";

const SPECTRA_BY_TYPE = {
    "1h": ["proton"],
    "13c": ["carbon"],
    both: ["proton", "carbon"],
};

const PREDICT_TIMEOUT_MS = 130000;

/** Defaults match NMRKit nmrshift OpenAPI options. */
const DEFAULT_NMRSHIFT_OPTIONS = {
    solvent: "Dimethylsulphoxide-D6 (DMSO-D6, C2D6SO)",
    frequency: 400,
    lineWidth: 1,
    nbPoints: 1024,
    peakShape: "lorentzian",
    tolerance: 0.001,
};

const SPECTRUM_LABELS = {
    proton: "¹H",
    carbon: "¹³C",
};

export default {
    components: {
        Head,
        ExclamationTriangleIcon,
        FlashMessages,
        Footer,
        PublicSiteHeader,
    },
    props: {
        nmrPredictUrl: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            editor: null,
            predictionType: "1h",
            isDragging: false,
            isPredicting: false,
            errorMessage: "",
            showResults: false,
            predictedMolfile: "",
            predictedStructureSvg: "",
            predictedNuclei: [],
            activePredictedTab: null,
            pendingNmriumPayload: null,
            atomPeaks: null,
            selectedAtomIndices: [],
            nmriumIframeReady: false,
            nmriumPayloadPosted: false,
            nmriumMessageHandler: null,
            deliveryTimeoutIds: [],
            nmriumInstanceId:
                typeof crypto !== "undefined" && crypto.randomUUID
                    ? crypto.randomUUID()
                    : `${Date.now()}-${Math.random().toString(36).slice(2)}`,
        };
    },
    computed: {
        nmriumIframeSrc() {
            const fallback =
                "https://nmriumdev.nmrxiv.org?defaultEmptyMessage=''&workspace=embedded";
            let base = this.$page.props.nmriumURL
                ? String(this.$page.props.nmriumURL)
                : fallback;

            // Force embedded workspace so the side panels stay collapsed.
            try {
                const url = new URL(base, window.location.origin);
                url.searchParams.set("workspace", "embedded");
                base = url.toString();
            } catch {
                if (/[?&]workspace=/.test(base)) {
                    base = base.replace(/([?&]workspace=)[^&]*/i, "$1embedded");
                } else {
                    const sep = base.includes("?") ? "&" : "?";
                    base = `${base}${sep}workspace=embedded`;
                }
            }

            if (/[?&]id=/.test(base)) {
                return base.replace(
                    /([?&]id=)[^&]*/i,
                    `$1${this.nmriumInstanceId}`
                );
            }

            const sep = base.includes("?") ? "&" : "?";

            return `${base}${sep}id=${this.nmriumInstanceId}`;
        },
        predictionParameterRows() {
            const options = DEFAULT_NMRSHIFT_OPTIONS;
            const spectra = (SPECTRA_BY_TYPE[this.predictionType] ?? []).map(
                (type) => SPECTRUM_LABELS[type] ?? type
            );

            return [
                {
                    label: "Spectra",
                    value: spectra.join(" + ") || "—",
                },
                {
                    label: "Solvent",
                    value: "DMSO-d₆",
                },
                {
                    label: "Frequency",
                    value: `${options.frequency} MHz`,
                },
                {
                    label: "Line width",
                    value: `${options.lineWidth} Hz`,
                },
                {
                    label: "Points",
                    value: String(options.nbPoints),
                },
                {
                    label: "Peak shape",
                    value: options.peakShape,
                },
            ];
        },
    },
    mounted() {
        this.attachNmriumMessageListener();
        this.$nextTick(async () => {
            this.editor = await createStructureEditor("predictionEditor");
        });
    },
    beforeUnmount() {
        this.clearDeliveryTimeouts();
        this.detachNmriumMessageListener();
        if (this.editor?.destroy) {
            this.editor.destroy();
        }
    },
    methods: {
        attachNmriumMessageListener() {
            if (this.nmriumMessageHandler) {
                return;
            }

            this.nmriumMessageHandler = (event) => {
                if (event.origin !== this.nmriumTargetOrigin()) {
                    return;
                }

                const actionType = event.data?.data?.state?.data?.actionType;
                if (
                    event.data?.type === "nmr-wrapper:data-change" &&
                    actionType === "INITIATE" &&
                    this.pendingNmriumPayload &&
                    !this.nmriumPayloadPosted
                ) {
                    this.postPredictionToNmrium();
                }
            };

            window.addEventListener("message", this.nmriumMessageHandler);
        },
        detachNmriumMessageListener() {
            if (!this.nmriumMessageHandler) {
                return;
            }

            window.removeEventListener("message", this.nmriumMessageHandler);
            this.nmriumMessageHandler = null;
        },
        clearDeliveryTimeouts() {
            this.deliveryTimeoutIds.forEach((id) => window.clearTimeout(id));
            this.deliveryTimeoutIds = [];
        },
        async handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) {
                return;
            }
            await this.loadFile(file);
        },
        async handleDrop(event) {
            this.isDragging = false;
            const file = event.dataTransfer.files[0];
            if (!file) {
                return;
            }
            await this.loadFile(file);
        },
        async loadFile(file) {
            if (!this.editor) {
                return;
            }

            const validExtensions = [".mol", ".sdf", ".sd"];
            const fileName = file.name.toLowerCase();
            const isValid = validExtensions.some((ext) =>
                fileName.endsWith(ext)
            );

            if (!isValid) {
                this.errorMessage = "Please upload a MOL or SDF file.";
                return;
            }

            try {
                const text = await file.text();
                this.editor.setMolFile(text);
                this.errorMessage = "";
            } catch (error) {
                console.error("Error loading file:", error);
                this.errorMessage = "Error loading file.";
            }
        },
        async pasteFromClipboard() {
            if (!this.editor) {
                return;
            }

            try {
                const text = await navigator.clipboard.readText();

                try {
                    const mol = OCL.Molecule.fromSmiles(text.trim());
                    this.editor.setMolFile(mol.toMolfile());
                    this.errorMessage = "";
                    return;
                } catch {
                    if (text.includes("M  END") || text.includes("$$$$")) {
                        this.editor.setMolFile(text);
                        this.errorMessage = "";
                    } else {
                        this.errorMessage =
                            "Clipboard content is not a valid SMILES or MOL format.";
                    }
                }
            } catch (error) {
                console.error("Error reading clipboard:", error);
                this.errorMessage =
                    "Unable to read clipboard. Please allow clipboard access.";
            }
        },
        spectraForPredictionType() {
            return SPECTRA_BY_TYPE[this.predictionType] ?? ["proton"];
        },
        nmriumTargetOrigin() {
            return resolveNmriumTargetOrigin(this.$page.props.nmriumURL);
        },
        predictionNmriumWindow() {
            const element = document.querySelector(
                'iframe[name="PredictionNMRiumIframe"]'
            );

            if (element?.contentWindow?.postMessage) {
                return element.contentWindow;
            }

            const named = window.frames.PredictionNMRiumIframe;

            return named?.postMessage ? named : null;
        },
        postPredictionToNmrium() {
            const iframe = this.predictionNmriumWindow();
            if (!iframe || !this.pendingNmriumPayload) {
                return false;
            }

            postNmriumLoad(
                iframe,
                {
                    data: this.pendingNmriumPayload,
                    type: "nmrium",
                },
                this.nmriumTargetOrigin(),
                this.activePredictedTab
            );
            this.nmriumPayloadPosted = true;

            return true;
        },
        deliverPredictionToNmrium() {
            this.clearDeliveryTimeouts();
            this.nmriumPayloadPosted = false;
            this.postPredictionToNmrium();

            [400, 1000, 2000, 4000].forEach((delayMs) => {
                const id = window.setTimeout(() => {
                    if (!this.pendingNmriumPayload) {
                        return;
                    }
                    this.nmriumPayloadPosted = false;
                    this.postPredictionToNmrium();
                }, delayMs);
                this.deliveryTimeoutIds.push(id);
            });
        },
        onNmriumIframeLoad() {
            this.nmriumIframeReady = true;
            if (this.pendingNmriumPayload && !this.nmriumPayloadPosted) {
                this.postPredictionToNmrium();
            }
        },
        ensureMolfileHeader(molfile, titleHint = "Structure") {
            if (!molfile) {
                return molfile;
            }

            const normalized = molfile
                .replace(/\r\n/g, "\n")
                .replace(/\r/g, "\n");
            const lines = normalized.split("\n");
            let countsLineIdx = null;

            for (let i = 0; i < lines.length; i++) {
                if (/V2000\s*$/.test(lines[i]) || /V3000\s*$/.test(lines[i])) {
                    countsLineIdx = i;
                    break;
                }
            }

            if (countsLineIdx === null || countsLineIdx >= 3) {
                return molfile;
            }

            const missing = 3 - countsLineIdx;
            const prepend = [titleHint];
            for (let i = 1; i < missing; i++) {
                prepend.push("");
            }

            return [...prepend, ...lines].join("\n");
        },
        molfileToSvg(molfile) {
            if (!molfile) {
                return "";
            }

            try {
                return interactiveStructureSvg(molfile, OCL);
            } catch (error) {
                console.error("Unable to render structure SVG:", error);
                return "";
            }
        },
        buildNmriumPayload(responseData) {
            const payload =
                responseData && typeof responseData === "object"
                    ? JSON.parse(JSON.stringify(responseData))
                    : { data: { spectra: [] } };

            if (!payload.data || typeof payload.data !== "object") {
                payload.data = { spectra: [] };
            }

            // Spectrum only. The 2D structure lives beside the iframe.
            payload.settings = {
                display: {
                    general: {
                        hidePanelOnLoad: true,
                        hidePanelsBar: true,
                        hideHelp: true,
                        hideLogs: true,
                        hideMaximize: true,
                        hideWorkspaces: true,
                        hideGeneralSettings: true,
                    },
                },
            };

            this.atomPeaks = attachPredictionPeaks(payload, {
                frequency: DEFAULT_NMRSHIFT_OPTIONS.frequency,
                lineWidth: DEFAULT_NMRSHIFT_OPTIONS.lineWidth,
                tolerance: DEFAULT_NMRSHIFT_OPTIONS.tolerance,
            });

            return payload;
        },
        onStructureClick(event) {
            const hit = event.target?.closest?.(".atom-hit");
            if (!hit || !event.currentTarget.contains(hit)) {
                return;
            }

            const atom = Number(hit.getAttribute("data-atom"));
            if (Number.isFinite(atom)) {
                this.selectAtom(atom);
            }
        },
        markStructureSelection(atomIndices) {
            this.selectedAtomIndices = Array.isArray(atomIndices)
                ? atomIndices.slice()
                : [];
            const selected = new Set(this.selectedAtomIndices);
            const panel = this.$refs.structurePanel;
            if (!panel) {
                return;
            }

            panel
                .querySelectorAll(".atom-glow, .atom-hit")
                .forEach((element) => {
                    const atom = Number(element.getAttribute("data-atom"));
                    element.classList.toggle("is-selected", selected.has(atom));
                });
        },
        selectAtom(index) {
            const atomIndex = Number(index);
            if (!Number.isFinite(atomIndex) || atomIndex < 1) {
                return;
            }

            const peak = this.atomPeaks?.get?.(atomIndex);
            const iframe = this.predictionNmriumWindow();
            const origin = this.nmriumTargetOrigin();

            if (!peak) {
                requestClearHighlight(iframe, origin);
                this.markStructureSelection([]);
                return;
            }

            if (peak.nucleus) {
                this.selectPredictedSpectrumTab(peak.nucleus);
            }
            requestHighlight(iframe, peak.highlightIds, origin);
            this.markStructureSelection(peak.atoms);
        },
        formatNucleusLabel(nucleus) {
            if (nucleus === "1H") {
                return "¹H";
            }
            if (nucleus === "13C") {
                return "¹³C";
            }

            return nucleus;
        },
        nucleiFromPayload(payload) {
            const spectra = payload?.data?.spectra;
            if (!Array.isArray(spectra)) {
                return [];
            }

            const nuclei = [];
            for (const spectrum of spectra) {
                const nucleus = spectrum?.info?.nucleus;
                if (
                    typeof nucleus === "string" &&
                    nucleus &&
                    !nuclei.includes(nucleus)
                ) {
                    nuclei.push(nucleus);
                }
            }

            return nuclei;
        },
        selectPredictedSpectrumTab(nucleus) {
            if (!nucleus) {
                return;
            }

            this.activePredictedTab = nucleus;

            const iframe = this.predictionNmriumWindow();
            if (!iframe) {
                return;
            }

            requestSelectTab(iframe, nucleus, this.nmriumTargetOrigin());
        },
        storeNmriumPayload(responseData, molfile) {
            const normalizedMolfile = this.ensureMolfileHeader(
                molfile,
                "Structure"
            );
            this.predictedMolfile = normalizedMolfile;
            this.predictedStructureSvg = this.molfileToSvg(normalizedMolfile);
            this.pendingNmriumPayload = markRaw(
                this.buildNmriumPayload(responseData)
            );
            this.predictedNuclei = this.nucleiFromPayload(
                this.pendingNmriumPayload
            );
            this.activePredictedTab = this.predictedNuclei[0] ?? null;
        },
        resetToEditor() {
            this.clearDeliveryTimeouts();
            this.showResults = false;
            this.predictedMolfile = "";
            this.predictedStructureSvg = "";
            this.predictedNuclei = [];
            this.activePredictedTab = null;
            this.pendingNmriumPayload = null;
            this.atomPeaks = null;
            this.selectedAtomIndices = [];
            this.nmriumIframeReady = false;
            this.nmriumPayloadPosted = false;
            this.errorMessage = "";
            this.nmriumInstanceId =
                typeof crypto !== "undefined" && crypto.randomUUID
                    ? crypto.randomUUID()
                    : `${Date.now()}-${Math.random().toString(36).slice(2)}`;
        },
        formatPredictError(error) {
            const status = error?.response?.status;
            const detail = error?.response?.data?.detail;

            if (status === 408) {
                return "Prediction timed out. Try a smaller molecule or try again.";
            }

            if (status === 422) {
                if (typeof detail === "string" && detail.trim() !== "") {
                    return detail;
                }
                if (this.isPredictionServiceFailure(detail)) {
                    return "The prediction service is temporarily unavailable. Please try again.";
                }
                if (
                    detail &&
                    typeof detail === "object" &&
                    typeof detail.message === "string" &&
                    detail.message.trim() !== ""
                ) {
                    return "Prediction failed for this structure. Try a single spectrum type, or simplify the structure.";
                }
                const message = error?.response?.data?.message;
                if (typeof message === "string" && message.trim() !== "") {
                    return message;
                }
                return "Invalid structure or prediction error. Check the structure and try again.";
            }

            if (status === 500 || status === 503) {
                const message = error?.response?.data?.message;
                if (typeof message === "string" && message.trim() !== "") {
                    return message;
                }
                return "Prediction service is temporarily unavailable.";
            }

            if (error?.code === "ECONNABORTED") {
                return "Prediction timed out. Try again.";
            }

            if (error?.message && /could not be cloned/i.test(error.message)) {
                return "Unable to load the prediction into NMRium. Please try again.";
            }

            return "Unable to reach the prediction service. Please try again.";
        },
        isPredictionServiceFailure(detail) {
            const text =
                typeof detail === "string"
                    ? detail
                    : JSON.stringify(detail || "");

            return /ECONNREFUSED|ECONNRESET|ENOTFOUND|NMR CLI command failed|certificate/i.test(
                text
            );
        },
        mergeNmriumPredictions(payloads) {
            const merged = {
                data: { spectra: [] },
                version: null,
            };

            for (const payload of payloads) {
                if (!payload || typeof payload !== "object") {
                    continue;
                }
                if (merged.version == null && payload.version != null) {
                    merged.version = payload.version;
                }
                const spectra = payload.data?.spectra;
                if (Array.isArray(spectra)) {
                    merged.data.spectra.push(...spectra);
                }
            }

            return merged;
        },
        async requestNmriumPrediction(structure, spectra) {
            const response = await axios.post(
                this.nmrPredictUrl,
                {
                    engine: "nmrshift",
                    structure,
                    spectra,
                    options: { ...DEFAULT_NMRSHIFT_OPTIONS },
                },
                { timeout: PREDICT_TIMEOUT_MS }
            );

            return response.data;
        },
        async requestNmriumPredictionWithFallback(structure, spectra) {
            try {
                return await this.requestNmriumPrediction(structure, spectra);
            } catch (error) {
                // Some NMRKit deployments fail when predicting multiple spectra
                // in one request; fall back to one request per spectrum type.
                if (spectra.length <= 1 || error?.response?.status !== 422) {
                    throw error;
                }

                const parts = [];
                for (const spectrum of spectra) {
                    parts.push(
                        await this.requestNmriumPrediction(structure, [
                            spectrum,
                        ])
                    );
                }

                return this.mergeNmriumPredictions(parts);
            }
        },
        async predictSpectrum() {
            if (!this.editor) {
                this.errorMessage = "Structure editor is not ready yet.";
                return;
            }

            if (this.isPredicting) {
                return;
            }

            let molfile;
            try {
                molfile = this.editor.getMolFile();
                const smiles = this.editor.getSmiles();
                if (!molfile || !smiles || smiles.trim() === "") {
                    this.errorMessage =
                        "Please draw or import a chemical structure first.";
                    return;
                }
            } catch (error) {
                console.error("Error reading structure:", error);
                this.errorMessage = "Error reading structure from editor.";
                return;
            }

            const normalizedMolfile = this.ensureMolfileHeader(
                molfile,
                "Structure"
            );

            this.isPredicting = true;
            this.showResults = false;
            this.errorMessage = "";
            this.predictedMolfile = "";
            this.predictedStructureSvg = "";
            this.predictedNuclei = [];
            this.activePredictedTab = null;
            this.pendingNmriumPayload = null;
            this.atomPeaks = null;
            this.selectedAtomIndices = [];
            this.nmriumPayloadPosted = false;
            this.nmriumIframeReady = false;

            try {
                const responseData =
                    await this.requestNmriumPredictionWithFallback(
                        normalizedMolfile,
                        this.spectraForPredictionType()
                    );

                this.storeNmriumPayload(responseData, normalizedMolfile);
                this.showResults = true;

                await this.$nextTick();
                this.deliverPredictionToNmrium();
            } catch (error) {
                console.error("Prediction failed:", error);
                this.errorMessage = this.formatPredictError(error);
                this.showResults = false;
            } finally {
                this.isPredicting = false;
            }
        },
    },
};
</script>

<style>
.predict-mol .atom-glow {
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s ease;
}

.predict-mol .atom-glow.is-selected {
    opacity: 1;
    filter: blur(2.5px);
}

.predict-mol .atom-hit {
    cursor: pointer;
    fill: transparent;
    stroke: none;
    transition: fill 0.12s ease;
}

.predict-mol .atom-hit:hover {
    fill: hsl(48 100% 50% / 0.18);
}

.predict-mol .atom-hit[data-elem="H"]:hover {
    fill: hsl(199 70% 60% / 0.22);
}

.predict-mol .atom-hit[data-elem="O"]:hover {
    fill: hsl(6 78% 57% / 0.2);
}

.predict-mol .atom-hit[data-elem="N"]:hover {
    fill: hsl(217 70% 60% / 0.2);
}
</style>
