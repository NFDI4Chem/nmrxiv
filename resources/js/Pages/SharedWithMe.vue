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
                                                            >Shared With Me</a
                                                        >
                                                    </div>
                                                </li>
                                            </ol>
                                        </nav>
                                        <h2
                                            class="mt-2 text-2xl font-bold break-words leading-7 text-gray-900 sm:text-3xl"
                                        >
                                            Shared With Me
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
                            No Projects Shared.
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            When projects are shared you will receive an email
                            with the invitation. Once accepted the project will
                            be added to your account. Team projects are
                            automatically added to the members.
                        </p>
                    </div></template
                >
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
import StudyCard from "@/Shared/StudyCard.vue";
import { Link } from "@inertiajs/inertia-vue3";
import { ChevronRightIcon, CubeIcon } from "@heroicons/vue/24/outline";
export default {
    components: {
        AppLayout,
        TeamProjects,
        StudyCard,
        Link,
        ChevronRightIcon,
        CubeIcon,
    },
    props: ["user", "team", "projects", "studies"],
};
</script>
