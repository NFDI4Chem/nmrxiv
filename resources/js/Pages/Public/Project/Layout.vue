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
                <div
                    v-if="workspace && dashboardProject"
                    class="border-b border-gray-200 dark:border-gray-700"
                >
                    <div
                        v-if="dashboardProject.is_deleted"
                        class="px-3 py-2 text-center text-red-800 bg-red-50 border-b border-red-100 dark:bg-red-950/40 dark:text-red-100"
                    >
                        <b>Warning: </b> This project is deleted. At the end of
                        the 30-day period, this project and all of its resources
                        will be deleted permanently and cannot be recovered.
                    </div>
                    <div
                        v-else-if="
                            !dashboardProject.is_public &&
                            !dashboardProject.is_published &&
                            dashboardProject.doi &&
                            !workspace.preview
                        "
                        class="border-b border-teal-200/80 bg-teal-50 px-3 py-3 text-center text-teal-900 dark:border-teal-800/50 dark:bg-teal-950/55 dark:text-teal-100"
                    >
                        <p
                            class="mx-auto max-w-3xl text-sm leading-relaxed sm:text-base"
                        >
                            <b>Info:</b>
                            This project is in embargo and is scheduled for
                            release on
                            <strong
                                class="font-semibold text-teal-950 dark:text-teal-50"
                                >{{
                                    formatDate(
                                        dashboardProject.release_date,
                                    )
                                }}</strong>. You cannot edit the project from this public page;
                            create a new version to update its contents.
                            <button
                                v-if="showReleaseDateEditLink"
                                type="button"
                                class="whitespace-nowrap font-semibold text-teal-800 underline decoration-teal-600/45 underline-offset-2 hover:text-teal-950 dark:text-teal-200 dark:decoration-teal-300/50 dark:hover:text-white"
                                @click="openReleaseDateModal"
                            >
                                Edit release date
                            </button>
                        </p>
                    </div>
                    <template v-else-if="dashboardProject.is_public">
                        <div
                            v-if="dashboardProject.is_archived"
                            class="px-3 py-2 text-center text-yellow-800 bg-yellow-50 border-b border-yellow-100 dark:bg-yellow-950/40 dark:text-yellow-100"
                        >
                            <b>Warning: </b> This project is archived. It is now
                            read-only.
                        </div>
                        <div
                            v-else
                            class="px-3 py-2 text-center text-green-800 bg-green-50 border-b border-green-100 dark:bg-green-950/40 dark:text-green-100"
                        >
                            <b>Info: </b> This project is published. You cannot
                            edit a published project. Contact us at
                            info.nmrxiv@uni-jena.de if you need to make changes.
                        </div>
                    </template>
                </div>
                <!-- Project header: single max-width column (matches tab strip + tab body) -->
                <div
                    class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8"
                >
                    <div class="border-b border-gray-200 bg-white py-3">
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
                                    <p
                                        class="text-sm font-semibold text-gray-900 truncate"
                                    >
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
                            <div
                                class="flex items-center space-x-2 flex-shrink-0"
                            >
                                <!-- Like/upvote button with count -->
                                <div
                                    v-if="project.data.stats"
                                    class="flex-shrink-0"
                                >
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
                    <div class="bg-white pb-6">
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
                                        class="flex flex-col sm:flex-row sm:items-start sm:space-x-4"
                                    >
                                        <!-- Project title -->
                                        <div class="min-w-0 flex-1">
                                            <div
                                                class="flex flex-wrap items-start gap-2"
                                            >
                                                <button
                                                    v-if="workspace"
                                                    type="button"
                                                    class="mt-1 shrink-0 rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                                                    :aria-pressed="
                                                        dashboardProject?.is_bookmarked
                                                            ? 'true'
                                                            : 'false'
                                                    "
                                                    aria-label="Toggle bookmark"
                                                    @click="toggleStarred"
                                                >
                                                    <BookmarkIconOutline
                                                        v-if="
                                                            !dashboardProject?.is_bookmarked
                                                        "
                                                        class="h-6 w-6 text-gray-400 dark:text-gray-500"
                                                    />
                                                    <BookmarkIconSolid
                                                        v-else
                                                        class="h-6 w-6 text-teal-600 dark:text-teal-400"
                                                    />
                                                </button>
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
                                            <div
                                                v-if="workspace"
                                                class="mt-3 flex flex-wrap items-center gap-3"
                                            >
                                                <access-dialogue
                                                    :available-roles="
                                                        workspace.availableRoles
                                                    "
                                                    :role="workspace.role"
                                                    :team="workspace.team"
                                                    :members="workspace.members"
                                                    :project="dashboardProject"
                                                    called-from="projectView"
                                                    model="project"
                                                />
                                                <Link
                                                    v-if="
                                                        workspace
                                                            .projectPermissions
                                                            ?.canManageSettings
                                                    "
                                                    :href="
                                                        route(
                                                            'dashboard.project.settings',
                                                            project.data.id
                                                        )
                                                    "
                                                    class="text-sm font-semibold text-gray-800 hover:text-teal-700 dark:text-gray-200"
                                                >
                                                    Project settings
                                                </Link>
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
        <main
            class="flex-1 relative z-0 overflow-y-auto focus:outline-none xl:order-last"
        >
            <!-- Same max-width + horizontal padding as project header -->
            <div
                class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8"
            >
                <!-- Navigation tabs section -->
                <div class="mt-6 sm:mt-2 2xl:mt-5">
                    <div class="border-b border-gray-200">
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
                                    'cursor-pointer text-gray-900 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                                ]"
                                aria-current="page"
                            >
                                <span class="capitalize">{{ tab.name }}</span>
                                <span
                                    v-if="
                                        tab.name === 'samples' &&
                                        samplesTabCount !== null
                                    "
                                    class="ml-0.5 font-medium text-gray-500 normal-case dark:text-gray-400"
                                >
                                    ({{ samplesTabCount }})
                                </span>
                            </Link>
                        </nav>
                    </div>
                </div>

                <!-- Content area for tab-specific content -->
                <div class="bg-white">
                    <!-- Slot for project-specific content based on selected tab -->
                    <slot name="project-content" />
                </div>
            </div>
        </main>

        <jet-dialog-modal
            :show="showReleaseDateModal"
            max-width="2xl"
            @close="closeReleaseDateModal"
        >
            <template #title>
                Update release date
            </template>

            <template #content>
                <div v-if="releaseDateModalError" class="mb-4">
                    <div
                        class="rounded-md bg-red-50 p-4 dark:bg-red-950/40"
                        role="alert"
                    >
                        <p
                            class="text-sm font-medium text-red-800 dark:text-red-200"
                        >
                            Could not update release date
                        </p>
                        <p class="mt-1 text-sm text-red-700 dark:text-red-300">
                            {{ releaseDateModalError }}
                        </p>
                    </div>
                </div>

                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Release date
                </label>
                <Datepicker
                    v-model="releaseDateForm.release_date"
                    :format="customDateFormat"
                    :min-date="new Date()"
                    :preview-format="customDateFormat"
                />
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Choose when this project becomes public. Validation rules
                    follow your selected date (for example, citation DOIs are
                    required when the date is today or in the past).
                </p>

                <div class="mt-5">
                    <h3
                        class="text-lg font-bold text-gray-400 dark:text-gray-500"
                    >
                        Terms & Conditions
                    </h3>
                    <div class="mt-3">
                        <div class="ml-2 flex items-start">
                            <input
                                id="public-release-conditions"
                                v-model="releaseDateAck.conditions"
                                type="checkbox"
                                class="mt-1 rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-800"
                            />
                            <label
                                for="public-release-conditions"
                                class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                            >
                                I understand that publishing makes all
                                underlying data publicly available on the nmrXiv
                                platform after the set release date.
                            </label>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="ml-2 flex items-start">
                            <input
                                id="public-release-terms"
                                v-model="releaseDateAck.terms"
                                type="checkbox"
                                class="mt-1 rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-800"
                            />
                            <label
                                for="public-release-terms"
                                class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                            >
                                I agree to the
                                <a
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    :href="route('terms.show')"
                                    class="underline text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                                    >Terms of Service</a
                                >
                                and
                                <a
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    :href="route('policy.show')"
                                    class="underline text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                                    >Privacy Policy</a
                                >
                                and hereby also grant nmrXiv permissions to
                                distribute the datasets (and meta-data) under
                                the specified license.
                            </label>
                        </div>
                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button type="button" @click="closeReleaseDateModal">
                    Cancel
                </jet-secondary-button>
                <jet-success-button
                    type="button"
                    class="ml-2"
                    :class="[
                        !releaseDateAck.terms || !releaseDateAck.conditions
                            ? 'bg-gray-200 cursor-not-allowed dark:bg-gray-700'
                            : 'bg-primary-600 hover:bg-primary-700',
                    ]"
                    :disabled="
                        releaseDateForm.processing ||
                        !releaseDateAck.terms ||
                        !releaseDateAck.conditions
                    "
                    @click="submitReleaseDateUpdate"
                >
                    Update release date
                </jet-success-button>
            </template>
        </jet-dialog-modal>
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
 * - Tab navigation system for different project sections
 * - Proper title case formatting for project names
 */

