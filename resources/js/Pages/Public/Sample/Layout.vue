<template>
    <app-layout :title="study.name">
        <template #header>
            <div>
                <div class="border-b">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
                        <img
                            v-if="study && study.owner"
                            class="inline h-7 w-7 rounded-full"
                            :src="study.owner.profile_photo_url"
                        />
                        <p
                            class="inline ml-3 text-xs font-bold text-gray-500 uppercase"
                        >
                            <a class="text-gray-900">{{
                                study.owner.first_name +
                                " " +
                                study.owner.last_name
                            }}</a>
                            <span class="block md:inline ml-10 md:ml-0">
                                updated on
                                <time>{{ formatDate(study.updated_at) }}</time>
                            </span>
                        </p>
                        <div class="flex mt-2 md:inline">
                            <div
                                v-if="study.download_url"
                                class="float-left md:float-right"
                            >
                                <a
                                    class="md:ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                    :href="study.download_url"
                                >
                                    Download
                                </a>
                            </div>
                            <div class="float-left md:float-right">
                                <div class="flex-shrink-0">
                                    <span
                                        v-if="study.stats"
                                        class="relative z-0 inline-flex shadow-sm rounded-full"
                                    >
                                        <button
                                            type="button"
                                            class="relative inline-flex items-center px-1 py-1 rounded-l-full border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50"
                                            @click="toggleUpVote()"
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
                                            class="-ml-px relative inline-flex items-center px-4 py-1 rounded-r-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                        >
                                            {{ study.stats.likes }}
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <img
                    class="h-32 w-full object-cover lg:h-48"
                    src="https://images.unsplash.com/photo-1637625854255-d893202554f4?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80"
                />
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
                                    v-for="tab in tabs"
                                    :key="tab.name"
                                    :href="
                                        study.public_url + '?tab=' + tab.name
                                    "
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
                    <slot name="sample-content"></slot>
                </div>
            </div>
        </main>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router } from "@inertiajs/vue3";
import { ArrowDownTrayIcon } from "@heroicons/vue/24/solid";
import DOIBadge from "@/Shared/DOIBadge.vue";

export default {
    components: {
        AppLayout,
        Link,
        ArrowDownTrayIcon,
        DOIBadge,
    },
    props: ["study"],
    data() {
        return {
            tabs: [],
        };
    },
    computed: {},
    mounted() {},
    methods: {},
};
</script>
