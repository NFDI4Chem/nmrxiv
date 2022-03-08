<template>
  <jet-form-section @submitted="updateProfileInformation">
    <template #title> Profile Information </template>

    <template #description>
      Update user's profile information and email address.
    </template>

    <template #form>
      <!-- Profile Photo -->
      <div
        class="col-span-6 sm:col-span-4"
        v-if="user && $page.props.jetstream.managesProfilePhotos"
      >
        <!-- Profile Photo File Input -->
        <input type="file" class="hidden" ref="photo" @change="updatePhotoPreview" />

        <jet-label for="photo" value="Photo" />

        <!-- Current Profile Photo -->
        <div v-if="user" class="mt-2" v-show="!photoPreview">
          <img
            :src="user.profile_photo_url"
            :alt="user.first_name"
            class="rounded-full h-20 w-20 object-cover"
          />
        </div>

        <!-- New Profile Photo Preview -->
        <div class="mt-2" v-show="photoPreview">
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
          type="button"
          class="mt-2"
          @click.prevent="deletePhoto"
          v-if="user && user.profile_photo_url"
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
          type="text"
          class="mt-1 block w-full"
          v-model="form.first_name"
          autocomplete="first_name"
        />
        <jet-input-error :message="form.errors.first_name" class="mt-2" />
      </div>

      <!-- Last Name -->
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="last_name" value="Last Name" />
        <jet-input
          id="last_name"
          type="text"
          class="mt-1 block w-full"
          v-model="form.last_name"
          autocomplete="last_name"
        />
        <jet-input-error :message="form.errors.last_name" class="mt-2" />
      </div>

      <!-- Email -->
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="email" value="Email" />
        <jet-input
          id="email"
          type="email"
          class="mt-1 block w-full"
          v-model="form.email"
        />
        <jet-input-error :message="form.errors.email" class="mt-2" />
      </div>

      <div v-if="!user" class="col-span-6 sm:col-span-4">
        <jet-label for="password" value="Password" />
        <jet-input
          id="password"
          type="password"
          class="mt-1 block w-full"
          v-model="form.password"
          ref="password"
          autocomplete="new-password"
        />
        <jet-input-error :message="form.errors.password" class="mt-2" />
      </div>

      <div v-if="!user" class="col-span-6 sm:col-span-4">
        <jet-label for="password_confirmation" value="Confirm Password" />
        <jet-input
          id="password_confirmation"
          type="password"
          class="mt-1 block w-full"
          v-model="form.password_confirmation"
          autocomplete="new-password"
        />
        <jet-input-error :message="form.errors.password_confirmation" class="mt-2" />
      </div>
    </template>

    <template #actions>
      <jet-secondary-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
          @click="back"
        >
          Cancel
      </jet-secondary-button>
      <jet-action-message :on="form.recentlySuccessful" class="mr-3">
        Saved.
      </jet-action-message>

      <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
        Save
      </jet-button>
    </template>
  </jet-form-section>
</template>

<script>
import JetButton from "@/Jetstream/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";

export default {
  components: {
    JetActionMessage,
    JetButton,
    JetFormSection,
    JetInput,
    JetInputError,
    JetLabel,
    JetSecondaryButton,
  },

  props: ["user"],

  data() {
    return {
      form: this.$inertia.form({
        _method: this.user ? "PUT" : "POST",
        first_name: this.user ? this.user.first_name : "",
        last_name: this.user ? this.user.last_name : "",
        email: this.user ? this.user.email : "",
        password: null,
        password_confirmation: null,
        terms: true,
        photo: null,
      }),

      photoPreview: null,
    };
  },

  methods: {
    updateProfileInformation() {
      if (this.$refs.photo) {
        this.form.photo = this.$refs.photo.files[0];
      }

      if (this.user) {
        this.form.post(route("users.update", this.user.id), {
          errorBag: "updateProfileInformation",
          preserveScroll: true,
          onSuccess: () => this.clearPhotoFileInput(),
        });
      } else {
        this.form.post(route("users.store"), {
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