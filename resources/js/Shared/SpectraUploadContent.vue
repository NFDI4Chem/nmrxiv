<template>
    <div class="flex flex-col">
        <div v-if="!compact" class="mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Spectra Search</h2>
            <p class="mt-2 text-gray-600">
                Drop a spectrum and we will pick its peaks for you
            </p>
        </div>

        <div
            class="relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed text-center transition-all"
            :class="[
                compact ? 'px-4 py-5' : 'min-h-[320px] p-8',
                isDragging
                    ? 'border-gray-900 bg-gray-50'
                    : 'border-gray-300 bg-gray-50/50',
            ]"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
        >
            <input
                ref="fileInput"
                type="file"
                :accept="accept"
                class="sr-only"
                tabindex="-1"
                aria-hidden="true"
                @change="handleFileSelect"
            />
            <input
                ref="folderInput"
                type="file"
                webkitdirectory
                class="sr-only"
                tabindex="-1"
                aria-hidden="true"
                @change="handleFileSelect"
            />

            <template v-if="isBusy">
                <p class="text-sm font-medium text-gray-900" aria-live="polite">
                    {{ busyLabel }}
                </p>
                <div
                    class="mt-3 h-1.5 w-full max-w-xs overflow-hidden rounded-full bg-gray-200"
                    role="progressbar"
                    :aria-valuenow="status === 'parsing' ? undefined : progress"
                    aria-valuemin="0"
                    aria-valuemax="100"
                >
                    <div
                        v-if="status === 'parsing'"
                        class="h-full w-1/3 animate-pulse rounded-full bg-gray-900"
                    />
                    <div
                        v-else
                        class="h-full rounded-full bg-gray-900 transition-all"
                        :style="{ width: `${progress}%` }"
                    />
                </div>
                <p class="mt-2 text-xs text-gray-500">{{ fileLabel }}</p>
                <button
                    type="button"
                    class="mt-3 text-xs font-medium text-gray-600 underline underline-offset-2 hover:text-gray-900"
                    @click="cancel"
                >
                    Cancel
                </button>
            </template>

            <template v-else>
                <ArrowUpTrayIcon
                    class="text-gray-400"
                    :class="compact ? 'h-6 w-6' : 'h-10 w-10'"
                    aria-hidden="true"
                />
                <p
                    class="mt-2 font-semibold text-gray-900"
                    :class="compact ? 'text-sm' : 'text-lg'"
                >
                    Drop a spectrum here
                </p>
                <p class="mt-1 max-w-md text-xs text-gray-500">
                    A Bruker, Varian or JEOL experiment folder, a zip of it, or
                    a JCAMP-DX file. We pick the peaks automatically; you can
                    edit them before searching.
                </p>
                <div class="mt-3 flex flex-wrap justify-center gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2"
                        @click="fileInput.click()"
                    >
                        Choose file
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2"
                        @click="folderInput.click()"
                    >
                        Choose folder
                    </button>
                </div>
            </template>
        </div>

        <p
            v-if="errorMessage"
            class="mt-2 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700"
            role="alert"
        >
            {{ errorMessage }}
        </p>

        <div v-if="spectra.length > 0" class="mt-3">
            <p class="text-xs font-medium text-gray-700">
                Found {{ spectra.length }}
                {{ spectra.length === 1 ? "spectrum" : "spectra" }} in
                {{ fileLabel }}
            </p>
            <ul
                class="mt-2 divide-y divide-gray-100 rounded-xl border border-gray-200 bg-white"
            >
                <li
                    v-for="spectrum in spectra"
                    :key="spectrum.index"
                    class="flex items-center justify-between gap-3 px-3 py-2"
                >
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-gray-900">
                            <span
                                class="mr-1.5 rounded bg-gray-100 px-1.5 py-0.5 text-xs font-semibold text-gray-700"
                                >{{ nucleusLabel(spectrum)
                                }}<template v-if="experimentLabel(spectrum)">
                                    · {{ experimentLabel(spectrum) }}</template
                                ></span
                            >
                            {{ spectrum.name || "Spectrum" }}
                        </p>
                        <p class="mt-0.5 text-xs text-gray-500">
                            <template v-if="spectrum.supported">
                                {{ peakCount(spectrum) }} peaks
                                <template v-if="spectrum.solvent">
                                    · {{ spectrum.solvent }}</template
                                >
                                <template v-if="spectrum.frequency">
                                    ·
                                    {{ Math.round(spectrum.frequency) }}
                                    MHz</template
                                >
                            </template>
                            <template v-else>
                                Only 1D ¹H and ¹³C spectra can be searched for
                                now
                            </template>
                        </p>
                    </div>
                    <button
                        v-if="spectrum.supported"
                        type="button"
                        class="shrink-0 rounded-lg px-3 py-1.5 text-xs font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900"
                        :class="
                            usedIndexes.includes(spectrum.index)
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                        "
                        :disabled="peakCount(spectrum) === 0"
                        @click="useSpectrum(spectrum)"
                    >
                        {{
                            usedIndexes.includes(spectrum.index)
                                ? "Peaks added"
                                : "Use these peaks"
                        }}
                    </button>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import { computed, ref } from "vue";
