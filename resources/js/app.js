require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import helpers from "./Mixins/Global.js";
import Children from "@/Shared/Children.vue";
import InstantSearch from 'vue-instantsearch/vue3/es';
import Vue3Tour from 'vue3-tour';
import mitt from 'mitt';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
const emitter = mitt();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .component('Children', Children)
            .mixin({ methods: { route } })
            .mixin(helpers)
            .use(InstantSearch)
            .use(Vue3Tour)
            .provide('emitter', emitter)
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
