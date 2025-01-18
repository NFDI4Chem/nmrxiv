<template>
    <jet-dialog-modal
        :show="showDialog"
        :max-width="'6xl'"
        @close="showDialog = false"
    >
        <template #title> Add Your Sample to a Published Project </template>
        <template #content>
            <div>
                Find a project to publish your sample under. You can pick only a
                project that has been published by you. <br />
                The sample/s will get the same license as the chosen project and
                will be made public shortly after publishing without embargo.
                <br /><br />
            </div>

            <div v-if="projects && projects.length">
                <select-rich
                    v-model:selected="project_object"
                    :items="projects"
                    @update:selected="updateProjectName"
                />
            </div>
            <div v-else>
                <p>No projects available.</p>
            </div>
            <jet-secondary-button
                class="float-left text-md font-bold text-white mt-8 mr-2 bg-green-600"
                @click="showPublishConfirmationModal = true"
            >
                Pick project
            </jet-secondary-button>
            <jet-confirmation-modal
                :show="showPublishConfirmationModal"
                @close="showPublishConfirmationModal = false"
            >
                <template #title> Are you sure you want to pick that project? </template>
                <template #content>
                    Once you confirm, all the fields you have filled in the last step will be overwritten by the 
                    selected project details. 
                </template>
                <template #footer>
                    <jet-secondary-button
                        @click="showPublishConfirmationModal = false"
                    >
                        Cancel
                    </jet-secondary-button>
                    <jet-secondary-button
                        class="ml-2 font-bold text-white bg-green-600"
                        @click="save"
                    >
                        Pick project
                    </jet-secondary-button>
                </template>
            </jet-confirmation-modal>
        </template>
        <template #footer>
            <div class="flex">
                <jet-secondary-button class="float-left" @click="onClose">
                    Close
                </jet-secondary-button>
            </div>
        </template>
    </jet-dialog-modal>
</template>

<script>
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import SelectRich from "@/Shared/SelectRich.vue";
import axios from "axios";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";

export default {
    components: {
        JetDialogModal,
        SelectRich,
        JetSecondaryButton,
        JetConfirmationModal,
    },
    props: {
        project: Object,
        study: Object,
    },

    data() {
        return {
            name: "",
            showPublishConfirmationModal: false,
            showDialog: false,
            project_object: null,
            project_id: null,
            license_id: null,
            projects: [],
            studies: [],
            user: null,
        };
    },

    mounted() {
        if (this.project) {
            this.loadInitial();
            this.user = this.project.owner_id;
            this.loadProjects();
        } else {
            this.loadInitial();
        }
    },

    methods: {
        loadInitial() {
            if (this.project_object) {
                this.project_object = this.project_object;
                this.project_id = this.project_object.id;
                this.project_name = this.project_object.name;
                this.license_id = this.project_object.license_id;
            }
        },
        updateProjectName() {
            this.project_id = this.project_object.id;
            this.name = this.project_object.name;
            this.license_id = this.project_object.license_id;
        },
        loadProjects() {
            const url = route("user.publicProjects", { user: this.user });
            axios
                .get(url)
                .then((res) => {
                    this.projects = res.data;
                })
                .catch((error) => {
                    console.error(
                        "There was an error fetching the projects:",
                        error
                    );
                });
        },
        toggleDialog() {
            this.showDialog = !this.showDialog;
        },
        onClose() {
            this.showDialog = false;
        },

        save() {
            this.showPublishConfirmationModal = false;
            if (this.project_id) {
                this.$emit("selected_project", this.project_object);
            }
            this.onClose();
        },
    },
};
</script>
