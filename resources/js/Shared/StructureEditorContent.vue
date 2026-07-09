<template>
    <div class="flex min-h-0 flex-col" :class="compact ? '' : 'h-full'">
        <!-- Title Section -->
        <div v-if="!compact" class="mb-4 shrink-0">
            <h2 class="text-3xl font-bold text-gray-900">Structure Search</h2>
            <p class="mt-1 text-gray-600">
                Draw, paste, or import a chemical structure
            </p>
        </div>

        <!-- Input Options -->
        <div class="mb-4 grid shrink-0 grid-cols-1 gap-3 md:grid-cols-2">
            <!-- File Upload -->
            <label
                class="relative flex items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:border-gray-400 hover:bg-gray-50 cursor-pointer"
                :class="isDragging ? 'border-gray-900 bg-gray-50' : ''"
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

            <!-- Clipboard Paste -->
            <button
                type="button"
                class="flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                :disabled="!editorReady"
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

        <!-- Structure Editor (fills remaining modal height) -->
        <div
            v-if="initError"
            class="mb-4 flex h-[360px] w-full items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 text-center text-sm text-red-700"
            :class="compact ? 'h-[360px]' : 'min-h-[360px] flex-1'"
        >
            Structure editor failed to load. Restart the dev server after
            running npm install.
        </div>
        <div
            v-else
            :id="editorId"
            ref="editorHostRef"
            class="mb-4 w-full shrink-0 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
            :class="compact ? 'h-[360px]' : 'min-h-0 flex-1'"
        />

        <!-- Search Type Selection -->
        <div class="shrink-0">
            <h3 class="mb-3 text-sm font-semibold text-gray-900">
                How should your structure match?
            </h3>

            <div
                class="grid grid-cols-1 gap-2 sm:grid-cols-3"
                role="radiogroup"
                aria-label="Structure search type"
            >
                <button
                    v-for="option in searchTypeOptions"
                    :key="option.value"
                    type="button"
                    role="radio"
                    :aria-checked="searchType === option.value"
                    class="group relative flex items-start gap-2.5 rounded-lg px-3 py-3 text-left transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2"
                    :class="
                        searchType === option.value
                            ? 'bg-white shadow-md ring-2 ring-gray-900'
                            : 'bg-white/70 ring-1 ring-gray-200/90 hover:bg-white hover:shadow-sm hover:ring-gray-300'
                    "
                    @click="selectSearchType(option.value)"
                >
                    <span
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg transition-colors"
                        :class="
                            searchType === option.value
                                ? 'bg-gray-900 text-white'
                                : 'bg-gray-100 text-gray-600 group-hover:bg-gray-200'
                        "
                    >
                        <component
                            :is="option.icon"
                            class="h-5 w-5"
                            aria-hidden="true"
                        />
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-semibold text-gray-900">
                            {{ option.label }}
                        </span>
                        <span
                            class="mt-0.5 block text-xs leading-snug text-gray-500"
                        >
                            {{ option.description }}
                        </span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, onBeforeUnmount, nextTick, watch } from "vue";
import {
    CheckBadgeIcon,
    ViewfinderCircleIcon,
    SparklesIcon,
} from "@heroicons/vue/24/outline";
import {
    createStructureEditor,
    loadOpenChemLib,
    STRUCTURE_SEARCH_EDITOR_OPTIONS,
} from "@/Utils/structureEditor";

const searchTypeOptions = [
    {
        value: "exact",
        label: "Exact match",
        description: "Same atoms, bonds, and stereochemistry",
        icon: CheckBadgeIcon,
    },
    {
        value: "substructure",
        label: "Substructure",
        description: "Records that contain your drawn fragment",
        icon: ViewfinderCircleIcon,
    },
    {
        value: "similarity",
        label: "Similarity",
        description: "Structurally related compounds by fingerprint",
        icon: SparklesIcon,
    },
];

