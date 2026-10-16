<template>
    <div class="flex flex-col gap-6 text-left">
        <div v-if="!compact">
            <h2 class="text-3xl font-bold text-gray-900">Spectra search</h2>
            <p class="mt-2 text-gray-600">
                Find public spectra with the peaks you are looking for. Drop a
                spectrum, paste a peak list, or type the peaks you know.
            </p>
        </div>

        <SpectraUploadContent
            v-if="showUpload"
            compact
            @upload-started="appliedFromSpectrum = {}"
            @spectrum-selected="applySpectrum"
        />

        <p
            v-if="notice"
            class="-mt-3 rounded-lg bg-emerald-50 px-3 py-2 text-xs text-emerald-800"
            aria-live="polite"
        >
            {{ notice }}
        </p>

        <section aria-label="Peaks">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div
                    class="inline-flex rounded-lg bg-gray-100 p-1"
                    role="tablist"
                    aria-label="Nucleus"
                >
                    <button
                        v-for="nucleus in nuclei"
                        :key="nucleus"
                        type="button"
                        role="tab"
                        :aria-selected="activeNucleus === nucleus"
                        class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900"
                        :class="
                            activeNucleus === nucleus
                                ? 'bg-white text-gray-900 shadow-sm'
                                : 'text-gray-600 hover:text-gray-900'
                        "
                        @click="activeNucleus = nucleus"
                    >
                        {{ nucleusLabels[nucleus] }} peaks
                        <span
                            v-if="filledCount(nucleus) > 0"
                            class="ml-1 rounded-full bg-gray-900 px-1.5 py-0.5 text-[10px] font-semibold text-white"
                            >{{ filledCount(nucleus) }}</span
                        >
                    </button>
                </div>
                <div class="flex items-center gap-4 text-xs font-medium">
                    <button
                        type="button"
                        class="text-gray-700 underline-offset-2 hover:text-gray-900 hover:underline"
                        :aria-expanded="pasteOpen"
                        @click="pasteOpen = !pasteOpen"
                    >
                        Paste peaks
                    </button>
                    <button
                        type="button"
                        class="text-gray-700 underline-offset-2 hover:text-gray-900 hover:underline disabled:opacity-50"
                        :disabled="loadingExample"
                        @click="loadExample"
                    >
                        {{ loadingExample ? "Loading…" : "Try an example" }}
                    </button>
                    <button
                        v-if="hasAnyRows"
                        type="button"
                        class="text-gray-500 underline-offset-2 hover:text-red-600 hover:underline"
                        @click="clearAll"
                    >
                        Clear
                    </button>
                </div>
            </div>

            <div
                v-if="pasteOpen"
                class="mt-3 rounded-xl border border-gray-200 bg-gray-50/60 p-3"
            >
                <label
                    for="peak-paste"
                    class="block text-xs font-medium text-gray-700"
                >
                    Paste {{ nucleusLabels[activeNucleus] }} peaks: a list, a
                    column from a spreadsheet, or text from a paper such as “δ
                    7.26 (m, 5H), 3.75 (s, 3H)”. Text with both ¹H and ¹³C data
                    fills both tables.
                </label>
                <textarea
                    id="peak-paste"
                    v-model="pasteText"
                    rows="4"
                    class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 font-mono text-sm focus:border-transparent focus:ring-2 focus:ring-gray-900"
                    :placeholder="pastePlaceholder"
                />
                <ul
                    v-if="pasteErrors.length"
                    class="mt-2 space-y-0.5 text-xs text-red-700"
                    role="alert"
                >
                    <li v-for="(error, index) in pasteErrors" :key="index">
                        {{ error }}
                    </li>
                </ul>
                <div class="mt-2 flex justify-end gap-2">
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-900"
                        @click="closePaste"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-gray-800 disabled:opacity-50"
                        :disabled="!pasteText.trim() || pasting"
                        @click="applyPaste"
                    >
                        {{ pasting ? "Reading…" : "Add peaks" }}
                    </button>
                </div>
            </div>

            <div class="mt-3 overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs text-gray-600">
                        <tr>
                            <th scope="col" class="px-3 py-2 font-medium">
                                Position (ppm)
                            </th>
                            <th
                                v-if="isProton"
                                scope="col"
                                class="px-2 py-2 font-medium"
                            >
                                Protons
                            </th>
                            <th
                                v-if="isProton"
                                scope="col"
                                class="px-2 py-2 font-medium"
                            >
                                Shape
                            </th>
                            <th scope="col" class="px-2 py-2 font-medium">
                                Rule
                            </th>
                            <th scope="col" class="w-8 px-2 py-2">
                                <span class="sr-only">Remove</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="(row, index) in activeRows"
                            :key="row.id"
                            class="align-top"
                        >
                            <td class="px-3 py-1.5">
                                <input
                                    :ref="(el) => setPositionInput(row.id, el)"
                                    v-model="row.position"
                                    type="text"
                                    inputmode="decimal"
                                    :aria-label="`Peak ${
                                        index + 1
                                    } position in ppm`"
                                    :aria-invalid="Boolean(errorFor(row))"
                                    placeholder="e.g. 3.75 or 5.0-5.5"
                                    class="w-full min-w-[9rem] rounded-md border px-2 py-1 text-sm focus:border-transparent focus:ring-2 focus:ring-gray-900"
                                    :class="
                                        errorFor(row)
                                            ? 'border-red-300 bg-red-50'
                                            : 'border-gray-200'
                                    "
                                    @keydown.enter.prevent="addRowAfter(index)"
                                />
                                <p
                                    v-if="errorFor(row)"
                                    class="mt-1 text-xs text-red-700"
                                >
                                    {{ errorFor(row) }}
                                </p>
                            </td>
                            <td v-if="isProton" class="px-2 py-1.5">
                                <input
                                    v-model="row.protons"
                                    type="text"
                                    inputmode="numeric"
                                    :aria-label="`Peak ${
                                        index + 1
                                    } number of protons`"
                                    placeholder="–"
                                    class="w-14 rounded-md border border-gray-200 px-2 py-1 text-sm focus:border-transparent focus:ring-2 focus:ring-gray-900"
                                />
                            </td>
                            <td v-if="isProton" class="px-2 py-1.5">
                                <select
                                    v-model="row.shape"
                                    :aria-label="`Peak ${index + 1} shape`"
                                    class="rounded-md border border-gray-200 py-1 pl-2 pr-7 text-sm focus:border-transparent focus:ring-2 focus:ring-gray-900"
                                >
                                    <option
                                        v-for="shape in shapes"
                                        :key="shape.value"
                                        :value="shape.value"
                                    >
                                        {{ shape.label }}
                                    </option>
                                </select>
                            </td>
                            <td class="px-2 py-1.5">
                                <select
                                    v-model="row.rule"
                                    :aria-label="`Peak ${index + 1} rule`"
                                    class="rounded-md border py-1 pl-2 pr-7 text-sm font-medium focus:border-transparent focus:ring-2 focus:ring-gray-900"
                                    :class="ruleClass(row.rule)"
                                >
                                    <option
                                        v-for="rule in rules"
                                        :key="rule.value"
                                        :value="rule.value"
                                    >
                                        {{ rule.label }}
                                    </option>
                                </select>
                            </td>
                            <td class="px-2 py-1.5">
                                <button
                                    type="button"
                                    class="rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900"
                                    :aria-label="`Remove peak ${index + 1}`"
                                    @click="removeRow(row.id)"
                                >
                                    <XMarkIcon class="h-4 w-4" />
                                </button>
                            </td>
                        </tr>
                        <tr v-if="activeRows.length === 0">
                            <td
                                :colspan="isProton ? 5 : 3"
                                class="px-3 py-4 text-center text-xs text-gray-500"
                            >
                                No {{ nucleusLabels[activeNucleus] }} peaks yet.
                                Add a peak, paste a list, or drop a spectrum.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-2 flex flex-wrap items-start justify-between gap-2">
                <button
                    type="button"
                    class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                    @click="addRow"
                >
                    <PlusIcon class="h-3.5 w-3.5" aria-hidden="true" />
                    Add a peak
                </button>
                <p class="max-w-md text-xs text-gray-500">
                    One value (3.75) or a region (5.0-5.5) for any peak in that
                    range. Use “Must not have” to rule out a group, for example
                    no aldehyde: 9.5-10.5.
                    <template v-if="isProton">
                        Protons and shape only help to sort the results.
                    </template>
                </p>
            </div>
        </section>

        <section
            aria-label="Search options"
            class="grid grid-cols-1 gap-5"
            :class="compact ? '' : 'lg:grid-cols-2'"
        >
            <fieldset>
                <legend class="text-sm font-semibold text-gray-900">
                    How to compare
                </legend>
                <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                    <label
                        v-for="option in modeOptions"
                        :key="option.value"
                        class="relative flex cursor-pointer flex-col rounded-lg border-2 bg-white p-3 transition-all hover:border-gray-300"
                        :class="
                            form.mode === option.value
                                ? 'border-gray-900 bg-gray-50'
                                : 'border-gray-200'
                        "
                    >
                        <input
                            v-model="form.mode"
                            type="radio"
                            name="spectrum-search-mode"
                            :value="option.value"
                            class="sr-only"
                        />
                        <span class="text-sm font-medium text-gray-900">
                            {{ option.label }}
                        </span>
                        <span class="mt-0.5 text-xs text-gray-500">
                            {{ option.description }}
                        </span>
                    </label>
                </div>
            </fieldset>

            <fieldset>
                <legend class="text-sm font-semibold text-gray-900">
                    How close is close enough
                </legend>
                <div class="mt-2 grid grid-cols-3 gap-2">
                    <label
                        v-for="(option, key) in closeness"
                        :key="key"
                        class="flex cursor-pointer flex-col items-center rounded-lg border-2 bg-white px-2 py-2 text-center transition-all hover:border-gray-300"
                        :class="
                            form.closeness === key
                                ? 'border-gray-900 bg-gray-50'
                                : 'border-gray-200'
                        "
                    >
                        <input
                            v-model="form.closeness"
                            type="radio"
                            name="spectrum-search-closeness"
                            :value="key"
                            class="sr-only"
                        />
                        <span class="text-sm font-medium text-gray-900">
                            {{ option.label }}
                        </span>
                        <span class="mt-0.5 text-[11px] text-gray-500">
                            ¹H ±{{ option["1H"] }} · ¹³C ±{{ option["13C"] }}
                            ppm
                        </span>
                    </label>
                </div>
                <p class="mt-1.5 text-xs text-gray-500">
                    Shifts move a little between solvents and instruments. Use
                    Relaxed if your solvent differs.
                </p>
            </fieldset>

            <div>
                <label
                    for="spectrum-search-solvent"
                    class="text-sm font-semibold text-gray-900"
                >
                    Solvent
                    <span class="font-normal text-gray-500">(optional)</span>
                </label>
                <select
                    id="spectrum-search-solvent"
                    v-model="form.solvent"
                    class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-gray-900"
                >
                    <option value="">Not specified</option>
                    <option
                        v-for="solvent in solventOptions"
                        :key="solvent"
                        :value="solvent"
                    >
                        {{ solvent }}
                    </option>
                </select>
                <label
                    class="mt-2 flex items-center gap-2 text-xs text-gray-700"
                    :class="form.solvent ? '' : 'opacity-50'"
                >
                    <input
                        v-model="form.sameSolvent"
                        type="checkbox"
                        :disabled="!form.solvent"
                        class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                    />
                    Only show spectra measured in this solvent
                </label>
            </div>

            <div class="space-y-3">
                <SwitchGroup
                    v-for="option in switchOptions"
                    :key="option.key"
                    as="div"
                    class="flex items-start justify-between gap-3"
                >
                    <span class="flex flex-col">
                        <SwitchLabel
                            as="span"
                            class="text-sm font-medium text-gray-900"
                            passive
                        >
                            {{ option.label }}
                        </SwitchLabel>
                        <SwitchDescription
                            as="span"
                            class="text-xs text-gray-500"
                        >
                            {{ option.description }}
                        </SwitchDescription>
                    </span>
                    <HSwitch
                        v-model="form[option.key]"
                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2"
                        :class="
                            form[option.key] ? 'bg-gray-900' : 'bg-gray-200'
                        "
                    >
                        <span
                            aria-hidden="true"
                            class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition"
                            :class="
                                form[option.key]
                                    ? 'translate-x-4'
                                    : 'translate-x-0'
                            "
                        />
                    </HSwitch>
                </SwitchGroup>
            </div>
        </section>

        <p class="text-xs text-gray-500">
            <span class="font-medium text-gray-600">Coming soon:</span>
            carbon type (CH, CH₂, CH₃), ¹H–¹³C pairs from HSQC, and a molecular
            formula filter.
        </p>
    </div>
</template>

<script>
import { computed, nextTick, reactive, ref, watch } from "vue";
import {
    Switch as HSwitch,
    SwitchDescription,
    SwitchGroup,
    SwitchLabel,
} from "@headlessui/vue";
import { PlusIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import SpectraUploadContent from "@/Shared/SpectraUploadContent.vue";
import {
    CLOSENESS,
    NUCLEI,
    NUCLEUS_LABELS,
    RULES,
    SHAPES,
    SOLVENTS,
    apiParamsToForm,
    canSearch,
    formToApiParams,
    newRow,
    parsePosition,
    rowError,
    rowsFromApiPeaks,
    rowsFromDetectedSpectrum,
} from "@/Utils/spectrumQuery.js";
import {
    fetchSpectrumExample,
    parsePeakText,
} from "@/Utils/unifiedSearchApi.js";

export default {
    components: {
        SpectraUploadContent,
        HSwitch,
        SwitchDescription,
        SwitchGroup,
        SwitchLabel,
        PlusIcon,
        XMarkIcon,
    },
    props: {
        initialParams: {
            type: Object,
            default: () => ({}),
        },
        compact: {
            type: Boolean,
            default: false,
        },
        showUpload: {
            type: Boolean,
            default: true,
        },
    },
    emits: ["search-params-updated"],
    setup(props, { emit }) {
        const form = reactive(apiParamsToForm(props.initialParams));
        const activeNucleus = ref(
            NUCLEI.find((nucleus) => form.peaks[nucleus].length > 0) ?? "1H"
        );
        const pasteOpen = ref(false);
        const pasteText = ref("");
        const pasteErrors = ref([]);
        const pasting = ref(false);
        const loadingExample = ref(false);
        const notice = ref(null);
        const appliedFromSpectrum = ref({});
        const positionInputs = new Map();

        const activeRows = computed(() => form.peaks[activeNucleus.value]);
        const isProton = computed(() => activeNucleus.value === "1H");
        const hasAnyRows = computed(() =>
            NUCLEI.some((nucleus) => form.peaks[nucleus].length > 0)
        );

        const solventOptions = computed(() =>
            form.solvent && !SOLVENTS.includes(form.solvent)
                ? [form.solvent, ...SOLVENTS]
                : SOLVENTS
        );

        const pastePlaceholder = computed(() =>
            isProton.value
                ? "7.26, 3.75, 2.10\n¹H NMR (400 MHz, CDCl₃) δ 7.35–7.20 (m, 5H), 3.75 (s, 3H)"
                : "170.1, 128.3, 60.5\n¹³C NMR (101 MHz, CDCl₃) δ 170.1, 128.3, 60.5"
        );

        const filledCount = (nucleus) =>
            form.peaks[nucleus].filter((row) => parsePosition(row.position))
                .length;

        const errorFor = (row) => rowError(row, activeNucleus.value);

        const ruleClass = (rule) =>
            ({
                must: "border-gray-300 text-gray-900",
                nice: "border-gray-200 text-gray-500",
                not: "border-red-200 bg-red-50 text-red-700",
            }[rule]);

        const setPositionInput = (id, element) => {
            if (element) {
                positionInputs.set(id, element);
            } else {
                positionInputs.delete(id);
            }
        };

        const focusRow = async (id) => {
            await nextTick();
            positionInputs.get(id)?.focus();
        };

        const addRow = () => {
            const row = newRow();
            form.peaks[activeNucleus.value].push(row);
            focusRow(row.id);
        };

        const addRowAfter = (index) => {
            const rows = form.peaks[activeNucleus.value];
            if (index === rows.length - 1) {
                addRow();
            } else {
                focusRow(rows[index + 1].id);
            }
        };

        const removeRow = (id) => {
            form.peaks[activeNucleus.value] = form.peaks[
                activeNucleus.value
            ].filter((row) => row.id !== id);
        };

        const clearAll = () => {
            for (const nucleus of NUCLEI) {
                form.peaks[nucleus] = [];
            }
            notice.value = null;
        };

        const fillRows = (nucleus, rows, { replace = false } = {}) => {
            const kept = replace
                ? []
                : form.peaks[nucleus].filter((row) => row.position.trim());
            const keptPositions = new Set(
                kept.map((row) => JSON.stringify(parsePosition(row.position)))
            );
            form.peaks[nucleus] = [
                ...kept,
                ...rows.filter(
                    (row) =>
                        !keptPositions.has(
                            JSON.stringify(parsePosition(row.position))
                        )
                ),
            ];
        };

        const applyParsedNuclei = (nuclei, { replace = false } = {}) => {
            const filledNuclei = NUCLEI.filter(
                (nucleus) => nuclei?.[nucleus]?.peaks?.length > 0
            );
            for (const nucleus of filledNuclei) {
                fillRows(nucleus, rowsFromApiPeaks(nuclei[nucleus].peaks), {
                    replace,
                });
            }

            if (
                filledNuclei.length > 0 &&
                !filledNuclei.includes(activeNucleus.value)
            ) {
                activeNucleus.value = filledNuclei[0];
            }

            return filledNuclei.map(
                (nucleus) =>
                    `${nuclei[nucleus].peaks.length} ${NUCLEUS_LABELS[nucleus]}`
            );
        };

        const applyPaste = async () => {
            pasting.value = true;
            pasteErrors.value = [];
            try {
                const result = await parsePeakText({
                    text: pasteText.value,
                    nucleus: activeNucleus.value,
                });
                const filled = applyParsedNuclei(result.nuclei);
                if (result.solvent && !form.solvent) {
                    form.solvent = result.solvent;
                }

                pasteErrors.value = Object.entries(result.nuclei ?? {}).flatMap(
                    ([nucleus, parsed]) =>
                        parsed.errors.map(
                            (error) =>
                                `${NUCLEUS_LABELS[nucleus]}: ${error.message}`
                        )
                );

                if (filled.length > 0) {
                    notice.value = `Added ${filled.join(" and ")} peaks.`;
                    if (pasteErrors.value.length === 0) {
                        closePaste();
                    }
                } else if (pasteErrors.value.length === 0) {
                    pasteErrors.value = ["No peaks were found in this text."];
                }
            } catch (error) {
                pasteErrors.value = [
                    error?.response?.data?.message ||
                        "Something went wrong while reading these peaks.",
                ];
            } finally {
                pasting.value = false;
            }
        };

        const closePaste = () => {
            pasteOpen.value = false;
            pasteText.value = "";
            pasteErrors.value = [];
        };

        const loadExample = async () => {
            loadingExample.value = true;
            try {
                const example = await fetchSpectrumExample();
                clearAll();
                applyParsedNuclei(example.nuclei, { replace: true });
                activeNucleus.value = "1H";
                form.mode = "contains";
                form.solvent = example.solvent ?? "";
                notice.value = example.sample?.name
                    ? `Loaded the strongest peaks of “${example.sample.name}”. Search to see which samples share them.`
                    : "Loaded an example.";
            } catch {
                notice.value =
                    "No example is available yet. Type a few peaks instead.";
            } finally {
                loadingExample.value = false;
            }
        };

        const applySpectrum = (spectrum) => {
            const rows = rowsFromDetectedSpectrum(spectrum);
            fillRows(spectrum.nucleus, rows, { replace: true });
            if (Object.keys(appliedFromSpectrum.value).length === 0) {
                activeNucleus.value = spectrum.nucleus;
            }
            appliedFromSpectrum.value = {
                ...appliedFromSpectrum.value,
                [spectrum.nucleus]: rows.length,
            };
            form.mode = "whole";
            if (spectrum.solvent) {
                form.solvent = spectrum.solvent;
            }

            const added = NUCLEI.filter(
                (nucleus) => appliedFromSpectrum.value[nucleus] !== undefined
            )
                .map(
                    (nucleus) =>
                        `${appliedFromSpectrum.value[nucleus]} ${NUCLEUS_LABELS[nucleus]}`
                )
                .join(" and ");
            notice.value = `Added ${added} peaks from your spectrum as “Nice to have”. Mark the key peaks as “Must have” to narrow the search.`;
        };

        watch(
            form,
            () => {
                emit("search-params-updated", {
                    params: formToApiParams(form),
                    canSearch: canSearch(form),
                    peakCount: NUCLEI.reduce(
                        (sum, nucleus) => sum + filledCount(nucleus),
                        0
                    ),
                });
            },
            { deep: true, immediate: true }
        );

        return {
            form,
            nuclei: NUCLEI,
            nucleusLabels: NUCLEUS_LABELS,
            rules: RULES,
            shapes: SHAPES,
            closeness: CLOSENESS,
            solventOptions,
            modeOptions: [
                {
                    value: "contains",
                    label: "Contains my peaks",
                    description:
                        "Every “Must have” peak is there. Other peaks in the spectrum are fine.",
                },
                {
                    value: "whole",
                    label: "Looks like my whole spectrum",
                    description:
                        "Missing or extra peaks lower the match. Best when you dropped a full spectrum.",
                },
            ],
            switchOptions: [
                {
                    key: "ignoreSolventPeaks",
                    label: "Ignore solvent and water peaks",
                    description:
                        "For example CDCl₃ at 7.26 and water at 1.56 ppm, on both sides.",
                },
                {
                    key: "allowOffset",
                    label: "Allow for calibration differences",
                    description:
                        "Lets a whole spectrum sit slightly higher or lower (up to 0.1 ppm for ¹H, 1.5 ppm for ¹³C) when at least 3 peaks match.",
                },
            ],
            activeNucleus,
            activeRows,
            isProton,
            hasAnyRows,
            pasteOpen,
            pasteText,
            pasteErrors,
            pastePlaceholder,
            pasting,
            loadingExample,
            notice,
            appliedFromSpectrum,
            filledCount,
            errorFor,
            ruleClass,
            setPositionInput,
            addRow,
            addRowAfter,
            removeRow,
            clearAll,
            applyPaste,
            closePaste,
            loadExample,
            applySpectrum,
        };
    },
};
</script>
