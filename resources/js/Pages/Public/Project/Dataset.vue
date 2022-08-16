<template>
    <app-layout :title="dataset.name">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="flex flex-nowrap justify-between py-6">
                        <div>
                            <div
                                class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"
                            >
                                {{ dataset.name }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div class="px-12 py-8 mx-auto max-w-4xl"></div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
    components: {
        AppLayout,
    },
    props: ["dataset"],
};
</script>
