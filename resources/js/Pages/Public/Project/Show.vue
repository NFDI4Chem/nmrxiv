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
                        <div class="sm:col-span-6">
                            <p
                                style="max-width: 100ch !important"
                                class="prose mt-1 text-sm text-blue-gray-500"
                                v-html="md(project.data.description)"
                            ></p>
                        </div>
                        <div>
                            <a
                                v-for="tag in project.data.tags"
                                :key="tag.id"
                                target="_blank"
                                :href="'/projects?tag=' + tag.name.en"
                                class="mr-1 float rounded-full border border-gray-200 items-center py-1.5 pl-3 pr-3 text-sm font-medium bg-white text-gray-900 hover:text-white hover:bg-black"
                                ><span>{{ tag.name.en }}</span></a
                            >
                        </div>
                    </div>

                    <div class="pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                        <div class="sm:col-span-6">
                            <h2 class="text-xl font-medium text-blue-gray-900">
                                Submitter(s)
                            </h2>
                        </div>
                        <div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div
                                v-for="user in project.data.users"
                                :key="user.email"
                                class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"
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
                                        <!-- <p class="text-sm text-gray-500 truncate">Co-Founder / CEO</p> -->
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                        <div class="sm:col-span-6">
                            <h2 class="text-xl font-medium text-blue-gray-900">
                                Author(s)
                            </h2>
                            <!-- <p class="mt-1 text-sm text-blue-gray-500">
                      This information will be displayed publicly so be careful what you
                      share.
                    </p> -->
                        </div>
                        <div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div
                                v-for="author in project.data.authors"
                                :key="author.id"
                                class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"
                            >
                                <div class="flex-1 min-w-0">
                                    <a class="focus:outline-none">
                                        <span
                                            class="absolute inset-0"
                                            aria-hidden="true"
                                        ></span>
                                        <p
                                            v-if="author.title"
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{
                                                author.title +
                                                " " +
                                                author.family_name +
                                                " " +
                                                author.given_name
                                            }}
                                        </p>
                                        <p
                                            v-else
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{
                                                author.family_name +
                                                " " +
                                                author.given_name
                                            }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ author.affiliation }}
                                            <br />
                                            <a
                                                v-if="author.orcid_id"
                                                :href="author.orcid_id"
                                                class="text-teal-500"
                                                >ORCID ID -
                                                {{ author.orcid_id }}</a
                                            >
                                        </p>
                                    </a>
                                </div>
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

export default {
    components: {
        ProjectLayout,
    },
    props: ["project", "tab"],
    data() {
        return {};
    },
    computed: {},
    mounted() {},
    methods: {},
};
</script>
