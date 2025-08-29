<!--
  Study Card Public Component
  
  A modern card component for displaying study information in public project views.
  Features responsive design, image carousel, molecular structure fallback, and
  comprehensive study metadata display with experiment types and status indicators.
-->
<template>
    <!-- Main study card container with hover effects and transitions -->
    <div
        v-if="study"
        class="group relative bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-gray-300 transition-all duration-300 ease-in-out overflow-hidden"
    >
        <!-- Clickable card area linking to study details -->
        <Link :href="study.public_url" class="block">
            <!-- Preview image section with multiple display options -->
            <div class="relative bg-gray-50 overflow-hidden">
                <!-- Study preview images with carousel functionality -->
                <div v-if="study.study_preview_urls && study.study_preview_urls.length > 0" class="relative h-full">
                    <!-- Single preview image display -->
                    <div v-if="study.study_preview_urls.length === 1">
                        <img
                            :src="study.study_preview_urls[0]"
                            :alt="`Preview of ${study.name}`"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        />
                    </div>
                    
                    <!-- Multiple preview images with carousel -->
                    <div v-else class="relative h-full">
                        <!-- Individual carousel images -->
                        <div
                            v-for="(url, index) in study.study_preview_urls"
                            :key="url"
                            v-show="index === selectedPreviewIndex"
                            class="absolute inset-0"
                        >
                            <img
                                :src="url"
                                :alt="`Preview ${index + 1} of ${study.name}`"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                        </div>

                        <!-- Carousel navigation indicators -->
                        <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2">
                            <div class="flex space-x-2 bg-black/30 backdrop-blur-sm rounded-full px-3 py-2">
                                <!-- Individual indicator buttons -->
                                <button
                                    v-for="(url, index) in study.study_preview_urls"
                                    :key="index"
                                    :class="[
                                        'w-2 h-2 rounded-full transition-all duration-200',
                                        index === selectedPreviewIndex
                                            ? 'bg-white scale-110'
                                            : 'bg-white/60 hover:bg-white/80'
                                    ]"
                                    @click.prevent="selectedPreviewIndex = index"
                                    :aria-label="`View preview ${index + 1}`"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Molecular structure fallback (when no preview images available) -->
                <div v-else-if="molecules && molecules[0] && molecules[0].canonical_smiles && molecules[0].canonical_smiles.trim() !== ''" class="h-full flex items-center justify-center bg-white">
                    <Depictor2D
                        :molecule="molecules[0].canonical_smiles"
                        :show-download="false"
                        class="max-w-full max-h-full"
                    />
                </div>

                <!-- No preview available fallback -->
                <div v-else class="h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
                    <div class="text-center">
                        <!-- No preview icon -->
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 font-medium">No Preview</p>
                    </div>
                </div>

                <!-- Study identifier badge with privacy status -->
                <div v-if="study.identifier" class="absolute top-2 right-2 sm:top-3 sm:right-3">
                    <div class="inline-flex items-center px-2 py-1 sm:px-2.5 sm:py-1 rounded-full text-xs font-medium bg-black/30 text-white backdrop-blur-md">
                        <!-- Privacy status icons -->
                        <LockOpenIcon v-if="study.is_public" class="w-3 h-3 mr-1 sm:mr-1.5" />
                        <LockClosedIcon v-else class="w-3 h-3 mr-1 sm:mr-1.5" />
                        <!-- Responsive identifier display -->
                        <span class="hidden sm:inline">{{ cleanIdentifier }}</span>
                        <span class="sm:hidden">{{ cleanIdentifier.substring(0, 6) }}...</span>
                    </div>
                </div>
            </div>

            <!-- Study content and metadata section -->
            <div class="bg-white">
                <div class="p-3 sm:p-4 border-t border-gray-200 cursor-pointer">
                    <!-- Creation date -->
                    <small class="text-gray-500">
                        {{ formatDate(study.created_at) }}
                    </small>
                    
                    <!-- Study title with line clamping -->
                    <p class="text-base sm:text-lg font-black text-gray-900 line-clamp-2 overflow-hidden break-all">
                        {{ study.name }}
                    </p>
                    
                    <!-- Study description with responsive line clamping -->
                    <p class="text-xs sm:text-sm text-gray-500 line-clamp-2 break-all sm:line-clamp-2">
                        {{ study.description }}
                    </p>
                    
                    <!-- Experiment types tags with overflow handling -->
                    <div v-if="hasExperimentTypes" class="mt-1 h-12 sm:h-14 overflow-hidden">
                        <!-- Individual experiment type tags (max 3 shown) -->
                        <span
                            v-for="(type, index) in study.experiment_types?.slice(0, 3)"
                            :key="type"
                            class="mt-1 inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"
                        >
                            {{ type }}
                        </span>
                        
                        <!-- "More" indicator for additional experiment types -->
                        <span
                            v-if="study.experiment_types && study.experiment_types.length > 3"
                            class="mt-1 inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"
                        >
                            +{{ study.experiment_types.length - 3 }} more
                        </span>
                    </div>
                </div>
            </div>
        </Link>
    </div>
