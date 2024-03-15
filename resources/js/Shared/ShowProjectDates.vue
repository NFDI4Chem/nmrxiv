<template>
    <div class="flex items-center text-sm text-gray-400">
        <CalendarDaysIcon
            class="flex-shrink-0 h-5 w-5 text-teal-500 mr-1"
            aria-hidden="true"
        />
        <p v-if="release_date" class="text-gray-400 font-bold mr-1">
            Published on:
        </p>
        <p v-if="release_date" class="text-gray-500">
            {{ formatDate(release_date) }} |
        </p>
        <p v-if="created_at" class="text-gray-400 font-bold ml-1.5 mr-1">
            Created on:
        </p>
        <p v-if="created_at" class="text-gray-500">
            {{ formatDate(created_at) }}
        </p>
        <p v-if="updated_at" class="text-gray-400 font-bold ml-1.5 mr-1">
            | Last updated:
        </p>
        <p v-if="updated_at" class="text-gray-500">
            {{ formatDateTime(updated_at) }}
        </p>
    </div>
</template>

<script>
import { CalendarDaysIcon } from "@heroicons/vue/24/solid";

export default {
    components: { CalendarDaysIcon },
    props: ["release_date", "created_at", "updated_at"],
};
</script>
