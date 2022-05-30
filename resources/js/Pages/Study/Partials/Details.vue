<template>
  <TransitionRoot as="template" :show="open">
    <Dialog as="div" class="fixed inset-0 overflow-hidden z-50">
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
                  <div class="py-6 px-4 sm:px-6" :style="[ (study.color && study.color!='') ? ('background-color:' + study.color)  : 'background-color:rgb(75,75,75)']">
                    <div class="flex items-center justify-between">
                      <DialogTitle class="text-lg font-medium text-white">
                        {{ study.name }}
                      </DialogTitle>
                      <div class="ml-3 h-7 flex items-center">
                        <button
                          type="button"
                          class="rounded-md hover:text-white"
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
                            for="study-name"
                            class="block text-sm font-medium text-gray-900"
                          >
                            Study name
                          </label>
                          <div class="mt-1">
                            <input
                              v-model="form.name"
                              type="text"
                              name="study-name"
                              id="study-name"
                              class="block w-full shadow-sm sm:text-sm focus:ring-gray-500 focus:border-gray-500 border-gray-300 rounded-md"
                            />
                          </div>
                          <jet-input-error
                            :message="form.errors.name"
                            class="mt-2"
                          />                          
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
                                      class="shadow-sm focus:ring-gray-500 focus:border-gray-500 block w-full sm:text-sm border-gray-300 rounded-md"
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
                        <div>
                          <label
                            for="study-name"
                            class="block text-sm font-medium text-gray-900"
                          >
                            Color
                          </label>
                          <color-picker v-model:pureColor="form.color"/>
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
                                  class="focus:ring-gray-500 h-4 w-4 text-gray-600 border-gray-300"
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
                                  Everyone with the link will see this study.
                                </p>
                              </div>
                            </div>
                            <div>
                              <div class="relative flex items-start">
                                <div class="absolute flex items-center h-5">
                                  <input
                                    :checked="form.is_public === false"
                                    v-model="form.is_public"
                                    id="privacy-private-to-study"
                                    name="privacy"
                                    value="false"
                                    aria-describedby="privacy-private-to-study-description"
                                    type="radio"
                                    class="focus:ring-gray-500 h-4 w-4 text-gray-600 border-gray-300"
                                  />
                                </div>
                                <div class="pl-7 text-sm">
                                  <label
                                    for="privacy-private-to-study"
                                    class="font-medium text-gray-900"
                                  >
                                    Private to study members
                                  </label>
                                  <p
                                    id="privacy-private-to-study-description"
                                    class="text-gray-500"
                                  >
                                    Only members of this study would be able to access.
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </fieldset>
                      </div>
                      <div class="pt-4 pb-6">
                        <div v-if="form.is_public == true || form.is_public == 'true'">
                          <label for="email" class="block text-sm font-medium text-gray-700">Public URL</label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <div class="relative flex items-stretch flex-grow focus-within:z-10">
                              <input @focus="$event.target.select()"  id="studyPublicURLCopy" v-model="study.public_url" type="text" class="rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300" />
                            </div>
                            <button @click="copyToClipboard(study.public_url, 'studyPublicURLCopy')" type="button" class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500">
                              <span><ClipboardCopyIcon class="h-5 w-5" aria-hidden="true" /></span>
                            </button>
                          </div>
                        </div>
                        <div v-else>
                          <div class="space-y-1">
                            <div class="relative flex items-start">
                              <div class="flex items-center h-5">
                                <input v-model="linkAccess" type="checkbox" class="focus:ring-gray-500 h-4 w-4 text-gray-600 border-gray-300 rounded" />
                              </div>
                              <div class="ml-3 text-sm">
                                <label class="font-medium text-gray-700">Any one with link</label>
                              </div>
                            </div>
                            <div v-if="linkAccess">
                              <div class="flex">
                                <div class="flex-grow">
                                  <input @focus="$event.target.select()" id="studyPrivateURLCopy" readonly type="text" :value="study.private_url" class="rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300" />
                                </div>
                                <button @click="copyToClipboard(study.private_url, 'studyPrivateURLCopy')" type="button" class="-ml-px relative inline-flex items-center space-x-2 px-2 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500">
                                  <span><ClipboardCopyIcon class="h-5 w-5" aria-hidden="true" /></span>
                                </button>
                              </div>
                              <div class="mt-3">
                                  <Listbox as="div" v-model="selectedAccessType">
                                    <div class="relative">
                                      <div class="inline-flex shadow-sm rounded-md divide-x divide-gray-600">
                                        <div class="relative z-0 inline-flex shadow-sm rounded-md divide-x divide-gray-600">
                                          <div class="relative inline-flex items-center bg-gray-500 py-2 pl-3 pr-4 border border-transparent rounded-l-md shadow-sm text-white">
                                            <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                            <p class="ml-2.5 text-sm font-medium">{{ selectedAccessType.title }}</p>
                                          </div>
                                          <ListboxButton class="relative inline-flex items-center bg-gray-500 p-2 rounded-l-none rounded-r-md text-sm font-medium text-white hover:bg-gray-600 focus:outline-none focus:z-10 focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 focus:ring-gray-500">
                                            <span class="sr-only">Change published status</span>
                                            <ChevronDownIcon class="h-5 w-5 text-white" aria-hidden="true" />
                                          </ListboxButton>
                                        </div>
                                      </div>

                                      <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                                        <ListboxOptions class="origin-top-right absolute z-10 left-0 mt-2 w-72 rounded-md shadow-lg overflow-hidden bg-white divide-y divide-gray-200 ring-1 ring-black ring-opacity-5 focus:outline-none">
                                          <ListboxOption as="template" v-for="option in publishingOptions" :key="option.title" :value="option" v-slot="{ active, selectedAccessType }">
                                            <li :class="[active ? 'text-white bg-gray-500' : 'text-gray-900', 'cursor-default select-none relative p-4 text-sm']">
                                              <div class="flex flex-col">
                                                <div class="flex justify-between">
                                                  <p :class="selectedAccessType ? 'font-semibold' : 'font-normal'">
                                                    {{ option.title }}
                                                  </p>
                                                  <span v-if="selectedAccessType" :class="active ? 'text-white' : 'text-gray-500'">
                                                    <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                                  </span>
                                                </div>
                                                <p :class="[active ? 'text-gray-200' : 'text-gray-500', 'mt-2']">
                                                  {{ option.description }}
                                                </p>
                                              </div>
                                            </li>
                                          </ListboxOption>
                                        </ListboxOptions>
                                      </transition>
                                    </div>
                                  </Listbox>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="mt-6 flex text-sm">
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
                        <study-activity :study="study" ref="activityDetailsElement"></study-activity>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="flex-shrink-0 px-4 py-4 flex justify-end">
                  <jet-action-message :on="form.recentlySuccessful" class="mr-3 py-2 text-green-200">
                      Saved.
                  </jet-action-message>
                  <jet-secondary-button
                    type="button"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    @click="open = false"
                  >
                    Cancel
                  </jet-secondary-button>
                  <jet-button
                    type="submit"
                    class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    @click="updateStudy"
                  >
                    Save
                  </jet-button>
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
import JetActionMessage from '@/Jetstream/ActionMessage.vue'
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
import StudyActivity from "@/Pages/Study/Partials/Activity.vue";
import { ColorPicker } from "vue3-colorpicker";
import "vue3-colorpicker/style.css";
import { Switch, SwitchGroup, SwitchLabel } from '@headlessui/vue'
import { ClipboardCopyIcon, CheckIcon, ChevronDownIcon } from '@heroicons/vue/solid'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from "@headlessui/vue";
import JetButton from "@/Jetstream/Button.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";

