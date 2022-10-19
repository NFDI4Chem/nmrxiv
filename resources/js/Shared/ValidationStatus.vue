<template>
    <div>
        <div
            v-if="valueExists"
            class="inline-flex py-2 mr-2 text-xs font-semibold leading-5 text-green-800"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-5 h-5"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
        </div>
        <div
            v-if="!valueExists && !isRequired"
            class="inline-flex py-2 mr-2 text-xs font-semibold leading-5 text-yellow-400"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-5 h-5"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                />
            </svg>
        </div>
        <div
            v-if="!valueExists && isRequired"
            class="inline-flex py-2 mr-2 text-xs font-semibold leading-5 text-red-800"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-5 h-5"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
        </div>
    </div>
</template>

<script>
export default {
    props: ["status"],
    computed: {
        isRequired() {
            if (typeof this.status != "boolean") {
                return this.status.indexOf("|required") > -1;
            }
            return true;
        },
        valueExists() {
            if (typeof this.status == "boolean") {
                return this.status;
            }
            return this.status.split("|")[0] == "true";
        },
    },
};
</script>
