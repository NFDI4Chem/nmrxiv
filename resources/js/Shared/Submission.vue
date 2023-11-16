<template>
    <jet-dialog-modal
        :max-width="'7xl'"
        :show="openCreateDatasetDialog"
        @close="openCreateDatasetDialog = false"
    >
        <template #title>
            <div v-if="!showPrimer">
                <div
                    v-if="!loading && currentDraft"
                    class="lg:border-b lg:border-gray-200 pb-3 pr-5 -mx-6"
                >
                    <div
                        id="tour-step-submission-header"
                        class="flex items-left justify-start pl-5 float-left"
                    >
                        <b>{{
                            steps.find((step) => step.status === "current").name
                        }}</b>
                    </div>
                    <nav
                        class="flex items-right justify-end"
                        aria-label="Progress"
                    >
                        <p class="text-sm font-medium">
                            Step
                            {{
                                steps.findIndex(
                                    (step) => step.status === "current"
                                ) + 1
                            }}
                            of
                            {{ steps.length }}
                        </p>
                        <ol
                            role="list"
                            class="ml-8 flex items-center space-x-5"
                        >
                            <li v-for="step in steps" :key="step.name">
                                <a
                                    v-if="step.status === 'complete'"
                                    class="block w-2.5 h-2.5 bg-teal-600 rounded-full hover:bg-teal-900"
                                >
                                </a>
                                <a
                                    v-else-if="step.status === 'current'"
                                    class="relative flex items-center justify-center"
                                    aria-current="step"
                                >
                                    <span
                                        class="absolute w-5 h-5 p-px flex"
                                        aria-hidden="true"
                                    >
                                        <span
                                            class="w-full h-full rounded-full bg-blue-100"
                                        />
                                    </span>
                                    <span
                                        class="relative block w-2.5 h-2.5 bg-teal-600 rounded-full"
                                        aria-hidden="true"
                                    />
                                </a>
                                <a
                                    v-else
                                    class="block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400"
                                >
                                </a>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div
                    v-else
                    class="lg:border-b lg:border-gray-200 pb-3 pr-5 -mx-6"
                >
                    <div
                        v-if="!currentDraft"
                        class="flex text-md font-bold items-left justify-start pl-5"
                    >
                        Drafts
                    </div>
                </div>
            </div>
        </template>
        <template #content>
            <div
                v-if="loadingStep"
                class="absolute inset-0 flex justify-center items-center z-10 bg-white rounded bg-opacity-70"
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
                <div v-if="datasetsToImport && datasetsToImport.length > 0">
                    <br />
                    Processing
                    {{
                        datasetsToImport.filter((f) => f.status == true)
                            .length + 1
                    }}
                    of {{ datasetsToImport.length }} spectra
                </div>
            </div>
            <div v-if="!loading">
                <div v-if="drafts.length > 0 && !currentDraft">
                    <div>
                        <ul role="list" class="-my-5 divide-y divide-gray-200">
                            <li
                                v-for="draft in drafts"
                                :key="draft.id"
                                class="py-4"
                            >
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-lg font-large text-black truncate"
                                        >
                                            <b>{{ draft.name }}</b>
                                        </p>
                                        <p
                                            class="text-sm font-medium text-gray-600 truncate pr-10"
                                        >
                                            {{ draft.description }}
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
                                                formatDateTime(draft.created_at)
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <Link
                                            :href="
                                                route('dashboard', {
                                                    action: 'submission',
                                                    draft_id: draft.id,
                                                })
                                            "
                                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ml-2"
                                        >
                                            Select
                                        </Link>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="relative mt-6">
                        <div
                            class="absolute inset-0 flex items-center"
                            aria-hidden="true"
                        >
                            <div class="w-full border-t border-gray-300" />
                        </div>
                        <div class="relative flex justify-center">
                            <span class="px-2 bg-white text-sm text-gray-500">
                                Or
                            </span>
                        </div>
                    </div>
                    <div class="my-6 justify-center items-center flex">
                        <button
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                            @click="createNewDraft()"
                        >
                            Create New
                        </button>
                    </div>
                </div>
                <div v-else>
                    <div v-if="showPrimer" class="-mx-6 -my-4">
                        <div>
                            <section
                                class="h-1/2 overflow-hidden bg-white overflow-hidden py-12"
                            >
                                <div
                                    class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
                                >
                                    <svg
                                        class="absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2"
                                        width="404"
                                        height="404"
                                        fill="none"
                                        viewBox="0 0 404 404"
                                        role="img"
                                        aria-labelledby="svg-nmrxiv"
                                    >
                                        <title id="svg-nmrxiv">nmrxiv</title>
                                        <defs>
                                            <pattern
                                                id="ad119f34-7694-4c31-947f-5c9d249b21f3"
                                                x="0"
                                                y="0"
                                                width="20"
                                                height="20"
                                                patternUnits="userSpaceOnUse"
                                            >
                                                <rect
                                                    x="0"
                                                    y="0"
                                                    width="4"
                                                    height="4"
                                                    class="text-gray-200"
                                                    fill="currentColor"
                                                ></rect>
                                            </pattern>
                                        </defs>
                                        <rect
                                            width="404"
                                            height="404"
                                            fill="url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                                        ></rect>
                                    </svg>
                                    <div class="relative">
                                        <blockquote>
                                            <div
                                                class="max-w-3xl mx-auto text-left text-xl leading-9 font-medium text-gray-900"
                                            >
                                                <p
                                                    class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"
                                                >
                                                    Basic Concepts
                                                </p>
                                                <p class="mt-3 text-lg">
                                                    In nmrXiv, data is organised
                                                    as
                                                    <b class="text-teal-700"
                                                        >projects</b
                                                    >. Consider a project is
                                                    equivalent to your
                                                    publication with the
                                                    corresponding NMR data to be
                                                    uploaded to nmrXiv. A
                                                    project can have multiple
                                                    studies.
                                                    <b class="text-teal-700"
                                                        >A study corresponds to
                                                        a group of NMR
                                                        experiments of one
                                                        sample</b
                                                    >, e.g. 1H, 13C, APT, COSY
                                                    HSQC, HMBC, NOESY in Bruker
                                                    Format or (another study) in
                                                    another format such as
                                                    JCAMP-DX / Varian.
                                                    <b class="text-teal-700"
                                                        >Each NMR measurement in
                                                        a study is referred to
                                                        as a dataset</b
                                                    >, e.g. 1H NMR or COSY (are
                                                    each a dataset).
                                                </p>
                                                <img
                                                    src="/img/primer.png"
                                                    alt=""
                                                />
                                                <p
                                                    class="text-md text-center leading-5 mb-3"
                                                >
                                                    <small
                                                        >nmrXiv allows you to
                                                        upload NMR raw data from
                                                        any NMR instrument. We
                                                        can currently
                                                        auto-detect
                                                        Bruker/Varian/JOEL
                                                        formats & JCAMP files
                                                        and will support more
                                                        raw & processed file
                                                        formats soon. Once you
                                                        upload your raw or
                                                        processed NMR data,
                                                        nmrXiv will
                                                        auto-generate the
                                                        studies and datasets for
                                                        you based on the
                                                        uploaded folder
                                                        structure.
                                                        <i
                                                            ><a
                                                                class="text-xs"
                                                                target="_blank"
                                                                href="https://docs.nmrxiv.org/docs/submission-guides/submission/folder-structure"
                                                                >More info
                                                                here</a
                                                            ></i
                                                        ></small
                                                    >
                                                </p>
                                            </div>
                                        </blockquote>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    <div v-else>
                        <div v-if="currentStep">
                            <div
                                v-if="currentStep.id == '01'"
                                id="submission-dropzone"
                            >
                                <div v-if="currentDraft">
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
                                        <div class="mt-1">
                                            <input
                                                v-model="draftForm.name"
                                                type="text"
                                                name="project-name"
                                                class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border-gray-300 rounded-md"
                                            />
                                        </div>
                                        <jet-input-error
                                            :message="draftForm.errors.name"
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
                                                    draftForm.description =
                                                        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore'
                                                "
                                                >Project Description
                                                (Optional)</span
                                            >
                                        </label>
                                        <div class="mt-1">
                                            <textarea
                                                id="description"
                                                v-model="draftForm.description"
                                                name="description"
                                                placeholder="Describe this project"
                                                rows="3"
                                                class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border border-gray-300 rounded-md"
                                            ></textarea>
                                        </div>
                                        <jet-input-error
                                            :message="
                                                draftForm.errors.description
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
                                            Keywords (Optional)
                                        </label>
                                        <div>
                                            <vue-tags-input
                                                v-model="draftForm.tag"
                                                placeholder="Type a keyword or keywords separated by comma (,) and press enter"
                                                :separators="[';', ',']"
                                                max-width="100%"
                                                :tags="draftForm.tags"
                                                @tags-changed="
                                                    (newTags) =>
                                                        (draftForm.tags =
                                                            newTags)
                                                "
                                            />
                                        </div>
                                        <jet-input-error
                                            :message="draftForm.errors.tags"
                                            class="mt-2"
                                        />
                                    </div>
                                    <div>
                                        <div>
                                            <small class="cursor-pointer"
                                                >Draft ID:
                                                {{ currentDraft.key }}</small
                                            >
                                            <div id="tour-step-upload-spectra">
                                                <file-system-browser
                                                    ref="fsbRef"
                                                    :readonly="false"
                                                    :draft="currentDraft"
                                                    @loading="
                                                        updateLoadingStatus
                                                    "
                                                ></file-system-browser>
                                            </div>
                                            <jet-input-error
                                                :message="
                                                    draftForm.errors.studies
                                                "
                                                class="mt-2"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                v-if="currentStep.id == '02'"
                                class="-mx-6 -my-4"
                            >
                                <div style="height: 80vh">
                                    <div class="flex-1 flex md:overflow-hidden">
                                        <nav
                                            aria-label="Sections"
                                            class="hidden flex-shrink-0 w-80 bg-white border-r border-blue-gray-200 md:flex md:flex-col"
                                        >
                                            <div
                                                class="border-gray-200 px-4 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 tracking-wider flex-shrink-0 border-b border-blue-gray-200"
                                            >
                                                {{
                                                    pluralize(
                                                        "STUDY",
                                                        studies.length
                                                    )
                                                }}
                                                ({{ studies.length }})
                                                <div
                                                    class="float-right cursor-pointer tooltip"
                                                    @click="autoImport()"
                                                >
                                                    <ArrowDownOnSquareStackIcon
                                                        class="w-5 h-5 mr-1 text-gray-600 hover:text-gray-500"
                                                    />
                                                    <span
                                                        class="bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextbottom"
                                                        >Click to auto import
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
                                                        'cursor-pointer flex p-4 pr-5 border-b border-blue-gray-200',
                                                    ]"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="flex-shrink-0 -mt-0.5 h-6 w-6 text-blue-gray-400"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"
                                                        />
                                                    </svg>
                                                    <div
                                                        class="ml-3 text-sm w-full"
                                                    >
                                                        <p
                                                            class="font-medium text-blue-gray-900 pr-4"
                                                        >
                                                            <a
                                                                class="hover:font-bold"
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
                                                                    study.sample
                                                                        .molecules
                                                                        .length >
                                                                    0
                                                                "
                                                                class="float-right"
                                                            >
                                                                <img
                                                                    class="flex-shrink-0 -mt-0.5 mr-2 h-6 w-6 text-blue-gray-400"
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
                                                                    ds, $dsIndex
                                                                ) in study.datasets"
                                                                :key="ds.id"
                                                            >
                                                                <div
                                                                    :class="[
                                                                        study.has_nmrium ||
                                                                        ds.has_nmrium
                                                                            ? 'bg-green-100 text-gray-800'
                                                                            : 'bg-gray-100 text-gray-800',
                                                                        'w-64 inline-flex truncate break-words items-center px-3 py-0.5 rounded-full text-xs font-medium mr-1 hover:bg-teal-700',
                                                                    ]"
                                                                    @click="
                                                                        selectStudy(
                                                                            study,
                                                                            $index,
                                                                            $dsIndex
                                                                        )
                                                                    "
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
                                        <div
                                            style="
                                                height: 80vh;
                                                overflow: scroll !important;
                                            "
                                            class="flex-1 xl:overflow-y-auto"
                                        >
                                            <div
                                                class="mx-auto flex flex-col md:px-0 xl:px-0"
                                            >
                                                <main class="flex-1">
                                                    <div
                                                        class="relative mx-auto md:px-0 xl:px-0"
                                                    >
                                                        <div class="pt-5 pb-16">
                                                            <div
                                                                class="px-3 sm:px-3"
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
                                                                class="lg:hidden px-4 sm:px-6"
                                                            >
                                                                <label
                                                                    for="selected-tab"
                                                                    class="sr-only"
                                                                    >Select a
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
                                                                class="hidden lg:block px-3 sm:px-3"
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
                                                                v-if="
                                                                    currentTab.name ==
                                                                    'Metadata'
                                                                "
                                                                class="px-4 sm:px-6"
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
                                                                            Study
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
                                                                            Study
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
                                                                v-if="
                                                                    currentTab.name ==
                                                                    'Experiments (Spectra)'
                                                                "
                                                                id="tour-step-nmrium"
                                                                class="px-4 sm:px-6 md:px-0"
                                                            >
                                                                <div
                                                                    class="p-3"
                                                                >
                                                                    <!-- <div>
                                                                        <label
                                                                            for="location"
                                                                            class="block text-sm font-medium text-gray-700"
                                                                            >Select
                                                                            Spectra
                                                                        </label>
                                                                        <select
                                                                            id="tour-step-select-exp"
                                                                            v-model="
                                                                                selectedDataset
                                                                            "
                                                                            name="location"
                                                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm rounded-md"
                                                                        >
                                                                            <option
                                                                                v-for="dataset in selectedStudy.datasets.sort(
                                                                                    (
                                                                                        a,
                                                                                        b
                                                                                    ) =>
                                                                                        a.name >
                                                                                        b.name
                                                                                            ? 1
                                                                                            : -1
                                                                                )"
                                                                                :key="
                                                                                    dataset.slug
                                                                                "
                                                                                :value="
                                                                                    dataset
                                                                                "
                                                                            >
                                                                                {{
                                                                                    dataset.name
                                                                                }}
                                                                                <span
                                                                                    v-if="
                                                                                        dataset.type
                                                                                    "
                                                                                >
                                                                                    ({{
                                                                                        dataset.type
                                                                                    }})
                                                                                </span>
                                                                            </option>
                                                                        </select>
                                                                    </div> -->
                                                                    <div
                                                                        class="my-3"
                                                                    >
                                                                        <SpectraEditor
                                                                            ref="spectraEditorREF"
                                                                            :dataset="
                                                                                selectedDataset
                                                                            "
                                                                            :project="
                                                                                project
                                                                            "
                                                                            :study="
                                                                                selectedStudy
                                                                            "
                                                                            @loading="
                                                                                updateLoadingStatus
                                                                            "
                                                                        ></SpectraEditor>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div
                                                                v-if="
                                                                    currentTab.name ==
                                                                    'Sample Info'
                                                                "
                                                                class="px-4 sm:px-6 pt-4"
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
                                                                        to the
                                                                        subtle
                                                                        but
                                                                        significant
                                                                        convolution
                                                                        of major
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
                                                                        of these
                                                                        chemical
                                                                        species
                                                                        (molecules)
                                                                        usually
                                                                        remain
                                                                        present
                                                                        even
                                                                        after a
                                                                        number
                                                                        of
                                                                        purification
                                                                        steps,
                                                                        and this
                                                                        RC is to
                                                                        some
                                                                        degree
                                                                        conserved
                                                                        even in
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
                                                                        or other
                                                                        purification
                                                                        steps
                                                                        are
                                                                        required
                                                                        prior to
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
                                                                        of your
                                                                        sample.
                                                                        If
                                                                        unknown
                                                                        please
                                                                        report
                                                                        the
                                                                        molecules
                                                                        that you
                                                                        expect
                                                                        to be
                                                                        present
                                                                        in your
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
                                                                                        molecule.standard_inchi
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
                                                                                                                molecule.standard_inchi
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
                                                                                    errorMessage
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
                                                                                saveMolecule
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
                                                                                    Create
                                                                                    a
                                                                                    new
                                                                                    database
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
                                                </main>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="currentStep.id == '03'">
                                <Validation
                                    :project="project"
                                    :validation="validation"
                                ></Validation>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
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
            <div
                v-if="errorMessage != null && errorMessage != ''"
                class="rounded-md bg-red-50 p-4 mb-2"
            >
                <div class="flex">
                    <div class="flex-shrink-0">
                        <ExclamationTriangleIcon
                            class="h-5 w-5 text-red-400"
                            aria-hidden="true"
                        />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-400">
                            There were errors with your submission
                        </h3>
                        <div
                            class="mt-2 text-sm text-red-900"
                            v-html="errorMessage"
                        ></div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <div class="flex">
                <div v-if="showPrimer && currentDraft" class="w-full">
                    <div class="float-left">
                        <div
                            class="relative mt-2 flex items-start"
                            @change="hidePrimer()"
                        >
                            <div class="flex h-5 items-center">
                                <input
                                    id="comments"
                                    aria-describedby="comments-description"
                                    name="comments"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                            </div>
                            <div class="ml-3 text-sm">
                                <label
                                    for="comments"
                                    class="font-medium text-gray-700"
                                    >Don't show this again</label
                                >
                            </div>
                        </div>
                    </div>
                    <jet-button
                        class="ml-2 float-right"
                        :class="{
                            'opacity-25': createDatasetForm.processing,
                        }"
                        @click="skipPrimer()"
                    >
                        Proceed
                    </jet-button>
                </div>
                <div v-else class="w-full">
                    <span v-if="!currentStep">
                        <Link
                            class="inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                            :href="returnUrl"
                        >
                            Cancel
                        </Link>
                    </span>
                    <span v-else>
                        <span v-if="currentStep.id == '01'">
                            <jet-secondary-button
                                v-if="drafts.length > 0"
                                class="ml-2 float-left"
                                :class="{
                                    'opacity-25': createDatasetForm.processing,
                                }"
                                :disabled="createDatasetForm.processing"
                                @click="openSelectDraftsView"
                            >
                                Drafts
                            </jet-secondary-button>
                            <Link
                                class="inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                :href="returnUrl"
                            >
                                Cancel
                            </Link>
                            <jet-button
                                id="tour-step-proceed-from-step-1"
                                class="ml-2"
                                :class="{
                                    'opacity-25': createDatasetForm.processing,
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
                        <span v-else-if="currentStep.id == '02'">
                            <jet-button
                                class="ml-2 float-left"
                                :class="{
                                    'opacity-25': createDatasetForm.processing,
                                }"
                                :disabled="createDatasetForm.processing"
                                @click="selectStep(1)"
                            >
                                Back
                            </jet-button>
                            <Link
                                :href="returnUrl"
                                class="inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                            >
                                Cancel
                            </Link>
                            <jet-button
                                id="tour-step-proceed-from-step-2"
                                class="ml-2"
                                :class="{
                                    'opacity-25': createDatasetForm.processing,
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
                        <span v-else-if="currentStep.id == '03'">
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
        </template>
    </jet-dialog-modal>
