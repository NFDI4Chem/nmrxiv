<template>
    <div class="tooltip">
        <InformationCircleIcon class="h-5 w-5 ml-2" />

        <span
            class="w-64 bg-gray-900 text-white p-4 shadow-lg rounded-md tooltiptext"
            v-html="text"
        ></span>
    </div>
</template>

<script>
import { InformationCircleIcon } from "@heroicons/vue/24/solid";

export default {
    components: { InformationCircleIcon },
    props: {
        text: String,
    },
};
</script>
