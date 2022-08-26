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
                    target: "#v-step-0",
                    header: {
                        title: "Get Started",
                    },
                    content: `Click to start uploading <br/> your datasets to nmrXiv. <br/> <small>Check out our guides  <br/>to prepare datasets <br/>for uploading <a class="text-green-400 hover:text-decoration-line" href="https://docs.nmrxiv.org">here</a>.</small>`,
                },
                {
                    target: "#v-step-1",
                    header: {
                        title: "Your space: Dashboard",
                    },
                    content: `You can find all your work<br/> organised under this section.<br/> <small>All your active projects <br/>and quicklinks are accessible <br/> from your dashboard. <br/>Please remember you will have <br/>dedicated dashboards for you <br/>and your teams.</small>`,
                },
                {
                    target: "#v-step-2",
                    header: {
                        title: "Your space: Shared with me",
                    },
                    content: `Projects and studies shared<br/> with you can be accessed from here.`,
                },
                {
                    target: "#v-step-3",
                    header: {
                        title: "Your space: Recent",
                    },
                    content: `Quick access to recently<br/> edited projects and studies.`,
                },
                {
                    target: "#v-step-4",
                    header: {
                        title: "Your space: Starred",
                    },
                    content: `Projects, studies that you starred<br/> can be accessed quickly from this view.`,
                },
                {
                    target: "#v-step-5",
                    header: {
                        title: "Your space: Trash",
                    },
                    content: `Projects, studies deleted will<br/> be available here for you to restore if need.`,
                },
                {
                    target: "#v-step-6",
                    header: {
                        title: "Guides on Submission",
                    },
                    content: `Check out our submission guides any time for more information. Here is a quick link for you right from your dashboard.`,
                },
                {
                    target: "#v-step-7",
                    header: {
                        title: "API",
                    },
                    content: `Developing a tool? want to access data or submit programmatically? Check out our API`,
                },
                {
                    target: "#v-step-8",
                    header: {
                        title: "Spectra Challenges",
                    },
                    content: `Are you proficient in structure ellucidation? Stay tuned for new challenges.`,
                },
                {
                    target: "#v-step-9",
                    header: {
                        title: "Need help! Email us",
                    },
                    content: `You can write to us via email for any further queries. We are happy to help!`,
                },
                {
                    target: "#v-step-10",
                    header: {
                        title: "Help! Chat window",
                    },
                    content: `Reach out to us with any questions, suggestions or bugs. We will get back to you as soon as we can`,
                },
                {
                    target: "#v-step-11",
                    header: {
                        title: "Documentation site",
                    },
                    content: `Check out our documentation site for our installation & submission guies, design principles and architectural overview.`,
                },
                {
                    target: "#v-step-12",
                    header: {
                        title: "Tour",
                    },
                    content: `You can always comeback and start this tour using this tour button`,
                },
                {
                    target: "#v-step-13",
                    header: {
                        title: "Account management",
                    },
                    content: `You can manage your teams, profile and other settings from the drop down here.`,
                },
                {
                    target: "#v-step-0",
                    header: {
                        title: "Let's upload a demo dataset",
                    },
                    content: `Click here to start uploading`,
                    before: type => new Promise((resolve, reject) => {
                        this.emitter.emit("openDatasetCreateDialog", {});
                        resolve("upload dialog open");
                    })
                },
                {
                    target: "#v-step-14",
                    header: {
                        title: "Files upload",
                    },
                    content: `Lets provide some basic details about your submission and then upload files`,
                },
                {
                    target: "#v-step-15",
                    header: {
                        title: "Name",
                    },
                    content: `Project name`,
                },
                {
                    target: "#v-step-16",
                    header: {
                        title: "Description",
                    },
                    content: `Project Description`,
                },
                {
                    target: "#v-step-17",
                    header: {
                        title: "Keywords",
                    },
                    content: `Project keywords`,
                },
                {
                    target: "#v-step-18",
                    header: {
                        title: "Spectra files",
                    },
                    content: `Lets start uploading your spectra data. Here is an example dataset. Download and then drag and drop the files here. Once uploaded press next.`,
                }
            ],
        };
    },
    mounted: function () {},
    methods: {},
};
</script>
