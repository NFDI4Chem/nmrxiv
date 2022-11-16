<template>
    <project-layout :project="project" :selectedTab="tab">
        <template #project-content>
            <div
                class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
            >
                <h3 class="text-xl font-extrabold text-blue-gray-900">
                    About project
                </h3>
                <div class="mt-2 space-y-8 divide-y divide-y-blue-gray-200">
                    <div
                        class="grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6"
                    >
                        <div class="col-span-6">
                            <p
                                style="max-width: 100ch !important"
                                class="prose mt-1 text-sm text-blue-gray-500"
                                v-html="md(project.data.description)"
                            ></p>
                        </div>
                    </div>

                    <div class="pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                        <div class="sm:col-span-6">
                            <h2
                                class="text-xl font-extrabold mb-3 text-blue-gray-900"
                            >
                                Submitter(s)
                            </h2>
                        </div>
                        <div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div
                                v-for="user in project.data.users"
                                :key="user.email"
                                class="relative rounded-lg border border-gray-300 bg-white p-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"
                            >
                                <div class="flex-shrink-0">
                                    <img
                                        class="h-10 w-10 rounded-full"
                                        :src="user.profile_photo_url"
                                        alt=""
                                    />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <a class="focus:outline-none">
                                        <span
                                            class="absolute inset-0"
                                            aria-hidden="true"
                                        ></span>
                                        <p
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{
                                                user.first_name +
                                                " " +
                                                user.last_name
                                            }}
                                        </p>
                                        <p
                                            class="text-sm text-gray-500 truncate"
                                        >
                                            @ {{ user.username }}
                                        </p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="
                                project.data.authors &&
                                project.data.authors.length > 0
                            "
                            class="pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6"
                        >
                            <div class="sm:col-span-6">
                                <h2
                                    class="text-xl font-extrabold mb-3 text-blue-gray-900"
                                >
                                    Author(s)
                                </h2>
                                <!-- <p class="mt-1 text-sm text-blue-gray-500">
                      This information will be displayed publicly so be careful what you
                      share.
                    </p> -->
                            </div>
                            <div
                                class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2"
                            >
                                <author-card
                                    :authors="this.project.data.authors"
                                />
                            </div>
                            <div
                                v-if="
                                    project.data.citations &&
                                    project.data.citations.length > 0
                                "
                                class="pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6"
                            >
                                <div class="sm:col-span-6">
                                    <h2
                                        class="text-xl font-extrabold mb-3 text-blue-gray-900"
                                    >
                                        Citation(s)
                                    </h2>
                                    <!-- <p class="mt-1 text-sm text-blue-gray-500">
                      This information will be displayed publicly so be careful what you
                      share.
                    </p> -->
                                </div>
                                <dd
                                    class="mt-2 text-md text-gray-900 space-y-5 focus:pointer-events-auto"
                                >
                                    <div
                                        class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2"
                                    >
                                        <div
                                            v-for="citation in project.data
                                                .citations"
                                            :key="citation.id"
                                            class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-top space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-teal-500"
                                        >
                                            <div class="flex-1 min-w-0">
                                                <a
                                                    class="focus:outline-none cursor-pointer"
                                                    :href="
                                                        getCitationLink(
                                                            citation.doi
                                                        )
                                                    "
                                                    :target="
                                                        getTarget(citation.doi)
                                                    "
                                                >
                                                    <span
                                                        class="absolute inset-0"
                                                        aria-hidden="true"
                                                    ></span>
                                                    <p
                                                        class="text-sm font-medium text-gray-900"
                                                    >
                                                        {{ citation.title }}
                                                    </p>
                                                    <p
                                                        class="text-sm text-teal-500"
                                                    >
                                                        {{ citation.authors }}
                                                    </p>
                                                    <p
                                                        class="text-sm text-gray-500"
                                                    >
                                                        {{
                                                            citation.citation_text
                                                        }}
                                                    </p>
                                                    <p
                                                        v-if="citation.doi"
                                                        class="text-sm font-sm text-gray-500"
                                                    >
                                                        DOI -
                                                        <a
                                                            :href="citation.doi"
                                                            class="text-teal-900"
                                                            >{{
                                                                citation.doi
                                                            }}</a
                                                        >
                                                    </p>
                                                    <p
                                                        class="text-sm text-gray-500 truncate ..."
                                                    >
                                                        {{ citation.abstract }}
                                                        ...
                                                    </p>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </project-layout>
</template>

<script>
import ProjectLayout from "@/Pages/Public/Project/Layout.vue";
import AuthorCard from "@/Shared/AuthorCard.vue";

export default {
    components: {
        ProjectLayout,
        AuthorCard,
    },
    props: ["project", "tab"],
    data() {
        return {};
    },
    computed: {},
    mounted() {},
    methods: {
        getOrcidLink(orcidId) {
            var link = "#";
            if (orcidId) {
                link = "https://orcid.org/" + orcidId;
            }
            return link;
        },
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
