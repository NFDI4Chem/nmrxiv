<template>
    <div class="flex flex-col h-full">
        <!-- Title Section -->
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Structure Search</h2>
            <p class="mt-2 text-gray-600">
                Draw, paste, or import a chemical structure
            </p>
        </div>

        <!-- Input Options -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-3">
            <!-- File Upload -->
            <label
                class="relative flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition-colors"
                :class="isDragging ? 'border-gray-900 bg-gray-50' : ''"
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="handleDrop"
            >
                <svg
                    class="w-4 h-4 mr-2"
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
                class="flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                @click="pasteFromClipboard"
            >
                <svg
                    class="w-4 h-4 mr-2"
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

        <!-- Structure Editor Card -->
        <div
            :id="editorId"
            class="w-full bg-white rounded-xl border border-gray-200 shadow-sm mb-4"
            style="height: 450px"
        />

        <!-- Search Type Selection -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Search Type
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Exact Match -->
                <label
                    for="search-type-exact"
                    class="relative flex items-start p-4 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-gray-300 transition-all"
                    :class="
                        searchType === 'exact'
                            ? 'border-gray-900 bg-gray-50'
                            : ''
                    "
                >
                    <input
                        id="search-type-exact"
                        name="search-type"
                        value="exact"
                        type="radio"
                        :checked="searchType === 'exact'"
                        class="sr-only"
                        @input="$emit('update:searchType', 'exact')"
                    />
                    <div class="flex-1">
                        <span class="block text-sm font-semibold text-gray-900">
                            Exact Match
                        </span>
                        <span class="block text-xs text-gray-500 mt-1">
                            Find identical structures
                        </span>
                    </div>
                </label>

                <!-- Substructure Search -->
                <label
                    for="search-type-sub"
                    class="relative flex items-start p-4 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-gray-300 transition-all"
                    :class="
                        searchType === 'substructure'
                            ? 'border-gray-900 bg-gray-50'
                            : ''
                    "
                >
                    <input
                        id="search-type-sub"
                        name="search-type"
                        type="radio"
                        value="substructure"
                        :checked="searchType === 'substructure'"
                        class="sr-only"
                        @input="$emit('update:searchType', 'substructure')"
                    />
                    <div class="flex-1">
                        <span class="block text-sm font-semibold text-gray-900">
                            Substructure
                        </span>
                        <span class="block text-xs text-gray-500 mt-1">
                            Find containing structures
                        </span>
                    </div>
                </label>

                <!-- Similarity Search -->
                <label
                    for="search-type-similar"
                    class="relative flex items-start p-4 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-gray-300 transition-all"
                    :class="
                        searchType === 'similarity'
                            ? 'border-gray-900 bg-gray-50'
                            : ''
                    "
                >
                    <input
                        id="search-type-similar"
                        name="search-type"
                        value="similarity"
                        type="radio"
                        :checked="searchType === 'similarity'"
                        class="sr-only"
                        @input="$emit('update:searchType', 'similarity')"
                    />
                    <div class="flex-1">
                        <span class="block text-sm font-semibold text-gray-900">
                            Similarity
                        </span>
                        <span class="block text-xs text-gray-500 mt-1">
                            Find similar structures
                        </span>
                    </div>
                </label>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from "vue";

export default {
    props: {
        editorId: {
            type: String,
            default: "structureSearchEditor",
        },
        searchType: {
            type: String,
            default: "exact",
        },
        editor: {
            type: Object,
            default: null,
        },
    },
    emits: ["update:searchType"],
    setup(props) {
        const isDragging = ref(false);
        const fileInput = ref(null);

        const handleFileSelect = async (event) => {
            const file = event.target.files[0];
            if (!file) return;
            await loadFile(file);
        };

        const handleDrop = async (event) => {
            isDragging.value = false;
            const file = event.dataTransfer.files[0];
            if (!file) return;
            await loadFile(file);
        };

        const loadFile = async (file) => {
            if (!props.editor) return;

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
                props.editor.setMolFile(text);
            } catch (error) {
                console.error("Error loading file:", error);
                alert("Error loading file");
            }
        };

        const pasteFromClipboard = async () => {
            if (!props.editor) return;

            try {
                const text = await navigator.clipboard.readText();

                // Try as SMILES first
                try {
                    const OCL = (await import("openchemlib/full")).default;
                    const mol = OCL.Molecule.fromSmiles(text.trim());
                    props.editor.setMolFile(mol.toMolfile());
                    return;
                } catch {
                    // If not SMILES, try as MOL file
                    if (text.includes("M  END") || text.includes("$$$$")) {
                        props.editor.setMolFile(text);
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
            isDragging,
            fileInput,
            handleFileSelect,
            handleDrop,
            pasteFromClipboard,
        };
    },
};
</script>
