<template>
    <div
        id="fs-dropzone"
        :class="[
            fullScreen
                ? 'fixed w-screen h-screen -ml-4 -mt-6 sm:ml-0 md:-ml-0 md:w-auto inset-0'
                : '',
            'mt-3 bg-white',
        ]"
    >
        <div>
            <div :class="[fullScreen ? 'px-6 py-4' : '', 'flex']">
                <div class="w-full px-5">
                    <div
                        v-if="!readonly"
                        class="text-sm cursor-pointer hover:text-blue-700 mt-2 mr-10"
                    >
                        <i
                            ><a
                                href="https://docs.nmrxiv.org/submission-guides/folder-structure.html"
                                target="_blank"
                                class="mb-4"
                            >
                                <ToolTip
                                    class="w-3.5 h-3.5"
                                    text="To submit data you will need an account with nmrXiv, so you will be redirected to our register page and once registered you can then go ahead and submit data. For more information please checkout our <a target='_blank' href='//docs.nmrxiv.org' class='text-gray-400' target='_blank'>documentation</a>."
                                ></ToolTip>
                                <span class="ml-4">
                                    Learn more about folder structuring
                                </span>
                            </a>
                        </i>
                        <a
                            @click="showMissingFilesDetailsModal()"
                            v-if="missing_files > 0"
                            class="text-red-900 text-strong float-right"
                        >
                            <svg
                                class="h-5 w-5 text-red-400 inline"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                            {{ missing_files }} files missing
                        </a>
                    </div>
                    <!-- <button class="float-right" @click="toggleFullScreen">
                        <span v-if="fullScreen">
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M9 19V15M9 15H5M9 15L4 20"
                                    stroke="black"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M15 5V9M15 9H19M15 9L20 4"
                                    stroke="black"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M9 5V9M9 9H5M9 9L4 4"
                                    stroke="black"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M15 19V15M15 15H19M15 15L20 20"
                                    stroke="black"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </span>
                        <span v-else>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"
                                />
                            </svg>
                        </span>
                    </button> -->
                </div>
            </div>
            <div
                v-if="!readonly"
                :class="[fullScreen ? 'px-6 py-4' : 'px-5', '']"
            >
                <div class="py-2 mb-3">
                    <div id="fs-dropzone-message" class="text-center">
                        <div
                            type="button"
                            class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-4 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                        >
                            <svg
                                class="mx-auto h-12 w-12 text-gray-400"
                                xmlns="http://www.w3.org/2000/svg"
                                stroke="currentColor"
                                fill="none"
                                viewBox="0 0 48 48"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m0-4c0 4.418-7.163 8-16 8S8 28.418 8 24m32 10v6m0 0v6m0-6h6m-6 0h-6"
                                />
                            </svg>
                            <span
                                class="mt-2 block text-lg font-bold text-blue-600"
                            >
                                Drop Files or Folders
                                <span
                                    v-if="
                                        $page.props.selectedFolder &&
                                        $page.props.selectedFolder != '/'
                                    "
                                >
                                    to "{{ $page.props.selectedFolder }}" folder
                                </span>
                                <form
                                    class="inline"
                                    enctype="multipart/form-data"
                                >
                                    or
                                    <button
                                        type="button"
                                        class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white px-2 border border-blue-500 hover:border-transparent rounded"
                                        id="fs-dropzone-click-target"
                                    >
                                        Select folders
                                    </button>
                                    to upload
                                    <div
                                        id="fs-dropzone-hidden-input-container"
                                    ></div>
                                </form>
                                <div class="text-sm text-gray-400">
                                    Need help? Check out our
                                    <a
                                        class="text-blue-800 hover:underline"
                                        href="https://docs.nmrxiv.org/submission-guides/submission-process.html#step-1-files-upload"
                                        target="_blank"
                                        >submission guides
                                    </a>
                                </div>
                            </span>
                            <div v-if="dropzone" class="relative mt-5">
                                <div
                                    class="overflow-hidden h-2 text-xs flex rounded bg-gray-200"
                                >
                                    <div
                                        :style="
                                            'width: ' + precentageUpload + '%'
                                        "
                                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"
                                    ></div>
                                </div>
                                <span
                                    v-if="status"
                                    class="mt-2 block text-sm font-medium text-gray-900"
                                >
                                    {{ status }}
                                    <div v-if="uploadBatchErrors.length > 0">
                                        -
                                        <a
                                            class="text-red-700 cursor-pointer"
                                            @click="
                                                showErrorBatchLogs =
                                                    !showErrorBatchLogs
                                            "
                                            >View logs</a
                                        >
                                    </div>
                                </span>
                                <div
                                    v-if="showErrorBatchLogs"
                                    class="mt-2 block text-sm font-medium text-gray-900"
                                >
                                    <div
                                        v-for="(
                                            error, $index
                                        ) in uploadBatchErrors"
                                        :key="$index"
                                        class="rounded-md"
                                        v-html="error"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="loading">
                <div class="h-[calc(100vh-260px)] text-center py-12">
                    <svg
                        class="animate-spin -ml-1 mr-3 h-5 w-5 text-dark flex-inline inline"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                    Loading Files...
                </div>
            </div>
            <div
                v-else
                :class="[
                    fullScreen
                        ? 'overflow-scroll h-full relative px-6 py-4'
                        : 'px-1',
                    'min-w-0 flex-1 bg-white border-t border-gray-200 lg:flex',
                ]"
            >
                <aside
                    :class="[
                        height ? height : '',
                        'py-2 lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col overflow-y-scroll overflow-x-scroll border-r',
                    ]"
                >
                    <children
                        :file="file"
                        :expanded-folders="expandedFolders"
                        @toggle-expansion="
                            (fsoId, isExpanded) =>
                                toggleFolderExpansion(fsoId, isExpanded)
                        "
                    ></children>
                    <div
                        v-if="
                            Object.keys(logs).length > 0 &&
                            !readonly &&
                            !isDeletingFiles
                        "
                        class="mt-4 text-sm cursor-pointer text-gray-400"
                        @click="showLogsDialog = true"
                    >
                        <InformationCircleIcon
                            class="h-5 w-5 inline text-gray-400 flex-shrink-0 mx-auto"
                            aria-hidden="true"
                        />
                        View logs
                    </div>
                    <jet-dialog-modal
                        :show="showLogsDialog"
                        @close="showLogsDialog = false"
                    >
                        <template #title>
                            <div class="block">
                                File logs
                                <div class="inline float-right">
                                    <select
                                        v-model="logFilter"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                    >
                                        <option
                                            v-for="filter in logFilters"
                                            :key="filter"
                                            :value="filter"
                                        >
                                            {{ filter }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </template>

                        <template #content>
                            <div
                                class="relative h-[74vh] overflow-x-scroll z-0 mt-1 rounded-lg cursor-pointer"
                            >
                                <ul
                                    v-if="Object.keys(filteredLogs).length > 0"
                                    role="list"
                                    class="divide-y divide-gray-200"
                                >
                                    <li
                                        v-for="file in Object.keys(
                                            filteredLogs
                                        )"
                                        :key="file"
                                        class="py-4 flex"
                                    >
                                        <CheckIcon
                                            v-if="
                                                logs[file].status == 'Success'
                                            "
                                            class="h-5 w-5 inline text-green-400"
                                            aria-hidden="true"
                                        />
                                        <ArrowUpTrayIcon
                                            v-if="
                                                logs[file].status ==
                                                'Inprogress'
                                            "
                                            class="h-5 w-5 inline text-yellow-400"
                                            aria-hidden="true"
                                        />
                                        <EllipsisVerticalIcon
                                            v-if="
                                                logs[file].status ==
                                                'Inprogress'
                                            "
                                            class="h-5 w-5 inline text-gray-400"
                                            aria-hidden="true"
                                        />
                                        <ExclamationCircleIcon
                                            v-if="logs[file].status == 'Error'"
                                            class="h-5 w-5 inline text-red-400"
                                            aria-hidden="true"
                                        />
                                        <div class="ml-3">
                                            <p
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ file }}
                                            </p>
                                            <p
                                                v-for="message in logs[file]
                                                    .messages"
                                                :key="message"
                                                class="text-sm text-gray-400"
                                            >
                                                {{ message }}
                                            </p>
                                        </div>
                                    </li>
                                </ul>
                                <div v-else class="mt-10">
                                    <i class="text-gray-400"
                                        >No logs with the status
                                        {{ logFilter }}</i
                                    >
                                </div>
                            </div>
                        </template>

                        <template #footer>
                            <jet-secondary-button
                                class="cursor-pointer"
                                @click="toggleShowLogsDialog"
                            >
                                Close
                            </jet-secondary-button>
                        </template>
                    </jet-dialog-modal>
                </aside>
                <section
                    :class="[
                        height ? height : '',
                        'p-6 flex-1 flex flex-col lg:order-last overflow-y-scroll',
                    ]"
                >
                    <div
                        v-if="
                            $page.props.selectedFileSystemObject &&
                            $page.props.selectedFileSystemObject.has_children
                        "
                        class="mb-3"
                    >
                        <div class="py-2 mb-2 block border-b pb-4">
                            <p class="font-bold text-xl">
                                {{
                                    $page.props.selectedFileSystemObject
                                        .relative_url
                                }}
                                <a
                                    v-if="
                                        $page.props.selectedFileSystemObject
                                            .id && !readonly
                                    "
                                    class="ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 float-right"
                                    @click="confirmFSODeletion"
                                >
                                    <TrashIcon
                                        class="cursor-pointer h-4 w-4 text-gray-900 mr-2"
                                        aria-hidden="true"
                                    />
                                    Delete
                                </a>
                                <a
                                    v-if="
                                        $page.props.selectedFileSystemObject
                                            .id &&
                                        readonly &&
                                        downloadURL
                                    "
                                    :href="downloadURL"
                                    class="ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 float-right"
                                >
                                    Download
                                </a>
                            </p>
                        </div>
                        <ul
                            role="list"
                            class="mb-3 mt-6 grid grid-cols-1 gap-5 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4"
                        >
                            <li
                                v-for="file in $page.props
                                    .selectedFileSystemObject.children"
                                :key="file.key"
                                class="relative shadow rounded-lg"
                            >
                                <div
                                    style="user-select: none"
                                    class="hover:cursor-pointer"
                                    @dblclick.stop="displaySelected(file)"
                                >
                                    <div
                                        class="group block w-full aspect-w-10 aspect-h-7 py-4 bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-indigo-500 overflow-hidden"
                                    >
                                        <span v-if="file.type == 'directory'">
                                            <FolderIcon
                                                class="cursor-pointer h-28 w-28 text-gray-400 flex-shrink-0 mx-auto"
                                                aria-hidden="true"
                                            />
                                        </span>
                                        <span v-else>
                                            <DocumentTextIcon
                                                class="h-28 w-28 text-gray-400 flex-shrink-0 mx-auto"
                                                aria-hidden="true"
                                            />
                                        </span>
                                        <span
                                            v-if="file.status == 'missing'"
                                            class="absolute right-0 top-0 pr-4 pt-4 text-sm font-medium text-gray-500 pointer-events-none"
                                        >
                                            <div
                                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"
                                            >
                                                <svg
                                                    class="h-6 w-6 text-red-600"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                                                    ></path>
                                                </svg>
                                            </div>
                                        </span>
                                    </div>
                                    <p
                                        class="mt-2 px-2 py-1 block text-sm font-medium truncate text-gray-900 pointer-events-none"
                                    >
                                        <span class="float-left">
                                            {{ file.name }}
                                        </span>
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div v-else>
                        <div
                            v-if="$page.props.selectedFileSystemObject"
                            class="py-2 mb-2 block border-b pb-4"
                        >
                            <p class="font-bold text-xl">
                                {{
                                    $page.props.selectedFileSystemObject
                                        .relative_url
                                }}
                                <a
                                    v-if="!readonly"
                                    class="ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 float-right"
                                    @click="confirmFSODeletion"
                                >
                                    <TrashIcon
                                        class="cursor-pointer h-4 w-4 text-gray-900 mr-2"
                                        aria-hidden="true"
                                    />
                                    Delete
                                </a>
                            </p>
                        </div>
                        <div class="pt-5">
                            <span
                                v-if="
                                    $page.props.selectedFileSystemObject &&
                                    $page.props.selectedFileSystemObject.type ==
                                        'file'
                                "
                            >
                                <File-details
                                    :file="$page.props.selectedFileSystemObject"
                                    :project="readonly ? project : null"
                                ></File-details>
                            </span>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Deletion Progress Overlay -->
    <div
        v-if="isDeletingFiles"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-md mx-4">
            <div class="flex items-center justify-center">
                <div class="flex flex-col items-center">
                    <!-- Loading Spinner -->
                    <svg
                        class="animate-spin h-12 w-12 text-red-600 mb-4"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                    <!-- Deletion Message -->
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        Deleting Files
                    </h3>
                    <p class="text-sm text-gray-600 text-center">
                        {{ deletionMessage }}
                    </p>
                    <div class="mt-4 text-xs text-gray-500">
                        Please wait, this may take a moment...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        v-if="
            (status != 'PROCESSING UPLOADED FILES' &&
                status != '' &&
                status != null) ||
            precentageUpload > 0
        "
        class="w-full h-screen mx-84 px-10 fixed block top-0 left-0 bg-white opacity-90 z-50"
    >
        <div
            role="status"
            class="absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2"
        >
            <svg
                aria-hidden="true"
                class="w-8 h-8 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                viewBox="0 0 100 101"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                    fill="currentColor"
                />
                <path
                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                    fill="currentFill"
                />
            </svg>
            <div class="mt-4 w-64 h-84">
                <div class="h-2 mb-2 text-xs flex rounded-md bg-gray-200">
                    <div
                        :style="'width: ' + precentageUpload + '%'"
                        class="shadow-none flex rounded-md flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"
                    ></div>
                </div>
                {{ status }}&emsp;({{ uploadedFilesCount }}/{{
                    totalFilesCount
                }})
            </div>
            <div class="w-64 text-gray-700 truncate">
                <small
                    ><i>{{ currentLog }}</i></small
                >
            </div>
            <button
                v-if="Object.keys(logs).length > 0 && !isDeletingFiles"
                class="mt-4 text-sm cursor-pointer bg-white-900"
                @click="showLogsDialog = true"
            >
                <InformationCircleIcon
                    class="h-5 w-5 inline flex-shrink-0 mx-auto"
                    aria-hidden="true"
                />
                View logs
            </button>
        </div>
    </div>
    <jet-confirmation-modal
        :show="fsoBeingDeleted"
        @close="fsoBeingDeleted = null"
    >
        <template #title> Delete </template>

        <template #content>
            Are you sure you would like to delete
            {{ $page.props.selectedFileSystemObject.name }}?
        </template>

        <template #footer>
            <jet-secondary-button @click="fsoBeingDeleted = null">
                Cancel
            </jet-secondary-button>

            <jet-danger-button class="ml-2" @click="deleteFSO">
                Delete
            </jet-danger-button>
        </template>
    </jet-confirmation-modal>

    <jet-confirmation-modal
        :show="missing_files > 0 && showMissingFilesDetails"
        @close="showMissingFilesDetails = null"
    >
        <template #title> Missing Files </template>

        <template #content>
            Following files are mising <br />
            <span v-for="file in missing_files_list">
                {{ file.relative_url }} <br />
            </span>
        </template>

        <template #footer>
            <jet-secondary-button @click="showMissingFilesDetails = null">
                Cancel
            </jet-secondary-button>
        </template>
    </jet-confirmation-modal>
