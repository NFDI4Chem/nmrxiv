<template>
    <div
        class="tooltip shrink-0 rounded-full p-0.5 text-blue-600 outline-none hover:bg-gray-100 hover:text-blue-800 focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-1"
        tabindex="0"
        role="button"
        aria-label="More information"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
            fill="currentColor"
            aria-hidden="true"
        >
            <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                clip-rule="evenodd"
            />
        </svg>
        <span
            class="w-64 bg-gray-900 text-white p-4 shadow-lg rounded-md tooltiptext"
            v-html="sanitizeHtml(text)"
        ></span>
    </div>
</template>

<script>
export default {
    props: {
        text: String,
    },
};
</script>
