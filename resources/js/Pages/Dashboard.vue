<template>
    <app-layout title="Dashboard">
        <template #header>
            <div>
                <div class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"> 
                    {{ user.current_team.name }} Dashboard
                </div> 
                <div class="flex mt-3 flex-row-reverse justify-end">
                    <img :key="user.id" v-for="user in team.users" class="w-8 h-8 -mr-3 rounded-full border-3 border-dark" :src="user.profile_photo_url" :alt="user.name" />
                </div>
            </div> 
            <div>
                <a :href="'/teams/'+ user.current_team.id " class="text-sm text-gray-800 font-bold">
                    Team Settings
                </a>
            </div>
            <!-- <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2> -->
        </template>

        <div class="py-12">
            <welcome :projects="projects" />
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import Welcome from '@/Jetstream/Welcome.vue'

    export default {
        components: {
            AppLayout,
            Welcome,
        },
        props: [
            'user',
            'team',
            'projects'
        ]
    }
</script>
