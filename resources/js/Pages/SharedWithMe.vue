<template>
  <app-layout title="Dashboard">
    <template #header>
      <div>
        <div class="flex flex-nowrap justify-between">
          <div class="lg:flex lg:items-center lg:justify-between">
            <div class="flex-1 min-w-0">
              <nav class="flex" aria-label="Breadcrumb">
                <ol role="list" class="flex items-center space-x-4">
                  <li>
                    <div class="flex">
                      <a class="text-sm font-medium text-gray-500 hover:text-gray-700"
                        >Dashboard</a
                      >
                    </div>
                  </li>
                  <li>
                    <div class="flex items-center">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                        class="flex-shrink-0 h-5 w-5 text-gray-400"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                          clip-rule="evenodd"
                        ></path></svg
                      ><a
                        class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                        >Shared With Me</a
                      >
                    </div>
                  </li>
                </ol>
              </nav>
              <h2
                class="mt-2 text-2xl font-bold break-words leading-7 text-gray-900 sm:text-3xl"
              >
                Shared With Me
              </h2>
            </div>
          </div>
        </div>
      </div>
    </template>
    <div class="px-12 py-8 mx-auto max-w-4xl">
      <team-projects  :mode="'listing'" :projects="projects"></team-projects>
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
  props: ["user", "team", "projects"],
};
</script>
