<template>
    <div class="min-w-0">
        <label
            v-if="label"
            :id="labelId"
            :for="usePills ? undefined : labelId"
            class="mb-1.5 block text-left text-xs font-medium text-gray-500"
        >
            {{ label }}
        </label>

        <div
            v-if="loading"
            class="h-9 animate-pulse rounded-lg bg-gray-100"
            :aria-labelledby="label ? labelId : undefined"
            role="status"
        >
            <span class="sr-only">Loading options</span>
        </div>

        <div
            v-else-if="options.length <= 1"
            class="flex h-9 items-center rounded-lg border border-dashed border-gray-200 bg-gray-50/50 px-3 text-xs text-gray-400"
        >
            No matching values
        </div>

        <FilterButtonGroup
            v-else-if="usePills"
            :model-value="modelValue"
            :options="pillOptions"
            :disabled-values="disabledValues"
            @update:model-value="$emit('update:modelValue', $event)"
        />

        <select
            v-else
            :id="labelId"
            :value="modelValue"
            class="field-select"
            :disabled="options.length <= 1"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option
                v-for="option in options"
                :key="String(option.value)"
                :value="option.value"
                :disabled="isDisabled(option.value)"
            >
                {{ option.label }}
            </option>
        </select>
    </div>
</template>

<script>
import { computed } from "vue";
import FilterButtonGroup from "@/Shared/FilterButtonGroup.vue";

let labelCounter = 0;

export default {
    components: {
        FilterButtonGroup,
    },
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
            default: () => [{ label: "Any", value: "" }],
        },
        disabledValues: {
            type: Array,
            default: () => [],
        },
        loading: {
            type: Boolean,
            default: false,
        },
        compact: {
            type: Boolean,
            default: false,
        },
        pillThreshold: {
            type: Number,
            default: null,
        },
        preferSelect: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["update:modelValue"],
    setup(props) {
        const labelId = `metadata-facet-field-${++labelCounter}`;

        const resolvedThreshold = computed(() => {
            if (props.pillThreshold !== null) {
                return props.pillThreshold;
            }

            return props.compact ? 5 : 6;
        });

        const longestLabelLength = computed(() =>
            Math.max(
                0,
                ...props.options.map((option) => String(option.label).length)
            )
        );

        const usePills = computed(() => {
            if (props.preferSelect) {
                return false;
            }

            if (props.options.length > resolvedThreshold.value) {
                return false;
            }

            return longestLabelLength.value <= 16;
        });

        const pillOptions = computed(() =>
            props.options.filter((option) => option.value !== "")
        );

        const isDisabled = (value) => {
            if (value === "" || value === null || value === undefined) {
                return false;
            }

            return props.disabledValues.includes(String(value));
        };

        return {
            labelId,
            usePills,
            pillOptions,
            isDisabled,
        };
    },
};
</script>

<style scoped>
.field-select {
    @apply h-9 w-full truncate rounded-lg border border-gray-200 bg-white px-3 py-2 text-left text-sm text-gray-900 shadow-sm transition-colors focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900/10 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400;
}
</style>
