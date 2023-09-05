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
        <div v-if="projects.length > 0" class="px-12 py-8 mx-auto max-w-4xl">
            <team-projects
                :team="team"
                :team-role="teamRole"
                :mode="'create'"
                :projects="projects"
            ></team-projects>
        </div>
        <div v-else>
            <div class="max-w-lg my-6 py-6 mx-auto">
                <div class="text-center">
                    <FolderPlusIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900">
                        No datasets
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Get started by uploading a dataset.
                    </p>
                    <div class="mt-6">
                        <create mode="button"></create>
                        <span
                            class="float-center text-xs cursor-pointer hover:text-blue-700 mt-2"
                        >
                            <a
                                href="https://docs.nmrxiv.org/docs/submission-guides/submission/upload"
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
                                <MegaphoneIcon class="h-6 w-6 text-white" />
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900">
                                <a
                                    id="tour-step-submission-guide"
                                    href="https://docs.nmrxiv.org/docs/introduction/intro"
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
                            <ChevronRightIcon class="h-4 w-4 text-gray-500" />
                        </div>
                    </div>
                </li>

                <li>
                    <div class="relative group py-4 flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <span
                                class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-purple-500"
                            >
                                <CommandLineIcon class="h-6 w-6 text-white" />
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900">
                                <a
                                    id="tour-step-api"
                                    href="https://docs.nmrxiv.org/docs/developer-guides/API"
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
                            <ChevronRightIcon class="h-4 w-4 text-gray-500" />
                        </div>
                    </div>
                </li>

                <li>
                    <div class="relative group py-4 flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <span
                                class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-yellow-500"
                            >
                                <CalendarIcon class="h-6 w-6 text-white" />
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
                            <ChevronRightIcon class="h-4 w-4 text-gray-500" />
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
import Create from "@/Shared/CreateButton.vue";
import Onboarding from "@/App/Onboarding.vue";
import { useMagicKeys } from "@vueuse/core";
import { getCurrentInstance } from "vue";
import { watchEffect } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import { computed } from "vue";
import {
    FolderPlusIcon,
    MegaphoneIcon,
    ChevronRightIcon,
    CommandLineIcon,
    CalendarIcon,
} from "@heroicons/vue/24/outline";
const { meta, u } = useMagicKeys();

export default {
    components: {
        AppLayout,
        computed,
        TeamProjects,
        Create,
        Onboarding,
        Link,
        FolderPlusIcon,
        MegaphoneIcon,
        ChevronRightIcon,
        CommandLineIcon,
        CalendarIcon,
    },
    props: ["user", "team", "projects", "teamRole", "filters"],
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
    },
};
</script>
