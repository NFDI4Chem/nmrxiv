<template>
    <div
        v-for="author in authors"
        :key="author.id"
        class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-top space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-teal-500"
    >
        <div class="flex-1 min-w-0">
            <div>
                <a
                    class="focus:outline-none cursor-pointer"
                    :href="getOrcidLink(author.orcid_id)"
                    :target="getTarget(author.orcid_id)"
                >
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <div class="mb-1 flex items-center justify-between">
                        <p class="text-sm font-medium text-teal-900">
                            {{ author.title }}
                            {{ author.given_name }}
                            {{ author.family_name }}
                        </p>
                        <button
                            v-if="author.pivot && author.pivot.contributor_type"
                            class="text-sm text-gray-400"
                        >
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold capitalize rounded-full bg-green-100 text-green-800"
                            >
                                {{
                                    author.pivot.contributor_type
                                        ? author.pivot.contributor_type
                                        : "Researcher"
                                }}
                            </span>
                        </button>
                    </div>
                    <p v-if="author.affiliation" class="text-xs text-gray-500">
                        {{ author.affiliation }}
                    </p>
                    <p v-if="author.orcid_id" class="text-xs text-teal-900">
                        <b class="text-gray-500">ORCID iD:</b>
                        {{ author.orcid_id }}
                    </p>
                    <p v-if="author.email_id" class="text-xs text-gray-500">
                        <b class="text-gray-500">Email-Id:</b>
                        {{ author.email_id }}
                    </p>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    components: {},
    props: ["authors"],
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
    },
};
</script>
