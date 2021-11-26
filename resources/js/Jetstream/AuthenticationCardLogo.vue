<template>
    <Link :href="'/'">
        <jet-application-logo class="block w-auto" />
    </Link>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';
    import JetApplicationLogo from '@/Jetstream/ApplicationLogo.vue'
    export default {
        components: {
            Link,
            JetApplicationLogo
        },
    }
</script>
