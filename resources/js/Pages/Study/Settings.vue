<template>
    <app-layout :title="study.name">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="flex flex-nowrap justify-between py-6">
                        <div>
                            <div
                                class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"
                            >
                                <Link
                                    :href="route('dashboard.studies', study.id)"
                                    >{{ study.name }}</Link
                                >&nbsp;/&nbsp;Settings
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div class="py-12 px-10">
            <div>
                <study-delete :study="study"></study-delete>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import StudyDelete from "@/Pages/Study/Partials/Delete.vue";

export default {
    components: {
        Link,
        AppLayout,
        StudyDelete,
    },
    props: ["study"],
};
</script>
