<template>
    <section
        v-if="hasPanel"
        class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm ring-1 ring-gray-900/5 dark:border-gray-700 dark:bg-slate-800/90 dark:ring-white/5"
    >
        <button
            type="button"
            class="flex w-full items-center justify-between gap-3 border-b border-gray-100 px-3 py-3 text-left transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-500 dark:border-gray-700 dark:hover:bg-slate-800/80 sm:px-4"
            :aria-expanded="isExpanded"
            :aria-controls="panelControlId"
            @click="toggleExpanded"
        >
            <div class="min-w-0 flex-1">
                <h3
                    :id="headingControlId"
                    class="text-lg font-semibold tracking-tight text-gray-900 dark:text-slate-100"
                >
                    HiFSA
                </h3>
            </div>
            <ChevronRightIcon
                class="h-5 w-5 shrink-0 text-gray-400 transition-transform duration-200 dark:text-slate-500"
                :class="{ 'rotate-90': isExpanded }"
                aria-hidden="true"
            />
        </button>
        <div
            v-show="isExpanded"
            :id="panelControlId"
            class="p-3 sm:p-4"
            role="region"
            :aria-labelledby="headingControlId"
        >
            <HifsaScoresPanel
                v-if="hasScores"
                :hifsa-data="hifsaData"
                :molecules="molecules"
            />
            <iframe
                v-else-if="hifsaPdfUrl"
                title="HiFSA report"
                frameborder="0"
                class="block w-full rounded-md border"
                style="height: min(75vh, 600px)"
                :src="hifsaPdfUrl"
            ></iframe>
        </div>
    </section>
</template>

<script>
import { ChevronRightIcon } from "@heroicons/vue/24/outline";
import HifsaScoresPanel from "@/Shared/HifsaScoresPanel.vue";

export default {
    name: "HifsaPanel",
    components: {
        ChevronRightIcon,
        HifsaScoresPanel,
    },
    props: {
        hifsaData: {
            type: Object,
            default: null,
        },
        hifsaPdfUrl: {
            type: String,
            default: null,
        },
        molecules: {
            type: Array,
            default: () => [],
        },
        expanded: {
            type: Boolean,
            default: true,
        },
        idPrefix: {
            type: String,
            default: "hifsa",
        },
    },
    emits: ["update:expanded"],
    data() {
        return {
            isExpanded: this.expanded,
        };
    },
    computed: {
        hasScores() {
            return Boolean(this.hifsaData?.scores);
        },
        hasPanel() {
            return this.hasScores || Boolean(this.hifsaPdfUrl);
        },
        headingControlId() {
            return `${this.idPrefix}-heading`;
        },
        panelControlId() {
            return `${this.idPrefix}-panel`;
        },
    },
    watch: {
        expanded(isOpen) {
            this.isExpanded = isOpen;
        },
    },
    methods: {
        toggleExpanded() {
            this.isExpanded = !this.isExpanded;
            this.$emit("update:expanded", this.isExpanded);
        },
    },
};
</script>
