<template>
    <div
        v-for="(citation, index) in citations"
        :key="citationKey(citation, index)"
        class="relative rounded-lg border border-gray-300 bg-white px-4 py-4 shadow-sm transition-all duration-200 hover:border-gray-400 hover:shadow-md focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500 sm:px-6 sm:py-5"
    >
        <div class="flex gap-3 sm:gap-4">
            <div class="min-w-0 flex-1 select-text">
                <h3 class="text-md mb-1 font-bold leading-5 text-gray-900">
                    {{ citation.title }}
                </h3>
                <div class="space-y-1">
                    <p class="text-sm font-medium text-gray-700">
                        {{ citation.authors }}
                    </p>
                    <p class="text-sm leading-5 text-gray-700">
                        {{ citation.citation_text }}
                    </p>
                    <p v-if="citation.doi" class="text-sm text-primary-600">
                        <span class="font-medium text-gray-500">DOI:</span>
                        {{ citation.doi }}
                    </p>
                </div>
            </div>
            <div
                v-if="hasToolbar(citation)"
                class="flex shrink-0 flex-col items-center gap-1 pl-2 sm:flex-row sm:items-start sm:gap-1.5 sm:pl-0"
                role="toolbar"
                :aria-label="
                    'Actions for citation: ' + (citation.title || 'citation')
                "
            >
                <a
                    v-if="showOpenLinkButton && citation.doi"
                    :href="getCitationLink(citation.doi)"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100 hover:text-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1"
                    title="Open DOI in new tab"
                >
                    <ArrowTopRightOnSquareIcon
                        class="h-5 w-5"
                        aria-hidden="true"
                    />
                    <span class="sr-only">Open DOI in new tab</span>
                </a>
                <button
                    v-if="showEditDelete"
                    type="button"
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1"
                    title="Edit citation"
                    @click="$emit('edit', citation)"
                >
                    <PencilIcon class="h-5 w-5" aria-hidden="true" />
                    <span class="sr-only">Edit citation</span>
                </button>
                <button
                    v-if="showEditDelete"
                    type="button"
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                    title="Delete citation"
                    @click="$emit('delete', citation)"
                >
                    <TrashIcon class="h-5 w-5" aria-hidden="true" />
                    <span class="sr-only">Delete citation</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { ArrowTopRightOnSquareIcon } from "@heroicons/vue/24/outline";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

export default {
    components: {
        ArrowTopRightOnSquareIcon,
        PencilIcon,
        TrashIcon,
    },
    props: {
        citations: {
            type: Array,
            default: () => [],
        },
        showEditDelete: {
            type: Boolean,
            default: false,
        },
        showOpenLinkButton: {
            type: Boolean,
            default: true,
        },
    },
    emits: ["edit", "delete"],
    methods: {
        citationKey(citation, index) {
            if (citation?.id != null) {
                return `citation-${citation.id}`;
            }
            if (citation?.doi) {
                return `doi-${citation.doi}`;
            }

            return `citation-idx-${index}`;
        },
        hasToolbar(citation) {
            return (
                this.showEditDelete ||
                (this.showOpenLinkButton && Boolean(citation?.doi))
            );
        },
        getCitationLink(doi) {
            if (doi) {
                return "https://doi.org/" + doi;
            }

            return "#";
        },
    },
};
</script>
