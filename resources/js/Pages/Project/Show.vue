<template>
    <app-layout :title="project.name">
        <template #header>
            <div
                v-if="project.is_deleted"
                class="text-center px-3 py-2 bg-red-50 text-red-700 border-b"
            >
                <b>Warning: </b> This project is deleted. At the end of the
                30-day period, the project and all of its resources are deleted
                and cannot be recovered. You can restore a deleted project
                within the 30-day recovery period.
            </div>
            <div
                v-if="!project.is_public && project.is_published"
                class="text-center px-3 py-2 bg-green-50 text-green-700 border-b"
            >
                <b>Info: </b> This project is published and set to be released
                on {{ formatDate(project.release_date) }}. You cannot edit a
                published project, please create a new version to updated the
                project.
            </div>
            <div v-if="project.is_public">
                <div
                    v-if="project.is_archived"
                    class="text-center px-3 py-2 bg-yellow-50 text-yellow-700 border-b"
                >
                    <b>Warning: </b> This project is archived. It is now
                    read-only.
                </div>
                <div
                    class="text-center px-3 py-2 bg-green-50 text-green-700 border-b"
                    v-else
                >
                    <b>Info: </b> This project is published. You cannot edit a
                    published project, please create a new version to updated
                    the project.
                </div>
            </div>
            <div v-if="project.is_public && project.doi != null">
                <Citation :model="'project'" :doi="project.doi"></Citation>
            </div>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="flex flex-nowrap justify-between pt-6 w-full">
                        <div class="">
                            <div
                                class="flex pr-20 cursor-pointer items-center text-xl text-gray-700 font-bold"
                            >
                                <StarIcon
                                    :class="[
                                        project.is_bookmarked
                                            ? 'text-yellow-400'
                                            : 'text-gray-200',
                                        'h-5 w-5 -ml-1 mr-1',
                                    ]"
                                    aria-hidden="true"
                                    @click="toogleStarred"
                                />
                                {{ project.name }}

                                <button
                                    v-if="canUpdateProject"
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

                            <div class="inline-flex items-center mt-3">
                                <access-dialogue
                                    :available-roles="availableRoles"
                                    :role="role"
                                    :team="team"
                                    :members="members"
                                    :project="project"
                                    called-from="projectView"
                                    model="project"
                                />

                                <ToolTip
                                    class="inline h-4 w-4 ml-0"
                                    text="The avatars (left) correspond to the users with whom the project is shared. Click there to edit sharing options."
                                ></ToolTip>
                                <a
                                    class="cursor-pointer hover:text-teal-900 inline-flex items-center ml-7"
                                    @click="toggleDetails"
                                >
                                    <FlagIcon class="w-4 h-4 text-gray-500" />
                                    <span class="ml-2">View details</span>
                                </a>
                                <a
                                    ><span
                                        v-if="project.is_public"
                                        class="inline-flex items-center"
                                    >
                                        <GlobeEuropeAfricaIcon
                                            class="h-4 w-4 ml-4 text-gray-800"
                                        />
                                        <span class="ml-2">Public</span>
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex ml-7 items-center"
                                    >
                                        <LockClosedIcon
                                            class="h-4 w-4 text-gray-800"
                                        />
                                        <span class="ml-2">Private</span>
                                    </span></a
                                >
                                <project-details
                                    ref="projectDetailsElement"
                                    :role="role"
                                    :projectPermissions="projectPermissions"
                                    :project="project"
                                />
                                <manage-author
                                    ref="manageAuthorElement"
                                    :project="project"
                                />
                                <manage-citation
                                    ref="manageCitationElement"
                                    :project="project"
                                />
                                <span
                                    class="capitalize inline-flex pr-4 ml-7 inline-flex items-center px-2.5 py-0.5 rounded-full text-s font-medium bg-gray-100 text-gray-800"
                                >
                                    <span v-if="role == 'reviewer'">
                                        <EyeIcon
                                            class="h-7 w-7 py-1 text-gray-800"
                                        />
                                    </span>
                                    <span v-if="role == 'collaborator'">
                                        <PencilSquareIcon
                                            class="h-7 w-7 py-1 text-gray-800"
                                        />
                                    </span>
                                    <span
                                        v-if="
                                            role == 'owner' || role == 'creator'
                                        "
                                    >
                                        <CubeTransparentIcon
                                            class="h-7 w-7 py-1 text-gray-800"
                                        />
                                    </span>
                                    {{ role }}
                                </span>
                                <span
                                    v-if="project.identifier"
                                    class="inline-flex pr-4 ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                >
                                    <HashtagIcon class="h-6 w-6 py-1" />
                                    <b>{{ project.identifier }}</b>
                                </span>
                            </div>
                        </div>
                        <div class="flex-nowrap">
                            <Link
                                v-if="canDeleteProject"
                                :href="
                                    route(
                                        'dashboard.project.settings',
                                        project.id
                                    )
                                "
                                class="text-sm flex-nowrap text-gray-800 font-bold"
                            >
                                Project&nbsp;Settings
                            </Link>
                        </div>
                    </div>
                    <div class="flex flex-nowrap justify-between pb-3">
                        <div
                            class="mt-2 flex items-center text-xs text-gray-400"
                        >
                            <CalendarIcon
                                class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-300"
                                aria-hidden="true"
                            />
                            Updated on
                            {{ formatDateTime(project.updated_at) }}
                        </div>
                        <div
                            v-if="!project.is_public && !project.is_published"
                            class="flex-nowrap"
                        >
                            <Publish :project="project" />
                        </div>
                        <div v-if="!project.is_public && project.is_published">
                            <span
                                class="ml-4 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-red-800 capitalize"
                            >
                                PUBLISHED -&emsp;
                                <b
                                    >Release date:
                                    {{ formatDate(project.release_date) }}</b
                                >
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div class="p-12 pt-8">
            <div>
                <div class="mb-8">
                    <div class="relative">
                        <div
                            class="absolute inset-0 flex items-center"
                            aria-hidden="true"
                        >
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex items-center justify-between">
                            <span
                                class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                            >
                                Description
                            </span>
                            <button
                                v-if="canUpdateProject"
                                type="button"
                                class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                @click="toggleDetails"
                            >
                                <PencilIcon
                                    class="w-4 h-4 mr-1 text-gray-600"
                                />
                                <span>Edit</span>
                            </button>
                        </div>
                    </div>
                    <dd class="mt-1 text-gray-900 space-y-5">
                        <p
                            style="max-width: 100ch !important"
                            class="prose mt-1 text-sm text-blue-gray-500"
                            v-html="md(project.description)"
                        ></p>
                    </dd>
                </div>
                <!-- Keywords -->
                <div class="mb-8">
                    <div class="relative">
                        <div
                            class="absolute inset-0 flex items-center"
                            aria-hidden="true"
                        >
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex items-center justify-between">
                            <span
                                class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                            >
                                Keywords
                            </span>
                            <button
                                v-if="canUpdateProject"
                                type="button"
                                class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                @click="toggleDetails"
                            >
                                <PencilIcon
                                    class="w-4 h-4 mr-1 text-gray-600"
                                />
                                <span>Edit</span>
                            </button>
                        </div>
                    </div>
                    <dd class="mt-1 text-md text-gray-900 space-y-5">
                        <p>
                            <span
                                v-for="tag in project.tags"
                                :key="tag.id"
                                class="mr-2"
                            >
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800"
                                >
                                    <svg
                                        class="-ml-0.5 mr-1.5 h-2 w-2 text-indigo-400"
                                        fill="currentColor"
                                        viewBox="0 0 8 8"
                                    >
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    {{ tag.name["en"] }}
                                </span>
                            </span>
                        </p>
                    </dd>
                </div>
                <!--License -->
                <div class="mb-4">
                    <div class="relative">
                        <div
                            class="absolute inset-0 flex items-center"
                            aria-hidden="true"
                        >
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex items-center justify-between">
                            <span
                                class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                            >
                                License
                            </span>
                            <button
                                v-if="canUpdateProject"
                                type="button"
                                class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                @click="toggleDetails"
                            >
                                <PencilIcon
                                    class="w-4 h-4 mr-1 text-gray-600"
                                />
                                <span>Edit</span>
                            </button>
                        </div>
                    </div>
                    <div>
                        <dd v-if="license" class="mt-1 text-gray-900 space-y-5">
                            <p
                                style="max-width: 100ch !important"
                                class="prose mt-1 text-sm text-blue-gray-500"
                            >
                                {{ license.title }}
                                <ToolTip
                                    v-if="project.license_id"
                                    class="inline h-4 w-4 ml-0"
                                    :text="license.description"
                                ></ToolTip>
                            </p>
                        </dd>
                    </div>
                </div>
                <!-- Citation -->
                <div class="mb-8">
                    <div class="relative">
                        <div
                            class="absolute inset-0 flex items-center"
                            aria-hidden="true"
                        >
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex items-center justify-between">
                            <span
                                class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                            >
                                Citation
                            </span>
                            <button
                                v-if="canUpdateProject"
                                type="button"
                                class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                @click="toggleManageCitation"
                            >
                                <PencilIcon
                                    class="w-4 h-4 mr-1 text-gray-600"
                                />
                                <span>Edit</span>
                            </button>
                        </div>
                    </div>
                    <dd
                        class="mt-2 text-md text-gray-900 space-y-5 focus:pointer-events-auto"
                    >
                        <div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div
                                v-for="citation in project.citations"
                                :key="citation.id"
                                class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-top space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-teal-500"
                            >
                                <div class="flex-1 min-w-0">
                                    <a
                                        class="focus:outline-none cursor-pointer"
                                        :href="getCitationLink(citation.doi)"
                                        :target="getTarget(citation.doi)"
                                    >
                                        <span
                                            class="absolute inset-0"
                                            aria-hidden="true"
                                        ></span>
                                        <p
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ citation.title }}
                                        </p>
                                        <p class="text-sm text-teal-500">
                                            {{ citation.authors }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ citation.citation_text }}
                                        </p>
                                        <p
                                            v-if="citation.doi"
                                            class="text-sm font-sm text-gray-500"
                                        >
                                            DOI -
                                            <a
                                                :href="citation.doi"
                                                class="text-teal-900"
                                                >{{ citation.doi }}</a
                                            >
                                        </p>
                                        <p
                                            class="text-sm text-gray-500 truncate ..."
                                        >
                                            {{ citation.abstract }} ...
                                        </p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </dd>
                </div>

                <!-- Author -->
                <div class="mb-8">
                    <div class="relative">
                        <div
                            class="absolute inset-0 flex items-center"
                            aria-hidden="true"
                        >
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex items-center justify-between">
                            <span
                                class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                            >
                                Author
                            </span>
                            <button
                                v-if="canUpdateProject"
                                type="button"
                                class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                @click="toggleManageAuthor"
                            >
                                <PencilIcon
                                    class="w-4 h-4 mr-1 text-gray-600"
                                />
                                <span>Edit</span>
                            </button>
                        </div>
                    </div>
                    <dd class="mt-2 text-md text-gray-900 space-y-5">
                        <div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <author-card :authors="this.project.authors" />
                        </div>
                    </dd>
                </div>

                <div class="relative py-5">
                    <div
                        class="absolute inset-0 flex items-center"
                        aria-hidden="true"
                    >
                        <div class="w-full border-t border-gray-100"></div>
                    </div>
                </div>
                <study-index
                    :editable="editable"
                    :project="project"
                    :role="role"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import AccessDialogue from "@/Shared/AccessDialogue.vue";
