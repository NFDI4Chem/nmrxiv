<template>
    <app-layout title="Dashboard">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="flex flex-nowrap justify-between py-6">
                        <div>
                            <div class="flex flex-nowrap justify-between">
                                <div
                                    class="lg:flex lg:items-center lg:justify-between"
                                >
                                    <div class="flex-1 min-w-0">
                                        <nav
                                            class="flex"
                                            aria-label="Breadcrumb"
                                        >
                                            <ol
                                                role="list"
                                                class="flex items-center space-x-4"
                                            >
                                                <li>
                                                    <div class="flex">
                                                        <a
                                                            class="text-sm font-medium text-gray-500 hover:text-gray-700"
                                                            >Dashboard</a
                                                        >
                                                    </div>
                                                </li>
                                                <li>
                                                    <div
                                                        class="flex items-center"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 20 20"
                                                            fill="currentColor"
                                                            aria-hidden="true"
                                                            class="flex-shrink-0 h-5 w-5 text-gray-400"
                                                        >
                                                            <path
                                                                fill-rule="evenodd"
                                                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                                clip-rule="evenodd"
                                                            ></path></svg
                                                        ><a
                                                            class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                                                            >Starred</a
                                                        >
                                                    </div>
                                                </li>
                                            </ol>
                                        </nav>
                                        <h2
                                            class="mt-2 text-2xl font-bold break-words leading-7 text-gray-900 sm:text-3xl"
                                        >
                                            Starred
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div class="px-12 py-8 mx-auto max-w-4xl">
            <team-projects :mode="'listing'" :projects="projects">
                <template #emptyText
                    ><div class="text-center py-12">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mx-auto h-24 w-24 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
                            />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">
                            No Starred Projects.
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Add stars to projects or datasets to find it easily
                            later.
                        </p>
                    </div></template
                >
            </team-projects>
        </div>
        <div class="px-12 border-t py-8 mx-auto max-w-4xl">
            <div>
                <h2 class="text-lg">Studies</h2>
            </div>
            <div class="flex items-baseline justify-between">
                <div class="py-8 mx-auto max-w-4xl">
                    <div
                        class="mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-3 lg:max-w-7xl"
                    >
                        <div v-for="study in studies" :key="study.uuid">
                            <Link
                                :href="route('dashboard.studies', [study.id])"
                            >
                                <study-card :study="study" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import TeamProjects from "@/Pages/Project/Index.vue";
import { Link } from "@inertiajs/inertia-vue3";
import StudyCard from "@/Shared/StudyCard.vue";

export default {
    components: {
        AppLayout,
        TeamProjects,
        StudyCard,
        Link,
    },
    props: ["user", "team", "projects", "studies"],
};
</script>
