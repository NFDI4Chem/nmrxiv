import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";

import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import helpers from "./Mixins/Global.js";
import Children from "@/Shared/Children.vue";
import InstantSearch from "vue-instantsearch/vue3/es";
import Vue3Tour from "vue3-tour";
import mitt from "mitt";

const appName =
    window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";
const emitter = mitt();

createInertiaApp({
    progress: {
        color: "#4B5563",
    },
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const application = createApp({ render: () => h(App, props) })
            .use(plugin)
            .component("Children", Children)
            .mixin({ methods: { route } })
            .mixin(helpers)
            .use(InstantSearch)
            .use(Vue3Tour);
        application.config.globalProperties.emitter = emitter;
        application.mount(el);
        return application;
    },
});
