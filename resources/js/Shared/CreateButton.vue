<template>
    <div>
        <span v-if="mode == 'button'">
            <span v-if="!$page.props.user.email">
                <a
                    href="/login"
                    class="p-3 cursor-pointer inline-flex items-center text-center border border-transparent text-base rounded-full shadow-sm text-white inline-flex items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="mr-3 ml-2 h-6 w-6 text-dark"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Upload &emsp;
                </a>
            </span>
            <span v-else>
                <Link
                    id="tour-step-upload"
                    :href="route('dashboard', { action: 'submission' })"
                    class="p-3 inline-flex items-center text-center border border-transparent text-base rounded-full shadow-sm text-white inline-flex items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="mr-3 ml-2 h-6 w-6 text-dark"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Upload &emsp;
                </Link>
            </span>
        </span>
        <span v-if="mode == 'icon'">
            <span v-if="!$page.props.user.email">
                <a
                    href="/login"
                    class="cursor-pointer inline-flex items-center text-center p-3 border border-transparent text-base font-medium rounded-full shadow-sm inline-flex items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-dark"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </a>
            </span>
            <span v-else
                ><Link
                    id="tour-step-upload"
                    :href="route('dashboard', { action: 'submission' })"
                    class="inline-flex items-center text-center p-3 border border-transparent text-base font-medium rounded-full shadow-sm inline-flex items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-dark"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg> </Link
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
    },
    props: {
        mode: String,
    },
    setup() {},
    computed: {},
    methods: {},
};
</script>
