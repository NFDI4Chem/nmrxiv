<template>
    <div
        v-for="author in authors"
        :key="author.id"
        class="select-text relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 hover:shadow-lg transition-all duration-200 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-teal-500"
    >
        <div class="flex-1 min-w-0">
            <a
                class="focus:outline-none cursor-pointer select-text block"
                :href="getOrcidLink(author.orcid_id)"
                :target="getTarget(author.orcid_id)"
            >
                <div class="flex items-start justify-between mb-1">
                    <h3 class="text-md font-bold text-gray-900">
                        {{ author.title }}
                        {{ author.given_name }}
                        {{ author.family_name }}
                    </h3>
                    <button
                        v-if="enableRoleClick"
                        type="button"
                        class="group inline-flex items-center focus:outline-none"
                        @click.stop.prevent="onRoleClick(author)"
                        title="Manage Role"
                    >
                        <Tag
                            :label="(author.pivot && author.pivot.contributor_type) || author.contributor_type || 'Researcher'"
                            class="cursor-pointer group-hover:shadow group-active:scale-[0.97] transition"
                        />
                    </button>
                    <Tag
                        v-else
                        :label="(author.pivot && author.pivot.contributor_type) || author.contributor_type || 'Researcher'"
                    />
                </div>
                
                <div class="space-y-1">
                    <p v-if="author.affiliation" class="text-sm text-gray-500">
                        {{ author.affiliation }}
                    </p>
                    <p v-if="author.orcid_id" class="text-sm text-teal-600">
                        <span class="font-medium text-gray-500">ORCID iD:</span>
                        {{ author.orcid_id }}
                    </p>
                    <p v-if="author.email_id" class="text-sm text-gray-500">
                        <span class="font-medium">Email:</span>
                        {{ author.email_id }}
                    </p>
                </div>
            </a>
        </div>
    </div>
</template>

<script>
import Tag from "@/Shared/Tag.vue";

export default {
    components: { Tag },
    props: {
        authors: { type: Array, required: true },
        enableRoleClick: { type: Boolean, default: false },
    },
    emits: ["role-click"],
    methods: {
        getOrcidLink(orcidId) {
            var link = "#";
            if (orcidId) {
                link = "https://orcid.org/" + orcidId;
            }
            return link;
        },
        getTarget(id) {
            var target = null;
            if (id) {
                target = "_blank";
            }
            return target;
        },
        onRoleClick(author) {
            this.$emit("role-click", author);
        },
    },
};
</script>
