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
            <div :class="[fullScreen ? 'px-6 py-4' : '', 'flex justify-end']">
                <button class="right" @click="toggleFullScreen">
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
                </button>
            </div>
            <div v-if="!readonly" :class="[fullScreen ? 'px-6 py-4' : '', '']">
                <form class="py-2 mb-3">
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
                                class="mt-2 block text-sm font-medium text-gray-900"
                            >
                                Drop Files or Folders to upload to
                                <span v-if="$page.props.selectedFolder"
                                    >"{{ $page.props.selectedFolder }}"</span
                                >
                                folder
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
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div
                :class="[
                    fullScreen
                        ? 'overflow-scroll h-full relative px-6 py-4'
                        : '',
                    'min-w-0 flex-1 bg-white border-t border-gray-200 lg:flex',
                ]"
            >
                <aside
                    class="py-3 px-2 lg:block lg:flex-shrink-0 lg:order-first overflow-scroll"
                >
                    <children :file="file"></children>
                </aside>
                <section class="p-6 flex-1 flex flex-col lg:order-last">
                    <div
                        v-if="
                            $page.props.selectedFileSystemObject &&
                            $page.props.selectedFileSystemObject.has_children
                        "
                        class="mb-3"
                    >
                        <ul
                            role="list"
                            class="mb-3 grid grid-cols-1 gap-5 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4"
                        >
                            <li
                                v-for="file in $page.props
                                    .selectedFileSystemObject.children"
                                :key="file.key"
                                class="relative shadow rounded-lg"
                            >
                                <div
                                    class="group block w-full aspect-w-10 aspect-h-7 py-4 bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-indigo-500 overflow-hidden"
                                >
                                    <span v-if="file.type == 'directory'">
                                        <FolderIcon
                                            class="cursor-pointer h-28 w-28 text-gray-400 flex-shrink-0 mx-auto"
                                            aria-hidden="true"
                                            @dblclick.stop="
                                                displaySelected(file)
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
                                    class="mt-2 px-2 py-1 block text-sm font-medium truncate text-gray-900 pointer-events-none"
                                >
                                    {{ file.name }}
                                </p>
                                <p
                                    class="block text-sm font-medium text-gray-500 pointer-events-none"
                                >
                                    {{ file.size }}
                                </p>
                            </li>
                        </ul>
                    </div>
                    <div v-else>
                        <div class="">
                            <span
                                v-if="
                                    $page.props.selectedFileSystemObject &&
                                    $page.props.selectedFileSystemObject.type ==
                                        'file'
                                "
                            >
                                <File-details
                                    :file="$page.props.selectedFileSystemObject"
                                ></File-details>
                            </span>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
<script>
import { Dropzone } from "dropzone";
import axiosRetry from "axios-retry";
import FileDetails from "@/Shared/FileDetails.vue";
import {
    FolderIcon,
    DocumentTextIcon,
    ChevronRightIcon,
    HomeIcon,
} from "@heroicons/vue/solid";

