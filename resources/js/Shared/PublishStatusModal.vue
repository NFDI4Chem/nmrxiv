<template>
    <div
        class="mx-auto max-w-3xl transform overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all"
    >
        <div class="py-16">
            <div class="text-center">
                <div
                    class="m-3 relative clear-both border-dotted border-2 border-gray-300 rounded-lg"
                >
                    <span
                        class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm rounded-md text-sky-500 bg-white transition ease-in-out duration-150 cursor-not-allowed"
                        disabled=""
                    >
                        <h1
                            class="capitalize text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl"
                        >
                            Processing
                        </h1>
                    </span>
                </div>
                <Link
                    type="button"
                    :href="route('dashboard')"
                    class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Go to Dashboard
                </Link>
            </div>
        </div>
        <div class="w-full">
            <div
                class="flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700"
            >
                <b>Whats next?</b>
                <div>
                    <p>
                        Please allow some time to process your submission. You will receive an email once your
                        submission is processed. Upon publishing you
                        will also receive an email with citation details
                        and other helpful information to share your
                        datasets.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";

export default {
    name: "PublishStatusModal",
    components: {
        Link,
    },
};
</script>