import { Link } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import StudyIndex from "@/Pages/Study/Index.vue";
import ProjectDetails from "./Partials/Details.vue";
import { ref } from "vue";
import {
    StarIcon,
    PencilIcon,
    CalendarIcon,
    FlagIcon,
    GlobeEuropeAfricaIcon,
    LockClosedIcon,
    HashtagIcon,
} from "@heroicons/vue/24/solid";
import {
    EyeIcon,
    PencilSquareIcon,
    CubeTransparentIcon,
} from "@heroicons/vue/24/outline";
import ManageAuthor from "@/Shared/ManageAuthor.vue";
import ToolTip from "@/Shared/ToolTip.vue";
import ManageCitation from "@/Shared/ManageCitation.vue";
import Citation from "@/Shared/Citation.vue";
import Publish from "@/Shared/Publish.vue";
import AuthorCard from "@/Shared/AuthorCard.vue";

export default {
    components: {
        Link,
        AppLayout,
        StudyIndex,
        ProjectDetails,
        StarIcon,
        PencilIcon,
        AccessDialogue,
        ManageAuthor,
        ToolTip,
        ManageCitation,
        CalendarIcon,
        Citation,
        Publish,
        AuthorCard,
        FlagIcon,
        GlobeEuropeAfricaIcon,
        LockClosedIcon,
        EyeIcon,
        PencilSquareIcon,
        CubeTransparentIcon,
        HashtagIcon,
    },
    props: [
        "project",
        "team",
        "members",
        "availableRoles",
        "projectPermissions",
        "role",
        "license",
    ],
    setup() {
        const projectDetailsElement = ref(null);
        const manageAuthorElement = ref(null);
        const manageCitationElement = ref(null);
        return {
            projectDetailsElement,
            manageAuthorElement,
            manageCitationElement,
        };
    },
    mounted() {
        const urlSearchParams = new URLSearchParams(window.location.search);
        const params = Object.fromEntries(urlSearchParams.entries());
        let editOperation = params["edit"];
        if (editOperation) {
            if (
                editOperation == "license" ||
                editOperation == "title" ||
                editOperation == "description" ||
                editOperation == "keywords" ||
                editOperation == "profile_image"
            ) {
                this.toggleDetails();
            } else if (editOperation == "citation") {
                this.toggleManageCitation();
            } else if (editOperation == "authors") {
                this.toggleManageAuthor();
            }
        }
    },
    data() {
        return {};
    },
    computed: {
        canDeleteProject() {
            if (this.role) {
                if (this.role == "owner" || this.role == "creator") {
                    return true;
                } else {
                    return false;
                }
            }
        },
        canUpdateProject() {
            return this.projectPermissions
                ? this.projectPermissions.canUpdateProject
                : false;
        },
    },
    methods: {
        toogleStarred() {
            const url = "/projects/" + this.project.id + "/toggleStarred";
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
                    Inertia.reload({ only: ["project"] });
                });
        },
        toggleDetails() {
            this.projectDetailsElement.toggleDetails();
        },
        toggleManageAuthor() {
            this.manageAuthorElement.toggleDialog();
        },
        toggleManageCitation() {
            this.manageCitationElement.toggleDialog();
            //this.emitter.emit("openAddCitationDialog", {});
        },
        getCitationLink(doi) {
            var link = "#";
            if (doi) {
                link = "https://doi.org/" + doi;
            }
            return link;
        },
        getTarget(id) {
            var target = null;
            if (id) {
                target = "_blank";
            }
            return target;
        },
    },
};
</script>
