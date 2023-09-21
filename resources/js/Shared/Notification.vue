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
                                    <h2
                                        class="text-2xl font-bold dark:text-white text-center"
                                    >
                                        Notifications
                                    </h2>
                                    <div
                                        class="inline-flex items-center justify-center w-full"
                                    >
                                        <hr
                                            class="w-64 h-1 my-8 bg-gray-200 border-0 rounded dark:bg-gray-700"
                                        />
                                        <div
                                            class="absolute px-4 -translate-x-1/2 bg-white left-1/2 dark:bg-gray-900"
                                        >
                                            <svg
                                                class="w-4 h-4 text-primary-dark dark:text-primary-dark"
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
                                            :key="notification.title"
                                        >
                                            <div
                                                class="relative rounded-lg border border-gray-300 bg-gray-50 px-6 py-5 shadow-sm items-top m-1 shadow-md transition-all"
                                            >
                                                <div
                                                    class="text-m font-semi-bold text-gray-900"
                                                    v-html="
                                                        notification.data.title
                                                    "
                                                ></div>
                                                <div
                                                    class="text-sm text-gray-500"
                                                    v-html="
                                                        notification.data
                                                            .message
                                                    "
                                                ></div>
                                                <div
                                                    class="flex justify-between mt-2"
                                                >
                                                    <button
                                                        type="button"
                                                        @click="
                                                            markNotificationAsRead(
                                                                notification
                                                            )
                                                        "
                                                        class="rounded-md text-sm font-medium text-teal-600 hover:text-teal-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                                    >
                                                        Mark As Read
                                                    </button>
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
                                                this.notifications.length == 0
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
</template>

<script>
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
} from "@headlessui/vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";
import JetButton from "@/Jetstream/Button.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import { EllipsisVerticalIcon } from "@heroicons/vue/20/solid";
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
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        EllipsisVerticalIcon,
    },
    props: [],
    data() {
        return {
            open: false,
            info: {},
            notifications: this.$page.props.user.notifications,
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
        toggleShowNotificationDialog(notifications) {
            this.info.title = "";
            this.info.body = "";
            this.open = !this.open;
        },
        markNotificationAsRead(notification) {
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
                            this.$page.props.user.notifications;
                    },
                    onError: (err) => console.error(err),
                }
            );
        },
        markAllNotificationAsRead() {
            this.markAllAsReadForm.post(
                route("users.markAllNotificationAsRead"),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.markAllAsReadForm.reset();
                        this.notifications =
                            this.$page.props.user.notifications;
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
