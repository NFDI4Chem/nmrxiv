<template>
    <app-layout title="Submit Data">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-6">
                    <div class="py-4">
                        <div>
                            <div
                                class="w-full sm:flex sm:items-center sm:justify-between"
                            >
                                <h3
                                    class="text-sm text-gray-700 uppercase font-bold tracking-widest"
                                >
                                    <Link
                                        class="mx-2 inline-flex items-center px-2.5 py-1 text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                        :href="'/upload'"
                                    >
                                        ←
                                    </Link>
                                    <span v-if="currentStep && currentDraft">
                                       <p class="inline focus:outline-none focus:ring-0 focus:bg-gray-100 p-2 rounded-md" @blur="updateDraft($event)" contenteditable> {{ currentDraft.name }} </p>
                                    </span>
                                    <span v-else>
                                        Submit data to nmrXiv
                                    </span>
                                </h3>
                                <div class="mt-3 sm:ml-4 sm:mt-0">
                                    <div v-if="showPrimer && currentDraft">
                                        <div class="float-left">
                                            <div
                                                @change="hidePrimer()"
                                                class="relative mt-2 flex items-start"
                                            >
                                                <div
                                                    class="flex h-5 items-center"
                                                >
                                                    <input
                                                        id="comments"
                                                        aria-describedby="comments-description"
                                                        name="comments"
                                                        type="checkbox"
                                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                    />
                                                </div>
                                                <div class="ml-3 text-sm mr-5">
                                                    <label
                                                        for="comments"
                                                        class="font-medium text-gray-700"
                                                        >Don't show this
                                                        again</label
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                        <jet-button
                                            class="ml-2 float-right"
                                            :class="{
                                                'opacity-25':
                                                    createDatasetForm.processing,
                                            }"
                                            @click="skipPrimer()"
                                        >
                                            Proceed
                                        </jet-button>
                                    </div>
                                    <div v-else>
                                        <span v-if="!currentStep">
                                            <Link
                                                class="inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                                :href="this.returnUrl"
                                            >
                                                Cancel
                                            </Link>
                                        </span>
                                        <span v-else>
                                            <span v-if="currentStep.id == '01'">
                                                <Link
                                                    class="mx-2 inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                                    :href="this.returnUrl"
                                                >
                                                    Cancel
                                                </Link>
                                                <jet-button
                                                    id="tour-step-proceed-from-step-1"
                                                    class="ml-2"
                                                    :class="{
                                                        'opacity-25':
                                                            createDatasetForm.processing,
                                                    }"
                                                    :disabled="
                                                        createDatasetForm.processing ||
                                                        loading ||
                                                        loadingStep
                                                    "
                                                    @click="process"
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
                                                    currentStep.id == '02'
                                                "
                                            >
                                                <jet-secondary-button
                                                    class="ml-2 float-left"
                                                    :class="{
                                                        'opacity-25':
                                                            createDatasetForm.processing,
                                                    }"
                                                    :disabled="
                                                        createDatasetForm.processing
                                                    "
                                                    @click="selectStep(1)"
                                                >
                                                    Back
                                                </jet-secondary-button>
                                                <jet-button
                                                    id="tour-step-proceed-from-step-2"
                                                    class="ml-2"
                                                    :class="{
                                                        'opacity-25':
                                                            createDatasetForm.processing,
                                                    }"
                                                    :disabled="
                                                        createDatasetForm.processing ||
                                                        loading ||
                                                        loadingStep
                                                    "
                                                    @click="closeDraft"
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
                                                    currentStep.id == '03'
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
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div>
            <div>
                <div v-if="!loading">
                    <div class="mx-auto">
                        <div class=" px-12" v-if="drafts.length > 0 && !currentDraft">
                            <div class="my-8 mx-12 mx-auto max-w-none">
                                <div
                                    class="bg-white border rounded-lg shadow-md"
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
                                                    Please select one of the
                                                    drafts below to continue or
                                                    start a new submission
                                                </p>
                                            </div>
                                            <div
                                                class="ml-4 mt-4 flex-shrink-0"
                                            >
                                                <button
                                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                                                    @click="createNewDraft()"
                                                >
                                                    + Create New
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <ul
                                        role="list"
                                        class="overflow-y-scroll h-[calc(100vh-290px)]"
                                    >
                                        <li
                                            v-for="draft in drafts"
                                            :key="draft.id"
                                            class="hover:cursor-pointer border-b hover:bg-gray-50 px-5 py-4"
                                        >
                                            <Link
                                                :href="
                                                    route('upload', {
                                                        draft_id: draft.id,
                                                    })
                                                "
                                            >
                                                <div
                                                    class="flex items-center space-x-4"
                                                >
                                                    <div class="flex-1 min-w-0">
                                                        <p
                                                            class="text-lg font-large text-black truncate"
                                                        >
                                                            <b>{{
                                                                draft.name
                                                            }}</b>
                                                        </p>
                                                        <p
                                                            class="text-sm font-medium text-gray-600 truncate pr-10"
                                                        >
                                                            {{
                                                                draft.description
                                                            }}
                                                        </p>
                                                        <p
                                                            class="text-sm font-medium text-gray-500 truncate"
                                                        >
                                                            ID: {{ draft.key }}
                                                        </p>
                                                       <p
                                                            class="text-sm text-gray-500 truncate"
                                                        >
                                                            Created at:
                                                            {{
                                                                formatDateTime(
                                                                    draft.created_at
                                                                )
                                                            }}
                                                        </p>
                                                         <!-- <p
                                                            class="text-sm text-gray-500 truncate"
                                                        >
                                                            Updated at:
                                                            {{
                                                                formatDateTime(
                                                                    draft.updated_at
                                                                )
                                                            }}
                                                        </p> -->
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
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="pt-6" v-if="showPrimer">
                                <div
                                    class="h-[calc(100vh-235px)] overflow-scroll"
                                >
                                    <primer />
                                </div>
                            </div>
                            <div v-else>
                                <div
                                    class=""
                                    v-if="currentStep && currentDraft"
                                >
                                    <div
                                        v-if="currentStep.id == '01'"
                                        id="submission-dropzone"
                                    >
                                        <div class="mx-5 pt-4">
                                            <small class="cursor-pointer"
                                                >Draft ID:
                                                {{ currentDraft.key }}</small
                                            >
                                        </div>
                                        <div id="tour-step-upload-spectra">
                                            <file-system-browser
                                                ref="fsbRef"
                                                :readonly="false"
                                                :draft="currentDraft"
                                                :height="'h-[calc(100vh-385px)]'"
                                            ></file-system-browser>
                                        </div>
                                        <jet-input-error
                                            :message="draftForm.errors.studies"
                                            class="mt-2"
                                        />
                                    </div>
                                    <div
                                        class="h-[calc(100vh-135px)]"
                                        v-if="currentStep.id == '02'"
                                    >
                                        <div>
                                            <div class="flex-1 flex">
                                                <nav
                                                    aria-label="Sections"
                                                    class="overflow-y-scroll h-[calc(100vh-135px)] hidden flex-shrink-0 w-64 bg-white border-r border-gray-200 md:flex md:flex-col"
                                                >
                                                    <div
                                                        class="border-gray-200 px-4 py-3 border-b bg-gray-50 text-left text-xs font-medium text-gray-500 tracking-wider flex-shrink-0 border-b border-blue-gray-200"
                                                    >
                                                        {{
                                                            pluralize(
                                                                "SAMPLE",
                                                                studies.length
                                                            )
                                                        }}
                                                        ({{ studies.length }})
                                                        <div
                                                            class="float-right cursor-pointer tooltip"
                                                            @click="
                                                                autoImport()
                                                            "
                                                        >
                                                            <ArrowDownOnSquareStackIcon
                                                                class="w-5 h-5 mr-1 text-gray-600 hover:text-gray-500"
                                                            />
                                                            <span
                                                                class="bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextbottom"
                                                                >Click to auto
                                                                import
                                                                spectra</span
                                                            >
                                                        </div>
                                                    </div>
                                                    <div
                                                        id="tour-step-side-panel-studies"
                                                        style="
                                                            height: 74vh;
                                                            overflow: scroll !important;
                                                        "
                                                    >
                                                        <a
                                                            v-for="(
                                                                study, $index
                                                            ) in studies"
                                                            :key="study.slug"
                                                            :class="[
                                                                selectedStudy.id ==
                                                                study.id
                                                                    ? 'bg-gray-800 text-white'
                                                                    : 'hover:bg-gray-200 hover:bg-opacity-50',
                                                                'cursor-pointer flex py-4 border-b border-blue-gray-200',
                                                            ]"
                                                        >
                                                            <div
                                                                class="px-4 text-sm w-full"
                                                            >
                                                                <p
                                                                    class="font-medium text-blue-gray-900"
                                                                >
                                                                    <a
                                                                        @click="
                                                                            selectStudy(
                                                                                study,
                                                                                $index
                                                                            )
                                                                        "
                                                                        >{{
                                                                            study.name
                                                                        }}</a
                                                                    >
                                                                    <span
                                                                        v-if="
                                                                            study
                                                                                .sample
                                                                                .molecules
                                                                                .length >
                                                                            0
                                                                        "
                                                                        class="float-right"
                                                                    >
                                                                        <img
                                                                            class="flex-shrink-0 -mt-0.5 h-6 w-6 text-blue-gray-400"
                                                                            src="https://upload.wikimedia.org/wikipedia/sco/3/35/ChEBI_logo.png"
                                                                            alt=""
                                                                        />
                                                                    </span>
                                                                </p>
                                                                <div
                                                                    class="mt-1 text-blue-gray-500"
                                                                >
                                                                    <span
                                                                        v-for="(
                                                                            ds,
                                                                            $dsIndex
                                                                        ) in study.datasets"
                                                                        :key="
                                                                            ds.id
                                                                        "
                                                                    >
                                                                        <div
                                                                            @click="
                                                                                selectStudy(
                                                                                    study,
                                                                                    $index,
                                                                                    $dsIndex
                                                                                )
                                                                            "
                                                                            :class="[
                                                                                ds.has_nmrium
                                                                                    ? 'bg-green-100 text-gray-800'
                                                                                    : 'bg-gray-100 text-gray-800',
                                                                                'mb-0.5 inline-flex truncate break-words items-center px-3 py-0.5 rounded-full text-xs font-medium mr-1 hover:bg-teal-700',
                                                                            ]"
                                                                        >
                                                                            <a
                                                                                >{{
                                                                                    ds.name
                                                                                }}
                                                                                <span
                                                                                    v-if="
                                                                                        ds.type
                                                                                    "
                                                                                    >({{
                                                                                        ds.type
                                                                                    }})</span
                                                                                ></a
                                                                            >
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </nav>
                                                <div class="flex-1 p-2">
                                                    <div
                                                        class="mx-auto flex flex-col md:px-0 xl:px-0"
                                                    >
                                                        <main class="flex-1">
                                                            <div
                                                                class="relative mx-auto md:px-0 xl:px-0"
                                                            >
                                                                <div
                                                                    class="pt-5 pb-16"
                                                                >
                                                                    <div
                                                                        class="px-2 sm:px-2"
                                                                    >
                                                                        <h1
                                                                            class="text-3xl font-extrabold text-gray-900"
                                                                        >
                                                                            {{
                                                                                selectedStudy.name
                                                                            }}
                                                                        </h1>
                                                                    </div>
                                                                    <div
                                                                        class="px-2 sm:px-2"
                                                                        v-if="
                                                                            selectedStudy
                                                                                .sample
                                                                                .molecules
                                                                                .length >
                                                                            0
                                                                        "
                                                                    >
                                                                        <ul
                                                                            role="list"
                                                                            class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                                                                        >
                                                                            <li
                                                                                v-for="molecule in selectedStudy
                                                                                    .sample
                                                                                    .molecules"
                                                                                class="col-span-1 divide-y divide-gray-200 cursor-pointer"
                                                                            >
                                                                                <div
                                                                                    @click="
                                                                                        selectTab(
                                                                                            this
                                                                                                .tabs[1]
                                                                                        )
                                                                                    "
                                                                                    class="rounded-lg bg-white shadow border my-3 flex justify-center items-center"
                                                                                >
                                                                                    <span
                                                                                        v-html="
                                                                                            getSVGString(
                                                                                                molecule
                                                                                            )
                                                                                        "
                                                                                    ></span>
                                                                                </div>
                                                                            </li>

                                                                            <!-- More people... -->
                                                                        </ul>
                                                                    </div>
                                                                    <div
                                                                        class="px-2 sm:px-2"
                                                                        v-else
                                                                    >
                                                                        <ul>
                                                                            <li
                                                                                class="col-span-1 divide-y divide-gray-200 cursor-pointer"
                                                                            >
                                                                                <div
                                                                                    @click="
                                                                                        selectTab(
                                                                                            this
                                                                                                .tabs[1]
                                                                                        )
                                                                                    "
                                                                                    class="rounded-lg bg-white shadow border my-3 flex justify-center items-center"
                                                                                >
                                                                                    <button
                                                                                        type="button"
                                                                                        class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                                                    >
                                                                                        <svg
                                                                                            class="mx-auto h-12 w-12 text-gray-400"
                                                                                            stroke="currentColor"
                                                                                            fill="none"
                                                                                            viewBox="0 0 48 48"
                                                                                            aria-hidden="true"
                                                                                        >
                                                                                            <path
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round"
                                                                                                stroke-width="2"
                                                                                                d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m0-4c0 4.418-7.163 8-16 8S8 28.418 8 24m32 10v6m0 0v6m0-6h6m-6 0h-6"
                                                                                            />
                                                                                        </svg>
                                                                                        <span
                                                                                            class="mt-2 block text-sm font-semibold text-gray-900"
                                                                                            >Add
                                                                                            structure
                                                                                            details</span
                                                                                        >
                                                                                    </button>
                                                                                </div>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                    <div
                                                                        class="lg:hidden px-2 sm:px-2"
                                                                    >
                                                                        <label
                                                                            for="selected-tab"
                                                                            class="sr-only"
                                                                            >Select
                                                                            a
                                                                            tab</label
                                                                        >
                                                                        <select
                                                                            id="selected-tab"
                                                                            name="selected-tab"
                                                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm rounded-md"
                                                                        >
                                                                            <option
                                                                                v-for="tab in tabs"
                                                                                :key="
                                                                                    tab.name
                                                                                "
                                                                                :selected="
                                                                                    tab.current
                                                                                "
                                                                            >
                                                                                {{
                                                                                    tab.name
                                                                                }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <div
                                                                        class="hidden lg:block px-2 sm:px-2"
                                                                    >
                                                                        <div
                                                                            class="border-b border-gray-200"
                                                                        >
                                                                            <nav
                                                                                class="-mb-px flex space-x-8"
                                                                            >
                                                                                <a
                                                                                    v-for="tab in tabs"
                                                                                    :key="
                                                                                        tab.name
                                                                                    "
                                                                                    :class="[
                                                                                        tab.current
                                                                                            ? 'border-teal-500 text-teal-600'
                                                                                            : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                                                                        'cursor-pointer whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                                                                                    ]"
                                                                                    @click="
                                                                                        selectTab(
                                                                                            tab
                                                                                        )
                                                                                    "
                                                                                >
                                                                                    {{
                                                                                        tab.name
                                                                                    }}
                                                                                </a>
                                                                            </nav>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="overflow-y-scroll h-[calc(100vh-353px)]"
                                                                    >
                                                                        <div
                                                                            v-if="
                                                                                currentTab.name ==
                                                                                'Metadata'
                                                                            "
                                                                            class="px-2 sm:px-2"
                                                                        >
                                                                            <div
                                                                                class="pt-4 xl:col-span-2"
                                                                            >
                                                                                <div
                                                                                    class="mb-3"
                                                                                >
                                                                                    <label
                                                                                        for="study-name"
                                                                                        class="block text-sm font-medium text-gray-700"
                                                                                    >
                                                                                        Sample
                                                                                        Name

                                                                                        <span
                                                                                            class="float-right border rounded-full px-2 py-1 mb-2"
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
                                                                                        <input
                                                                                            v-model="
                                                                                                studyForm.name
                                                                                            "
                                                                                            type="text"
                                                                                            name="study-name"
                                                                                            class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border-gray-300 rounded-md"
                                                                                        />
                                                                                        <jet-input-error
                                                                                            :message="
                                                                                                studyForm
                                                                                                    .errors
                                                                                                    .name
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
                                                                                        Sample
                                                                                        Description
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
                                                                                                (
                                                                                                    newTags
                                                                                                ) =>
                                                                                                    (studyForm.tags =
                                                                                                        newTags)
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
                                                                                <div
                                                                                    class="mb-6"
                                                                                >
                                                                                    <button
                                                                                        type="button"
                                                                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                                                                        @click="
                                                                                            saveStudyDetails
                                                                                        "
                                                                                    >
                                                                                        SAVE
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            id="tour-step-nmrium"
                                                                            v-if="
                                                                                currentTab.name ==
                                                                                'Experiments (Spectra)'
                                                                            "
                                                                            class="px-2 sm:px-2 md:px-0"
                                                                        >
                                                                            <div
                                                                                class="px-2"
                                                                            >
                                                                                <div
                                                                                    class="my-7"
                                                                                >
                                                                                    <SpectraEditor
                                                                                        :project="
                                                                                            project
                                                                                        "
                                                                                        :study="
                                                                                            selectedStudy
                                                                                        "
                                                                                        ref="spectraEditorREF"
                                                                                    ></SpectraEditor>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            v-if="
                                                                                currentTab.name ==
                                                                                'Sample Info'
                                                                            "
                                                                            class="px-2 sm:px-2 pt-4"
                                                                        >
                                                                            <h1
                                                                                class="text-xl font-bold text-gray-900"
                                                                            >
                                                                                Sample
                                                                                Details
                                                                            </h1>
                                                                            <!-- <section
                                                                aria-labelledby="activity-title"
                                                                class="mt-2 xl:mt-2"
                                                            >
                                                                <div>
                                                                    <div
                                                                        class="pt-3"
                                                                    >
                                                                        <div>
                                                                            <label
                                                                                for="about"
                                                                                class="block text-sm font-medium text-gray-700"
                                                                            >
                                                                                Sample
                                                                                Collection
                                                                                Protocol
                                                                            </label>
                                                                            <div
                                                                                class="mt-1"
                                                                            >
                                                                                <textarea
                                                                                    id="about"
                                                                                    readonly
                                                                                    name="about"
                                                                                    rows="3"
                                                                                    class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                                                                ></textarea>
                                                                            </div>
                                                                            <p
                                                                                class="mt-2 text-sm text-gray-500"
                                                                            >
                                                                                Describe
                                                                                your
                                                                                sample
                                                                                collection
                                                                                protocol.
                                                                                Protocol
                                                                                parameters
                                                                                can
                                                                                be
                                                                                provided
                                                                                below.
                                                                            </p>
                                                                        </div>
                                                                       <div class="mt-3">
                                        <h2 class="text-sm font-medium text-gray-500">
                                          Sample Collection Parameters
                                        </h2>
                                        <ul role="list" class="mt-2 leading-8">
                                          <li class="inline">
                                            <a
                                              href="#"
                                              class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5"
                                            >
                                              <div
                                                class="absolute flex-shrink-0 flex items-center justify-center"
                                              >
                                                <span
                                                  class="h-1.5 w-1.5 rounded-full bg-rose-500"
                                                  aria-hidden="true"
                                                ></span>
                                              </div>
                                              <div
                                                class="ml-3.5 text-sm font-medium text-gray-900"
                                              >
                                                Bug
                                              </div>
                                            </a>
                                          </li>
                                          <li class="inline">
                                            <a
                                              href="#"
                                              class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5"
                                            >
                                              <div
                                                class="absolute flex-shrink-0 flex items-center justify-center"
                                              >
                                                <span
                                                  class="h-1.5 w-1.5 rounded-full bg-teal-500"
                                                  aria-hidden="true"
                                                ></span>
                                              </div>
                                              <div
                                                class="ml-3.5 text-sm font-medium text-gray-900"
                                              >
                                                Accessibility
                                              </div>
                                            </a>
                                          </li>
                                        </ul>
                                      </div> 
                                                                    </div>
                                                                </div>
                                                            </section> -->
                                                                            <div
                                                                                class="py-4 max-w-7xl mx-auto"
                                                                            >
                                                                                <div
                                                                                    class="sm:flex sm:items-center sm:justify-between"
                                                                                >
                                                                                    <h3
                                                                                        class="text-xl font-bold text-gray-900"
                                                                                    >
                                                                                        Chemical
                                                                                        composition
                                                                                        (optional)
                                                                                    </h3>
                                                                                </div>
                                                                                <p
                                                                                    class="mt-2 pb-3 border-b border-gray-200 text-sm text-gray-500"
                                                                                >
                                                                                    Residual
                                                                                    Complexity
                                                                                    (RC)
                                                                                    refers
                                                                                    to
                                                                                    the
                                                                                    subtle
                                                                                    but
                                                                                    significant
                                                                                    convolution
                                                                                    of
                                                                                    major
                                                                                    and
                                                                                    minor
                                                                                    ("hidden")
                                                                                    chemical
                                                                                    species
                                                                                    in
                                                                                    materials
                                                                                    that
                                                                                    originate
                                                                                    from
                                                                                    biochemical
                                                                                    or
                                                                                    synthetic
                                                                                    reaction
                                                                                    mixtures.
                                                                                    Certain
                                                                                    levels
                                                                                    of
                                                                                    these
                                                                                    chemical
                                                                                    species
                                                                                    (molecules)
                                                                                    usually
                                                                                    remain
                                                                                    present
                                                                                    even
                                                                                    after
                                                                                    a
                                                                                    number
                                                                                    of
                                                                                    purification
                                                                                    steps,
                                                                                    and
                                                                                    this
                                                                                    RC
                                                                                    is
                                                                                    to
                                                                                    some
                                                                                    degree
                                                                                    conserved
                                                                                    even
                                                                                    in
                                                                                    highly
                                                                                    purified
                                                                                    materials.
                                                                                    In
                                                                                    principle,
                                                                                    RC
                                                                                    affects
                                                                                    all
                                                                                    "pure"
                                                                                    materials,
                                                                                    including
                                                                                    synthetic
                                                                                    compound,
                                                                                    whenever
                                                                                    chromatographic
                                                                                    or
                                                                                    other
                                                                                    purification
                                                                                    steps
                                                                                    are
                                                                                    required
                                                                                    prior
                                                                                    to
                                                                                    their
                                                                                    biological
                                                                                    evaluation.
                                                                                    Please
                                                                                    report
                                                                                    the
                                                                                    chemical
                                                                                    composition
                                                                                    (if
                                                                                    known)
                                                                                    of
                                                                                    your
                                                                                    sample.
                                                                                    If
                                                                                    unknown
                                                                                    please
                                                                                    report
                                                                                    the
                                                                                    molecules
                                                                                    that
                                                                                    you
                                                                                    expect
                                                                                    to
                                                                                    be
                                                                                    present
                                                                                    in
                                                                                    your
                                                                                    sample
                                                                                    with-out
                                                                                    the
                                                                                    composition.
                                                                                </p>
                                                                            </div>

                                                                            <div
                                                                                class="grid grid-cols-2 gap-2"
                                                                            >
                                                                                <div
                                                                                    class="pr-2"
                                                                                >
                                                                                    <div
                                                                                        v-if="
                                                                                            selectedStudy
                                                                                                .sample
                                                                                                .molecules
                                                                                                .length >
                                                                                            0
                                                                                        "
                                                                                        class="flow-root"
                                                                                    >
                                                                                        <ul
                                                                                            role="list"
                                                                                            class="-mb-8"
                                                                                        >
                                                                                            <li
                                                                                                v-for="molecule in selectedStudy
                                                                                                    .sample
                                                                                                    .molecules"
                                                                                                :key="
                                                                                                    molecule.STANDARD_INCHI
                                                                                                "
                                                                                            >
                                                                                                <div
                                                                                                    class="relative pb-8"
                                                                                                >
                                                                                                    <span
                                                                                                        class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200"
                                                                                                        aria-hidden="true"
                                                                                                    ></span>
                                                                                                    <div
                                                                                                        class="relative flex items-start space-x-3"
                                                                                                    >
                                                                                                        <div
                                                                                                            v-if="
                                                                                                                molecule &&
                                                                                                                molecule.pivot
                                                                                                            "
                                                                                                            class="relative"
                                                                                                        >
                                                                                                            <div
                                                                                                                class="rounded-full border p-2 z-10 bg-gray-100 text-sm"
                                                                                                            >
                                                                                                                {{
                                                                                                                    molecule
                                                                                                                        .pivot
                                                                                                                        .percentage_composition
                                                                                                                }}%
                                                                                                            </div>
                                                                                                            <!-- <span
                                                class="absolute -bottom-0.5 -right-1 bg-white rounded-tl px-0.5 py-px"
                                              >
                                                <svg
                                                  class="h-5 w-5 text-gray-400"
                                                  x-description="Heroicon name: solid/chat-alt"
                                                  xmlns="http://www.w3.org/2000/svg"
                                                  viewBox="0 0 20 20"
                                                  fill="currentColor"
                                                  aria-hidden="true"
                                                >
                                                  <path
                                                    fill-rule="evenodd"
                                                    d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                                    clip-rule="evenodd"
                                                  ></path>
                                                </svg>
                                              </span> -->
                                                                                                        </div>
                                                                                                        <div
                                                                                                            class="min-w-0 flex-1"
                                                                                                        >
                                                                                                            <div>
                                                                                                                <div
                                                                                                                    class="text-sm"
                                                                                                                >
                                                                                                                    <a
                                                                                                                        href="#"
                                                                                                                        class="font-medium text-gray-900"
                                                                                                                        >{{
                                                                                                                            molecule.STANDARD_INCHI
                                                                                                                        }}</a
                                                                                                                    >
                                                                                                                </div>
                                                                                                                <!-- <p class="mt-0.5 text-sm text-gray-500">
                                                  Commented 6d ago
                                                </p> -->
                                                                                                            </div>
                                                                                                            <div
                                                                                                                class="mt-2 text-sm text-gray-700"
                                                                                                            >
                                                                                                                <div
                                                                                                                    class="rounded-md border my-3 flex justify-center items-center"
                                                                                                                >
                                                                                                                    <span
                                                                                                                        v-html="
                                                                                                                            getSVGString(
                                                                                                                                molecule
                                                                                                                            )
                                                                                                                        "
                                                                                                                    ></span>
                                                                                                                </div>
                                                                                                                <button
                                                                                                                    class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                                                                                    @click="
                                                                                                                        deleteMolecule(
                                                                                                                            molecule
                                                                                                                        )
                                                                                                                    "
                                                                                                                >
                                                                                                                    <TrashIcon
                                                                                                                        class="w-4 h-4 inline mr-1"
                                                                                                                    ></TrashIcon
                                                                                                                    >Delete
                                                                                                                </button>
                                                                                                                <button
                                                                                                                    class="inline-flex ml-2 items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                                                                                    @click="
                                                                                                                        editMolecule(
                                                                                                                            molecule
                                                                                                                        )
                                                                                                                    "
                                                                                                                >
                                                                                                                    <PencilIcon
                                                                                                                        class="w-4 h-4 inline mr-1"
                                                                                                                    ></PencilIcon
                                                                                                                    >Edit
                                                                                                                </button>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </li>
                                                                                        </ul>
                                                                                        <div
                                                                                            class="rounded-full border p-2 z-10 bg-gray-100 text-sm mt-14 text-center"
                                                                                        >
                                                                                            Sample
                                                                                            chemical
                                                                                            composition
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        v-else
                                                                                    >
                                                                                        <div
                                                                                            class="text-center my-10 py-10"
                                                                                        >
                                                                                            <svg
                                                                                                class="mx-auto h-12 w-12 text-gray-400"
                                                                                                fill="none"
                                                                                                viewBox="0 0 24 24"
                                                                                                stroke="currentColor"
                                                                                                aria-hidden="true"
                                                                                            >
                                                                                                <path
                                                                                                    vector-effect="non-scaling-stroke"
                                                                                                    stroke-linecap="round"
                                                                                                    stroke-linejoin="round"
                                                                                                    stroke-width="2"
                                                                                                    d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                                                                                                />
                                                                                            </svg>
                                                                                            <h3
                                                                                                class="mt-2 text-sm font-medium text-gray-900"
                                                                                            >
                                                                                                No
                                                                                                structures
                                                                                                associated
                                                                                                with
                                                                                                the
                                                                                                sample
                                                                                                yet!
                                                                                            </h3>
                                                                                            <p
                                                                                                class="mt-1 text-sm text-gray-500"
                                                                                            >
                                                                                                Get
                                                                                                started
                                                                                                by
                                                                                                adding
                                                                                                a
                                                                                                new
                                                                                                molecule.
                                                                                            </p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div
                                                                                    class="pl-2"
                                                                                >
                                                                                    <div
                                                                                        class="sm:col-span-4"
                                                                                    >
                                                                                        <label
                                                                                            for="email"
                                                                                            class="block text-sm font-medium text-gray-700"
                                                                                        >
                                                                                            SMILES
                                                                                        </label>
                                                                                        <div
                                                                                            class="mt-1 mb-2"
                                                                                        >
                                                                                            <input
                                                                                                id="smiles"
                                                                                                v-model="
                                                                                                    smiles
                                                                                                "
                                                                                                name="smiles"
                                                                                                type="text"
                                                                                                class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                                                                @blur="
                                                                                                    loadSmiles
                                                                                                "
                                                                                            />
                                                                                        </div>
                                                                                        <button
                                                                                            v-if="
                                                                                                smiles &&
                                                                                                smiles !=
                                                                                                    ''
                                                                                            "
                                                                                            class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mt-2"
                                                                                            @click="
                                                                                                loadSmiles
                                                                                            "
                                                                                        >
                                                                                            Load
                                                                                            Structure
                                                                                        </button>
                                                                                        <jet-input-error
                                                                                            :message="
                                                                                                this
                                                                                                    .errorMessage
                                                                                            "
                                                                                            class="mt-2"
                                                                                        />
                                                                                    </div>
                                                                                    <div
                                                                                        class="relative"
                                                                                    >
                                                                                        <div
                                                                                            class="absolute inset-0 flex items-center"
                                                                                            aria-hidden="true"
                                                                                        >
                                                                                            <div
                                                                                                class="w-full border-t border-gray-300"
                                                                                            />
                                                                                        </div>
                                                                                        <div
                                                                                            class="relative flex justify-center"
                                                                                        >
                                                                                            <span
                                                                                                class="px-2 bg-white text-sm text-gray-500"
                                                                                            >
                                                                                                Or
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <span
                                                                                        class="float-right text-xs cursor-pointer hover:text-blue-700 m-2"
                                                                                    >
                                                                                        <a
                                                                                            href="https://docs.nmrxiv.org/docs/submission-guides/submission/editor"
                                                                                            target="_blank"
                                                                                            >Need
                                                                                            help?
                                                                                        </a>
                                                                                    </span>
                                                                                    <div
                                                                                        id="structureSearchEditor"
                                                                                        class="w-full border my-4 rounded-md"
                                                                                        style="
                                                                                            height: 400px;
                                                                                        "
                                                                                    />
                                                                                    <div
                                                                                        class="mt-1 mb-6"
                                                                                    >
                                                                                        <label
                                                                                            for="email"
                                                                                            class="block text-sm font-medium text-gray-700"
                                                                                        >
                                                                                            Percentage
                                                                                            composition
                                                                                            ({{
                                                                                                percentage
                                                                                            }}%)
                                                                                        </label>
                                                                                        <slider
                                                                                            v-model="
                                                                                                percentage
                                                                                            "
                                                                                            :min="
                                                                                                0
                                                                                            "
                                                                                            :max="
                                                                                                getMax
                                                                                            "
                                                                                            :height="
                                                                                                10
                                                                                            "
                                                                                            color="#000"
                                                                                            track-color="#999"
                                                                                        />
                                                                                    </div>
                                                                                    <button
                                                                                        type="button"
                                                                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                                                                        @click="
                                                                                            saveMolecule(
                                                                                                null
                                                                                            )
                                                                                        "
                                                                                    >
                                                                                        ADD
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                            <div
                                                                                v-if="
                                                                                    selectedStudy
                                                                                "
                                                                            >
                                                                                <div
                                                                                    v-if="
                                                                                        selectedStudy.molecules &&
                                                                                        selectedStudy
                                                                                            .molecules
                                                                                            .length ==
                                                                                            0
                                                                                    "
                                                                                >
                                                                                    <template>
                                                                                        <button
                                                                                            type="button"
                                                                                            class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                                                                        >
                                                                                            <svg
                                                                                                class="mx-auto h-12 w-12 text-gray-400"
                                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                                                stroke="currentColor"
                                                                                                fill="none"
                                                                                                viewBox="0 0 48 48"
                                                                                                aria-hidden="true"
                                                                                            >
                                                                                                <path
                                                                                                    stroke-linecap="round"
                                                                                                    stroke-linejoin="round"
                                                                                                    stroke-width="2"
                                                                                                    d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m0-4c0 4.418-7.163 8-16 8S8 28.418 8 24m32 10v6m0 0v6m0-6h6m-6 0h-6"
                                                                                                />
                                                                                            </svg>
                                                                                            <span
                                                                                                class="mt-2 block text-sm font-medium text-gray-900"
                                                                                            >
                                                                                                Add
                                                                                                structure
                                                                                                details
                                                                                            </span>
                                                                                        </button>
                                                                                    </template>
                                                                                </div>
                                                                                <div
                                                                                    v-if="
                                                                                        selectedStudy.molecules &&
                                                                                        selectedStudy
                                                                                            .molecules
                                                                                            .length >
                                                                                            0
                                                                                    "
                                                                                >
                                                                                    Mols
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </main>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="currentStep.id == '03'">
                                        <div
                                            v-if="!validationStatus"
                                            class="p-4"
                                        >
                                            <Validation
                                                :project="project"
                                                :validation="validation"
                                            ></Validation>
                                        </div>
                                        <div v-else>
                                            <div class="p-4">
                                                <div class="p-8">
                                                    <div>
                                                        <label
                                                            class="block tracking-wider text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"
                                                        >
                                                            <small
                                                                >PUBLISH AS
                                                                PROJECT</small
                                                            >
                                                        </label>
                                                        <toggle-button
                                                            v-model:enabled="
                                                                publishForm.enableProjectMode
                                                            "
                                                        />
                                                    </div>
                                                    <div
                                                        v-if="
                                                            publishForm.enableProjectMode
                                                        "
                                                    >
                                                        <div
                                                            class="p-4 bg-gray-100 rounded-md"
                                                        >
                                                            <div
                                                                id="tour-step-project-name"
                                                                class="mb-3"
                                                            >
                                                                <label
                                                                    for="project-name"
                                                                    class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                                                                >
                                                                    Project Name
                                                                </label>
                                                                <div
                                                                    class="mt-1"
                                                                >
                                                                    <input
                                                                        v-model="
                                                                            publishForm
                                                                                .project
                                                                                .name
                                                                        "
                                                                        type="text"
                                                                        name="project-name"
                                                                        class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border-gray-300 rounded-md"
                                                                    />
                                                                </div>
                                                                <jet-input-error
                                                                    :message="
                                                                        publishForm
                                                                            .errors
                                                                            .name
                                                                    "
                                                                    class="mt-2"
                                                                />
                                                            </div>
                                                            <div
                                                                id="tour-step-project-desc"
                                                                class="mb-3"
                                                            >
                                                                <label
                                                                    for="description"
                                                                    class="block text-sm font-medium text-gray-700"
                                                                >
                                                                    <span
                                                                        @click="
                                                                            publishForm.project.description =
                                                                                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore'
                                                                        "
                                                                        >Project
                                                                        Description
                                                                        (Optional)</span
                                                                    >
                                                                </label>
                                                                <div
                                                                    class="mt-1"
                                                                >
                                                                    <textarea
                                                                        id="description"
                                                                        v-model="
                                                                            publishForm
                                                                                .project
                                                                                .description
                                                                        "
                                                                        name="description"
                                                                        placeholder="Describe this project"
                                                                        rows="3"
                                                                        class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border border-gray-300 rounded-md"
                                                                    ></textarea>
                                                                </div>
                                                                <jet-input-error
                                                                    :message="
                                                                        publishForm
                                                                            .errors
                                                                            .description
                                                                    "
                                                                    class="mt-2"
                                                                />
                                                            </div>
                                                            <div
                                                                id="tour-step-project-keywords"
                                                                class="mb-3"
                                                            >
                                                                <label
                                                                    for="description"
                                                                    class="block text-sm font-medium text-gray-700"
                                                                >
                                                                    Keywords
                                                                    (Optional)
                                                                </label>
                                                                <div>
                                                                    <vue-tags-input
                                                                        v-model="
                                                                            publishForm
                                                                                .project
                                                                                .tag
                                                                        "
                                                                        placeholder="Type a keyword or keywords separated by comma (,) and press enter"
                                                                        :separators="[
                                                                            ';',
                                                                            ',',
                                                                        ]"
                                                                        max-width="100%"
                                                                        :tags="
                                                                            publishForm
                                                                                .project
                                                                                .tags
                                                                        "
                                                                        @tags-changed="
                                                                            (
                                                                                newTags
                                                                            ) =>
                                                                                (publishForm.project.tags =
                                                                                    newTags)
                                                                        "
                                                                    />
                                                                </div>
                                                                <jet-input-error
                                                                    :message="
                                                                        publishForm
                                                                            .errors
                                                                            .tags
                                                                    "
                                                                    class="mt-2"
                                                                />
                                                            </div>
                                                            <div class="mb-2">
                                                                <div
                                                                    class="relative pl-2"
                                                                >
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
                                                                            class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                                                                        >
                                                                            Citation
                                                                        </span>
                                                                        <button
                                                                            type="button"
                                                                            class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                                                            @click="
                                                                                toggleManageCitation
                                                                            "
                                                                        >
                                                                            <PencilIcon
                                                                                class="w-4 h-4 mr-1 text-gray-600"
                                                                            />
                                                                            <span
                                                                                >Edit</span
                                                                            >
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
                                                                            v-for="citation in project.citations"
                                                                            :key="
                                                                                citation.id
                                                                            "
                                                                            class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-top space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-teal-500"
                                                                        >
                                                                            <div
                                                                                class="flex-1 min-w-0"
                                                                            >
                                                                                <a
                                                                                    class="focus:outline-none cursor-pointer"
                                                                                    :href="
                                                                                        getCitationLink(
                                                                                            citation.doi
                                                                                        )
                                                                                    "
                                                                                    :target="
                                                                                        getTarget(
                                                                                            citation.doi
                                                                                        )
                                                                                    "
                                                                                >
                                                                                    <span
                                                                                        class="absolute inset-0"
                                                                                        aria-hidden="true"
                                                                                    ></span>
                                                                                    <p
                                                                                        class="text-sm font-medium text-gray-900"
                                                                                    >
                                                                                        {{
                                                                                            citation.title
                                                                                        }}
                                                                                    </p>
                                                                                    <p
                                                                                        class="text-sm text-teal-500"
                                                                                    >
                                                                                        {{
                                                                                            citation.authors
                                                                                        }}
                                                                                    </p>
                                                                                    <p
                                                                                        class="text-sm text-gray-500"
                                                                                    >
                                                                                        {{
                                                                                            citation.citation_text
                                                                                        }}
                                                                                    </p>
                                                                                    <p
                                                                                        v-if="
                                                                                            citation.doi
                                                                                        "
                                                                                        class="text-sm font-sm text-gray-500"
                                                                                    >
                                                                                        DOI
                                                                                        -
                                                                                        <a
                                                                                            :href="
                                                                                                citation.doi
                                                                                            "
                                                                                            class="text-teal-900"
                                                                                            >{{
                                                                                                citation.doi
                                                                                            }}</a
                                                                                        >
                                                                                    </p>
                                                                                    <p
                                                                                        class="text-sm text-gray-500 truncate ..."
                                                                                    >
                                                                                        {{
                                                                                            citation.abstract
                                                                                        }}
                                                                                        ...
                                                                                    </p>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </dd>
                                                            </div>
                                                            <div class="mb-2">
                                                                <div
                                                                    class="relative pl-2"
                                                                >
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
                                                                            class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                                                                        >
                                                                            Author
                                                                        </span>
                                                                        <button
                                                                            type="button"
                                                                            class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                                                            @click="
                                                                                toggleManageAuthor
                                                                            "
                                                                        >
                                                                            <PencilIcon
                                                                                class="w-4 h-4 mr-1 text-gray-600"
                                                                            />
                                                                            <span
                                                                                >Edit</span
                                                                            >
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
                                                                            :authors="
                                                                                project.authors
                                                                            "
                                                                        />
                                                                    </div>
                                                                </dd>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"
                                                        >
                                                            Release Date
                                                        </label>
                                                        <Datepicker
                                                            v-model="
                                                                publishForm.releaseDate
                                                            "
                                                        ></Datepicker>
                                                        <p
                                                            class="mt-1 text-sm text-gray-500"
                                                        >
                                                            Publish your data
                                                            now or choose a
                                                            release date to auto
                                                            publish your project
                                                            to public.
                                                        </p>
                                                    </div>
                                                    <div class="mt-5">
                                                        <h3
                                                            class="text-lg font-bold text-gray-400"
                                                        >
                                                            Terms & Conditions
                                                        </h3>

                                                        <div class="mt-3">
                                                            <div class="ml-2">
                                                                <div
                                                                    class="flex items-top"
                                                                >
                                                                    <input
                                                                        type="checkbox"
                                                                        class="rounded mt-1 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                                        id="conditions"
                                                                        v-model="
                                                                            publishForm.conditions
                                                                        "
                                                                        name="conditions"
                                                                    />
                                                                    <div
                                                                        class="ml-2 text-sm"
                                                                    >
                                                                        I
                                                                        understand
                                                                        once the
                                                                        project
                                                                        is
                                                                        published,
                                                                        all the
                                                                        underlying
                                                                        studies
                                                                        and
                                                                        spectra
                                                                        will
                                                                        also be
                                                                        made
                                                                        public
                                                                        and
                                                                        agree to
                                                                        make
                                                                        this
                                                                        data
                                                                        persistently
                                                                        available
                                                                        in this
                                                                        location.
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-2">
                                                            <div class="ml-2">
                                                                <div
                                                                    class="flex items-center"
                                                                >
                                                                    <input
                                                                        type="checkbox"
                                                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                                        id="terms"
                                                                        v-model="
                                                                            publishForm.terms
                                                                        "
                                                                        name="terms"
                                                                    />
                                                                    <div
                                                                        class="ml-2 text-sm"
                                                                    >
                                                                        I agree
                                                                        to the
                                                                        <a
                                                                            target="_blank"
                                                                            :href="
                                                                                route(
                                                                                    'terms.show'
                                                                                )
                                                                            "
                                                                            class="underline text-sm text-gray-600 hover:text-gray-900"
                                                                            >Terms
                                                                            of
                                                                            Service</a
                                                                        >
                                                                        and
                                                                        <a
                                                                            target="_blank"
                                                                            :href="
                                                                                route(
                                                                                    'policy.show'
                                                                                )
                                                                            "
                                                                            class="underline text-sm text-gray-600 hover:text-gray-900"
                                                                            >Privacy
                                                                            Policy</a
                                                                        >
                                                                        and
                                                                        hereby
                                                                        also
                                                                        grant
                                                                        nmrXiv
                                                                        permissions
                                                                        to
                                                                        distribute
                                                                        the
                                                                        datasets
                                                                        (and
                                                                        meta-data)
                                                                        under
                                                                        the
                                                                        specified
                                                                        license.
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="px-8 pb-8 pt-0">
                                                        <button
                                                            type="button"
                                                            :class="[
                                                                !publishForm.terms ||
                                                                !publishForm.conditions
                                                                    ? 'bg-gray-200 cursor-not-allowed'
                                                                    : 'bg-green-600 hover:bg-green-700',
                                                                'inline-flex w-full justify-center rounded-md border border-transparent px-4 py-2 text-base font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:w-auto sm:text-sm',
                                                            ]"
                                                            @click="publish"
                                                            :disabled="
                                                                !publishForm.terms &&
                                                                !publishForm.conditions
                                                            "
                                                        >
                                                            Publish
                                                        </button>
                                                        <button
                                                            type="button"
                                                            class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                                            @click="
                                                                open = false
                                                            "
                                                        >
                                                            Not right yet
                                                        </button>
                                                    </div>
                                                    <div v-if="errors">
                                                        <div
                                                            class="rounded-md bg-red-50 p-4 mx-4 mb-4"
                                                        >
                                                            <div class="flex">
                                                                <div
                                                                    class="flex-shrink-0"
                                                                >
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
                                                                <div
                                                                    class="ml-3"
                                                                >
                                                                    <h3
                                                                        class="text-sm font-medium text-red-800"
                                                                    >
                                                                        Error
                                                                        publishing
                                                                        your
                                                                        project
                                                                    </h3>
                                                                    <div
                                                                        class="mt-2 text-sm text-red-700"
                                                                    >
                                                                        <ul
                                                                            role="list"
                                                                            class="list-disc space-y-1 pl-5"
                                                                        >
                                                                            <li>
                                                                                {{
                                                                                    errors
                                                                                }}
                                                                            </li>
                                                                        </ul>
                                                                    </div>
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
                                                                Upon clicking
                                                                publish, your
                                                                project is
                                                                submitted to our
                                                                queue system for
                                                                automatic
                                                                processing. Once
                                                                successfully
                                                                processed, your
                                                                data is assigned
                                                                with stable
                                                                identifiers, and
                                                                DOIs are
                                                                generated. You
                                                                will receive an
                                                                email with
                                                                citation details
                                                                and other
                                                                helpful
                                                                information to
                                                                share your
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-[calc(100vh-260px)] text-center py-12" v-else>
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
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import TeamProjects from "@/Pages/Project/Index.vue";
import Create from "@/Shared/CreateButton.vue";
import Onboarding from "@/App/Onboarding.vue";
import { Link } from "@inertiajs/inertia-vue3";
import { computed } from "vue";
import FileSystemBrowser from "./../Shared/FileSystemBrowser.vue";
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
import Primer from "@/Shared/Primer.vue";

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
        AppLayout,
        computed,
        TeamProjects,
        Create,
        Onboarding,
        FileSystemBrowser,
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
        Primer,
    },
    props: ["draft_id"],
    setup() {},

    data() {
        return {
            openCreateDatasetDialog: false,
            currentDraft: null,
            loadingStep: false,
            selectedStudy: null,

            drafts: [],

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

            smiles: "",
            percentage: 1,
            editor: null,

            studyForm: this.$inertia.form({
                _method: "POST",
                name: "",
                description: "",
                error_message: null,
                tags: [],
                tag: "",
                tags_array: [],
            }),

            loading: false,
            showPrimer: false,
            returnUrl: "/dashboard",
            validationStatus: true,

            steps: [
                {
                    id: "01",
                    step: "tour-step-submission-header",
                    name: "Files Upload",
                    description: "Vitae sed mi luctus laoreet.",
                    href: "#",
                    status: "upcoming",
                },
                {
                    id: "02",
                    step: "v-step-20",
                    name: "Assignments & Metadata",
                    description: "Cursus semper viverra.",
                    href: "#",
                    status: "upcoming",
                },
                {
                    id: "03",
                    name: "Complete ~ Community Standards",
                    description: "Penatibus eu quis ante.",
                    href: "#",
                    status: "upcoming",
                },
            ],

            tabs: [
                { name: "Experiments (Spectra)", current: true },
                { name: "Sample Info", current: false },
                { name: "Metadata", current: false },
            ],

            publishForm: this.$inertia.form({
                _method: "POST",
                project: {
                    name: "",
                    description: "",
                    error_message: null,
                    tags: [],
                    tag: "",
                    tags_array: [],
                    owner_id: null,
                },
                conditions: false,
                terms: false,
                enableProjectMode: false,
                releaseDate: this.setReleaseDate(),
            }),

            errors: null,
        };
    },

    setup() {
        const manageAuthorElement = ref(null);
        const manageCitationElement = ref(null);
        return {
            manageAuthorElement,
            manageCitationElement,
        };
    },

    mounted() {
        this.fetchDrafts().then((response) => {
            this.drafts = response.data.drafts;
            this.sharedDrafts = response.data.sharedDrafts;
            if (this.draft_id) {
                let selectedDraft = this.drafts.find(
                    (d) => d.id == this.draft_id
                );
                if (!selectedDraft) {
                    selectedDraft = this.sharedDrafts.find(
                        (d) => d.id == this.draft_id
                    );
                }
                this.selectDraft(selectedDraft);
                this.loading = false;
            } else {
                this.defaultDraft = response.data.default;
                if (this.drafts.length == 0) {
                    this.currentDraft = this.defaultDraft;
                    this.loading = false;
                } else {
                    this.loading = false;
                }
            }
        });
        this.showPrimer = !this.primed;
    },

    computed: {
        mailFromAddress() {
            return String(this.$page.props.mailFromAddress);
        },

        mailTo() {
            return "mailto:" + String(this.$page.props.mailFromAddress);
        },
    },

    methods: {
        updateDraft(e){
            console.log(e.target.innerText)
            this.currentDraft.name = e.target.innerText
            axios.put( "/dashboard/drafts/" +
                            this.currentDraft.id 
                    , this.currentDraft)
                    .then((response) => {
                        this.currentDraft = response.data
                    });
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
            var current_date = new Date();
            var relase_date = new Date();
            relase_date.setDate(current_date.getDate());
            return relase_date;
        },
        process() {
            this.errorMessage = null;
            let foldersExist = false;
            this.$refs.fsbRef.file.children.forEach((fso) => {
                if (fso.has_children) {
                    foldersExist = true;
                }
            });

            this.hasStudies(this.$refs.fsbRef.file);

            if (
                this.$refs.fsbRef.file &&
                this.$refs.fsbRef.file.children.length > 0 &&
                foldersExist &&
                this.studiesExist
            ) {
                this.loadingStep = true;
                this.draftForm.owner_id = this.$page.props.user.id;
                this.draftForm.tags_array = this.draftForm.tags.map(
                    (a) => a.text
                );
                axios
                    .post(
                        "/dashboard/drafts/" +
                            this.currentDraft.id +
                            "/process",
                        this.draftForm
                    )
                    .then(
                        (response) => {
                            this.loadingStep = false;
                            this.project = response.data.project;
                            this.studies = response.data.studies;
                            if (
                                this.project &&
                                this.studies &&
                                this.studies.length > 0
                            ) {
                                this.selectStudy(this.studies[0], 0);
                                this.selectedDataset =
                                    this.selectedStudy.datasets[0];
                                this.loadingStep = false;
                                this.selectStep(2);
                            } else {
                                if (this.studies.length == 0) {
                                    this.loadingStep = false;
                                }
                            }
                        },
                        (error) => {
                            this.loadingStep = false;

                            Object.keys(error.response.data.errors).forEach(
                                (key) => {
                                    error.response.data.errors[key] =
                                        error.response.data.errors[key].join(
                                            ", "
                                        );
                                }
                            );
                            this.draftForm.errors = error.response.data.errors;
                            this.draftForm.error_message =
                                error.response.data.message;
                            this.draftForm.hasErrors = true;
                            Object.keys(this.draftForm.errors).forEach(
                                (key) => {
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
                                }
                            );
                        }
                    );
            } else {
                if (
                    this.$refs.fsbRef.file.children.length > 0 &&
                    !foldersExist
                ) {
                    this.errorMessage =
                        "Spectra files needs to be organised into folders. Please create a folder corresponding to each sample and add all your NMR spectroscopic experiment output files are added to the corresponding folders";
                } else if (this.$refs.fsbRef.file.children.length <= 0) {
                    this.errorMessage =
                        "Please upload spectral data to proceed.";
                } else if (!this.studiesExist) {
                    this.errorMessage =
                        "Please upload spectral data to proceed.";
                } else {
                    this.errorMessage =
                        "Please make sure you fill in all the required data before you proceed";
                }
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
        saveMolecule(mol) {
            if (!mol) {
                let mol = this.editor.getMolFile();
                axios
                    .post(
                        "https://www.cheminfo.org/webservices/inchi",
                        "molfile=" + mol
                    )
                    .then((response) => {
                        let InChI = response.data.output.InChI;
                        let InChIKey = response.data.output.InChIKey;
                        let MF = this.editor
                            .getMolecule()
                            .getMolecularFormula().formula;
                        axios
                            .post(
                                "/dashboard/studies/" +
                                    this.selectedStudy.id +
                                    "/molecule",
                                {
                                    InChI: InChI,
                                    InChIKey: InChIKey,
                                    percentage: this.percentage,
                                    formula: MF,
                                    mol: mol,
                                }
                            )
                            .then((res) => {
                                this.selectedStudy.sample.molecules = res.data;
                                this.smiles = "";
                                this.percentage = 0;
                                // this.editor.setSmiles("");
                            });
                    });
            } else {
                axios
                    .post(
                        "/dashboard/studies/" +
                            this.selectedStudy.id +
                            "/molecule",
                        {
                            InChI: mol.inchi,
                            InChIKey: mol.inchikey,
                            percentage: 0,
                            mol: mol.standardized_mol,
                            canonical_smiles: mol.canonical_smiles,
                        }
                    )
                    .then((res) => {
                        this.selectedStudy.sample.molecules = res.data;
                        this.smiles = "";
                        this.percentage = 0;
                        // this.editor.setSmiles("");
                    });
            }
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
                    this.selectedStudy.sample.molecules = res.data;
                    this.smiles = "";
                    this.percentage = 0;
                    this.editor.setSmiles("");
                });
        },
        getSVGString(molecule) {
            if (molecule.MOL) {
                let mol = OCL.Molecule.fromMolfile(
                    "\n  " + molecule.MOL.replaceAll('"', "")
                );
                return mol.toSVG(200, 200);
            } else {
                console.log(molecule);
            }
        },
        selectStudy(study, index, datasetIndex = null) {
            this.selectedStudyIndex = index;
            this.selectedStudy = study;
            this.studyForm.name = this.selectedStudy.name;
            this.studyForm.description = this.selectedStudy.description;
            let tags = [];
            this.selectedStudy.tags.forEach((t) => {
                tags.push({
                    text: t.name["en"],
                });
            });
            this.studyForm.tags = tags;

            axios
                .get(
                    "/dashboard/studies/" + this.selectedStudy.id + "/nmredata"
                )
                .then((response) => {
                    let nmredataFiles = response.data;
                    nmredataFiles.forEach((file) => {
                        // get file content
                        let username =
                            this.$page.props.team && this.$page.props.team.owner
                                ? this.$page.props.team.owner.username
                                : this.project.owner.username;
                        let url =
                            this.url +
                            "/" +
                            username +
                            "/studies" +
                            "/" +
                            this.selectedStudy.id +
                            "/file/" +
                            file.slug;
                        axios.get(url).then((response) => {
                            // convert to smiles
                            axios
                                .post(
                                    "https://dev.api.naturalproducts.net/latest/chem/standardize",
                                    response.data
                                )
                                .then((res) => {
                                    let STANDARD_INCHI = res.data.inchi;
                                    let molExists = false;
                                    this.selectedStudy.sample.molecules.forEach(
                                        (mol) => {
                                            if (
                                                mol.STANDARD_INCHI ==
                                                STANDARD_INCHI
                                            ) {
                                                molExists = true;
                                            }
                                        }
                                    );
                                    if (!molExists) {
                                        this.saveMolecule(res.data);
                                    }
                                });
                        });

                        // save molecule if doesnt exist
                    });
                });

            if (!datasetIndex) {
                this.selectedDSIndex = 0;
            } else {
                this.selectedDSIndex = datasetIndex;
            }
        },
        selectTab(tab) {
            this.tabs.forEach((t) => {
                if (t.name == tab.name) {
                    t.current = true;
                    this.$nextTick(() => {
                        this.editor = OCL.StructureEditor.createSVGEditor(
                            "structureSearchEditor",
                            1
                        );
                    });
                } else {
                    t.current = false;
                }
            });
        },
        toggleManageAuthor() {
            this.manageAuthorElement.toggleDialog();
        },
        toggleManageCitation() {
            this.manageCitationElement.toggleDialog();
        },
        closeDraft() {
            this.loadingStep = true;
            axios
                .post(
                    "/dashboard/drafts/" + this.currentDraft.id + "/complete",
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
                    this.validationStatus = true;
                    this.validation.project.studies.forEach((study) => {
                        if (study.status == false) {
                            this.validationStatus = false;
                        }
                    });
                    if (this.project) {
                        // this.loadingStep = false;
                        // this.loadLicenses();
                        // this.selectStep(3);
                        // this.publishForm.project.name = this.draftForm.name;
                        // this.publishForm.project.description =
                        //     this.draftForm.description;
                        // this.publishForm.project.tags = this.draftForm.tags;
                        // this.trackProject();
                        window.location = "/publish/" + this.currentDraft.id
                    }
                });
        },
        publish() {
            if (this.publishForm.conditions && this.publishForm.terms) {
                this.errors = null;
                axios
                    .post(
                        route("dashboard.project.publish", this.project.id),
                        this.form
                    )
                    .catch((err) => {
                        this.errors = err.response.data.errors;
                    })
                    .then((response) => {
                        this.status = response.data.project.status;
                        this.trackProject();
                    });
            }
        },
        trackProject() {
            axios
                .get("/projects/status/" + this.project.id + "/queue")
                .then((response) => {
                    this.status = response.data.status;
                    if (this.status != "complete") {
                        setTimeout(() => this.trackProject(), 10000);
                    } else {
                        return this.$inertia.visit(
                            this.route("dashboard.projects", [this.project.id])
                        );
                    }
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
        createNewDraft() {
            if (!this.primed) {
                this.showPrimer = true;
                this.newDraft();
            } else {
                this.newDraft();
            }
        },
        newDraft() {
            if (this.defaultDraft) {
                this.defaultDraft.name =
                    "Untitled Project - " + new Date().toLocaleString();
                this.selectDraft(this.defaultDraft);
            } else {
                this.fetchDrafts().then((response) => {
                    this.defaultDraft = response.data.default;
                    this.loading = false;
                    this.defaultDraft.name =
                        "Untitled Project - " + new Date().toLocaleString();
                    this.selectDraft(this.defaultDraft);
                });
            }
        },
        selectDraft(draft) {
            this.currentDraft = draft;
            this.draftForm.name = this.currentDraft.name;
            this.draftForm.description = this.currentDraft.description;
            let tags = [];
            this.file = null;
            this.draftForm.tags = [];
            if (this.currentDraft.tags) {
                this.currentDraft.tags.forEach((t) => {
                    tags.push({
                        text: t.name["en"],
                    });
                });
                this.draftForm.tags = tags;
            }
            this.selectStep(1);
        },
        updateLoadingStatus(status) {
            console.log(status)
            this.loading = status;
        },
        fetchDrafts() {
            this.loading = true;
            return axios.get("/dashboard/drafts");
        },
        selectStep(id) {
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
                // this.loadingStep = true;
                this.$nextTick(function () {
                    if (this.$refs.fsbRef) {
                        this.$refs.fsbRef.annotate();
                    }
                });
            } else if (id == 2) {
                this.$nextTick(function () {
                    if (this.$refs.spectraEditorREF) {
                        this.$refs.spectraEditorREF.registerEvents();
                    }
                });
            }
        },
        hidePrimer() {
            axios.post("/primer/skip").then(() => {
                Inertia.reload({
                    only: ["user", "user.permissions", "user.roles"],
                });
            });
        },
        skipPrimer() {
            this.showPrimer = false;
            this.selectStep(1);
        },
        saveStudyDetails() {
            this.loadingStep = true;
            this.studyForm.tags_array = this.studyForm.tags.map((a) => a.text);

            axios
                .put(
                    "/dashboard/studies/" + this.selectedStudy.id + "/update",
                    this.studyForm
                )
                .catch((error) => {
                    this.loadingStep = false;
                    Object.keys(error.response.data.errors).forEach((key) => {
                        error.response.data.errors[key] =
                            error.response.data.errors[key].join(", ");
                    });
                    this.studyForm.errors = error.response.data.errors;
                    this.studyForm.error_message = error.response.data.message;
                    this.studyForm.hasErrors = true;
                })
                .then((response) => {
                    if (response) {
                        this.loadingStep = false;
                        this.selectStudy(
                            response.data,
                            this.selectedStudyIndex
                        );
                        this.studyForm.hasErrors = false;
                    }
                });
        },
        autoGenerateDescription() {
            let desc =
                "This dataset contains NMR spectra obtained for the sample -" +
                this.selectedStudy.name +
                "</br>";
            let sampleNames = [];
            let sampleTags = [];
            this.selectedStudy.datasets.forEach((ds) => {
                if (ds.nmrium_info) {
                    let nmriuminfo = JSON.parse(ds.nmrium_info);
                    if (nmriuminfo) {
                        let spectra = nmriuminfo.spectra;
                        spectra.forEach((sp) => {
                            desc =
                                desc +
                                "Experiment: " +
                                sp.info.experiment +
                                "</br>";
                            desc =
                                desc + "Nucleus: " + sp.info.nucleus + "</br>";
                            desc = desc + "Type: " + sp.info.type + "</br>";
                            desc =
                                desc +
                                "Temperature: " +
                                sp.info.temperature +
                                "</br>";
                            desc =
                                desc + "Solvent: " + sp.info.solvent + "</br>";
                            desc =
                                desc +
                                "Origin Frequency: " +
                                sp.info.originFrequency +
                                "</br>";
                            desc =
                                desc +
                                "Probe Name: " +
                                sp.info.probeName +
                                "</br>";
                            sampleTags.push(
                                this.isString(sp.info.nucleus)
                                    ? sp.info.nucleus
                                    : sp.info.nucleus.join(" - ")
                            );
                            if (sp.info.sampleName) {
                                sampleNames.push(sp.info.sampleName);
                                desc =
                                    desc +
                                    "Sample Name: " +
                                    sp.info.sampleName +
                                    "</br>";
                            }
                        });
                        desc = desc + "</br></br>";
                    }
                }
            });
            this.studyForm.description = desc;
            if (sampleNames.length > 0) {
                sampleNames = [...new Set(sampleNames)];
                let sampleName = sampleNames.join(", ");
                if (sampleName != "") {
                    this.studyForm.name = sampleNames.join(", ");
                }
            }
            if (sampleTags.length > 0) {
                sampleTags = [...new Set(sampleTags)];
                let tags = [];
                sampleTags.forEach((t) => {
                    tags.push({
                        text: t,
                    });
                });
                this.studyForm.tags = tags;
            }
        },
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
        currentStep() {
            return this.steps.filter((s) => s.status == "current")[0];
        },
        currentTab() {
            return this.tabs.find((t) => t.current);
        },
    },
};
</script>
