<template>
    <app-layout title="Publish Data">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-6">
                    <div class="py-4">
                        <div>
                            <span
                                class="ml-12 text-sm font-bold text-teal-600 group-hover:text-teal-800"
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
                                        class="ml-1 mr-2 inline-flex items-center px-2.5 py-1 text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                        :href="
                                            '/upload?draft_id=' +
                                            draft.id +
                                            '&step=2'
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
            <div
                v-if="publishForm.processing"
                class="absolute w-full h-full text-center py-12 pt-24 bg-opacity-90 bg-white z-50"
            >
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
                Saving...
            </div>
            <div v-if="status == 'draft'">
                <div id="project-details" class="p-4">
                    <div class="p-8">
                        <div>
                            <label
                                class="block tracking-wider text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"
                            >
                                <small v-if="publishForm.enableProjectMode"
                                    >PUBLISH AS PROJECT</small
                                >
                                <small v-else>PUBLISH SAMPLES</small>
                            </label>
                            <toggle-button
                                v-model:enabled="publishForm.enableProjectMode"
                                @blur="updateDraft"
                            />
                        </div>
                        <div v-if="publishForm.enableProjectMode">
                            <div class="p-4 bg-gray-100 rounded-md">
                                <div id="project-name" class="mb-3">
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
                                            class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border-gray-300 rounded-md"
                                            @blur="updateProject"
                                        />
                                    </div>
                                    <jet-input-error
                                        :message="publishForm.errors.name"
                                        class="mt-2"
                                    />
                                </div>
                                <div id="project-desc" class="mb-3">
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
                                            v-model="publishForm.description"
                                            name="project-description"
                                            placeholder="Describe this project"
                                            rows="3"
                                            class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border border-gray-300 rounded-md"
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
                                        class="block text-sm font-medium text-gray-500 after:content-['*'] after:ml-0.5 after:text-red-500"
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
                                                (newTags) =>
                                                    (publishForm.tags = newTags)
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
                                                class="bg-gray-100 border text-gray-800 mb-0.5 inline-flex truncate break-words items-center px-3 py-2 rounded-full text-sm font-medium mr-1"
                                            >
                                                <ontology-term-annotation
                                                    :annotation="species"
                                                ></ontology-term-annotation>
                                                <span
                                                    class="cursor-pointer"
                                                    @click="
                                                        removeSpecies($index)
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
                                <div class="mb-2">
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
                                            class="relative flex items-center justify-between"
                                        >
                                            <span
                                                class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500 after:content-['*'] after:ml-0.5 after:text-red-500"
                                            >
                                                Citation
                                            </span>
                                            <button
                                                type="button"
                                                class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                                @click="toggleManageCitation"
                                            >
                                                <PencilIcon
                                                    class="w-4 h-4 mr-1 text-gray-600"
                                                />
                                                <span>Edit</span>
                                            </button>
                                        </div>
                                    </div>
                                    <dd
                                        class="mt-2 text-md text-gray-900 space-y-5 focus:pointer-events-auto"
                                    >
                                        <div
                                            class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2"
                                        >
                                            <div
                                                class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2"
                                            >
                                                <citation-card
                                                    :citations="
                                                        project.citations
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </dd>
                                </div>
                                <div class="mb-2">
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
                                            class="relative flex items-center justify-between"
                                        >
                                            <span
                                                class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500 after:content-['*'] after:ml-0.5 after:text-red-500"
                                            >
                                                Author
                                            </span>
                                            <button
                                                type="button"
                                                class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                                @click="toggleManageAuthor"
                                            >
                                                <PencilIcon
                                                    class="w-4 h-4 mr-1 text-gray-600"
                                                />
                                                <span>Edit</span>
                                            </button>
                                        </div>
                                    </div>
                                    <dd
                                        class="mt-2 text-md text-gray-900 space-y-5"
                                    >
                                        <div
                                            class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-3"
                                        >
                                            <author-card
                                                :authors="project.authors"
                                            />
                                        </div>
                                    </dd>
                                </div>
                                <div class="px-2">
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
                                        @change="updatePhotoPreview"
                                    />

                                    <div v-show="!photoPreview" class="mt-2">
                                        <img
                                            :src="
                                                project.project_photo_url
                                                    ? project.project_photo_url
                                                    : 'https://via.placeholder.com/400x200'
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
                        <div id="release" class="mt-3">
                            <label
                                class="block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"
                            >
                                Release Date
                            </label>
                            <Datepicker
                                v-model="publishForm.release_date"
                                @update:modelValue="updateProject"
                                :min-date="new Date()"
                                :format="customFormat"
                                :preview-format="customFormat"
                            ></Datepicker>
                            <p class="mt-1 text-sm text-gray-500">
                                Publish your data now immediately or set a
                                future release date to automatically make your
                                project public. If you opt for a future release
                                date, you have the flexibility to modify the
                                publication date and choose to publish instantly
                                from your project's dashboard view.
                            </p>
                        </div>
                        <div class="mt-5">
                            <!--License -->
                            <div id="license" class="mb-4">
                                <div>
                                    <div
                                        class="mt-6 grid grid-cols-1 gap-x-4 sm:grid-cols-1"
                                    >
                                        <div v-if="licenses">
                                            <span
                                                class="float-right text-xs cursor-pointer hover:text-blue-700 mt-2"
                                            >
                                                <a
                                                    target="_blank"
                                                    href="https://docs.nmrxiv.org/submission-guides/licenses"
                                                    >How to choose the right
                                                    license?</a
                                                >
                                            </span>
                                            <select-rich
                                                v-model:selected="license"
                                                label="License"
                                                :items="licenses"
                                                @update:selected="updateProject"
                                            />
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
                                            class="rounded mt-1 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            name="conditions"
                                        />
                                        <div class="ml-2 text-sm">
                                            <span
                                                v-if="
                                                    publishForm.enableProjectMode
                                                "
                                            >
                                                I understand once the project is
                                                published, all the underlying
                                                samples and spectra will also be
                                                made public and agree to make
                                                this data persistently available
                                                in the nmrXiv platform.
                                            </span>
                                            <span v-else>
                                                I understand once the samples
                                                are published, all the
                                                underlying spectra will also be
                                                made public and agree to make
                                                this data persistently available
                                                in the nmrXiv platform.
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
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            name="terms"
                                        />
                                        <div class="ml-2 text-sm">
                                            I agree to the
                                            <a
                                                target="_blank"
                                                :href="route('terms.show')"
                                                class="underline text-sm text-gray-600 hover:text-gray-900"
                                                >Terms of Service</a
                                            >
                                            and
                                            <a
                                                target="_blank"
                                                :href="route('policy.show')"
                                                class="underline text-sm text-gray-600 hover:text-gray-900"
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
                                    !publishForm.terms ||
                                    !publishForm.conditions ||
                                    publishForm.processing
                                        ? 'bg-gray-200 cursor-not-allowed'
                                        : 'bg-green-600 hover:bg-green-700',
                                    'ml-2',
                                ]"
                                :disabled="
                                    !publishForm.terms &&
                                    !publishForm.conditions
                                "
                                @click="showPublishConfirmationModal = true"
                            >
                                Publish Now
                            </jet-success-button>
                            <jet-success-button
                                v-else
                                type="button"
                                :class="[
                                    !publishForm.terms ||
                                    !publishForm.conditions ||
                                    publishForm.processing
                                        ? 'bg-gray-200 cursor-not-allowed'
                                        : 'bg-green-600 hover:bg-green-700',
                                    'inline-flex w-full justify-center rounded-md border border-transparent px-4 py-2 text-base font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:w-auto sm:text-sm',
                                ]"
                                :disabled="
                                    !publishForm.terms &&
                                    !publishForm.conditions
                                "
                                @click="showPublishConfirmationModal = true"
                            >
                                Publish with Embargo
                            </jet-success-button>
                            <Link
                                type="button"
                                class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
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
                                class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm rounded-md text-sky-500 bg-white transition ease-in-out duration-150 cursor-not-allowed"
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
                            class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
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
            <jet-confirmation-modal
                :show="showPublishConfirmationModal"
                @close="showPublishConfirmationModal = false"
            >
                <template #title> Are you sure you want to publish? </template>
                <template v-if="isReleasedToday()" #content>
                    Once the data is published you will no longer be able to
                    change the data uploaded! If published as a project, you may
                    add more samples (spectra) to the project later.
                </template>

                <template v-if="isReleasedToday()" #footer>
                    <jet-secondary-button
                        @click="showPublishConfirmationModal = false"
                    >
                        Cancel
                    </jet-secondary-button>
                    <jet-success-button class="ml-2" @click="publish">
                        Publish Now
                    </jet-success-button>
                </template>
                <template v-if="!isReleasedToday()" #content>
                    Opting for an Embargo publication grants your project a DOI,
                    yet it stays private exclusively for you. You have the
                    option to share the project with others and can adjust the
                    release date or promptly make it public through the
                    project's dashboard view. But once the data is published you
                    will no longer be able to change the data uploaded! If
                    published as a project, you may add more samples (spectra)
                    to the project later if desired.
                </template>
                <template v-if="!isReleasedToday()" #footer>
                    <jet-secondary-button
                        @click="showPublishConfirmationModal = false"
                    >
                        Cancel
                    </jet-secondary-button>
                    <jet-success-button class="ml-2" @click="publish">
                        Publish with Embargo
                    </jet-success-button>
                </template>
            </jet-confirmation-modal>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import TeamProjects from "@/Pages/Project/Index.vue";
