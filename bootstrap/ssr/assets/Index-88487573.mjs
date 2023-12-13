import ApiTokenManager from "./ApiTokenManager-bb9cd9ae.mjs";
import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import { resolveComponent, mergeProps, withCtx, createVNode, useSSRContext } from "vue";
import { ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./ActionMessage-3207b224.mjs";
import "./ActionSection-c1b53bcc.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./Button-8d6976fd.mjs";
import "./ConfirmationModal-d0b47856.mjs";
import "./Modal-b2fb4cf2.mjs";
import "./DangerButton-9c23157c.mjs";
import "./DialogModal-f54eea4c.mjs";
import "./FormSection-d2d52d64.mjs";
import "./Input-f9a8cf77.mjs";
import "./Checkbox-ed79f759.mjs";
import "./InputError-6ae0b65c.mjs";
import "./Label-c5144ba4.mjs";
import "./SecondaryButton-3cee9e05.mjs";
import "./SectionBorder-7d1f222a.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
import "@heroicons/vue/24/solid";
import "@inertiajs/vue3";
import "@vueuse/core";
import "@heroicons/vue/24/outline";
import "@headlessui/vue";
import "./AnnouncementBanner-3ebe3621.mjs";
import "popper.js";
import "./FlashMessages-f0051c19.mjs";
import "@sipec/vue3-tags-input";
import "vue3-slider";
import "openchemlib/full.js";
import "./SelectRich-b58dcc3f.mjs";
import "./ToolTip-cf9882a3.mjs";
import "dropzone";
import "axios-retry";
import "@heroicons/vue/20/solid";
const _sfc_main = {
  components: {
    ApiTokenManager,
    AppLayout
  },
  props: ["tokens", "availablePermissions", "defaultPermissions"]
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_api_token_manager = resolveComponent("api-token-manager");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "API Tokens" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><h2 class="font-semibold text-xl text-gray-800 leading-tight"${_scopeId}> API Tokens </h2></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-12" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between py-6" }, [
                createVNode("h2", { class: "font-semibold text-xl text-gray-800 leading-tight" }, " API Tokens ")
              ])
            ])
          ])
        ];
      }
    }),
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div${_scopeId}><div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_api_token_manager, {
          tokens: $props.tokens,
          "available-permissions": $props.availablePermissions,
          "default-permissions": $props.defaultPermissions
        }, null, _parent2, _scopeId));
        _push2(`</div></div>`);
      } else {
        return [
          createVNode("div", null, [
            createVNode("div", { class: "max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" }, [
              createVNode(_component_api_token_manager, {
                tokens: $props.tokens,
                "available-permissions": $props.availablePermissions,
                "default-permissions": $props.defaultPermissions
              }, null, 8, ["tokens", "available-permissions", "default-permissions"])
            ])
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/API/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Index as default
};
