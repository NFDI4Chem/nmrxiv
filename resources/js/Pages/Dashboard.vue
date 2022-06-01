<template>
  <app-layout title="Dashboard">
    <template #header>
      <div>
        <div
          class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"
        >
          <div v-if="team.personal_team">Your</div>
          <div v-else>
            {{ user.current_team.name }}
          </div>
          &nbsp;Dashboard
        </div>
        <div v-if="team.users" class="flex mt-3 flex-row-reverse justify-end">
          <img
            :key="user.id"
            v-for="user in team.users"
            class="w-8 h-8 -mr-2 rounded-full border-2 border-white"
            :src="user.profile_photo_url"
            :alt="user.name"
          />
        </div>
      </div>
      <div v-if="!team.personal_team">
        <a
          :href="'/teams/' + user.current_team.id"
          class="text-sm text-gray-800 font-bold"
        >
          Team Settings
        </a>
      </div>
    </template>
    <div class="px-12 py-8 mx-auto max-w-4xl">
      <team-projects
        :team="team"
        :editable="editable"
        :mode="'create'"
        :projects="projects"
      ></team-projects>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import TeamProjects from "@/Pages/Project/Index.vue";

export default {
  components: {
    AppLayout,
    TeamProjects,
  },
  props: ["user", "team", "projects", "teamRole"],
  computed: {
    editable() {
      if (this.teamRole && this.teamRole.name) {
        if (this.teamRole.key == "owner" || this.teamRole.key == "collaborator") {
          return true;
        } else {
          return false;
        }
      }
    },
  },
};
</script>