</template>

<script>
/**
 * Study Card Public Component
 * 
 * A sophisticated card component for displaying study information in public project views.
 * Features include:
 * - Multi-image carousel with navigation indicators
 * - Molecular structure visualization fallback using Depictor2D
 * - Responsive design with mobile-optimized layouts
 * - Study metadata display (title, description, experiment types)
 * - Privacy status indicators (public/private)
 * - Clean identifier formatting
 * - Hover effects and smooth transitions
 * 
 * The component intelligently handles different content types:
 * 1. Study preview images (with carousel for multiple images)
 * 2. Molecular structure diagrams (when no preview images available)
 * 3. Placeholder content (when no visual content is available)
 */

// Icon imports from Heroicons
import { LockClosedIcon } from "@heroicons/vue/24/solid";  // Private study indicator
import { LockOpenIcon } from "@heroicons/vue/24/solid";    // Public study indicator
import { PencilIcon } from "@heroicons/vue/24/solid";      // Edit icon (unused but imported)
import { EnvelopeIcon } from "@heroicons/vue/24/solid";    // Message icon (unused but imported)

// Navigation and component imports
import { Link } from "@inertiajs/vue3";                    // Inertia.js Link component
import Depictor2D from "@/Shared/Depictor2D.vue";         // Molecular structure visualization

export default {
    name: "StudyCardPublic",

    /**
     * Component dependencies
     */
    components: {
        LockClosedIcon,     // Private study lock icon
        LockOpenIcon,       // Public study unlock icon
        EnvelopeIcon,       // Message icon (unused)
        PencilIcon,         // Edit icon (unused)
        Link,               // Inertia.js navigation link
        Depictor2D,         // Molecular structure renderer
    },

    /**
     * Component props
     * @prop {Object} study - Study data object containing all study information
     * @prop {Object} project - Parent project data object (optional)
     */
    props: ["study", "project"],

    /**
     * Composition API setup (currently unused)
     */
    setup() {},

    /**
     * Component reactive data
     */
    data() {
        return {
            /**
             * Currently selected preview image index for carousel navigation
             * Used when study has multiple preview images
             */
            selectedPreviewIndex: 0,
        };
    },

    /**
     * Component methods (currently empty but available for future functionality)
     */
    methods: {
        /**
         * Format date for display in study metadata
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

    /**
     * Computed properties
     */
    computed: {
        /**
         * Get molecules data for molecular structure display
         * 
         * Attempts to retrieve molecular data from multiple possible locations:
         * 1. Direct study.molecules array
         * 2. study.sample.molecules array (nested structure)
         * 
         * @returns {Array} Array of molecule objects with canonical_smiles data
         */
        molecules() {
            // Check for direct molecules array on study
            if (this.study.molecules && Array.isArray(this.study.molecules)) {
                return this.study.molecules;
            }
            
            // Check for molecules in nested sample structure
            if (this.study.sample && this.study.sample.molecules && Array.isArray(this.study.sample.molecules)) {
                return this.study.sample.molecules;
            }
            
            // Return empty array if no molecules found
            return [];
        },

        /**
         * Check if study has valid experiment types to display
         * 
         * Validates that experiment_types exists, has content, and contains
         * non-empty strings to avoid displaying empty or invalid tags.
         * 
         * @returns {Boolean} True if study has displayable experiment types
         */
        hasExperimentTypes() {
            return this.study.experiment_types && 
                   this.study.experiment_types.length > 0 && 
                   this.study.experiment_types.some(type => type && type.trim() !== '');
        },

        /**
         * Get cleaned study identifier for display
         * 
         * Removes the "NMRXIV:" prefix from study identifiers to create
         * cleaner, more readable identifier badges.
         * 
         * @returns {String} Cleaned identifier string without prefix
         */
        cleanIdentifier() {
            if (!this.study.identifier) return '';
            return this.study.identifier.replace(/^NMRXIV:\s*/, '');
        },
    },
};
</script>
