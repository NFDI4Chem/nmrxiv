import ProjectLayout from "./Layout-3465446f.mjs";
import { Head } from "@inertiajs/vue3";
import { resolveComponent, withCtx, createVNode, toDisplayString, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderStyle } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./AppLayout-b93d1c11.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
import "@heroicons/vue/24/solid";
import "@vueuse/core";
import "@heroicons/vue/24/outline";
import "@headlessui/vue";
import "./AnnouncementBanner-3ebe3621.mjs";
import "popper.js";
import "./FlashMessages-f0051c19.mjs";
import "./DialogModal-f54eea4c.mjs";
import "./Modal-b2fb4cf2.mjs";
import "./SecondaryButton-3cee9e05.mjs";
import "./Button-8d6976fd.mjs";
import "@sipec/vue3-tags-input";
import "vue3-slider";
import "openchemlib/full.js";
import "./SelectRich-b58dcc3f.mjs";
import "./ToolTip-cf9882a3.mjs";
import "./InputError-6ae0b65c.mjs";
import "dropzone";
import "axios-retry";
import "./ConfirmationModal-d0b47856.mjs";
import "./DangerButton-9c23157c.mjs";
import "@heroicons/vue/20/solid";
import "./DOIBadge-164e4f67.mjs";
const _sfc_main = {
  components: {
    ProjectLayout,
    Head
  },
  props: ["project", "tab"],
  data() {
    return {};
  },
  computed: {},
  mounted() {
  },
  methods: {}
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_project_layout = resolveComponent("project-layout");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_Head, {
    title: "License - " + $props.project.data.name
  }, null, _parent));
  _push(ssrRenderComponent(_component_project_layout, {
    project: $props.project,
    "selected-tab": $props.tab
  }, {
    "project-content": withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"${_scopeId}><h3 class="text-xl font-extrabold text-blue-gray-900"${_scopeId}>${ssrInterpolate($props.project.data.license.title)}</h3><div class="mt-3 space-y-8 divide-y divide-y-blue-gray-200"${_scopeId}><div class="grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6"${_scopeId}><div class="sm:col-span-6"${_scopeId}><p style="${ssrRenderStyle({ "max-width": "100ch !important" })}" class="prose mt-1 text-sm text-blue-gray-500"${_scopeId}>${$props.project.data.license.body}</p></div></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" }, [
            createVNode("h3", { class: "text-xl font-extrabold text-blue-gray-900" }, toDisplayString($props.project.data.license.title), 1),
            createVNode("div", { class: "mt-3 space-y-8 divide-y divide-y-blue-gray-200" }, [
              createVNode("div", { class: "grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6" }, [
                createVNode("div", { class: "sm:col-span-6" }, [
                  createVNode("p", {
                    style: { "max-width": "100ch !important" },
                    class: "prose mt-1 text-sm text-blue-gray-500",
                    innerHTML: $props.project.data.license.body
                  }, null, 8, ["innerHTML"])
                ])
              ])
            ])
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`<!--]-->`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Project/License.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const License = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  License as default
};
