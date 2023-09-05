<template>
    <TabList class="flex items-center">
        <Tab v-slot="{ selected }" as="template">
            <button
                :class="[
                    selected
                        ? 'text-gray-900 bg-gray-100 hover:bg-gray-200'
                        : 'text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100',
                    'px-3 py-1.5 border border-transparent text-sm font-medium rounded-md',
                ]"
            >
                Write
            </button>
        </Tab>
        <Tab v-slot="{ selected }" as="template">
            <button
                :class="[
                    selected
                        ? 'text-gray-900 bg-gray-100 hover:bg-gray-200'
                        : 'text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100',
                    'ml-2 px-3 py-1.5 border border-transparent text-sm font-medium rounded-md',
                ]"
            >
                Preview
            </button>
        </Tab>

        <slot></slot>
    </TabList>
</template>

<script>
import { Tab, TabList } from "@headlessui/vue";

export default {
    components: { Tab, TabList },
};
</script>
