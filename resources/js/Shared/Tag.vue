<template>
    <!-- Generic label chip mode -->
    <div v-if="label" class="flex-shrink-0">
        <span :class="chipClasses">{{ label }}</span>
    </div>

    <!-- Identifier badge mode -->
    <div v-else-if="identifier" class="mt-2 flex-shrink-0 sm:mt-0">
        <span :class="chipClasses">#{{ identifier }}</span>
    </div>

    <!-- Keywords / tags -->
    <div v-else-if="tags && tags.length">
        <div class="flex flex-wrap justify-start gap-1.5 sm:gap-2">
            <a
                v-for="tag in tags"
                :key="tag.id"
                :class="[chipClasses, linkChipClasses]"
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
            default: "md",
            validator: (v) => ["sm", "md"].includes(v),
        },
    },
    computed: {
        chipClasses() {
            const base =
                "inline-flex max-w-full min-w-0 items-center rounded-full border border-transparent " +
                "truncate bg-gradient-to-b from-white to-slate-50/95 font-medium text-slate-700 shadow-sm " +
                "ring-1 ring-inset ring-slate-900/[0.06] antialiased " +
                "dark:from-slate-800/95 dark:to-slate-900/90 dark:text-slate-100 dark:ring-white/[0.08]";
            const sizes = {
                md: "h-7 px-3 text-sm leading-none tracking-normal",
                sm: "h-6 px-2.5 text-[11px] leading-none tracking-normal sm:text-xs",
            };
            return `${base} ${sizes[this.size]}`;
        },
        linkChipClasses() {
            return (
                "cursor-pointer no-underline focus-visible:outline-none focus-visible:ring-2 " +
                "focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-950"
            );
        },
    },
};
</script>
