<template>
    <app-layout :title="study.name">
        <template #header>
            <!-- Study header section with background styling -->
            <div class="bg-white index_beams">
                <!-- Top header bar with owner info and actions -->
                <div class="border-b bg-white">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                        <!-- Responsive flex container for header content -->
                        <div
                            class="flex flex-col space-y-3 sm:flex-row sm:items-center sm:justify-between sm:space-y-0"
                        >
                            <!-- Left side: Owner information -->
                            <div class="flex items-center space-x-3">
                                <!-- Owner profile photo -->
                                <img
                                    v-if="study && study.owner"
                                    class="h-8 w-8 rounded-full flex-shrink-0"
                                    :src="study.owner.profile_photo_url"
                                    :alt="
                                        study.owner.first_name +
                                        ' ' +
                                        study.owner.last_name
                                    "
                                />
                                <!-- Owner details and last updated info -->
                                <div class="min-w-0 flex-1">
                                    <!-- Owner full name -->
                                    <p
                                        class="text-sm font-semibold text-gray-900 truncate"
                                    >
                                        {{
                                            study.owner.first_name +
                                            " " +
                                            study.owner.last_name
                                        }}
                                    </p>
                                    <!-- Last updated timestamp -->
                                    <p class="text-xs text-gray-500">
                                        Updated on
                                        <time class="font-medium">{{
                                            formatDate(study.updated_at)
                                        }}</time>
                                    </p>
                                </div>
                            </div>

                            <!-- Right side: Action buttons and statistics -->
                            <div
                                class="flex items-center space-x-2 flex-shrink-0"
                            >
                                <!-- Like/upvote button with count -->
                                <div v-if="study.stats" class="flex-shrink-0">
                                    <div
                                        class="inline-flex shadow-sm rounded-full"
                                    >
                                        <!-- Upvote button -->
                                        <button
                                            type="button"
                                            class="relative inline-flex items-center px-2 py-1.5 rounded-l-full border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"
                                            @click="toggleUpVote()"
                                        >
                                            <!-- Upvote arrow icon -->
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
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
                                        <!-- Like count display -->
                                        <span
                                            class="-ml-px relative inline-flex items-center px-3 py-1.5 rounded-r-full border border-gray-300 bg-white text-sm font-semibold text-gray-900"
                                        >
                                            {{ study.stats.likes }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Download button (shown if download URL is available) -->
                                <div
                                    v-if="study.download_url"
                                    class="flex-shrink-0"
                                >
                                    <a
                                        class="inline-flex items-center px-4 py-1.5 rounded-full border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"
                                        :href="study.download_url"
                                    >
                                        <!-- Download icon -->
                                        <svg
                                            class="-ml-1 mr-2 h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                        Download
                                    </a>
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
                <div class="border-b border-gray-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <Link
                                v-for="tab in tabs"
                                :key="tab.name"
                                :href="study.public_url + '?tab=' + tab.name"
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
