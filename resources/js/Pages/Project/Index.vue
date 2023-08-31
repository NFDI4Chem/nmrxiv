<template>
    <div>
        <div class="flex items-baseline justify-between">
            <div id="tour-step-projects">
                <h2 class="text-lg">Projects</h2>
                <!-- <div class="mt-2 text-sm text-gray-700"> -->
                <!-- <div class="max-w-2xl">You may house a variety of projects.</div> -->
                <!-- </div> -->
            </div>
            <div class="flex-shrink-0 ml-4">
                <!-- <button
                    v-if="mode == 'create' && editableTeamRole"
                    id="v-step-1"
                    type="button"
                    class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    @click="openProjectCreateDialog"
                >
                    New Project
                </button> -->
            </div>
        </div>
        <span v-if="projects.length <= 0">
            <div v-if="mode == 'create' && editableTeamRole" class="mt-4">
                <div class="px-6 py-4 bg-white shadow-md rounded-lg">
                    <div class="flex items-center">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            class="h-6 w-6"
                        >
                            <path
                                d="M3 6l9 4v12l-9-4V6zm14-3v2c0 1.1-2.24 2-5 2s-5-.9-5-2V3c0 1.1 2.24 2 5 2s5-.9 5-2z"
                                class="fill-current text-gray-400"
                            ></path>
                            <polygon
                                points="21 6 12 10 12 22 21 18"
                                class="fill-current text-gray-600"
                            ></polygon>
                        </svg>
                        <div
                            class="ml-3 font-semibold text-sm text-gray-600 uppercase tracking-wider"
                        >
                            Create Your First Project
                        </div>
                    </div>
                    <div class="mt-3 max-w-2xl text-sm text-gray-700">
                        nmrXiv is organized around projects. Projects can
                        contain as many studies as you wish and each study
                        receives its very own URL. To learn more, check out our
                        documentation.
                    </div>
                    <!-- <button
                        type="button"
                        class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 mt-6 focus:ring-offset-2 focus:ring-indigo-500"
                        @click="openProjectCreateDialog()"
                    >
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
                        <span
                            class="mt-2 block text-sm font-medium text-gray-900"
                        >
                            Create a new project
                        </span>
                    </button> -->
                </div>
            </div>
            <div v-else>
                <slot name="emptyText"></slot>
            </div>
        </span>
        <span v-else>
            <div
                v-for="project in projects"
                :key="project.uuid"
                class="mt-8 relative"
            >
                <div class="absolute rotate-90 -left-16 top-1/3">
                    <span
                        v-if="project.is_public"
                        class="inline-flex bg-gray-100 px-4 items-center border-b border-l border-r rounded-b-md"
                    >
                        <GlobeEuropeAfricaIcon
                            class="h-3 w-3 text-gray-800 inline"
                        />

                        <span class="ml-2">Public</span>
                    </span>
                    <span
                        v-else
                        class="inline-flex bg-red-100 px-4 items-center border-b border-l border-r rounded-b-md"
                    >
                        <LockClosedIcon class="h-3 w-3 text-gray-800 inline" />

                        <span class="ml-2">Private</span>
                    </span>
                </div>
                <a>
                    <div
                        class="flex justify-between items-center bg-white shadow-md border rounded-lg px-6 py-6"
                    >
                        <div
                            @click="getLink(project)"
                            class="flex-grow cursor-pointer"
                        >
                            <div class="flex justify-between items-baseline">
                                <div class="font-bold text-lg text-gray-600">
                                    <div class="flex items-center">
                                        <span
                                            class="cursor-pointer flex max-w-2xl break-words block"
                                        >
                                            <span class="-ml-2 mr-1">
                                                <StarIcon
                                                    :class="[
                                                        project.is_bookmarked
                                                            ? 'text-yellow-400'
                                                            : 'text-gray-200',
                                                        'h-6 w-6 flex-shrink-0 -ml-1 mr-1',
                                                    ]"
                                                ></StarIcon> </span
                                            >{{ project.name }}
                                            <span
                                                v-if="
                                                    project.draft_id != null &&
                                                    project.status == null
                                                "
                                                class="ml-4 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800"
                                            >
                                                Draft
                                            </span>
                                            <span
                                                v-if="
                                                    project.status != null &&
                                                    project.status != 'complete'
                                                "
                                                class="ml-4 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 capitalize"
                                            >
                                                {{ project.status }}
                                            </span>
                                            <span
                                                v-if="
                                                    project.is_deleted !=
                                                        null &&
                                                    project.is_deleted
                                                "
                                                class="ml-4 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800 capitalize"
                                            >
                                                DELETED
                                            </span>
                                            <span
                                                v-if="
                                                    project.is_archived !=
                                                        null &&
                                                    project.is_archived
                                                "
                                                class="ml-4 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-red-800 capitalize"
                                            >
                                                ARCHIVED
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-baseline mt-1">
                                <span class="text-sm text-gray-600">
                                    <div
                                        v-if="
                                            (team &&
                                                team.id != project.team_id) ||
                                            project.owner_id !=
                                                $page.props.user.id
                                        "
                                        class="text-sm text-gray-600"
                                    >
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                        >
                                            Shared by:
                                            {{
                                                project.owner
                                                    ? project.owner.first_name
                                                    : ""
                                            }}
                                            {{
                                                project.owner
                                                    ? project.owner.last_name
                                                    : ""
                                            }}</span
                                        >
                                    </div>
                                </span>
                            </div>
                            <div class="grid grid-cols-1">
                                <div>
                                    <dd
                                        class="text-xs text-gray-900 space-y-5 mt-1"
                                    >
                                        <p>
                                            <span
                                                v-for="tag in project.tags"
                                                :key="tag.id"
                                                class="mr-2"
                                            >
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800"
                                                >
                                                    <svg
                                                        class="-ml-0.5 mr-1.5 h-2 w-2 text-indigo-400"
                                                        fill="currentColor"
                                                        viewBox="0 0 8 8"
                                                    >
                                                        <circle
                                                            cx="4"
                                                            cy="4"
                                                            r="3"
                                                        />
                                                    </svg>
                                                    {{ tag.name["en"] }}
                                                </span>
                                            </span>
                                        </p>
                                    </dd>
                                </div>
                                <div
                                    class="text-xs text-gray-400 pr-5 mb-1 mt-1 truncate ..."
                                >
                                    {{ project.description }}
                                </div>
                                <div class="text-xs text-gray-400 pr-5">
                                    <span class="font-bold text-gray-600"
                                        >Last updated on</span
                                    >
                                    {{ formatDate(project.updated_at) }}
                                </div>
                                <div class="text-xs text-gray-400 pr-5">
                                    <span class="font-bold text-gray-600"
                                        >Created on</span
                                    >
                                    {{ formatDate(project.created_at) }}
                                </div>
                                <span
                                    v-if="
                                        !project.is_public &&
                                        project.is_published
                                    "
                                    class="inline-flex items-center mt-2 px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-red-800 capitalize"
                                >
                                    PUBLISHED -&emsp;
                                    <b
                                        >Release date:
                                        {{
                                            formatDate(project.release_date)
                                        }}</b
                                    >
                                </span>
                            </div>
                        </div>
                        <div class="border-l cursor-pointer">
                            <a
                                target="_blank"
                                :href="
                                    route('dashboard.projects', [project.id])
                                "
                                class="text-gray-500 hover:text-gray-900"
                            >
                                <ArrowTopRightOnSquareIcon
                                    class="h-5 w-5 text-gray-600 ml-4"
                                />
                            </a>
                            <!-- <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                class="h-8 w-8 text-gray-600 fill-current ml-4"
              >
                <path
                  d="M9.3 8.7a1 1 0 0 1 1.4-1.4l4 4a1 1 0 0 1 0 1.4l-4 4a1 1 0 0 1-1.4-1.4l3.29-3.3-3.3-3.3z"
                ></path>
              </svg> -->
                        </div>
                    </div>
                </a>
            </div>
        </span>
    </div>
</template>
<script>
import { Link } from "@inertiajs/inertia-vue3";
import {
    StarIcon,
    GlobeEuropeAfricaIcon,
    LockClosedIcon,
    ArrowTopRightOnSquareIcon,
} from "@heroicons/vue/24/solid";
export default {
    components: {
        Link,
        StarIcon,
        GlobeEuropeAfricaIcon,
        LockClosedIcon,
        ArrowTopRightOnSquareIcon,
    },
    props: ["projects", "mode", "teamRole", "team"],
    setup() {},
    data() {
        return {};
    },
    methods: {
        openDatasetCreateDialog(data) {
            this.emitter.emit("openDatasetCreateDialog", data);
        },
        openProjectCreateDialog(data) {
            this.emitter.emit("openProjectCreateDialog", data);
        },
        getLink(project) {
            if (project) {
                return this.$inertia.visit(
                    this.route("dashboard.projects", [project.id])
                );
            }
        },
    },
};
</script>
