<template>
    <div class="w-full min-w-0">
        <!-- <div class="flex items-baseline justify-between">
            <div id="tour-step-projects">
                <h2 class="text-lg">Projects</h2>
                <div class="mt-2 text-sm text-gray-700">
                <div class="max-w-2xl">You may house a variety of projects.</div>
                </div>
            </div>
        </div> -->
        <div v-if="projects.length <= 0" class="w-full min-w-0">
            <div
                v-if="mode == 'create' && editableTeamRole"
                class="mt-4 w-full"
            >
                <div
                    class="w-full rounded-lg border border-gray-200 bg-white px-6 py-7 shadow-sm ring-1 ring-black/[0.04]"
                >
                    <div class="flex items-center gap-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            class="h-5 w-5 shrink-0"
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
                            class="text-xs font-semibold uppercase tracking-wide text-gray-500"
                        >
                            Create your first project
                        </div>
                    </div>
                    <div
                        class="mt-3 block w-full min-w-0 text-sm leading-relaxed text-gray-600"
                    >
                        nmrXiv is organized around projects. Projects can
                        contain as many samples as you wish and each sample
                        receives its very own URL. Use the "UPLOAD" button on
                        the upper left side to start uploading projects or
                        samples. To learn more, check out our documentation.
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
        </div>
        <div
            v-else
            class="flex w-full min-w-0 flex-col items-stretch gap-5"
        >
            <article
                v-for="project in projects"
                :key="project.uuid"
                class="group relative flex w-full max-w-none min-w-0 flex-row overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm ring-1 ring-black/[0.04] transition-all duration-200 hover:border-gray-300 hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] dark:border-gray-800 dark:bg-gray-950 dark:ring-white/10"
            >
                <!-- Left status ribbon (Draft / Published / Embargo, …) -->
                <aside
                    v-if="project.status"
                    class="flex w-11 shrink-0 flex-col items-center justify-center gap-1 border-r border-black/[0.06] px-0.5 py-3 text-center sm:w-12 sm:py-4"
                    :class="statusRibbonSurface(project)"
                    :aria-label="`Project status: ${statusRibbonLabel(project)}`"
                >
                    <component
                        :is="statusRibbonIcon(project)"
                        class="h-4 w-4 shrink-0"
                        :class="statusRibbonIconTone(project)"
                        aria-hidden="true"
                    />
                    <span
                        class="max-w-[2.75rem] select-none text-[9px] font-bold uppercase leading-tight tracking-wide [text-orientation:mixed] [writing-mode:vertical-rl] sm:max-w-none sm:text-[10px]"
                        :class="statusRibbonTextTone(project)"
                    >
                        {{ statusRibbonLabel(project) }}
                    </span>
                </aside>
                <div class="flex min-w-0 flex-1 flex-col">
                    <div class="px-4 py-4 sm:px-5">
                        <!-- Title row + quick actions -->
                        <div
                            class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
                        >
                            <div
                                class="min-w-0 flex-1 cursor-pointer rounded-lg outline-none transition-colors focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                                @click="getLink(project)"
                            >
                                <h2
                                    class="break-words text-lg font-semibold leading-snug tracking-tight text-gray-900 transition-colors group-hover:text-indigo-700 sm:text-xl dark:text-gray-100 dark:group-hover:text-indigo-400"
                                >
                                    {{ project.name }}
                                </h2>
                            </div>
                            <div
                                class="flex shrink-0 items-center gap-1 sm:border-l sm:border-gray-100 sm:pl-4"
                                @click.stop
                            >
                                <button
                                    type="button"
                                    class="rounded-lg p-1.5 transition-colors hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:hover:bg-gray-800"
                                    :class="
                                        project.is_bookmarked
                                            ? 'text-amber-400'
                                            : 'text-gray-300 hover:text-amber-400 dark:text-gray-600'
                                    "
                                    :aria-pressed="project.is_bookmarked"
                                    :aria-label="
                                        project.is_bookmarked
                                            ? 'Remove project from starred'
                                            : 'Star project'
                                    "
                                    @click.stop="toggleProjectStarred(project)"
                                >
                                    <StarIcon class="h-5 w-5" aria-hidden="true" />
                                </button>
                                <div class="tooltip">
                                    <a
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        :href="getProjectSummaryLink(project)"
                                        class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:hover:bg-gray-800 dark:hover:text-gray-100"
                                        aria-label="Open project summary in a new tab"
                                    >
                                        <ArrowTopRightOnSquareIcon
                                            class="h-5 w-5"
                                        />
                                    </a>
                                    <span
                                        class="tooltiptextbottom rounded-md bg-gray-900 px-2 py-1 text-center text-xs text-white shadow-lg"
                                        >Summary view</span
                                    >
                                </div>
                                <div
                                    v-if="!project.is_public"
                                    class="tooltip"
                                >
                                    <a
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        :href="getProjectSettingsLink(project)"
                                        class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:hover:bg-gray-800 dark:hover:text-gray-100"
                                        aria-label="Open project settings in a new tab"
                                    >
                                        <Cog6ToothIcon class="h-5 w-5" />
                                    </a>
                                    <span
                                        class="tooltiptextbottom rounded-md bg-gray-900 px-2 py-1 text-center text-xs text-white shadow-lg"
                                        >Settings</span
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Shared owner -->
                        <div
                            v-if="
                                (team && team.id != project.team_id) ||
                                project.owner_id !=
                                    $page.props.auth.user.id
                            "
                            class="mt-3"
                        >
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-900 ring-1 ring-inset ring-indigo-100 dark:bg-indigo-950/60 dark:text-indigo-200 dark:ring-indigo-900"
                            >
                                <UserCircleIcon
                                    class="h-3.5 w-3.5 shrink-0 text-indigo-600 dark:text-indigo-400"
                                    aria-hidden="true"
                                />
                                Shared by
                                {{
                                    project.owner
                                        ? `${project.owner.first_name} ${project.owner.last_name}`
                                        : ""
                                }}
                            </span>
                        </div>

                        <!-- Description + tags (compact block) -->
                        <div class="mt-3 space-y-2">
                            <p
                                v-if="project.description"
                                class="line-clamp-3 text-sm leading-snug text-gray-600 dark:text-gray-400"
                            >
                                {{ project.description }}
                            </p>
                            <p
                                v-else
                                class="text-sm leading-snug text-gray-400 dark:text-gray-500"
                            >
                                No description yet.
                            </p>
                            <div v-if="project.tags && project.tags.length">
                                <Tag size="sm" :tags="project.tags" />
                            </div>
                        </div>
                    </div>

                    <!-- Footer meta -->
                    <div
                        class="border-t border-gray-100 bg-gray-50/80 px-4 py-3 sm:px-5 dark:border-gray-800 dark:bg-gray-900/40"
                    >
                        <div
                            class="flex flex-row flex-wrap items-center justify-between gap-x-4 gap-y-2"
                        >
                            <ShowProjectDates
                                class="min-w-0 flex-1"
                                :release_date="project.release_date"
                                :created_at="project.created_at"
                                :updated_at="project.updated_at"
                            />
                            <span
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-medium tabular-nums"
                                :class="
                                    project.is_public
                                        ? 'border-emerald-200/80 bg-emerald-50 text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-200'
                                        : 'border-gray-200 bg-gray-100 text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200'
                                "
                            >
                                <GlobeAltIcon
                                    v-if="project.is_public"
                                    class="h-3.5 w-3.5 shrink-0 text-emerald-600 dark:text-emerald-400"
                                />
                                <LockClosedIcon
                                    v-else
                                    class="h-3.5 w-3.5 shrink-0 text-gray-600 dark:text-gray-400"
                                />
                                {{ project.is_public ? "Public" : "Private" }}
                            </span>
                        </div>
                        <div
                            v-if="
                                !project.is_public &&
                                project.release_date &&
                                project.doi
                            "
                            class="mt-3 flex items-center gap-2 rounded-lg border border-amber-200/90 bg-amber-50 px-3 py-2 text-xs font-medium text-amber-950 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-100"
                        >
                            <span class="font-semibold">Scheduled release</span>
                            <span class="tabular-nums">{{
                                formatRecordTimestamp(project.release_date)
                            }}</span>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
