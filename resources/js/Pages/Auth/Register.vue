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
                <div
                    v-if="
                        $page.props.environment &&
                        $page.props.environment.toLowerCase() != 'production'
                    "
                    class="pb-4"
                >
                    <div
                        class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 mt-3 text-sm text-amber-800"
                    >
                        <p class="flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                            <span>
                                <strong class="font-semibold">Demo Environment:</strong> This site
                                is for demonstration purposes only. Please visit
                                <a
                                    href="https://nmrxiv.org"
                                    target="_blank"
                                    class="font-medium underline hover:text-amber-900 transition-colors"
                                    >nmrxiv.org</a
                                > for production use.
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            <!-- First Name -->
            <div class="mt-4">
                <jet-label
                    class="after:content-['*'] after:ml-0.5 after:text-red-500"
                    for="first_name"
                    value="First Name"
                />
                <jet-input
                    id="first_name"
                    v-model="form.first_name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="first_name"
                />
            </div>
            <!-- Last Name -->
            <div class="mt-4">
                <jet-label
                    class="after:content-['*'] after:ml-0.5 after:text-red-500"
                    for="last_name"
                    value="Last Name"
                />
                <jet-input
                    id="last_name"
                    v-model="form.last_name"
                    name="lastname"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="last_name"
                />
            </div>
            <!-- Email -->
            <div class="mt-4">
                <jet-label
                    class="after:content-['*'] after:ml-0.5 after:text-red-500"
                    for="email"
                    value="Email"
                />
                <jet-input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="email"
                />
            </div>
            <!-- Username -->
            <div class="mt-4">
                <jet-label
                    class="after:content-['*'] after:ml-0.5 after:text-red-500"
                    for="username"
                    value="Username"
                />
                <jet-input
                    id="username"
                    v-model="form.username"
                    type="text"
                    class="mt-1 block w-full"
                    required
                />
            </div>
            <!-- ORCID iD -->
            <div class="mt-4">
                <jet-label
                    class="after:content-['(optional)'] after:ml-0.5 after:text-gray-500"
                    for="orcid"
                    value="ORCID iD"
                />
                <div class="mt-1 flex rounded-md shadow-sm">
                    <div
                        class="relative flex items-stretch flex-grow focus-within:z-10"
                    >
                        <jet-input
                            id="orcid"
                            v-model="form.orcid_id"
                            type="text"
                            class="rounded-l-md focus:ring-indigo-200 focus:border-indigo-200 block w-full rounded-none sm:text-medium border-gray-300"
                        />
                    </div>
                    <button
                        type="button"
                        class="tooltip -ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="loading"
                        @click="findOrcidID()"
                    >
                        <svg
                            v-if="loading"
                            class="animate-spin h-5 w-5 text-gray-700"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            ></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        <img
                            v-else
                            alt="ORCID logo"
                            src="https://orcid.org/assets/vectors/orcid.logo.icon.svg"
                            class="w-6"
                        />
                        <span
                            v-if="!loading"
                            class="bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextbottom"
                            >Click to find ORCID iD</span
                        >
                    </button>
                </div>
                <jet-input-error :message="error.orcid" class="mt-2" />
            </div>
            <!-- Affiliation -->
            <div class="mt-4 col-span-6 sm:col-span-4">
                <jet-label
                    class="after:content-['(optional)'] after:ml-0.5 after:text-gray-500"
                    for="affiliation"
                    value="Affiliation"
                />
                <ror-affiliation-typeahead
                    v-model="form.affiliation"
                    v-model:ror-id="form.ror_id"
                    input-id="affiliation"
                    input-class="mt-1 block w-full"
                    placeholder=""
                />
                <p class="mt-1 text-xs text-gray-500">
                    Start typing to search for your organization. Select from
                    the dropdown or enter a custom name.
                </p>
                <jet-input-error :message="error.affiliation" class="mt-2" />
            </div>
            <!-- Password -->
            <div class="mt-4">
                <jet-label
                    class="after:content-['*'] after:ml-0.5 after:text-red-500"
                    for="password"
                    value="Password"
                />
                <jet-input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
            </div>
            <!-- Confirm Password -->
            <div class="mt-4">
                <jet-label
                    for="password_confirmation"
                    class="after:content-['*'] after:ml-0.5 after:text-red-500"
                    value="Confirm Password"
                />
                <jet-input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
            </div>

            <div
                v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature"
                class="mt-4"
            >
                <jet-label for="terms">
                    <div class="flex items-center">
                        <jet-checkbox
                            id="terms"
                            v-model:checked="form.terms"
                            name="terms"
                        />

                        <div class="ml-2">
                            I agree to the
                            <Link
                                target="_blank"
                                :href="route('terms.show')"
                                class="underline text-sm text-gray-600 hover:text-gray-900"
                                >Terms of Service</Link
                            >
                            and
                            <Link
                                target="_blank"
                                :href="route('policy.show')"
                                class="underline text-sm text-gray-600 hover:text-gray-900"
                                >Privacy Policy</Link
                            >
                        </div>
                    </div>
                </jet-label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <jet-button
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-full shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
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
    <!-- Find ORCID iD Modal -->
    <select-orcid-id
        ref="selectOrcidIdElement"
        v-model:orcid-id="form.orcid_id"
        v-model:affiliation="form.affiliation"
        @loading-complete="loading = false"
    />
</template>

<script>
import JetAuthenticationCard from "@/Jetstream/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import { Head, Link } from "@inertiajs/vue3";
import AnnouncementBanner from "@/Shared/AnnouncementBanner.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import SelectOrcidId from "@/Shared/SelectOrcidId.vue";
import RorAffiliationTypeahead from "@/Shared/RorAffiliationTypeahead.vue";
import { ref } from "vue";

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
        JetInputError,
        SelectOrcidId,
        RorAffiliationTypeahead,
    },
    setup() {
        const selectOrcidIdElement = ref(null);
        return {
            selectOrcidIdElement,
        };
    },

    data() {
        return {
            form: this.$inertia.form({
                first_name: "",
                last_name: "",
                email: "",
                username: "",
                orcid_id: "",
                affiliation: "",
                ror_id: "",
                password: "",
                password_confirmation: "",
                terms: false,
            }),
            orcidIdSearchResults: [],
            showOrcidIdDialog: false,
            loading: false,
            error: {},
        };
    },

    methods: {
        /**
         * Search for ORCID ID with validation and throttling
         * Prevents multiple simultaneous searches
         */
        findOrcidID() {
            // Prevent search if already loading
            if (this.loading) {
                return;
            }

            this.error.orcid = "";

            // Validate required fields
            if (!this.form.first_name || !this.form.last_name) {
                this.error.orcid = "Please enter first name and last name";
                return;
            }

            if (!this.form.first_name.trim() || !this.form.last_name.trim()) {
                this.error.orcid = "First name and last name cannot be empty";
                return;
            }

            this.loading = true;
            this.selectOrcidIdElement.findOrcidID(
                this.form.first_name.trim(),
                this.form.last_name.trim()
            );
        },

        submit() {
            this.form.post(this.route("register"), {
                onFinish: () =>
                    this.form.reset("password", "password_confirmation"),
            });
        },
    },
};
</script>
