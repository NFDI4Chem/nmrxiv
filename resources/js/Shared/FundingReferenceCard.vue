<template>
    <div
        v-for="(fundingReference, index) in fundingReferences"
        :key="fundingReferenceKey(fundingReference, index)"
        class="relative rounded-lg border border-gray-300 bg-white px-4 py-4 shadow-sm transition-all duration-200 hover:border-gray-400 hover:shadow-md focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500 sm:px-6 sm:py-5"
    >
        <div class="flex gap-3 sm:gap-4">
            <div class="min-w-0 flex-1 select-text">
                <h3 class="text-md mb-1 font-bold leading-5 text-gray-900">
                    {{ fundingReference.funder_name }}
                </h3>
                <div class="space-y-1">
                    <p
                        v-if="fundingReference.award_title"
                        class="text-sm font-medium text-gray-700"
                    >
                        {{ fundingReference.award_title }}
                    </p>
                    <p
                        v-if="fundingReference.award_number"
                        class="text-sm text-gray-700"
                    >
                        <span class="font-medium text-gray-500">Award:</span>
                        {{ fundingReference.award_number }}
                    </p>
                    <p
                        v-if="fundingReference.funder_identifier"
                        class="text-sm text-gray-700"
                    >
                        <span class="font-medium text-gray-500"
                            >Funder ID:</span
                        >
                        {{ fundingReference.funder_identifier }}
                        <span
                            v-if="fundingReference.funder_identifier_type"
                            class="text-gray-500"
                        >
                            ({{ fundingReference.funder_identifier_type }})
                        </span>
                    </p>
                    <p
                        v-if="fundingReference.award_uri"
                        class="text-sm text-primary-600"
                    >
                        <a
                            :href="fundingReference.award_uri"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="hover:underline"
                        >
                            {{ fundingReference.award_uri }}
                        </a>
                    </p>
                </div>
            </div>
            <div
                v-if="showEditDelete"
                class="flex shrink-0 flex-col items-center gap-1 pl-2 sm:flex-row sm:items-start sm:gap-1.5 sm:pl-0"
                role="toolbar"
                :aria-label="
                    'Actions for funding reference: ' +
                    (fundingReference.funder_name || 'funding')
                "
            >
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1"
                    title="Edit funding reference"
                    @click="$emit('edit', fundingReference)"
                >
                    <PencilIcon class="h-5 w-5" aria-hidden="true" />
                    <span class="sr-only">Edit funding reference</span>
                </button>
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                    title="Delete funding reference"
                    @click="$emit('delete', fundingReference)"
                >
                    <TrashIcon class="h-5 w-5" aria-hidden="true" />
                    <span class="sr-only">Delete funding reference</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

export default {
    components: {
        PencilIcon,
        TrashIcon,
    },
    props: {
        fundingReferences: {
            type: Array,
            default: () => [],
        },
        showEditDelete: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["edit", "delete"],
    methods: {
        fundingReferenceKey(fundingReference, index) {
            if (fundingReference?.id != null) {
                return `funding-${fundingReference.id}`;
            }

            return `funding-idx-${index}`;
        },
    },
};
</script>
