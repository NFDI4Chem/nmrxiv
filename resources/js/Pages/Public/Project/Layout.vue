<template>
    <app-layout :title="project.name">
        <template #header>
            <div class="bg-white">
                <div class="border-b">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                        <!-- Mobile-first responsive layout -->
                        <div class="flex flex-col space-y-3 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                            <!-- Owner info section -->
                            <div class="flex items-center space-x-3">
                                <img
                                    v-if="project && project.data.owner"
                                    class="h-8 w-8 rounded-full flex-shrink-0"
                                    :src="project.data.owner.profile_photo_url"
                                    :alt="project.data.owner.first_name + ' ' + project.data.owner.last_name"
                                />
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{
                                            project.data.owner.first_name +
                                            " " +
                                            project.data.owner.last_name
                                        }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Updated on
                                        <time class="font-medium">{{
                                            formatDate(project.data.updated_at)
                                        }}</time>
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Action buttons section -->
                            <div class="flex items-center space-x-2 flex-shrink-0">
                                <!-- Like button -->
                                <div v-if="project.data.stats" class="flex-shrink-0">
                                    <div class="inline-flex shadow-sm rounded-full">
                                        <button
                                            type="button"
                                            class="relative inline-flex items-center px-2 py-1.5 rounded-l-full border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"
                                            @click="toggleUpVote()"
                                        >
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
                                        <span
                                            class="-ml-px relative inline-flex items-center px-3 py-1.5 rounded-r-full border border-gray-300 bg-white text-sm font-semibold text-gray-900"
                                        >
                                            {{ project.data.stats.likes }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Download button -->
                                <div v-if="project.data.download_url" class="flex-shrink-0">
                                    <a
                                        class="inline-flex items-center px-4 py-1.5 rounded-full border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"
                                        :href="project.data.download_url"
                                    >
                                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="pb-6">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="relative z-10 pt-5">
                                <div v-if="project.data.photo_url && project.data.photo_url != ''" class="flex justify-center sm:justify-start mb-6">
                                    <img
                                        class="h-20 w-20 sm:h-24 sm:w-24 lg:h-32 lg:w-32 object-cover rounded-lg border-4 border-white shadow-lg"
                                        :src="project.data.photo_url"
                                        :alt="project.data.name"
                                    />
                                </div>
                                
                                <!-- Project info -->
                                <div class="text-center sm:text-left bg-white rounded-lg py-4 sm:py-6">
                                    <!-- Project title and identifier -->
                                    <div v-if="project" class="flex flex-col sm:flex-row sm:items-start sm:justify-between sm:space-x-4">
                                        <div class="min-w-0 flex-1">
                                            <h1 class="text-xl sm:text-2xl lg:text-3xl font-semibold text-gray-900 break-words leading-tight">
                                                {{ toTitleCase((project.data && project.data.name) || project.name || 'Project Name') }}
                                            </h1>
                                        </div>
                                        <div v-if="(project.data && project.data.identifier) || project.identifier" class="mt-2 sm:mt-0 flex-shrink-0">
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                                #{{ (project.data && project.data.identifier) || project.identifier }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- DOI Badge -->
                                    <div v-if="(project.data && project.data.doi) || project.doi" class="mt-3">
                                        <DOIBadge :doi="(project.data && project.data.doi) || project.doi"></DOIBadge>
                                    </div>
                                    
                                    <!-- Tags -->
                                    <div v-if="((project.data && project.data.tags) || project.tags) && ((project.data && project.data.tags && project.data.tags.length > 0) || (project.tags && project.tags.length > 0))" class="mt-4">
                                        <div class="flex flex-wrap justify-center sm:justify-start gap-2">
                                            <a
                                                v-for="tag in (project.data && project.data.tags) || project.tags"
                                                :key="tag.id"
                                                class="inline-flex items-center rounded-full bg-teal-50 px-3 py-1 text-sm font-medium text-teal-700 hover:bg-teal-100 transition-colors cursor-pointer"
                                                :href="'/projects?tag=' + tag.name.en"
                                            >
                                                {{ tag.name.en }}
                                            </a>
                                        </div>
                                    </div>
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
                                    v-for="tab in tabs"
                                    :key="tab.name"
                                    :href="
                                        project.data.public_url +
                                        '?tab=' +
                                        tab.name
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
                    <slot name="project-content"></slot>
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
                    name: "samples",
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
    computed: {
        url() {
            return String(this.$page.props.url);
        },
    },
    mounted() {},
    methods: {
        toggleUpVote() {
            if (
                this.$page.props.auth.username &&
                this.$page.props.auth.username != ""
            ) {
                const url =
                    "/projects/" + this.project.data.id + "/toggleUpVote";
                axios
                    .get(url)
                    .catch((err) => {
                        if (
                            err.response.status !== 200 ||
                            err.response.status !== 201
                        ) {
                            throw new Error(
                                `API call failed with status code: ${err.response.status} after multiple attempts`
                            );
                        }
                    })
                    .then(function () {
                        router.reload({ only: ["project"] });
                    });
            } else {
                this.$inertia.visit(route("login"));
            }
        },
        navigateToTab(tabName) {
            router.visit(this.project.data.public_url + '?tab=' + tabName);
        },
        toTitleCase(str) {
            if (!str) return '';
            
            // Words that should remain lowercase (articles, prepositions, conjunctions)
            const lowercaseWords = ['a', 'an', 'and', 'as', 'at', 'but', 'by', 'for', 'if', 'in', 'nor', 'of', 'on', 'or', 'so', 'the', 'to', 'up', 'yet'];
            
            return str.toLowerCase().split(' ').map((word, index) => {
                // Always capitalize the first and last word
                if (index === 0 || index === str.split(' ').length - 1) {
                    return word.charAt(0).toUpperCase() + word.slice(1);
                }
                
                // Keep certain words lowercase unless they're the first or last word
                if (lowercaseWords.includes(word)) {
                    return word;
                }
                
                // Capitalize other words
                return word.charAt(0).toUpperCase() + word.slice(1);
            }).join(' ');
        },
    },
};
</script>
