<template>
    <Head title="Register" />
    <announcement-banner />
    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo />
        </template>

        <jet-validation-errors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <div v-if= "$page.props.environment.toLowerCase() != 'production' " class="pb-4">
                    <div
                        class="border border-teal-500 rounded px-4 py-3 mt-3 max-w-2xl text-sm text-gray-700 font-bold"
                    >
                        <p>
                            Warning: This site is for demonstration purpose
                            only. You can test most of the nmrXiv features but
                            DO NOT use the current site for your work. All the data
                            stored here can be reset anytime.
                        </p>
                    </div>
                </div>
                <jet-label for="first_name" value="First Name" />
                <jet-input
                    id="first_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.first_name"
                    required
                    autofocus
                    autocomplete="first_name"
                />
            </div>

            <div class="mt-4">
                <jet-label for="last_name" value="Last Name" />
                <jet-input
                    id="last_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.last_name"
                    required
                    autofocus
                    autocomplete="last_name"
                />
            </div>

            <div class="mt-4">
                <jet-label for="email" value="Email" />
                <jet-input
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                />
            </div>

            <div class="mt-4">
                <jet-label for="password" value="Password" />
                <jet-input
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
            </div>

            <div class="mt-4">
                <jet-label
                    for="password_confirmation"
                    value="Confirm Password"
                />
                <jet-input
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
            </div>

            <div
                class="mt-4"
                v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature"
            >
                <jet-label for="terms">
                    <div class="flex items-center">
                        <jet-checkbox
                            name="terms"
                            id="terms"
                            v-model:checked="form.terms"
                        />

                        <div class="ml-2">
                            I agree to the
                            <a
                                target="_blank"
                                :href="route('terms.show')"
                                class="underline text-sm text-gray-600 hover:text-gray-900"
                                >Terms of Service</a
                            >
                            and
                            <a
                                target="_blank"
                                :href="route('policy.show')"
                                class="underline text-sm text-gray-600 hover:text-gray-900"
                                >Privacy Policy</a
                            >
                        </div>
                    </div>
                </jet-label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <jet-button
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-black-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </jet-button>
            </div>

            <div class="flex items-center justify-center mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900"
                >
                    Already registered? Login here
                </Link>
            </div>
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
import { Head, Link } from "@inertiajs/inertia-vue3";
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
        Link,
        AnnouncementBanner,
    },

    data() {
        return {
            form: this.$inertia.form({
                first_name: "",
                last_name: "",
                email: "",
                password: "",
                password_confirmation: "",
                terms: false,
            }),
        };
    },

    methods: {
        submit() {
            this.form.post(this.route("register"), {
                onFinish: () =>
                    this.form.reset("password", "password_confirmation"),
            });
        },
    },
};
</script>
