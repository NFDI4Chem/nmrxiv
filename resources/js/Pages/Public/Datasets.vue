<template>
    <app-layout title="Spectra">
        <template #header>
            <div class="bg-white mb-0">
                <div class="px-8">
                    <div class="flex flex-nowrap justify-between py-6">
                        <div>
                            <div
                                class="text-4xl mb-3 font-bold tracking-tight text-gray-900"
                            >
                                Spectra
                            </div>
                            <p>
                                Explore, analyze, and share raw spectra and
                                assignements. Learn more about
                                <a
                                    class="text-teal-900"
                                    href="https://docs.nmrxiv.org/docs/introduction/intro"
                                    target="_blank"
                                    >spectra</a
                                >.
                            </p>
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
                            <dataset-search
                                v-model="form.search"
                                class="mr-4 w-full"
                                @reset="reset"
                            >
                            </dataset-search>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="min-h-[calc(100vh-500px)] px-12 mb-24 mx-auto">
            <div class="relative border-gray-200 pt-4">
                <div class="mx-auto flex items-center justify-between">
                    <Menu as="div" class="relative inline-block text-left">
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
                                class="z-10 origin-top-left absolute left-0 mt-2 w-40 rounded-md shadow-2xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                            >
                                <div class="py-1">
                                    <MenuItem
                                        v-for="option in sortOptions"
                                        :key="option.name"
                                        v-slot="{ active }"
                                    >
                                        <a
                                            @click="form.sort = option.value"
                                            :class="[
                                                form.sort == option.value
                                                    ? 'font-medium text-gray-900'
                                                    : 'text-gray-500',
                                                active ? 'bg-gray-100' : '',
                                                'block px-4 py-2 text-sm cursor-pointer',
                                            ]"
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
                                @click="form.mode = 'list'"
                                type="button"
                                :class="[
                                    filters.mode == 'list'
                                        ? 'bg-gray-300 text-gray-900'
                                        : '',
                                    'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50',
                                ]"
                            >
                                <span class="sr-only">List</span>
                                <QueueListIcon
                                    class="h-5 w-5"
                                    aria-hidden="true"
                                />
                            </button>
                            <button
                                @click="form.mode = 'grid'"
                                type="button"
                                :class="[
                                    filters.mode == 'grid'
                                        ? 'bg-gray-300 text-gray-900'
                                        : '',
                                    '-ml-px relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50',
                                ]"
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

            <div v-if="datasets.meta.total > 0">
                <div class="border-t mt-3 border-gray-100">
                    <h2 class="text-gray-600 text-md mt-4 font-bold">
                        Results ({{ datasets.meta.total }})
                    </h2>
                </div>
                <div v-if="filters.mode == 'grid'">
                    <div
                        class="mt-4 mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-4 lg:max-w-7xl"
                    >
                        <span
                            v-for="dataset in datasets.data"
                            :key="dataset.id"
                        >
                            <dataset-card
                                :mode="filters.mode"
                                :dataset="dataset"
                            ></dataset-card>
                        </span>
                    </div>
                </div>
                <div v-if="filters.mode == 'list'">
                    <div
                        class="mt-4 bg-white shadow overflow-hidden sm:rounded-md"
                    >
                        <ul
                            role="list"
                            class="divide-y border rounded-md divide-gray-200"
                        >
                            <span
                                v-for="dataset in datasets.data"
                                :key="dataset.id"
                            >
                                <dataset-card
                                    :mode="filters.mode ? filters.mode : 'grid'"
                                    :dataset="dataset"
                                ></dataset-card>
                            </span>
                        </ul>
                    </div>
                </div>
                <div
                    v-if="datasets.meta.total > datasets.meta.per_page"
                    class="py-10"
                >
                    <Pagination :links="datasets.meta.links"></Pagination>
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
                        No datasets
                    </h3>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import DatasetSearch from "@/Shared/DatasetSearch.vue";
import DatasetCard from "@/Shared/DatasetCard.vue";
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
        DatasetSearch,
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
        DatasetCard,
    },
    props: {
        datasets: {
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
    data() {
        return {
            sortOptions: [
                { name: "Creation", value: "creation" },
                { name: "Newest", value: "newest" },
            ],
            open: ref(false),
            form: {
                search: this.filters.search,
                sort: "newest",
                mode: "grid",
            },
        };
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
