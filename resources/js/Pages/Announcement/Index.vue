<template>
    <app-layout title="Announcements and Notifications">
        <template #header>
            <div>
                <bread-crumbs :pages="pages" />
                <div class="mt-2 md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <div
                            class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3"
                        >
                            Announcements
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div class="py-12 px-10">
            <div class="mb-6 flex justify-between items-center">
                <!-- Search -->
                <div class="flex items-center">
                    <div class="flex w-full bg-white shadow rounded">
                        <input
                            v-model="search"
                            class="relative w-full border-0 px-6 py-3 rounded-r focus:shadow-outline"
                            autocomplete="off"
                            type="text"
                            name="search"
                            placeholder="Search…"
                        />
                    </div>
                    <button
                        class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500"
                        type="button"
                        @click="reset()"
                    >
                        Reset
                    </button>
                </div>
                <Button
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                    @click="openAnnouncementCreateDialog()"
                    type="button"
                >
                    <span>Create</span>&nbsp;
                    <span class="hidden md:inline">Announcement</span>
                </Button>
            </div>
            <div>
                <div class="bg-white rounded-md shadow overflow-x-auto">
                    <table class="divide-y divide-gray-200 w-full table-fixed">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Title
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Message
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Status
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Start Time
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    End Time
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Created By
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Edit
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Delete
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="announcement in announcements"
                                :key="announcement.id"
                            >
                                <td class="py-4 whitespace-nowrap">
                                    <div class="ml-4">
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ announcement.title }}
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="py-4 text-sm text-gray-500 break-normal"
                                >
                                    <div class="ml-4">
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ announcement.message }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span
                                        v-if="
                                            announcement.status == 'active' ||
                                            announcement.status == 'Active'
                                        "
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                                    >
                                        Active
                                    </span>
                                    <span
                                        v-else
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-green-800"
                                    >
                                        Inactive
                                    </span>
                                </td>
                                <td class="py-4 whitespace-nowrap">
                                    <div class="ml-4">
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ announcement.start_time }}
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 whitespace-nowrap">
                                    <div class="ml-4">
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ announcement.end_time }}
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 whitespace-nowrap">
                                    <div class="ml-4">
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ announcement.created_by }}
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
                                >
                                    <button
                                        type="button"
                                        class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300"
                                        @click="
                                            openAnnouncementEditDialog(
                                                announcement
                                            )
                                        "
                                    >
                                        <icon name="edit" />
                                    </button>
                                </td>
                                <td
                                    class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
                                >
                                    <button
                                        type="button"
                                        class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                        @click="
                                            confirmAnnouncementDeletion(
                                                announcement.id
                                            )
                                        "
                                    >
                                        <icon name="delete" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Delete Announcement Confirmation Modal -->
                    <jet-dialog-modal
                        :show="confirmingAnnouncementDeletion"
                        @close="closeModal"
                    >
                        <template #title> Delete Announcement </template>

                        <template #content>
                            Are you sure you want to delete this announcement?
                            <div class="mt-4"></div>
                        </template>

                        <template #footer>
                            <jet-secondary-button @click="closeModal">
                                Cancel
                            </jet-secondary-button>

                            <jet-danger-button
                                class="ml-2"
                                @click="deleteAnnouncement(announcementId)"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Delete Announcement
                            </jet-danger-button>
                        </template>
                    </jet-dialog-modal>
                </div>
            </div>
            <announcement-create
                ref="announcementCreateElement"
            ></announcement-create>
            <announcement-edit
                ref="announcementEditElement"
            ></announcement-edit>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import BreadCrumbs from "../../Jetstream/BreadCrumbs.vue";
import Icon from "@/Shared/Icon.vue";
import AnnouncementCreate from "@/Pages/Announcement/Partials/Create.vue";
import AnnouncementEdit from "@/Pages/Announcement/Partials/Edit.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import { ref, watch } from "vue";
import { Inertia } from "@inertiajs/inertia";
import ToggleButton from "@/Shared/ToggleButton.vue";

export default {
    components: {
        AppLayout,
        BreadCrumbs,
        Icon,
        AnnouncementCreate,
        AnnouncementEdit,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
        JetDialogModal,
        ToggleButton,
    },
    setup() {
        const announcementCreateElement = ref(null);
        const announcementEditElement = ref(null);
        const search = ref("");
        watch(search, (value) => {
            Inertia.get(
                "announcements",
                { search: value },
                {
                    preserveState: true,
                }
            );
        });
        return {
            announcementCreateElement,
            announcementEditElement,
            search,
        };
    },
    props: {
        announcements: Array,
        filters: Object,
    },
    data() {
        return {
            form: this.$inertia.form({}),
            confirmingAnnouncementDeletion: false,
            announcementId: null,
            pages: [
                { name: "Console", route: "console", current: false },
                {
                    name: "Announcements",
                    route: "announcements",
                    current: true,
                },
            ],
        };
    },
    methods: {
        // convertDate(date){
        //     if(date){
        //         date = date.getDate();
        //         date.toDateString();
        //         return date;
        //     }
        //     return null;
        // },
        reset() {
            this.search = null;
        },
        openAnnouncementCreateDialog() {
            this.announcementCreateElement.toggleCreateAnnouncementDialog();
        },
        openAnnouncementEditDialog(announcement) {
            this.announcementEditElement.toggleEditAnnouncementDialog(
                announcement
            );
        },
        deleteAnnouncement(id) {
            this.form.delete(route("announcements.destroy", id), {
                onSuccess: () => {
                    this.closeModal();
                    this.announcementId = null;
                },
                onError: (err) => console.error(err),
            });
        },
        confirmAnnouncementDeletion(id) {
            console.log("confirm method called for " + id);
            this.announcementId = id;
            this.confirmingAnnouncementDeletion = true;
        },
        closeModal() {
            this.confirmingAnnouncementDeletion = false;
            this.announcementId = null;
        },
    },
};
</script>
