<template>
    <Head :title="'License - ' + project.data.name" />
    <project-layout :project="project" :selected-tab="tab">
        <template #project-content>
            <div class="pb-10 mb-10 py-6">
                <div
                    v-if="workspace && canManageProjectLicense"
                    class="mb-6 rounded-md border border-teal-100 bg-teal-50 px-4 py-3 text-sm text-teal-900 dark:border-teal-900/40 dark:bg-teal-950/30 dark:text-teal-100"
                >
                    <span>To change the license,</span>
                    <Link
                        :href="
                            route('dashboard.project.settings', project.data.id)
                        "
                        class="ml-1 font-semibold text-teal-800 underline hover:text-teal-950 dark:text-teal-200"
                    >
                        open project settings
                    </Link>
                    <span class="hidden sm:inline"> in the dashboard.</span>
                </div>
                <h3 class="text-xl font-extrabold text-blue-gray-900">
                    {{ project.data.license.title }}
                </h3>

                <div class="mt-3 space-y-8 divide-y divide-y-blue-gray-200">
                    <div
                        class="grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6"
                    >
                        <div class="sm:col-span-6">
                            <p
                                style="max-width: 100ch !important"
                                class="prose mt-1 text-sm text-blue-gray-500"
                                v-html="sanitizeHtml(project.data.license.body)"
                            ></p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </project-layout>
</template>

<script>
import ProjectLayout from "@/Pages/Public/Project/Layout.vue";
import { Head, Link } from "@inertiajs/vue3";

export default {
    components: {
        ProjectLayout,
        Head,
        Link,
    },
    props: ["project", "tab"],
    data() {
        return {};
    },
    computed: {
        workspace() {
            return this.$page.props.workspace ?? null;
        },
        canManageProjectLicense() {
            const p = this.workspace?.projectPermissions;
            return (
                p?.canUpdateProject === true || p?.canManageSettings === true
            );
        },
    },
    mounted() {},
    methods: {},
};
</script>
