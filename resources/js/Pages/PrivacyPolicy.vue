<template>
    <Head title="Privacy Policy" />

    <div class="font-sans text-gray-900 antialiased">
        <div class="pt-4 bg-gray-100 index_beams">
            <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
                <div>
                    <jet-authentication-card-logo />
                </div>

                <div
                    class="w-full sm:max-w-2xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose mb-4"
                    v-html="policy"
                ></div>
            </div>
        </div>
    </div>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo.vue";

export default {
    components: {
        Head,
        JetAuthenticationCardLogo,
    },
    props: ["policy"],
};
</script>
