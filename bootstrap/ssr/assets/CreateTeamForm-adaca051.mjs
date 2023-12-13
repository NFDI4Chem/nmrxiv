import { J as JetButton } from "./Button-8d6976fd.mjs";
import { J as JetFormSection } from "./FormSection-d2d52d64.mjs";
import { J as JetInput } from "./Input-f9a8cf77.mjs";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { J as JetLabel } from "./Label-c5144ba4.mjs";
import { resolveComponent, mergeProps, withCtx, createTextVNode, createVNode, toDisplayString, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderAttr, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./SectionTitle-70590d9c.mjs";
const _sfc_main = {
  components: {
    JetButton,
    JetFormSection,
    JetInput,
    JetInputError,
    JetLabel
  },
  data() {
    return {
      form: this.$inertia.form({
        name: ""
      })
    };
  },
  methods: {
    createTeam() {
      this.form.post(route("teams.store"), {
        errorBag: "createTeam",
        preserveScroll: true
      });
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_form_section = resolveComponent("jet-form-section");
  const _component_jet_label = resolveComponent("jet-label");
  const _component_jet_input = resolveComponent("jet-input");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_jet_button = resolveComponent("jet-button");
  _push(ssrRenderComponent(_component_jet_form_section, mergeProps({ onSubmitted: $options.createTeam }, _attrs), {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Team Details `);
      } else {
        return [
          createTextVNode(" Team Details ")
        ];
      }
    }),
    description: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Create a new team to collaborate with others on projects. `);
      } else {
        return [
          createTextVNode(" Create a new team to collaborate with others on projects. ")
        ];
      }
    }),
    form: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="col-span-6"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, { value: "Team Owner" }, null, _parent2, _scopeId));
        _push2(`<div class="flex items-center mt-2"${_scopeId}><img class="w-12 h-12 rounded-full object-cover"${ssrRenderAttr("src", _ctx.$page.props.auth.user.profile_photo_url)}${ssrRenderAttr("alt", _ctx.$page.props.auth.user.name)}${_scopeId}><div class="ml-4 leading-tight"${_scopeId}><div${_scopeId}>${ssrInterpolate(_ctx.$page.props.auth.user.first_name)} ${ssrInterpolate(_ctx.$page.props.auth.user.last_name)}</div><div class="text-gray-700 text-sm"${_scopeId}>${ssrInterpolate(_ctx.$page.props.auth.user.email)}</div></div></div></div><div class="col-span-6 sm:col-span-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, {
          for: "name",
          value: "Team Name"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input, {
          id: "name",
          modelValue: $data.form.name,
          "onUpdate:modelValue": ($event) => $data.form.name = $event,
          type: "text",
          class: "mt-1 block w-full",
          autofocus: ""
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.form.errors.name,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "col-span-6" }, [
            createVNode(_component_jet_label, { value: "Team Owner" }),
            createVNode("div", { class: "flex items-center mt-2" }, [
              createVNode("img", {
                class: "w-12 h-12 rounded-full object-cover",
                src: _ctx.$page.props.auth.user.profile_photo_url,
                alt: _ctx.$page.props.auth.user.name
              }, null, 8, ["src", "alt"]),
              createVNode("div", { class: "ml-4 leading-tight" }, [
                createVNode("div", null, toDisplayString(_ctx.$page.props.auth.user.first_name) + " " + toDisplayString(_ctx.$page.props.auth.user.last_name), 1),
                createVNode("div", { class: "text-gray-700 text-sm" }, toDisplayString(_ctx.$page.props.auth.user.email), 1)
              ])
            ])
          ]),
          createVNode("div", { class: "col-span-6 sm:col-span-4" }, [
            createVNode(_component_jet_label, {
              for: "name",
              value: "Team Name"
            }),
            createVNode(_component_jet_input, {
              id: "name",
              modelValue: $data.form.name,
              "onUpdate:modelValue": ($event) => $data.form.name = $event,
              type: "text",
              class: "mt-1 block w-full",
              autofocus: ""
            }, null, 8, ["modelValue", "onUpdate:modelValue"]),
            createVNode(_component_jet_input_error, {
              message: $data.form.errors.name,
              class: "mt-2"
            }, null, 8, ["message"])
          ])
        ];
      }
    }),
    actions: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_button, {
          class: { "opacity-25": $data.form.processing },
          disabled: $data.form.processing
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Create `);
            } else {
              return [
                createTextVNode(" Create ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_jet_button, {
            class: { "opacity-25": $data.form.processing },
            disabled: $data.form.processing
          }, {
            default: withCtx(() => [
              createTextVNode(" Create ")
            ]),
            _: 1
          }, 8, ["class", "disabled"])
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Teams/Partials/CreateTeamForm.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const CreateTeamForm = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  CreateTeamForm as default
};
