<template>
    <app-layout :title="study.name">
        <template #header>
            <div
                v-if="study.is_deleted"
                class="text-center px-3 py-1 bg-red-50 text-red-700 border-b"
            >
                <b>Warning: </b> This study is deleted. At the end of the 30-day
                period, the study and all of its resources are deleted and
                cannot be recovered. You can restore a deleted study/project
                within the 30-day recovery period.
            </div>
            <div>
                <div
                    v-if="study.is_public && study.is_archived"
                    class="text-center px-3 py-2 bg-yellow-50 text-yellow-700 border-b"
                >
                    <b>Warning: </b> This project is archived. It is now
                    read-only.
                </div>
                <div
                    class="text-center px-3 py-2 bg-green-50 text-green-700 border-b"
                    v-if="study.is_public && !study.is_archived"
                >
                    <b>Info: </b> This project is published. You cannot edit a
                    published project, please create a new version to updated
                    the project.
                </div>
            </div>
            <div v-if="study.is_public && study.doi != null">
                <Citation :model="'study'" :doi="study.doi"></Citation>
            </div>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="flex flex-nowrap justify-between py-6">
                        <div
                            class="lg:flex lg:items-center lg:justify-between w-full"
                        >
                            <div class="flex-1 min-w-0">
                                <nav class="flex" aria-label="Breadcrumb">
                                    <ol
                                        role="list"
                                        class="flex items-center space-x-4"
                                    >
                                        <li>
                                            <div class="flex">
                                                <Link
                                                    :href="route('dashboard')"
                                                    class="text-sm font-medium text-gray-500 hover:text-gray-700"
                                                    >Dashboard</Link
                                                >
                                            </div>
                                        </li>
                                        <li>
                                            <div class="flex items-center">
                                                <ChevronRightIcon
                                                    class="flex-shrink-0 h-5 w-5 text-gray-400"
                                                    aria-hidden="true"
                                                />
                                                <Link
                                                    :href="
                                                        route(
                                                            'dashboard.projects',
                                                            [project.id]
                                                        )
                                                    "
                                                    class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                                                    >{{ project.name }}</Link
                                                >
                                            </div>
                                        </li>
                                    </ol>
                                </nav>
                                <div
                                    class="flex pr-20 mt-2 cursor-pointer items-center text-xl text-gray-700 font-bold"
                                >
                                    <StarIcon
                                        :class="[
                                            study.is_bookmarked
                                                ? 'text-yellow-400'
                                                : 'text-gray-200',
                                            'h-5 w-5 flex-shrink-0 -ml-1 mr-1',
                                        ]"
                                        aria-hidden="true"
                                        @click="toogleStarred"
                                    />
                                    {{ study.name }}
                                    <button
                                        v-if="canUpdateStudy"
                                        type="button"
                                        class="inline-flex items-center shadow-sm px-4 py-1.5 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                        @click="toggleDetails"
                                    >
                                        <PencilIcon
                                            class="w-4 h-4 mr-1 text-gray-600"
                                        />
                                        <span>Edit</span>
                                    </button>
                                </div>
                                <div
                                    class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6"
                                >
                                    <div
                                        class="mt-2 flex items-center text-sm text-gray-700"
                                    >
                                        <access-dialogue
                                            :available-roles="availableRoles"
                                            :role="studyRole"
                                            :team="team"
                                            :study="study"
                                            :members="members"
                                            :project="project"
                                            :model="model"
                                            called-from="studyView"
                                        />
                                        <ToolTip
                                            class="inline h-4 w-12 ml-0"
                                            text="The avatars (left) correspond to the users with whom the study is shared. Click there to edit sharing options."
                                        ></ToolTip>
                                        <a
                                            class="cursor-pointer inline-flex items-center"
                                            @click="toggleDetails"
                                        >
                                            <FlagIcon
                                                class="w-4 h-4 text-gray-500"
                                            />
                                            <span class="ml-2"
                                                >View details</span
                                            ></a
                                        >
                                        <div class="ml-3">
                                            <span
                                                v-if="study.is_public"
                                                class="inline-flex items-center"
                                            >
                                                <GlobeEuropeAfricaIcon
                                                    class="h-4 w-4 ml-4 text-gray-800"
                                                />
                                                <span class="ml-2">Public</span>
                                            </span>
                                            <span
                                                v-else
                                                class="inline-flex items-center"
                                            >
                                                <LockClosedIcon
                                                    class="h-4 w-4 text-gray-800"
                                                />
                                                <span class="ml-2"
                                                    >Private</span
                                                >
                                            </span>
                                        </div>
                                    </div>
                                    <div
                                        class="mt-2 flex items-center text-sm text-gray-500"
                                    >
                                        <span
                                            class="capitalize inline-flex pr-4 ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                        >
                                            <span
                                                v-if="studyRole == 'reviewer'"
                                            >
                                                <EyeIcon
                                                    class="h-7 w-7 py-1 text-gray-800"
                                                />
                                            </span>
                                            <span
                                                v-if="
                                                    studyRole == 'collaborator'
                                                "
                                            >
                                                <PencilSquareIcon
                                                    class="h-7 w-7 py-1 text-gray-800"
                                                />
                                            </span>
                                            <span
                                                v-if="
                                                    studyRole == 'owner' ||
                                                    studyRole == 'creator'
                                                "
                                            >
                                                <CubeTransparentIcon
                                                    class="h-7 w-7 py-1 text-gray-800"
                                                />
                                            </span>
                                            {{ studyRole }}
                                        </span>
                                        <span
                                            v-if="study.identifier"
                                            class="inline-flex pr-4 ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                        >
                                            <HashtagIcon class="h-6 w-6 py-1" />
                                            <b>{{ study.identifier }}</b>
                                        </span>
                                    </div>
                                    <study-details
                                        ref="studyDetailsElement"
                                        :study="study"
                                        :role="studyRole"
                                        :studyPermissions="studyPermissions"
                                    />
                                </div>
                                <div
                                    class="flex flex-nowrap justify-between pb-3"
                                >
                                    <div
                                        class="mt-2 flex items-center text-xs text-gray-400"
                                    >
                                        <CalendarIcon
                                            class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-300"
                                            aria-hidden="true"
                                        />
                                        Updated on
                                        {{ formatDateTime(study.updated_at) }}
                                    </div>
                                    <div>
                                        <span
                                            v-if="
                                                !study.is_public &&
                                                study.is_published
                                            "
                                            class="ml-4 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-red-800 capitalize"
                                        >
                                            PUBLISHED -&emsp;
                                            <b v-if="study.release_date"
                                                >Release date:
                                                {{
                                                    formatDate(
                                                        study.release_date
                                                    )
                                                }}</b
                                            >
                                            <b v-else
                                                >Release date:
                                                {{
                                                    formatDate(
                                                        project.release_date
                                                    )
                                                }}</b
                                            >
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div class="pb-12 pt-6 px-10">
            <slot name="scontent"></slot>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import StudyDetails from "./Partials/Details.vue";
