<template>
    <InertiaLink :href="'/'">
        <jet-application-logo class="block w-auto" />
    </InertiaLink>
</template>

<script>
import { Link as InertiaLink } from "@inertiajs/vue3";
import JetApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
export default {
    components: {
        InertiaLink,
        JetApplicationLogo,
    },
};
</script>
