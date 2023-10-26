<template>
    <div>
        <iframe
            :width="width"
            :height="height"
            :src="this.$page.props.CM_API + 'depict/3D?smiles=' + encodedSmiles"
            frameborder="0"
        ></iframe>
        <div
            v-if="showDownload"
            @click="
                (e) =>
                    downloadMolFile3D(
                        e,
                        molecule,
                        identifier,
                        this.$page.props.CM_API
                    )
            "
            class="cursor-pointer mt-1 text-sm text-gray-900 float-right"
        >
            <a style="box-shadow: none" class="hover:text-gray-600"
                >Download Molfile(3D)</a
            >
        </div>
    </div>
</template>
<script>
export default {
    components: {},
    props: {
        molecule: String,
        width: {
            type: Number,
            default: 300,
        },
        height: {
            type: Number,
            default: 300,
        },
        showDownload: {
            type: Boolean,
            default: false,
        },
        identifier: String,
    },
    computed: {
        encodedSmiles() {
            return encodeURIComponent(this.molecule);
        },
    },
    data() {
        return {
            results: [],
        };
    },
    mounted() {},
    methods: {},
};
</script>
