<template>
  <app-layout :title="project.name">
    <template  #header>
      <div class="">
        <div
          class="flex pr-20 items-center text-sm text-gray-700 uppercase font-bold tracking-widest"
        >
          <StarIcon :class="[ project.starred ? 'text-yellow-400' : 'text-gray-200', 'h-5 w-5 flex-shrink-0 -ml-1 mr-1']" aria-hidden="true" /> {{ project.name }}
        </div>
        <div class="cursor-pointer inline-flex items-center mt-3">
          <span v-if="project.is_public" class="inline-flex items-center">
            <svg
              class="h-3 w-3 text-green-400 inline"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 64 64"
              width="512"
              height="512"
            >
              <g id="globe">
                <path
                  d="M53.85,47.85A27,27,0,0,1,24,57.8V56l3-3V49l4-4V42l4,4h5l2-2h8Z"
                />
                <path
                  d="M42,20.59v2.56L38.07,27H31l-5.36,5.26L31,37.51v5.06L27.44,39H22.86L16,32.11V24.2L11.8,20h-4A27,27,0,0,1,32,5a26.55,26.55,0,0,1,7.06.94L36,9H30v4l4,4h4.33Z"
                />
                <path
                  d="M32,60A28,28,0,1,1,60,32,28,28,0,0,1,32,60ZM32,6A26,26,0,1,0,58,32,26,26,0,0,0,32,6Z"
                />
              </g>
            </svg>
            <span class="ml-2">Public</span>
          </span>
          <span v-else class="inline-flex items-center">
            <svg
              class="h-3 w-3 text-gray-400 inline"
              version="1.1"
              id="Capa_1"
              xmlns="http://www.w3.org/2000/svg"
              xmlns:xlink="http://www.w3.org/1999/xlink"
              x="0px"
              y="0px"
              viewBox="0 0 512 512"
              style="enable-background: new 0 0 512 512"
              xml:space="preserve"
            >
              <g>
                <g>
                  <path
                    d="M437.333,192h-32v-42.667C405.333,66.99,338.344,0,256,0S106.667,66.99,106.667,149.333V192h-32
                                C68.771,192,64,196.771,64,202.667v266.667C64,492.865,83.135,512,106.667,512h298.667C428.865,512,448,492.865,448,469.333
                                V202.667C448,196.771,443.229,192,437.333,192z M287.938,414.823c0.333,3.01-0.635,6.031-2.656,8.292
                                c-2.021,2.26-4.917,3.552-7.948,3.552h-42.667c-3.031,0-5.927-1.292-7.948-3.552c-2.021-2.26-2.99-5.281-2.656-8.292l6.729-60.51
                                c-10.927-7.948-17.458-20.521-17.458-34.313c0-23.531,19.135-42.667,42.667-42.667s42.667,19.135,42.667,42.667
                                c0,13.792-6.531,26.365-17.458,34.313L287.938,414.823z M341.333,192H170.667v-42.667C170.667,102.281,208.948,64,256,64
                                s85.333,38.281,85.333,85.333V192z"
                  />
                </g>
              </g>
              <g></g>
              <g></g>
              <g></g>
              <g></g>
              <g></g>
              <g></g>
              <g></g>
              <g></g>
              <g></g>
              <g></g>
              <g></g>
              <g></g>
              <g></g>
              <g></g>
              <g></g>
            </svg>
            <span class="ml-2">Private</span>
          </span>
          <a @click="toggleDetails" class="cursor-pointer inline-flex items-center ml-7"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4"><path d="M4 15a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h7a1 1 0 0 1 .7.3L13.42 5H21a1 1 0 0 1 .9 1.45L19.61 11l2.27 4.55A1 1 0 0 1 21 17h-8a1 1 0 0 1-.7-.3L10.58 15H4z" class="fill-current text-gray-400"></path> <rect width="2" height="20" x="2" y="2" rx="1" class="fill-current text-gray-600"></rect></svg> <span class="ml-2">View details</span></a>
          <project-details ref="projectDetailsElement" :project="project" />
        </div>
      </div>
      <div class="flex-nowrap">
        <Link
          :href="route('project.settings', project.id)"
          class="text-sm flex-nowrap text-gray-800 font-bold"
        >
          Project&nbsp;Settings
        </Link>
      </div>
    </template>
    <div class="py-12 px-10">
      <div>
        <study-index :project="project" :studies="studies" />
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import StudyIndex from "@/Pages/Study/Index.vue";
import ProjectDetails from './Partials/Details.vue';
import { ref } from 'vue'
import { StarIcon } from "@heroicons/vue/solid";

export default {
  components: {
    Link,
    AppLayout,
    StudyIndex,
    ProjectDetails,
    StarIcon
  },
  props: ["project", "studies"],
  data() {
    return {
    };
  },
  setup() {
    const projectDetailsElement = ref(null)
    return {
      projectDetailsElement
    }
  },
  methods: {
    toggleDetails(){
      this.projectDetailsElement.toggleDetails()
    }
  },
};
</script>
