<template>
  <div>
    <study-content :project="project" :study="study" current="Files">
      <template #study-section>
        <div class="divide-y divide-gray-200 sm:col-span-9">
          <div class="py-6 px-4 sm:p-6">
            <div class="flex flex-item">
              <h2 class="text-lg leading-6 font-medium text-gray-900">Data set</h2>
            </div>
          </div>
          <div v-if="files">
            <div class="min-w-0 flex-1 min-h-screen border-t border-gray-200 lg:flex">
              <aside class="hidden py-3 px-2 lg:block lg:flex-shrink-0 lg:order-first">
                <div
                  v-if="files.length > 0"
                  class="h-full relative flex flex-col w-64 border-r border-gray-200 bg-gray-100 overflow-y-auto"
                >
                  <nav class="flex-1 px-2 pt-3 space-y-1 bg-white" aria-label="Sidebar">
                    <div
                      @click="displaySelected('root')"
                      :class="[
                          (selectedFileSystemObject && selectedFileSystemObject == 'root')
                            ? 'bg-gray-100 text-gray-900'
                            : 'cursor-pointer bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                          'cursor-pointer group w-full flex items-center pl-3 pr-2 py-2 text-sm font-medium rounded-md',
                        ]"
                    >
                        <FolderIcon
                          class="-ml-1.5 mr-1 h-5 w-5 text-gray-400"
                          aria-hidden="true"
                        />
                      <span class="text-gray-400">/</span>
                    </div>
                    <template v-for="file in files" :key="file.name">
                      <div class="ml-4" v-if="!file.has_children">
                        <div
                          @click="displaySelected(file)"
                          :class="[
                            file.current
                              ? 'bg-gray-100 text-gray-900'
                              : 'cursor-pointer bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                            'cursor-pointer group w-full flex items-center pl-3 pr-2 py-2 text-sm font-medium rounded-md',
                          ]"
                        >
                          <span v-if="file.type == 'directory'">
                            <FolderIcon
                              class="-ml-1.5 mr-1 h-5 w-5 text-gray-400"
                              aria-hidden="true"
                            />
                          </span>
                          <span v-else>
                            <DocumentTextIcon
                              class="-ml-1.5 mr-1 h-5 w-5 text-gray-400"
                              aria-hidden="true"
                            />
                          </span>
                          {{ file.name }}
                        </div>
                      </div>
                      <Disclosure as="div" v-else class="space-y-1 ml-4" v-slot="{ open }">
                        <div
                          @click="displaySelected(file)"
                          :class="[
                            (selectedFileSystemObject && file.id == selectedFileSystemObject.id)
                              ? 'cursor-pointer bg-gray-100 text-gray-900'
                              : 'cursor-pointer bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                            'group w-full flex items-center pr-2 py-2 text-left text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                          ]"
                        >
                          <DisclosureButton>
                            <svg
                              :class="[
                                open ? 'text-gray-400 rotate-90' : 'text-gray-300',
                                'mr-2 flex-shrink-0 h-5 w-5 transform group-hover:text-gray-400 transition-colors ease-in-out duration-150',
                              ]"
                              viewBox="0 0 20 20"
                              aria-hidden="true"
                            >
                              <path d="M6 6L14 10L6 14V6Z" fill="currentColor" />
                            </svg>
                          </DisclosureButton>
                          <span>
                            <span v-if="file.type == 'directory'">
                              <FolderIcon
                                class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-400"
                                aria-hidden="true"
                              />
                            </span>
                            <span v-else>
                              <DocumentTextIcon
                                class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-400"
                                aria-hidden="true"
                              />
                            </span>
                            {{ file.name }}
                          </span>
                        </div>
                        <DisclosurePanel class="space-y-1">
                          <div
                            @click="displaySelected(subItem)"
                            v-for="subItem in file.children"
                            :key="subItem.name"
                            as="div"
                            class="cursor-pointer group w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50"
                          >
                            <span v-if="subItem.type == 'directory'">
                              <FolderIcon
                                class="-ml-1.5 mr-1 h-5 w-5 text-gray-400"
                                aria-hidden="true"
                              />
                            </span>
                            <span v-else>
                              <DocumentTextIcon
                                class="-ml-1.5 mr-1 h-5 w-5 text-gray-400"
                                aria-hidden="true"
                              />
                            </span>
                            {{ subItem.name }}
                          </div>
                        </DisclosurePanel>
                      </Disclosure>
                    </template>
                  </nav>
                </div>
              </aside>
              <section
                class="min-w-0 p-6 flex-1 h-full flex flex-col overflow-y-auto lg:order-last"
              >
               <div>
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
                      Drop Files or Folders to upload to <span v-if="selectedFileSystemObject">"{{ selectedFolder }}"</span> folder
                    </span>
                    
                    <div v-if="progress > 0" class="relative mt-5">
                        <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                          <div
                            :style="'width: '+progress+'%'"
                            class="
                              shadow-none
                              flex flex-col
                              text-center
                              whitespace-nowrap
                              text-white
                              justify-center
                              bg-green-500
                            "
                          ></div>
                        </div>
                      </div>
                      <div v-if="progress > 0" >
                        <span class="mt-2 block text-sm font-medium text-gray-900">
                          {{ status }} ({{progress}}%)
                        </span>
                      </div>
                  </div>
                </div>
              </form>
            </div>
                <div
                  class="mb-3"
                  v-if="selectedFileSystemObject && selectedFileSystemObject.has_children"
                >
                  <ul
                    role="list"
                    class="mb-3 grid grid-cols-1 gap-5 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4"
                  >
                    <li
                      v-for="file in selectedFileSystemObject.children"
                      :key="file.key"
                      class="relative shadow rounded-lg"
                    >
                      <div
                        class="group block w-full aspect-w-10 aspect-h-7 py-4 bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-indigo-500 overflow-hidden"
                      >
                        <span v-if="file.type == 'directory'">
                          <FolderIcon
                            class="h-28 w-28 text-gray-400 flex-shrink-0 mx-auto"
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
                    <span v-if="selectedFileSystemObject && selectedFileSystemObject.type == 'file'" >
                      <span v-if="selectedFileSystemObject.key.indexOf('.dx') > -1 || selectedFileSystemObject.key.indexOf('.jdx') > -1  || selectedFileSystemObject.key.indexOf('.zip') > -1  || selectedFileSystemObject.key.indexOf('.json') > -1">
                        <iframe v-on:load="loadSpectra()" name="crossDomainIframe" frameborder="0" allowfullscreen style="width:100%; height:500px" src="//nmriumdev.nmrxiv.org/"></iframe>
                      </span>
                      <span v-else>
                        {{ selectedFileSystemObject }}
                      </span>
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
import StudyContent from "@/Pages/Study/Content.vue";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { FolderIcon, DocumentTextIcon } from "@heroicons/vue/solid";
import { Inertia } from '@inertiajs/inertia';
import { nextTick } from 'vue';

