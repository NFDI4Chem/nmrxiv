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
                    content: `Click to create project, <br/> study or dataset!`,
                },
            ],
        };
    },
    mounted: function () {},
    methods: {},
};
</script>
