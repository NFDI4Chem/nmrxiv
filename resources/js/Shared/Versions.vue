<template>
    <TransitionRoot as="template" :show="open">
        <Dialog
            as="div"
            class="fixed inset-0 overflow-hidden z-50"
            @close="open = false"
        >
            <div class="absolute inset-0 overflow-hidden">
                <TransitionChild
                    as="template"
                    enter="ease-in-out duration-500"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="ease-in-out duration-500"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <DialogOverlay
                        class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    />
                </TransitionChild>
                <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                    <TransitionChild
                        as="template"
                        enter="transform transition ease-in-out duration-500 sm:duration-700"
                        enter-from="translate-x-full"
                        enter-to="translate-x-0"
                        leave="transform transition ease-in-out duration-500 sm:duration-700"
                        leave-from="translate-x-0"
                        leave-to="translate-x-full"
                    >
                        <div class="relative w-96">
                            <TransitionChild
                                as="template"
                                enter="ease-in-out duration-500"
                                enter-from="opacity-0"
                                enter-to="opacity-100"
                                leave="ease-in-out duration-500"
                                leave-from="opacity-100"
                                leave-to="opacity-0"
                            >
                                <div
                                    class="absolute top-0 left-0 -ml-8 pt-4 pr-2 flex sm:-ml-10 sm:pr-4"
                                >
                                    <button
                                        type="button"
                                        class="rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                                        @click="open = false"
                                    >
                                        <span class="sr-only">Close panel</span>
                                        <XMarkIcon
                                            class="h-6 w-6"
                                            aria-hidden="true"
                                        />
                                    </button>
                                </div>
                            </TransitionChild>
                            <div class="h-full bg-white p-8 overflow-y-auto">
                                <div class="pb-16 space-y-6">
                                    <div>
                                        <div
                                            class="mt-0 flex items-start justify-between"
                                        >
                                            <div>
                                                <div
                                                    class="mt-0 flex items-start justify-between"
                                                >
                                                    <div
                                                        class="grid grid-cols-1 pt-1 text-left pt-0"
                                                    >
                                                        <div
                                                            class="text-xs italic text-gray-600 pr-5"
                                                        >
                                                            <span
                                                                class="text-gray-400"
                                                                >Last updated
                                                                on</span
                                                            ><br />
                                                            {{
                                                                formatDateTime(
                                                                    dataset.updated_at
                                                                )
                                                            }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div>
                                            <h2
                                                id="timeline-title"
                                                class="text-lg font-medium text-gray-900"
                                            >
                                                Edited by
                                            </h2>
                                            <div class="mt-6 flow-root">
                                                <ul role="list" class="-mb-8">
                                                    <li
                                                        v-for="version in versions"
                                                        :key="version.id"
                                                    >
                                                        <div
                                                            class="relative pb-8"
                                                        >
                                                            <span
                                                                class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                                                aria-hidden="true"
                                                            ></span>
                                                            <div
                                                                class="relative flex space-x-3"
                                                            >
                                                                <div>
                                                                    <img
                                                                        :src="
                                                                            version
                                                                                .user
                                                                                .profile_photo_url
                                                                        "
                                                                        :alt="
                                                                            version
                                                                                .user
                                                                                .name
                                                                        "
                                                                        class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white object-cover"
                                                                    />
                                                                </div>
                                                                <div
                                                                    class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4"
                                                                >
                                                                    <div>
                                                                        <a
                                                                            class="font-medium text-gray-900"
                                                                            >{{
                                                                                version
                                                                                    .user
                                                                                    .name
                                                                            }}</a
                                                                        ><br />
                                                                        <small
                                                                            ><time
                                                                                >{{
                                                                                    formatDateTime(
                                                                                        version.updated_at
                                                                                    )
                                                                                }}</time
                                                                            ></small
                                                                        >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <small
                                    v-if="versions.length"
                                    class="text-gray-500"
                                    ><i
                                        >No logs before <br />{{
                                            formatDateTime(
                                                versions[versions.length - 1][
                                                    "updated_at"
                                                ]
                                            )
                                        }}</i
                                    ></small
                                >
                            </div>
                        </div>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script>
import {
    Dialog,
    DialogOverlay,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import { HeartIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import { PlusSmallIcon } from "@heroicons/vue/24/solid";

export default {
    components: {
        Dialog,
        DialogOverlay,
        TransitionChild,
        TransitionRoot,
        HeartIcon,
        PlusSmallIcon,
        XMarkIcon,
    },
    props: ["dataset"],
    data() {
        return {
            open: false,
            versions: [],
        };
    },
    updated() {
        this.fetchVersions();
    },
    methods: {
        toggleVersions() {
            this.open = !this.open;
        },
        fetchVersions() {
            axios
                .get(
                    "/dashboard/datasets/" + this.dataset.id + "/nmriumVersions"
                )
                .then((response) => {
                    this.versions = response.data;
                });
        },
    },
};
</script>
