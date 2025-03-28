<template>
    <Head title="Log in" />
    <announcement-banner />

    <jet-authentication-card class="index_beams">
        <template #logo>
            <jet-authentication-card-logo />
        </template>
        <jet-validation-errors class="mb-4" />

        <div v-if="$page.props.flash.message" class="alert">
            {{ $page.props.flash.message }}
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <div
                    v-if="$page.props.environment.toLowerCase() != 'production'"
                    class="pb-4"
                >
                    <div
                        class="border border-teal-500 rounded px-4 py-3 mt-3 max-w-2xl text-sm text-gray-700 font-bold"
                    >
                        <p>
                            <span style="color: red">Warning:</span> This site
                            is for demonstration purpose only. You can test most
                            of the nmrXiv features but DO NOT use the current
                            site for your work. All the data stored here can be
                            reset anytime. For real data please visit
                            <a
                                href="https://nmrxiv.org"
                                target="_blank"
                                style="color: teal"
                                >nmrxiv.org.</a
                            >
                        </p>
                    </div>
                </div>
                <jet-label for="email" value="Email" />
                <jet-input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                />
            </div>

            <div class="mt-4">
                <jet-label for="password" value="Password" />
                <jet-input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
            </div>

            <div class="flex items-center justify-between mt-4">
                <div class="flex items-center">
                    <label class="flex items-center">
                        <jet-checkbox
                            v-model:checked="form.remember"
                            name="remember"
                        />
                        <span class="ml-2 text-sm text-gray-600"
                            >Remember me</span
                        >
                    </label>
                </div>

                <div class="text-sm">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="underline text-sm text-gray-600 hover:text-gray-900"
                    >
                        Forgot your password?
                    </Link>
                </div>
            </div>

            <div class="mt-4">
                <jet-button
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </jet-button>
            </div>

            <div class="flex items-center justify-center mt-4">
                <Link
                    :href="route('register')"
                    class="underline text-sm text-gray-600 hover:text-gray-900"
                >
                    Not registered? Register here
                </Link>
            </div>

            <single-sign-on />
        </form>
    </jet-authentication-card>
</template>

<script>
import JetAuthenticationCard from "@/Jetstream/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import SingleSignOn from "@/App/SingleSignOn.vue";
import { Head, Link } from "@inertiajs/vue3";
import AnnouncementBanner from "@/Shared/AnnouncementBanner.vue";

export default {
    components: {
        Head,
        JetAuthenticationCard,
        JetAuthenticationCardLogo,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetValidationErrors,
        SingleSignOn,
        Link,
        AnnouncementBanner,
    },

    props: {
        canResetPassword: Boolean,
        status: String,
    },

    data() {
        return {
            form: this.$inertia.form({
                email: "",
                password: "",
                remember: false,
            }),
        };
    },
    methods: {
        submit() {
            this.form
                .transform((data) => ({
                    ...data,
                    remember: this.form.remember ? "on" : "",
                }))
                .post(this.route("login"), {
                    onFinish: () => this.form.reset("password"),
                });
        },
    },
};
</script>
