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
        <div
          v-for="project in projects.data"
          :key="project.id"
          class="flex flex-col rounded-lg shadow-lg overflow-hidden"
        >
          <div
            class="flex-shrink-0 h-32 bg-gradient-to-tl from-sky-400 to-cyan-300 opacity-60"
          ></div>
          <div class="flex-1 bg-white p-3 flex flex-col justify-between">
            <div class="flex-1">
              <a :href="'/projects/' + project.slug" class="block">
                <p class="text-lg font-semibold text-gray-900 line-clamp-2">
                  {{ project.name }}
                </p>
                <p class="mt-2 text-sm text-gray-500 line-clamp-4">
                  {{ project.description }}
                </p>
              </a>
            </div>
            <div class="mt-6 flex items-center">
              <div class="flex-shrink-0">
                <a href="#">
                  <img
                    class="h-10 w-10 rounded-full"
                    src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                    alt="Roel Aufderehar"
                  />
                </a>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">
                  <a href="#" class="hover:underline"> Roel Aufderehar </a>
                </p>
                <div class="flex space-x-1 text-sm text-gray-500">
                  <time datetime="2020-03-16">{{ formatDate(project.created_at) }} </time>
                  <span aria-hidden="true"> &middot; </span>
                  <!-- <span> 6 min read </span> -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
  components: {
    AppLayout,
  },
  props: ["projects"],
};
</script>
