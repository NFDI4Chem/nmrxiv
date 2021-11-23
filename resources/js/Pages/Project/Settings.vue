<template>
  <app-layout :title="project.name">
    <template #header>
      <div>
        <div
          class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"
        >
          <Link :href="route('project', project.id)">{{ project.name }}</Link>&nbsp;/&nbsp;Settings
        </div>
      </div>
    </template>
    <div class="py-12 px-10">
      <div>
        <project-delete :project="project"></project-delete>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import ProjectDelete from "@/Pages/Project/Partials/Delete.vue";

export default {
  components: {
    Link,
    AppLayout,
    ProjectDelete
  },
  props: ["project", "studies"],
};
</script>
