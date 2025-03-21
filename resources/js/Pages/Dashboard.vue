<template>
    <app-layout title="Dashboard">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="flex flex-nowrap justify-between py-6">
                        <div>
                            <div
                                class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"
                            >
                                <div v-if="team.personal_team">Your</div>
                                <div v-else>
                                    {{ user.current_team.name }}
                                </div>
                                &nbsp;Dashboard
                            </div>
                            <div
                                v-if="team.users"
                                class="flex mt-3 flex-row-reverse justify-end"
                            >
                                <img
                                    v-for="user in team.users"
                                    :key="user.id"
                                    class="w-8 h-8 -mr-2 rounded-full border-2 border-white"
                                    :src="user.profile_photo_url"
                                    :alt="user.name"
                                />
                                <img
                                    class="w-8 h-8 -mr-2 rounded-full border-2 border-white"
                                    :src="team.owner.profile_photo_url"
                                    :alt="team.owner.name"
                                />
                            </div>
                        </div>
                        <div v-if="!team.personal_team">
                            <Link
                                :href="'/teams/' + user.current_team.id"
                                class="text-sm text-gray-800 font-bold"
                            >
                                Team Settings
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div
            v-if="projects.length > 0 || samples.length > 0"
            class="px-8 py-8 mx-auto max-w-4xl"
        >
            <div>
                <div class="sm:hidden">
                    <label for="tabs" class="sr-only">Select a tab</label>
                    <!-- Use an "onChange" listener to redirect the user to the selected tab URL. -->
                    <select
                        id="tabs"
                        name="tabs"
                        class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option
                            :selected="selectedTab === 'projects'"
                            value="projects"
                        >
                            Projects
                        </option>
                        <option
                            :selected="selectedTab === 'samples'"
                            value="samples"
                        >
                            Samples
                        </option>
                    </select>
                </div>
                <div class="hidden sm:block">
                    <nav class="flex space-x-4" aria-label="Tabs">
                        <!-- Current: "bg-indigo-100 text-indigo-700", Default: "text-gray-500 hover:text-gray-700" -->
                        <a
                            href="?tab=projects"
                            :class="[
                                selectedTab == 'projects'
                                    ? 'bg-indigo-100 text-indigo-700'
                                    : '',
                                'rounded-md px-3 py-2 text-sm font-medium',
                            ]"
                            aria-current="page"
                            >Projects</a
                        >
                        <a
                            href="?tab=samples"
                            :class="[
                                selectedTab == 'samples'
                                    ? 'bg-indigo-100 text-indigo-700'
                                    : '',
                                'rounded-md px-3 py-2 text-sm font-medium',
                            ]"
                            >Samples</a
                        >
                    </nav>
                </div>
            </div>
            <div v-if="selectedTab == 'projects'">
                <team-projects
                    :team="team"
                    :team-role="teamRole"
                    :mode="'create'"
                    :projects="projects"
                ></team-projects>
            </div>
            <div v-if="selectedTab == 'samples'">
                <team-samples
                    :team="team"
                    :team-role="teamRole"
                    :mode="'create'"
                    :studies="samples"
                ></team-samples>
            </div>
        </div>
        <div v-else>
            <div class="max-w-lg my-6 py-6 mx-auto">
                <div class="text-center">
                    <svg
                        class="mx-auto h-12 w-12 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            vector-effect="non-scaling-stroke"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                        />
                    </svg>
                    <h3 class="mt-2 text-md font-medium text-gray-900">
                        You have no <b>projects</b> or <b>samples</b> yet
                    </h3>
                    <div v-if="editableTeamRole" class="mt-2">
                        <p class="mb-1 text-sm text-gray-500">
                            Get started by uploading your data.
                        </p>
                        <create class="mt-5" mode="button"></create>
                        <span
                            class="float-center text-xs cursor-pointer hover:text-blue-700 mt-2"
                        >
                            <a
                                href="https://docs.nmrxiv.org/submission-guides/submission-process.html"
                                target="_blank"
                                >Need Help?
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-12 py-8 mx-auto max-w-4xl">
            <ul
                role="list"
                class="mt-6 border-b border-gray-200 divide-y divide-gray-200"
            >
                <li>
                    <div class="relative group py-4 flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <span
                                class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-pink-500"
                            >
                                <!-- Heroicon name: outline/speakerphone -->
                                <svg
                                    class="h-6 w-6 text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                                    />
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900">
                                <a
                                    id="tour-step-submission-guide"
                                    href="https://docs.nmrxiv.org/introduction/intro"
                                    target="_blank"
                                >
                                    <span
                                        class="absolute inset-0"
                                        aria-hidden="true"
                                    ></span>
                                    Get started! How to use nmrXiv?
                                </a>
                            </div>
                            <p class="text-sm text-gray-500">
                                Documentation for using nmrXiv. Explore, learn
                                and archive NMR datasets.
                            </p>
                        </div>
                        <div class="flex-shrink-0 self-center">
                            <!-- Heroicon name: solid/chevron-right -->
                            <svg
                                class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="relative group py-4 flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <span
                                class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-purple-500"
                            >
                                <!-- Heroicon name: outline/terminal -->
                                <svg
                                    class="h-6 w-6 text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900">
                                <a
                                    id="tour-step-api"
                                    href="https://docs.nmrxiv.org/developer-guides/api.html"
                                    target="_blank"
                                >
                                    <span
                                        class="absolute inset-0"
                                        aria-hidden="true"
                                    ></span>
                                    Public API Documentation
                                </a>
                            </div>
                            <p class="text-sm text-gray-500">
                                Search, interact and download NMR Datasets as a
                                part of your software or your data science
                                workflow.
                            </p>
                        </div>
                        <div class="flex-shrink-0 self-center">
                            <!-- Heroicon name: solid/chevron-right -->
                            <svg
                                class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="relative group py-4 flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <span
                                class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-yellow-500"
                            >
                                <!-- Heroicon name: outline/calendar -->
                                <svg
                                    class="h-6 w-6 text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900">
                                <a id="tour-step-spectra-challenge" href="/">
                                    <span
                                        class="absolute inset-0"
                                        aria-hidden="true"
                                    ></span>
                                    Challenges
                                </a>
                            </div>
                            <p class="text-sm text-gray-500">
                                Structure elucidation challenges are designed
                                for researchers at all different stages of their
                                careers.
                            </p>
                        </div>
                        <div class="flex-shrink-0 self-center">
                            <!-- Heroicon name: solid/chevron-right -->
                            <svg
                                class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="mt-6 flex">
                <a
                    id="tour-step-get-in-touch"
                    :href="mailTo"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                    >Or get in touch<span aria-hidden="true"> &rarr;</span></a
                >
            </div>
        </div>
        <onboarding></onboarding>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import TeamProjects from "@/Pages/Project/Index.vue";
