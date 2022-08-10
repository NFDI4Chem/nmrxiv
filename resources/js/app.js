import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/inertia-vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { InertiaProgress } from "@inertiajs/progress";
import helpers from "./Mixins/Global.js";
import Children from "@/Shared/Children.vue";
import InstantSearch from "vue-instantsearch/vue3/es";
import Vue3Tour from "vue3-tour";
import mitt from "mitt";

import VueSimpleContextMenu from "vue-simple-context-menu";
import "vue-simple-context-menu/dist/vue-simple-context-menu.css";

const appName =
    window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";
const emitter = mitt();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, app, props, plugin }) {
        const application = createApp({ render: () => h(app, props) })
            .use(plugin)
            .component("Children", Children)
            .component("vue-simple-context-menu", VueSimpleContextMenu)
            .mixin({ methods: { route } })
            .mixin(helpers)
            .use(InstantSearch)
            .use(Vue3Tour);
        application.config.globalProperties.emitter = emitter;
        application.mount(el);
        return application;
    },
});

InertiaProgress.init({ color: "#4B5563" });
