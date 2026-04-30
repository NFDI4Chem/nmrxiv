<template>
    <app-layout :title="study.name">
        <template #header>
            <div
                v-if="study.is_deleted"
                class="text-center px-3 py-1 bg-red-50 text-red-700 border-b"
            >
                <b>Warning: </b> This {{ publishType }} is deleted. At the end
                of the 30-day period, this {{ publishType }} and all of its
                resources will be deleted permanently and cannot be recovered.
                You can only restore a deleted {{ publishType }} within the
                30-day recovery period.
            </div>
            <div
                v-if="
                    !study.is_public &&
                    !study.is_published &&
                    study.doi &&
                    !preview
                "
                class="text-center px-3 py-2 bg-green-50 text-green-700 border-b"
            >
                <b>Info: </b> This sample is in embargo and set to be released
                on {{ formatDate(study.release_date) }}. You cannot edit the
                sample. Contact us at info.nmrxiv@uni-jena.de if you need to
                make changes.
            </div>
            <div>
                <div
                    v-if="study.is_public && study.is_archived"
                    class="text-center px-3 py-2 bg-yellow-50 text-yellow-700 border-b"
                >
                    <b>Warning: </b> This {{ publishType }} is archived. It is
                    now read-only.
                </div>
                <div
                    v-if="study.is_public && !study.is_archived"
                    class="text-center px-3 py-2 bg-green-50 text-green-700 border-b"
                >
                    <b>Info: </b> This {{ publishType }} is published. You
                    cannot edit a published {{ publishType }}. Contact us at
                    info.nmrxiv@uni-jena.de if you need to make changes.
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
                                        <li v-if="!preview">
                                            <div class="flex">
                                                <Link
                                                    :href="route('dashboard')"
                                                    class="text-sm font-medium text-gray-500 hover:text-gray-700"
                                                    >Dashboard</Link
                                                >
                                            </div>
                                        </li>
                                        <li v-if="project">
                                            <div class="flex items-center">
                                                <ChevronRightIcon
                                                    v-if="!preview"
                                                    class="flex-shrink-0 h-5 w-5 text-gray-400"
                                                    aria-hidden="true"
                                                />
                                                <Link
                                                    :href="
                                                        preview &&
                                                        project.obfuscationcode
                                                            ? route(
                                                                  'project.preview',
                                                                  [
                                                                      project.obfuscationcode,
                                                                  ]
                                                              )
                                                            : route(
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
                                    class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6"
                                >
                                    <div
                                        v-if="!study.is_deleted"
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
                                        <a
                                            class="cursor-pointer inline-flex items-center"
                                            @click="toggleDetails"
                                        >
                                            <svg
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
                                            <span class="ml-2"
                                                >View details</span
                                            ></a
                                        >
                                        <div class="ml-3">
                                            <span
                                                v-if="study.is_public"
                                                class="inline-flex items-center"
                                            >
                                                <svg
                                                    class="h-3 w-3 text-green-400 inline"
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
                                                class="inline-flex items-center"
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
                                                        enable-background: new 0
                                                            0 512 512;
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
                                            class="capitalize inline-flex pr-4 ml-7 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                        >
                                            <span
                                                v-if="studyRole == 'reviewer'"
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
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                    />
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                    />
                                                </svg>
                                            </span>
                                            <span
                                                v-if="
                                                    studyRole == 'collaborator'
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
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                    />
                                                </svg>
                                            </span>
                                            <span
                                                v-if="
                                                    studyRole == 'owner' ||
                                                    studyRole == 'creator'
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
                                            {{ studyRole }}
                                        </span>
                                        <div
                                            class="inline-flex items-center space-x-3"
                                        >
                                            <span
                                                v-if="study.identifier"
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
                                                <b>{{ study.identifier }}</b>
                                            </span>
                                        </div>
                                    </div>
                                    <study-details
                                        ref="studyDetailsElement"
                                        :study="study"
                                        :role="studyRole"
                                        :study-permissions="studyPermissions"
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
                                            <b
                                                v-else-if="
                                                    project &&
                                                    project.release_date
                                                "
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
                                <div class="flex flex-nowrap justify-left pb-3">
                                    <div v-if="study.doi" class="text-gray-400">
                                        <DOIBadge :doi="study.doi"></DOIBadge>
                                    </div>
                                </div>

                                <div
                                    v-if="isStudyInEmbargo"
                                    class="flex flex-nowrap justify-end pb-3"
                                >
                                    <span
                                        class="py-2 inline-flex items-center px-3 rounded-md text-sm font-medium bg-yellow-100 text-red-800 capitalize hover:bg-yellow-200 cursor-pointer transition-colors"
                                        @click="showPublishDialog = true"
                                    >
                                        Release date:
                                        {{ formatDate(study.release_date) }}
                                        <PencilIcon
                                            class="w-4 h-4 ml-2 text-gray-600"
                                        />
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <TransitionRoot :show="showPublishDialog" as="template" appear>
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
                                                    {{ study.name }}
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
                                                    <small>STUDY NAME</small>
                                                </label>
                                                <h1
                                                    class="text-2xl font-extrabold text-gray-900"
                                                >
                                                    {{ study.name }}
                                                </h1>
                                            </div>
                                            <div class="mt-3">
                                                <label
                                                    class="block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"
                                                >
                                                    Release Date
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
                                                    class="mt-1 text-sm text-gray-500"
                                                >
                                                    Publish your data now or
                                                    choose a release date to
                                                    auto publish your sample to
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
                                                                id="conditions"
                                                                v-model="
                                                                    study.conditions
                                                                "
                                                                type="checkbox"
                                                                class="rounded mt-1 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                                name="conditions"
                                                            />
                                                            <div
                                                                class="ml-2 text-sm"
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
                                                                    study.terms
                                                                "
                                                                type="checkbox"
                                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
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
                                                        !study.terms &&
                                                        !study.conditions
                                                            ? 'bg-gray-200 cursor-not-allowed'
                                                            : 'bg-green-600 hover:bg-green-700',
                                                        'ml-2',
                                                    ]"
                                                    :disabled="
                                                        !study.terms &&
                                                        !study.conditions
                                                    "
                                                    @click="
                                                        showPublishConfirmationModal = true
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
                                                                your sample
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
                                                        your sample is submitted
                                                        to our queue system for
                                                        automatic processing.
                                                        Once successfully
                                                        processed, your data is
                                                        assigned with stable
                                                        identifiers, and DOIs
                                                        are generated. You will
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
                        change the data uploaded!
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
        <div class="pb-12 pt-6 px-10 min-h-[calc(100vh-theme(spacing.16))]">
            <slot name="scontent"></slot>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import StudyDetails from "./Partials/Details.vue";
