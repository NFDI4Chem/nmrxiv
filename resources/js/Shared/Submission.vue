<template>
  <jet-dialog-modal
    :max-width="'7xl'"
    :show="createDatasetDialog"
    @close="createDatasetDialog = false"
  >
    <template #title>
      <!-- <nav class="flex items-center justify-center" aria-label="Progress">
        <p class="text-sm font-medium">
          Step {{ steps.findIndex((step) => step.status === "current") + 1 }} of
          {{ steps.length }}
        </p>
        <ol role="list" class="ml-8 flex items-center space-x-5">
          <li v-for="step in steps" :key="step.name">
            <a
              v-if="step.status === 'complete'"
              :href="step.href"
              class="block w-2.5 h-2.5 bg-teal-600 rounded-full hover:bg-teal-900"
            >
              <span class="sr-only">{{ step.name }}</span>
            </a>
            <a
              v-else-if="step.status === 'current'"
              :href="step.href"
              class="relative flex items-center justify-center"
              aria-current="step"
            >
              <span class="absolute w-5 h-5 p-px flex" aria-hidden="true">
                <span class="w-full h-full rounded-full bg-teal-200" />
              </span>
              <span
                class="relative block w-2.5 h-2.5 bg-teal-600 rounded-full"
                aria-hidden="true"
              />
              <span class="sr-only">{{ step.name }}</span>
            </a>
            <a
              v-else
              :href="step.href"
              class="block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400"
            >
              <span class="sr-only">{{ step.name }}</span>
            </a>
          </li>
        </ol>
      </nav> -->
      <div class="lg:border-t lg:border-b lg:border-gray-200">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-label="Progress">
          <ol
            role="list"
            class="rounded-md overflow-hidden lg:flex lg:border-l lg:border-r lg:border-gray-200 lg:rounded-none"
          >
            <li
              v-for="(step, stepIdx) in steps"
              :key="step.id"
              class="relative overflow-hidden lg:flex-1"
            >
              <div
                :class="[
                  stepIdx === 0 ? 'border-b-0 rounded-t-md' : '',
                  stepIdx === steps.length - 1 ? 'border-t-0 rounded-b-md' : '',
                  'border border-gray-200 overflow-hidden lg:border-0',
                ]"
              >
                <a v-if="step.status === 'complete'" :href="step.href" class="group">
                  <span
                    class="absolute top-0 left-0 w-1 h-full bg-transparent group-hover:bg-gray-200 lg:w-full lg:h-1 lg:bottom-0 lg:top-auto"
                    aria-hidden="true"
                  />
                  <span
                    :class="[
                      stepIdx !== 0 ? 'lg:pl-9' : '',
                      'px-6 py-5 flex items-start text-sm font-medium',
                    ]"
                  >
                    <span class="flex-shrink-0">
                      <span
                        class="w-10 h-10 flex items-center justify-center bg-teal-600 rounded-full"
                      >
                        <CheckIcon class="w-6 h-6 text-white" aria-hidden="true" />
                      </span>
                    </span>
                    <span class="mt-0.5 ml-4 min-w-0 flex flex-col">
                      <span class="text-xs font-semibold tracking-wide uppercase">{{
                        step.name
                      }}</span>
                      <span class="text-sm font-medium text-gray-500">{{
                        step.description
                      }}</span>
                    </span>
                  </span>
                </a>
                <a
                  v-else-if="step.status === 'current'"
                  :href="step.href"
                  aria-current="step"
                >
                  <span
                    class="absolute top-0 left-0 w-1 h-full bg-teal-600 lg:w-full lg:h-1 lg:bottom-0 lg:top-auto"
                    aria-hidden="true"
                  />
                  <span
                    :class="[
                      stepIdx !== 0 ? 'lg:pl-9' : '',
                      'px-6 py-5 flex items-start text-sm font-medium',
                    ]"
                  >
                    <span class="flex-shrink-0">
                      <span
                        class="w-10 h-10 flex items-center justify-center border-2 border-teal-600 rounded-full"
                      >
                        <span class="text-teal-600">{{ step.id }}</span>
                      </span>
                    </span>
                    <span class="mt-0.5 ml-4 min-w-0 flex flex-col">
                      <span
                        class="text-xs font-semibold text-teal-600 tracking-wide uppercase"
                        >{{ step.name }}</span
                      >
                      <span class="text-sm font-medium text-gray-500">{{
                        step.description
                      }}</span>
                    </span>
                  </span>
                </a>
                <a v-else :href="step.href" class="group">
                  <span
                    class="absolute top-0 left-0 w-1 h-full bg-transparent group-hover:bg-gray-200 lg:w-full lg:h-1 lg:bottom-0 lg:top-auto"
                    aria-hidden="true"
                  />
                  <span
                    :class="[
                      stepIdx !== 0 ? 'lg:pl-9' : '',
                      'px-6 py-5 flex items-start text-sm font-medium',
                    ]"
                  >
                    <span class="flex-shrink-0">
                      <span
                        class="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-full"
                      >
                        <span class="text-gray-500">{{ step.id }}</span>
                      </span>
                    </span>
                    <span class="mt-0.5 ml-4 min-w-0 flex flex-col">
                      <span
                        class="text-xs font-semibold text-gray-500 tracking-wide uppercase"
                        >{{ step.name }}</span
                      >
                      <span class="text-sm font-medium text-gray-500">{{
                        step.description
                      }}</span>
                    </span>
                  </span>
                </a>
                <template v-if="stepIdx !== 0">
                  <!-- Separator -->
                  <div
                    class="hidden absolute top-0 left-0 w-3 inset-0 lg:block"
                    aria-hidden="true"
                  >
                    <svg
                      class="h-full w-full text-gray-300"
                      viewBox="0 0 12 82"
                      fill="none"
                      preserveAspectRatio="none"
                    >
                      <path
                        d="M0.5 0V31L10.5 41L0.5 51V82"
                        stroke="currentcolor"
                        vector-effect="non-scaling-stroke"
                      />
                    </svg>
                  </div>
                </template>
              </div>
            </li>
          </ol>
        </nav>
      </div>
    </template>

    <template #content>
      <div class="max-h-screen">
        <form class="dropzone py-2 mb-3">
          <div id="submission-dropzon-message" class="text-center">
            <div
              type="button"
              class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
            >
              <svg
                class="mx-auto h-12 w-12 text-gray-400"
                xmlns="http://www.w3.org/2000/svg"
                stroke="currentColor"
                fill="none"
                viewBox="0 0 48 48"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m0-4c0 4.418-7.163 8-16 8S8 28.418 8 24m32 10v6m0 0v6m0-6h6m-6 0h-6"
                />
              </svg>
              <span class="mt-2 block text-sm font-medium text-gray-900">
                Drop Files or Folders to upload to
                <span v-if="$page.props.selectedFolder"
                  >"{{ $page.props.selectedFolder }}"</span
                >
                folder
              </span>

              <div v-if="progress > 0" class="relative mt-5">
                <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                  <div
                    :style="'width: ' + progress + '%'"
                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"
                  ></div>
                </div>
              </div>
              <div v-if="progress > 0">
                <span class="mt-2 block text-sm font-medium text-gray-900">
                  {{ status }} ({{ progress }}%)
                </span>
              </div>
            </div>
          </div>
        </form>
      </div>
    </template>

    <!-- <template #footer>
      <jet-secondary-button @click="toggleCreateDatasetDialog">
        Cancel
      </jet-secondary-button>

      <jet-button
        class="ml-2"
        @click="createDataset"
        :class="{ 'opacity-25': createDatasetForm.processing }"
        :disabled="createDatasetForm.processing"
      >
        Save
      </jet-button>
    </template> -->
  </jet-dialog-modal>