</template>
<script>
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import VueTagsInput from "@sipec/vue3-tags-input";
import { ref } from "vue";
import slider from "vue3-slider";
import OCL from "openchemlib/full";
import SelectRich from "@/Shared/SelectRich.vue";
import SpectraEditor from "@/Shared/SpectraEditor.vue";
import Validation from "@/Shared/Validation.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import FileSystemBrowser from "./FileSystemBrowser.vue";
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
} from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/vue3";

export default {
    components: {
        Link,
        JetDialogModal,
        JetSecondaryButton,
        JetButton,
        VueTagsInput,
        slider,
        SelectRich,
        XCircleIcon,
        PencilIcon,
        JetInputError,
        FileSystemBrowser,
        ClipboardDocumentIcon,
        QuestionMarkCircleIcon,
        ExclamationTriangleIcon,
        ArrowDownOnSquareStackIcon,
        TrashIcon,
        PlayIcon,
        PauseIcon,
        SpectraEditor,
        Validation,
    },
    props: [],
    data() {
        return {
            openCreateDatasetDialog: false,
            loading: false,
            loadingStep: false,
            currentDraft: null,
            errorMessage: null,
            datasetsToImport: null,
            errorMessage: "",

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

            file: null,

            status: null,

            selectedStudy: null,
            studyName: "",
            studyDescription: "",
            studyTags: [],
            studyTag: "",

            selectedDataset: null,
            currentMolecules: [],

            showPrimer: false,

            smiles: "",
            percentage: 1,
            editor: null,

            updateProjectForm: this.$inertia.form({
                _method: "PUT",
                name: null,
                description: null,
                error_message: null,
                is_public: false,
                license: null,
                owner_id: null,
                team_id: null,
                license_id: null,
                releaseDate: this.setReleaseDate(),
            }),

            studyForm: this.$inertia.form({
                _method: "POST",
                name: "",
                description: "",
                error_message: null,
                tags: [],
                tag: "",
                tags_array: [],
            }),

            licenses: [],

            project: null,
            initialised: false,

            autoimportList: [],
            confirmPublicAccess: false,

            studiesExist: false,
            returnUrl: "/dashboard",
        };
    },
    computed: {
        primed() {
            return this.$page.props.user.primed;
        },

        currentStep() {
            return this.steps.filter((s) => s.status == "current")[0];
        },

        currentTab() {
            return this.tabs.find((t) => t.current);
        },

        downloadURL() {
            return (
                this.url +
                "/" +
                this.project.owner.username +
                "/datasets/" +
                this.project.slug
            );
        },

        url() {
            return String(this.$page.props.url)
                ? String(this.$page.props.url)
                : "https://dev.nmrxiv.org";
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
    },
    mounted() {
        const initialise = (data) => {
            this.fetchDrafts().then((response) => {
                this.returnUrl =
                    this.url +
                    this.returnUrl +
                    (data.return_url ? data.return_url : "");
                this.drafts = response.data.drafts;
                this.sharedDrafts = response.data.sharedDrafts;
                if (data.draft_id) {
                    let selectedDraft = this.drafts.find(
                        (d) => d.id == data.draft_id
                    );
                    if (!selectedDraft) {
                        selectedDraft = this.sharedDrafts.find(
                            (d) => d.id == data.draft_id
                        );
                    }
                    this.selectDraft(selectedDraft);
                    this.loading = false;
                    this.toggleOpenCreateDatasetDialog();
                } else {
                    this.defaultDraft = response.data.default;
                    this.toggleOpenCreateDatasetDialog();
                    if (this.drafts.length == 0) {
                        this.currentDraft = this.defaultDraft;
                        this.selectStep(1);
                        this.loading = false;
                    } else {
                        this.loading = false;
                    }
                    this.showPrimer = !this.primed;
                }
            });
        };
        this.emitter.all.set("openDatasetCreateDialog", [initialise]);
    },
    unmounted() {},
    methods: {
        hidePrimer() {
            axios.post("/primer/skip").then(() => {
                router.reload({
                    only: ["user", "user.permissions", "user.roles"],
                });
            });
            var pattern = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
        },
        skipPrimer() {
            this.showPrimer = false;
        },
        getSVGString(molecule) {
            if (molecule.MOL) {
                let mol = OCL.Molecule.fromMolfile(
                    "\n  " + molecule.MOL.replaceAll('"', "")
                );
                return mol.toSVG(200, 200);
            }
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

        updateLoadingStatus(status) {
            this.loadingStep = status;
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

        updateProject() {
            this.updateProjectForm.clearErrors();
            this.loadingStep = true;
            this.updateProjectForm.name = this.project.name;
            this.updateProjectForm.owner_id = this.project.owner_id;
            this.updateProjectForm.license_id = this.updateProjectForm.license
                ? this.updateProjectForm.license.id
                : null;
            this.updateProjectForm.team_id = this.project.team_id;
            this.updateProjectForm.description = this.project.description;
            this.updateProjectForm.post(
                route("dashboard.project.update", this.project.id),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.loadingStep = false;
                    },
                    onError: (err) => {
                        this.loadingStep = false;
                    },
                }
            );
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

        setReleaseDate() {
            var current_date = new Date();
            var relase_date = new Date();
            relase_date.setDate(current_date.getDate() + 7);
            return relase_date;
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

        editMolecule(mol) {
            this.editor.setMolFile("\n  " + mol.MOL.replaceAll('"', ""));
            this.percentage = parseInt(mol.pivot.percentage_composition);
            axios
                .delete(
                    "/dashboard/studies/" +
                        this.selectedStudy.id +
                        "/molecule/" +
                        mol.id
                )
                .then((res) => {
                    this.selectedStudy.sample.molecules = res.data;
                });
        },

        saveMolecule() {
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
                            this.editor.setSmiles("");
                        });
                });
        },

        loadSmiles() {
            this.errorMessage = "";
            if (this.smiles && this.smiles != "") {
                try {
                    let mol = OCL.Molecule.fromSmiles(this.smiles);
                    this.editor.setSmiles(this.smiles);
                } catch (e) {
                    this.errorMessage = "The entered SMILES is not valid.";
                }
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

        closeDraft() {
            this.loadingStep = true;
            axios
                .post(
                    "/dashboard/drafts/" + this.currentDraft.id + "/complete",
                    {}
                )
                .catch((error) => {
                    this.loadingStep = false;
                })
                .then((response) => {
                    this.project = response.data.project;
                    this.validation = this.parseJSON(
                        response.data.validation.report
                    );
                    if (this.project) {
                        this.loadingStep = false;
                        this.loadLicenses();
                        this.selectStep(3);
                        // this.trackProject();
                    }
                });
        },

        trackProject() {
            axios
                .get("/projects/status/" + this.project.id + "/queue")
                .then((response) => {
                    this.project.status = response.data.status;
                    if (this.project.status != "complete") {
                        setTimeout(() => this.trackProject(), 10000);
                    }
                });
        },

        openSelectDraftsView() {
            this.steps.forEach((s) => {
                s.status = "upcoming";
            });
            this.currentDraft = null;
        },

        selectDataset(dataset) {
            this.selectedDataset = dataset;
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
            if (!datasetIndex) {
                this.selectedDSIndex = 0;
                this.selectDataset(this.selectedStudy.datasets[0]);
            } else {
                this.selectedDSIndex = datasetIndex;
                this.selectDataset(this.selectedStudy.datasets[datasetIndex]);
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

        fetchProjectData() {
            axios
                .get("/dashboard/drafts/" + this.currentDraft.id + "/info")
                .then((response) => {
                    this.project = response.data.project;
                    this.studies = response.data.studies;
                    if (
                        this.project &&
                        this.studies &&
                        this.studies.length > 0
                    ) {
                        this.selectStudy(this.studies[0], 0);
                        this.selectedDataset = this.selectedStudy.datasets[0];
                        this.loadingStep = false;
                        this.selectStep(2);
                    } else {
                        if (this.studies.length == 0) {
                            this.loadingStep = false;
                        }
                    }
                });
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
                        // this.$refs.spectraEditorREF.registerEvents();
                    }
                });
            }
        },

        toggleOpenCreateDatasetDialog() {
            this.openCreateDatasetDialog = !this.openCreateDatasetDialog;
        },

        autoImport() {
            this.loadingStep = true;
            this.datasetsToImport = [];
            this.studies.forEach((study) => {
                study.datasets.forEach((dataset) => {
                    if (!dataset.has_nmrium) {
                        this.datasetsToImport.push({
                            projectSlug: this.project.slug,
                            studySlug: study.slug,
                            datasetSlug: dataset.slug,
                            datasetId: dataset.id,
                            status: false,
                        });
                    }
                });
            });
            this.fetchNMRium();
        },

        fetchNMRium() {
            let datasetDetails = this.datasetsToImport.filter(
                (f) => f.status == false
            )[0];
            if (datasetDetails) {
                this.loadingStep = true;
                let ownerUserName = this.$page.props.team
                    ? this.$page.props.team.owner.username
                    : this.project.owner.username;
                axios
                    .post("https://nodejsdev.nmrxiv.org/spectra-parser", {
                        urls: [
                            this.url +
                                "/" +
                                ownerUserName +
                                "/datasets/" +
                                datasetDetails.projectSlug +
                                "/" +
                                datasetDetails.studySlug +
                                "/" +
                                datasetDetails.datasetSlug,
                        ],
                        snapshot: false,
                    })
                    .then((response) => {
                        axios
                            .post(
                                "/dashboard/datasets/" +
                                    datasetDetails.datasetId +
                                    "/nmriumInfo",
                                response.data.data
                            )
                            .then((res) => {
                                this.loadingStep = false;
                                this.datasetsToImport.filter(
                                    (f) =>
                                        f.datasetId == datasetDetails.datasetId
                                )[0].status = true;
                                this.fetchNMRium();
                            })
                            .catch((err) => {
                                this.loadingStep = false;
                                this.datasetsToImport.filter(
                                    (f) =>
                                        f.datasetId == datasetDetails.datasetId
                                )[0].status = true;
                                this.fetchNMRium();
                            });
                    })
                    .catch((error) => {
                        this.loadingStep = false;
                        this.datasetsToImport.filter(
                            (f) => f.datasetId == datasetDetails.datasetId
                        )[0].status = true;
                        this.fetchNMRium();
                    });
            } else {
                if (this.datasetsToImport.length > 0) {
                    this.datasetsToImport = null;
                    this.fetchProjectData();
                }
            }
        },

        fetchDrafts() {
            this.loading = true;
            return axios.get("/dashboard/drafts");
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

        UpdateAndClose() {
            this.updateProject();
            this.toggleOpenCreateDatasetDialog();
        },
    },
};
</script>
