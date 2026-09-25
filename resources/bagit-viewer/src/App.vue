<template>
    <div class="min-h-screen">
        <header class="border-b border-gray-200 bg-white">
            <div
                class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6 lg:px-8"
            >
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wide text-teal-700"
                    >
                        nmrXiv
                    </p>
                    <h1 class="text-xl font-bold text-gray-900">
                        BagIt Viewer
                    </h1>
                </div>
                <p class="text-xs text-gray-500">
                    v{{ version }} · built {{ buildDate }}
                </p>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div v-if="!bags.length" class="space-y-10">
                <BagPicker @bags="onBags" />
                <RecentZips
                    v-if="recent.length"
                    :items="recent"
                    :busy-path="openingPath"
                    :error="recentError"
                    @clear="clearHistory"
                    @open="openRecent"
                />
            </div>

            <div v-else class="grid gap-8 lg:grid-cols-12">
                <div class="space-y-8 lg:col-span-4 xl:col-span-3">
                    <BagList
                        :bags="bags"
                        :selected-id="selected?.id"
                        @select="selected = $event"
                        @clear="clear"
                    />
                    <RecentZips
                        v-if="recent.length"
                        :items="recent"
                        compact
                        :busy-path="openingPath"
                        :error="recentError"
                        @clear="clearHistory"
                        @open="openRecent"
                    />
                </div>
                <div class="lg:col-span-8 xl:col-span-9">
                    <SampleView
                        v-if="selected"
                        :bag="selected"
                        :key="selected.id"
                    />
                    <p v-else class="text-sm text-gray-500">
                        Select a bag to validate and view.
                    </p>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import BagPicker from './components/BagPicker.vue';
import BagList from './components/BagList.vue';
import SampleView from './components/SampleView.vue';
import RecentZips from './components/RecentZips.vue';
import {
    clearRecentZips,
    displayNameFromPath,
    loadRecentZipFile,
    loadRecentZips,
    rememberZip,
} from './bag/history.js';
import { bagsFromZipFiles } from './bag/openZip.js';

const version =
    typeof __BAGIT_VIEWER_VERSION__ !== 'undefined'
        ? // eslint-disable-next-line no-undef -- injected by Vite define
          __BAGIT_VIEWER_VERSION__
        : '1.0.0';
const buildDate =
    typeof __BAGIT_VIEWER_BUILD_DATE__ !== 'undefined'
        ? // eslint-disable-next-line no-undef -- injected by Vite define
          __BAGIT_VIEWER_BUILD_DATE__
        : '';

const bags = ref([]);
const selected = ref(null);
const recent = ref([]);
const openingPath = ref(null);
const recentError = ref(null);

onMounted(() => {
    recent.value = loadRecentZips();
});

function onBags(found) {
    replaceBags(found);
    void recordOpenedZips(found);
}

function replaceBags(found) {
    for (const bag of bags.value) {
        if (!found.includes(bag)) {
            bag._dispose?.();
        }
    }
    bags.value = found;
    selected.value = found[0] || null;
    recentError.value = null;
}

async function recordOpenedZips(found) {
    let updated = false;
    for (const bag of found) {
        if (bag.kind !== 'zip') {
            continue;
        }

        const sourceFile = bag._sourceFile;
        if (sourceFile instanceof Blob) {
            recent.value = await rememberZip({ file: sourceFile });
            updated = true;
            continue;
        }

        if (bag.zipName) {
            recent.value = await rememberZip({
                name: displayNameFromPath(bag.zipName),
                path: bag.zipName,
            });
            updated = true;
        }
    }
    if (!updated) {
        recent.value = loadRecentZips();
    }
}

async function openRecent(item) {
    if (!item?.path || openingPath.value) {
        return;
    }

    openingPath.value = item.path;
    recentError.value = null;

    try {
        const file = await loadRecentZipFile(item.path);
        if (!file) {
            recentError.value =
                'Cached copy not found. Open the zip once more with “Open zip files”.';

            return;
        }

        const found = await bagsFromZipFiles([file]);
        if (found.length === 0) {
            recentError.value = 'That cached zip no longer contains a BagIt bag.';

            return;
        }

        replaceBags(found);
        recent.value = await rememberZip({ file });
    } catch (error) {
        recentError.value = error?.message || 'Failed to open recent zip';
    } finally {
        openingPath.value = null;
    }
}

async function clearHistory() {
    recent.value = await clearRecentZips();
    recentError.value = null;
}

function clear() {
    for (const bag of bags.value) {
        bag._dispose?.();
    }
    bags.value = [];
    selected.value = null;
    recent.value = loadRecentZips();
    recentError.value = null;
}
</script>
