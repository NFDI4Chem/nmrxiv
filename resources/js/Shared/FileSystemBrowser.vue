<!--
  File System Browser Component
  
  A comprehensive file browser interface that supports drag-and-drop uploads,
  file tree navigation, and file management operations. Features include:
  - Dropzone for file/folder uploads with progress tracking
  - Hierarchical file tree with expandable folders
  - File details panel with metadata display
  - Missing file detection and reporting
  - Checksum calculation for uploaded files
  - Sequential batch processing for large uploads
-->
<template>
    <!-- Main dropzone container with responsive fullscreen support -->
    <div
        id="fs-dropzone"
        :class="[
            fullScreen
                ? 'fixed w-screen h-screen -ml-4 -mt-6 sm:ml-0 md:-ml-0 md:w-auto inset-0'
                : height
                ? `flex flex-col min-h-0 ${height} overflow-hidden`
                : 'min-h-0 h-screen overflow-hidden',
            'bg-white rounded-lg',
        ]"
    >
        <div class="flex min-h-0 flex-1 flex-col">
            <!-- Header section with help links and missing files indicator -->
            <div :class="[fullScreen ? 'px-6 py-2' : '', 'flex flex-shrink-0']">
                <div class="w-full px-5">
                    <!-- Help and documentation section (only shown in edit mode) -->
                    <div
                        v-if="!readonly"
                        class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1.5 pr-2 sm:pr-5"
                    >
                        <!-- Documentation link with tooltip -->
                        <div class="min-w-0 flex-1 text-sm text-gray-700">
                            <div
                                class="inline-flex flex-wrap items-center gap-x-2 gap-y-1 sm:gap-x-3"
                            >
                                <ToolTip
                                    class="text-blue-600"
                                    text="To submit data you will need an account with nmrXiv, so you will be redirected to our register page and once registered you can then go ahead and submit data. For more information please checkout our <a target='_blank' href='//docs.nmrxiv.org' class='text-gray-400' target='_blank'>documentation</a>."
                                />
                                <a
                                    class="text-blue-600 underline decoration-blue-300/70 underline-offset-2 transition hover:text-blue-800 hover:decoration-blue-500"
                                    href="https://docs.nmrxiv.org/submission-guides/folder-structure.html"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    Learn more about folder structuring
                                </a>
                            </div>
                        </div>

                        <div
                            class="flex shrink-0 flex-wrap items-center justify-end gap-x-4 gap-y-1"
                        >
                            <span
                                v-if="draft && draft.key"
                                class="cursor-default select-all text-xs font-medium tabular-nums text-gray-500"
                                title="Draft reference"
                            >
                                Draft ID:
                                <span class="font-mono text-gray-700">{{
                                    draft.key
                                }}</span>
                            </span>

                            <!-- Missing files warning (shown when files are missing) -->
                            <a
                                v-if="missing_files > 0"
                                class="cursor-pointer text-sm font-semibold text-red-900"
                                @click="showMissingFilesDetailsModal()"
                            >
                                <!-- Warning triangle icon -->
                                <svg
                                    class="inline h-5 w-5 text-red-400"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                {{ missing_files }} files missing
                            </a>
                        </div>
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
            <!-- File upload dropzone section (only shown in edit mode) -->
            <div
                v-if="!readonly"
                :class="[
                    hasProjectFiles
                        ? fullScreen
                            ? 'px-6 py-2 flex-shrink-0'
                            : 'px-5 flex-shrink-0'
                        : 'h-full flex flex-col flex-1 min-h-0 p-5',
                ]"
            >
                <div
                    :class="[
                        hasProjectFiles
                            ? 'py-1 mb-2'
                            : 'h-full overflow-hidden flex-1 flex items-center justify-center',
                    ]"
                >
                    <!-- Dropzone message container -->
                    <div
                        id="fs-dropzone-message"
                        :class="[
                            'text-center',
                            hasProjectFiles ? 'w-full' : 'w-full h-full',
                        ]"
                    >
                        <!-- Main dropzone area with dashed border -->
                        <div
                            type="button"
                            :class="[
                                'relative block w-full border-2 border-gray-300 border-dashed rounded-lg text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500',
                                hasProjectFiles
                                    ? 'p-4'
                                    : 'h-full flex flex-col items-center justify-center p-8',
                            ]"
                        >
                            <div
                                :class="[
                                    hasProjectFiles
                                        ? ''
                                        : 'h-full w-full flex flex-col items-center justify-center',
                                ]"
                            >
                                <!-- Database/storage icon for dropzone -->
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

                                <!-- Dropzone instructions and controls -->
                                <span
                                    class="mt-2 block text-lg font-bold text-blue-600"
                                >
                                    Drop Files or Folders
                                    <!-- Dynamic destination folder indicator -->
                                    <span
                                        v-if="
                                            $page.props.selectedFolder &&
                                            $page.props.selectedFolder != '/'
                                        "
                                    >
                                        to "{{ $page.props.selectedFolder }}"
                                        folder
                                    </span>

                                    <!-- File selection form -->
                                    <form
                                        class="inline"
                                        enctype="multipart/form-data"
                                    >
                                        or
                                        <!-- Folder selection button -->
                                        <button
                                            id="fs-dropzone-click-target"
                                            type="button"
                                            class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white px-2 border border-blue-500 hover:border-transparent rounded"
                                        >
                                            Select folders
                                        </button>
                                        to upload
                                        <!-- Hidden input container for Dropzone.js -->
                                        <div
                                            id="fs-dropzone-hidden-input-container"
                                        />
                                    </form>

                                    <!-- Help link to submission guides -->
                                    <div class="text-sm text-gray-400">
                                        Need help? Check out our
                                        <a
                                            class="text-blue-800 hover:underline"
                                            href="https://docs.nmrxiv.org/submission-guides/submission-process.html#step-1-files-upload"
                                            target="_blank"
                                        >
                                            submission guides
                                        </a>
                                    </div>
                                </span>
                            </div>

                            <!-- Upload progress section (shown during upload) -->
                            <div v-if="dropzone" class="relative mt-5">
                                <!-- Progress bar -->
                                <div
                                    class="overflow-hidden h-2 text-xs flex rounded bg-gray-200"
                                >
                                    <div
                                        :style="
                                            'width: ' + precentageUpload + '%'
                                        "
                                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"
                                    />
                                </div>

                                <!-- Status message and error logs -->
                                <span
                                    v-if="status"
                                    class="mt-2 block text-sm font-medium text-gray-900"
                                >
                                    {{ status }}
                                    <!-- Error logs toggle (shown when there are batch errors) -->
                                    <div v-if="uploadBatchErrors.length > 0">
                                        -
                                        <a
                                            class="text-red-700 cursor-pointer"
                                            @click="
                                                showErrorBatchLogs =
                                                    !showErrorBatchLogs
                                            "
                                        >
                                            View logs
                                        </a>
                                    </div>
                                </span>

                                <!-- Error batch logs display -->
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
                                        v-html="sanitizeHtml(error)"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Loading state display -->
            <div v-if="loading">
                <div class="h-[calc(100vh-260px)] text-center py-12">
                    <!-- Loading spinner -->
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
                        />
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        />
                    </svg>
                    Loading Files...
                </div>
            </div>

            <!-- Main content area with file tree and details panel -->
            <div
                v-else-if="hasProjectFiles"
                :class="[
                    fullScreen ? 'overflow-hidden h-full relative px-6' : '',
                    'flex min-h-0 min-w-0 flex-1',
                ]"
            >
                <!-- Left sidebar with file tree -->
                <aside
                    ref="sidebarRef"
                    :class="[
                        'flex h-full min-h-0 flex-shrink-0 flex-col bg-white border-r border-gray-200',
                    ]"
                    :style="{ width: sidebarWidth + 'px' }"
                >
                    <!-- Sidebar header (same bar height as right panel headers) -->
                    <div
                        class="flex min-h-12 shrink-0 flex-wrap items-center justify-between gap-x-2 gap-y-1 border-b border-gray-100 bg-gray-50 px-5"
                    >
                        <span
                            class="shrink-0 text-sm font-semibold text-gray-900"
                            >Folders</span
                        >
                        <div
                            class="flex shrink-0 items-center gap-1 sm:gap-2"
                            role="toolbar"
                            aria-label="Folder tree"
                        >
                            <button
                                type="button"
                                class="rounded p-0.5 text-gray-500 transition hover:bg-gray-100/80 hover:text-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-40"
                                :disabled="!hasProjectFiles || loading"
                                title="Expand all folders"
                                aria-label="Expand all folders"
                                @click="expandAllFoldersInTree"
                            >
                                <ArrowsPointingOutIcon
                                    class="h-4 w-4"
                                    aria-hidden="true"
                                />
                            </button>
                            <button
                                type="button"
                                class="rounded p-0.5 text-gray-500 transition hover:bg-gray-100/80 hover:text-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-40"
                                :disabled="
                                    !hasProjectFiles ||
                                    loading ||
                                    expandedFolders.size === 0
                                "
                                title="Collapse all folders"
                                aria-label="Collapse all folders"
                                @click="collapseAllFoldersInTree"
                            >
                                <ArrowsPointingInIcon
                                    class="h-4 w-4"
                                    aria-hidden="true"
                                />
                            </button>
                            <span
                                class="mx-0.5 hidden h-5 w-px shrink-0 bg-gray-200 sm:block"
                                aria-hidden="true"
                            ></span>
                            <HeadlessMenu
                                as="div"
                                class="relative z-40 inline-flex"
                            >
                                <MenuButton
                                    type="button"
                                    class="inline-flex items-center rounded p-0 text-gray-600 transition hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-400 focus-visible:ring-offset-1"
                                    aria-label="Sort folders by"
                                    aria-haspopup="true"
                                >
                                    <Squares2X2OutlineIcon
                                        class="h-3.5 w-3.5"
                                        aria-hidden="true"
                                    />
                                </MenuButton>
                                <transition
                                    enter-active-class="transition ease-out duration-100"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95"
                                >
                                    <MenuItems
                                        class="absolute left-full top-0 z-[100] ml-1.5 w-52 origin-top-left overflow-hidden rounded-lg bg-white p-0 shadow-lg ring-1 ring-black/5 focus:outline-none"
                                    >
                                        <MenuItem v-slot="{ active }">
                                            <button
                                                type="button"
                                                :class="[
                                                    treeSortBy ===
                                                    'alphabetical'
                                                        ? 'bg-gray-900 text-white'
                                                        : active
                                                        ? 'bg-gray-100 text-gray-900'
                                                        : 'bg-white text-gray-700',
                                                    'flex w-full items-center gap-2 rounded-t-lg px-3 py-2 text-left text-sm',
                                                ]"
                                                @click="
                                                    setTreeSortBy(
                                                        'alphabetical'
                                                    )
                                                "
                                            >
                                                <QueueListIconOutline
                                                    class="h-4 w-4 shrink-0 opacity-90"
                                                    aria-hidden="true"
                                                />
                                                <span>Name</span>
                                            </button>
                                        </MenuItem>
                                        <MenuItem v-slot="{ active }">
                                            <button
                                                type="button"
                                                :class="[
                                                    treeSortBy === 'timestamp'
                                                        ? 'bg-gray-900 text-white'
                                                        : active
                                                        ? 'bg-gray-100 text-gray-900'
                                                        : 'bg-white text-gray-700',
                                                    'flex w-full items-center gap-2 rounded-b-lg px-3 py-2 text-left text-sm',
                                                ]"
                                                @click="
                                                    setTreeSortBy('timestamp')
                                                "
                                            >
                                                <ClockIconOutline
                                                    class="h-4 w-4 shrink-0 opacity-90"
                                                    aria-hidden="true"
                                                />
                                                <span>Date modified</span>
                                            </button>
                                        </MenuItem>
                                    </MenuItems>
                                </transition>
                            </HeadlessMenu>
                            <button
                                type="button"
                                class="rounded p-0.5 text-gray-400 transition hover:bg-gray-100/80 hover:text-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-400 focus-visible:ring-offset-1"
                                :title="
                                    treeSortOrder === 'asc'
                                        ? treeSortBy === 'timestamp'
                                            ? 'Oldest first — click to show newest first'
                                            : 'A to Z — click for Z to A'
                                        : treeSortBy === 'timestamp'
                                        ? 'Newest first — click to show oldest first'
                                        : 'Z to A — click for A to Z'
                                "
                                :aria-label="
                                    treeSortOrder === 'asc'
                                        ? 'Switch to descending order'
                                        : 'Switch to ascending order'
                                "
                                @click="toggleTreeSortOrder"
                            >
                                <ChevronUpOutlineIcon
                                    v-if="treeSortOrder === 'asc'"
                                    class="h-3 w-3 stroke-[1.5]"
                                    aria-hidden="true"
                                />
                                <ChevronDownOutlineIcon
                                    v-else
                                    class="h-3 w-3 stroke-[1.5]"
                                    aria-hidden="true"
                                />
                            </button>
                        </div>
                    </div>

                    <!-- Scrollable file tree -->
                    <div
                        class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden px-2 pt-2 pb-10"
                    >
                        <!-- Recursive file tree component -->
                        <children
                            :file="file"
                            :expanded-folders="expandedFolders"
                            :tree-sort-by="treeSortBy"
                            :tree-sort-order="treeSortOrder"
                            @toggle-expansion="
                                (fsoId, isExpanded) =>
                                    toggleFolderExpansion(fsoId, isExpanded)
                            "
                        />
                    </div>

                    <!-- Sidebar footer with logs -->
                    <div
                        v-if="
                            Object.keys(logs).length > 0 &&
                            !readonly &&
                            !isDeletingFiles
                        "
                        class="flex-shrink-0 px-3 py-2 border-t border-gray-100 bg-gray-50 text-sm cursor-pointer text-gray-500 hover:text-gray-700 transition-colors"
                        @click="showLogsDialog = true"
                    >
                        <InformationCircleIcon
                            class="h-4 w-4 inline mr-1"
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
                                class="relative h-[74vh] overflow-x-auto z-0 mt-1 rounded-lg"
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
                                        class="py-4 flex items-start"
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

                <!-- Resize handle -->
                <div
                    class="flex-shrink-0 w-1 cursor-col-resize bg-transparent hover:bg-teal-400 active:bg-teal-500 transition-colors group relative"
                    @mousedown="startResize"
                >
                    <div
                        class="absolute inset-y-0 -left-1 -right-1 group-hover:bg-teal-400/10"
                    ></div>
                </div>

                <!-- Details panel -->
                <section
                    :class="[
                        'flex h-full min-h-0 flex-1 flex-col overflow-hidden bg-white hidden md:flex',
                    ]"
                >
                    <div
                        v-if="
                            $page.props.selectedFileSystemObject &&
                            $page.props.selectedFileSystemObject.has_children
                        "
                        class="flex min-h-0 flex-1 flex-col overflow-hidden"
                    >
                        <!-- Panel header -->
                        <div
                            class="flex min-h-12 shrink-0 items-center border-b border-gray-100 bg-gray-50 px-5"
                        >
                            <div
                                class="flex w-full justify-between items-center gap-3"
                            >
                                <nav
                                    class="flex min-w-0 flex-1 flex-wrap items-center gap-x-0.5 gap-y-1 text-sm"
                                    aria-label="Folder path"
                                >
                                    <button
                                        type="button"
                                        class="inline-flex shrink-0 items-center rounded p-0.5 text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-1"
                                        :class="
                                            pathBreadcrumbSegments.length === 0
                                                ? 'text-gray-900'
                                                : ''
                                        "
                                        title="Project root"
                                        aria-label="Go to project root"
                                        @click="breadcrumbGoToRoot"
                                    >
                                        <HomeIcon
                                            class="h-4 w-4"
                                            aria-hidden="true"
                                        />
                                    </button>
                                    <template
                                        v-for="(
                                            seg, idx
                                        ) in pathBreadcrumbSegments"
                                        :key="seg.path + '-' + idx"
                                    >
                                        <ChevronRightIcon
                                            class="mx-0.5 h-3.5 w-3.5 shrink-0 text-gray-400"
                                            aria-hidden="true"
                                        />
                                        <button
                                            v-if="!seg.isLast"
                                            type="button"
                                            class="min-w-0 max-w-[12rem] truncate text-left font-medium text-gray-700 transition hover:text-teal-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-1 sm:max-w-xs md:max-w-md"
                                            :title="seg.label"
                                            @click="
                                                breadcrumbNavigateToPath(
                                                    seg.path
                                                )
                                            "
                                        >
                                            {{ seg.label }}
                                        </button>
                                        <span
                                            v-else
                                            class="min-w-0 max-w-[12rem] truncate font-semibold text-gray-900 sm:max-w-xs md:max-w-md"
                                            :title="seg.label"
                                            aria-current="page"
                                        >
                                            {{ seg.label }}
                                        </span>
                                    </template>
                                </nav>
                                <div
                                    class="flex items-center space-x-2 flex-shrink-0"
                                >
                                    <!-- View Toggle -->
                                    <div
                                        class="flex rounded-md border border-gray-200 overflow-hidden"
                                    >
                                        <button
                                            :class="[
                                                viewMode === 'grid'
                                                    ? 'bg-gray-100 text-gray-900'
                                                    : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50',
                                                'p-1.5 transition-colors',
                                            ]"
                                            title="Grid View"
                                            @click="setViewMode('grid')"
                                        >
                                            <Squares2X2Icon class="h-4 w-4" />
                                        </button>
                                        <button
                                            :class="[
                                                viewMode === 'list'
                                                    ? 'bg-gray-100 text-gray-900'
                                                    : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50',
                                                'p-1.5 transition-colors border-l border-gray-200',
                                            ]"
                                            title="List View"
                                            @click="setViewMode('list')"
                                        >
                                            <ListBulletIcon class="h-4 w-4" />
                                        </button>
                                    </div>
                                    <button
                                        v-if="
                                            $page.props.selectedFileSystemObject
                                                .id && !readonly
                                        "
                                        class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-200 bg-white text-xs font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors"
                                        @click="confirmFSODeletion"
                                    >
                                        <TrashIcon
                                            class="h-3.5 w-3.5 mr-1.5"
                                            aria-hidden="true"
                                        />
                                        Delete
                                    </button>
                                    <a
                                        v-if="
                                            $page.props.selectedFileSystemObject
                                                .id &&
                                            readonly &&
                                            downloadURL
                                        "
                                        :href="downloadURL"
                                        class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-200 bg-white text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors"
                                    >
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Scrollable content area -->
                        <div
                            class="min-h-0 flex-1 overflow-y-auto px-5 pt-3 pb-12"
                        >
                            <!-- List View Table Header -->
                            <div v-if="viewMode === 'list'" class="mt-2">
                                <div
                                    class="bg-gray-50 px-3 py-2 border border-gray-200 rounded-t-md"
                                >
                                    <div
                                        class="grid grid-cols-12 gap-4 text-xs font-medium text-gray-600"
                                    >
                                        <div class="col-span-4">
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-px rounded text-gray-700 transition hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-1"
                                                title="Sort by name"
                                                aria-label="Sort by name"
                                                @click="sortFiles('name')"
                                            >
                                                <QueueListIconOutline
                                                    class="h-4 w-4 shrink-0"
                                                    :class="
                                                        sortBy === 'name'
                                                            ? 'text-gray-900'
                                                            : 'text-gray-600'
                                                    "
                                                    aria-hidden="true"
                                                />
                                                <ChevronUpOutlineIcon
                                                    v-if="
                                                        sortBy === 'name' &&
                                                        sortOrder === 'asc'
                                                    "
                                                    class="h-2.5 w-2.5 shrink-0 text-gray-900 stroke-[1.75]"
                                                    aria-hidden="true"
                                                />
                                                <ChevronDownOutlineIcon
                                                    v-else-if="
                                                        sortBy === 'name' &&
                                                        sortOrder === 'desc'
                                                    "
                                                    class="h-2.5 w-2.5 shrink-0 text-gray-900 stroke-[1.75]"
                                                    aria-hidden="true"
                                                />
                                            </button>
                                        </div>
                                        <div class="col-span-3">
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-px rounded text-gray-700 transition hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-1"
                                                title="Sort by date modified"
                                                aria-label="Sort by date modified"
                                                @click="sortFiles('date')"
                                            >
                                                <ClockIconOutline
                                                    class="h-4 w-4 shrink-0"
                                                    :class="
                                                        sortBy === 'date'
                                                            ? 'text-gray-900'
                                                            : 'text-gray-600'
                                                    "
                                                    aria-hidden="true"
                                                />
                                                <ChevronUpOutlineIcon
                                                    v-if="
                                                        sortBy === 'date' &&
                                                        sortOrder === 'asc'
                                                    "
                                                    class="h-2.5 w-2.5 shrink-0 text-gray-900 stroke-[1.75]"
                                                    aria-hidden="true"
                                                />
                                                <ChevronDownOutlineIcon
                                                    v-else-if="
                                                        sortBy === 'date' &&
                                                        sortOrder === 'desc'
                                                    "
                                                    class="h-2.5 w-2.5 shrink-0 text-gray-900 stroke-[1.75]"
                                                    aria-hidden="true"
                                                />
                                            </button>
                                        </div>
                                        <div class="col-span-2">
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-px rounded text-gray-700 transition hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-1"
                                                title="Sort by size"
                                                aria-label="Sort by size"
                                                @click="sortFiles('size')"
                                            >
                                                <ScaleIconOutline
                                                    class="h-4 w-4 shrink-0"
                                                    :class="
                                                        sortBy === 'size'
                                                            ? 'text-gray-900'
                                                            : 'text-gray-600'
                                                    "
                                                    aria-hidden="true"
                                                />
                                                <ChevronUpOutlineIcon
                                                    v-if="
                                                        sortBy === 'size' &&
                                                        sortOrder === 'asc'
                                                    "
                                                    class="h-2.5 w-2.5 shrink-0 text-gray-900 stroke-[1.75]"
                                                    aria-hidden="true"
                                                />
                                                <ChevronDownOutlineIcon
                                                    v-else-if="
                                                        sortBy === 'size' &&
                                                        sortOrder === 'desc'
                                                    "
                                                    class="h-2.5 w-2.5 shrink-0 text-gray-900 stroke-[1.75]"
                                                    aria-hidden="true"
                                                />
                                            </button>
                                        </div>
                                        <div class="col-span-2">
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-px rounded text-gray-700 transition hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-1"
                                                title="Sort by kind"
                                                aria-label="Sort by kind"
                                                @click="sortFiles('kind')"
                                            >
                                                <TagIconOutline
                                                    class="h-4 w-4 shrink-0"
                                                    :class="
                                                        sortBy === 'kind'
                                                            ? 'text-gray-900'
                                                            : 'text-gray-600'
                                                    "
                                                    aria-hidden="true"
                                                />
                                                <ChevronUpOutlineIcon
                                                    v-if="
                                                        sortBy === 'kind' &&
                                                        sortOrder === 'asc'
                                                    "
                                                    class="h-2.5 w-2.5 shrink-0 text-gray-900 stroke-[1.75]"
                                                    aria-hidden="true"
                                                />
                                                <ChevronDownOutlineIcon
                                                    v-else-if="
                                                        sortBy === 'kind' &&
                                                        sortOrder === 'desc'
                                                    "
                                                    class="h-2.5 w-2.5 shrink-0 text-gray-900 stroke-[1.75]"
                                                    aria-hidden="true"
                                                />
                                            </button>
                                        </div>
                                        <div class="col-span-1">
                                            <!-- Download column - no header text -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <ul
                                role="list"
                                :class="[
                                    'mb-3',
                                    viewMode === 'grid'
                                        ? 'grid grid-cols-1 gap-5 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4'
                                        : 'divide-y divide-gray-200 border border-gray-200 rounded-b-md',
                                ]"
                            >
                                <li
                                    v-for="file in sortedFiles"
                                    :key="file.key"
                                    :class="[
                                        viewMode === 'grid'
                                            ? 'relative shadow rounded-lg'
                                            : 'hover:bg-gray-50',
                                    ]"
                                >
                                    <!-- Grid View Layout -->
                                    <template v-if="viewMode === 'grid'">
                                        <div
                                            style="user-select: none"
                                            class="hover:cursor-pointer"
                                            @dblclick.stop="
                                                displaySelected(file)
                                            "
                                        >
                                            <div
                                                class="group block w-full aspect-w-10 aspect-h-7 py-4 bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-indigo-500 overflow-hidden"
                                            >
                                                <span
                                                    v-if="
                                                        file.type == 'directory'
                                                    "
                                                >
                                                    <FolderIcon
                                                        class="cursor-pointer h-28 w-28 text-teal-600 flex-shrink-0 mx-auto"
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
                                                    v-if="
                                                        file.status == 'missing'
                                                    "
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
                                                <span
                                                    class="float-left"
                                                    :title="file.name"
                                                >
                                                    {{
                                                        truncateMiddle(
                                                            file.name,
                                                            25
                                                        )
                                                    }}
                                                </span>
                                            </p>
                                            <div
                                                class="flex items-center justify-between px-2 pb-1"
                                            >
                                                <p
                                                    class="text-sm font-medium text-gray-500"
                                                >
                                                    {{ formatFileSize(file) }}
                                                </p>
                                                <a
                                                    v-if="
                                                        file.type !==
                                                            'directory' &&
                                                        readonly
                                                    "
                                                    :href="
                                                        getFileDownloadURL(file)
                                                    "
                                                    class="text-gray-400 hover:text-indigo-600 transition-colors duration-200 pointer-events-auto"
                                                    title="Download file"
                                                    @click.stop
                                                >
                                                    <ArrowDownTrayIcon
                                                        class="h-4 w-4"
                                                    />
                                                </a>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- List View Layout -->
                                    <template v-else>
                                        <div
                                            class="px-3 py-3 grid grid-cols-12 gap-4 items-center"
                                        >
                                            <!-- Name Column -->
                                            <div
                                                class="col-span-4 flex items-center"
                                            >
                                                <div class="flex-shrink-0 mr-3">
                                                    <FolderIcon
                                                        v-if="
                                                            file.type ==
                                                            'directory'
                                                        "
                                                        class="cursor-pointer h-5 w-5 text-teal-600"
                                                        aria-hidden="true"
                                                        @dblclick.stop="
                                                            displaySelected(
                                                                file
                                                            )
                                                        "
                                                    />
                                                    <DocumentTextIcon
                                                        v-else
                                                        class="h-5 w-5 text-gray-400"
                                                        aria-hidden="true"
                                                    />
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p
                                                        class="text-sm font-medium text-gray-900 truncate"
                                                        :title="file.name"
                                                    >
                                                        {{ file.name }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Date Modified Column -->
                                            <div class="col-span-3">
                                                <p
                                                    class="text-sm text-gray-500"
                                                >
                                                    {{
                                                        formatDate(
                                                            file.updated_at ||
                                                                file.created_at
                                                        )
                                                    }}
                                                </p>
                                            </div>

                                            <!-- Size Column -->
                                            <div class="col-span-2">
                                                <p
                                                    class="text-sm text-gray-500"
                                                >
                                                    {{
                                                        file.type ===
                                                        "directory"
                                                            ? "--"
                                                            : formatFileSize(
                                                                  file
                                                              )
                                                    }}
                                                </p>
                                            </div>

                                            <!-- Kind Column -->
                                            <div class="col-span-2">
                                                <p
                                                    class="text-sm text-gray-500"
                                                >
                                                    {{
                                                        file.type ===
                                                        "directory"
                                                            ? "Folder"
                                                            : "Document"
                                                    }}
                                                </p>
                                            </div>

                                            <!-- Download Column -->
                                            <div
                                                class="col-span-1 flex justify-center"
                                            >
                                                <a
                                                    v-if="
                                                        file.type !==
                                                            'directory' &&
                                                        readonly
                                                    "
                                                    :href="
                                                        getFileDownloadURL(file)
                                                    "
                                                    class="text-gray-400 hover:text-indigo-600 transition-colors duration-200"
                                                    title="Download file"
                                                >
                                                    <ArrowDownTrayIcon
                                                        class="h-5 w-5"
                                                    />
                                                </a>
                                                <!-- No download icon for folders -->
                                            </div>
                                        </div>
                                    </template>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div
                        v-else
                        class="flex min-h-0 flex-1 flex-col overflow-hidden"
                    >
                        <!-- File detail header -->
                        <div
                            v-if="$page.props.selectedFileSystemObject"
                            class="flex min-h-12 shrink-0 items-center border-b border-gray-100 bg-gray-50 px-5"
                        >
                            <div
                                class="flex w-full items-center justify-between gap-3"
                            >
                                <nav
                                    class="flex min-w-0 flex-1 flex-wrap items-center gap-x-0.5 gap-y-1 text-sm"
                                    aria-label="File path"
                                >
                                    <button
                                        type="button"
                                        class="inline-flex shrink-0 items-center rounded p-0.5 text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-1"
                                        :class="
                                            pathBreadcrumbSegments.length === 0
                                                ? 'text-gray-900'
                                                : ''
                                        "
                                        title="Project root"
                                        aria-label="Go to project root"
                                        @click="breadcrumbGoToRoot"
                                    >
                                        <HomeIcon
                                            class="h-4 w-4"
                                            aria-hidden="true"
                                        />
                                    </button>
                                    <template
                                        v-for="(
                                            seg, idx
                                        ) in pathBreadcrumbSegments"
                                        :key="seg.path + '-' + idx"
                                    >
                                        <ChevronRightIcon
                                            class="mx-0.5 h-3.5 w-3.5 shrink-0 text-gray-400"
                                            aria-hidden="true"
                                        />
                                        <button
                                            v-if="!seg.isLast"
                                            type="button"
                                            class="min-w-0 max-w-[12rem] truncate text-left font-medium text-gray-700 transition hover:text-teal-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-1 sm:max-w-xs md:max-w-md"
                                            :title="seg.label"
                                            @click="
                                                breadcrumbNavigateToPath(
                                                    seg.path
                                                )
                                            "
                                        >
                                            {{ seg.label }}
                                        </button>
                                        <span
                                            v-else
                                            class="min-w-0 max-w-[12rem] truncate font-semibold text-gray-900 sm:max-w-xs md:max-w-md"
                                            :title="seg.label"
                                            aria-current="page"
                                        >
                                            {{ seg.label }}
                                        </span>
                                    </template>
                                </nav>
                                <div
                                    class="flex items-center space-x-2 flex-shrink-0"
                                >
                                    <button
                                        v-if="
                                            $page.props.selectedFileSystemObject
                                                .id && !readonly
                                        "
                                        class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-200 bg-white text-xs font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors"
                                        @click="confirmFSODeletion"
                                    >
                                        <TrashIcon
                                            class="h-3.5 w-3.5 mr-1.5"
                                            aria-hidden="true"
                                        />
                                        Delete
                                    </button>
                                    <a
                                        v-if="
                                            $page.props.selectedFileSystemObject
                                                .id &&
                                            readonly &&
                                            downloadURL
                                        "
                                        :href="downloadURL"
                                        class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-200 bg-white text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors"
                                    >
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- File details content -->
                        <div
                            class="min-h-0 flex-1 overflow-y-auto px-5 pt-4 pb-12"
                        >
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
            <span v-for="(file, index) in missing_files_list" :key="index">
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
/**
 * File System Browser Component
 *
 * A comprehensive file management interface that provides:
 * - Drag-and-drop file/folder uploads with Dropzone.js integration
 * - Hierarchical file tree navigation with expansion state management
 * - File details panel with metadata display and download functionality
 * - Batch upload processing with checksum calculation for integrity
 * - Missing file detection and reporting
 * - File/folder deletion with progress tracking
 * - URL-based state persistence for navigation and expansion
 * - Sequential processing to prevent race conditions during uploads
 *
 * Key Features:
 * - Supports both read-only and edit modes
 * - Calculates MD5 and SHA-256 checksums for uploaded files
 * - Maintains expansion state across page reloads via URL parameters
 * - Provides detailed upload logs with error tracking
 * - Handles large file uploads with progress indication
 * - Integrates with scientific data formats (Bruker, Varian, JOEL, JCAMP)
 */

