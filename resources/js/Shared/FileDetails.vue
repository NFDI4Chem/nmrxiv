<!-- This example requires Tailwind CSS v2.0+ -->
<template>
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 text-xl font-bold text-gray-900">
                {{ file.name }}
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
                        {{ bytesToSize(JSON.parse(file.info).size) }}
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
                                    <PaperClipIcon
                                        class="flex-shrink-0 h-5 w-5 text-gray-400"
                                    />
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
                <!-- <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
          <dt class="text-sm font-medium text-gray-500">Updated at</dt>
          <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
            {{ formatDateTime(file.updated_at) }}
          </dd>
        </div> -->
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
    },
    mounted() {},
    methods: {},
};
</script>
