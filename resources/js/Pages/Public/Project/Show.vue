<template>
    <Head :title="project.data.name" />

    <project-layout :project="project" :selected-tab="tab">
        <template #project-content>
            <div class="pb-10 mb-10 pt-4 pb-6">
                <div class="mt-6 lg:grid lg:grid-cols-12 lg:gap-x-6 lg:gap-y-6">
                    <div
                        :class="[
                            'min-w-0 space-y-4',
                            hasInfoSidebar ? 'lg:col-span-9' : 'lg:col-span-12',
                        ]"
                    >
                        <!-- About project (original public layout + edit tools) -->
                        <div>
                            <div
                                class="flex flex-wrap items-start justify-between gap-2"
                            >
                                <h3
                                    class="text-xl font-extrabold text-blue-gray-900 dark:text-gray-100"
                                >
                                    About project
                                </h3>
                                <button
                                    v-if="canUpdateProject"
                                    type="button"
                                    class="inline-flex shrink-0 items-center gap-1 rounded-md border border-gray-300 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-1 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
                                    @click="toggleDetails"
                                >
                                    <PencilIcon class="h-3.5 w-3.5" />
                                    <span>Edit</span>
                                </button>
                            </div>
                            <div
                                class="grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6"
                            >
                                <div class="col-span-6">
                                    <p
                                        v-if="project.data.description"
                                        style="max-width: 100ch !important"
                                        class="mt-1 text-md text-blue-gray-500 dark:text-blue-gray-400"
                                        v-html="md(project.data.description)"
                                    ></p>
                                    <p
                                        v-else
                                        class="mt-1 text-md text-blue-gray-500 dark:text-blue-gray-400"
                                    >
                                        No description has been provided yet.
                                    </p>
                                </div>
                            </div>
                            <div
                                v-if="workspace && dashboardProject"
                                class="mt-4 flex flex-wrap items-center gap-3"
                            >
                                <Publish
                                    v-if="
                                        !dashboardProject.is_public &&
                                        !dashboardProject.is_published &&
                                        !dashboardProject.is_deleted &&
                                        !dashboardProject.doi &&
                                        workspace.role != 'reviewer'
                                    "
                                    :project="dashboardProject"
                                />
                            </div>
                        </div>

                        <div
                            v-if="
                                project.data.species &&
                                project.data.species.length > 0
                            "
                            class="pt-2"
                        >
                            <h3
                                class="text-xl font-extrabold text-blue-gray-900 dark:text-gray-100"
                            >
                                Organism
                            </h3>
                            <div class="pt-3">
                                <div
                                    v-for="(species, $index) in project.data
                                        .species"
                                    :key="$index"
                                    class="bg-gray-100 text-gray-800 mb-0.5 inline-flex truncate break-words items-center px-3 py-2 rounded-full text-sm font-medium mr-1"
                                >
                                    <i
                                        ><ontology-term-annotation
                                            :annotation="species"
                                        ></ontology-term-annotation
                                    ></i>
                                </div>
                            </div>
                        </div>

                        <div class="gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                            <div
                                v-if="
                                    project.data.users &&
                                    project.data.users.length
                                "
                                class="pt-2 sm:col-span-6"
                            >
                                <h2
                                    class="text-xl font-extrabold mb-3 text-blue-gray-900 dark:text-gray-100"
                                >
                                    {{ submittersLabel }}
                                </h2>
                            </div>

                            <div
                                v-if="
                                    project.data.users &&
                                    project.data.users.length
                                "
                                class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2"
                            >
                                <div
                                    v-for="u in project.data.users"
                                    :key="u.email"
                                    class="relative rounded-lg border border-gray-300 bg-white p-5 shadow-sm hover:shadow-lg transition-all duration-200 flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"
                                >
                                    <div class="flex-shrink-0">
                                        <img
                                            class="h-10 w-10 rounded-full"
                                            :src="u.profile_photo_url"
                                            alt=""
                                        />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <a class="focus:outline-none">
                                            <span
                                                class="absolute inset-0"
                                                aria-hidden="true"
                                            ></span>
                                            <p
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{
                                                    u.first_name +
                                                    " " +
                                                    u.last_name
                                                }}
                                            </p>
                                            <p
                                                class="text-sm text-gray-500 truncate"
                                            >
                                                @ {{ u.username }}
                                            </p>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="
                                    canUpdateProject ||
                                    (project.data.authors &&
                                        project.data.authors.length > 0) ||
                                    (project.data.citations &&
                                        project.data.citations.length > 0)
                                "
                                class="pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6"
                            >
                                <template
                                    v-if="
                                        canUpdateProject ||
                                        (project.data.authors &&
                                            project.data.authors.length > 0)
                                    "
                                >
                                    <div
                                        class="sm:col-span-6 flex flex-wrap items-center justify-between gap-2"
                                    >
                                        <h2
                                            class="text-xl font-extrabold mb-0 text-blue-gray-900 dark:text-gray-100"
                                        >
                                            {{ authorsLabel }}
                                        </h2>
                                        <button
                                            v-if="canUpdateProject"
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200"
                                            @click="toggleManageAuthor"
                                        >
                                            <PencilIcon
                                                class="h-3.5 w-3.5 mr-1"
                                            />
                                            Edit
                                        </button>
                                    </div>

                                    <div
                                        class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2"
                                    >
                                        <author-card
                                            :authors="
                                                project.data.authors || []
                                            "
                                            :show-edit-delete="canUpdateProject"
                                            @edit="onAuthorCardEdit"
                                            @delete="onAuthorCardDelete"
                                        />
                                    </div>
                                </template>

                                <div
                                    v-if="
                                        canUpdateProject ||
                                        (project.data.citations &&
                                            project.data.citations.length > 0)
                                    "
                                    class="pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6"
                                >
                                    <div
                                        class="sm:col-span-6 flex flex-wrap items-center justify-between gap-2"
                                    >
                                        <h2
                                            class="text-xl font-extrabold mb-0 text-blue-gray-900 dark:text-gray-100"
                                        >
                                            {{ citationsLabel }}
                                        </h2>
                                        <button
                                            v-if="canUpdateProject"
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200"
                                            @click="toggleManageCitation"
                                        >
                                            <PencilIcon
                                                class="h-3.5 w-3.5 mr-1"
                                            />
                                            Edit
                                        </button>
                                    </div>

                                    <dd
                                        class="sm:col-span-6 mt-2 text-md text-gray-900 space-y-5 focus:pointer-events-auto"
                                    >
                                        <div
                                            class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2"
                                        >
                                            <citation-card
                                                :citations="
                                                    project.data.citations || []
                                                "
                                                :show-edit-delete="
                                                    canUpdateProject
                                                "
                                                @edit="onCitationCardEdit"
                                                @delete="onCitationCardDelete"
                                            />
                                        </div>
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>

                    <aside
                        v-if="hasInfoSidebar"
                        class="mt-10 min-w-0 lg:mt-0 lg:col-span-3"
                    >
                        <div
                            class="space-y-8 bg-white py-6 pl-0 pr-0 dark:bg-gray-900/80 lg:sticky lg:top-6"
                        >
                            <div
                                v-if="
                                    (project.data && project.data.identifier) ||
                                    project.identifier
                                "
                            >
                                <h3
                                    class="text-sm font-bold text-gray-900 dark:text-gray-100"
                                >
                                    Identifier
                                </h3>
                                <div class="mt-2">
                                    <Tag
                                        :identifier="
                                            (project.data &&
                                                project.data.identifier) ||
                                            project.identifier
                                        "
                                    />
                                </div>
                            </div>

                            <div
                                v-if="
                                    (project.data && project.data.doi) ||
                                    project.doi
                                "
                            >
                                <h3
                                    class="text-sm font-bold text-gray-900 dark:text-gray-100"
                                >
                                    DOI
                                </h3>
                                <div class="mt-2">
                                    <DOIBadge
                                        :doi="
                                            (project.data &&
                                                project.data.doi) ||
                                            project.doi
                                        "
                                    />
                                </div>
                            </div>

                            <div v-if="showDoiCitation" class="w-full min-w-0">
                                <Citation
                                    :model="'project'"
                                    :doi="project.data.doi"
                                />
                            </div>

                            <div v-if="licenseTitle">
                                <h3
                                    class="text-sm font-bold text-gray-900 dark:text-gray-100"
                                >
                                    License
                                </h3>
                                <div class="mt-2 text-sm">
                                    <a
                                        v-if="licenseUrl"
                                        :href="licenseUrl"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="font-medium text-gray-900 underline decoration-gray-400 underline-offset-2 hover:text-teal-700 dark:text-gray-200 dark:hover:text-teal-400"
                                    >
                                        {{ licenseTitle }}
                                    </a>
                                    <span
                                        v-else
                                        class="font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ licenseTitle }}
                                    </span>
                                </div>
                            </div>

                            <div
                                v-if="
                                    (project.data &&
                                        project.data.release_date) ||
                                    project.release_date
                                "
                            >
                                <h3
                                    class="text-sm font-bold text-gray-900 dark:text-gray-100"
                                >
                                    Published
                                </h3>
                                <p
                                    class="mt-2 text-sm font-medium text-gray-800 dark:text-gray-200"
                                >
                                    {{
                                        formatDate(
                                            (project.data &&
                                                project.data.release_date) ||
                                                project.release_date
                                        )
                                    }}
                                </p>
                            </div>

                            <div
                                v-if="
                                    (project.data && project.data.created_at) ||
                                    project.created_at
                                "
                            >
                                <h3
                                    class="text-sm font-bold text-gray-900 dark:text-gray-100"
                                >
                                    Created
                                </h3>
                                <p
                                    class="mt-2 text-sm font-medium text-gray-800 dark:text-gray-200"
                                >
                                    {{
                                        formatDate(
                                            (project.data &&
                                                project.data.created_at) ||
                                                project.created_at
                                        )
                                    }}
                                </p>
                            </div>

                            <div v-if="tagsList.length > 0">
                                <h3
                                    class="text-sm font-bold text-gray-900 dark:text-gray-100"
                                >
                                    Tags
                                </h3>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <a
                                        v-for="tag in displayedTags"
                                        :key="tag.id"
                                        :href="'/projects?tag=' + tag.name.en"
                                        class="inline-flex items-center rounded-full border border-gray-300 bg-white px-3 py-1 text-xs font-medium text-gray-900 shadow-sm transition-colors hover:border-gray-400 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-950 dark:text-gray-100 dark:hover:bg-gray-800"
                                    >
                                        {{ tag.name.en }}
                                    </a>
                                    <button
                                        v-if="
                                            tagsList.length > 8 && !showAllTags
                                        "
                                        type="button"
                                        class="inline-flex items-center rounded-full border border-dashed border-gray-300 px-3 py-1 text-xs font-medium text-teal-700 hover:bg-teal-50 dark:border-gray-600 dark:text-teal-400 dark:hover:bg-teal-950/40"
                                        @click="showAllTags = true"
                                    >
                                        +{{ tagsList.length - 8 }} more
                                    </button>
                                    <button
                                        v-if="
                                            tagsList.length > 8 && showAllTags
                                        "
                                        type="button"
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400"
                                        @click="showAllTags = false"
                                    >
                                        Show less
                                    </button>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>

                <project-details
                    v-if="workspace && dashboardProject"
                    ref="projectDetailsElement"
                    :project="dashboardProject"
                    :project-permissions="workspace.projectPermissions"
                    :role="workspace.role"
                />
                <manage-author
                    v-if="workspace && dashboardProject"
                    ref="manageAuthorElement"
                    :project="dashboardProject"
                />
                <manage-citation
                    v-if="workspace && dashboardProject"
                    ref="manageCitationElement"
                    :project="dashboardProject"
                />
            </div>
        </template>
    </project-layout>

    <component :is="'script'" type="application/ld+json">{{
        schema
    }}</component>