const publishingOptions = [
  { value: 'viewer', title: 'Viewer', description: 'Anyone on the internet with this link can view', current: true },
  { value: 'commentor', title: 'Commentor', description: 'Anyone on the internet with this link can comment', current: false },
  { value: 'editor', title: 'Editor', description: 'Anyone on the internet with this link can edit (sign in required)', current: false },
]

export default {
  components: {
    Dialog,
    DialogOverlay,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
    JetInputError,
    Listbox,
    ListboxButton,
    ListboxLabel,
    ListboxOption,
    ListboxOptions,
    StudyActivity,
    Tab,
    JetActionMessage,
    JetButton,
    JetSecondaryButton,
    TabGroup,
    TabList,
    TabPanel,
    TabPanels,
    LinkIcon,
    PlusSmIcon,
    QuestionMarkCircleIcon,
    ExclamationCircleIcon,
    XIcon,
    ColorPicker,
    Switch,
    SwitchGroup,
    SwitchLabel,
    ClipboardCopyIcon,
    CheckIcon,
    ChevronDownIcon,
  },
  props: ["study"],
  setup() {
    const activityDetailsElement = ref(null)
    return {
      publishingOptions,
      activityDetailsElement
    }
  },
  data() {
    return {
      form: this.$inertia.form({
        _method: "PUT",
        name: this.study.name,
        description: this.study.description,
        error_message: null,
        team_id: this.study.team_id,
        owner_id: this.study.owner_id,
        color: this.study.color,
        starred: this.study.starred,
        is_public: this.study.is_public,
        access: this.study.access,
        access_type: this.study.access_type,
      }),
      open: false,
      selectedAccessType: publishingOptions.filter( option => option.value == this.study.access_type)[0],
      linkAccess : this.study.access == 'link'
    };
  },
  methods: {
    toggleDetails() {
      this.open = !this.open;
    },
    updateStudy() {
      this.form.owner_id = this.study.owner_id;
      this.form.team_id = this.study.team_id;
      this.form.project_id = this.study.project_id;
      if(this.linkAccess){
        this.form.access = "link"
        this.form.access_type = this.selectedAccessType.value
      }else{
        this.form.access = "restricted"
      }
      this.form.post(route("dashboard.study.update", this.study.id), {
        preserveScroll: true,
        onSuccess: () => { this.open = false },
        onError: (err) => console.error(err),
      });
    },
    toggleActivityDetails() {
      this.activityDetailsElement.toggleDetails()
    }
  },
};
</script>
