<template>
  <app-layout title="Dashboard">
    <template #header>
      <div class="bg-white border-b">
        <div class="px-12">
          <div class="flex flex-nowrap justify-between py-6">
            <div>
              <div class="flex flex-nowrap justify-between">
                <div class="lg:flex lg:items-center lg:justify-between">
                  <div class="flex-1 min-w-0">
                    <nav class="flex" aria-label="Breadcrumb">
                      <ol role="list" class="flex items-center space-x-4">
                        <li>
                          <div class="flex">
                            <a
                              class="text-sm font-medium text-gray-500 hover:text-gray-700"
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
                              >Trash</a
                            >
                          </div>
                        </li>
                      </ol>
                    </nav>
                    <h2
                      class="mt-2 text-2xl font-bold break-words leading-7 text-gray-900 sm:text-3xl"
                    >
                      Trash
                    </h2>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
    <div v-if="projects.length > 0" class="px-12 py-8 mx-auto max-w-4xl">
      <team-projects :mode="'listing'" :projects="projects"></team-projects>
    </div>
    <div v-else>
      <div class="text-center py-12">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="mx-auto h-24 w-24 text-gray-400"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
          />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No items in the trash.</h3>
        <p class="mt-1 text-sm text-gray-500">
          You dont have any Projects, Studies or Datasets in your trash.
        </p>
      </div>
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
  props: ["user", "projects"],
};
</script>
