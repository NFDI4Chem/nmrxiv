const { warn } = require("vue");

module.exports = {
    env: {
        node: true,
        browser: true,
    },
    globals: {
        axios: "readonly",
        route: "readonly",
    },
    extends: ["eslint:recommended", "plugin:vue/vue3-recommended", "prettier"],
    rules: {
        // override/add rules settings here, such as:
        // 'vue/no-unused-vars': 'error'
        "vue/no-reserved-component-names": "warn",
        "vue/multi-word-component-names": "warn",
        "no-undef":"warn",
        "no-useless-escape":"off",
        "vue/no-mutating-props":"off",
    },
};
