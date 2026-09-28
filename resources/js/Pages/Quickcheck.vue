<template>
    <div class="bg-white">
        <Head title="Assignment Quickcheck - nmrXiv"></Head>
        <FlashMessages />
        <main>
            <div class="relative">
                <PublicSiteHeader />

                <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                    <div class="mb-10 text-center">
                        <h1
                            class="text-4xl font-bold text-gray-900 sm:text-5xl"
                        >
                            Assignment Quickcheck
                        </h1>
                        <p
                            class="mx-auto mt-3 max-w-3xl text-base text-gray-500 sm:text-lg"
                        >
                            Check ¹³C and ¹H assignments against nmrshiftdb2
                            predictions before you submit: a quality mark per
                            nucleus, the atoms that do not fit, and likely
                            interchanged assignments.
                        </p>
                    </div>

                    <div class="mx-auto max-w-6xl">
                        <div
                            v-show="!result"
                            class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-xl"
                        >
                            <nav
                                class="flex border-b border-gray-200"
                                aria-label="Input method"
                            >
                                <button
                                    v-for="option in modes"
                                    :key="option.id"
                                    type="button"
                                    class="flex-1 px-4 py-3 text-sm font-semibold transition-colors"
                                    :class="
                                        mode === option.id
                                            ? 'border-b-2 border-teal-600 text-teal-700'
                                            : 'text-gray-500 hover:text-gray-700'
                                    "
                                    :aria-pressed="mode === option.id"
                                    @click="switchMode(option.id)"
                                >
                                    {{ option.label }}
                                </button>
                            </nav>

                            <div class="space-y-6 px-6 py-8 sm:p-10">
                                <div v-show="mode === 'file'" class="space-y-4">
                                    <p class="text-sm text-gray-600">
                                        Upload the SD file exported from Mnova
                                        with assignments (it carries the
                                        structure and the CHEMICAL_SHIFTS tags)
                                        or an NMReDATA file.
                                    </p>
                                    <label
                                        class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-white px-4 py-10 text-sm font-medium text-gray-700 transition-colors hover:border-gray-400 hover:bg-gray-50"
                                        :class="
                                            isDragging
                                                ? 'border-gray-900 bg-gray-50'
                                                : ''
                                        "
                                        @dragover.prevent="isDragging = true"
                                        @dragleave.prevent="isDragging = false"
                                        @drop.prevent="handleDrop"
                                    >
                                        <span>{{
                                            file
                                                ? file.name
                                                : "Drop or select an SDF / NMReDATA file"
                                        }}</span>
                                        <span
                                            class="mt-1 text-xs font-normal text-gray-500"
                                            >.sdf, .sd, .nmredata · up to 2
                                            MB</span
                                        >
                                        <input
                                            ref="fileInput"
                                            type="file"
                                            accept=".sdf,.sd,.nmredata,.txt"
                                            class="sr-only"
                                            @change="handleFileSelect"
                                        />
                                    </label>
                                </div>

                                <div
                                    v-show="mode === 'manual'"
                                    class="space-y-4"
                                >
                                    <p class="text-sm text-gray-600">
                                        Draw or paste the structure; atom
                                        numbers are shown in the editor. For ¹H
                                        rows, enter the number of the atom that
                                        carries the proton. Equivalent atoms
                                        share a row (e.g. “2, 6”).
                                    </p>
                                    <div
                                        id="quickcheckEditor"
                                        class="w-full rounded-xl border border-gray-200 bg-white shadow-sm"
                                        style="height: 420px"
                                    />

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                                <tr
                                                    class="text-left text-xs font-medium text-gray-500"
                                                >
                                                    <th class="py-2 pr-3">
                                                        Nucleus
                                                    </th>
                                                    <th class="py-2 pr-3">
                                                        Atom numbers
                                                    </th>
                                                    <th class="py-2 pr-3">
                                                        δ (ppm)
                                                    </th>
                                                    <th class="py-2 pr-3">
                                                        Label (optional)
                                                    </th>
                                                    <th class="py-2">
                                                        <span class="sr-only"
                                                            >Remove</span
                                                        >
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(row, index) in rows"
                                                    :key="row.key"
                                                >
                                                    <td class="py-1 pr-3">
                                                        <select
                                                            v-model="
                                                                row.nucleus
                                                            "
                                                            class="rounded-md border-gray-300 py-1.5 text-sm focus:border-teal-500 focus:ring-teal-500"
                                                        >
                                                            <option value="13C">
                                                                ¹³C
                                                            </option>
                                                            <option value="1H">
                                                                ¹H
                                                            </option>
                                                        </select>
                                                    </td>
                                                    <td class="py-1 pr-3">
                                                        <input
                                                            v-model="row.atoms"
                                                            type="text"
                                                            placeholder="2, 6"
                                                            class="w-28 rounded-md border-gray-300 py-1.5 text-sm focus:border-teal-500 focus:ring-teal-500"
                                                        />
                                                    </td>
                                                    <td class="py-1 pr-3">
                                                        <input
                                                            v-model="row.shift"
                                                            type="number"
                                                            step="0.01"
                                                            placeholder="106.5"
                                                            class="w-28 rounded-md border-gray-300 py-1.5 text-sm focus:border-teal-500 focus:ring-teal-500"
                                                        />
                                                    </td>
                                                    <td class="py-1 pr-3">
                                                        <input
                                                            v-model="row.label"
                                                            type="text"
                                                            placeholder="C-2/6"
                                                            class="w-32 rounded-md border-gray-300 py-1.5 text-sm focus:border-teal-500 focus:ring-teal-500"
                                                        />
                                                    </td>
                                                    <td class="py-1">
                                                        <button
                                                            type="button"
                                                            class="text-xs text-gray-400 hover:text-red-600"
                                                            :aria-label="
                                                                'Remove row ' +
                                                                (index + 1)
                                                            "
                                                            @click="
                                                                removeRow(index)
                                                            "
                                                        >
                                                            Remove
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button
                                        type="button"
                                        class="text-sm font-medium text-teal-700 hover:underline"
                                        @click="addRow"
                                    >
                                        + Add shift
                                    </button>
                                </div>

                                <div
                                    class="flex flex-wrap items-end gap-4 border-t border-gray-100 pt-6"
                                >
                                    <label class="text-sm text-gray-700">
                                        <span
                                            class="block text-xs font-medium text-gray-500"
                                            >Solvent</span
                                        >
                                        <select
                                            v-model="solvent"
                                            class="mt-1 rounded-md border-gray-300 py-1.5 text-sm focus:border-teal-500 focus:ring-teal-500"
                                        >
                                            <option value="">
                                                {{
                                                    mode === "file"
                                                        ? "From file"
                                                        : "Not specified"
                                                }}
                                            </option>
                                            <option
                                                v-for="option in solvents"
                                                :key="option"
                                                :value="option"
                                            >
                                                {{ option }}
                                            </option>
                                        </select>
                                    </label>
                                    <button
                                        type="button"
                                        class="ml-auto inline-flex items-center rounded-lg bg-gray-900 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 disabled:cursor-not-allowed disabled:opacity-50"
                                        :disabled="isChecking || !canSubmit"
                                        @click="submit"
                                    >
                                        {{
                                            isChecking
                                                ? "Checking with nmrshiftdb2…"
                                                : "Run Quickcheck"
                                        }}
                                    </button>
                                </div>

                                <p
                                    v-if="error"
                                    class="text-sm text-red-600"
                                    role="alert"
                                >
                                    {{ error }}
                                </p>

                                <div
                                    v-if="isChecking"
                                    class="animate-pulse space-y-3"
                                >
                                    <div
                                        class="h-12 rounded-lg bg-gray-100"
                                    ></div>
                                    <div
                                        class="h-40 rounded-lg bg-gray-100"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <div v-if="result" class="space-y-4">
                            <div class="flex justify-end print:hidden">
                                <button
                                    type="button"
                                    class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                    @click="result = null"
                                >
                                    ← Check other assignments
                                </button>
                            </div>
                            <QuickcheckReport
                                :report="result.report"
                                :molfile="result.input.structure.molfile"
                                :details="reportDetails"
                            />
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
import FlashMessages from "@/Shared/FlashMessages.vue";
import Footer from "@/Shared/Footer.vue";
import PublicSiteHeader from "@/Shared/PublicSiteHeader.vue";
import QuickcheckReport from "@/Shared/Assignments/QuickcheckReport.vue";
import { createStructureEditor } from "@/Utils/structureEditor";
import { plainMolfile } from "@/Utils/quickcheck.js";

