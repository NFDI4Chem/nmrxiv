<template>
    <div class="flex flex-col h-full">
        <!-- Title Section -->
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Metadata Search</h2>
            <p class="mt-2 text-gray-600">
                Search by free text or specific NMR metadata fields
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column: Free Text Search -->
            <div class="space-y-4">
                <div>
                    <label
                        class="block text-sm font-semibold text-gray-900 mb-2"
                    >
                        Free Text Search
                    </label>
                    <textarea
                        v-model="freeText"
                        placeholder="Enter keywords to search across all metadata fields..."
                        rows="4"
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent resize-none"
                    />
                </div>

                <!-- Sample Information -->
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Sample Information
                    </h3>

                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 mb-1.5"
                        >
                            NMR Solvent
                        </label>
                        <input
                            v-model="solvent"
                            type="text"
                            placeholder="e.g., D₂O, CDCl₃, DMSO-d6"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 mb-1.5"
                        >
                            Temperature (K)
                        </label>
                        <input
                            v-model.number="temperature"
                            type="number"
                            step="0.01"
                            placeholder="e.g., 298.15"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 mb-1.5"
                        >
                            Sample Tube Diameter (mm)
                        </label>
                        <select
                            v-model="tubeDiameter"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                        >
                            <option value="">Any</option>
                            <option value="3">3 mm</option>
                            <option value="5">5 mm</option>
                            <option value="10">10 mm</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Right Column: Acquisition Parameters -->
            <div class="space-y-4">
                <!-- Acquisition Parameters -->
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Acquisition Parameters
                    </h3>

                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 mb-1.5"
                        >
                            Acquisition Nucleus
                        </label>
                        <select
                            v-model="acquisitionNucleus"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                        >
                            <option value="">Any</option>
                            <option value="1H">1H</option>
                            <option value="13C">13C</option>
                            <option value="15N">15N</option>
                            <option value="19F">19F</option>
                            <option value="31P">31P</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 mb-1.5"
                        >
                            Proton Frequency (MHz)
                        </label>
                        <select
                            v-model="protonFrequency"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                        >
                            <option value="">Any</option>
                            <option value="300">300</option>
                            <option value="400">400</option>
                            <option value="500">500</option>
                            <option value="600">600</option>
                            <option value="700">700</option>
                            <option value="800">800</option>
                            <option value="900">900</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 mb-1.5"
                        >
                            NMR Method
                        </label>
                        <input
                            v-model="nmrMethod"
                            type="text"
                            placeholder="e.g., HSQC, COSY, NOESY"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 mb-1.5"
                        >
                            Pulse Sequence Name
                        </label>
                        <input
                            v-model="pulseSequence"
                            type="text"
                            placeholder="e.g., zg30, zgpg30"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 mb-1.5"
                        >
                            Number of Scans
                        </label>
                        <input
                            v-model.number="numberOfScans"
                            type="number"
                            placeholder="e.g., 8, 16, 32"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                        />
                    </div>
                </div>

                <!-- Instrument Information -->
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Instrument Information
                    </h3>

                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 mb-1.5"
                        >
                            Manufacturer
                        </label>
                        <select
                            v-model="manufacturer"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                        >
                            <option value="">Any</option>
                            <option value="Bruker">Bruker</option>
                            <option value="JEOL">JEOL</option>
                            <option value="Agilent">Agilent</option>
                            <option value="Varian">Varian</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 mb-1.5"
                        >
                            Instrument Model
                        </label>
                        <input
                            v-model="instrumentModel"
                            type="text"
                            placeholder="e.g., AVANCE III HD"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Clear All Button -->
        <div class="mt-4 flex justify-end">
            <button
                v-if="hasAnyValue"
                class="text-xs font-medium text-gray-600 hover:text-red-600 underline transition-colors"
                @click="clearAll"
            >
                Clear all fields
            </button>
        </div>
    </div>
</template>

<script>
import { ref, computed } from "vue";

export default {
    emits: ["search-params-updated"],
    setup(props, { emit }) {
        // Free text
        const freeText = ref("");

        // Sample information
        const solvent = ref("");
        const temperature = ref(null);
        const tubeDiameter = ref("");

        // Acquisition parameters
        const acquisitionNucleus = ref("");
        const protonFrequency = ref("");
        const nmrMethod = ref("");
        const pulseSequence = ref("");
        const numberOfScans = ref(null);

        // Instrument information
        const manufacturer = ref("");
        const instrumentModel = ref("");

        const hasAnyValue = computed(() => {
            return !!(
                freeText.value ||
                solvent.value ||
                temperature.value ||
                tubeDiameter.value ||
                acquisitionNucleus.value ||
                protonFrequency.value ||
                nmrMethod.value ||
                pulseSequence.value ||
                numberOfScans.value ||
                manufacturer.value ||
                instrumentModel.value
            );
        });

        const clearAll = () => {
            freeText.value = "";
            solvent.value = "";
            temperature.value = null;
            tubeDiameter.value = "";
            acquisitionNucleus.value = "";
            protonFrequency.value = "";
            nmrMethod.value = "";
            pulseSequence.value = "";
            numberOfScans.value = null;
            manufacturer.value = "";
            instrumentModel.value = "";
        };

        const emitParams = () => {
            emit("search-params-updated", {
                freeText: freeText.value,
                solvent: solvent.value,
                temperature: temperature.value,
                tubeDiameter: tubeDiameter.value,
                acquisitionNucleus: acquisitionNucleus.value,
                protonFrequency: protonFrequency.value,
                nmrMethod: nmrMethod.value,
                pulseSequence: pulseSequence.value,
                numberOfScans: numberOfScans.value,
                manufacturer: manufacturer.value,
                instrumentModel: instrumentModel.value,
            });
        };

        return {
            freeText,
            solvent,
            temperature,
            tubeDiameter,
            acquisitionNucleus,
            protonFrequency,
            nmrMethod,
            pulseSequence,
            numberOfScans,
            manufacturer,
            instrumentModel,
            hasAnyValue,
            clearAll,
            emitParams,
        };
    },
};
</script>
