import OCL from "openchemlib/full.js";
import { mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderStyle } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  components: {},
  props: {
    molecule: String,
    width: {
      type: Number,
      default: 300
    },
    height: {
      type: Number,
      default: 300
    },
    source: {
      type: String,
      default: "cpm"
    },
    CIP: {
      type: Boolean,
      default: false
    },
    showDownload: {
      type: Boolean,
      default: false
    },
    identifier: String
  },
  data() {
    return {
      results: []
    };
  },
  computed: {
    encodedSmiles() {
      return encodeURIComponent(this.molecule);
    }
  },
  mounted() {
  },
  methods: {
    getSVGString() {
      if (this.molecule) {
        const options = {
          suppressChiralText: true,
          autoCropMargin: true
        };
        let mol = OCL.Molecule.fromSmiles(this.molecule);
        return mol.toSVG(
          this.width,
          this.height,
          mol.getIDCode,
          options
        );
      }
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "justify-center" }, _attrs))}>`);
  if ($props.source == "ocl") {
    _push(`<div class="flex justify-center align-middle pt-5">${$options.getSVGString($props.molecule)}</div>`);
  } else {
    _push(`<div class="flex"><img class="mx-auto w-100"${ssrRenderAttr(
      "src",
      _ctx.$page.props.CM_API + "depict/2D?smiles=" + $options.encodedSmiles + "&height=" + $props.height + "&width=" + $props.width + "&CIP=" + $props.CIP + "&toolkit=rdkit"
    )} alt=""></div>`);
  }
  if ($props.showDownload) {
    _push(`<div class="cursor-pointer mt-1 text-sm text-gray-900 float-right"><a style="${ssrRenderStyle({ "box-shadow": "none" })}" class="hover:text-gray-600">Download Molfile(2D)</a></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Depictor2D.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Depictor2D = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Depictor2D as D
};
