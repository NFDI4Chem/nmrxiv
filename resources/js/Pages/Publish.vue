<template>
    <app-layout title="Publish Data">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-6">
                    <div class="py-4">
                        <div>
                            <span
                                class="ml-12 text-sm font-bold text-primary-600 group-hover:text-primary-800"
                                >Step 3 / 3 -

                                <span> Publish data </span>
                            </span>
                            <div
                                class="w-full sm:flex sm:items-center sm:justify-between"
                            >
                                <h3
                                    class="text-sm text-gray-700 uppercase font-bold tracking-widest"
                                >
                                    <Link
                                        class="ml-1 mr-2 inline-flex items-center px-2.5 py-1 text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                        :href="
                                            route('upload', {
                                                draft_id: draft.id,
                                                step: 2,
                                            })
                                        "
                                    >
                                        ←
                                    </Link>
                                    <span>
                                        <p class="inline">
                                            <b>{{ draft.name }}</b>
                                        </p>
                                    </span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div class="mb-10">
            <transition
                enter-active-class="transition ease-out duration-150"
                enter-from-class="opacity-0 translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-y-1"
            >
                <div
                    v-if="saveStatus !== 'idle'"
                    class="fixed right-4 top-20 z-50 flex items-center gap-2 rounded-full border bg-white/95 px-3 py-1.5 text-sm shadow-md backdrop-blur-sm"
                    :class="
                        saveStatus === 'error'
                            ? 'border-red-200 text-red-700'
                            : saveStatus === 'saved'
                            ? 'border-emerald-200 text-emerald-700'
                            : 'border-gray-200 text-gray-700'
                    "
                    role="status"
                    aria-live="polite"
                >
                    <svg
                        v-if="saveStatus === 'saving'"
                        class="h-3.5 w-3.5 animate-spin"
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
                    <svg
                        v-else-if="saveStatus === 'saved'"
                        class="h-3.5 w-3.5"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 011.42-1.42l2.79 2.79 6.79-6.79a1 1 0 011.42 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <svg
                        v-else
                        class="h-3.5 w-3.5"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span class="font-medium">
                        <span v-if="saveStatus === 'saving'">{{
                            saveStatusSavingLabel
                        }}</span>
                        <span v-else-if="saveStatus === 'saved'">{{
                            saveStatusSavedLabel
                        }}</span>
                        <span v-else>{{ saveStatusErrorLabel }}</span>
                    </span>
                </div>
            </transition>
            <div v-if="status == 'draft'">
                <div id="project-details" class="p-4">
                    <div class="p-8">
                        <div class="mb-6">
                            <p
                                id="publish-mode-heading"
                                class="mb-3 text-base font-semibold leading-snug text-gray-900 dark:text-gray-100"
                            >
                                How should this submission be published?
                            </p>
                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-start sm:gap-6 lg:gap-8"
                            >
                                <div class="shrink-0 flex flex-col gap-3">
                                    <div class="sm:hidden">
                                        <select
                                            id="publish-mode-tab-select"
                                            name="publish-mode"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                            aria-labelledby="publish-mode-heading"
                                            :value="
                                                publishForm.enableProjectMode
                                                    ? 'project'
                                                    : 'samples'
                                            "
                                            @change="
                                                onPublishModeSelect($event)
                                            "
                                        >
                                            <option value="project">
                                                Group as one publication
                                            </option>
                                            <option value="samples">
                                                Publish each sample on its own
                                            </option>
                                        </select>
                                    </div>
                                    <div
                                        class="hidden sm:flex sm:items-center sm:gap-4"
                                    >
                                        <div
                                            class="inline-flex rounded-lg bg-gray-100 p-1 ring-1 ring-inset ring-gray-200/80 dark:bg-gray-800 dark:ring-gray-700/80"
                                            role="tablist"
                                            aria-labelledby="publish-mode-heading"
                                        >
                                            <button
                                                type="button"
                                                role="tab"
                                                :aria-selected="
                                                    publishForm.enableProjectMode
                                                "
                                                :class="[
                                                    publishForm.enableProjectMode
                                                        ? 'bg-white text-gray-900 shadow-sm ring-1 ring-gray-200'
                                                        : 'text-gray-600 hover:bg-white/60 hover:text-gray-900',
                                                    'rounded-lg px-3 py-2.5 text-left text-xs font-medium leading-snug transition-colors sm:px-4 sm:text-sm',
                                                ]"
                                                @click="
                                                    setPublishProjectMode(true)
                                                "
                                            >
                                                Group as one publication
                                            </button>
                                            <button
                                                type="button"
                                                role="tab"
                                                :aria-selected="
                                                    !publishForm.enableProjectMode
                                                "
                                                :class="[
                                                    !publishForm.enableProjectMode
                                                        ? 'bg-white text-gray-900 shadow-sm ring-1 ring-gray-200'
                                                        : 'text-gray-600 hover:bg-white/60 hover:text-gray-900',
                                                    'rounded-lg px-3 py-2.5 text-left text-xs font-medium leading-snug transition-colors sm:px-4 sm:text-sm',
                                                ]"
                                                @click="
                                                    setPublishProjectMode(false)
                                                "
                                            >
                                                Publish each sample on its own
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <p
                                    class="text-sm text-gray-600 leading-relaxed sm:flex-1 sm:min-w-0 sm:pt-0.5"
                                >
                                    <span v-if="publishForm.enableProjectMode">
                                        <strong
                                            >Group as one publication</strong
                                        >
                                        puts your samples under one nmrXiv
                                        project with shared metadata and a
                                        canonical DOI — use this when samples
                                        belong to the same study or publication.
                                    </span>
                                    <span v-else>
                                        <strong
                                            >Publish each sample on its
                                            own</strong
                                        >
                                        creates a separate record for each
                                        sample instead of one grouped
                                        project—use this when measurements are
                                        unrelated or add samples to your
                                        spectral library.
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div>
                            <div class="p-4 bg-gray-100 rounded-md">
                                <div
                                    v-if="!publishForm.enableProjectMode"
                                    class="mb-3"
                                >
                                    <button
                                        type="button"
                                        class="flex w-full items-center justify-between gap-3 rounded-lg border border-gray-200 bg-white px-4 py-3 text-left shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                        :aria-expanded="samplesMetadataExpanded"
                                        aria-controls="publish-optional-metadata"
                                        @click="
                                            samplesMetadataExpanded =
                                                !samplesMetadataExpanded
                                        "
                                    >
                                        <span class="min-w-0">
                                            <span
                                                class="block text-sm font-medium text-gray-900"
                                                >Optional metadata</span
                                            >
                                            <span
                                                class="mt-0.5 block text-xs text-gray-500"
                                                >Keywords, organism, citations,
                                                and authors</span
                                            >
                                        </span>
                                        <ChevronDownIcon
                                            v-if="!samplesMetadataExpanded"
                                            class="h-5 w-5 shrink-0 text-gray-400"
                                            aria-hidden="true"
                                        />
                                        <ChevronUpIcon
                                            v-else
                                            class="h-5 w-5 shrink-0 text-gray-400"
                                            aria-hidden="true"
                                        />
                                    </button>
                                </div>
                                <div
                                    v-show="
                                        publishForm.enableProjectMode ||
                                        samplesMetadataExpanded
                                    "
                                    id="publish-optional-metadata"
                                >
                                    <div
                                        v-if="publishForm.enableProjectMode"
                                        id="project-name"
                                        class="mb-3"
                                    >
                                        <label
                                            for="project-name"
                                            class="block text-sm font-medium text-gray-500 after:content-['*'] after:ml-0.5 after:text-red-500"
                                        >
                                            Project Name
                                        </label>
                                        <div class="mt-1">
                                            <input
                                                v-model="publishForm.name"
                                                type="text"
                                                name="project-name"
                                                class="block w-full shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm border-gray-300 rounded-md"
                                                @blur="updateProject"
                                            />
                                        </div>
                                        <jet-input-error
                                            :message="publishForm.errors.name"
                                            class="mt-2"
                                        />
                                        <!-- Draft Warning Error Message -->
                                        <div
                                            v-if="
                                                hasDraftInName &&
                                                !draftWarningConfirmed
                                            "
                                            class="mt-2 flex items-center text-red-600 text-sm"
                                        >
                                            <svg
                                                class="w-4 h-4 mr-1.5 flex-shrink-0"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            <span
                                                >Invalid project name - contains
                                                "DRAFT" - please update the
                                                project name to publish</span
                                            >
                                        </div>
                                    </div>
                                    <div
                                        v-if="publishForm.enableProjectMode"
                                        id="project-desc"
                                        class="mb-3"
                                    >
                                        <label
                                            for="description"
                                            class="block text-sm font-medium text-gray-500 after:content-['*'] after:ml-0.5 after:text-red-500"
                                        >
                                            <span
                                                @click="
                                                    publishForm.description =
                                                        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore'
                                                "
                                                >Project Description
                                            </span>
                                        </label>
                                        <div class="mt-1">
                                            <textarea
                                                id="description"
                                                v-model="
                                                    publishForm.description
                                                "
                                                name="project-description"
                                                placeholder="Describe this project"
                                                rows="3"
                                                class="block w-full shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm border border-gray-300 rounded-md"
                                                @blur="updateProject"
                                            ></textarea>
                                        </div>
                                        <jet-input-error
                                            :message="
                                                publishForm.errors.description
                                            "
                                            class="mt-2"
                                        />
                                    </div>
                                    <div id="project-keywords" class="mb-3">
                                        <label
                                            for="description"
                                            class="block text-sm font-medium text-gray-500"
                                            :class="
                                                publishForm.enableProjectMode
                                                    ? `after:content-['*'] after:ml-0.5 after:text-red-500`
                                                    : `after:content-['(Optional)'] after:ml-0.5 after:text-gray-500`
                                            "
                                        >
                                            Keywords
                                        </label>
                                        <div>
                                            <vue-tags-input
                                                v-model="publishForm.tag"
                                                placeholder="Type a keyword or keywords separated by comma (,) and press enter"
                                                :separators="[';', ',']"
                                                max-width="100%"
                                                :tags="publishForm.tags"
                                                @blur="updateProject"
                                                @tags-changed="
                                                    onPublishKeywordsChanged
                                                "
                                            />
                                        </div>
                                        <jet-input-error
                                            :message="publishForm.errors.tags"
                                            class="mt-2"
                                        />
                                    </div>
                                    <div>
                                        <div id="project-organism" class="mb-3">
                                            <label
                                                for="description"
                                                class="block text-sm font-medium text-gray-500"
                                            >
                                                Organism (Optional)
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
                                                        :value="projectSpecies"
                                                        placeholder="Search species"
                                                        @change="
                                                            projectSpecies =
                                                                $event.detail[0]
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
                                                                projectSpecies
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
                                            <div class="mt-2">
                                                <div
                                                    v-for="(
                                                        species, $index
                                                    ) in publishForm.species"
                                                    :key="$index"
                                                    class="bg-gray-100 border text-gray-800 mb-0.5 inline-flex truncate break-words items-center px-3 py-2 rounded-full text-sm font-medium mr-1 bg-white"
                                                >
                                                    <ontology-term-annotation
                                                        :annotation="species"
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
                                    <div
                                        id="project-citations"
                                        class="mb-2 pb-4"
                                    >
                                        <div class="relative pl-2">
                                            <div
                                                class="absolute inset-0 flex items-center"
                                                aria-hidden="true"
                                            >
                                                <div
                                                    class="w-full border-t border-gray-300"
                                                ></div>
                                            </div>
                                            <div
                                                class="relative flex items-center"
                                            >
                                                <span
                                                    class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                                                    :class="
                                                        publishForm.enableProjectMode
                                                            ? `after:content-['*'] after:ml-0.5 after:text-red-500`
                                                            : `after:content-['(Optional)'] after:ml-0.5 after:text-gray-500`
                                                    "
                                                >
                                                    Citation
                                                </span>
                                            </div>
                                        </div>
                                        <dd
                                            class="mt-2 text-md text-gray-900 focus:pointer-events-auto"
                                        >
                                            <div
                                                v-if="!hasPublicationCitations"
                                                class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-10 text-center sm:px-10"
                                            >
                                                <DocumentTextIcon
                                                    class="mx-auto h-12 w-12 text-gray-300"
                                                    aria-hidden="true"
                                                />
                                                <h4
                                                    class="mt-4 text-sm font-semibold text-gray-900"
                                                >
                                                    No publication citations yet
                                                </h4>
                                                <p
                                                    class="mt-2 text-sm text-gray-500 max-w-md mx-auto leading-relaxed"
                                                >
                                                    Link the paper or preprint
                                                    this dataset supports.
                                                    Import metadata from a DOI
                                                    or enter details
                                                    manually—ideal before going
                                                    public or when your article
                                                    is still in review.
                                                </p>
                                                <button
                                                    type="button"
                                                    class="mt-6 inline-flex items-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                                    @click="
                                                        toggleManageCitation
                                                    "
                                                >
                                                    <PlusIcon
                                                        class="w-5 h-5 mr-2 -ml-0.5"
                                                        aria-hidden="true"
                                                    />
                                                    Add citation
                                                </button>
                                            </div>
                                            <div
                                                v-else
                                                class="mt-1 flex flex-col gap-3"
                                            >
                                                <citation-card
                                                    :citations="
                                                        project.citations
                                                    "
                                                    show-edit-delete
                                                    @edit="onCitationCardEdit"
                                                    @delete="
                                                        onCitationCardDelete
                                                    "
                                                />
                                                <div class="pt-1">
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                                        @click="
                                                            toggleManageCitation
                                                        "
                                                    >
                                                        <PlusIcon
                                                            class="w-5 h-5 mr-2 -ml-0.5"
                                                            aria-hidden="true"
                                                        />
                                                        Add citation
                                                    </button>
                                                </div>
                                            </div>
                                        </dd>
                                    </div>
                                    <div id="project-funding" class="mb-2 pb-4">
                                        <div class="relative pl-2">
                                            <div
                                                class="absolute inset-0 flex items-center"
                                                aria-hidden="true"
                                            >
                                                <div
                                                    class="w-full border-t border-gray-300"
                                                ></div>
                                            </div>
                                            <div
                                                class="relative flex items-center"
                                            >
                                                <span
                                                    class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500 after:content-['(Optional)'] after:ml-0.5 after:text-gray-500"
                                                >
                                                    Funding
                                                </span>
                                            </div>
                                        </div>
                                        <dd
                                            class="mt-2 text-md text-gray-900 focus:pointer-events-auto"
                                        >
                                            <div
                                                v-if="!hasFundingReferences"
                                                class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-10 text-center sm:px-10"
                                            >
                                                <DocumentTextIcon
                                                    class="mx-auto h-12 w-12 text-gray-300"
                                                    aria-hidden="true"
                                                />
                                                <h4
                                                    class="mt-4 text-sm font-semibold text-gray-900"
                                                >
                                                    No funding references yet
                                                </h4>
                                                <p
                                                    class="mt-2 text-sm text-gray-500 max-w-md mx-auto leading-relaxed"
                                                >
                                                    Declare third-party funding
                                                    such as DFG grants. This
                                                    metadata is included in your
                                                    DataCite DOI record.
                                                </p>
                                                <button
                                                    type="button"
                                                    class="mt-6 inline-flex items-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                                    @click="
                                                        toggleManageFundingReference
                                                    "
                                                >
                                                    <PlusIcon
                                                        class="w-5 h-5 mr-2 -ml-0.5"
                                                        aria-hidden="true"
                                                    />
                                                    Add funding reference
                                                </button>
                                            </div>
                                            <div
                                                v-else
                                                class="mt-1 flex flex-col gap-3"
                                            >
                                                <funding-reference-card
                                                    :funding-references="
                                                        project.funding_references ||
                                                        []
                                                    "
                                                    show-edit-delete
                                                    @edit="
                                                        onFundingReferenceCardEdit
                                                    "
                                                    @delete="
                                                        onFundingReferenceCardDelete
                                                    "
                                                />
                                                <div class="pt-1">
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                                        @click="
                                                            toggleManageFundingReference
                                                        "
                                                    >
                                                        <PlusIcon
                                                            class="w-5 h-5 mr-2 -ml-0.5"
                                                            aria-hidden="true"
                                                        />
                                                        Add funding reference
                                                    </button>
                                                </div>
                                            </div>
                                        </dd>
                                    </div>
                                    <div
                                        id="project-authors"
                                        class="mb-2 pt-4 pb-4"
                                    >
                                        <div class="relative pl-2">
                                            <div
                                                class="absolute inset-0 flex items-center"
                                                aria-hidden="true"
                                            >
                                                <div
                                                    class="w-full border-t border-gray-300"
                                                ></div>
                                            </div>
                                            <div
                                                class="relative flex items-center"
                                            >
                                                <span
                                                    class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                                                    :class="
                                                        publishForm.enableProjectMode
                                                            ? `after:content-['*'] after:ml-0.5 after:text-red-500`
                                                            : `after:content-['(Optional)'] after:ml-0.5 after:text-gray-500`
                                                    "
                                                >
                                                    Author
                                                </span>
                                            </div>
                                        </div>
                                        <dd class="mt-2 text-md text-gray-900">
                                            <div
                                                v-if="!hasPublicationAuthors"
                                                class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-10 text-center sm:px-10"
                                            >
                                                <UserGroupIcon
                                                    class="mx-auto h-12 w-12 text-gray-300"
                                                    aria-hidden="true"
                                                />
                                                <h4
                                                    class="mt-4 text-sm font-semibold text-gray-900"
                                                >
                                                    No authors listed yet
                                                </h4>
                                                <p
                                                    class="mt-2 text-sm text-gray-500 max-w-md mx-auto leading-relaxed"
                                                >
                                                    Credit everyone who
                                                    contributed to this
                                                    deposition—names,
                                                    affiliations, and ORCID iDs
                                                    when available. You can set
                                                    roles and reorder the list
                                                    after adding people.
                                                </p>
                                                <button
                                                    type="button"
                                                    class="mt-6 inline-flex items-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                                    @click="toggleManageAuthor"
                                                >
                                                    <PlusIcon
                                                        class="w-5 h-5 mr-2 -ml-0.5"
                                                        aria-hidden="true"
                                                    />
                                                    Add authors
                                                </button>
                                            </div>
                                            <div
                                                v-else
                                                class="mt-1 flex flex-col gap-3"
                                            >
                                                <p
                                                    v-if="
                                                        orderedAuthors.length >
                                                        1
                                                    "
                                                    class="text-xs text-gray-500"
                                                >
                                                    Use the grip to drag authors
                                                    into the order they should
                                                    appear in the publication.
                                                </p>
                                                <draggable
                                                    v-model="orderedAuthors"
                                                    item-key="id"
                                                    handle=".publish-author-drag-handle"
                                                    :animation="200"
                                                    ghost-class="opacity-60"
                                                    chosen-class="publish-author-draggable-chosen"
                                                    class="flex flex-col gap-3"
                                                    :disabled="
                                                        authorOrderSaveInProgress
                                                    "
                                                    @start="
                                                        onPublishAuthorDragStart
                                                    "
                                                    @end="
                                                        onPublishAuthorDragEnd
                                                    "
                                                >
                                                    <template
                                                        #item="{ element }"
                                                    >
                                                        <div
                                                            class="flex items-stretch overflow-hidden rounded-lg border border-gray-300 bg-white shadow-sm transition-all duration-200 hover:border-gray-400 hover:shadow-md focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500"
                                                        >
                                                            <button
                                                                type="button"
                                                                class="publish-author-drag-handle flex shrink-0 cursor-grab items-center justify-center self-stretch border-0 border-r border-gray-200 bg-gray-50 px-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 active:cursor-grabbing sm:px-2.5"
                                                                aria-label="Drag to reorder authors"
                                                            >
                                                                <Bars3Icon
                                                                    class="h-5 w-5"
                                                                    aria-hidden="true"
                                                                />
                                                            </button>
                                                            <author-card
                                                                :authors="[
                                                                    element,
                                                                ]"
                                                                flush
                                                                show-edit-delete
                                                                @edit="
                                                                    onAuthorCardEdit
                                                                "
                                                                @delete="
                                                                    onAuthorCardDelete
                                                                "
                                                            />
                                                        </div>
                                                    </template>
                                                </draggable>
                                                <div class="pt-1">
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                                        @click="
                                                            toggleManageAuthor
                                                        "
                                                    >
                                                        <PlusIcon
                                                            class="h-5 w-5 mr-2 -ml-0.5"
                                                            aria-hidden="true"
                                                        />
                                                        Add author
                                                    </button>
                                                </div>
                                            </div>
                                        </dd>
                                    </div>
                                    <div
                                        v-if="publishForm.enableProjectMode"
                                        class="px-2"
                                    >
                                        <div
                                            class="relative flex items-center justify-between"
                                        >
                                            <span
                                                class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500 after:content-['(Optional)'] after:ml-0.5 after:text-gray-500"
                                            >
                                                Project Image
                                            </span>
                                        </div>
                                        <input
                                            ref="photo"
                                            type="file"
                                            class="hidden"
                                            accept="image/jpeg,image/png,image/gif,image/webp"
                                            @change="updatePhotoPreview"
                                        />

                                        <div
                                            v-show="!photoPreview"
                                            class="mt-2"
                                        >
                                            <img
                                                :src="
                                                    project.project_photo_url
                                                        ? project.project_photo_url
                                                        : 'https://placehold.co/400x200'
                                                "
                                                :alt="project.name"
                                                class="h-24 w-72 rounded-md object-cover"
                                            />
                                        </div>

                                        <div v-show="photoPreview" class="mt-2">
                                            <span
                                                class="block h-24 w-72 rounded"
                                                :style="
                                                    'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' +
                                                    photoPreview +
                                                    '\');'
                                                "
                                            >
                                            </span>
                                        </div>

                                        <jet-secondary-button
                                            class="mt-2 mr-2"
                                            type="button"
                                            @click.prevent="selectNewPhoto"
                                        >
                                            Select A New Photo
                                        </jet-secondary-button>

                                        <!-- <jet-secondary-button
                                                        type="button"
                                                        class="mt-2"
                                                        @click.prevent="deletePhoto"
                                                        v-if="project.project_photo_path"
                                                    >
                                                        Remove Photo
                                                    </jet-secondary-button> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label
                                class="block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"
                            >
                                {{
                                    pluralize("SAMPLE", project.studies.length)
                                }}
                                ({{ project.studies.length }})
                            </label>
                            <div>
                                <div
                                    class="mt-4 mb-8 mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-4 lg:max-w-7xl"
                                >
                                    <div
                                        v-for="study in project.studies"
                                        :key="study.uuid"
                                    >
                                        <study-info :study="study" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 p-4 bg-gray-100 rounded-md">
                            <div
                                v-if="publishForm.enableProjectMode"
                                id="release"
                            >
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Release
                                </label>
                                <div class="sm:hidden mt-2">
                                    <label
                                        for="release-visibility-select"
                                        class="sr-only"
                                        >Choose public release or embargo</label
                                    >
                                    <select
                                        id="release-visibility-select"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :value="releaseVisibility"
                                        @change="
                                            setReleaseVisibility(
                                                $event.target.value
                                            )
                                        "
                                    >
                                        <option value="public">Public</option>
                                        <option value="embargo">Embargo</option>
                                    </select>
                                </div>
                                <div
                                    class="hidden sm:flex sm:items-center sm:gap-4 mt-2"
                                >
                                    <div
                                        class="inline-flex rounded-lg bg-gray-100 p-1 ring-1 ring-inset ring-gray-200/80"
                                        role="tablist"
                                        aria-label="Release as public or under embargo"
                                    >
                                        <button
                                            type="button"
                                            role="tab"
                                            :aria-selected="
                                                releaseVisibility === 'public'
                                            "
                                            :class="[
                                                releaseVisibility === 'public'
                                                    ? 'bg-white text-gray-900 shadow-sm ring-1 ring-gray-200'
                                                    : 'text-gray-600 hover:bg-white/60 hover:text-gray-900',
                                                'rounded-lg px-5 py-2.5 text-sm font-medium transition-colors',
                                            ]"
                                            @click="
                                                setReleaseVisibility('public')
                                            "
                                        >
                                            Public
                                        </button>
                                        <button
                                            type="button"
                                            role="tab"
                                            :aria-selected="
                                                releaseVisibility === 'embargo'
                                            "
                                            :class="[
                                                releaseVisibility === 'embargo'
                                                    ? 'bg-white text-gray-900 shadow-sm ring-1 ring-gray-200'
                                                    : 'text-gray-600 hover:bg-white/60 hover:text-gray-900',
                                                'rounded-lg px-5 py-2.5 text-sm font-medium transition-colors',
                                            ]"
                                            @click="
                                                setReleaseVisibility('embargo')
                                            "
                                        >
                                            Embargo
                                        </button>
                                    </div>
                                </div>
                                <div
                                    v-if="releaseVisibility === 'embargo'"
                                    class="mt-3"
                                >
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Embargo until
                                    </label>
                                    <Datepicker
                                        v-model="publishForm.release_date"
                                        :min-date="new Date()"
                                        :format="customDateFormat"
                                        :preview-format="customDateFormat"
                                        @update:model-value="updateProject"
                                    ></Datepicker>
                                </div>
                                <div
                                    class="mt-3 bg-gray-50 border border-gray-200 rounded-lg p-4"
                                >
                                    <!-- Immediate Publication -->
                                    <div
                                        v-if="isImmediatePublication"
                                        class="space-y-3"
                                    >
                                        <p class="text-sm text-gray-600">
                                            Your data becomes publicly
                                            accessible right away with a DOI.
                                            Choose
                                            <strong>Embargo</strong> above if
                                            you want a scheduled release for
                                            peer review. Need help?
                                            <a
                                                href="https://docs.nmrxiv.org/submission-guides/submission-process.html#step-3-publish-data"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="font-medium text-primary-600 underline decoration-primary-300 underline-offset-2 hover:text-primary-800 hover:decoration-primary-500"
                                                >Read more</a
                                            >
                                        </p>
                                    </div>

                                    <!-- Scheduled Release (Embargo) -->
                                    <div v-else class="space-y-3">
                                        <p class="text-sm text-gray-600">
                                            <strong
                                                >Scheduled Release
                                                (Embargo):</strong
                                            >
                                            You have selected a future date for
                                            publication. Your data remains
                                            private until then.
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            You can:
                                        </p>
                                        <ul
                                            class="ml-4 text-sm text-gray-500 space-y-1"
                                        >
                                            <li class="flex items-start">
                                                <span
                                                    class="inline-block w-1.5 h-1.5 bg-gray-400 rounded-full mt-2 mr-2 flex-shrink-0"
                                                ></span>
                                                <span
                                                    >Share reviewer access links
                                                    for confidential peer
                                                    review</span
                                                >
                                            </li>
                                            <li class="flex items-start">
                                                <span
                                                    class="inline-block w-1.5 h-1.5 bg-gray-400 rounded-full mt-2 mr-2 flex-shrink-0"
                                                ></span>
                                                <span
                                                    >Receive advance
                                                    notifications before
                                                    publication</span
                                                >
                                            </li>
                                            <li class="flex items-start">
                                                <span
                                                    class="inline-block w-1.5 h-1.5 bg-gray-400 rounded-full mt-2 mr-2 flex-shrink-0"
                                                ></span>
                                                <span
                                                    >Modify the release date or
                                                    publish instantly from your
                                                    dashboard</span
                                                >
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div v-else id="release">
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Release
                                </label>
                                <p class="mt-2 text-sm text-gray-600">
                                    Publishing as individual samples always uses
                                    immediate public release. Embargo scheduling
                                    is only available when you publish as a
                                    project.
                                </p>
                            </div>
                            <div class="mt-6">
                                <!--License -->
                                <div id="license" class="mb-0">
                                    <div>
                                        <div
                                            class="mt-0 grid grid-cols-1 gap-x-4 sm:grid-cols-1"
                                        >
                                            <div v-if="licenses">
                                                <span
                                                    class="float-right text-xs cursor-pointer hover:text-primary-700 mt-2"
                                                >
                                                    <a
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        href="https://docs.nmrxiv.org/submission-guides/licenses"
                                                        class="text-primary-600 hover:text-primary-800 underline"
                                                        >How to choose the right
                                                        license?</a
                                                    >
                                                </span>
                                                <select-rich
                                                    v-model:selected="license"
                                                    label="License"
                                                    :items="licenses"
                                                    searchable
                                                    placeholder="Search a license"
                                                    @update:selected="
                                                        updateProject
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5">
                            <h3 class="text-lg font-bold text-gray-400">
                                Terms & Conditions
                            </h3>

                            <div class="mt-3">
                                <div class="ml-2">
                                    <div class="flex items-top">
                                        <input
                                            id="conditions"
                                            v-model="publishForm.conditions"
                                            type="checkbox"
                                            class="rounded mt-1 border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                            name="conditions"
                                        />
                                        <div class="ml-2 text-sm">
                                            <span
                                                v-if="
                                                    publishForm.enableProjectMode
                                                "
                                            >
                                                I understand that publishing
                                                makes all underlying data
                                                publicly available on the nmrXiv
                                                platform after the set release
                                                date.
                                            </span>
                                            <span v-else>
                                                I understand that publishing
                                                makes all underlying data
                                                publicly available on the nmrXiv
                                                platform after the set release
                                                date.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="ml-2">
                                    <div class="flex items-center">
                                        <input
                                            id="terms"
                                            v-model="publishForm.terms"
                                            type="checkbox"
                                            class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                            name="terms"
                                        />
                                        <div class="ml-2 text-sm">
                                            I agree to the
                                            <a
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                :href="route('terms.show')"
                                                class="text-sm font-medium text-primary-700 underline decoration-primary-300 underline-offset-2 hover:text-primary-900 hover:decoration-primary-500"
                                                >Terms of Service</a
                                            >
                                            and
                                            <a
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                :href="route('policy.show')"
                                                class="text-sm font-medium text-primary-700 underline decoration-primary-300 underline-offset-2 hover:text-primary-900 hover:decoration-primary-500"
                                                >Privacy Policy</a
                                            >
                                            and hereby also grant nmrXiv
                                            permissions to distribute the
                                            datasets (and meta-data) under the
                                            specified license.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div id="publish-details">&nbsp;</div>
                        <div class="px-8 pb-8 pt-0">
                            <jet-success-button
                                v-if="isReleasedToday()"
                                type="button"
                                :class="[
                                    !canPublish
                                        ? 'bg-gray-200 cursor-not-allowed'
                                        : 'bg-green-600 hover:bg-green-700',
                                    'ml-2',
                                ]"
                                :disabled="!canPublish"
                                @click="handlePublishClick"
                            >
                                Publish Now
                            </jet-success-button>
                            <jet-success-button
                                v-else
                                type="button"
                                :class="[
                                    !canPublish
                                        ? 'bg-gray-200 cursor-not-allowed'
                                        : 'bg-green-600 hover:bg-green-700',
                                    'inline-flex w-full justify-center rounded-md border border-transparent px-4 py-2 text-base font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:w-auto sm:text-sm',
                                ]"
                                :disabled="!canPublish"
                                @click="handlePublishClick"
                            >
                                Publish with Embargo
                            </jet-success-button>
                            <Link
                                type="button"
                                class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                :href="route('dashboard')"
                            >
                                NOT RIGHT YET
                            </Link>
                        </div>
                        <div v-if="errors">
                            <div class="rounded-md bg-red-50 p-4 mx-4 mb-4">
                                <div>
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <!-- Heroicon name: mini/x-circle -->
                                            <svg
                                                class="h-5 w-5 text-red-400"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3
                                                class="text-sm font-medium text-red-800"
                                            >
                                                Error publishing your project
                                            </h3>
                                            <div
                                                class="mt-2 text-sm text-red-700"
                                            >
                                                <ul
                                                    role="list"
                                                    class="list-disc space-y-1 pl-5"
                                                >
                                                    <li>
                                                        {{ errors }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="validation" class="mt-6">
                                        <Validation
                                            :project="project"
                                            :validation="validation"
                                            :draft="draft.id"
                                        ></Validation>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mx-4">
                        <div
                            class="flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700"
                        >
                            <b>Whats next?</b>
                            <div>
                                <p>
                                    Upon clicking publish, your project is
                                    submitted to our queue system for automatic
                                    processing. Once successfully processed,
                                    your data is assigned with stable
                                    identifiers, and DOIs are generated. You
                                    will receive an email with citation details
                                    and other helpful information to share your
                                    datasets.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div v-if="project">
                        <manage-author
                            ref="manageAuthorElement"
                            :project="project"
                        />
                        <manage-citation
                            ref="manageCitationElement"
                            :project="project"
                        />
                        <manage-funding-reference
                            ref="manageFundingReferenceElement"
                            :project="project"
                        />
                    </div>
                </div>
            </div>
            <div
                v-else
                class="mt-24 mx-auto max-w-3xl transform overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all"
            >
                <div class="py-16">
                    <div class="text-center">
                        <div
                            class="m-3 relative clear-both border-dotted border-2 border-gray-300 rounded-lg"
                        >
                            <span
                                class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm rounded-md text-primary-600 bg-white transition ease-in-out duration-150 cursor-not-allowed"
                                disabled=""
                                ><h1
                                    class="capitalize text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl"
                                >
                                    {{ status }}
                                </h1></span
                            >
                        </div>
                        <Link
                            type="button"
                            :href="route('dashboard')"
                            class="inline-flex items-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                        >
                            Go to Dashboard
                        </Link>
                    </div>
                </div>
                <div class="w-full">
                    <div
                        class="flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700"
                    >
                        <b>Whats next?</b>
                        <div>
                            <p>
                                Please allow some time to process your
                                submission. You will recieve an email once your
                                submission is processed. Upon publishing you
                                will also receive an email with citation details
                                and other helpful information to share your
                                datasets.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Single Sample Modal -->
            <jet-confirmation-modal
                :show="showSingleSampleModal"
                max-width="5xl"
                @close="showSingleSampleModal = false"
            >
                <template #title> Single sample detected in project </template>
                <template #content>
                    <div class="space-y-3">
                        <p class="text-sm text-gray-600">
                            Your project contains only one sample. In nmrXiv,
                            single samples can be published directly without
                            creating a project. Projects are intended to group
                            multiple related samples, for example those
                            belonging to the same study or publication.
                            <a
                                href="https://docs.nmrxiv.org/submission-guides/submission-process.html#step-3-publish-data"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="font-medium text-primary-600 underline decoration-primary-300 underline-offset-2 hover:text-primary-800 hover:decoration-primary-500"
                                >Click to read more.</a
                            >
                        </p>
                        <p class="text-sm text-gray-600">
                            How would you like to proceed?
                        </p>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button
                        @click="showSingleSampleModal = false"
                    >
                        Cancel
                    </jet-secondary-button>
                    <jet-secondary-button @click="publishAsSample">
                        Publish each sample on its own
                    </jet-secondary-button>
                    <jet-success-button @click="publishAsProject">
                        Group as one publication
                    </jet-success-button>
                </template>
            </jet-confirmation-modal>

            <!-- Draft Warning Modal -->
            <jet-confirmation-modal
                :show="showDraftWarningModal"
                @close="showDraftWarningModal = false"
            >
                <template #title>
                    <div class="flex items-center">
                        <svg
                            class="w-6 h-6 text-amber-500 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"
                            ></path>
                        </svg>
                        Project Name Contains "DRAFT"
                    </div>
                </template>
                <template #content>
                    <div class="space-y-3">
                        <p class="text-sm text-gray-600">
                            Your project name contains the word "DRAFT", which
                            suggests this might be a work-in-progress version.
                        </p>
                        <div
                            class="bg-amber-50 border border-amber-200 rounded-md p-3"
                        >
                            <p class="text-sm text-amber-800">
                                <strong>Current name:</strong>
                                {{ publishForm.name }}
                            </p>
                        </div>
                        <p class="text-sm text-gray-600">
                            Are you sure you want to proceed with publishing
                            this project with the current name?
                        </p>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button
                        @click="showDraftWarningModal = false"
                    >
                        Cancel
                    </jet-secondary-button>
                    <jet-success-button @click="confirmDraftWarning">
                        Yes, Proceed
                    </jet-success-button>
                </template>
            </jet-confirmation-modal>

            <jet-confirmation-modal
                :show="showPublishConfirmationModal"
                @close="showPublishConfirmationModal = false"
            >
                <template #title> </template>
                <template #content>
                    <div v-if="isReleasedToday()" class="text-sm text-gray-600">
                        <span v-if="publishForm.enableProjectMode">
                            Once the data is published you will no longer be
                            able to change the data uploaded! If published as a
                            project, you may add more samples (spectra) to the
                            project later.
                        </span>
                        <span v-else>
                            Once the data is published you will no longer be
                            able to change the data uploaded! These samples will
                            be published as individual records.
                        </span>
                    </div>
                    <div v-else class="text-sm text-gray-600">
                        <span v-if="publishForm.enableProjectMode">
                            Opting for an Embargo publication grants your
                            project a DOI, yet it stays private exclusively for
                            you. You have the option to share the project with
                            others and can adjust the release date or promptly
                            make it public through the project's dashboard view.
                            But once the data is published you will no longer be
                            able to change the data uploaded! If published as a
                            project, you may add more samples (spectra) to the
                            project later if desired.
                            <a
                                href="https://docs.nmrxiv.org/submission-guides/embargo.html"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="font-medium text-primary-600 underline decoration-primary-300 underline-offset-2 hover:text-primary-800 hover:decoration-primary-500"
                                >Learn more</a
                            >
                        </span>
                        <span v-else>
                            Individual samples publish with immediate public
                            release when processing completes. Uploaded content
                            can no longer be edited after publication.
                        </span>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button
                        @click="showPublishConfirmationModal = false"
                    >
                        Cancel
                    </jet-secondary-button>
                    <jet-success-button
                        v-if="isReleasedToday()"
                        @click="publish"
                    >
                        Publish Now
                    </jet-success-button>
                    <jet-success-button v-else @click="publish">
                        Publish with Embargo
                    </jet-success-button>
                </template>
            </jet-confirmation-modal>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import JetInputError from "@/Jetstream/InputError.vue";
import axios from "axios";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import VueTagsInput from "@sipec/vue3-tags-input";
import { ref } from "vue";
import Validation from "@/Shared/Validation.vue";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import ManageAuthor from "@/Shared/ManageAuthor.vue";
import ManageCitation from "@/Shared/ManageCitation.vue";
import ManageFundingReference from "@/Shared/ManageFundingReference.vue";
import AuthorCard from "@/Shared/AuthorCard.vue";
import StudyInfo from "@/Shared/StudyInfo.vue";
import SelectRich from "@/Shared/SelectRich.vue";
import CitationCard from "@/Shared/CitationCard.vue";
import FundingReferenceCard from "@/Shared/FundingReferenceCard.vue";
import { PlusIcon } from "@heroicons/vue/24/solid";
import {
    Bars3Icon,
    ChevronDownIcon,
    ChevronUpIcon,
    DocumentTextIcon,
    UserGroupIcon,
} from "@heroicons/vue/24/outline";
import Draggable from "vuedraggable";
import "@/lib/ontology-elements";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetSuccessButton from "@/Jetstream/SuccessButton.vue";

export default {
    components: {
        ManageAuthor,
        AuthorCard,
        ManageCitation,
        ManageFundingReference,
        Datepicker,
        VueTagsInput,
        SelectRich,
        AppLayout,
        Link,
        JetInputError,
        JetSecondaryButton,
        JetSuccessButton,
        JetConfirmationModal,
        PlusIcon,
        DocumentTextIcon,
        UserGroupIcon,
        Validation,
        StudyInfo,
        CitationCard,
        FundingReferenceCard,
        Draggable,
        Bars3Icon,
        ChevronDownIcon,
        ChevronUpIcon,
    },
    props: ["user", "team", "project", "teamRole", "draft"],

    setup() {
        const manageAuthorElement = ref(null);
        const manageCitationElement = ref(null);
        const manageFundingReferenceElement = ref(null);
        return {
            manageAuthorElement,
            manageCitationElement,
            manageFundingReferenceElement,
        };
    },

    data() {
        return {
            publishForm: this.$inertia.form({
                _method: "PUT",
                suppress_project_updated_flash: true,
                name: "",
                description: "",
                error_message: null,
                tags: [],
                tag: "",
                tags_array: [],
                project_tags_updated: true,
                project_species_updated: true,
                owner_id: null,
                species: [],
                photo: null,
                conditions: false,
                license_id: null,
                terms: false,
                enableProjectMode: false,
                release_date: this.setReleaseDate(),
            }),
            licenses: null,
            license: null,
            returnUrl: route("dashboard"),
            errors: null,
            projectSpecies: "",
            status: "draft",
            validation: null,
            showPublishConfirmationModal: false,
            showDraftWarningModal: false,
            showSingleSampleModal: false,
            draftWarningConfirmed: false,
            photoPreview: null,
            /**
             * Status of background autosaves driven by blur/change events.
             * One of: 'idle' | 'saving' | 'saved' | 'error'.
             */
            saveStatus: "idle",
            /** 'default' | 'authorOrder' — drives copy in the save-status pill */
            saveStatusVariant: "default",
            saveStatusTimer: null,
            /** Local list for drag-and-drop author order on this page */
            orderedAuthors: [],
            /** Author ids in order when a drag started (to detect real changes) */
            publishAuthorsOrderBeforeDrag: [],
            authorOrderSaveInProgress: false,
            /** 'public' = immediate release; 'embargo' = date picker shown */
            releaseVisibility: "public",
            /** When publishing as samples, optional metadata starts collapsed */
            samplesMetadataExpanded: false,
        };
    },
    computed: {
        url() {
            return String(this.$page.props.url);
        },
        getMax() {
            if (this.selectedStudy) {
                let totalCount = 0;
                this.selectedStudy.sample.molecules.forEach((mol) => {
                    totalCount += parseInt(mol.pivot.percentage_composition);
                });
                return 100 - totalCount;
            } else {
                return 100;
            }
        },
        primed() {
            return this.$page.props.auth.user?.primed;
        },
        currentTab() {
            return this.tabs.find((t) => t.current);
        },
        isImmediatePublication() {
            if (this.releaseVisibility === "public") {
                return true;
            }
            if (!this.publishForm.release_date) return true;
            const today = new Date();
            const releaseDate = new Date(this.publishForm.release_date);
            // Reset time to compare only dates
            today.setHours(0, 0, 0, 0);
            releaseDate.setHours(0, 0, 0, 0);
            return releaseDate.getTime() === today.getTime();
        },
        hasDraftInName() {
            if (!this.publishForm.enableProjectMode) {
                return false;
            }

            return (
                this.publishForm.name &&
                this.publishForm.name.toLowerCase().includes("draft")
            );
        },
        canPublish() {
            return (
                this.publishForm.terms &&
                this.publishForm.conditions &&
                !this.publishForm.processing &&
                (!this.hasDraftInName || this.draftWarningConfirmed)
            );
        },
        hasPublicationCitations() {
            return (
                Array.isArray(this.project?.citations) &&
                this.project.citations.length > 0
            );
        },
        hasFundingReferences() {
            return (
                Array.isArray(this.project?.funding_references) &&
                this.project.funding_references.length > 0
            );
        },
        hasPublicationAuthors() {
            return (
                Array.isArray(this.project?.authors) &&
                this.project.authors.length > 0
            );
        },
        saveStatusSavingLabel() {
            return this.saveStatusVariant === "authorOrder"
                ? "Updating author order…"
                : "Saving…";
        },
        saveStatusSavedLabel() {
            return this.saveStatusVariant === "authorOrder"
                ? "Author order updated"
                : "Saved";
        },
        saveStatusErrorLabel() {
            return this.saveStatusVariant === "authorOrder"
                ? "Couldn't update author order"
                : "Couldn't save changes";
        },
    },
    watch: {
        "project.authors": {
            deep: true,
            handler() {
                this.syncOrderedAuthorsFromProject();
            },
        },
        "publishForm.enableProjectMode"(enabled) {
            if (!enabled) {
                this.samplesMetadataExpanded = false;
                this.applySampleModeReleaseSchedule();
            }
        },
    },
    beforeUnmount() {
        if (this.saveStatusTimer) {
            clearTimeout(this.saveStatusTimer);
            this.saveStatusTimer = null;
        }
    },
    created() {
        this.releaseVisibility = this.computeInitialReleaseVisibility();
        if (this.releaseVisibility === "public") {
            const d = new Date();
            d.setHours(0, 0, 0, 0);
            this.publishForm.release_date = d;
        }
        this.syncOrderedAuthorsFromProject();
    },

    mounted() {
        if (this.draft) {
            this.publishForm.name = this.project.name
                ? this.project.name
                : this.draft.name;
            this.publishForm.enableProjectMode = this.draft.project_enabled;
            this.publishForm.description = this.project.description;
            let tags = [];
            this.project.tags.forEach((t) => {
                tags.push({
                    text: t.name["en"],
                });
            });
            this.publishForm.tags = tags;
            this.license = this.project.license;
            this.status =
                this.project.status && this.project.status != ""
                    ? this.project.status
                    : "draft";
            this.publishForm.species = JSON.parse(this.project.species)
                ? JSON.parse(this.project.species)
                : [];
        }
        this.loadLicenses();

        this.applySampleModeReleaseSchedule();

        this.$nextTick(() => {
            const urlSearchParams = new URLSearchParams(window.location.search);
            const params = Object.fromEntries(urlSearchParams.entries());
            let edit = params["edit"];

            if (
                !this.publishForm.enableProjectMode &&
                ["citation", "keywords", "organism", "authors"].includes(edit)
            ) {
                this.samplesMetadataExpanded = true;
            }

            if (edit == "citation") {
                this.$nextTick(() => {
                    const el = document.getElementById("project-citations");
                    if (el) {
                        this.scrollTo(el);
                    }
                });
                this.toggleManageCitation();
            } else if (edit == "title") {
                this.scrollTo(document.getElementById("project-name"));
            } else if (edit == "description") {
                this.scrollTo(document.getElementById("project-desc"));
            } else if (edit == "keywords") {
                this.scrollTo(document.getElementById("project-keywords"));
            } else if (edit == "organism") {
                this.scrollTo(document.getElementById("project-organism"));
            } else if (edit == "license") {
                this.scrollTo(document.getElementById("license"));
            } else if (edit == "release") {
                this.scrollTo(document.getElementById("release"));
            } else if (edit == "authors") {
                this.$nextTick(() => {
                    const el = document.getElementById("project-authors");
                    if (el) {
                        this.scrollTo(el);
                    }
                });
                this.toggleManageAuthor();
            }
        });
    },
    methods: {
        setPublishProjectMode(enabled) {
            if (this.publishForm.enableProjectMode === enabled) {
                return;
            }
            this.publishForm.enableProjectMode = enabled;
            this.updateDraft();
        },
        onPublishModeSelect(event) {
            const value = event.target.value;
            this.setPublishProjectMode(value === "project");
        },
        selectNewPhoto() {
            this.$refs.photo.click();
        },
        updatePhotoPreview() {
            const photo = this.$refs.photo.files[0];

            if (!photo) return;

            const reader = new FileReader();

            reader.onload = (e) => {
                this.photoPreview = e.target.result;
            };

            reader.readAsDataURL(photo);
            this.updateProject();
        },
        scrollTo(element) {
            this.$nextTick(() => {
                element.classList.add("shake");
                setTimeout(function () {
                    element.classList.remove("shake");
                }, 2500);
            });
        },
        setSaveStatus(status, variant = "default") {
            if (this.saveStatusTimer) {
                clearTimeout(this.saveStatusTimer);
                this.saveStatusTimer = null;
            }
            this.saveStatusVariant = variant;
            this.saveStatus = status;
            if (status === "saved" || status === "error") {
                this.saveStatusTimer = setTimeout(() => {
                    this.saveStatus = "idle";
                    this.saveStatusVariant = "default";
                }, 2500);
            }
        },
        updateDraft() {
            this.setSaveStatus("saving");
            axios
                .put(route("dashboard.draft.update", this.draft.id), {
                    project_enabled: this.publishForm.enableProjectMode ? 1 : 0,
                })
                .then(() => this.setSaveStatus("saved"))
                .catch(() => this.setSaveStatus("error"));
        },
        updateProject(callbacks = {}) {
            const { onSuccess = null, onError = null } = callbacks;

            this.draftWarningConfirmed = false;

            if (this.$refs.photo) {
                this.publishForm.photo = this.$refs.photo.files[0];
            }

            if (this.publishForm.tag && this.publishForm.tag != "") {
                this.publishForm.tag.split(",").forEach((_t) => {
                    let exists = false;
                    this.publishForm.tags.forEach((t) => {
                        if (t.text == _t) {
                            exists = true;
                        }
                    });
                    if (!exists) {
                        this.publishForm.tags.push({
                            text: _t,
                        });
                    }
                });
                this.publishForm.tag = "";
            }
            this.loadingStep = true;

            this.publishForm.license_id = this.license ? this.license.id : null;
            this.publishForm.owner_id = this.project.owner_id;
            this.publishForm.tags_array = this.publishForm.tags
                ? this.publishForm.tags.map((a) => a.text)
                : [];

            this.setSaveStatus("saving");
            this.publishForm.post(
                route("dashboard.project.update", this.project.id),
                {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        this.setSaveStatus("saved");
                        if (onSuccess) {
                            onSuccess();
                        }
                    },
                    onError: () => {
                        this.setSaveStatus("error");
                        if (onError) {
                            onError();
                        }
                    },
                }
            );
        },
        onPublishKeywordsChanged(newTags) {
            const list = Array.isArray(newTags) ? newTags : [];
            this.publishForm.tags = list.map((t) =>
                typeof t === "object" && t !== null
                    ? { ...t }
                    : { text: String(t) }
            );
            this.$nextTick(() => {
                this.updateProject();
            });
        },
        updateSpecies(species) {
            if (species && species != "") {
                this.publishForm.species.push(species);
                this.projectSpecies = "";
                this.updateProject();
            }
        },
        removeSpecies(index) {
            if (index > -1) {
                this.publishForm.species.splice(index, 1);
                this.updateProject();
            }
        },
        getTarget(id) {
            var target = null;
            if (id) {
                target = "_blank";
            }
            return target;
        },
        getCitationLink(doi) {
            var link = "#";
            if (doi) {
                link = "https://doi.org/" + doi;
            }
            return link;
        },
        setReleaseDate() {
            if (!this.project.release_date) {
                var current_date = new Date();
                var relase_date = new Date();
                relase_date.setDate(current_date.getDate());
                return relase_date;
            } else {
                return this.project.release_date;
            }
        },
        computeInitialReleaseVisibility() {
            if (!this.project.release_date) {
                return "public";
            }
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const release = new Date(this.project.release_date);
            release.setHours(0, 0, 0, 0);
            return release.getTime() > today.getTime() ? "embargo" : "public";
        },
        setReleaseVisibility(mode) {
            if (!this.publishForm.enableProjectMode) {
                return;
            }
            if (this.releaseVisibility === mode) {
                return;
            }
            this.releaseVisibility = mode;
            if (mode === "public") {
                const d = new Date();
                d.setHours(0, 0, 0, 0);
                this.publishForm.release_date = d;
            } else {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const release = this.publishForm.release_date
                    ? new Date(this.publishForm.release_date)
                    : new Date();
                release.setHours(0, 0, 0, 0);
                if (release.getTime() <= today.getTime()) {
                    const tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    this.publishForm.release_date = tomorrow;
                }
            }
            this.updateProject();
        },
        loadLicenses() {
            if (this.$page.props.licenses) {
                this.licenses = this.$page.props.licenses;
            } else {
                axios.get(route("licenses")).then((res) => {
                    this.licenses = res.data;
                    this.$page.props.licenses = res.data;
                });
            }
        },
        getSVGString(molecule) {
            if (molecule.MOL) {
                let mol = OCL.Molecule.fromMolfile(
                    "\n  " + molecule.MOL.replaceAll('"', "")
                );
                return mol.toSVG(200, 200);
            }
        },
        toggleManageAuthor() {
            this.manageAuthorElement.toggleDialog();
        },
        toggleManageCitation() {
            this.manageCitationElement.toggleDialog();
        },
        toggleManageFundingReference() {
            this.manageFundingReferenceElement.toggleDialog();
        },
        onCitationCardEdit(citation) {
            const modal = this.manageCitationElement;
            if (!modal.showDialog) {
                modal.toggleDialog();
            }
            this.$nextTick(() => {
                modal.edit(citation);
            });
        },
        onCitationCardDelete(citation) {
            this.manageCitationElement.confirmDeletion(citation);
        },
        onFundingReferenceCardEdit(fundingReference) {
            const modal = this.manageFundingReferenceElement;
            if (!modal.showDialog) {
                modal.toggleDialog();
            }
            this.$nextTick(() => {
                modal.edit(fundingReference);
            });
        },
        onFundingReferenceCardDelete(fundingReference) {
            this.manageFundingReferenceElement.confirmDeletion(
                fundingReference
            );
        },
        onAuthorCardEdit(author) {
            const modal = this.manageAuthorElement;
            if (!modal.showDialog) {
                modal.toggleDialog();
            }
            this.$nextTick(() => {
                modal.edit(author);
            });
        },
        onAuthorCardDelete(author) {
            this.manageAuthorElement.confirmDeletion(author);
        },

        syncOrderedAuthorsFromProject() {
            const raw = this.project?.authors;
            if (!Array.isArray(raw) || raw.length === 0) {
                this.orderedAuthors = [];

                return;
            }
            this.orderedAuthors = [...raw].sort((a, b) => {
                const ao = a.pivot?.sort_order ?? 0;
                const bo = b.pivot?.sort_order ?? 0;

                return ao - bo;
            });
        },

        serializeAuthorForSync(author) {
            return {
                id: author.id,
                title: author.title ?? null,
                given_name: author.given_name,
                family_name: author.family_name,
                orcid_id: author.orcid_id ?? null,
                email_id: author.email_id ?? null,
                affiliation: author.affiliation ?? null,
                ror_id: author.ror_id ?? null,
                contributor_type:
                    (author.pivot && author.pivot.contributor_type) ||
                    author.contributor_type ||
                    "Researcher",
            };
        },

        onPublishAuthorDragStart() {
            this.publishAuthorsOrderBeforeDrag = this.orderedAuthors.map(
                (a) => a.id
            );
        },

        onPublishAuthorDragEnd() {
            const after = this.orderedAuthors.map((a) => a.id);
            const before = this.publishAuthorsOrderBeforeDrag;
            const unchanged =
                after.length === before.length &&
                after.every((id, i) => id === before[i]);
            if (unchanged) {
                return;
            }
            this.persistPublishAuthorOrder();
        },

        persistPublishAuthorOrder() {
            this.authorOrderSaveInProgress = true;
            this.setSaveStatus("saving", "authorOrder");
            const form = this.$inertia.form({
                authors: this.orderedAuthors.map((a) =>
                    this.serializeAuthorForSync(a)
                ),
            });
            form.post(route("author.save", this.project.id), {
                preserveScroll: true,
                preserveState: true,
                only: ["project"],
                onSuccess: () => {
                    this.setSaveStatus("saved", "authorOrder");
                },
                onError: () => {
                    this.syncOrderedAuthorsFromProject();
                    this.setSaveStatus("error", "authorOrder");
                },
                onFinish: () => {
                    this.authorOrderSaveInProgress = false;
                },
            });
        },
        handlePublishClick() {
            // Check if name contains "DRAFT" and hasn't been confirmed yet
            if (this.hasDraftInName && !this.draftWarningConfirmed) {
                this.showDraftWarningModal = true;
                return;
            }
            // For public/immediate publication, warn when project mode is enabled
            // but only one sample is present. For embargo publication, always
            // proceed as a project (sample embargo is not supported).
            const isEmbargoPublication = this.releaseVisibility === "embargo";
            if (
                !isEmbargoPublication &&
                this.project.studies &&
                this.project.studies.length === 1 &&
                this.publishForm.enableProjectMode
            ) {
                this.showSingleSampleModal = true;
                return;
            }
            // Proceed to normal publish confirmation
            this.showPublishConfirmationModal = true;
        },
        confirmDraftWarning() {
            this.draftWarningConfirmed = true;
            this.showDraftWarningModal = false;
            this.showPublishConfirmationModal = true;
        },
        publishAsSample() {
            this.showSingleSampleModal = false;
            this.publishForm.enableProjectMode = false;
            if (this.draft) {
                this.updateDraft();
            }
            this.showPublishConfirmationModal = true;
        },
        publishAsProject() {
            this.showSingleSampleModal = false;
            this.publishForm.enableProjectMode = true;
            if (this.draft) {
                this.updateDraft();
            }
            this.showPublishConfirmationModal = true;
        },
        publish() {
            this.showPublishConfirmationModal = false;
            if (this.publishForm.conditions && this.publishForm.terms) {
                this.errors = null;
                this.updateProject({
                    onSuccess: () => {
                        axios
                            .post(
                                route(
                                    "dashboard.project.publish",
                                    this.project.id
                                ),
                                this.publishForm
                            )
                            .catch((err) => {
                                this.errors = err.response.data.errors;
                                this.validation =
                                    err.response.data.validation.report;
                            })
                            .then((response) => {
                                this.status = response.data.project.status;
                                // this.trackProject();
                            });
                    },
                    onError: () => {
                        this.errors =
                            "Please resolve the highlighted form errors before publishing.";
                    },
                });
            }
        },
        applySampleModeReleaseSchedule() {
            if (this.publishForm.enableProjectMode) {
                return;
            }
            this.releaseVisibility = "public";
            const d = new Date();
            d.setHours(0, 0, 0, 0);
            const prev = this.publishForm.release_date
                ? new Date(this.publishForm.release_date)
                : null;
            if (prev) {
                prev.setHours(0, 0, 0, 0);
            }
            const needsPersist = !prev || prev.getTime() !== d.getTime();
            this.publishForm.release_date = d;
            if (needsPersist) {
                this.updateProject();
            }
        },
        isReleasedToday() {
            if (!this.publishForm.enableProjectMode) {
                return true;
            }
            if (this.releaseVisibility === "public") {
                return true;
            }
            var currentDate = new Date();
            var releaseDate = new Date(this.publishForm.release_date);
            if (releaseDate > currentDate) {
                return false;
            } else {
                return true;
            }
        },
    },
};
</script>
