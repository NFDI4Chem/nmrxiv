<template>
    <div
        class="rounded-2xl border-2 border-dashed p-8 text-center transition"
        :class="
            dragging
                ? 'border-teal-500 bg-teal-50'
                : 'border-gray-300 bg-white hover:border-gray-400'
        "
        @dragenter.prevent="onDragEnter"
        @dragover.prevent="dragging = true"
        @dragleave.prevent="onDragLeave"
        @drop.prevent="onDrop"
    >
        <div class="mx-auto max-w-lg space-y-4">
            <h2 class="text-lg font-semibold text-gray-900">
                Open a BagIt folder or zip
            </h2>
            <p class="text-sm text-gray-600">
                Drag and drop a folder, one or more
                <code class="rounded bg-gray-100 px-1">.zip</code>
                BagIt archives, or an extracted bag folder. Everything stays on
                your computer — nothing is uploaded.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <button
                    type="button"
                    class="inline-flex items-center rounded-lg bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                    @click="pickFolder"
                >
                    Open folder
                </button>
                <button
                    type="button"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                    @click="pickZips"
                >
                    Open zip files
                </button>
            </div>
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
            <p v-if="busy" class="text-sm text-gray-500">Reading files…</p>
        </div>

        <input
            ref="folderInput"
            type="file"
            class="hidden"
            webkitdirectory
            directory
            multiple
            @change="onFolderPicked"
        />
        <input
            ref="zipInput"
            type="file"
            class="hidden"
            accept=".zip,application/zip"
            multiple
            @change="onZipsPicked"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import {
    entriesFromDataTransfer,
    entriesFromFileList,
} from '../bag/sources.js';
import { discoverBags, findZipEntries } from '../bag/discover.js';
import { bagsFromZipFiles } from '../bag/openZip.js';

const emit = defineEmits(['bags']);

const folderInput = ref(null);
const zipInput = ref(null);
const dragging = ref(false);
const busy = ref(false);
const error = ref(null);
let dragDepth = 0;

function onDragEnter() {
    dragDepth += 1;
    dragging.value = true;
}

function pickFolder() {
    folderInput.value?.click();
}

function pickZips() {
    zipInput.value?.click();
}

function onDragLeave() {
    dragDepth = Math.max(0, dragDepth - 1);
    if (dragDepth === 0) {
        dragging.value = false;
    }
}

async function onDrop(event) {
    dragging.value = false;
    dragDepth = 0;
    await ingestEntries(await entriesFromDataTransfer(event.dataTransfer));
}

async function onFolderPicked(event) {
    const files = event.target.files;
    event.target.value = '';
    await ingestEntries(entriesFromFileList(files));
}

async function onZipsPicked(event) {
    const files = Array.from(event.target.files || []);
    event.target.value = '';
    await ingestZipFiles(files);
}

async function ingestEntries(entries) {
    error.value = null;
    busy.value = true;

    try {
        const bags = [];
        const discovered = discoverBags(entries, { kind: 'folder' });
        bags.push(...discovered);

        const zips = findZipEntries(entries);
        for (const zipEntry of zips) {
            const blob = await zipEntry.getBlob();
            const zipName = basenamePath(zipEntry.path);
            const file = new File([blob], zipName, {
                type: 'application/zip',
            });
            const zipBags = await bagsFromZipFiles([file]);
            bags.push(...zipBags);
        }

        if (bags.length === 0) {
            error.value =
                'No BagIt bags found. Look for a folder containing bagit.txt, or a .zip archive of a bag.';

            return;
        }

        emit('bags', bags);
    } catch (err) {
        error.value = err?.message || 'Failed to read files';
    } finally {
        busy.value = false;
    }
}

async function ingestZipFiles(files) {
    error.value = null;
    busy.value = true;

    try {
        const bags = await bagsFromZipFiles(files);

        if (bags.length === 0) {
            error.value =
                'Those zip files do not contain a BagIt bag (no bagit.txt).';

            return;
        }

        emit('bags', bags);
    } catch (err) {
        error.value = err?.message || 'Failed to read zip files';
    } finally {
        busy.value = false;
    }
}

function basenamePath(path) {
    const parts = String(path).replace(/\\/g, '/').split('/');

    return parts[parts.length - 1] || path;
}
</script>
