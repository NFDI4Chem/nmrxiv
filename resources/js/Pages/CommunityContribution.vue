<template>
    <app-layout title="Community Contribution">
        <template #navbar>
            <div class="flex min-w-0 flex-col justify-center gap-0.5">
                <p
                    class="text-[10px] font-semibold uppercase leading-none tracking-[0.15em] text-teal-600"
                >
                    Deposit data
                </p>
                <div class="flex min-w-0 items-center gap-2">
                    <h1 class="shrink-0 text-sm font-semibold text-gray-900">
                        Community Contribution
                    </h1>
                    <span
                        class="hidden min-w-0 truncate text-xs text-gray-500 md:inline"
                    >
                        Contribute raw or processed spectra for open science.
                    </span>
                </div>
            </div>
        </template>

        <div class="flex min-h-0 flex-1 flex-col">
            <div
                v-if="filesErrorMessage"
                class="mx-5 mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900"
                role="alert"
            >
                {{ filesErrorMessage }}
            </div>

            <div
                v-if="processing"
                class="mx-5 mt-4 rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-900"
                role="status"
            >
                Processing uploaded files…
            </div>

            <div
                v-if="submitSuccessMessage"
                class="mx-5 mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900"
                role="status"
            >
                {{ submitSuccessMessage }}
            </div>

            <div
                v-if="!filesStateReady"
                class="flex min-h-[500px] flex-1 items-center justify-center border-t border-gray-200"
                role="status"
                aria-live="polite"
            >
                <ArrowPathIcon
                    class="h-8 w-8 animate-spin text-teal-600"
                    aria-hidden="true"
                />
                <span class="sr-only">Loading workspace…</span>
            </div>

            <div
                v-else
                class="flex min-h-0 flex-1 overflow-hidden"
                :class="
                    hasFiles
                        ? 'min-h-[500px] flex-row border-t border-gray-200'
                        : 'flex-col px-5 py-4'
                "
            >
                <div
                    ref="sidebarColumnRef"
                    class="flex min-h-0 flex-col"
                    :class="
                        hasFiles
                            ? 'h-full w-72 shrink-0 border-r border-gray-200 bg-white'
                            : 'min-h-0 flex-1'
                    "
                >
                    <div
                        v-if="hasFiles"
                        ref="uploadSectionRef"
                        class="shrink-0 border-b border-gray-200 bg-white p-3"
                    >
                        <div class="flex flex-col gap-2">
                            <button
                                type="button"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2"
                                @click="openUploadModal"
                            >
                                <ArrowUpTrayIcon
                                    class="h-4 w-4 shrink-0"
                                    aria-hidden="true"
                                />
                                Upload data
                            </button>
                            <button
                                v-if="publishableStudies.length > 0"
                                type="button"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-green-700 focus-visible:ring-offset-2"
                                @click="openSubmitModal"
                            >
                                Submit data
                                <span
                                    class="rounded-full bg-green-900/40 px-2 py-0.5 text-xs font-medium"
                                >
                                    {{ publishableStudies.length }}
                                </span>
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="hasFiles"
                        class="shrink-0 overflow-hidden"
                        :style="{ height: `${fileTreeHeight}px` }"
                    >
                        <file-system-browser
                            ref="fsbRef"
                            :key="`sidebar-${currentDraft.id}`"
                            :draft="currentDraft"
                            :readonly="true"
                            tree-only
                            :studies="studies"
                            :submitted-study-ids="submittedStudyIdsList"
                            :studies-workspace-ready="studiesWorkspaceReady"
                            :draft-processing="processing"
                            :height="'h-full min-h-0 w-full'"
                            @loading="onSidebarFilesLoading"
                            @sample-folder-selected="onSampleFolderSelected"
                            @sample-folder-reset="onSampleFolderReset"
                        />
                    </div>

                    <div
                        v-if="hasFiles"
                        class="group relative h-1 shrink-0 cursor-row-resize bg-gray-100 transition-colors hover:bg-teal-400 active:bg-teal-500"
                        role="separator"
                        aria-orientation="horizontal"
                        aria-label="Resize structure section"
                        @mousedown="startStructureResize"
                    >
                        <div
                            class="absolute inset-x-0 -top-1 -bottom-1 group-hover:bg-teal-400/10"
                        ></div>
                    </div>

                    <div
                        v-if="!hasFiles"
                        class="min-h-0 flex-1 overflow-hidden"
                    >
                        <file-system-browser
                            ref="fsbRef"
                            :key="`upload-${currentDraft.id}`"
                            :draft="currentDraft"
                            :height="'flex-1 min-h-0 w-full'"
                            @loading="onUploadFilesLoading"
                        />
                    </div>

                    <div
                        v-if="hasFiles"
                        class="flex min-h-[180px] min-w-0 flex-1 flex-col overflow-hidden bg-white p-3"
                    >
                        <div
                            class="mb-2 flex shrink-0 items-center justify-between gap-2"
                        >
                            <h2
                                class="text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Structure
                            </h2>
                            <div
                                v-if="
                                    hasStructureAssigned &&
                                    hasSelectedCompleteStudy
                                "
                                class="flex shrink-0 items-center gap-3"
                            >
                                <button
                                    type="button"
                                    class="text-xs font-medium text-teal-600 transition hover:text-teal-700"
                                    @click="openEditStructureModal"
                                >
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    class="text-xs font-medium text-red-600 transition hover:text-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                                    :disabled="structureDeleting"
                                    @click="confirmDeleteStructure"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                        <div
                            ref="structureViewerRef"
                            class="relative flex min-h-[140px] flex-1 flex-col overflow-hidden bg-white"
                        >
                            <div
                                v-if="structureLoading"
                                class="flex min-h-0 flex-1 items-center justify-center"
                                role="status"
                                aria-live="polite"
                            >
                                <ArrowPathIcon
                                    class="h-8 w-8 animate-spin text-teal-600"
                                    aria-hidden="true"
                                />
                                <span class="sr-only">Loading structure…</span>
                            </div>
                            <div
                                v-else-if="
                                    hasStructureAssigned &&
                                    hasSelectedCompleteStudy
                                "
                                class="relative flex min-h-0 flex-1 flex-col overflow-hidden"
                            >
                                <div
                                    v-if="structureDepictionLoading"
                                    class="absolute inset-0 z-10 flex items-center justify-center bg-white"
                                    role="status"
                                    aria-live="polite"
                                >
                                    <ArrowPathIcon
                                        class="h-8 w-8 animate-spin text-teal-600"
                                        aria-hidden="true"
                                    />
                                    <span class="sr-only"
                                        >Loading structure…</span
                                    >
                                </div>
                                <div
                                    class="flex h-full min-h-[120px] w-full items-center justify-center overflow-hidden"
                                >
                                    <Depictor2D
                                        :key="`${
                                            selectedStudy?.id ?? 'none'
                                        }-${structureSmiles}`"
                                        source="ocl"
                                        class="h-full w-full"
                                        :molecule="structureSmiles"
                                        :height="structureDepictorHeight"
                                        :width="structureDepictorWidth"
                                        :show-download="false"
                                        @loading="onStructureDepictionLoading"
                                    />
                                </div>
                            </div>
                            <div
                                v-else-if="
                                    hasSelectedCompleteStudy &&
                                    !hasStructureAssigned
                                "
                                class="flex min-h-0 flex-1 flex-col items-center justify-center gap-3 overflow-y-auto px-2 py-2 text-center"
                            >
                                <div
                                    v-if="casStructureSuggestion"
                                    class="w-full max-w-full rounded-lg border border-teal-200 bg-teal-50 p-3 text-left"
                                >
                                    <p
                                        class="text-sm font-medium text-teal-900"
                                    >
                                        Suggested structure
                                    </p>
                                    <p
                                        class="mt-1 text-xs leading-relaxed text-teal-800"
                                    >
                                        CAS registry number
                                        <span class="font-mono font-semibold">{{
                                            casStructureSuggestion.cas
                                        }}</span>
                                        detected in folder
                                        <span class="font-medium">{{
                                            casStructureSuggestion.folderName
                                        }}</span
                                        >.
                                    </p>
                                    <div
                                        v-if="casStructureSuggestion.loading"
                                        class="mt-3 flex items-center gap-2 text-sm text-teal-800"
                                        role="status"
                                    >
                                        <ArrowPathIcon
                                            class="h-4 w-4 animate-spin shrink-0"
                                            aria-hidden="true"
                                        />
                                        Looking up compound…
                                    </div>
                                    <template v-else>
                                        <p
                                            v-if="
                                                casStructureSuggestion.compoundName
                                            "
                                            class="compound-name mt-2 text-sm text-gray-800"
                                            v-html="
                                                sanitizeCompoundName(
                                                    casStructureSuggestion.compoundName
                                                )
                                            "
                                        />
                                        <div
                                            v-if="casStructureSuggestion.smiles"
                                            class="mt-2 flex min-h-[100px] items-center justify-center overflow-hidden rounded-md border border-teal-100 bg-white"
                                        >
                                            <Depictor2D
                                                source="ocl"
                                                class="h-full w-full max-h-[140px]"
                                                :molecule="
                                                    casStructureSuggestion.smiles
                                                "
                                                :height="140"
                                                :width="200"
                                                :show-download="false"
                                            />
                                        </div>
                                        <p
                                            v-if="casStructureSuggestion.error"
                                            class="mt-2 text-xs text-amber-800"
                                        >
                                            {{ casStructureSuggestion.error }}
                                        </p>
                                        <div
                                            class="mt-3 flex flex-wrap justify-center gap-2"
                                        >
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-teal-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm transition hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                :disabled="
                                                    casStructureSuggestion.loading
                                                "
                                                @click="
                                                    openAddStructureFromCasSuggestion
                                                "
                                            >
                                                Use suggested structure
                                            </button>
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50"
                                                @click="openAddStructureModal"
                                            >
                                                Add manually
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <template v-else>
                                    <p
                                        class="text-sm leading-relaxed text-gray-500"
                                    >
                                        No structure assigned to this sample
                                        yet.
                                    </p>
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-md border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
                                        @click="openAddStructureModal"
                                    >
                                        Add structure
                                    </button>
                                </template>
                            </div>
                            <p
                                v-else
                                class="flex min-h-0 flex-1 items-center justify-center text-center text-sm leading-relaxed text-gray-500"
                            >
                                Select a sample folder to view structure.
                            </p>
                        </div>
                    </div>
                </div>

                <main
                    v-if="hasFiles"
                    class="min-w-0 flex-1 overflow-hidden bg-white p-4"
                >
                    <SpectraEditor
                        v-if="
                            project &&
                            selectedStudy &&
                            selectedStudy.internal_status === 'complete'
                        "
                        :project="project"
                        :study="selectedStudy"
                        @loading="spectraLoading"
                    />
                    <div
                        v-else
                        class="flex h-full items-center justify-center rounded-lg border border-dashed border-gray-200 bg-gray-50 px-6 text-center text-sm text-gray-500"
                    >
                        {{
                            processing
                                ? "Preparing spectra viewer…"
                                : selectedStudy &&
                                  selectedStudy.internal_status !== "complete"
                                ? "Processing sample spectra…"
                                : "Select a sample folder to view structure and spectra."
                        }}
                    </div>
                </main>
            </div>
        </div>

        <jet-dialog-modal
            :show="showUploadModal"
            max-width="7xl"
            @close="closeUploadModal"
        >
            <template #title>Upload data</template>

            <template #content>
                <p class="mb-4 text-sm text-gray-600">
                    Add raw or processed NMR files to your community
                    contribution. Organise spectra into folders — one folder per
                    sample.
                </p>
                <div class="h-[min(70vh,640px)] min-h-[360px] overflow-hidden">
                    <file-system-browser
                        v-if="showUploadModal"
                        ref="uploadFsbRef"
                        :key="`modal-${currentDraft.id}`"
                        :draft="currentDraft"
                        :height="'h-full min-h-0 w-full'"
                        @loading="onUploadFilesLoading"
                    />
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click="closeUploadModal">
                    Done
                </jet-secondary-button>
            </template>
        </jet-dialog-modal>

        <add-study-structure-modal
            :show="showStructureModal"
            :study="selectedStudy"
            :mode="structureModalMode"
            :molecule="structureModalMolecule"
            :prefill-cas="structureModalPrefillCas"
            :prefill-cas-smiles="structureModalPrefillCasSmiles"
            @close="closeStructureModal"
            @saved="onStructureSaved"
        />

        <community-submit-samples-modal
            :show="showSubmitModal"
            :draft-id="currentDraft.id"
            :publishable-studies="publishableStudies"
            @close="closeSubmitModal"
            @submitted="onSamplesSubmitted"
        />

        <jet-confirmation-modal
            :show="showDeleteStructureModal"
            @close="closeDeleteStructureModal"
        >
            <template #title>Delete structure</template>

            <template #content>
                Remove the assigned structure from this sample? You can add a
                new structure afterward.
            </template>

            <template #footer>
                <jet-secondary-button
                    :disabled="structureDeleting"
                    @click="closeDeleteStructureModal"
                >
                    Cancel
                </jet-secondary-button>
                <jet-danger-button
                    class="ml-2"
                    :class="{ 'opacity-25': structureDeleting }"
                    :disabled="structureDeleting"
                    @click="deleteStructure"
                >
                    {{ structureDeleting ? "Deleting…" : "Delete" }}
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
    </app-layout>
</template>

<script setup>
import axios from "axios";
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { useElementSize } from "@vueuse/core";
import { ArrowPathIcon, ArrowUpTrayIcon } from "@heroicons/vue/24/outline";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import FileSystemBrowser from "@/Shared/FileSystemBrowser.vue";
import SpectraEditor from "@/Shared/SpectraEditor.vue";
import Depictor2D from "@/Shared/Depictor2D.vue";
import AddStudyStructureModal from "@/Shared/AddStudyStructureModal.vue";
import CommunitySubmitSamplesModal from "@/Shared/CommunitySubmitSamplesModal.vue";
import {
    applyStudyStatusUpdates,
    fetchDraft,
    fetchDraftStudiesStatus,
    findStudyForFolder,
    isDraftSampleFolder,
    isStudyFolderProcessing,
    isStudyReadyToPublish,
    loadStudiesFromDraft,
    studyHasAssignedStructure,
    processDraft,
    validateDraftFolders,
} from "@/Composables/useDraftProcessing";
import { extractCasRegistryNumberFromText } from "@/Utils/casRegistryNumber";
import { moleculeStructureSmiles } from "@/Utils/moleculeStructureSmiles";
import Global from "@/Mixins/Global.js";

function sanitizeCompoundName(html) {
    return Global.methods.sanitizeHtml(html);
}

const props = defineProps({
    draft: {
        type: Object,
        required: true,
    },
});

const page = usePage();

function clearFileBrowserInertiaProps() {
    if (page.props.selectedFileSystemObject) {
        page.props.selectedFileSystemObject = null;
    }

    if (page.props.selectedFolder) {
        page.props.selectedFolder = "/";
    }
}

const currentDraft = ref({ ...props.draft });
const fsbRef = ref(null);
const uploadFsbRef = ref(null);
const showUploadModal = ref(false);
const showSubmitModal = ref(false);
const submitSuccessMessage = ref(null);
const showStructureModal = ref(false);
const showDeleteStructureModal = ref(false);
const structureModalMode = ref("add");
const structureModalMolecule = ref(null);
const structureModalPrefillCas = ref("");
const structureModalPrefillCasSmiles = ref("");
const structureDeleting = ref(false);
const selectedSampleFolder = ref(null);
const casStructureSuggestion = ref(null);
let casSuggestionRequestId = 0;
const hasFiles = ref(false);
const filesStateReady = ref(false);
const loadingFiles = ref(false);
const processing = ref(false);
const filesErrorMessage = ref(null);
const project = ref(null);
const studies = ref([]);
/** @type {import('vue').Ref<Set<number>>} */
const submittedStudyIds = ref(new Set());
const studiesLoadAttempted = ref(false);
const selectedStudy = ref(null);
const pendingStudyFolder = ref(null);
const lastProcessedFingerprint = ref(null);
const structureLoading = ref(false);
const structureDepictionLoading = ref(false);
let studyStatusPollTimer = null;
const sidebarColumnRef = ref(null);
const uploadSectionRef = ref(null);
const structureViewerRef = ref(null);

const FILE_TREE_HEIGHT_KEY = "nmrxiv-community-file-tree-height";
const DEFAULT_FILE_TREE_HEIGHT = 224;
const MIN_FILE_TREE_HEIGHT = 120;
const MIN_STRUCTURE_HEIGHT = 120;
const STRUCTURE_RESIZE_HANDLE_HEIGHT = 4;

const fileTreeHeight = ref(DEFAULT_FILE_TREE_HEIGHT);
const isResizingStructure = ref(false);

function loadSavedFileTreeHeight() {
    const saved = localStorage.getItem(FILE_TREE_HEIGHT_KEY);

    if (!saved) {
        return;
    }

    const parsed = Number.parseInt(saved, 10);

    if (!Number.isNaN(parsed) && parsed >= MIN_FILE_TREE_HEIGHT) {
        fileTreeHeight.value = parsed;
    }
}

function clampFileTreeHeight(nextHeight) {
    if (!sidebarColumnRef.value) {
        return Math.max(MIN_FILE_TREE_HEIGHT, nextHeight);
    }

    const sidebarRect = sidebarColumnRef.value.getBoundingClientRect();
    const uploadBottom =
        uploadSectionRef.value?.getBoundingClientRect().bottom ??
        sidebarRect.top;
    const usedAboveTree = uploadBottom - sidebarRect.top;
    const maxFileTreeHeight =
        sidebarRect.height -
        usedAboveTree -
        MIN_STRUCTURE_HEIGHT -
        STRUCTURE_RESIZE_HANDLE_HEIGHT;

    return Math.max(
        MIN_FILE_TREE_HEIGHT,
        Math.min(maxFileTreeHeight, nextHeight)
    );
}

function onStructureResize(event) {
    if (!isResizingStructure.value || !sidebarColumnRef.value) {
        return;
    }

    const sidebarRect = sidebarColumnRef.value.getBoundingClientRect();
    const uploadBottom =
        uploadSectionRef.value?.getBoundingClientRect().bottom ??
        sidebarRect.top;

    fileTreeHeight.value = clampFileTreeHeight(event.clientY - uploadBottom);
}

function stopStructureResize() {
    if (!isResizingStructure.value) {
        return;
    }

    isResizingStructure.value = false;
    document.removeEventListener("mousemove", onStructureResize);
    document.removeEventListener("mouseup", stopStructureResize);
    document.body.style.cursor = "";
    document.body.style.userSelect = "";
    localStorage.setItem(FILE_TREE_HEIGHT_KEY, String(fileTreeHeight.value));
}

function startStructureResize(event) {
    event.preventDefault();
    isResizingStructure.value = true;
    document.addEventListener("mousemove", onStructureResize);
    document.addEventListener("mouseup", stopStructureResize);
    document.body.style.cursor = "row-resize";
    document.body.style.userSelect = "none";
}
const { height: structureViewerHeight, width: structureViewerWidth } =
    useElementSize(structureViewerRef);

const structureDepictorHeight = computed(() =>
    Math.max(120, Math.floor(structureViewerHeight.value))
);

const structureDepictorWidth = computed(() =>
    Math.max(120, Math.floor(structureViewerWidth.value))
);

const hasSelectedCompleteStudy = computed(
    () => selectedStudy.value?.internal_status === "complete"
);

const primaryMolecule = computed(() => {
    if (!hasSelectedCompleteStudy.value) {
        return null;
    }

    const molecules = selectedStudy.value?.sample?.molecules;

    return molecules?.[0] ?? null;
});

const structureSmiles = computed(() =>
    moleculeStructureSmiles(primaryMolecule.value)
);

const hasStructureAssigned = computed(() =>
    Boolean(structureSmiles.value?.trim())
);

const publishableStudies = computed(() =>
    studies.value.filter((study) => isStudyReadyToPublish(study))
);

const submittedStudyIdsList = computed(() =>
    Array.from(submittedStudyIds.value)
);

const studiesWorkspaceReady = computed(
    () =>
        studiesLoadAttempted.value &&
        (Boolean(project.value) || studies.value.length > 0)
);

function markStudiesSubmitted(studyIds) {
    if (!studyIds?.length) {
        return;
    }

    const next = new Set(submittedStudyIds.value);
    studyIds.forEach((id) => next.add(Number(id)));
    submittedStudyIds.value = next;
}

function filterWorkspaceStudies(studyRows) {
    if (!Array.isArray(studyRows)) {
        return [];
    }

    return studyRows.filter((study) => !submittedStudyIds.value.has(study.id));
}

function activeFileBrowser() {
    return uploadFsbRef.value ?? fsbRef.value;
}

function syncHasFilesFromBrowser(browser = activeFileBrowser()) {
    const root = browser?.file;

    hasFiles.value = Array.isArray(root?.children) && root.children.length > 0;
}

async function syncHasFilesFromApi() {
    try {
        const { data } = await axios.get(
            `/dashboard/drafts/${currentDraft.value.id}/files`
        );

        hasFiles.value =
            data.has_sample_folders === true ||
            (Array.isArray(data.file?.children) &&
                data.file.children.length > 0) ||
            (data.sample_folders?.total ?? 0) > 0;
    } catch {
        // Keep current hasFiles state.
    }
}

function fileTreeFingerprint(browser = activeFileBrowser()) {
    const root = browser?.file;

    if (browser?.sampleFoldersPagination) {
        return JSON.stringify({
            total: browser.sampleFoldersPagination.total,
            missing: browser.missing_files ?? 0,
        });
    }

    if (!root?.children?.length) {
        return null;
    }

    return JSON.stringify(
        root.children.map((child) => ({
            id: child.id,
            name: child.name,
            childCount: child.children?.length ?? 0,
        }))
    );
}

function spectraLoading(isLoading) {
    loadingFiles.value = isLoading;
}

function clearStudyStatusPoll() {
    if (studyStatusPollTimer) {
        clearTimeout(studyStatusPollTimer);
        studyStatusPollTimer = null;
    }
}

function scheduleStudyStatusPoll() {
    clearStudyStatusPoll();

    const needsPolling = studies.value.some(
        (study) =>
            !submittedStudyIds.value.has(Number(study.id)) &&
            study.internal_status !== "complete"
    );

    if (!needsPolling) {
        return;
    }

    studyStatusPollTimer = setTimeout(pollStudyStatuses, 30000);
}

async function refreshStudiesFromDraft() {
    const existing = await loadStudiesFromDraft(currentDraft.value.id);

    if (!existing) {
        return;
    }

    const previousSelectedId = selectedStudy.value?.id;

    await hydrateStudies(existing);

    if (previousSelectedId) {
        const refreshed = studies.value.find(
            (study) => study.id === previousSelectedId
        );

        if (refreshed) {
            selectedStudy.value = refreshed;
        }
    }

    trySelectPendingStudy();
}

async function trySelectPendingStudy() {
    if (!pendingStudyFolder.value) {
        return;
    }

    const study = findStudyForFolder(pendingStudyFolder.value, studies.value);

    if (study?.internal_status === "complete") {
        selectedStudy.value = study;
    }
}

async function pollStudyStatuses() {
    if (!currentDraft.value?.id || studies.value.length === 0) {
        return;
    }

    try {
        const rows = await fetchDraftStudiesStatus(currentDraft.value.id);
        const rowIds = new Set(rows.map((row) => row.id));
        studies.value = filterWorkspaceStudies(
            studies.value.filter((study) => rowIds.has(study.id))
        );
        const hadInProgress = studies.value.some(
            (study) => study.internal_status !== "complete"
        );
        applyStudyStatusUpdates(studies.value, rows);

        const hasInProgress = studies.value.some(
            (study) => study.internal_status !== "complete"
        );

        if (hadInProgress && !hasInProgress) {
            await refreshStudiesFromDraft();
        } else {
            trySelectPendingStudy();

            if (
                selectedStudy.value?.id &&
                selectedStudy.value.internal_status === "complete" &&
                !hasStructureAssigned.value
            ) {
                await refreshStudiesFromDraft();
            }
        }
    } catch {
        // Retry on the next interval.
    } finally {
        scheduleStudyStatusPoll();
    }
}

async function hydrateStudies(result, preferredStudy = null) {
    studiesLoadAttempted.value = true;
    project.value = result.project;
    studies.value = filterWorkspaceStudies(result.studies ?? []);

    if (
        preferredStudy &&
        preferredStudy.internal_status === "complete" &&
        studies.value.some((study) => study.id === preferredStudy.id)
    ) {
        selectedStudy.value = preferredStudy;
    }

    scheduleStudyStatusPoll();
}

async function ensureStudiesLoaded() {
    if (project.value && studies.value.length > 0) {
        return;
    }

    const existing = await loadStudiesFromDraft(currentDraft.value.id);

    if (existing) {
        await hydrateStudies(existing);
    }
}

function findFolderInTree(root, folderId) {
    if (!root || folderId == null) {
        return null;
    }

    if (Number(root.id) === Number(folderId)) {
        return root;
    }

    if (!Array.isArray(root.children)) {
        return null;
    }

    for (const child of root.children) {
        const match = findFolderInTree(child, folderId);

        if (match) {
            return match;
        }
    }

    return null;
}

function resolveFolderFromBrowser(folder, browser = fsbRef.value) {
    if (!folder?.id || !browser?.file) {
        return folder;
    }

    return findFolderInTree(browser.file, folder.id) ?? folder;
}

async function selectStudyFromFolder(folder) {
    if (!isDraftSampleFolder(folder)) {
        return;
    }

    pendingStudyFolder.value = folder;

    await ensureStudiesLoaded();

    let resolvedFolder = resolveFolderFromBrowser(folder);
    let study = findStudyForFolder(resolvedFolder, studies.value);

    if (!study && hasFiles.value && !processing.value) {
        await tryAutoProcess(fsbRef.value, { force: true });
        await refreshStudiesFromDraft();
        resolvedFolder = resolveFolderFromBrowser(resolvedFolder);
        study = findStudyForFolder(resolvedFolder, studies.value);
    }

    if (!study) {
        await refreshStudiesFromDraft();
        study = findStudyForFolder(resolvedFolder, studies.value);
    }

    if (!study) {
        selectedStudy.value = null;

        return;
    }

    if (
        study.internal_status === "complete" &&
        !study.sample?.molecules?.length
    ) {
        await refreshStudiesFromDraft();
        study = findStudyForFolder(resolvedFolder, studies.value);
    }

    selectedStudy.value = study ?? null;
}

function onStructureDepictionLoading(isLoading) {
    structureDepictionLoading.value = isLoading;
}

function sampleFolderDisplayName(folder) {
    return folder?.name ?? folder?.filename ?? "";
}

function clearCasStructureSuggestion() {
    casSuggestionRequestId += 1;
    casStructureSuggestion.value = null;
}

async function refreshCasStructureSuggestion() {
    const requestId = ++casSuggestionRequestId;
    casStructureSuggestion.value = null;

    if (
        !hasSelectedCompleteStudy.value ||
        hasStructureAssigned.value ||
        !selectedSampleFolder.value
    ) {
        return;
    }

    const folderName = sampleFolderDisplayName(selectedSampleFolder.value);
    const cas = extractCasRegistryNumberFromText(folderName);

    if (!cas) {
        return;
    }

    casStructureSuggestion.value = {
        cas,
        folderName,
        compoundName: null,
        smiles: null,
        loading: true,
        error: null,
    };

    try {
        const { data } = await axios.get("/cas/detail", {
            params: { cas_rn: cas },
            timeout: 30000,
        });

        if (requestId !== casSuggestionRequestId) {
            return;
        }

        const smiles = data.smile || data.canonicalSmile;

        casStructureSuggestion.value = {
            cas,
            folderName,
            compoundName: data.name ?? null,
            smiles: smiles ?? null,
            loading: false,
            error: smiles
                ? null
                : "No structural data available for this CAS number.",
        };
    } catch (error) {
        if (requestId !== casSuggestionRequestId) {
            return;
        }

        casStructureSuggestion.value = {
            cas,
            folderName,
            compoundName: null,
            smiles: null,
            loading: false,
            error:
                error.response?.data?.error ||
                error.response?.data?.message ||
                "Could not look up this CAS number. You can still try importing it manually.",
        };
    }
}

async function onSampleFolderSelected(folder) {
    if (
        isStudyFolderProcessing(
            folder,
            studies.value,
            submittedStudyIds.value,
            {
                studiesWorkspaceReady: studiesWorkspaceReady.value,
                draftProcessing: processing.value,
            }
        )
    ) {
        return;
    }

    selectedSampleFolder.value = folder;
    structureLoading.value = true;
    structureDepictionLoading.value = false;
    clearCasStructureSuggestion();

    try {
        await selectStudyFromFolder(folder);
        await refreshCasStructureSuggestion();
    } finally {
        structureLoading.value = false;
    }
}

function markStudyPending(studyId) {
    if (!studyId) {
        return;
    }

    const study = studies.value.find((row) => row.id === studyId);

    if (study) {
        study.internal_status = null;
        study.has_nmrium = false;
    }

    if (selectedStudy.value?.id === studyId) {
        selectedStudy.value = null;
    }
}

async function onSampleFolderReset({ studyId } = {}) {
    lastProcessedFingerprint.value = null;
    markStudyPending(studyId);
    scheduleStudyStatusPoll();

    await tryAutoProcess(fsbRef.value, { force: true });
}

async function refreshSidebarFileTree() {
    await nextTick();

    if (typeof fsbRef.value?.loadFiles === "function") {
        fsbRef.value.loadFiles();
    }
}

async function tryAutoProcess(
    browser = activeFileBrowser(),
    { force = false } = {}
) {
    if (loadingFiles.value || processing.value) {
        return;
    }

    if (!hasFiles.value) {
        await syncHasFilesFromApi();
    }

    if (!hasFiles.value) {
        return;
    }

    const fingerprint = fileTreeFingerprint(browser);

    if (
        !force &&
        fingerprint &&
        fingerprint === lastProcessedFingerprint.value
    ) {
        return;
    }

    const validation = validateDraftFolders(browser, studies.value);

    if (!validation.valid) {
        filesErrorMessage.value = validation.error;

        return;
    }

    filesErrorMessage.value = null;
    processing.value = true;

    try {
        const result = await processDraft(currentDraft.value.id, {
            owner_id: page.props.auth?.user?.id,
            tags_array: [],
        });

        await hydrateStudies(result);
        lastProcessedFingerprint.value = fingerprint;
        trySelectPendingStudy();
        await refreshSidebarFileTree();
    } catch (error) {
        filesErrorMessage.value =
            error.response?.data?.message ||
            "Could not process the uploaded files. Please check the folder structure and try again.";
    } finally {
        processing.value = false;
    }
}

async function onUploadFilesLoading(isLoading) {
    loadingFiles.value = isLoading;

    if (!isLoading) {
        await nextTick();
        syncHasFilesFromBrowser(activeFileBrowser());

        if (!hasFiles.value) {
            await syncHasFilesFromApi();
        }

        if (hasFiles.value && fsbRef.value !== activeFileBrowser()) {
            await refreshSidebarFileTree();
        }

        await tryAutoProcess(activeFileBrowser(), { force: true });
    }
}

async function onSidebarFilesLoading(isLoading) {
    if (!isLoading) {
        await nextTick();
        syncHasFilesFromBrowser(fsbRef.value);

        if (!hasFiles.value) {
            await syncHasFilesFromApi();
        }

        if (hasFiles.value) {
            await tryAutoProcess(fsbRef.value);
            await syncWorkspaceStudies();
        }
    }
}

async function syncWorkspaceStudies() {
    const existing = await loadStudiesFromDraft(currentDraft.value.id);

    if (existing) {
        await hydrateStudies(existing);

        return;
    }

    if (project.value) {
        studiesLoadAttempted.value = true;
    }
}

function openStructureModal(mode) {
    if (!hasSelectedCompleteStudy.value) {
        return;
    }

    structureModalMode.value = mode;
    structureModalMolecule.value =
        mode === "edit" ? primaryMolecule.value : null;
    showStructureModal.value = true;
}

function openAddStructureModal() {
    structureModalPrefillCas.value = "";
    structureModalPrefillCasSmiles.value = "";
    openStructureModal("add");
}

function openAddStructureFromCasSuggestion() {
    const suggestion = casStructureSuggestion.value;

    if (!suggestion?.cas) {
        openAddStructureModal();
        return;
    }

    structureModalPrefillCas.value = suggestion.cas;
    structureModalPrefillCasSmiles.value = suggestion.smiles ?? "";
    openStructureModal("add");
}

function openEditStructureModal() {
    if (!hasStructureAssigned.value && !primaryMolecule.value) {
        return;
    }

    openStructureModal("edit");
}

function confirmDeleteStructure() {
    if (!primaryMolecule.value || !selectedStudy.value?.id) {
        return;
    }

    showDeleteStructureModal.value = true;
}

function closeDeleteStructureModal() {
    if (structureDeleting.value) {
        return;
    }

    showDeleteStructureModal.value = false;
}

async function deleteStructure() {
    const molecule = primaryMolecule.value;
    const studyId = selectedStudy.value?.id;

    if (!molecule?.id || !studyId) {
        return;
    }

    structureDeleting.value = true;
    filesErrorMessage.value = null;

    try {
        const response = await axios.delete(
            `/dashboard/studies/${studyId}/molecule/${molecule.id}`
        );

        syncStudyMolecules(studyId, response.data);
        showDeleteStructureModal.value = false;
        structureDepictionLoading.value = false;
    } catch (error) {
        filesErrorMessage.value =
            error.response?.data?.message ||
            error.message ||
            "Could not delete the structure. Please try again.";
        showDeleteStructureModal.value = false;
    } finally {
        structureDeleting.value = false;
    }
}

function closeStructureModal() {
    showStructureModal.value = false;
    structureModalMode.value = "add";
    structureModalMolecule.value = null;
    structureModalPrefillCas.value = "";
    structureModalPrefillCasSmiles.value = "";
}

function syncStudyMolecules(studyId, molecules) {
    const study = studies.value.find((row) => row.id === studyId);

    if (study) {
        if (!study.sample) {
            study.sample = { molecules: [] };
        }

        study.sample.molecules = molecules;
        study.has_structure = studyHasAssignedStructure(study);
    }

    if (selectedStudy.value?.id === studyId) {
        if (!selectedStudy.value.sample) {
            selectedStudy.value.sample = { molecules: [] };
        }

        selectedStudy.value.sample.molecules = molecules;
        selectedStudy.value.has_structure = studyHasAssignedStructure(
            selectedStudy.value
        );
    }
}

async function onStructureSaved(molecules) {
    if (!selectedStudy.value?.id) {
        return;
    }

    clearCasStructureSuggestion();
    syncStudyMolecules(selectedStudy.value.id, molecules);

    const studyId = selectedStudy.value.id;
    const refreshed = studies.value.find((study) => study.id === studyId);

    if (refreshed) {
        selectedStudy.value = refreshed;
    }

    structureDepictionLoading.value = false;
}

function openSubmitModal() {
    if (publishableStudies.value.length === 0) {
        return;
    }

    submitSuccessMessage.value = null;
    showSubmitModal.value = true;
}

function closeSubmitModal() {
    showSubmitModal.value = false;
}

async function onSamplesSubmitted(result) {
    const submittedIds = new Set(
        (result?.study_ids ?? []).map((id) => Number(id))
    );

    markStudiesSubmitted([...submittedIds]);

    studies.value = studies.value.filter(
        (study) => !submittedIds.has(study.id)
    );

    if (selectedStudy.value?.id && submittedIds.has(selectedStudy.value.id)) {
        selectedStudy.value = null;
        selectedSampleFolder.value = null;
        clearCasStructureSuggestion();
    }

    submitSuccessMessage.value =
        result?.message ||
        "Your selected samples have been queued for processing.";

    await refreshSidebarFileTree();

    scheduleSubmittedFolderTreeRefresh();
}

let submittedFolderRefreshTimer = null;

function scheduleSubmittedFolderTreeRefresh() {
    if (submittedFolderRefreshTimer) {
        clearTimeout(submittedFolderRefreshTimer);
    }

    const delaysMs = [5000, 15000, 30000];
    let step = 0;

    const poll = async () => {
        await refreshSidebarFileTree();
        step += 1;

        if (step < delaysMs.length && submittedStudyIds.value.size > 0) {
            submittedFolderRefreshTimer = setTimeout(poll, delaysMs[step]);
        }
    };

    submittedFolderRefreshTimer = setTimeout(poll, delaysMs[0]);
}

function openUploadModal() {
    showUploadModal.value = true;

    nextTick(() => {
        if (typeof uploadFsbRef.value?.loadFiles === "function") {
            uploadFsbRef.value.loadFiles();
        }

        if (typeof uploadFsbRef.value?.loadDropZone === "function") {
            uploadFsbRef.value.loadDropZone();
        }
    });
}

function closeUploadModal() {
    showUploadModal.value = false;
}

async function refreshDraft() {
    const draft = await fetchDraft(currentDraft.value.id);
    currentDraft.value = draft;
}

async function loadFileBrowserTree() {
    await nextTick();

    if (typeof fsbRef.value?.loadFiles === "function") {
        fsbRef.value.loadFiles();
    }
}

let removeInertiaBeforeListener = null;

onMounted(async () => {
    loadSavedFileTreeHeight();

    removeInertiaBeforeListener = router.on("before", () => {
        clearFileBrowserInertiaProps();
    });

    try {
        await refreshDraft();
        await syncHasFilesFromApi();
    } finally {
        filesStateReady.value = true;
    }
});

watch(filesStateReady, async (ready) => {
    if (!ready) {
        return;
    }

    await nextTick();
    await loadFileBrowserTree();
    fileTreeHeight.value = clampFileTreeHeight(fileTreeHeight.value);

    const existing = await loadStudiesFromDraft(currentDraft.value.id);

    if (existing) {
        await hydrateStudies(existing);
        lastProcessedFingerprint.value = fileTreeFingerprint(fsbRef.value);
    }
});

watch(
    () => currentDraft.value?.id,
    async (draftId, previousDraftId) => {
        if (!draftId || draftId === previousDraftId) {
            return;
        }

        await loadFileBrowserTree();
    }
);

watch(
    () => props.draft,
    (draft) => {
        currentDraft.value = { ...draft };
    }
);

onUnmounted(() => {
    stopStructureResize();
    clearStudyStatusPoll();

    if (submittedFolderRefreshTimer) {
        clearTimeout(submittedFolderRefreshTimer);
        submittedFolderRefreshTimer = null;
    }

    clearFileBrowserInertiaProps();

    if (typeof removeInertiaBeforeListener === "function") {
        removeInertiaBeforeListener();
        removeInertiaBeforeListener = null;
    }
});
</script>

<style scoped>
.compound-name :deep(.text-smallcaps) {
    font-variant: small-caps;
}
</style>
