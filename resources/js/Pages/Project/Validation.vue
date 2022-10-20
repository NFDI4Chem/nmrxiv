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
                                >&nbsp;/&nbsp;Validation
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div class="py-8 px-10">
            <Validation
                :project="project"
                :validation="validation.report"
            ></Validation>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import Validation from "@/Shared/Validation.vue";

export default {
    components: {
        Link,
        AppLayout,
        Validation,
    },
    props: ["project", "validation"],
};
</script>