</template>

<script>
import ProjectLayout from "@/Pages/Public/Project/Layout.vue";
import AuthorCard from "@/Shared/AuthorCard.vue";
import CitationCard from "@/Shared/CitationCard.vue";
import ManageAuthor from "@/Shared/ManageAuthor.vue";
import ManageCitation from "@/Shared/ManageCitation.vue";
import ProjectDetails from "@/Pages/Project/Partials/Details.vue";
import Publish from "@/Shared/Publish.vue";
import Citation from "@/Shared/Citation.vue";
import DOIBadge from "@/Shared/DOIBadge.vue";
import Tag from "@/Shared/Tag.vue";
import "ontology-elements/dist/index.js";
import { Head } from "@inertiajs/vue3";
import { PencilIcon } from "@heroicons/vue/24/solid";

export default {
    name: "ProjectShow",

    components: {
        ProjectLayout,
        AuthorCard,
        CitationCard,
        Citation,
        DOIBadge,
        Head,
        ManageAuthor,
        ManageCitation,
        ProjectDetails,
        Publish,
        PencilIcon,
        Tag,
    },

    props: {
        project: {
            type: Object,
            required: true,
        },
        tab: {
            type: String,
            required: true,
        },
    },

    data() {
        return {
            schema: {},
            showAllTags: false,
        };
    },

    computed: {
        workspace() {
            return this.$page.props.workspace ?? null;
        },
        dashboardProject() {
            return this.workspace?.dashboardProject ?? null;
        },
        canUpdateProject() {
            return !!this.workspace?.projectPermissions?.canUpdateProject;
        },
        tagsList() {
            const raw = this.project?.data?.tags || this.project?.tags || [];

            return Array.isArray(raw) ? raw : [];
        },
        displayedTags() {
            if (this.showAllTags || this.tagsList.length <= 8) {
                return this.tagsList;
            }

            return this.tagsList.slice(0, 8);
        },
        licenseTitle() {
            const license =
                this.project?.data?.license || this.project?.license;

            return license?.title ?? null;
        },
        licenseUrl() {
            const license =
                this.project?.data?.license || this.project?.license;

            return license?.url ?? null;
        },
        showDoiCitation() {
            return (
                this.project?.data?.is_public && this.project?.data?.doi != null
            );
        },
        submittersLabel() {
            const count = this.project?.data?.users?.length ?? 0;

            return count === 1 ? "Submitter" : "Submitters";
        },
        authorsLabel() {
            const count = this.project?.data?.authors?.length ?? 0;

            return count === 1 ? "Author" : "Authors";
        },
        citationsLabel() {
            const count = this.project?.data?.citations?.length ?? 0;

            return count === 1 ? "Citation" : "Citations";
        },
        hasInfoSidebar() {
            const hasId = !!(
                this.project?.data?.identifier || this.project?.identifier
            );
            const hasDoi = !!(this.project?.data?.doi || this.project?.doi);
            const hasDates = !!(
                this.project?.data?.release_date ||
                this.project?.release_date ||
                this.project?.data?.created_at ||
                this.project?.created_at
            );
            const hasTags = this.tagsList.length > 0;

            return (
                hasId ||
                hasDoi ||
                this.showDoiCitation ||
                this.licenseTitle ||
                hasDates ||
                hasTags
            );
        },
    },

    mounted() {
        axios
            .get(route("bioschemas.id", this.project.data.identifier))
            .then((response) => {
                this.schema = response.data;
            });

        this.handleEditQueryParam();
    },

    methods: {
        formatDate(value) {
            if (!value) {
                return "";
            }
            const d = new Date(value);

            return d.toLocaleDateString(undefined, {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        },
        toggleDetails() {
            this.$refs.projectDetailsElement?.toggleDetails();
        },
        toggleManageAuthor() {
            this.$refs.manageAuthorElement?.toggleDialog();
        },
        toggleManageCitation() {
            this.$refs.manageCitationElement?.toggleDialog();
        },
        onCitationCardEdit(citation) {
            const modal = this.$refs.manageCitationElement;
            if (!modal) {
                return;
            }
            if (!modal.showDialog) {
                modal.toggleDialog();
            }
            this.$nextTick(() => {
                modal.edit(citation);
            });
        },
        onCitationCardDelete(citation) {
            this.$refs.manageCitationElement?.confirmDeletion(citation);
        },
        onAuthorCardEdit(author) {
            const modal = this.$refs.manageAuthorElement;
            if (!modal) {
                return;
            }
            if (!modal.showDialog) {
                modal.toggleDialog();
            }
            this.$nextTick(() => {
                modal.edit(author);
            });
        },
        onAuthorCardDelete(author) {
            this.$refs.manageAuthorElement?.confirmDeletion(author);
        },
        stripEditQueryParamFromUrl() {
            const url = new URL(window.location.href);
            if (!url.searchParams.has("edit")) {
                return;
            }
            url.searchParams.delete("edit");
            const search = url.searchParams.toString();
            const next = url.pathname + (search ? `?${search}` : "") + url.hash;
            window.history.replaceState({}, "", next);
        },
        handleEditQueryParam() {
            if (!this.workspace) {
                return;
            }
            const params = new URLSearchParams(window.location.search);
            const edit = params.get("edit");
            if (!edit) {
                return;
            }
            if (edit === "release_date") {
                return;
            }
            const allowed = [
                "license",
                "title",
                "description",
                "keywords",
                "profile_image",
                "release_date",
                "citation",
                "authors",
            ];
            if (!allowed.includes(edit)) {
                return;
            }
            this.$nextTick(() => {
                if (edit === "citation") {
                    this.toggleManageCitation();
                } else if (edit === "authors") {
                    this.toggleManageAuthor();
                } else {
                    this.toggleDetails();
                }
                this.stripEditQueryParamFromUrl();
            });
        },
    },
};
</script>
