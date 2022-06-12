<template>
  <jet-dialog-modal
    :max-width="'7xl'"
    :show="createDatasetDialog"
    @close="createDatasetDialog = false"
  >
    <template #title>
      <div
        v-if="!loading && currentDraft"
        class="lg:border-t lg:border-b lg:border-gray-200"
      >
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
      <div v-if="!loading" class="max-h-screen">
        <div v-if="drafts.length > 0 && !currentDraft">
          <label class="block text-sm font-medium text-gray-900"><b>Drafts</b></label>
          <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="-my-5 divide-y divide-gray-200 p-4">
              <li v-for="draft in drafts" :key="draft.id" class="py-4">
                <div class="flex items-center space-x-4">
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                      {{ draft.name }}
                    </p>
                    <p class="text-sm text-gray-500 truncate">
                      {{ draft.created_at }}
                    </p>
                  </div>
                  <div>
                    <a
                      @click="selectDraft(draft)"
                      class="cursor-pointer inline-flex items-center shadow-sm px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50"
                    >
                      Select
                    </a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div class="mt-6">
            <a
              @click="createNewDraft()"
              class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Create New
            </a>
          </div>
        </div>
        <div v-else>
          <div v-if="currentStep.id == '01'">
            <div v-if="currentDraft">
              <small @click="annotate()">Draft ID: {{ currentDraft.name }}</small>
              <form id="submission-dropzone" class="py-2 mb-3">
                <div id="submission-dropzone-message" class="text-center">
                  <div
                    type="button"
                    class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-4 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
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
              <div v-if="file != null" style="height: 40vh; overflow: scroll !important">
                <children :file="file"></children>
              </div>
            </div>
          </div>
          <div v-if="currentStep.id == '02'">
            <div style="height: 80vh; overflow: scroll !important">
              <div class="mb-4 bg-white flex items-start justify-center">
                <div class="relative z-0 inline-flex shadow-sm rounded-md">
                  <button
                    type="button"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                    <span class="sr-only">Previous</span>
                  </button>
                  <Menu as="div" class="-ml-px relative block">
                    <MenuButton
                      class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                      <b>STUDY: {{ selectedStudy.name }}</b>
                      <ChevronDownIcon class="h-5 w-5" aria-hidden="true" />
                    </MenuButton>
                    <transition
                      enter-active-class="transition ease-out duration-100"
                      enter-from-class="transform opacity-0 scale-95"
                      enter-to-class="transform opacity-100 scale-100"
                      leave-active-class="transition ease-in duration-75"
                      leave-from-class="transform opacity-100 scale-100"
                      leave-to-class="transform opacity-0 scale-95"
                    >
                      <MenuItems
                        class="origin-top-right absolute right-0 mt-2 -mr-1 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                      >
                        <div class="py-1">
                          <MenuItem
                            v-for="study in studies"
                            :key="study.slug"
                            v-slot="{ active }"
                          >
                            <a
                              @click="selectStudy(study)"
                              :class="[
                                active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                                'block px-4 py-2 text-sm',
                              ]"
                            >
                              {{ study.name }}
                            </a>
                          </MenuItem>
                        </div>
                      </MenuItems>
                    </transition>
                  </Menu>
                  <Menu as="div" class="-ml-px relative block">
                    <MenuButton
                      class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                      <b>SPECTRA DATASET: {{ selectedDataset.name }}</b>
                      <ChevronDownIcon class="h-5 w-5" aria-hidden="true" />
                    </MenuButton>
                    <transition
                      enter-active-class="transition ease-out duration-100"
                      enter-from-class="transform opacity-0 scale-95"
                      enter-to-class="transform opacity-100 scale-100"
                      leave-active-class="transition ease-in duration-75"
                      leave-from-class="transform opacity-100 scale-100"
                      leave-to-class="transform opacity-0 scale-95"
                    >
                      <MenuItems
                        class="origin-top-right absolute right-0 mt-2 -mr-1 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                      >
                        <div class="py-1">
                          <MenuItem
                            v-for="dataset in selectedStudy.datasets"
                            :key="dataset.slug"
                            v-slot="{ active }"
                          >
                            <a
                              @click="selectDataset(dataset)"
                              :class="[
                                active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                                'block px-4 py-2 text-sm',
                              ]"
                            >
                              {{ dataset.name }}
                            </a>
                          </MenuItem>
                        </div>
                      </MenuItems>
                    </transition>
                  </Menu>
                  <button
                    type="button"
                    class="-ml-px relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <span class="sr-only">Next</span>
                    <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                  </button>
                </div>
              </div>
              <iframe
                v-on:load="loadSpectra()"
                name="submissionNMRiumIframe"
                frameborder="0"
                allowfullscreen
                class="rounded-md border"
                style="width: 100%; height: 75vh"
                :src="nmriumURL"
              ></iframe>
            </div>
          </div>
        </div>
      </div>
      <div v-else>
        <svg
          class="animate-spin -ml-1 mr-3 h-5 w-5 text-dark flex-inline inline"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        Loading...
      </div>
    </template>

    <template v-if="!loading && currentDraft" #footer>
      <jet-secondary-button @click="toggleCreateDatasetDialog">
        Cancel
      </jet-secondary-button>

      <span v-if="currentStep.id == '01'">
        <jet-button
          class="ml-2"
          @click="process"
          :class="{ 'opacity-25': createDatasetForm.processing }"
          :disabled="createDatasetForm.processing"
        >
          Proceed
        </jet-button>
      </span>
    </template>
  </jet-dialog-modal>
