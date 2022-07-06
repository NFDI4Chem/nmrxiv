<template>
  <app-layout :title="project.name">
    <template v-if="project" #header>
      <div class="bg-white border-b">
        <div class="border-b">
          <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
            <img
              v-if="project && project.data.owner"
              class="inline h-7 w-7 rounded-full"
              :src="project.data.owner.profile_photo_url"
            />
            <p class="inline ml-3 text-xs font-bold text-gray-500 uppercase">
              <a href="#" class="text-gray-900">{{
                project.data.owner.first_name + " " + project.data.owner.last_name
              }}</a>
              updated on
              <time datetime="2020-08-25">{{ formatDate(project.data.updated_at) }}</time>
            </p>

            <div class="float-right">
              <a
                class="ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
              >
                Download
              </a>
            </div>
            <div class="float-right">
              <div class="flex-shrink-0">
                <span
                  v-if="project.data.stats"
                  class="relative z-0 inline-flex shadow-sm rounded-lg"
                >
                  <button
                    type="button"
                    class="relative inline-flex items-center px-1 py-1 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>
                  <a
                    class="-ml-px relative inline-flex items-center px-4 py-1 rounded-r-md border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    {{ project.data.stats.likes }}
                  </a>
                </span>
              </div>
            </div>
          </div>
        </div>
        <div>
          <div class="pb-5">
            <div>
              <div>
                <img
                  class="h-32 w-full object-cover lg:h-48"
                  src="https://images.unsplash.com/photo-1637625854255-d893202554f4?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80"
                  alt=""
                />
              </div>
              <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="-mt-12 sm:-mt-16 sm:flex sm:items-end sm:space-x-5">
                  <div class="flex mb-5">
                    <img
                      class="h-24 w-72 object-cover border rounded ring-4 ring-white sm:h-32 sm:w-96"
                      :src="project.data.photo_url"
                      alt=""
                    />
                  </div>
                  <div
                    class="mt-6 sm:flex-1 sm:min-w-0 sm:flex sm:items-center sm:justify-end sm:space-x-6 sm:pb-1"
                  >
                    <div class="sm:hidden 2xl:block min-w-0 flex-1">
                      <h1 class="text-2xl font-bold text-gray-900 break-words">
                        {{ project.data.name }}
                      </h1>
                    </div>
                  </div>
                </div>
                <div class="hidden sm:block 2xl:hidden min-w-0 flex-1">
                  <h1 class="text-2xl font-bold text-gray-900 break-words">
                    {{ project.data.name }}
                  </h1>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
    <div>
      <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none xl:order-last">
        <div>
          <div class="mt-6 sm:mt-2 2xl:mt-5">
            <div class="border-b border-gray-200">
              <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                  <a
                    @click="selectedTab = tab.name"
                    :class="[
                      selectedTab == tab.name ? 'border-pink-500 text-gray-900' : '',
                      'cursor-pointer text-gray-900 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                    ]"
                    :key="tab.name"
                    v-for="tab in tabs"
                    aria-current="page"
                  >
                    {{ tab.name }}
                  </a>
                </nav>
              </div>
            </div>
          </div>

          <div v-if="selectedTab == 'Data'">
            <div class="pb-10 mb-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
              <h3 class="text-xl font-extrabold text-blue-gray-900">About project</h3>

              <div class="mt-6 space-y-8 divide-y divide-y-blue-gray-200">
                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                  <div class="sm:col-span-6">
                    <h2 class="text-xl font-medium text-blue-gray-900">Description</h2>
                    <p class="mt-1 text-sm text-blue-gray-500">
                      {{ project.data.description }}
                    </p>
                  </div>
                  <div>
                    <a
                      :key="tag.id"
                      v-for="tag in project.data.tags"
                      target="_blank"
                      :href="'/projects?tag=' + tag.name.en"
                      class="mr-1 float rounded-full border border-gray-200 items-center py-1.5 pl-3 pr-3 text-sm font-medium bg-white text-gray-900 hover:text-white hover:bg-black"
                      ><span>{{ tag.name.en }}</span></a
                    >
                  </div>
                </div>

                <div class="pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                  <div class="sm:col-span-6">
                    <h2 class="text-xl font-medium text-blue-gray-900">Authors</h2>
                    <!-- <p class="mt-1 text-sm text-blue-gray-500">
                      This information will be displayed publicly so be careful what you
                      share.
                    </p> -->
                  </div>
                  <div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div
                      :key="user.email"
                      v-for="user in project.data.users"
                      class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"
                    >
                      <div class="flex-shrink-0">
                        <img
                          class="h-10 w-10 rounded-full"
                          :src="user.profile_photo_url"
                          alt=""
                        />
                      </div>
                      <div class="flex-1 min-w-0">
                        <a class="focus:outline-none">
                          <span class="absolute inset-0" aria-hidden="true"></span>
                          <p class="text-sm font-medium text-gray-900">
                            {{ user.first_name + " " + user.last_name }}
                          </p>
                          <!-- <p class="text-sm text-gray-500 truncate">Co-Founder / CEO</p> -->
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                  <div class="sm:col-span-6">
                    <h2 class="text-xl font-medium text-blue-gray-900">
                      Studies ({{ project.data.studies.length }})
                    </h2>
                    <p class="mt-1 text-sm text-blue-gray-500 mb-4">
                      This information will be displayed publicly so be careful what you
                      share.
                    </p>
                  </div>
                  <div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div
                      :key="study.id"
                      v-for="study in project.data.studies"
                      class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"
                    >
                      <div class="flex-shrink-0"></div>
                      <div class="flex-1 min-w-0">
                        <a class="focus:outline-none">
                          <span class="absolute inset-0" aria-hidden="true"></span>
                          <p class="text-md font-bold text-gray-900">
                            {{ study.name }}
                          </p>
                          <div class="-m-1 mt-2 flex flex-wrap items-center">
                            <a
                              :key="dataset.id"
                              v-for="dataset in study.datasets"
                              target="_blank"
                              :href="'/datasets/' + dataset.slug"
                              class="mr-1 inline-flex rounded-full border border-gray-200 items-center py-1.5 pl-3 pr-3 text-sm font-medium bg-white text-gray-900 hover:text-white hover:bg-black"
                              ><span>{{ dataset.name }}</span></a
                            >
                          </div>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-if="selectedTab == 'Files'"></div>
          <div v-if="selectedTab == 'License'">
            <div class="pb-10 mb-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
              <h3 class="text-xl font-extrabold text-blue-gray-900"> {{ project.data.license.title }}</h3>

              <div class="mt-6 space-y-8 divide-y divide-y-blue-gray-200">
                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                  <div class="sm:col-span-6">
                    <p class="mt-1 text-md text-blue-gray-500">
                      {{ project.data.license.description }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
  components: {
    AppLayout,
  },
  props: ["project"],
  data() {
    return {
      selectedTab: "Data",
      tabs: [
        {
          name: "Data",
          description: "",
          icon: "",
        },
        {
          name: "Files",
          description: "",
          icon: "",
        },
        {
          name: "License",
          description: "",
          icon: "",
        },
      ],
    };
  },
};
</script>
