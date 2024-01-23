<template>
    <div
        v-for="citation in citations"
        :key="citation.id"
        class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-top space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-teal-500"
    >
        <div class="flex-1 min-w-0">
            <a
                class="focus:outline-none cursor-pointer select-text"
                :href="getCitationLink(citation.doi)"
                :target="getTarget(citation.doi)"
            >
                <p class="text-sm font-medium text-gray-900">
                    {{ citation.title }}
                </p>
                <p class="text-sm text-teal-500">
                    {{ citation.authors }}
                </p>
                <p class="text-sm text-gray-500">
                    {{ citation.citation_text }}
                </p>
                <p v-if="citation.doi" class="text-sm font-sm text-teal-500">
                    DOI - {{ citation.doi }}
                </p>
            </a>
        </div>
    </div>
</template>

<script>
export default {
    components: {},
    props: ["citations"],
    methods: {
        getCitationLink(doi) {
            var link = "#";
            if (doi) {
                link = "https://doi.org/" + doi;
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
>
