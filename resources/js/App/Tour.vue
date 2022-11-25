<template>
    <div>
        <v-tour name="appTour" :steps="steps"></v-tour>
    </div>
</template>

<script>
export default {
    name: "AppTour",
    data() {
        return {
            steps: [
                {
                    target: "#tour-step-upload",
                    header: {
                        title: "Get Started",
                    },
                    content: `Click to start uploading <br/> your datasets to nmrXiv. <br/> <small>Check out our guides  <br/>to prepare datasets <br/>for uploading <a class="text-green-400 hover:text-decoration-line" href="https://docs.nmrxiv.org" target="_blank">here</a>.</small>`,
                },
                {
                    target: "#tour-step-dashboard",
                    header: {
                        title: "Your space: Dashboard",
                    },
                    content: `<small>You can find all your work<br/> organised under this section.<br/> All your active projects <br/>and quicklinks are accessible <br/> from your dashboard. <br/>Please remember you will have <br/>dedicated dashboards for you <br/>and your teams.</small>`,
                },
                {
                    target: "#tour-step-shared-with-me",
                    header: {
                        title: "Your space: Shared with me",
                    },
                    content: `<small>Projects and studies shared<br/> with you can be accessed from here.</small>`,
                },
                {
                    target: "#tour-step-recent",
                    header: {
                        title: "Your space: Recent",
                    },
                    content: `<small>Quick access to recently<br/> edited projects and studies.</small>`,
                },
                {
                    target: "#tour-step-starred",
                    header: {
                        title: "Your space: Starred",
                    },
                    content: `<small>Projects and studies that you star<br/> can be accessed quickly from this view.</small>`,
                },
                {
                    target: "#tour-step-trash",
                    header: {
                        title: "Your space: Trash",
                    },
                    content: `<small>Restore your deleted projects and studies <br/> (within 30 days) from here.</small>`,
                },
                {
                    target: "#tour-step-submission-guide",
                    header: {
                        title: "Guides on Submission",
                    },
                    content: `<small>Check out our submission guides any time for more information. Here is a quick link for you right from your dashboard.</small>`,
                },
                {
                    target: "#tour-step-api",
                    header: {
                        title: "API",
                    },
                    content: `<small>Developing a tool? Check out our API to access our data.</small>`,
                },
                {
                    target: "#tour-step-spectra-challenge",
                    header: {
                        title: "Spectra Challenges",
                    },
                    content: `<small>Are you proficient in structure ellucidation? Stay tuned for new challenges.</small>`,
                },
                {
                    target: "#tour-step-get-in-touch",
                    header: {
                        title: "Need help! Email us",
                    },
                    content: `<small>Any queries? Write to us via email.<br/>We are happy to help!</small>`,
                },
                {
                    target: "#tour-step-support-bubble",
                    header: {
                        title: "Help! Chat window",
                    },
                    content: `<small>Reach out to us with any questions, suggestions or bugs. We will get back to you as soon as we can.</small>`,
                },
                {
                    target: "#tour-step-documentation",
                    header: {
                        title: "Documentation site",
                    },
                    content: `<small>Check out our documentation site for the installation, submission guides, design principles and architectural overview.</small>`,
                },
                {
                    target: "#tour-step-account-management",
                    header: {
                        title: "Account management",
                    },
                    content: `<small>Manage your teams, profile and other settings from the drop down here.</small>`,
                },

                {
                    target: "#tour-step-upload",
                    header: {
                        title: "Let's upload a demo dataset",
                    },
                    content: `<small>Click here to start uploading.</small>`,
                    before: (type) =>
                        new Promise((resolve, reject) => {
                            this.emitter.emit("openDatasetCreateDialog", {});
                            resolve("upload dialog open");
                        }),
                },
                {
                    target: "#tour-step-submission-header",
                    header: {
                        title: "Files upload",
                    },
                    content: `<small>Lets provide some basic details about your submission and then upload files.</small>`,
                },
                {
                    target: "#tour-step-project-name",
                    header: {
                        title: "Project Name",
                    },
                    content: `<small>Enter a suitable name for your project.</small>`,
                },
                {
                    target: "#tour-step-project-desc",
                    header: {
                        title: "Project Description",
                    },
                    content: `<small>Briefly describe your project in not less than 20 words.</small>`,
                },
                {
                    target: "#tour-step-project-keywords",
                    header: {
                        title: "Project Keywords",
                    },
                    content: `<small>Classify your project </br>by entering keywords or tags.</small>`,
                },
                {
                    target: "#tour-step-upload-spectra",
                    header: {
                        title: "Upload Spectra files",
                    },
                    content: `<small>Lets start uploading your spectra data. <a class="text-green-400 hover:text-decoration-line" href="https://drive.google.com/drive/folders/1PS9jYkjCl_2iwwpod7rkxsmiH32QSgCM" target="_blank">Here</a> is an example dataset. Download and then drag and drop the files here.</small>`,
                },
                {
                    target: "#tour-step-proceed-from-step-1",
                    header: {
                        title: "Proceed",
                    },
                    content: `<small>Once the upload is finish. Press on the Proceed button to continue.</small>`,
                },
                {
                    target: "#tour-step-submission-header",
                    header: {
                        title: "Assignment and Meta data",
                    },
                    content: `<small>In this step you can use NMRium to edit your spectra and assign the peaks to the corresponding chemical groups, additionally, you can provide the metadata of the studies and samples.</small>`,
                },
                {
                    target: "#tour-step-side-panel-studies",
                    header: {
                        title: "Studies",
                    },
                    content: `<small>In this panel, you have a view of all the studies in the project with the datasets they include.</small> `,
                },
                {
                    target: "#tour-step-select-exp",
                    header: {
                        title: "Select Experiments",
                    },
                    content: `<small>You can navigate through different datasets (experiments) within the study through this dropdown.</small>`,
                },
                {
                    target: "#tour-step-nmrium",
                    header: {
                        title: "NMRium",
                    },
                    content: `<small>Visualize and analyse your spectra data using NMRium.<br/> You can visualize both 1D and 2D spectra, do smart peak picking and much more. <br/>Click <a class="text-green-400 hover:text-decoration-line" href="https://docs.nmrxiv.org/docs/introduction/nmrium" target="_blank">here</a> to learn more about NMRium.</small> `,
                },
                {
                    target: "#tour-step-spectra-info",
                    header: {
                        title: "Spectra Info",
                    },
                    content: `<small>In the Info table, you get all the information extracted about the NMR experiment.</small>`,
                },
                // {
                //     target: "#sample-info",
                //     header: {
                //         title: "Sample Info.",
                //     },
                //     content: `<small></small>`,
                // },
                // {
                //     target: "#meta-data",
                //     header: {
                //         title: "Metadata.",
                //     },
                //     content: `<small></small>`,
                // },
                {
                    target: "#tour-step-proceed-from-step-2",
                    header: {
                        title: "Proceed",
                    },
                    content: `<small>Click on proceed to submit your data and allow us for further processing.</small>`,
                },
                {
                    target: "#tour-step-queue",
                    header: {
                        title: "Queued",
                    },
                    content: `<small>Give us some moment to process your submission. <br/>You will automatically be routed to next step and also recieve an email once the process is complete.</small>`,
                },
                {
                    target: "#tour-step-submission-header",
                    header: {
                        title: "Complete",
                    },
                    content: `<small>GREAT!! <br/>Your data is now submitted to us and you are almost done with the submission process. </br>Here you find the summary of the project and we ask you to update few of the essential info to make your project complete. </small>`,
                },
                {
                    target: "#tour-step-summary",
                    header: {
                        title: "Summary",
                    },
                    content: `<small>Checkout to find the quick summary of your Study(s) and the list of Dataset(s) within.</small>`,
                },
                {
                    target: "#tour-step-privacy",
                    header: {
                        title: "Privacy",
                    },
                    content: `<small>From here you can choose to keep your project as private or make it public for open access.</small>`,
                },
                {
                    target: "#tour-step-release-date",
                    header: {
                        title: "Release Date",
                    },
                    content: `<small>Choose a release date to auto publish your project on selected date and time.</small>`,
                },
                {
                    target: "#tour-step-license",
                    header: {
                        title: "License",
                    },
                    content: `<small>Add a license to your project from the drop down. </br> This step is required before making your project public.</small>`,
                },
                {
                    target: "#tour-step-save",
                    header: {
                        title: "Save",
                    },
                    content: `<small>Dont forget to click on Save after making any changes.</small>`,
                },
                {
                    target: "#tour-step-download",
                    header: {
                        title: "Download",
                    },
                    content: `<small>Here is the quick link to download your project.</small>`,
                },
                {
                    target: "#tour-step-finish",
                    header: {
                        title: "Finish",
                    },
                    content: `<small>Thanks for completing the tour!! Press on Finish once your final changes are done and you will be routed to the dashboard where you can find your project listed. </small>`,
                },
                /* {
                    target: "#tour-step-projects",
                    header: {
                        title: "Projects.",
                    },
                    content: `<small>Now that the project is created succesfully, you can find the list of your projects in this section.<br/> You can always visit your project by clicking on it and make changes if required.</small>`,
                },
                {
                    target: "#tour-step-tour",
                    header: {
                        title: "Tour",
                    },
                    content: `<small>Thanks for completing the tour!! <br/> You can always comeback and start this tour by clicking on this tour button.</small>`,
                },*/
            ],
        };
    },
    mounted: function () {},
    methods: {},
};
</script>
