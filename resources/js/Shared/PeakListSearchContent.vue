<template>
    <div class="flex flex-col h-full">
        <!-- Title Section -->
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-gray-900">
                Peak List Search
            </h2>
            <p class="mt-2 text-gray-600">
                Search by chemical shift peaks and parameters
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column: Peak Input -->
            <div class="space-y-4">
                <!-- Input Format Toggle -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Input Format
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label
                            class="relative flex items-center p-2.5 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-300 transition-all"
                            :class="inputFormat === 'list' ? 'border-gray-900 bg-gray-50' : ''"
                        >
                            <input
                                v-model="inputFormat"
                                type="radio"
                                value="list"
                                class="sr-only"
                            />
                            <div class="flex-1 text-center">
                                <span class="block text-sm font-medium text-gray-900">
                                    Peak List
                                </span>
                            </div>
                        </label>
                        
                        <label
                            class="relative flex items-center p-2.5 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-300 transition-all"
                            :class="inputFormat === 'acs' ? 'border-gray-900 bg-gray-50' : ''"
                        >
                            <input
                                v-model="inputFormat"
                                type="radio"
                                value="acs"
                                class="sr-only"
                            />
                            <div class="flex-1 text-center">
                                <span class="block text-sm font-medium text-gray-900">
                                    ACS String
                                </span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Peak List Input -->
                <div v-if="inputFormat === 'list'">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-semibold text-gray-900">
                            Chemical Peaks
                        </label>
                        <button
                            v-if="peakList"
                            @click="clearPeaks"
                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                            title="Clear peaks"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <textarea
                        v-model="peakList"
                        placeholder="Enter peaks (one per line)&#10;&#10;Examples:&#10;7.26&#10;3.75;0.5s (with intensity and multiplicity)&#10;56.2;2.2 (13C/1H correlation)&#10;&#10;Diastereotopic pairs:&#10;1.8&#10;+1.6"
                        rows="12"
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent resize-none font-mono"
                    />
                    <div class="mt-2 flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 mb-1">
                                <span class="font-semibold">Format:</span> Shift or Shift;Intensity[Multiplicity]
                            </p>
                            <p class="text-xs text-gray-400">
                                Multiplicity: s (singlet), d (doublet), t (triplet), q (quartet), m (multiplet)
                            </p>
                        </div>
                        <button
                            @click="loadExample"
                            class="flex-shrink-0 text-xs font-medium text-gray-600 hover:text-gray-900 underline transition-colors"
                        >
                            Load example
                        </button>
                    </div>
                </div>

                <!-- ACS String Input -->
                <div v-if="inputFormat === 'acs'">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-semibold text-gray-900">
                            ACS String for NMR
                        </label>
                        <button
                            v-if="acsString"
                            @click="clearACS"
                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                            title="Clear ACS string"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <textarea
                        v-model="acsString"
                        placeholder="Paste ACS-formatted NMR data&#10;&#10;Example:&#10;¹H NMR (500 MHz, CDCl₃) δ 7.26 (m, 2H), 3.75 (s, 3H), 1.45 (d, J = 7.0 Hz, 6H)"
                        rows="12"
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent resize-none"
                    />
                    <div class="mt-2 flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 mb-1">
                                <span class="font-semibold">Format:</span> Standard ACS NMR data format
                            </p>
                            <p class="text-xs text-gray-400">
                                Paste NMR data from supporting information or publications
                            </p>
                        </div>
                        <button
                            @click="loadACSExample"
                            class="flex-shrink-0 text-xs font-medium text-gray-600 hover:text-gray-900 underline transition-colors"
                        >
                            Load example
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Search Parameters -->
            <div class="space-y-4">
                <!-- Spectrum Type -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Spectrum Type
                    </label>
                    <select
                        v-model="spectrumType"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                    >
                        <option value="1H">1H NMR</option>
                        <option value="13C">13C NMR</option>
                        <option value="13C/1H">13C/1H Correlation</option>
                        <option value="15N">15N NMR</option>
                        <option value="19F">19F NMR</option>
                        <option value="31P">31P NMR</option>
                    </select>
                    <p v-if="spectrumType === '13C/1H'" class="mt-1.5 text-xs text-gray-500">
                        Enter pairs as: 13C_shift;1H_shift (e.g., 56.2;2.2)
                    </p>
                </div>

                <!-- Search Type -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Search Type
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label
                            class="relative flex items-center p-3 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-300 transition-all"
                            :class="searchType === 'partial' ? 'border-gray-900 bg-gray-50' : ''"
                        >
                            <input
                                v-model="searchType"
                                type="radio"
                                value="partial"
                                class="sr-only"
                            />
                            <div class="flex-1 text-center">
                                <span class="block text-sm font-medium text-gray-900">
                                    Partial
                                </span>
                                <span class="block text-xs text-gray-500 mt-0.5">
                                    Subspectrum
                                </span>
                            </div>
                        </label>
                        
                        <label
                            class="relative flex items-center p-3 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-300 transition-all"
                            :class="searchType === 'complete' ? 'border-gray-900 bg-gray-50' : ''"
                        >
                            <input
                                v-model="searchType"
                                type="radio"
                                value="complete"
                                class="sr-only"
                            />
                            <div class="flex-1 text-center">
                                <span class="block text-sm font-medium text-gray-900">
                                    Complete
                                </span>
                                <span class="block text-xs text-gray-500 mt-0.5">
                                    Full spectrum
                                </span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Tolerance -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Tolerance ± (ppm)
                    </label>
                    <input
                        v-model.number="tolerance"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="0.01"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                    />
                </div>

                <!-- Frequency -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Frequency (MHz)
                    </label>
                    <select
                        v-model="frequency"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                    >
                        <option value="300">300</option>
                        <option value="400">400</option>
                        <option value="500">500</option>
                        <option value="600">600</option>
                        <option value="700">700</option>
                        <option value="800">800</option>
                        <option value="900">900</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from "vue";

export default {
    emits: ["search-params-updated"],
    setup(props, { emit }) {
        const inputFormat = ref("list");
        const peakList = ref("");
        const acsString = ref("");
        const spectrumType = ref("1H");
        const searchType = ref("partial");
        const tolerance = ref(0.01);
        const frequency = ref("500");

        const loadExample = () => {
            peakList.value = `1.45;0.5d
1.47;0.3d
3.75;1.0s
3.76;0.8t
7.26;0.6m
7.28;0.4m`;
        };

        const loadACSExample = () => {
            acsString.value = `¹H NMR (500 MHz, CDCl₃) δ 7.26 (m, 2H), 3.75 (s, 3H), 1.45 (d, J = 7.0 Hz, 6H)`;
        };

        const clearPeaks = () => {
            peakList.value = "";
        };

        const clearACS = () => {
            acsString.value = "";
        };

        // Emit params whenever they change
        const emitParams = () => {
            emit("search-params-updated", {
                inputFormat: inputFormat.value,
                peaks: peakList.value,
                acsString: acsString.value,
                spectrumType: spectrumType.value,
                searchType: searchType.value,
                tolerance: tolerance.value,
                frequency: frequency.value,
            });
        };

        // Watch for changes (you could use watch() here for auto-emit)
        
        return {
            inputFormat,
            peakList,
            acsString,
            spectrumType,
            searchType,
            tolerance,
            frequency,
            loadExample,
            loadACSExample,
            clearPeaks,
            clearACS,
            emitParams,
        };
    },
};
</script>