</template>
<script>
import { router } from "@inertiajs/vue3";
import { StarIcon } from "@heroicons/vue/24/solid";
import {
    ArchiveBoxIcon,
    ArrowPathIcon,
    ArrowTopRightOnSquareIcon,
    CheckCircleIcon,
    Cog6ToothIcon,
    DocumentTextIcon,
    GlobeAltIcon,
    LockClosedIcon,
    ShieldExclamationIcon,
    TrashIcon,
    UserCircleIcon,
} from "@heroicons/vue/24/outline";
import Tag from "@/Shared/Tag.vue";
import ShowProjectDates from "@/Shared/ShowProjectDates.vue";
export default {
    components: {
        StarIcon,
        ArrowTopRightOnSquareIcon,
        Cog6ToothIcon,
        ArchiveBoxIcon,
        ArrowPathIcon,
        DocumentTextIcon,
        CheckCircleIcon,
        GlobeAltIcon,
        LockClosedIcon,
        ShieldExclamationIcon,
        TrashIcon,
        UserCircleIcon,
        ShowProjectDates,
        Tag,
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
            if (!project.is_public) {
                if (project.draft_id) {
                    if (project.is_deleted) {
                        return router.visit(
                            this.route("dashboard.projects", [project.id])
                        );
                    } else {
                        if (
                            project.draft.current_step &&
                            project.draft.current_step == 3
                        ) {
                            router.visit("/publish/" + project.draft_id);
                        } else {
                            return router.visit(
                                "/upload?draft_id=" +
                                    project.draft_id +
                                    "&step=" +
                                    project.draft.current_step
                            );
                        }
                    }
                } else {
                    if (project.doi && project.release_date) {
                        return router.visit(
                            this.route("dashboard.projects", [project.id])
                        );
                    } else {
                        alert(
                            "Draft missing. Please contact us at info.nmrxiv@uni-jena.de."
                        );
                    }
                }
            } else {
                return router.visit(
                    this.route("dashboard.projects", [project.id])
                );
            }
        },
        getProjectSummaryLink(project) {
            return route("dashboard.projects", [project.id]);
        },
        getProjectSettingsLink(project) {
            return route("dashboard.project.settings", [project.id]);
        },
        toggleProjectStarred(project) {
            window.axios
                .get(this.route("project.toggle-starred", [project.id]))
                .then(() => {
                    router.reload({ only: ["projects"] });
                })
                .catch(() => {});
        },
        statusRibbonKey(project) {
            return String(project.status ?? "").toLowerCase();
        },
        statusRibbonLabel(project) {
            const labels = {
                deleted: "Deleted",
                embargo: "Embargo",
                draft: "Draft",
                archived: "Archived",
                published: "Published",
                complete: "Complete",
                processing: "Processing",
            };
            const key = this.statusRibbonKey(project);
            if (!project.status) {
                return "";
            }
            return (
                labels[key] ??
                String(project.status)
                    .replace(/_/g, " ")
                    .replace(/\b\w/g, (c) => c.toUpperCase())
            );
        },
        statusRibbonSurface(project) {
            const key = this.statusRibbonKey(project);
            const surfaces = {
                deleted:
                    "bg-red-50 border-red-100/90 dark:bg-red-950/45 dark:border-red-900/55",
                embargo:
                    "bg-amber-50 border-amber-100 dark:bg-amber-950/45 dark:border-amber-900/55",
                draft: "bg-rose-50 border-rose-100 dark:bg-rose-950/45 dark:border-rose-900/55",
                archived:
                    "bg-gray-100 border-gray-200 dark:bg-gray-800 dark:border-gray-700",
                published:
                    "bg-emerald-50 border-emerald-100 dark:bg-emerald-950/45 dark:border-emerald-900/55",
                complete:
                    "bg-emerald-50 border-emerald-100 dark:bg-emerald-950/45 dark:border-emerald-900/55",
                processing:
                    "bg-sky-50 border-sky-100 dark:bg-sky-950/45 dark:border-sky-900/55",
            };
            return (
                surfaces[key] ??
                "bg-gray-50 border-gray-200 dark:bg-gray-900 dark:border-gray-700"
            );
        },
        statusRibbonIcon(project) {
            const key = this.statusRibbonKey(project);
            const icons = {
                deleted: TrashIcon,
                embargo: ShieldExclamationIcon,
                draft: DocumentTextIcon,
                archived: ArchiveBoxIcon,
                published: CheckCircleIcon,
                complete: CheckCircleIcon,
                processing: ArrowPathIcon,
            };
            return icons[key] ?? DocumentTextIcon;
        },
        statusRibbonIconTone(project) {
            const key = this.statusRibbonKey(project);
            const tones = {
                deleted: "text-red-700 dark:text-red-300",
                embargo: "text-amber-800 dark:text-amber-300",
                draft: "text-rose-800 dark:text-rose-300",
                archived: "text-gray-700 dark:text-gray-300",
                published: "text-emerald-700 dark:text-emerald-300",
                complete: "text-emerald-700 dark:text-emerald-300",
                processing: "text-sky-700 dark:text-sky-300",
            };
            return (
                tones[key] ?? "text-gray-800 dark:text-gray-200"
            );
        },
        statusRibbonTextTone(project) {
            const key = this.statusRibbonKey(project);
            const tones = {
                deleted: "text-red-950 dark:text-red-100",
                embargo: "text-amber-950 dark:text-amber-100",
                draft: "text-rose-950 dark:text-rose-100",
                archived: "text-gray-900 dark:text-gray-100",
                published: "text-emerald-950 dark:text-emerald-100",
                complete: "text-emerald-950 dark:text-emerald-100",
                processing: "text-sky-950 dark:text-sky-100",
            };
            return (
                tones[key] ?? "text-gray-900 dark:text-gray-100"
            );
        },
    },
};
</script>
