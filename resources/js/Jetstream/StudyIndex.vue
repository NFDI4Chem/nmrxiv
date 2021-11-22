<template>
  <div>
    <div class="px-12 py-8 mx-auto max-w-4xl">
      <div>
        <div class="flex items-baseline justify-between">
          <div>
            <h2 class="text-lg">Studies</h2>
            <div class="mt-2 text-sm text-gray-700">
              <div class="max-w-2xl">
                Each project may house a variety of studies. Studies can be versioned,
                command invocations, and metrics.
              </div>
            </div>
          </div>
          <div class="flex-shrink-0 ml-4">
            <button
              @click="createStudyDialog = true"
              type="button"
              class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              New Study
            </button>
          </div>
        </div>
        <span v-if="studies.length <= 0">
          <div class="mt-4">
            <div class="px-6 py-4 bg-white shadow-md rounded-lg">
              <div class="flex items-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  class="h-6 w-6"
                >
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
                nmrXiv is organized around studies. A project can contain as many studies
                as you wish and each study receives its very own URL. To learn more, check
                out our documentation.
              </div>
                <button @click="createStudyDialog = true" type="button" class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 mt-6 focus:ring-offset-2 focus:ring-indigo-500">
                 <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
      <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
    </svg>
                  <span class="mt-2 block text-sm font-medium text-gray-900">
                    Create a new study
                  </span>
                </button>
            </div>
          </div>
        </span>
        <span v-else>
          <div :key="project.uuid" v-for="project in studies" class="mt-8">
            <Link :href="route('project', [project.id])">
            <div class="flex justify-between items-center bg-white shadow-md rounded-lg px-6 py-6 hover:drop-shadow-xl cursor-pointer">
              <div class="flex-grow">
                <div class="flex justify-between items-baseline">
                  <div class="font-semibold text-md text-gray-600 uppercase tracking-wider">
                    <div class="flex items-center">
                      <span class="bg-green-400 flex-shrink-0 inline-block h-2 w-2 rounded-full"></span>
                      <span class="ml-3 block truncate">
                        {{ project.name }}
                      </span>
                    </div>
                  </div>
                  <span class="text-sm text-gray-600"> <span class="text-gray-400">Created on</span> {{ formatDate(project.created_at) }} </span>
                </div>
                <div class="flex justify-between items-baseline mt-2">
                  <span class="text-sm text-gray-600"
                    >{{ project.description }}</span
                  >
                </div>
              </div>
              <div>
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
          </div>
        </span>
      </div>
      <!---->
      <jet-dialog-modal :show="createStudyDialog" @close="createStudyDialog = false">
        <template #title> New Study </template>

        <template #content>
          <div>
            <div class="relative z-0 mt-1 rounded-lg cursor-pointer">
              <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-6">
                  <label for="name" class="block text-sm font-medium text-gray-700">
                    Name
                  </label>
                  <div class="mt-1 flex rounded-md shadow-sm">
                    <input
                      v-model="createStudyForm.name"
                      type="text"
                      name="name"
                      id="name"
                      autocomplete="name"
                      class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                    />
                  </div>
                </div>

                <div class="sm:col-span-6">
                  <label
                    for="description"
                    class="block text-sm font-medium text-gray-700"
                  >
                    Description
                  </label>
                  <div class="mt-1">
                    <textarea
                      v-model="createStudyForm.description"
                      id="description"
                      name="description"
                      rows="3"
                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                    />
                  </div>
                  <p class="mt-2 text-sm text-gray-500">
                    Write a few sentences about the project.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </template>

        <template #footer>
          <jet-secondary-button @click="createStudyDialog = false">
            Cancel
          </jet-secondary-button>

          <jet-button
            class="ml-2"
            @click="createStudy"
            :class="{ 'opacity-25': createStudyForm.processing }"
            :disabled="createStudyForm.processing"
          >
            Save
          </jet-button>
        </template>
      </jet-dialog-modal>
    </div>
  </div>
</template>

<script>
import JetApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import { CheckCircleIcon, ChevronRightIcon } from "@heroicons/vue/solid";
import { Link } from "@inertiajs/inertia-vue3";

export default {
  components: {
    JetApplicationLogo,
    JetDialogModal,
    JetSecondaryButton,
    JetButton,
    Link,
    CheckCircleIcon,
    ChevronRightIcon,
  },
  props: ["studies"],
  data() {
    return {
      createStudyForm: this.$inertia.form({
        _method: "POST",
        name: "",
        description: "",
        error_message: null,
        team_id: null,
        owner_id: null,
      }),
      createStudyDialog: false,
    };
  },
  methods: {
    createStudy() {
      this.createStudyForm.owner_id = this.$page.props.user.id;
      this.createStudyForm.team_id = this.$page.props.team.id;
      this.createStudyForm.post(route("studies.create"), {
        preserveScroll: true,
        onSuccess: () => {
          this.createStudyDialog = false;
          this.createStudyForm.reset();
        },
        onError: (err) => console.error(err),
      });
    },
  },
};
</script>
