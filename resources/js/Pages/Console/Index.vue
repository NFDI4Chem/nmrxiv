<template>
    <app-layout title="Console">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="flex flex-nowrap justify-between py-6">
                        <div>
                            <div
                                class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"
                            >
                                Console
                            </div>
                        </div>
                        <div>&nbsp;</div>
                    </div>
                </div>
            </div>
        </template>
        <div class="py-12 px-10">
            <div>
                <div
                    class="rounded-lg bg-gray-200 overflow-hidden shadow divide-y divide-gray-200 sm:divide-y-0 sm:grid sm:grid-cols-2 sm:gap-px"
                >
                    <!-- User and Profile Block -->
                    <blocks
                        heading="Users & Profiles"
                        description="Allows managing users including edit/view their permissions."
                        name="users"
                        path="console.users"
                    />
                    <!-- Announcement Block -->
                    <blocks
                        heading="Announcements"
                        description="Manage announcements."
                        name="announcements"
                        path="console.announcements"
                    />
                    <div class="relative group bg-white p-6">
                        <div>
                            <span
                                class="rounded-lg inline-flex p-3 bg-sky-50 text-sky-700 ring-4 ring-white"
                            >
                                <!-- Icon component getting conditionally rendered on the basis of icon name passed -->
                                <EllipsisVerticalIcon
                                    class="h-5 w-5"
                                    aria-hidden="true"
                                />
                            </span>
                        </div>
                        <!-- Heading and Description section -->
                        <div class="mt-8">
                            <h3 class="text-lg font-medium">
                                <a
                                    target="_blank"
                                    href="/horizon"
                                    class="focus:outline-none"
                                >
                                    <span
                                        class="absolute inset-0"
                                        aria-hidden="true"
                                    ></span>
                                    Queues
                                </a>
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Monitor queues and jobs status
                            </p>
                        </div>
                        <span
                            class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400"
                            aria-hidden="true"
                        >
                            <svg
                                class="h-6 w-6"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z"
                                />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import Blocks from "@/Shared/Blocks.vue";
import { EllipsisVerticalIcon } from "@heroicons/vue/24/solid";

export default {
    components: {
        Link,
        AppLayout,
        Blocks,
        EllipsisVerticalIcon,
    },
};
</script>
