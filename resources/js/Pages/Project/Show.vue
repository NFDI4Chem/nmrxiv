<!--
  Dashboard-only project home: used when the project has no public P-id yet, and for
  obfuscated reviewer preview (ProjectController@review). Projects with a numeric
  identifier redirect to the unified public shell at /project/P{n}.
-->
<template>
    <app-layout :title="project.name">
        <template #header>
            <div
                v-if="project.is_deleted"
                class="text-center px-3 py-2 bg-red-50 text-red-700 border-b"
            >
                <b>Warning: </b> This project is deleted. At the end of the
                30-day period, this project and all of its resources will be
                deleted permanently and cannot be recovered. You can only
                restore a deleted project within the 30-day recovery period.
            </div>
            <div
                v-if="
                    !project.is_public &&
                    !project.is_published &&
                    project.doi &&
                    !preview
                "
                class="text-center px-3 py-2 bg-green-50 text-green-700 border-b"
            >
                <b>Info: </b> This project is in embargo and set to be released
                on {{ formatDate(project.release_date) }}. You cannot edit the
                project, please create a new version to update the project.
            </div>
            <div v-if="project.is_public">
                <div
                    v-if="project.is_archived"
                    class="px-3 py-3 text-center text-yellow-800 bg-yellow-50 border-b dark:bg-yellow-950/40 dark:text-yellow-100 dark:border-yellow-900/50"
                >
                    <p>
                        <b>Warning: </b> This project is archived. It is now
                        read-only.
                    </p>
                    <p
                        v-if="publicProjectRecordUrl"
                        class="mt-2 text-sm text-yellow-900/90 dark:text-yellow-100/90"
                    >
                        Public record (share / cite):
                        <Link
                            :href="publicProjectRecordUrl"
                            class="inline-flex items-center gap-1 font-mono font-semibold text-teal-800 underline underline-offset-2 hover:text-teal-900 dark:text-teal-300 dark:hover:text-teal-200"
                        >
                            {{ publicProjectRouteParam }}
                            <ArrowTopRightOnSquareIcon
                                class="h-3.5 w-3.5 shrink-0"
                                aria-hidden="true"
                            />
                        </Link>
                        <span class="block sm:inline sm:before:content-['_']">
                            This dashboard URL is only for your signed-in
                            workspace.
                        </span>
                    </p>
                </div>
                <div
                    v-else
                    class="border-b border-emerald-200/80 bg-emerald-50 px-4 py-3 text-center text-emerald-900 dark:border-emerald-800/50 dark:bg-emerald-950/55 dark:text-emerald-100"
                >
                    <p
                        class="mx-auto max-w-3xl text-sm leading-relaxed sm:text-base"
                    >
                        <span
                            class="font-semibold text-emerald-950 dark:text-emerald-50"
                            >Published.</span
                        >
                        This project is read-only. To request changes, contact
                        <a
                            href="mailto:info.nmrxiv@uni-jena.de"
                            class="font-medium text-emerald-800 underline decoration-emerald-600/45 underline-offset-2 hover:text-emerald-950 dark:text-emerald-200 dark:hover:text-white"
                            >info.nmrxiv@uni-jena.de</a
                        >.
                    </p>
                    <p
                        v-if="publicProjectRecordUrl"
                        class="mx-auto mt-3 max-w-3xl text-sm leading-relaxed text-emerald-900/90 dark:text-emerald-100/90"
                    >
                        Share and cite the stable public page:
                        <Link
                            :href="publicProjectRecordUrl"
                            class="inline-flex items-center gap-1 font-mono font-semibold text-teal-800 underline underline-offset-2 hover:text-teal-900 dark:text-teal-300 dark:hover:text-teal-200"
                        >
                            {{ publicProjectRouteParam }}
                            <ArrowTopRightOnSquareIcon
                                class="h-3.5 w-3.5 shrink-0"
                                aria-hidden="true"
                            />
                        </Link>
                        <span class="block sm:inline sm:before:content-['_']">
                            The address you see here is your team workspace on
                            the dashboard, not the public record link.
                        </span>
                    </p>
                </div>
            </div>
            <div v-if="preview">
                <div
                    class="text-center px-3 py-2 bg-green-50 text-green-700 border-b"
                >
                    <b>Info: </b> You are viewing the project in read-only mode.
                </div>
            </div>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="w-full space-y-3 pt-6">
                        <div
                            class="flex flex-nowrap items-start justify-between gap-4"
                        >
                            <div class="min-w-0 flex-1">
                                <div
                                    class="flex cursor-pointer items-center pr-20 text-xl font-bold text-gray-700 dark:text-gray-200"
                                >
                                    <StarIcon
                                        :class="[
                                            project.is_bookmarked
                                                ? 'text-yellow-400'
                                                : 'text-gray-200',
                                            'h-5 w-5 flex-shrink-0 -ml-1 mr-1',
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
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            aria-hidden="true"
                                            class="w-4 h-4 mr-2 text-gray-600"
                                        >
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                            ></path>
                                        </svg>
                                        <span>Edit</span>
                                    </button>
                                </div>
                            </div>
                            <div v-if="!canUpdateProject" class="shrink-0">
                                <img
                                    v-if="project.project_photo_url"
                                    :src="project.project_photo_url"
                                    class="h-24 w-72 -ml-4 rounded-md object-cover"
                                />
                            </div>
                            <div class="shrink-0">
                                <Link
                                    v-if="canManageProjectSetings"
                                    :href="
                                        route(
                                            'dashboard.project.settings',
                                            project.id
                                        )
                                    "
                                    class="flex-nowrap text-sm font-bold text-gray-800 dark:text-gray-200"
                                >
                                    Project&nbsp;Settings
                                </Link>
                            </div>
                        </div>
                        <div
                            v-if="
                                (project.is_public && project.doi != null) ||
                                project.release_date ||
                                project.created_at ||
                                project.updated_at
                            "
                            class="w-full min-w-0 border-t border-gray-100 pt-4 dark:border-gray-800"
                        >
                            <div class="space-y-4">
                                <Citation
                                    v-if="
                                        project.is_public && project.doi != null
                                    "
                                    :model="'project'"
                                    :doi="project.doi"
                                ></Citation>
                                <ShowProjectDates
                                    v-if="
                                        project.release_date ||
                                        project.created_at ||
                                        project.updated_at
                                    "
                                    variant="simple"
                                    :release_date="project.release_date"
                                    :created_at="project.created_at"
                                    :updated_at="project.updated_at"
                                />
                            </div>
                        </div>
                        <div
                            v-if="!project.is_deleted"
                            class="flex min-w-0 flex-wrap items-center gap-y-2"
                        >
                            <access-dialogue
                                :available-roles="availableRoles"
                                :role="role"
                                :team="team"
                                :members="members"
                                :project="project"
                                called-from="projectView"
                                model="project"
                            />

                            <a
                                class="cursor-pointer hover:text-teal-900 inline-flex items-center ml-7"
                                @click="toggleDetails"
                                ><svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    class="w-4 h-4"
                                >
                                    <path
                                        d="M4 15a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h7a1 1 0 0 1 .7.3L13.42 5H21a1 1 0 0 1 .9 1.45L19.61 11l2.27 4.55A1 1 0 0 1 21 17h-8a1 1 0 0 1-.7-.3L10.58 15H4z"
                                        class="fill-current text-gray-400"
                                    ></path>
                                    <rect
                                        width="2"
                                        height="20"
                                        x="2"
                                        y="2"
                                        rx="1"
                                        class="fill-current text-gray-600"
                                    ></rect>
                                </svg>
                                <span class="ml-2">View details</span>
                            </a>
                            <a
                                ><span
                                    v-if="project.is_public"
                                    class="inline-flex items-center"
                                >
                                    <svg
                                        class="h-3 w-3 ml-4 text-green-400 inline"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 64 64"
                                        width="512"
                                        height="512"
                                    >
                                        <g id="globe">
                                            <path
                                                d="M53.85,47.85A27,27,0,0,1,24,57.8V56l3-3V49l4-4V42l4,4h5l2-2h8Z"
                                            />
                                            <path
                                                d="M42,20.59v2.56L38.07,27H31l-5.36,5.26L31,37.51v5.06L27.44,39H22.86L16,32.11V24.2L11.8,20h-4A27,27,0,0,1,32,5a26.55,26.55,0,0,1,7.06.94L36,9H30v4l4,4h4.33Z"
                                            />
                                            <path
                                                d="M32,60A28,28,0,1,1,60,32,28,28,0,0,1,32,60ZM32,6A26,26,0,1,0,58,32,26,26,0,0,0,32,6Z"
                                            />
                                        </g>
                                    </svg>
                                    <span class="ml-2">Public</span>
                                </span>
                                <span
                                    v-else
                                    class="inline-flex ml-7 items-center"
                                >
                                    <svg
                                        id="Capa_1"
                                        class="h-3 w-3 text-gray-400 inline"
                                        version="1.1"
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                        x="0px"
                                        y="0px"
                                        viewBox="0 0 512 512"
                                        style="
                                            enable-background: new 0 0 512 512;
                                        "
                                        xml:space="preserve"
                                    >
                                        <g>
                                            <g>
                                                <path
                                                    d="M437.333,192h-32v-42.667C405.333,66.99,338.344,0,256,0S106.667,66.99,106.667,149.333V192h-32
                                C68.771,192,64,196.771,64,202.667v266.667C64,492.865,83.135,512,106.667,512h298.667C428.865,512,448,492.865,448,469.333
                                V202.667C448,196.771,443.229,192,437.333,192z M287.938,414.823c0.333,3.01-0.635,6.031-2.656,8.292
                                c-2.021,2.26-4.917,3.552-7.948,3.552h-42.667c-3.031,0-5.927-1.292-7.948-3.552c-2.021-2.26-2.99-5.281-2.656-8.292l6.729-60.51
                                c-10.927-7.948-17.458-20.521-17.458-34.313c0-23.531,19.135-42.667,42.667-42.667s42.667,19.135,42.667,42.667
                                c0,13.792-6.531,26.365-17.458,34.313L287.938,414.823z M341.333,192H170.667v-42.667C170.667,102.281,208.948,64,256,64
                                s85.333,38.281,85.333,85.333V192z"
                                                />
                                            </g>
                                        </g>
                                        <g></g>
                                        <g></g>
                                        <g></g>
                                        <g></g>
                                        <g></g>
                                        <g></g>
                                        <g></g>
                                        <g></g>
                                        <g></g>
                                        <g></g>
                                        <g></g>
                                        <g></g>
                                        <g></g>
                                        <g></g>
                                        <g></g>
                                    </svg>
                                    <span class="ml-2">Private</span>
                                </span></a
                            >
                            <project-details
                                ref="projectDetailsElement"
                                :role="role"
                                :project-permissions="projectPermissions"
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
                                class="capitalize inline-flex pr-4 ml-7 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                            >
                                <span v-if="role == 'reviewer'">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6 py-1 mr-1"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />
                                    </svg>
                                </span>
                                <span v-if="role == 'collaborator'">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6 py-1 mr-1"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                        />
                                    </svg>
                                </span>
                                <span
                                    v-if="role == 'owner' || role == 'creator'"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6 py-1 mr-1"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"
                                        />
                                    </svg>
                                </span>
                                {{ role }}
                            </span>
                            <Tag
                                v-if="project.identifier"
                                :identifier="project.identifier"
                                class="ml-4"
                            />
                        </div>
                    </div>
                    <div class="flex flex-nowrap justify-between pb-3">
                        <div
                            v-if="project.identifier"
                            class="text-gray-400 mt-2"
                        >
                            <DOIBadge :doi="project.doi"></DOIBadge>
                        </div>
                        <div
                            v-if="
                                !project.is_public &&
                                !project.is_published &&
                                !project.is_deleted &&
                                !project.doi &&
                                role != 'reviewer'
                            "
                            class="flex-nowrap"
                        >
                            <Publish :project="project" />
                        </div>
                        <div
                            v-if="
                                !project.is_public &&
                                !project.is_published &&
                                project.doi &&
                                !preview
                            "
                        >
                            <span
                                class="ml-4 py-2 inline-flex items-center px-3 rounded-md text-sm font-medium bg-yellow-100 text-red-800 capitalize hover:bg-yellow-200"
                            >
                                <button @click="openReleaseDateDialog">
                                    Release date:
                                    {{ formatDate(project.release_date) }}
                                </button>
                                <PencilIcon
                                    class="w-4 h-4 ml-2 text-gray-600"
                                />
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <TransitionRoot
                    :show="showPublishDialog"
                    as="template"
                    appear
                    @after-leave="onPublishDialogAfterLeave"
                >
                    <Dialog as="div" class="relative z-10">
                        <TransitionChild
                            as="template"
                            enter="ease-out duration-300"
                            enter-from="opacity-0"
                            enter-to="opacity-100"
                            leave="ease-in duration-200"
                            leave-from="opacity-100"
                            leave-to="opacity-0"
                        >
                            <div
                                class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity"
                            />
                        </TransitionChild>

                        <div
                            class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20"
                        >
                            <TransitionChild
                                as="template"
                                enter="ease-out duration-300"
                                enter-from="opacity-0 scale-95"
                                enter-to="opacity-100 scale-100"
                                leave="ease-in duration-200"
                                leave-from="opacity-100 scale-100"
                                leave-to="opacity-0 scale-95"
                            >
                                <DialogPanel
                                    class="mx-auto max-w-3xl transform overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all"
                                >
                                    <div
                                        v-if="
                                            status == 'queued' ||
                                            status == 'processing' ||
                                            status == 'complete'
                                        "
                                    >
                                        <div class="py-16">
                                            <div class="text-center">
                                                <p
                                                    class="text-sm font-semibold text-primary-600 uppercase tracking-wide"
                                                >
                                                    {{ project.name }}
                                                </p>
                                                <span v-if="status == 'queued'">
                                                    <div
                                                        class="m-3 relative clear-both border-dotted border-2 border-gray-300 rounded-lg"
                                                    >
                                                        <span
                                                            class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm rounded-md text-sky-500 bg-white transition ease-in-out duration-150 cursor-not-allowed"
                                                            disabled=""
                                                            ><h1
                                                                class="capitalize text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl"
                                                            >
                                                                {{ status }}
                                                            </h1></span
                                                        >
                                                    </div>
                                                    <Link
                                                        type="button"
                                                        :href="
                                                            route('dashboard')
                                                        "
                                                        class="inline-flex items-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                                    >
                                                        Go to Dashboard
                                                    </Link>
                                                </span>
                                                <span
                                                    v-if="
                                                        status == 'processing'
                                                    "
                                                >
                                                    <div
                                                        class="m-3 relative clear-both border-dotted border-2 border-gray-300 rounded-lg"
                                                    >
                                                        <span
                                                            class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm rounded-md text-sky-500 bg-white transition ease-in-out duration-150 cursor-not-allowed"
                                                            disabled=""
                                                            ><h1
                                                                class="capitalize text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl"
                                                            >
                                                                {{ status }}
                                                            </h1></span
                                                        ><span
                                                            class="flex absolute h-3 w-3 top-0 right-0 -mt-1 -mr-1"
                                                            ><span
                                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"
                                                            ></span
                                                            ><span
                                                                class="relative inline-flex rounded-full h-3 w-3 bg-sky-500"
                                                            ></span
                                                        ></span>
                                                    </div>
                                                    <Link
                                                        type="button"
                                                        :href="
                                                            route('dashboard')
                                                        "
                                                        class="inline-flex items-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                                    >
                                                        Go to Dashboard
                                                    </Link>
                                                </span>
                                                <span
                                                    v-if="status == 'complete'"
                                                >
                                                    <div
                                                        class="m-3 clear-both relative border-dotted border-2 border-gray-300 rounded-lg"
                                                    >
                                                        <span
                                                            class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm rounded-md text-sky-500 bg-white transition ease-in-out duration-150 cursor-not-allowed"
                                                            disabled=""
                                                            ><h1
                                                                class="capitalize text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl"
                                                            >
                                                                {{ status }}
                                                            </h1></span
                                                        ><span
                                                            class="flex absolute h-3 w-3 top-0 right-0 -mt-1 -mr-1"
                                                            ><span
                                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"
                                                            ></span
                                                            ><span
                                                                class="relative inline-flex rounded-full h-3 w-3 bg-sky-500"
                                                            ></span
                                                        ></span>
                                                    </div>
                                                    <Link
                                                        type="button"
                                                        :href="
                                                            route('dashboard')
                                                        "
                                                        class="inline-flex items-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                                    >
                                                        Go to Dashboard
                                                    </Link>
                                                </span>
                                            </div>
                                        </div>
                                        <div
                                            v-if="status != 'complete'"
                                            class="w-full"
                                        >
                                            <div
                                                class="flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700"
                                            >
                                                <b>Whats next?</b>
                                                <div>
                                                    <p>
                                                        Please allow some time
                                                        to process your
                                                        submission. You will
                                                        recieve an email once
                                                        your submission is
                                                        processed. You will
                                                        receive an email with
                                                        citation details and
                                                        other helpful
                                                        information to share
                                                        your datasets.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <div class="p-8">
                                            <div>
                                                <label
                                                    class="block tracking-wider text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"
                                                >
                                                    <small>PROJECT NAME</small>
                                                </label>
                                                <h1
                                                    class="text-2xl font-extrabold text-gray-900"
                                                >
                                                    {{ project.name }}
                                                </h1>
                                            </div>

                                            <div
                                                class="mt-6"
                                                role="tablist"
                                                aria-label="Release scheduling"
                                            >
                                                <div
                                                    class="inline-flex rounded-lg border border-gray-200 bg-gray-100 p-0.5 dark:border-gray-600 dark:bg-gray-800"
                                                >
                                                    <button
                                                        type="button"
                                                        role="tab"
                                                        :aria-selected="
                                                            releaseModalMode ===
                                                            'update_date'
                                                        "
                                                        class="rounded-md px-4 py-2 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                                                        :class="
                                                            releaseModalMode ===
                                                            'update_date'
                                                                ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-950 dark:text-gray-100'
                                                                : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100'
                                                        "
                                                        @click="
                                                            setReleaseModalMode(
                                                                'update_date'
                                                            )
                                                        "
                                                    >
                                                        Update release date
                                                    </button>
                                                    <button
                                                        type="button"
                                                        role="tab"
                                                        :aria-selected="
                                                            releaseModalMode ===
                                                            'publish_now'
                                                        "
                                                        class="rounded-md px-4 py-2 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                                                        :class="
                                                            releaseModalMode ===
                                                            'publish_now'
                                                                ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-950 dark:text-gray-100'
                                                                : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100'
                                                        "
                                                        @click="
                                                            setReleaseModalMode(
                                                                'publish_now'
                                                            )
                                                        "
                                                    >
                                                        Publish now
                                                    </button>
                                                </div>
                                            </div>

                                            <div
                                                v-show="
                                                    releaseModalMode ===
                                                    'update_date'
                                                "
                                                class="mt-5"
                                            >
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                                >
                                                    Release date
                                                </label>
                                                <Datepicker
                                                    v-model="form.release_date"
                                                    :format="customDateFormat"
                                                    :min-date="new Date()"
                                                    :preview-format="
                                                        customDateFormat
                                                    "
                                                ></Datepicker>
                                                <p
                                                    class="mt-2 text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    Choose when this project
                                                    becomes public. Validation
                                                    rules follow your selected
                                                    date (for example, citation
                                                    DOIs are required when the
                                                    date is today or in the
                                                    past).
                                                </p>
                                            </div>

                                            <div
                                                v-show="
                                                    releaseModalMode ===
                                                    'publish_now'
                                                "
                                                class="mt-5 rounded-lg border border-amber-200/90 bg-amber-50/80 px-4 py-3 text-sm text-amber-950 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-100"
                                            >
                                                <p class="font-medium">
                                                    Immediate publishing
                                                </p>
                                                <p
                                                    class="mt-1 text-amber-900/90 dark:text-amber-200/90"
                                                >
                                                    Your release date will be
                                                    set to today and the
                                                    submission will be queued
                                                    for processing. All
                                                    validation rules for an
                                                    immediate release apply
                                                    (including citation DOIs
                                                    where required).
                                                </p>
                                            </div>

                                            <div class="mt-5">
                                                <h3
                                                    class="text-lg font-bold text-gray-400 dark:text-gray-500"
                                                >
                                                    Terms & Conditions
                                                </h3>

                                                <div class="mt-3">
                                                    <div class="ml-2">
                                                        <div
                                                            class="flex items-top"
                                                        >
                                                            <input
                                                                id="conditions"
                                                                v-model="
                                                                    project.conditions
                                                                "
                                                                type="checkbox"
                                                                class="rounded mt-1 border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-800"
                                                                name="conditions"
                                                            />
                                                            <div
                                                                class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                            >
                                                                I understand
                                                                that publishing
                                                                makes all
                                                                underlying data
                                                                publicly
                                                                available on the
                                                                nmrXiv platform
                                                                after the set
                                                                release date.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <div class="ml-2">
                                                        <div
                                                            class="flex items-center"
                                                        >
                                                            <input
                                                                id="terms"
                                                                v-model="
                                                                    project.terms
                                                                "
                                                                type="checkbox"
                                                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-800"
                                                                name="terms"
                                                            />
                                                            <div
                                                                class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                            >
                                                                I agree to the
                                                                <a
                                                                    target="_blank"
                                                                    rel="noopener noreferrer"
                                                                    :href="
                                                                        route(
                                                                            'terms.show'
                                                                        )
                                                                    "
                                                                    class="underline text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                                                                    >Terms of
                                                                    Service</a
                                                                >
                                                                and
                                                                <a
                                                                    target="_blank"
                                                                    rel="noopener noreferrer"
                                                                    :href="
                                                                        route(
                                                                            'policy.show'
                                                                        )
                                                                    "
                                                                    class="underline text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                                                                    >Privacy
                                                                    Policy</a
                                                                >
                                                                and hereby also
                                                                grant nmrXiv
                                                                permissions to
                                                                distribute the
                                                                datasets (and
                                                                meta-data) under
                                                                the specified
                                                                license.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <div
                                                class="flex flex-wrap items-center gap-2 px-8 pb-8 pt-0"
                                            >
                                                <template
                                                    v-if="
                                                        releaseModalMode ===
                                                        'update_date'
                                                    "
                                                >
                                                    <jet-success-button
                                                        type="button"
                                                        :class="[
                                                            !project.terms ||
                                                            !project.conditions
                                                                ? 'bg-gray-200 cursor-not-allowed dark:bg-gray-700'
                                                                : 'bg-primary-600 hover:bg-primary-700',
                                                        ]"
                                                        :disabled="
                                                            !project.terms ||
                                                            !project.conditions
                                                        "
                                                        @click="
                                                            updatePublishDate()
                                                        "
                                                    >
                                                        Update release date
                                                    </jet-success-button>
                                                </template>
                                                <template v-else>
                                                    <jet-success-button
                                                        type="button"
                                                        :class="[
                                                            !project.terms &&
                                                            !project.conditions
                                                                ? 'bg-gray-200 cursor-not-allowed dark:bg-gray-700'
                                                                : 'bg-green-600 hover:bg-green-700',
                                                        ]"
                                                        :disabled="
                                                            !project.terms ||
                                                            !project.conditions
                                                        "
                                                        @click="
                                                            showPublishConfirmationModal = true
                                                        "
                                                    >
                                                        Publish now
                                                    </jet-success-button>
                                                </template>
                                                <jet-secondary-button
                                                    type="button"
                                                    @click="
                                                        closePublishDialog()
                                                    "
                                                >
                                                    Cancel
                                                </jet-secondary-button>
                                            </div>
                                            <div v-if="errors">
                                                <div
                                                    class="rounded-md bg-red-50 p-4 mx-4 mb-4"
                                                >
                                                    <div class="flex">
                                                        <div
                                                            class="flex-shrink-0"
                                                        >
                                                            <svg
                                                                class="h-5 w-5 text-red-400"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 20 20"
                                                                fill="currentColor"
                                                                aria-hidden="true"
                                                            >
                                                                <path
                                                                    fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                                                    clip-rule="evenodd"
                                                                />
                                                            </svg>
                                                        </div>
                                                        <div class="ml-3">
                                                            <h3
                                                                class="text-sm font-medium text-red-800 dark:text-red-200"
                                                            >
                                                                {{
                                                                    releaseModalErrorHeading
                                                                }}
                                                            </h3>
                                                            <div
                                                                class="mt-2 text-sm text-red-700"
                                                            >
                                                                <p>
                                                                    {{ errors }}
                                                                </p>
                                                                <ul
                                                                    v-if="
                                                                        publishValidationHints.length
                                                                    "
                                                                    role="list"
                                                                    class="mt-2 list-disc space-y-1 pl-5"
                                                                >
                                                                    <li
                                                                        v-for="(
                                                                            hint,
                                                                            idx
                                                                        ) in publishValidationHints"
                                                                        :key="
                                                                            'publish-hint-' +
                                                                            idx
                                                                        "
                                                                    >
                                                                        {{
                                                                            hint
                                                                        }}
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="w-full">
                                            <div
                                                v-if="
                                                    releaseModalMode ===
                                                    'update_date'
                                                "
                                                class="flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700 dark:bg-gray-900/50 dark:text-gray-300"
                                            >
                                                <b class="shrink-0">Tip</b>
                                                <div class="min-w-0 pl-1">
                                                    <p>
                                                        Updating the date only
                                                        reschedules when your
                                                        project becomes public.
                                                        Confirm the terms below,
                                                        then save. Use
                                                        <span
                                                            class="font-semibold"
                                                            >Publish now</span
                                                        >
                                                        when you are ready to
                                                        submit for immediate
                                                        processing.
                                                    </p>
                                                </div>
                                            </div>
                                            <div
                                                v-else
                                                class="flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700 dark:bg-gray-900/50 dark:text-gray-300"
                                            >
                                                <b class="shrink-0"
                                                    >What happens next?</b
                                                >
                                                <div class="min-w-0 pl-1">
                                                    <p>
                                                        Your project is
                                                        submitted to the
                                                        processing queue. You
                                                        will receive an email
                                                        when processing
                                                        finishes, with citation
                                                        details and links to
                                                        share your data.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </Dialog>
                </TransitionRoot>
                <jet-confirmation-modal
                    :show="showPublishConfirmationModal"
                    @close="showPublishConfirmationModal = false"
                >
                    <template #title>
                        Are you sure you want to publish?
                    </template>

                    <template #content>
                        Once the data is published you will no longer be able to
                        change the data uploaded! If published as a project, you
                        may add more compounds (spectra) to the project later.
                    </template>

                    <template #footer>
                        <jet-secondary-button
                            @click="showPublishConfirmationModal = false"
                        >
                            Cancel
                        </jet-secondary-button>
                        <jet-success-button class="ml-2" @click="publish">
                            Publish Now
                        </jet-success-button>
                    </template>
                </jet-confirmation-modal>
            </div>
        </template>
        <div class="p-12 pt-8">
            <div>
                <section
                    class="mb-12 w-full max-w-none border-b border-gray-200 pb-10 dark:border-gray-800"
                    aria-labelledby="project-description-heading"
                >
                    <div
                        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <h2
                            id="project-description-heading"
                            class="text-xl font-semibold tracking-tight text-gray-900 sm:text-2xl dark:text-gray-50"
                        >
                            About this project
                        </h2>
                        <button
                            v-if="canUpdateProject"
                            type="button"
                            class="inline-flex shrink-0 items-center gap-1.5 self-start rounded-full border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-gray-500 dark:hover:bg-gray-800 dark:focus-visible:ring-offset-gray-900 sm:self-auto"
                            @click="toggleDetails"
                        >
                            <PencilIcon class="h-3.5 w-3.5" />
                            <span>Edit description</span>
                        </button>
                    </div>

                    <div
                        class="mt-6 w-full max-w-none"
                        role="region"
                        aria-label="Project description text"
                    >
                        <div
                            v-if="project.description"
                            class="prose prose-gray w-full max-w-none text-base leading-relaxed text-gray-800 prose-headings:font-semibold prose-a:text-teal-700 prose-a:no-underline hover:prose-a:underline dark:prose-invert dark:text-gray-200 sm:prose-lg"
                            v-html="md(project.description)"
                        ></div>
                        <p
                            v-else
                            class="text-base leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            No description has been provided yet.
                        </p>
                    </div>

                    <div
                        class="mt-8 flex flex-wrap gap-2"
                        aria-label="Keywords"
                    >
                        <Tag
                            v-if="project.tags && project.tags.length"
                            size="sm"
                            :tags="project.tags"
                        />
                        <span
                            v-else
                            class="text-sm text-gray-500 dark:text-gray-400"
                            >No keywords added yet.</span
                        >
                    </div>

                    <div
                        class="mt-6 flex flex-col gap-1 text-xs text-gray-500 sm:flex-row sm:items-baseline sm:justify-between dark:text-gray-400"
                    >
                        <span
                            v-if="project.identifier"
                            class="font-mono text-[11px] text-gray-600 dark:text-gray-300"
                            >{{ project.identifier }}</span
                        >
                        <span
                            v-if="project.updated_at"
                            class="tabular-nums text-gray-500 dark:text-gray-400"
                        >
                            Last updated
                            {{ formatRecordTimestamp(project.updated_at) }}
                        </span>
                    </div>
                </section>
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
                            <Tag :tags="project.tags" />
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
                <div
                    v-if="canUpdateProject || project.citations.length > 0"
                    class="border-b border-gray-200 pb-8"
                >
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
                            <citation-card
                                :citations="project.citations"
                                :show-edit-delete="canUpdateProject"
                                @edit="onCitationCardEdit"
                                @delete="onCitationCardDelete"
                            />
                        </div>
                    </dd>
                </div>

                <!-- Author -->
                <div
                    v-if="canUpdateProject || project.authors.length > 0"
                    class="mb-8 pt-8"
                >
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
                            <author-card
                                :authors="project.authors"
                                :show-edit-delete="canUpdateProject"
                                @edit="onAuthorCardEdit"
                                @delete="onAuthorCardDelete"
                            />
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
                    :team-role="teamRole"
                    :preview="preview"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import AccessDialogue from "@/Shared/AccessDialogue.vue";
import { Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import StudyIndex from "@/Pages/Study/Index.vue";
import ProjectDetails from "./Partials/Details.vue";
import { ref } from "vue";
import { StarIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { ArrowTopRightOnSquareIcon } from "@heroicons/vue/24/outline";
import ManageAuthor from "@/Shared/ManageAuthor.vue";
import ToolTip from "@/Shared/ToolTip.vue";
import ManageCitation from "@/Shared/ManageCitation.vue";
import Citation from "@/Shared/Citation.vue";
import Publish from "@/Shared/Publish.vue";
import AuthorCard from "@/Shared/AuthorCard.vue";
import CitationCard from "@/Shared/CitationCard.vue";
import DOIBadge from "@/Shared/DOIBadge.vue";
import Tag from "@/Shared/Tag.vue";
import Datepicker from "@vuepic/vue-datepicker";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetSuccessButton from "@/Jetstream/SuccessButton.vue";
import {
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import ShowProjectDates from "@/Shared/ShowProjectDates.vue";
import "@vuepic/vue-datepicker/dist/main.css";

export default {
    components: {
        Link,
        AppLayout,
        StudyIndex,
        ProjectDetails,
        StarIcon,
        PencilIcon,
        ArrowTopRightOnSquareIcon,
        AccessDialogue,
        ManageAuthor,
        ToolTip,
        ManageCitation,
        Citation,
        Publish,
        AuthorCard,
        CitationCard,
        DOIBadge,
        Tag,
        Dialog,
        DialogPanel,
        TransitionChild,
        TransitionRoot,
        Datepicker,
        JetConfirmationModal,
        JetSecondaryButton,
        JetSuccessButton,
        ShowProjectDates,
    },
    props: [
        "project",
        "team",
        "members",
        "availableRoles",
        "projectPermissions",
        "role",
        "teamRole",
        "license",
        "preview",
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
    data() {
        return {
            form: this.$inertia.form({
                _method: "PUT",
                name: this.project.name,
                enableProjectMode: this.project.enableProjectMode,
                release_date: this.project.release_date,
            }),
            showPublishDialog: false,
            showPublishConfirmationModal: false,
            releaseModalMode: "update_date",
            releaseErrorContext: null,
            // Added reactive props to avoid Vue warnings during render
            errors: null,
            status: null,
            query: "",
            validation: null,
        };
    },
    computed: {
        canDeleteProject() {
            return this.projectPermissions
                ? this.projectPermissions.canDeleteProject
                : false;
        },
        canUpdateProject() {
            return this.projectPermissions
                ? this.projectPermissions.canUpdateProject
                : false;
        },
        canManageProjectSetings() {
            return this.projectPermissions
                ? this.projectPermissions.canManageSettings
                : false;
        },
        publishValidationHints() {
            const hints = this.$page.props.flash?.publish_validation_hints;

            return Array.isArray(hints) ? hints : [];
        },
        releaseModalErrorHeading() {
            if (this.releaseErrorContext === "update") {
                return "Could not update release date";
            }
            if (this.releaseErrorContext === "publish") {
                return "Error publishing your project";
            }

            return this.releaseModalMode === "publish_now"
                ? "Error publishing your project"
                : "Could not update release date";
        },
        publicProjectRouteParam() {
            if (!this.project?.identifier) {
                return null;
            }
            const raw = String(this.project.identifier).replace(
                /^NMRXIV:/i,
                ""
            );
            if (!raw) {
                return null;
            }
            const normalized = /^[Pp][0-9]+$/.test(raw)
                ? raw.replace(/^p/, "P")
                : `P${raw}`;

            return normalized;
        },
        publicProjectRecordUrl() {
            if (!this.publicProjectRouteParam) {
                return null;
            }

            return this.route(
                "public.project.id",
                this.publicProjectRouteParam
            );
        },
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
            } else if (editOperation == "release_date") {
                if (this.eligibleForReleaseDateDialog()) {
                    this.releaseModalMode = "update_date";
                    this.errors = null;
                    this.releaseErrorContext = null;
                    this.showPublishDialog = true;
                }
                this.stripEditQueryParamFromUrl();
            }
        }
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
                .then(function () {
                    router.reload({ only: ["project"] });
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
        },
        onCitationCardEdit(citation) {
            const modal = this.manageCitationElement;
            if (!modal.showDialog) {
                modal.toggleDialog();
            }
            this.$nextTick(() => {
                modal.edit(citation);
            });
        },
        onCitationCardDelete(citation) {
            this.manageCitationElement.confirmDeletion(citation);
        },
        onAuthorCardEdit(author) {
            const modal = this.manageAuthorElement;
            if (!modal.showDialog) {
                modal.toggleDialog();
            }
            this.$nextTick(() => {
                modal.edit(author);
            });
        },
        onAuthorCardDelete(author) {
            this.manageAuthorElement.confirmDeletion(author);
        },
        openReleaseDateDialog() {
            this.releaseModalMode = "update_date";
            this.errors = null;
            this.releaseErrorContext = null;
            this.showPublishDialog = true;
        },
        setReleaseModalMode(mode) {
            if (this.releaseModalMode === mode) {
                return;
            }
            this.releaseModalMode = mode;
            this.errors = null;
            this.releaseErrorContext = null;
        },
        closePublishDialog() {
            this.showPublishDialog = false;
        },
        onPublishDialogAfterLeave() {
            this.query = "";
            this.releaseModalMode = "update_date";
            this.errors = null;
            this.releaseErrorContext = null;
        },
        eligibleForReleaseDateDialog() {
            return (
                !this.project.is_public &&
                !this.project.is_published &&
                this.project.doi &&
                !this.preview
            );
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
        publish() {
            this.showPublishConfirmationModal = false;
            this.showPublishDialog = false;
            this.form.release_date = new Date();
            if (!this.project.conditions || !this.project.terms) {
                return;
            }
            this.errors = null;
            this.releaseErrorContext = null;
            this.form.put(route("dashboard.project.publish", this.project.id), {
                preserveScroll: true,
                onSuccess: (page) => {
                    this.status =
                        page.props.project?.status ?? this.project.status;
                    this.showPublishDialog = false;
                    this.releaseErrorContext = null;
                },
                onError: (errors) => {
                    this.releaseErrorContext = "publish";
                    const msg = errors.publish;
                    this.errors = Array.isArray(msg)
                        ? msg[0]
                        : msg ?? "Publishing failed.";
                    this.showPublishDialog = true;
                },
            });
        },
        updatePublishDate() {
            if (!this.project.conditions || !this.project.terms) {
                return;
            }
            this.project.release_date = this.form.release_date;
            this.errors = null;
            this.releaseErrorContext = null;
            this.form.put(
                route("dashboard.project.updateReleaseDate", this.project.id),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.showPublishDialog = false;
                        this.releaseErrorContext = null;
                    },
                    onError: (errors) => {
                        this.releaseErrorContext = "update";
                        const keys = Object.keys(errors);
                        if (keys.length === 0) {
                            this.errors = "Could not update release date.";
                        } else {
                            const k = keys[0];
                            const v = errors[k];
                            this.errors = Array.isArray(v)
                                ? v[0]
                                : String(v ?? "Could not update release date.");
                        }
                        this.showPublishDialog = true;
                    },
                }
            );
        },
    },
};
</script>
