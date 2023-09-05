<template>
    <button
        :type="type"
        class="inline-flex items-center shadow-sm px-4 py-1.5 text-sm leading-5 font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
    >
        <PencilIcon class="w-4 h-4 mr-1 text-gray-600" />
        <span>Edit</span>
        <slot></slot>
    </button>
</template>

<script>
import { PencilIcon } from "@heroicons/vue/24/solid";

export default {
    components: { PencilIcon },
    props: {
        type: {
            type: String,
            default: "button",
        },
    },
};
</script>
