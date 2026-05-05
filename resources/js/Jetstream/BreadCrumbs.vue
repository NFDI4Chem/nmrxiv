<template>
    <nav class="hidden sm:flex" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-4">
            <li v-for="(page, index) in pages" :key="page.name">
                <div class="flex items-center">
                    <ChevronRightIcon
                        v-if="index > 0"
                        class="flex-shrink-0 h-5 w-5 text-gray-400 mr-4"
                        aria-hidden="true"
                    />
                    <InertiaLink
                        :href="route(page.route, page.id)"
                        class="text-sm font-medium text-gray-500 hover:text-gray-700"
                        :aria-current="page.current ? 'page' : undefined"
                        >{{ page.name }}</InertiaLink
                    >
                </div>
            </li>
        </ol>
    </nav>
</template>

<script>
import { Link as InertiaLink } from "@inertiajs/vue3";
import { ChevronRightIcon } from "@heroicons/vue/24/solid";

export default {
    components: {
        ChevronRightIcon,
        InertiaLink,
    },
    props: {
        pages: {
            type: Array,
            required: true,
        },
    },
    setup() {},
};
</script>