import Create from "@/Shared/CreateButton.vue";
import Onboarding from "@/App/Onboarding.vue";
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";
import JetInputError from "@/Jetstream/InputError.vue";
import axios from "axios";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import VueTagsInput from "@sipec/vue3-tags-input";
import { ref } from "vue";
import slider from "vue3-slider";
import Validation from "@/Shared/Validation.vue";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import ManageAuthor from "@/Shared/ManageAuthor.vue";
import ToolTip from "@/Shared/ToolTip.vue";
import ManageCitation from "@/Shared/ManageCitation.vue";
import Citation from "@/Shared/Citation.vue";
import AuthorCard from "@/Shared/AuthorCard.vue";
import StudyInfo from "@/Shared/StudyInfo.vue";
import SelectRich from "@/Shared/SelectRich.vue";
import CitationCard from "@/Shared/CitationCard.vue";
import {
    XCircleIcon,
    ClipboardDocumentIcon,
    QuestionMarkCircleIcon,
    ExclamationTriangleIcon,
    TrashIcon,
    PlayIcon,
    PauseIcon,
    PencilIcon,
    ArrowDownOnSquareStackIcon,
    BellAlertIcon,
    StarIcon,
    CalendarIcon,
} from "@heroicons/vue/24/solid";
import SpectraEditor from "@/Shared/SpectraEditor.vue";
import ToggleButton from "@/Shared/ToggleButton.vue";
import "ontology-elements/dist/index.js";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetSuccessButton from "@/Jetstream/SuccessButton.vue";

