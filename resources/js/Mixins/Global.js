import * as marked from "marked";
import { copyText } from "vue3-clipboard";
import pluralize from "pluralize";

export default {
    methods: {
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
            for (i in localStorage) {
                if (localStorage.hasOwnProperty(i)) {
                    if (i.match(query) || (!query && typeof i === "string")) {
                        let value = JSON.parse(localStorage.getItem(i));
                        if (value) {
                            results.push({ key: i, val: value });
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
        copyToClipboard(text, id) {
            document.getElementById(id).select();
            copyText(text, undefined, (error, event) => {
                if (error) {
                    console.log(error);
                } else {
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
