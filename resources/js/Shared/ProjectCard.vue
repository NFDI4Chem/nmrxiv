<!--
  Project Card Component
  
  A versatile project display component that supports multiple layout modes (mini, grid, list).
  Features project image, title, description, tags, owner information, statistics, and actions.
  Includes interactive upvote functionality and responsive design for different screen sizes.
-->
<template>
    <!-- Main card container -->
    <div>
        <!-- Project card wrapper with hover effects -->
        <div v-if="project" class="hover:shadow-lg">
            <!-- Grid/Mini Mode Layout -->
            <div v-if="mode == 'mini' || mode == 'grid'">
                <!-- Card container with rounded corners and shadow -->
                <div class="relative flex flex-col rounded-lg shadow-lg border border-gray-200">
                    <!-- Clickable project link area -->
                    <Link
                        :href="project.public_url"
                        class="block cursor-pointer"
                    >
                        <!-- Project image/header section -->
                        <div class="relative rounded-t-lg lg:h-36 xl:h-36">
                            <!-- Project photo (if available) -->
                            <img
                                v-if="
                                    project.photo_url && project.photo_url != ''
                                "
                                :src="project.photo_url"
                                alt=""
                                class="w-full h-full object-center rounded-t-lg object-cover"
                            />
                            <!-- Fallback pattern background (if no photo) -->
                            <div
                                v-else
                                class="flex-shrink-0 lg:h-36 xl:h-36 pattern-diagonal-lines pattern-gray-400 pattern-bg-white pattern-size-2 pattern-opacity-20"
                            />
                            
                            <!-- Overlay with upvote button (top-right corner) -->
                            <div class="absolute top-0 right-0">
                                <div class="p-2 flex items-center">
                                    <div class="flex-shrink-0">
                                        <!-- Upvote button group -->
                                        <span
                                            v-if="project.stats"
                                            class="relative z-0 inline-flex rounded-md"
                                        >
                                            <!-- Upvote button -->
                                            <button
                                                type="button"
                                                class="relative inline-flex items-center px-1 py-1 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50"
                                                @click="toggleUpVote()"
                                            >
                                                <!-- Upvote arrow icon -->
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
                                            <!-- Like count display -->
                                            <a
                                                class="-ml-px relative inline-flex items-center px-2 py-1 rounded-r-md border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                            >
                                                {{ project.stats.likes }}
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Link>

                    <!-- Project content section -->
                    <div
                        :class="[
                            mode != 'mini' ? '' : 'rounded-b-lg',
                            'flex-1 bg-white flex flex-col justify-between',
                        ]"
                    >
                        <!-- Main content area with fixed height for consistent layout -->
                        <div style="min-height: 168px" class="flex-1 p-3 border-t border-gray-200 cursor-pointer">
                            <!-- Project identifier (if available) -->
                            <small
                                v-if="project.identifier"
                                class="text-gray-500"
                            >
                                #{{ project.identifier }}
                            </small>
                            
                            <!-- Clickable content area -->
                            <Link
                                :href="project.public_url"
                                class="block cursor-pointer"
                            >
                                <!-- Project title with line clamping -->
                                <p class="text-lg h-14 font-black text-gray-900 line-clamp-2">
                                    {{ project.name }}
                                </p>
                                
                                <!-- Project description with line clamping -->
                                <p class="text-xs text-gray-500 line-clamp-3">
                                    {{ project.description }}
                                </p>
                                
                                <!-- Project tags section with overflow handling -->
                                <div class="mt-1 h-14 overflow-hidden">
                                    <span class="px-2 py-1">
                                    <Tag :tags="project.tags" size="sm" />
                                    </span>
                                </div>
                            </Link>
                        </div>
                    </div>
                    <!-- Owner information footer (shown in grid mode, not mini) -->
                    <div
                        v-if="mode != 'mini'"
                        class="p-3 rounded-b-lg bg-white border-t flex"
                    >
                        <!-- Owner profile photo -->
                        <div class="flex-0.5 self-center align-middle">
                            <img
                                class="h-7 w-7 rounded-full"
                                :src="project.owner.profile_photo_url"
                            />
                        </div>
                        
                        <!-- Owner name and creation date -->
                        <div class="flex-auto pl-4 text-xs font-xs font-semibold text-black">
                            <!-- Owner full name -->
                            <p class="text-ellipsis overflow-hidden ...">
                                {{ project.owner.first_name }}
                                {{ project.owner.last_name }}
                            </p>
                            <!-- Project creation date -->
                            <div class="flex-1 space-x-1 text-xs font-xs text-gray-500">
                                <time datetime="2020-03-16">
                                    {{ formatDate(project.created_at) }}
                                </time>
                            </div>
                        </div>
                        
                        <!-- Actions menu -->
                        <div class="flex-0.5 self-center">
                            <Menu as="div" class="relative text-left">
                                <!-- Menu trigger button -->
                                <div>
                                    <MenuButton
                                        class="bg-white rounded-full flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500"
                                    >
                                        <span class="sr-only">Open options</span>
                                        <EllipsisVerticalIcon
                                            class="h-5 w-5"
                                            aria-hidden="true"
                                        />
                                    </MenuButton>
                                </div>
                                
                                <!-- Menu dropdown with transitions -->
                                <transition
                                    enter-active-class="transition ease-out duration-100"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95"
                                >
                                    <!-- Menu items container -->
                                    <MenuItems
                                        class="absolute right-0 bottom-full mb-2 w-64 z-[9999] rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                                    >
                                        <div>
                                            <!-- Download option (if available) -->
                                            <MenuItem
                                                v-if="project.download_url"
                                                v-slot="{ active }"
                                                class="border-b"
                                            >
                                                <a
                                                    :href="project.download_url"
                                                    :class="[
                                                        active
                                                            ? 'bg-gray-100 text-gray-600'
                                                            : 'text-gray-700',
                                                        'block px-4 py-4 text-sm cursor-pointer hover:text-gray-900',
                                                    ]"
                                                >
                                                    <ArrowDownTrayIcon
                                                        class="h-5 w-5 inline"
                                                        aria-hidden="true"
                                                    />
                                                    Download
                                                </a>
                                            </MenuItem>
                                            
                                            <!-- License information -->
                                            <MenuItem>
                                                <div class="px-4 py-2">
                                                    <p class="pb-2">
                                                        <small class="text-gray-500">License</small><br />
                                                        <span class="mt-2 text-xs text-gray-900 break-words">
                                                            {{ project.license.title }}
                                                        </span>
                                                    </p>
                                                </div>
                                            </MenuItem>
                                        </div>
                                    </MenuItems>
                                </transition>
                            </Menu>
                        </div>
                    </div>
                </div>
            </div>
            <!-- List Mode Layout -->
            <div v-if="mode == 'list'">
                <!-- List item container -->
                <li class="flex border-b bprder-gray-100">
                    <!-- Project image section (left side) -->
                    <div class="flex-shrink-0">
                        <!-- Project photo (if available) -->
                        <img
                            v-if="project.photo_url && project.photo_url != ''"
                            :src="project.photo_url"
                            alt=""
                            class="border w-36 lg:h-36 xl:h-36 m-2 mr-0 h-full object-center object-cover"
                        />
                        <!-- Fallback pattern background (if no photo) -->
                        <div
                            v-else
                            class="w-36 m-2 flex-shrink-0 border mr-0 lg:h-36 xl:h-36 pattern-diagonal-lines pattern-gray-400 pattern-bg-white pattern-size-2 pattern-opacity-20 border-b border-gray-400"
                        />
                    </div>

                    <div
                        class="flex-1 flex flex-col px-4 py-2 sm:px-6 justify-between"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <small
                                    v-if="project.identifier"
                                    class="text-gray-500"
                                    >#{{ project.identifier }}</small
                                >
                                <p
                                    class="text-lg font-black text-gray-900 line-clamp-2 font-black"
                                >
                                    <Link
                                        :href="project.public_url"
                                        class="block cursor-pointer"
                                    >
                                        {{ project.name }}
                                    </Link>
                                </p>
                                <p
                                    class="my-2 text-sm text-gray-500 line-clamp-2 pr-8"
                                >
                                    {{ project.description }}
                                </p>
                            </div>
                            <div class="ml-2 flex-shrink-0 flex">
                                <div>
                                    <div class="p-2 flex items-center">
                                        <div class="flex-shrink-0">
                                            <span
                                                v-if="project.stats"
                                                class="relative z-0 inline-flex shadow-sm rounded-md"
                                            >
                                                <button
                                                    type="button"
                                                    class="relative inline-flex items-center px-1 py-1 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50"
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
                                                    class="-ml-px relative inline-flex items-center px-2 py-1 rounded-r-md border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                                >
                                                    {{ project.stats.likes }}
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p
                                    class="flex items-center text-sm text-gray-500"
                                >
                                    <img
                                        class="h-7 w-5 mr-2 rounded-full"
                                        :src="project.owner.profile_photo_url"
                                    />
                                    {{ project.owner.first_name }}
                                    {{ project.owner.last_name }}
                                </p>
                                <p
                                    class="flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-3"
                                >
                                    <ScaleIcon
                                        class="text-gray-400 h-5 w-5 mr-1.5"
                                    ></ScaleIcon>
                                    {{ project.license.title }}
                                </p>
                                <p
                                    v-if="project.download_url"
                                    class="flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-2"
                                >
                                    <a
                                        :href="project.download_url"
                                        class="block px-4 text-sm cursor-pointer hover:text-gray-900', ]"
                                    >
                                        <ArrowDownTrayIcon
                                            class="h-5 w-5 inline"
                                            aria-hidden="true"
                                    /></a>
                                </p>
                            </div>
                            <div
                                class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0"
                            >
                                <svg
                                    class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <p>
                                    <time datetime="2020-03-16"
                                        >{{ formatDate(project.created_at) }}
                                    </time>
                                </p>
                            </div>
                        </div>
                    </div>
                </li>
            </div>
        </div>
    </div>
