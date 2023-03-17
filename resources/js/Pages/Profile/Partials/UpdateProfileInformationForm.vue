<template>
    <jet-form-section @submitted="updateProfileInformation">
        <template #title> Profile Information </template>

        <template #description>
            Update your account's profile information and email address.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div
                v-if="$page.props.jetstream.managesProfilePhotos"
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
                <div v-show="!photoPreview" class="mt-2">
                    <img
                        :src="user.profile_photo_url"
                        :alt="user.first_name + ' ' + user.last_name"
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
                    v-if="user.profile_photo_path"
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
                    >
                        <button type="button" class="" @click="findOrcidID()">
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
                <jet-input-error :message="this.error.orcid" class="mt-2" />
            </div>

            <!-- Affiliation -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="affiliation" value="Affiliation" />
                <jet-input
                    id="affiliation"
                    v-model="form.affiliation"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="affiliation"
                />
                <jet-input-error
                    :message="form.errors.affiliation"
                    class="mt-2"
                />
            </div>
        </template>

        <template #actions>
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
    <select-orcid-id
        ref="selectOrcidIdElement"
        v-model:selected="this.form.orcid_id"
        v-model:affiliation="this.form.affiliation"
    />
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
import { ref } from "vue";

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
                _method: "PUT",
                first_name: this.user.first_name,
                last_name: this.user.last_name,
                email: this.user.email,
                username: this.user.username,
                orcid_id: this.user.orcid_id,
                affiliation: this.user.affiliation,
                photo: null,
            }),
            error: {},
            photoPreview: null,
        };
    },
    setup() {
        const selectOrcidIdElement = ref(null);
        return {
            selectOrcidIdElement,
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

            this.form.post(route("user-profile-information.update"), {
                errorBag: "updateProfileInformation",
                preserveScroll: true,
                onSuccess: () => this.clearPhotoFileInput(),
            });
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
            this.$inertia.delete(route("current-user-photo.destroy"), {
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
    },
};
</script>
