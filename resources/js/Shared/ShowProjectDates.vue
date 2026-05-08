<template>
    <div
        class="flex flex-col gap-2 text-xs sm:flex-row sm:items-center sm:gap-3"
    >
        <CalendarDaysIcon
            class="h-3.5 w-3.5 shrink-0 text-teal-500 sm:h-4 sm:w-4"
            aria-hidden="true"
        />
        <div
            class="flex min-w-0 flex-1 flex-col gap-2 text-gray-500 sm:flex-row sm:flex-wrap sm:items-baseline sm:gap-x-3 sm:gap-y-1"
        >
            <template v-if="release_date">
                <div class="flex flex-wrap items-baseline gap-x-1.5">
                    <span class="font-medium text-gray-500 dark:text-gray-400"
                        >Published</span
                    >
                    <span
                        class="tabular-nums text-gray-800 dark:text-gray-200"
                        >{{ formatRecordTimestamp(release_date) }}</span
                    >
                </div>
                <span
                    v-if="release_date && (created_at || updated_at)"
                    class="hidden text-gray-300 sm:inline dark:text-gray-600"
                    aria-hidden="true"
                    >·</span
                >
            </template>

            <template v-if="created_at">
                <div class="flex flex-wrap items-baseline gap-x-1.5">
                    <span class="font-medium text-gray-500 dark:text-gray-400"
                        >Created</span
                    >
                    <span
                        class="tabular-nums text-gray-800 dark:text-gray-200"
                        >{{ formatRecordTimestamp(created_at) }}</span
                    >
                </div>
                <span
                    v-if="created_at && updated_at"
                    class="hidden text-gray-300 sm:inline dark:text-gray-600"
                    aria-hidden="true"
                    >·</span
                >
            </template>

            <div
                v-if="updated_at"
                class="flex flex-wrap items-baseline gap-x-1.5"
            >
                <span class="font-medium text-gray-500 dark:text-gray-400"
                    >Updated</span
                >
                <span
                    class="tabular-nums text-gray-800 dark:text-gray-200"
                    >{{ formatRecordTimestamp(updated_at) }}</span
                >
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
