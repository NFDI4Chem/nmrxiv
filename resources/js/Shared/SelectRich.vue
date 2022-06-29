<!-- This example requires Tailwind CSS v2.0+ -->
<template>
  <Listbox as="div" v-model="proxySelected">
    <ListboxLabel v-if="label" class="block text-sm font-medium text-gray-700">{{label}}</ListboxLabel>
    <div class="mt-1 relative">
      <ListboxButton class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
        <span v-if="selected" class="block truncate">{{ selected.title }}</span>
        <span v-else class="block truncate">Select a license</span>
        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
          <SelectorIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
        </span>
      </ListboxButton>
      <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <ListboxOptions class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
          <ListboxOption as="template" v-for="item in items" :key="item.id" :value="item" v-slot="{ active, selected }">
            <li :class="[active ? 'text-white bg-teal-600' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-8 pr-4']">
              <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                {{ item.title }}
                <!-- ToolTip class="inline ml-3 float-right overflow-visible" :text="item.description"></ToolTip -->
              </span>
              <span v-if="selected" :class="[active ? 'text-white' : 'text-teal-600', 'absolute inset-y-0 left-0 flex items-center pl-1.5']">
                <CheckIcon class="h-5 w-5" aria-hidden="true" />
              </span>
            </li>
          </ListboxOption>
        </ListboxOptions>
      </transition>
    </div>
  </Listbox>
</template>

<script>
import { ref } from 'vue';
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue';
import { CheckIcon, SelectorIcon } from '@heroicons/vue/solid';
import ToolTip from "@/Shared/ToolTip";

export default{
    components:{
        Listbox, 
        ListboxButton, 
        ListboxLabel, 
        ListboxOption, 
        ListboxOptions,
        CheckIcon, 
        SelectorIcon,
        ToolTip,
    },
    props:{
        items: [],
        selected: {},
        label: null
    },
    computed: {
        proxySelected: {
            get() {
                return this.selected;
            },

            set(val) {
                this.$emit("update:selected", val);
            },
        },
    },
};
</script>