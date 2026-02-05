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
                <template #emptyText>
                    <div class="text-center py-16">
                        <svg
                            class="mx-auto h-16 w-16 text-gray-300"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"
                            />
                        </svg>
                        <h3 class="mt-4 text-base font-semibold text-gray-900">
                            No starred items
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                            Star your important projects and datasets to find them quickly.
                        </p>
                    </div>
                </template>
            </team-projects>
        </div>
        <div
            v-if="studies.length > 0"
            class="px-12 border-t py-8 mx-auto max-w-4xl"
        >
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
import { Link } from "@inertiajs/vue3";
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
