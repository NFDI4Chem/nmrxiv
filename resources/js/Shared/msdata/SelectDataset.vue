<template>
    <div class="p-6">
        <div>
            <label
                :for="name"
                class="block text-sm font-medium text-gray-700"
            >{{labelText}}


    <select
        id="tour-step-msdata-{{name}}"
        v-model=selected
        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm rounded-md"
        :name="name"
        @change="$emit('update:selectedOption',selected);"
    >
        <option
            v-for="opt in allOptions"
            :key="opt.id"
            :value="opt"
        >
            {{
                opt.name
            }}
        </option>
    </select>
            </label>
        </div>
    </div>
</template>
<script>
export default {
    name: 'SelectDataset',
    props: {
        selectedOption: {},
        allOptions: {},
        labelText: String,
        name: String

    },
    data() {
        return {
            selected: this.allOptions.indexOf(this.selectedOption) > -1 ? this.selectedOption : this.allOptions[0]
        }
    },
    watch: {
        $props: {
            handler() {
                this.selected = this.allOptions.indexOf(this.selected) > -1 ? this.selected : this.allOptions[0];
                this.$emit('update:selectedOption',this.selected)
            }, deep: true, immediate: true
        },
        selected(newSelected) {
            return this.allOptions.indexOf(newSelected) > -1 ? newSelected : this.allOptions[0]
        },
        allOptions() {

        }
    },
    emits: ['update:selectedOption']
}
</script>
