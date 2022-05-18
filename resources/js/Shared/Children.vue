<template>
  <span class="text-sm" v-if="file">
    <nav class="flex-1 space-y-0 bg-white" aria-label="Sidebar">
      <Disclosure
        as="div"
        :defaultOpen="file.name == '/'"
        class="space-y-1"
        v-slot="{ open = true }"
      >
        <div
          @click.stop="displaySelected(file)"
          :class="[
            $page.props.selectedFileSystemObject &&
            $page.props.selectedFileSystemObject.relative_url == file.relative_url
              ? 'cursor-pointer bg-gray-100'
              : 'cursor-pointer bg-white text-gray-600',
            'group w-full flex items-center pr-2 py-1 text-left font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
          ]"
        >
          <DisclosureButton>
            <span v-if="file.loading">
              <svg
                class="animate-spin mr-3 ml-1 h-5 w-5 text-dark"
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
            </span>
            <span v-else>
              <svg
                :class="[
                  open ? 'text-gray-400 rotate-90' : 'text-gray-300',
                  'mr-2 flex-shrink-0 inline h-5 w-5 transform group-hover:text-gray-400 transition-colors ease-in-out duration-150',
                ]"
                viewBox="0 0 20 20"
                aria-hidden="true"
              >
                <path d="M6 6L14 10L6 14V6Z" fill="currentColor" />
              </svg>
            </span>
          </DisclosureButton>
          <FolderIcon
            class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-400"
            aria-hidden="true"
          />
          {{ file.name }}
        </div>
        <DisclosurePanel class="space-y-1">
          <span v-for="sfile in file.children" :key="sfile.name">
            <div class="ml-2">
              <div
                @click.stop="displaySelected(sfile)"
                :class="[
                  sfile.current
                    ? 'text-gray-900'
                    : 'cursor-pointer bg-white text-gray-600',
                  'cursor-pointer group w-full flex items-center font-medium rounded-md',
                ]"
              >
                <span v-if="sfile.type == 'directory'">
                  <span v-if="sfile.has_children">
                    <Disclosure as="div" class="space-y-1" v-slot="{ open }">
                      <div
                        @click.stop="displaySelected(sfile)"
                        :class="[
                          $page.props.selectedFileSystemObject &&
                          $page.props.selectedFileSystemObject.relative_url ==
                            sfile.relative_url
                            ? 'cursor-pointer bg-gray-100 text-gray-900'
                            : 'cursor-pointer bg-white text-gray-600',
                          'group w-full flex items-center pr-2 py-1 text-left font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                        ]"
                      >
                        <DisclosureButton>
                          <span v-if="sfile.loading">
                            <svg
                              class="animate-spin ml-1 mr-3 h-5 w-5 text-dark"
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
                          </span>
                          <span v-else>
                            <svg
                              :class="[
                                open ? 'text-gray-400 rotate-90' : 'text-gray-300',
                                'mr-2 inline flex-shrink-0 h-5 w-5 transform group-hover:text-gray-400 transition-colors ease-in-out duration-150',
                              ]"
                              viewBox="0 0 20 20"
                              aria-hidden="true"
                            >
                              <path d="M6 6L14 10L6 14V6Z" fill="currentColor" />
                            </svg>
                          </span>
                        </DisclosureButton>
                        <span>
                          <span v-if="sfile.type == 'directory'">
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
                          {{ sfile.name }}
                        </span>
                      </div>
                      <DisclosurePanel class="space-y-0">
                        <div
                          @click.stop="displaySelected(subItem)"
                          v-for="subItem in sfile.children"
                          :key="subItem.name"
                          as="div"
                          class="cursor-pointer group w-full flex items-center pl-7 pr-2 py-0 font-medium text-gray-600 rounded-md"
                        >
                          <span v-if="subItem.type == 'directory'">
                            <children
                              :file="subItem"
                              :files="subItem.children"
                              :study="study"
                              :project="project"
                            ></children>
                          </span>
                          <span
                            :class="[
                              $page.props.selectedFileSystemObject &&
                              $page.props.selectedFileSystemObject.relative_url ==
                                subItem.relative_url
                                ? 'cursor-pointer bg-gray-100'
                                : 'cursor-pointer bg-white text-gray-600',
                              'p-1 rounded-md truncate ...',
                            ]"
                            v-else
                          >
                            <DocumentTextIcon
                              class="mr-1 inline h-5 w-5 text-gray-400"
                              aria-hidden="true"
                            />
                            {{ subItem.name }}
                          </span>
                        </div>
                      </DisclosurePanel>
                    </Disclosure>
                  </span>
                </span>
                <span
                  :class="[
                    $page.props.selectedFileSystemObject &&
                    $page.props.selectedFileSystemObject.relative_url ==
                      sfile.relative_url
                      ? 'cursor-pointer bg-gray-100'
                      : 'cursor-pointer bg-white text-gray-600',
                    'p-1 ml-5 rounded-md truncate ...',
                  ]"
                  v-else
                >
                  <DocumentTextIcon
                    class="inline mr-1 h-5 w-5 text-gray-400"
                    aria-hidden="true"
                  />
                  {{ sfile.name }}
                </span>
              </div>
            </div>
          </span>
        </DisclosurePanel>
      </Disclosure>
    </nav>
  </span>
</template>

<script>
import StudyContent from "@/Pages/Study/Content.vue";
import { FolderIcon, DocumentTextIcon } from "@heroicons/vue/solid";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";

export default {
  props: ["study", "project", "file"],
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
    return {};
  },
  mounted() {},
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

      this.$emit("reload-nmrium");
    },
  },
  computed: {},
};
</script>