</template>

<script>
import { Dropzone } from "dropzone";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import { CheckCircleIcon } from "@heroicons/vue/solid";
import { Link } from "@inertiajs/inertia-vue3";
import { Tab, TabGroup, TabList, TabPanel, TabPanels } from "@headlessui/vue";
import { AtSymbolIcon, CodeIcon, LinkIcon } from "@heroicons/vue/solid";
import JetInputError from "@/Jetstream/InputError.vue";
import { ref } from "vue";
import { Switch, SwitchDescription, SwitchGroup, SwitchLabel } from "@headlessui/vue";
import { inject } from "vue";
import { CheckIcon } from "@heroicons/vue/solid";
import axiosRetry from "axios-retry";

import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { ChevronLeftIcon, ChevronRightIcon, ChevronDownIcon } from "@heroicons/vue/solid";

export default {
  components: {
    CheckIcon,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,

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
    ChevronDownIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    JetInputError,
  },

  mounted() {
    const emitter = inject("emitter");
    emitter.on("openDatasetCreateDialog", () => {
      this.createDatasetDialog = true;
      this.fetchDrafts();
    });
  },

  onUpdated() {},

  data() {
    return {
      drafts: [],
      defaultDraft: null,
      currentDraft: null,
      selectedStudy: null,
      selectedDataset: null,
      loading: false,
      project: null,
      studies: null,
      progress: 0,
      file: null,
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

  props: [],

  methods: {
    loadDropZone() {
      this.$nextTick(() => {
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
          dictDefaultMessage: document.querySelector("#submission-dropzone-message")
            .innerHTML,
          done() {},
          accept(file, done) {
            const url = "/dashboard/storage/signed-draft-storage-url";

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
                draft_id: vm.currentDraft.id,
                // study_id: vm.study.id,
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
            vm.annotate();
            vm.$page.props.selectedFileSystemObject = this.files[0];
          },
        };
        this.dropzone = new Dropzone("#submission-dropzone", options);
        vm.dropzone.on("processing", (file) => {
          vm.status = "UPLOAD IN PROGRESS";
          vm.dropzone.options.url = file.uploadURL;
        });
      });
    },
    createNewDraft() {
      this.selectDraft(this.defaultDraft);
    },
    annotate() {
      axios
        .get("/dashboard/drafts/" + this.currentDraft.id + "/annotate")
        .then((response) => {
          this.loadFiles();
        });
    },
    selectDataset(dataset){
      this.selectedDataset = dataset
      this.loadSpectra()
    },
    selectStudy(study){
      this.selectedStudy = study
      this.selectDataset(this.selectedStudy.datasets[0])
    },  
    process() {
      axios
        .get("/dashboard/drafts/" + this.currentDraft.id + "/process")
        .then((response) => {
          this.project = response.data.project;
          this.studies = response.data.studies;
          if (this.project && this.studies.length > 0) {
            this.selectedStudy = this.studies[0];
            this.selectedDataset = this.selectedStudy.datasets[0]
            this.selectStep(2);
          }
        });
    },
    selectDraft(draft) {
      this.currentDraft = draft;
      this.loadDropZone();
      this.loadFiles();
    },
    fetchDrafts() {
      this.loading = true;
      axios.get("/dashboard/drafts").then((response) => {
        this.drafts = response.data.drafts;
        this.defaultDraft = response.data.default;
        if (this.drafts.length == 0) {
          this.currentDraft = this.defaultDraft;
          this.loading = false;
          this.loadDropZone();
        } else {
          this.loading = false;
        }
      });
    },
    loadSpectra() {
      const iframe = window.frames.submissionNMRiumIframe;
      if (iframe) {
        let data = {
          urls: [
            this.url + "/download/asc/datasets/" + this.selectedDataset.id + "/" + this.selectedDataset.name + ".zip"
          ],
        };
        iframe.postMessage({ type: `nmr-wrapper:loadURLs`, data }, "*");
      }
    },
    loadFiles() {
      axios
        .get("/dashboard/drafts/" + this.currentDraft.id + "/files")
        .then((response) => {
          this.file = response.data.file;
        });
    },
    selectStep(id) {
      this.steps.forEach((step) => {
        if (parseInt(step.id) < id) {
          step.status = "complete";
        } else if (parseInt(step.id) == id) {
          step.status = "current";
        } else {
          step.status = "upcoming";
        }
      });
    },
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
  computed: {
    currentStep() {
      return this.steps.filter((s) => s.status == "current")[0];
    },
    url() {
      return String(this.$page.props.url);
    },
    nmriumURL() {
      return this.$page.props.nmriumURL
        ? String(this.$page.props.nmriumURL)
        : "http://nmriumdev.nmrxiv.org";
    },
  },
};
</script>
