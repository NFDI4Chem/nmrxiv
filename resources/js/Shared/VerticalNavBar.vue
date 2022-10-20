<template>
    <div>
        <nav class="flex-1 px-2 pt-3 space-y-1 bg-white" aria-label="Sidebar">
            <template v-for="file in files" :key="file.name">
                <div v-if="!file.has_children">
                    <div
                        :class="[
                            file.current
                                ? 'bg-gray-100 text-gray-900'
                                : 'cursor-pointer bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                            'cursor-pointer group w-full flex items-center pl-3 pr-2 py-2 text-sm font-medium rounded-md',
                        ]"
                        @click="displaySelected(file)"
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
                <Disclosure v-else v-slot="{ open }" as="div" class="space-y-1">
                    <div
                        v-if="selectedFileSystemObject"
                        :class="[
                            file.id == selectedFileSystemObject.id
                                ? 'cursor-pointer bg-gray-100 text-gray-900'
                                : 'cursor-pointer bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                            'group w-full flex items-center pr-2 py-2 text-left text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                        ]"
                        @click="displaySelected(file)"
                    >
                        <DisclosureButton>
                            <svg
                                :class="[
                                    open
                                        ? 'text-gray-400 rotate-90'
                                        : 'text-gray-300',
                                    'mr-2 flex-shrink-0 h-5 w-5 transform group-hover:text-gray-400 transition-colors ease-in-out duration-150',
                                ]"
                                viewBox="0 0 20 20"
                                aria-hidden="true"
                            >
                                <path
                                    d="M6 6L14 10L6 14V6Z"
                                    fill="currentColor"
                                />
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
                        <vertical-nav-bar
                            :files="file.children"
                        ></vertical-nav-bar>
                        <!-- <div
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
            </div> -->
                    </DisclosurePanel>
                </Disclosure>
            </template>
        </nav>
    </div>
</template>

<script>
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { FolderIcon, DocumentTextIcon } from "@heroicons/vue/24/solid";

export default {
    components: {
        FolderIcon,
        DocumentTextIcon,
        Disclosure,
        DisclosureButton,
        DisclosurePanel,
    },
    props: ["files"],
    methods: {},
};
</script>
