<template>
    <jet-action-section>
        <template #title>
            Delete Project
        </template>

        <template #description>
            Permanently delete your project.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                Once your project is deleted, all of its resources and data will be permanently deleted. Before deleting your project, please download any data or information that you wish to retain.
            </div>

            <div class="mt-5">
                    <jet-danger-button @click="confirmProjectDeletion">
                        Delete Project
                    </jet-danger-button>
            </div>

            <!-- Delete Project Confirmation Modal  -->
            <jet-dialog-modal :show="confirmingProjectDeletion" @close="closeModal">
                <template #title>
                    Delete Project
                </template>
                <template #content>
                    Are you sure you want to delete your project? Once your project is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your project.

                    <div class="mt-4">
                        <jet-input type="password" class="mt-1 block w-3/4" placeholder="Password"
                                    ref="password"
                                    v-model="form.password"
                                    @keyup.enter="deleteProject" />

                        <jet-input-error :message="form.errors.password" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <jet-secondary-button @click="closeModal">
                        Cancel
                    </jet-secondary-button>

                    <jet-danger-button class="ml-2" @click="deleteProject" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Delete Project
                    </jet-danger-button>
                </template>
            </jet-dialog-modal>

            <!-- Delete Project Confirmation Modal if User doesnot have password -->
            <jet-dialog-modal :show="confirmingProjectDeletionWithoutPassword" @close="closeModal">
                <template #title>
                    Delete Project
                </template>

                <template #content>
                    You need to set your password first before deleting the project.
                </template>

                <template #footer>
                    <jet-secondary-button @click="closeModal">
                        Close
                    </jet-secondary-button>
                </template>
            </jet-dialog-modal>
        </template>
    </jet-action-section>
</template>

<script>
    import JetActionSection from '@/Jetstream/ActionSection.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import JetDangerButton from '@/Jetstream/DangerButton.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
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
                confirmingProjectDeletion: false,
                confirmingProjectDeletionWithoutPassword: false,

                form: this.$inertia.form({
                    password: '',
                }),

                hasPassword: false
            }
        },

        props: ['project'],

        methods: {
            confirmProjectDeletion() {
                console.log('check password starts..');
                axios.get(route('projects.checkIfUserHasPassword', this.project.id)).then(res => {
                    this.hasPassword = res.data.hasPassword;
                }).finally(() => { 
                    if(this.hasPassword)
                        this.confirmingProjectDeletion = true;
                    else 
                        this.confirmingProjectDeletionWithoutPassword = true;
                });

               setTimeout(() => this.$refs.password.focus(), 250)
            },

            deleteProject() {
                this.form.delete(route('project.destroy', this.project.id), {
                    preserveScroll: true,
                    onSuccess: () => this.closeModal(),
                    onError: () => this.$refs.password.focus(),
                    onFinish: () => this.form.reset(),
                })
            },

            // setPassword(){
            //    this.form.post(this.route('password.request'));
            // },

            closeModal() {
                this.confirmingProjectDeletion = false
                this.confirmingProjectDeletionWithoutPassword = false
                this.form.reset()
            },

        },
    }
</script>
