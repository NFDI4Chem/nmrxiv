<template>
    <Head :title="dataset.data.name + ' - ' + study.data.name" />
    <project-layout :project="project" :selected-tab="tab">
        <template #project-content>
            <div
                class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
            >
                <h1 class="mt-2 text-2xl font-bold break-words text-gray-900">
                    <span>
                        {{ study.data.name }} >
                        <span class="text-blue-500"
                            >{{ dataset.data.name }}
                            <span v-if="dataset.data.type"
                                >- ({{
                                    dataset.data.type.replace(/,\s*$/, "")
                                }})</span
                            ></span
                        >
                    </span>
                    <div class="text-sm">
                        <span class="text-gray-400 pt-2">
                            <DOIBadge
                                :doi="dataset.data.doi"
                                color="bg-green-100"
                            ></DOIBadge>
                        </span>
                    </div>
                    <div class="float-right">
                        <span class="flex-0.5 self-center">
                            <Menu
                                v-if="dataset && study.data.is_public"
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
                                                            :value="shareURL"
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
                </h1>
                <!-- <div class="mt-4">
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
                                Description
                            </span>
                        </div>
                    </div>
                    <p
                        style="max-width: 100ch !important"
                        class="prose mt-1 text-sm text-blue-gray-500"
                        v-html="md(study.data.description)"
                    ></p>
                </div> -->
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
                    <h1
                        class="mt-2 text-2xl font-bold break-words text-gray-900"
                    ></h1>
                    <div class="my-7">
                        <SpectraViewer
                            :dataset="dataset.data"
                            :project="project.data"
                            :study="study.data"
                        ></SpectraViewer>
                    </div>
                </div>
                <div v-if="study.data.tags.length > 0" class="mt-4">
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
            </div>
        </template>
    </project-layout>
    <component :is="'script'" type="application/ld+json">{{
        schema
    }}</component>
</template>

<script>
import ProjectLayout from "@/Pages/Public/Project/Layout.vue";
import { ShareIcon, ClipboardDocumentIcon } from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import SpectraViewer from "@/Shared/SpectraViewer.vue";
import DOIBadge from "@/Shared/DOIBadge.vue";
import Depictor2D from "@/Shared/Depictor2D.vue";
import { Head } from "@inertiajs/vue3";

export default {
    components: {
        ProjectLayout,
        ShareIcon,
        ClipboardDocumentIcon,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        SpectraViewer,
        DOIBadge,
        Depictor2D,
        Head,
    },
    props: ["project", "tab", "study", "dataset"],
    data() {
        return {
            selectedSpectraData: null,
            schema: {},
        };
    },
    computed: {
        shareURL() {
            return this.dataset.data.public_url;
        },
        url() {
            return String(this.$page.props.url);
        },
    },
    mounted() {
        axios
            .get(route("bioschemas.id", this.dataset.data.identifier))
            .then((response) => {
                this.schema = response.data;
            });
    },
};
</script>
