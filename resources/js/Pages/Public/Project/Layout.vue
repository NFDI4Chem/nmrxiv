<template>
    <app-layout :title="project.name">
        <template #header>
            <div class="bg-white border-b">
                <div class="border-b">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
                        <img
                            v-if="project && project.data.owner"
                            class="inline h-7 w-7 rounded-full"
                            :src="project.data.owner.profile_photo_url"
                        />
                        <p
                            class="inline ml-3 text-xs font-bold text-gray-500 uppercase"
                        >
                            <a class="text-gray-900">{{
                                project.data.owner.first_name +
                                " " +
                                project.data.owner.last_name
                            }}</a>
                            updated on
                            <time datetime="2020-08-25">{{
                                formatDate(project.data.updated_at)
                            }}</time>
                        </p>

                        <div class="float-right">
                            <a
                                class="ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                Download
                            </a>
                        </div>
                        <div class="float-right">
                            <div class="flex-shrink-0">
                                <span
                                    v-if="project.data.stats"
                                    class="relative z-0 inline-flex shadow-sm rounded-lg"
                                >
                                    <button
                                        type="button"
                                        class="relative inline-flex items-center px-1 py-1 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                    <a
                                        class="-ml-px relative inline-flex items-center px-4 py-1 rounded-r-md border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                        {{ project.data.stats.likes }}
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="pb-5">
                        <div>
                            <div>
                                <img
                                    class="h-32 w-full object-cover lg:h-48"
                                    src="https://images.unsplash.com/photo-1637625854255-d893202554f4?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80"
                                    alt=""
                                />
                            </div>
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                <div
                                    class="-mt-12 sm:-mt-16 sm:flex sm:items-end sm:space-x-5"
                                >
                                    <div class="flex mb-5">
                                        <img
                                            class="h-24 w-72 object-cover border rounded ring-4 ring-white sm:h-32 sm:w-96"
                                            :src="project.data.photo_url"
                                            alt=""
                                        />
                                    </div>
                                    <div
                                        class="mt-6 sm:flex-1 sm:min-w-0 sm:flex sm:items-center sm:justify-end sm:space-x-6 sm:pb-1"
                                    >
                                        <div
                                            class="sm:hidden 2xl:block min-w-0 flex-1"
                                        >
                                            <h1
                                                class="text-2xl font-bold text-gray-900 break-words"
                                            >
                                                {{ project.data.name }}
                                            </h1>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="hidden sm:block 2xl:hidden min-w-0 flex-1"
                                >
                                    <h1
                                        class="text-2xl font-bold text-gray-900 break-words"
                                    >
                                        {{ project.data.name }}
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <main
            class="flex-1 relative z-0 overflow-y-auto focus:outline-none xl:order-last"
        >
            <div>
                <div class="mt-6 sm:mt-2 2xl:mt-5">
                    <div class="border-b border-gray-200">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <nav
                                class="-mb-px flex space-x-8"
                                aria-label="Tabs"
                            >
                                <Link
                                    :href="
                                        route('public.project', {
                                            owner: project.data.owner.username,
                                            slug: project.data.slug,
                                            _query: {
                                                tab: tab.name,
                                            },
                                        })
                                    "
                                    v-for="tab in tabs"
                                    :key="tab.name"
                                    :class="[
                                        selectedTab == tab.name
                                            ? 'border-pink-500 text-gray-900'
                                            : '',
                                        'cursor-pointer capitalize text-gray-900 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                                    ]"
                                    aria-current="page"
                                >
                                    {{ tab.name }}
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="bg-white">
                    <slot name="project-content"></slot>
                </div>
            </div>
        </main>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";

export default {
    components: {
        AppLayout,
        Link,
    },
    props: ["project", "selectedTab"],
    data() {
        return {
            tabs: [
                {
                    name: "info",
                    description: "",
                    icon: "",
                },
                {
                    name: "studies",
                    description: "",
                    icon: "",
                },
                {
                    name: "files",
                    description: "",
                    icon: "",
                },
                {
                    name: "license",
                    description: "",
                    icon: "",
                },
            ],
        };
    },
    computed: {},
    mounted() {},
    methods: {},
};
</script>
