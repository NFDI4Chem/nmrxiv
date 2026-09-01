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
                                <!-- View and download statistics -->
                                <div
                                    v-if="study.stats"
                                    class="hidden sm:flex items-center gap-3 text-sm text-gray-600"
                                >
                                    <span
                                        class="inline-flex items-center gap-1"
                                        title="Views"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M10 12a2 2 0 100-4 2 2 0 000 4z"
                                            />
                                            <path
                                                fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                        {{ study.stats.views }}
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1"
                                        title="Downloads"
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            aria-hidden="true"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                        {{ study.stats.downloads }}
                                    </span>
                                </div>

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

                                <!-- Download menu (shown if download URL is available) -->
                                <div
                                    v-if="study.download_url"
                                    class="flex-shrink-0"
                                >
                                    <Menu as="div" class="relative text-left">
                                        <MenuButton
                                            type="button"
                                            class="inline-flex items-center rounded-full border border-gray-300 bg-white px-4 py-1.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500"
                                        >
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
                                            <ChevronDownIcon
                                                class="ml-2 h-4 w-4 text-gray-500"
                                                aria-hidden="true"
                                            />
                                        </MenuButton>
                                        <transition
                                            enter-active-class="transition ease-out duration-100"
                                            enter-from-class="transform scale-95 opacity-0"
                                            enter-to-class="transform scale-100 opacity-100"
                                            leave-active-class="transition ease-in duration-75"
                                            leave-from-class="transform scale-100 opacity-100"
                                            leave-to-class="transform scale-95 opacity-0"
                                        >
                                            <MenuItems
                                                class="absolute right-0 z-50 mt-2 w-72 origin-top-right overflow-hidden rounded-lg bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none dark:bg-gray-800 dark:ring-white/10"
                                            >
                                                <MenuItem v-slot="{ active }">
                                                    <button
                                                        type="button"
                                                        :class="[
                                                            active
                                                                ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-gray-100'
                                                                : 'text-gray-700 dark:text-gray-200',
                                                            'block w-full px-4 py-2 text-left text-sm font-medium',
                                                        ]"
                                                        @click="
                                                            requestDownload(
                                                                study.download_url,
                                                                trackingIdentifier(
                                                                    study
                                                                )
                                                            )
                                                        "
                                                    >
                                                        Download Project Data
                                                    </button>
                                                </MenuItem>
                                                <MenuItem v-slot="{ active }">
                                                    <button
                                                        type="button"
                                                        :disabled="
                                                            !bagitArchiveReady
                                                        "
                                                        :title="
                                                            bagitArchiveReady
                                                                ? null
                                                                : bagitStatusLabel +
                                                                  ' - archive not ready yet'
                                                        "
                                                        :class="[
                                                            bagitArchiveReady &&
                                                            active
                                                                ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-gray-100'
                                                                : '',
                                                            bagitArchiveReady
                                                                ? 'text-gray-700 dark:text-gray-200'
                                                                : 'cursor-not-allowed text-gray-400 dark:text-gray-500',
                                                            'block w-full px-4 py-2 text-left text-sm font-medium',
                                                        ]"
                                                        @click="
                                                            requestBagitDownload
                                                        "
                                                    >
                                                        Download Bagit Archive
                                                        for this sample
                                                    </button>
                                                </MenuItem>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
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

        <DownloadTermsModal
            :show="showDownloadTerms"
            :download-url="pendingDownloadUrl"
            :download-identifier="pendingDownloadIdentifier"
            :license-title="study?.license?.title"
            @close="closeDownloadTerms"
        />
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { ChevronDownIcon } from "@heroicons/vue/24/outline";
import DownloadTermsModal from "@/Shared/DownloadTermsModal.vue";
import DownloadTerms from "@/Mixins/DownloadTerms.js";

export default {
    components: {
        AppLayout,
        Link,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        ChevronDownIcon,
        DownloadTermsModal,
    },
    mixins: [DownloadTerms],
    props: ["study"],
    data() {
        return {
            tabs: [],
        };
    },
    computed: {
        bagitJobStatus() {
            return (
                this.study?.metadata_bagit_generation_status ||
                (this.study?.bagit_archive_link ? "completed" : "pending")
            );
        },
        bagitArchiveReady() {
            return (
                this.bagitJobStatus === "completed" &&
                Boolean(this.study?.bagit_archive_link)
            );
        },
        bagitStatusLabel() {
            switch (this.bagitJobStatus) {
                case "pending":
                    return "Queued";
                case "processing":
                    return "Processing";
                case "failed":
                    return "Failed";
                case "completed":
                    return "Ready";
                default:
                    return "Queued";
            }
        },
    },
    mounted() {},
    methods: {
        requestBagitDownload() {
            if (!this.bagitArchiveReady) {
                return;
            }

            this.requestDownload(
                this.study.bagit_archive_link,
                this.trackingIdentifier(this.study)
            );
        },
    },
};
</script>
