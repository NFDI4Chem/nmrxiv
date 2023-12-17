import { createInertiaApp } from "@inertiajs/vue3";
import createServer from "@inertiajs/vue3/server";
import { renderToString } from "@vue/server-renderer";
import { createSSRApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/vue.m";
import { Ziggy } from "./ziggy.js";
import helpers from "./Mixins/Global.js";

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        resolve: (name) => {
            const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
            return pages[`./Pages/${name}.vue`];
        },
        setup({ App, props, plugin }) {
            return createSSRApp({
                render: () => h(App, props),
            })
                .use(plugin)
                .use(ZiggyVue, Ziggy)
                .mixin(helpers);
        },
    })
);