</template>
<script>
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import { Dropzone } from "dropzone";
import axiosRetry from "axios-retry";
import FileDetails from "@/Shared/FileDetails.vue";
import SelectInput from "@/Shared/SelectInput.vue";
import ToolTip from "@/Shared/ToolTip.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import {
    FolderIcon,
    DocumentTextIcon,
    ChevronRightIcon,
    HomeIcon,
    InformationCircleIcon,
    EllipsisVerticalIcon,
    ArrowUpTrayIcon,
    CheckIcon,
    ExclamationCircleIcon,
    TrashIcon,
    ArrowDownTrayIcon,
} from "@heroicons/vue/24/solid";
import ChecksumCalculator from "@/Utils/ChecksumCalculator.js";

export default {
    components: {
        FolderIcon,
        DocumentTextIcon,
        ArrowDownTrayIcon,
        ChevronRightIcon,
        InformationCircleIcon,
        HomeIcon,
        FileDetails,
        JetDialogModal,
        JetSecondaryButton,
        EllipsisVerticalIcon,
        ExclamationCircleIcon,
        ArrowUpTrayIcon,
        CheckIcon,
        SelectInput,
        TrashIcon,
        ToolTip,
        JetConfirmationModal,
        JetDangerButton,
    },
    props: ["draft", "readonly", "height", "project"],

    emits: ["loading", "proceed"],

    data() {
        return {
            status: "",
            dropzone: null,
            fullScreen: false,
            precentageUpload: 0,
            busy: false,
            loading: false,
            file: null,
            url: null,
            logs: {},
            logFilter: "Error",
            logFilters: ["Error", "Success", "Queued", "Inprogress"],
            uploadBatchErrors: [],
            showErrorBatchLogs: false,
            showLogsDialog: false,
            currentLog: null,
            fsoBeingDeleted: null,
            showMissingFilesDetails: null,
            missing_files: 0,
            missing_files_list: [],
            // Deletion progress
            isDeletingFiles: false,
            deletionMessage: "",
            // Tree expansion state
            expandedFolders: new Set(),
            // Step tracking for clearing state on step changes
            currentStep: null,
        };
    },
    computed: {
        baseURL() {
            return String(this.$page.props.url);
        },
        filteredLogs() {
            let logsClone = JSON.parse(JSON.stringify(this.logs));
            Object.keys(logsClone).forEach((key) => {
                if (logsClone[key].status != this.logFilter)
                    delete logsClone[key];
            });
            return logsClone;
        },
        nmriumURL() {
            return this.$page.props.nmriumURL
                ? String(this.$page.props.nmriumURL)
                : "//nmriumdev.nmrxiv.org";
        },
        downloadURL() {
            if (this.$page.props.selectedFileSystemObject.download_url) {
                return this.$page.props.selectedFileSystemObject.download_url;
            } else {
                if (this.project) {
                    if (
                        this.$page.props.selectedFileSystemObject &&
                        this.$page.props.selectedFileSystemObject
                            .relative_url == "/"
                    ) {
                        return this.project.download_url;
                    } else {
                        return (
                            this.baseURL +
                            "/" +
                            this.project.owner.username +
                            "/download/" +
                            this.project.slug +
                            "?key=" +
                            this.$page.props.selectedFileSystemObject.name +
                            "&uuid=" +
                            this.$page.props.selectedFileSystemObject.uuid
                        );
                    }
                } else {
                    return null;
                }
            }
        },

        // Watch for URL changes to detect step changes
        currentUrl() {
            return window.location.search;
        },
    },

    watch: {
        // Watch for URL parameter changes
        currentUrl: {
            handler() {
                this.handleURLChange();
            },
            immediate: false,
        },
    },

    mounted() {
        if (this.draft) {
            this.url =
                this.baseURL + "/dashboard/drafts/" + this.draft.id + "/files";
            this.loadDropZone();
        }

        // Initialize current step and set up step change detection
        this.initializeStepTracking();

        // Restore expanded state from URL
        this.restoreExpandedStateFromURL();

        // Listen for popstate events (browser back/forward) to detect URL changes
        window.addEventListener("popstate", this.handleURLChange);

        // Listen for global proceed events
        window.addEventListener("file-browser-proceed", this.handleProceed);
    },

    beforeUnmount() {
        // Clear file tree state before component unmounts
        this.clearURLParameters();

        // Clean up event listeners
        window.removeEventListener("popstate", this.handleURLChange);
        window.removeEventListener("file-browser-proceed", this.handleProceed);
    },
    methods: {
        /**
         * Initialize step tracking from current URL
         */
        initializeStepTracking() {
            const urlParams = new URLSearchParams(window.location.search);
            this.currentStep = urlParams.get("step");
        },

        /**
         * Handle URL changes to detect step changes
         */
        handleURLChange() {
            const urlParams = new URLSearchParams(window.location.search);
            const newStep = urlParams.get("step");

            // If step has changed, clear expanded and selected state
            if (newStep !== this.currentStep) {
                this.clearFileTreeState();
                this.currentStep = newStep;
            }
        },

        /**
         * Clear file tree state when step changes
         */
        clearFileTreeState() {
            // Clear expanded folders
            this.expandedFolders.clear();

            // Reset selection to root
            if (this.file) {
                this.$page.props.selectedFileSystemObject = this.file;
                this.$page.props.selectedFolder = "/";
            }

            // Remove expanded and selected parameters from URL
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.delete("expanded");
            urlParams.delete("selected");

            // Update URL without the file tree state parameters
            const newUrl = `${
                window.location.pathname
            }?${urlParams.toString()}`;
            window.history.replaceState({}, "", newUrl);

            // Force UI refresh to reflect the cleared state
            this.$nextTick(() => {
                this.forceRefreshExpandedState();
            });
        },

        /**
         * Check for step changes (can be called externally)
         */
        checkForStepChange() {
            this.handleURLChange();
        },

        /**
         * Manually clear file tree state (can be called from parent components)
         */
        clearState() {
            this.clearFileTreeState();
        },

        /**
         * Handle proceed button click - clear file tree state
         */
        handleProceed() {
            this.clearFileTreeState();
            this.$emit("proceed");
        },

        /**
         * Clear expanded and selected parameters from URL (lightweight version)
         */
        clearURLParameters() {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.delete("expanded");
            urlParams.delete("selected");

            const newUrl = `${
                window.location.pathname
            }?${urlParams.toString()}`;
            window.history.replaceState({}, "", newUrl);
        },

        /**
         * Helper to trigger proceed event globally (for external use)
         * Usage from anywhere: window.dispatchEvent(new Event('file-browser-proceed'))
         */

        /**
         * Calculate checksums for uploaded files before processing
         */
        async calculateChecksumsForFiles(files) {
            this.status = "CALCULATING CHECKSUMS";

            // Filter out directories and unreadable files

            // First pass: basic filtering
            const potentialFiles = files.filter((file) => {
                const fileName = file.fullPath || file.name;

                // Skip directories and invalid files
                if (file.size === 0 && file.type === "") return false;
                if (fileName.endsWith("/") || fileName.endsWith("\\"))
                    return false;
                if (typeof file.slice !== "function") return false;
                if (
                    file.size === 0 &&
                    fileName.includes("/") &&
                    !fileName.includes(".")
                )
                    return false;

                return true;
            });

            // Second pass: test file readability to catch pseudo-files
            const actualFiles = [];
            for (const file of potentialFiles) {
                const fileName = file.fullPath || file.name;
                try {
                    // Test if we can actually read from this file
                    const testChunk = file.slice(0, Math.min(1, file.size));

                    await new Promise((resolve, reject) => {
                        const reader = new FileReader();
                        reader.onload = () => resolve();
                        reader.onerror = () => reject(reader.error);
                        reader.readAsArrayBuffer(testChunk);
                    });

                    actualFiles.push(file);
                } catch (error) {
                    // Skip this file silently
                }
            }

            if (actualFiles.length === 0) {
                this.status = "READY";
                return;
            }

            const progressTracker =
                ChecksumCalculator.createMultiFileProgressTracker(
                    actualFiles,
                    (progress) => {
                        this.status = `CALCULATING CHECKSUMS (${progress}%)`;
                    }
                );

            const checksumPromises = actualFiles.map(async (file) => {
                try {
                    const fileName = file.fullPath || file.name;

                    // Update log status
                    if (this.logs[fileName]) {
                        this.logs[fileName].status = "Calculating checksum";
                        this.logs[fileName].messages.push(
                            "Starting checksum calculation"
                        );
                    }

                    // Calculate both MD5 and SHA-256 for maximum compatibility
                    const checksums =
                        await ChecksumCalculator.calculateBothChecksums(
                            file,
                            (progress) => {
                                if (this.logs[fileName]) {
                                    this.logs[fileName].messages[
                                        this.logs[fileName].messages.length - 1
                                    ] = `Calculating checksum: ${progress}%`;
                                }
                                progressTracker(fileName, progress);
                            }
                        );

                    // Store checksums in file object for later use
                    file.checksums = checksums;

                    // Update log with success
                    if (this.logs[fileName]) {
                        this.logs[fileName].messages.push(
                            `Checksums calculated - MD5: ${checksums.md5.substring(
                                0,
                                8
                            )}..., SHA256: ${checksums.sha256.substring(
                                0,
                                8
                            )}...`
                        );
                    }
                } catch (error) {
                    console.error(
                        `Error calculating checksums for ${file.name}:`,
                        error
                    );

                    const fileName = file.fullPath || file.name;
                    if (this.logs[fileName]) {
                        this.logs[fileName].status = "Error";
                        this.logs[fileName].messages.push(
                            `Checksum calculation failed: ${error.message}`
                        );
                    }

                    // Don't fail the entire upload for checksum errors
                    file.checksums = null;
                }
            });

            await Promise.all(checksumPromises);

            this.status = "CHECKSUMS CALCULATED";
            console.log(
                "All checksums calculated, proceeding with upload preparation"
            );
        },

        confirmFSODeletion() {
            this.fsoBeingDeleted = this.$page.props.selectedFileSystemObject.id;
        },
        deleteFSO() {
            if (this.$page.props.selectedFileSystemObject.id) {
                this.fsoBeingDeleted = null;

                // Store the deleted object and its parent for reverting selection
                const deletedObject = this.$page.props.selectedFileSystemObject;
                const parentFolder = this.findParentFolder(
                    this.file,
                    deletedObject
                );

                // Show deletion progress overlay
                this.isDeletingFiles = true;
                this.deletionMessage = `Removing "${deletedObject.name}" and all its contents...`;

                this.updateBusyStatus(true);
                this.$emit("loading", true);

                axios
                    .delete(
                        "/dashboard/drafts/" +
                            this.draft.id +
                            "/files/" +
                            deletedObject.id
                    )
                    .then(async (response) => {
                        // Hide deletion overlay
                        this.isDeletingFiles = false;
                        this.deletionMessage = "";

                        this.updateBusyStatus(false);
                        this.$emit("loading", false);

                        if (response.data.success) {
                            // Show success message with details
                            let message = response.data.message;
                            if (response.data.has_storage_errors) {
                                message += ` Note: ${response.data.storage_errors.length} storage operation(s) had issues.`;
                            }

                            // Clear logs after successful deletion
                            this.logs = {};

                            // Store parent info for reversion after tree refresh
                            const parentInfo = parentFolder
                                ? {
                                      id: parentFolder.id,
                                      name: parentFolder.name,
                                      relative_url: parentFolder.relative_url,
                                  }
                                : null;

                            // Refresh the file tree and then revert selection
                            this.annotateAndRevertToParent(
                                deletedObject,
                                parentInfo
                            );
                        } else {
                            console.error(
                                "Deletion failed:",
                                response.data.message
                            );
                            // You can add error toast notification here
                        }
                    })
                    .catch((error) => {
                        // Hide deletion overlay
                        this.isDeletingFiles = false;
                        this.deletionMessage = "";

                        this.updateBusyStatus(false);
                        this.$emit("loading", false);

                        console.error("Deletion request failed:", error);

                        if (
                            error.response &&
                            error.response.data &&
                            error.response.data.message
                        ) {
                            console.error(
                                "Server error:",
                                error.response.data.message
                            );
                            // You can add error toast notification here
                        } else {
                            console.error("Network or unknown error occurred");
                        }
                    });
            }
        },
        showMissingFilesDetailsModal() {
            this.fetchMissingFiles();
            this.showMissingFilesDetails = true;
        },
        fetchMissingFiles() {
            axios
                .get("/dashboard/drafts/" + this.draft.id + "/missing-files")
                .then((response) => {
                    console.log(response);
                    this.missing_files_list = response.data.missing_files;
                });
        },
        toggleFullScreen() {
            this.fullScreen = !this.fullScreen;
        },
        toggleShowLogsDialog() {
            this.showLogsDialog = !this.showLogsDialog;
        },
        updateBusyStatus(status) {
            this.busy = status;
        },
        loadFiles() {
            this.updateBusyStatus(true);
            if (this.draft) {
                axios.get(this.url).then((response) => {
                    this.file = response.data.file;
                    this.file.has_children = true;
                    this.updateBusyStatus(true);
                    this.$emit("loading", false);
                    this.loading = false;
                    this.missing_files = response.data.missing_files;

                    // Apply expanded state and select last expanded folder
                    this.$nextTick(async () => {
                        this.applyExpandedState(this.file);

                        // Fetch any missing folder contents for expanded folders
                        await this.fetchMissingFolderContents();

                        // Now that all folder contents are loaded, select the target object
                        if (this.expandedFolders.size > 0) {
                            await this.selectLastExpandedFolder();
                        } else {
                            this.$page.props.selectedFileSystemObject =
                                this.file;
                            this.$page.props.selectedFolder = "/";
                        }

                        // Force refresh to ensure UI updates
                        this.forceRefreshExpandedState();
                    });
                });
            } else {
                this.file = this.$page.props.selectedFileSystemObject;
            }
        },
        annotate() {
            this.updateBusyStatus(true);
            this.$emit("loading", true);
            this.loading = true;
            this.status = "PROCESSING UPLOADED FILES";
            axios
                .get("/dashboard/drafts/" + this.draft.id + "/annotate")
                .then(() => {
                    this.updateBusyStatus(false);
                    this.status = null;
                    this.loadFiles();
                });
        },

        /**
         * Annotate files and then revert to parent folder after deletion
         */
        async annotateAndRevertToParent(deletedObject, parentInfo) {
            this.updateBusyStatus(true);
            this.$emit("loading", true);
            this.loading = true;
            this.status = "PROCESSING UPLOADED FILES";

            try {
                await axios.get(
                    "/dashboard/drafts/" + this.draft.id + "/annotate"
                );
                this.updateBusyStatus(false);
                this.status = null;

                // Load files and then revert to parent
                await new Promise((resolve) => {
                    const originalLoadFiles = this.loadFiles;
                    this.loadFiles = () => {
                        originalLoadFiles.call(this);

                        // Wait for the next tick to ensure file tree is loaded, then revert
                        this.$nextTick(async () => {
                            await this.revertToParentAfterDeletion(
                                deletedObject,
                                parentInfo
                            );
                            resolve();
                        });
                    };
                    this.loadFiles();
                    // Restore original loadFiles method
                    this.loadFiles = originalLoadFiles;
                });
            } catch (error) {
                console.error("Error during annotation:", error);
                this.updateBusyStatus(false);
                this.status = null;
                this.loading = false;
                this.$emit("loading", false);

                // Still try to revert to parent even if annotation fails
                await this.revertToParentAfterDeletion(
                    deletedObject,
                    parentInfo
                );
            }
        },
        processFilesDZL(vm, filesBatch) {
            vm.batchesCount += 1;
            const url = "/dashboard/storage/signed-draft-storage-url";
            const client = axios.create({ baseURL: window.location.origin });
            vm.currentLog = "Fetching temporary storage url";

            // Ensure checksums are included in the file data
            const filesWithChecksums = filesBatch.map((file) => ({
                upload: file.upload || {
                    filename: file.name,
                    total: file.size,
                },
                fullPath: file.fullPath || file.webkitRelativePath || file.name,
                checksums: file.checksums || null, // Include calculated checksums
            }));

            axiosRetry(client, {
                retries: 3,
                retryCondition: (error) => {
                    // console.log(
                    //     "retring failed upload requests - Signed storage URL"
                    // );
                    return (
                        error.response.status === 500 ||
                        error.response.status === 502
                    );
                },
            });
            client
                .post(url, {
                    draft_files: filesWithChecksums,
                    destination: vm.$page.props.selectedFolder,
                    draft_id: vm.draft.id,
                })
                .catch((err) => {
                    this.processedBatches += 1;
                    if (this.processedBatches == this.batches) {
                        if (this.dropzone.files.length > 0) {
                            this.status = "ERROR UPLOADING FILES";
                            this.updateBusyStatus(false);
                            this.dropzone.files.forEach((file) => {
                                let message = "Upload failed";
                                if (file.fullPath) {
                                    vm.logs[file.fullPath].status = "Error";
                                    vm.logs[file.fullPath].messages.push(
                                        message +
                                            " (API call failed with status code:" +
                                            err.response.status +
                                            ") "
                                    );
                                } else {
                                    vm.logs[file.name].status = "Error";
                                    vm.logs[file.name].messages.push(
                                        message +
                                            "(API call failed with status code:" +
                                            err.response.status +
                                            ")"
                                    );
                                }
                            });
                        }
                    }
                    this.uploadBatchErrors.push(err.response.data);
                    // console.log(
                    //     "Error retrieving signed storage URLS",
                    //     err.response
                    // );
                    // if (
                    //     err.response.status !== 200 ||
                    //     err.response.status !== 201
                    // ) {
                    //     throw new Error(
                    //         `API call failed with status code: ${err.response.status}`
                    //     );
                    // }
                })
                .then((response) => {
                    if (response) {
                        vm.currentLog =
                            "Uploading files to temporary storage url";
                        let data = response.data;
                        this.processedBatches += 1;
                        data.forEach((u) => {
                            let cFile = vm.dropzone.files.find((f) => {
                                if (f.fullPath) {
                                    return f.fullPath.trim() == u.fullPath;
                                } else {
                                    return (
                                        "/" + f.name.trim() == u.fullPath ||
                                        vm.$page.props.selectedFolder +
                                            "/" +
                                            f.name.trim() ==
                                            u.fullPath
                                    );
                                }
                            });
                            if (cFile) {
                                let message =
                                    "Presigned Upload URL receieved.  Starting Upload.";
                                if (cFile.fullPath) {
                                    vm.logs[cFile.fullPath].status =
                                        "Inprogress";
                                    vm.logs[cFile.fullPath].messages.push(
                                        message
                                    );
                                } else {
                                    vm.logs[cFile.name].status = "Inprogress";
                                    vm.logs[cFile.name].messages.push(message);
                                }

                                let headers = u.headers;
                                if ("Host" in headers) {
                                    delete headers.Host;
                                }
                                cFile.uploadURL = u.url;
                                setTimeout(() =>
                                    vm.dropzone.processFile(cFile)
                                );
                            }
                        });
                    }
                });
        },
        loadDropZone() {
            this.$nextTick(() => {
                const vm = this;
                vm.totalFilesCount = 0;
                vm.uploadedFilesCount = 0;
                vm.batchCount = 100;
                vm.count = 0;
                vm.batches = 0;
                vm.processedBatches = 0;

                vm.$page.props.selectedFileSystemObject = vm.file;
                vm.$page.props.selectedFolder = "/";

                vm.selectedFSO = [];

                let options = {
                    url: "/",
                    method: "put",
                    sending(file, xhr) {
                        let _send = xhr.send;
                        xhr.send = () => {
                            _send.call(xhr, file);
                        };
                    },
                    autoProcessQueue: false,
                    uploadMultiple: true,
                    disablePreviews: true,
                    parallelUploads: 100,
                    useFsAccessApi: false,
                    autoQueue: false,
                    maxFiles: 20000,
                    clickable: "#fs-dropzone-click-target",
                    hiddenInputContainer: "#fs-dropzone-hidden-input-container",
                    dictDefaultMessage: document.querySelector(
                        "#fs-dropzone-message"
                    ).innerHTML,
                    totaluploadprogress: function (progress) {
                        vm.progress = Math.ceil(progress);
                    },
                };
                vm.dropzone = new Dropzone("#fs-dropzone", options);
                vm.dropzone.hiddenFileInput.setAttribute(
                    "webkitdirectory",
                    true
                );

                vm.dropzone.on("processing", (file) => {
                    vm.dropzone.options.url = file.uploadURL;
                    vm.status = "UPLOAD IN PROGRESS";
                });
                vm.dropzone.on("success", (file) => {
                    let message = "Upload complete";
                    if (file.fullPath) {
                        vm.logs[file.fullPath].status = "Success";
                        vm.logs[file.fullPath].messages.push(message);
                    } else {
                        vm.logs[file.name].status = "Success";
                        vm.logs[file.name].messages.push(message);
                    }
                    vm.uploadedFilesCount += 1;
                    vm.precentageUpload =
                        (vm.uploadedFilesCount / vm.totalFilesCount) * 100;
                    vm.currentLog = file.fullPath;
                });
                vm.dropzone.on("error", (file) => {
                    let message = "Upload failed";
                    if (file.fullPath) {
                        vm.logs[file.fullPath].status = "Error";
                        vm.logs[file.fullPath].messages.push(message);
                    } else {
                        vm.logs[file.name].status = "Error";
                        vm.logs[file.name].messages.push(message);
                    }
                });
                vm.dropzone.on("addedfile", (file) => {
                    if (file.fullPath) {
                        vm.logs[file.fullPath] = {
                            status: "Queued",
                            messages: [],
                        };
                    } else if (file.webkitRelativePath) {
                        file.fullPath = file.webkitRelativePath;
                        vm.logs[file.webkitRelativePath] = {
                            status: "Queued",
                            messages: [],
                        };
                    } else {
                        vm.logs[file.name] = {
                            status: "Queued",
                            messages: [],
                        };
                    }
                    vm.selectedFSO.push(file);
                });
                vm.dropzone.on("addedfiles", (files) => {
                    if (files.length > 0) {
                        this.updateBusyStatus(true);

                        // Convert FileList to array for processing
                        let filesArray = [];
                        for (let i = 0; i < files.length; i++) {
                            filesArray.push(files[i]);
                        }

                        // Extract real File objects from Dropzone wrappers if needed
                        const realFilesArray = filesArray.map((file) => {
                            // If it's a Dropzone wrapper object, try to extract the real File
                            if (
                                "upload" in file &&
                                file.upload &&
                                file.upload.file
                            ) {
                                return file.upload.file;
                            }
                            // If it doesn't have slice method but has other properties, it might be wrapped
                            if (
                                typeof file.slice !== "function" &&
                                file instanceof File === false
                            ) {
                                // Try to find the original file object in the wrapper
                                for (const key in file) {
                                    if (file[key] instanceof File) {
                                        return file[key];
                                    }
                                }
                            }
                            return file; // Return as-is if it seems to be a real File object
                        });

                        // Calculate checksums for all files before processing
                        vm.calculateChecksumsForFiles(realFilesArray).then(
                            () => {
                                setTimeout(() => {
                                    var timer = setInterval(function () {
                                        if (
                                            vm.totalFilesCount ===
                                            vm.selectedFSO.length
                                        ) {
                                            clearInterval(timer);
                                            vm.status = "BATCH UPLOAD STARTED";
                                            for (
                                                let i = 0;
                                                i < vm.totalFilesCount;
                                                i += vm.batchCount
                                            ) {
                                                let filesBatch =
                                                    vm.dropzone.files.slice(
                                                        i,
                                                        i + vm.batchCount
                                                    );
                                                vm.batches += 1;
                                                vm.processFilesDZL(
                                                    vm,
                                                    filesBatch
                                                );
                                            }
                                        } else {
                                            vm.totalFilesCount =
                                                vm.selectedFSO.length;
                                            vm.status = "CALCULATING CHECKSUMS";
                                        }
                                    }, 500);
                                });
                            }
                        );
                    }
                });
                vm.dropzone.on("queuecomplete", () => {
                    vm.status = "UPLOAD COMPLETE";
                    vm.annotate();
                    vm.currentLog = null;
                    vm.dropzone.removeAllFiles();
                    vm.precentageUpload = 0;
                    vm.totalFilesCount = 0;
                    vm.uploadedFilesCount = 0;
                    this.updateBusyStatus(false);
                    setTimeout(() => {
                        vm.status = null;
                    }, 5000);
                });
            });
        },
        displaySelected(file) {
            this.$page.props.selectedFileSystemObject = file;
            let sFolder = "/";
            if (this.$page.props.selectedFileSystemObject.name == "/") {
                sFolder = "/";
            } else {
                if (this.$page.props.selectedFileSystemObject.type != "file") {
                    sFolder =
                        this.$page.props.selectedFileSystemObject.relative_url;
                } else {
                    if (
                        this.$page.props.selectedFileSystemObject.parent_id ==
                        null
                    ) {
                        sFolder = "/";
                    } else {
                        sFolder =
                            this.$page.props.selectedFileSystemObject.relative_url.replace(
                                "/" +
                                    this.$page.props.selectedFileSystemObject
                                        .name,
                                ""
                            );
                    }
                }
            }

            this.$page.props.selectedFolder = sFolder;

            if (file.has_children && file.level > 0 && !file.children) {
                file.loading = true;
                axios
                    .get("/api/v1/files/children/" + file.id)
                    .then((response) => {
                        file.children = response.data.files[0].children;
                        file.loading = false;
                    });
            }
        },

        /**
         * Track when a folder is expanded/collapsed
         */
        toggleFolderExpansion(fsoId, isExpanded) {
            if (isExpanded) {
                this.expandedFolders.add(fsoId);
            } else {
                this.expandedFolders.delete(fsoId);
            }
            this.updateURLWithExpandedState();
        },

        /**
         * Update URL with current expanded state
         */
        updateURLWithExpandedState() {
            const urlParams = new URLSearchParams(window.location.search);

            if (this.expandedFolders.size > 0) {
                urlParams.set(
                    "expanded",
                    Array.from(this.expandedFolders).join(",")
                );
            } else {
                urlParams.delete("expanded");
            }

            // Also save the currently selected folder
            if (
                this.$page.props.selectedFileSystemObject &&
                this.$page.props.selectedFileSystemObject.id
            ) {
                urlParams.set(
                    "selected",
                    this.$page.props.selectedFileSystemObject.id
                );
            }

            const newUrl = `${
                window.location.pathname
            }?${urlParams.toString()}`;
            window.history.replaceState({}, "", newUrl);
        },

        /**
         * Restore expanded state from URL parameters
         */
        restoreExpandedStateFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            const expandedParam = urlParams.get("expanded");

            if (expandedParam) {
                this.expandedFolders = new Set(
                    expandedParam
                        .split(",")
                        .filter((id) => id)
                        .map((id) => parseInt(id))
                );
            }
        },

        /**
         * Fetch missing folder contents for expanded folders that don't have children loaded
         */
        async fetchMissingFolderContents() {
            if (!this.file || this.expandedFolders.size === 0) return;

            let iterationCount = 0;
            const maxIterations = 10; // Prevent infinite loops

            // Keep checking for missing folders until all are loaded
            while (iterationCount < maxIterations) {
                // Find folders that are marked as expanded but don't have children loaded
                const missingFolders = [];
                this.findMissingExpandedFolders(this.file, missingFolders);

                if (missingFolders.length === 0) {
                    break;
                }

                // Fetch children for all missing folders in parallel
                const fetchPromises = missingFolders.map((folder) =>
                    this.fetchFolderChildren(folder)
                );

                try {
                    await Promise.all(fetchPromises);

                    // Re-apply expanded state after loading
                    this.applyExpandedState(this.file);
                } catch (error) {
                    console.error(
                        "Error loading missing folder contents:",
                        error
                    );
                    break; // Exit on error to prevent infinite loop
                }

                iterationCount++;
            }

            // Final UI refresh after all loading is complete
            this.$nextTick(() => {
                this.forceRefreshExpandedState();
            });
        },

        /**
         * Recursively find folders that should be expanded but don't have children loaded
         */
        findMissingExpandedFolders(fileObject, missingFolders) {
            if (!fileObject) return;

            // Check if this folder should be expanded but doesn't have children loaded
            if (fileObject.id && this.expandedFolders.has(fileObject.id)) {
                if (
                    fileObject.has_children &&
                    (!fileObject.children || fileObject.children.length === 0)
                ) {
                    // Avoid duplicate entries
                    if (!missingFolders.find((f) => f.id === fileObject.id)) {
                        missingFolders.push(fileObject);
                    }
                }
            }

            // Also check if the currently selected folder needs its children loaded
            if (
                fileObject.id &&
                this.$page.props.selectedFileSystemObject &&
                this.$page.props.selectedFileSystemObject.id ===
                    fileObject.id &&
                fileObject.has_children &&
                (!fileObject.children || fileObject.children.length === 0)
            ) {
                // Avoid duplicate entries
                if (!missingFolders.find((f) => f.id === fileObject.id)) {
                    missingFolders.push(fileObject);
                }
            }

            // Recursively check children (even if current folder is missing children,
            // we might have partial data for some subfolders)
            if (fileObject.children && Array.isArray(fileObject.children)) {
                fileObject.children.forEach((child) => {
                    if (child.type === "directory") {
                        this.findMissingExpandedFolders(child, missingFolders);
                    }
                });
            }
        },

        /**
         * Fetch children for a specific folder
         */
        async fetchFolderChildren(folder) {
            if (folder.loading) return; // Already loading

            folder.loading = true;

            try {
                const response = await axios.get(
                    `/api/v1/files/children/${folder.id}`
                );

                // Validate response structure
                if (
                    response.data &&
                    response.data.files &&
                    response.data.files[0] &&
                    response.data.files[0].children
                ) {
                    folder.children = response.data.files[0].children;
                } else {
                    folder.children = []; // Set empty array to prevent repeated requests
                }
            } catch (error) {
                console.error(
                    `Failed to load children for ${folder.name}:`,
                    error
                );

                // Set empty array to prevent repeated requests for this folder
                folder.children = [];

                // If this is a 404 or similar, the folder might not exist anymore
                if (error.response && error.response.status === 404) {
                    this.expandedFolders.delete(folder.id);
                    this.updateURLWithExpandedState();
                }
            } finally {
                folder.loading = false;
            }
        },

        /**
         * Check if a folder should be expanded
         */
        isFolderExpanded(fsoId) {
            return this.expandedFolders.has(fsoId);
        },

        /**
         * Apply expanded state to file tree after refresh
         */
        applyExpandedState(fileObject) {
            if (!fileObject) return;

            // Check if this folder should be expanded
            if (fileObject.id && this.expandedFolders.has(fileObject.id)) {
                fileObject.isExpanded = true;
            }

            // Recursively apply to children
            if (fileObject.children && Array.isArray(fileObject.children)) {
                fileObject.children.forEach((child) => {
                    this.applyExpandedState(child);
                });
            }
        },

        /**
         * Force refresh expanded folders in the UI
         */
        forceRefreshExpandedState() {
            // Force re-render by updating a reactive property
            this.$nextTick(() => {
                this.applyExpandedState(this.file);
                this.$forceUpdate();
            });
        },

        /**
         * Find and select the last expanded folder for right panel
         */
        async selectLastExpandedFolder() {
            const urlParams = new URLSearchParams(window.location.search);
            const selectedParam = urlParams.get("selected");

            let targetId = null;

            // First try to use the selected parameter from URL
            if (selectedParam) {
                targetId = parseInt(selectedParam);
            } else {
                // Fall back to last expanded folder
                const expandedIds = Array.from(this.expandedFolders);
                if (expandedIds.length === 0) return;
                targetId = expandedIds[expandedIds.length - 1];
            }

            // Find the object (file or folder) in the file tree
            const selectedObject = this.findObjectById(this.file, targetId);
            if (selectedObject) {
                this.$page.props.selectedFileSystemObject = selectedObject;
                this.updateSelectedFolder(selectedObject);

                // Handle based on object type
                if (selectedObject.type === "directory") {
                    // If this folder has children flag but no children loaded, fetch them
                    if (
                        selectedObject.has_children &&
                        (!selectedObject.children ||
                            selectedObject.children.length === 0)
                    ) {
                        await this.fetchFolderChildren(selectedObject);

                        // Update the selected object with the newly loaded children
                        this.$page.props.selectedFileSystemObject =
                            selectedObject;
                    }
                } else {
                    // File selected - expand all parent folders leading to this file
                    await this.expandPathToObject(selectedObject);

                    // Re-find the file object after all parents' children are loaded (reference might have changed)
                    const updatedFileObject = this.findObjectById(
                        this.file,
                        targetId
                    );
                    if (updatedFileObject) {
                        this.$page.props.selectedFileSystemObject =
                            updatedFileObject;
                        this.updateSelectedFolder(updatedFileObject);
                    }
                }
            }
        },

        /**
         * Recursively find an object (file or folder) by ID in the file tree
         */
        findObjectById(fileObject, targetId) {
            if (!fileObject) return null;

            if (fileObject.id === targetId) {
                return fileObject;
            }

            if (fileObject.children && Array.isArray(fileObject.children)) {
                for (const child of fileObject.children) {
                    const found = this.findObjectById(child, targetId);
                    if (found) return found;
                }
            }

            return null;
        },

        /**
         * Find the parent folder of a given file or folder object
         */
        findParentFolder(fileObject, targetObject, parent = null) {
            if (!fileObject) return null;

            // If we find the target object, return its parent
            if (fileObject.id === targetObject.id) {
                return parent;
            }

            if (fileObject.children && Array.isArray(fileObject.children)) {
                for (const child of fileObject.children) {
                    const found = this.findParentFolder(
                        child,
                        targetObject,
                        fileObject
                    );
                    if (found) return found;
                }
            }

            return null;
        },

        /**
         * Expand all parent folders leading to a specific object
         */
        async expandPathToObject(targetObject) {
            const pathFolders = [];
            this.buildPathToObject(this.file, targetObject, [], pathFolders);

            // Add all path folders to expanded folders and load their children if needed
            for (const folder of pathFolders) {
                if (folder.has_children && folder.id) {
                    this.expandedFolders.add(folder.id);

                    // Load children if not already loaded
                    if (!folder.children || folder.children.length === 0) {
                        await this.fetchFolderChildren(folder);
                    }
                }
            }

            this.updateURLWithExpandedState();
        },

        /**
         * Build array of all folders in path to target object
         */
        buildPathToObject(currentObject, targetObject, currentPath, result) {
            if (!currentObject) return false;

            // Add current object to path if it's a directory
            if (
                currentObject.type === "directory" ||
                currentObject.name === "/"
            ) {
                currentPath = [...currentPath, currentObject];
            }

            // If we found the target, save the path
            if (currentObject.id === targetObject.id) {
                result.push(...currentPath);
                return true;
            }

            // Search in children
            if (
                currentObject.children &&
                Array.isArray(currentObject.children)
            ) {
                for (const child of currentObject.children) {
                    if (
                        this.buildPathToObject(
                            child,
                            targetObject,
                            currentPath,
                            result
                        )
                    ) {
                        return true;
                    }
                }
            }

            return false;
        },

        /**
         * Update selected folder for right panel
         */
        updateSelectedFolder(folder) {
            let sFolder = "/";
            if (folder.name == "/") {
                sFolder = "/";
            } else {
                if (folder.type != "file") {
                    sFolder = folder.relative_url;
                } else {
                    if (folder.parent_id == null) {
                        sFolder = "/";
                    } else {
                        sFolder = folder.relative_url.replace(
                            "/" + folder.name,
                            ""
                        );
                    }
                }
            }
            this.$page.props.selectedFolder = sFolder;
        },

        /**
         * Revert selection to parent folder after a file/folder is deleted
         */
        async revertToParentAfterDeletion(deletedObject, parentInfo) {
            // Remove the deleted object from expanded folders if it was a folder
            if (deletedObject.type === "directory" && deletedObject.id) {
                this.expandedFolders.delete(deletedObject.id);
            }

            if (parentInfo && parentInfo.id) {
                // Find the parent folder in the refreshed tree
                const parentFolder = this.findObjectById(
                    this.file,
                    parentInfo.id
                );

                if (parentFolder) {
                    // Select the parent folder
                    this.$page.props.selectedFileSystemObject = parentFolder;
                    this.updateSelectedFolder(parentFolder);

                    // Ensure parent folder is in expanded folders if it has children
                    if (parentFolder.has_children) {
                        this.expandedFolders.add(parentFolder.id);
                    }

                    // Load parent folder's children if needed
                    if (
                        parentFolder.has_children &&
                        (!parentFolder.children ||
                            parentFolder.children.length === 0)
                    ) {
                        await this.fetchFolderChildren(parentFolder);
                    }

                    // Update URL with new selection and expanded state
                    const urlParams = new URLSearchParams(
                        window.location.search
                    );
                    urlParams.set("selected", parentFolder.id);

                    if (this.expandedFolders.size > 0) {
                        urlParams.set(
                            "expanded",
                            Array.from(this.expandedFolders).join(",")
                        );
                    } else {
                        urlParams.delete("expanded");
                    }

                    const newUrl = `${
                        window.location.pathname
                    }?${urlParams.toString()}`;
                    window.history.replaceState({}, "", newUrl);
                } else {
                    this.revertToRoot(deletedObject);
                }
            } else {
                this.revertToRoot(deletedObject);
            }

            // Refresh UI to reflect changes
            this.$nextTick(() => {
                this.forceRefreshExpandedState();
            });
        },

        /**
         * Helper method to revert to root folder
         */
        revertToRoot(deletedObject) {
            this.$page.props.selectedFileSystemObject = this.file;
            this.$page.props.selectedFolder = "/";

            // Update URL to remove selection and update expanded state
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.delete("selected");

            if (this.expandedFolders.size > 0) {
                urlParams.set(
                    "expanded",
                    Array.from(this.expandedFolders).join(",")
                );
            } else {
                urlParams.delete("expanded");
            }

            const newUrl = `${
                window.location.pathname
            }?${urlParams.toString()}`;
            window.history.replaceState({}, "", newUrl);
        },
    },
};
</script>