import { StarIcon } from "@heroicons/vue/24/solid";
import { ref } from "vue";
import { CalendarIcon, ChevronRightIcon } from "@heroicons/vue/24/solid";
import AccessDialogue from "@/Shared/AccessDialogue.vue";
import Citation from "@/Shared/Citation.vue";
import DOIBadge from "@/Shared/DOIBadge.vue";
import { PencilIcon } from "@heroicons/vue/24/solid";
import { router } from "@inertiajs/vue3";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import {
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetSuccessButton from "@/Jetstream/SuccessButton.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";

export default {
    components: {
        Link,
        AppLayout,
        StudyDetails,
        CalendarIcon,
        ChevronRightIcon,
        StarIcon,
        AccessDialogue,
        Citation,
        DOIBadge,
        PencilIcon,
        Datepicker,
        Dialog,
        DialogPanel,
        TransitionChild,
        TransitionRoot,
        JetSecondaryButton,
        JetSuccessButton,
        JetConfirmationModal,
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
        "preview",
    ],
    setup() {
        const studyDetailsElement = ref(null);
        return {
            studyDetailsElement,
        };
    },

    data() {
        return {
            showPublishDialog: false,
            form: this.$inertia.form({
                _method: "PUT",
                release_date: this.study?.release_date || new Date(),
            }),
            errors: null,
            status: null,
            showPublishConfirmationModal: false,
        };
    },

    computed: {
        canUpdateStudy() {
            return this.studyPermissions
                ? this.studyPermissions.canUpdateStudy
                : false;
        },
        publishType() {
            return this.project?.project_enabled ? "project" : "sample";
        },
        isStudyInEmbargo() {
            return this.study.status === "embargo";
        },
    },
    methods: {
        toggleDetails() {
            this.studyDetailsElement.toggleDetails();
        },
        updateReleaseDate() {
            this.form.put(
                route("dashboard.study.updateReleaseDate", this.study.id),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.showPublishDialog = false;
                        router.reload({ only: ["study"] });
                    },
                    onError: () => {
                        this.errors = "Failed to update release date";
                    },
                }
            );
        },
        customDateFormat(date) {
            const day = String(date.getDate()).padStart(2, "0");
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, "0");
            const minutes = String(date.getMinutes()).padStart(2, "0");

            return `${month}/${day}/${year}, ${hours}:${minutes}`;
        },
        publish() {
            this.showPublishConfirmationModal = false;
            this.showPublishDialog = false;
            this.form.release_date = new Date();
            if (this.study.conditions && this.study.terms) {
                this.errors = null;
                this.form
                    .post(
                        route("dashboard.study.publish", this.study.id),
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
                .then(function () {
                    router.reload({ only: ["study"] });
                });
        },
    },
};
</script>
