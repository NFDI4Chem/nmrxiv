<template>
  <div>
    <study-content
      model="study"
      :project="project"
      :study="study"
      :team="team"
      :members="members"
      :availableRoles="availableRoles"
      :studyPermissions="studyPermissions"
      :studyRole="studyRole"
      current="About"
    >
      <template #study-section>
        <div class="divide-y divide-gray-200 sm:col-span-9">
          <div class="py-3 px-4 sm:p-6 lg:pb-8">
            <div class="mt-0">
              <div class="mb-4">
                <div class="relative">
                  <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                  </div>
                  <div class="relative flex items-center justify-between">
                    <span class="pr-3 text-lg bg-white font-medium text-gray-500">
                      Description
                    </span>
                    <button
                      type="button"
                      @click="openStudyDetailsPane"
                      class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                        class="w-4 h-4 mr-2 text-gray-600"
                      >
                        <path
                          d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                        ></path>
                      </svg>
                      <span>Edit</span>
                    </button>
                  </div>
                </div>
                <dd class="mt-1 text-md text-gray-900 space-y-5">
                  <p>
                    {{ study.description }}
                  </p>
                </dd>
              </div>
              <div class="mb-4">
                <div class="relative">
                  <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                  </div>
                  <div class="relative flex items-center justify-between">
                    <span class="pr-3 text-lg bg-white font-medium text-gray-500">
                      Tags
                    </span>
                    <button
                      type="button"
                      @click="openStudyDetailsPane"
                      class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                        class="w-4 h-4 mr-2 text-gray-600"
                      >
                        <path
                          d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                        ></path>
                      </svg>
                      <span>Edit</span>
                    </button>
                  </div>
                </div>
                <dd class="mt-1 text-md text-gray-900 space-y-5">
                  <p>
                    <span class="mr-2" v-for="tag in study.tags" :key="tag.id">
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800"
                      >
                        <svg
                          class="-ml-0.5 mr-1.5 h-2 w-2 text-indigo-400"
                          fill="currentColor"
                          viewBox="0 0 8 8"
                        >
                          <circle cx="4" cy="4" r="3" />
                        </svg>
                        {{ tag.name["en"] }}
                      </span>
                    </span>
                  </p>
                </dd>
              </div>
              
            </div>
          </div>
        </div>
      </template>
    </study-content>
  </div>
</template>

<script>
import { PlusSmIcon } from "@heroicons/vue/solid";
import StudyContent from "@/Pages/Study/Content.vue";
import { inject } from "vue";
export default {
  props: [
    "study",
    "project",
    "team",
    "members",
    "availableRoles",
    "studyPermissions",
    "studyRole",
  ],
  components: {
    StudyContent,
    PlusSmIcon,
  },
  setup() {
    const emitter = inject("emitter");
    const openStudyDetailsPane = () => {
      emitter.emit("openStudyDetails", {});
    };
    return {
      openStudyDetailsPane,
    };
  },
  mounted() {},
};
</script>