import TeamSamples from "@/Shared/Samples.vue";
import Create from "@/Shared/CreateButton.vue";
import Onboarding from "@/App/Onboarding.vue";
import { useMagicKeys } from "@vueuse/core";
import { getCurrentInstance } from "vue";
import { watchEffect } from "vue";
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const { meta, u } = useMagicKeys();

export default {
    components: {
        AppLayout,
        computed,
        TeamProjects,
        TeamSamples,
        Create,
        Onboarding,
        Link,
    },
    data() {
        return {
            selectedTab: "projects",
        };
    },
    props: ["user", "team", "projects", "samples", "teamRole", "filters"],
    setup() {
        const app = getCurrentInstance();
        const openDatasetCreateDialog = (data) => {
            app.appContext.config.globalProperties.emitter.emit(
                "openDatasetCreateDialog",
                data
            );
        };
        watchEffect(() => {
            if (meta.value && u.value) {
                openDatasetCreateDialog({
                    draft_id: null,
                });
            }
        });

        return {
            openDatasetCreateDialog,
        };
    },

    computed: {
        mailFromAddress() {
            return String(this.$page.props.mailFromAddress);
        },

        mailTo() {
            return "mailto:" + String(this.$page.props.mailFromAddress);
        },
    },

    mounted() {
        if (this.filters.action == "submission") {
            this.emitter.emit("openDatasetCreateDialog", {
                draft_id: this.filters.draft_id,
            });
        }

        const urlSearchParams = new URLSearchParams(window.location.search);
        const params = Object.fromEntries(urlSearchParams.entries());
        this.selectedTab = params["tab"] ? params["tab"] : "projects";
    },
};
</script>
