import { J as JetActionSection } from "./ActionSection-c1b53bcc.mjs";
import { J as JetConfirmationModal } from "./ConfirmationModal-d0b47856.mjs";
import { J as JetDangerButton } from "./DangerButton-9c23157c.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { J as JetInput } from "./Input-f9a8cf77.mjs";
import { resolveComponent, withCtx, createTextVNode, createVNode, withKeys, useSSRContext } from "vue";
import { ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./Modal-b2fb4cf2.mjs";
const _sfc_main = {
  components: {
    JetActionSection,
    JetConfirmationModal,
    JetDangerButton,
    JetSecondaryButton,
    JetInputError,
    JetInput
  },
  props: ["team"],
  data() {
    return {
      confirmingTeamDeletion: false,
      deleting: false,
      form: this.$inertia.form({
        password: ""
      })
    };
  },
  methods: {
    confirmTeamDeletion() {
      this.confirmingTeamDeletion = true;
    },
    deleteTeam() {
      this.form.delete(route("app.teams.destroy", this.team), {
        onError: () => this.$refs.password.focus(),
        errorBag: "deleteTeam",
        onFinish: () => this.form.reset()
      });
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_action_section = resolveComponent("jet-action-section");
  const _component_jet_danger_button = resolveComponent("jet-danger-button");
  const _component_jet_confirmation_modal = resolveComponent("jet-confirmation-modal");
  const _component_jet_input = resolveComponent("jet-input");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  _push(ssrRenderComponent(_component_jet_action_section, _attrs, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Delete Team `);
      } else {
        return [
          createTextVNode(" Delete Team ")
        ];
      }
    }),
    description: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Permanently delete this team. `);
      } else {
        return [
          createTextVNode(" Permanently delete this team. ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="max-w-xl text-sm text-gray-600"${_scopeId}> Once a team is deleted, all of its resources and data will be permanently deleted. Before deleting this team, please download any data or information regarding this team that you wish to retain. </div><div class="mt-5"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_danger_button, { onClick: $options.confirmTeamDeletion }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Delete Team `);
            } else {
              return [
                createTextVNode(" Delete Team ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div>`);
        _push2(ssrRenderComponent(_component_jet_confirmation_modal, {
          show: $data.confirmingTeamDeletion,
          onClose: ($event) => $data.confirmingTeamDeletion = false
        }, {
          title: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Delete Team `);
            } else {
              return [
                createTextVNode(" Delete Team ")
              ];
            }
          }),
          content: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Are you sure you want to delete this team? Once a team is deleted, all of its resources and data will be permanently deleted. <div class="mt-4"${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_jet_input, {
                ref: "password",
                modelValue: $data.form.password,
                "onUpdate:modelValue": ($event) => $data.form.password = $event,
                type: "password",
                class: "mt-1 block w-3/4",
                placeholder: "Password",
                onKeyup: $options.deleteTeam
              }, null, _parent3, _scopeId2));
              _push3(ssrRenderComponent(_component_jet_input_error, {
                message: $data.form.errors.password,
                class: "mt-2"
              }, null, _parent3, _scopeId2));
              _push3(`</div>`);
              _push3(ssrRenderComponent(_component_jet_input_error, {
                message: $data.form.errors.hasProject,
                class: "mt-2"
              }, null, _parent3, _scopeId2));
            } else {
              return [
                createTextVNode(" Are you sure you want to delete this team? Once a team is deleted, all of its resources and data will be permanently deleted. "),
                createVNode("div", { class: "mt-4" }, [
                  createVNode(_component_jet_input, {
                    ref: "password",
                    modelValue: $data.form.password,
                    "onUpdate:modelValue": ($event) => $data.form.password = $event,
                    type: "password",
                    class: "mt-1 block w-3/4",
                    placeholder: "Password",
                    onKeyup: withKeys($options.deleteTeam, ["enter"])
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "onKeyup"]),
                  createVNode(_component_jet_input_error, {
                    message: $data.form.errors.password,
                    class: "mt-2"
                  }, null, 8, ["message"])
                ]),
                createVNode(_component_jet_input_error, {
                  message: $data.form.errors.hasProject,
                  class: "mt-2"
                }, null, 8, ["message"])
              ];
            }
          }),
          footer: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_jet_secondary_button, {
                onClick: ($event) => $data.confirmingTeamDeletion = false
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Cancel `);
                  } else {
                    return [
                      createTextVNode(" Cancel ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(ssrRenderComponent(_component_jet_danger_button, {
                class: ["ml-2", { "opacity-25": $data.form.processing }],
                disabled: $data.form.processing,
                onClick: $options.deleteTeam
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Delete Team `);
                  } else {
                    return [
                      createTextVNode(" Delete Team ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
            } else {
              return [
                createVNode(_component_jet_secondary_button, {
                  onClick: ($event) => $data.confirmingTeamDeletion = false
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Cancel ")
                  ]),
                  _: 1
                }, 8, ["onClick"]),
                createVNode(_component_jet_danger_button, {
                  class: ["ml-2", { "opacity-25": $data.form.processing }],
                  disabled: $data.form.processing,
                  onClick: $options.deleteTeam
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Delete Team ")
                  ]),
                  _: 1
                }, 8, ["class", "disabled", "onClick"])
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode("div", { class: "max-w-xl text-sm text-gray-600" }, " Once a team is deleted, all of its resources and data will be permanently deleted. Before deleting this team, please download any data or information regarding this team that you wish to retain. "),
          createVNode("div", { class: "mt-5" }, [
            createVNode(_component_jet_danger_button, { onClick: $options.confirmTeamDeletion }, {
              default: withCtx(() => [
                createTextVNode(" Delete Team ")
              ]),
              _: 1
            }, 8, ["onClick"])
          ]),
          createVNode(_component_jet_confirmation_modal, {
            show: $data.confirmingTeamDeletion,
            onClose: ($event) => $data.confirmingTeamDeletion = false
          }, {
            title: withCtx(() => [
              createTextVNode(" Delete Team ")
            ]),
            content: withCtx(() => [
              createTextVNode(" Are you sure you want to delete this team? Once a team is deleted, all of its resources and data will be permanently deleted. "),
              createVNode("div", { class: "mt-4" }, [
                createVNode(_component_jet_input, {
                  ref: "password",
                  modelValue: $data.form.password,
                  "onUpdate:modelValue": ($event) => $data.form.password = $event,
                  type: "password",
                  class: "mt-1 block w-3/4",
                  placeholder: "Password",
                  onKeyup: withKeys($options.deleteTeam, ["enter"])
                }, null, 8, ["modelValue", "onUpdate:modelValue", "onKeyup"]),
                createVNode(_component_jet_input_error, {
                  message: $data.form.errors.password,
                  class: "mt-2"
                }, null, 8, ["message"])
              ]),
              createVNode(_component_jet_input_error, {
                message: $data.form.errors.hasProject,
                class: "mt-2"
              }, null, 8, ["message"])
            ]),
            footer: withCtx(() => [
              createVNode(_component_jet_secondary_button, {
                onClick: ($event) => $data.confirmingTeamDeletion = false
              }, {
                default: withCtx(() => [
                  createTextVNode(" Cancel ")
                ]),
                _: 1
              }, 8, ["onClick"]),
              createVNode(_component_jet_danger_button, {
                class: ["ml-2", { "opacity-25": $data.form.processing }],
                disabled: $data.form.processing,
                onClick: $options.deleteTeam
              }, {
                default: withCtx(() => [
                  createTextVNode(" Delete Team ")
                ]),
                _: 1
              }, 8, ["class", "disabled", "onClick"])
            ]),
            _: 1
          }, 8, ["show", "onClose"])
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Teams/Partials/DeleteTeamForm.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const DeleteTeamForm = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  DeleteTeamForm as default
};
