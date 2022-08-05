<template>
  <app-layout :title="project.name">
    <template v-if="project" #header>
      <div class="bg-white border-b">
        <div class="border-b">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
            <img
              v-if="project && project.data.owner"
              class="inline h-7 w-7 rounded-full"
              :src="project.data.owner.profile_photo_url"
            />
            <p class="inline ml-3 text-xs font-bold text-gray-500 uppercase">
              <a class="text-gray-900">{{
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
              <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
              <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                  <a
                    @click="selectTab(tab)"
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
          <div class="bg-white">
            <div v-if="selectedTab == 'Data'">
              <div class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
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
                      <h2 class="text-xl font-medium text-blue-gray-900">Submitter(s)</h2>
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
                      <h2 class="text-xl font-medium text-blue-gray-900">Author(s)</h2>
                      <!-- <p class="mt-1 text-sm text-blue-gray-500">
                      This information will be displayed publicly so be careful what you
                      share.
                    </p> -->
                    </div>
                    <div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2">
                      <div
                        :key="author.id"
                        v-for="author in project.data.authors"
                        class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"
                      >
                        <div class="flex-1 min-w-0">
                          <a class="focus:outline-none">
                            <span class="absolute inset-0" aria-hidden="true"></span>
                            <p v-if="author.title" class="text-sm font-medium text-gray-900">
                              {{ author.title + " " + author.family_name + " " + author.given_name }}
                            </p>
                            <p v-else class="text-sm font-medium text-gray-900">
                              {{ author.family_name + " " + author.given_name }}
                            </p>
                            <p class="text-sm text-gray-500">{{ author.affiliation }}
                              <br>
                            <a :href="author.orcid_id" v-if="author.orcid_id" class="text-teal-500">ORCID ID - {{author.orcid_id}}</a>
                            </p>
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
                      <!-- <p class="mt-1 text-sm text-blue-gray-500 mb-4">
                        This information will be displayed publicly so be careful what you
                        share.
                      </p> -->
                    </div>
                    <div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2">
                       <div
                        :key="study.uuid"
                        v-for="study in project.data.studies.sort((a, b) => (a.name > b.name ? 1 : -1))"
                      >
                        <study-card :study="study" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="selectedTab == 'Files'">
              <div
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
                v-if="project.data.files"
              >
                <nav
                  v-if="$page.props.selectedFolder"
                  class="flex p-3"
                  aria-label="Breadcrumb"
                >
                  <ol role="list" class="flex items-center space-x-2">
                    <li>
                      <div>
                        <a class="text-gray-400 hover:text-gray-900">
                          <HomeIcon class="flex-shrink-0 h-5 w-5" aria-hidden="true" />
                          <span class="sr-only">Home</span>
                        </a>
                      </div>
                    </li>
                    <li
                      v-for="page in $page.props.selectedFolder.split('/')"
                      :key="page.name"
                    >
                      <div v-if="page != ''" class="flex items-center">
                        <ChevronRightIcon
                          class="flex-shrink-0 h-5 w-5 text-gray-400"
                          aria-hidden="true"
                        />
                        <a
                          class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                          :aria-current="page ? 'page' : undefined"
                          >{{ page }}</a
                        >
                      </div>
                    </li>
                  </ol>
                </nav>
                <div class="min-w-0 flex-1 min-h-screen border-t border-gray-200 lg:flex">
                  <aside
                    class="hidden py-3 px-2 lg:block lg:flex-shrink-0 lg:order-first"
                  >
                    <div
                      v-if="project.data.files != null"
                      class="h-full relative flex flex-col w-64 border-r border-gray-200 overflow-y-auto"
                    >
                      <children :file="project.data.files" :project="project"></children>
                    </div>
                  </aside>
                  <section
                    class="min-w-0 p-6 flex-1 h-full flex flex-col overflow-y-auto lg:order-last"
                  >
                    <div
                      class="mb-3"
                      v-if="
                        $page.props.selectedFileSystemObject &&
                        $page.props.selectedFileSystemObject.has_children
                      "
                    >
                      <ul
                        role="list"
                        class="mb-3 grid grid-cols-1 gap-5 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4"
                      >
                        <li
                          v-for="file in $page.props.selectedFileSystemObject.children"
                          :key="file.key"
                          class="relative shadow rounded-lg"
                        >
                          <div
                            class="group block w-full aspect-w-10 aspect-h-7 py-4 bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-indigo-500 overflow-hidden"
                          >
                            <span v-if="file.type == 'directory'">
                              <FolderIcon
                                @dblclick.stop="displaySelected(file)"
                                class="cursor-pointer h-28 w-28 text-gray-400 flex-shrink-0 mx-auto"
                                aria-hidden="true"
                              />
                            </span>
                            <span v-else>
                              <DocumentTextIcon
                                class="h-28 w-28 text-gray-400 flex-shrink-0 mx-auto"
                                aria-hidden="true"
                              />
                            </span>
                          </div>
                          <p
                            class="mt-2 px-2 py-1 block text-sm font-medium text-gray-900 pointer-events-none"
                          >
                            {{ file.name }}
                          </p>
                          <p
                            class="block text-sm font-medium text-gray-500 pointer-events-none"
                          >
                            {{ file.size }}
                          </p>
                        </li>
                      </ul>
                    </div>
                    <div v-else>
                      <div class="">
                        <span
                          v-if="
                            $page.props.selectedFileSystemObject &&
                            $page.props.selectedFileSystemObject.type == 'file'
                          "
                        >
                          <File-details
                            :project="project.data"
                            :file="$page.props.selectedFileSystemObject"
                          ></File-details>
                        </span>
                      </div>
                    </div>
                  </section>
                </div>
              </div>
            </div>
            <div v-if="selectedTab == 'License'">
              <div class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <h3 class="text-xl font-extrabold text-blue-gray-900">
                  {{ project.data.license.title }}
                </h3>

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
        </div>
      </main>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import FileDetails from "@/Shared/FileDetails.vue";
import {
  FolderIcon,
  DocumentTextIcon,
  ChevronRightIcon,
  HomeIcon,
} from "@heroicons/vue/solid";
import StudyCard from "@/Shared/StudyCard.vue";

export default {
  components: {
    AppLayout,
    FileDetails,
    FolderIcon,
    DocumentTextIcon,
    ChevronRightIcon,
    HomeIcon,
    StudyCard
  },
  props: ["project"],
  data() {
    return {
      selectedTab: "Data",
      selectedStudy: null,
      selectedDataset: null,

      selectedSpectraData: null,

      currentMolecules: [],

      studyTabs: [
        { name: "Experiments (Spectra)", current: true },
        { name: "Chemical Composition", current: false },
        { name: "Meta Data", current: false },
      ],

      currentTab: "",
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
  mounted() {
    if (this.project) {
      this.$page.props.selectedFileSystemObject = this.project.data.files;
    }
  },
  methods: {
    selectTab(tab) {
      this.selectedTab = tab.name;
      
    },
  },
  computed: {
    url() {
      return String(this.$page.props.url);
    },
  },
};
</script>
