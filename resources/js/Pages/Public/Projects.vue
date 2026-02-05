<template>
    <app-layout title="Projects">
        <template #header>
            <div class="relative border-b border-zinc-900/5 overflow-hidden">
                <!-- Animated mesh gradient background -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 via-indigo-50/30 to-purple-50/30"></div>
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
                    <div class="absolute top-0 right-1/4 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
                    <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"></div>
                </div>
                
                <div
                    class="relative pt-10 dark:border-white/5 mx-8 py-12 sm:py-12"
                >
                    <div
                        class="text-4xl mb-3 font-bold tracking-tight text-gray-900"
                    >
                        Browse Projects
                    </div>
                    <p>
                        Explore, analyse, and share raw spectral data and
                        assignments. Learn more about
                        <a
                            class="text-teal-900"
                            href="https://docs.nmrxiv.org/introduction/intro"
                            target="_blank"
                            >projects</a
                        >.
                    </p>
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
                            <project-search
                                v-model="form.search"
                                class="mr-4 w-full"
                                @reset="reset"
                            >
                            </project-search>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <div class="min-h-[calc(100vh-500px)] px-6 px-md-12 mb-24 mx-auto">
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
                                class="origin-top-left absolute left-0 mt-2 w-40 rounded-md shadow-2xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
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
                                    'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10',
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
            <div v-if="projects.meta.total > 0">
                <div class="border-t mt-3 border-gray-100">
                    <h2 class="text-gray-600 text-md mt-4 font-bold">
                        Results ({{ projects.meta.total }})
                    </h2>
                </div>
                <div v-if="filters.mode == 'grid'">
                    <div
                        class="mt-4 mx-auto grid gap-8 lg:grid-cols-4 2xl:grid-cols-6 w-full"
                    >
                        <span
                            v-for="project in projects.data.filter(
                                (p) => p.owner
                            )"
                            :key="project.id"
                        >
                            <project-card
                                :mode="filters.mode"
                                :project="project"
                            ></project-card>
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
                                v-for="project in projects.data.filter(
                                    (p) => p.owner
                                )"
                                :key="project.id"
                            >
                                <project-card
                                    :mode="filters.mode ? filters.mode : 'grid'"
                                    :project="project"
                                ></project-card>
                            </span>
                        </ul>
                    </div>
                </div>
                <div
                    v-if="projects.meta.total > projects.meta.per_page"
                    class="py-10"
                >
                    <Pagination :links="projects.meta.links"></Pagination>
                </div>
            </div>
            <div v-else>
                <div
                    class="text-center rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-5"
                >
                    <svg
                        class="mx-auto h-16 w-16 text-gray-300"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"
                        />
                    </svg>
                    <h3 class="mt-4 text-base font-semibold text-gray-900">
                        No projects available
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                        Public projects will appear here once they are published.
                    </p>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import { ref } from "vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import throttle from "lodash/throttle";
import {
    ChevronDownIcon,
    QueueListIcon,
    Squares2X2Icon,
} from "@heroicons/vue/24/solid";
import AppLayout from "@/Layouts/AppLayout.vue";
import ProjectCard from "@/Shared/ProjectCard.vue";
import ProjectSearch from "@/Shared/ProjectSearch.vue";
import Pagination from "@/Shared/Pagination.vue";
import pickBy from "lodash/pickBy";
export default {
    components: {
        AppLayout,
        ProjectCard,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        ChevronDownIcon,
        QueueListIcon,
        ProjectSearch,
        Pagination,
        Squares2X2Icon,
    },
    props: {
        projects: {
            default: () => ({}),
            type: Object,
        },
        filters: {
            default: () => ({
                search: "",
                sort: "creation",
                mode: "grid",
            }),
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
                sort: "creation",
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
                this.filters.sort = "creation";
            }
        }
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get("/projects", pickBy(this.form), {
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
