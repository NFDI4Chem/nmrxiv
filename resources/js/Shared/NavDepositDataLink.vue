<template>
    <DepositDataMenu size="nav" :full-width="fullWidth" />
</template>

<script setup>
import DepositDataMenu from "@/Shared/DepositDataMenu.vue";

defineProps({
    fullWidth: {
        type: Boolean,
        default: false,
    },
});
</script>
