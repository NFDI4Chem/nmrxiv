import { J as JetActionSection } from "./ActionSection-c1b53bcc.mjs";
import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { J as JetDangerButton } from "./DangerButton-9c23157c.mjs";
import { J as JetInput } from "./Input-f9a8cf77.mjs";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { resolveComponent, withCtx, createTextVNode, createVNode, withKeys, useSSRContext } from "vue";
import { ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./Modal-b2fb4cf2.mjs";
const _sfc_main = {
  components: {
    JetActionSection,
    JetDangerButton,
    JetDialogModal,
    JetInput,
    JetInputError,
    JetSecondaryButton
  },
  data() {
    return {
      confirmingUserDeletion: false,
      form: this.$inertia.form({
        password: ""
      })
    };
  },
  methods: {
    confirmUserDeletion() {
      this.confirmingUserDeletion = true;
      setTimeout(() => this.$refs.password.focus(), 250);
    },
    deleteUser() {
      this.form.delete(route("current-user.destroy"), {
        preserveScroll: true,
        onSuccess: () => this.closeModal(),
        onError: () => this.$refs.password.focus(),
        onFinish: () => this.form.reset()
      });
    },
    closeModal() {
      this.confirmingUserDeletion = false;
      this.form.reset();
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_action_section = resolveComponent("jet-action-section");
  const _component_jet_danger_button = resolveComponent("jet-danger-button");
  const _component_jet_dialog_modal = resolveComponent("jet-dialog-modal");
  const _component_jet_input = resolveComponent("jet-input");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  _push(ssrRenderComponent(_component_jet_action_section, _attrs, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Delete Account `);
      } else {
        return [
          createTextVNode(" Delete Account ")
        ];
      }
    }),
    description: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Permanently delete your account. `);
      } else {
        return [
          createTextVNode(" Permanently delete your account. ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="max-w-xl text-sm text-gray-600"${_scopeId}> Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain. </div><div class="mt-5"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_danger_button, { onClick: $options.confirmUserDeletion }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Delete Account `);
            } else {
              return [
                createTextVNode(" Delete Account ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div>`);
        _push2(ssrRenderComponent(_component_jet_dialog_modal, {
          show: $data.confirmingUserDeletion,
          onClose: $options.closeModal
        }, {
          title: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Delete Account `);
            } else {
              return [
                createTextVNode(" Delete Account ")
              ];
            }
          }),
          content: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account. <div class="mt-4"${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_jet_input, {
                ref: "password",
                modelValue: $data.form.password,
                "onUpdate:modelValue": ($event) => $data.form.password = $event,
                type: "password",
                class: "mt-1 block w-3/4",
                placeholder: "Password",
                onKeyup: $options.deleteUser
              }, null, _parent3, _scopeId2));
              _push3(ssrRenderComponent(_component_jet_input_error, {
                message: $data.form.errors.password,
                class: "mt-2"
              }, null, _parent3, _scopeId2));
              _push3(`</div>`);
            } else {
              return [
                createTextVNode(" Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account. "),
                createVNode("div", { class: "mt-4" }, [
                  createVNode(_component_jet_input, {
                    ref: "password",
                    modelValue: $data.form.password,
                    "onUpdate:modelValue": ($event) => $data.form.password = $event,
                    type: "password",
                    class: "mt-1 block w-3/4",
                    placeholder: "Password",
                    onKeyup: withKeys($options.deleteUser, ["enter"])
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "onKeyup"]),
                  createVNode(_component_jet_input_error, {
                    message: $data.form.errors.password,
                    class: "mt-2"
                  }, null, 8, ["message"])
                ])
              ];
            }
          }),
          footer: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_jet_secondary_button, { onClick: $options.closeModal }, {
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
                onClick: $options.deleteUser
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Delete Account `);
                  } else {
                    return [
                      createTextVNode(" Delete Account ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
            } else {
              return [
                createVNode(_component_jet_secondary_button, { onClick: $options.closeModal }, {
                  default: withCtx(() => [
                    createTextVNode(" Cancel ")
                  ]),
                  _: 1
                }, 8, ["onClick"]),
                createVNode(_component_jet_danger_button, {
                  class: ["ml-2", { "opacity-25": $data.form.processing }],
                  disabled: $data.form.processing,
                  onClick: $options.deleteUser
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Delete Account ")
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
          createVNode("div", { class: "max-w-xl text-sm text-gray-600" }, " Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain. "),
          createVNode("div", { class: "mt-5" }, [
            createVNode(_component_jet_danger_button, { onClick: $options.confirmUserDeletion }, {
              default: withCtx(() => [
                createTextVNode(" Delete Account ")
              ]),
              _: 1
            }, 8, ["onClick"])
          ]),
          createVNode(_component_jet_dialog_modal, {
            show: $data.confirmingUserDeletion,
            onClose: $options.closeModal
          }, {
            title: withCtx(() => [
              createTextVNode(" Delete Account ")
            ]),
            content: withCtx(() => [
              createTextVNode(" Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account. "),
              createVNode("div", { class: "mt-4" }, [
                createVNode(_component_jet_input, {
                  ref: "password",
                  modelValue: $data.form.password,
                  "onUpdate:modelValue": ($event) => $data.form.password = $event,
                  type: "password",
                  class: "mt-1 block w-3/4",
                  placeholder: "Password",
                  onKeyup: withKeys($options.deleteUser, ["enter"])
                }, null, 8, ["modelValue", "onUpdate:modelValue", "onKeyup"]),
                createVNode(_component_jet_input_error, {
                  message: $data.form.errors.password,
                  class: "mt-2"
                }, null, 8, ["message"])
              ])
            ]),
            footer: withCtx(() => [
              createVNode(_component_jet_secondary_button, { onClick: $options.closeModal }, {
                default: withCtx(() => [
                  createTextVNode(" Cancel ")
                ]),
                _: 1
              }, 8, ["onClick"]),
              createVNode(_component_jet_danger_button, {
                class: ["ml-2", { "opacity-25": $data.form.processing }],
                disabled: $data.form.processing,
                onClick: $options.deleteUser
              }, {
                default: withCtx(() => [
                  createTextVNode(" Delete Account ")
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Profile/Partials/DeleteUserForm.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const DeleteUserForm = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  DeleteUserForm as default
};
