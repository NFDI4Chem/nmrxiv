import StudyContent from "./Content-5913e7b1.mjs";
import { resolveComponent, withCtx, createVNode, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./Layout-114f561c.mjs";
import "./AppLayout-b93d1c11.mjs";
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
import "./Details-f3d36d90.mjs";
import "./ActionMessage-3207b224.mjs";
import "./Activity-90a526a4.mjs";
import "vue3-colorpicker";
/* empty css                 */import "./AccessDialogue-1fdd1bde.mjs";
import "./ActionSection-c1b53bcc.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./Input-f9a8cf77.mjs";
import "./Label-c5144ba4.mjs";
import "./SectionBorder-7d1f222a.mjs";
import "./Citation-80069afd.mjs";
import "axios";
const _sfc_main = {
  components: {
    StudyContent
  },
  props: ["study", "project"],
  setup() {
  },
  mounted() {
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_study_content = resolveComponent("study-content");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  _push(ssrRenderComponent(_component_study_content, {
    project: $props.project,
    study: $props.study,
    current: "Protocols"
  }, {
    "study-section": withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="divide-y divide-gray-200 sm:col-span-9"${_scopeId}><div class="py-6 px-4 sm:p-6 lg:pb-8"${_scopeId}><div${_scopeId}><h2 class="text-lg leading-6 font-medium text-gray-900"${_scopeId}> Protocols </h2></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "divide-y divide-gray-200 sm:col-span-9" }, [
            createVNode("div", { class: "py-6 px-4 sm:p-6 lg:pb-8" }, [
              createVNode("div", null, [
                createVNode("h2", { class: "text-lg leading-6 font-medium text-gray-900" }, " Protocols ")
              ])
            ])
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Study/Protocols.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Protocols = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Protocols as default
};