// Layout and navigation imports
import AppLayout from "@/Layouts/AppLayout.vue";
import AccessDialogue from "@/Shared/AccessDialogue.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetSuccessButton from "@/Jetstream/SuccessButton.vue";
import Datepicker from "@vuepic/vue-datepicker";
import { Link, router } from "@inertiajs/vue3";
import { BookmarkIcon as BookmarkIconSolid } from "@heroicons/vue/24/solid";
import { BookmarkIcon as BookmarkIconOutline } from "@heroicons/vue/24/outline";

import "@vuepic/vue-datepicker/dist/main.css";

// Icon imports from Heroicons

export default {
    name: "PublicProjectLayout",

    /**
     * Component dependencies
     */
    components: {
        AppLayout, // Main application layout wrapper
        AccessDialogue,
        JetDialogModal,
        JetSecondaryButton,
        JetSuccessButton,
        Datepicker,
        Link, // Inertia.js Link component for navigation
        BookmarkIconSolid,
        BookmarkIconOutline,
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
            showReleaseDateModal: false,
            releaseDateModalError: null,
            releaseDateAck: {
                conditions: false,
                terms: false,
            },
            releaseDateForm: null,
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
        workspace() {
            return this.$page.props.workspace ?? null;
        },
        dashboardProject() {
            return this.workspace?.dashboardProject ?? null;
        },
        samplesTabCount() {
            const raw = this.project?.data?.samples_count;

            return typeof raw === "number" && Number.isFinite(raw)
                ? raw
                : null;
        },
        showReleaseDateEditLink() {
            const p = this.dashboardProject;
            const w = this.workspace;
            if (!p || !w || w.preview) {
                return false;
            }

            return (
                !p.is_public &&
                !p.is_published &&
                Boolean(p.doi)
            );
        },
    },

    created() {
        this.releaseDateForm = this.$inertia.form({
            _method: "PUT",
            name: "",
            enableProjectMode: false,
            release_date: null,
        });
    },

    mounted() {
        this.$nextTick(() => {
            this.handleReleaseDateEditQueryParam();
        });
    },

    /**
     * Component methods
     */
    methods: {
        openReleaseDateModal() {
            const p = this.dashboardProject;
            if (!p?.id) {
                return;
            }
            this.releaseDateAck = { conditions: false, terms: false };
            this.releaseDateModalError = null;
            this.releaseDateForm.name = p.name;
            this.releaseDateForm.enableProjectMode = Boolean(
                p.enableProjectMode ?? p.enable_project_mode,
            );
            this.releaseDateForm.release_date = p.release_date;
            this.releaseDateForm.clearErrors();
            this.showReleaseDateModal = true;
        },

        closeReleaseDateModal() {
            this.showReleaseDateModal = false;
            this.releaseDateModalError = null;
        },

        stripReleaseDateEditQueryFromUrl() {
            const url = new URL(window.location.href);
            if (url.searchParams.get("edit") !== "release_date") {
                return;
            }
            url.searchParams.delete("edit");
            const search = url.searchParams.toString();
            const next =
                url.pathname + (search ? `?${search}` : "") + url.hash;
            window.history.replaceState({}, "", next);
        },

        handleReleaseDateEditQueryParam() {
            const params = new URLSearchParams(window.location.search);
            if (params.get("edit") !== "release_date") {
                return;
            }
            if (this.showReleaseDateEditLink) {
                this.openReleaseDateModal();
            }
            this.stripReleaseDateEditQueryFromUrl();
        },

        submitReleaseDateUpdate() {
            if (
                !this.releaseDateAck.conditions ||
                !this.releaseDateAck.terms ||
                !this.dashboardProject?.id
            ) {
                return;
            }
            this.releaseDateModalError = null;
            this.releaseDateForm.put(
                this.route(
                    "dashboard.project.updateReleaseDate",
                    this.dashboardProject.id,
                ),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.showReleaseDateModal = false;
                        router.reload({ only: ["project", "workspace"] });
                    },
                    onError: (errors) => {
                        const keys = Object.keys(errors);
                        if (keys.length === 0) {
                            this.releaseDateModalError =
                                "Could not update release date.";
                        } else {
                            const k = keys[0];
                            const v = errors[k];
                            this.releaseDateModalError = Array.isArray(v)
                                ? v[0]
                                : String(
                                      v ?? "Could not update release date.",
                                  );
                        }
                    },
                },
            );
        },

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
        toggleStarred() {
            if (!this.project?.data?.id) {
                return;
            }
            window.axios
                .get(
                    this.route("project.toggle-starred", [
                        this.project.data.id,
                    ])
                )
                .then(() => {
                    router.reload({ only: ["project", "workspace"] });
                })
                .catch(() => {});
        },
        toggleUpVote() {
            // Check if user is authenticated
            if (
                this.$page.props.auth.username &&
                this.$page.props.auth.username != ""
            ) {
                // Construct API endpoint URL
                const url =
                    "/projects/" + this.project.data.id + "/toggleUpVote";

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
                "a",
                "an",
                "and",
                "as",
                "at",
                "but",
                "by",
                "for",
                "if",
                "in",
                "nor",
                "of",
                "on",
                "or",
                "so",
                "the",
                "to",
                "up",
                "yet",
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
            if (!dateString) return "";

            const date = new Date(dateString);
            const options = {
                year: "numeric",
                month: "long",
                day: "numeric",
            };

            return date.toLocaleDateString("en-US", options);
        },
    },
};
</script>
