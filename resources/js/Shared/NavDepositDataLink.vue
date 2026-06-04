<template>
    <Link href="/upload" class="deposit-data-cta" :class="linkClass">
        <ArrowUpTrayIcon class="h-4 w-4 shrink-0" aria-hidden="true" />
        <span>Deposit data</span>
    </Link>
</template>

<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import { ArrowUpTrayIcon } from "@heroicons/vue/24/solid";
import { depositButtonClasses } from "@/Utils/depositButtonClasses";

const props = defineProps({
    fullWidth: {
        type: Boolean,
        default: false,
    },
});

const linkClass = computed(() =>
    depositButtonClasses({ fullWidth: props.fullWidth, size: "nav" })
);
</script>