export default {
  props: ["study", "project", "files"],
  components: {
    StudyContent,
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    FolderIcon,
    DocumentTextIcon,
  },
  setup() {
    return {};
  },
  data() {
    return {
      progress: 0,
      status: null,
      selectedFileSystemObject: null,
      selectedFolder: "/"
    };
  },
  mounted() {
    const vm = this;
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
      uploadMultiple: true,
      disablePreviews: true,
      parallelUploads: 100,
      maxFiles: 100,
      dictDefaultMessage: document.querySelector("#dropzone-message").innerHTML,
      done(){
      },
      accept(file, done) {
        const url = "/storage/signed-storage-url";
        axios
          .post(url, {
            file: file,
            destination: vm.selectedFolder,
            project_id: vm.project.id,
            study_id: vm.study.id,
          })
          .then(function (response) {
            let data = response.data;
            let headers = data.headers;
            if ("Host" in headers) {
              delete headers.Host;
            }
            file.uploadURL = data.url;
            done();
            setTimeout(() => vm.dropzone.processFile(file));
          });
      },
      totaluploadprogress: function(progress) {
        vm.progress = Math.ceil(progress)
      },
      queuecomplete: function(){
        vm.status = "UPLOAD COMPLETE"
        Inertia.reload()
        this.selectedFileSystemObject = this.files[0];
      }
    };
    this.dropzone = new Dropzone(this.$el, options);

    vm.dropzone.on("processing", (file) => {
      vm.status = "UPLOAD IN PROGRESS"
      vm.dropzone.options.url = file.uploadURL;
    });

    this.selectedFileSystemObject = "root"
  },
  methods: {
    async displaySelected(file) {
      this.selectedFileSystemObject = file;
      if(file == "root" ){
        this.selectedFolder = "/"
      }else{
        if(this.selectedFileSystemObject.type != 'file'){
          this.selectedFolder = this.selectedFileSystemObject.relative_url
        }else{
          if(this.selectedFileSystemObject.parent_id == null){
            this.selectedFolder = "/"
          }else{
            this.selectedFolder = this.files.filter(f => f.id == this.selectedFileSystemObject.parent_id)[0].relative_url
          }
        }
      }
      this.loadSpectra()
    },
    loadSpectra(){
      const iframe = window.frames.crossDomainIframe
      if(iframe){
        let data = {
          spectra: [
            {
              source: {
                jcampURL:
                  'http://localhost:80/asc/studies/'+this.study.id+'/file/' + this.selectedFileSystemObject.slug,
              },
            },
          ]
        }
        iframe.postMessage({ type: `nmr-wrapper:load`, data }, "*")
      }
    }
  },
};
</script>
