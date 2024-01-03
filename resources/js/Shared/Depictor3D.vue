<template>
    <div class="mx-auto w-100">
        <iframe
            width="100%"
            :height="height - 65"
            :src="$page.props.CM_API + 'depict/3D?smiles=' + encodedSmiles"
            frameborder="0"
        ></iframe>
        <div
            v-if="showDownload"
            class="cursor-pointer mt-1 text-sm text-gray-900 float-right"
            @click="
                (e) =>
                    downloadMolFile3D(
                        e,
                        molecule,
                        identifier,
                        $page.props.CM_API
                    )
            "
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
    data() {
        return {
            results: [],
        };
    },
    computed: {
        encodedSmiles() {
            return encodeURIComponent(this.molecule);
        },
    },
    mounted() {},
    methods: {},
};
</script>
