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
                project, please create a new version to updated the project.
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
                    v-else
                    class="text-center px-3 py-2 bg-green-50 text-green-700 border-b"
                >
                    <b>Info: </b> This project is public. You cannot edit a
                    published project, please create a new version to updated
                    the project.
                </div>
            </div>
            <div v-if="preview">
                <div
                    class="text-center px-3 py-2 bg-green-50 text-green-700 border-b"
                >
                    <b>Info: </b> You are viewing the project in read-only mode.
                </div>
            </div>
            <div v-if="project.is_public && project.doi != null">
                <Citation :model="'project'" :doi="project.doi"></Citation>
                <ShowProjectDates
                    :release_date="project.release_date"
                    :created_at="project.created_at"
                    :updated_at="project.updated_at"
                />
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
                            <div
                                class="inline-flex items-center mt-3"
                                v-if="!project.is_deleted"
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
                                                enable-background: new 0 0 512
                                                    512;
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
                                        v-if="
                                            role == 'owner' || role == 'creator'
                                        "
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
                                <span
                                    v-if="project.identifier"
                                    class="inline-flex pr-4 ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6 py-1"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5"
                                        />
                                    </svg>
                                    <b>{{ project.identifier }}</b>
                                </span>
                            </div>
                        </div>
                        <div class="flex-nowrap">
                            <Link
                                v-if="canManageProjectSetings"
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
                                !project.doi
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
                                <button @click="showPublishDialog = true">
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
                    @after-leave="query = ''"
                    appear
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
                                                    class="text-sm font-semibold text-indigo-600 uppercase tracking-wide"
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
                                                        class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
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
                                                        class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
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
                                                        class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
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
                                            <div class="mt-3">
                                                <label
                                                    class="block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"
                                                >
                                                    Release Date
                                                </label>
                                                <Datepicker
                                                    v-model="
                                                        project.release_date
                                                    "
                                                ></Datepicker>
                                                <p
                                                    class="mt-1 text-sm text-gray-500"
                                                >
                                                    Publish your data now or
                                                    choose a release date to
                                                    auto publish your project to
                                                    public.
                                                </p>
                                            </div>
                                            <div class="mt-5">
                                                <h3
                                                    class="text-lg font-bold text-gray-400"
                                                >
                                                    Terms & Conditions
                                                </h3>

                                                <div class="mt-3">
                                                    <div class="ml-2">
                                                        <div
                                                            class="flex items-top"
                                                        >
                                                            <input
                                                                type="checkbox"
                                                                class="rounded mt-1 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                                id="conditions"
                                                                v-model="
                                                                    project.conditions
                                                                "
                                                                name="conditions"
                                                            />
                                                            <div
                                                                class="ml-2 text-sm"
                                                            >
                                                                I understand
                                                                once the project
                                                                is published,
                                                                all the
                                                                underlying
                                                                samples and
                                                                spectra will
                                                                also be made
                                                                public and agree
                                                                to make this
                                                                data
                                                                persistently
                                                                available in the
                                                                nmrXiv platfor
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
                                                                type="checkbox"
                                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                                id="terms"
                                                                v-model="
                                                                    project.terms
                                                                "
                                                                name="terms"
                                                            />
                                                            <div
                                                                class="ml-2 text-sm"
                                                            >
                                                                I agree to the
                                                                <a
                                                                    target="_blank"
                                                                    :href="
                                                                        route(
                                                                            'terms.show'
                                                                        )
                                                                    "
                                                                    class="underline text-sm text-gray-600 hover:text-gray-900"
                                                                    >Terms of
                                                                    Service</a
                                                                >
                                                                and
                                                                <a
                                                                    target="_blank"
                                                                    :href="
                                                                        route(
                                                                            'policy.show'
                                                                        )
                                                                    "
                                                                    class="underline text-sm text-gray-600 hover:text-gray-900"
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
                                            <div class="px-8 pb-8 pt-0">
                                                <jet-success-button
                                                    type="button"
                                                    :class="[
                                                        !project.terms &&
                                                        !project.conditions
                                                            ? 'bg-gray-200 cursor-not-allowed'
                                                            : 'bg-green-600 hover:bg-green-700',
                                                        'ml-2',
                                                    ]"
                                                    @click="
                                                        showPublishConfirmationModal = true
                                                    "
                                                    :disabled="
                                                        !project.terms &&
                                                        !project.conditions
                                                    "
                                                >
                                                    Publish now
                                                </jet-success-button>
                                                <jet-secondary-button
                                                    type="button"
                                                    class="ml-2"
                                                    @click="updatePublishDate()"
                                                >
                                                    Update publish date
                                                </jet-secondary-button>
                                                <jet-secondary-button
                                                    type="button"
                                                    class="ml-2"
                                                    @click="
                                                        showPublishDialog = false
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
                                                                class="text-sm font-medium text-red-800"
                                                            >
                                                                Error publishing
                                                                your project
                                                            </h3>
                                                            <div
                                                                class="mt-2 text-sm text-red-700"
                                                            >
                                                                <ul
                                                                    role="list"
                                                                    class="list-disc space-y-1 pl-5"
                                                                >
                                                                    <li>
                                                                        {{
                                                                            errors
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
                                                class="flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700"
                                            >
                                                <b>Whats next?</b>
                                                <div>
                                                    <p>
                                                        Upon clicking publish,
                                                        your project is
                                                        submitted to our queue
                                                        system for automatic
                                                        processing. Once
                                                        successfully processed,
                                                        your data is assigned
                                                        with stable identifiers,
                                                        and DOIs are generated.
                                                        You will receive an
                                                        email with citation
                                                        details and other
                                                        helpful information to
                                                        share your datasets.
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
                                class="mt-1 inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"
                            >
                                {{ tag.name["en"] }}
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
                            <citation-card :citations="project.citations" />
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
                            <author-card :authors="project.authors" />
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
import ManageAuthor from "@/Shared/ManageAuthor.vue";
import ToolTip from "@/Shared/ToolTip.vue";
import ManageCitation from "@/Shared/ManageCitation.vue";
import Citation from "@/Shared/Citation.vue";
import Publish from "@/Shared/Publish.vue";
import AuthorCard from "@/Shared/AuthorCard.vue";
import CitationCard from "@/Shared/CitationCard.vue";
import DOIBadge from "@/Shared/DOIBadge.vue";
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
        Citation,
        Publish,
        AuthorCard,
        CitationCard,
        DOIBadge,
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
            //this.emitter.emit("openAddCitationDialog", {});
        },
        publish() {
            this.showPublishConfirmationModal = false;
            this.showPublishDialog = false;
            this.form.release_date = new Date();
            if (this.project.conditions && this.project.terms) {
                this.errors = null;
                this.form
                    .post(
                        route("dashboard.project.publish", this.project.id),
                        this.form
                    )
                    .catch((err) => {
                        this.errors = err.response.data.errors;
                        this.validation = err.response.data.validation.report;
                    })
                    .then((response) => {
                        this.status = response.data.project.status;
                        this.showPublishDialog = false;
                    });
            }
        },
        updatePublishDate() {
            this.form.release_date = this.project.release_date;
            this.form.put(
                route("dashboard.project.updateReleaseDate", this.project.id),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.showPublishDialog = false;
                    },
                    onError: (err) => {},
                }
            );
        },
    },
};
</script>
