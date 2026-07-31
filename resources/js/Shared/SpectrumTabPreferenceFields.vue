<template>
    <div class="space-y-4">
        <div>
            <jet-label :id="dimensionLabelId" value="Spectrum dimension" />
            <div
                class="mt-2 flex flex-wrap gap-2"
                role="group"
                :aria-labelledby="dimensionLabelId"
            >
                <button
                    v-for="option in dimensionOptions"
                    :key="option.value || 'automatic'"
                    type="button"
                    :disabled="disabled"
                    :class="choiceButtonClass(dimension === option.value)"
                    :aria-pressed="dimension === option.value"
                    @click="selectDimension(option.value)"
                >
                    {{ option.label }}
                </button>
            </div>
        </div>

        <div v-if="dimension === '1d'">
            <jet-label :id="detailLabelId" value="Nucleus" />
            <div
                class="mt-2 flex flex-wrap gap-2"
                role="group"
                :aria-labelledby="detailLabelId"
            >
                <button
                    v-for="nucleus in tabs1D"
                    :key="nucleus"
                    type="button"
                    :disabled="disabled"
                    :class="choiceButtonClass(tab === nucleus)"
                    :aria-pressed="tab === nucleus"
                    @click="$emit('update:tab', nucleus)"
                >
                    {{ nucleus }}
                </button>
            </div>
        </div>

        <div v-else-if="dimension === '2d'">
            <jet-label :id="detailLabelId" value="2D experiment" />
            <div
                class="mt-2 flex flex-wrap gap-2"
                role="group"
                :aria-labelledby="detailLabelId"
            >
                <button
                    v-for="experiment in tabs2D"
                    :key="experiment"
                    type="button"
                    :disabled="disabled"
                    :class="choiceButtonClass(tab === experiment)"
                    :aria-pressed="tab === experiment"
                    @click="$emit('update:tab', experiment)"
                >
                    {{ experiment }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import JetLabel from "@/Jetstream/Label.vue";

let nextFieldId = 0;

export default {
    components: {
        JetLabel,
    },
    props: {
        dimension: {
            type: String,
            default: "",
        },
        tab: {
            type: String,
            default: "",
        },
        tabs1D: {
            type: Array,
            default: () => [],
        },
        tabs2D: {
            type: Array,
            default: () => [],
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["update:dimension", "update:tab"],
    data() {
        const id = ++nextFieldId;

        return {
            dimensionLabelId: `spectrum-dimension-${id}`,
            detailLabelId: `spectrum-detail-${id}`,
            dimensionOptions: [
                { value: "", label: "Automatic" },
                { value: "1d", label: "1D" },
                { value: "2d", label: "2D" },
            ],
        };
    },
    methods: {
        choiceButtonClass(selected) {
            return [
                "rounded-md border px-3 py-1.5 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-primary-200 focus:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-50",
                selected
                    ? "border-primary-500 bg-primary-50 text-primary-700 dark:border-primary-400 dark:bg-primary-950/50 dark:text-primary-300"
                    : "border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700",
            ];
        },
        selectDimension(value) {
            this.$emit("update:dimension", value);

            if (!value) {
                this.$emit("update:tab", "");

                return;
            }

            const options = value === "1d" ? this.tabs1D : this.tabs2D;
            const currentIsValid = options.includes(this.tab);

            if (!currentIsValid && options.length > 0) {
                this.$emit("update:tab", options[0]);
            }
        },
    },
};
</script>
