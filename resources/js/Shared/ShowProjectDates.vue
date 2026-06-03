<template>
    <div
        v-if="variant === 'simple' ? segments.length > 0 : hasDefaultContent"
        :class="[
            'flex min-w-0 flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-600 dark:text-gray-400',
            variant !== 'simple' &&
                topDivider &&
                'border-t border-gray-200 pt-3 dark:border-gray-700 sm:pt-3.5',
        ]"
        role="group"
        :aria-label="
            variant === 'simple'
                ? 'Publication and revision dates'
                : 'Project dates'
        "
    >
        <CalendarDaysIcon
            class="h-3.5 w-3.5 shrink-0 text-gray-400 dark:text-gray-500"
            aria-hidden="true"
        />

        <template v-if="variant === 'simple'">
            <div class="flex min-w-0 flex-wrap items-baseline gap-x-3 gap-y-1">
                <template v-for="(seg, idx) in segments" :key="seg.label">
                    <span
                        v-if="idx > 0"
                        class="select-none text-gray-300 dark:text-gray-600"
                        aria-hidden="true"
                        >&nbsp;·&nbsp;</span
                    >
                    <span
                        class="inline-flex max-w-full min-w-0 flex-wrap items-baseline gap-x-1.5"
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
        </template>

        <template v-else>
            <span
                v-if="showPublishedDate"
                class="inline-flex max-w-full min-w-0 flex-wrap items-baseline gap-x-1.5"
            >
                <span class="font-medium text-gray-500 dark:text-gray-500"
                    >Published</span
                >
                <time
                    class="tabular-nums text-gray-800 dark:text-gray-200"
                    :datetime="isoDatetime(release_date)"
                    >{{ formatRecordTimestamp(release_date) }}</time
                >
            </span>
            <span
                v-if="showPublishedDate && lastUpdatedValue"
                class="select-none text-gray-300 dark:text-gray-600"
                aria-hidden="true"
                >&nbsp;·&nbsp;</span
            >
            <span
                v-if="lastUpdatedValue"
                class="inline-flex max-w-full min-w-0 flex-wrap items-baseline gap-x-1.5"
            >
                <span class="font-medium text-gray-500 dark:text-gray-500"
                    >Last updated</span
                >
                <time
                    class="tabular-nums text-gray-800 dark:text-gray-200"
                    :datetime="isoDatetime(lastUpdatedValue)"
                    >{{ formatRecordTimestamp(lastUpdatedValue) }}</time
                >
            </span>
            <div v-if="showCreatedInfo" class="tooltip">
                <button
                    type="button"
                    class="rounded-full p-0.5 text-gray-400 transition-colors hover:bg-gray-200/80 hover:text-gray-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 dark:hover:bg-gray-700 dark:hover:text-gray-300"
                    :aria-label="createdTooltipLabel"
                    :title="createdTooltipLabel"
                >
                    <InformationCircleIcon
                        class="h-3.5 w-3.5"
                        aria-hidden="true"
                    />
                </button>
                <span
                    class="tooltiptextbottom z-10 whitespace-nowrap rounded-md bg-gray-900 px-2 py-1 text-center text-xs text-white shadow-lg dark:bg-gray-800"
                    role="tooltip"
                >
                    {{ createdTooltipLabel }}
                </span>
            </div>
        </template>
    </div>
</template>

<script>
import { CalendarDaysIcon } from "@heroicons/vue/24/solid";
import { InformationCircleIcon } from "@heroicons/vue/24/outline";

export default {
    components: { CalendarDaysIcon, InformationCircleIcon },
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
        is_published: {
            type: Boolean,
            default: false,
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
        hasDefaultContent() {
            return Boolean(this.showPublishedDate || this.lastUpdatedValue);
        },
        showPublishedDate() {
            return Boolean(this.release_date && this.is_published);
        },
        lastUpdatedValue() {
            return this.updated_at ?? this.created_at ?? null;
        },
        showCreatedInfo() {
            if (!this.created_at || !this.updated_at) {
                return false;
            }

            return (
                this.formatRecordTimestamp(this.created_at) !==
                this.formatRecordTimestamp(this.updated_at)
            );
        },
        createdTooltipLabel() {
            if (!this.created_at) {
                return "";
            }

            return `Created ${this.formatRecordTimestamp(this.created_at)}`;
        },
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
