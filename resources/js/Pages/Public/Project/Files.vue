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
                    <div
                        class="min-w-0 flex-1 min-h-screen border-gray-200 lg:flex"
                    >
                        <aside
                            class="hidden py-3 px-2 lg:block lg:flex-shrink-0 lg:order-first"
                        >
                            <div
                                v-if="project.data.files != null"
                                class="h-full relative flex flex-col w-64 border-r border-gray-200 overflow-y-auto"
                            >
                                <children
                                    :file="project.data.files"
                                    :project="project"
                                ></children>
                            </div>
                        </aside>
                        <section
                            class="min-w-0 p-6 flex-1 h-full flex flex-col overflow-y-auto lg:order-last"
                        >
                            <div
                                v-if="
                                    $page.props.selectedFileSystemObject &&
                                    $page.props.selectedFileSystemObject
                                        .has_children
                                "
                                class="mb-3"
                            >
                                <div v-if="$page.props.selectedFileSystemObject.uuid" class="py-2 mb-2 block">
                                    <p class="font-bold text-xl">
                                        {{
                                            $page.props.selectedFileSystemObject
                                                .name
                                        }}
                                        <a
                                            :href="downloadURL"
                                            class="ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 float-right"
                                        >
                                            Download
                                        </a>
                                    </p>
                                </div>
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
                                            <span
                                                v-if="file.type == 'directory'"
                                            >
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
                                            class="mt-2 px-2 py-1 block truncate text-sm font-medium text-gray-900 pointer-events-none"
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
                                            $page.props
                                                .selectedFileSystemObject &&
                                            $page.props.selectedFileSystemObject
                                                .type == 'file'
                                        "
                                    >
                                        <File-details
                                            :project="project.data"
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
    </project-layout>
</template>

<script>
import ProjectLayout from "@/Pages/Public/Project/Layout.vue";
import FileDetails from "@/Shared/FileDetails.vue";
import {
    FolderIcon,
    DocumentTextIcon,
    ChevronRightIcon,
    HomeIcon,
} from "@heroicons/vue/24/solid";

export default {
    components: {
        ProjectLayout,
        FileDetails,
        FolderIcon,
        DocumentTextIcon,
        ChevronRightIcon,
        HomeIcon,
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
    },
    methods: {},
};
</script>
