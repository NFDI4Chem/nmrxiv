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
            <div>
                <div class="md:grid mb-10 md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1 flex justify-between">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900">
                                Schema
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Project's repository schema version and
                                validations details
                            </p>
                        </div>
                        <div class="px-4 sm:px-0"></div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div
                            class="px-4 py-5 sm:p-6 bg-white shadow sm:rounded-lg"
                        >
                            <div class="max-w-xl text-sm text-gray-600">
                                Some of the nmrXiv functionality might not be
                                available if your project is not migrated to our
                                latest schema version. Where ever possible, we
                                automatically migrate your data to be compatible
                                with our latest schema version. Automatic
                                migration might not be possible in some cases
                                due to major nmrXiv schema version updates or
                                missing metadata. Please get in touch with us to
                                find out if it's possible to update your project
                                to the latest schema version. You can find all
                                information related to the nmrXiv schema on our
                                documentation site.
                            </div>
                            <div class="mt-5">
                                <span
                                    type="button"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:outline-none focus:border-yellow-700 focus:ring focus:ring-yellow-200 active:bg-yellow-600 disabled:opacity-25 transition"
                                >
                                    <InformationCircleIcon
                                        class="flex-shrink-0 mr-1.5 h-5 w-5 mr-4 inline"
                                    />
                                    {{ project.schema_version }}
                                </span>
                                <span
                                    class="ml-4"
                                    v-if="project.schema_version == schema"
                                >
                                    Migrate now?
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
import { InformationCircleIcon } from "@heroicons/vue/24/solid";

export default {
    components: {
        Link,
        AppLayout,
        ProjectDelete,
        ProjectRestore,
        ProjectArchive,
        ProjectUnarchive,
        InformationCircleIcon,
    },
    props: ["project", "studies", "schema"],
};
</script>
