<template>
    <div>
        <span v-if="mode == 'button'">
            <span v-if="!$page.props.auth.user?.email">
                <Link href="/login" :class="buttonClasses">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="mr-2 h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Submit Data
                </Link>
            </span>
            <span v-else>
                <Link
                    id="tour-step-upload"
                    :href="route('upload')"
                    :class="buttonClasses"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="mr-2 h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Submit Data
                </Link>
            </span>
        </span>
        <span v-if="mode == 'icon'">
            <span v-if="!$page.props.auth.user">
                <Link
                    href="/login"
                    class="inline-flex items-center justify-center p-3 bg-blue-600 border border-transparent rounded-full text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 transition-colors duration-200 shadow-md hover:shadow-lg"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </Link>
            </span>
            <span v-else>
                <Link
                    id="tour-step-upload"
                    :href="route('upload')"
                    class="inline-flex items-center justify-center p-3 bg-blue-600 border border-transparent rounded-full text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 transition-colors duration-200 shadow-md hover:shadow-lg"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </Link>
            </span>
        </span>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";
export default {
    components: {
        Link,
    },
    props: {
        mode: String,
        compact: {
            type: Boolean,
            default: false,
        },
    },
    setup() {},
    computed: {
        buttonClasses() {
            return [
                this.compact
                    ? "inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-full hover:bg-blue-700 transition-colors duration-200 shadow-md hover:shadow-lg"
                    : "w-full inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white text-base font-semibold rounded-full hover:bg-blue-700 transition-colors duration-200 shadow-md hover:shadow-lg",
            ];
        },
    },
    methods: {},
};
</script>
