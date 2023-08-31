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
                                                        <ChevronRightIcon
                                                            class="flex-shrink-0 h-4 w-4 text-gray-600"
                                                        />
                                                        <a
                                                            class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                                                            >Recent</a
                                                        >
                                                    </div>
                                                </li>
                                            </ol>
                                        </nav>
                                        <h2
                                            class="mt-2 text-2xl font-bold break-words leading-7 text-gray-900 sm:text-3xl"
                                        >
                                            Recent
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
                        <CubeIcon class="mx-auto h-24 w-24 text-gray-400" />

                        <h3 class="mt-2 text-sm font-medium text-gray-900">
                            No Projects Yet.
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            When you have projects associated with your account
                            they appear here in the order of the most recently
                            updated ones first.
                        </p>
                    </div></template
                >
            </team-projects>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import TeamProjects from "@/Pages/Project/Index.vue";
import { ChevronRightIcon, CubeIcon } from "@heroicons/vue/24/outline";
export default {
    components: {
        AppLayout,
        TeamProjects,
        ChevronRightIcon,
        CubeIcon,
    },
    props: ["user", "team", "projects"],
};
</script>
