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
      current="Files"
    >
      <template #study-section>
        <div class="divide-y divide-gray-200 sm:col-span-9">
          <!-- <div class="py-6 px-4 sm:p-6">
            <div class="flex flex-item">
              <h2 class="text-lg leading-6 font-medium text-gray-900">Data set</h2>
            </div>
          </div> -->
          <div v-if="file">
            <nav
              v-if="$page.props.selectedFolder"
              class="flex p-3"
              aria-label="Breadcrumb"
            >
              <ol role="list" class="flex items-center space-x-2">
                <li>
                  <div>
                    <a class="text-gray-400 hover:text-gray-900">
                      <HomeIcon class="flex-shrink-0 h-5 w-5" aria-hidden="true" />
                      <span class="sr-only">Home</span>
                    </a>
                  </div>
                </li>
                <li
                  v-for="page in $page.props.selectedFolder.split('/')"
                  :key="page.name"
                >
                  <div v-if="page != ''" class="flex items-center">
                    <ChevronRightIcon
                      class="flex-shrink-0 h-5 w-5 text-gray-400"
                      aria-hidden="true"
                    />
                    <a
                      class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                      :aria-current="page ? 'page' : undefined"
                      >{{ page }}</a
                    >
                  </div>
                </li>
              </ol>
            </nav>
            <div class="min-w-0 flex-1 min-h-screen border-t border-gray-200 lg:flex">
              <aside class="hidden py-3 px-2 lg:block lg:flex-shrink-0 lg:order-first">
                <div
                  v-if="file != null"
                  class="h-full relative flex flex-col w-64 border-r border-gray-200 overflow-y-auto"
                >
                  <children
                    @reload-nmrium="loadSpectra()"
                    :file="file"
                    :study="study"
                    :project="project"
                  ></children>
                </div>
              </aside>
              <section
                class="min-w-0 p-6 flex-1 h-full flex flex-col overflow-y-auto lg:order-last"
              >
                <div v-if="canUpdateFiles">
                  <form class="dropzone py-2 mb-3">
                    <div id="dropzone-message" class="text-center">
                      <div
                        type="button"
                        class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
                          <div
                            class="overflow-hidden h-2 text-xs flex rounded bg-gray-200"
                          >
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
                <div
                  class="mb-3"
                  v-if="
                    $page.props.selectedFileSystemObject &&
                    $page.props.selectedFileSystemObject.has_children
                  "
                >
                  <div class="py-2 mb-2 block">
                    <p class="font-bold text-xl">
                      {{ $page.props.selectedFileSystemObject.name }}
                      <a
                        :href="downloadURL"
                        class="ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 float-right"
                      >
                        Download
                      </a>
                    </p>
                  </div>
                  <ul
                    role="list"
                    class="mb-3 grid grid-cols-1 gap-5 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4"
                  >
                    <li
                      v-for="file in $page.props.selectedFileSystemObject.children"
                      :key="file.key"
                      class="relative shadow rounded-lg"
                    >
                      <div
                        class="group block w-full aspect-w-10 aspect-h-7 py-4 bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-indigo-500 overflow-hidden"
                      >
                        <span v-if="file.type == 'directory'">
                          <FolderIcon
                            @dblclick.stop="displaySelected(file)"
                            class="cursor-pointer h-28 w-28 text-gray-400 flex-shrink-0 mx-auto"
                            aria-hidden="true"
                          />
                        </span>
                        <span v-else>
                          <DocumentTextIcon
                            class="h-28 w-28 text-gray-400 flex-shrink-0 mx-auto"
                            aria-hidden="true"
                          />
                        </span>
                      </div>
                      <p
                        class="mt-2 px-2 py-1 block text-sm font-medium text-gray-900 pointer-events-none"
                      >
                        {{ file.name }}
                      </p>
                      <p
                        class="block text-sm font-medium text-gray-500 pointer-events-none"
                      >
                        {{ file.size }}
                      </p>
                    </li>
                  </ul>
                </div>
                <div v-else>
                  <div class="">
                    <span
                      v-if="
                        $page.props.selectedFileSystemObject &&
                        $page.props.selectedFileSystemObject.type == 'file'
                      "
                    >
                      <div
                        class="mb-4"
                        v-if="
                          $page.props.selectedFileSystemObject.key.indexOf('.dx') > -1 ||
                          $page.props.selectedFileSystemObject.key.indexOf('.jdx') > -1 ||
                          $page.props.selectedFileSystemObject.key.indexOf('.zip') > -1 ||
                          $page.props.selectedFileSystemObject.key.indexOf('.json') > -1
                        "
                      >
                        <iframe
                          v-on:load="loadSpectra()"
                          name="crossDomainIframe"
                          frameborder="0"
                          allowfullscreen
                          class="rounded-md border"
                          style="width: 100%; height: 500px"
                          :src="nmriumURL"
                        ></iframe>
                      </div>
                      <div
                        class="rounded-md border my-3 flex justify-center items-center"
                        v-if="svgString"
                      >
                        <span v-html="svgString"></span>
                      </div>
                      <File-details
                        :study="study"
                        :file="$page.props.selectedFileSystemObject"
                      ></File-details>
                    </span>
                  </div>
                </div>
              </section>
            </div>
          </div>
        </div>
      </template>
    </study-content>
  </div>
