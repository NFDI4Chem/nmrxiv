<template>
  <div>
    <div class="flex items-baseline justify-between">
      <div>
        <h2 class="text-lg">Studies</h2>
        <div class="mt-2 text-sm text-gray-700">
          <div class="max-w-2xl text-m">
            Each project may house a variety of studies. Studies can be versioned, command
            invocations, and metrics.
          </div>
        </div>
      </div>
      <div class="flex-shrink-0 ml-4">
        <button
          v-if="editable"
          @click="openStudyCreateDialog()"
          type="button"
          class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          New Study
        </button>
      </div>
    </div>
    <div v-if="studies.length <= 0 && editable">
      <div class="mt-4 px-12 py-8 mx-auto max-w-4xl">
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
              Create Your First Study
            </div>
          </div>
          <div class="mt-3 max-w-2xl text-sm text-gray-700">
            nmrXiv is organized around studies. A project can contain as many studies as
            you wish and each study receives its very own URL. To learn more, check out
            our documentation.
          </div>
          <button
            @click="openStudyCreateDialog()"
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
              Create a new study
            </span>
          </button>
        </div>
      </div>
    </div>
    <div v-else
        class="mt-12 mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-3 lg:max-w-7xl"
      >
      <div :key="study.uuid" v-for="study in studies">
        <Link :href="route('dashboard.studies', [study.id])">
          <study-card :study="study" />
        </Link>
      </div>
    </div>
    <study-create :project="project"></study-create>
  </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import StudyCreate from "@/Pages/Study/Partials/Create.vue";
import StudyCard from "@/Shared/StudyCard.vue";
import JetButton from "@/Jetstream/Button.vue";
import { inject } from 'vue';
export default {
  components: {
    Link,
    StudyCreate,
    StudyCard,
    JetButton,
  },
  setup() {
    const emitter = inject('emitter'); // Inject `emitter`
    const openStudyCreateDialog = () => {
      emitter.emit('openStudyCreateDialog', 100);
    };
    return {
      openStudyCreateDialog
    }
  },
  props: ["studies", "project", "editable"],
  data() {
    return {};
  },
  methods: {
  },
};
</script>
