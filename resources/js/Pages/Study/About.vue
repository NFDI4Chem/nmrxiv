<template>
    <div>
        <study-content
            model="study"
            :project="project"
            :study="study"
            :team="team"
            :members="members"
            :available-roles="availableRoles"
            :study-permissions="studyPermissions"
            :study-role="studyRole"
            current="About"
            :preview="preview"
        >
            <template #study-section>
                <div class="divide-y divide-gray-200 sm:col-span-9">
                    <div class="py-3 px-4 sm:p-6 lg:pb-8">
                        <div class="mt-0">
                            <div
                                v-if="canUpdateStudy || study.tags.length > 0"
                                class="mb-4"
                            >
                                <div class="relative">
                                    <div
                                        class="absolute inset-0 flex items-center"
                                        aria-hidden="true"
                                    >
                                        <div
                                            class="w-full border-t border-gray-300"
                                        ></div>
                                    </div>
                                    <div
                                        class="relative flex items-center justify-between"
                                    >
                                        <span
                                            class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                                        >
                                            Keywords
                                        </span>
                                        <button
                                            v-if="canUpdateStudy"
                                            type="button"
                                            class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                            @click="openStudyDetailsPane"
                                        >
                                            <PencilIcon
                                                class="w-4 h-4 mr-1 text-gray-600"
                                            />
                                            <span>Edit</span>
                                        </button>
                                    </div>
                                </div>
                                <dd
                                    class="mt-1 text-md text-gray-900 space-y-5"
                                >
                                    <p>
                                        <span
                                            v-for="tag in study.tags"
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
                            <div class="mb-4">
                                <div class="relative">
                                    <div
                                        class="absolute inset-0 flex items-center"
                                        aria-hidden="true"
                                    >
                                        <div
                                            class="w-full border-t border-gray-300"
                                        ></div>
                                    </div>
                                    <div
                                        class="relative flex items-center justify-between"
                                    >
                                        <span
                                            class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                                        >
                                            Description
                                        </span>
                                        <button
                                            v-if="canUpdateStudy"
                                            type="button"
                                            class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                            @click="openStudyDetailsPane"
                                        >
                                            <PencilIcon
                                                class="w-4 h-4 mr-1 text-gray-600"
                                            />
                                            <span>Edit</span>
                                        </button>
                                    </div>
                                </div>
                                <dd
                                    class="mt-1 text-md text-gray-900 space-y-5"
                                >
                                    <p
                                        style="max-width: 100ch !important"
                                        class="prose mt-4 text-sm text-blue-gray-500"
                                        v-html="md(study.description)"
                                    ></p>
                                </dd>
                            </div>
                            <div class="mb-4">
                                <div class="relative">
                                    <div
                                        class="absolute inset-0 flex items-center"
                                        aria-hidden="true"
                                    >
                                        <div
                                            class="w-full border-t border-gray-300"
                                        ></div>
                                    </div>
                                    <div
                                        class="relative flex items-center justify-between"
                                    >
                                        <span
                                            class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                                        >
                                            License
                                        </span>
                                        <button
                                            v-if="canUpdateStudy"
                                            type="button"
                                            class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                            @click="openStudyDetailsPane"
                                        >
                                            <PencilIcon
                                                class="w-4 h-4 mr-1 text-gray-600"
                                            />
                                            <span>Edit</span>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <dd
                                        v-if="license"
                                        class="text-md text-gray-900 space-y-5"
                                    >
                                        <p
                                            style="max-width: 100ch !important"
                                            class="prose mt-4 text-sm text-blue-gray-500"
                                        >
                                            {{ license.title }}
                                            <ToolTip
                                                v-if="study.license_id"
                                                class="inline h-4 w-4 ml-0"
                                                :text="license.description"
                                            ></ToolTip>
                                        </p>
                                    </dd>
                                </div>
                            </div>

                            <!-- Author -->
                            <div
                                v-if="
                                    (project &&
                                        project.authors &&
                                        project.authors.length > 0) ||
                                    (study &&
                                        study.authors &&
                                        study.authors.length > 0)
                                "
                                class="mb-8"
                            >
                                <div class="relative">
                                    <div
                                        class="absolute inset-0 flex items-center"
                                        aria-hidden="true"
                                    >
                                        <div
                                            class="w-full border-t border-gray-300"
                                        ></div>
                                    </div>
                                    <div
                                        class="relative flex items-center justify-between"
                                    >
                                        <span
                                            class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                                        >
                                            Author
                                        </span>
                                    </div>
                                </div>
                                <dd
                                    class="mt-2 text-md text-gray-900 space-y-5"
                                >
                                    <div
                                        class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-3"
                                    >
                                        <author-card
                                            :authors="
                                                study.authors &&
                                                study.authors.length > 0
                                                    ? study.authors
                                                    : project.authors
                                            "
                                        />
                                    </div>
                                </dd>
                            </div>

                            <div class="mb-4">
                                <div class="relative mb-4" aria-hidden="true">
                                    <div
                                        class="w-full border-t border-gray-300"
                                    ></div>
                                </div>
                                <ChemicalCompositionEditor
                                    :study="study"
                                    :can-update-study="canUpdateStudy"
                                    :chemistry-standardize-url="
                                        chemistryStandardizeUrl
                                    "
                                    :expanded="chemicalCompositionExpanded"
                                    id-prefix="study-chemical-composition"
                                    @update:expanded="
                                        chemicalCompositionExpanded = $event
                                    "
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </study-content>
    </div>
</template>

<script>
import { PencilIcon } from "@heroicons/vue/24/solid";
import StudyContent from "@/Pages/Study/Content.vue";
import ToolTip from "@/Shared/ToolTip.vue";
import AuthorCard from "@/Shared/AuthorCard.vue";
import ChemicalCompositionEditor from "@/Shared/ChemicalCompositionEditor.vue";
export default {
    components: {
        StudyContent,
        ToolTip,
        PencilIcon,
        AuthorCard,
        ChemicalCompositionEditor,
    },
    props: [
        "study",
        "project",
        "team",
        "members",
        "availableRoles",
        "studyPermissions",
        "studyRole",
        "license",
        "preview",
    ],
    data() {
        return {
            chemicalCompositionExpanded: true,
        };
    },
    computed: {
        canUpdateStudy() {
            return this.studyPermissions
                ? this.studyPermissions.canUpdateStudy
                : false;
        },
        chemistryStandardizeUrl() {
            return this.$page.props.chemistryStandardizeUrl;
        },
    },
    methods: {
        openStudyDetailsPane() {
            this.emitter.emit("openStudyDetails", {});
        },
    },
};
</script>