// UI Component imports
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";

// Third-party library imports
import { Dropzone } from "dropzone";
import axiosRetry from "axios-retry";

// Shared component imports
import FileDetails from "@/Shared/FileDetails.vue";
import ToolTip from "@/Shared/ToolTip.vue";

// Icon imports from Heroicons
import {
    FolderIcon,
    DocumentTextIcon,
    InformationCircleIcon,
    EllipsisVerticalIcon,
    ArrowUpTrayIcon,
    CheckIcon,
    ExclamationCircleIcon,
    TrashIcon,
    ArrowDownTrayIcon,
    Squares2X2Icon,
    ListBulletIcon,
} from "@heroicons/vue/24/solid";

import {
    QueueListIcon as QueueListIconOutline,
    ClockIcon as ClockIconOutline,
    ScaleIcon as ScaleIconOutline,
    TagIcon as TagIconOutline,
    ChevronUpIcon as ChevronUpOutlineIcon,
    ChevronDownIcon as ChevronDownOutlineIcon,
    Squares2X2Icon as Squares2X2OutlineIcon,
    HomeIcon,
    ChevronRightIcon,
    ArrowsPointingOutIcon,
    ArrowsPointingInIcon,
} from "@heroicons/vue/24/outline";

import {
    Menu as HeadlessMenu,
    MenuButton,
    MenuItems,
    MenuItem,
} from "@headlessui/vue";

