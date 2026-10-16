<template>
    <div class="border-b border-amber-200 bg-amber-50" role="status">
        <div
            class="mx-auto flex max-w-7xl items-start gap-3 px-4 py-4 sm:px-6 lg:px-8"
        >
            <ExclamationTriangleIcon
                class="mt-0.5 h-5 w-5 shrink-0 text-amber-600"
                aria-hidden="true"
            />
            <p class="text-sm leading-relaxed text-amber-950">
                <span class="font-semibold">Experimental feature.</span>
                Please use {{ feature }} with caution. It is still under
                development, and the results should be verified independently
                before you rely on them. If you need assistance, please contact
                us at
                <a
                    :href="mailtoHref"
                    class="font-medium underline decoration-amber-400 underline-offset-2 hover:text-amber-800"
                    >info.nmrxiv@uni-jena.de</a
                >
                or through the
                <a
                    href="https://helpdesk.nfdi4chem.de/"
                    class="font-medium underline decoration-amber-400 underline-offset-2 hover:text-amber-800"
                    target="_blank"
                    rel="noopener noreferrer"
                    >NFDI4Chem Help Desk</a
                >.
            </p>
        </div>
    </div>
</template>

<script>
import { computed } from "vue";
import { ExclamationTriangleIcon } from "@heroicons/vue/24/outline";

export default {
    components: {
        ExclamationTriangleIcon,
    },
    props: {
        feature: {
            type: String,
            required: true,
        },
    },
    setup(props) {
        const mailtoHref = computed(
            () =>
                `mailto:info.nmrxiv@uni-jena.de?subject=${encodeURIComponent(
                    props.feature
                )}`
        );

        return {
            mailtoHref,
        };
    },
};
</script>