</template>

<script>
/**
 * Project Card Component
 * 
 * A versatile project display component that supports multiple layout modes:
 * - 'mini': Compact card with minimal information
 * - 'grid': Full card with owner info and actions menu
 * - 'list': Horizontal layout for list views
 * 
 * Features:
 * - Responsive design with different layouts for different screen sizes
 * - Interactive upvote functionality with authentication checks
 * - Project image display with fallback pattern
 * - Owner information and creation date
 * - Actions menu with download and license information
 * - Tag display with overflow handling
 * - Consistent styling and hover effects
 */

// Icon imports from Heroicons
import {
    EllipsisVerticalIcon,    // Three dots menu icon
    ScaleIcon,               // License/legal icon
    ArrowDownTrayIcon,       // Download icon
} from "@heroicons/vue/24/solid";

// HeadlessUI components for dropdown menu
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";

// Inertia.js imports for navigation and routing
import { router } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import Tag from "@/Shared/Tag.vue";

export default {
    name: "ProjectCard",

    /**
     * Component dependencies
     */
    components: {
        Link,                   // Inertia.js Link component for navigation
        EllipsisVerticalIcon,   // Menu trigger icon
        Menu,                   // HeadlessUI Menu container
        MenuButton,             // HeadlessUI Menu button
        MenuItem,               // HeadlessUI Menu item
        MenuItems,              // HeadlessUI Menu items container
        ArrowDownTrayIcon,      // Download icon
        ScaleIcon,              // License icon
    Tag,                    // Reusable tag component
    },

    /**
     * Component props
     * @prop {Object} project - Project data object containing all project information
     * @prop {String} mode - Display mode: 'mini', 'grid', or 'list'
     */
    props: ["project", "mode"],

    /**
     * Component methods
     */
    methods: {
        /**
         * Toggle the upvote/like status for the project
         * 
         * Handles user interaction with the upvote button by:
         * - Checking user authentication status
         * - Making API call to toggle upvote status
         * - Reloading projects data to reflect changes
         * - Redirecting to login if user is not authenticated
         * 
         * Includes error handling for API failures and uses Inertia's
         * selective reloading for better performance.
         */
        toggleUpVote() {
            // Check if user is authenticated
            if (
                this.$page.props.auth.username &&
                this.$page.props.auth.username != ""
            ) {
                // Construct API endpoint URL
                const url = "/projects/" + this.project.id + "/toggleUpVote";
                
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
                        // Reload only projects data to reflect changes
                        router.reload({ only: ["projects"] });
                    });
            } else {
                // Redirect to login if user is not authenticated
                this.$inertia.visit(route("login"));
            }
        },

        /**
         * Format date for display in project metadata
         * 
         * Converts ISO date strings to human-readable format
         * suitable for displaying creation dates.
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
