<template>
    <div class="justify-center">
        <div
            v-if="source == 'ocl'"
            class="flex justify-center align-middle pt-5"
            v-html="getSVGString(molecule)"
        ></div>
        <div v-else class="flex">
            <img
                class="mx-auto w-100"
                :src="
                    $page.props.CM_API +
                    'depict/2D?smiles=' +
                    encodedSmiles +
                    '&height=' +
                    height +
                    '&width=' +
                    width +
                    '&CIP=' +
                    CIP +
                    '&toolkit=cdk'
                "
                alt=""
            />
        </div>
        <div
            v-if="showDownload"
            class="cursor-pointer mt-1 text-sm text-gray-900 float-right"
            @click="
                (e) =>
                    downloadMolFile2D(
                        e,
                        molecule,
                        identifier,
                        $page.props.CM_API
                    )
            "
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
