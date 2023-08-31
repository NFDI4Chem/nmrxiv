<template>
    <div>
        <span v-if="mode == 'button'">
            <span v-if="!$page.props.user.email">
                <Link
                    href="/login"
                    class="p-3 cursor-pointer inline-flex items-center text-center border border-transparent text-base rounded-full shadow-sm text-white inline-flex items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                >
                    <ArrowUpTrayIcon class="mr-3 ml-2 h-6 w-6 text-dark" />

                    Upload &emsp;
                </Link>
            </span>
            <span v-else>
                <Link
                    id="tour-step-upload"
                    :href="route('dashboard', { action: 'submission' })"
                    class="p-3 inline-flex items-center text-center border border-transparent text-base rounded-full shadow-sm text-white inline-flex items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                >
                    <ArrowUpTrayIcon class="mr-3 ml-2 h-6 w-6 text-dark" />

                    Upload &emsp;
                </Link>
            </span>
        </span>
        <span v-if="mode == 'icon'">
            <span v-if="!$page.props.user.email">
                <Link
                    href="/login"
                    class="cursor-pointer inline-flex items-center text-center p-3 border border-transparent text-base font-medium rounded-full shadow-sm inline-flex items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                >
                    <ArrowUpTrayIcon class="h-6 w-6 text-dark" />
                </Link>
            </span>
            <span v-else
                ><Link
                    id="tour-step-upload"
                    :href="route('dashboard', { action: 'submission' })"
                    class="inline-flex items-center text-center p-3 border border-transparent text-base font-medium rounded-full shadow-sm inline-flex items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                >
                    <ArrowUpTrayIcon class="h-6 w-6 text-dark" /> </Link
            ></span>
        </span>
    </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import { Menu, MenuItem, MenuItems } from "@headlessui/vue";
import {
    ArchiveBoxIcon,
    PencilSquareIcon,
    TableCellsIcon,
    PlusIcon,
} from "@heroicons/vue/24/solid";
import { ArrowUpTrayIcon } from "@heroicons/vue/24/outline";

export default {
    components: {
        Link,
        Menu,
        MenuItem,
        MenuItems,
        ArchiveBoxIcon,
        PencilSquareIcon,
        TableCellsIcon,
        PlusIcon,
        ArrowUpTrayIcon,
    },
    props: {
        mode: String,
    },
    setup() {},
    computed: {},
    methods: {},
};
</script>
