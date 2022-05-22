<template>
    <app-layout :title="dataset.name">
        <template #header>
            <div>
                <div class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"> 
                    {{ dataset.name }}
                </div> 
            </div> 
        </template>
        <div class="px-12 py-8 mx-auto max-w-4xl">
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'

    export default {
        components: {
            AppLayout,
        },
        props: [
            'dataset'
        ]
    }
</script>
