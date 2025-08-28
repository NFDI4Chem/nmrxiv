<template>
    <div class="flex flex-col sm:flex-row sm:items-center text-sm sm:text-base text-gray-500 space-y-1 sm:space-y-0 mt-5">
        <!-- Icon and title (mobile) -->
        <div class="flex items-center sm:hidden mb-1">
            <CalendarDaysIcon
                class="flex-shrink-0 h-4 w-4 text-teal-500 mr-2"
                aria-hidden="true"
            />
        </div>
        
        <!-- Desktop layout with icon -->
        <div class="hidden sm:flex sm:items-center">
            <CalendarDaysIcon
                class="flex-shrink-0 h-5 w-5 text-teal-500 mr-2"
                aria-hidden="true"
            />
        </div>
        
        <!-- Date information -->
        <div class="flex flex-col sm:flex-row sm:items-center space-y-1 sm:space-y-0 sm:space-x-4">
            <!-- Published date -->
            <div v-if="release_date" class="flex items-center">
                <span class="font-medium text-gray-600 mr-1.5">Published:</span>
                <span class="font-semibold text-gray-800">{{ formatDate(release_date) }}</span>
            </div>
            
            <!-- Separator for desktop -->
            <div v-if="release_date && (created_at || updated_at)" class="hidden sm:block text-gray-300">•</div>
            
            <!-- Created date -->
            <div v-if="created_at" class="flex items-center">
                <span class="font-medium text-gray-600 mr-1.5">Created:</span>
                <span class="font-semibold text-gray-800">{{ formatDate(created_at) }}</span>
            </div>
            
            <!-- Separator for desktop -->
            <div v-if="created_at && updated_at" class="hidden sm:block text-gray-300">•</div>
            
            <!-- Updated date -->
            <div v-if="updated_at" class="flex items-center">
                <span class="font-medium text-gray-600 mr-1.5">Updated:</span>
                <span class="font-semibold text-gray-800">{{ formatDateTime(updated_at) }}</span>
            </div>
        </div>
    </div>
</template>

<script>
import { CalendarDaysIcon } from "@heroicons/vue/24/solid";

export default {
    components: { CalendarDaysIcon },
    props: ["release_date", "created_at", "updated_at"],
};
</script>
