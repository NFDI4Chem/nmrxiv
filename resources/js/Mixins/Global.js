import * as marked from "marked";
import { copyText } from "vue3-clipboard";
import pluralize from "pluralize";
import OCL from "openchemlib/full";

export default {
    methods: {
        tagQuery(query) {
            if (query) {
                if (this.isInchi(query)) {
                    return "InChi";
                } else if (this.isInChiKey(query)) {
                    return "InChiKey";
                } else {
                    try {
                        let molecule = OCL.Molecule.fromSmiles(query);
                    } catch (err) {
                        return "text";
                    }
                    return "smiles";
                }
            }
        },
        removeDuplicates(a) {
            return a.filter(function (value, index) {
                return a.indexOf(value) == index;
            });
        },
        isInchi(query) {
            return (
                !!query
                    .trim()
                    .match(
                        /^((InChI=)?[^J][0-9BCOHNSOPrIFla+\-\(\)\\\/,pqbtmsih]{6,})$/gi
                    ) && query.startsWith("InChI=")
            );
        },
        isInChiKey(query) {
            return (
                (25 === query.length &&
                    "-" === query[14] &&
                    !!query.match(/^([0-9A-Z\-]+)$/)) ||
                (27 === query.length &&
                    "-" === query[14] &&
                    "-" === query[25] &&
                    !!query.match(/^([0-9A-Z\-]+)$/))
            );
        },
        hasAnyRole: function (roles) {
            return this.checkIfValueExists(roles, "roles");
        },
        hasAnyPermission: function (permissions) {
            return this.checkIfValueExists(permissions, "permissions");
        },
        checkIfValueExists(queryArray, type) {
            if (this.$page.props.user && this.$page.props.user[type]) {
                let allValues = Array.from(this.$page.props.user[type]);
                return queryArray.some((r) => allValues.indexOf(r) >= 0);
            }
        },
        formatDate(timestamp) {
            const date = new Date(timestamp);
            return new Intl.DateTimeFormat("default", {
                dateStyle: "long",
            }).format(date);
        },
        formatDateTime(timestamp) {
            const date = new Date(timestamp);
            return new Intl.DateTimeFormat("en", {
                dateStyle: "full",
                timeStyle: "short",
            }).format(date);
        },
        md(data) {
            return data ? marked.parse(data) : "";
        },
        getHash(input) {
            var hash = 0,
                len = input.length;
            for (var i = 0; i < len; i++) {
                hash = (hash << 5) - hash + input.charCodeAt(i);
                hash |= 0; // to 32bit integer
            }
            return hash;
        },
        findLocalItems(query) {
            var i,
                results = [];
            if (typeof window !== "undefined") {
                for (i in localStorage) {
                    if (localStorage.hasOwnProperty(i)) {
                        if (
                            i.match(query) ||
                            (!query && typeof i === "string")
                        ) {
                            let value = JSON.parse(localStorage.getItem(i));
                            if (value) {
                                results.push({ key: i, val: value });
                            }
                        }
                    }
                }
            }
            return results;
        },
        slugify(str) {
            str = str.replace(/^\s+|\s+$/g, "");
            str = str.toLowerCase();
            var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
            var to = "aaaaeeeeiiiioooouuuunc------";
            for (var i = 0, l = from.length; i < l; i++) {
                str = str.replace(
                    new RegExp(from.charAt(i), "g"),
                    to.charAt(i)
                );
            }
            str = str
                .replace(/[^a-z0-9 -]/g, "")
                .replace(/\s+/g, "-")
                .replace(/-+/g, "-");

            return str;
        },
        copyToClipboard(text) {
            copyText(text, undefined, (error) => {
                if (error) {
                    console.log(error);
                } else {
                    console.log("copied");
                    // console.log(event)
                }
            });
        },
        bytesToSize(bytes) {
            var sizes = ["Bytes", "KB", "MB", "GB", "TB"];
            if (bytes == 0) return "0 Byte";
            var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
            return Math.round(bytes / Math.pow(1024, i), 2) + " " + sizes[i];
        },
        isString(val) {
            return typeof val === "string" || val instanceof String;
        },
        parseJSON(val) {
            if (!val) {
                return null;
            }
            if (this.isString(val)) {
                return JSON.parse(val);
            }
            return val;
        },
        downloadMolFile2D(e, smiles, identifier, CM_API) {
            axios
                .get(
                    CM_API +
                        "convert/mol2D?smiles=" +
                        encodeURIComponent(smiles) +
                        "&generator=rdkit"
                )
                .then((response) => {
                    this.saveTextAsFile(
                        response.data,
                        "Molfile_V2_" + identifier + ".mol",
                        "chemical/x-mdl-molfile"
                    );
                })
                .catch((error) => {
                    console.error("Error downloading the file:", error);
                });
        },
        downloadMolFile3D(e, smiles, identifier, CM_API) {
            axios
                .get(
                    CM_API +
                        "convert/mol3D?smiles=" +
                        encodeURIComponent(smiles) +
                        "&generator=rdkit"
                )
                .then((response) => {
                    this.saveTextAsFile(
                        response.data,
                        "Molfile_V3_" + identifier + ".mol",
                        "chemical/x-mdl-molfile"
                    );
                })
                .catch((error) => {
                    console.error("Error downloading the file:", error);
                });
        },
        getMolfileStringBySmiles(smiles) {
            try {
                const npMolecule = OCL.Molecule.fromSmiles(smiles);
                return npMolecule.toMolfileV3();
            } catch (e) {
                console.log(e.name + " in OpenChemLib: " + e.message);
            }
        },
        saveTextAsFile(textToWrite, fileNameToSaveAs, fileType) {
            let textFileAsBlob = new Blob([textToWrite], { type: fileType });
            let downloadLink = document.createElement("a");
            downloadLink.download = fileNameToSaveAs;
            downloadLink.innerHTML = "Download File";

            if (window.webkitURL != null) {
                downloadLink.href =
                    window.webkitURL.createObjectURL(textFileAsBlob);
            } else {
                downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
                downloadLink.style.display = "none";
                document.body.appendChild(downloadLink);
            }

            downloadLink.click();
        },
        /*Extract Doi from URL*/
        extractDoi(query) {
            if (query.indexOf("http") > -1) {
                var url = new URL(query);
                query = url.pathname.replace("/", "");
            }
            return query.trim();
        },
        fixedDecimelPoint(input, decimelPoint) {
            return Number.parseFloat(input).toFixed(decimelPoint);
        },
        hasAnyRole: function (roles) {
            return this.checkIfValueExists(roles, "roles");
        },
        hasAnyPermission: function (permissions) {
            return this.checkIfValueExists(permissions, "permissions");
        },
        checkIfValueExists(queryArray, type) {
            if (this.$page.props.user && this.$page.props.user[type]) {
                let allValues = Array.from(this.$page.props.user[type]);
                return queryArray.some((r) => allValues.indexOf(r) >= 0);
            }
        },
        formatDate(timestamp) {
            const date = new Date(timestamp);
            return new Intl.DateTimeFormat("default", {
                dateStyle: "long",
            }).format(date);
        },
        formatDateTime(timestamp) {
            const date = new Date(timestamp);
            return new Intl.DateTimeFormat("en", {
                dateStyle: "full",
                timeStyle: "short",
            }).format(date);
        },
        md(data) {
            return data ? marked.parse(data) : "";
        },
        getHash(input) {
            var hash = 0,
                len = input.length;
            for (var i = 0; i < len; i++) {
                hash = (hash << 5) - hash + input.charCodeAt(i);
                hash |= 0; // to 32bit integer
            }
            return hash;
        },
        slugify(str) {
            str = str.replace(/^\s+|\s+$/g, "");
            str = str.toLowerCase();
            var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
            var to = "aaaaeeeeiiiioooouuuunc------";
            for (var i = 0, l = from.length; i < l; i++) {
                str = str.replace(
                    new RegExp(from.charAt(i), "g"),
                    to.charAt(i)
                );
            }
            str = str
                .replace(/[^a-z0-9 -]/g, "")
                .replace(/\s+/g, "-")
                .replace(/-+/g, "-");

            return str;
        },
        copyToClipboard(text, element) {
            if (typeof element == "string") {
                document.getElementById(element).select();
            } else {
                element.select();
            }
            copyText(text, undefined, (error) => {
                if (error) {
                    console.log(error);
                }
            });
        },
        bytesToSize(bytes) {
            var sizes = ["Bytes", "KB", "MB", "GB", "TB"];
            if (bytes == 0) return "0 Byte";
            var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
            return Math.round(bytes / Math.pow(1024, i), 2) + " " + sizes[i];
        },
        isString(val) {
            return typeof val === "string" || val instanceof String;
        },
        parseJSON(val) {
            if (!val) {
                return null;
            }
            if (this.isString(val)) {
                return JSON.parse(val);
            }
            return val;
        },
        isValidEmail(email) {
            var re =
                /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        },
        isEmpty(obj) {
            return Object.keys(obj).length === 0;
        },
        pluralize(value, number) {
            return pluralize(value, number);
        },

        /*Extract Doi from URL*/
        extractDoi(query) {
            if (query.indexOf("http") > -1) {
                var url = new URL(query);
                query = url.pathname.replace("/", "");
            }
            return query.trim();
        },
    },

    computed: {
        editable() {
            if (this.role) {
                if (
                    this.role == "creator" ||
                    this.role == "owner" ||
                    this.role == "collaborator"
                ) {
                    return true;
                }
            }
            if (this.teamRole && this.teamRole.key) {
                if (
                    this.teamRole.key == "creator" ||
                    this.teamRole.key == "owner" ||
                    this.teamRole.key == "collaborator"
                ) {
                    return true;
                }
            } else {
                return false;
            }
        },
        editableTeamRole() {
            if (this.teamRole && this.teamRole.name) {
                if (
                    this.teamRole.key == "owner" ||
                    this.teamRole.key == "collaborator"
                ) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        },
    },
};
