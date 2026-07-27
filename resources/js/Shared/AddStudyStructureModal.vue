<template>
    <jet-dialog-modal :show="show" max-width="2xl" @close="handleClose">
        <template #title>
            <div class="flex items-center justify-between gap-2">
                <span>{{
                    isEditMode ? "Edit structure" : "Add structure"
                }}</span>
                <a
                    href="https://docs.nmrxiv.org/submission-guides/editor.html"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-xs font-medium text-teal-600 hover:text-teal-700"
                >
                    Help
                </a>
            </div>
        </template>

        <template #content>
            <div class="flex flex-col gap-3">
                <div
                    class="rounded-md bg-gray-100 p-0.5"
                    role="tablist"
                    aria-label="Structure input method"
                >
                    <div class="flex gap-0.5">
                        <button
                            type="button"
                            role="tab"
                            :aria-selected="activeTab === 'editor'"
                            :class="tabButtonClass('editor')"
                            @click="activeTab = 'editor'"
                        >
                            Draw
                        </button>
                        <button
                            type="button"
                            role="tab"
                            :aria-selected="activeTab === 'paste'"
                            :class="tabButtonClass('paste')"
                            @click="activeTab = 'paste'"
                        >
                            Paste
                        </button>
                        <button
                            type="button"
                            role="tab"
                            :aria-selected="activeTab === 'cas'"
                            :class="tabButtonClass('cas')"
                            @click="activeTab = 'cas'"
                        >
                            CAS
                        </button>
                        <button
                            type="button"
                            role="tab"
                            :aria-selected="activeTab === 'file'"
                            :class="tabButtonClass('file')"
                            @click="activeTab = 'file'"
                        >
                            File
                        </button>
                    </div>
                </div>

                <div
                    v-if="structureCandidates.length > 1"
                    class="rounded-lg border border-teal-200 bg-teal-50 p-3"
                >
                    <p class="text-sm font-medium text-teal-900">
                        {{ structureCandidates.length }} structures found —
                        choose one to load
                    </p>
                    <ul
                        class="mt-2 max-h-40 space-y-1 overflow-y-auto"
                        role="listbox"
                        aria-label="Structures in file"
                    >
                        <li
                            v-for="candidate in structureCandidates"
                            :key="candidate.id"
                        >
                            <button
                                type="button"
                                role="option"
                                class="flex w-full items-center justify-between rounded-md px-3 py-2 text-left text-sm transition hover:bg-white"
                                :class="
                                    selectedCandidateId === candidate.id
                                        ? 'bg-white font-medium text-teal-800 ring-1 ring-teal-300'
                                        : 'text-gray-700'
                                "
                                @click="selectStructureCandidate(candidate)"
                            >
                                <span class="truncate">{{
                                    candidate.label
                                }}</span>
                                <span
                                    class="ml-2 shrink-0 text-xs text-gray-500"
                                >
                                    #{{ candidate.index }}
                                </span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div
                    v-show="activeTab === 'cas'"
                    class="flex min-h-[200px] flex-col gap-2"
                >
                    <div class="rounded-lg border border-gray-300 p-3">
                        <div
                            class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3"
                        >
                            <input
                                v-model="casInput"
                                type="text"
                                placeholder="CAS e.g. 58-08-2"
                                class="min-w-0 flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                :disabled="casLoading"
                                @keyup.enter="importFromCas"
                            />
                            <button
                                type="button"
                                class="inline-flex items-center rounded-md border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="!casInput.trim() || casLoading"
                                @click="importFromCas"
                            >
                                <ArrowPathIcon
                                    v-if="casLoading"
                                    class="-ml-1 mr-2 h-4 w-4 animate-spin"
                                    aria-hidden="true"
                                />
                                {{ casLoading ? "Loading…" : "Import" }}
                            </button>
                        </div>
                        <button
                            v-if="casInput"
                            type="button"
                            class="mt-2 text-xs font-medium text-gray-600 hover:text-gray-900"
                            @click="clearCasInput"
                        >
                            Clear
                        </button>
                    </div>
                    <p class="text-xs text-gray-500">
                        Look up a CAS Registry Number and load the structure
                        into the editor for review before saving.
                    </p>
                </div>

                <div
                    v-show="activeTab === 'file'"
                    class="flex min-h-[200px] flex-col gap-2"
                >
                    <label
                        class="relative flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-white px-4 py-8 text-center transition hover:border-teal-400 hover:bg-teal-50/40"
                        :class="
                            isFileDragging
                                ? 'border-teal-400 bg-teal-50/60'
                                : ''
                        "
                        @click="openStructureFilePicker"
                        @dragover.prevent="isFileDragging = true"
                        @dragleave.prevent="isFileDragging = false"
                        @drop.prevent="handleStructureFileDrop"
                    >
                        <ArrowUpTrayIcon
                            class="h-8 w-8 text-gray-400"
                            aria-hidden="true"
                        />
                        <span class="mt-2 text-sm font-medium text-gray-900">
                            Drop or select MOL / SDF / CDXML
                        </span>
                        <span class="mt-1 text-xs text-gray-500">
                            Multiple structures? You can pick one after upload.
                        </span>
                        <input
                            ref="structureFileInputRef"
                            type="file"
                            accept=".mol,.sdf,.sd,.cdxml"
                            class="sr-only"
                            @change="handleStructureFileSelect"
                        />
                    </label>
                    <p v-if="loadedFileName" class="text-xs text-gray-600">
                        Loaded: {{ loadedFileName }}
                    </p>
                </div>

                <div
                    v-show="activeTab === 'paste'"
                    class="flex min-h-[200px] flex-col gap-2"
                >
                    <textarea
                        v-model="chemicalInput"
                        placeholder="SMILES (one line) or MOL / SDF / CDXML…"
                        class="min-h-[200px] w-full resize-y rounded-md border-gray-300 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500"
                        @input="handleInput"
                    />
                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="!chemicalInput.trim() || fileLoading"
                            @click="loadFromPaste"
                        >
                            Load into editor
                        </button>
                        <button
                            v-if="chemicalInput"
                            type="button"
                            class="text-xs font-medium text-gray-600 hover:text-gray-900"
                            @click="clearPasteInput"
                        >
                            Clear
                        </button>
                    </div>
                    <p v-if="detectedFormat" class="text-xs text-gray-500">
                        Detected: {{ detectedFormat }}
                    </p>
                </div>

                <div
                    v-if="activeTab === 'editor'"
                    ref="editorHostRef"
                    class="h-[min(360px,50vh)] min-h-[240px] w-full overflow-hidden rounded-lg border border-gray-200 bg-gray-50 shadow-inner"
                />

                <p
                    v-if="errorMessage || casError"
                    class="text-sm text-red-600"
                    role="alert"
                >
                    {{ errorMessage || casError }}
                </p>
            </div>
        </template>

        <template #footer>
            <jet-secondary-button :disabled="saving" @click="handleClose">
                Cancel
            </jet-secondary-button>
            <button
                type="button"
                class="ml-3 inline-flex items-center rounded-md border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                :disabled="saving"
                @click="saveStructure"
            >
                <ArrowPathIcon
                    v-if="saving"
                    class="-ml-1 mr-2 h-4 w-4 animate-spin"
                    aria-hidden="true"
                />
                {{ saving ? "Saving…" : "Save structure" }}
            </button>
        </template>
    </jet-dialog-modal>
