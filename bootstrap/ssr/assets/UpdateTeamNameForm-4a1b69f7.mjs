import { J as JetActionMessage } from "./ActionMessage-3207b224.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { J as JetFormSection } from "./FormSection-d2d52d64.mjs";
import { J as JetInput } from "./Input-f9a8cf77.mjs";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { J as JetLabel } from "./Label-c5144ba4.mjs";
import { resolveComponent, mergeProps, createSlots, withCtx, createTextVNode, createVNode, toDisplayString, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderAttr, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./SectionTitle-70590d9c.mjs";
const _sfc_main = {
  components: {
    JetActionMessage,
    JetButton,
    JetFormSection,
    JetInput,
    JetInputError,
    JetLabel
  },
  props: ["team", "permissions"],
  data() {
    return {
      form: this.$inertia.form({
        name: this.team.name
      })
    };
  },
  methods: {
    updateTeamName() {
      this.form.put(route("teams.update", this.team), {
        errorBag: "updateTeamName",
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
  const _component_jet_action_message = resolveComponent("jet-action-message");
  const _component_jet_button = resolveComponent("jet-button");
  _push(ssrRenderComponent(_component_jet_form_section, mergeProps({ onSubmitted: $options.updateTeamName }, _attrs), createSlots({
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Team Name `);
      } else {
        return [
          createTextVNode(" Team Name ")
        ];
      }
    }),
    description: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` The team&#39;s name and owner information. `);
      } else {
        return [
          createTextVNode(" The team's name and owner information. ")
        ];
      }
    }),
    form: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="col-span-6"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, { value: "Team Owner" }, null, _parent2, _scopeId));
        _push2(`<div class="flex items-center mt-2"${_scopeId}><img class="w-12 h-12 rounded-full object-cover"${ssrRenderAttr("src", $props.team.owner.profile_photo_url)}${ssrRenderAttr("alt", $props.team.owner.name)}${_scopeId}><div class="ml-4 leading-tight"${_scopeId}><div${_scopeId}>${ssrInterpolate($props.team.owner.name)}</div><div class="text-gray-700 text-sm"${_scopeId}>${ssrInterpolate($props.team.owner.email)}</div></div></div></div><div class="col-span-6 sm:col-span-4"${_scopeId}>`);
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
          disabled: !$props.permissions.canUpdateTeam
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
                src: $props.team.owner.profile_photo_url,
                alt: $props.team.owner.name
              }, null, 8, ["src", "alt"]),
              createVNode("div", { class: "ml-4 leading-tight" }, [
                createVNode("div", null, toDisplayString($props.team.owner.name), 1),
                createVNode("div", { class: "text-gray-700 text-sm" }, toDisplayString($props.team.owner.email), 1)
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
              disabled: !$props.permissions.canUpdateTeam
            }, null, 8, ["modelValue", "onUpdate:modelValue", "disabled"]),
            createVNode(_component_jet_input_error, {
              message: $data.form.errors.name,
              class: "mt-2"
            }, null, 8, ["message"])
          ])
        ];
      }
    }),
    _: 2
  }, [
    $props.permissions.canUpdateTeam ? {
      name: "actions",
      fn: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(ssrRenderComponent(_component_jet_action_message, {
            on: $data.form.recentlySuccessful,
            class: "mr-3"
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Saved. `);
              } else {
                return [
                  createTextVNode(" Saved. ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_button, {
            class: { "opacity-25": $data.form.processing },
            disabled: $data.form.processing
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Save `);
              } else {
                return [
                  createTextVNode(" Save ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
        } else {
          return [
            createVNode(_component_jet_action_message, {
              on: $data.form.recentlySuccessful,
              class: "mr-3"
            }, {
              default: withCtx(() => [
                createTextVNode(" Saved. ")
              ]),
              _: 1
            }, 8, ["on"]),
            createVNode(_component_jet_button, {
              class: { "opacity-25": $data.form.processing },
              disabled: $data.form.processing
            }, {
              default: withCtx(() => [
                createTextVNode(" Save ")
              ]),
              _: 1
            }, 8, ["class", "disabled"])
          ];
        }
      }),
      key: "0"
    } : void 0
  ]), _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Teams/Partials/UpdateTeamNameForm.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const UpdateTeamNameForm = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  UpdateTeamNameForm as default
};
