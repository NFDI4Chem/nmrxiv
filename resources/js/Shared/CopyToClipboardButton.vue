<template>
    <button
        :type="type"
        class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"
    >
        <span
            ><ClipboardDocumentIcon class="h-5 w-5" aria-hidden="true"
        /></span>
        <slot></slot>
    </button>
</template>

<script>
import { ClipboardDocumentIcon } from "@heroicons/vue/24/solid";
export default {
    components: { ClipboardDocumentIcon },
    props: {
        type: {
            type: String,
            default: "button",
        },
    },
};
</script>
