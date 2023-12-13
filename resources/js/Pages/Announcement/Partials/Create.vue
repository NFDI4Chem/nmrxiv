<template>
    <jet-dialog-modal
        :show="createAnnouncementDialog"
        @close="createAnnouncementDialog = false"
    >
        <template #title> New Announcement </template>
        <template #content>
            <div class="relative z-0 mt-1 rounded-lg cursor-pointer">
                <div
                    class="mt-6 grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6"
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
                                id="name"
                                v-model="createAnnouncementForm.title"
                                type="text"
                                name="name"
                                autocomplete="off"
                                class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                            />
                        </div>
                        <jet-input-error
                            :message="createAnnouncementForm.errors.title"
                            class="mt-2"
                        />
                    </div>
                    <div class="sm:col-span-6">
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                        >
                            Message
                        </label>
                        <div>
                            <textarea
                                id="description"
                                v-model="createAnnouncementForm.message"
                                name="description"
                                placeholder="Type your message.."
                                rows="3"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            />
                        </div>
                        <jet-input-error
                            :message="createAnnouncementForm.errors.message"
                            class="mt-2"
                        />
                    </div>
                </div>
                <div class="py-2">
                    <label
                        class="block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"
                    >
                        Status
                    </label>
                    <toggle-button
                        v-model:enabled="createAnnouncementForm.enabled"
                    />
                </div>

                <div class="sm grid grid-cols-2 gap-4 pt-1">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                        >
                            Start Time
                        </label>
                        <Datepicker
                            v-model="createAnnouncementForm.start_time"
                        ></Datepicker>
                        <jet-input-error
                            :message="createAnnouncementForm.errors.start_time"
                            class="mt-2"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                        >
                            End Time
                        </label>
                        <Datepicker
                            v-model="createAnnouncementForm.end_time"
                        ></Datepicker>
                        <jet-input-error
                            :message="createAnnouncementForm.errors.end_time"
                            class="mt-2"
                        />
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
                :class="{ 'opacity-25': createAnnouncementForm.processing }"
                :disabled="createAnnouncementForm.processing"
                @click="createAnnouncement"
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
import { CheckCircleIcon, ChevronRightIcon } from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/vue3";
import {
    AtSymbolIcon,
    CodeBracketIcon,
    LinkIcon,
} from "@heroicons/vue/24/solid";
import JetInputError from "@/Jetstream/InputError.vue";
import { ref } from "vue";
import ToggleButton from "@/Shared/ToggleButton.vue";
import {
    Switch,
    SwitchDescription,
    SwitchGroup,
    SwitchLabel,
} from "@headlessui/vue";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

export default {
    components: {
        Switch,
        SwitchDescription,
        SwitchGroup,
        SwitchLabel,
        AtSymbolIcon,
        CodeBracketIcon,
        LinkIcon,
        JetDialogModal,
        JetSecondaryButton,
        JetButton,
        Link,
        CheckCircleIcon,
        ChevronRightIcon,
        Datepicker,
        ToggleButton,
        JetInputError,
    },

    props: [],

    data() {
        return {
            createAnnouncementForm: this.$inertia.form({
                _method: "POST",
                title: "",
                message: "",
                enabled: true,
                error_message: null,
                creator_id: null,
                start_time: new Date(),
                end_time: this.setEndTime(),
            }),
            createAnnouncementDialog: false,
        };
    },

    methods: {
        setEndTime() {
            var start_time = new Date();
            var end_time = new Date();
            end_time.setDate(start_time.getDate() + 7);
            return end_time;
        },
        createAnnouncement() {
            this.createAnnouncementForm.creator_id = this.$page.props.auth.user.id;
            this.createAnnouncementForm.post(
                route("console.announcements.create"),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.createAnnouncementDialog = false;
                        this.createAnnouncementForm.reset();
                    },
                    onError: (err) => console.error(err),
                }
            );
        },
        toggleCreateAnnouncementDialog() {
            this.createAnnouncementDialog = !this.createAnnouncementDialog;
        },
        onCancel() {
            this.createAnnouncementDialog = false;
            this.createAnnouncementForm.clearErrors();
            this.$page.props.errors = new Object();
        },
    },
};
</script>
