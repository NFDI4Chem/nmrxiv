<template>
    <app-layout title="Spectra">
        <template #header>
            <div class="relative border-b border-zinc-900/5">
                <div
                    class="relative pt-10 dark:border-white/5 mx-8 py-12 sm:py-12"
                >
                    <div
                        v-if="!molecule"
                        class="absolute inset-0 bg-gradient-to-r from-[#36b49f] to-[#DBFF75] opacity-40 [mask-image:radial-gradient(farthest-side_at_top,white,transparent)] dark:from-[#36b49f]/30 dark:to-[#DBFF75]/30 dark:opacity-100"
                    >
                        <svg
                            aria-hidden="true"
                            class="absolute inset-x-0 inset-y-[-50%] h-[200%] w-full skew-y-[-18deg] fill-black/40 stroke-black/50 mix-blend-overlay dark:fill-white/2.5 dark:stroke-white/5"
                        >
                            <defs>
                                <pattern
                                    id=":r99:"
                                    width="72"
                                    height="56"
                                    patternUnits="userSpaceOnUse"
                                    x="-12"
                                    y="4"
                                >
                                    <path d="M.5 56V.5H72" fill="none"></path>
                                </pattern>
                            </defs>
                            <rect
                                width="100%"
                                height="100%"
                                stroke-width="0"
                                fill="url(#:r99:)"
                            ></rect>
                            <svg x="-12" y="4" class="overflow-visible">
                                <rect
                                    stroke-width="0"
                                    width="73"
                                    height="57"
                                    x="288"
                                    y="168"
                                ></rect>
                                <rect
                                    stroke-width="0"
                                    width="73"
                                    height="57"
                                    x="144"
                                    y="56"
                                ></rect>
                                <rect
                                    stroke-width="0"
                                    width="73"
                                    height="57"
                                    x="504"
                                    y="168"
                                ></rect>
                                <rect
                                    stroke-width="0"
                                    width="73"
                                    height="57"
                                    x="720"
                                    y="336"
                                ></rect>
                            </svg>
                        </svg>
                    </div>
                    <div v-if="!molecule">
                        <div
                            class="text-4xl mb-3 font-bold tracking-tight text-gray-900"
                        >
                            Browse Spectra (Samples)
                        </div>
                        <p>
                            Explore, analyse, and share raw spectra and
                            assignments. Learn more about
                            <a
                                class="text-teal-900"
                                href="https://docs.nmrxiv.org/docs/introduction/intro"
                                target="_blank"
                                >spectra</a
                            >.
                        </p>
                    </div>
                    <div v-if="molecule">
                        <div
                            class="mx-auto flex max-w-2xl items-center justify-between gap-x-8 lg:mx-0 lg:max-w-none"
                        >
                            <div class="flex items-center gap-x-6">
                                <div
                                    class="aspect-h-2 aspect-w-3 overflow-hidden rounded-lg shadow-md border"
                                >
                                    <img
                                        :src="
                                            $page.props.CM_API +
                                            'depict/2D?height=360&width=360&smiles=' +
                                            molecule.canonical_smiles
                                        "
                                        class="bg-white object-cover object-center"
                                    />
                                </div>
                                <div
                                    class="mx-auto mt-14 max-w-2xl sm:mt-16 lg:col-span-3 lg:row-span-2 lg:row-end-2 lg:mt-0 lg:max-w-none"
                                >
                                    <div class="flex flex-col-reverse">
                                        <div class="mt-4">
                                            <h1
                                                class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl"
                                            >
                                                {{ molecule.iupac_name }}
                                            </h1>
                                            <p
                                                class="mt-2 text-sm text-gray-500"
                                            >
                                                {{ molecule.canonical_smiles }}
                                            </p>
                                            <div>
                                                <h3
                                                    class="mt-3 text-sm text-gray-500"
                                                >
                                                    <a>
                                                        <span
                                                            class="absolute inset-0"
                                                        ></span>
                                                        Molecular Formula:
                                                        <span
                                                            class="text-base inline font-semibold text-gray-900"
                                                            >{{
                                                                molecule.molecular_formula
                                                            }}</span
                                                        >
                                                    </a>
                                                    &emsp; &middot; &emsp;
                                                    <a>
                                                        <span
                                                            class="absolute inset-0"
                                                        ></span>
                                                        Molecular Weight:
                                                        <span
                                                            class="text-base inline font-semibold text-gray-900"
                                                            >{{
                                                                molecule.molecular_weight
                                                            }}</span
                                                        >
                                                    </a>
                                                </h3>
                                            </div>
                                            <p
                                                v-if="molecule.synonyms"
                                                class="mt-3 text-gray-500"
                                            >
                                                <span
                                                    class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-sm mr-2 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 mt-2"
                                                    v-for="synonym in molecule.synonyms
                                                        .split(',')
                                                        .slice(1, 20)"
                                                >
                                                    {{ synonym }}
                                                </span>
                                            </p>
                                        </div>
                                        <div>
                                            <div
                                                class="flex items-center inline"
                                            >
                                                <p class="mr-3">
                                                    {{ molecule.identifier }}
                                                </p>

                                                <svg
                                                    class="text-yellow-400 h-5 w-5 flex-shrink-0"
                                                    x-state:on="Active"
                                                    x-state:off="Default"
                                                    x-state-description='Active: "text-yellow-400", Default: "text-gray-300"'
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                                        clip-rule="evenodd"
                                                    ></path>
                                                </svg>
                                                <svg
                                                    class="text-yellow-400 h-5 w-5 flex-shrink-0"
                                                    x-state-description='undefined: "text-yellow-400", undefined: "text-gray-300"'
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                                        clip-rule="evenodd"
                                                    ></path>
                                                </svg>
                                                <svg
                                                    class="text-yellow-400 h-5 w-5 flex-shrink-0"
                                                    x-state-description='undefined: "text-yellow-400", undefined: "text-gray-300"'
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                                        clip-rule="evenodd"
                                                    ></path>
                                                </svg>
                                                <svg
                                                    class="text-yellow-400 h-5 w-5 flex-shrink-0"
                                                    x-state-description='undefined: "text-yellow-400", undefined: "text-gray-300"'
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                                        clip-rule="evenodd"
                                                    ></path>
                                                </svg>
                                                <svg
                                                    class="text-gray-300 h-5 w-5 flex-shrink-0"
                                                    x-state-description='undefined: "text-yellow-400", undefined: "text-gray-300"'
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                                        clip-rule="evenodd"
                                                    ></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div
                                        class="mt-10 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2"
                                    >
                                        <button
                                            type="button"
                                            class="flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-8 py-3 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-50"
                                        >
                                            Pay $220
                                        </button>
                                        <button
                                            type="button"
                                            class="flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-50 px-8 py-3 text-base font-medium text-indigo-700 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-50"
                                        >
                                            Preview
                                        </button>
                                    </div> -->
                                </div>
                            </div>

                            <!-- <div class="flex items-center gap-x-4 sm:gap-x-6">
            <a
                :href="
                    '/upload?compound=' + molecule.identifier
                "
                class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >
                Upload Spectra
            </a>
        </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div>
            <div class="bg-white">
                <section aria-labelledby="filter-heading">
                    <div class="bg-gray-100 border-t border-b">
                        <div
                            class="mx-auto py-3 px-4 sm:flex sm:items-center sm:px-6 lg:px-8"
                        >
                            <study-search
                                v-model="form.search"
                                class="mr-4 w-full"
                                @reset="reset"
                            >
                            </study-search>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="min-h-[calc(100vh-500px)] px-12 mb-24 mx-auto">
            <div class="relative border-gray-200 pt-4">
                <div class="mx-auto flex items-center justify-between">
                    <Menu as="div" class="relative inline-block text-left z-10">
                        <div>
                            <MenuButton
                                class="group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900"
                            >
                                Sort by:&nbsp;<span
                                    class="capitalize font-black text-gray-900"
                                    >{{ form.sort }}</span
                                >
                                <ChevronDownIcon
                                    class="flex-shrink-0 -mr-1 ml-1 h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                    aria-hidden="true"
                                />
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
                                class="origin-top-left absolute left-0 mt-2 w-40 rounded-md shadow-2xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                            >
                                <div class="py-1">
                                    <MenuItem
                                        v-for="option in sortOptions"
                                        :key="option.name"
                                        v-slot="{ active }"
                                    >
                                        <a
                                            :class="[
                                                option.current
                                                    ? 'font-medium text-gray-900'
                                                    : 'text-gray-500',
                                                active ? 'bg-gray-100' : '',
                                                'block px-4 py-2 text-sm cursor-pointer',
                                            ]"
                                            @click="form.sort = option.value"
                                        >
                                            {{ option.name }}
                                        </a>
                                    </MenuItem>
                                </div>
                            </MenuItems>
                        </transition>
                    </Menu>
                    <div class="float-right">
                        <span
                            class="relative z-0 inline-flex shadow-sm rounded-md"
                        >
                            <button
                                type="button"
                                :class="[
                                    filters.mode == 'list'
                                        ? 'bg-gray-300 text-gray-900'
                                        : '',
                                    'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50',
                                ]"
                                @click="form.mode = 'list'"
                            >
                                <span class="sr-only">List</span>
                                <QueueListIcon
                                    class="h-5 w-5"
                                    aria-hidden="true"
                                />
                            </button>
                            <button
                                type="button"
                                :class="[
                                    filters.mode == 'grid'
                                        ? 'bg-gray-300 text-gray-900'
                                        : '',
                                    '-ml-px relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50',
                                ]"
                                @click="form.mode = 'grid'"
                            >
                                <span class="sr-only">Grid</span>
                                <Squares2X2Icon
                                    class="h-5 w-5"
                                    aria-hidden="true"
                                />
                            </button>
                        </span>
                    </div>
                </div>
            </div>

            <div v-if="studies.meta && studies.meta.total > 0">
                <div class="border-t mt-3 border-gray-100">
                    <h2 class="text-gray-600 text-md mt-4 font-bold">
                        Results ({{ studies.meta.total }})
                    </h2>
                </div>
                <div v-if="filters.mode == 'grid'">
                    <div
                        class="mt-4 mx-auto grid gap-8 lg:grid-cols-4 2xl:grid-cols-6"
                    >
                        <span v-for="study in studies.data" :key="study.id">
                            <StudyPublicCard
                                :mode="filters.mode"
                                :study="study"
                            ></StudyPublicCard>
                        </span>
                    </div>
                </div>
                <div v-if="filters.mode == 'list'">
                    <div class="mt-4 bg-white overflow-hidden sm:rounded-md">
                        <ul role="list" class="rounded-md">
                            <div
                                v-for="study in studies.data"
                                :key="study.id"
                                class="mb-3"
                            >
                                <StudyPublicCard
                                    :mode="filters.mode ? filters.mode : 'grid'"
                                    :study="study"
                                ></StudyPublicCard>
                            </div>
                        </ul>
                    </div>
                </div>
                <div
                    v-if="studies.meta.total > studies.meta.per_page"
                    class="py-10"
                >
                    <Pagination :links="studies.meta.links"></Pagination>
                </div>
            </div>
            <div v-else>
                <div
                    class="text-center rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-5"
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
                    <h3 class="mt-2 text-sm font-medium text-gray-900">
                        No studies
                    </h3>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import StudySearch from "@/Shared/StudySearch.vue";