export default {
    components: {
        ManageAuthor,
        AuthorCard,
        ManageCitation,
        Citation,
        ToolTip,
        Datepicker,
        VueTagsInput,
        slider,
        SelectRich,
        AppLayout,
        computed,
        TeamProjects,
        Create,
        Onboarding,
        Link,
        JetInputError,
        JetSecondaryButton,
        JetButton,
        XCircleIcon,
        ClipboardDocumentIcon,
        QuestionMarkCircleIcon,
        ExclamationTriangleIcon,
        TrashIcon,
        PlayIcon,
        PauseIcon,
        PencilIcon,
        ArrowDownOnSquareStackIcon,
        BellAlertIcon,
        SpectraEditor,
        Validation,
        ToggleButton,
        StarIcon,
        CalendarIcon,
        StudyInfo,
        CitationCard,
        JetConfirmationModal,
        JetSuccessButton,
    },
    props: ["user", "team", "project", "teamRole", "draft"],

    setup() {
        const manageAuthorElement = ref(null);
        const manageCitationElement = ref(null);

        // Custom format function
        const customFormat = (date) => {
            if (!date) return "";
            const day = String(date.getDate()).padStart(2, "0");
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, "0");
            const minutes = String(date.getMinutes()).padStart(2, "0");
            return `${day}/${month}/${year}, ${hours}:${minutes}`;
        };

        return {
            manageAuthorElement,
            manageCitationElement,
            customFormat,
        };
    },

    data() {
        return {
            publishForm: this.$inertia.form({
                _method: "PUT",
                name: "",
                description: "",
                error_message: null,
                tags: [],
                tag: "",
                tags_array: [],
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
            returnUrl: "/dashboard",
            errors: null,
            projectSpecies: "",
            status: "draft",
            validation: null,
            showPublishConfirmationModal: false,
            photoPreview: null,
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
            return this.$page.props.user.primed;
        },
        currentTab() {
            return this.tabs.find((t) => t.current);
        },
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

        this.$nextTick(() => {
            const urlSearchParams = new URLSearchParams(window.location.search);
            const params = Object.fromEntries(urlSearchParams.entries());
            let edit = params["edit"];

            if (edit == "citation") {
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
                this.toggleManageAuthor();
            }
        });
    },
    methods: {
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
        updateDraft() {
            axios.put("/dashboard/drafts/" + this.draft.id, {
                project_enabled: this.publishForm.enableProjectMode ? 1 : 0,
            });
        },
        updateProject() {
            // if (this.publishForm.enableProjectMode) {
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
            this.publishForm.species = this.publishForm.species;
            this.publishForm.owner_id = this.project.owner_id;
            this.publishForm.tags_array = this.publishForm.tags
                ? this.publishForm.tags.map((a) => a.text)
                : [];
            this.publishForm.post(
                route("dashboard.project.update", this.project.id),
                {
                    preserveScroll: true,
                    onSuccess: () => {},
                    onError: (err) => {},
                }
            );

            // axios.put(route("dashboard.project.update", this.project.id), {
            //     name: this.publishForm.name,
            //     description: this.publishForm.description,
            //     tags: this.publishForm.tags,
            //     tags_array: this.publishForm.tags
            //         ? this.publishForm.tags.map((a) => a.text)
            //         : [],
            //     license_id: this.license ? this.license.id : null,
            //     species: this.publishForm.species,
            //     release_date: this.publishForm.release_date,
            //     photo: this.$refs.photo ? this.$refs.photo.files[0] : null
            // }, {
            //         headers: {
            //         'Content-Type': 'multipart/form-data'
            //         }
            //     });
            // .then((res) => {
            //     console.log("success");
            // });
            // }
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
        publish() {
            this.showPublishConfirmationModal = false;
            if (this.publishForm.conditions && this.publishForm.terms) {
                this.errors = null;
                axios
                    .post(
                        route("dashboard.project.publish", this.project.id),
                        this.publishForm
                    )
                    .catch((err) => {
                        this.errors = err.response.data.errors;
                        this.validation = err.response.data.validation.report;
                    })
                    .then((response) => {
                        this.status = response.data.project.status;
                        // this.trackProject();
                    });
            }
        },
        isReleasedToday() {
            var currentDate = new Date();
            var releaseDate = new Date(this.publishForm.release_date);
            if (releaseDate > currentDate) {
                return false;
            } else {
                return true;
            }
        },
        // trackProject() {
        //     axios
        //         .get("/projects/status/" + this.project.id + "/queue")
        //         .then((response) => {
        //             this.status = response.data.status;
        //             if (this.status != "complete") {
        //                 setTimeout(() => this.trackProject(), 10000);
        //             } else {
        //                 return this.$inertia.visit(
        //                     this.route("dashboard.projects", [this.project.id])
        //                 );
        //             }
        //         });
        // },
    },
};
</script>
