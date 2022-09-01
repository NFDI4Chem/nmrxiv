<template>
    <app-layout :title="project.name">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="flex flex-nowrap justify-between py-6">
                        <div>
                            <div
                                class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"
                            >
                                <Link
                                    :href="
                                        route('dashboard.projects', project.id)
                                    "
                                    >{{ project.name }}</Link
                                >&nbsp;/&nbsp;Settings
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div class="py-12 px-10">
            <div v-if="!project.is_public && !project.is_deleted">
                <project-delete :project="project"></project-delete>
            </div>
            <div v-if="project.is_deleted">
                <project-restore :project="project"></project-restore>
            </div>
            <div v-if="project.is_public && !project.is_archived">
                <project-archive :project="project"></project-archive>
            </div>
            <div v-if="project.is_public && project.is_archived">
                <project-unarchive :project="project"></project-unarchive>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import ProjectDelete from "@/Pages/Project/Partials/Delete.vue";
import ProjectRestore from "@/Pages/Project/Partials/Restore.vue";
import ProjectArchive from "@/Pages/Project/Partials/Archive.vue";
import ProjectUnarchive from "@/Pages/Project/Partials/Unarchive.vue";

export default {
    components: {
        Link,
        AppLayout,
        ProjectDelete,
        ProjectRestore,
        ProjectArchive,
        ProjectUnarchive,
    },
    props: ["project", "studies"],
};
</script>
