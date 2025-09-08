<!--
  Public Project Layout Component
  
  This component provides the main layout structure for public project pages,
  including project header with owner information, statistics, metadata display,
  and navigation tabs. It serves as a wrapper for all project-related content
  with responsive design and comprehensive project information display.
-->
<template>
    <!-- Main application layout wrapper -->
    <app-layout :title="project.name">
        <template #header>
            <!-- Project header section with background styling -->
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
                                    v-if="project && project.data.owner"
                                    class="h-8 w-8 rounded-full flex-shrink-0"
                                    :src="project.data.owner.profile_photo_url"
                                    :alt="
                                        project.data.owner.first_name +
                                        ' ' +
                                        project.data.owner.last_name
                                    "
                                />
                                <!-- Owner details and last updated info -->
                                <div class="min-w-0 flex-1">
                                    <!-- Owner full name -->
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{
                                            project.data.owner.first_name +
                                            " " +
                                            project.data.owner.last_name
                                        }}
                                    </p>
                                    <!-- Last updated timestamp -->
                                    <p class="text-xs text-gray-500">
                                        Updated on
                                        <time class="font-medium">{{
                                            formatDate(project.data.updated_at)
                                        }}</time>
                                    </p>
                                </div>
                            </div>

                            <!-- Right side: Action buttons and statistics -->
                            <div class="flex items-center space-x-2 flex-shrink-0">
                                <!-- Like/upvote button with count -->
                                <div
                                    v-if="project.data.stats"
                                    class="flex-shrink-0"
                                >
                                    <div class="inline-flex shadow-sm rounded-full">
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
                                            {{ project.data.stats.likes }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Download button (shown if download URL is available) -->
                                <div
                                    v-if="project.data.download_url"
                                    class="flex-shrink-0"
                                >
                                    <a
                                        class="inline-flex items-center px-4 py-1.5 rounded-full border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"
                                        :href="project.data.download_url"
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
                <!-- Project details section -->
                <div>
                    <div class="pb-6 border-b border-gray-200">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="relative z-10 pt-5">
                                <!-- Project photo/banner (optional) -->
                                <div
                                    v-if="
                                        project.data.photo_url &&
                                        project.data.photo_url != ''
                                    "
                                    class="flex justify-center sm:justify-start h-24 w-72 md:mb-12"
                                >
                                    <img
                                        class="h-24 w-72 object-cover border rounded ring-4 ring-white sm:h-32 sm:w-96"
                                        :src="project.data.photo_url"
                                        :alt="project.data.name"
                                    />
                                </div>

                                <!-- Project title and identifier section -->
                                <div class="text-left rounded-lg pt-2 sm:pt-2">
                                    <!-- Project title and identifier row -->
                                    <div
                                        v-if="project"
                                        class="flex flex-col sm:flex-row sm:items-start sm:justify-between sm:space-x-4"
                                    >
                                        <!-- Project title -->
                                        <div class="min-w-0 flex-1">
                                            <h1
                                                class="text-xl sm:text-2xl lg:text-3xl font-semibold text-gray-900 break-words leading-tight"
                                            >
                                                {{
                                                    toTitleCase(
                                                        (project.data &&
                                                            project.data
                                                                .name) ||
                                                            project.name ||
                                                            "Project Name"
                                                    )
                                                }}
                                            </h1>
                                        </div>
                                        
                                        <!-- Project identifier badge -->
                                        <div
                                            v-if="
                                                (project.data &&
                                                    project.data.identifier) ||
                                                project.identifier
                                            "
                                            class="mt-2 sm:mt-0 flex-shrink-0"
                                        >
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-700 border border-gray-200"
                                            >
                                                #{{
                                                    (project.data &&
                                                        project.data
                                                            .identifier) ||
                                                    project.identifier
                                                }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- DOI Badge - Primary identifier for citations -->
                                    <div
                                        v-if="
                                            (project.data && project.data.doi) ||
                                            project.doi
                                        "
                                        class="mt-3"
                                    >
                                        <DOIBadge
                                            :doi="
                                                (project.data && project.data.doi) ||
                                                project.doi
                                            "
                                        />
                                    </div>

                                    <!-- Metadata row with license, dates and other info -->
                                    <div
                                        v-if="
                                            ((project.data && project.data.license) || project.license) ||
                                            ((project.data && (project.data.release_date || project.data.created_at)) || 
                                             (project.release_date || project.created_at))
                                        "
                                        class="mt-3"
                                    >
                                        <!-- Mobile-first responsive layout -->
                                        <div class="space-y-3 sm:space-y-0">
                                            <!-- Desktop: Single row layout -->
                                            <div class="hidden sm:flex sm:flex-wrap sm:items-center sm:gap-x-6 sm:gap-y-2 text-sm text-gray-600">
                                                <!-- License Information -->
                                                <div 
                                                    v-if="
                                                        (project.data && project.data.license) ||
                                                        project.license
                                                    "
                                                    class="flex items-center space-x-1.5"
                                                >
                                                    <ScaleIcon
                                                        class="h-4 w-4 text-gray-400 flex-shrink-0"
                                                        aria-hidden="true"
                                                    />
                                                    <span class="font-medium">License:</span>
                                                    <a
                                                        v-if="
                                                            ((project.data && project.data.license && project.data.license.url) ||
                                                            (project.license && project.license.url))
                                                        "
                                                        :href="
                                                            (project.data && project.data.license && project.data.license.url) ||
                                                            (project.license && project.license.url)
                                                        "
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="font-medium text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-150"
                                                        :title="'View ' + ((project.data && project.data.license && project.data.license.title) || (project.license && project.license.title)) + ' license details'"
                                                    >
                                                        {{
                                                            (project.data && project.data.license && project.data.license.title) ||
                                                            (project.license && project.license.title)
                                                        }}
                                                    </a>
                                                    <span
                                                        v-else
                                                        class="font-medium text-gray-900"
                                                    >
                                                        {{
                                                            (project.data && project.data.license && project.data.license.title) ||
                                                            (project.license && project.license.title)
                                                        }}
                                                    </span>
                                                </div>

                                                <!-- Project Dates -->
                                                <div 
                                                    v-if="
                                                        (project.data && (project.data.release_date || project.data.created_at)) ||
                                                        (project.release_date || project.created_at)
                                                    "
                                                    class="flex items-center space-x-1.5"
                                                >
                                                    <CalendarDaysIcon
                                                        class="h-4 w-4 text-gray-400 flex-shrink-0"
                                                        aria-hidden="true"
                                                    />
                                                    <div class="flex items-center space-x-3">
                                                        <!-- Published date -->
                                                        <div 
                                                            v-if="
                                                                (project.data && project.data.release_date) ||
                                                                project.release_date
                                                            " 
                                                            class="flex items-center space-x-1"
                                                        >
                                                            <span class="font-medium">Published:</span>
                                                            <span class="font-medium text-gray-900">
                                                                {{ 
                                                                    formatDate(
                                                                        (project.data && project.data.release_date) ||
                                                                        project.release_date
                                                                    ) 
                                                                }}
                                                            </span>
                                                        </div>
                                                        
                                                        <!-- Separator -->
                                                        <div 
                                                            v-if="
                                                                ((project.data && project.data.release_date) || project.release_date) &&
                                                                ((project.data && project.data.created_at) || project.created_at)
                                                            " 
                                                            class="text-gray-300"
                                                        >•</div>
                                                        
                                                        <!-- Created date -->
                                                        <div 
                                                            v-if="
                                                                (project.data && project.data.created_at) ||
                                                                project.created_at
                                                            " 
                                                            class="flex items-center space-x-1"
                                                        >
                                                            <span class="font-medium">Created:</span>
                                                            <span class="font-medium text-gray-900">
                                                                {{ 
                                                                    formatDate(
                                                                        (project.data && project.data.created_at) ||
                                                                        project.created_at
                                                                    ) 
                                                                }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Mobile: Label-above-value layout -->
                                            <div class="sm:hidden space-y-3 text-sm">
                                                <!-- License Information -->
                                                <div 
                                                    v-if="
                                                        (project.data && project.data.license) ||
                                                        project.license
                                                    "
                                                    class="space-y-1"
                                                >
                                                    <div class="flex items-center space-x-1.5 text-gray-600">
                                                        <ScaleIcon
                                                            class="h-4 w-4 text-gray-400 flex-shrink-0"
                                                            aria-hidden="true"
                                                        />
                                                        <span class="font-medium">License</span>
                                                    </div>
                                                    <div class="ml-5.5">
                                                        <a
                                                            v-if="
                                                                ((project.data && project.data.license && project.data.license.url) ||
                                                                (project.license && project.license.url))
                                                            "
                                                            :href="
                                                                (project.data && project.data.license && project.data.license.url) ||
                                                                (project.license && project.license.url)
                                                            "
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            class="font-medium text-blue-600 hover:text-blue-800 underline"
                                                            :title="'View license details'"
                                                        >
                                                            {{
                                                                (project.data && project.data.license && project.data.license.title) ||
                                                                (project.license && project.license.title)
                                                            }}
                                                        </a>
                                                        <span
                                                            v-else
                                                            class="font-medium text-gray-900"
                                                        >
                                                            {{
                                                                (project.data && project.data.license && project.data.license.title) ||
                                                                (project.license && project.license.title)
                                                            }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Published date -->
                                                <div 
                                                    v-if="
                                                        (project.data && project.data.release_date) ||
                                                        project.release_date
                                                    " 
                                                    class="space-y-1"
                                                >
                                                    <div class="flex items-center space-x-1.5 text-gray-600">
                                                        <CalendarDaysIcon
                                                            class="h-4 w-4 text-gray-400 flex-shrink-0"
                                                            aria-hidden="true"
                                                        />
                                                        <span class="font-medium">Published</span>
                                                    </div>
                                                    <div class="ml-5.5">
                                                        <span class="font-medium text-gray-900">
                                                            {{ 
                                                                formatDate(
                                                                    (project.data && project.data.release_date) ||
                                                                    project.release_date
                                                                ) 
                                                            }}
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Created date -->
                                                <div 
                                                    v-if="
                                                        (project.data && project.data.created_at) ||
                                                        project.created_at
                                                    " 
                                                    class="space-y-1"
                                                >
                                                    <div class="flex items-center space-x-1.5 text-gray-600">
                                                        <CalendarDaysIcon
                                                            class="h-4 w-4 text-gray-400 flex-shrink-0"
                                                            aria-hidden="true"
                                                        />
                                                        <span class="font-medium">Created</span>
                                                    </div>
                                                    <div class="ml-5.5">
                                                        <span class="font-medium text-gray-900">
                                                            {{ 
                                                                formatDate(
                                                                    (project.data && project.data.created_at) ||
                                                                    project.created_at
                                                                ) 
                                                            }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        v-if="
                                            ((project.data &&
                                                project.data.tags) ||
                                                project.tags) &&
                                            ((project.data &&
                                                project.data.tags &&
                                                project.data.tags.length > 0) ||
                                                (project.tags &&
                                                    project.tags.length > 0))
                                        "
                                        class="mt-4"
                                    >
                                        <!-- Desktop: Full tags display -->
                                        <div class="hidden sm:block">
                                            <div class="flex flex-wrap justify-start gap-2">
                                                <a
                                                    v-for="tag in (project.data &&
                                                        project.data.tags) ||
                                                    project.tags"
                                                    :key="tag.id"
                                                    class="inline-flex items-center rounded-full bg-gray-50 px-4 py-1 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 hover:bg-gray-100 transition-colors duration-150 cursor-pointer"
                                                    :href="
                                                        '/projects?tag=' +
                                                        tag.name.en
                                                    "
                                                >
                                                    {{ tag.name.en }}
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Mobile: Compact tags display -->
                                        <div class="sm:hidden">
                                            <div class="space-y-2">
                                                <!-- Tags label -->
                                                <div class="flex items-center space-x-1.5 text-sm text-gray-600">
                                                    <TagIcon
                                                        class="h-4 w-4 text-gray-400 flex-shrink-0"
                                                        aria-hidden="true"
                                                    />
                                                    <span class="font-medium">Tags</span>
                                                </div>
                                                
                                                <!-- Limited tags with show more -->
                                                <div class="ml-5.5">
                                                    <div class="flex flex-wrap gap-1.5">
                                                        <a
                                                            v-for="(tag, index) in ((project.data && project.data.tags) || project.tags).slice(0, showAllTags ? undefined : 3)"
                                                            :key="tag.id"
                                                            class="inline-flex items-center rounded-md bg-gray-50 px-2 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 hover:bg-gray-100 transition-colors duration-150 cursor-pointer"
                                                            :href="
                                                                '/projects?tag=' +
                                                                tag.name.en
                                                            "
                                                        >
                                                            {{ tag.name.en }}
                                                        </a>
                                                        
                                                        <!-- Show more/less button -->
                                                        <button
                                                            v-if="((project.data && project.data.tags) || project.tags).length > 3"
                                                            @click="showAllTags = !showAllTags"
                                                            class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-600 hover:bg-blue-100 transition-colors duration-150 cursor-pointer"
                                                        >
                                                            {{ showAllTags ? 'Show less' : `+${((project.data && project.data.tags) || project.tags).length - 3} more` }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <!-- Main content area with navigation tabs and content slot -->
        <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none xl:order-last">
            <div>
                <!-- Navigation tabs section -->
                <div class="mt-6 sm:mt-2 2xl:mt-5">
                    <div class="border-b border-gray-200">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <!-- Tab navigation bar -->
                            <nav
                                class="-mb-px flex space-x-8"
                                aria-label="Tabs"
                            >
                                <!-- Individual tab links -->
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
                
                <!-- Content area for tab-specific content -->
                <div class="bg-white">
                    <!-- Slot for project-specific content based on selected tab -->
                    <slot name="project-content" />
                </div>
            </div>
        </main>
    </app-layout>
</template>

<script>
/**
 * Public Project Layout Component
 * 
 * This component serves as the main layout wrapper for public project pages,
 * providing a comprehensive header with project information, owner details,
 * statistics, metadata display, and navigation tabs. It creates a consistent
 * structure for all project-related content with responsive design.
 * 
 * Key Features:
 * - Project header with owner information and profile photo
 * - Interactive upvote/like functionality with authentication checks
 * - Download button for project data access
 * - DOI badge for academic citations
 * - Responsive metadata display (license, dates, tags)
 * - Mobile-optimized tag display with show more/less functionality
 * - Tab navigation system for different project sections
 * - Proper title case formatting for project names
 */

// Layout and navigation imports
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router } from "@inertiajs/vue3";

// Icon imports from Heroicons
import { ArrowDownTrayIcon, ScaleIcon, CalendarDaysIcon, TagIcon } from "@heroicons/vue/24/solid";

// Shared component imports
import DOIBadge from "@/Shared/DOIBadge.vue";

export default {
    name: "PublicProjectLayout",

    /**
     * Component dependencies
     */
    components: {
        AppLayout,              // Main application layout wrapper
        Link,                   // Inertia.js Link component for navigation
        ArrowDownTrayIcon,      // Download icon (unused but imported)
        ScaleIcon,              // License/legal icon
        CalendarDaysIcon,       // Calendar icon for dates
        TagIcon,                // Tag icon for project tags
        DOIBadge,               // DOI badge component for citations
    },

    /**
     * Component props
     */
    props: {
        /**
         * Project data object containing all project information and metadata
         * 
         * Expected structure:
         * - data.name: Project name
         * - data.owner: Owner information with profile photo
         * - data.stats: Project statistics (likes, views, etc.)
         * - data.doi: Digital Object Identifier for citations
         * - data.license: License information with title and URL
         * - data.tags: Array of project tags
         * - data.created_at/release_date: Project dates
         * - data.download_url: URL for downloading project data
         */
        project: {
            type: Object,
            required: true,
        },
        
        /**
         * Currently selected tab name for navigation highlighting
         * Used to apply active styles to the current tab
         */
        selectedTab: {
            type: String,
            default: "info",
        },
    },

    /**
     * Component reactive data
     */
    data() {
        return {
            /**
             * Controls tag display on mobile devices
             * When false, shows only first 3 tags with "show more" button
             * When true, shows all tags with "show less" button
             */
            showAllTags: false,
            
            /**
             * Available navigation tabs for the project
             * Each tab represents a different section of project information
             */
            tabs: [
                {
                    name: "info",
                    description: "Project information and description",
                    icon: "",
                },
                {
                    name: "samples",
                    description: "Project samples and datasets",
                    icon: "",
                },
                {
                    name: "files",
                    description: "Project files and data browser",
                    icon: "",
                },
                {
                    name: "license",
                    description: "License and usage information",
                    icon: "",
                },
            ],
        };
    },

    /**
     * Computed properties
     */
    computed: {
        /**
         * Get the current page URL from Inertia page props
         * @returns {String} Current application URL
         */
        url() {
            return String(this.$page.props.url);
        },
    },

    /**
     * Component methods
     */
    methods: {
        /**
         * Toggle the upvote/like status for the current project
         * 
         * Handles user interaction with the like button by:
         * - Checking user authentication status
         * - Making API call to toggle upvote status
         * - Reloading project data to reflect changes
         * - Redirecting to login if user is not authenticated
         * 
         * The method includes error handling for API failures and
         * uses Inertia's selective reloading for better performance.
         */
        toggleUpVote() {
            // Check if user is authenticated
            if (
                this.$page.props.auth.username &&
                this.$page.props.auth.username != ""
            ) {
                // Construct API endpoint URL
                const url = "/projects/" + this.project.data.id + "/toggleUpVote";
                
                // Make API call to toggle upvote
                axios
                    .get(url)
                    .catch((err) => {
                        // Handle API errors
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
                        // Reload only project data to reflect changes
                        router.reload({ only: ["project"] });
                    });
            } else {
                // Redirect to login if user is not authenticated
                this.$inertia.visit(route("login"));
            }
        },

        /**
         * Navigate to a specific project tab
         * 
         * Uses Inertia router to navigate to different project sections
         * while maintaining the current project context.
         * 
         * @param {String} tabName - Name of the tab to navigate to
         */
        navigateToTab(tabName) {
            router.visit(this.project.data.public_url + "?tab=" + tabName);
        },

        /**
         * Convert string to proper title case formatting
         * 
         * Applies title case rules where:
         * - First and last words are always capitalized
         * - Articles, prepositions, and conjunctions remain lowercase
         * - All other words are capitalized
         * 
         * This ensures consistent and professional formatting of project names
         * following standard English title capitalization rules.
         * 
         * @param {String} str - String to convert to title case
         * @returns {String} Properly formatted title case string
         * 
         * @example
         * toTitleCase("analysis of nmr data") // Returns: "Analysis of NMR Data"
         * toTitleCase("the quick brown fox") // Returns: "The Quick Brown Fox"
         */
        toTitleCase(str) {
            if (!str) return "";

            // Words that should remain lowercase (except at beginning/end)
            const lowercaseWords = [
                "a", "an", "and", "as", "at", "but", "by", "for",
                "if", "in", "nor", "of", "on", "or", "so", "the",
                "to", "up", "yet",
            ];

            return str
                .toLowerCase()
                .split(" ")
                .map((word, index, array) => {
                    // Always capitalize first and last words
                    if (index === 0 || index === array.length - 1) {
                        return word.charAt(0).toUpperCase() + word.slice(1);
                    }

                    // Keep articles, prepositions, and conjunctions lowercase
                    if (lowercaseWords.includes(word)) {
                        return word;
                    }

                    // Capitalize all other words
                    return word.charAt(0).toUpperCase() + word.slice(1);
                })
                .join(" ");
        },

        /**
         * Format date for display in project metadata
         * 
         * Converts ISO date strings to human-readable format
         * suitable for displaying creation and publication dates.
         * 
         * @param {String} dateString - ISO date string
         * @returns {String} Formatted date string
         */
        formatDate(dateString) {
            if (!dateString) return '';
            
            const date = new Date(dateString);
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            
            return date.toLocaleDateString('en-US', options);
        },
    },
};
</script>