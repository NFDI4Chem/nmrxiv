<template>
    <div class="mx-auto mt-8 w-full max-w-2xl px-4 text-left">
        <div
            class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900"
        >
            <div
                class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800/80"
            >
                <div>
                    <h2
                        class="text-sm font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Processing logs
                    </h2>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                        <span class="capitalize">{{ currentStatus }}</span>
                        <span
                            v-if="logs.length"
                            class="before:mx-1.5 before:content-['·']"
                        >
                            {{ logs.length }}
                            {{ logs.length === 1 ? "entry" : "entries" }}
                        </span>
                    </p>
                </div>
                <span
                    v-if="isPolling"
                    class="inline-flex items-center gap-1.5 text-xs font-medium text-sky-700 dark:text-sky-300"
                >
                    <span
                        class="h-1.5 w-1.5 animate-pulse rounded-full bg-sky-500"
                        aria-hidden="true"
                    />
                    Live
                </span>
            </div>

            <div
                v-if="isStale"
                class="border-b border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-100"
                role="status"
            >
                No activity for
                {{ staleAfterMinutes }} minutes. Processing may be stuck. If
                this continues, contact
                <a
                    href="mailto:info.nmrxiv@uni-jena.de"
                    class="font-medium underline"
                    >info.nmrxiv@uni-jena.de</a
                >.
            </div>

            <div
                v-if="currentStatus === 'failed'"
                class="border-b border-red-200 bg-red-50 px-4 py-3 dark:border-red-900 dark:bg-red-950/40"
            >
                <p class="text-sm font-medium text-red-800 dark:text-red-200">
                    Processing failed
                </p>
                <p
                    v-if="latestErrorMessage"
                    class="mt-1 break-words text-sm text-red-700 dark:text-red-300"
                >
                    {{ latestErrorMessage }}
                </p>
                <button
                    type="button"
                    class="mt-3 inline-flex items-center rounded-md border border-transparent bg-red-700 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-red-600 dark:hover:bg-red-500"
                    :disabled="retrying"
                    @click="retry"
                >
                    {{ retrying ? "Working…" : retryLabel }}
                </button>
            </div>

            <div class="relative max-h-[50vh] overflow-y-auto">
                <ol
                    v-if="logs.length > 0"
                    role="log"
                    aria-live="polite"
                    class="divide-y divide-gray-100 dark:divide-gray-800"
                >
                    <li
                        v-for="(log, index) in logs"
                        :key="`${log.timestamp}-${index}`"
                        class="flex items-start gap-3 px-4 py-3 transition-colors"
                        :class="
                            index === logs.length - 1
                                ? 'bg-sky-50/70 dark:bg-sky-950/30'
                                : 'hover:bg-gray-50/80 dark:hover:bg-gray-800/50'
                        "
                    >
                        <span
                            class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full"
                            :class="logLevelMeta(log.level).iconWrapperClass"
                            aria-hidden="true"
                        >
                            <component
                                :is="logLevelMeta(log.level).iconComponent"
                                class="h-4 w-4"
                                :class="logLevelMeta(log.level).iconClass"
                            />
                        </span>
                        <div class="min-w-0 flex-1">
                            <div
                                class="flex flex-wrap items-center gap-x-2 gap-y-0.5"
                            >
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide"
                                    :class="logLevelMeta(log.level).badgeClass"
                                >
                                    {{ logLevelMeta(log.level).label }}
                                </span>
                                <span
                                    v-if="log.stage"
                                    class="rounded bg-gray-100 px-1.5 py-0.5 font-mono text-[10px] text-gray-600 dark:bg-gray-800 dark:text-gray-300"
                                >
                                    {{ log.stage }}
                                </span>
                                <time
                                    v-if="log.timestamp"
                                    :datetime="log.timestamp"
                                    class="font-mono text-[11px] text-gray-500 dark:text-gray-400"
                                >
                                    {{ formatDateTime(log.timestamp) }}
                                </time>
                            </div>
                            <p
                                class="mt-1 whitespace-pre-wrap break-words text-sm leading-relaxed text-gray-900 dark:text-gray-100"
                            >
                                {{ log.message }}
                            </p>
                            <details
                                v-if="
                                    log.context &&
                                    Object.keys(log.context).length > 0
                                "
                                class="mt-2 text-xs text-gray-600 dark:text-gray-400"
                            >
                                <summary
                                    class="inline-flex cursor-pointer select-none items-center gap-1 rounded text-gray-600 transition hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-400 focus-visible:ring-offset-1 dark:text-gray-400 dark:hover:text-gray-200"
                                >
                                    <span>Show details</span>
                                </summary>
                                <pre
                                    class="mt-2 max-h-64 overflow-auto whitespace-pre-wrap rounded-md border border-gray-200 bg-gray-50 p-3 font-mono text-[11px] leading-relaxed text-gray-800 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                                    >{{
                                        JSON.stringify(log.context, null, 2)
                                    }}</pre
                                >
                            </details>
                        </div>
                    </li>
                </ol>
                <div
                    v-else
                    class="flex flex-col items-center justify-center px-6 py-12 text-center"
                >
                    <InformationCircleIcon
                        class="mb-3 h-8 w-8 text-gray-400"
                        aria-hidden="true"
                    />
                    <h3
                        class="text-sm font-semibold text-gray-900 dark:text-gray-100"
                    >
                        No processing logs yet
                    </h3>
                    <p
                        class="mt-1 max-w-xs text-xs leading-relaxed text-gray-500 dark:text-gray-400"
                    >
                        Logs will appear here as your submission moves through
                        the publish queue.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { router } from "@inertiajs/vue3";
