<template>
    <div class="mt-5 md:mt-0">
        <form @submit.prevent="$emit('submitted')">
            <div
                :class="
                    hasActions
                        ? 'sm:rounded-tl-md sm:rounded-tr-md'
                        : 'sm:rounded-md'
                "
            >
                <div class="grid grid-cols-6 gap-6">
                    <slot name="form"></slot>
                </div>
            </div>
            <div v-if="hasActions" class="mt-3">
                <slot name="actions"></slot>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    components: {},
    emits: ["submitted"],

    computed: {
        hasActions() {
            return !!this.$slots.actions;
        },
    },
};
</script>
