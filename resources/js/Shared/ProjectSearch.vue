<template>
    <div class="flex items-center">
        <div class="flex w-full bg-white shadow rounded-full">
            <input
                class="relative w-full border-0 px-6 py-3 rounded-full focus:shadow-outline"
                autocomplete="off"
                type="text"
                name="project-search"
                placeholder="Search"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
            />
        </div>
        <button
            class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500"
            type="button"
            @click="$emit('reset')"
        >
            Reset
        </button>
    </div>
</template>

<script>
import Dropdown from "@/Shared/Dropdown.vue";

export default {
    components: {
        Dropdown,
    },
    props: {
        modelValue: String,
        maxWidth: {
            type: Number,
            default: 300,
        },
    },
};
</script>
