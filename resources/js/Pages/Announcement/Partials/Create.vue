<template>
  <jet-dialog-modal
    :show="createAnnouncementDialog"
    @close="createAnnouncementDialog = false"
  >
    <template #title> New Announcement </template>
    <template #content>
      <jet-validation-errors class="mb-4" />
      <div class="relative z-0 mt-1 rounded-lg cursor-pointer">
        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
          <div class="sm:col-span-6">
            <label
              for="name"
              class="
                block
                text-sm
                font-medium
                text-gray-700
                after:content-['*'] after:ml-0.5 after:text-red-500
              "
            >
              Title
            </label>
            <div class="mt-1 flex rounded-md shadow-sm">
              <input
                v-model="createAnnouncementForm.title"
                type="text"
                name="name"
                id="name"
                autocomplete="off"
                class="
                  flex-1
                  focus:ring-indigo-500 focus:border-indigo-500
                  block
                  w-full
                  min-w-0
                  rounded
                  sm:text-sm
                  border-gray-300
                "
              />
            </div>
          </div>
          <div class="sm:col-span-6">
            <label
              for="name"
              class="
                block
                text-sm
                font-medium
                text-gray-700
                after:content-['*'] after:ml-0.5 after:text-red-500
              "
            >
              Message
            </label>
            <div>
              <textarea
                v-model="createAnnouncementForm.message"
                id="description"
                name="description"
                placeholder="Type your message.."
                rows="3"
                class="
                  shadow-sm
                  focus:ring-indigo-500 focus:border-indigo-500
                  block
                  w-full
                  sm:text-sm
                  border-gray-300
                  rounded-md
                "
              />
            </div>
          </div>
        </div>

        <div class="sm grid grid-cols-2 gap-4 pt-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">
              Start Time
            </label>
            <Datepicker
              v-model="createAnnouncementForm.start_time"
            ></Datepicker>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">
              End Time
            </label>
            <Datepicker v-model="createAnnouncementForm.end_time"></Datepicker>
          </div>
        </div>
      </div>
    </template>

    <template #footer>
      <jet-secondary-button @click="createAnnouncementDialog = false">
        Cancel
      </jet-secondary-button>

      <jet-button
        class="ml-2"
        @click="createAnnouncement"
        :class="{ 'opacity-25': createAnnouncementForm.processing }"
        :disabled="createAnnouncementForm.processing"
      >
        Create
      </jet-button>
    </template>
  </jet-dialog-modal>
</template>

<script>
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import { CheckCircleIcon, ChevronRightIcon } from "@heroicons/vue/solid";
import { Link } from "@inertiajs/inertia-vue3";
import { AtSymbolIcon, CodeIcon, LinkIcon } from "@heroicons/vue/solid";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import { ref } from "vue";
import {
  Switch,
  SwitchDescription,
  SwitchGroup,
  SwitchLabel,
} from "@headlessui/vue";
import Datepicker from "vue3-date-time-picker";
import "vue3-date-time-picker/dist/main.css";

export default {
  components: {
    Switch,
    SwitchDescription,
    SwitchGroup,
    SwitchLabel,
    AtSymbolIcon,
    CodeIcon,
    LinkIcon,
    JetDialogModal,
    JetSecondaryButton,
    JetValidationErrors,
    JetButton,
    Link,
    CheckCircleIcon,
    ChevronRightIcon,
    Datepicker,
  },

  data() {
    return {
      createAnnouncementForm: this.$inertia.form({
        _method: "POST",
        title: "",
        message: "",
        error_message: null,
        creator_id: null,
        start_time: null,
        end_time: null,
      }),
      createAnnouncementDialog: false,
    };
  },

  props: [],

  methods: {
    createAnnouncement() {
      this.createAnnouncementForm.creator_id = this.$page.props.user.id;
      this.createAnnouncementForm.post(route("announcements.create"), {
        preserveScroll: true,
        onSuccess: () => {
          this.createAnnouncementDialog = false;
          this.createAnnouncementForm.reset();
        },
        onError: (err) => console.error(err),
      });
    },
    toggleCreateAnnouncementDialog() {
      this.createAnnouncementDialog = !this.createAnnouncementDialog;
    },
  },
};
</script>
