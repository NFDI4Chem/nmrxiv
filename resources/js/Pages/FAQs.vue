<template>
    <div class="bg-white min-h-screen flex flex-col">
        <Head title="FAQs - nmrXiv"></Head>
        <FlashMessages />
        <main class="flex-1">
            <PublicSiteHeader :bordered="false" />

            <FAQs />
        </main>

        <Footer />
    </div>
</template>

<script>
import { Head } from "@inertiajs/vue3";
import FlashMessages from "@/Shared/FlashMessages.vue";
import Footer from "@/Shared/Footer.vue";
import PublicSiteHeader from "@/Shared/PublicSiteHeader.vue";
import FAQs from "@/App/FAQs.vue";

export default {
    components: {
        Head,
        FlashMessages,
        Footer,
        PublicSiteHeader,
        FAQs,
    },
};
</script>