</template>

<script>
import { Dropzone } from "dropzone";
import { Inertia } from "@inertiajs/inertia";
import StudyContent from "@/Pages/Study/Content.vue";
import FileDetails from "@/Shared/FileDetails.vue";
import axiosRetry from "axios-retry";
import OCL from "openchemlib/minimal";

import {
  FolderIcon,
  DocumentTextIcon,
  ChevronRightIcon,
  HomeIcon,
} from "@heroicons/vue/solid";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
export default {
  props: [
    "study",
    "project",
    "file",
    "team",
    "members",
    "availableRoles",
    "studyPermissions",
    "studyRole",
  ],
  components: {
    StudyContent,
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    FolderIcon,
    DocumentTextIcon,
    FileDetails,
    ChevronRightIcon,
    HomeIcon,
  },
  setup() {
    return {};
  },
  data() {
    return {
      progress: 0,
      status: null,
      selectedFileSystemObject: null,
      svgString: null,
    };
  },
  mounted() {
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
      dictDefaultMessage: document.querySelector("#dropzone-message").innerHTML,
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

    this.$page.props.selectedFileSystemObject = "root";
  },
  methods: {
    displaySelected(file) {
      this.$page.props.selectedFileSystemObject = file;

      let sFolder = "/";
      if (this.$page.props.selectedFileSystemObject.name == "/") {
        sFolder = "/";
      } else {
        if (this.$page.props.selectedFileSystemObject.type != "file") {
          sFolder = this.$page.props.selectedFileSystemObject.relative_url;
        } else {
          if (this.$page.props.selectedFileSystemObject.parent_id == null) {
            sFolder = "/";
          } else {
            sFolder = this.$page.props.selectedFileSystemObject.relative_url.replace(
              "/" + this.$page.props.selectedFileSystemObject.name,
              ""
            );
          }
        }
      }
      this.$page.props.selectedFolder = sFolder;

      if (file.has_children && file.level > 0 && !file.children) {
        file.loading = true;
        axios
          .get("/api/v1/files/children/" + this.study.id + "/" + file.id)
          .then((response) => {
            file.children = response.data.files[0].children;
            file.loading = false;
          });
      }
      this.$emit("reloadnmrium");
    },
    loadSpectra() {
      if (
        this.$page.props.selectedFileSystemObject &&
        this.$page.props.selectedFileSystemObject.key.indexOf(".mol") > -1
      ) {
        this.loadMol();
      }
      const iframe = window.frames.crossDomainIframe;
      if (iframe) {
        let data = {
          urls: [
            this.downloadURL
          ],
        };
        iframe.postMessage({ type: `nmr-wrapper:loadURLs`, data }, "*");
      }
    },
    loadMol() {
      this.svgString = null;
      axios
        .get(this.downloadURL)
        .then((response) => {
          if (response && response.data != "") {
            let mol = OCL.Molecule.fromMolfile(response.data);
            if (mol.toIsomericSmiles() != "") {
              this.svgString = mol.toSVG(300, 300);
            }
          }
        });
    },
  },
  computed: {
    url() {
      return String(this.$page.props.url);
    },
    nmriumURL() {
      return this.$page.props.nmriumURL
        ? String(this.$page.props.nmriumURL)
        : "//nmriumdev.nmrxiv.org";
    },
    downloadURL(){
      return this.url + "/" + this.project.owner.username + '/download/' + this.project.slug + "?key=" + this.$page.props.selectedFileSystemObject.name + "&uuid=" + this.$page.props.selectedFileSystemObject.uuid
    },
    canUpdateFiles(){
      return this.studyPermissions ? this.studyPermissions.canUpdateStudy : false;
    },
},
};
</script>
