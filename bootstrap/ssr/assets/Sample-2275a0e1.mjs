import { S as SpectraViewer } from "./SpectraViewer-3065e728.mjs";
import { D as Depictor2D } from "./Depictor2D-b60253ae.mjs";
import { D as DOIBadge } from "./DOIBadge-164e4f67.mjs";
import { resolveComponent, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "openchemlib/full.js";
const _sfc_main = {
  components: {
    SpectraViewer,
    Depictor2D,
    DOIBadge
  },
  props: ["study"],
  data() {
    return {};
  },
  computed: {},
  mounted() {
  },
  methods: {}
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_SpectraViewer = resolveComponent("SpectraViewer");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  _push(ssrRenderComponent(_component_SpectraViewer, {
    ref: "spectraViewerREF",
    study: $props.study.data
  }, null, _parent));
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Embed/Sample.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Sample = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Sample as default
};
