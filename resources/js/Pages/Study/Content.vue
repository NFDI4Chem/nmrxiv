<template>
    <div>
        <study-layout
            :model="model"
            :project="project"
            :study="study"
            :team="team"
            :members="members"
            :available-roles="availableRoles"
            :study-permissions="studyPermissions"
            :study-role="studyRole"
        >
            <template #scontent>
                <div class="bg-white shadow-md rounded-lg">
                    <div class="md:hidden">
                        <label for="tabs" class="sr-only">Select a tab</label>
                        <select
                            class="block w-full focus:ring-teal-500 focus:border-teal-500 border-gray-300 rounded-md"
                        >
                            <option
                                v-for="tab in subNavigation"
                                :key="tab.name"
                                :selected="tab.name === current"
                            >
                                {{ tab.name }}
                            </option>
                        </select>
                    </div>
                    <div class="hidden md:block">
                        <div
                            class="border-b border-t rounded-t-md border-gray-200 pl-4"
                        >
                            <nav
                                class="-mb-px flex space-x-8"
                                aria-label="Tabs"
                            >
                                <Link
                                    v-for="tab in subNavigation"
                                    :key="tab.name"
                                    :href="route(tab.route, [study.id])"
                                    :class="[
                                        tab.name == current
                                            ? 'border-teal-500 text-teal-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                        'group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm',
                                    ]"
                                    :aria-current="
                                        tab.name === current
                                            ? 'page'
                                            : undefined
                                    "
                                >
                                    <component
                                        :is="tab.icon"
                                        :class="[
                                            tab.name === current
                                                ? 'text-teal-500'
                                                : 'text-gray-400 group-hover:text-gray-500',
                                            '-ml-0.5 mr-2 h-5 w-5',
                                        ]"
                                        aria-hidden="true"
                                    />
                                    <span>{{ tab.name }}</span>
                                </Link>
                            </nav>
                        </div>
                    </div>
                    <slot name="study-section"></slot>
                </div>
            </template>
        </study-layout>
    </div>
</template>

<script>
import StudyLayout from "@/Pages/Study/Layout.vue";
import { Link } from "@inertiajs/vue3";
import {
    CircleStackIcon,
    FolderOpenIcon,
    Squares2X2Icon,
} from "@heroicons/vue/24/outline";
const subNavigation = [
    { name: "About", route: "dashboard.studies", icon: CircleStackIcon },
    {
        name: "Spectra",
        route: "dashboard.study.datasets",
        icon: Squares2X2Icon,
    },
    { name: "Files", route: "dashboard.study.files", icon: FolderOpenIcon },
];

export default {
    components: {
        StudyLayout,
        Link,
    },
    props: [
        "study",
        "project",
        "current",
        "team",
        "members",
        "availableRoles",
        "studyPermissions",
        "studyRole",
        "model",
    ],
    setup() {
        return {
            subNavigation,
        };
    },
};
</script>
