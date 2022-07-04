<template>
  <app-layout title="Projects">
    <template #header>
      <div class="bg-white border-b">
        <div class="px-12">
          <div class="flex flex-nowrap justify-between py-6">
            <div>
              <div
                class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"
              >
                Projects
              </div>
              <p>
                Explore, analyze, and share raw spectral data and assignements. Learn more
                about datasets.
              </p>
            </div>
          </div>
        </div>
      </div>
    </template>
    <div class="px-12 mb-24 mx-auto">
      <div
        class="mt-12 mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-4 lg:max-w-7xl"
      >
      <span v-for="project in projects.data"
          :key="project.id">
        <project-card :project="project"></project-card>
      </span>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import ProjectCard from "@/Shared/ProjectCard.vue";

export default {
  components: {
    AppLayout,
    ProjectCard
  },
  props: ["projects"],
};
</script>
