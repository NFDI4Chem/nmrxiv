<template>
    <app-layout title="Announcements and Notifications">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="flex flex-nowrap justify-between py-6">
                        <div>
                            <bread-crumbs :pages="pages" />
                            <div
                                class="mt-2 md:flex md:items-center md:justify-between"
                            >
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3"
                                    >
                                        Announcements
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div class="py-12 px-10">
            <span v-if="announcements.length > 0">
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
                        type="button"
                        @click="openAnnouncementCreateDialog()"
                    >
                        <span>Create</span>&nbsp;
                        <span class="hidden md:inline">Announcement</span>
                    </Button>
                </div>
                <div>
                    <div class="bg-white rounded-md shadow overflow-x-auto">
                        <table
                            class="divide-y divide-gray-200 w-full table-fixed"
                        >
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        colspan="3"
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Title
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="announcement in announcements"
                                    :key="announcement.id"
                                >
                                    <td colspan="3" class="py-4">
                                        <div class="ml-4">
                                            <h3
                                                class="text-lg leading-6 font-medium text-gray-900"
                                            >
                                                {{ announcement.title }}
                                            </h3>
                                            <p
                                                class="mt-1 mb-3 text-sm text-blue-gray-900 break-normal"
                                            >
                                                {{ announcement.message }}
                                            </p>
                                            <label
                                                class="block text-sm mt-2 font-medium text-gray-500"
                                            >
                                                Created by
                                            </label>
                                            <p
                                                class="mt-1 text-md text-blue-gray-900"
                                            >
                                                {{ announcement.created_by }}
                                            </p>
                                            <label
                                                class="block text-sm mt-2 font-medium text-gray-500"
                                            >
                                                From
                                            </label>
                                            <p
                                                class="mt-1 text-sm text-blue-gray-900"
                                            >
                                                {{
                                                    formatDateTime(
                                                        announcement.start_time
                                                    )
                                                }}
                                            </p>
                                            <label
                                                class="block text-sm mt-2 font-medium text-gray-500"
                                            >
                                                To
                                            </label>
                                            <p
                                                class="mt-1 text-sm text-blue-gray-900"
                                            >
                                                {{
                                                    formatDateTime(
                                                        announcement.end_time
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </td>
                                    <td
                                        class="px-5 py-4 text-center whitespace-nowrap"
                                    >
                                        <span
                                            v-if="
                                                announcement.status ==
                                                    'active' ||
                                                announcement.status == 'Active'
                                            "
                                            class="px-4 py-2 inline-flex text-md leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                                        >
                                            Active
                                        </span>
                                        <span
                                            v-else
                                            class="px-4 py-2 inline-flex text-md leading-5 font-semibold rounded-full bg-red-100 text-green-800"
                                        >
                                            Inactive
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
                                    >
                                        <button
                                            type="button"
                                            class="inline-flex items-center p-1 border border-transparent rounded-md mr-2 shadow-sm text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300"
                                            @click="
                                                openAnnouncementEditDialog(
                                                    announcement
                                                )
                                            "
                                        >
                                            <icon name="edit" />
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex items-center p-1 border border-transparent rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
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
                                Are you sure you want to delete this
                                announcement?
                                <div class="mt-4"></div>
                            </template>

                            <template #footer>
                                <jet-secondary-button @click="closeModal">
                                    Cancel
                                </jet-secondary-button>

                                <jet-danger-button
                                    class="ml-2"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    @click="deleteAnnouncement(announcementId)"
                                >
                                    Delete Announcement
                                </jet-danger-button>
                            </template>
                        </jet-dialog-modal>
                    </div>
                </div>
            </span>
            <span v-else>
                <button
                    type="button"
                    class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    @click="openAnnouncementCreateDialog()"
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
                        Create a new announcement
                    </span>
                </button>
            </span>
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
    props: {
        announcements: Array,
        filters: Object,
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
    data() {
        return {
            form: this.$inertia.form({}),
            confirmingAnnouncementDeletion: false,
            announcementId: null,
            pages: [
                { name: "Console", route: "console", current: false },
                {
                    name: "Announcements",
                    route: "console.announcements",
                    current: true,
                },
            ],
        };
    },
    methods: {
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
            this.form.delete(route("console.announcements.destroy", id), {
                onSuccess: () => {
                    this.closeModal();
                    this.announcementId = null;
                },
                onError: (err) => console.error(err),
            });
        },
        confirmAnnouncementDeletion(id) {
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
