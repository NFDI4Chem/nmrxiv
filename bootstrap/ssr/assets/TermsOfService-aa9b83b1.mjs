import { Head } from "@inertiajs/vue3";
import { J as JetAuthenticationCardLogo } from "./AuthenticationCardLogo-26af12ea.mjs";
import { resolveComponent, useSSRContext } from "vue";
import { ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./ApplicationLogo-88e5413e.mjs";
const _sfc_main = {
  components: {
    Head,
    JetAuthenticationCardLogo
  },
  props: ["terms"]
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_jet_authentication_card_logo = resolveComponent("jet-authentication-card-logo");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_Head, { title: "Terms of Service" }, null, _parent));
  _push(`<div class="font-sans text-gray-900 antialiased"><div class="pt-4 bg-gray-100 index_beams"><div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0"><div>`);
  _push(ssrRenderComponent(_component_jet_authentication_card_logo, null, null, _parent));
  _push(`</div><div class="w-full sm:max-w-2xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose">${$props.terms}</div></div></div></div><!--]-->`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/TermsOfService.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const TermsOfService = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  TermsOfService as default
};
