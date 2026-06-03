<template>
    <div>
        <span v-if="mode == 'button'">
            <span v-if="!$page.props.auth.user?.email">
                <Link
                    href="/login"
                    class="deposit-data-cta"
                    :class="buttonClasses"
                >
                    <ArrowUpTrayIcon class="mr-2 h-5 w-5 shrink-0" />
                    Deposit data
                </Link>
            </span>
            <span v-else>
                <Link
                    id="tour-step-upload"
                    :href="route('upload')"
                    class="deposit-data-cta"
                    :class="buttonClasses"
                >
                    <ArrowUpTrayIcon class="mr-2 h-5 w-5 shrink-0" />
                    Deposit data
                </Link>
            </span>
        </span>
        <span v-if="mode == 'icon'">
            <span v-if="!$page.props.auth.user">
                <Link
                    href="/login"
                    class="deposit-data-cta"
                    :class="iconClasses"
                >
                    <ArrowUpTrayIcon class="h-5 w-5" aria-hidden="true" />
                    <span class="sr-only">Deposit data</span>
                </Link>
            </span>
            <span v-else>
                <Link
                    id="tour-step-upload"
                    :href="route('upload')"
                    class="deposit-data-cta"
                    :class="iconClasses"
                >
                    <ArrowUpTrayIcon class="h-5 w-5" aria-hidden="true" />
                    <span class="sr-only">Deposit data</span>
                </Link>
            </span>
        </span>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import { ArrowUpTrayIcon } from "@heroicons/vue/24/solid";
import { depositButtonClasses } from "@/Utils/depositButtonClasses";

export default {
    components: {
        Link,
        ArrowUpTrayIcon,
    },
    props: {
        mode: String,
        compact: {
            type: Boolean,
            default: false,
        },
    },
    computed: {
        buttonClasses() {
            return depositButtonClasses({
                size: this.compact ? "sidebarCompact" : "sidebar",
            });
        },
        iconClasses() {
            return depositButtonClasses({ size: "icon" });
        },
    },
};
</script>