export default {
    props: {
        compact: {
            type: Boolean,
            default: false,
        },
        editorId: {
            type: String,
            default: "structureSearchEditor",
        },
        searchType: {
            type: String,
            default: "exact",
        },
        initialSmiles: {
            type: String,
            default: null,
        },
    },
    emits: ["update:searchType", "ready"],
    setup(props, { emit }) {
        const isDragging = ref(false);
        const fileInput = ref(null);
        const editorHostRef = ref(null);
        const editorReady = ref(false);
        const initError = ref(null);

        let editorApi = null;

        const applySmiles = async (smiles) => {
            if (!editorApi || !smiles?.trim()) {
                return;
            }

            const OCL = await loadOpenChemLib();
            editorApi.setMolFile(
                OCL.Molecule.fromSmiles(smiles.trim()).toMolfile()
            );
        };

        const mountEditor = async () => {
            if (editorApi) {
                return true;
            }

            if (!editorHostRef.value) {
                return false;
            }

            try {
                editorApi = await createStructureEditor(
                    editorHostRef.value,
                    STRUCTURE_SEARCH_EDITOR_OPTIONS
                );
                editorReady.value = true;
                emit("ready", editorApi);

                if (props.initialSmiles) {
                    await applySmiles(props.initialSmiles);
                }

                return true;
            } catch (error) {
                console.error("Failed to initialize structure editor:", error);
                initError.value = error;

                return true;
            }
        };

        const mountEditorWhenReady = async () => {
            await nextTick();

            if (await mountEditor()) {
                return;
            }

            await new Promise((resolve) => {
                requestAnimationFrame(resolve);
            });

            if (await mountEditor()) {
                return;
            }

            await new Promise((resolve) => {
                requestAnimationFrame(resolve);
            });

            await mountEditor();
        };

        onMounted(() => {
            mountEditorWhenReady();
        });

        onBeforeUnmount(() => {
            if (editorApi?.destroy) {
                editorApi.destroy();
            }

            editorApi = null;
        });

        watch(
            () => props.initialSmiles,
            (smiles) => {
                if (smiles) {
                    applySmiles(smiles);
                }
            }
        );

        const selectSearchType = (value) => {
            emit("update:searchType", value);
        };

        const handleFileSelect = async (event) => {
            const file = event.target.files[0];
            if (!file) {
                return;
            }

            await loadFile(file);
        };

        const handleDrop = async (event) => {
            isDragging.value = false;
            const file = event.dataTransfer.files[0];
            if (!file) {
                return;
            }

            await loadFile(file);
        };

        const loadFile = async (file) => {
            if (!editorApi) {
                return;
            }

            const validExtensions = [".mol", ".sdf", ".sd"];
            const fileName = file.name.toLowerCase();
            const isValid = validExtensions.some((ext) =>
                fileName.endsWith(ext)
            );

            if (!isValid) {
                alert("Please upload a MOL or SDF file");
                return;
            }

            try {
                const text = await file.text();
                editorApi.setMolFile(text);
            } catch (error) {
                console.error("Error loading file:", error);
                alert("Error loading file");
            }
        };

        const pasteFromClipboard = async () => {
            if (!editorApi) {
                return;
            }

            try {
                const text = await navigator.clipboard.readText();

                try {
                    const OCL = await loadOpenChemLib();
                    const mol = OCL.Molecule.fromSmiles(text.trim());
                    editorApi.setMolFile(mol.toMolfile());
                    return;
                } catch {
                    if (text.includes("M  END") || text.includes("$$$$")) {
                        editorApi.setMolFile(text);
                    } else {
                        alert(
                            "Clipboard content is not a valid SMILES or MOL format"
                        );
                    }
                }
            } catch (error) {
                console.error("Error reading clipboard:", error);
                alert(
                    "Unable to read clipboard. Please allow clipboard access."
                );
            }
        };

        return {
            searchTypeOptions,
            isDragging,
            fileInput,
            editorHostRef,
            editorReady,
            initError,
            selectSearchType,
            handleFileSelect,
            handleDrop,
            pasteFromClipboard,
        };
    },
};
</script>
