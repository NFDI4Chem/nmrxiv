<template>
    <div class="flex items-center">
        <div class="flex w-full bg-white shadow rounded-full">
            <input
                class="relative w-full border-0 px-6 py-3 rounded-full focus:shadow-outline"
                autocomplete="off"
                type="text"
                name="search"
                placeholder="Search"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
            />
        </div>
        <WordButton
            class="px-4 ml-3 hover:text-gray-700"
            @click="$emit('reset')"
            >Reset</WordButton
        >
    </div>
</template>

<script>
import Dropdown from "@/Shared/Dropdown.vue";
import WordButton from "@/Shared/WordButton.vue";
export default {
    components: {
        Dropdown,
        WordButton,
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