export default {
    components: {
        FolderIcon,
        DocumentTextIcon,
        ChevronRightIcon,
        HomeIcon,
        FileDetails,
    },
    props: ["draft", "readonly"],
    data() {
        return {
            status: "",
            dropzone: null,
            fullScreen: false,
            precentageUpload: 0,
            busy: false,
            file: null,
            url: null,
        };
    },
    computed: {
        baseURL() {
            return String(this.$page.props.url);
        },
    },
    mounted() {
        if (this.draft) {
            this.url =
                this.baseURL + "/dashboard/drafts/" + this.draft.id + "/files";
            this.loadFiles();
            this.loadDropZone();
        }
    },
    methods: {
        toggleFullScreen() {
            this.fullScreen = !this.fullScreen;
        },
        updateBusyStatus(status) {
            this.busy = status;
            this.$emit("loading", this.busy);
        },
        loadFiles() {
            this.updateBusyStatus(true);
            axios.get(this.url).then((response) => {
                this.updateBusyStatus(false);
                this.file = response.data.file;
                this.file.has_children = true;
                this.$page.props.selectedFileSystemObject = this.file;
            });
        },
        annotate() {
            this.updateBusyStatus(true);
            axios
                .get("/dashboard/drafts/" + this.draft.id + "/annotate")
                .then(() => {
                    this.updateBusyStatus(false);
                    this.loadFiles();
                });
        },
        processFilesDZL(vm, filesBatch) {
            vm.batchesCount += 1;
            const url = "/dashboard/storage/signed-draft-storage-url";
            const client = axios.create({ baseURL: window.location.origin });
            axiosRetry(client, {
                retries: 3,
                retryCondition: (error) => {
                    return error.response.status === 500;
                },
            });
            client
                .post(url, {
                    draft_files: filesBatch,
                    destination: vm.$page.props.selectedFolder,
                    draft_id: vm.draft.id,
                })
                .catch((err) => {
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
                    data.forEach((u) => {
                        let cFile = vm.dropzone.files.find((f) => {
                            if (f.fullPath) {
                                return f.fullPath == u.fullPath;
                            } else {
                                return "/" + f.name == u.fullPath;
                            }
                        });
                        if (cFile) {
                            let headers = u.headers;
                            if ("Host" in headers) {
                                delete headers.Host;
                            }
                            cFile.uploadURL = u.url;
                            setTimeout(() => vm.dropzone.processFile(cFile));
                        }
                    });
                });
        },
        loadDropZone() {
            this.$nextTick(() => {
                const vm = this;

                vm.totalFilesCount = 0;
                vm.uploadedFilesCount = 0;
                vm.batchCount = 100;
                vm.count = 0;

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
                    autoQueue: false,
                    maxFiles: 10000,
                    dictDefaultMessage: document.querySelector(
                        "#fs-dropzone-message"
                    ).innerHTML,
                    totaluploadprogress: function (progress) {
                        vm.progress = Math.ceil(progress);
                    },
                };
                vm.dropzone = new Dropzone("#fs-dropzone", options);
                vm.dropzone.on("processing", (file) => {
                    vm.dropzone.options.url = file.uploadURL;
                    vm.status = "UPLOAD IN PROGRESS";
                });
                vm.dropzone.on("success", (file) => {
                    vm.uploadedFilesCount += 1;
                    vm.precentageUpload =
                        (vm.uploadedFilesCount / vm.totalFilesCount) * 100;
                });
                vm.dropzone.on("addedfile", (file) => {
                    vm.selectedFSO.push(file);
                });
                vm.dropzone.on("addedfiles", (files) => {
                    this.updateBusyStatus(true);
                    setTimeout(() => {
                        var timer = setInterval(function () {
                            if (vm.totalFilesCount === vm.selectedFSO.length) {
                                clearInterval(timer);
                                vm.status = "BATCH UPLOAD STARTED";
                                for (
                                    let i = 0;
                                    i < vm.totalFilesCount;
                                    i += vm.batchCount
                                ) {
                                    let filesBatch = vm.dropzone.files.slice(
                                        i,
                                        i + vm.batchCount
                                    );
                                    vm.processFilesDZL(vm, filesBatch);
                                }
                            } else {
                                vm.totalFilesCount = vm.selectedFSO.length;
                                vm.status = "PROCESSING FILES";
                            }
                        }, 500);
                    });
                });
                vm.dropzone.on("queuecomplete", () => {
                    vm.annotate();
                    vm.dropzone.removeAllFiles();
                    vm.precentageUpload = 0;
                    vm.totalFilesCount = 0;
                    vm.uploadedFilesCount = 0;
                    vm.status = "UPLOAD COMPLETE";
                    this.updateBusyStatus(false);
                    setTimeout(() => {
                        vm.status = null;
                    }, 5000);
                });
            });
        },
    },
};
</script>
