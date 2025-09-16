<template>
    <div>
        <SpectraViewer
            ref="spectraViewerREF"
            :study="study.data"
        ></SpectraViewer>
    </div>
</template>

<script>
import SpectraViewer from "@/Shared/SpectraViewer.vue";

export default {
    components: {
        SpectraViewer,
    },
    props: ["study"],
    data() {
        return {};
    },
    computed: {},
    mounted() {},
    methods: {},
};
</script>
