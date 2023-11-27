<template>
    <Listbox v-model="proxySelected" as="div">
        <ListboxLabel
            v-if="label"
            class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
            >{{ label }}</ListboxLabel
        >
        <div class="mt-1 relative">
            <ListboxButton
                class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 sm:text-sm"
            >
                <span v-if="selected" class="block truncate">{{
                    selected.title
                }}</span>
                <span v-else class="block truncate">--Select--</span>
                <span
                    class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none"
                >
                    <ChevronUpDownIcon
                        class="h-5 w-5 text-gray-400"
                        aria-hidden="true"
                    />
                </span>
            </ListboxButton>
            <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <ListboxOptions
                    class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                >
                    <ListboxOption
                        v-for="item in items"
                        :key="item.id"
                        v-slot="{ active, selected }"
                        as="template"
                        :value="item"
                    >
                        <li
                            :class="[
                                active
                                    ? 'text-white bg-teal-600'
                                    : 'text-gray-900',
                                'cursor-default border-b select-none relative py-2 pl-8 pr-4',
                            ]"
                        >
                            <span
                                :class="[
                                    selected ? 'font-semibold' : 'font-normal',
                                    'block truncate',
                                ]"
                            >
                                <b>{{ item.title }}</b> <br />
                                <small
                                    v-if="item.description"
                                    v-html="item.description"
                                >
                                </small>
                                <!-- ToolTip class="inline ml-3 float-right overflow-visible" :text="item.description"></ToolTip -->
                            </span>
                            <span
                                v-if="selected"
                                :class="[
                                    active ? 'text-white' : 'text-teal-600',
                                    'absolute inset-y-0 left-0 flex items-center pl-1.5',
                                ]"
                            >
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
import { ref } from "vue";
import {
    Listbox,
    ListboxButton,
    ListboxLabel,
    ListboxOption,
    ListboxOptions,
} from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/24/solid";
import ToolTip from "@/Shared/ToolTip.vue";

export default {
    components: {
        Listbox,
        ListboxButton,
        ListboxLabel,
        ListboxOption,
        ListboxOptions,
        CheckIcon,
        ChevronUpDownIcon,
        ToolTip,
    },
    props: ["items", "selected", "label"],
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
