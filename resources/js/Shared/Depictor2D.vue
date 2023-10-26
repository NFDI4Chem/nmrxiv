<template>
    <div class="justify-center">
        <div
            v-if="source == 'ocl'"
            class="flex justify-center align-middle pt-5"
            v-html="getSVGString(molecule)"
        ></div>
        <div class="flex" v-else>
            <img
                class="mx-auto w-100"
                :src="
                    this.$page.props.CM_API +
                    'depict/2D?smiles=' +
                    encodedSmiles +
                    '&height=' +
                    height +
                    '&width=' +
                    width +
                    '&CIP=' +
                    CIP +
                    '&toolkit=rdkit'
                "
                alt=""
            />
        </div>
        <div
            v-if="showDownload"
            @click="
                (e) =>
                    downloadMolFile2D(
                        e,
                        molecule,
                        identifier,
                        this.$page.props.CM_API
                    )
            "
            class="cursor-pointer mt-1 text-sm text-gray-900 float-right"
        >
            <a style="box-shadow: none" class="hover:text-gray-600"
                >Download Molfile(2D)</a
            >
        </div>
    </div>
</template>
<script>
import OCL from "openchemlib/full";

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
        source: {
            type: String,
            default: "cpm",
        },
        CIP: {
            type: Boolean,
            default: false,
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
    methods: {
        getSVGString() {
            if (this.molecule) {
                const options = {
                    suppressChiralText: true,
                    autoCropMargin: true,
                };
                let mol = OCL.Molecule.fromSmiles(this.molecule);
                return mol.toSVG(
                    this.width,
                    this.height,
                    mol.getIDCode,
                    options
                );
            }
        },
    },
};
</script>
