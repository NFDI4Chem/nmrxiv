<template>
    <div class="relative">
        <Menu as="div" class="relative inline-block text-left">
            <div>
                <MenuButton
                    class="inline-flex items-center justify-center w-10 h-10 rounded-md border border-gray-300 shadow-sm bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    <AdjustmentsHorizontalIcon class="h-5 w-5 text-gray-400" />
                </MenuButton>
            </div>

            <transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
            >
                <MenuItems
                    class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                >
                    <div class="py-1">
                        <MenuItem v-slot="{ active }">
                            <button
                                :class="[
                                    active
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-700',
                                    modelValue === 'all'
                                        ? 'font-semibold'
                                        : 'font-normal',
                                    'group flex w-full items-center px-4 py-2 text-sm',
                                ]"
                                @click="$emit('update:modelValue', 'all')"
                            >
                                All Status
                            </button>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <button
                                :class="[
                                    active
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-700',
                                    modelValue === 'draft'
                                        ? 'font-semibold'
                                        : 'font-normal',
                                    'group flex w-full items-center px-4 py-2 text-sm',
                                ]"
                                @click="$emit('update:modelValue', 'draft')"
                            >
                                Draft
                            </button>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <button
                                :class="[
                                    active
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-700',
                                    modelValue === 'published'
                                        ? 'font-semibold'
                                        : 'font-normal',
                                    'group flex w-full items-center px-4 py-2 text-sm',
                                ]"
                                @click="$emit('update:modelValue', 'published')"
                            >
                                Published
                            </button>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <button
                                :class="[
                                    active
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-700',
                                    modelValue === 'archived'
                                        ? 'font-semibold'
                                        : 'font-normal',
                                    'group flex w-full items-center px-4 py-2 text-sm',
                                ]"
                                @click="$emit('update:modelValue', 'archived')"
                            >
                                Archived
                            </button>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <button
                                :class="[
                                    active
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-700',
                                    modelValue === 'embargo'
                                        ? 'font-semibold'
                                        : 'font-normal',
                                    'group flex w-full items-center px-4 py-2 text-sm',
                                ]"
                                @click="$emit('update:modelValue', 'embargo')"
                            >
                                Embargo
                            </button>
                        </MenuItem>
                    </div>
                </MenuItems>
            </transition>
        </Menu>
    </div>
</template>

<script>
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import { AdjustmentsHorizontalIcon } from "@heroicons/vue/24/outline";

export default {
    components: {
        Menu,
        MenuButton,
        MenuItems,
        MenuItem,
        AdjustmentsHorizontalIcon,
    },
    props: {
        modelValue: {
            type: String,
            default: "all",
        },
    },
};
</script>
