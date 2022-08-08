<template>
    <div
        v-if="hasErrors"
        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
        role="alert"
    >
        <strong class="font-medium pb-0">Something went wrong.</strong>
        <span class="block sm:inline">
            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
            </ul>
        </span>
    </div>
</template>

<script>
export default {
    computed: {
        errors() {
            return this.$page.props.errors;
        },

        hasErrors() {
            return Object.keys(this.errors).length > 0;
        },
    },
};
</script>
