<template>
    <!--  Appear when clicked on any particular notification -->
    <div
        v-if="show && selectedNotificationForm"
        aria-live="assertive"
        class="fixed inset-0 z-10 flex items-end py-0 pointer-events-none sm:p-6 sm:items-start"
    >
        <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
            <!-- Notification panel, dynamically insert this into the live region when it needs to be displayed -->
            <transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    class="max-w-sm w-full bg-yellow-100 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
                >
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <EnvelopeIcon
                                    class="h-6 w-6 text-teal-600"
                                    aria-hidden="true"
                                />
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ selectedNotificationForm.title }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ selectedNotificationForm.message }}
                                </p>
                                <div class="mt-3 flex space-x-7">
                                    <button
                                        type="button"
                                        @click="markNotificationAsRead()"
                                        class="bg-yellow-100 rounded-md text-sm font-medium text-teal-600 hover:text-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                    >
                                        Mark As Read
                                    </button>
                                </div>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex">
                                <button
                                    @click="show = false"
                                    class="bg-yellow-100 rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                >
                                    <span class="sr-only">Close</span>
                                    <XCircleIcon
                                        class="h-5 w-5"
                                        aria-hidden="true"
                                    />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script>
import { EnvelopeIcon, XCircleIcon } from "@heroicons/vue/24/outline";
export default {
    components: {
        EnvelopeIcon,
        XCircleIcon,
    },
    props: {
        notification: Object,
    },
    data() {
        return {
            show: false,
            selectedNotificationForm: this.$inertia.form({
                _method: "POST",
                id: "",
                title: "",
                message: "",
                user_id: "",
            }),
        };
    },
    methods: {
        markNotificationAsRead() {
            this.selectedNotificationForm.post(
                route("users.markNotificationAsRead", this.$page.props.user),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.show = !this.show;
                        this.selectedNotificationForm.reset();
                    },
                    onError: (err) => console.error(err),
                }
            );
        },
        toggleShowNotificationDialog(notification) {
            this.selectedNotificationForm.user_id = notification.notifiable_id;
            this.selectedNotificationForm.id = notification.id;
            this.selectedNotificationForm.title = notification.data["title"];
            this.selectedNotificationForm.message =
                notification.data["message"];
            this.show = true;
        },
    },
};
</script>
