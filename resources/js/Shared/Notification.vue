<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="fixed inset-0 z-50" @close="open = false">
            <TransitionChild
                as="template"
                enter="ease-in-out duration-500"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in-out duration-500"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                />
            </TransitionChild>

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div
                        class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10"
                    >
                        <TransitionChild
                            as="template"
                            enter="transform transition ease-in-out duration-500 sm:duration-700"
                            enter-from="translate-x-full"
                            enter-to="translate-x-0"
                            leave="transform transition ease-in-out duration-500 sm:duration-700"
                            leave-from="translate-x-0"
                            leave-to="translate-x-full"
                        >
                            <DialogPanel
                                class="pointer-events-auto relative w-screen max-w-md"
                            >
                                <TransitionChild
                                    as="template"
                                    enter="ease-in-out duration-500"
                                    enter-from="opacity-0"
                                    enter-to="opacity-100"
                                    leave="ease-in-out duration-500"
                                    leave-from="opacity-100"
                                    leave-to="opacity-0"
                                >
                                    <div
                                        class="absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4"
                                    >
                                        <button
                                            type="button"
                                            class="rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                                            @click="open = false"
                                        >
                                            <span class="sr-only"
                                                >Close panel</span
                                            >
                                            <XMarkIcon
                                                class="h-6 w-6"
                                                aria-hidden="true"
                                            />
                                        </button>
                                    </div>
                                </TransitionChild>
                                <div
                                    class="flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl"
                                >
                                    <h2 class="text-2xl font-bold text-center">
                                        Notifications
                                    </h2>
                                    <div
                                        class="inline-flex items-center justify-center w-full"
                                    >
                                        <hr
                                            class="w-64 h-1 my-8 bg-gray-200 border-0 rounded dark:bg-gray-700"
                                        />
                                        <div
                                            class="absolute px-4 -translate-x-1/2 bg-white left-1/2"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor"
                                                viewBox="0 0 18 14"
                                            >
                                                <path
                                                    d="M6 0H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3H2a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Zm10 0h-4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3h-1a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 overflow-y-auto m-2">
                                        <div
                                            v-if="notifications.length == 0"
                                            class="text-m font-semi-bold text-center text-gray-500 italic"
                                        >
                                            No unread notifications.
                                        </div>
                                        <div
                                            v-for="notification in notifications"
                                            :key="notification.id"
                                        >
                                            <div
                                                class="relative rounded-lg border border-gray-300 bg-gray-50 px-6 py-5 shadow-sm items-top m-1 shadow-md transition-all"
                                            >
                                                <div
                                                    class="text-m font-semi-bold text-gray-900"
                                                    v-html="
                                                        sanitizeHtml(
                                                            notification.data
                                                                .title
                                                        )
                                                    "
                                                ></div>
                                                <div
                                                    class="text-sm text-gray-500"
                                                    v-html="
                                                        sanitizeHtml(
                                                            notification.data
                                                                .message
                                                        )
                                                    "
                                                ></div>
                                                <div
                                                    class="flex justify-between mt-2"
                                                >
                                                    <div class="flex gap-3">
                                                        <button
                                                            v-if="
                                                                isWhatsNewNotification(
                                                                    notification
                                                                )
                                                            "
                                                            type="button"
                                                            class="rounded-md text-sm font-medium text-teal-600 hover:text-teal-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                                            @click="
                                                                openWhatsNewNotification(
                                                                    notification
                                                                )
                                                            "
                                                        >
                                                            View
                                                        </button>
                                                        <button
                                                            type="button"
                                                            class="rounded-md text-sm font-medium text-teal-600 hover:text-teal-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                                            @click="
                                                                markNotificationAsRead(
                                                                    notification
                                                                )
                                                            "
                                                        >
                                                            Mark As Read
                                                        </button>
                                                    </div>
                                                    <div
                                                        class="text-sm text-gray-400 justify-end italic"
                                                    >
                                                        {{
                                                            calculateDaysDifference(
                                                                notification.created_at
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr
                                        class="h-px my-1 bg-gray-200 border-0 dark:bg-gray-700"
                                    />
                                    <div class="flex justify-end m-2">
                                        <jet-secondary-button
                                            type="button"
                                            @click="open = false"
                                        >
                                            Close
                                        </jet-secondary-button>
                                        <jet-button
                                            type="button"
                                            class="ml-4"
                                            :disabled="
                                                notifications.length == 0
                                            "
                                            @click="markAllNotificationAsRead()"
                                        >
                                            Mark All As Read
                                        </jet-button>
                                    </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
    <TransitionRoot as="template" :show="whatsNewOpen">
        <Dialog as="div" class="fixed inset-0 z-[60]" @close="closeWhatsNew">
            <TransitionChild
                as="template"
                enter="ease-out duration-200"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-150"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-gray-900 bg-opacity-60" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div
                    class="flex min-h-full items-center justify-center p-4 text-center"
                >
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-200"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100"
                        leave="ease-in duration-150"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <DialogPanel
                            class="w-full max-w-3xl transform overflow-hidden rounded-lg bg-white text-left align-middle shadow-xl transition-all"
                        >
                            <div
                                class="flex items-start justify-between border-b border-gray-200 px-6 py-5"
                            >
                                <div>
                                    <DialogTitle
                                        as="h2"
                                        class="text-2xl font-bold text-gray-900"
                                    >
                                        What's New
                                    </DialogTitle>
                                    <p
                                        v-if="
                                            selectedWhatsNew?.data
                                                ?.release_version
                                        "
                                        class="mt-1 text-sm font-medium text-teal-700"
                                    >
                                        {{
                                            selectedWhatsNew.data
                                                .release_version
                                        }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="rounded-md text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-teal-500"
                                    @click="closeWhatsNew"
                                >
                                    <span class="sr-only">Close</span>
                                    <XMarkIcon
                                        class="h-6 w-6"
                                        aria-hidden="true"
                                    />
                                </button>
                            </div>
                            <div class="max-h-[65vh] overflow-y-auto px-6 py-5">
                                <h3
                                    class="text-lg font-semibold text-gray-900"
                                    v-html="
                                        sanitizeHtml(
                                            selectedWhatsNew?.data?.title
                                        )
                                    "
                                ></h3>
                                <div
                                    class="prose prose-sm mt-4 max-w-none text-gray-700"
                                    v-html="
                                        md(
                                            selectedWhatsNew?.data
                                                ?.release_notes ||
                                                selectedWhatsNew?.data?.message
                                        )
                                    "
                                ></div>
                            </div>
                            <div
                                class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4"
                            >
                                <jet-secondary-button
                                    type="button"
                                    @click="closeWhatsNew"
                                >
                                    Remind Me Later
                                </jet-secondary-button>
                                <jet-button
                                    type="button"
                                    @click="
                                        markNotificationAsRead(selectedWhatsNew)
                                    "
                                >
                                    Mark As Read
                                </jet-button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script>
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";
import JetButton from "@/Jetstream/Button.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
export default {
    components: {
        Dialog,
        DialogPanel,
        DialogTitle,
        TransitionChild,
        TransitionRoot,
        XMarkIcon,
        JetButton,
        JetSecondaryButton,
    },
    props: [],
    data() {
        return {
            open: false,
            whatsNewOpen: false,
            selectedWhatsNew: null,
            info: {},
            notifications: this.$page.props.auth.user?.notifications,
            notificationForm: this.$inertia.form({
                _method: "POST",
                id: "",
                title: "",
                message: "",
                user_id: "",
            }),
            markAllAsReadForm: this.$inertia.form({
                _method: "POST",
            }),
        };
    },
    methods: {
        toggleShowNotificationDialog() {
            this.info.title = "";
            this.info.body = "";
            this.open = !this.open;
        },
        markNotificationAsRead(notification) {
            if (!notification) {
                return;
            }
            this.notificationForm.user_id = notification.notifiable_id;
            this.notificationForm.id = notification.id;
            this.notificationForm.title = notification.data["title"];
            this.notificationForm.message = notification.data["message"];
            this.notificationForm.post(
                route(
                    "users.markNotificationAsRead",
                    notification.notifiable_id
                ),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.notificationForm.reset();
                        this.notifications =
                            this.$page.props.auth.user?.notifications;
                        if (this.selectedWhatsNew?.id === notification.id) {
                            this.closeWhatsNew();
                        }
                    },
                    onError: (err) => console.error(err),
                }
            );
        },
        isWhatsNewNotification(notification) {
            return notification?.data?.kind === "whats_new";
        },
        openWhatsNewNotification(notification) {
            this.selectedWhatsNew = notification;
            this.whatsNewOpen = true;
        },
        closeWhatsNew() {
            this.whatsNewOpen = false;
            this.selectedWhatsNew = null;
        },
        markAllNotificationAsRead() {
            this.markAllAsReadForm.post(
                route("users.markAllNotificationAsRead"),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.markAllAsReadForm.reset();
                        this.notifications =
                            this.$page.props.auth.user?.notifications;
                    },
                    onError: (err) => console.error(err),
                }
            );
        },
        calculateDaysDifference(createdDate) {
            var createdDateObj = new Date(createdDate);
            var currentDate = new Date();
            var differenceInMs = currentDate - createdDateObj;
            var differenceInDays = Math.floor(
                differenceInMs / (1000 * 60 * 60 * 24)
            );
            if (differenceInDays === 0) {
                return "today";
            } else if (differenceInDays === 1) {
                return "yesterday";
            } else {
                return `${differenceInDays} days ago`;
            }
        },
    },
};
</script>
