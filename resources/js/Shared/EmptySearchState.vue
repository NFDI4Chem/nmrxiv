<template>
    <div :class="rootClasses" role="status" aria-live="polite">
        <div
            class="pointer-events-none absolute inset-0 opacity-60 dark:opacity-40"
            aria-hidden="true"
        >
            <div
                class="absolute -top-16 left-1/2 h-40 w-40 -translate-x-1/2 rounded-full blur-3xl"
                :class="accent.blur"
            ></div>
            <div
                class="absolute -bottom-10 right-1/4 h-28 w-28 rounded-full blur-2xl"
                :class="accent.blurSecondary"
            ></div>
        </div>

        <div class="relative">
            <div
                class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl shadow-lg ring-1 ring-black/5 dark:ring-white/10"
                :class="accent.iconBg"
            >
                <component
                    :is="iconComponent"
                    class="h-8 w-8"
                    :class="accent.iconColor"
                    aria-hidden="true"
                />
            </div>

            <h3
                class="mt-6 text-lg font-semibold tracking-tight text-gray-900 dark:text-gray-100"
            >
                {{ displayTitle }}
            </h3>
            <p
                class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-gray-600 dark:text-gray-400"
            >
                {{ displayMessage }}
            </p>

            <div
                v-if="showClearButton || $slots.actions"
                class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row sm:gap-4"
            >
                <button
                    v-if="showClearButton"
                    type="button"
                    class="inline-flex items-center rounded-full border border-transparent bg-teal-700 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-teal-700/30 transition-all hover:bg-teal-600 hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 dark:focus:ring-offset-gray-900"
                    @click="$emit('clear-search')"
                >
                    Clear search
                </button>
                <slot name="actions" />
            </div>
        </div>
    </div>
</template>

<script>
import {
    BeakerIcon,
    DocumentTextIcon,
    FolderIcon,
    MagnifyingGlassIcon,
    RectangleStackIcon,
} from "@heroicons/vue/24/outline";

const ENTITY_ACCENTS = {
    projects: {
        blur: "bg-teal-200/50 dark:bg-teal-500/20",
        blurSecondary: "bg-indigo-200/40 dark:bg-indigo-500/15",
        iconBg: "bg-gradient-to-br from-teal-500 to-teal-700",
        iconColor: "text-white",
    },
    samples: {
        blur: "bg-violet-200/50 dark:bg-violet-500/20",
        blurSecondary: "bg-teal-200/40 dark:bg-teal-500/15",
        iconBg: "bg-gradient-to-br from-violet-500 to-indigo-600",
        iconColor: "text-white",
    },
    compounds: {
        blur: "bg-violet-200/50 dark:bg-violet-500/20",
        blurSecondary: "bg-teal-200/40 dark:bg-teal-500/15",
        iconBg: "bg-gradient-to-br from-violet-500 to-indigo-600",
        iconColor: "text-white",
    },
    drafts: {
        blur: "bg-amber-200/50 dark:bg-amber-500/20",
        blurSecondary: "bg-orange-200/40 dark:bg-orange-500/15",
        iconBg: "bg-gradient-to-br from-amber-500 to-orange-600",
        iconColor: "text-white",
    },
    default: {
        blur: "bg-indigo-200/50 dark:bg-indigo-500/20",
        blurSecondary: "bg-gray-200/50 dark:bg-gray-600/20",
        iconBg: "bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700",
        iconColor: "text-gray-500 dark:text-gray-300",
    },
};

export default {
    name: "EmptySearchState",
    components: {
        BeakerIcon,
        DocumentTextIcon,
        FolderIcon,
        MagnifyingGlassIcon,
        RectangleStackIcon,
    },
    props: {
        /**
         * The type of entity being searched (e.g., 'projects', 'samples', 'drafts').
         * `samples` is labelled as compounds in copy (compound library terminology).
         */
        entityType: {
            type: String,
            required: true,
        },
        /**
         * The current search query or filter summary
         */
        searchQuery: {
            type: String,
            required: true,
        },
        /**
         * Custom title to override the default
         */
        title: {
            type: String,
            default: null,
        },
        /**
         * Custom message to override the default
         */
        message: {
            type: String,
            default: null,
        },
        /**
         * Whether to show the clear button
         */
        showClearButton: {
            type: Boolean,
            default: true,
        },
        /**
         * `public` — fixed max width for browse pages (projects, compounds).
         */
        layout: {
            type: String,
            default: "default",
            validator: (value) => ["default", "public"].includes(value),
        },
    },
    emits: ["clear-search"],
    computed: {
        rootClasses() {
            const base =
                "relative min-w-0 overflow-hidden rounded-2xl border border-gray-200/80 bg-gradient-to-b from-white via-white to-gray-50/90 px-6 py-16 text-center shadow-sm dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-gray-950/80";

            if (this.layout === "public") {
                return `${base} w-full max-w-2xl`;
            }

            return `${base} w-full`;
        },
        entityKey() {
            return String(this.entityType || "")
                .trim()
                .toLowerCase();
        },
        entityLabel() {
            const raw = this.entityKey;
            if (!raw) {
                return "results";
            }
            const plural = ["projects", "drafts"];
            if (plural.includes(raw)) {
                return raw;
            }
            if (raw === "samples") {
                return "compounds";
            }

            return raw.endsWith("s") ? raw : `${raw}s`;
        },
        isCatalogEmpty() {
            const hasCustomTitle =
                this.title != null && String(this.title).trim() !== "";
            const hasActiveSearch =
                String(this.searchQuery || "").trim() !== "";

            return hasCustomTitle && !hasActiveSearch;
        },
        accent() {
            if (!this.isCatalogEmpty) {
                return ENTITY_ACCENTS.default;
            }

            return ENTITY_ACCENTS[this.entityKey] ?? ENTITY_ACCENTS.default;
        },
        iconComponent() {
            if (!this.isCatalogEmpty) {
                return MagnifyingGlassIcon;
            }

            const icons = {
                projects: FolderIcon,
                drafts: DocumentTextIcon,
                samples: BeakerIcon,
                compounds: BeakerIcon,
            };

            return icons[this.entityKey] ?? RectangleStackIcon;
        },
        displayTitle() {
            if (this.title != null && String(this.title).trim() !== "") {
                return this.title;
            }

            return `No matching ${this.entityLabel}`;
        },
        displayMessage() {
            if (this.message != null && String(this.message).trim() !== "") {
                return this.message;
            }

            const q = String(this.searchQuery || "").trim();
            if (!q) {
                return `Adjust search keywords or filters to find ${this.entityLabel} in your workspace.`;
            }

            return `Nothing matched “${q}”. Try broader terms, or clear filters to see everything available.`;
        },
    },
};
</script>
