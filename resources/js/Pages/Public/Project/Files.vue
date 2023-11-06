<template>
    <project-layout :project="project" :selectedTab="tab">
        <template #project-content>
            <div class="pb-10 mb-10 max-w-7xl mx-auto p-4">
                <div v-if="project.data.files">
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
                            <li>
                                <div class="flex items-center">
                                    <ChevronRightIcon
                                        class="flex-shrink-0 h-5 w-5 text-gray-400"
                                        aria-hidden="true"
                                    />
                                    <a
                                        class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                                        >{{ project.data.name }}</a
                                    >
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
                    <div>
                        <file-system-browser
                            ref="fsbRef"
                            :readonly="true"
                            :height="'h-[calc(100vh-385px)]'"
                        ></file-system-browser>
                    </div>
                </div>
            </div>
        </template>
    </project-layout>
</template>

<script>
import ProjectLayout from "@/Pages/Public/Project/Layout.vue";
import FileSystemBrowser from "./../../../Shared/FileSystemBrowser.vue";
import {
    FolderIcon,
    DocumentTextIcon,
    ChevronRightIcon,
    HomeIcon,
} from "@heroicons/vue/24/solid";

export default {
    components: {
        ProjectLayout,
        FolderIcon,
        DocumentTextIcon,
        ChevronRightIcon,
        HomeIcon,
        FileSystemBrowser,
    },
    props: ["project", "tab"],
    data() {
        return {
            selectedFileSystemObject: null,
        };
    },
    computed: {
        url() {
            return String(this.$page.props.url);
        },
        downloadURL() {
            return (
                this.url +
                "/" +
                this.project.data.owner.username +
                "/download/" +
                this.project.data.slug +
                "?key=" +
                this.$page.props.selectedFileSystemObject.name +
                "&uuid=" +
                this.$page.props.selectedFileSystemObject.uuid
            );
        },
    },
    mounted() {
        this.$page.props.selectedFileSystemObject = this.project.data.files;
        this.$page.props.selectedFolder = "/";
        this.$nextTick(function () {
            if (this.$refs.fsbRef) {
                this.$refs.fsbRef.loadFiles();
            }
        });
    },
    methods: {},
};
</script>
