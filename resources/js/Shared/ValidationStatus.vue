<template>
    <div>
        <div
            v-if="valueExists"
            class="inline-flex py-2 mr-2 text-xs font-semibold leading-5 text-green-800"
        >
            <CheckCircleIcon class="w-5 h-5" />
        </div>
        <div
            v-if="!valueExists && !isRequired"
            class="inline-flex py-2 mr-2 text-xs font-semibold leading-5 text-yellow-400"
        >
            <ExclamationTriangleIcon class="w-5 h-5" />
        </div>
        <div
            v-if="!valueExists && isRequired"
            class="inline-flex py-2 mr-2 text-xs font-semibold leading-5 text-red-800"
        >
            <XCircleIcon class="w-5 h-5" />
        </div>
    </div>
</template>

<script>
import {
    XCircleIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon,
} from "@heroicons/vue/24/outline";

export default {
    components: { XCircleIcon, ExclamationTriangleIcon, CheckCircleIcon },
    props: ["status"],
    computed: {
        isRequired() {
            if (typeof this.status != "boolean") {
                return this.status.indexOf("|required") > -1;
            }
            return true;
        },
        valueExists() {
            if (typeof this.status == "boolean") {
                return this.status;
            }
            return this.status.split("|")[0] == "true";
        },
    },
};
</script>
