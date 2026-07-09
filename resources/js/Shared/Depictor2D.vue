<template>
    <div class="flex h-full w-full min-h-0 flex-col">
        <div class="flex min-h-0 flex-1 items-center justify-center">
            <template v-if="source === 'ocl'">
                <!-- OCL SVG is generated locally, not user HTML — do not run through sanitizeHtml (it strips <svg>). -->
                <div
                    v-if="depictionSvg"
                    class="flex max-h-full max-w-full items-center justify-center [&_svg]:max-h-full [&_svg]:max-w-full"
                    v-html="depictionSvg"
                ></div>
                <p
                    v-else-if="molecule?.trim()"
                    class="px-2 text-center text-xs text-gray-500"
                >
                    Could not render structure preview.
                </p>
            </template>
            <img
                v-else
                class="max-h-full max-w-full object-contain"
                :src="depictionImageUrl"
                :width="width"
                :height="height"
                alt=""
                @load="onDepictionLoaded"
                @error="onDepictionLoaded"
            />
        </div>
        <div
            v-if="showDownload"
            class="mt-1 shrink-0 cursor-pointer text-right text-sm text-gray-900"
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
import OCL from "openchemlib";

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

    emits: ["loading"],
    data() {
        return {
            results: [],
        };
    },
    computed: {
        depictionSvg() {
            if (this.source !== "ocl" || !this.molecule?.trim()) {
                return null;
            }

            try {
                const options = {
                    suppressChiralText: true,
                    autoCropMargin: true,
                };
                const mol = OCL.Molecule.fromSmiles(this.molecule.trim());
                const width = Math.max(120, this.width || 120);
                const height = Math.max(120, this.height || 120);

                return mol.toSVG(width, height, mol.getIDCode, options);
            } catch {
                return null;
            }
        },
        encodedSmiles() {
            return encodeURIComponent(this.molecule);
        },
        depictionImageUrl() {
            return (
                this.$page.props.CM_API +
                "depict/2D?smiles=" +
                this.encodedSmiles +
                "&height=" +
                this.height +
                "&width=" +
                this.width +
                "&CIP=" +
                this.CIP +
                "&toolkit=cdk"
            );
        },
    },
    watch: {
        molecule() {
            this.notifyDepictionLoading();
        },
        depictionSvg() {
            if (this.source === "ocl") {
                this.$emit("loading", false);
            }
        },
        depictionImageUrl() {
            if (this.source !== "ocl") {
                this.notifyDepictionLoading();
            }
        },
    },
    mounted() {
        this.notifyDepictionLoading();
        this.$nextTick(() => {
            const image = this.$el.querySelector("img");

            if (image?.complete) {
                this.onDepictionLoaded();
            }
        });
    },
    methods: {
        notifyDepictionLoading() {
            if (!this.molecule) {
                this.$emit("loading", false);

                return;
            }

            if (this.source === "ocl") {
                this.$emit("loading", false);

                return;
            }

            this.$emit("loading", true);
        },
        onDepictionLoaded() {
            this.$emit("loading", false);
        },
    },
};
</script>
