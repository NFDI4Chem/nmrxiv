<template>
    <app-layout title="Submit Data">
        <!-- Header -->
        <template #header>
            <!-- Animated mesh gradient background -->
            <div class="relative border-b border-zinc-900/5 overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-sky-50/30 via-teal-50/30 to-cyan-50/30"
                ></div>
                <div class="absolute inset-0 opacity-20">
                    <div
                        class="absolute top-0 left-1/4 w-96 h-96 bg-teal-200 rounded-full mix-blend-multiply filter blur-3xl animate-blob"
                    ></div>
                    <div
                        class="absolute top-0 right-1/4 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"
                    ></div>
                    <div
                        class="absolute -bottom-32 left-1/3 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"
                    ></div>
                </div>
                <!-- End of Background pattern -->
                <div class="relative px-6 py-4">
                    <div class="w-full">
                        <div class="flex flex-col gap-3">
                            <!-- Top bar: back + step title (left) · primary actions (right) -->
                            <div
                                id="tour-step-submission-header"
                                class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2"
                            >
                                <div
                                    class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3"
                                >
                                    <template v-if="currentStep">
                                        <span v-if="step == '1'">
                                            <Link
                                                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200/90 bg-white/90 text-gray-600 shadow-sm backdrop-blur-sm transition-all hover:border-teal-200 hover:bg-white hover:text-teal-700 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2"
                                                :href="
                                                    showPrimer && currentDraft
                                                        ? route('dashboard')
                                                        : '/upload'
                                                "
                                                :aria-label="
                                                    showPrimer && currentDraft
                                                        ? 'Back to dashboard'
                                                        : 'Back to uploads'
                                                "
                                            >
                                                <ArrowLeftIcon
                                                    class="h-5 w-5"
                                                    aria-hidden="true"
                                                />
                                            </Link>
                                        </span>
                                        <span v-else>
                                            <button
                                                type="button"
                                                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200/90 bg-white/90 text-gray-600 shadow-sm backdrop-blur-sm transition-all hover:border-teal-200 hover:bg-white hover:text-teal-700 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2"
                                                aria-label="Back to file upload"
                                                @click="selectStep(1)"
                                            >
                                                <ArrowLeftIcon
                                                    class="h-5 w-5"
                                                    aria-hidden="true"
                                                />
                                            </button>
                                        </span>
                                    </template>
                                    <template v-else>
                                        <Link
                                            class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200/90 bg-white/90 text-gray-600 shadow-sm backdrop-blur-sm transition-all hover:border-teal-200 hover:bg-white hover:text-teal-700 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2"
                                            :href="'/upload'"
                                            aria-label="Back to uploads"
                                        >
                                            <ArrowLeftIcon
                                                class="h-5 w-5"
                                                aria-hidden="true"
                                            />
                                        </Link>
                                    </template>

                                    <span
                                        v-if="
                                            currentStep &&
                                            !(showPrimer && currentDraft)
                                        "
                                        class="min-w-0 text-sm font-bold leading-snug text-teal-600"
                                    >
                                        Step
                                        <span v-if="currentStep.id">{{
                                            currentStep.id
                                        }}</span>
                                        / 3 -
                                        <span v-if="currentStep.id == '1'">
                                            File Upload
                                        </span>
                                        <span v-if="currentStep.id == '2'">
                                            Auto Processing, Assignments and
                                            Validation
                                        </span>
                                    </span>
                                    <span
                                        v-else
                                        class="text-sm font-bold uppercase tracking-widest text-gray-700"
                                    >
                                        Submit data to nmrXiv
                                    </span>
                                </div>

                                <div
                                    v-if="!(showPrimer && currentDraft)"
                                    class="flex shrink-0 flex-wrap items-center justify-end gap-2"
                                >
                                    <div>
                                        <span v-if="!currentStep">
                                            <Link
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200"
                                                :href="returnUrl"
                                            >
                                                Cancel
                                            </Link>
                                        </span>
                                        <span v-else>
                                            <span
                                                v-if="currentStep.id == '1'"
                                                class="inline-flex flex-wrap items-center gap-2"
                                            >
                                                <Link
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200"
                                                    :href="returnUrl"
                                                >
                                                    Cancel
                                                </Link>
                                                <jet-button
                                                    id="tour-step-proceed-from-step-1"
                                                    :class="{
                                                        'opacity-25':
                                                            createDatasetForm.processing,
                                                    }"
                                                    :disabled="
                                                        createDatasetForm.processing ||
                                                        loading ||
                                                        loadingStep
                                                    "
                                                    @click="process()"
                                                >
                                                    <span v-if="loadingStep">
                                                        <svg
                                                            class="animate-spin -ml-1 mr-3 h-2 w-2 text-white"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <circle
                                                                class="opacity-25"
                                                                cx="12"
                                                                cy="12"
                                                                r="10"
                                                                stroke="currentColor"
                                                                stroke-width="4"
                                                            ></circle>
                                                            <path
                                                                class="opacity-75"
                                                                fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                            ></path>
                                                        </svg>
                                                    </span>
                                                    Proceed
                                                </jet-button>
                                            </span>
                                            <span
                                                v-else-if="
                                                    currentStep.id == '2'
                                                "
                                                class="inline-flex flex-wrap items-center gap-2"
                                            >
                                                <jet-button
                                                    id="tour-step-proceed-from-step-2"
                                                    :class="{
                                                        'opacity-25':
                                                            createDatasetForm.processing,
                                                    }"
                                                    :disabled="
                                                        createDatasetForm.processing ||
                                                        loading ||
                                                        loadingStep
                                                    "
                                                    @click="closeDraft()"
                                                >
                                                    <span v-if="loadingStep">
                                                        <svg
                                                            class="animate-spin -ml-1 mr-3 h-2 w-2 text-white"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <circle
                                                                class="opacity-25"
                                                                cx="12"
                                                                cy="12"
                                                                r="10"
                                                                stroke="currentColor"
                                                                stroke-width="4"
                                                            ></circle>
                                                            <path
                                                                class="opacity-75"
                                                                fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                            ></path>
                                                        </svg>
                                                    </span>
                                                    Proceed
                                                </jet-button>
                                            </span>
                                            <span
                                                v-else-if="
                                                    currentStep.id == '3'
                                                "
                                            >
                                                <Link
                                                    id="tour-step-finish"
                                                    class="inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                                    :href="route('dashboard')"
                                                >
                                                    Finish
                                                </Link>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Project name (draft in progress) -->
                            <div
                                v-if="
                                    currentStep && currentDraft && !showPrimer
                                "
                                id="tour-step-project-name"
                                class="flex w-full min-w-0 flex-col gap-1.5"
                            >
                                <div
                                    class="flex w-full min-w-0 items-start justify-between gap-x-4 gap-y-1"
                                >
                                    <span
                                        id="upload-draft-name-label"
                                        class="shrink-0 text-xs font-medium normal-case tracking-normal text-gray-600"
                                    >
                                        Project name
                                    </span>
                                    <p
                                        id="upload-draft-name-hint"
                                        class="min-w-0 flex-1 text-right text-xs font-normal normal-case tracking-normal leading-snug text-gray-500"
                                    >
                                        Click the name or pencil to edit. Press
                                        Enter to save.
                                    </p>
                                </div>
                                <div
                                    class="group flex w-full min-w-0 items-stretch overflow-hidden rounded-lg border border-gray-300 bg-white/95 shadow-sm transition-colors hover:border-teal-400/90 hover:shadow-md focus-within:border-teal-500 focus-within:ring-2 focus-within:ring-teal-500/25 focus-within:ring-offset-2 focus-within:ring-offset-white"
                                >
                                    <p
                                        ref="draftNameEditor"
                                        class="min-w-[12rem] flex-1 cursor-text px-3 py-2 text-base font-semibold normal-case tracking-normal text-gray-900 outline-none"
                                        contenteditable="true"
                                        role="textbox"
                                        tabindex="0"
                                        spellcheck="true"
                                        aria-labelledby="upload-draft-name-label"
                                        aria-describedby="upload-draft-name-hint"
                                        @blur="updateDraft($event)"
                                        @keydown.enter.prevent="
                                            $event.target.blur()
                                        "
                                    >
                                        {{ currentDraft.name }}
                                    </p>
                                    <button
                                        type="button"
                                        class="flex shrink-0 items-center rounded-r-lg border-l border-gray-200 px-3 text-gray-400 transition-colors hover:bg-gray-50 hover:text-teal-600 focus-visible:z-10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-500"
                                        aria-label="Focus project name field"
                                        @click="focusDraftName"
                                    >
                                        <PencilIcon
                                            class="h-4 w-4"
                                            aria-hidden="true"
                                        />
                                    </button>
                                </div>
                                <jet-input-error
                                    :message="draftForm.errors.name"
                                    class="mt-0.5 normal-case"
                                />

                                <div class="mt-4 flex flex-col gap-1.5">
                                    <div
                                        class="flex min-w-0 items-start justify-between gap-3"
                                    >
                                        <span
                                            id="upload-doi-label"
                                            class="text-xs font-medium normal-case tracking-normal text-gray-600"
                                        >
                                            Digital Object Identifier
                                        </span>
                                        <span
                                            v-if="doiCopySucceeded"
                                            class="shrink-0 text-right text-xs font-medium leading-snug text-teal-700"
                                            role="status"
                                            aria-live="polite"
                                        >
                                            Copied to clipboard.
                                        </span>
                                    </div>
                                    <template v-if="project?.provisional_doi">
                                        <div class="space-y-2">
                                            <div
                                                class="flex w-full min-w-0 overflow-hidden rounded-lg border border-gray-300 bg-gray-50 shadow-sm"
                                            >
                                                <input
                                                    id="upload-reserved-doi-preview"
                                                    type="text"
                                                    readonly
                                                    tabindex="-1"
                                                    class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2 font-mono text-sm text-gray-800 outline-none"
                                                    :value="
                                                        reservedDoiDisplayUrl
                                                    "
                                                    aria-labelledby="upload-doi-label"
                                                    aria-describedby="upload-reserved-doi-hint-persisted"
                                                />
                                                <button
                                                    type="button"
                                                    class="inline-flex shrink-0 items-center gap-1.5 border-l border-gray-200 bg-white px-3 py-2 text-sm font-medium text-teal-700 transition hover:bg-teal-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-500"
                                                    @click="
                                                        copyReservedDoiToClipboard()
                                                    "
                                                >
                                                    <ClipboardDocumentIcon
                                                        class="h-4 w-4 shrink-0"
                                                        aria-hidden="true"
                                                    />
                                                    Copy
                                                </button>
                                            </div>
                                            <p
                                                id="upload-reserved-doi-hint-persisted"
                                                class="text-xs leading-relaxed text-gray-500"
                                            >
                                                This provisional DOI is saved
                                                for this draft. Copy the URL to
                                                cite it in your files or
                                                manuscripts; it will be
                                                registered when you publish this
                                                submission.
                                            </p>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="flex flex-col gap-2">
                                            <button
                                                type="button"
                                                class="group inline-flex w-fit max-w-full shrink-0 cursor-pointer items-center gap-2 self-start rounded-lg px-4 py-2 text-sm font-semibold shadow-sm transition duration-150 ease-out focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                                                :class="
                                                    needsReservedDoi
                                                        ? 'border border-gray-300 bg-white text-gray-800 hover:border-gray-400 hover:bg-gray-50 hover:shadow-md active:bg-gray-100'
                                                        : 'border border-gray-800/35 bg-gray-900 text-white hover:border-gray-800 hover:bg-gray-800 hover:shadow-md active:bg-gray-950'
                                                "
                                                :disabled="
                                                    provisionalDoiLoading ||
                                                    !currentDraft?.id
                                                "
                                                :aria-busy="
                                                    provisionalDoiLoading
                                                "
                                                :aria-pressed="needsReservedDoi"
                                                @click="
                                                    onReservedDoiSwitch(
                                                        !needsReservedDoi
                                                    )
                                                "
                                            >
                                                <svg
                                                    v-if="provisionalDoiLoading"
                                                    class="h-4 w-4 shrink-0 animate-spin text-current"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    aria-hidden="true"
                                                >
                                                    <circle
                                                        class="opacity-25"
                                                        cx="12"
                                                        cy="12"
                                                        r="10"
                                                        stroke="currentColor"
                                                        stroke-width="4"
                                                    ></circle>
                                                    <path
                                                        class="opacity-75"
                                                        fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                    ></path>
                                                </svg>
                                                <span>{{
                                                    needsReservedDoi
                                                        ? "Remove provisional DOI"
                                                        : "Reserve provisional DOI"
                                                }}</span>
                                                <ChevronRightIcon
                                                    v-if="
                                                        !needsReservedDoi &&
                                                        !provisionalDoiLoading
                                                    "
                                                    class="h-4 w-4 shrink-0 opacity-90 transition group-hover:translate-x-0.5 group-hover:opacity-100"
                                                    aria-hidden="true"
                                                />
                                            </button>
                                            <p
                                                class="text-xs leading-relaxed text-gray-500"
                                            >
                                                Reserve a provisional DOI for
                                                this draft so you can cite it in
                                                files or manuscripts before
                                                publication.
                                            </p>
                                        </div>
                                        <p
                                            v-if="provisionalDoiError"
                                            class="text-xs font-medium text-red-600"
                                            role="alert"
                                            aria-live="polite"
                                        >
                                            {{ provisionalDoiError }}
                                        </p>

                                        <div
                                            v-if="needsReservedDoi"
                                            class="mt-3 space-y-2"
                                        >
                                            <div
                                                class="flex w-full min-w-0 overflow-hidden rounded-lg border border-gray-300 bg-gray-50 shadow-sm"
                                            >
                                                <input
                                                    id="upload-reserved-doi-preview-pending"
                                                    type="text"
                                                    readonly
                                                    tabindex="-1"
                                                    class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2 font-mono text-sm text-gray-800 outline-none"
                                                    :value="
                                                        reservedDoiDisplayUrl
                                                    "
                                                    aria-labelledby="upload-doi-label"
                                                    aria-describedby="upload-reserved-doi-hint"
                                                />
                                                <button
                                                    type="button"
                                                    class="inline-flex shrink-0 items-center gap-1.5 border-l border-gray-200 bg-white px-3 py-2 text-sm font-medium text-teal-700 transition hover:bg-teal-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-500"
                                                    @click="
                                                        copyReservedDoiToClipboard()
                                                    "
                                                >
                                                    <ClipboardDocumentIcon
                                                        class="h-4 w-4 shrink-0"
                                                        aria-hidden="true"
                                                    />
                                                    Copy
                                                </button>
                                            </div>
                                            <p
                                                id="upload-reserved-doi-hint"
                                                class="text-xs leading-relaxed text-gray-500"
                                            >
                                                Copy this preview URL to cite
                                                the dataset in your files or
                                                manuscripts. The identifier is
                                                registered when you publish this
                                                submission.
                                            </p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <!-- End of Header -->

        <div class="relative flex min-h-0 flex-1 flex-col">
            <div v-if="!loading" class="flex min-h-0 flex-1 flex-col">
                <div class="mx-auto flex w-full min-h-0 flex-1 flex-col">
                    <div
                        v-if="drafts.length > 0 && !currentDraft"
                        class="px-12"
                    >
                        <div class="my-8 mx-12 mx-auto max-w-none">
                            <div
                                class="overflow-hidden bg-white border rounded-lg shadow-md"
                            >
                                <div
                                    class="border-b rounded-t-lg bg-gray-50 border-gray-200 px-4 py-5 sm:px-6"
                                >
                                    <div
                                        class="-ml-4 -mt-4 flex flex-wrap items-center justify-between sm:flex-nowrap"
                                    >
                                        <div class="ml-4 mt-4">
                                            <h3
                                                class="text-base font-semibold leading-6 text-gray-900"
                                            >
                                                Drafts
                                            </h3>
                                            <p
                                                class="mt-1 text-sm text-gray-500"
                                            >
                                                Please select one of the drafts
                                                below to continue or start a new
                                                submission
                                            </p>
                                        </div>
                                        <div
                                            class="ml-4 mt-4 flex items-center justify-between flex-shrink-0"
                                        >
                                            <div
                                                v-if="filteredDrafts.length > 0"
                                                class="text-sm text-gray-600 mr-4"
                                            >
                                                Showing
                                                {{
                                                    (currentDraftsPage - 1) *
                                                        draftsPerPage +
                                                    1
                                                }}
                                                to
                                                {{
                                                    Math.min(
                                                        currentDraftsPage *
                                                            draftsPerPage,
                                                        filteredDrafts.length
                                                    )
                                                }}
                                                of
                                                {{ filteredDrafts.length }}
                                                drafts
                                            </div>
                                            <div
                                                class="flex items-center gap-4"
                                            >
                                                <div class="w-72">
                                                    <SearchInput
                                                        v-model="
                                                            searchDraftQuery
                                                        "
                                                        rounded-full
                                                        name="draft-search"
                                                        placeholder="Search drafts..."
                                                        @reset="
                                                            searchDraftQuery =
                                                                ''
                                                        "
                                                    />
                                                </div>
                                                <button
                                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                                                    @click="createNewDraft()"
                                                >
                                                    + Create New
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="overflow-y-scroll h-[calc(100vh-290px)]"
                                >
                                    <!-- Empty search results message for drafts -->
                                    <div
                                        v-if="
                                            drafts.length > 0 &&
                                            searchDraftQuery &&
                                            filteredDrafts.length === 0
                                        "
                                        class="flex items-center justify-center h-full"
                                    >
                                        <EmptySearchState
                                            entity-type="drafts"
                                            layout="embedded"
                                            :search-query="searchDraftQuery"
                                            @clear-search="
                                                searchDraftQuery = ''
                                            "
                                        />
                                    </div>

                                    <!-- Drafts list -->
                                    <ul
                                        v-else
                                        role="list"
                                        class="divide-y divide-gray-200"
                                    >
                                        <li
                                            v-for="draft in paginatedDrafts"
                                            :key="draft.id"
                                            class="border-b px-5 py-4"
                                        >
                                            <div
                                                class="flex items-center justify-between"
                                            >
                                                <Link
                                                    :href="
                                                        route('upload', {
                                                            draft_id: draft.id,
                                                        })
                                                    "
                                                    class="flex-1 hover:cursor-pointer hover:bg-gray-50 -mx-5 -my-4 px-5 py-4"
                                                >
                                                    <div
                                                        class="flex items-center space-x-4"
                                                    >
                                                        <div
                                                            class="flex-1 min-w-0 mr-auto max-w-2xl"
                                                        >
                                                            <div
                                                                class="flex items-center gap-2 mb-1"
                                                            >
                                                                <p
                                                                    class="text-lg font-large text-black truncate"
                                                                >
                                                                    <b>{{
                                                                        draft.name
                                                                    }}</b>
                                                                </p>
                                                                <span
                                                                    v-if="
                                                                        draft.eln
                                                                    "
                                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                                                >
                                                                    {{
                                                                        draft.external_id
                                                                    }}
                                                                </span>
                                                                <DraftStatusBadge
                                                                    :draft="
                                                                        draft
                                                                    "
                                                                />
                                                            </div>
                                                            <p
                                                                class="text-sm font-medium text-gray-700 truncate pr-10"
                                                            >
                                                                {{
                                                                    draft.description
                                                                }}
                                                            </p>
                                                            <p
                                                                class="text-sm font-medium text-gray-500 truncate"
                                                            >
                                                                ID:
                                                                {{ draft.key }}
                                                                &middot; Created
                                                                at:
                                                                {{
                                                                    formatDateTime(
                                                                        draft.created_at
                                                                    )
                                                                }}
                                                                <span
                                                                    v-if="
                                                                        draft.external_id
                                                                    "
                                                                >
                                                                    &middot;
                                                                    External ID:
                                                                    {{
                                                                        draft.external_id
                                                                    }}
                                                                </span>
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <svg
                                                                class="h-5 w-5 flex-none text-gray-400"
                                                                viewBox="0 0 20 20"
                                                                fill="currentColor"
                                                                aria-hidden="true"
                                                            >
                                                                <path
                                                                    fill-rule="evenodd"
                                                                    d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                                    clip-rule="evenodd"
                                                                ></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </Link>
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                                <div
                                    class="flex items-center justify-between rounded-b-lg px-6 py-3 border-t bg-white"
                                >
                                    <div class="text-sm text-gray-600">
                                        Page {{ currentDraftsPage }} of
                                        {{ totalDraftPages }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            class="px-3 py-1.5 rounded border text-sm disabled:opacity-50"
                                            :disabled="currentDraftsPage === 1"
                                            @click="
                                                currentDraftsPage = Math.max(
                                                    1,
                                                    currentDraftsPage - 1
                                                )
                                            "
                                        >
                                            Previous
                                        </button>
                                        <button
                                            type="button"
                                            class="px-3 py-1.5 rounded border text-sm disabled:opacity-50"
                                            :disabled="
                                                currentDraftsPage ===
                                                totalDraftPages
                                            "
                                            @click="
                                                currentDraftsPage = Math.min(
                                                    totalDraftPages,
                                                    currentDraftsPage + 1
                                                )
                                            "
                                        >
                                            Next
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex min-h-0 flex-1 flex-col">
                        <div
                            v-if="showPrimer"
                            class="flex min-h-0 flex-1 flex-col"
                        >
                            <div
                                class="min-h-0 flex-1 overflow-y-auto px-4 sm:px-6 py-4"
                            >
                                <Primer
                                    :show-actions="!!currentDraft"
                                    :processing="createDatasetForm.processing"
                                    @proceed="skipPrimer()"
                                    @dont-show-again="hidePrimer()"
                                />
                            </div>
                        </div>
                        <div v-else class="flex min-h-0 flex-1 flex-col">
                            <div
                                v-if="currentStep && currentDraft"
                                class="flex min-h-0 flex-1 flex-col overflow-hidden"
                            >
                                <div
                                    v-if="
                                        processingWarnings.length > 0 &&
                                        currentStep.id == '1'
                                    "
                                    class="mx-5 mt-3"
                                    role="alert"
                                >
                                    <div
                                        class="flex gap-3 rounded-lg border border-amber-200 bg-amber-50/95 px-4 py-3 shadow-sm ring-1 ring-amber-900/5 dark:border-amber-900/60 dark:bg-amber-950/50 dark:ring-amber-500/10"
                                    >
                                        <ExclamationCircleIcon
                                            class="mt-0.5 h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400"
                                            aria-hidden="true"
                                        />
                                        <div
                                            class="min-w-0 flex-1 text-sm leading-relaxed text-amber-900 dark:text-amber-100"
                                        >
                                            <p class="font-semibold">
                                                Sample folder structure needs
                                                attention before you can proceed
                                            </p>
                                            <ul
                                                class="mt-1 list-disc space-y-1 pl-5"
                                            >
                                                <li
                                                    v-for="(
                                                        warning, idx
                                                    ) in processingWarnings"
                                                    :key="idx"
                                                >
                                                    {{ warning }}
                                                </li>
                                            </ul>
                                            <p
                                                class="mt-2 text-xs text-amber-800 dark:text-amber-200"
                                            >
                                                Reorganise the folders above so
                                                each sample lives in its own
                                                top-level folder, then click
                                                Proceed again.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-if="currentStep.id == '1'"
                                    id="submission-dropzone"
                                    class="flex min-h-0 flex-1 flex-col overflow-hidden border-gray-100"
                                >
                                    <div
                                        v-if="filesErrorMessage"
                                        class="mx-5 mb-3 pt-4"
                                    >
                                        <div
                                            role="alert"
                                            class="flex gap-3 rounded-lg border border-red-200 bg-red-50/95 px-4 py-3 shadow-sm ring-1 ring-red-900/5 dark:border-red-900/60 dark:bg-red-950/50 dark:ring-red-500/10"
                                        >
                                            <ExclamationCircleIcon
                                                class="mt-0.5 h-5 w-5 shrink-0 text-red-600 dark:text-red-400"
                                                aria-hidden="true"
                                            />
                                            <div
                                                class="min-w-0 flex-1 text-sm font-medium leading-relaxed text-red-900 dark:text-red-100 [&_a]:font-semibold [&_a]:text-red-800 [&_a]:underline [&_a]:underline-offset-2 [&_a]:transition-colors hover:[&_a]:text-red-950 dark:[&_a]:text-red-200 dark:hover:[&_a]:text-red-50"
                                                v-html="
                                                    sanitizeHtml(
                                                        filesErrorMessage
                                                    )
                                                "
                                            ></div>
                                        </div>
                                    </div>
                                    <div
                                        class="relative mt-3 flex min-h-0 flex-1 flex-col overflow-hidden bg-white"
                                    >
                                        <div
                                            id="tour-step-upload-spectra"
                                            class="flex min-h-[500px] flex-1 flex-col overflow-hidden"
                                        >
                                            <file-system-browser
                                                ref="fsbRef"
                                                :readonly="false"
                                                :draft="currentDraft"
                                                :height="'flex-1 w-full'"
                                                @loading="filesLoading"
                                                @show-processing-logs="
                                                    showProcessingLogs(
                                                        currentDraft
                                                    )
                                                "
                                            ></file-system-browser>
                                        </div>
                                    </div>
                                    <jet-input-error
                                        :message="draftForm.errors.studies"
                                        class="mt-2"
                                    />
                                </div>
                                <div
                                    v-if="currentStep.id == '2'"
                                    class="flex min-h-0 flex-1 flex-col"
                                >
                                    <div
                                        class="h-[calc(100vh-135px)] overflow-hidden border-t border-gray-200 dark:border-gray-700"
                                    >
                                        <div class="flex-1 flex">
                                            <div
                                                v-if="showSummary"
                                                aria-label="Sections"
                                                class="flex flex-shrink-0 w-64 h-[calc(100vh-135px)] min-h-0 flex-col overflow-y-hidden border-r border-gray-200 bg-white dark:border-gray-700 dark:bg-slate-900"
                                            >
                                                <div
                                                    :class="[
                                                        displaySamplesSummaryInfo
                                                            ? 'bg-gray-100 text-gray-900 dark:bg-slate-800 dark:text-slate-100'
                                                            : 'text-gray-600 dark:text-slate-400',
                                                        'flex-shrink-0 cursor-pointer border-b border-gray-200 px-3 py-2.5 text-left text-xs font-semibold uppercase tracking-wide transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-slate-800/80',
                                                    ]"
                                                    @click="showSamplesSummary"
                                                >
                                                    <span>Summary</span>
                                                </div>
                                                <div
                                                    class="flex-shrink-0 border-b border-gray-200 bg-gray-50/90 px-3 py-2.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:border-gray-700 dark:bg-slate-900/50 dark:text-slate-400"
                                                >
                                                    <div
                                                        class="flex items-center justify-between gap-2"
                                                    >
                                                        <span>
                                                            {{
                                                                pluralize(
                                                                    "SAMPLE",
                                                                    studies.length
                                                                )
                                                            }}
                                                            ({{
                                                                studies.length
                                                            }})
                                                        </span>
                                                        <div
                                                            class="flex items-center gap-3"
                                                        >
                                                            <button
                                                                v-if="
                                                                    studiesWithDatasets.length >
                                                                    0
                                                                "
                                                                type="button"
                                                                class="rounded p-0.5 text-gray-500 transition hover:bg-gray-100/80 hover:text-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-40"
                                                                :disabled="
                                                                    allStudyDatasetsExpanded
                                                                "
                                                                title="Expand all datasets"
                                                                aria-label="Expand all datasets"
                                                                @click="
                                                                    expandAllStudyDatasets()
                                                                "
                                                            >
                                                                <ArrowsPointingOutIcon
                                                                    class="h-4 w-4"
                                                                    aria-hidden="true"
                                                                />
                                                            </button>
                                                            <button
                                                                v-if="
                                                                    studiesWithDatasets.length >
                                                                    0
                                                                "
                                                                type="button"
                                                                class="rounded p-0.5 text-gray-500 transition hover:bg-gray-100/80 hover:text-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-40"
                                                                :disabled="
                                                                    Object.keys(
                                                                        expandedStudyIds
                                                                    ).filter(
                                                                        (k) =>
                                                                            expandedStudyIds[
                                                                                k
                                                                            ]
                                                                    ).length ===
                                                                    0
                                                                "
                                                                title="Collapse all datasets"
                                                                aria-label="Collapse all datasets"
                                                                @click="
                                                                    collapseAllStudyDatasets()
                                                                "
                                                            >
                                                                <ArrowsPointingInIcon
                                                                    class="h-4 w-4"
                                                                    aria-hidden="true"
                                                                />
                                                            </button>
                                                            <div
                                                                class="cursor-pointer tooltip"
                                                                @click="
                                                                    toggleCompoundDetails()
                                                                "
                                                            >
                                                                <EyeIcon
                                                                    v-if="
                                                                        !showCompoundDetails
                                                                    "
                                                                    class="w-4 h-4 text-gray-600 hover:text-gray-500"
                                                                />
                                                                <EyeSlashIcon
                                                                    v-else
                                                                    class="w-4 h-4 text-gray-600 hover:text-gray-500"
                                                                />
                                                                <div
                                                                    class="bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextleft"
                                                                >
                                                                    <span
                                                                        v-if="
                                                                            !showCompoundDetails
                                                                        "
                                                                        >Show
                                                                        Compound
                                                                        details</span
                                                                    ><span
                                                                        v-else
                                                                        >Hide
                                                                        Compound
                                                                        details</span
                                                                    >
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    id="tour-step-side-panel-studies"
                                                    class="no-scrollbar min-h-0 flex-1 overflow-auto"
                                                >
                                                    <div
                                                        v-for="(
                                                            study, $index
                                                        ) in studies"
                                                        :key="study.slug"
                                                        :aria-current="
                                                            selectedStudy &&
                                                            selectedStudy.id ===
                                                                study.id
                                                                ? 'true'
                                                                : undefined
                                                        "
                                                        :class="[
                                                            'flex border-b border-gray-100 transition-colors dark:border-gray-700',
                                                            study.internal_status ==
                                                            'complete'
                                                                ? 'cursor-pointer'
                                                                : '',
                                                            study.internal_status ==
                                                            'complete'
                                                                ? selectedStudy &&
                                                                  selectedStudy.id ==
                                                                      study.id
                                                                    ? 'border-l-[3px] border-l-teal-600 bg-gray-50 dark:border-l-teal-500 dark:bg-slate-800/90'
                                                                    : 'border-l-[3px] border-l-transparent hover:bg-gray-50/90 dark:hover:bg-slate-800/50'
                                                                : 'cursor-not-allowed select-none opacity-80',
                                                        ]"
                                                        :aria-disabled="
                                                            study.internal_status !=
                                                            'complete'
                                                        "
                                                        @click.stop="
                                                            study.internal_status ==
                                                                'complete' &&
                                                                selectStudy(
                                                                    study,
                                                                    $index
                                                                )
                                                        "
                                                    >
                                                        <div
                                                            class="w-full px-3 py-3 text-sm"
                                                        >
                                                            <div
                                                                v-if="
                                                                    study.internal_status ==
                                                                    'complete'
                                                                "
                                                                class="font-medium text-gray-900 dark:text-slate-100"
                                                            >
                                                                <div
                                                                    v-if="
                                                                        showCompoundDetails &&
                                                                        study
                                                                            .sample
                                                                            .molecules[0]
                                                                    "
                                                                    class="mb-3 overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-600 dark:bg-slate-900/40"
                                                                >
                                                                    <Depictor2D
                                                                        class="rounded-md"
                                                                        :molecule="
                                                                            study
                                                                                .sample
                                                                                .molecules[0]
                                                                                .canonical_smiles
                                                                        "
                                                                    ></Depictor2D>
                                                                </div>
                                                                <div
                                                                    class="flex items-start justify-between gap-2 pb-0.5"
                                                                >
                                                                    <span
                                                                        class="min-w-0 flex-1 leading-snug text-gray-900 dark:text-slate-100"
                                                                    >
                                                                        {{
                                                                            study.name
                                                                        }}
                                                                    </span>
                                                                    <span
                                                                        v-if="
                                                                            study
                                                                                .sample
                                                                                .molecules
                                                                                .length >
                                                                            0
                                                                        "
                                                                        class="shrink-0 opacity-70"
                                                                    >
                                                                        <img
                                                                            class="h-5 w-5 text-blue-gray-400"
                                                                            src="https://upload.wikimedia.org/wikipedia/sco/3/35/ChEBI_logo.png"
                                                                            alt=""
                                                                        />
                                                                    </span>
                                                                </div>

                                                                <div
                                                                    v-if="
                                                                        study.datasets &&
                                                                        study
                                                                            .datasets
                                                                            .length >
                                                                            0
                                                                    "
                                                                    class="mt-2"
                                                                >
                                                                    <button
                                                                        type="button"
                                                                        class="inline-flex items-center gap-1 rounded text-[11px] font-medium text-gray-600 hover:text-gray-900 focus:outline-none dark:text-slate-400 dark:hover:text-slate-100"
                                                                        :aria-expanded="
                                                                            isStudyDatasetsExpanded(
                                                                                study
                                                                            )
                                                                        "
                                                                        @click.stop="
                                                                            toggleStudyDatasets(
                                                                                study
                                                                            )
                                                                        "
                                                                    >
                                                                        <ChevronDownIcon
                                                                            v-if="
                                                                                isStudyDatasetsExpanded(
                                                                                    study
                                                                                )
                                                                            "
                                                                            class="h-3 w-3"
                                                                        />
                                                                        <ChevronRightIcon
                                                                            v-else
                                                                            class="h-3 w-3"
                                                                        />
                                                                        <span>
                                                                            {{
                                                                                study
                                                                                    .datasets
                                                                                    .length
                                                                            }}
                                                                            {{
                                                                                pluralize(
                                                                                    "dataset",
                                                                                    study
                                                                                        .datasets
                                                                                        .length
                                                                                )
                                                                            }}
                                                                        </span>
                                                                    </button>
                                                                    <ul
                                                                        v-if="
                                                                            isStudyDatasetsExpanded(
                                                                                study
                                                                            )
                                                                        "
                                                                        class="mt-1.5 flex list-none flex-wrap gap-1.5 p-0 text-left"
                                                                        role="list"
                                                                    >
                                                                        <li
                                                                            v-for="ds in study.datasets"
                                                                            :key="
                                                                                ds.id
                                                                            "
                                                                            class="inline-flex max-w-full min-w-0 items-center gap-1 rounded-full border px-2.5 py-1 text-[11px] leading-snug"
                                                                            :class="
                                                                                ds.has_nmrium
                                                                                    ? 'border-teal-200/90 bg-teal-50 text-teal-900 ring-1 ring-inset ring-teal-500/15 dark:border-teal-800 dark:bg-teal-950/50 dark:text-teal-100 dark:ring-teal-400/20'
                                                                                    : 'border-gray-200 bg-gray-50 text-gray-700 dark:border-gray-600 dark:bg-slate-800 dark:text-slate-300'
                                                                            "
                                                                        >
                                                                            <span
                                                                                class="min-w-0 truncate font-medium"
                                                                                >{{
                                                                                    ds.name
                                                                                }}</span
                                                                            ><span
                                                                                v-if="
                                                                                    ds.type
                                                                                "
                                                                                class="shrink-0 text-gray-500 dark:text-slate-400"
                                                                            >
                                                                                ·
                                                                                {{
                                                                                    ds.type
                                                                                }}</span
                                                                            >
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div v-else>
                                                                <div
                                                                    class="p-4 max-w-sm w-full mx-auto"
                                                                >
                                                                    <div
                                                                        class="animate-pulse flex space-x-4"
                                                                    >
                                                                        <div
                                                                            class="flex-1 space-y-6 py-1"
                                                                        >
                                                                            <div
                                                                                class="h-2 bg-slate-200"
                                                                            ></div>
                                                                            <div
                                                                                class="space-y-3"
                                                                            >
                                                                                <div
                                                                                    class="grid grid-cols-3 gap-4"
                                                                                >
                                                                                    <div
                                                                                        class="h-2 bg-slate-200 rounded col-span-2"
                                                                                    ></div>
                                                                                    <div
                                                                                        class="h-2 bg-slate-200 rounded col-span-1"
                                                                                    ></div>
                                                                                </div>
                                                                                <div
                                                                                    class="h-2 bg-slate-200 rounded"
                                                                                ></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-1 px-2 bg-white">
                                                <div
                                                    v-if="
                                                        displaySamplesSummaryInfo
                                                    "
                                                >
                                                    <div
                                                        class="cursor-pointer tooltip m-3 text-gray-500 hover:text-gray-700"
                                                        @click="
                                                            toggleSummaryBar()
                                                        "
                                                    >
                                                        <ChevronDoubleLeftIcon
                                                            v-if="showSummary"
                                                            class="h-6 w-6"
                                                            aria-hidden="true"
                                                        />
                                                        <ChevronDoubleRightIcon
                                                            v-else
                                                            class="h-6 w-6"
                                                            aria-hidden="true"
                                                        />
                                                        <div
                                                            class="bg-gray-900 text-center text-white px-2 py-1 text-xs font-small shadow-lg rounded-md tooltiptextright"
                                                        >
                                                            <span
                                                                v-if="
                                                                    !showSummary
                                                                "
                                                                >Show Compound
                                                                Summary</span
                                                            ><span v-else
                                                                >Hide Compound
                                                                Summary</span
                                                            >
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="-mx-2 border-b border-b-gray-900/10 lg:border-t lg:border-t-gray-900/5"
                                                    >
                                                        <dl
                                                            class="mx-auto grid max-w-7xl grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 lg:px-2 xl:px-0"
                                                        >
                                                            <div
                                                                class="flex items-baseline flex-wrap justify-between gap-y-2 gap-x-4 border-t border-gray-900/5 px-4 py-10 sm:px-6 lg:border-t-0 xl:px-8"
                                                            >
                                                                <dt
                                                                    class="text-sm font-medium leading-6 text-gray-500"
                                                                >
                                                                    Samples
                                                                </dt>
                                                                <!-- <dd class="text-xs font-medium text-gray-700">x%</dd> -->
                                                                <dd
                                                                    class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900"
                                                                >
                                                                    {{
                                                                        studies.length
                                                                    }}
                                                                </dd>
                                                            </div>
                                                            <div
                                                                class="flex items-baseline flex-wrap justify-between gap-y-2 gap-x-4 border-t border-gray-900/5 px-4 py-10 sm:px-6 lg:border-t-0 xl:px-8 sm:border-l"
                                                            >
                                                                <dt
                                                                    class="text-sm font-medium leading-6 text-gray-500"
                                                                >
                                                                    Spectra
                                                                </dt>
                                                                <!-- <dd class="text-xs font-medium text-rose-600">x%</dd> -->
                                                                <dd
                                                                    class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900"
                                                                >
                                                                    {{
                                                                        spectraCount
                                                                    }}
                                                                </dd>
                                                            </div>
                                                            <div
                                                                class="flex items-baseline flex-wrap justify-between gap-y-2 gap-x-4 border-t border-gray-900/5 px-4 py-10 sm:px-6 lg:border-t-0 xl:px-8 lg:border-l"
                                                            >
                                                                <dt
                                                                    class="text-sm font-medium leading-6 text-gray-500"
                                                                >
                                                                    Molecules
                                                                </dt>
                                                                <!-- <dd class="text-xs font-medium text-gray-700">x%</dd> -->
                                                                <dd
                                                                    class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900"
                                                                >
                                                                    {{
                                                                        moleculesCount
                                                                    }}
                                                                </dd>
                                                            </div>
                                                            <div
                                                                v-if="
                                                                    inprogressStudies.length ==
                                                                    0
                                                                "
                                                                class="flex items-baseline flex-wrap justify-between gap-y-2 gap-x-4 border-t border-gray-900/5 px-4 py-10 sm:px-6 lg:border-t-0 xl:px-8 sm:border-l"
                                                            >
                                                                <dt
                                                                    class="text-sm font-medium leading-6 text-gray-500"
                                                                >
                                                                    Samples
                                                                    Validation
                                                                </dt>
                                                                <!-- <dd class="text-xs font-medium text-rose-600">x%</dd> -->
                                                                <dd
                                                                    class="w-full flex-none"
                                                                >
                                                                    <span
                                                                        v-if="
                                                                            validationStatus
                                                                        "
                                                                    >
                                                                        <span
                                                                            class="text-green-600 text-3xl font-medium leading-10 tracking-tight text-gray-900"
                                                                        >
                                                                            Success
                                                                        </span>
                                                                    </span>
                                                                    <span
                                                                        v-else
                                                                    >
                                                                        <span
                                                                            class="text-yellow-600 text-3xl font-medium leading-10 tracking-tight text-gray-900"
                                                                        >
                                                                            Incomplete
                                                                        </span>
                                                                        <span
                                                                            class="text-sm text-red-500 tracking-tight"
                                                                        >
                                                                            Meta
                                                                            data
                                                                            is
                                                                            missing.
                                                                            Please
                                                                            check
                                                                            the
                                                                            validation
                                                                            report
                                                                            below.
                                                                        </span>
                                                                    </span>
                                                                </dd>
                                                            </div>
                                                        </dl>
                                                    </div>
                                                    <div
                                                        class="overflow-y-scroll h-[calc(100vh-153px)] mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8"
                                                    >
                                                        <div
                                                            v-if="
                                                                inprogressStudies.length ===
                                                                0
                                                            "
                                                        >
                                                            <div
                                                                v-if="
                                                                    importPendingSamples.length >
                                                                    0
                                                                "
                                                                class="mb-4"
                                                            >
                                                                <div
                                                                    class="bg-white border shadow sm:rounded-lg"
                                                                >
                                                                    <div
                                                                        class="px-4 py-5 sm:p-6"
                                                                    >
                                                                        <h3
                                                                            class="text-base font-semibold leading-6 text-gray-900"
                                                                        >
                                                                            Spectra
                                                                            Metadata
                                                                            Not
                                                                            Found
                                                                        </h3>
                                                                        <div
                                                                            class="mt-2 max-w-xl text-sm text-gray-500"
                                                                        >
                                                                            <p>
                                                                                Some
                                                                                important
                                                                                Spectra
                                                                                metadata
                                                                                are
                                                                                needed.
                                                                                Would
                                                                                you
                                                                                like
                                                                                us
                                                                                to
                                                                                automatically
                                                                                import
                                                                                the
                                                                                missing
                                                                                Spectra
                                                                                information
                                                                                and
                                                                                kickstart
                                                                                the
                                                                                processing?
                                                                            </p>
                                                                        </div>
                                                                        <div
                                                                            class="mt-5"
                                                                        >
                                                                            <button
                                                                                type="button"
                                                                                class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500"
                                                                                @click="
                                                                                    autoImport
                                                                                "
                                                                            >
                                                                                Import
                                                                                now
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="mx-auto grid mt-10 max-w-2xl grid-cols-1 grid-rows-1 items-start gap-x-8 gap-y-8 lg:mx-0 lg:max-w-none lg:grid-cols-3"
                                                        >
                                                            <div
                                                                class="lg:col-span-3 mb-24 pb-24"
                                                            >
                                                                <div
                                                                    v-if="
                                                                        validation &&
                                                                        inprogressStudies.length ==
                                                                            0
                                                                    "
                                                                >
                                                                    <button
                                                                        class="px-3 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ml-2 float-right hover:bg-gray-200 hover:text-gray-900"
                                                                        @click="
                                                                            fetchValidations
                                                                        "
                                                                    >
                                                                        <svg
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            fill="none"
                                                                            viewBox="0 0 24 24"
                                                                            stroke-width="1.5"
                                                                            stroke="currentColor"
                                                                            class="w-4 h-4 mr-2 inline"
                                                                        >
                                                                            <path
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"
                                                                            />
                                                                        </svg>

                                                                        Refresh
                                                                    </button>
                                                                    <Validation
                                                                        :project="
                                                                            project
                                                                        "
                                                                        :validation="
                                                                            validation
                                                                        "
                                                                        :mode="'study'"
                                                                        @update="
                                                                            editData
                                                                        "
                                                                    ></Validation>
                                                                    &emsp;
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div v-else>
                                                    <div
                                                        v-if="selectedStudy"
                                                        class="mx-auto flex flex-col md:px-0 xl:px-0"
                                                    >
                                                        <div>
                                                            <div>
                                                                <div
                                                                    class="px-4 py-1.5 -mx-2 bg-gray-50 border-b px-4 py-3 flex items-center font-semibold text-sm text-slate-900 dark:text-slate-200 bg-slate-50/90 dark:bg-slate-700/90 backdrop-blur-sm ring-1 ring-slate-900/10 dark:ring-black/10 break-all"
                                                                >
                                                                    <div
                                                                        class="cursor-pointer tooltip m-3 text-gray-500 hover:text-gray-700"
                                                                        @click="
                                                                            toggleSummaryBar()
                                                                        "
                                                                    >
                                                                        <ChevronDoubleLeftIcon
                                                                            v-if="
                                                                                showSummary
                                                                            "
                                                                            class="h-6 w-6"
                                                                            aria-hidden="true"
                                                                        />
                                                                        <ChevronDoubleRightIcon
                                                                            v-else
                                                                            class="h-6 w-6"
                                                                            aria-hidden="true"
                                                                        />
                                                                        <div
                                                                            class="bg-gray-900 text-center text-white px-2 py-1 text-xs font-small shadow-lg rounded-md tooltiptextright"
                                                                        >
                                                                            <span
                                                                                v-if="
                                                                                    !showSummary
                                                                                "
                                                                                >Show
                                                                                Compound
                                                                                Summary</span
                                                                            ><span
                                                                                v-else
                                                                                >Hide
                                                                                Compound
                                                                                Summary</span
                                                                            >
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="flex min-w-0 flex-1 flex-col gap-1.5 py-0.5 pr-1 sm:pr-2"
                                                                    >
                                                                        <div
                                                                            class="flex w-full min-w-0 flex-wrap items-start justify-between gap-x-4 gap-y-1"
                                                                        >
                                                                            <label
                                                                                for="upload-sample-name"
                                                                                class="shrink-0 text-xs font-medium normal-case tracking-normal text-gray-600 dark:text-slate-300"
                                                                            >
                                                                                Sample
                                                                                name
                                                                            </label>
                                                                            <div
                                                                                class="flex min-w-0 flex-1 flex-wrap items-center justify-end gap-x-3 gap-y-1"
                                                                            >
                                                                                <p
                                                                                    id="upload-sample-name-hint"
                                                                                    class="max-w-md text-right text-xs font-normal normal-case tracking-normal leading-snug text-gray-500 dark:text-slate-400"
                                                                                >
                                                                                    Click
                                                                                    the
                                                                                    name
                                                                                    or
                                                                                    pencil
                                                                                    to
                                                                                    edit.
                                                                                    Press
                                                                                    Enter
                                                                                    to
                                                                                    save.
                                                                                </p>
                                                                                <span
                                                                                    v-if="
                                                                                        studySaving
                                                                                    "
                                                                                    class="inline-flex shrink-0 items-center rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-medium text-sky-800 dark:bg-sky-900/40 dark:text-sky-200"
                                                                                >
                                                                                    Saving…
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="group flex w-full min-w-0 items-stretch overflow-hidden rounded-lg border border-gray-300 bg-white/95 shadow-sm transition-colors hover:border-teal-400/90 hover:shadow-md focus-within:border-teal-500 focus-within:ring-2 focus-within:ring-teal-500/25 focus-within:ring-offset-2 focus-within:ring-offset-white dark:border-gray-600 dark:bg-slate-800/95 dark:hover:border-teal-500/70 dark:focus-within:ring-teal-500/40 dark:focus-within:ring-offset-slate-800"
                                                                        >
                                                                            <input
                                                                                id="upload-sample-name"
                                                                                ref="studyNameInput"
                                                                                v-model="
                                                                                    studyNameDraft
                                                                                "
                                                                                type="text"
                                                                                :disabled="
                                                                                    busy
                                                                                "
                                                                                name="sample-name"
                                                                                autocomplete="off"
                                                                                spellcheck="true"
                                                                                aria-describedby="upload-sample-name-hint"
                                                                                class="min-w-0 flex-1 cursor-text border-0 bg-transparent px-3 py-2 text-base font-semibold normal-case tracking-normal text-gray-900 outline-none placeholder:text-gray-400 dark:text-slate-100 dark:placeholder:text-slate-500"
                                                                                @blur="
                                                                                    saveStudyNameEdit
                                                                                "
                                                                                @keydown.enter.prevent="
                                                                                    $event.target.blur()
                                                                                "
                                                                                @keydown.esc.prevent="
                                                                                    cancelStudyNameEdit
                                                                                "
                                                                            />
                                                                            <button
                                                                                type="button"
                                                                                class="flex shrink-0 items-center rounded-r-lg border-l border-gray-200 px-3 text-gray-400 transition-colors hover:bg-gray-50 hover:text-teal-600 focus-visible:z-10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-500 dark:border-gray-600 dark:hover:bg-slate-700/80 dark:hover:text-teal-400"
                                                                                aria-label="Focus sample name field"
                                                                                @click="
                                                                                    focusStudyName
                                                                                "
                                                                            >
                                                                                <PencilIcon
                                                                                    class="h-4 w-4"
                                                                                    aria-hidden="true"
                                                                                />
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="scroll-smooth overflow-y-scroll h-[calc(100vh-153px)]"
                                                                    @scroll="
                                                                        onScroll
                                                                    "
                                                                >
                                                                    <div
                                                                        v-if="
                                                                            !hideDownArrow
                                                                        "
                                                                        class="absolute bottom-0 right-0 object-right-bottom rounded-xl overflow-auto p-8"
                                                                    >
                                                                        <div
                                                                            class="flex justify-center"
                                                                        >
                                                                            <a
                                                                                href="#chemical-composition"
                                                                            >
                                                                                <div
                                                                                    class="drop-shadow-md animate-bounce bg-gray-300 dark:bg-slate-800 p-2 w-10 h-10 ring-1 ring-slate-900/5 dark:ring-slate-200/20 shadow-lg rounded-full flex items-center justify-center"
                                                                                >
                                                                                    <svg
                                                                                        class="w-6 h-6 text-white-500"
                                                                                        fill="none"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        stroke-width="2"
                                                                                        viewBox="0 0 24 24"
                                                                                        stroke="currentColor"
                                                                                    >
                                                                                        <path
                                                                                            d="M19 14l-7 7m0 0l-7-7m7 7V3"
                                                                                        ></path>
                                                                                    </svg>
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="px-2 sm:px-2 md:px-0 pb-24"
                                                                    >
                                                                        <div
                                                                            class="px-2"
                                                                        >
                                                                            <div
                                                                                v-if="
                                                                                    !showPrimer &&
                                                                                    currentDraft &&
                                                                                    currentStep
                                                                                "
                                                                                id="spectra-editor-anchor"
                                                                                class="relative mt-7 mb-3 min-h-[120px]"
                                                                            >
                                                                                <div
                                                                                    v-if="
                                                                                        loader.kind ===
                                                                                        'iframe'
                                                                                    "
                                                                                    class="pointer-events-none absolute inset-x-0 -top-1 z-10 flex flex-col items-stretch"
                                                                                >
                                                                                    <div
                                                                                        class="h-1 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
                                                                                    >
                                                                                        <div
                                                                                            class="h-full w-2/5 animate-pulse rounded-full bg-sky-600 dark:bg-sky-500"
                                                                                        ></div>
                                                                                    </div>
                                                                                    <p
                                                                                        class="mt-1 truncate text-left text-xs font-medium text-gray-600 dark:text-gray-300"
                                                                                    >
                                                                                        <span
                                                                                            v-if="
                                                                                                loader
                                                                                                    .iframe
                                                                                                    ?.phase ===
                                                                                                'save'
                                                                                            "
                                                                                            >Saving…</span
                                                                                        >
                                                                                        <span
                                                                                            v-else
                                                                                            >Loading
                                                                                            spectra</span
                                                                                        >
                                                                                        <span
                                                                                            v-if="
                                                                                                loader
                                                                                                    .iframe
                                                                                                    ?.sampleLabel
                                                                                            "
                                                                                            class="text-gray-400 dark:text-gray-500"
                                                                                            >&nbsp;·
                                                                                            {{
                                                                                                loader
                                                                                                    .iframe
                                                                                                    .sampleLabel
                                                                                            }}</span
                                                                                        >
                                                                                    </p>
                                                                                </div>
                                                                                <SpectraEditor
                                                                                    ref="spectraEditorREF"
                                                                                    :project="
                                                                                        project
                                                                                    "
                                                                                    :study="
                                                                                        selectedStudy
                                                                                    "
                                                                                    @loading="
                                                                                        spectraLoading
                                                                                    "
                                                                                ></SpectraEditor>
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="mx-auto flex max-w-7xl flex-col gap-3"
                                                                        >
                                                                            <HifsaPanel
                                                                                v-if="
                                                                                    selectedStudy
                                                                                "
                                                                                :hifsa-data="
                                                                                    selectedStudy.hifsa_data
                                                                                "
                                                                                :hifsa-pdf-url="
                                                                                    selectedStudy.hifsa_pdf_url
                                                                                "
                                                                                :molecules="
                                                                                    selectedStudy
                                                                                        .sample
                                                                                        ?.molecules ||
                                                                                    []
                                                                                "
                                                                                :expanded="
                                                                                    hifsaExpanded
                                                                                "
                                                                                id-prefix="upload-hifsa"
                                                                                @update:expanded="
                                                                                    hifsaExpanded =
                                                                                        $event
                                                                                "
                                                                            />

                                                                            <ChemicalCompositionEditor
                                                                                v-if="
                                                                                    selectedStudy
                                                                                "
                                                                                :study="
                                                                                    selectedStudy
                                                                                "
                                                                                :can-update-study="
                                                                                    true
                                                                                "
                                                                                :chemistry-standardize-url="
                                                                                    chemistryStandardizeUrl
                                                                                "
                                                                                :expanded="
                                                                                    chemicalCompositionExpanded
                                                                                "
                                                                                id-prefix="upload-chemical-composition"
                                                                                @update:expanded="
                                                                                    chemicalCompositionExpanded =
                                                                                        $event
                                                                                "
                                                                            />
                                                                            <section
                                                                                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm ring-1 ring-gray-900/5 dark:border-gray-700 dark:bg-slate-800/90 dark:ring-white/5"
                                                                            >
                                                                                <button
                                                                                    type="button"
                                                                                    class="flex w-full items-center justify-between gap-3 border-b border-gray-100 px-3 py-3 text-left transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-500 dark:border-gray-700 dark:hover:bg-slate-800/80 sm:px-4"
                                                                                    :aria-expanded="
                                                                                        assignmentsExpanded
                                                                                    "
                                                                                    aria-controls="upload-assignments-panel"
                                                                                    @click="
                                                                                        assignmentsExpanded =
                                                                                            !assignmentsExpanded
                                                                                    "
                                                                                >
                                                                                    <div
                                                                                        class="min-w-0 flex-1"
                                                                                    >
                                                                                        <h3
                                                                                            id="upload-assignments-heading"
                                                                                            class="text-lg font-semibold tracking-tight text-gray-900 dark:text-slate-100"
                                                                                        >
                                                                                            Assignments
                                                                                        </h3>
                                                                                    </div>
                                                                                    <ChevronRightIcon
                                                                                        class="h-5 w-5 shrink-0 text-gray-400 transition-transform duration-200 dark:text-slate-500"
                                                                                        :class="{
                                                                                            'rotate-90':
                                                                                                assignmentsExpanded,
                                                                                        }"
                                                                                        aria-hidden="true"
                                                                                    />
                                                                                </button>
                                                                                <div
                                                                                    v-show="
                                                                                        assignmentsExpanded
                                                                                    "
                                                                                    id="upload-assignments-panel"
                                                                                    class="space-y-4 px-3 pb-4 pt-3 sm:px-4"
                                                                                    role="region"
                                                                                    aria-labelledby="upload-assignments-heading"
                                                                                >
                                                                                    <div
                                                                                        class="rounded-md border border-teal-100 bg-teal-50/70 p-3 text-xs leading-relaxed text-teal-900 dark:border-teal-900/40 dark:bg-teal-950/40 dark:text-teal-100"
                                                                                    >
                                                                                        <p
                                                                                            class="font-medium"
                                                                                        >
                                                                                            Two
                                                                                            ways
                                                                                            to
                                                                                            record
                                                                                            assignments
                                                                                        </p>
                                                                                        <ol
                                                                                            class="mt-1 list-decimal space-y-1 pl-5"
                                                                                        >
                                                                                            <li>
                                                                                                Paste
                                                                                                an
                                                                                                <span
                                                                                                    class="font-semibold"
                                                                                                    >ACS-style
                                                                                                    assignment
                                                                                                    string</span
                                                                                                >
                                                                                                (or
                                                                                                a
                                                                                                list
                                                                                                of
                                                                                                atom-number
                                                                                                /
                                                                                                peak
                                                                                                pairs)
                                                                                                into
                                                                                                the
                                                                                                textarea
                                                                                                for
                                                                                                each
                                                                                                spectrum
                                                                                                below
                                                                                                and
                                                                                                hit
                                                                                                <span
                                                                                                    class="font-semibold"
                                                                                                    >Save</span
                                                                                                >.
                                                                                            </li>
                                                                                            <li>
                                                                                                Use
                                                                                                the
                                                                                                <span
                                                                                                    class="font-semibold"
                                                                                                    >NMRium
                                                                                                    viewer
                                                                                                    above</span
                                                                                                >
                                                                                                to
                                                                                                assign
                                                                                                atoms
                                                                                                graphically:
                                                                                                press
                                                                                                <kbd
                                                                                                    class="rounded border border-teal-300/70 bg-white px-1 py-0.5 text-[10px] font-mono dark:border-teal-700 dark:bg-slate-800"
                                                                                                    >r</kbd
                                                                                                >
                                                                                                for
                                                                                                ranges,
                                                                                                click
                                                                                                <span
                                                                                                    class="italic"
                                                                                                    >Auto
                                                                                                    Ranges
                                                                                                    Picking</span
                                                                                                >,
                                                                                                then
                                                                                                drag
                                                                                                a
                                                                                                range
                                                                                                link
                                                                                                onto
                                                                                                an
                                                                                                atom
                                                                                                in
                                                                                                the
                                                                                                structure.
                                                                                                Diastereotopic
                                                                                                atoms
                                                                                                expand
                                                                                                with
                                                                                                <kbd
                                                                                                    class="rounded border border-teal-300/70 bg-white px-1 py-0.5 text-[10px] font-mono dark:border-teal-700 dark:bg-slate-800"
                                                                                                    >Shift</kbd
                                                                                                >
                                                                                                +
                                                                                                click.
                                                                                                Assigned
                                                                                                atoms
                                                                                                turn
                                                                                                yellow.
                                                                                                <a
                                                                                                    href="https://docs.nmrium.org/help/assignment/"
                                                                                                    target="_blank"
                                                                                                    rel="noopener noreferrer"
                                                                                                    class="ml-1 underline decoration-dotted underline-offset-2 hover:no-underline"
                                                                                                    >Full
                                                                                                    guide
                                                                                                    ↗</a
                                                                                                >
                                                                                            </li>
                                                                                        </ol>
                                                                                    </div>

                                                                                    <div
                                                                                        v-if="
                                                                                            !selectedStudy ||
                                                                                            !(
                                                                                                selectedStudy.datasets &&
                                                                                                selectedStudy
                                                                                                    .datasets
                                                                                                    .length
                                                                                            )
                                                                                        "
                                                                                        class="rounded-md border border-dashed border-gray-300 bg-gray-50/70 p-4 text-sm text-gray-600 dark:border-gray-700 dark:bg-slate-800/60 dark:text-slate-300"
                                                                                    >
                                                                                        No
                                                                                        spectra
                                                                                        are
                                                                                        attached
                                                                                        to
                                                                                        this
                                                                                        sample
                                                                                        yet,
                                                                                        so
                                                                                        there
                                                                                        is
                                                                                        nothing
                                                                                        to
                                                                                        assign.
                                                                                    </div>

                                                                                    <div
                                                                                        v-else
                                                                                        class="space-y-3"
                                                                                    >
                                                                                        <nav
                                                                                            class="border-b border-gray-200 dark:border-slate-700"
                                                                                            aria-label="Spectrum groups"
                                                                                        >
                                                                                            <ul
                                                                                                role="tablist"
                                                                                                class="-mb-px flex flex-wrap gap-x-4 gap-y-1"
                                                                                            >
                                                                                                <li
                                                                                                    v-for="group in groupedAssignmentDatasets"
                                                                                                    :key="
                                                                                                        'tab-' +
                                                                                                        group.key
                                                                                                    "
                                                                                                >
                                                                                                    <button
                                                                                                        type="button"
                                                                                                        role="tab"
                                                                                                        :aria-selected="
                                                                                                            activeAssignmentGroup ===
                                                                                                            group.key
                                                                                                        "
                                                                                                        class="-mb-px inline-flex items-center gap-2 rounded-t-md border-b-2 px-3 py-2 text-sm font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                                                                                                        :class="
                                                                                                            activeAssignmentGroup ===
                                                                                                            group.key
                                                                                                                ? 'border-teal-600 text-gray-900 dark:border-teal-400 dark:text-slate-100'
                                                                                                                : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-slate-400 dark:hover:text-slate-200'
                                                                                                        "
                                                                                                        @click="
                                                                                                            activeAssignmentGroup =
                                                                                                                group.key
                                                                                                        "
                                                                                                    >
                                                                                                        {{
                                                                                                            group.label
                                                                                                        }}
                                                                                                        <span
                                                                                                            class="rounded-full bg-gray-100 px-1.5 py-0.5 text-[10px] font-semibold text-gray-600 dark:bg-slate-800 dark:text-slate-300"
                                                                                                            >{{
                                                                                                                group
                                                                                                                    .datasets
                                                                                                                    .length
                                                                                                            }}</span
                                                                                                        >
                                                                                                    </button>
                                                                                                </li>
                                                                                            </ul>
                                                                                        </nav>
                                                                                        <section
                                                                                            v-for="group in groupedAssignmentDatasets"
                                                                                            v-show="
                                                                                                activeAssignmentGroup ===
                                                                                                group.key
                                                                                            "
                                                                                            :key="
                                                                                                'panel-' +
                                                                                                group.key
                                                                                            "
                                                                                            role="tabpanel"
                                                                                        >
                                                                                            <div
                                                                                                class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-slate-900/60"
                                                                                            >
                                                                                                <div
                                                                                                    class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-4 py-2.5 dark:border-gray-800"
                                                                                                >
                                                                                                    <div
                                                                                                        class="flex min-w-0 items-center gap-2.5"
                                                                                                    >
                                                                                                        <span
                                                                                                            class="inline-block h-2 w-2 shrink-0 rounded-full"
                                                                                                            :class="
                                                                                                                groupHasAssignments(
                                                                                                                    group
                                                                                                                )
                                                                                                                    ? 'bg-emerald-500 ring-2 ring-emerald-500/20'
                                                                                                                    : 'bg-gray-200 dark:bg-slate-700'
                                                                                                            "
                                                                                                            :title="
                                                                                                                groupHasAssignments(
                                                                                                                    group
                                                                                                                )
                                                                                                                    ? 'Assignment saved'
                                                                                                                    : 'No assignment'
                                                                                                            "
                                                                                                            aria-hidden="true"
                                                                                                        ></span>
                                                                                                        <span
                                                                                                            class="truncate text-base font-semibold tracking-tight text-gray-900 dark:text-slate-100"
                                                                                                            >{{
                                                                                                                group.label
                                                                                                            }}</span
                                                                                                        >
                                                                                                        <span
                                                                                                            class="hidden truncate text-xs text-gray-500 dark:text-slate-400 sm:inline"
                                                                                                            >·
                                                                                                            applies
                                                                                                            to
                                                                                                            <template
                                                                                                                v-for="(
                                                                                                                    ds,
                                                                                                                    idx
                                                                                                                ) in group.datasets"
                                                                                                                :key="
                                                                                                                    'expno-' +
                                                                                                                    ds.id
                                                                                                                "
                                                                                                                ><span
                                                                                                                    v-if="
                                                                                                                        idx >
                                                                                                                        0
                                                                                                                    "
                                                                                                                    >, </span
                                                                                                                >{{
                                                                                                                    ds.type &&
                                                                                                                    ds.type.split(
                                                                                                                        " - "
                                                                                                                    )[1]
                                                                                                                        ? ds.type.split(
                                                                                                                              " - "
                                                                                                                          )[1] +
                                                                                                                          " (expno " +
                                                                                                                          ds.name +
                                                                                                                          ")"
                                                                                                                        : "expno " +
                                                                                                                          ds.name
                                                                                                                }}</template
                                                                                                            ></span
                                                                                                        >
                                                                                                    </div>
                                                                                                    <span
                                                                                                        v-if="
                                                                                                            groupAssignmentSavingKey ===
                                                                                                            group.key
                                                                                                        "
                                                                                                        class="text-xs italic text-gray-500 dark:text-slate-400"
                                                                                                        >Saving…</span
                                                                                                    >
                                                                                                    <span
                                                                                                        v-else-if="
                                                                                                            groupAssignmentSavedAt[
                                                                                                                group
                                                                                                                    .key
                                                                                                            ] &&
                                                                                                            !groupAssignmentErrors[
                                                                                                                group
                                                                                                                    .key
                                                                                                            ]
                                                                                                        "
                                                                                                        class="hidden text-xs text-gray-500 dark:text-slate-400 sm:inline"
                                                                                                        >Saved
                                                                                                        {{
                                                                                                            groupAssignmentSavedAt[
                                                                                                                group
                                                                                                                    .key
                                                                                                            ]
                                                                                                        }}</span
                                                                                                    >
                                                                                                </div>

                                                                                                <div
                                                                                                    class="px-4 py-3"
                                                                                                >
                                                                                                    <textarea
                                                                                                        v-model="
                                                                                                            groupAssignmentDraft[
                                                                                                                group
                                                                                                                    .key
                                                                                                            ]
                                                                                                        "
                                                                                                        rows="4"
                                                                                                        class="block w-full resize-y rounded-md border-gray-300 bg-white px-3 py-2 font-mono text-xs text-gray-900 shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-gray-600 dark:bg-slate-800 dark:text-slate-100"
                                                                                                        @blur="
                                                                                                            autosaveAssignmentsForGroup(
                                                                                                                group
                                                                                                            )
                                                                                                        "
                                                                                                    ></textarea>

                                                                                                    <p
                                                                                                        v-if="
                                                                                                            groupAssignmentErrors[
                                                                                                                group
                                                                                                                    .key
                                                                                                            ]
                                                                                                        "
                                                                                                        class="mt-2 text-xs text-red-600 dark:text-red-400"
                                                                                                    >
                                                                                                        {{
                                                                                                            groupAssignmentErrors[
                                                                                                                group
                                                                                                                    .key
                                                                                                            ]
                                                                                                        }}
                                                                                                    </p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </section>
                                                                                    </div>
                                                                                </div>
                                                                            </section>
                                                                        </div>

                                                                        <section
                                                                            class="mx-auto mt-3 max-w-7xl overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm ring-1 ring-gray-900/5 dark:border-gray-700 dark:bg-slate-800/90 dark:ring-white/5"
                                                                        >
                                                                            <button
                                                                                type="button"
                                                                                class="flex w-full items-center justify-between gap-3 border-b border-gray-100 px-3 py-3 text-left transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-500 dark:border-gray-700 dark:hover:bg-slate-800/80 sm:px-4"
                                                                                :aria-expanded="
                                                                                    sampleDetailsExpanded
                                                                                "
                                                                                aria-controls="sample-details-panel"
                                                                                @click="
                                                                                    sampleDetailsExpanded =
                                                                                        !sampleDetailsExpanded
                                                                                "
                                                                            >
                                                                                <div
                                                                                    class="min-w-0 flex-1"
                                                                                >
                                                                                    <h3
                                                                                        id="sample-details-heading"
                                                                                        class="text-lg font-semibold tracking-tight text-gray-900 dark:text-slate-100"
                                                                                    >
                                                                                        Sample
                                                                                        details
                                                                                    </h3>
                                                                                </div>
                                                                                <ChevronRightIcon
                                                                                    class="h-5 w-5 shrink-0 text-gray-400 transition-transform duration-200 dark:text-slate-500"
                                                                                    :class="{
                                                                                        'rotate-90':
                                                                                            sampleDetailsExpanded,
                                                                                    }"
                                                                                    aria-hidden="true"
                                                                                />
                                                                            </button>
                                                                            <div
                                                                                v-show="
                                                                                    sampleDetailsExpanded
                                                                                "
                                                                                id="sample-details-panel"
                                                                                class="px-2 pb-4 pt-2 sm:px-4"
                                                                                role="region"
                                                                                aria-labelledby="sample-details-heading"
                                                                            >
                                                                                <div
                                                                                    class="pt-2 xl:col-span-2"
                                                                                >
                                                                                    <div
                                                                                        class="mb-3"
                                                                                    >
                                                                                        <label
                                                                                            for="description"
                                                                                            class="block text-sm font-medium text-gray-700"
                                                                                        >
                                                                                            Sample
                                                                                            Description
                                                                                            <span
                                                                                                class="float-right rounded-full px-2"
                                                                                                @click="
                                                                                                    autoGenerateDescription()
                                                                                                "
                                                                                            >
                                                                                                Auto
                                                                                                generate
                                                                                            </span>
                                                                                        </label>
                                                                                        <div
                                                                                            class="mt-1"
                                                                                        >
                                                                                            <textarea
                                                                                                id="study-description"
                                                                                                v-model="
                                                                                                    studyForm.description
                                                                                                "
                                                                                                name="study-description"
                                                                                                rows="3"
                                                                                                class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border border-gray-300 rounded-md"
                                                                                                @blur="
                                                                                                    saveStudyDetails
                                                                                                "
                                                                                            ></textarea>
                                                                                            <jet-input-error
                                                                                                :message="
                                                                                                    studyForm
                                                                                                        .errors
                                                                                                        .description
                                                                                                "
                                                                                                class="mt-2"
                                                                                            />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        class="mb-3"
                                                                                    >
                                                                                        <label
                                                                                            for="description"
                                                                                            class="block text-sm font-medium text-gray-700"
                                                                                        >
                                                                                            Keywords
                                                                                        </label>
                                                                                        <div>
                                                                                            <vue-tags-input
                                                                                                v-model="
                                                                                                    studyForm.tag
                                                                                                "
                                                                                                placeholder="Type a keyword or keywords separated by comma (,) and press enter"
                                                                                                :separators="[
                                                                                                    ';',
                                                                                                    ',',
                                                                                                ]"
                                                                                                max-width="100%"
                                                                                                :tags="
                                                                                                    studyForm.tags
                                                                                                "
                                                                                                @tags-changed="
                                                                                                    updateTags
                                                                                                "
                                                                                            />
                                                                                            <jet-input-error
                                                                                                :message="
                                                                                                    studyForm
                                                                                                        .errors
                                                                                                        .tags
                                                                                                "
                                                                                                class="mt-2"
                                                                                            />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div
                                                                                    class="mb-3"
                                                                                >
                                                                                    <label
                                                                                        for="description"
                                                                                        class="block text-sm font-medium text-gray-700"
                                                                                    >
                                                                                        Organism
                                                                                        (Optional)
                                                                                    </label>

                                                                                    <div
                                                                                        class="mt-2 sm:flex sm:items-start sm:justify-between"
                                                                                    >
                                                                                        <div
                                                                                            class="text-sm text-gray-500 w-full"
                                                                                        >
                                                                                            <ontology-autocomplete
                                                                                                class="rounded-md"
                                                                                                format="text"
                                                                                                :value="
                                                                                                    studySpecies
                                                                                                "
                                                                                                placeholder="Search species"
                                                                                                @change="
                                                                                                    studySpecies =
                                                                                                        $event
                                                                                                            .detail[0]
                                                                                                "
                                                                                                @blur="
                                                                                                    updateSpecies(
                                                                                                        studySpecies
                                                                                                    )
                                                                                                "
                                                                                            ></ontology-autocomplete>
                                                                                        </div>
                                                                                        <div
                                                                                            class="mt-5 sm:ml-6 sm:mt-0 sm:flex sm:flex-shrink-0 sm:items-center"
                                                                                        >
                                                                                            <button
                                                                                                type="button"
                                                                                                class="inline-flex items-center gap-x-1.5 py-3 bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                                                                                @click="
                                                                                                    updateSpecies(
                                                                                                        studySpecies
                                                                                                    )
                                                                                                "
                                                                                            >
                                                                                                <svg
                                                                                                    class="-ml-0.5 h-5 w-5 text-gray-400"
                                                                                                    viewBox="0 0 20 20"
                                                                                                    fill="currentColor"
                                                                                                    aria-hidden="true"
                                                                                                >
                                                                                                    <path
                                                                                                        d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"
                                                                                                    ></path>
                                                                                                </svg>
                                                                                                Add
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        class="mt-2"
                                                                                    >
                                                                                        <div
                                                                                            v-for="(
                                                                                                species,
                                                                                                $index
                                                                                            ) in studyForm.species"
                                                                                            :key="
                                                                                                $index
                                                                                            "
                                                                                            class="bg-gray-100 text-gray-800 mb-0.5 inline-flex truncate break-words items-center px-3 py-2 rounded-full text-sm font-medium mr-1"
                                                                                        >
                                                                                            <ontology-term-annotation
                                                                                                :annotation="
                                                                                                    species
                                                                                                "
                                                                                            ></ontology-term-annotation>
                                                                                            <span
                                                                                                class="cursor-pointer"
                                                                                                @click="
                                                                                                    removeSpecies(
                                                                                                        $index
                                                                                                    )
                                                                                                "
                                                                                            >
                                                                                                <svg
                                                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                                                    viewBox="0 0 24 24"
                                                                                                    fill="currentColor"
                                                                                                    class="w-5 h-5 ml-2"
                                                                                                >
                                                                                                    <path
                                                                                                        fill-rule="evenodd"
                                                                                                        d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z"
                                                                                                        clip-rule="evenodd"
                                                                                                    />
                                                                                                </svg>
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </section>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        v-if="loader.kind === 'import'"
                                        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 px-4 py-8 backdrop-blur-sm dark:bg-slate-950/70"
                                        role="alertdialog"
                                        aria-modal="true"
                                        aria-busy="true"
                                        :aria-labelledby="
                                            loader.importMeta
                                                ? 'spectra-loading-title'
                                                : loader.bannerMessage
                                                ? 'spectra-loading-banner'
                                                : undefined
                                        "
                                    >
                                        <div
                                            class="w-full max-w-sm rounded-xl border border-gray-200/90 bg-white p-8 shadow-2xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/40"
                                        >
                                            <div
                                                class="flex flex-col items-center text-center"
                                            >
                                                <div
                                                    class="mb-5 flex h-12 w-12 items-center justify-center rounded-full bg-sky-50 dark:bg-sky-950/50"
                                                    aria-hidden="true"
                                                >
                                                    <svg
                                                        class="h-7 w-7 animate-spin text-sky-600 dark:text-sky-400"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <circle
                                                            class="opacity-25"
                                                            cx="12"
                                                            cy="12"
                                                            r="10"
                                                            stroke="currentColor"
                                                            stroke-width="4"
                                                        />
                                                        <path
                                                            class="opacity-90"
                                                            fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                        />
                                                    </svg>
                                                </div>

                                                <template
                                                    v-if="loader.importMeta"
                                                >
                                                    <h2
                                                        id="spectra-loading-title"
                                                        class="text-base font-semibold tracking-tight text-gray-900 dark:text-white"
                                                    >
                                                        Extracting spectra
                                                    </h2>
                                                    <p
                                                        class="mt-2 text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500"
                                                    >
                                                        Sample
                                                        {{
                                                            spectraImportStepCurrent
                                                        }}
                                                        of
                                                        {{
                                                            loader.importMeta
                                                                .total
                                                        }}
                                                    </p>
                                                    <div
                                                        class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800"
                                                    >
                                                        <div
                                                            class="h-full rounded-full bg-sky-600 transition-[width] duration-300 ease-out dark:bg-sky-500"
                                                            :style="{
                                                                width:
                                                                    spectraImportProgressPercent +
                                                                    '%',
                                                            }"
                                                        />
                                                    </div>
                                                    <p
                                                        class="mt-5 max-w-full truncate text-sm font-medium text-gray-800 dark:text-gray-200"
                                                        :title="
                                                            loader.importMeta
                                                                .sampleLabel
                                                        "
                                                    >
                                                        {{
                                                            loader.importMeta
                                                                .sampleLabel
                                                        }}
                                                    </p>
                                                    <p
                                                        class="mt-2 text-xs leading-relaxed text-gray-500 dark:text-gray-400"
                                                    >
                                                        This may take a moment.
                                                    </p>
                                                </template>
                                                <template
                                                    v-else-if="
                                                        loader.bannerMessage
                                                    "
                                                >
                                                    <p
                                                        id="spectra-loading-banner"
                                                        class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                                    >
                                                        {{
                                                            loader.bannerMessage
                                                        }}
                                                    </p>
                                                </template>

                                                <button
                                                    type="button"
                                                    class="mt-8 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 dark:border-slate-600 dark:bg-slate-800 dark:text-gray-200 dark:hover:bg-slate-700"
                                                    @click="showSamplesSummary"
                                                >
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="h-[calc(100vh-260px)] text-center py-12">
                <svg
                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-dark flex-inline inline"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                </svg>
                Loading...
            </div>
        </div>

        <!-- Processing Logs Modal -->
        <jet-dialog-modal
            :show="showLogsDialog"
            :max-width="'4xl'"
            @close="showLogsDialog = false"
        >
            <template #title>
                <div class="flex items-start gap-3">
                    <span
                        class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-200"
                        aria-hidden="true"
                    >
                        <InformationCircleIcon class="h-5 w-5" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <div
                            class="text-base font-semibold leading-6 text-gray-900"
                        >
                            Processing logs
                        </div>
                        <div
                            class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-gray-500"
                        >
                            <span
                                v-if="selectedDraftForLogs?.name"
                                class="truncate font-medium text-gray-700"
                                :title="selectedDraftForLogs.name"
                            >
                                {{ selectedDraftForLogs.name }}
                            </span>
                            <span
                                v-if="selectedDraftForLogs?.key"
                                aria-hidden="true"
                                >&middot;</span
                            >
                            <span
                                v-if="selectedDraftForLogs?.key"
                                class="font-mono text-[11px] text-gray-500"
                            >
                                ID:&nbsp;{{ selectedDraftForLogs.key }}
                            </span>
                            <span
                                v-if="
                                    selectedDraftForLogs?.processing_logs
                                        ?.length
                                "
                                aria-hidden="true"
                                >&middot;</span
                            >
                            <span
                                v-if="
                                    selectedDraftForLogs?.processing_logs
                                        ?.length
                                "
                                class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-700 ring-1 ring-inset ring-gray-200"
                            >
                                {{
                                    selectedDraftForLogs.processing_logs.length
                                }}
                                {{
                                    selectedDraftForLogs.processing_logs
                                        .length === 1
                                        ? "entry"
                                        : "entries"
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </template>

            <template #content>
                <div
                    class="relative -mx-1 mt-3 flex max-h-[70vh] flex-col overflow-hidden rounded-lg border border-gray-200 bg-white"
                >
                    <ol
                        v-if="
                            selectedDraftForLogs?.processing_logs &&
                            selectedDraftForLogs.processing_logs.length > 0
                        "
                        role="log"
                        aria-live="polite"
                        class="flex-1 divide-y divide-gray-100 overflow-y-auto"
                    >
                        <li
                            v-for="(
                                log, index
                            ) in selectedDraftForLogs.processing_logs"
                            :key="index"
                            class="group flex items-start gap-3 px-4 py-3 transition-colors hover:bg-gray-50/80"
                        >
                            <span
                                class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full"
                                :class="
                                    logLevelMeta(log.level).iconWrapperClass
                                "
                                aria-hidden="true"
                            >
                                <component
                                    :is="logLevelMeta(log.level).iconComponent"
                                    class="h-4 w-4"
                                    :class="logLevelMeta(log.level).iconClass"
                                />
                            </span>
                            <div class="min-w-0 flex-1">
                                <div
                                    class="flex flex-wrap items-center gap-x-2 gap-y-0.5"
                                >
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide"
                                        :class="
                                            logLevelMeta(log.level).badgeClass
                                        "
                                    >
                                        {{ logLevelMeta(log.level).label }}
                                    </span>
                                    <time
                                        v-if="log.timestamp"
                                        :datetime="log.timestamp"
                                        class="font-mono text-[11px] text-gray-500"
                                    >
                                        {{ formatDateTime(log.timestamp) }}
                                    </time>
                                </div>
                                <p
                                    class="mt-1 break-words text-sm leading-relaxed text-gray-900"
                                >
                                    {{ log.message }}
                                </p>
                                <details
                                    v-if="
                                        log.context &&
                                        Object.keys(log.context).length > 0
                                    "
                                    class="mt-2 text-xs text-gray-600"
                                >
                                    <summary
                                        class="inline-flex cursor-pointer select-none items-center gap-1 rounded text-gray-600 transition hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-400 focus-visible:ring-offset-1"
                                    >
                                        <span>Show details</span>
                                    </summary>
                                    <pre
                                        class="mt-2 max-h-64 overflow-auto whitespace-pre-wrap rounded-md border border-gray-200 bg-gray-50 p-3 font-mono text-[11px] leading-relaxed text-gray-800"
                                        >{{
                                            JSON.stringify(log.context, null, 2)
                                        }}</pre
                                    >
                                </details>
                            </div>
                        </li>
                    </ol>
                    <div
                        v-else
                        class="flex flex-col items-center justify-center px-6 py-16 text-center"
                    >
                        <span
                            class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-500 ring-1 ring-inset ring-gray-200"
                            aria-hidden="true"
                        >
                            <InformationCircleIcon class="h-6 w-6" />
                        </span>
                        <h3 class="text-sm font-semibold text-gray-900">
                            No processing logs yet
                        </h3>
                        <p
                            class="mt-1 max-w-xs text-xs leading-relaxed text-gray-500"
                        >
                            Logs will appear here as the draft is validated and
                            processed.
                        </p>
                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click="showLogsDialog = false">
                    Close
                </jet-secondary-button>
            </template>
        </jet-dialog-modal>

        <!-- Processing overlay: blocks all interaction while samples are being processed. -->
        <div
            v-if="showProcessingOverlay"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 px-4 py-8 backdrop-blur-sm"
            role="alertdialog"
            aria-modal="true"
            aria-labelledby="processing-overlay-title"
            aria-describedby="processing-overlay-description"
            @keydown.stop
            @click.stop
            @wheel.stop
        >
            <div
                class="w-full max-w-md overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl ring-1 ring-gray-900/5"
            >
                <div class="px-6 py-7 sm:px-8">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-teal-50"
                            aria-hidden="true"
                        >
                            <svg
                                class="h-6 w-6 animate-spin text-teal-600"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2
                                id="processing-overlay-title"
                                class="text-base font-semibold text-gray-900"
                            >
                                Processing uploaded data
                            </h2>
                            <p
                                id="processing-overlay-description"
                                class="mt-1.5 text-sm leading-relaxed text-gray-600"
                            >
                                Please wait while your samples are being
                                processed. This may take a few minutes.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div
                            class="flex items-baseline justify-between text-sm"
                        >
                            <span class="font-medium text-gray-700">
                                Processed
                            </span>
                            <span class="font-mono tabular-nums text-gray-900">
                                {{ processedStudiesCount }} /
                                {{ totalStudiesCount }} samples
                            </span>
                        </div>
                        <div
                            class="mt-2 h-2 w-full overflow-hidden rounded-full bg-gray-100"
                            role="progressbar"
                            :aria-valuenow="processingProgressPercent"
                            aria-valuemin="0"
                            aria-valuemax="100"
                        >
                            <div
                                class="h-full rounded-full bg-teal-500 transition-all duration-500 ease-out"
                                :style="{
                                    width: processingProgressPercent + '%',
                                }"
                            ></div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="isProcessingStuck"
                    class="border-t border-amber-100 bg-amber-50/80 px-6 py-5 sm:px-8"
                >
                    <div class="flex items-start gap-3">
                        <ExclamationCircleIcon
                            class="mt-0.5 h-5 w-5 shrink-0 text-amber-600"
                            aria-hidden="true"
                        />
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-amber-900">
                                Taking longer than expected?
                            </p>
                            <p
                                class="mt-1 text-sm leading-relaxed text-amber-800"
                            >
                                Processing usually completes within a few
                                minutes. If it still seems stuck, please reach
                                out and we'll take a look.
                            </p>
                            <a
                                class="mt-3 inline-flex items-center gap-1.5 rounded-md bg-amber-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2"
                                :href="contactSupportHref"
                            >
                                Contact support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <MixtureCompositionHelpModal
            :show="showCompositionHelpModal"
            @close="showCompositionHelpModal = false"
        />
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { router, Link } from "@inertiajs/vue3";
import JetInputError from "@/Jetstream/InputError.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import { markRaw, ref } from "vue";
import Primer from "@/Shared/Primer.vue";
import FileSystemBrowser from "./../Shared/FileSystemBrowser.vue";
import Validation from "@/Shared/Validation.vue";
import SearchInput from "@/Shared/SearchInput.vue";
import EmptySearchState from "@/Shared/EmptySearchState.vue";
import DraftStatusBadge from "@/Shared/DraftStatusBadge.vue";
import {
    TrashIcon,
    PencilIcon,
    EyeIcon,
    EyeSlashIcon,
    ChevronDoubleLeftIcon,
    ChevronDoubleRightIcon,
    InformationCircleIcon,
    CheckIcon,
    ExclamationCircleIcon,
} from "@heroicons/vue/24/solid";
import {
    ArrowLeftIcon,
    ClipboardDocumentIcon,
    ChevronRightIcon,
    ChevronDownIcon,
    ArrowsPointingOutIcon,
    ArrowsPointingInIcon,
} from "@heroicons/vue/24/outline";
import SpectraEditor from "@/Shared/SpectraEditor.vue";
import ChemicalCompositionEditor from "@/Shared/ChemicalCompositionEditor.vue";
import HifsaPanel from "@/Shared/HifsaPanel.vue";
import Depictor from "@/Shared/Depictor.vue";
import Depictor2D from "@/Shared/Depictor2D.vue";
import slider from "vue3-slider";
import VueTagsInput from "@sipec/vue3-tags-input";
import "@/lib/ontology-elements";
import Global from "@/Mixins/Global.js";
import OCL from "openchemlib";
import {
    detectStructureInputFormat,
    detectStructureInputType,
    editorHasStructureContent,
    resolveStructureForEditorWithStandardize,
} from "@/Utils/structureImport";
import { createStructureEditor } from "@/Utils/structureEditor";
import MixtureCompositionForm from "@/Shared/MixtureCompositionForm.vue";
import MixtureCompositionHelpModal from "@/Shared/MixtureCompositionHelpModal.vue";
import {
    applySampleMoleculeResponse,
    basisUnitLabel,
    formatMixtureValue,
} from "@/Utils/mixtureComposition";
import { studyHasHifsa } from "@/Utils/hifsaNmriumFileFilter.js";

