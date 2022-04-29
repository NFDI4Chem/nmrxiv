<template>
  <div>
    <study-content :project="project" :study="study" current="About">
      <template #study-section>
        <div class="divide-y divide-gray-200 sm:col-span-9">
          <!-- Profile section -->
          <div class="py-3 px-4 sm:p-6 lg:pb-8">
            <div class="mt-0">
              <div>
                <div class="relative">
                  <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300" />
                  </div>
                  <div class="relative flex items-center justify-between">
                    <span class="pr-3 bg-white text-lg font-medium text-gray-900">
                      <b>Description</b>
                    </span>
                    <button
                      type="button"
                      class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      <PlusSmIcon
                        class="-ml-1.5 mr-1 h-5 w-5 text-gray-400"
                        aria-hidden="true"
                      />
                      <span>Edit</span>
                    </button>
                  </div>
                </div>
                <p
                  v-html="md(study.description)"
                  class="mt-3 text-gray-600"
                ></p>
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
export default {
  props: ["study", "project"],
  components: {
    StudyContent,
    PlusSmIcon,
  },
  setup() {},
  mounted() {},
};
</script>
