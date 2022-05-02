<template>
  <app-layout :title="study.name">
    <template #header>
      <div class="lg:flex lg:items-center lg:justify-between">
        <div class="flex-1 min-w-0">
          <nav class="flex" aria-label="Breadcrumb">
            <ol role="list" class="flex items-center space-x-4">
              <li>
                <div class="flex">
                  <Link
                    :href="route('dashboard')"
                    class="text-sm font-medium text-gray-500 hover:text-gray-700"
                    >Dashboard</Link
                  >
                </div>
              </li>
              <li>
                <div class="flex items-center">
                  <ChevronRightIcon
                    class="flex-shrink-0 h-5 w-5 text-gray-400"
                    aria-hidden="true"
                  />
                  <Link
                    :href="route('project', [project.id])"
                    class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                    >{{ project.name }}</Link
                  >
                </div>
              </li>
            </ol>
          </nav>
          <h2
            class="mt-2 text-2xl font-bold break-words leading-7 text-gray-900 sm:text-3xl"
          >
            {{ study.name }}
          </h2>
          <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
              <span v-if="study.is_public" class="inline-flex items-center">
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
            </div>
            <div class="mt-2 flex items-center text-sm text-gray-500">
              <CalendarIcon
                class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
                aria-hidden="true"
              />
              Updated on {{ formatDateTime(study.updated_at) }}
            </div>
            <div class="mt-2 flex items-center text-sm text-gray-500">
              <a @click="toggleDetails" class="cursor-pointer inline-flex items-center"
                ><ExclamationCircleIcon
                              class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                              aria-hidden="true"
                            />
                <span class="ml-2">View details</span></a
              >
            </div>
            <study-details ref="studyDetailsElement" :study="study" />
          </div>
        </div>
      </div>
    </template>
    <div class="pb-12 pt-6 px-10">
      <study-content :study="study"></study-content>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import StudyDetails from "./Partials/Details.vue";
import StudyContent from "./Partials/Content.vue";
import { ref } from "vue";
import {
  BriefcaseIcon,
  CalendarIcon,
  CheckIcon,
  ChevronDownIcon,
  ChevronRightIcon,
  CurrencyDollarIcon,
  LinkIcon,
  LocationMarkerIcon,
  PencilIcon,
  ExclamationCircleIcon
} from "@heroicons/vue/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";


export default {
  components: {
    Link,
    AppLayout,
    StudyDetails,
    ExclamationCircleIcon,
    StudyContent,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    BriefcaseIcon,
    CalendarIcon,
    CheckIcon,
    ChevronDownIcon,
    ChevronRightIcon,
    CurrencyDollarIcon,
    LinkIcon,
    LocationMarkerIcon,
    PencilIcon,
  },
  props: ["study", "project"],
  data() {
    return {};
  },
  setup() {
    const studyDetailsElement = ref(null);
    return {
      studyDetailsElement,
    };
  },
  methods: {
    toggleDetails() {
      this.studyDetailsElement.toggleDetails();
    },
  },
};
</script>
