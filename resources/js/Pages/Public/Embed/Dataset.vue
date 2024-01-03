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
import Depictor2D from "@/Shared/Depictor2D.vue";
import DOIBadge from "@/Shared/DOIBadge.vue";

export default {
    components: {
        SpectraViewer,
        Depictor2D,
        DOIBadge,
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
