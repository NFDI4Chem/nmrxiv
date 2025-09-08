<template>
    <Head :title="study.data.name" />
    <sample-layout :study="study.data">
        <template #sample-content>
            <div
                class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
            >
                <ShowProjectDates
                    :release_date="study.data.release_date"
                    :created_at="study.data.created_at"
                />
                <div class="mt-2">
                    <!-- Study title -->
                    <h1 class="text-2xl font-bold break-words text-gray-900">
                        <div class="text-blue-500 break-all">
                            {{ study.data.name }}
                        </div>
                    </h1>

                    <!-- Header controls section -->
                    <div class="mt-3">
                        <!-- DOI Badge (left aligned) -->
                        <div class="float-left">
                            <DOIBadge
                                :doi="study.data.doi"
                                color="bg-yellow-300"
                            ></DOIBadge>
                        </div>

                        <!-- Desktop layout controls (right aligned) -->
                        <div class="hidden sm:block float-right">
                            <!-- Share button (desktop) -->
                            <div class="float-right">
                                <!-- Share dropdown menu -->
                                <Menu
                                    v-if="study.data.is_public"
                                    as="div"
                                    class="relative text-left"
                                >
                                    <!-- Share button trigger -->
                                    <div>
                                        <MenuButton
                                            class="bg-white text-sm rounded-full flex items-center text-gray-400 hover:text-gray-600 border border-gray-200 px-3 py-1"
                                        >
                                            <ShareIcon
                                                class="h-4 w-4 text-gray-800 flex-shrink-0 mr-2"
                                                aria-hidden="true"
                                            ></ShareIcon
                                            >Share
                                        </MenuButton>
                                    </div>
                                    <!-- Share dropdown transition -->
                                    <transition
                                        enter-active-class="transition ease-out duration-100"
                                        enter-from-class="transform opacity-0 scale-95"
                                        enter-to-class="transform opacity-100 scale-100"
                                        leave-active-class="transition ease-in duration-75"
                                        leave-from-class="transform opacity-100 scale-100"
                                        leave-to-class="transform opacity-0 scale-95"
                                    >
                                        <!-- Share dropdown menu items -->
                                        <MenuItems
                                            class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                        >
                                            <div class="py-1">
                                                <!-- Share URL input and copy button -->
                                                <MenuItem v-slot="{ active }">
                                                    <div
                                                        :class="[
                                                            active
                                                                ? 'bg-gray-100 text-gray-900'
                                                                : 'text-gray-700',
                                                            'block px-4 py-2 text-sm flex',
                                                        ]"
                                                    >
                                                        <!-- URL input field -->
                                                        <div class="flex-grow">
                                                            <input
                                                                id="datasetPublicURLCopyDesktop"
                                                                readonly
                                                                type="text"
                                                                :value="
                                                                    shareURL
                                                                "
                                                                class="rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                                                                @focus="
                                                                    $event.target.select()
                                                                "
                                                            />
                                                        </div>

                                                        <!-- Copy to clipboard button -->
                                                        <button
                                                            type="button"
                                                            class="-ml-px relative inline-flex items-center space-x-2 px-2 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"
                                                            @click="
                                                                copyToClipboard(
                                                                    shareURL,
                                                                    'datasetPublicURLCopyDesktop'
                                                                )
                                                            "
                                                        >
                                                            <span
                                                                ><ClipboardDocumentIcon
                                                                    class="h-5 w-5"
                                                                    aria-hidden="true"
                                                            /></span>
                                                        </button>
                                                    </div>
                                                </MenuItem>
                                            </div>
                                        </MenuItems>
                                    </transition>
                                </Menu>
                            </div>

                            <!-- Study identifier (desktop) -->
                            <div class="text-sm float-right">
                                <div
                                    class="hover:text-blue-600 hover:cursor-pointer text-gray-500 mx-2 my-1"
                                >
                                    <p class="inline m-0 p-0">
                                        #{{ study.data.identifier }}
                                    </p>
                                </div>
                            </div>

                            <!-- Clear floats -->
                            <div class="clear-both"></div>
                        </div>
                    </div>
                    <div class="clear-both"></div>

                    <!-- Mobile layout section -->
                    <div class="mt-4">
                        <!-- Mobile controls (stacked vertically) -->
                        <div class="flex flex-col gap-3 sm:hidden">
                            <!-- Share button (mobile) -->
                            <div>
                                <!-- Share dropdown menu (mobile) -->
                                <Menu
                                    v-if="study.data.is_public"
                                    as="div"
                                    class="relative text-left"
                                >
                                    <!-- Share button trigger (mobile) -->
                                    <div>
                                        <MenuButton
                                            class="bg-white text-sm rounded-full flex items-center text-gray-400 hover:text-gray-600 border border-gray-200 px-3 py-1"
                                        >
                                            <ShareIcon
                                                class="h-4 w-4 text-gray-800 flex-shrink-0 mr-2"
                                                aria-hidden="true"
                                            ></ShareIcon
                                            >Share
                                        </MenuButton>
                                    </div>
                                    <transition
                                        enter-active-class="transition ease-out duration-100"
                                        enter-from-class="transform opacity-0 scale-95"
                                        enter-to-class="transform opacity-100 scale-100"
                                        leave-active-class="transition ease-in duration-75"
                                        leave-from-class="transform opacity-100 scale-100"
                                        leave-to-class="transform opacity-0 scale-95"
                                    >
                                        <MenuItems
                                            class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                        >
                                            <div class="py-1">
                                                <MenuItem v-slot="{ active }">
                                                    <div
                                                        :class="[
                                                            active
                                                                ? 'bg-gray-100 text-gray-900'
                                                                : 'text-gray-700',
                                                            'block px-4 py-2 text-sm flex',
                                                        ]"
                                                    >
                                                        <div class="flex-grow">
                                                            <input
                                                                id="datasetPublicURLCopy"
                                                                readonly
                                                                type="text"
                                                                :value="
                                                                    shareURL
                                                                "
                                                                class="rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                                                                @focus="
                                                                    $event.target.select()
                                                                "
                                                            />
                                                        </div>
                                                        <button
                                                            type="button"
                                                            class="-ml-px relative inline-flex items-center space-x-2 px-2 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"
                                                            @click="
                                                                copyToClipboard(
                                                                    shareURL,
                                                                    'datasetPublicURLCopy'
                                                                )
                                                            "
                                                        >
                                                            <span
                                                                ><ClipboardDocumentIcon
                                                                    class="h-5 w-5"
                                                                    aria-hidden="true"
                                                            /></span>
                                                        </button>
                                                    </div>
                                                </MenuItem>
                                            </div>
                                        </MenuItems>
                                    </transition>
                                </Menu>
                            </div>
                            <!-- Study identifier (mobile) -->
                            <div class="text-sm">
                                <div
                                    class="inline hover:text-blue-600 hover:cursor-pointer text-gray-500"
                                >
                                    #{{ study.data.identifier }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="-mx-4"
                    v-if="study.data.is_public && study.data.doi != null"
                >
                    <Citation
                        :model="'sample'"
                        :doi="study.data.doi"
                    ></Citation>
                </div>

                <div v-if="study.data.tags.length > 0" class="relative mt-4">
                    <div class="relative">
                        <div
                            class="absolute inset-0 flex items-center"
                            aria-hidden="true"
                        >
                            <div class="w-full border-t border-gray-100"></div>
                        </div>
                        <div class="relative flex items-center justify-between">
                            <span
                                class="pr-3 text-md bg-white font-medium text-gray-400"
                            >
                                Keywords
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <dd class="mt-1 text-md text-gray-900 space-y-5">
                            <p>
                                <span
                                    v-for="tag in study.data.tags"
                                    :key="tag.id"
                                    class="mr-2"
                                >
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800"
                                    >
                                        <svg
                                            class="-ml-0.5 mr-1.5 h-2 w-2 text-indigo-400"
                                            fill="currentColor"
                                            viewBox="0 0 8 8"
                                        >
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        {{ tag.name["en"] }}
                                    </span>
                                </span>
                            </p>
                        </dd>
                    </div>
                </div>
                <div
                    v-if="
                        study.data.sample.molecules.length > 0 ||
                        study.data.sample.description == ''
                    "
                    class="mt-4"
                >
                    <div class="gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                        <div class="pt-2 sm:col-span-6">
                            <h2
                                class="text-xl font-extrabold mb-3 text-blue-gray-900"
                            >
                                Submitter
                            </h2>
                        </div>
                    </div>
                    <div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div
                            class="relative rounded-lg border border-gray-300 bg-white p-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"
                        >
                            <div class="flex-shrink-0">
                                <img
                                    class="h-10 w-10 rounded-full"
                                    :src="study.data.owner.profile_photo_url"
                                    alt=""
                                />
                            </div>
                            <div class="flex-1 min-w-0">
                                <a class="focus:outline-none">
                                    <span
                                        class="absolute inset-0"
                                        aria-hidden="true"
                                    ></span>
                                    <p
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{
                                            study.data.owner.first_name +
                                            " " +
                                            study.data.owner.last_name
                                        }}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate">
                                        @ {{ study.data.owner.username }}
                                    </p>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submitted Through Information -->
                    <div v-if="study.data.submitted_through" class="mt-4">
                        <div class="flex items-center space-x-3 text-sm text-gray-600">
                            <svg
                                class="h-4 w-4 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"
                                />
                            </svg>
                            <span>Submitted via:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-gray-800 uppercase">
                                <img :src="`/img/eln/${study.data.submitted_through}.png`" class="h-12" />
                            </span>
                            <a :href="study.data.external_url" target="_blank" class="inline-flex items-center py-1 rounded text-md font-bold text-gray-800 uppercase hover:text-blue-600">
                                <span class="inline-flex items-center py-1 rounded text-md font-bold uppercase">
                                    {{ study.data.external_id }} 
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                    </svg>
                                </span>
                            </a>
                        </div>
                    </div>
                    
                    <div class="relative">
                        &nbsp;
                    </div>
                    <div
                        v-if="study.data.sample.molecules.length > 0"
                        class="mt-3"
                    >
                        <div class="relative flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">
                            Molecular Composition
                            </h2>
                        </div>
                        <div class="grid md:grid-cols-2 gap-2 mt-2">
                            <div class="pr-2">
                                <div
                                    v-if="
                                        study.data.sample.molecules.length > 0
                                    "
                                    class="flow-root"
                                >
                                    <ul role="list" class="-mb-8">
                                        <li
                                            v-for="molecule in study.data.sample
                                                .molecules"
                                            :key="molecule.standard_inchi"
                                        >
                                            <div class="relative pb-8">
                                                <span
                                                    class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-100"
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
                                                                molecule.pivot
                                                                    .percentage_composition
                                                            }}%
                                                        </div>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div>
                                                            <div
                                                                class="text-sm"
                                                            >
                                                                <a
                                                                    class="font-medium text-gray-900"
                                                                    >{{
                                                                        molecule.standard_inchi
                                                                    }}</a
                                                                >
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="mt-2 text-sm text-gray-700"
                                                        >
                                                            <div
                                                                class="rounded-md border my-3 flex justify-center items-center"
                                                            >
                                                                <Depictor2D
                                                                    class="py-4 -px-4"
                                                                    :molecule="
                                                                        molecule.canonical_smiles
                                                                    "
                                                                ></Depictor2D>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                    <div
                                        class="rounded-full border p-2 z-100 bg-gray-100 text-sm mt-4 text-center"
                                    >
                                        Sample chemical composition
                                    </div>
                                </div>
                                <div v-else>
                                    <div class="text-center my-10 py-10">
                                        <h3
                                            class="mt-2 text-sm font-medium text-gray-900"
                                        >
                                            No structures associated with the
                                            sample yet!
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Get started by adding a new
                                            molecule.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="relative">
                        <div
                            class="absolute inset-0 flex items-center"
                            aria-hidden="true"
                        >
                            <div class="w-full border-t border-gray-100"></div>
                        </div>
                        <div class="relative flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">
                                Spectra
                            </h2>
                        </div>
                    </div>
                    <div class="mt-3">
                        <SpectraViewer
                            ref="spectraViewerREF"
                            :study="study.data"
                        ></SpectraViewer>
                    </div>

                    <div class="my-6">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold text-gray-900">
                                    Datasets
                                </h2>
                                <span
                                    class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full"
                                >
                                    {{ study.data.datasets.length }}
                                    {{
                                        study.data.datasets.length === 1
                                            ? "Dataset"
                                            : "Datasets"
                                    }}
                                </span>
                            </div>

                            <div
                                v-if="study.data.datasets.length === 0"
                                class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300"
                            >
                                <svg
                                    class="mx-auto h-12 w-12 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    />
                                </svg>
                                <h3
                                    class="mt-2 text-sm font-medium text-gray-900"
                                >
                                    No datasets available
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    There are no spectra datasets associated
                                    with this study yet.
                                </p>
                            </div>

                            <div
                                v-else
                                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                            >
                                <div
                                    v-for="dataset in study.data.datasets.sort(
                                        (a, b) => (a.name > b.name ? 1 : -1)
                                    )"
                                    :key="dataset.slug"
                                    class="group relative bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 hover:border-gray-300"
                                >
                                    <a
                                        :href="dataset.external_url"
                                        target="_blank"
                                        class="block p-4 h-full"
                                    >
                                        <div>
                                            <div
                                                class="flex items-start justify-between mb-1"
                                            >
                                                <h3
                                                    class="text-md font-bold text-gray-600 group-hover:text-blue-600 transition-colors duration-200 line-clamp-2 flex-1 pr-2 capitalize"
                                                >
                                                    {{ dataset.name }} 
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-2 inline-block">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                                    </svg>
                                                </h3>
                                            </div>

                                            <div
                                                class="flex flex-col gap-2 flex-1"
                                            >
                                                <div v-if="dataset.type">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                                    >
                                                        {{
                                                            dataset.type.replace(
                                                                /,\s*$/,
                                                                ""
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- License Information Section -->
                    <div v-if="study.data.license" class="mt-6">
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <div class="flex items-start space-x-4">
                                <!-- License Icon -->
                                <div class="flex-shrink-0">
                                    <svg
                                        class="h-6 w-6 text-gray-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                        />
                                    </svg>
                                </div>
                                
                                <!-- License Content -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        License Information
                                    </h3>
                                    
                                    <!-- Desktop Layout -->
                                    <div class="hidden sm:block">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-medium text-gray-600">License:</span>
                                            <a
                                                v-if="study.data.license.url"
                                                :href="study.data.license.url"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-150"
                                                :title="'View ' + study.data.license.title + ' license details'"
                                            >
                                                {{ study.data.license.title }}
                                            </a>
                                            <span
                                                v-else
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ study.data.license.title }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Mobile Layout -->
                                    <div class="sm:hidden space-y-2">
                                        <div class="text-sm text-gray-600">
                                            <span class="font-medium">License</span>
                                        </div>
                                        <div>
                                            <a
                                                v-if="study.data.license.url"
                                                :href="study.data.license.url"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="text-sm font-medium text-blue-600 hover:text-blue-800 underline"
                                                :title="'View license details'"
                                            >
                                                {{ study.data.license.title }}
                                            </a>
                                            <span
                                                v-else
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ study.data.license.title }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- License Description (if available) -->
                                    <div v-if="study.data.license.description" class="mt-3">
                                        <p v-html="study.data.license.description" class="text-sm text-gray-600 leading-relaxed">
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        v-if="
                            study.data.description &&
                            study.data.description.length > 0
                        "
                        class="overflow-hidden w-full"
                    >
                        <div class="relative">
                            <div
                                class="absolute inset-0 flex items-center"
                                aria-hidden="true"
                            >
                                <div
                                    class="w-full border-t border-gray-100"
                                ></div>
                            </div>
                            <div
                                class="relative flex items-center justify-between"
                            >
                                <span
                                    class="pr-3 text-md bg-white font-medium text-gray-400"
                                >
                                    Description
                                </span>
                            </div>
                        </div>
                        <div>
                            <p
                                class="overflow-scroll mt-1 px-0 relative text-sm text-blue-gray-500 h-64 pb-10"
                                v-html="study.data.description"
                            ></p>
                            <div class="relative" aria-hidden="true">
                                <div
                                    class="absolute -inset-x-20 bottom-0 bg-gradient-to-t from-white pt-[7%]"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </sample-layout>
    <component :is="'script'" type="application/ld+json">{{
        schema
    }}</component>
</template>

<script>
import SampleLayout from "@/Pages/Public/Sample/Layout.vue";
import { ShareIcon, ClipboardDocumentIcon } from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import SpectraViewer from "@/Shared/SpectraViewer.vue";
import Depictor2D from "@/Shared/Depictor2D.vue";
import DOIBadge from "@/Shared/DOIBadge.vue";
import { Head } from "@inertiajs/vue3";
import Citation from "@/Shared/Citation.vue";
import ShowProjectDates from "@/Shared/ShowProjectDates.vue";

export default {
    components: {
        SampleLayout,
        ShareIcon,
        ClipboardDocumentIcon,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        SpectraViewer,
        Depictor2D,
        DOIBadge,
        Head,
        Citation,
        ShowProjectDates,
    },
    props: ["project", "tab", "study"],
    data() {
        return {
            schema: {},
        };
    },
    computed: {
        shareURL() {
            return this.study.data.public_url;
        },
        url() {
            return String(this.$page.props.url);
        },
    },
    mounted() {
        axios
            .get(route("bioschemas.id", this.study.data.identifier))
            .then((response) => {
                this.schema = response.data;
            });
    },
    methods: {},
};
</script>
