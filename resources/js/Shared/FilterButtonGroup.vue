<template>
    <div class="text-left">
        <p
            v-if="label"
            :id="labelId"
            class="mb-2 block text-[11px] font-semibold uppercase tracking-wide text-gray-500"
        >
            {{ label }}
        </p>
        <div
            class="flex w-full flex-wrap gap-1.5"
            role="group"
            :aria-labelledby="label ? labelId : undefined"
        >
            <button
                v-for="option in options"
                :key="String(option.value)"
                type="button"
                class="max-w-full cursor-pointer truncate rounded-full border px-3 py-1.5 text-xs font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-1"
                :class="buttonClass(option.value)"
                :disabled="isDisabled(option.value)"
                :aria-pressed="isSelected(option.value)"
                :aria-disabled="isDisabled(option.value)"
                :title="option.label"
                @click="select(option.value)"
            >
                {{ option.label }}
            </button>
        </div>
    </div>
</template>

<script>
let labelCounter = 0;

export default {
    props: {
        modelValue: {
            type: [String, Number],
            default: "",
        },
        label: {
            type: String,
            default: "",
        },
        options: {
            type: Array,
            required: true,
        },
        disabledValues: {
            type: Array,
            default: () => [],
        },
    },
    emits: ["update:modelValue"],
    setup(props, { emit }) {
        const labelId = `filter-button-group-${++labelCounter}`;

        const isSelected = (value) =>
            String(props.modelValue ?? "") === String(value);

        const isDisabled = (value) => {
            if (value === "" || value === null || value === undefined) {
                return false;
            }

            return props.disabledValues.includes(String(value));
        };

        const buttonClass = (value) => {
            if (isDisabled(value)) {
                return isSelected(value)
                    ? "cursor-not-allowed border-gray-200 bg-gray-100 text-gray-400"
                    : "cursor-not-allowed border-gray-100 bg-white text-gray-300";
            }

            return isSelected(value)
                ? "border-gray-900 bg-gray-900 text-white shadow-sm"
                : "border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:text-gray-900";
        };

        const select = (value) => {
            if (isDisabled(value)) {
                return;
            }

            if (isSelected(value)) {
                emit("update:modelValue", "");

                return;
            }

            emit("update:modelValue", value);
        };

        return {
            labelId,
            isSelected,
            isDisabled,
            buttonClass,
            select,
        };
    },
};
</script>
