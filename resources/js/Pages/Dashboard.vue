<template>
    <app-layout title="Dashboard">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="flex flex-nowrap justify-between py-6">
                        <div>
                            <div
                                class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"
                            >
                                <div v-if="team.personal_team">Your</div>
                                <div v-else>
                                    {{ user.current_team.name }}
                                </div>
                                &nbsp;Dashboard
                            </div>
                            <div
                                v-if="team.users"
                                class="flex mt-3 flex-row-reverse justify-end"
                            >
                                <img
                                    v-for="user in team.users"
                                    :key="user.id"
                                    class="w-8 h-8 -mr-2 rounded-full border-2 border-white"
                                    :src="user.profile_photo_url"
                                    :alt="user.name"
                                />
                                <img
                                    class="w-8 h-8 -mr-2 rounded-full border-2 border-white"
                                    :src="team.owner.profile_photo_url"
                                    :alt="team.owner.name"
                                />
                            </div>
                        </div>
                        <div v-if="!team.personal_team">
                            <Link
                                :href="'/teams/' + user.current_team.id"
                                class="text-sm text-gray-800 font-bold"
                            >
                                Team Settings
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div
            v-if="showDashboardLists"
            class="mx-auto w-full max-w-5xl min-w-0 px-4 pb-10 pt-6 sm:px-6 lg:px-8"
        >
            <!-- Workspace filters: Shared / Recent / Starred / Trash -->
            <section
                v-if="isWorkspaceView"
                class="flex w-full min-w-0 flex-col gap-8"
                :aria-labelledby="workspaceSectionHeadingId"
            >
                <header>
                    <h2
                        :id="workspaceSectionHeadingId"
                        class="text-lg font-semibold tracking-tight text-gray-900"
                    >
                        {{ workspaceCopy.title }}
                    </h2>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        {{ workspaceCopy.description }}
                    </p>
                </header>

                <div class="w-full min-w-0">
                    <team-projects
                        class="block w-full min-w-0 max-w-none"
                        :team="team"
                        :team-role="teamRole"
                        mode="listing"
                        :projects="workspaceProjects"
                    >
                        <template #emptyText>
                            <div
                                class="rounded-xl border border-gray-200/90 bg-gray-50/70 px-8 py-16 text-center dark:border-gray-800 dark:bg-gray-950/35"
                                role="status"
                                aria-live="polite"
                            >
                                <component
                                    :is="workspaceCopy.emptyIcon"
                                    class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
                                    aria-hidden="true"
                                />
                                <h3
                                    class="mt-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-gray-100"
                                >
                                    {{ workspaceCopy.emptyProjectsTitle }}
                                </h3>
                                <p
                                    class="mx-auto mt-3 max-w-lg text-sm leading-relaxed text-gray-600 dark:text-gray-400"
                                >
                                    {{ workspaceCopy.emptyProjectsBody }}
                                </p>
                            </div>
                        </template>
                    </team-projects>
                </div>

                <div
                    v-if="workspaceStudies.length > 0"
                    class="w-full min-w-0 border-t border-gray-100 pt-8"
                >
                    <h3 class="text-lg font-semibold text-gray-900">
                        Compound library
                    </h3>
                    <div
                        class="mx-auto mt-4 grid max-w-md gap-8 sm:max-w-lg lg:max-w-7xl lg:grid-cols-3"
                    >
                        <div
                            v-for="study in workspaceStudies"
                            :key="study.uuid"
                        >
                            <Link
                                :href="route('dashboard.studies', [study.id])"
                            >
                                <study-card :study="study" />
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <template v-else>
                <!-- Primary navigation: projects vs compound library -->
                <div class="mb-8">
                    <div class="sm:hidden">
                        <label for="dashboard-tab-select" class="sr-only"
                            >Choose projects or compound library</label
                        >
                        <select
                            id="dashboard-tab-select"
                            name="tabs"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            :value="selectedTab"
                            @change="onTabSelect($event)"
                        >
                            <option value="projects">Projects</option>
                            <option value="samples">Compound library</option>
                        </select>
                    </div>
                    <div
                        class="hidden sm:flex sm:items-center sm:justify-between sm:gap-4"
                    >
                        <div
                            class="inline-flex rounded-lg bg-gray-100 p-1 ring-1 ring-inset ring-gray-200/80"
                            role="tablist"
                            aria-label="Projects and compound library"
                        >
                            <Link
                                :href="dashboardUrl({ tab: 'projects' })"
                                role="tab"
                                :class="[
                                    selectedTab === 'projects'
                                        ? 'bg-white text-gray-900 shadow-sm ring-1 ring-gray-200'
                                        : 'text-gray-600 hover:bg-white/60 hover:text-gray-900',
                                    'rounded-lg px-5 py-2.5 text-sm font-medium transition-colors',
                                ]"
                                :aria-current="
                                    selectedTab === 'projects'
                                        ? 'page'
                                        : undefined
                                "
                            >
                                Projects
                            </Link>
                            <Link
                                :href="dashboardUrl({ tab: 'samples' })"
                                role="tab"
                                :class="[
                                    selectedTab === 'samples'
                                        ? 'bg-white text-gray-900 shadow-sm ring-1 ring-gray-200'
                                        : 'text-gray-600 hover:bg-white/60 hover:text-gray-900',
                                    'rounded-lg px-5 py-2.5 text-sm font-medium transition-colors',
                                ]"
                                :aria-current="
                                    selectedTab === 'samples'
                                        ? 'page'
                                        : undefined
                                "
                            >
                                Compound library
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Projects -->
                <section
                    v-if="selectedTab === 'projects'"
                    aria-labelledby="dashboard-projects-heading"
                    class="flex w-full min-w-0 flex-col gap-6"
                >
                    <header>
                        <h2
                            id="dashboard-projects-heading"
                            class="text-lg font-semibold tracking-tight text-gray-900"
                        >
                            Projects
                        </h2>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Search by name or description, narrow by status,
                            then open a project to manage compounds, spectra,
                            and files.
                        </p>
                    </header>

                    <div
                        v-if="hasProjects"
                        class="flex w-full min-w-0 flex-col gap-6"
                    >
                        <div
                            class="flex flex-nowrap items-center gap-2 sm:gap-3"
                        >
                            <div class="min-w-0 flex-1">
                                <label
                                    for="dashboard-projects-search"
                                    class="sr-only"
                                    >Search projects by name, description, or
                                    ID</label
                                >
                                <SearchInput
                                    input-id="dashboard-projects-search"
                                    :model-value="filters.projects_q"
                                    :filters-active="
                                        filters.projects_status !== 'all'
                                    "
                                    name="project-search"
                                    placeholder="Search by name, description, or ID…"
                                    @update:model-value="onProjectsSearchInput"
                                    @reset="clearProjectFilters"
                                />
                            </div>
                            <div
                                class="flex shrink-0 items-center border-l border-gray-200 pl-2 sm:pl-3"
                            >
                                <span class="sr-only">Filter by status</span>
                                <StatusFilter
                                    :model-value="filters.projects_status"
                                    @update:model-value="onProjectsStatus"
                                />
                            </div>
                        </div>

                        <div
                            v-if="projects.total > 0"
                            class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 border-b border-gray-100 py-2"
                        >
                            <p
                                class="text-sm text-gray-600"
                                role="status"
                                aria-live="polite"
                            >
                                Showing
                                <span class="tabular-nums">{{
                                    projects.from
                                }}</span>
                                –
                                <span class="tabular-nums">{{
                                    projects.to
                                }}</span>
                                of
                                <span class="tabular-nums">{{
                                    projects.total
                                }}</span>
                                {{
                                    filters.projects_status !== "all"
                                        ? " " + filters.projects_status + " "
                                        : " "
                                }}projects
                                <span
                                    v-if="projects.last_page > 1"
                                    class="text-gray-500"
                                >
                                    · Page {{ projects.current_page }} of
                                    {{ projects.last_page }}
                                </span>
                            </p>
                            <div
                                v-if="showProjectsPerPageSelect"
                                class="flex shrink-0 items-center sm:ml-auto"
                            >
                                <select
                                    id="dashboard-projects-per-page-top"
                                    aria-label="Rows per page"
                                    class="block rounded-lg border-gray-300 py-1.5 pl-2.5 pr-8 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:min-w-[5.5rem]"
                                    :value="
                                        Number(filters.projects_per_page) || 10
                                    "
                                    @change="onProjectsPerPageChange"
                                >
                                    <option
                                        v-for="n in perPageOptions"
                                        :key="'proj-top-' + n"
                                        :value="n"
                                    >
                                        {{ n }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div v-if="projects.total > 0" class="w-full min-w-0">
                            <team-projects
                                class="block w-full min-w-0 max-w-none"
                                :team="team"
                                :team-role="teamRole"
                                mode="create"
                                :projects="projects.data"
                            />
                        </div>

                        <div
                            v-if="
                                projects.total === 0 &&
                                (filters.projects_q ||
                                    filters.projects_status !== 'all')
                            "
                            class="w-full min-w-0 py-12 text-center"
                        >
                            <EmptySearchState
                                entity-type="projects"
                                :search-query="
                                    filters.projects_q ||
                                    (filters.projects_status !== 'all'
                                        ? `status: ${filters.projects_status}`
                                        : '')
                                "
                                :title="
                                    filters.projects_q
                                        ? 'No projects match your search'
                                        : filters.projects_status !== 'all'
                                        ? 'No projects with this status'
                                        : 'No projects to show'
                                "
                                :message="
                                    filters.projects_q
                                        ? 'Try different keywords or spelling. You can also clear the search to browse all projects in this workspace.'
                                        : filters.projects_status !== 'all'
                                        ? 'Try another status filter, or reset filters to see every project you have access to.'
                                        : undefined
                                "
                                @clear-search="clearProjectFilters"
                            />
                        </div>

                        <div
                            v-if="
                                projects.total > 0 &&
                                (showProjectsPerPageSelect ||
                                    projects.last_page > 1)
                            "
                            class="flex flex-col gap-4 border-t border-gray-100 pt-5 sm:flex-row-reverse sm:items-end sm:justify-between sm:gap-6"
                        >
                            <div
                                v-if="showProjectsPerPageSelect"
                                class="flex w-full shrink-0 items-center justify-end sm:w-auto"
                                :class="
                                    projects.last_page > 1 ? '' : 'sm:ml-auto'
                                "
                            >
                                <select
                                    id="dashboard-projects-per-page-bottom"
                                    aria-label="Rows per page"
                                    class="block w-full rounded-lg border-gray-300 py-2 pl-3 pr-10 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-auto sm:min-w-[5.5rem]"
                                    :value="
                                        Number(filters.projects_per_page) || 10
                                    "
                                    @change="onProjectsPerPageChange"
                                >
                                    <option
                                        v-for="n in perPageOptions"
                                        :key="'proj-' + n"
                                        :value="n"
                                    >
                                        {{ n }}
                                    </option>
                                </select>
                            </div>
                            <div
                                v-if="projects.last_page > 1"
                                class="min-w-0 flex-1"
                            >
                                <Pagination
                                    :links="projects.links"
                                    navigation-label="Projects pagination"
                                />
                            </div>
                        </div>
                    </div>

                    <div v-else class="w-full min-w-0">
                        <team-projects
                            class="block w-full min-w-0 max-w-none"
                            :team="team"
                            :team-role="teamRole"
                            mode="create"
                            :projects="[]"
                        />
                    </div>
                </section>

                <!-- Compound library: grid of compound cards -->
                <section
                    v-if="selectedTab === 'samples'"
                    aria-labelledby="dashboard-samples-heading"
                    class="flex w-full min-w-0 flex-col gap-6"
                >
                    <header>
                        <h2
                            id="dashboard-samples-heading"
                            class="text-lg font-semibold tracking-tight text-gray-900"
                        >
                            Compound library
                        </h2>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Every compound record in this workspace, including
                            entries inside projects and standalone submissions.
                            Search, filter by visibility, then open a record to
                            continue.
                        </p>
                    </header>

                    <div
                        v-if="hasSamples"
                        class="flex w-full min-w-0 flex-col gap-6"
                    >
                        <div
                            class="flex flex-nowrap items-center gap-2 sm:gap-3"
                        >
                            <div class="min-w-0 flex-1">
                                <label
                                    for="dashboard-samples-search"
                                    class="sr-only"
                                    >Search compound library by name,
                                    description, or ID</label
                                >
                                <SearchInput
                                    input-id="dashboard-samples-search"
                                    :model-value="filters.samples_q"
                                    :filters-active="
                                        filters.samples_status !== 'all'
                                    "
                                    name="compound-library-search"
                                    placeholder="Search by name, description, or ID…"
                                    @update:model-value="onSamplesSearchInput"
                                    @reset="clearSampleFilters"
                                />
                            </div>
                            <div
                                class="flex shrink-0 items-center border-l border-gray-200 pl-2 sm:pl-3"
                            >
                                <span class="sr-only"
                                    >Filter by visibility</span
                                >
                                <StatusFilter
                                    variant="visibility"
                                    :model-value="filters.samples_status"
                                    @update:model-value="onSamplesStatus"
                                />
                            </div>
                        </div>

                        <div
                            v-if="samples.total > 0"
                            class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 border-b border-gray-100 py-2"
                        >
                            <p
                                class="text-sm text-gray-600"
                                role="status"
                                aria-live="polite"
                            >
                                Showing
                                <span class="tabular-nums">{{
                                    samples.from
                                }}</span>
                                –
                                <span class="tabular-nums">{{
                                    samples.to
                                }}</span>
                                of
                                <span class="tabular-nums">{{
                                    samples.total
                                }}</span>
                                <template
                                    v-if="filters.samples_status === 'public'"
                                >
                                    public
                                </template>
                                <template
                                    v-else-if="
                                        filters.samples_status === 'private'
                                    "
                                >
                                    private
                                </template>
                                compounds
                                <span
                                    v-if="samples.last_page > 1"
                                    class="text-gray-500"
                                >
                                    · Page {{ samples.current_page }} of
                                    {{ samples.last_page }}
                                </span>
                            </p>
                            <div
                                v-if="showSamplesPerPageSelect"
                                class="flex shrink-0 items-center sm:ml-auto"
                            >
                                <select
                                    id="dashboard-samples-per-page-top"
                                    aria-label="Rows per page"
                                    class="block rounded-lg border-gray-300 py-1.5 pl-2.5 pr-8 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:min-w-[5.5rem]"
                                    :value="
                                        Number(filters.samples_per_page) || 12
                                    "
                                    @change="onSamplesPerPageChange"
                                >
                                    <option
                                        v-for="n in samplesPerPageOptions"
                                        :key="'samp-top-' + n"
                                        :value="n"
                                    >
                                        {{ n }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div v-if="samples.total > 0" class="w-full min-w-0">
                            <compound-cards
                                class="block w-full min-w-0 max-w-none"
                                :team="team"
                                :team-role="teamRole"
                                mode="create"
                                :studies="samples.data"
                            />
                        </div>

                        <div
                            v-if="
                                samples.total === 0 &&
                                (filters.samples_q ||
                                    filters.samples_status !== 'all')
                            "
                            class="w-full min-w-0 py-12 text-center"
                        >
                            <EmptySearchState
                                entity-type="samples"
                                :search-query="
                                    filters.samples_q ||
                                    (filters.samples_status !== 'all'
                                        ? `visibility: ${filters.samples_status}`
                                        : '')
                                "
                                :title="
                                    filters.samples_q
                                        ? 'No compounds match your search'
                                        : filters.samples_status !== 'all'
                                        ? filters.samples_status === 'public'
                                            ? 'No public compounds'
                                            : 'No private compounds'
                                        : 'Nothing to show'
                                "
                                :message="
                                    filters.samples_q
                                        ? 'Try different keywords or spelling. You can also clear the search to browse your full compound library in this workspace.'
                                        : filters.samples_status !== 'all'
                                        ? 'Try another visibility filter, or reset filters to see every compound in this workspace.'
                                        : undefined
                                "
                                @clear-search="clearSampleFilters"
                            />
                        </div>

                        <div
                            v-if="
                                samples.total > 0 &&
                                (showSamplesPerPageSelect ||
                                    samples.last_page > 1)
                            "
                            class="flex flex-col gap-4 border-t border-gray-100 pt-5 sm:flex-row-reverse sm:items-end sm:justify-between sm:gap-6"
                        >
                            <div
                                v-if="showSamplesPerPageSelect"
                                class="flex w-full shrink-0 items-center justify-end sm:w-auto"
                                :class="
                                    samples.last_page > 1 ? '' : 'sm:ml-auto'
                                "
                            >
                                <select
                                    id="dashboard-samples-per-page-bottom"
                                    aria-label="Rows per page"
                                    class="block w-full rounded-lg border-gray-300 py-2 pl-3 pr-10 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-auto sm:min-w-[5.5rem]"
                                    :value="
                                        Number(filters.samples_per_page) || 12
                                    "
                                    @change="onSamplesPerPageChange"
                                >
                                    <option
                                        v-for="n in samplesPerPageOptions"
                                        :key="'samp-' + n"
                                        :value="n"
                                    >
                                        {{ n }}
                                    </option>
                                </select>
                            </div>
                            <div
                                v-if="samples.last_page > 1"
                                class="min-w-0 flex-1"
                            >
                                <Pagination
                                    :links="samples.links"
                                    navigation-label="Compound library pagination"
                                />
                            </div>
                        </div>
                    </div>

                    <div v-else class="w-full min-w-0">
                        <compound-cards
                            class="block w-full min-w-0 max-w-none"
                            :team="team"
                            :team-role="teamRole"
                            mode="create"
                            :studies="[]"
                        />
                    </div>
                </section>
            </template>
        </div>
        <div v-else>
            <div
                class="mx-auto w-full max-w-5xl min-w-0 px-4 pb-10 pt-6 sm:px-6 lg:px-8"
            >
                <div
                    class="rounded-xl border border-gray-200/90 bg-gray-50/60 px-8 py-14 text-center dark:border-gray-800 dark:bg-gray-950/40"
                >
                    <svg
                        class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            vector-effect="non-scaling-stroke"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                        />
                    </svg>
                    <h3
                        class="mt-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-gray-100"
                    >
                        Your workspace is empty
                    </h3>
                    <p
                        class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-gray-600 dark:text-gray-400"
                    >
                        Projects and compounds will appear here once you begin a
                        submission. Use Upload to add datasets and metadata to
                        nmrXiv.
                    </p>
                    <div v-if="editableTeamRole" class="mt-8 space-y-4">
                        <create class="inline-flex" mode="button"></create>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <a
                                href="https://docs.nmrxiv.org/submission-guides/submission-process.html"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="font-medium text-indigo-600 underline decoration-indigo-600/30 underline-offset-2 transition-colors hover:text-indigo-800 hover:decoration-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300"
                                >Submission guide</a
                            >
                            — step-by-step help for preparing your data.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mx-auto w-full max-w-5xl min-w-0 px-4 py-8 sm:px-6 lg:px-8">
            <ul
                role="list"
                class="mt-6 border-b border-gray-200 divide-y divide-gray-200"
            >
                <li>
                    <div class="relative group py-4 flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <span
                                class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-pink-500"
                            >
                                <!-- Heroicon name: outline/speakerphone -->
                                <svg
                                    class="h-6 w-6 text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                                    />
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900">
                                <a
                                    id="tour-step-submission-guide"
                                    href="https://docs.nmrxiv.org/introduction/intro"
                                    target="_blank"
                                >
                                    <span
                                        class="absolute inset-0"
                                        aria-hidden="true"
                                    ></span>
                                    Get started! How to use nmrXiv?
                                </a>
                            </div>
                            <p class="text-sm text-gray-500">
                                Documentation for using nmrXiv. Explore, learn
                                and archive NMR datasets.
                            </p>
                        </div>
                        <div class="flex-shrink-0 self-center">
                            <!-- Heroicon name: solid/chevron-right -->
                            <svg
                                class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="relative group py-4 flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <span
                                class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-purple-500"
                            >
                                <!-- Heroicon name: outline/terminal -->
                                <svg
                                    class="h-6 w-6 text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900">
                                <a
                                    id="tour-step-api"
                                    href="https://docs.nmrxiv.org/developer-guides/api.html"
                                    target="_blank"
                                >
                                    <span
                                        class="absolute inset-0"
                                        aria-hidden="true"
                                    ></span>
                                    Public API Documentation
                                </a>
                            </div>
                            <p class="text-sm text-gray-500">
                                Search, interact and download NMR Datasets as a
                                part of your software or your data science
                                workflow.
                            </p>
                        </div>
                        <div class="flex-shrink-0 self-center">
                            <!-- Heroicon name: solid/chevron-right -->
                            <svg
                                class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="relative group py-4 flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <span
                                class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-yellow-500"
                            >
                                <!-- Heroicon name: outline/calendar -->
                                <svg
                                    class="h-6 w-6 text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900">
                                <a id="tour-step-spectra-challenge" href="/">
                                    <span
                                        class="absolute inset-0"
                                        aria-hidden="true"
                                    ></span>
                                    Challenges
                                </a>
                            </div>
                            <p class="text-sm text-gray-500">
                                Structure elucidation challenges are designed
                                for researchers at all different stages of their
                                careers.
                            </p>
                        </div>
                        <div class="flex-shrink-0 self-center">
                            <!-- Heroicon name: solid/chevron-right -->
                            <svg
                                class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="mt-6 flex">
                <a
                    id="tour-step-get-in-touch"
                    :href="mailTo"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                    >Or get in touch<span aria-hidden="true"> &rarr;</span></a
                >
            </div>
        </div>
        <onboarding></onboarding>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import TeamProjects from "@/Pages/Project/Index.vue";
import CompoundCards from "@/Shared/CompoundCards.vue";
import Create from "@/Shared/CreateButton.vue";
import Onboarding from "@/App/Onboarding.vue";
import SearchInput from "@/Shared/SearchInput.vue";
import EmptySearchState from "@/Shared/EmptySearchState.vue";
import StatusFilter from "@/Shared/StatusFilter.vue";
import Pagination from "@/Shared/Pagination.vue";
import StudyCard from "@/Shared/StudyCard.vue";
import {
    ClockIcon,
    StarIcon,
    TrashIcon,
    UserGroupIcon,
} from "@heroicons/vue/24/outline";
import { useMagicKeys } from "@vueuse/core";
import { getCurrentInstance } from "vue";
import { watchEffect } from "vue";
import { Link, router } from "@inertiajs/vue3";

const { meta, u } = useMagicKeys();

export default {
    components: {
        AppLayout,
        TeamProjects,
        CompoundCards,
        Create,
        Onboarding,
        SearchInput,
        EmptySearchState,
        StatusFilter,
        Pagination,
        StudyCard,
        Link,
        UserGroupIcon,
        ClockIcon,
        StarIcon,
        TrashIcon,
    },
    props: {
        user: {
            type: Object,
            required: true,
        },
        team: {
            type: Object,
            default: null,
        },
        projects: {
            type: Object,
            required: true,
        },
        samples: {
            type: Object,
            required: true,
        },
        teamRole: {
            type: [String, Object],
            default: null,
        },
        filters: {
            type: Object,
            required: true,
        },
        hasProjects: {
            type: Boolean,
            default: false,
        },
        hasSamples: {
            type: Boolean,
            default: false,
        },
        workspaceProjects: {
            type: Array,
            default: () => [],
        },
        workspaceStudies: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            projectSearchTimer: null,
            sampleSearchTimer: null,
            perPageOptions: [5, 10, 25, 50],
            /** Compound library tab: page size options (default 12). */
            samplesPerPageOptions: [12, 24, 36, 48, 50],
        };
    },
    setup() {
        const app = getCurrentInstance();
        const openDatasetCreateDialog = (data) => {
            app.appContext.config.globalProperties.emitter.emit(
                "openDatasetCreateDialog",
                data
            );
        };
        watchEffect(() => {
            if (meta.value && u.value) {
                openDatasetCreateDialog({
                    draft_id: null,
                });
            }
        });

        return {
            openDatasetCreateDialog,
        };
    },
    computed: {
        mailFromAddress() {
            return String(this.$page.props.mailFromAddress);
        },

        mailTo() {
            return "mailto:" + String(this.$page.props.mailFromAddress);
        },

        selectedTab() {
            return this.filters.tab === "samples" ? "samples" : "projects";
        },

        editableTeamRole() {
            return (
                this.teamRole &&
                (this.teamRole == "owner" || this.teamRole == "admin")
            );
        },

        showDashboardLists() {
            if (!this.team) {
                return false;
            }
            return this.hasProjects || this.hasSamples || this.isWorkspaceView;
        },

        isWorkspaceView() {
            const w = this.filters.workspace || "default";

            return ["shared", "recent", "starred", "trashed"].includes(w);
        },

        workspaceSectionHeadingId() {
            return "dashboard-workspace-heading";
        },

        workspaceCopy() {
            const w = this.filters.workspace || "default";
            /** @type {Record<string, Record<string, unknown>>} */
            const map = {
                shared: {
                    title: "Shared with me",
                    description:
                        "Projects and compounds others have shared with you appear here once you accept an invitation.",
                    emptyProjectsTitle: "Nothing shared yet",
                    emptyProjectsBody:
                        "When a colleague invites you to a project or grants access, it will be listed here. Invitations are sent by email—accept them to see shared items in this workspace.",
                    emptyIcon: "UserGroupIcon",
                },
                recent: {
                    title: "Recent",
                    description:
                        "Projects you have edited recently, across teams you belong to, sorted by latest activity.",
                    emptyProjectsTitle: "No recent projects",
                    emptyProjectsBody:
                        "Once you create or update a project, it will appear here for quicker access. Open the main workspace view to start or continue a submission.",
                    emptyIcon: "ClockIcon",
                },
                starred: {
                    title: "Starred",
                    description:
                        "Pin projects and compounds you refer to often. Starred items are personal to your account.",
                    emptyProjectsTitle: "No starred items",
                    emptyProjectsBody:
                        "Use the star action on a project or compound card to add it here. Starred items stay easy to find across sessions.",
                    emptyIcon: "StarIcon",
                },
                trashed: {
                    title: "Trash",
                    description:
                        "Projects you delete are retained here until they are permanently removed or restored.",
                    emptyProjectsTitle: "Trash is empty",
                    emptyProjectsBody:
                        "Deleted projects will appear in this list. You may restore a project from its menu while it remains in trash, subject to your workspace rules.",
                    emptyIcon: "TrashIcon",
                },
            };

            const entry = map[w] ?? map.recent;
            const iconMap = {
                UserGroupIcon,
                ClockIcon,
                StarIcon,
                TrashIcon,
            };

            return {
                ...entry,
                emptyIcon: iconMap[entry.emptyIcon] ?? ClockIcon,
            };
        },

        showProjectsPerPageSelect() {
            const total = Number(this.projects?.total) || 0;
            const per = Number(this.filters.projects_per_page) || 10;

            return total >= per;
        },

        showSamplesPerPageSelect() {
            const total = Number(this.samples?.total) || 0;
            const per = Number(this.filters.samples_per_page) || 12;

            return total >= per;
        },
    },

    mounted() {
        if (this.filters.action == "submission") {
            this.emitter.emit("openDatasetCreateDialog", {
                draft_id: this.filters.draft_id,
            });
        }
    },

    beforeUnmount() {
        clearTimeout(this.projectSearchTimer);
        clearTimeout(this.sampleSearchTimer);
    },
    methods: {
        /**
         * Omit default / empty query keys so bookmarks and Inertia visits stay readable.
         *
         * @param {Record<string, unknown>} merged
         */
        compactDashboardParams(merged) {
            const tab = merged.tab === "samples" ? "samples" : "projects";
            const projectsPage = Number(merged.projects_page) || 1;
            const samplesPage = Number(merged.samples_page) || 1;
            const projectsPerPage = Number(merged.projects_per_page) || 10;
            const samplesPerPage = Number(merged.samples_per_page) || 12;
            const projectsStatus = merged.projects_status || "all";
            const samplesStatus = merged.samples_status || "all";
            const projectsQ = String(merged.projects_q || "").trim();
            const samplesQ = String(merged.samples_q || "").trim();
            const workspace = merged.workspace || "default";

            /** @type {Record<string, string|number>} */
            const out = {};

            if (workspace !== "default") {
                out.workspace = workspace;
            }

            if (tab !== "projects") {
                out.tab = tab;
            }
            if (projectsPage !== 1) {
                out.projects_page = projectsPage;
            }
            if (samplesPage !== 1) {
                out.samples_page = samplesPage;
            }
            if (projectsPerPage !== 10) {
                out.projects_per_page = projectsPerPage;
            }
            if (samplesPerPage !== 12) {
                out.samples_per_page = samplesPerPage;
            }
            if (projectsStatus !== "all") {
                out.projects_status = projectsStatus;
            }
            if (samplesStatus !== "all") {
                out.samples_status = samplesStatus;
            }
            if (projectsQ !== "") {
                out.projects_q = projectsQ;
            }
            if (samplesQ !== "") {
                out.samples_q = samplesQ;
            }
            if (merged.action) {
                out.action = merged.action;
            }
            if (
                merged.draft_id !== null &&
                merged.draft_id !== undefined &&
                merged.draft_id !== ""
            ) {
                out.draft_id = merged.draft_id;
            }

            return out;
        },

        dashboardUrl(overrides = {}) {
            const merged = { ...this.filters, ...overrides };

            return this.route("dashboard", this.compactDashboardParams(merged));
        },

        visitDashboard(overrides = {}) {
            router.get(
                this.dashboardUrl(overrides),
                {},
                {
                    preserveScroll: true,
                    replace: true,
                }
            );
        },

        onTabSelect(event) {
            this.visitDashboard({ tab: event.target.value });
        },

        onProjectsSearchInput(value) {
            clearTimeout(this.projectSearchTimer);
            this.projectSearchTimer = setTimeout(() => {
                this.visitDashboard({
                    projects_q: value || "",
                    projects_page: 1,
                });
            }, 300);
        },

        onSamplesSearchInput(value) {
            clearTimeout(this.sampleSearchTimer);
            this.sampleSearchTimer = setTimeout(() => {
                this.visitDashboard({
                    samples_q: value || "",
                    samples_page: 1,
                });
            }, 300);
        },

        onProjectsStatus(status) {
            this.visitDashboard({
                projects_status: status,
                projects_page: 1,
            });
        },

        onSamplesStatus(status) {
            this.visitDashboard({
                samples_status: status,
                samples_page: 1,
            });
        },

        onProjectsPerPageChange(event) {
            const n = Number(event.target.value);
            if (!Number.isFinite(n)) {
                return;
            }
            this.visitDashboard({
                projects_per_page: n,
                projects_page: 1,
            });
        },

        onSamplesPerPageChange(event) {
            const n = Number(event.target.value);
            if (!Number.isFinite(n)) {
                return;
            }
            this.visitDashboard({
                samples_per_page: n,
                samples_page: 1,
            });
        },

        clearProjectFilters() {
            this.visitDashboard({
                projects_q: "",
                projects_status: "all",
                projects_page: 1,
            });
        },

        clearSampleFilters() {
            this.visitDashboard({
                samples_q: "",
                samples_status: "all",
                samples_page: 1,
            });
        },
    },
};
</script>