import StudyPublicCard from "@/Shared/StudyCardPublic.vue";
import { ref } from "vue";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import Pagination from "@/Shared/Pagination.vue";
import {
    Dialog,
    DialogPanel,
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    Popover,
    PopoverButton,
    PopoverGroup,
    PopoverPanel,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";
import {
    ChevronDownIcon,
    QueueListIcon,
    Squares2X2Icon,
} from "@heroicons/vue/24/solid";

export default {
    components: {
        AppLayout,
        Link,
        StudySearch,
        Pagination,
        Dialog,
        DialogPanel,
        Disclosure,
        DisclosureButton,
        DisclosurePanel,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        Popover,
        PopoverButton,
        PopoverGroup,
        PopoverPanel,
        TransitionChild,
        TransitionRoot,
        XMarkIcon,
        ChevronDownIcon,
        QueueListIcon,
        Squares2X2Icon,
        StudyPublicCard,
    },
    props: {
        studies: {
            default: [],
            type: Object,
        },
        filters: {
            default: {
                search: "",
                sort: "newest",
                mode: "grid",
            },
            type: Object,
        },
        molecule: {
            default: [],
            type: Object,
        },
    },
    data() {
        return {
            sortOptions: [
                { name: "Creation", value: "creation", current: true },
                { name: "Newest", value: "newest", current: false },
            ],
            open: ref(false),
            form: {
                search: this.filters.search,
                sort: "newest",
                mode: "grid",
            },
        };
    },
    mounted() {
        if (this.filters) {
            if (this.filters.search == null) {
                this.filters.search = "";
            }
            if (this.filters.mode == null) {
                this.filters.mode = "grid";
            }
            if (this.filters.sort == null) {
                this.filters.sort = "newest";
            }
        }
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get("/spectra", pickBy(this.form), {
                    preserveState: true,
                });
            }, 150),
        },
    },
    methods: {
        reset() {
            this.form = {
                search: "",
                sort: "newest",
                mode: "grid",
            };
        },
    },
};
</script>