import {
    CheckCircleIcon,
    ExclamationCircleIcon,
    InformationCircleIcon,
} from "@heroicons/vue/24/outline";

const ACTIVE_STATUSES = ["queued", "processing"];
const TERMINAL_SUCCESS = ["published", "embargo", "complete"];
const POLL_INTERVAL_MS = 5000;

export default {
    name: "ProcessingLogs",

    components: {
        CheckCircleIcon,
        ExclamationCircleIcon,
        InformationCircleIcon,
    },

    props: {
        project: {
            type: Object,
            required: true,
        },
    },

    emits: ["status-changed"],

    data() {
        return {
            logs: Array.isArray(this.project?.process_logs)
                ? this.project.process_logs
                : [],
            currentStatus: this.project?.status || "draft",
            isStale: false,
            hasDraft:
                this.project?.draft_id != null || this.project?.draft != null,
            retrying: false,
            pollTimer: null,
            staleAfterMinutes: 30,
        };
    },

    computed: {
        isPolling() {
            return ACTIVE_STATUSES.includes(this.currentStatus);
        },
        retryLabel() {
            return this.hasDraft ? "Back to edit" : "Retry processing";
        },
        latestErrorMessage() {
            for (let i = this.logs.length - 1; i >= 0; i--) {
                const log = this.logs[i];
                if (
                    String(log.level || "").toUpperCase() === "ERROR" ||
                    log.stage === "failed"
                ) {
                    return log.message;
                }
            }
            return null;
        },
    },

    mounted() {
        this.fetchStatus();
        this.startPolling();
    },

    beforeUnmount() {
        this.stopPolling();
    },

    methods: {
        startPolling() {
            this.stopPolling();
            if (!this.isPolling) {
                return;
            }
            this.pollTimer = setInterval(() => {
                this.fetchStatus();
            }, POLL_INTERVAL_MS);
        },

        stopPolling() {
            if (this.pollTimer) {
                clearInterval(this.pollTimer);
                this.pollTimer = null;
            }
        },

        fetchStatus() {
            if (!this.project?.id) {
                return;
            }

            axios
                .get(route("project.status", this.project.id))
                .then((response) => {
                    const data = response.data || {};
                    const previousStatus = this.currentStatus;
                    this.currentStatus = data.status || this.currentStatus;
                    this.logs = Array.isArray(data.logs) ? data.logs : [];
                    this.isStale = Boolean(data.is_stale);
                    if (typeof data.has_draft === "boolean") {
                        this.hasDraft = data.has_draft;
                    }

                    if (previousStatus !== this.currentStatus) {
                        this.$emit("status-changed", this.currentStatus);
                    }

                    if (ACTIVE_STATUSES.includes(this.currentStatus)) {
                        if (!this.pollTimer) {
                            this.startPolling();
                        }
                    } else {
                        this.stopPolling();
                        if (
                            TERMINAL_SUCCESS.includes(this.currentStatus) &&
                            previousStatus !== this.currentStatus
                        ) {
                            router.reload({ preserveScroll: true });
                        }
                    }
                })
                .catch(() => {
                    // Keep last known state; next poll will retry.
                });
        },

        retry() {
            if (this.retrying || !this.project?.id) {
                return;
            }

            this.retrying = true;
            router.post(
                route("project.publish.retry", this.project.id),
                {},
                {
                    preserveScroll: true,
                    onFinish: () => {
                        this.retrying = false;
                    },
                    onSuccess: () => {
                        this.fetchStatus();
                    },
                }
            );
        },

        formatDateTime(value) {
            if (!value) {
                return "";
            }
            try {
                return new Date(value).toLocaleString();
            } catch {
                return value;
            }
        },

        logLevelMeta(rawLevel) {
            const key = String(rawLevel || "info").toLowerCase();

            const presets = {
                error: {
                    label: "Error",
                    iconComponent: "ExclamationCircleIcon",
                    iconWrapperClass:
                        "bg-red-50 ring-1 ring-inset ring-red-200 dark:bg-red-950 dark:ring-red-800",
                    iconClass: "text-red-600 dark:text-red-400",
                    badgeClass:
                        "bg-red-50 text-red-700 ring-1 ring-inset ring-red-200 dark:bg-red-950 dark:text-red-300 dark:ring-red-800",
                },
                warning: {
                    label: "Warning",
                    iconComponent: "InformationCircleIcon",
                    iconWrapperClass:
                        "bg-amber-50 ring-1 ring-inset ring-amber-200 dark:bg-amber-950 dark:ring-amber-800",
                    iconClass: "text-amber-600 dark:text-amber-400",
                    badgeClass:
                        "bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-200 dark:bg-amber-950 dark:text-amber-200 dark:ring-amber-800",
                },
                warn: {
                    label: "Warning",
                    iconComponent: "InformationCircleIcon",
                    iconWrapperClass:
                        "bg-amber-50 ring-1 ring-inset ring-amber-200 dark:bg-amber-950 dark:ring-amber-800",
                    iconClass: "text-amber-600 dark:text-amber-400",
                    badgeClass:
                        "bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-200 dark:bg-amber-950 dark:text-amber-200 dark:ring-amber-800",
                },
                info: {
                    label: "Info",
                    iconComponent: "InformationCircleIcon",
                    iconWrapperClass:
                        "bg-sky-50 ring-1 ring-inset ring-sky-200 dark:bg-sky-950 dark:ring-sky-800",
                    iconClass: "text-sky-600 dark:text-sky-400",
                    badgeClass:
                        "bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-200 dark:bg-sky-950 dark:text-sky-300 dark:ring-sky-800",
                },
                success: {
                    label: "Success",
                    iconComponent: "CheckCircleIcon",
                    iconWrapperClass:
                        "bg-emerald-50 ring-1 ring-inset ring-emerald-200 dark:bg-emerald-950 dark:ring-emerald-800",
                    iconClass: "text-emerald-600 dark:text-emerald-400",
                    badgeClass:
                        "bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:ring-emerald-800",
                },
            };

            return {
                key,
                ...(presets[key] ?? presets.info),
            };
        },
    },
};
</script>
