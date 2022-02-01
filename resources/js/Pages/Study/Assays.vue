<template>
  <div>
    <study-content :project="project" :study="study" current="Assays">
      <template #study-section>
        <div class="divide-y divide-gray-200 sm:col-span-9">
          <!-- Profile section -->
          <div class="py-6 px-4 sm:p-6 lg:pb-8">
            <div>
              <h2 class="text-lg leading-6 font-medium text-gray-900">Assays</h2>
              <p class="mt-1 text-sm text-gray-500">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod.
              </p>
            </div>
          </div>
        </div>
      </template>
    </study-content>
  </div>
</template>

<script>
import StudyContent from "@/Pages/Study/Content.vue";
export default {
  props: ["study", "project"],
  components: {
    StudyContent,
  },
  setup() {
  },
  mounted() {},
};
</script>