export default {
    components: {
        AppLayout,
        Link,
        JetInputError,
        JetSecondaryButton,
        JetButton,
        JetDialogModal,
        Primer,
        FileSystemBrowser,
        SearchInput,
        EmptySearchState,
        DraftStatusBadge,
        TrashIcon,
        PencilIcon,
        EyeIcon,
        EyeSlashIcon,
        Validation,
        SpectraEditor,
        ChemicalCompositionEditor,
        HifsaPanel,
        Depictor,
        Depictor2D,
        slider,
        VueTagsInput,
        ChevronDoubleLeftIcon,
        ChevronDoubleRightIcon,
        InformationCircleIcon,
        CheckIcon,
        ExclamationCircleIcon,
        ArrowLeftIcon,
        ClipboardDocumentIcon,
        ChevronRightIcon,
        ChevronDownIcon,
        ArrowsPointingOutIcon,
        ArrowsPointingInIcon,
        MixtureCompositionForm,
        MixtureCompositionHelpModal,
    },
    mixins: [Global],
    props: ["draft_id", "deposition"],
    setup() {
        return {};
    },
    data() {
        return {
            returnUrl: "/dashboard",

            loading: false,
            loadingStep: false,
            /** Single source for upload overlays: import = centered modal; iframe = thin bar over NMRium. */
            loader: {
                kind: "idle",
                importMeta: null,
                bannerMessage: null,
                iframe: null,
            },
            studySaving: false,
            checkStudyStatusTimerId: null,
            showCompoundDetails: true,
            hideDownArrow: false,

            chemicalCompositionExpanded: false,

            assignmentsExpanded: false,
            assignmentsDraft: {},
            assignmentsSavedAt: {},
            assignmentsErrors: {},
            assignmentsSavingId: null,
            activeAssignmentGroup: null,
            groupAssignmentDraft: {},
            groupAssignmentSavedAt: {},
            groupAssignmentErrors: {},
            groupAssignmentSavingKey: null,

            sampleDetailsExpanded: false,

            currentDraft: null,
            drafts: [],
            searchDraftQuery: "",
            currentDraftsPage: 1,
            draftsPerPage: 15,
            searchDebounceTimer: null,

            draftForm: this.$inertia.form({
                _method: "POST",
                name: "",
                description: "",
                error_message: null,
                tags: [],
                tag: "",
                tags_array: [],
                owner_id: null,
            }),

            project: null,
            studies: null,
            studiesToImport: [],
            processingWarnings: [],
            studySpecies: null,
            inprogressStudies: [],
            /**
             * Tracks whether processing is taking longer than expected so we
             * can surface a "Contact support" affordance inside the
             * processing overlay.
             */
            isProcessingStuck: false,
            processingStuckTimer: null,
            processingStuckThresholdMs: 90 * 1000,
            selectedStudy: null,
            hifsaExpanded: true,
            studyNameDraft: "",

            /**
             * Map of study.id -> boolean indicating whether the dataset chips
             * for that sample are expanded in the sidebar. Empty by default,
             * so every sample's dataset list starts collapsed.
             */
            expandedStudyIds: {},

            studyForm: this.$inertia.form({
                _method: "POST",
                name: "",
                description: "",
                error_message: null,
                tags: [],
                species: [],
                tag: "",
                tags_array: [],
            }),

            displaySamplesSummaryInfo: true,
            querySample: null,

            validation: null,
            validationStatus: true,

            isDragging: false,
            structureLoading: false,
            structureLoadCounter: 0,
            percentage: 99.99,
            /** 'pure' | 'mixture' | 'unknown' — unknown stores no pivot percentage */
            compositionSampleType: "pure",
            mixtureDraft: {
                value: null,
                integrated_signal: null,
                n_nuclei: null,
            },
            editor: null,
            editorHasStructure: false,

            showPrimer: false,
            busy: false,

            steps: [
                {
                    id: "1",
                    step: "tour-step-submission-header",
                    name: "Files Upload",
                    description: "Vitae sed mi luctus laoreet.",
                    href: "#",
                    status: "upcoming",
                },
                {
                    id: "2",
                    step: "v-step-20",
                    name: "Assignments & Metadata",
                    description: "Cursus semper viverra.",
                    href: "#",
                    status: "upcoming",
                },
                {
                    id: "3",
                    name: "Complete ~ Community Standards",
                    description: "Penatibus eu quis ante.",
                    href: "#",
                    status: "upcoming",
                },
            ],

            step: 1,

            errorMessage: null,
            filesErrorMessage: null,

            createDatasetForm: this.$inertia.form({
                _method: "POST",
                name: "",
                description: "",
                error_message: null,
                team_id: null,
                owner_id: null,
                color: null,
                starred: null,
                project_id: this.project ? this.project.id : null,
                is_public: ref(false),
            }),
            showSummary: true,
            showLogsDialog: false,
            showCompositionHelpModal: false,
            selectedDraftForLogs: null,

            needsReservedDoi: false,
            doiCopySucceeded: false,
            doiCopyResetTimer: null,
            provisionalDoiLoading: false,
            provisionalDoiError: null,

            /** Populated from parallel GET /info during mounted deep-link (step 2). */
            prefetchedStepTwoPayload: null,
            /** Avoid duplicate nmredata-driven standardize bursts per study. */
            autoImportMolecularDataStartedFor: markRaw(new Set()),
            /** Per-session molfile -> standardize axios promise. */
            standardizeRequestCache: markRaw(new Map()),
        };
    },
    computed: {
        /**
         * Whether the full-screen processing overlay should be shown.
         * Surfaced while we're on step 2 and at least one study is still
         * being processed.
         */
        showProcessingOverlay() {
            return (
                Array.isArray(this.inprogressStudies) &&
                this.inprogressStudies.length > 0 &&
                this.currentStep &&
                this.currentStep.id == "2"
            );
        },
        totalStudiesCount() {
            return Array.isArray(this.studies) ? this.studies.length : 0;
        },
        processedStudiesCount() {
            const total = this.totalStudiesCount;
            const inProgress = Array.isArray(this.inprogressStudies)
                ? this.inprogressStudies.length
                : 0;
            return Math.max(0, total - inProgress);
        },
        processingProgressPercent() {
            const total = this.totalStudiesCount;
            if (!total) {
                return 0;
            }
            return Math.round((this.processedStudiesCount / total) * 100);
        },
        contactSupportHref() {
            const draftRef = this.currentDraft
                ? ` (Draft #${this.currentDraft.id}${
                      this.currentDraft.name
                          ? ` – ${this.currentDraft.name}`
                          : ""
                  })`
                : "";
            const subject = encodeURIComponent(
                `Submission processing stuck${draftRef}`
            );
            return `mailto:info.nmrxiv@uni-jena.de?subject=${subject}`;
        },
        /**
         * Studies that actually have datasets — used to drive the
         * "Expand all / Collapse all" toggle in the sidebar.
         */
        studiesWithDatasets() {
            return (this.studies || []).filter(
                (s) => s && s.datasets && s.datasets.length > 0
            );
        },
        allStudyDatasetsExpanded() {
            const eligible = this.studiesWithDatasets;
            if (eligible.length === 0) {
                return false;
            }
            return eligible.every((s) => Boolean(this.expandedStudyIds[s.id]));
        },
        filteredDrafts() {
            let drafts = this.drafts;

            if (this.deposition === "publication") {
                drafts = drafts.filter((d) => !this.isCommunityDraft(d));
            }

            if (!this.searchDraftQuery) {
                return drafts;
            }
            const q = this.searchDraftQuery.toLowerCase().trim();
            return drafts.filter((d) => {
                const name = (d.name || "").toLowerCase();
                const desc = (d.description || "").toLowerCase();
                const idText = String(d.id || "").toLowerCase();
                const keyText = String(d.key || "").toLowerCase();
                return (
                    name.includes(q) ||
                    desc.includes(q) ||
                    idText.includes(q) ||
                    keyText.includes(q)
                );
            });
        },
        paginatedDrafts() {
            const start = (this.currentDraftsPage - 1) * this.draftsPerPage;
            return this.filteredDrafts.slice(start, start + this.draftsPerPage);
        },
        totalDraftPages() {
            return Math.max(
                1,
                Math.ceil(this.filteredDrafts.length / this.draftsPerPage)
            );
        },
        currentStep() {
            return this.steps.filter((s) => s.status == "current")[0];
        },
        reservedDoiPreview() {
            if (!this.currentDraft?.id) {
                return "https://doi.org/10.5281/nmrxiv.preview.pending";
            }

            return `https://doi.org/10.5281/nmrxiv.preview.draft-${this.currentDraft.id}`;
        },
        reservedDoiDisplayUrl() {
            if (this.project?.provisional_doi_url) {
                return this.project.provisional_doi_url;
            }
            if (this.project?.provisional_doi) {
                const host = "https://doi.org".replace(/\/$/, "");
                return `${host}/${this.project.provisional_doi}`;
            }
            return this.reservedDoiPreview;
        },
        primed() {
            return this.$page.props.auth.user?.primed;
        },
        chemistryStandardizeUrl() {
            return this.$page.props.chemistryStandardizeUrl;
        },
        importPendingSamples() {
            return this.studiesToImport.filter((f) => f.status == false);
        },
        spectraCount() {
            let i = 0;
            this.studies.forEach((s) => {
                i = i + s.datasets.length;
            });
            return i;
        },
        moleculesCount() {
            let i = 0;
            this.studies.forEach((s) => {
                i = i + s.sample.molecules.length;
            });
            return i;
        },
        /** Molecules listed for the currently selected sample (composition header badge). */
        selectedStudyMoleculeCount() {
            return this.selectedStudy?.sample?.molecules?.length ?? 0;
        },
        /** Pure sample mode only applies before any compound is listed for this sample */
        compositionPureSampleDisabled() {
            const n = this.selectedStudy?.sample?.molecules?.length ?? 0;
            return n > 0;
        },
        getMax() {
            if (!this.selectedStudy) {
                return 100;
            }
            let total = 0;
            this.selectedStudy.sample.molecules.forEach((mol) => {
                const v = parseFloat(mol.pivot?.percentage_composition);
                total += Number.isFinite(v) ? v : 0;
            });
            const remaining = 100 - total;
            const epsilon = 1e-5;
            if (remaining <= epsilon) {
                return 0;
            }

            return remaining;
        },
        compositionSliderMax() {
            const m = this.getMax;

            return m < 0 ? 0 : m;
        },
        mixtureBasisUnitLabel() {
            return basisUnitLabel(
                this.selectedStudy?.sample?.mixture_composition?.basis ??
                    this.$refs.mixtureCompositionForm?.localBasis ??
                    "mole_percent"
            );
        },
        canAddMixtureComponent() {
            return (
                this.$refs.mixtureCompositionForm?.canAddComponent?.() ?? false
            );
        },
        spectraImportStepCurrent() {
            const m = this.loader.importMeta;
            if (!m || !m.total) {
                return 0;
            }
            return Math.min(m.completedCount + 1, m.total);
        },
        spectraImportProgressPercent() {
            const m = this.loader.importMeta;
            if (!m || !m.total) {
                return 0;
            }
            return Math.min(
                100,
                Math.round(((m.completedCount + 1) / m.total) * 100)
            );
        },
        /**
         * Group the selected sample's datasets by nucleus channel(s) the way
         * NMRium organises its experiment tabs:
         *
         *   1) homonuclear 1D, sorted by nucleus (1H, 13C, 19F, 31P, …),
         *   2) homonuclear 2D (1H-1H, 13C-13C, …),
         *   3) heteronuclear 2D (1H-13C, 1H-15N, …),
         *   4) anything we couldn't classify (legacy "1D NMR" / null types).
         *
         * Within a group, datasets keep their natural order (numeric expno
         * first, then alphabetical for jdx-style names).
         */
        groupedAssignmentDatasets() {
            const datasets =
                (this.selectedStudy && this.selectedStudy.datasets) || [];
            if (!datasets.length) {
                return [];
            }

            const groups = new Map();
            datasets.forEach((ds) => {
                const type = (ds && ds.type) || "";
                const head = type.split(" - ")[0].trim();
                const key = head || "Other";
                if (!groups.has(key)) {
                    groups.set(key, { key, label: key, datasets: [] });
                }
                groups.get(key).datasets.push(ds);
            });

            const nucleusRank = (nuc) => {
                const order = ["1H", "13C", "19F", "31P", "15N", "29Si", "11B"];
                const idx = order.indexOf(nuc);
                return idx === -1 ? order.length + nuc.charCodeAt(0) : idx;
            };

            const tier = (key) => {
                if (key === "Other") {
                    return [9, 0, 0];
                }
                const m = key.match(/^(.+?)\s*NMR$/);
                if (!m) {
                    return [8, 0, 0];
                }
                const channels = m[1].split("-");
                if (channels.length === 1) {
                    return [1, nucleusRank(channels[0]), 0];
                }
                const a = nucleusRank(channels[0]);
                const b = nucleusRank(channels[1]);
                return [channels[0] === channels[1] ? 2 : 3, a, b];
            };

            return Array.from(groups.values()).sort((x, y) => {
                const tx = tier(x.key);
                const ty = tier(y.key);
                for (let i = 0; i < tx.length; i++) {
                    if (tx[i] !== ty[i]) {
                        return tx[i] - ty[i];
                    }
                }
                return x.key.localeCompare(y.key);
            });
        },
    },
    watch: {
        showProcessingOverlay: {
            immediate: true,
            handler(active) {
                if (active) {
                    this.startProcessingStuckTimer();
                } else {
                    this.clearProcessingStuckTimer();
                    this.isProcessingStuck = false;
                }
            },
        },
        processedStudiesCount(newCount, oldCount) {
            if (this.showProcessingOverlay && newCount > (oldCount || 0)) {
                this.startProcessingStuckTimer();
            }
        },
        groupedAssignmentDatasets: {
            immediate: true,
            handler(groups) {
                if (!Array.isArray(groups) || groups.length === 0) {
                    this.activeAssignmentGroup = null;
                    this.groupAssignmentDraft = {};
                    return;
                }
                const stillExists = groups.some(
                    (g) => g.key === this.activeAssignmentGroup
                );
                if (!stillExists) {
                    this.activeAssignmentGroup = groups[0].key;
                }
                this.seedGroupAssignmentDraft(groups);
            },
        },
        searchDraftQuery() {
            // Debounce the search query to avoid excessive pagination resets
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }
            this.searchDebounceTimer = setTimeout(() => {
                this.currentDraftsPage = 1;
            }, 300); // 300ms debounce delay
        },
        getMax(newMax) {
            if (this.compositionSampleType === "pure") {
                const capped = Math.min(99.99, newMax);
                if (this.percentage !== capped) {
                    this.percentage = capped;
                }
                return;
            }
            if (this.compositionSampleType === "unknown") {
                return;
            }
            if (this.percentage > newMax) {
                this.percentage = newMax;
            }
        },
        compositionSampleType(mode) {
            if (mode === "pure") {
                this.percentage = Math.min(99.99, this.compositionSliderMax);
            } else if (mode === "mixture") {
                const max = this.compositionSliderMax;
                if (this.percentage > max) {
                    this.percentage = max;
                }
            }
        },
        compositionPureSampleDisabled(disabled) {
            if (disabled && this.compositionSampleType === "pure") {
                this.compositionSampleType = "mixture";
            }
        },
        chemicalCompositionExpanded(isOpen) {
            if (isOpen) {
                this.ensureStructureSearchEditor();
            }
        },
    },
    mounted() {
        const urlSearchParams = new URLSearchParams(window.location.search);
        const params = Object.fromEntries(urlSearchParams.entries());
        this.step = params["step"] ? params["step"] : "1";
        this.querySample = params["sample"] ? params["sample"] : null;

        this.uploadUrlPopstateHandler = () => {
            this.handleUploadUrlChange();
        };
        window.addEventListener("popstate", this.uploadUrlPopstateHandler);

        this.fetchDrafts().then((response) => {
            this.drafts = response.data.drafts;
            this.sharedDrafts = response.data.sharedDrafts;
            if (this.draft_id) {
                if (this.draft_id != "null") {
                    let selectedDraft = this.drafts.find(
                        (d) => d.id == this.draft_id
                    );
                    if (!selectedDraft) {
                        selectedDraft = this.sharedDrafts.find(
                            (d) => d.id == this.draft_id
                        );
                    }
                    if (
                        !selectedDraft &&
                        response.data.default.id == this.draft_id
                    ) {
                        selectedDraft = response.data.default;
                    }
                    const bootstrapDraft = (draft) => {
                        this.selectDraft(draft);
                        this.loading = false;
                    };
                    if (selectedDraft) {
                        if (
                            this.step === "2" &&
                            String(this.draft_id) === String(selectedDraft.id)
                        ) {
                            Promise.all([
                                axios
                                    .get(
                                        "/dashboard/drafts/" +
                                            this.draft_id +
                                            "/show"
                                    )
                                    .catch(() => null),
                                axios
                                    .get(
                                        "/dashboard/drafts/" +
                                            this.draft_id +
                                            "/info"
                                    )
                                    .catch(() => null),
                            ])
                                .then(([showRes, infoRes]) => {
                                    let draftToUse = selectedDraft;
                                    if (showRes?.data?.draft) {
                                        draftToUse = showRes.data.draft;
                                    }
                                    const p = infoRes?.data?.project;
                                    const studiesList = infoRes?.data?.studies;
                                    if (
                                        p &&
                                        studiesList &&
                                        studiesList.length > 0
                                    ) {
                                        this.prefetchedStepTwoPayload =
                                            infoRes.data;
                                    }
                                    bootstrapDraft(draftToUse);
                                })
                                .catch(() => {
                                    bootstrapDraft(selectedDraft);
                                });
                        } else {
                            bootstrapDraft(selectedDraft);
                        }
                    } else {
                        const parallel =
                            this.step === "2"
                                ? Promise.all([
                                      this.fetchDraftById(this.draft_id),
                                      axios.get(
                                          "/dashboard/drafts/" +
                                              this.draft_id +
                                              "/info"
                                      ),
                                  ])
                                : this.fetchDraftById(this.draft_id).then(
                                      (r) => [r, null]
                                  );

                        parallel
                            .then(([draftResponse, infoRes]) => {
                                if (
                                    infoRes?.data?.project &&
                                    infoRes.data.studies?.length > 0
                                ) {
                                    this.prefetchedStepTwoPayload =
                                        infoRes.data;
                                }
                                bootstrapDraft(draftResponse.data.draft);
                            })
                            .catch(() => {
                                this.loading = false;
                            });
                    }
                } else {
                    alert(
                        "Could not find the draft. Redirecting to the upload page."
                    );
                    return router.visit("upload");
                }
            } else {
                this.defaultDraft = response.data.default;
                if (this.drafts.length == 0) {
                    this.selectDraft(this.defaultDraft);
                    this.loading = false;
                } else {
                    this.loading = false;
                }
            }
        });
        this.showPrimer = !this.primed;

        let localItems = this.findLocalItems("show_compound_details");
        if (localItems.length > 0) {
            this.showCompoundDetails = JSON.parse(localItems[0].val);
        }
    },
    beforeUnmount() {
        // Clean up the search debounce timer to prevent memory leaks
        if (this.searchDebounceTimer) {
            clearTimeout(this.searchDebounceTimer);
        }
        if (this.doiCopyResetTimer) {
            clearTimeout(this.doiCopyResetTimer);
        }
        if (this.checkStudyStatusTimerId != null) {
            clearTimeout(this.checkStudyStatusTimerId);
            this.checkStudyStatusTimerId = null;
        }
        if (this.uploadUrlPopstateHandler) {
            window.removeEventListener(
                "popstate",
                this.uploadUrlPopstateHandler
            );
            this.uploadUrlPopstateHandler = null;
        }
        this.clearProcessingStuckTimer();
    },
    methods: {
        startProcessingStuckTimer() {
            this.clearProcessingStuckTimer();
            this.isProcessingStuck = false;
            this.processingStuckTimer = setTimeout(() => {
                this.isProcessingStuck = true;
            }, this.processingStuckThresholdMs);
        },
        clearProcessingStuckTimer() {
            if (this.processingStuckTimer) {
                clearTimeout(this.processingStuckTimer);
                this.processingStuckTimer = null;
            }
        },
        focusDraftName() {
            this.$nextTick(() => {
                const editor = this.$refs.draftNameEditor;
                if (!editor || typeof editor.focus !== "function") {
                    return;
                }
                editor.focus();
            });
        },
        async copyReservedDoiToClipboard() {
            try {
                await navigator.clipboard.writeText(this.reservedDoiDisplayUrl);
                this.doiCopySucceeded = true;
                if (this.doiCopyResetTimer) {
                    clearTimeout(this.doiCopyResetTimer);
                }
                this.doiCopyResetTimer = setTimeout(() => {
                    this.doiCopySucceeded = false;
                    this.doiCopyResetTimer = null;
                }, 2500);
            } catch {
                this.doiCopySucceeded = false;
            }
        },
        onScroll() {
            this.hideDownArrow = true;
        },
        showProcessingLogs(draft) {
            this.selectedDraftForLogs = draft;
            this.showLogsDialog = true;
        },

        /**
         * Visual metadata for a processing-log level (icon component + color tokens).
         *
         * @param {string|null|undefined} rawLevel
         * @returns {{ key: string, label: string, iconComponent: string, iconWrapperClass: string, iconClass: string, badgeClass: string }}
         */
        logLevelMeta(rawLevel) {
            const key = String(rawLevel || "info").toLowerCase();

            const presets = {
                error: {
                    label: "Error",
                    iconComponent: "ExclamationCircleIcon",
                    iconWrapperClass:
                        "bg-red-50 ring-1 ring-inset ring-red-200",
                    iconClass: "text-red-600",
                    badgeClass:
                        "bg-red-50 text-red-700 ring-1 ring-inset ring-red-200",
                },
                warning: {
                    label: "Warning",
                    iconComponent: "InformationCircleIcon",
                    iconWrapperClass:
                        "bg-amber-50 ring-1 ring-inset ring-amber-200",
                    iconClass: "text-amber-600",
                    badgeClass:
                        "bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-200",
                },
                warn: {
                    label: "Warning",
                    iconComponent: "InformationCircleIcon",
                    iconWrapperClass:
                        "bg-amber-50 ring-1 ring-inset ring-amber-200",
                    iconClass: "text-amber-600",
                    badgeClass:
                        "bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-200",
                },
                info: {
                    label: "Info",
                    iconComponent: "InformationCircleIcon",
                    iconWrapperClass:
                        "bg-sky-50 ring-1 ring-inset ring-sky-200",
                    iconClass: "text-sky-600",
                    badgeClass:
                        "bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-200",
                },
                success: {
                    label: "Success",
                    iconComponent: "CheckIcon",
                    iconWrapperClass:
                        "bg-emerald-50 ring-1 ring-inset ring-emerald-200",
                    iconClass: "text-emerald-600",
                    badgeClass:
                        "bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200",
                },
            };

            return {
                key,
                ...(presets[key] ?? presets.info),
            };
        },
        toggleCompoundDetails() {
            this.showCompoundDetails = !this.showCompoundDetails;
            localStorage.setItem(
                "show_compound_details",
                this.showCompoundDetails
            );
        },
        isStudyDatasetsExpanded(study) {
            if (!study) {
                return false;
            }
            return Boolean(this.expandedStudyIds[study.id]);
        },
        toggleStudyDatasets(study) {
            if (!study) {
                return;
            }
            this.expandedStudyIds = {
                ...this.expandedStudyIds,
                [study.id]: !this.expandedStudyIds[study.id],
            };
        },
        expandAllStudyDatasets() {
            const next = {};
            (this.studies || []).forEach((study) => {
                if (study && study.datasets && study.datasets.length > 0) {
                    next[study.id] = true;
                }
            });
            this.expandedStudyIds = next;
        },
        collapseAllStudyDatasets() {
            this.expandedStudyIds = {};
        },
        cancelStudyNameEdit() {
            this.studyNameDraft = this.selectedStudy?.name || "";
            this.$refs.studyNameInput?.blur?.();
        },
        focusStudyName() {
            if (!this.selectedStudy || this.busy) {
                return;
            }
            const input = this.$refs.studyNameInput;
            if (input && typeof input.focus === "function") {
                input.focus();
                input.select?.();
            }
        },
        saveStudyNameEdit() {
            if (!this.selectedStudy || this.busy) {
                return;
            }

            const nextName = (this.studyNameDraft || "")
                .trim()
                .replace(/\n+/g, " ");

            if (!nextName) {
                this.studyNameDraft = this.selectedStudy.name || "";

                return;
            }

            if (nextName === this.selectedStudy.name) {
                this.studyNameDraft = nextName;

                return;
            }

            this.studyNameDraft = nextName;
            this.studyForm.name = nextName;
            this.selectedStudy.name = nextName;
            this.saveStudyDetails();
        },
        showSamplesSummary() {
            this.resetLoader();
            this.displaySamplesSummaryInfo = true;
            this.selectedStudy = null;
            this.setQueryStringParameter("sample", null);
        },
        fetchDrafts() {
            this.loading = true;

            const params = {};

            if (this.deposition) {
                params.deposition = this.deposition;
            }

            return axios.get("/dashboard/drafts", { params });
        },
        isCommunityDraft(draft) {
            return (
                draft?.settings?.deposition_type === "community" ||
                (draft?.name || "").startsWith("Community Contribution (Draft:")
            );
        },
        fetchDraftById(draftId) {
            return axios.get("/dashboard/drafts/" + draftId + "/show");
        },
        syncNeedsReservedFromProject() {
            this.needsReservedDoi = !!(
                this.project && this.project.provisional_doi
            );
        },
        hydrateProvisionalDoiFromDraftInfo() {
            if (!this.currentDraft?.id) {
                return Promise.resolve();
            }
            return axios
                .get("/dashboard/drafts/" + this.currentDraft.id + "/info")
                .then((response) => {
                    const p = response.data.project;
                    if (!p) {
                        this.needsReservedDoi = false;
                        return;
                    }
                    if (this.project && this.project.id === p.id) {
                        this.project = {
                            ...this.project,
                            provisional_doi: p.provisional_doi ?? null,
                            provisional_doi_url: p.provisional_doi_url ?? null,
                        };
                    } else {
                        this.project = p;
                    }
                    this.syncNeedsReservedFromProject();
                })
                .catch(() => {});
        },
        applyProvisionalPayloadToProject(payload) {
            if (!payload?.provisional_doi) {
                return;
            }
            this.project = {
                ...(this.project || {}),
                provisional_doi: payload.provisional_doi,
                provisional_doi_url: payload.url ?? null,
            };
            this.syncNeedsReservedFromProject();
        },
        stripProvisionalFromProject() {
            if (!this.project) {
                return;
            }
            const next = { ...this.project };
            delete next.provisional_doi;
            next.provisional_doi_url = null;
            this.project = next;
            this.syncNeedsReservedFromProject();
        },
        async onReservedDoiSwitch(wantsOn) {
            if (this.provisionalDoiLoading || !this.currentDraft?.id) {
                return;
            }
            this.provisionalDoiError = null;
            this.provisionalDoiLoading = true;
            const prev = this.needsReservedDoi;
            this.needsReservedDoi = wantsOn;
            try {
                if (wantsOn) {
                    const { data } = await axios.post(
                        "/dashboard/drafts/" +
                            this.currentDraft.id +
                            "/provisional-doi"
                    );
                    this.applyProvisionalPayloadToProject(data);
                    await this.hydrateProvisionalDoiFromDraftInfo();
                } else {
                    await axios.delete(
                        "/dashboard/drafts/" +
                            this.currentDraft.id +
                            "/provisional-doi"
                    );
                    this.stripProvisionalFromProject();
                }
            } catch (e) {
                this.needsReservedDoi = prev;
                const msg =
                    e.response?.data?.message ||
                    (Array.isArray(e.response?.data?.errors?.name)
                        ? e.response.data.errors.name[0]
                        : null) ||
                    "Could not update the provisional DOI. Please try again.";
                this.provisionalDoiError = msg;
            } finally {
                this.provisionalDoiLoading = false;
            }
        },
        selectDraft(draft) {
            if (
                this.deposition === "publication" &&
                this.isCommunityDraft(draft)
            ) {
                return;
            }

            this.needsReservedDoi = false;
            this.doiCopySucceeded = false;
            this.provisionalDoiError = null;
            if (this.doiCopyResetTimer) {
                clearTimeout(this.doiCopyResetTimer);
                this.doiCopyResetTimer = null;
            }
            this.currentDraft = draft;
            this.draftForm.name = this.currentDraft.name;
            this.draftForm.description = this.currentDraft.description;
            let tags = [];
            this.file = null;
            this.draftForm.tags = [];
            this.draftForm.owner_id = this.$page.props.auth.user?.id;
            if (this.currentDraft.tags) {
                this.currentDraft.tags.forEach((t) => {
                    tags.push({
                        text: t.name["en"],
                    });
                });
                this.draftForm.tags = tags;
            }
            this.$nextTick(() => {
                if (this.step == "2") {
                    this.showPrimer = false;
                    this.loadSamplesSummary();
                } else {
                    this.selectStep(1);
                    this.hydrateProvisionalDoiFromDraftInfo();
                }
            });
        },
        createNewDraft() {
            if (!this.primed) {
                this.showPrimer = true;
                this.newDraft();
            } else {
                this.newDraft();
            }
        },
        newDraft() {
            console.log("newDraft method called..");
            if (this.defaultDraft) {
                this.defaultDraft.name =
                    "Untitled Project (Draft: " +
                    this.defaultDraft.key.split("-")[0] +
                    ")";
                this.selectDraft(this.defaultDraft);
            } else {
                this.fetchDrafts().then((response) => {
                    this.defaultDraft = response.data.default;
                    this.loading = false;
                    this.defaultDraft.name =
                        "Untitled Project (Draft: " +
                        this.defaultDraft.key.split("-")[0] +
                        ")";
                    this.selectDraft(this.defaultDraft);
                });
            }
        },
        updateDraft(e, stepId) {
            if (e) {
                this.currentDraft.name = e.target.innerText;
            }
            this.currentDraft.current_step = stepId;
            this.draftForm.errors = [];

            return axios
                .put(
                    "/dashboard/drafts/" + this.currentDraft.id,
                    this.currentDraft
                )
                .then((response) => {
                    this.currentDraft = response.data;
                    this.draftForm.name = this.currentDraft.name;
                });
        },
        closeDraft() {
            this.updateDraft(null, 3);
            this.loadingStep = true;
            this.fetchValidations().then(() => {
                if (this.validationStatus) {
                    this.loadingStep = true;
                    axios
                        .post(
                            "/dashboard/drafts/" +
                                this.currentDraft.id +
                                "/complete",
                            {}
                        )
                        .catch(() => {
                            this.loadingStep = false;
                        })
                        .then((response) => {
                            this.project = response.data.project;
                            this.validation = this.parseJSON(
                                response.data.validation.report
                            );
                            if (this.project) {
                                window.location =
                                    "/publish/" + this.currentDraft.id;
                            }
                        });
                } else {
                    this.loadingStep = false;
                    this.showSamplesSummary();
                    alert(
                        "Samples validation failed: Please provide all meta data to proceed"
                    );
                }
            });
        },
        selectStep(id) {
            if (
                id == 2 &&
                Array.isArray(this.processingWarnings) &&
                this.processingWarnings.length > 0
            ) {
                // Nested sample folders detected during processing — the user
                // must reorganise the upload before we let them advance, so
                // route any step-2 attempt (button click, direct ?step=2 URL,
                // back/forward nav) back to step 1 where the warning lives.
                id = 1;
            }
            this.steps.forEach((step) => {
                if (parseInt(step.id) < id) {
                    step.status = "complete";
                } else if (parseInt(step.id) == id) {
                    step.status = "current";
                } else {
                    step.status = "upcoming";
                }
            });
            if (id == 1) {
                this.updateDraft(null, 1);
                // this.loadingStep = true;
                this.selectedStudyIndex = null;
                this.selectedStudy = null;
                this.$nextTick(function () {
                    this.setQueryStringParameter(
                        "draft_id",
                        this.currentDraft.id
                    );
                    this.setQueryStringParameter("step", 1);
                    this.setQueryStringParameter("sample", null);
                    this.step = "1";
                    if (this.$refs.fsbRef) {
                        this.$refs.fsbRef.annotate();
                    }
                });
            } else if (id == 2) {
                this.$nextTick(() => {
                    const putPromise = this.updateDraft(null, 2);
                    const validationPromise =
                        this.project && this.project.id
                            ? this.fetchValidations()
                            : Promise.resolve();
                    Promise.all([putPromise, validationPromise]).then(() => {
                        this.setQueryStringParameter(
                            "draft_id",
                            this.currentDraft.id
                        );
                        this.setQueryStringParameter("step", 2);
                        this.step = "2";
                        this.updateAutoImportList();
                    });
                });
            }
        },
        /**
         * Re-sync local state with the current URL after a browser nav
         * (back/forward) or a manual address-bar edit. When the URL no
         * longer carries a `sample`, drop the open sample and return to
         * the samples summary view instead of leaving stale state behind.
         */
        handleUploadUrlChange() {
            const params = new URLSearchParams(window.location.search);
            let nextStep = params.get("step") || "1";
            const nextSample = params.get("sample");

            if (
                nextStep === "2" &&
                Array.isArray(this.processingWarnings) &&
                this.processingWarnings.length > 0
            ) {
                nextStep = "1";
                this.setQueryStringParameter("step", 1);
                this.setQueryStringParameter("sample", null);
            }

            this.step = nextStep;
            this.querySample = nextSample || null;

            if (nextStep === "2" && !nextSample && this.selectedStudy) {
                this.showSamplesSummary();
            }
        },
        setQueryStringParameter(name, value) {
            const params = new URLSearchParams(window.location.search);
            if (value) {
                params.set(name, value);
            } else {
                params.delete(name);
            }
            window.history.replaceState(
                {},
                "",
                decodeURIComponent(`${window.location.pathname}?${params}`)
            );
        },
        hidePrimer() {
            axios.post("/primer/skip").then(() => {
                router.reload({
                    only: ["user", "user.permissions", "user.roles"],
                });
            });
        },
        skipPrimer() {
            this.showPrimer = false;
            this.selectStep(1);
        },
        filesLoading(e) {
            this.loadingStep = e;
        },
        updateLoadingStatus(status) {
            this.loading = status;
        },
        process() {
            this.errorMessage = null;
            this.filesErrorMessage = null;
            let foldersExist = false;
            this.$refs.fsbRef.file.children.forEach((fso) => {
                if (fso.has_children) {
                    foldersExist = true;
                }
            });
            if (foldersExist) {
                this.hasStudies(this.$refs.fsbRef.file);
                this.loadSamplesSummary({ forceProcess: true });
            }
            if (
                this.$refs.fsbRef.file &&
                this.$refs.fsbRef.file.children.length > 0 &&
                foldersExist &&
                this.studiesExist
            ) {
                this.loadingStep = true;
                this.draftForm.owner_id = this.$page.props.auth.user?.id;
                this.draftForm.tags_array = this.draftForm.tags.map(
                    (a) => a.text
                );
            } else {
                if (
                    this.$refs.fsbRef.file.children.length > 0 &&
                    !foldersExist
                ) {
                    this.filesErrorMessage =
                        "Spectra files needs to be organised into folders. Please create a folder corresponding to each sample and add all your NMR spectroscopic experiment output files are added to the corresponding folders";
                } else if (this.$refs.fsbRef.file.children.length <= 0) {
                    this.filesErrorMessage =
                        "Please upload spectral data to proceed.";
                } else if (!this.studiesExist) {
                    this.filesErrorMessage =
                        "Please organize the spectral data into folders corresponding to the given samples and re-upload. Refer to the <a href='https://docs.nmrxiv.org/submission-guides/folder-structure.html' style='color:blue' target='_blank' rel='noopener noreferrer'>documentation</a> for more details.";
                } else {
                    this.filesErrorMessage =
                        "Please make sure you fill in all the required data before you proceed";
                }
            }
        },
        loadSamplesSummary(options = {}) {
            const { forceProcess = false } = options;

            const handleSuccess = (response) => {
                this.loadingStep = false;
                this.project = response.data.project;
                this.studies = response.data.studies;
                const nextWarnings = Array.isArray(response.data.warnings)
                    ? response.data.warnings
                    : [];
                this.processingWarnings = nextWarnings;
                this.syncNeedsReservedFromProject();
                if (this.project && this.studies && this.studies.length > 0) {
                    this.loadingStep = false;
                    if (nextWarnings.length > 0) {
                        // Keep the user on step 1 until the nested sample
                        // folder issue is resolved. Step 2 cannot be entered
                        // because ArchiveStudy / NMRium import rely on a
                        // single sample folder per study.
                        this.selectStep(1);
                        return;
                    }
                    this.selectStep(2);
                    this.$nextTick(() => {
                        if (this.querySample) {
                            let i = 0;
                            this.studies.forEach((s) => {
                                if (s.id == this.querySample) {
                                    this.selectStudy(s, i);
                                }
                                i = i + 1;
                            });
                        } else {
                            this.showSamplesSummary();
                        }
                    });
                    this.inprogressStudies = this.studies.filter(
                        (study) => study.internal_status != "complete"
                    );
                    if (this.inprogressStudies.length > 0) {
                        this.checkStudyStatus();
                    } else if (
                        this.studies.some((study) => !study.has_nmrium)
                    ) {
                        const missingDownloadUrl = this.studies.some(
                            (s) => !s.has_nmrium && !s.download_url
                        );
                        if (missingDownloadUrl) {
                            this.fetchProjectDetails()
                                .then((res) => {
                                    this.project = res.data.project;
                                    this.studies = res.data.studies;
                                    this.syncNeedsReservedFromProject();
                                })
                                .finally(() => {
                                    this.autoImport();
                                });
                        } else {
                            this.autoImport();
                        }
                    }
                } else {
                    if (this.studies?.length == 0) {
                        this.loadingStep = false;
                    }
                }
            };

            const handleError = (error) => {
                this.loadingStep = false;
                if (!error.response?.data?.errors) {
                    return;
                }
                Object.keys(error.response.data.errors).forEach((key) => {
                    error.response.data.errors[key] =
                        error.response.data.errors[key].join(", ");
                });
                this.draftForm.errors = error.response.data.errors;
                this.draftForm.error_message = error.response.data.message;
                this.draftForm.hasErrors = true;
                Object.keys(this.draftForm.errors).forEach((key) => {
                    if (!this.errorMessage) {
                        this.errorMessage =
                            "<b class='capitalize'>" +
                            key +
                            "</b>: " +
                            this.draftForm.errors[key] +
                            "</br>";
                    } else {
                        this.errorMessage +=
                            "<b class='capitalize'>" +
                            key +
                            "</b>: " +
                            this.draftForm.errors[key] +
                            "</br>";
                    }
                });
            };

            if (!forceProcess && this.prefetchedStepTwoPayload) {
                const payload = this.prefetchedStepTwoPayload;
                this.prefetchedStepTwoPayload = null;
                const p = payload.project;
                const studiesList = payload.studies;
                if (p && studiesList && studiesList.length > 0) {
                    handleSuccess({
                        data: {
                            project: p,
                            studies: studiesList,
                        },
                    });

                    return Promise.resolve();
                }
            }

            if (forceProcess) {
                return this.fetchProjectDetails()
                    .then(handleSuccess)
                    .catch(handleError);
            }

            return axios
                .get("/dashboard/drafts/" + this.currentDraft.id + "/info")
                .then((infoRes) => {
                    const p = infoRes.data.project;
                    const studiesList = infoRes.data.studies;

                    if (!p || !studiesList || studiesList.length === 0) {
                        return this.fetchProjectDetails().then(handleSuccess);
                    }

                    return handleSuccess({
                        data: {
                            project: p,
                            studies: studiesList,
                        },
                    });
                })
                .catch(() =>
                    this.fetchProjectDetails()
                        .then(handleSuccess)
                        .catch(handleError)
                );
        },
        fetchDraftStudiesStatus() {
            return axios.get(
                "/dashboard/drafts/" + this.currentDraft.id + "/status"
            );
        },
        checkStudyStatus() {
            if (this.checkStudyStatusTimerId != null) {
                clearTimeout(this.checkStudyStatusTimerId);
            }
            this.checkStudyStatusTimerId = window.setTimeout(() => {
                this.checkStudyStatusTimerId = null;
                if (!this.currentDraft?.id || !this.studies) {
                    return;
                }
                this.fetchDraftStudiesStatus()
                    .then((response) => {
                        this.loadingStep = false;
                        const rows = response.data.studies || [];
                        const byId = {};
                        rows.forEach((s) => {
                            byId[s.id] = s;
                        });
                        this.studies.forEach((study) => {
                            const st = byId[study.id];
                            if (st) {
                                study.internal_status = st.internal_status;
                                study.has_nmrium = st.has_nmrium;
                            }
                        });
                        this.syncNeedsReservedFromProject();
                        if (this.project?.id) {
                            this.fetchValidations();
                        }
                        this.resetLoader();
                        this.inprogressStudies = this.studies.filter(
                            (study) => study.internal_status != "complete"
                        );
                        if (this.inprogressStudies.length > 0) {
                            this.checkStudyStatus();
                        } else {
                            this.fetchProjectDetails()
                                .then((res) => {
                                    this.project = res.data.project;
                                    this.studies = res.data.studies;
                                    this.syncNeedsReservedFromProject();
                                })
                                .finally(() => {
                                    this.autoImport();
                                });
                        }
                    })
                    .catch(() => {
                        this.loadingStep = false;
                        this.resetLoader();
                    });
            }, 30000);
        },
        editData(e) {
            let i = 0;
            this.studies.forEach((s) => {
                if (s.name == e.model.name) {
                    this.selectStudy(s, i);
                }
                i = i + 1;
            });
        },
        hasStudies(file) {
            if (file.model_type == "study") {
                this.studiesExist = true;
            }
            if (file.has_children && file.children) {
                file.children.forEach((fso) => {
                    this.hasStudies(fso);
                });
            }
        },
        /**
         * @param {() => void} [onReady]
         */
        ensureStructureSearchEditor(onReady) {
            if (!this.chemicalCompositionExpanded) {
                if (typeof onReady === "function") {
                    onReady();
                }
                return;
            }
            const run = (retriesLeft) => {
                this.$nextTick(() => {
                    requestAnimationFrame(async () => {
                        const el = document.getElementById(
                            "structureSearchEditor"
                        );
                        if (!el || !el.isConnected) {
                            if (retriesLeft > 0) {
                                run(retriesLeft - 1);
                            } else if (typeof onReady === "function") {
                                onReady();
                            }
                            return;
                        }
                        const { width, height } = el.getBoundingClientRect();
                        if (width < 4 || height < 4) {
                            if (retriesLeft > 0) {
                                run(retriesLeft - 1);
                            } else if (typeof onReady === "function") {
                                onReady();
                            }
                            return;
                        }
                        if (!this.editor) {
                            this.editor = await createStructureEditor(
                                "structureSearchEditor"
                            );
                            this.editor.onChange(() => {
                                this.syncEditorHasStructure();
                            });
                        }
                        this.syncEditorHasStructure();
                        if (typeof onReady === "function") {
                            onReady();
                        }
                    });
                });
            };
            run(20);
        },
        syncEditorHasStructure() {
            this.editorHasStructure = editorHasStructureContent(this.editor);
        },
        selectStudy(study, index, datasetIndex = null) {
            if (!this.busy) {
                if (study.internal_status == "complete") {
                    this.selectedStudyIndex = index;
                    this.selectedStudy = study;
                    this.chemicalCompositionExpanded = false;
                    this.setQueryStringParameter("sample", study.id);
                    this.studyNameDraft = this.selectedStudy.name;
                    this.studyForm.name = this.selectedStudy.name;
                    this.studyForm.description = (
                        this.selectedStudy.description ?? ""
                    ).replace(/<\/br>/g, " ");
                    this.studyForm.species = JSON.parse(
                        this.selectedStudy.species
                    )
                        ? JSON.parse(this.selectedStudy.species)
                        : [];
                    let tags = [];
                    this.selectedStudy.tags.forEach((t) => {
                        tags.push({
                            text: t.name["en"],
                        });
                    });
                    this.studyForm.tags = tags;
                    if (this.displaySamplesSummaryInfo) {
                        this.displaySamplesSummaryInfo = false;
                    }
                    this.editor = null;
                    this.editorHasStructure = false;
                    this.syncCompositionSampleType(this.selectedStudy.sample);
                    this.seedAssignmentsDraft(this.selectedStudy);
                    this.$nextTick(() => {
                        const el = document.getElementById(
                            "structureSearchEditor"
                        );
                        if (el) {
                            el.innerHTML = "";
                        }
                        this.percentage = Math.min(
                            99.99,
                            this.compositionSliderMax
                        );
                    });
                    if (
                        this.selectedStudy &&
                        this.selectedStudy.sample.molecules.length == 0
                    ) {
                        this.autoImportMolecularData(this.selectedStudy);
                    }
                    if (!datasetIndex) {
                        this.selectedDSIndex = 0;
                    } else {
                        this.selectedDSIndex = datasetIndex;
                    }
                }
            }
        },
        fetchProjectDetails() {
            return axios.post(
                "/dashboard/drafts/" + this.currentDraft.id + "/process",
                this.draftForm
            );
        },
        fetchValidations() {
            if (!this.project?.id) {
                return Promise.resolve();
            }

            return axios
                .get("/projects/" + this.project.id + "/validation")
                .then((response) => {
                    this.validation = response.data.report;
                    const studies = this.validation?.project?.studies;
                    this.validationStatus = true;
                    if (!Array.isArray(studies) || studies.length === 0) {
                        this.validationStatus = false;
                    } else {
                        studies.forEach((study) => {
                            if (study.status == false) {
                                this.validationStatus = false;
                            }
                        });
                    }
                });
        },
        /**
         * Pre-fill the per-spectrum textareas from each dataset's saved
         * `assignments.acs` (set by the dashboard.datasets.assignments.update
         * endpoint). Called every time the user navigates between samples so
         * the editor reflects what's persisted, never stale data from the
         * previously selected study.
         */
        seedAssignmentsDraft(study) {
            const next = {};
            const datasets = (study && study.datasets) || [];
            datasets.forEach((ds) => {
                next[ds.id] = (ds.assignments && ds.assignments.acs) || "";
            });
            this.assignmentsDraft = next;
            this.assignmentsErrors = {};
            this.assignmentsSavedAt = {};
        },
        /**
         * Returns true when the dataset has any user-saved assignment
         * content. Mirrors `Dataset::hasAssignments()` on the backend so
         * the "saved" / "empty" pill in the UI matches what the validator
         * sees.
         */
        datasetHasAssignments(ds) {
            const a = ds && ds.assignments;
            if (!a || typeof a !== "object") {
                return false;
            }
            if (typeof a.acs === "string" && a.acs.trim() !== "") {
                return true;
            }
            if (Array.isArray(a.atom_peaks) && a.atom_peaks.length > 0) {
                return true;
            }
            return false;
        },
        /**
         * Persist the textarea content for one spectrum. Empty content
         * clears the field on the server (validator will then mark the
         * dataset as missing assignments). Refreshes validations so the
         * sample-level badge updates without a full page reload.
         */
        /**
         * Seed the per-nucleus-group textarea drafts from any existing
         * `assignments.acs` saved on the contained datasets. NMR-wise, all
         * datasets observing the same nucleus channel (e.g. a 13C and its
         * DEPT, or a COSY and a NOESY both detecting 1H) share the same
         * chemical-shift assignment list — so we collapse them to a single
         * input and use the first non-empty value as the seed. Whichever
         * dataset the user previously saved into wins; subsequent saves
         * normalise the rest of the group to the same string.
         */
        seedGroupAssignmentDraft(groups) {
            const next = {};
            (groups || []).forEach((group) => {
                let acs = "";
                for (const ds of group.datasets) {
                    const saved =
                        (ds && ds.assignments && ds.assignments.acs) || "";
                    if (saved.trim() !== "") {
                        acs = saved;
                        break;
                    }
                }
                next[group.key] = acs;
            });
            this.groupAssignmentDraft = next;
            this.groupAssignmentErrors = {};
            this.groupAssignmentSavedAt = {};
        },
        /**
         * True when any dataset in the nucleus-channel group has saved
         * assignment content. Drives the green status dot in the tab strip
         * and the per-card indicator.
         */
        groupHasAssignments(group) {
            if (!group || !Array.isArray(group.datasets)) {
                return false;
            }
            return group.datasets.some((ds) => this.datasetHasAssignments(ds));
        },
        /**
         * Autosave-on-blur for the shared group textarea. Skips the request
         * when nothing changed from what every dataset already has saved.
         */
        autosaveAssignmentsForGroup(group) {
            if (
                !group ||
                !Array.isArray(group.datasets) ||
                !group.datasets.length
            ) {
                return;
            }
            const key = group.key;
            const next = (this.groupAssignmentDraft[key] || "").trim();
            const allMatch = group.datasets.every((ds) => {
                const saved = (
                    (ds.assignments && ds.assignments.acs) ||
                    ""
                ).trim();
                return saved === next;
            });
            if (allMatch) {
                return;
            }
            return this.saveAssignmentsForGroup(group);
        },
        /**
         * Persist the shared assignment string to every dataset in a nucleus
         * group via parallel PUTs. We copy the same payload to each row so
         * the validator (which inspects per-dataset `assignments`) sees the
         * group as fully assigned.
         */
        saveAssignmentsForGroup(group) {
            if (
                !group ||
                !Array.isArray(group.datasets) ||
                !group.datasets.length
            ) {
                return;
            }
            const key = group.key;
            const acs = (this.groupAssignmentDraft[key] || "").trim();
            this.groupAssignmentSavingKey = key;
            this.groupAssignmentErrors = {
                ...this.groupAssignmentErrors,
                [key]: null,
            };

            const requests = group.datasets.map((ds) =>
                axios
                    .put("/dashboard/datasets/" + ds.id + "/assignments", {
                        acs,
                        source: "manual",
                    })
                    .then((response) => {
                        const saved =
                            response.data && response.data.assignments;
                        ds.assignments = saved || null;
                    })
            );

            return Promise.all(requests)
                .then(() => {
                    this.groupAssignmentSavedAt = {
                        ...this.groupAssignmentSavedAt,
                        [key]: new Date().toLocaleTimeString(),
                    };
                    return this.fetchValidations();
                })
                .catch((err) => {
                    const msg =
                        (err.response &&
                            (err.response.data?.message ||
                                err.response.statusText)) ||
                        err.message ||
                        "Failed to save";
                    this.groupAssignmentErrors = {
                        ...this.groupAssignmentErrors,
                        [key]: msg,
                    };
                })
                .finally(() => {
                    this.groupAssignmentSavingKey = null;
                });
        },
        /**
         * Autosave-on-blur wrapper around `saveAssignmentsForDataset`. Skips
         * the network round-trip when the textarea content is unchanged from
         * what is already persisted on the dataset, so tabbing through the
         * cards does not generate a flood of identical writes.
         */
        autosaveAssignmentsForDataset(dataset) {
            if (!dataset || !dataset.id) {
                return;
            }
            const id = dataset.id;
            const next = (this.assignmentsDraft[id] || "").trim();
            const previous = (
                (dataset.assignments && dataset.assignments.acs) ||
                ""
            ).trim();
            if (next === previous) {
                return;
            }
            return this.saveAssignmentsForDataset(dataset);
        },
        saveAssignmentsForDataset(dataset) {
            if (!dataset || !dataset.id) {
                return;
            }
            const id = dataset.id;
            const acs = (this.assignmentsDraft[id] || "").trim();
            this.assignmentsSavingId = id;
            this.assignmentsErrors = {
                ...this.assignmentsErrors,
                [id]: null,
            };
            return axios
                .put("/dashboard/datasets/" + id + "/assignments", {
                    acs,
                    source: "manual",
                })
                .then((response) => {
                    const saved = response.data && response.data.assignments;
                    dataset.assignments = saved || null;
                    this.assignmentsSavedAt = {
                        ...this.assignmentsSavedAt,
                        [id]: new Date().toLocaleTimeString(),
                    };
                    return this.fetchValidations();
                })
                .catch((err) => {
                    const msg =
                        (err.response &&
                            (err.response.data?.message ||
                                err.response.statusText)) ||
                        err.message ||
                        "Failed to save";
                    this.assignmentsErrors = {
                        ...this.assignmentsErrors,
                        [id]: msg,
                    };
                })
                .finally(() => {
                    this.assignmentsSavingId = null;
                });
        },
        updateAutoImportList() {
            this.studiesToImport = [];
            this.studies.forEach((study) => {
                // HiFSA samples must load via NMRium URL+fileFilter; NMRKit has
                // no fileFilter and would persist Cosmic Truth EXTRA/ artifacts.
                if (!study.has_nmrium && !studyHasHifsa(study)) {
                    this.studiesToImport.push({
                        projectSlug: this.project.slug,
                        study: study,
                        status: false,
                    });
                }
            });
        },
        resetLoader() {
            if (this.loader && this.loader.kind === "idle") {
                return;
            }
            this.loader = {
                kind: "idle",
                importMeta: null,
                bannerMessage: null,
                iframe: null,
            };
        },
        spectraLoading(e) {
            if (!e || !e.status) {
                this.resetLoader();

                return;
            }
            if (e.importMeta) {
                this.loader = {
                    kind: "import",
                    importMeta: e.importMeta,
                    bannerMessage: null,
                    iframe: null,
                };
            } else if (e.viewerMeta) {
                this.loader = {
                    kind: "iframe",
                    importMeta: null,
                    bannerMessage: null,
                    iframe: e.viewerMeta,
                };
            } else if (e.preparingImport) {
                this.loader = {
                    kind: "import",
                    importMeta: null,
                    bannerMessage: "Preparing import…",
                    iframe: null,
                };
            } else if (e.message != null && e.message !== "") {
                this.loader = {
                    kind: "import",
                    importMeta: null,
                    bannerMessage: e.message,
                    iframe: null,
                };
            }
        },
        updateTags(e) {
            this.busy = true;
            if (!e.type) {
                this.studyForm.tags = e;
            }
            this.studyForm.tags_array = this.studyForm.tags.map((a) => a.text);
            this.saveStudyDetails();
        },
        saveStudyDetails() {
            this.busy = true;
            this.studySaving = true;
            this.loadingStep = true;
            axios
                .put(
                    "/dashboard/studies/" + this.selectedStudy.id + "/update",
                    this.studyForm
                )
                .then((response) => {
                    if (response) {
                        this.busy = false;
                        this.studies[this.selectedStudyIndex] = response.data;
                        this.selectedStudy = response.data;
                        this.studyNameDraft = response.data.name;
                        this.studyForm.hasErrors = false;
                    }
                })
                .catch((error) => {
                    this.busy = false;
                    Object.keys(error.response.data.errors).forEach((key) => {
                        error.response.data.errors[key] =
                            error.response.data.errors[key].join(", ");
                    });
                    this.studyForm.errors = error.response.data.errors;
                    this.studyForm.error_message = error.response.data.message;
                    this.studyForm.hasErrors = true;
                })
                .finally(() => {
                    this.studySaving = false;
                    this.loadingStep = false;
                });
        },
        deleteMolecule(mol) {
            axios
                .delete(
                    "/dashboard/studies/" +
                        this.selectedStudy.id +
                        "/molecule/" +
                        mol.id
                )
                .then((res) => {
                    applySampleMoleculeResponse(
                        this.selectedStudy.sample,
                        res.data
                    );
                    this.smiles = "";
                    this.percentage = 0;
                    this.syncCompositionSampleType(this.selectedStudy.sample);
                    this.$nextTick(() => {
                        this.percentage = Math.min(
                            99.99,
                            this.compositionSliderMax
                        );
                        this.$refs.mixtureCompositionForm?.resetDraft?.();
                    });
                    if (this.editor) {
                        this.editor.setSmiles("");
                        this.syncEditorHasStructure();
                    }
                });
        },
        editMolecule(mol) {
            this.activeInputTab = "editor";
            const mixtureComponent = this.getMixtureComponentForMolecule(mol);
            if (mixtureComponent) {
                this.compositionSampleType = "mixture";
                this.$nextTick(() => {
                    this.$refs.mixtureCompositionForm?.resetDraft?.();
                    if (this.$refs.mixtureCompositionForm) {
                        this.$refs.mixtureCompositionForm.draft = {
                            value: mixtureComponent.value,
                            integrated_signal:
                                mixtureComponent.integrated_signal ?? "",
                            n_nuclei: mixtureComponent.n_nuclei ?? "",
                        };
                        if (
                            mixtureComponent.integrated_signal ||
                            mixtureComponent.n_nuclei
                        ) {
                            this.$refs.mixtureCompositionForm.expandDetails?.();
                        }
                    }
                });
            } else {
                const raw = mol.pivot?.percentage_composition;
                if (this.isCompositionPercentUnknown(raw)) {
                    this.compositionSampleType = "unknown";
                } else {
                    this.compositionSampleType = "mixture";
                    this.percentage =
                        parseFloat(mol.pivot.percentage_composition) || 0;
                }
            }
            this.ensureStructureSearchEditor(() => {
                if (this.editor) {
                    this.editor.setSmiles(mol.canonical_smiles);
                    this.syncEditorHasStructure();
                }
                axios
                    .delete(
                        "/dashboard/studies/" +
                            this.selectedStudy.id +
                            "/molecule/" +
                            mol.id
                    )
                    .then((res) => {
                        applySampleMoleculeResponse(
                            this.selectedStudy.sample,
                            res.data
                        );
                    });
            });
        },
        saveMolecule(mol, study) {
            if (!study) {
                study = this.selectedStudy;
            }
            if (!mol) {
                this.ensureStructureSearchEditor(() => {
                    if (!this.editor) {
                        return;
                    }
                    const molfile = this.editor.getMolFile();
                    this.standardizeMolecules(molfile).then((res) => {
                        this.associateMoleculeToStudy(res.data, study);
                    });
                });
                return;
            }
            this.associateMoleculeToStudy(mol, study);
        },
        associateMoleculeToStudy(mol, study) {
            const payload = {
                InChI: mol.inchi,
                InChIKey: mol.inchikey,
                mol: mol.standardized_mol,
                canonical_smiles: mol.canonical_smiles,
                composition_mode: this.compositionSampleType,
            };

            if (this.compositionSampleType === "unknown") {
                payload.percentage = null;
            } else if (this.compositionSampleType === "mixture") {
                const mixturePayload =
                    this.$refs.mixtureCompositionForm?.mixturePayload?.();
                if (!mixturePayload?.basis || mixturePayload.value == null) {
                    return;
                }
                Object.assign(payload, mixturePayload);
            } else {
                payload.percentage = this.percentage;
            }

            axios
                .post("/dashboard/studies/" + study.id + "/molecule", payload)
                .then((res) => {
                    applySampleMoleculeResponse(study.sample, res.data);
                    this.chemicalInput = "";
                    this.detectedFormat = "";
                    this.syncCompositionSampleType(study.sample);
                    this.$nextTick(() => {
                        this.percentage = Math.min(
                            99.99,
                            this.compositionSliderMax
                        );
                        this.$refs.mixtureCompositionForm?.resetDraft?.();
                    });
                    if (this.editor) {
                        this.editor.setSmiles("");
                        this.syncEditorHasStructure();
                    }
                });
        },
        saveMixtureMetadata(metadata) {
            if (
                !this.selectedStudy?.sample?.mixture_composition ||
                !metadata?.basis
            ) {
                return;
            }

            axios
                .put(
                    "/dashboard/studies/" +
                        this.selectedStudy.id +
                        "/mixture-composition",
                    metadata
                )
                .then((res) => {
                    applySampleMoleculeResponse(
                        this.selectedStudy.sample,
                        res.data
                    );
                });
        },
        getMixtureComponentForMolecule(molecule) {
            const components =
                this.selectedStudy?.sample?.mixture_composition?.components ??
                [];

            return components.find(
                (component) => component.molecule_id === molecule.id
            );
        },
        syncCompositionSampleType(sample) {
            const molecules = sample?.molecules ?? [];
            if (molecules.length === 0) {
                this.compositionSampleType = "pure";
                return;
            }

            if (sample?.mixture_composition) {
                this.compositionSampleType = "mixture";
                return;
            }

            const allUnknown = molecules.every((molecule) =>
                this.isCompositionPercentUnknown(
                    molecule.pivot?.percentage_composition
                )
            );
            this.compositionSampleType = allUnknown ? "unknown" : "mixture";
        },
        formatMixtureValue,
        standardizeMolecules(mol) {
            const key = String(mol ?? "").trim();
            if (!key) {
                return Promise.reject(new Error("Empty molfile"));
            }
            if (this.standardizeRequestCache.has(key)) {
                return this.standardizeRequestCache.get(key);
            }
            const promise = axios.post(this.chemistryStandardizeUrl, key);
            this.standardizeRequestCache.set(key, promise);
            return promise;
        },
        /**
         * Normalize the NMRKit `/latest/spectra/parse/url` payload into the
         * legacy NMRium shape that our SpectraEditor + embedded NMRium iframe
         * understand. The new endpoint returns `data.sources` (plural array)
         * and `spectrum.selector`, while NMRium expects `data.source`
         * (singular with merged baseURL/relativePath entries) and
         * `spectrum.sourceSelector`. Transform in-place.
         */
        normalizeNmriumPayload(parsedSpectra) {
            if (!parsedSpectra || typeof parsedSpectra !== "object") {
                return;
            }

            if (
                !parsedSpectra.source &&
                Array.isArray(parsedSpectra.sources) &&
                parsedSpectra.sources.length > 0
            ) {
                const mergedEntries = [];
                parsedSpectra.sources.forEach((src) => {
                    const baseURL = src?.baseURL ?? "";
                    const entries = Array.isArray(src?.entries)
                        ? src.entries
                        : [];
                    entries.forEach((entry) => {
                        mergedEntries.push({
                            baseURL,
                            relativePath: entry?.relativePath ?? "",
                        });
                    });
                });
                if (mergedEntries.length > 0) {
                    parsedSpectra.source = { entries: mergedEntries };
                }
            }

            if (Array.isArray(parsedSpectra.spectra)) {
                parsedSpectra.spectra.forEach((spec) => {
                    if (!spec || typeof spec !== "object") {
                        return;
                    }
                    if (!spec.sourceSelector && spec.selector) {
                        spec.sourceSelector = spec.selector;
                    }
                });
            }
        },
        autoImportMolecularData(study) {
            if (this.autoImportMolecularDataStartedFor.has(study.id)) {
                return;
            }
            this.autoImportMolecularDataStartedFor.add(study.id);

            const baseUrl = String(
                this.$page.props.url ?? this.url ?? ""
            ).replace(/\/+$/, "");
            if (!baseUrl) {
                console.warn(
                    "[autoImportMolecularData] skipping study " +
                        study.id +
                        " - no public base URL"
                );

                return;
            }
            axios
                .get("/dashboard/studies/" + study.id + "/annotations")
                .then((response) => {
                    let nmredataFiles = response.data;
                    nmredataFiles.forEach((file) => {
                        let username =
                            this.$page.props.team && this.$page.props.team.owner
                                ? this.$page.props.team.owner.username
                                : this.project.owner.username;
                        let url =
                            baseUrl +
                            "/" +
                            username +
                            "/studies" +
                            "/" +
                            study.id +
                            "/file/" +
                            file.slug;
                        axios.get(url).then((response) => {
                            // convert to smiles
                            axios
                                .post(
                                    this.chemistryStandardizeUrl,
                                    response.data
                                )
                                .then((res) => {
                                    let standard_inchi = res.data.inchi;
                                    let molExists = false;
                                    study.sample.molecules.forEach((mol) => {
                                        if (
                                            mol.standard_inchi == standard_inchi
                                        ) {
                                            molExists = true;
                                        }
                                    });
                                    if (!molExists) {
                                        this.saveMolecule(res.data, study);
                                    }
                                });
                        });
                    });
                });
        },
        autoGenerateDescription() {
            let desc =
                "This dataset contains NMR spectra obtained for the sample -" +
                this.selectedStudy.name +
                "</br>";
            axios
                .get(
                    "/dashboard/studies/" +
                        this.selectedStudy.id +
                        "/nmriumInfo"
                )
                .then((response) => {
                    let nmrium_info = response.data;
                    if (nmrium_info) {
                        // MIChI-compliant fields mapping
                        const michiFields = {
                            solvent: "NMR Solvent",
                            temperature: "Temperature",
                            nucleus: "Nucleus",
                            experiment: "NMR Pulse Sequence",
                            pulseSequence: "NMR Pulse Sequence",
                            numberOfScans: "Number of Scans",
                            originFrequency: "Observed Frequency",
                            baseFrequency: "Observed Frequency",
                            spectralWidth: "Spectral Width",
                            numberOfPoints: "Number of Data Points",
                            relaxationTime: "Relaxation Delay",
                            relaxationDelay: "Relaxation Delay",
                            fieldStrength: "Magnetic Field Strength",
                            probeName: "NMR Probe",
                        };

                        nmrium_info.data.spectra.forEach((spectra) => {
                            Object.keys(spectra.info).forEach((key) => {
                                // Only include MIChI-compliant fields
                                if (michiFields[key]) {
                                    desc =
                                        desc +
                                        michiFields[key] +
                                        ": " +
                                        spectra.info[key] +
                                        "</br>";
                                }
                            });
                        });
                        this.studyForm.description = desc.replace(
                            /<\/br>/g,
                            " "
                        );
                        this.saveStudyDetails();
                    }
                });
        },
        autoImport() {
            this.spectraLoading({
                status: true,
                preparingImport: true,
            });
            this.updateAutoImportList();
            if (this.studiesToImport.length > 0) {
                this.fetchNMRium();
            } else {
                console.log(
                    "Nothing to import: NMRium spectra JSON already exists"
                );
                this.resetLoader();
            }
        },
        /**
         * After a single study finishes auto-importing its NMRium JSON,
         * propagate has_nmrium=true to local state so badges/buttons react,
         * and trigger a SpectraEditor reload if this study is on screen.
         */
        markStudyImported(study) {
            if (!study) {
                return;
            }
            study.has_nmrium = true;
            const localIdx = this.studies.findIndex((s) => s.id === study.id);
            if (localIdx >= 0 && this.studies[localIdx] !== study) {
                this.studies[localIdx].has_nmrium = true;
            }
            if (this.selectedStudy && this.selectedStudy.id === study.id) {
                this.selectedStudy.has_nmrium = true;
                this.refreshSpectraEditor();
            }
        },
        /**
         * After fetchProjectDetails replaces this.studies with fresh objects,
         * re-bind selectedStudy/selectedStudyIndex by id so the right pane,
         * sidebar (compounds), and validation badges read off the new objects.
         */
        rebindSelectedStudyAfterImport() {
            if (!this.selectedStudy || !Array.isArray(this.studies)) {
                return;
            }
            const idx = this.studies.findIndex(
                (s) => s.id === this.selectedStudy.id
            );
            if (idx === -1) {
                return;
            }
            this.selectedStudy = this.studies[idx];
            this.selectedStudyIndex = idx;
            this.refreshSpectraEditor();
        },
        /**
         * Tell the inlined SpectraEditor to invalidate its NMRium cache and
         * reload for the currently bound study (its watcher bails on same id).
         */
        refreshSpectraEditor() {
            const ref = this.$refs.spectraEditorREF;
            if (ref && typeof ref.reload === "function") {
                this.$nextTick(() => {
                    ref.reload();
                });
            }
        },
        fetchNMRium() {
            let studyDetails =
                this.importPendingSamples.length > 0
                    ? this.importPendingSamples[0]
                    : null;
            if (studyDetails) {
                const completedCount =
                    this.studies.length - this.importPendingSamples.length;
                const sampleLabel =
                    (studyDetails.study.name &&
                        String(studyDetails.study.name).trim()) ||
                    studyDetails.study.slug ||
                    "Sample";

                this.spectraLoading({
                    status: true,
                    importMeta: {
                        completedCount,
                        total: this.studies.length,
                        sampleLabel,
                    },
                });

                const baseUrl = String(
                    this.$page.props.url ?? this.url ?? ""
                ).replace(/\/+$/, "");

                let url = null;
                if (studyDetails.study.download_url) {
                    url = studyDetails.study.download_url;
                } else if (baseUrl) {
                    let username = this.$page.props.team?.owner
                        ? this.$page.props.team.owner.username
                        : this.project.owner.username;

                    url =
                        baseUrl +
                        "/" +
                        username +
                        "/datasets/" +
                        this.project.slug +
                        "/" +
                        studyDetails.study.slug;
                }

                if (!url) {
                    console.warn(
                        "[autoImport] skipping study " +
                            studyDetails.study.id +
                            " - no download_url and no public base URL"
                    );
                    let pending = this.studiesToImport.filter(
                        (f) => f.study.id == studyDetails.study.id
                    )[0];
                    if (pending) {
                        pending.status = true;
                    }
                    this.loadingStep = false;
                    this.fetchNMRium();

                    return;
                }

                let safeUrl = url;
                try {
                    safeUrl = encodeURI(decodeURI(url));
                } catch (e) {
                    safeUrl = encodeURI(url);
                }

                const spectraParserUrl =
                    this.$page.props.spectraParserUrl ||
                    "https://dev.nmrkit.nmrxiv.org/latest/spectra/parse/url";
                axios
                    .post(spectraParserUrl, {
                        url: safeUrl,
                        capture_snapshot: false,
                    })
                    .then((response) => {
                        const nmriumState =
                            response.data?.nmriumState ?? response.data ?? {};
                        let parsedSpectra = nmriumState.data ?? {};
                        this.normalizeNmriumPayload(parsedSpectra);
                        if (Array.isArray(parsedSpectra.spectra)) {
                            parsedSpectra.spectra.forEach((spec) => {
                                delete spec["data"];
                                delete spec["meta"];
                                delete spec["originalData"];
                                delete spec["originalInfo"];
                            });
                        }
                        const version =
                            nmriumState.version ??
                            parsedSpectra.version ??
                            null;
                        delete parsedSpectra["version"];
                        const molecules = Array.isArray(parsedSpectra.molecules)
                            ? parsedSpectra.molecules
                            : [];
                        if (molecules.length > 0) {
                            molecules.forEach((mol) => {
                                this.standardizeMolecules(mol.molfile).then(
                                    (res) => {
                                        this.associateMoleculeToStudy(
                                            res.data,
                                            studyDetails.study
                                        );
                                    }
                                );
                            });
                        }
                        return axios
                            .post(
                                "/dashboard/studies/" +
                                    studyDetails.study.id +
                                    "/nmriumInfo",
                                {
                                    data: parsedSpectra,
                                    version: version,
                                }
                            )
                            .then(() => {
                                this.studiesToImport.filter(
                                    (f) => f.study.id == studyDetails.study.id
                                )[0].status = true;
                                this.markStudyImported(studyDetails.study);
                                this.autoImportMolecularData(
                                    studyDetails.study
                                );
                                this.fetchNMRium();
                            })
                            .catch(() => {
                                this.studiesToImport.filter(
                                    (f) => f.study.id == studyDetails.study.id
                                )[0].status = true;
                                this.fetchNMRium();
                            })
                            .finally(() => {
                                this.loadingStep = false;
                            });
                    })
                    .catch((err) => {
                        const status = err?.response?.status;
                        const detail =
                            err?.response?.data?.detail ?? err?.response?.data;
                        console.warn(
                            "[autoImport] spectra-parser failed for study " +
                                studyDetails.study.id +
                                " (HTTP " +
                                status +
                                "): " +
                                (typeof detail === "string"
                                    ? detail
                                    : JSON.stringify(detail)) +
                                " | url=" +
                                safeUrl
                        );
                        let study = this.studiesToImport.filter(
                            (f) => f.study.id == studyDetails.study.id
                        )[0];
                        if (study) {
                            study.status = true;
                            this.fetchNMRium();
                        }
                        this.loadingStep = false;
                    });
            } else {
                this.fetchProjectDetails()
                    .then((response) => {
                        this.project = response.data.project;
                        this.studies = response.data.studies;
                        this.syncNeedsReservedFromProject();
                        this.rebindSelectedStudyAfterImport();
                        return this.fetchValidations();
                    })
                    .finally(() => {
                        this.loadingStep = false;
                        this.resetLoader();
                    });
            }
        },
        updateSpecies(species) {
            if (species && species != "") {
                this.studyForm.species.push(species);
                this.saveStudyDetails();
                this.studySpecies = "";
            }
        },
        removeSpecies(index) {
            if (index > -1) {
                this.studyForm.species.splice(index, 1);
                this.saveStudyDetails();
            }
        },
        handleEditorPaste(event) {
            if (!this.chemicalCompositionExpanded) {
                return;
            }

            const pastedText = event.clipboardData?.getData("text/plain") ?? "";

            if (!pastedText.trim()) {
                return;
            }

            const inputType = detectStructureInputType(pastedText);

            if (
                inputType === "CAS" ||
                inputType === "SMILES" ||
                inputType === "MOL/SDF"
            ) {
                event.preventDefault();
                event.stopPropagation();
                this.importStructureInput(pastedText);
            }
        },

        async importStructureInput(text, fileName = "") {
            const trimmed = text.trim();

            if (!trimmed) {
                return;
            }

            const inputType = detectStructureInputType(trimmed, fileName);
            this.errorMessage = "";

            if (inputType === "CAS") {
                await this.loadCasStructure(trimmed);
                return;
            }

            if (inputType === "SMILES" || inputType === "MOL/SDF") {
                await this.loadStructureIntoEditor(
                    trimmed,
                    fileName,
                    inputType
                );
                return;
            }

            this.errorMessage =
                "Unrecognized format. Paste or drop SMILES, MOL, SDF, or a CAS number.";
        },

        async loadStructureIntoEditor(text, fileName = "", format = null) {
            const loadId = ++this.structureLoadCounter;
            this.errorMessage = "";
            this.structureLoading = true;

            const resolvedFormat = format ?? detectStructureInputFormat(text);
            /** @type {{ type: 'smiles' | 'molfile', value: string } | null} */
            let structureToLoad = null;

            try {
                if (resolvedFormat === "SMILES") {
                    const smiles = text.trim();
                    OCL.Molecule.fromSmiles(smiles);
                    structureToLoad = { type: "smiles", value: smiles };
                } else if (resolvedFormat === "MOL/SDF") {
                    structureToLoad =
                        await resolveStructureForEditorWithStandardize(
                            text,
                            fileName,
                            (molfile) => this.standardizeMolecules(molfile)
                        );
                } else {
                    this.errorMessage =
                        "Unable to detect chemical format. Please check your input.";
                    return;
                }
            } catch (e) {
                if (loadId !== this.structureLoadCounter) {
                    return;
                }
                this.errorMessage =
                    "Could not parse the structure. Check the file or pasted content.";
                return;
            } finally {
                if (loadId === this.structureLoadCounter) {
                    this.structureLoading = false;
                }
            }

            if (loadId !== this.structureLoadCounter || !structureToLoad) {
                return;
            }

            this.ensureStructureSearchEditor(() => {
                if (loadId !== this.structureLoadCounter || !this.editor) {
                    return;
                }

                try {
                    if (structureToLoad.type === "smiles") {
                        this.editor.setSmiles(structureToLoad.value);
                    } else {
                        this.editor.setMolFile(structureToLoad.value);
                    }
                    this.syncEditorHasStructure();
                } catch (e) {
                    this.errorMessage =
                        "Could not load the structure into the editor.";
                }
            });
        },

        async fetchFromCAS(casNumber) {
            try {
                const response = await axios.get("/cas/detail", {
                    params: {
                        cas_rn: casNumber,
                    },
                    timeout: 30000,
                });

                return response.data;
            } catch (error) {
                const errorMessage =
                    error.response?.data?.error ||
                    error.response?.data?.message ||
                    "CAS API server error - please try again later";
                throw new Error(errorMessage);
            }
        },

        async loadCasStructure(casNumber) {
            const loadId = ++this.structureLoadCounter;
            this.errorMessage = "";
            this.structureLoading = true;

            try {
                const casData = await this.fetchFromCAS(casNumber);
                const smiles = casData.smile || casData.canonicalSmile;

                if (!smiles) {
                    this.errorMessage = `No structural data available for CAS ${casNumber}`;
                    return;
                }

                if (loadId !== this.structureLoadCounter) {
                    return;
                }

                this.ensureStructureSearchEditor(() => {
                    if (loadId !== this.structureLoadCounter || !this.editor) {
                        return;
                    }

                    this.editor.setSmiles(smiles);
                    this.syncEditorHasStructure();
                });
            } catch (error) {
                if (loadId === this.structureLoadCounter) {
                    this.errorMessage = error.message;
                }
            } finally {
                if (loadId === this.structureLoadCounter) {
                    this.structureLoading = false;
                }
            }
        },

        handleDragOver(event) {
            event.preventDefault();
            this.isDragging = true;
        },

        handleDragLeave(event) {
            event.preventDefault();
            this.isDragging = false;
        },

        handleDrop(event) {
            event.preventDefault();
            this.isDragging = false;

            const files = Array.from(event.dataTransfer?.files ?? []);

            if (files.length > 0) {
                this.processFiles(files);
                return;
            }

            const text = event.dataTransfer?.getData("text/plain") ?? "";

            if (text.trim()) {
                this.importStructureInput(text);
            }
        },

        processFiles(files) {
            if (files.length === 0) {
                return;
            }

            const validFiles = files.filter((file) => {
                const extension = file.name.toLowerCase().split(".").pop();
                return ["mol", "sdf", "sd"].includes(extension);
            });

            if (validFiles.length === 0) {
                this.errorMessage = "Please drop a valid MOL or SDF file.";
                return;
            }

            const file = validFiles[0];
            const reader = new FileReader();

            reader.onload = (e) => {
                this.importStructureInput(e.target.result, file.name);
            };

            reader.onerror = () => {
                this.errorMessage = "Error reading file. Please try again.";
            };

            reader.readAsText(file);

            if (validFiles.length > 1) {
                this.errorMessage = `Only the first file (${file.name}) was loaded.`;
            }
        },
        toggleSummaryBar() {
            this.showSummary = !this.showSummary;
        },
        /**
         * True when pivot stores no numeric composition (explicit unknown or empty).
         *
         * @param {string|number|null|undefined} value
         */
        isCompositionPercentUnknown(value) {
            if (value === undefined || value === null) {
                return true;
            }
            const s = String(value).trim();
            if (s === "") {
                return true;
            }
            if (s.toLowerCase() === "unknown") {
                return true;
            }

            return false;
        },
        /**
         * Format composition percentage for display (supports fractional %).
         *
         * @param {string|number|null|undefined} value
         * @returns {string}
         */
        formatCompositionPercent(value) {
            const n = Number(value);
            if (!Number.isFinite(n)) {
                return value != null && value !== "" ? String(value) : "0";
            }
            if (Math.abs(n - Math.round(n)) < 1e-9) {
                return String(Math.round(n));
            }

            return n.toFixed(3).replace(/\.?0+$/, "");
        },
    },
};
</script>

<style scoped>
/* Blob animations */
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>
