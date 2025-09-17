<template>
    <!-- Generic label chip mode -->
    <div v-if="label" class="flex-shrink-0">
        <span :class="chipClasses">{{ label }}</span>
    </div>

    <!-- Identifier badge mode -->
    <div v-else-if="identifier" class="mt-2 sm:mt-0 flex-shrink-0">
        <span :class="chipClasses">#{{ identifier }}</span>
    </div>

    <!-- Desktop: Full tags display mode -->
    <div v-else-if="tags && tags.length" class="hidden sm:block">
        <div class="flex flex-wrap justify-start gap-2">
            <a
                v-for="tag in tags"
                :key="tag.id"
                :class="[chipClasses, 'hover:bg-gray-100 transition-colors duration-150 cursor-pointer']"
                :href="'/projects?tag=' + (tag?.name?.en ?? '')"
            >
                {{ tag?.name?.en }}
            </a>
        </div>
    </div>
</template>

<script>
export default {
    name: "Tag",
    props: {
        label: {
            type: String,
            default: null,
        },
        identifier: {
            type: [String, Number],
            default: null,
        },
        tags: {
            type: Array,
            default: () => [],
        },
        // Size variant: 'md' (default) or 'sm' for more compact chips
        size: {
            type: String,
            default: 'md',
            validator: (v) => ['sm', 'md'].includes(v),
        },
    },
    computed: {
        chipClasses() {
            const base = 'inline-flex items-center rounded-full bg-gray-50 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10';
            const sizes = {
                md: 'px-4 py-1 text-sm',
                sm: 'px-2 py-0.5 text-xs',
            };
            return `${base} ${sizes[this.size]}`;
        },
    },
};
</script>
