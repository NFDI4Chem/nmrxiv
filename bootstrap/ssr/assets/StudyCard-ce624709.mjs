import { LockClosedIcon, LockOpenIcon, EnvelopeIcon, PencilIcon, StarIcon } from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/vue3";
import { D as Depictor2D } from "./Depictor2D-b60253ae.mjs";
import { resolveComponent, mergeProps, withCtx, createVNode, createTextVNode, toDisplayString, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  components: {
    LockClosedIcon,
    LockOpenIcon,
    EnvelopeIcon,
    PencilIcon,
    StarIcon,
    Link,
    Depictor2D
  },
  props: ["study"],
  setup() {
  },
  data() {
    return {
      selectedPreviewIndex: 0
    };
  },
  methods: {}
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Depictor2D = resolveComponent("Depictor2D");
  const _component_LockClosedIcon = resolveComponent("LockClosedIcon");
  const _component_Link = resolveComponent("Link");
  if ($props.study) {
    _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col border rounded-lg shadow-lg transition ease-in-out delay-150 duration-300 overflow-hidden" }, _attrs))}><div class="relative overflow-hidden bg-white-200"><div class="pt-2 px-2"><ul role="list"><li class="col-span-1 divide-y divide-gray-200 cursor-pointer"><div class="bg-white rounded-t-md flex justify-center items-center">`);
    if ($props.study.molecules[0] && $props.study.molecules[0].canonical_smiles) {
      _push(`<span>`);
      _push(ssrRenderComponent(_component_Depictor2D, {
        class: "py-2",
        molecule: $props.study.molecules[0].canonical_smiles,
        "show-download": false
      }, null, _parent));
      _push(`</span>`);
    } else {
      _push(`<span><div class="h-64"><div class="absolute inset-0 flex items-center justify-center"><h2 class="text-gray-200 font-bold"><i>-- molecule(s) information missing --</i></h2></div></div></span>`);
    }
    _push(`</div></li></ul><div class="bg-white"></div></div></div><div class="flex-1 border-t bg-white p-3 flex flex-col justify-between"><div>`);
    if ($props.study.identifier) {
      _push(`<small class="text-gray-500 float-left">#${ssrInterpolate($props.study.identifier)}</small>`);
    } else {
      _push(`<!---->`);
    }
    _push(`<div class="float-right">`);
    if ($props.study.is_public) {
      _push(`<div class="flex items-center mt-1"><svg class="h-3 w-3 mr-1 text-green-400 inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="512" height="512"><g id="globe"><path d="M53.85,47.85A27,27,0,0,1,24,57.8V56l3-3V49l4-4V42l4,4h5l2-2h8Z"></path><path d="M42,20.59v2.56L38.07,27H31l-5.36,5.26L31,37.51v5.06L27.44,39H22.86L16,32.11V24.2L11.8,20h-4A27,27,0,0,1,32,5a26.55,26.55,0,0,1,7.06.94L36,9H30v4l4,4h4.33Z"></path><path d="M32,60A28,28,0,1,1,60,32,28,28,0,0,1,32,60ZM32,6A26,26,0,1,0,58,32,26,26,0,0,0,32,6Z"></path></g></svg><p class="text-xs text-gray-600">Public</p></div>`);
    } else {
      _push(`<div class="flex items-center mt-1">`);
      _push(ssrRenderComponent(_component_LockClosedIcon, { class: "w-3 h-3 mr-1 text-teal-600" }, null, _parent));
      _push(`<p class="text-xs text-gray-600">Private</p></div>`);
    }
    _push(`</div></div>`);
    _push(ssrRenderComponent(_component_Link, {
      href: _ctx.route("dashboard.studies", [$props.study.id])
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`<div class="flex items-center font-bold truncate text-lg text-gray-600"${_scopeId}><span class=""${_scopeId}></span> ${ssrInterpolate($props.study.name)}</div><div class="pt-1"${_scopeId}><div class="text-xs text-gray-600"${_scopeId}><span class="text-gray-400"${_scopeId}>Last updated on</span> ${ssrInterpolate(_ctx.formatDate($props.study.updated_at))}</div></div>`);
        } else {
          return [
            createVNode("div", { class: "flex items-center font-bold truncate text-lg text-gray-600" }, [
              createVNode("span", { class: "" }),
              createTextVNode(" " + toDisplayString($props.study.name), 1)
            ]),
            createVNode("div", { class: "pt-1" }, [
              createVNode("div", { class: "text-xs text-gray-600" }, [
                createVNode("span", { class: "text-gray-400" }, "Last updated on"),
                createTextVNode(" " + toDisplayString(_ctx.formatDate($props.study.updated_at)), 1)
              ])
            ])
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</div></div>`);
  } else {
    _push(`<!---->`);
  }
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/StudyCard.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const StudyCard = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  StudyCard as S
};
