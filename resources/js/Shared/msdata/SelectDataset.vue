<template>
    <select
        id="tour-step-select-exp"
        v-model="selectedDataset"
        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm rounded-md"
        name="location"
    >
        <option
            v-for="dataset in selectedStudy"
            :key="dataset.slug"
            :value="dataset"
        >
            {{
                dataset.name
            }}
        </option>
    </select>
</template>
<script>
export default {
    name: 'SelectDataset',
    props: {
        selectedDataset: {},
        selectedStudy: {}
    }
}
</script>
