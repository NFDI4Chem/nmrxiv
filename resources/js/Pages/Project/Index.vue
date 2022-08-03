<template>
  <div>
    <div class="flex items-baseline justify-between">
      <div>
        <h2 class="text-lg">Projects</h2>
        <!-- <div class="mt-2 text-sm text-gray-700"> -->
        <!-- <div class="max-w-2xl">You may house a variety of projects.</div> -->
        <!-- </div> -->
      </div>
      <div class="flex-shrink-0 ml-4">
        <button
          id="v-step-1"
          v-if="mode == 'create' && editable"
          @click="openProjectCreateDialog"
          type="button"
          class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          New Project
        </button>
      </div>
    </div>
    <span v-if="projects.length <= 0">
      <div v-if="mode == 'create' && editable" class="mt-4">
        <div class="px-6 py-4 bg-white shadow-md rounded-lg">
          <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6">
              <path
                d="M3 6l9 4v12l-9-4V6zm14-3v2c0 1.1-2.24 2-5 2s-5-.9-5-2V3c0 1.1 2.24 2 5 2s5-.9 5-2z"
                class="fill-current text-gray-400"
              ></path>
              <polygon
                points="21 6 12 10 12 22 21 18"
                class="fill-current text-gray-600"
              ></polygon>
            </svg>
            <div
              class="ml-3 font-semibold text-sm text-gray-600 uppercase tracking-wider"
            >
              Create Your First Project
            </div>
          </div>
          <div class="mt-3 max-w-2xl text-sm text-gray-700">
            nmrXiv is organized around projects. Projects can contain as many studies as
            you wish and each study receives its very own URL. To learn more, check out
            our documentation.
          </div>
          <button
            @click="openProjectCreateDialog()"
            type="button"
            class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 mt-6 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              class="mx-auto h-12 w-12 text-gray-400"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              aria-hidden="true"
            >
              <path
                vector-effect="non-scaling-stroke"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
              />
            </svg>
            <span class="mt-2 block text-sm font-medium text-gray-900">
              Create a new project
            </span>
          </button>
        </div>
      </div>
      <div v-else>
        <slot name="emptyText"></slot>
      </div>
    </span>
    <span v-else>
      <div :key="project.uuid" v-for="project in projects" class="mt-8">
        <span v-if="project.draft_id">
          <Link
            :href="
              route('dashboard', { action: 'submission', draft_id: project.draft_id })
            "
            class="cursor-pointer"
          >
            <div
              class="flex justify-between items-center bg-white shadow-md rounded-lg px-6 py-6 hover:drop-shadow-xl cursor-pointer"
            >
              <div class="flex-grow">
                <div class="flex justify-between items-baseline">
                  <div class="font-bold text-lg text-gray-600">
                    <div class="flex items-center">
                      <span class="flex max-w-2xl break-words block">
                        {{ project.name }}
                        <span
                          v-if="project.draft_id != null"
                          class="ml-4 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800"
                        >
                          Draft
                        </span>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="flex justify-between items-baseline mt-3">
                  <span class="text-sm text-gray-600">
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
                    <div
                      v-if="
                        (team && team.id != project.team_id) ||
                        project.owner_id != $page.props.user.id
                      "
                      class="text-sm text-gray-600 pr-5"
                    >
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                      >
                        Shared by: {{ project.owner.first_name }}
                        {{ project.owner.last_name }}</span
                      >
                    </div>
                  </span>
                  <div class="grid grid-cols-1 pt-1">
                    <div class="text-sm text-gray-600 pr-5">
                      <span class="text-gray-400">Last updated on</span>
                      {{ formatDate(project.updated_at) }}
                    </div>
                    <div class="text-sm text-gray-600 pr-5">
                      <span class="text-gray-400">Created on</span>
                      {{ formatDate(project.created_at) }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="border-l">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  class="h-8 w-8 text-gray-600 fill-current ml-4"
                >
                  <path
                    d="M9.3 8.7a1 1 0 0 1 1.4-1.4l4 4a1 1 0 0 1 0 1.4l-4 4a1 1 0 0 1-1.4-1.4l3.29-3.3-3.3-3.3z"
                  ></path>
                </svg>
              </div>
            </div>
          </Link>
        </span>
        <span v-else>
          <Link :href="route('dashboard.projects', [project.id])" class="cursor-pointer">
            <div
              class="flex justify-between items-center bg-white shadow-md rounded-lg px-6 py-6 hover:drop-shadow-xl cursor-pointer"
            >
              <div class="flex-grow">
                <div class="flex justify-between items-baseline">
                  <div class="font-bold text-lg text-gray-600">
                    <div class="flex items-center">
                      <span class="flex max-w-2xl break-words block">
                        {{ project.name }}
                        <span
                          v-if="project.draft_id != null"
                          class="ml-4 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800"
                        >
                          Draft
                        </span>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="flex justify-between items-baseline mt-3">
                  <span class="text-sm text-gray-600">
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
                    <div
                      v-if="
                        (team && team.id != project.team_id) ||
                        project.owner_id != $page.props.user.id
                      "
                      class="text-sm text-gray-600 pr-5"
                    >
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                      >
                        Shared by: {{ project.owner.first_name }}
                        {{ project.owner.last_name }}</span
                      >
                    </div>
                  </span>
                  <div class="grid grid-cols-1 pt-1">
                    <div class="text-sm text-gray-600 pr-5">
                      <span class="text-gray-400">Last updated on</span>
                      {{ formatDate(project.updated_at) }}
                    </div>
                    <div class="text-sm text-gray-600 pr-5">
                      <span class="text-gray-400">Created on</span>
                      {{ formatDate(project.created_at) }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="border-l">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  class="h-8 w-8 text-gray-600 fill-current ml-4"
                >
                  <path
                    d="M9.3 8.7a1 1 0 0 1 1.4-1.4l4 4a1 1 0 0 1 0 1.4l-4 4a1 1 0 0 1-1.4-1.4l3.29-3.3-3.3-3.3z"
                  ></path>
                </svg>
              </div>
            </div>
          </Link>
        </span>
      </div>
    </span>
  </div>
</template>
<script>
import { Link } from "@inertiajs/inertia-vue3";

export default {
  components: {
    Link,
  },
  props: ["projects", "mode", "editable", "team"],
};
</script>
