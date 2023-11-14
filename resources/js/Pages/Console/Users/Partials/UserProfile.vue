<template>
    <jet-form-section @submitted="updateProfileInformation">
        <template #title> Profile Information </template>

        <template #description>
            Update user's profile information and email address.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div
                v-if="user && $page.props.jetstream.managesProfilePhotos"
                class="col-span-6 sm:col-span-4"
            >
                <!-- Profile Photo File Input -->
                <input
                    ref="photo"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                />

                <jet-label for="photo" value="Photo" />

                <!-- Current Profile Photo -->
                <div v-if="user" v-show="!photoPreview" class="mt-2">
                    <img
                        :src="user.profile_photo_url"
                        :alt="user.first_name"
                        class="rounded-full h-20 w-20 object-cover"
                    />
                </div>

                <!-- New Profile Photo Preview -->
                <div v-show="photoPreview" class="mt-2">
                    <span
                        class="block rounded-full w-20 h-20"
                        :style="
                            'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' +
                            photoPreview +
                            '\');'
                        "
                    >
                    </span>
                </div>

                <jet-secondary-button
                    class="mt-2 mr-2"
                    type="button"
                    @click.prevent="selectNewPhoto"
                >
                    Select A New Photo
                </jet-secondary-button>

                <jet-secondary-button
                    v-if="user && user.profile_photo_url"
                    type="button"
                    class="mt-2"
                    @click.prevent="deletePhoto"
                >
                    Remove Photo
                </jet-secondary-button>

                <jet-input-error :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- First Name -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="first_name" value="First Name" />
                <jet-input
                    id="first_name"
                    v-model="form.first_name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="first_name"
                />
                <jet-input-error
                    :message="form.errors.first_name"
                    class="mt-2"
                />
            </div>

            <!-- Last Name -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="last_name" value="Last Name" />
                <jet-input
                    id="last_name"
                    v-model="form.last_name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="last_name"
                />
                <jet-input-error
                    :message="form.errors.last_name"
                    class="mt-2"
                />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="email" value="Email" />
                <jet-input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                />
                <jet-input-error :message="form.errors.email" class="mt-2" />
            </div>

            <!-- Username -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="username" value="Username" />
                <jet-input
                    id="username"
                    v-model="form.username"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="username"
                />
                <jet-input-error :message="form.errors.username" class="mt-2" />
            </div>

            <!-- Orcid Id -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="orcid" value="ORCID iD" />
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
                    <div
                        class="tooltip -ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500 cursor-pointer"
                        @click="findOrcidID()"
                    >
                        <button type="button" class="">
                            <img
                                alt="ORCID logo"
                                src="https://orcid.org/assets/vectors/orcid.logo.icon.svg"
                                width="20"
                                height="20"
                            />
                        </button>
                        <span
                            class="bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextbottom"
                            >Click to find ORCID iD</span
                        >
                    </div>
                </div>
                <jet-input-error :message="error.orcid" class="mt-2" />
            </div>

            <div v-if="!user" class="col-span-6 sm:col-span-4">
                <jet-label for="password" value="Password" />
                <jet-input
                    id="password"
                    ref="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <jet-input-error :message="form.errors.password" class="mt-2" />
            </div>

            <div v-if="!user" class="col-span-6 sm:col-span-4">
                <jet-label
                    for="password_confirmation"
                    value="Confirm Password"
                />
                <jet-input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <jet-input-error
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                />
            </div>
        </template>

        <template #actions>
            <jet-secondary-button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="back"
            >
                Cancel
            </jet-secondary-button>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <jet-button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </jet-button>
        </template>
    </jet-form-section>
    <!-- Find ORCID iD Modal -->
    <!-- <select-orcid-id
        ref="selectOrcidIdElement"
        :orcidId="form.orcid_id"
    /> -->
</template>

<script>
import JetButton from "@/Jetstream/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import SelectOrcidId from "@/Shared/SelectOrcidId.vue";

export default {
    components: {
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        SelectOrcidId,
    },

    props: ["user"],

    data() {
        return {
            form: this.$inertia.form({
                _method: this.user ? "PUT" : "POST",
                first_name: this.user ? this.user.first_name : "",
                last_name: this.user ? this.user.last_name : "",
                email: this.user ? this.user.email : "",
                username: this.user ? this.user.username : "",
                orcid_id: this.user ? this.user.orcid_id : "",
                password: null,
                password_confirmation: null,
                terms: true,
                photo: null,
            }),
            error: {},
            photoPreview: null,
        };
    },

    methods: {
        findOrcidID() {
            this.error.orcid = "";
            if (this.form.first_name && this.form.last_name) {
                this.selectOrcidIdElement.findOrcidID(
                    this.form.first_name,
                    this.form.last_name
                );
            } else {
                this.error.orcid = "Please enter first name and last name";
            }
        },
        updateProfileInformation() {
            if (this.$refs.photo) {
                this.form.photo = this.$refs.photo.files[0];
            }

            if (this.user) {
                this.form.post(route("console.users.update", this.user.id), {
                    errorBag: "updateProfileInformation",
                    preserveScroll: true,
                    onSuccess: () => this.clearPhotoFileInput(),
                });
            } else {
                this.form.post(route("console.users.store"), {
                    errorBag: "updateProfileInformation",
                    preserveScroll: true,
                    onSuccess: () => this.clearPhotoFileInput(),
                });
            }
        },

        selectNewPhoto() {
            this.$refs.photo.click();
        },

        updatePhotoPreview() {
            const photo = this.$refs.photo.files[0];

            if (!photo) return;

            const reader = new FileReader();

            reader.onload = (e) => {
                this.photoPreview = e.target.result;
            };

            reader.readAsDataURL(photo);
        },

        deletePhoto() {
            this.$inertia.delete(route("users.destroy-photo", this.user.id), {
                preserveScroll: true,

                onSuccess: () => {
                    this.photoPreview = null;
                    this.clearPhotoFileInput();
                },
            });
        },

        clearPhotoFileInput() {
            if (this.$refs.photo?.value) {
                this.$refs.photo.value = null;
            }
        },

        back() {
            window.history.back();
        },
    },
};
</script>
