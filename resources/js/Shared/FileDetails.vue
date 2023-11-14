<!-- This example requires Tailwind CSS v2.0+ -->
<template>
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 text-xl font-bold text-gray-900">
                <span
                    v-if="file.status == 'missing'"
                    class="float-left inline pr-4 pt-1 text-sm font-medium pointer-events-none"
                >
                    <span
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
                    </span> </span
                >{{ file.name }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">File information</p>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div
                    class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                >
                    <dt class="text-sm font-medium text-gray-500">
                        Uploaded at
                    </dt>
                    <dd
                        class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                    >
                        {{ formatDateTime(file.created_at) }}
                    </dd>
                    <dt class="text-sm font-medium text-gray-500">File size</dt>
                    <dd
                        class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                    >
                        {{ bytesToSize(fileInfo.size) }}
                    </dd>
                    <dt class="text-sm font-medium text-gray-500">ETag</dt>
                    <dd
                        class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                    >
                        <div v-if="fileInfo.ETag">
                            {{ fileInfo.ETag.replace(/"/g, "") }}
                        </div>
                        <div v-else>-</div>
                    </dd>
                </div>
                <div></div>
                <div
                    class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                >
                    <dt class="text-sm font-medium text-gray-500">Content</dt>
                    <dd
                        class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                    >
                        <ul
                            role="list"
                            class="border border-gray-200 rounded-md divide-y divide-gray-200"
                        >
                            <li
                                class="pl-3 pr-4 py-3 flex items-center justify-between text-sm"
                            >
                                <div class="w-0 flex-1 flex items-center">
                                    <svg
                                        class="flex-shrink-0 h-5 w-5 text-gray-400"
                                        x-description="Heroicon name: solid/paper-clip"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                    <span class="ml-2 flex-1 w-0 truncate">
                                        {{ file.name }}
                                    </span>
                                </div>
                                <div
                                    v-if="downloadURL"
                                    class="ml-4 flex-shrink-0"
                                >
                                    <a
                                        :href="downloadURL"
                                        class="font-medium text-indigo-600 hover:text-indigo-500"
                                    >
                                        Download
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</template>

<script>
import { PaperClipIcon } from "@heroicons/vue/24/solid";

export default {
    components: {
        PaperClipIcon,
    },
    props: ["file", "project", "study"],
    setup() {
        return {};
    },
    data() {
        return {};
    },
    computed: {
        downloadURL() {
            if (this.study || this.project) {
                let project = null;
                if (this.study && !this.project) {
                    project = this.study.project;
                } else {
                    project = this.project;
                }
                if (project) {
                    return (
                        this.url +
                        "/" +
                        project.owner.username +
                        "/download/" +
                        project.slug +
                        "?key=" +
                        this.file.key +
                        "&uuid=" +
                        this.file.uuid
                    );
                }
            }
        },
        url() {
            return String(this.$page.props.url);
        },
        fileInfo() {
            return JSON.parse(this.file.info);
        },
    },
};
</script>
