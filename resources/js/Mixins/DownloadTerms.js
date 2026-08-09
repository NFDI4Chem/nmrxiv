/**
 * Options API helpers for gating downloads behind DownloadTermsModal.
 */
import { trackingIdentifier } from "@/Utils/trackingIdentifier.js";

export default {
    data() {
        return {
            showDownloadTerms: false,
            pendingDownloadUrl: null,
            pendingDownloadIdentifier: null,
        };
    },

    methods: {
        requestDownload(url, identifier = null, event = null) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            if (!url || url === "#") {
                return;
            }

            this.pendingDownloadUrl = url;
            this.pendingDownloadIdentifier = identifier;
            this.showDownloadTerms = true;
        },

        closeDownloadTerms() {
            this.showDownloadTerms = false;
            this.pendingDownloadUrl = null;
            this.pendingDownloadIdentifier = null;
        },

        trackingIdentifier,
    },
};
