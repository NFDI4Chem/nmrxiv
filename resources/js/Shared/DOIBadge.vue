<template>
    <div>
        <div v-if="doi">
            <div
                class="inline-flex items-center cursor-pointer"
                @click.prevent="copyDOIToClipboard"
            >
                <span
                    class="inline-flex items-center rounded-l-md border-r bg-gray-800 px-2 py-1 text-sm font-medium text-white"
                    >DOI:</span
                >
                <span
                    :class="[
                        color ? color : 'bg-blue-100',
                        'inline-flex items-center rounded-r-md px-2 py-1 text-sm font-medium text-gray-900',
                    ]"
                    >{{ doi }}</span
                >
                <input
                    readonly
                    type="hidden"
                    :value="doi"
                    class="rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                    @focus="$event.target.select()"
                />
                <span v-if="isVisible" class="text-gray-500 text-xs ml-2"
                    >Copied to clipboard!</span
                >
            </div>
        </div>
    </div>
</template>

<script>
export default {
    components: {},
    props: ["doi", "color"],
    data() {
        return {
            isVisible: false,
        };
    },
    methods: {
        copyDOIToClipboard(event) {
            let targetInput =
                event.target.parentElement.getElementsByTagName("input")[0];
            if (targetInput) {
                this.isVisible = true;
                setTimeout(() => {
                    this.isVisible = false;
                }, 2500);
                this.copyToClipboard(
                    "https://doi.org/" + this.doi,
                    targetInput
                );
            }
        },
    },
};
</script>
