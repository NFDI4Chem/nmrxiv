<template>
    <div
        v-for="citation in citations"
        :key="citation.id"
        class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 hover:shadow-lg transition-all duration-200 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-teal-500"
    >
        <div class="flex-1 min-w-0">
            <a
                class="focus:outline-none cursor-pointer select-text block"
                :href="getCitationLink(citation.doi)"
                :target="getTarget(citation.doi)"
            >
                <h3 class="text-md font-bold text-gray-900 mb-1 leading-5">
                    {{ citation.title }}
                </h3>
                
                <div>
                    <p class="text-sm text-gray-700 font-medium">
                        {{ citation.authors }}
                    </p>
                    <p class="text-sm text-gray-700 leading-5">
                        {{ citation.citation_text }}
                    </p>
                    <p v-if="citation.doi" class="text-sm text-teal-600">
                        <span class="font-medium text-gray-500">DOI:</span>
                        {{ citation.doi }}
                    </p>
                </div>
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