import axios from "axios";
import { ArrowUpTrayIcon } from "@heroicons/vue/24/outline";
import {
    entriesFromDataTransfer,
    entriesFromFileList,
} from "@/Utils/droppedEntries.js";
import {
    SPECTRA_UPLOAD_ACCEPT,
    buildSpectraUploadFile,
    parseSpectraUpload,
    spectraUploadErrorMessage,
} from "@/Utils/spectraUpload.js";
import { NUCLEUS_LABELS } from "@/Utils/spectrumQuery.js";

export default {
    components: { ArrowUpTrayIcon },
    props: {
        compact: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["spectrum-selected", "upload-started"],
    setup(props, { emit }) {
        const isDragging = ref(false);
        const fileInput = ref(null);
        const folderInput = ref(null);
        const status = ref("idle");
        const progress = ref(0);
        const fileLabel = ref("");
        const errorMessage = ref(null);
        const spectra = ref([]);
        const usedIndexes = ref([]);
        let controller = null;

        const isBusy = computed(() =>
            ["zipping", "uploading", "parsing"].includes(status.value)
        );

        const busyLabel = computed(
            () =>
                ({
                    zipping: `Preparing folder… ${Math.round(progress.value)}%`,
                    uploading: `Uploading… ${Math.round(progress.value)}%`,
                    parsing:
                        "Processing the spectrum and picking peaks. This can take up to a minute.",
                }[status.value] ?? "")
        );

        const nucleusLabel = (spectrum) =>
            NUCLEUS_LABELS[spectrum.nucleus] ??
            (Array.isArray(spectrum.nucleus)
                ? spectrum.nucleus
                      .map((nucleus) => NUCLEUS_LABELS[nucleus] ?? nucleus)
                      .join("–")
                : spectrum.nucleus || "?");

        const isPlain1d = (spectrum) =>
            !spectrum.experiment ||
            String(spectrum.experiment).toLowerCase() === "1d";

        const experimentLabel = (spectrum) =>
            spectrum.experiment && !isPlain1d(spectrum)
                ? String(spectrum.experiment).toUpperCase()
                : null;

        const peakCount = (spectrum) =>
            spectrum.signals.filter((signal) => !signal.is_solvent).length;

        const useSpectrum = (spectrum) => {
            if (!usedIndexes.value.includes(spectrum.index)) {
                usedIndexes.value = [...usedIndexes.value, spectrum.index];
            }
            emit("spectrum-selected", spectrum);
        };

        const process = async (entries) => {
            controller?.abort();
            controller = new AbortController();
            const { signal } = controller;

            emit("upload-started");
            errorMessage.value = null;
            spectra.value = [];
            usedIndexes.value = [];
            progress.value = 0;
            fileLabel.value =
                entries.length === 1
                    ? entries[0].path.split("/").pop()
                    : entries[0]?.path.split("/")[0] || "your files";

            try {
                status.value = "zipping";
                const file = await buildSpectraUploadFile(entries, {
                    onZipProgress: (percent) => (progress.value = percent),
                });
                if (signal.aborted) {
                    return;
                }

                status.value = "uploading";
                progress.value = 0;
                const detected = await parseSpectraUpload(file, {
                    signal,
                    onUploadProgress: (percent) => {
                        progress.value = percent;
                        if (percent >= 100) {
                            status.value = "parsing";
                        }
                    },
                });

                spectra.value = detected;
                status.value = "done";

                const supported = detected.filter(
                    (spectrum) => spectrum.supported && peakCount(spectrum) > 0
                );
                if (supported.length === 0) {
                    errorMessage.value =
                        "We could not pick peaks from this spectrum. You can still type the peaks below.";
                } else {
                    const firstPerNucleus = new Map();
                    for (const spectrum of supported) {
                        if (
                            isPlain1d(spectrum) &&
                            !firstPerNucleus.has(spectrum.nucleus)
                        ) {
                            firstPerNucleus.set(spectrum.nucleus, spectrum);
                        }
                    }
                    const toApply =
                        firstPerNucleus.size > 0
                            ? [...firstPerNucleus.values()]
                            : supported.slice(0, 1);
                    toApply.forEach(useSpectrum);
                }
            } catch (error) {
                if (axios.isCancel(error) || signal.aborted) {
                    return;
                }
                status.value = "error";
                errorMessage.value = spectraUploadErrorMessage(error);
            }
        };

        const cancel = () => {
            controller?.abort();
            status.value = "idle";
            progress.value = 0;
        };

        const handleFileSelect = (event) => {
            const entries = entriesFromFileList(event.target.files);
            event.target.value = "";
            if (entries.length > 0) {
                process(entries);
            }
        };

        const handleDrop = async (event) => {
            isDragging.value = false;
            const entries = await entriesFromDataTransfer(event.dataTransfer);
            if (entries.length > 0) {
                process(entries);
            }
        };

        return {
            accept: SPECTRA_UPLOAD_ACCEPT,
            isDragging,
            fileInput,
            folderInput,
            status,
            progress,
            fileLabel,
            errorMessage,
            spectra,
            usedIndexes,
            isBusy,
            busyLabel,
            nucleusLabel,
            experimentLabel,
            peakCount,
            useSpectrum,
            cancel,
            handleFileSelect,
            handleDrop,
        };
    },
};
</script>
