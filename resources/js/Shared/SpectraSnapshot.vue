<template>
    <div class="p-6">
        <iframe
            name="snapshotNMRiumIframe"
            frameborder="0"
            allowfullscreen
            class="rounded-md border"
            style="width: 100%; height: 75vh; max-height: 600px"
            :src="nmriumURL"
            @load="loadSpectra()"
        ></iframe>
    </div>
</template>

<script>
export default {
    components: {},
    props: {
        id: Number,
    },
    emits: ["loading"],
    setup() {},
    data() {
        return {
            spectraError: null,
        };
    },
    computed: {
        url() {
            return String(this.$page.props.url)
                ? String(this.$page.props.url)
                : "https://nmrxiv.org";
        },
        nmriumURL() {
            return this.$page.props.nmriumURL
                ? String(this.$page.props.nmriumURL + "&id=" + Math.random())
                : "https://nmriumdev.nmrxiv.org?defaultEmptyMessage=''&workspace=embedded&id=" +
                      Math.random();
        },
    },
    mounted() {
        this.registerEvents();
    },
    methods: {
        registerEvents() {
            const saveNMRiumUpdates = (e) => {
                const { data, type } = e.data;
                if (type == "nmr-wrapper:action-response") {
                    if (
                        e.origin != "https://nmriumdev.nmrxiv.org" &&
                        e.origin != "https://nmrium.nmrxiv.org"
                    ) {
                        return;
                    }
                    let actionType = data.type;
                    if (actionType == "exportSpectraViewerAsBlob") {
                        this.saveDSPreview(data.data);
                    }
                }
                if (data && data.source == "data") {
                    if (
                        e.origin != "https://nmriumdev.nmrxiv.org" &&
                        e.origin != "https://nmrium.nmrxiv.org"
                    ) {
                        return;
                    }
                    if (type == "nmr-wrapper:error") {
                        this.spectraError = e.data;
                        this.updateLoadingStatus(false);
                        return;
                    }
                    let state = data.state;
                    this.version = state.version;
                    let actionType = state.data.actionType;

                    if (type == "nmr-wrapper:data-change") {
                        if (state.data.spectra.length > 0) {
                            console.log(actionType);
                            if (actionType == "INITIATE") {
                                this.exportPreview();
                                return;
                            }
                        }
                    }
                }
            };
            if (!this.$props.eventRegistered) {
                window.addEventListener("message", saveNMRiumUpdates);
                this.$props.eventRegistered = true;
            } else {
                window.removeEventListener("message", saveNMRiumUpdates);
                this.$props.eventRegistered = false;
            }
        },
        exportPreview() {
            setTimeout(() => {
                // console.log(this.id)
                const iframe = window.frames.snapshotNMRiumIframe;
                if (iframe) {
                    let data = {
                        type: "exportSpectraViewerAsBlob",
                    };
                    iframe.postMessage(
                        {
                            type: `nmr-wrapper:action-request`,
                            data,
                        },
                        "*"
                    );
                }
            }, 1000);
        },
        loadSpectra() {
            const iframe = window.frames.snapshotNMRiumIframe;
            this.spectraError = null;
            if (iframe) {
                axios
                    .get("/dashboard/datasets/" + this.id + "/nmriumInfo")
                    .then((response) => {
                        let nmrium_info = response.data;
                        if (nmrium_info) {
                            let data = {
                                data: nmrium_info,
                                type: "nmrium",
                            };
                            iframe.postMessage(
                                { type: `nmr-wrapper:load`, data },
                                "*"
                            );
                        } else {
                            this.loadFromURLs(null);
                        }
                    });
            }
        },
        saveDSPreview(data) {
            if (this.id) {
                const reader = new FileReader();
                reader.addEventListener("loadend", () => {
                    let svg = reader.result;
                    axios
                        .post("/dashboard/datasets/" + this.id + "/preview", {
                            img: svg,
                        })
                        .then((response) => {
                            this.updateLoadingStatus(false, "dataset loaded");
                        });
                });
                reader.readAsText(data.blob);
            }
        },
        updateLoadingStatus(status, message) {
            this.$emit("loading", {
                status: status,
                message: message,
            });
        },
    },
};
</script>
