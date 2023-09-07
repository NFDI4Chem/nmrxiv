<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-white font-semibold text-sm text-gray-500 hover:text-gray-700 cursor-pointer"
    >
        <slot></slot>
    </button>
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            default: "button",
        },
    },
};
</script>
