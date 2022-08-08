import * as marked from "marked";
import { copyText } from "vue3-clipboard";

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
    },
};