</template>

<script setup>
import axios from "axios";
import { computed, nextTick, onBeforeUnmount, ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import { ArrowPathIcon, ArrowUpTrayIcon } from "@heroicons/vue/24/outline";
import { moleculeFromMolfileOrText } from "@/Utils/molfileNormalize";
import {
    detectStructureFormat,
    parseStructureFile,
    parseStructureText,
} from "@/Utils/parseStructureFile";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import {
    createStructureEditor,
    loadOpenChemLib,
} from "@/Utils/structureEditor";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    study: {
        type: Object,
        default: null,
    },
    mode: {
        type: String,
        default: "add",
        validator: (value) => ["add", "edit"].includes(value),
    },
    molecule: {
        type: Object,
        default: null,
    },
    prefillCas: {
        type: String,
        default: "",
    },
    prefillCasSmiles: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["close", "saved"]);

const page = usePage();

const activeTab = ref("editor");
const chemicalInput = ref("");
const detectedFormat = ref("");
const casInput = ref("");
const casError = ref("");
const casLoading = ref(false);
const errorMessage = ref("");
const saving = ref(false);
const fileLoading = ref(false);
const isFileDragging = ref(false);
const loadedFileName = ref("");
const structureFileInputRef = ref(null);
const structureCandidates = ref([]);
const selectedCandidateId = ref(null);
const editorHostRef = ref(null);
let editor = null;

