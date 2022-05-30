<template>
    <app-layout title="Console">
        <template #header>
            <div>
                <div
                    class="
                        flex
                        items-center
                        text-sm text-gray-700
                        uppercase
                        font-bold
                        tracking-widest
                    "
                >
                    Console
                </div>
            </div>
            <div>&nbsp;</div>
        </template>
        <div class="py-12 px-10">
            <div>
                <div
                    class="
                        rounded-lg
                        bg-gray-200
                        overflow-hidden
                        shadow
                        divide-y divide-gray-200
                        sm:divide-y-0 sm:grid sm:grid-cols-2 sm:gap-px
                    "
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
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import Blocks from "@/Shared/Blocks.vue";

export default {
    components: {
        Link,
        AppLayout,
        Blocks,
    },
};
</script>