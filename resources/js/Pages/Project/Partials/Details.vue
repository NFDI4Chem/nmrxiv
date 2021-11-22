<template>
  <TransitionRoot as="template" :show="open">
    <Dialog as="div" class="fixed inset-0 overflow-hidden z-50" @close="open = false">
      <div class="absolute inset-0 overflow-hidden">
        <DialogOverlay class="absolute inset-0" />

        <div class="fixed inset-y-0 pl-16 max-w-full right-0 flex">
          <TransitionChild
            as="template"
            enter="transform transition ease-in-out duration-500 sm:duration-700"
            enter-from="translate-x-full"
            enter-to="translate-x-0"
            leave="transform transition ease-in-out duration-500 sm:duration-700"
            leave-from="translate-x-0"
            leave-to="translate-x-full"
          >
            <div class="w-screen max-w-md">
              <div
                class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl"
              >
                <div class="flex-1 h-0 overflow-y-auto">
                  <div class="py-6 px-4 bg-indigo-700 sm:px-6">
                    <div class="flex items-center justify-between">
                      <DialogTitle class="text-lg font-medium text-white">
                        {{ project.name }}
                      </DialogTitle>
                      <div class="ml-3 h-7 flex items-center">
                        <button
                          type="button"
                          class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                          @click="open = false"
                        >
                          <span class="sr-only">Close panel</span>
                          <XIcon class="h-6 w-6" aria-hidden="true" />
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="flex-1 flex flex-col justify-between">
                    <div class="px-4 divide-y divide-gray-200 sm:px-6">
                      <div class="space-y-6 pt-6 pb-5">
                        <div>
                          <label
                            for="project-name"
                            class="block text-sm font-medium text-gray-900"
                          >
                            Project name
                          </label>
                          <div class="mt-1">
                            <input
                              v-model="form.name"
                              type="text"
                              name="project-name"
                              id="project-name"
                              class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md"
                            />
                          </div>
                        </div>
                        <div>
                          <label
                            for="description"
                            class="block text-sm font-medium text-gray-900"
                          >
                            Description
                          </label>
                          <div class="mt-1">
                            <TabGroup>
                              <TabList class="flex items-center">
                                <Tab as="template" v-slot="{ selected }">
                                  <button
                                    :class="[
                                      selected
                                        ? 'text-gray-900 bg-gray-100 hover:bg-gray-200'
                                        : 'text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100',
                                      'px-3 py-1.5 border border-transparent text-sm font-medium rounded-md',
                                    ]"
                                  >
                                    Write
                                  </button>
                                </Tab>
                                <Tab as="template" v-slot="{ selected }">
                                  <button
                                    :class="[
                                      selected
                                        ? 'text-gray-900 bg-gray-100 hover:bg-gray-200'
                                        : 'text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100',
                                      'ml-2 px-3 py-1.5 border border-transparent text-sm font-medium rounded-md',
                                    ]"
                                  >
                                    Preview
                                  </button>
                                </Tab>
                              </TabList>
                              <TabPanels class="mt-2">
                                <TabPanel class="p-0.5 -m-0.5 rounded-lg">
                                  <label for="comment" class="sr-only">Comment</label>
                                  <div>
                                    <textarea
                                      v-model="form.description"
                                      id="description"
                                      name="description"
                                      placeholder="Description (Optional)"
                                      rows="3"
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    />
                                  </div>
                                </TabPanel>
                                <TabPanel class="p-0.5 -m-0.5 rounded-lg">
                                  <div class="border-b">
                                    <div class="mx-px mt-px px-3 pt-2 pb-12">
                                      <span
                                        class="text-gray-400 text-sm font-medium"
                                        v-if="form.description == ''"
                                      >
                                        Nothing to preview
                                      </span>
                                      <span v-else>
                                        <div
                                          class="prose"
                                          v-html="md(form.description)"
                                        ></div>
                                      </span>
                                    </div>
                                  </div>
                                </TabPanel>
                              </TabPanels>
                            </TabGroup>
                            <label class="block text-sm font-medium text-gray-700"
                              ><small
                                ><svg
                                  aria-hidden="true"
                                  height="16"
                                  viewBox="0 0 16 16"
                                  version="1.1"
                                  width="16"
                                  data-view-component="true"
                                  class="octicon octicon-markdown v-align-bottom inline"
                                >
                                  <path
                                    fill-rule="evenodd"
                                    d="M14.85 3H1.15C.52 3 0 3.52 0 4.15v7.69C0 12.48.52 13 1.15 13h13.69c.64 0 1.15-.52 1.15-1.15v-7.7C16 3.52 15.48 3 14.85 3zM9 11H7V8L5.5 9.92 4 8v3H2V5h2l1.5 2L7 5h2v6zm2.99.5L9.5 8H11V5h2v3h1.5l-2.51 3.5z"
                                  ></path>
                                </svg>
                                Styling with Markdown is supported</small
                              >
                            </label>
                            <jet-input-error
                              :message="form.errors.description"
                              class="mt-2"
                            />
                          </div>
                        </div>
                        <fieldset>
                          <legend class="text-sm font-medium text-gray-900">
                            Privacy
                          </legend>
                          <div class="mt-2 space-y-5">
                            <div class="relative flex items-start">
                              <div class="absolute flex items-center h-5">
                                <input
                                  :checked="form.is_public === true"
                                  v-model="form.is_public"
                                  id="privacy-public"
                                  name="privacy"
                                  value="true"
                                  aria-describedby="privacy-public-description"
                                  type="radio"
                                  class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                />
                              </div>
                              <div class="pl-7 text-sm">
                                <label
                                  for="privacy-public"
                                  class="font-medium text-gray-900"
                                >
                                  Public access
                                </label>
                                <p id="privacy-public-description" class="text-gray-500">
                                  Everyone with the link will see this project.
                                </p>
                              </div>
                            </div>
                            <div>
                              <div class="relative flex items-start">
                                <div class="absolute flex items-center h-5">
                                  <input
                                    :checked="form.is_public === false"
                                    v-model="form.is_public"
                                    id="privacy-private-to-project"
                                    name="privacy"
                                    value="false"
                                    aria-describedby="privacy-private-to-project-description"
                                    type="radio"
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                  />
                                </div>
                                <div class="pl-7 text-sm">
                                  <label
                                    for="privacy-private-to-project"
                                    class="font-medium text-gray-900"
                                  >
                                    Private to project members
                                  </label>
                                  <p
                                    id="privacy-private-to-project-description"
                                    class="text-gray-500"
                                  >
                                    Only members of this project would be able to access.
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </fieldset>
                      </div>
                      <div class="pt-4 pb-6">
                        <div
                          v-if="form.is_public == true || form.is_public == 'true'"
                          class="flex text-sm"
                        >
                          <a
                            href="#"
                            class="group inline-flex items-center font-medium text-indigo-600 hover:text-indigo-900"
                          >
                            <LinkIcon
                              class="h-5 w-5 text-indigo-500 group-hover:text-indigo-900"
                              aria-hidden="true"
                            />
                            <span class="ml-2"> Copy link </span>
                          </a>
                        </div>
                        <div class="mt-4 flex text-sm">
                          <a
                            class="group inline-flex items-center text-gray-500 hover:text-gray-900"
                          >
                            <QuestionMarkCircleIcon
                              class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                              aria-hidden="true"
                            />
                            <span class="ml-2"> Learn more about sharing </span>
                          </a>
                        </div>
                        <div class="mt-4 flex text-sm">
                          <a
                            @click="toggleActivityDetails"
                            class="cursor-pointer group inline-flex items-center text-gray-500 hover:text-gray-900"
                          >
                            <ExclamationCircleIcon
                              class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                              aria-hidden="true"
                            />
                            <span class="ml-2"> Activity </span>
                          </a>
                        </div>
                        <activity-details :project="project" ref="activityDetailsElement"></activity-details>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="flex-shrink-0 px-4 py-4 flex justify-end">
                  <button
                    type="button"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    @click="open = false"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    @click="updateProject"
                  >
                    Save
                  </button>
                </div>
              </div>
            </div>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import { ref } from "vue";
