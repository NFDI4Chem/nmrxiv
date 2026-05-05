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
                                                            >Trash</a
                                                        >
                                                    </div>
                                                </li>
                                            </ol>
                                        </nav>
                                        <h2
                                            class="mt-2 text-2xl font-bold break-words leading-7 text-gray-900 sm:text-3xl"
                                        >
                                            Trash
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div v-if="projects.length > 0" class="px-12 py-8 mx-auto max-w-4xl">
            <team-projects
                :mode="'listing'"
                :projects="projects"
            ></team-projects>
        </div>
        <div v-else class="px-12 py-8 mx-auto max-w-4xl">
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
                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                    />
                </svg>
                <h3 class="mt-4 text-base font-semibold text-gray-900">
                    Trash is empty
                </h3>
                <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                    Deleted projects, studies, and datasets will appear here.
                </p>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import TeamProjects from "@/Pages/Project/Index.vue";

export default {
    components: {
        AppLayout,
        TeamProjects,
    },
    props: ["user", "projects"],
};
</script>
