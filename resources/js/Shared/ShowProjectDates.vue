<template>
    <div v-if="segments.length > 0" class="min-w-0">
        <div
            v-if="variant === 'simple'"
            class="flex min-w-0 flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-600 dark:text-gray-400"
            role="group"
            aria-label="Publication and revision dates"
        >
            <CalendarDaysIcon
                class="h-3.5 w-3.5 shrink-0 text-gray-400 dark:text-gray-500"
                aria-hidden="true"
            />
            <div
                class="flex min-w-0 flex-wrap items-baseline gap-x-3 gap-y-1"
            >
                <template v-for="(seg, idx) in segments" :key="seg.label">
                    <span
                        v-if="idx > 0"
                        class="select-none text-gray-300 dark:text-gray-600"
                        aria-hidden="true"
                        >&nbsp;·&nbsp;</span
                    >
                    <span
                        class="inline-flex flex-wrap items-baseline gap-x-1.5"
                    >
                        <span
                            class="font-medium text-gray-500 dark:text-gray-500"
                            >{{ seg.label }}</span
                        >
                        <time
                            class="tabular-nums text-gray-800 dark:text-gray-200"
                            :datetime="isoDatetime(seg.value)"
                            >{{ formatRecordTimestamp(seg.value) }}</time
                        >
                    </span>
                </template>
            </div>
        </div>

        <div
            v-else
            :class="[
                'flex min-w-0 flex-col gap-2.5 text-xs text-gray-600 sm:flex-row sm:items-center sm:gap-3 dark:text-gray-400',
                topDivider &&
                    'border-t border-gray-200 pt-3 dark:border-gray-700 sm:pt-3.5',
            ]"
        >
            <div
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-gray-100 text-teal-600 ring-1 ring-inset ring-gray-200/60 dark:bg-gray-800 dark:text-teal-400 dark:ring-gray-700/80"
                aria-hidden="true"
            >
                <CalendarDaysIcon class="h-4 w-4" />
            </div>
            <div
                class="flex min-w-0 flex-1 flex-wrap items-center gap-2 sm:gap-2.5"
                role="group"
                aria-label="Publication and revision dates"
            >
                <div
                    v-for="seg in segments"
                    :key="seg.label"
                    class="inline-flex max-w-full min-w-0 flex-wrap items-baseline gap-x-1.5 gap-y-0.5 rounded-md border border-gray-200/80 bg-white px-2 py-1 shadow-sm dark:border-gray-700 dark:bg-gray-900/60"
                >
                    <span
                        class="shrink-0 text-[0.6875rem] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-500"
                        >{{ seg.label }}</span
                    >
                    <time
                        class="min-w-0 break-words text-xs font-medium tabular-nums text-gray-900 dark:text-gray-100"
                        :datetime="isoDatetime(seg.value)"
                        >{{ formatRecordTimestamp(seg.value) }}</time
                    >
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { CalendarDaysIcon } from "@heroicons/vue/24/solid";

export default {
    components: { CalendarDaysIcon },
    props: {
        release_date: {
            type: [String, Number, Date],
            default: null,
        },
        created_at: {
            type: [String, Number, Date],
            default: null,
        },
        updated_at: {
            type: [String, Number, Date],
            default: null,
        },
        topDivider: {
            type: Boolean,
            default: false,
        },
        variant: {
            type: String,
            default: "default",
            validator(value) {
                return ["default", "simple"].includes(value);
            },
        },
    },
    computed: {
        segments() {
            const rows = [];
            if (this.release_date) {
                rows.push({ label: "Published", value: this.release_date });
            }
            if (this.created_at) {
                rows.push({ label: "Created", value: this.created_at });
            }
            if (this.updated_at) {
                rows.push({ label: "Updated", value: this.updated_at });
            }
            return rows;
        },
    },
    methods: {
        isoDatetime(timestamp) {
            if (!timestamp) {
                return undefined;
            }
            const date = new Date(timestamp);
            if (Number.isNaN(date.getTime())) {
                return undefined;
            }
            return date.toISOString();
        },
    },
};
</script>
