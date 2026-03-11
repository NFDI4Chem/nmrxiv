<template>
    <div class="flex flex-col h-full">
        <!-- Title Section -->
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Spectra Search</h2>
            <p class="mt-2 text-gray-600">
                Upload your NMR spectra files to search
            </p>
        </div>

        <!-- Upload Area -->
        <label
            class="relative flex flex-col items-center justify-center min-h-[450px] border-2 border-dashed rounded-2xl transition-all cursor-pointer"
            :class="
                isDragging
                    ? 'border-gray-900 bg-gray-50'
                    : 'border-gray-300 bg-gray-50/50 hover:border-gray-400 hover:bg-gray-50'
            "
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
        >
            <input
                ref="fileInput"
                type="file"
                multiple
                accept=".jdx,.dx,.jdf,.fid,.zip,.nmrML,.mnova"
                class="sr-only"
                @change="handleFileSelect"
            />

            <div
                class="w-20 h-20 mb-6 rounded-full bg-gray-100 flex items-center justify-center"
            >
                <svg
                    class="w-10 h-10 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                    />
                </svg>
            </div>

            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                Drop spectra files here
            </h3>
            <p class="text-sm text-gray-500 mb-6 text-center max-w-sm">
                or click to browse from your computer
            </p>

            <span
                class="px-6 py-3 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors inline-flex items-center pointer-events-none"
            >
                <svg
                    class="w-5 h-5 mr-2"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>
                Select Files
            </span>

            <p class="mt-4 text-xs text-gray-400">
                Supported formats: JCAMP-DX, Bruker, JEOL, nmrML, ZIP
            </p>

            <!-- Selected Files Count -->
            <div
                v-if="uploadedFiles.length > 0"
                class="absolute top-4 right-4 px-3 py-1.5 bg-gray-900 text-white text-sm font-medium rounded-full"
            >
                {{ uploadedFiles.length }} file{{
                    uploadedFiles.length > 1 ? "s" : ""
                }}
                selected
            </div>
        </label>
    </div>
</template>

<script>
import { ref } from "vue";

export default {
    emits: ["files-uploaded"],
    setup(props, { emit }) {
        const isDragging = ref(false);
        const uploadedFiles = ref([]);
        const fileInput = ref(null);

        const handleFileSelect = (event) => {
            const files = Array.from(event.target.files);
            addFiles(files);
            // Reset input
            fileInput.value.value = "";
        };

        const handleDrop = (event) => {
            isDragging.value = false;
            const files = Array.from(event.dataTransfer.files);
            addFiles(files);
        };

        const addFiles = (files) => {
            const validExtensions = [
                ".jdx",
                ".dx",
                ".jdf",
                ".fid",
                ".zip",
                ".nmrml",
                ".mnova",
            ];
            const validFiles = files.filter((file) => {
                const fileName = file.name.toLowerCase();
                return validExtensions.some((ext) => fileName.endsWith(ext));
            });

            if (validFiles.length !== files.length) {
                alert(
                    "Some files were not added. Only JCAMP-DX, Bruker, JEOL, nmrML, and ZIP files are supported."
                );
            }

            uploadedFiles.value = [...uploadedFiles.value, ...validFiles];
            emit("files-uploaded", uploadedFiles.value);
        };

        return {
            isDragging,
            uploadedFiles,
            fileInput,
            handleFileSelect,
            handleDrop,
        };
    },
};
</script>
