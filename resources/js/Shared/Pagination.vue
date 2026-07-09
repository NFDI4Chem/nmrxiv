<template>
    <nav
        v-if="safeLinks.length > 0"
        :aria-label="navigationLabel"
        class="flex flex-wrap items-center justify-between gap-3 text-sm font-medium text-gray-700"
    >
        <div class="flex shrink-0 items-center">
            <Link
                v-if="prevLink && prevLink.url"
                rel="prev"
                :href="prevLink.url"
                class="inline-flex h-10 items-center rounded-lg border border-gray-300 bg-white px-4 hover:bg-gray-100 focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 focus:ring-opacity-25 focus:ring-offset-1 focus:ring-offset-indigo-600"
            >
                Previous
            </Link>
            <span
                v-else
                class="inline-flex h-10 cursor-not-allowed items-center rounded-lg border border-gray-200 bg-gray-50 px-4 text-gray-400"
                aria-disabled="true"
            >
                Previous
            </span>
        </div>

        <div
            class="flex max-w-full min-w-0 flex-1 justify-center overflow-x-auto px-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden sm:overflow-visible"
            aria-label="Page numbers"
        >
            <div class="flex shrink-0 gap-2">
                <template v-for="(link, idx) in pageLinks" :key="'p-' + idx">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        :aria-current="link.active ? 'page' : undefined"
                        :class="[
                            link.active
                                ? 'border-indigo-600 ring-1 ring-indigo-600'
                                : '',
                            'inline-flex h-10 min-w-[2.5rem] shrink-0 items-center justify-center rounded-lg border border-gray-300 bg-white px-3 hover:bg-gray-100 focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 focus:ring-opacity-25 focus:ring-offset-1 focus:ring-offset-indigo-600',
                        ]"
                    >
                        <span v-html="link.label"></span>
                    </Link>
                    <span
                        v-else
                        class="inline-flex h-10 min-w-[2.5rem] shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 px-3 text-gray-500"
                        v-html="link.label"
                    ></span>
                </template>
            </div>
        </div>

        <div class="flex shrink-0 items-center justify-end">
            <Link
                v-if="nextLink && nextLink.url"
                rel="next"
                :href="nextLink.url"
                class="inline-flex h-10 items-center rounded-lg border border-gray-300 bg-white px-4 hover:bg-gray-100 focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 focus:ring-opacity-25 focus:ring-offset-1 focus:ring-offset-indigo-600"
            >
                Next
            </Link>
            <span
                v-else
                class="inline-flex h-10 cursor-not-allowed items-center rounded-lg border border-gray-200 bg-gray-50 px-4 text-gray-400"
                aria-disabled="true"
            >
                Next
            </span>
        </div>
    </nav>
</template>

<script>
import { Link } from "@inertiajs/vue3";

export default {
    components: {
        Link,
    },
    props: {
        links: {
            type: Array,
            default: () => [],
        },
        navigationLabel: {
            type: String,
            default: "Pagination",
        },
    },
    computed: {
        safeLinks() {
            return Array.isArray(this.links) ? this.links : [];
        },
        prevLink() {
            return this.safeLinks[0] ?? null;
        },
        nextLink() {
            const L = this.safeLinks;

            return L[L.length - 1] ?? null;
        },
        pageLinks() {
            const L = this.safeLinks;

            return L.length > 2 ? L.slice(1, -1) : [];
        },
    },
};
</script>
