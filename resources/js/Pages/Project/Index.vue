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
                        include multiple samples, and each sample is assigned
                        its own URL. To begin uploading projects or samples, use
                        the Submit Data button. For more information, please
                        refer to our
                        <a
                            href="https://docs.nmrxiv.org/introduction/intro"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-indigo-600 hover:text-indigo-500 underline underline-offset-2"
                        >
                            documentation </a
                        >.
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
        <div v-else class="flex w-full min-w-0 flex-col items-stretch gap-5">
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
                    :aria-label="`Project status: ${statusRibbonLabel(
                        project
                    )}`"
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
                                    <StarIcon
                                        class="h-5 w-5"
                                        aria-hidden="true"
                                    />
                                </button>
                                <div
                                    v-if="showProjectSummaryLink(project)"
                                    class="tooltip"
                                >
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
                                <div v-if="!project.is_public" class="tooltip">
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
                                project.owner_id != $page.props.auth.user.id
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
                                No description has been provided.
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
                                :is_published="project.is_public"
                            />
                            <div
                                class="flex shrink-0 items-center gap-2"
                                @click.stop
                            >
                                <button
                                    v-if="
                                        showDashboardShareButton(project) &&
                                        statusRibbonKey(project) !== 'embargo'
                                    "
                                    type="button"
                                    class="inline-flex items-center gap-1 rounded-full border border-gray-200 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 shadow-sm ring-1 ring-black/[0.04] hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:ring-white/10 dark:hover:bg-gray-700/80"
                                    :class="
                                        isDashboardFooterShareOpen(project)
                                            ? 'border-primary-200 bg-primary-50/80 text-primary-900 dark:border-primary-800 dark:bg-primary-950/40 dark:text-primary-100'
                                            : ''
                                    "
                                    :aria-expanded="
                                        isDashboardFooterShareOpen(project)
                                    "
                                    :aria-controls="
                                        'footer-share-doi-' + project.uuid
                                    "
                                    @click="toggleDashboardFooterShare(project)"
                                >
                                    <ShareIcon
                                        class="h-3.5 w-3.5 shrink-0"
                                        aria-hidden="true"
                                    />
                                    Share
                                </button>
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
                                    {{
                                        project.is_public ? "Public" : "Private"
                                    }}
                                </span>
                            </div>
                        </div>
                        <div
                            v-show="isDashboardFooterShareOpen(project)"
                            :id="'footer-share-doi-' + project.uuid"
                            class="mt-2 rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-xs text-gray-900 shadow-sm dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100"
                        >
                            <dl class="grid gap-2.5 text-left">
                                <div v-if="hasDashboardDoiToShare(project)">
                                    <dt
                                        class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400"
                                    >
                                        {{ dashboardDoiHeadingLabel(project) }}
                                    </dt>
                                    <dd class="mt-0.5 min-w-0">
                                        <a
                                            :href="
                                                dashboardDoiLinkHref(project)
                                            "
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-1 break-all font-medium text-primary-700 underline decoration-primary-300 underline-offset-2 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-200"
                                            @click.stop
                                        >
                                            {{ dashboardDoiLinkText(project) }}
                                            <ArrowTopRightOnSquareIcon
                                                class="h-3.5 w-3.5 shrink-0 opacity-80"
                                                aria-hidden="true"
                                            />
                                        </a>
                                    </dd>
                                </div>
                                <div
                                    v-if="showDashboardUserShare(project)"
                                    :class="
                                        hasDashboardDoiToShare(project)
                                            ? 'border-t border-gray-100 pt-2.5 dark:border-gray-800'
                                            : ''
                                    "
                                >
                                    <dt
                                        class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400"
                                    >
                                        People
                                    </dt>
                                    <dd class="mt-1">
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1.5 rounded-md border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-800 shadow-sm hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
                                            @click.stop="
                                                openProjectUserShare(project)
                                            "
                                        >
                                            <UserGroupIcon
                                                class="h-3.5 w-3.5 shrink-0"
                                                aria-hidden="true"
                                            />
                                            {{
                                                project.is_public
                                                    ? "View who has access"
                                                    : "Share with users"
                                            }}
                                        </button>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <div
                            v-if="showScheduledReleaseCard(project)"
                            class="mt-3 rounded-lg border border-amber-200/90 px-3 py-3 text-xs text-amber-950 dark:border-amber-900 dark:text-amber-100"
                            :class="scheduledReleaseCardClass(project)"
                        >
                            <div
                                class="flex flex-wrap items-center justify-between gap-x-2 gap-y-2"
                            >
                                <div
                                    class="min-w-0 flex flex-1 flex-wrap items-baseline gap-x-2 gap-y-1 font-medium"
                                >
                                    <span class="font-semibold"
                                        >Scheduled release</span
                                    >
                                    <span
                                        class="tabular-nums text-amber-900 dark:text-amber-50"
                                    >
                                        {{
                                            formatRecordTimestampUtc(
                                                project.release_date
                                            )
                                        }}
                                    </span>
                                    <span
                                        v-if="project.status === 'embargo'"
                                        class="rounded-full bg-amber-100/90 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-900 ring-1 ring-amber-200/80 dark:bg-amber-900/60 dark:text-amber-100 dark:ring-amber-800"
                                    >
                                        Embargo
                                    </span>
                                </div>
                                <div
                                    class="ml-auto flex shrink-0 items-center gap-1"
                                    @click.stop
                                >
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 rounded-lg border border-amber-200/80 bg-white/90 px-2 py-1.5 text-[11px] font-medium text-amber-950 shadow-sm ring-1 ring-amber-100/80 hover:bg-amber-100/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-1 dark:border-amber-800 dark:bg-amber-900/50 dark:text-amber-100 dark:ring-amber-800/60 dark:hover:bg-amber-900/80"
                                        :aria-expanded="
                                            isScheduledReleaseShareOpen(project)
                                        "
                                        :aria-controls="
                                            'scheduled-release-details-' +
                                            project.uuid
                                        "
                                        :class="
                                            isScheduledReleaseShareOpen(project)
                                                ? 'border-amber-300 bg-amber-100 dark:border-amber-700 dark:bg-amber-900'
                                                : ''
                                        "
                                        @click.stop="
                                            toggleScheduledReleaseShare(project)
                                        "
                                    >
                                        <ShareIcon
                                            class="h-3.5 w-3.5 shrink-0"
                                            aria-hidden="true"
                                        />
                                        Share
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 rounded-lg border border-amber-200/80 bg-white/90 px-2 py-1.5 text-[11px] font-medium text-amber-950 shadow-sm ring-1 ring-amber-100/80 hover:bg-amber-100/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-1 dark:border-amber-800 dark:bg-amber-900/50 dark:text-amber-100 dark:ring-amber-800/60 dark:hover:bg-amber-900/80"
                                        @click.stop="
                                            openReleaseDateModalForProject(
                                                project
                                            )
                                        "
                                    >
                                        <PencilSquareIcon
                                            class="h-3.5 w-3.5 shrink-0"
                                            aria-hidden="true"
                                        />
                                        Edit
                                    </button>
                                </div>
                            </div>
                            <div
                                v-show="isScheduledReleaseShareOpen(project)"
                                class="mt-2 border-t border-amber-200/70 pt-2 dark:border-amber-800/80"
                            >
                                <dl
                                    :id="
                                        'scheduled-release-details-' +
                                        project.uuid
                                    "
                                    class="grid gap-2.5 text-left dark:border-amber-800/80"
                                >
                                    <div v-if="hasDashboardDoiToShare(project)">
                                        <dt
                                            class="text-[11px] font-semibold uppercase tracking-wide text-amber-800/90 dark:text-amber-200/90"
                                        >
                                            {{
                                                dashboardDoiHeadingLabel(
                                                    project
                                                )
                                            }}
                                        </dt>
                                        <dd class="mt-0.5 min-w-0">
                                            <a
                                                :href="
                                                    dashboardDoiLinkHref(
                                                        project
                                                    )
                                                "
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center gap-1 break-all font-medium text-primary-700 underline decoration-primary-300 underline-offset-2 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-200"
                                                @click.stop
                                            >
                                                {{
                                                    dashboardDoiLinkText(
                                                        project
                                                    )
                                                }}
                                                <ArrowTopRightOnSquareIcon
                                                    class="h-3.5 w-3.5 shrink-0 opacity-80"
                                                    aria-hidden="true"
                                                />
                                            </a>
                                        </dd>
                                    </div>
                                    <div
                                        v-if="showDashboardUserShare(project)"
                                        :class="
                                            hasDashboardDoiToShare(project) ||
                                            project.obfuscationcode
                                                ? 'border-t border-amber-200/70 pt-2.5 dark:border-amber-800/80'
                                                : ''
                                        "
                                    >
                                        <dt
                                            class="text-[11px] font-semibold uppercase tracking-wide text-amber-800/90 dark:text-amber-200/90"
                                        >
                                            People
                                        </dt>
                                        <dd class="mt-1">
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1.5 rounded-md border border-amber-200/80 bg-white/90 px-2.5 py-1.5 text-xs font-medium text-amber-950 shadow-sm hover:bg-amber-100/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-1 dark:border-amber-800 dark:bg-amber-900/50 dark:text-amber-100 dark:hover:bg-amber-900/80"
                                                @click.stop="
                                                    openProjectUserShare(
                                                        project
                                                    )
                                                "
                                            >
                                                <UserGroupIcon
                                                    class="h-3.5 w-3.5 shrink-0"
                                                    aria-hidden="true"
                                                />
                                                {{
                                                    project.is_public
                                                        ? "View who has access"
                                                        : "Share with users"
                                                }}
                                            </button>
                                        </dd>
                                    </div>
                                    <div v-if="project.obfuscationcode">
                                        <dt
                                            class="text-[11px] font-semibold uppercase tracking-wide text-amber-800/90 dark:text-amber-200/90"
                                        >
                                            Reviewer access (no login)
                                        </dt>
                                        <dd class="mt-1">
                                            <div
                                                class="flex min-w-0 rounded-md border border-amber-200/80 bg-white/90 shadow-sm dark:border-amber-800 dark:bg-gray-950/50"
                                            >
                                                <input
                                                    type="text"
                                                    readonly
                                                    class="min-w-0 flex-1 truncate border-0 bg-transparent px-2 py-1.5 text-[11px] text-gray-800 focus:ring-0 dark:text-gray-200 rounded-l-md"
                                                    :value="
                                                        reviewerPreviewUrl(
                                                            project
                                                        )
                                                    "
                                                    :aria-label="
                                                        'Reviewer preview URL for ' +
                                                        project.name
                                                    "
                                                    @focus="
                                                        $event.target.select()
                                                    "
                                                    @click.stop
                                                />
                                                <button
                                                    type="button"
                                                    class="inline-flex shrink-0 items-center justify-center border-l border-amber-200/80 bg-amber-100/50 px-2 py-1.5 text-amber-950 hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-900/40 dark:text-amber-100 dark:hover:bg-amber-900/70"
                                                    :aria-label="
                                                        'Open reviewer preview for ' +
                                                        project.name +
                                                        ' in a new tab'
                                                    "
                                                    @click.stop="
                                                        openReviewerPreviewInNewTab(
                                                            project
                                                        )
                                                    "
                                                >
                                                    <ArrowTopRightOnSquareIcon
                                                        class="h-3.5 w-3.5"
                                                        aria-hidden="true"
                                                    />
                                                </button>
                                                <button
                                                    type="button"
                                                    class="inline-flex shrink-0 items-center gap-1 border-l border-amber-200/80 bg-amber-100/50 px-2 py-1.5 text-[11px] font-medium text-amber-950 hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-900/40 dark:text-amber-100 dark:hover:bg-amber-900/70 rounded-r-md"
                                                    @click.stop="
                                                        copyText(
                                                            reviewerPreviewUrl(
                                                                project
                                                            )
                                                        )
                                                    "
                                                >
                                                    <ClipboardDocumentIcon
                                                        class="h-3.5 w-3.5"
                                                        aria-hidden="true"
                                                    />
                                                    Copy
                                                </button>
                                            </div>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
        <access-dialogue
            v-if="activeUserShareProject"
            :key="activeUserShareProject.uuid"
            ref="projectUserShareDialogue"
            hide-trigger
            :available-roles="availableRoles"
            :role="activeUserShareProject.viewer_role"
            :team="projectShareTeam(activeUserShareProject)"
            :members="projectMembers(activeUserShareProject)"
            :project="activeUserShareProject"
            called-from="projectView"
            model="project"
            @sharing-updated="syncActiveUserShareProject"
        />
    </div>
</template>
<script>
import { router } from "@inertiajs/vue3";
import { StarIcon, ShareIcon, PencilSquareIcon } from "@heroicons/vue/24/solid";
import {
    ArchiveBoxIcon,
    ArrowPathIcon,
    ArrowTopRightOnSquareIcon,
    CheckCircleIcon,
    ClipboardDocumentIcon,
    Cog6ToothIcon,
    DocumentTextIcon,
    GlobeAltIcon,
    LockClosedIcon,
    ShieldExclamationIcon,
    TrashIcon,
    UserCircleIcon,
    UserGroupIcon,
} from "@heroicons/vue/24/outline";
import Tag from "@/Shared/Tag.vue";
import ShowProjectDates from "@/Shared/ShowProjectDates.vue";
import AccessDialogue from "@/Shared/AccessDialogue.vue";
export default {
    components: {
        StarIcon,
        ShareIcon,
        PencilSquareIcon,
        ArrowTopRightOnSquareIcon,
        ClipboardDocumentIcon,
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
        UserGroupIcon,
        ShowProjectDates,
        Tag,
        AccessDialogue,
    },
    props: ["projects", "mode", "teamRole", "team"],
    setup() {},
    data() {
        return {
            /** UUID → whether share details (DOI / provisional DOI, reviewer URL) are expanded */
            scheduledReleaseShareOpenByUuid: {},
            /** UUID → whether dashboard footer “Share” (DOI / provisional DOI) is expanded */
            dashboardFooterShareOpenByUuid: {},
            /** Project whose “Share with users” modal is open */
            activeUserShareProject: null,
        };
    },
    computed: {
        availableRoles() {
            return this.$page.props.availableRoles ?? [];
        },
    },
    watch: {
        projects: {
            deep: true,
            handler() {
                this.syncActiveUserShareProject();
            },
        },
    },
    methods: {
        syncActiveUserShareProject() {
            if (!this.activeUserShareProject) {
                return;
            }

            const fresh = this.projects.find(
                (project) => project.id === this.activeUserShareProject.id
            );

            if (fresh) {
                this.activeUserShareProject = fresh;
            }
        },
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
                        return router.visit(this.projectHomeHref(project));
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
                        return router.visit(this.projectHomeHref(project));
                    } else {
                        alert(
                            "Draft missing. Please contact us at info.nmrxiv@uni-jena.de."
                        );
                    }
                }
            } else {
                return router.visit(this.projectHomeHref(project));
            }
        },
        projectHomeHref(project) {
            if (project?.identifier) {
                return project.public_url;
            }

            return this.route("dashboard.projects", [project.id]);
        },
        showProjectSummaryLink(project) {
            return this.statusRibbonKey(project) !== "draft";
        },
        getProjectSummaryLink(project) {
            return this.projectHomeHref(project);
        },
        getProjectSettingsLink(project) {
            return route("dashboard.project.settings", [project.id]);
        },
        /**
         * Private project with a future release shown as embargo on the dashboard ribbon.
         */
        showScheduledReleaseCard(project) {
            if (!project || project.is_public || !project.release_date) {
                return false;
            }

            return this.statusRibbonKey(project) === "embargo";
        },
        isScheduledReleaseOverdue(project) {
            const releaseDate = new Date(project?.release_date);

            return (
                !Number.isNaN(releaseDate.getTime()) &&
                releaseDate.getTime() <= Date.now()
            );
        },
        scheduledReleaseCardClass(project) {
            if (this.isScheduledReleaseOverdue(project)) {
                return "bg-red-50 dark:bg-red-950/40";
            }

            return "bg-amber-50 dark:bg-amber-950/40";
        },
        isScheduledReleaseShareOpen(project) {
            return !!this.scheduledReleaseShareOpenByUuid[project.uuid];
        },
        toggleScheduledReleaseShare(project) {
            const id = project.uuid;
            const next = !this.scheduledReleaseShareOpenByUuid[id];

            this.scheduledReleaseShareOpenByUuid = {
                ...this.scheduledReleaseShareOpenByUuid,
                [id]: next,
            };
        },
        showFooterProvisionalDoiShare(project) {
            return this.hasDashboardDoiToShare(project);
        },
        showDashboardUserShare(project) {
            return Boolean(project && !project.is_deleted);
        },
        showDashboardShareButton(project) {
            return (
                this.showFooterProvisionalDoiShare(project) ||
                this.showDashboardUserShare(project)
            );
        },
        projectMembers(project) {
            return project?.users ?? [];
        },
        projectShareTeam(project) {
            const projectTeam = project?.team;
            if (projectTeam && !projectTeam.personal_team) {
                return projectTeam;
            }

            if (this.team && !this.team.personal_team) {
                return this.team;
            }

            return null;
        },
        openProjectUserShare(project) {
            this.activeUserShareProject = project;
            this.$nextTick(() => {
                this.$refs.projectUserShareDialogue?.openDialog();
            });
        },
        hasDashboardDoiToShare(project) {
            const hasProvisional =
                project?.provisional_doi && project?.provisional_doi_url;
            const hasPublishedDoi = project?.is_published && project?.doi;

            return !!(hasProvisional || hasPublishedDoi);
        },
        dashboardDoiHeadingLabel(project) {
            return project?.is_published ? "DOI" : "Provisional DOI";
        },
        dashboardDoiLinkHref(project) {
            if (project?.is_published && project?.doi) {
                const raw = String(project.doi).trim();

                if (/^https?:\/\//i.test(raw)) {
                    return raw;
                }

                return `https://doi.org/${raw}`;
            }

            return project?.provisional_doi_url ?? "#";
        },
        dashboardDoiLinkText(project) {
            if (project?.is_published && project?.doi) {
                return String(project.doi)
                    .trim()
                    .replace(/^https?:\/\/doi\.org\//i, "");
            }

            return project?.provisional_doi ?? "";
        },
        isDashboardFooterShareOpen(project) {
            return !!this.dashboardFooterShareOpenByUuid[project.uuid];
        },
        toggleDashboardFooterShare(project) {
            const id = project.uuid;
            const next = !this.dashboardFooterShareOpenByUuid[id];

            this.dashboardFooterShareOpenByUuid = {
                ...this.dashboardFooterShareOpenByUuid,
                [id]: next,
            };
        },
        openReleaseDateModalForProject(project) {
            const url = this.projectHomeHref(project) + "?edit=release_date";
            window.open(url, "_blank");
        },
        reviewerPreviewUrl(project) {
            if (!project?.obfuscationcode) {
                return "";
            }

            return this.route("project.preview", [project.obfuscationcode]);
        },
        openReviewerPreviewInNewTab(project) {
            const url = this.reviewerPreviewUrl(project);
            if (!url) {
                return;
            }
            window.open(url, "_blank");
        },
        async copyText(text) {
            if (!text) {
                return;
            }

            try {
                await navigator.clipboard.writeText(text);
            } catch {
                try {
                    const el = document.createElement("textarea");
                    el.value = text;
                    el.setAttribute("readonly", "");
                    el.style.position = "fixed";
                    el.style.left = "-9999px";
                    document.body.appendChild(el);
                    el.select();
                    document.execCommand("copy");
                    document.body.removeChild(el);
                } catch {
                    /* ignore */
                }
            }
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
            return tones[key] ?? "text-gray-800 dark:text-gray-200";
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
            return tones[key] ?? "text-gray-900 dark:text-gray-100";
        },
    },
};
</script>
