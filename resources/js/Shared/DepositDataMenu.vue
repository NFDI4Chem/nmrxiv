<template>
    <button
        :id="triggerId"
        ref="triggerRef"
        type="button"
        class="deposit-data-cta"
        :class="[triggerClass, menuWrapperClass]"
        aria-haspopup="menu"
        :aria-expanded="open"
        @click="toggle"
    >
        <ArrowUpTrayIcon :class="uploadIconClass" aria-hidden="true" />
        <span v-if="showLabel">Deposit data</span>
        <ChevronDownIcon
            v-if="showLabel"
            class="h-4 w-4 shrink-0 opacity-80"
            aria-hidden="true"
        />
        <span v-if="!showLabel" class="sr-only">Deposit data</span>
    </button>

    <teleport to="body">
        <div v-if="open">
            <div
                class="fixed inset-0 z-[99998]"
                aria-hidden="true"
                @click="close"
            />
            <div
                ref="panelRef"
                class="z-[99999]"
                style="position: absolute"
                role="menu"
                @click.stop
            >
                <div
                    class="w-80 rounded-lg bg-white py-1 shadow-lg ring-1 ring-black/5"
                >
                    <Link
                        v-for="option in depositDataOptions"
                        :key="option.id"
                        :href="depositDataUrl(option.id)"
                        role="menuitem"
                        class="block px-4 py-3 text-left transition-colors hover:bg-teal-50"
                        @click="close"
                    >
                        <span class="block text-sm font-semibold text-gray-900">
                            {{ option.title }}
                        </span>
                        <span
                            class="mt-0.5 block text-xs leading-relaxed text-gray-500"
                        >
                            {{ option.description }}
                        </span>
                    </Link>
                </div>
            </div>
        </div>
    </teleport>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from "vue";
import { Link } from "@inertiajs/vue3";
import Popper from "popper.js";
import { ArrowUpTrayIcon, ChevronDownIcon } from "@heroicons/vue/24/solid";
import { depositButtonClasses } from "@/Utils/depositButtonClasses";
import { depositDataOptions, depositDataUrl } from "@/Utils/depositDataOptions";

const props = defineProps({
    size: {
        type: String,
        default: "nav",
        validator: (value) =>
            ["nav", "sidebar", "sidebarCompact", "icon"].includes(value),
    },
    fullWidth: {
        type: Boolean,
        default: false,
    },
    triggerId: {
        type: String,
        default: "tour-step-upload",
    },
});

const open = ref(false);
const triggerRef = ref(null);
const panelRef = ref(null);
let popper = null;

const showLabel = computed(() => props.size !== "icon");

const menuWrapperClass = computed(() =>
    props.fullWidth || props.size === "sidebar" ? "w-full" : ""
);

const triggerClass = computed(() =>
    depositButtonClasses({
        fullWidth: props.fullWidth,
        size: props.size,
    })
);

const uploadIconClass = computed(() => {
    if (props.size === "icon") {
        return "h-5 w-5";
    }

    if (props.size === "nav") {
        return "h-4 w-4 shrink-0";
    }

    return "h-5 w-5 shrink-0";
});

const placement = computed(() => {
    if (props.size === "icon") {
        return "right-start";
    }

    if (props.size === "nav") {
        return "bottom-end";
    }

    return "bottom-start";
});

function toggle() {
    open.value = !open.value;
}

function close() {
    open.value = false;
}

function onEscape(event) {
    if (event.key === "Escape") {
        close();
    }
}

watch(open, (isOpen) => {
    if (isOpen) {
        nextTick(() => {
            if (!triggerRef.value || !panelRef.value) {
                return;
            }

            popper = new Popper(triggerRef.value, panelRef.value, {
                placement: placement.value,
                modifiers: {
                    preventOverflow: {
                        boundariesElement: "viewport",
                    },
                },
            });
        });

        return;
    }

    if (popper) {
        setTimeout(() => {
            popper?.destroy();
            popper = null;
        }, 100);
    }
});

onMounted(() => {
    document.addEventListener("keydown", onEscape);
});

onUnmounted(() => {
    document.removeEventListener("keydown", onEscape);
    popper?.destroy();
});
</script>
