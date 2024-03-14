<template>
    <Head :title="project.data.name" />
    <project-layout :project="project" :selected-tab="tab">
        <template #project-content>
            <div
                class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-6"
            >
                <div
                    class="-mx-4"
                    v-if="project.data.is_public && project.data.doi != null"
                >
                    <Citation
                        :model="'project'"
                        :doi="project.data.doi"
                    ></Citation>
                    <ShowProjectDates
                        :release_date="project.data.release_date"
                        :created_at="project.data.created_at"
                    />
                </div>
                <div class="mt-6 space-y-4 divide-y divide-y-blue-gray-200">
                    <div>
                        <h3 class="text-xl font-extrabold text-blue-gray-900">
                            About project
                        </h3>
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
                    </div>

                    <div
                        v-if="
                            project.data.species &&
                            project.data.species.length > 0
                        "
                        class="pt-2"
                    >
                        <h3 class="text-xl font-extrabold text-blue-gray-900">
                            Organism
                        </h3>
                        <div class="pt-3">
                            <div
                                v-for="(species, $index) in project.data
                                    .species"
                                :key="$index"
                                class="bg-gray-100 text-gray-800 mb-0.5 inline-flex truncate break-words items-center px-3 py-2 rounded-full text-sm font-medium mr-1"
                            >
                                <i
                                    ><ontology-term-annotation
                                        :annotation="species"
                                    ></ontology-term-annotation
                                ></i>
                            </div>
                        </div>
                    </div>

                    <div class="gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                        <div class="pt-2 sm:col-span-6">
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
                            </div>
                            <div
                                class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2"
                            >
                                <author-card :authors="project.data.authors" />
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
                                </div>
                                <dd
                                    class="mt-2 text-md text-gray-900 space-y-5 focus:pointer-events-auto"
                                >
                                    <div
                                        class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2"
                                    >
                                        <citation-card
                                            :citations="project.data.citations"
                                        />
                                    </div>
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </project-layout>
    <component :is="'script'" type="application/ld+json">{{
        schema
    }}</component>
</template>

<script>
import ProjectLayout from "@/Pages/Public/Project/Layout.vue";
import AuthorCard from "@/Shared/AuthorCard.vue";
import CitationCard from "@/Shared/CitationCard.vue";
import "ontology-elements/dist/index.js";
import Citation from "@/Shared/Citation.vue";
import { Head } from "@inertiajs/vue3";
import ShowProjectDates from "@/Shared/ShowProjectDates.vue";

export default {
    components: {
        ProjectLayout,
        AuthorCard,
        CitationCard,
        Citation,
        Head,
        ShowProjectDates,
    },
    props: ["project", "tab"],
    data() {
        return {
            schema: {},
        };
    },
    computed: {},
    mounted() {
        axios
            .get(route("bioschemas.id", this.project.data.identifier))
            .then((response) => {
                this.schema = response.data;
            });
    },
    methods: {},
};
</script>