import {
  Dialog,
  DialogOverlay,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
import { XIcon } from "@heroicons/vue/outline";
import { LinkIcon, PlusSmIcon, QuestionMarkCircleIcon, ExclamationCircleIcon } from "@heroicons/vue/solid";
import { Tab, TabGroup, TabList, TabPanel, TabPanels } from "@headlessui/vue";
import JetInputError from "@/Jetstream/InputError.vue";
import ActivityDetails from "@/Shared/ActivityDetails.vue";

export default {
  components: {
    Dialog,
    DialogOverlay,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
    JetInputError,
    ActivityDetails,
    Tab,
    TabGroup,
    TabList,
    TabPanel,
    TabPanels,
    LinkIcon,
    PlusSmIcon,
    QuestionMarkCircleIcon,
    ExclamationCircleIcon,
    XIcon,
  },
  props: ["project"],
  setup() {
    const activityDetailsElement = ref(null)
    return {
      activityDetailsElement
    }
  },
  data() {
    return {
      form: this.$inertia.form({
        _method: "PUT",
        name: this.project.name,
        description: this.project.description,
        error_message: null,
        team_id: this.project.team_id,
        owner_id: this.project.owner_id,
        color: this.project.color,
        starred: this.project.starred,
        is_public: this.project.is_public,
      }),
      open: false,
    };
  },
  methods: {
    toggleDetails() {
      this.open = !this.open;
    },
    updateProject() {
      this.form.owner_id = this.project.owner_id;
      this.form.team_id = this.project.team_id;
      this.form.post(route("projects.update", this.project.id), {
        preserveScroll: true,
        onSuccess: () => {},
        onError: (err) => console.error(err),
      });
    },
    toggleActivityDetails() {
      this.activityDetailsElement.toggleDetails()
    }
  },
};
</script>
