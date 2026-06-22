<template>
    <DepositDataMenu
        :size="
            mode === 'icon' ? 'icon' : compact ? 'sidebarCompact' : 'sidebar'
        "
    />
</template>

<script>
import DepositDataMenu from "@/Shared/DepositDataMenu.vue";

export default {
    components: {
        DepositDataMenu,
    },
    props: {
        mode: String,
        compact: {
            type: Boolean,
            default: false,
        },
    },
};
</script>