</template>

<script>
import { Dropzone } from "dropzone";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import { CheckCircleIcon, ChevronRightIcon } from "@heroicons/vue/solid";
import { Link } from "@inertiajs/inertia-vue3";
import { Tab, TabGroup, TabList, TabPanel, TabPanels } from "@headlessui/vue";
import { AtSymbolIcon, CodeIcon, LinkIcon } from "@heroicons/vue/solid";
import JetInputError from "@/Jetstream/InputError.vue";
import { ref } from "vue";
import { Switch, SwitchDescription, SwitchGroup, SwitchLabel } from "@headlessui/vue";
import { inject } from "vue";
import { CheckIcon } from '@heroicons/vue/solid'
export default {
  components: {
    CheckIcon,
    Tab,
    TabGroup,
    TabList,
    TabPanel,
    TabPanels,
    Switch,
    SwitchDescription,
    SwitchGroup,
    SwitchLabel,
    AtSymbolIcon,
    CodeIcon,
    LinkIcon,
    JetDialogModal,
    JetSecondaryButton,
    JetButton,
    Link,
    CheckCircleIcon,
    ChevronRightIcon,
    JetInputError,
  },

  mounted() {
    const emitter = inject("emitter");
    emitter.on("openDatasetCreateDialog", () => {
      this.createDatasetDialog = true;
    });
  },

  onUpdated() {
    const vm = this;
    vm.$page.props.selectedFileSystemObject = vm.file;
    vm.$page.props.selectedFolder = "/";
    let options = {
      url: "/",
      method: "put",
      sending(file, xhr) {
        let _send = xhr.send;
        xhr.send = () => {
          _send.call(xhr, file);
        };
      },
      autoProcessQueue: false,
      uploadMultiple: false,
      disablePreviews: true,
      parallelUploads: 1,
      maxFiles: 10000,
      dictDefaultMessage: document.querySelector("#submission-dropzon-message").innerHTML,
      done() {},
      accept(file, done) {
        const url = "/dashboard/storage/signed-storage-url";

        const client = axios.create({ baseURL: window.location.origin });
        axiosRetry(client, {
          retries: 3,
          retryCondition: (error) => {
            return error.response.status === 500;
          },
        });

        client
          .post(url, {
            file: file,
            destination: vm.$page.props.selectedFolder,
            project_id: vm.project.id,
            study_id: vm.study.id,
          })
          .catch((err) => {
            // The first request fails
            if (err.response.status !== 200 || err.response.status !== 201) {
              throw new Error(
                `API call failed with status code: ${err.response.status} after multiple attempts`
              );
            }
          })
          .then(function (response) {
            let data = response.data;
            let headers = data.headers;
            if ("Host" in headers) {
              delete headers.Host;
            }
            file.uploadURL = data.url;
            setTimeout(() => vm.dropzone.processFile(file));
            done();
          });
      },
      totaluploadprogress: function (progress) {
        vm.progress = Math.ceil(progress);
      },
      queuecomplete: function () {
        vm.status = "UPLOAD COMPLETE";
        Inertia.reload();
        this.$page.props.selectedFileSystemObject = this.files[0];
      },
    };
    this.dropzone = new Dropzone(this.$el, options);

    vm.dropzone.on("processing", (file) => {
      vm.status = "UPLOAD IN PROGRESS";
      vm.dropzone.options.url = file.uploadURL;
    });
  },

  data() {
    return {
      createDatasetForm: this.$inertia.form({
        _method: "POST",
        name: "",
        description: "",
        error_message: null,
        team_id: null,
        owner_id: null,
        color: null,
        starred: null,
        project_id: this.project ? this.project.id : null,
        is_public: ref(false),
      }),
      steps: [
        {
          id: "01",
          name: "Files Upload",
          description: "Vitae sed mi luctus laoreet.",
          href: "#",
          status: "current",
        },
        {
          id: "02",
          name: "Assignments",
          description: "Cursus semper viverra.",
          href: "#",
          status: "upcoming",
        },
        {
          id: "03",
          name: "Meta data",
          description: "Penatibus eu quis ante.",
          href: "#",
          status: "upcoming",
        },
        {
          id: "04",
          name: "Complete",
          description: "Penatibus eu quis ante.",
          href: "#",
          status: "upcoming",
        },
      ],
      createDatasetDialog: false,
    };
  },

  props: ["project"],

  methods: {
    createDataset() {
      this.createDatasetForm.owner_id = this.$page.props.user.id;
      this.createDatasetForm.team_id = this.$page.props.user.current_team.id;
      this.createDatasetForm.project_id = this.project.id;
      this.createDatasetForm.post(route("dashboard.dataset.create"), {
        preserveScroll: true,
        onSuccess: () => {
          this.createDatasetDialog = false;
          this.createDatasetForm.reset();
        },
        onError: (err) => console.error(err),
      });
    },
    toggleCreateDatasetDialog() {
      this.createDatasetDialog = !this.createDatasetDialog;
      this.createDatasetForm.clearErrors();
    },
  },
};
</script>