import { ref } from "vue";
import {
    CalendarIcon,
    ChevronRightIcon,
    PencilIcon,
    FlagIcon,
    StarIcon,
    GlobeEuropeAfricaIcon,
    LockClosedIcon,
    HashtagIcon,
} from "@heroicons/vue/24/solid";
import {
    EyeIcon,
    PencilSquareIcon,
    CubeTransparentIcon,
} from "@heroicons/vue/24/outline";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import AccessDialogue from "@/Shared/AccessDialogue.vue";
import Citation from "@/Shared/Citation.vue";
import { Inertia } from "@inertiajs/inertia";
import ToolTip from "@/Shared/ToolTip.vue";
export default {
    components: {
        Link,
        AppLayout,
        StudyDetails,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        CalendarIcon,
        ChevronRightIcon,
        PencilIcon,
        StarIcon,
        AccessDialogue,
        Citation,
        ToolTip,
        FlagIcon,
        GlobeEuropeAfricaIcon,
        LockClosedIcon,
        EyeIcon,
        PencilSquareIcon,
        CubeTransparentIcon,
        HashtagIcon,
    },
    props: [
        "study",
        "project",
        "team",
        "members",
        "availableRoles",
        "studyPermissions",
        "studyRole",
        "model",
    ],
    setup() {
        const studyDetailsElement = ref(null);
        return {
            studyDetailsElement,
        };
    },

    computed: {
        canUpdateStudy() {
            return this.studyPermissions
                ? this.studyPermissions.canUpdateStudy
                : false;
        },
    },

    data() {
        return {};
    },
    methods: {
        toggleDetails() {
            this.studyDetailsElement.toggleDetails();
        },
        toogleStarred() {
            const url = "/studies/" + this.study.id + "/toggleStarred";
            axios
                .get(url)
                .catch((err) => {
                    if (
                        err.response.status !== 200 ||
                        err.response.status !== 201
                    ) {
                        throw new Error(
                            `API call failed with status code: ${err.response.status}`
                        );
                    }
                })
                .then(function (response) {
                    Inertia.reload({ only: ["study"] });
                });
        },
    },
};
</script>
