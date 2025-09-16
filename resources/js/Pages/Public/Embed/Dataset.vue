<template>
    <div>
        <SpectraViewer
            ref="spectraViewerREF"
            :dataset="dataset.data"
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
    props: ["dataset", "study"],
    data() {
        return {};
    },
    computed: {},
    mounted() {},
    methods: {},
};
</script>
