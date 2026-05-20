<template>
    <div
        v-for="(author, index) in authors"
        :key="authorKey(author, index)"
        :class="cardRootClass"
    >
        <div class="flex gap-3 sm:gap-4">
            <div class="min-w-0 flex-1">
                <div class="mb-1 flex flex-wrap items-center gap-x-2 gap-y-1">
                    <h3 class="text-md font-bold leading-5 text-gray-900">
                        {{ author.title }}
                        {{ author.given_name }}
                        {{ author.family_name }}
                    </h3>
                    <button
                        v-if="enableRoleClick"
                        type="button"
                        class="group inline-flex shrink-0 items-center focus:outline-none"
                        title="Manage Role"
                        @click.stop.prevent="onRoleClick(author)"
                    >
                        <Tag
                            :label="
                                (author.pivot &&
                                    author.pivot.contributor_type) ||
                                author.contributor_type ||
                                'Researcher'
                            "
                            class="cursor-pointer transition group-hover:shadow group-active:scale-[0.97]"
                        />
                    </button>
                    <Tag
                        v-else
                        :label="
                            (author.pivot && author.pivot.contributor_type) ||
                            author.contributor_type ||
                            'Researcher'
                        "
                        class="shrink-0"
                    />
                </div>

                <div class="space-y-1">
                    <p v-if="author.affiliation" class="text-sm text-gray-500">
                        {{ author.affiliation }}
                    </p>
                    <p v-if="author.orcid_id" class="text-sm text-primary-600">
                        <span class="font-medium text-gray-500">ORCID iD:</span>
                        <a
                            v-if="getOrcidLink(author.orcid_id) !== '#'"
                            :href="getOrcidLink(author.orcid_id)"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="underline hover:text-primary-800"
                            @click.stop
                        >
                            {{ author.orcid_id }}
                        </a>
                        <template v-else>{{ author.orcid_id }}</template>
                    </p>
                    <p v-if="author.email_id" class="text-sm text-gray-500">
                        <span class="font-medium">Email:</span>
                        {{ author.email_id }}
                    </p>
                </div>
            </div>
            <div
                v-if="showEditDelete"
                class="flex shrink-0 flex-col items-center gap-1 pl-2 sm:flex-row sm:items-start sm:gap-1.5 sm:pl-0"
                role="toolbar"
                :aria-label="
                    'Actions for author: ' +
                    (author.family_name || author.given_name || 'author')
                "
            >
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1"
                    title="Edit author"
                    @click="$emit('edit', author)"
                >
                    <PencilIcon class="h-5 w-5" aria-hidden="true" />
                    <span class="sr-only">Edit author</span>
                </button>
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                    title="Delete author"
                    @click="$emit('delete', author)"
                >
                    <TrashIcon class="h-5 w-5" aria-hidden="true" />
                    <span class="sr-only">Delete author</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";
import Tag from "@/Shared/Tag.vue";

export default {
    components: { Tag, PencilIcon, TrashIcon },
    props: {
        authors: {
            type: Array,
            default: () => [],
        },
        enableRoleClick: { type: Boolean, default: false },
        showEditDelete: { type: Boolean, default: false },
        /** No outer border/shadow — parent provides a single frame (e.g. publish + drag handle). */
        flush: { type: Boolean, default: false },
    },
    emits: ["role-click", "edit", "delete"],
    computed: {
        cardRootClass() {
            if (this.flush) {
                return "select-text relative min-w-0 flex-1 px-4 py-4 sm:px-6 sm:py-5";
            }

            return "select-text relative rounded-lg border border-gray-300 bg-white px-4 py-4 shadow-sm transition-all duration-200 hover:border-gray-400 hover:shadow-md focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500 sm:px-6 sm:py-5";
        },
    },
    methods: {
        authorKey(author, index) {
            if (author?.id != null) {
                return `author-${author.id}`;
            }

            const gn = author?.given_name || "";
            const fn = author?.family_name || "";

            return `author-idx-${index}-${gn}-${fn}`;
        },
        getOrcidLink(orcidId) {
            if (orcidId) {
                return "https://orcid.org/" + orcidId;
            }

            return "#";
        },
        onRoleClick(author) {
            this.$emit("role-click", author);
        },
    },
};
</script>
