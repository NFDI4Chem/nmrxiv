<template>
  <jet-action-section>
    <template #title> Delete Project </template>

    <template #description> Permanently delete your project. </template>

    <template #content>
      <div class="max-w-xl text-sm text-gray-600">
        Once your project is deleted, all of its resources and data will be permanently
        deleted. Before deleting your project, please download any data or information
        that you wish to retain.
      </div>

      <div class="mt-5">
        <jet-danger-button @click="confirmProjectDeletion">
          Delete Project
        </jet-danger-button>
      </div>

      <!-- Delete Project Confirmation Modal  -->
      <jet-dialog-modal :show="confirmingProjectDeletion" @close="closeModal">
        <template #title> Delete Project </template>
        <template #content>
          <span v-if="loading">
            <svg
              class="animate-spin -ml-1 mr-3 h-5 w-5 text-dark"
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
              Loading
            </svg>
          </span>
          <span v-else>
            <span v-if="hasPassword">
              Are you sure you want to delete your project? Once your project is deleted,
              all of its resources and data will be permanently deleted. Please enter your
              password to confirm you would like to permanently delete your project.

              <div class="mt-4">
                <jet-input
                  type="password"
                  class="mt-1 block w-3/4"
                  placeholder="Password"
                  ref="password"
                  v-model="form.password"
                  @keyup.enter="deleteProject"
                />
                <jet-input-error :message="form.errors.password" class="mt-2" />
              </div>
            </span>
            <span v-else>
              You need to set your password first before deleting the project.
            </span>
          </span>
        </template>

        <template v-if="!loading" #footer>
          <jet-secondary-button @click="closeModal"> Cancel </jet-secondary-button>

          <jet-danger-button
            v-if="hasPassword"
            class="ml-2"
            @click="deleteProject"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Delete Project
          </jet-danger-button>
        </template>
      </jet-dialog-modal>
    </template>
  </jet-action-section>
</template>

<script>
import JetActionSection from "@/Jetstream/ActionSection.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import { Link } from "@inertiajs/inertia-vue3";

export default {
  components: {
    JetActionSection,
    JetDangerButton,
    JetDialogModal,
    JetInput,
    JetInputError,
    JetSecondaryButton,
    Link,
  },

  data() {
    return {
      loading: false,
      confirmingProjectDeletion: false,
      confirmingProjectDeletionWithoutPassword: false,

      form: this.$inertia.form({
        password: "",
      }),

      hasPassword: false,
    };
  },

  props: ["project"],

  methods: {
    confirmProjectDeletion() {
      this.loading = true;
      this.confirmingProjectDeletion = true;
      axios
        .get(route("projects.checkIfUserHasPassword", this.project.id))
        .then((res) => {
          this.hasPassword = res.data.hasPassword;
        })
        .finally(() => {
          this.loading = false;
        });
    },

    deleteProject() {
      this.form.delete(route("project.destroy", this.project.id), {
        preserveScroll: true,
        onSuccess: () => this.closeModal(),
        onError: () => this.$refs.password.focus(),
        onFinish: () => this.form.reset(),
      });
    },

    closeModal() {
      this.confirmingProjectDeletion = false;
      this.confirmingProjectDeletionWithoutPassword = false;
      this.form.reset();
    },
  },
};
</script>