let rowKey = 0;

function emptyRow(nucleus = "13C") {
    rowKey += 1;
    return { key: rowKey, nucleus, atoms: "", shift: "", label: "" };
}

function parseAtoms(value) {
    return String(value)
        .split(/[\s,;]+/)
        .filter(Boolean)
        .map((atom) => parseInt(atom, 10))
        .filter((atom) => Number.isInteger(atom) && atom > 0);
}

export default {
    components: {
        Head,
        FlashMessages,
        Footer,
        PublicSiteHeader,
        QuickcheckReport,
    },
    props: {
        quickcheckUrl: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            modes: [
                { id: "file", label: "Upload assignment file" },
                { id: "manual", label: "Draw structure and assign" },
            ],
            mode: "file",
            file: null,
            isDragging: false,
            rows: [emptyRow("13C"), emptyRow("1H")],
            solvent: "",
            solvents: [
                "CDCl3",
                "DMSO-d6",
                "CD3OD",
                "D2O",
                "Acetone-d6",
                "C6D6",
                "Pyridine-d5",
                "THF-d8",
                "CCl4",
            ],
            editor: null,
            isChecking: false,
            error: null,
            result: null,
        };
    },
    computed: {
        filledRows() {
            return this.rows.filter(
                (row) => row.shift !== "" && row.shift !== null
            );
        },
        reportDetails() {
            return {
                title:
                    this.mode === "file" && this.file
                        ? this.file.name
                        : "Manually entered assignments",
                source: this.result?.input?.structure?.source,
            };
        },
        canSubmit() {
            return this.mode === "file"
                ? this.file !== null
                : this.filledRows.length > 0;
        },
    },
    beforeUnmount() {
        if (this.editor?.destroy) {
            this.editor.destroy();
        }
    },
    methods: {
        async switchMode(mode) {
            this.mode = mode;
            this.error = null;
            if (mode === "manual" && !this.editor) {
                await this.$nextTick();
                this.editor = await createStructureEditor("quickcheckEditor", {
                    showAtomNumbers: true,
                });
            }
        },
        handleFileSelect(event) {
            this.file = event.target.files?.[0] || null;
            this.error = null;
        },
        handleDrop(event) {
            this.isDragging = false;
            this.file = event.dataTransfer.files?.[0] || null;
            this.error = null;
        },
        addRow() {
            const last = this.rows[this.rows.length - 1];
            this.rows.push(emptyRow(last?.nucleus));
        },
        removeRow(index) {
            this.rows.splice(index, 1);
            if (this.rows.length === 0) {
                this.addRow();
            }
        },
        buildPayload() {
            if (this.mode === "file") {
                const form = new FormData();
                form.append("source", "file");
                form.append("file", this.file);
                if (this.solvent) {
                    form.append("solvent", this.solvent);
                }
                return form;
            }

            const molecule = this.editor?.getMolecule();
            if (!molecule || molecule.getAllAtoms() === 0) {
                throw new Error("Draw the structure first.");
            }
            const invalid = this.filledRows.find(
                (row) => parseAtoms(row.atoms).length === 0
            );
            if (invalid) {
                throw new Error(
                    "Every shift needs at least one atom number from the structure."
                );
            }

            return {
                source: "manual",
                molfile: plainMolfile(molecule),
                solvent: this.solvent || null,
                assignments: this.filledRows.map((row) => ({
                    nucleus: row.nucleus,
                    atoms: parseAtoms(row.atoms),
                    shift: Number(row.shift),
                    label: row.label || null,
                })),
            };
        },
        submit() {
            this.error = null;
            let payload;
            try {
                payload = this.buildPayload();
            } catch (error) {
                this.error = error.message;
                return;
            }

            this.isChecking = true;
            axios
                .post(this.quickcheckUrl, payload)
                .then(({ data }) => {
                    this.result = data;
                    window.scrollTo({ top: 0, behavior: "smooth" });
                })
                .catch((error) => {
                    this.error = this.errorMessage(error);
                })
                .finally(() => {
                    this.isChecking = false;
                });
        },
        errorMessage(error) {
            const status = error.response?.status;
            if (status === 429) {
                return "Too many checks in a short time. Please wait a minute and try again.";
            }
            if (status === 503) {
                return (
                    error.response.data?.message ||
                    "nmrshiftdb2 is not reachable right now. Please try again later."
                );
            }
            const errors = error.response?.data?.errors;
            if (errors) {
                return Object.values(errors).flat()[0];
            }
            return (
                error.response?.data?.message ||
                "The Quickcheck failed. Please try again."
            );
        },
    },
};
</script>
