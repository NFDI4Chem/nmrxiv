<template>
    <Head :title="study.data.name" />
    <sample-layout :study="study.data">
        <template #sample-content>
            <div
                class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
            >
                <div class="border-b border-gray-200 pb-5">
                    <div class="sm:flex sm:items-baseline sm:justify-between">
                        <div class="sm:w-0 sm:flex-1">
                            <h1
                                class="mt-2 text-3xl font-extrabold tracking-tight font-bold break-words text-blue-500 text-base"
                            >
                                {{ study.data.name }}
                            </h1>
                            <p class="mt-1 truncate text-sm text-gray-500">
                                <DOIBadge
                                    :doi="study.data.doi"
                                    color="bg-yellow-300"
                                ></DOIBadge>
                            </p>
                        </div>

                        <div
                            class="mt-4 flex items-center justify-between sm:ml-6 sm:mt-0 sm:flex-shrink-0 sm:justify-start"
                        >
                            <div
                                class="inline hover:text-blue-600 mr-6 hover:cursor-pointer text-gray-200"
                            >
                                #{{ study.data.identifier }}
                            </div>

                            <span class="flex-0.5 self-center">
                                <Menu
                                    v-if="study.data.is_public"
                                    as="div"
                                    class="relative text-left"
                                >
                                    <div>
                                        <MenuButton
                                            class="bg-white text-sm rounded-full flex items-center text-gray-400 hover:text-gray-600"
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
                            </span>
                        </div>
                    </div>
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
                                Sample
                            </span>
                        </div>
                    </div>
                    <div
                        v-if="study.data.sample.molecules.length > 0"
                        class="mt-3"
                    >
                        <label>Molecular Composition</label>
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
                                        class="rounded-full border p-2 z-10 bg-gray-100 text-sm mt-14 text-center"
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
                            <span
                                class="pr-3 text-md bg-white font-medium text-gray-400"
                            >
                                Spectra
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <SpectraViewer
                            ref="spectraViewerREF"
                            :study="study.data"
                        ></SpectraViewer>
                    </div>

                    <div class="my-2">
                        <div>
                            <h2 class="text-sm font-medium text-gray-500">
                                Spectra Datasets
                            </h2>
                            <ul
                                role="list"
                                class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3"
                            >
                                <li
                                    v-for="dataset in study.data.datasets.sort(
                                        (a, b) => (a.name > b.name ? 1 : -1)
                                    )"
                                    :key="dataset.slug"
                                    class="col-span-1 flex rounded-md shadow-sm"
                                >
                                    <!-- <a
                                        class="cursor-pointer"
                                        :href="
                                            '/' +
                                            dataset.identifier.replace(
                                                'NMRXIV:',
                                                ''
                                            )
                                        "
                                        target="_blank"
                                    > -->
                                    <!-- <div
                                        class="flex w-16 flex-shrink-0 items-center justify-center bg-pink-600 rounded-l-md text-sm font-medium text-white"
                                    >
                                    
                                    </div> -->
                                    <div
                                        class="flex flex-1 border-l rounded-md items-center justify-between truncate rounded-r-md border-b border-r border-t border-gray-200 bg-white"
                                    >
                                        <div
                                            class="flex-1 truncate px-4 py-2 text-sm"
                                        >
                                            <a
                                                class="font-medium text-lg text-gray-900 hover:text-gray-600"
                                            >
                                                {{ dataset.name }}
                                                <span v-if="dataset.type"
                                                    >({{
                                                        dataset.type.replace(
                                                            /,\s*$/,
                                                            ""
                                                        )
                                                    }})</span
                                                ></a
                                            >
                                            <p class="text-gray-500">
                                                <DOIBadge
                                                    color="bg-green-100"
                                                    :doi="dataset.doi"
                                                ></DOIBadge>
                                            </p>
                                        </div>
                                        <!-- <div class="flex-shrink-0 pr-2">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                    class="h-5 w-5 text-gray-600 ml-4"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"
                                                    ></path>
                                                </svg>
                                            </div> -->
                                    </div>
                                    <!-- </a> -->
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div>&emsp;</div>
                    <div v-if="study.data.license" class="mt-4">
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
                                    License
                                </span>
                            </div>
                        </div>
                        <p
                            style="max-width: 100ch !important"
                            class="prose mt-1 text-sm text-blue-gray-500"
                            v-html="study.data.license.title"
                        ></p>
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
