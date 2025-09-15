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
            current="Files"
            :preview="preview"
        >
            <template #study-section>
                <div class="divide-y divide-gray-200 sm:col-span-9">
                    <!-- <div class="py-6 px-4 sm:p-6">
            <div class="flex flex-item">
              <h2 class="text-lg leading-6 font-medium text-gray-900">Data set</h2>
            </div>
          </div> -->
                    <div v-if="file">
                        <nav
                            v-if="$page.props.selectedFolder"
                            class="flex p-3"
                            aria-label="Breadcrumb"
                        >
                            <ol role="list" class="flex items-center space-x-2">
                                <li>
                                    <div>
                                        <a
                                            class="text-gray-400 hover:text-gray-900"
                                        >
                                            <HomeIcon
                                                class="flex-shrink-0 h-5 w-5"
                                                aria-hidden="true"
                                            />
                                            <span class="sr-only">Home</span>
                                        </a>
                                    </div>
                                </li>
                                <li
                                    v-for="page in $page.props.selectedFolder.split(
                                        '/'
                                    )"
                                    :key="page.name"
                                >
                                    <div
                                        v-if="page != ''"
                                        class="flex items-center"
                                    >
                                        <ChevronRightIcon
                                            class="flex-shrink-0 h-5 w-5 text-gray-400"
                                            aria-hidden="true"
                                        />
                                        <a
                                            class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                                            :aria-current="
                                                page ? 'page' : undefined
                                            "
                                            >{{ page }}</a
                                        >
                                    </div>
                                </li>
                            </ol>
                        </nav>
                        <div
                            class="min-w-0 flex-1 min-h-screen border-t border-gray-200 lg:flex"
                        >
                            <aside
                                class="hidden py-3 px-2 lg:block lg:flex-shrink-0 lg:order-first"
                            >
                                <div
                                    v-if="file != null"
                                    class="h-full relative flex flex-col w-64 border-r border-gray-200 overflow-y-auto"
                                >
                                    <children
                                        :file="file"
                                        :study="study"
                                        :project="project"
                                    ></children>
                                </div>
                            </aside>
                            <section
                                class="min-w-0 p-6 flex-1 h-full flex flex-col overflow-y-auto lg:order-last"
                            >
                                <!-- <div v-if="canUpdateFiles">
                                    <form class="dropzone py-2 mb-3">
                                        <div
                                            id="dropzone-message"
                                            class="text-center"
                                        >
                                            <div
                                                type="button"
                                                class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
                                                    class="mt-2 block text-sm font-medium text-gray-900"
                                                >
                                                    Drop Files or Folders to
                                                    upload to
                                                    <span
                                                        v-if="
                                                            $page.props
                                                                .selectedFolder
                                                        "
                                                        >"{{
                                                            $page.props
                                                                .selectedFolder
                                                        }}"</span
                                                    >
                                                    folder
                                                </span>

                                                <div
                                                    v-if="progress > 0"
                                                    class="relative mt-5"
                                                >
                                                    <div
                                                        class="overflow-hidden h-2 text-xs flex rounded bg-gray-200"
                                                    >
                                                        <div
                                                            :style="
                                                                'width: ' +
                                                                progress +
                                                                '%'
                                                            "
                                                            class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"
                                                        ></div>
                                                    </div>
                                                </div>
                                                <div v-if="progress > 0">
                                                    <span
                                                        class="mt-2 block text-sm font-medium text-gray-900"
                                                    >
                                                        {{ status }} ({{
                                                            progress
                                                        }}%)
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div> -->
                                <div
                                    v-if="
                                        $page.props.selectedFileSystemObject &&
                                        $page.props.selectedFileSystemObject
                                            .has_children
                                    "
                                    class="mb-3"
                                >
                                    <div class="py-2 mb-2 block">
                                        <div class="flex justify-between items-center">
                                            <p class="font-bold text-xl">
                                                {{
                                                    $page.props
                                                        .selectedFileSystemObject
                                                        .name
                                                }}
                                            </p>
                                            <div class="flex items-center space-x-3">
                                                <!-- View Toggle - New Feature -->
                                                <div class="flex space-x-1">
                                                    <button
                                                        @click="setViewMode('grid')"
                                                        :class="[
                                                            viewMode === 'grid'
                                                                ? 'text-gray-900'
                                                                : 'text-gray-400 hover:text-gray-600',
                                                            'p-1'
                                                        ]"
                                                        title="Grid View"
                                                    >
                                                        <Squares2X2Icon class="h-5 w-5" />
                                                    </button>
                                                    <button
                                                        @click="setViewMode('list')"
                                                        :class="[
                                                            viewMode === 'list'
                                                                ? 'text-gray-900'
                                                                : 'text-gray-400 hover:text-gray-600',
                                                            'p-1'
                                                        ]"
                                                        title="List View"
                                                    >
                                                        <ListBulletIcon class="h-5 w-5" />
                                                    </button>
                                                </div>
                                                <a
                                                    :href="downloadURL"
                                                    class="cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                                >
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- List View Table Header -->
                                    <div v-if="viewMode === 'list'">
                                        <div class="bg-gray-50 px-3 py-2 border border-gray-200 rounded-t-md">
                                            <div class="grid grid-cols-12 gap-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <div class="col-span-4">
                                                    <button 
                                                        @click="sortFiles('name')"
                                                        class="flex items-center space-x-1 hover:text-gray-700 focus:outline-none"
                                                    >
                                                        <span>Name</span>
                                                        <ChevronUpIcon 
                                                            v-if="sortBy === 'name' && sortOrder === 'asc'"
                                                            class="h-3 w-3"
                                                        />
                                                        <ChevronDownIcon 
                                                            v-else-if="sortBy === 'name' && sortOrder === 'desc'"
                                                            class="h-3 w-3"
                                                        />
                                                    </button>
                                                </div>
                                                <div class="col-span-3">
                                                    <button 
                                                        @click="sortFiles('date')"
                                                        class="flex items-center space-x-1 hover:text-gray-700 focus:outline-none"
                                                    >
                                                        <span>Date Modified</span>
                                                        <ChevronUpIcon 
                                                            v-if="sortBy === 'date' && sortOrder === 'asc'"
                                                            class="h-3 w-3"
                                                        />
                                                        <ChevronDownIcon 
                                                            v-else-if="sortBy === 'date' && sortOrder === 'desc'"
                                                            class="h-3 w-3"
                                                        />
                                                    </button>
                                                </div>
                                                <div class="col-span-2">
                                                    <button 
                                                        @click="sortFiles('size')"
                                                        class="flex items-center space-x-1 hover:text-gray-700 focus:outline-none"
                                                    >
                                                        <span>Size</span>
                                                        <ChevronUpIcon 
                                                            v-if="sortBy === 'size' && sortOrder === 'asc'"
                                                            class="h-3 w-3"
                                                        />
                                                        <ChevronDownIcon 
                                                            v-else-if="sortBy === 'size' && sortOrder === 'desc'"
                                                            class="h-3 w-3"
                                                        />
                                                    </button>
                                                </div>
                                                <div class="col-span-2">
                                                    <button 
                                                        @click="sortFiles('kind')"
                                                        class="flex items-center space-x-1 hover:text-gray-700 focus:outline-none"
                                                    >
                                                        <span>Kind</span>
                                                        <ChevronUpIcon 
                                                            v-if="sortBy === 'kind' && sortOrder === 'asc'"
                                                            class="h-3 w-3"
                                                        />
                                                        <ChevronDownIcon 
                                                            v-else-if="sortBy === 'kind' && sortOrder === 'desc'"
                                                            class="h-3 w-3"
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
                                                : 'divide-y divide-gray-200 border border-gray-200 rounded-b-md'
                                        ]"
                                    >
                                        <li
                                            v-for="file in sortedFiles"
                                            :key="file.key"
                                            :class="[
                                                viewMode === 'grid'
                                                    ? 'relative shadow rounded-lg'
                                                    : 'hover:bg-gray-50'
                                            ]"
                                        >
                                            <!-- Grid View Layout -->
                                            <template v-if="viewMode === 'grid'">
                                                <div
                                                    class="group block w-full aspect-w-10 aspect-h-7 py-4 bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-indigo-500 overflow-hidden"
                                                >
                                                    <span
                                                        v-if="
                                                            file.type == 'directory'
                                                        "
                                                    >
                                                        <FolderIcon
                                                            class="cursor-pointer h-28 w-28 text-gray-400 flex-shrink-0 mx-auto"
                                                            aria-hidden="true"
                                                            @dblclick.stop="
                                                                displaySelected(
                                                                    file
                                                                )
                                                            "
                                                        />
                                                    </span>
                                                    <span v-else>
                                                        <DocumentTextIcon
                                                            class="h-28 w-28 text-gray-400 flex-shrink-0 mx-auto"
                                                            aria-hidden="true"
                                                        />
                                                    </span>
                                                </div>
                                                <p
                                                    class="mt-2 px-2 py-1 block truncate text-sm font-medium text-gray-900 pointer-events-none"
                                                    :title="file.name"
                                                >
                                                    {{ file.name }}
                                                </p>
                                                <div class="flex items-center justify-between px-2 pb-1">
                                                    <p class="text-sm font-medium text-gray-500">
                                                        {{ formatFileSize(file) }}
                                                    </p>
                                                    <a
                                                        v-if="file.type !== 'directory'"
                                                        :href="url + '/' + project.owner.username + '/download/' + project.slug + '?key=' + file.key + '&uuid=' + file.uuid"
                                                        class="text-gray-400 hover:text-indigo-600 transition-colors duration-200 pointer-events-auto"
                                                        title="Download file"
                                                        @click.stop
                                                    >
                                                        <ArrowDownTrayIcon class="h-4 w-4" />
                                                    </a>
                                                </div>
                                            </template>

                                            <!-- List View Layout -->
                                            <template v-else>
                                                <div class="px-3 py-3 grid grid-cols-12 gap-4 items-center">
                                                    <!-- Name Column -->
                                                    <div class="col-span-4 flex items-center">
                                                        <div class="flex-shrink-0 mr-3">
                                                            <FolderIcon
                                                                v-if="file.type == 'directory'"
                                                                class="cursor-pointer h-5 w-5 text-teal-600"
                                                                aria-hidden="true"
                                                                @dblclick.stop="displaySelected(file)"
                                                            />
                                                            <DocumentTextIcon
                                                                v-else
                                                                class="h-5 w-5 text-gray-400"
                                                                aria-hidden="true"
                                                            />
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-sm font-medium text-gray-900 truncate" :title="file.name">
                                                                {{ file.name }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Date Modified Column -->
                                                    <div class="col-span-3">
                                                        <p class="text-sm text-gray-500">
                                                            {{ formatDate(file.updated_at || file.created_at) }}
                                                        </p>
                                                    </div>
                                                    
                                                    <!-- Size Column -->
                                                    <div class="col-span-2">
                                                        <p class="text-sm text-gray-500">
                                                            {{ file.type === 'directory' ? '--' : formatFileSize(file) }}
                                                        </p>
                                                    </div>
                                                    
                                                    <!-- Kind Column -->
                                                    <div class="col-span-2">
                                                        <p class="text-sm text-gray-500">
                                                            {{ file.type === 'directory' ? 'Folder' : 'Document' }}
                                                        </p>
                                                    </div>
                                                    
                                                    <!-- Download Column -->
                                                    <div class="col-span-1 flex justify-center">
                                                        <a
                                                            v-if="file.type !== 'directory'"
                                                            :href="url + '/' + project.owner.username + '/download/' + project.slug + '?key=' + file.key + '&uuid=' + file.uuid"
                                                            class="text-gray-400 hover:text-indigo-600 transition-colors duration-200"
                                                            title="Download file"
                                                        >
                                                            <ArrowDownTrayIcon class="h-5 w-5" />
                                                        </a>
                                                        <!-- No download icon for folders -->
                                                    </div>
                                                </div>
                                            </template>
                                        </li>
                                    </ul>
                                </div>
                                <div v-else>
                                    <div class="">
                                        <span
                                            v-if="
                                                $page.props
                                                    .selectedFileSystemObject &&
                                                $page.props
                                                    .selectedFileSystemObject
                                                    .type == 'file'
                                            "
                                        >
                                            <!-- <div
                                                v-if="
                                                    $page.props.selectedFileSystemObject.key.indexOf(
                                                        '.dx'
                                                    ) > -1 ||
                                                    $page.props.selectedFileSystemObject.key.indexOf(
                                                        '.jdx'
                                                    ) > -1 ||
                                                    $page.props.selectedFileSystemObject.key.indexOf(
                                                        '.zip'
                                                    ) > -1 ||
                                                    $page.props.selectedFileSystemObject.key.indexOf(
                                                        '.json'
                                                    ) > -1
                                                "
                                                class="mb-4"
                                            >
                                                <iframe
                                                    name="crossDomainIframe"
                                                    frameborder="0"
                                                    allowfullscreen
                                                    class="rounded-md border"
                                                    style="
                                                        width: 100%;
                                                        height: 500px;
                                                    "
                                                    :src="nmriumURL"
                                                    @load="loadSpectra()"
                                                ></iframe>
                                            </div> -->
                                            <!-- <div
                                                v-if="svgString"
                                                class="rounded-md border my-3 flex justify-center items-center"
                                            >
                                                <span v-html="svgString"></span>
                                            </div> -->
                                            <File-details
                                                :study="study"
                                                :file="
                                                    $page.props
                                                        .selectedFileSystemObject
                                                "
                                            ></File-details>
                                        </span>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </template>
        </study-content>
    </div>
</template>

<script>
import { Dropzone } from "dropzone";
import { router } from "@inertiajs/vue3";
import StudyContent from "@/Pages/Study/Content.vue";
import FileDetails from "@/Shared/FileDetails.vue";
import axiosRetry from "axios-retry";
import OCL from "openchemlib/minimal";

import {
    FolderIcon,
    DocumentTextIcon,
    ChevronRightIcon,
    HomeIcon,
    Squares2X2Icon,
    ListBulletIcon,
    ChevronUpIcon,
    ChevronDownIcon,
    ArrowDownTrayIcon,
} from "@heroicons/vue/24/solid";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
export default {
    components: {
        StudyContent,
        Disclosure,
        DisclosureButton,
        DisclosurePanel,
        FolderIcon,
        DocumentTextIcon,
        FileDetails,
        ChevronRightIcon,
        HomeIcon,
        Squares2X2Icon,
        ListBulletIcon,
        ChevronUpIcon,
        ChevronDownIcon,
        ArrowDownTrayIcon,
    },
    props: [
        "study",
        "project",
        "file",
        "team",
        "members",
        "availableRoles",
        "studyPermissions",
        "studyRole",
        "preview",
    ],
    setup() {
        return {};
    },
    data() {
        return {
            progress: 0,
            status: null,
            selectedFileSystemObject: null,
            svgString: null,
            viewMode: 'grid', // 'grid' or 'list'
            sortBy: 'name', // 'name', 'date', 'size', 'kind'
            sortOrder: 'asc', // 'asc' or 'desc'
        };
    },
    computed: {
        url() {
            return String(this.$page.props.url);
        },
        nmriumURL() {
            return this.$page.props.nmriumURL
                ? String(this.$page.props.nmriumURL)
                : "//nmriumdev.nmrxiv.org";
        },
        downloadURL() {
            return (
                this.url +
                "/" +
                this.project.owner.username +
                "/download/" +
                this.project.slug +
                "?key=" +
                this.$page.props.selectedFileSystemObject.key +
                "&uuid=" +
                this.$page.props.selectedFileSystemObject.uuid
            );
        },
        canUpdateFiles() {
            return this.studyPermissions
                ? this.studyPermissions.canUpdateStudy
                : false;
        },
        sortedFiles() {
            if (!this.$page.props.selectedFileSystemObject?.children) {
                return [];
            }
            
            const files = [...this.$page.props.selectedFileSystemObject.children];
            
            return files.sort((a, b) => {
                let aValue, bValue;
                
                switch (this.sortBy) {
                    case 'name':
                        aValue = a.name.toLowerCase();
                        bValue = b.name.toLowerCase();
                        break;
                    case 'date':
                        aValue = new Date(a.updated_at || a.created_at || 0);
                        bValue = new Date(b.updated_at || b.created_at || 0);
                        break;
                    case 'size':
                        // Use the new method to get accurate file sizes for sorting
                        aValue = this.getFileSizeForSorting(a);
                        bValue = this.getFileSizeForSorting(b);
                        break;
                    case 'kind':
                        aValue = a.type === 'directory' ? 'folder' : 'document';
                        bValue = b.type === 'directory' ? 'folder' : 'document';
                        break;
                    default:
                        return 0;
                }
                
                if (aValue < bValue) {
                    return this.sortOrder === 'asc' ? -1 : 1;
                }
                if (aValue > bValue) {
                    return this.sortOrder === 'asc' ? 1 : -1;
                }
                return 0;
            });
        },
    },
    mounted() {
        const vm = this;
        
        // Load view mode from localStorage
        const savedViewMode = localStorage.getItem('nmrxiv-files-view-mode');
        if (savedViewMode && ['grid', 'list'].includes(savedViewMode)) {
            vm.viewMode = savedViewMode;
        }
        
        vm.$page.props.selectedFileSystemObject = vm.file;
        vm.$page.props.selectedFolder = vm.file.children[0]
            ? vm.file.children[0].relative_url
            : "";
        if (!this.study.is_public) {
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
                uploadMultiple: false,
                disablePreviews: true,
                parallelUploads: 1,
                maxFiles: 10000,
                dictDefaultMessage: document.querySelector("#dropzone-message")
                    ? document.querySelector("#dropzone-message").innerHTML
                    : null,
                done() {},
                accept(file, done) {
                    const url = "/dashboard/storage/signed-storage-url";

                    const client = axios.create({
                        baseURL: window.location.origin,
                    });
                    axiosRetry(client, {
                        retries: 3,
                        retryCondition: (error) => {
                            return error.response.status === 500;
                        },
                    });

                    client
                        .post(url, {
                            file: file,
                            destination: vm.$page.props.selectedFolder,
                            project_id: vm.project.id,
                            study_id: vm.study.id,
                        })
                        .catch((err) => {
                            // The first request fails
                            if (
                                err.response.status !== 200 ||
                                err.response.status !== 201
                            ) {
                                throw new Error(
                                    `API call failed with status code: ${err.response.status} after multiple attempts`
                                );
                            }
                        })
                        .then(function (response) {
                            let data = response.data;
                            let headers = data.headers;
                            if ("Host" in headers) {
                                delete headers.Host;
                            }
                            file.uploadURL = data.url;
                            setTimeout(() => vm.dropzone.processFile(file));
                            done();
                        });
                },
                totaluploadprogress: function (progress) {
                    vm.progress = Math.ceil(progress);
                },
                queuecomplete: function () {
                    vm.status = "UPLOAD COMPLETE";
                    router.reload();
                    this.$page.props.selectedFileSystemObject = this.files[0];
                },
            };
            this.dropzone = new Dropzone(this.$el, options);

            vm.dropzone.on("processing", (file) => {
                vm.status = "UPLOAD IN PROGRESS";
                vm.dropzone.options.url = file.uploadURL;
            });
        }

        this.$page.props.selectedFileSystemObject = this.file.children[0];
    },
    methods: {
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
                    })
                    .catch((error) => {
                        console.error(`Failed to load children for ${file.name}:`, error);
                        file.children = []; // Set empty array to prevent repeated requests
                        file.loading = false;
                    });
            }
        },
        loadMol() {
            this.svgString = null;
            axios.get(this.downloadURL).then((response) => {
                if (response && response.data != "") {
                    let mol = OCL.Molecule.fromMolfile(response.data);
                    if (mol.toIsomericSmiles() != "") {
                        this.svgString = mol.toSVG(300, 300);
                    }
                }
            });
        },
        setViewMode(mode) {
            this.viewMode = mode;
            localStorage.setItem('nmrxiv-files-view-mode', mode);
        },
        formatDate(dateString) {
            if (!dateString) return '--';
            
            const date = new Date(dateString);
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const fileDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            
            const options = {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            
            // Format like "18. Aug 2025 at 10:52"
            return date.toLocaleDateString('en-GB', options)
                .replace(',', ' at')
                .replace(/(\d+)/, '$1.');
        },
        sortFiles(column) {
            if (this.sortBy === column) {
                // Toggle sort order if clicking the same column
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                // Set new column and default to ascending
                this.sortBy = column;
                this.sortOrder = 'asc';
            }
        },
        formatFileSize(file) {
            if (file.type === 'directory') return '--';
            
            try {
                const info = JSON.parse(file.info || '{}');
                const sizeInBytes = parseInt(info.size || 0);
                
                if (sizeInBytes === 0) return '0 bytes';
                
                const units = ['bytes', 'KB', 'MB', 'GB', 'TB'];
                const unitIndex = Math.floor(Math.log(sizeInBytes) / Math.log(1024));
                const size = (sizeInBytes / Math.pow(1024, unitIndex)).toFixed(1);
                
                return `${size} ${units[unitIndex]}`;
            } catch (error) {
                // Fallback to original size if JSON parsing fails
                return file.size || '0 bytes';
            }
        },
        getFileSizeForSorting(file) {
            if (file.type === 'directory') return 0;
            
            try {
                const info = JSON.parse(file.info || '{}');
                return parseInt(info.size || 0);
            } catch (error) {
                // Fallback to parsing original size string
                return parseInt(file.size?.replace(/[^\d]/g, '') || 0);
            }
        },
        getFileDownloadURL(file) {
            console.log('getFileDownloadURL called for:', file.name);
            if (file.type === 'directory') return '#';
            
            return (
                this.url +
                "/" +
                this.project.owner.username +
                "/download/" +
                this.project.slug +
                "?key=" +
                file.key +
                "&uuid=" +
                file.uuid
            );
        },
    },
};
</script>


