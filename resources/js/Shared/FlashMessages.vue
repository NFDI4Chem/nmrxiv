<template>
    <div
        aria-live="assertive"
        class="fixed inset-0 flex items-end px-4 py-6 z-10 pointer-events-none sm:p-6 sm:items-start"
    >
        <!-- On Success -->
        <div
            v-if="$page.props.flash.success && show"
            class="w-full flex flex-col items-center space-y-4 sm:items-end"
        >
            <transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
                >
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <CheckCircleIcon
                                    class="h-6 w-6 text-green-400"
                                    aria-hidden="true"
                                />
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $page.props.flash.success }}
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex">
                                <button
                                    class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    @click="show = false"
                                >
                                    <span class="sr-only">Close</span>
                                    <XMarkIcon
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
        <!-- On Error -->
        <div
            v-if="$page.props.flash.error && show"
            class="w-full flex flex-col items-center space-y-4 sm:items-end"
        >
            <transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
            </transition>
            <div
                class="max-w-sm w-full bg-red-100 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
            >
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <ExclamationTriangleIcon
                                class="h-6 w-6 text-red-400"
                                aria-hidden="true"
                            />
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $page.props.flash.error }}
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button
                                class="bg-red-100 rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                @click="show = false"
                            >
                                <span class="sr-only">Close</span>
                                <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { CheckCircleIcon } from "@heroicons/vue/24/outline";
import { XMarkIcon } from "@heroicons/vue/24/solid";
import { ExclamationTriangleIcon } from "@heroicons/vue/24/outline";

export default {
    components: {
        CheckCircleIcon,
        XMarkIcon,
        ExclamationTriangleIcon,
    },
    data() {
        return {
            show: true,
            timeout: null,
        };
    },
    watch: {
        "$page.props.flash": {
            deep: true,
            handler() {
                this.show = true;
                this.timeout = setTimeout(() => (this.show = false), 5000);
            },
        },
    },
};
</script>
