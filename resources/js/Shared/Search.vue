<template>
  <button
    @click="open = true"
    type="button"
    class="hidden -ml-4 sm:flex items-center w-96 text-left space-x-3 px-4 h-12 bg-white ring-1 ring-slate-900/10 hover:ring-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-sm rounded-lg text-slate-400 dark:bg-slate-800 dark:ring-0 dark:text-slate-300 dark:highlight-white/5 dark:hover:bg-slate-700"
  >
    <svg
      width="24"
      height="24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
      class="flex-none text-slate-300 dark:text-slate-400"
      aria-hidden="true"
    >
      <path d="m19 19-3.5-3.5"></path>
      <circle cx="11" cy="11" r="6"></circle></svg
    ><span class="flex-auto">Search...</span
    >
    <!-- <kbd class="font-sans font-semibold dark:text-slate-500"
      ><abbr title="Command" class="no-underline text-slate-300 dark:text-slate-500"
        >⌘</abbr
      >
      K</kbd> -->
  </button>
  <ais-instant-search :index-name="index" :search-client="searchClient">
    <ais-autocomplete>
      <template v-slot="{ currentRefinement, indices, refine }">
        <TransitionRoot :show="open" as="template" @after-leave="query = ''" appear>
          <Dialog as="div" class="relative z-10" @close="open = false">
            <TransitionChild
              as="template"
              enter="ease-out duration-300"
              enter-from="opacity-0"
              enter-to="opacity-100"
              leave="ease-in duration-200"
              leave-from="opacity-100"
              leave-to="opacity-0"
            >
              <div class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
              <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0 scale-95"
                enter-to="opacity-100 scale-100"
                leave="ease-in duration-200"
                leave-from="opacity-100 scale-100"
                leave-to="opacity-0 scale-95"
              >
                <DialogPanel
                  class="mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all"
                >
                  <Combobox
                    as="div"
                    class="mx-auto max-w-2xl transform divide-y divide-gray-500 divide-opacity-10 overflow-hidden rounded-xl bg-white bg-opacity-80 shadow-2xl ring-1 ring-black ring-opacity-5 backdrop-blur backdrop-filter transition-all"
                    @update:modelValue="onSelect"
                  >
                    <div class="relative">
                      <SearchIcon
                        class="pointer-events-none absolute top-3.5 left-4 h-5 w-5 text-gray-900 text-opacity-40"
                        aria-hidden="true"
                      />
                      <ComboboxInput
                        id="searchAC"
                        class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm"
                        placeholder="Search..."
                        @input="refine($event.currentTarget.value)"
                      />
                    </div>

                    <ComboboxOptions
                      static
                      class="max-h-80 scroll-py-2 divide-y divide-gray-500 divide-opacity-10 overflow-y-auto"
                    >
                      <div
                        aria-labelledby="searchAC"
                        v-for="index in indices"
                        :key="index.indexId"
                        class="dropdown-menu p-0 w-100"
                      >
                        <li v-if="currentRefinement === ''" class="p-2">
                          <!-- <div
                            class="flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700"
                          >
                            Type
                            <kbd
                              class="mx-1 flex h-5 w-5 items-center justify-center rounded border bg-white font-semibold sm:mx-2 border-gray-400 text-gray-900"
                              >#</kbd
                            >
                            <span class="sm:hidden">for projects,</span
                            ><span class="hidden sm:inline">to access projects,</span
                            ><kbd
                              class="mx-1 flex h-5 w-5 items-center justify-center rounded border bg-white font-semibold sm:mx-2 border-gray-400 text-gray-900"
                              >&gt;</kbd
                            >
                            for users, and
                            <kbd
                              class="mx-1 flex h-5 w-5 items-center justify-center rounded border bg-white font-semibold sm:mx-2 border-gray-400 text-gray-900"
                              >?</kbd
                            >
                            for help.
                          </div> -->
                        <div
                            class="flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700"
                          >
                            Type to search projects or datasets or click below for help.
                          </div>
                        </li>
                        <li v-if="currentRefinement != ''" class="p-2">
                          <h2
                            v-if="currentRefinement === '' && recent.length > 0"
                            class="mt-4 mb-2 px-3 text-xs font-semibold text-gray-900"
                          >
                            Recent searches
                          </h2>
                          <div v-if="currentRefinement" class="border rounded-md">
                            <span v-if="index.hits.length > 0">
                              <ul class="text-sm text-gray-700">
                                <ComboboxOption
                                  v-for="hit in index.hits"
                                  :key="hit.objectID"
                                  :value="hit"
                                  as="template"
                                  v-slot="{ active }"
                                >
                                  <li
                                    :class="[
                                      'flex cursor-default select-none items-center rounded-md px-3 py-2',
                                      active && 'bg-gray-900 bg-opacity-5 text-gray-900',
                                    ]"
                                  >
                                   <Link class="w-full" :href="'/projects/'+hit.slug">
                                      <FolderIcon
                                          :class="[
                                            'h-6 w-6 inline flex-none text-gray-900 text-opacity-40',
                                            active && 'text-opacity-100',
                                          ]"
                                          aria-hidden="true"
                                        />
                                        <span class="ml-3 flex-auto truncate">{{
                                          hit.name
                                        }}</span>
                                        <span
                                          v-if="active"
                                          class="ml-3 flex-none text-gray-500"
                                          >Jump to...</span
                                        >
                                   </Link>
                                  </li>
                                </ComboboxOption>
                              </ul>
                            </span>
                            <span v-else>
                              <div
                                v-if="currentRefinement !== '' && index.hits.length === 0"
                                class="py-14 px-6 text-center sm:px-14"
                              >
                                <!-- <FolderIcon
                                class="mx-auto h-6 w-6 text-gray-900 text-opacity-40"
                                aria-hidden="true"
                              /> -->
                                <p class="mt-4 text-sm text-gray-900">
                                  We couldn't find any results with that term. Please try
                                  again.
                                </p>
                              </div>
                            </span>
                          </div>
                        </li>
                      </div>
                      <li v-if="currentRefinement === ''" class="p-2">
                        <h2 class="sr-only">Quick actions</h2>
                        <ul class="text-sm text-gray-700">
                          <ComboboxOption
                            v-for="action in quickActions"
                            :key="action.shortcut"
                            :value="action"
                            as="template"
                            v-slot="{ active }"
                          >
                            <li
                              :class="[
                                'flex cursor-default select-none items-center rounded-md px-3 py-2',
                                active && 'bg-gray-900 bg-opacity-5 text-gray-900',
                              ]"
                            >
                              <component
                                :is="action.icon"
                                :class="[
                                  'h-6 w-6 flex-none text-gray-900 text-opacity-40',
                                  active && 'text-opacity-100',
                                ]"
                                aria-hidden="true"
                              />
                              <span class="ml-3 flex-auto truncate">{{
                                action.name
                              }}</span>
                              <!-- <span
                                class="ml-3 flex-none text-xs font-semibold text-gray-500"
                              >
                                <kbd class="font-sans">⌘</kbd>
                                <kbd class="font-sans">{{ action.shortcut }}</kbd>
                              </span> -->
                            </li>
                          </ComboboxOption>
                        </ul>
                      </li>
                    </ComboboxOptions>
                  </Combobox>
                </DialogPanel>
              </TransitionChild>
            </div>
          </Dialog>
        </TransitionRoot>
      </template>
    </ais-autocomplete>
  </ais-instant-search>