// Utility imports
import ChecksumCalculator from "@/Utils/ChecksumCalculator.js";

export default {
    name: "FileSystemBrowser",

    /**
     * Component dependencies
     */
    components: {
        FolderIcon, // Folder icon for directories
        DocumentTextIcon, // Document icon for files
        InformationCircleIcon, // Information icon for logs
        FileDetails, // File details display component
        JetDialogModal, // Modal dialog component
        JetSecondaryButton, // Secondary button component
        EllipsisVerticalIcon, // More options icon
        ExclamationCircleIcon, // Error/warning icon
        ArrowUpTrayIcon, // Upload icon
        ArrowDownTrayIcon, // Download icon
        CheckIcon, // Success checkmark icon
        TrashIcon, // Delete icon
        ToolTip, // Tooltip component
        JetConfirmationModal, // Confirmation dialog component
        JetDangerButton, // Danger button component
        Squares2X2Icon, // Grid view icon
        ListBulletIcon, // List view icon
        QueueListIconOutline,
        ClockIconOutline,
        ScaleIconOutline,
        TagIconOutline,
        ChevronUpOutlineIcon,
        ChevronDownOutlineIcon,
        Squares2X2OutlineIcon,
        HeadlessMenu,
        MenuButton,
        MenuItems,
        MenuItem,
        HomeIcon,
        ChevronRightIcon,
        ArrowsPointingOutIcon,
        ArrowsPointingInIcon,
    },

    /**
     * Component props
     * @prop {Object} draft - Draft object for file uploads (optional)
     * @prop {Boolean} readonly - Whether the browser is in read-only mode
     * @prop {String} height - Tailwind height classes for the root #fs-dropzone (e.g. h-full min-h-0 when the parent establishes height via flex).
     * @prop {Object} project - Project object for public file access (optional)
     */
    props: ["draft", "readonly", "height", "project"],

    /**
     * Events emitted by this component
     */
    emits: ["loading", "proceed"],

    /**
     * Component reactive data
     */
    data() {
        return {
            // Upload status and progress
            status: "", // Current upload/processing status message
            dropzone: null, // Dropzone.js instance
            fullScreen: false, // Fullscreen mode toggle (unused)
            precentageUpload: 0, // Upload progress percentage
            busy: false, // General busy state indicator
            loading: false, // File loading state

            // File system data
            file: null, // Root file system object
            url: null, // Base URL for API calls

            // Logging and error tracking
            logs: {}, // Upload logs by filename
            logFilter: "Error", // Current log filter selection
            logFilters: ["Error", "Success", "Queued", "Inprogress"], // Available log filters
            uploadBatchErrors: [], // Batch upload error messages
            showErrorBatchLogs: false, // Toggle for error log display
            showLogsDialog: false, // Modal dialog for detailed logs
            currentLog: null, // Currently processing file log

            // Upload configuration
            sequentialProcessing: true, // Enable sequential processing to avoid race conditions

            // File operations
            fsoBeingDeleted: null, // File system object being deleted
            showMissingFilesDetails: null, // Modal for missing files details
            missing_files: 0, // Count of missing files
            missing_files_list: [], // List of missing file objects

            // Deletion progress tracking
            isDeletingFiles: false, // Deletion operation in progress
            deletionMessage: "", // Current deletion status message

            // Tree expansion state management
            expandedFolders: new Set(), // Set of expanded folder IDs

            // Navigation state tracking
            currentStep: null, // Current step for state clearing

            // View mode and sorting
            viewMode: "grid", // 'grid' or 'list'
            sortBy: "name", // 'name', 'date', 'size', 'kind'
            sortOrder: "asc", // 'asc' or 'desc'

            // Sidebar tree sorting (Children.vue)
            treeSortBy: "alphabetical", // 'alphabetical' | 'timestamp'
            treeSortOrder: "asc", // 'asc' | 'desc'

            // Resizable sidebar
            sidebarWidth: 320, // Default sidebar width in pixels
            isResizing: false, // Whether sidebar is being resized
        };
    },
    /**
     * Computed properties
     */
    computed: {
        /**
         * Get the base URL from page props
         * @returns {String} Base application URL
         */
        baseURL() {
            return String(this.$page.props.url);
        },

        /**
         * Filter logs based on current log filter selection
         *
         * Creates a filtered copy of the logs object containing only
         * entries that match the current logFilter status.
         *
         * @returns {Object} Filtered logs object
         */
        filteredLogs() {
            let logsClone = JSON.parse(JSON.stringify(this.logs));
            Object.keys(logsClone).forEach((key) => {
                if (logsClone[key].status != this.logFilter)
                    delete logsClone[key];
            });
            return logsClone;
        },
        hasProjectFiles() {
            return Boolean(
                this.file &&
                    Array.isArray(this.file.children) &&
                    this.file.children.length > 0
            );
        },

        /**
         * Get NMRium URL for spectral data visualization
         * @returns {String} NMRium application URL
         */
        nmriumURL() {
            return this.$page.props.nmriumURL
                ? String(this.$page.props.nmriumURL)
                : "//nmriumdev.nmrxiv.org";
        },

        /**
         * Generate download URL for the currently selected file or folder
         *
         * Creates appropriate download URLs based on context:
         * - Uses direct download_url if available on the file object
         * - For project files, constructs URL with owner/slug/file parameters
         * - Returns null if no download context is available
         *
         * @returns {String|null} Complete download URL or null
         */
        downloadURL() {
            // Use direct download URL if available
            if (this.$page.props.selectedFileSystemObject.download_url) {
                return this.$page.props.selectedFileSystemObject.download_url;
            } else {
                // Construct download URL for project files
                if (this.project) {
                    // Root folder download
                    if (
                        this.$page.props.selectedFileSystemObject &&
                        this.$page.props.selectedFileSystemObject
                            .relative_url == "/"
                    ) {
                        return this.project.download_url;
                    } else {
                        // Individual file/folder download
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

        /**
         * Watch for URL changes to detect step changes
         *
         * Used by watchers to detect when the user navigates to different
         * steps in the submission process, triggering state cleanup.
         *
         * @returns {String} Current URL search parameters
         */
        currentUrl() {
            return window.location.search;
        },

        /**
         * Get sorted files for the current folder
         * @returns {Array} Sorted array of files and folders
         */
        sortedFiles() {
            if (!this.$page.props.selectedFileSystemObject?.children) {
                return [];
            }

            const files = [
                ...this.$page.props.selectedFileSystemObject.children,
            ];

            return files.sort((a, b) => {
                let aValue, bValue;

                switch (this.sortBy) {
                    case "name":
                        aValue = a.name.toLowerCase();
                        bValue = b.name.toLowerCase();
                        break;
                    case "date":
                        aValue = new Date(a.updated_at || a.created_at || 0);
                        bValue = new Date(b.updated_at || b.created_at || 0);
                        break;
                    case "size":
                        aValue = this.getFileSizeForSorting(a);
                        bValue = this.getFileSizeForSorting(b);
                        break;
                    case "kind":
                        aValue = a.type === "directory" ? "folder" : "document";
                        bValue = b.type === "directory" ? "folder" : "document";
                        break;
                    default:
                        return 0;
                }

                if (aValue < bValue) {
                    return this.sortOrder === "asc" ? -1 : 1;
                }
                if (aValue > bValue) {
                    return this.sortOrder === "asc" ? 1 : -1;
                }
                return 0;
            });
        },

        /**
         * Path segments for breadcrumb navigation (labels + cumulative paths).
         *
         * @returns {Array<{ label: string, path: string, isLast: boolean }>}
         */
        pathBreadcrumbSegments() {
            const fso = this.$page.props.selectedFileSystemObject;
            if (!fso?.relative_url) {
                return [];
            }

            return this.buildPathBreadcrumbSegments(fso.relative_url);
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
        // Load view mode from localStorage
        const savedViewMode = localStorage.getItem("nmrxiv-files-view-mode");
        if (savedViewMode && ["grid", "list"].includes(savedViewMode)) {
            this.viewMode = savedViewMode;
        }

        // Load sidebar width from localStorage
        const savedWidth = localStorage.getItem("nmrxiv-sidebar-width");
        if (savedWidth) {
            const parsed = parseInt(savedWidth, 10);
            if (!isNaN(parsed) && parsed >= 180 && parsed <= 600) {
                this.sidebarWidth = parsed;
            }
        }

        const savedTreeSortBy = localStorage.getItem(
            "nmrxiv-files-tree-sort-by"
        );
        if (
            savedTreeSortBy === "alphabetical" ||
            savedTreeSortBy === "timestamp"
        ) {
            this.treeSortBy = savedTreeSortBy;
        }

        const savedTreeSortOrder = localStorage.getItem(
            "nmrxiv-files-tree-sort-order"
        );
        if (savedTreeSortOrder === "asc" || savedTreeSortOrder === "desc") {
            this.treeSortOrder = savedTreeSortOrder;
        }

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

        // Prevent page refresh/navigation during upload
        window.addEventListener("beforeunload", this.handleBeforeUnload);
    },

    beforeUnmount() {
        // Clear file tree state before component unmounts
        this.clearURLParameters();

        // Clean up event listeners
        window.removeEventListener("popstate", this.handleURLChange);
        window.removeEventListener("file-browser-proceed", this.handleProceed);
        window.removeEventListener("beforeunload", this.handleBeforeUnload);

        // Clean up resize listeners
        document.removeEventListener("mousemove", this.onResize);
        document.removeEventListener("mouseup", this.stopResize);
    },
    /**
     * Component methods
     */
    methods: {
        /**
         * Truncate text in the middle with ellipsis for long file/folder names
         *
         * This method prevents UI overflow by shortening long names while
         * preserving both the beginning and end of the filename, which is
         * often more useful than simple truncation.
         *
         * @param {String} text - The text to truncate
         * @param {Number} maxLength - Maximum allowed length including ellipsis
         * @returns {String} Truncated text with ellipsis in the middle
         *
         * @example
         * truncateMiddle("very-long-filename.txt", 15)
         * // Returns: "very-l...me.txt"
         */
        truncateMiddle(text, maxLength) {
            if (!text || text.length <= maxLength) {
                return text;
            }

            const start = Math.ceil((maxLength - 3) / 2);
            const end = Math.floor((maxLength - 3) / 2);

            return (
                text.substring(0, start) +
                "..." +
                text.substring(text.length - end)
            );
        },

        /**
         * Normalize a relative path for comparison (leading slash, no duplicate slashes).
         *
         * @param {string|null|undefined} path
         * @returns {string}
         */
        normalizeRelativePath(path) {
            if (path == null || path === "") {
                return "/";
            }

            const normalized = String(path).replace(/\/+/g, "/");

            if (normalized === "/") {
                return "/";
            }

            return (
                "/" +
                normalized
                    .replace(/^\/+|\/+$/g, "")
                    .split("/")
                    .filter(Boolean)
                    .join("/")
            );
        },

        /**
         * Build breadcrumb segment descriptors from a relative_url.
         *
         * @param {string} relativeUrl
         * @returns {Array<{ label: string, path: string, isLast: boolean }>}
         */
        buildPathBreadcrumbSegments(relativeUrl) {
            const norm = this.normalizeRelativePath(relativeUrl);

            if (norm === "/") {
                return [];
            }

            const parts = norm.split("/").filter(Boolean);
            const segments = [];
            let acc = "";

            for (let i = 0; i < parts.length; i++) {
                acc += "/" + parts[i];
                segments.push({
                    label: parts[i],
                    path: acc,
                    isLast: i === parts.length - 1,
                });
            }

            return segments;
        },

        /**
         * Find a file or folder in the tree by normalized relative_url.
         *
         * @param {object|null} fileObject
         * @param {string} targetPath
         * @returns {object|null}
         */
        findObjectByRelativeUrl(fileObject, targetPath) {
            const norm = this.normalizeRelativePath(targetPath);

            if (norm === "/") {
                if (
                    fileObject &&
                    (fileObject.name === "/" ||
                        this.normalizeRelativePath(fileObject.relative_url) ===
                            "/")
                ) {
                    return fileObject;
                }

                return null;
            }

            return this.findObjectByRelativeUrlRecursive(fileObject, norm);
        },

        /**
         * @param {object|null} node
         * @param {string} norm
         * @returns {object|null}
         */
        findObjectByRelativeUrlRecursive(node, norm) {
            if (!node) {
                return null;
            }

            if (this.normalizeRelativePath(node.relative_url) === norm) {
                return node;
            }

            if (node.children && Array.isArray(node.children)) {
                for (const child of node.children) {
                    const found = this.findObjectByRelativeUrlRecursive(
                        child,
                        norm
                    );

                    if (found) {
                        return found;
                    }
                }
            }

            return null;
        },

        /**
         * Navigate breadcrumb to a folder or file at the given path.
         *
         * @param {string} targetPath
         * @returns {Promise<void>}
         */
        async breadcrumbNavigateToPath(targetPath) {
            const target = this.findObjectByRelativeUrl(this.file, targetPath);

            if (!target) {
                return;
            }

            await this.displaySelected(target);
        },

        /**
         * Navigate to project root.
         *
         * @returns {Promise<void>}
         */
        async breadcrumbGoToRoot() {
            if (!this.file) {
                return;
            }

            await this.displaySelected(this.file);
        },

        /**
         * Initialize step tracking from current URL
         *
         * Extracts the current step parameter from the URL to track
         * navigation state changes in multi-step processes.
         */
        initializeStepTracking() {
            const urlParams = new URLSearchParams(window.location.search);
            this.currentStep = urlParams.get("step");
        },

        /**
         * Handle URL changes to detect step changes
         *
         * Monitors URL changes to detect when users navigate between
         * different steps in the submission process. When a step change
         * is detected, clears the file tree state to prevent confusion.
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
         * Replace the expandedFolders Set so Vue 3 picks up the change.
         *
         * Vue 3 reactivity for `Set` instances does not always notify
         * dependents on `.add` / `.delete` / `.clear` mutations. Reassigning
         * the property with a new `Set` instance reliably triggers updates.
         *
         * @param {Iterable<number>|Set<number>} [iterable]
         */
        setExpandedFolders(iterable = []) {
            this.expandedFolders = new Set(iterable);
        },

        /**
         * Mutate the expandedFolders Set immutably via a callback.
         *
         * Clones the current Set, hands it to the callback for mutation,
         * then reassigns it so Vue picks up the change.
         *
         * @param {(next: Set<number>) => void} mutator
         */
        mutateExpandedFolders(mutator) {
            const next = new Set(this.expandedFolders);
            mutator(next);
            this.expandedFolders = next;
        },

        /**
         * Clear file tree state when step changes
         */
        clearFileTreeState() {
            this.setExpandedFolders();

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
         * Prevent page navigation/refresh during active uploads
         *
         * Shows a browser confirmation dialog when the user attempts to
         * navigate away or refresh the page while files are being uploaded.
         *
         * @param {Event} event - The beforeunload event
         */
        handleBeforeUnload(event) {
            // Check if upload is in progress
            const uploadInProgress =
                this.dropzone && this.status && this.precentageUpload < 100;

            // Check if deletion is in progress
            const deletionInProgress = this.isDeletingFiles;

            if (uploadInProgress || deletionInProgress) {
                // Standard way to trigger confirmation dialog
                event.preventDefault();

                // Set appropriate message based on operation
                const message = uploadInProgress
                    ? "Files are being uploaded. Are you sure you want to leave?"
                    : "Files are being deleted. Are you sure you want to leave?";

                // Chrome requires returnValue to be set
                event.returnValue = message;
                return event.returnValue;
            }
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
         * Check if all files are processed and manually trigger queuecomplete
         */
        checkAndTriggerQueueComplete() {
            // Count files with error status
            const errorFiles = Object.values(this.logs).filter(
                (log) => log.status === "Error"
            ).length;
            const successFiles = Object.values(this.logs).filter(
                (log) => log.status === "Success"
            ).length;
            const processedFiles = errorFiles + successFiles;

            // Check if all files have been processed (either success or error)
            if (
                processedFiles >= this.totalFilesCount &&
                this.totalFilesCount > 0
            ) {
                this.handleQueueComplete();
            }
        },

        /**
         * Handle queue completion (both automatic and manual)
         */
        handleQueueComplete() {
            this.status = "UPLOAD COMPLETE";
            this.annotate();
            this.currentLog = null;
            if (this.dropzone) {
                this.dropzone.removeAllFiles();
            }
            this.precentageUpload = 0;
            this.totalFilesCount = 0;
            this.uploadedFilesCount = 0;
            this.updateBusyStatus(false);
            setTimeout(() => {
                this.status = null;
            }, 5000);
        },

        /**
         * Handle completion when no files are processed (empty queue)
         */
        handleEmptyQueueCompletion() {
            // Reset all upload-related state
            this.status = null;
            this.precentageUpload = 0;
            this.totalFilesCount = 0;
            this.uploadedFilesCount = 0;
            this.currentLog = null;

            // Clear dropzone if it exists
            if (this.dropzone) {
                this.dropzone.removeAllFiles();
            }

            // Update busy status
            this.updateBusyStatus(false);
        },

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

                // Manually trigger completion since no files will be processed
                this.$nextTick(() => {
                    this.handleEmptyQueueCompletion();
                });

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
        },

        /**
         * Confirm file system object deletion
         *
         * Sets up the confirmation dialog for deleting a file or folder.
         * Stores the ID of the object to be deleted for the confirmation modal.
         */
        confirmFSODeletion() {
            this.fsoBeingDeleted = this.$page.props.selectedFileSystemObject.id;
        },

        /**
         * Delete file system object (file or folder)
         *
         * Performs the actual deletion of a file or folder after confirmation.
         * Handles the complete deletion workflow including:
         * - Progress indication with overlay
         * - Parent folder identification for navigation reversion
         * - API call to delete the object
         * - Error handling and user feedback
         * - File tree refresh and state management
         *
         * The method ensures proper cleanup and user experience by:
         * - Showing deletion progress to the user
         * - Reverting selection to parent folder after deletion
         * - Clearing related logs and state
         * - Handling both success and error scenarios
         */
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
                            if (response.data.has_storage_errors) {
                                console.log(
                                    `Note: ${response.data.storage_errors.length} storage operation(s) had issues.`
                                );
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

        /**
         * Show missing files details modal
         *
         * Displays a modal dialog containing details about files that are
         * missing from the file system. Fetches the latest missing files
         * data before showing the modal.
         */
        showMissingFilesDetailsModal() {
            this.fetchMissingFiles();
            this.showMissingFilesDetails = true;
        },

        /**
         * Fetch missing files from the server
         *
         * Retrieves the list of files that are referenced in the database
         * but missing from the actual file system. This helps users identify
         * and resolve file integrity issues.
         */
        fetchMissingFiles() {
            axios
                .get("/dashboard/drafts/" + this.draft.id + "/missing-files")
                .then((response) => {
                    console.log(response);
                    this.missing_files_list = response.data.missing_files;
                });
        },

        /**
         * Toggle fullscreen mode
         *
         * Switches the file browser between normal and fullscreen display modes.
         * Currently unused but available for future implementation.
         */
        toggleFullScreen() {
            this.fullScreen = !this.fullScreen;
        },

        /**
         * Toggle the logs dialog visibility
         *
         * Shows or hides the detailed upload logs modal dialog.
         * Used to display comprehensive information about file upload
         * status, errors, and processing details.
         */
        toggleShowLogsDialog() {
            this.showLogsDialog = !this.showLogsDialog;
        },

        /**
         * Start resizing the sidebar via mouse drag
         * @param {MouseEvent} event - The mousedown event on the resize handle
         */
        startResize(event) {
            event.preventDefault();
            this.isResizing = true;
            document.addEventListener("mousemove", this.onResize);
            document.addEventListener("mouseup", this.stopResize);
            document.body.style.cursor = "col-resize";
            document.body.style.userSelect = "none";
        },

        /**
         * Handle mousemove during sidebar resize
         * @param {MouseEvent} event - The mousemove event
         */
        onResize(event) {
            if (!this.isResizing || !this.$refs.sidebarRef) return;
            const containerLeft =
                this.$refs.sidebarRef.parentElement.getBoundingClientRect()
                    .left;
            let newWidth = event.clientX - containerLeft;
            // Clamp between min 180px and max 600px
            newWidth = Math.max(180, Math.min(600, newWidth));
            this.sidebarWidth = newWidth;
        },

        /**
         * Stop resizing the sidebar and persist width
         */
        stopResize() {
            this.isResizing = false;
            document.removeEventListener("mousemove", this.onResize);
            document.removeEventListener("mouseup", this.stopResize);
            document.body.style.cursor = "";
            document.body.style.userSelect = "";
            // Persist sidebar width preference
            localStorage.setItem(
                "nmrxiv-sidebar-width",
                String(this.sidebarWidth)
            );
        },

        /**
         * Update the busy status of the component
         *
         * Sets the busy state to indicate when the component is performing
         * operations like file uploads, deletions, or API calls. This helps
         * prevent user interactions during critical operations.
         *
         * @param {Boolean} status - True if component is busy, false otherwise
         */
        updateBusyStatus(status) {
            this.busy = status;
        },

        /**
         * Load files from the server
         *
         * Fetches the file tree structure from the server and initializes
         * the file browser interface. This method handles:
         * - Loading the root file system object
         * - Restoring expanded folder states from URL parameters
         * - Selecting the appropriate folder based on navigation state
         * - Fetching missing folder contents for expanded folders
         * - Updating the UI to reflect the loaded state
         *
         * The method supports both draft mode (with server data) and
         * read-only mode (with pre-loaded data from page props).
         */
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

        /**
         * Annotate uploaded files
         *
         * Triggers the server-side annotation process for uploaded files.
         * This process analyzes uploaded files to:
         * - Detect file types and formats
         * - Extract metadata from scientific data files
         * - Identify instrument-specific data structures
         * - Generate thumbnails and previews where applicable
         * - Update the file tree with processed information
         *
         * After annotation completes, the file tree is reloaded to show
         * the updated file structure and metadata.
         */
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
        async processFilesDZL(vm, filesBatch) {
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

            return client
                .post(url, {
                    draft_files: filesWithChecksums,
                    destination: vm.$page.props.selectedFolder,
                    draft_id: vm.draft.id,
                })
                .catch((err) => {
                    // Log errors for the current batch
                    filesBatch.forEach((file) => {
                        let message = "Upload failed";
                        if (file.fullPath) {
                            vm.logs[file.fullPath].status = "Error";
                            vm.logs[file.fullPath].messages.push(
                                message +
                                    " (API call failed with status code:" +
                                    (err.response?.status || "unknown") +
                                    ") "
                            );
                        } else {
                            vm.logs[file.name].status = "Error";
                            vm.logs[file.name].messages.push(
                                message +
                                    "(API call failed with status code:" +
                                    (err.response?.status || "unknown") +
                                    ")"
                            );
                        }
                    });

                    this.uploadBatchErrors.push(
                        err.response?.data || err.message
                    );
                    throw err; // Re-throw to be caught by sequential processor
                })
                .then((response) => {
                    if (response) {
                        vm.currentLog =
                            "Uploading files to temporary storage url";
                        let data = response.data;

                        data.forEach((u) => {
                            let cFile = vm.dropzone.files.find((f) => {
                                if (f.fullPath) {
                                    return f.fullPath.trim() == u.fullPath;
                                } else {
                                    // Try multiple matching strategies for files without fullPath
                                    const fileName = f.name.trim();
                                    const uploadPath = u.fullPath;

                                    // Direct name match
                                    const match1 = fileName == uploadPath;

                                    // Name with leading slash
                                    const match2 = "/" + fileName == uploadPath;

                                    // Name with folder prefix
                                    const folderPrefix =
                                        vm.$page.props.selectedFolder === "/"
                                            ? ""
                                            : vm.$page.props.selectedFolder;
                                    const match3 =
                                        folderPrefix + "/" + fileName ==
                                        uploadPath;

                                    return match1 || match2 || match3;
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

                        return response;
                    }
                });
        },
        async processFilesSequentially(vm) {
            try {
                // Process files in sequential batches to avoid race conditions
                for (let i = 0; i < vm.totalFilesCount; i += vm.batchCount) {
                    let filesBatch = vm.dropzone.files.slice(
                        i,
                        i + vm.batchCount
                    );
                    vm.batches += 1;

                    vm.status = `PROCESSING BATCH ${vm.batches} OF ${Math.ceil(
                        vm.totalFilesCount / vm.batchCount
                    )}`;

                    // Wait for each batch to complete before processing the next
                    await vm.processFilesDZL(vm, filesBatch);

                    // Small delay between batches to prevent overwhelming the server
                    await new Promise((resolve) => setTimeout(resolve, 100));
                }

                vm.status = "ALL BATCHES PROCESSED";
            } catch (error) {
                console.error("Error in sequential file processing:", error);
                vm.status = "ERROR IN BATCH PROCESSING";
                vm.updateBusyStatus(false);
            }
        },
        /**
         * Initialize Dropzone.js for file uploads
         *
         * Sets up the drag-and-drop file upload interface using Dropzone.js.
         * This method configures:
         * - Upload batch processing with sequential handling
         * - File selection and folder upload support
         * - Progress tracking and status updates
         * - Event handlers for upload lifecycle
         * - Checksum calculation integration
         * - Error handling and logging
         *
         * The dropzone is configured for:
         * - Large file uploads (up to 20,000 files)
         * - Folder structure preservation
         * - Sequential batch processing to prevent server overload
         * - Comprehensive upload logging and error tracking
         */
        loadDropZone() {
            this.$nextTick(() => {
                const vm = this;

                // Initialize upload counters and batch configuration
                vm.totalFilesCount = 0;
                vm.uploadedFilesCount = 0;
                vm.batchCount = 10; // Reduced batch size for sequential processing
                vm.count = 0;
                vm.batches = 0;
                vm.processedBatches = 0;

                // Set initial file system state
                vm.$page.props.selectedFileSystemObject = vm.file;
                vm.$page.props.selectedFolder = "/";

                vm.selectedFSO = [];

                // Configure Dropzone.js options
                let options = {
                    url: "/", // Placeholder URL (will be updated per file)
                    method: "put", // HTTP method for uploads
                    sending(file, xhr) {
                        // Custom sending logic to use file object directly
                        let _send = xhr.send;
                        xhr.send = () => {
                            _send.call(xhr, file);
                        };
                    },
                    autoProcessQueue: false, // Manual queue processing for batch control
                    uploadMultiple: true, // Support multiple file uploads
                    disablePreviews: true, // Disable image previews for performance
                    parallelUploads: 100, // Allow many parallel uploads
                    useFsAccessApi: false, // Disable File System Access API
                    autoQueue: false, // Manual queue management
                    maxFiles: 20000, // Support large numbers of files
                    clickable: "#fs-dropzone-click-target", // Click target element
                    hiddenInputContainer: "#fs-dropzone-hidden-input-container", // Hidden input container
                    dictDefaultMessage: document.querySelector(
                        "#fs-dropzone-message"
                    ).innerHTML, // Default message
                    totaluploadprogress: function (progress) {
                        vm.progress = Math.ceil(progress);
                    },
                };

                // Initialize Dropzone instance
                vm.dropzone = new Dropzone("#fs-dropzone", options);

                // Enable folder selection by setting webkitdirectory attribute
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

                    // Check if all files are complete and manually trigger queuecomplete
                    vm.checkAndTriggerQueueComplete();
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

                    // Check if all files are complete and manually trigger queuecomplete
                    vm.checkAndTriggerQueueComplete();
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
                                            vm.processFilesSequentially(vm);
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
                    vm.handleQueueComplete();
                });
            });
        },
        /**
         * Display selected file or folder in the details panel
         *
         * Updates the application state to show the selected file or folder
         * in the right panel. This method handles:
         * - Setting the selected file system object globally
         * - Calculating the correct folder path for breadcrumb navigation
         * - Lazy loading folder contents if needed (via expandPathToObject)
         * - Expanding the left sidebar tree to reveal the selected item
         * - Updating the selected folder state for UI consistency
         *
         * The method determines the correct folder path based on:
         * - Root folder ("/") for the root directory
         * - Full relative URL for directories
         * - Parent directory path for individual files
         *
         * @param {Object} file - The file or folder object to display
         */
        async displaySelected(file) {
            this.$page.props.selectedFileSystemObject = file;
            let sFolder = "/";

            // Calculate the appropriate folder path for breadcrumb navigation
            if (this.$page.props.selectedFileSystemObject.name == "/") {
                sFolder = "/";
            } else {
                if (this.$page.props.selectedFileSystemObject.type != "file") {
                    // For directories, use the full relative URL
                    sFolder =
                        this.$page.props.selectedFileSystemObject.relative_url;
                } else {
                    // For files, extract the parent directory path
                    if (
                        this.$page.props.selectedFileSystemObject.parent_id ==
                        null
                    ) {
                        sFolder = "/";
                    } else {
                        // Remove filename from path to get parent directory
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

            // Update the selected folder for breadcrumb display
            this.$page.props.selectedFolder = sFolder;

            // Expand left tree to this item and load children on the path; updates URL
            await this.expandPathToObject(file);
        },

        /**
         * Recursively collect folder ids in the loaded tree that can be expanded.
         *
         * @param {object|null} fileObject
         * @param {Array<number>} ids
         * @returns {Array<number>}
         */
        collectExpandableFolderIds(fileObject, ids = []) {
            if (!fileObject) {
                return ids;
            }

            const isDir =
                fileObject.type === "directory" || fileObject.name === "/";

            if (isDir && fileObject.has_children && fileObject.id) {
                ids.push(fileObject.id);
            }

            if (fileObject.children && Array.isArray(fileObject.children)) {
                for (const child of fileObject.children) {
                    this.collectExpandableFolderIds(child, ids);
                }
            }

            return ids;
        },

        /**
         * Expand all folders that appear in the currently loaded tree data.
         */
        expandAllFoldersInTree() {
            if (!this.file) {
                return;
            }

            const ids = this.collectExpandableFolderIds(this.file, []);
            this.setExpandedFolders(ids);
            this.updateURLWithExpandedState();

            this.$nextTick(() => {
                this.forceRefreshExpandedState();
            });
        },

        /**
         * Collapse every expanded folder in the sidebar tree.
         */
        collapseAllFoldersInTree() {
            this.setExpandedFolders();
            this.updateURLWithExpandedState();

            this.$nextTick(() => {
                this.forceRefreshExpandedState();
            });
        },

        /**
         * Track when a folder is expanded/collapsed
         */
        toggleFolderExpansion(fsoId, isExpanded) {
            this.mutateExpandedFolders((next) => {
                if (isExpanded) {
                    next.add(fsoId);
                } else {
                    next.delete(fsoId);
                }
            });

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
                this.setExpandedFolders(
                    expandedParam
                        .split(",")
                        .filter((id) => id)
                        .map((id) => parseInt(id, 10))
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
                    this.mutateExpandedFolders((next) => {
                        next.delete(folder.id);
                    });
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

            const expandable = pathFolders.filter(
                (folder) => folder.has_children && folder.id
            );

            this.mutateExpandedFolders((next) => {
                for (const folder of expandable) {
                    next.add(folder.id);
                }
            });

            for (const folder of expandable) {
                if (!folder.children || folder.children.length === 0) {
                    await this.fetchFolderChildren(folder);
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
            const parentFolder =
                parentInfo && parentInfo.id
                    ? this.findObjectById(this.file, parentInfo.id)
                    : null;

            this.mutateExpandedFolders((next) => {
                if (deletedObject.type === "directory" && deletedObject.id) {
                    next.delete(deletedObject.id);
                }

                if (parentFolder && parentFolder.has_children) {
                    next.add(parentFolder.id);
                }
            });

            if (!parentFolder) {
                this.revertToRoot();

                this.$nextTick(() => {
                    this.forceRefreshExpandedState();
                });

                return;
            }

            this.$page.props.selectedFileSystemObject = parentFolder;
            this.updateSelectedFolder(parentFolder);

            if (
                parentFolder.has_children &&
                (!parentFolder.children || parentFolder.children.length === 0)
            ) {
                await this.fetchFolderChildren(parentFolder);
            }

            const urlParams = new URLSearchParams(window.location.search);
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

            this.$nextTick(() => {
                this.forceRefreshExpandedState();
            });
        },

        /**
         * Helper method to revert to root folder
         */
        revertToRoot() {
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

        /**
         * Set view mode and save to localStorage
         */
        setViewMode(mode) {
            this.viewMode = mode;
            localStorage.setItem("nmrxiv-files-view-mode", mode);
        },

        persistTreeSortPreferences() {
            localStorage.setItem("nmrxiv-files-tree-sort-by", this.treeSortBy);
            localStorage.setItem(
                "nmrxiv-files-tree-sort-order",
                this.treeSortOrder
            );
        },

        setTreeSortBy(mode) {
            if (this.treeSortBy === mode) {
                return;
            }
            this.treeSortBy = mode;
            this.persistTreeSortPreferences();
        },

        toggleTreeSortOrder() {
            this.treeSortOrder = this.treeSortOrder === "asc" ? "desc" : "asc";
            this.persistTreeSortPreferences();
        },

        /**
         * Handle sorting by column
         */
        sortFiles(column) {
            if (this.sortBy === column) {
                // Toggle sort order if clicking the same column
                this.sortOrder = this.sortOrder === "asc" ? "desc" : "asc";
            } else {
                // Set new column and default to ascending
                this.sortBy = column;
                this.sortOrder = "asc";
            }
        },

        /**
         * Format file size for display
         */
        formatFileSize(file) {
            if (file.type === "directory") return "--";

            try {
                const info = JSON.parse(file.info || "{}");
                const sizeInBytes = parseInt(info.size || 0);

                if (sizeInBytes === 0) return "0 bytes";

                const units = ["bytes", "KB", "MB", "GB", "TB"];
                const unitIndex = Math.floor(
                    Math.log(sizeInBytes) / Math.log(1024)
                );
                const size = (sizeInBytes / Math.pow(1024, unitIndex)).toFixed(
                    1
                );

                return `${size} ${units[unitIndex]}`;
            } catch (error) {
                // Fallback to original size if JSON parsing fails
                return file.size || "0 bytes";
            }
        },

        /**
         * Get file size for sorting purposes
         */
        getFileSizeForSorting(file) {
            if (file.type === "directory") return 0;

            try {
                const info = JSON.parse(file.info || "{}");
                return parseInt(info.size || 0);
            } catch (error) {
                // Fallback to parsing original size string
                return parseInt(file.size?.replace(/[^\d]/g, "") || 0);
            }
        },

        /**
         * Format date for display
         */
        formatDate(dateString) {
            if (!dateString) return "--";

            const date = new Date(dateString);
            const options = {
                day: "numeric",
                month: "short",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            };

            // Format like "18. Aug 2025 at 10:52"
            return date
                .toLocaleDateString("en-GB", options)
                .replace(",", " at")
                .replace(/(\d+)/, "$1.");
        },

        /**
         * Get download URL for individual files
         */
        getFileDownloadURL(file) {
            if (file.type === "directory") return "#";

            if (this.project) {
                return (
                    this.baseURL +
                    "/" +
                    this.project.owner.username +
                    "/download/" +
                    this.project.slug +
                    "?key=" +
                    file.key +
                    "&uuid=" +
                    file.uuid
                );
            }
            return "#";
        },
    },
};
</script>
