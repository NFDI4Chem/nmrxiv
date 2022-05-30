<template>
    <jet-dialog-modal
        :show="editAnnouncementDialog"
        @close="editAnnouncementDialog = false"
    >
        <template #title> Edit Announcement </template>

        <template #content>
            <jet-validation-errors class="mb-4" />
            <div class="relative z-0 mt-1 rounded-lg cursor-pointer">
                <div
                    class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6"
                >
                    <div class="sm:col-span-6">
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                        >
                            Title
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input
                                v-model="editAnnouncementForm.title"
                                type="text"
                                name="name"
                                id="name"
                                autocomplete="off"
                                class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                            />
                        </div>
                         <jet-input-error :message="editAnnouncementForm.errors.title" class="mt-2" />
                    </div>
                    <div class="sm:col-span-6">
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                        >
                            Message
                        </label>
                        <div mt-1 flex rounded-md shadow-sm>
                            <textarea
                                v-model="editAnnouncementForm.message"
                                id="description"
                                name="description"
                                placeholder="Message to the users.."
                                rows="3"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            />
                        </div>
                        <jet-input-error
                            :message="editAnnouncementForm.errors.message"
                            class="mt-2"
                        />
                        <div class="py-2">
                            <label
                                class="block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"
                            >
                                Status
                            </label>
                            <toggle-button
                                v-model:enabled="editAnnouncementForm.enabled"
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
                            v-model="editAnnouncementForm.start_time"
                        ></Datepicker>
                        <jet-input-error :message="editAnnouncementForm.errors.start_time" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            End Time
                        </label>
                        <Datepicker
                            v-model="editAnnouncementForm.end_time"
                        ></Datepicker>
                        <jet-input-error :message="editAnnouncementForm.errors.end_time" class="mt-2" />
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <jet-secondary-button @click="onCancel">
                Cancel
            </jet-secondary-button>

            <jet-button
                class="ml-2"
                @click="editAnnouncement"
                :class="{ 'opacity-25': editAnnouncementForm.processing }"
                :disabled="editAnnouncementForm.processing"
            >
                Update
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
import JetInputError from "@/Jetstream/InputError.vue";
import ToggleButton from "@/Shared/ToggleButton.vue";
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
        JetButton,
        Link,
        CheckCircleIcon,
        ChevronRightIcon,
        JetInputError,
        Datepicker,
        ToggleButton,
    },

    data() {
        return {
            selectedAnnouncement: null,
            editAnnouncementForm: this.$inertia.form({
                _method: "POST",
                id: "",
                title: "",
                message: "",
                enabled: null,
                error_message: null,
                creator_id: null,
                start_time: null,
                end_time: null,
            }),
            editAnnouncementDialog: false,
        };
    },

    props: [],

    methods: {
        editAnnouncement() {
            //this.editAnnouncementForm.creator_id = this.$page.props.user.id;
            this.editAnnouncementForm.post(
                route("console.announcements.edit", this.editAnnouncementForm.id),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.editAnnouncementDialog = false;
                        this.editAnnouncementForm.reset();
                    },
                    onError: (err) => console.error(err),
                }
            );
        },
        toggleEditAnnouncementDialog(announcement) {
            this.selectedAnnouncement = announcement;
            this.editAnnouncementForm.id = announcement.id;
            this.editAnnouncementForm.title = announcement.title;
            this.editAnnouncementForm.message = announcement.message;
            if(announcement.status == 'active' || announcement.status == 'Active')
              this.editAnnouncementForm.enabled = true; 
            else 
              this.editAnnouncementForm.enabled = false;
            this.editAnnouncementForm.start_time = announcement.start_time;
            this.editAnnouncementForm.end_time = announcement.end_time;
            this.editAnnouncementDialog = !this.editAnnouncementDialog;
        },
        onCancel() {
            this.editAnnouncementDialog = false;
            this.editAnnouncementForm.clearErrors();
            this.$page.props.errors = new Object();
        },
    },
};
</script>