</template>

<style scoped></style>

<script>
import { defineComponent } from "vue";
import { instantMeiliSearch } from "@meilisearch/instant-meilisearch";
import { computed, ref } from "vue";
import { SearchIcon } from "@heroicons/vue/solid";
import { Link } from "@inertiajs/inertia-vue3";
import {
  DocumentAddIcon,
  FolderIcon,
  FolderAddIcon,
  HashtagIcon,
  TagIcon,
} from "@heroicons/vue/outline";
import {
  Combobox,
  ComboboxInput,
  ComboboxOptions,
  ComboboxOption,
  Dialog,
  DialogPanel,
  DialogOverlay,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";

const projects = [
  //   { id: 1, name: "Workflow Inc. / Website Redesign", url: "#" },
  // More projects...
];
const recent = [];
// [projects[0]];
const quickActions = [
  { name: "Ask a question...", icon: DocumentAddIcon, shortcut: "N", url: "#" },
  { name: "Learn Spectral analysis...", icon: TagIcon, shortcut: "F", url: "#" },
  //   { name: "Add hashtag...", icon: HashtagIcon, shortcut: "H", url: "#" },
  //   { name: "Add label...", icon: TagIcon, shortcut: "L", url: "#" },
];

export default {
  props: ["akey", "host"],
  components: {
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
    DialogPanel,
    Dialog,
    DialogOverlay,
    FolderIcon,
    SearchIcon,
    TransitionChild,
    TransitionRoot,
    Link
  },
  data: function () {
    return {
      selected: null,
      department: "all",
      query: null,
      filteredProjects: [],
      searchClient: instantMeiliSearch(this.host, this.akey),
    };
  },
  setup() {
    const open = ref(false);

    return {
      open,
      recent,
      quickActions,
      onSelect(item) {
        window.location = item.url;
      },
    };
  },

  methods: {
    submit() {
      if (this.query && this.query != "") {
        window.location.href = "/search?query=" + this.query;
      } else {
        window.location.href = "/search";
      }
    },
    searchFunction(helper) {
      const page = helper.getPage();
      helper.setQuery(this.query).setPage(page).search();
    },
  },

  computed: {
    index() {
      return this.$page.props.SCOUT_PREFIX + "projects";
    },
  },
};
</script>