const isEditMode = computed(() => props.mode === "edit");

function tabButtonClass(tab) {
    return [
        activeTab.value === tab
            ? "bg-white text-teal-800 shadow-sm ring-1 ring-gray-200/80"
            : "text-gray-600 hover:text-gray-900",
        "min-w-0 flex-1 rounded-md px-2 py-1.5 text-center text-xs font-medium transition-colors sm:text-sm",
    ];
}

function detectFormat(input) {
    if (!input || input.trim() === "") {
        return "";
    }

    const trimmed = input.trim();
    const fileFormat = detectStructureFormat(trimmed);

    if (fileFormat === "cdxml") {
        return "CDXML";
    }

    if (fileFormat === "sdf") {
        return trimmed.includes("$$$$") ? "SDF (multi)" : "SDF";
    }

    if (fileFormat === "mol") {
        return "MOL";
    }

    const lines = trimmed.split("\n");

    if (lines.length <= 2 && trimmed.length < 500) {
        const smilesPattern = /^[A-Za-z0-9@+\-\[\]()=#\\/\\.:]+$/;

        if (smilesPattern.test(trimmed.replace(/\s/g, ""))) {
            return "SMILES";
        }
    }

    return "SMILES";
}

function handleInput() {
    detectedFormat.value = chemicalInput.value.trim()
        ? detectFormat(chemicalInput.value)
        : "";
}

function clearPasteInput() {
    chemicalInput.value = "";
    detectedFormat.value = "";
    clearStructureCandidates();
}

function clearStructureCandidates() {
    structureCandidates.value = [];
    selectedCandidateId.value = null;
}

async function presentStructureCandidates(candidates, sourceLabel = "") {
    clearStructureCandidates();
    errorMessage.value = "";

    if (candidates.length === 0) {
        throw new Error("No structures were found in the input.");
    }

    structureCandidates.value = candidates.map((candidate) => ({
        ...candidate,
        source: candidate.source ?? "paste",
    }));

    if (sourceLabel) {
        loadedFileName.value = sourceLabel;
    }

    if (candidates.length === 1) {
        await selectStructureCandidate(candidates[0]);
        return;
    }

    selectedCandidateId.value = null;
}

async function selectStructureCandidate(candidate) {
    selectedCandidateId.value = candidate.id;
    chemicalInput.value = candidate.molfile;
    detectedFormat.value = "MOL";
    activeTab.value = "editor";
    errorMessage.value = "";

    await mountEditorWhenReady(true);

    if (!editor) {
        errorMessage.value =
            "Structure editor could not be initialized. Try again.";
        return;
    }

    try {
        editor.setMolFile(candidate.molfile);
    } catch {
        errorMessage.value =
            "Could not load the selected structure into the editor.";
    }
}

function openStructureFilePicker() {
    structureFileInputRef.value?.click();
}

async function loadStructureCandidatesFromText(text, fileName = "") {
    fileLoading.value = true;
    errorMessage.value = "";
    casError.value = "";

    try {
        const format = detectStructureFormat(text, fileName);

        if (format) {
            const candidates = await parseStructureText(text, fileName);
            await presentStructureCandidates(
                candidates,
                fileName || format.toUpperCase()
            );
            return;
        }

        const pasteFormat = detectFormat(text);

        if (pasteFormat === "SMILES") {
            activeTab.value = "editor";
            await mountEditorWhenReady(true);

            if (editor) {
                editor.setSmiles(text.trim());
            }

            clearStructureCandidates();
            return;
        }

        throw new Error(
            "Unsupported structure format. Use SMILES, MOL, SDF, or CDXML."
        );
    } catch (error) {
        errorMessage.value = error.message || "Could not parse the structure.";
        clearStructureCandidates();
    } finally {
        fileLoading.value = false;
    }
}

async function loadFromPaste() {
    if (!chemicalInput.value.trim()) {
        return;
    }

    await loadStructureCandidatesFromText(chemicalInput.value.trim());
}

async function handleStructureFile(file) {
    if (!file) {
        return;
    }

    fileLoading.value = true;
    errorMessage.value = "";

    try {
        const candidates = await parseStructureFile(file);
        await presentStructureCandidates(candidates, file.name);
    } catch (error) {
        errorMessage.value =
            error.message || "Could not read the structure file.";
        clearStructureCandidates();
    } finally {
        fileLoading.value = false;
        isFileDragging.value = false;

        if (structureFileInputRef.value) {
            structureFileInputRef.value.value = "";
        }
    }
}

async function handleStructureFileSelect(event) {
    const file = event.target.files?.[0];
    await handleStructureFile(file);
}

async function handleStructureFileDrop(event) {
    isFileDragging.value = false;
    const file = event.dataTransfer.files?.[0];
    await handleStructureFile(file);
}

function destroyEditor() {
    if (editor?.destroy) {
        editor.destroy();
    }

    editor = null;
}

async function mountEditor(force = false) {
    if (!editorHostRef.value) {
        return false;
    }

    if (editor && !force) {
        return true;
    }

    if (editor && force) {
        destroyEditor();
    }

    editor = await createStructureEditor(editorHostRef.value);
    await seedEditorFromMolecule();

    return true;
}

async function mountEditorWhenReady(force = false) {
    if (activeTab.value !== "editor") {
        return;
    }

    const attempt = async (retriesLeft) => {
        await nextTick();
        await new Promise((resolve) => {
            requestAnimationFrame(resolve);
        });

        if (!editorHostRef.value || activeTab.value !== "editor") {
            if (retriesLeft > 0) {
                return attempt(retriesLeft - 1);
            }

            return;
        }

        const { width, height } = editorHostRef.value.getBoundingClientRect();

        if (width < 4 || height < 4) {
            if (retriesLeft > 0) {
                return attempt(retriesLeft - 1);
            }

            return;
        }

        await mountEditor(force);
    };

    await attempt(24);
}

async function seedEditorFromMolecule() {
    if (!editor || !isEditMode.value || !props.molecule) {
        return;
    }

    const mol = props.molecule;

    if (mol.sdf?.trim()) {
        editor.setMolFile(mol.sdf);
        return;
    }

    if (mol.canonical_smiles?.trim()) {
        editor.setSmiles(mol.canonical_smiles);
        return;
    }

    if (isEditMode.value) {
        chemicalInput.value = "";
        activeTab.value = "editor";
    }
}

async function applyPasteToEditor() {
    if (!chemicalInput.value.trim()) {
        return;
    }

    if (editor && selectedCandidateId.value) {
        editor.setMolFile(chemicalInput.value);
        return;
    }

    await loadStructureCandidatesFromText(chemicalInput.value.trim());
}

function clearCasInput() {
    casInput.value = "";
    casError.value = "";
}

async function fetchFromCas(casNumber) {
    const response = await axios.get("/cas/detail", {
        params: { cas_rn: casNumber },
        timeout: 30000,
    });

    return response.data;
}

async function importFromCas() {
    if (!casInput.value.trim()) {
        casError.value = "Please enter a CAS Registry Number";
        return;
    }

    const casNumber = casInput.value.trim();
    casLoading.value = true;
    casError.value = "";
    errorMessage.value = "";

    try {
        const casData = await fetchFromCas(casNumber);
        const smiles = casData.smile || casData.canonicalSmile;

        if (!smiles) {
            casError.value = `No structural data (SMILES) available for CAS number ${casNumber}`;
            return;
        }

        chemicalInput.value = smiles;
        detectedFormat.value = "SMILES (from CAS)";
        activeTab.value = "editor";

        await mountEditorWhenReady(true);

        if (editor) {
            editor.setSmiles(smiles);
        }
    } catch (error) {
        casError.value =
            error.response?.data?.error ||
            error.response?.data?.message ||
            error.message ||
            "CAS lookup failed. Please verify the number and try again.";
    } finally {
        casLoading.value = false;
    }
}

function resetForm() {
    activeTab.value = "editor";
    chemicalInput.value = "";
    detectedFormat.value = "";
    casInput.value = "";
    casError.value = "";
    casLoading.value = false;
    fileLoading.value = false;
    isFileDragging.value = false;
    loadedFileName.value = "";
    clearStructureCandidates();
    errorMessage.value = "";
    saving.value = false;
}

async function applyStructurePrefill() {
    if (isEditMode.value) {
        return;
    }

    const cas = props.prefillCas?.trim();

    if (!cas) {
        return;
    }

    casInput.value = cas;

    const smiles = props.prefillCasSmiles?.trim();

    if (smiles) {
        chemicalInput.value = smiles;
        detectedFormat.value = "SMILES (from CAS)";
        activeTab.value = "editor";
        await mountEditorWhenReady(true);

        if (editor) {
            editor.setSmiles(smiles);
        }

        return;
    }

    activeTab.value = "cas";
}

function handleClose() {
    if (saving.value) {
        return;
    }

    resetForm();
    destroyEditor();
    emit("close");
}

async function saveStructure() {
    if (!props.study?.id || saving.value) {
        return;
    }

    saving.value = true;
    errorMessage.value = "";

    try {
        if (
            structureCandidates.value.length > 1 &&
            !selectedCandidateId.value
        ) {
            errorMessage.value = "Select which structure to use before saving.";
            return;
        }

        if (activeTab.value === "cas") {
            if (!editor) {
                errorMessage.value =
                    "Import a structure from CAS before saving.";
                return;
            }
        } else if (activeTab.value === "file") {
            if (!editor) {
                errorMessage.value =
                    "Upload a structure file and select a structure before saving.";
                return;
            }
        } else if (activeTab.value === "paste") {
            await applyPasteToEditor();
        }

        if (!editor) {
            activeTab.value = "editor";
            await mountEditorWhenReady();
        }

        if (!editor) {
            throw new Error("Structure editor is not ready yet.");
        }

        const molfile = await validateEditorMolfile(editor);
        const { data: standardized } = await axios.post(
            page.props.chemistryStandardizeUrl,
            molfile,
            {
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
            }
        );

        if (isEditMode.value && props.molecule?.id) {
            await axios.delete(
                `/dashboard/studies/${props.study.id}/molecule/${props.molecule.id}`
            );
        }

        const percentage = isEditMode.value
            ? props.molecule?.pivot?.percentage_composition ?? 0
            : 0;

        const { data } = await axios.post(
            `/dashboard/studies/${props.study.id}/molecule`,
            {
                InChI: standardized.inchi,
                InChIKey: standardized.inchikey,
                percentage,
                mol: standardized.standardized_mol,
                canonical_smiles: standardized.canonical_smiles,
            }
        );

        emit("saved", Array.isArray(data) ? data : data.molecules);
        resetForm();
        destroyEditor();
        emit("close");
    } catch (error) {
        const detail =
            error.response?.data?.detail ||
            error.response?.data?.error ||
            error.response?.data?.message;

        errorMessage.value =
            detail ||
            error.message ||
            "Could not save the structure. Draw or import a valid structure and try again.";
    } finally {
        saving.value = false;
    }
}

async function validateEditorMolfile(editorApi) {
    const editorMolecule = editorApi.getMolecule();

    if (!editorMolecule || editorMolecule.getAtoms() < 1) {
        throw new Error(
            "The structure is empty. Draw or import a molecule before saving."
        );
    }

    let molfile = editorApi.getMolFile()?.trim();

    if (!molfile) {
        return editorMolecule.toMolfile();
    }

    const OCL = await loadOpenChemLib();
    const { molecule: parsed, normalized } = moleculeFromMolfileOrText(
        molfile,
        OCL
    );

    if (parsed?.getAtoms() >= 1) {
        return normalized;
    }

    return editorMolecule.toMolfile();
}

watch(
    () => props.show,
    async (visible) => {
        if (!visible) {
            destroyEditor();
            return;
        }

        resetForm();

        if (isEditMode.value && props.molecule) {
            if (props.molecule.cas) {
                casInput.value = props.molecule.cas;
            }

            if (props.molecule.canonical_smiles) {
                chemicalInput.value = props.molecule.canonical_smiles;
                detectedFormat.value = detectFormat(chemicalInput.value);
            }
        } else {
            await applyStructurePrefill();
        }

        await nextTick();

        if (activeTab.value === "editor") {
            await mountEditorWhenReady();
        }
    }
);

watch(
    () => props.molecule,
    async () => {
        if (!props.show || activeTab.value !== "editor" || !editor) {
            return;
        }

        await seedEditorFromMolecule();
    }
);

watch(activeTab, async (tab, previousTab) => {
    if (!props.show) {
        return;
    }

    if (previousTab === "editor" && tab !== "editor") {
        destroyEditor();
        return;
    }

    if (tab === "editor") {
        await mountEditorWhenReady();
    }
});

onBeforeUnmount(() => {
    destroyEditor();
});
</script>
