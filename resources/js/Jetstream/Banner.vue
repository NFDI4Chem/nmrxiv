<template>
    <div
        v-if="show && message"
        class="fixed z-50 bg-yellow-400 border-b w-screen border-black-800"
    >
        <div class="max-w-7xl mx-auto py-1 px-6">
            <p
                class="text-sm text-center text-dark font-semibold animate-pulse"
            >
                <span> {{ message }} </span>
            </p>
            <div
                class="absolute inset-y-0 right-0 pt-1 pr-1 flex items-start sm:pt-1 sm:pr-2 sm:items-start"
            >
                <button
                    type="button"
                    class="flex p-1 rounded-md focus:outline-none hover:bg-yellow-300 focus:ring-2 focus:ring-white"
                    @click.prevent="show = false"
                >
                    <span class="sr-only">Dismiss</span>
                    <XMarkIcon class="h-3 w-3 text-dark" aria-hidden="true" />
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { XMarkIcon } from "@heroicons/vue/24/outline";
import Button from "@/Shared/Dropdown.vue";
export default {
    components: {
        XMarkIcon,
        Button,
    },
    data() {
        return {
            show: true,
        };
    },

    computed: {
        style() {
            return this.$page.props.jetstream.flash?.bannerStyle || "success";
        },

        message() {
            return this.$page.props.jetstream.flash?.banner || "";
        },
    },
};
</script>
