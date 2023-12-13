import { J as JetActionSection } from "./ActionSection-c1b53bcc.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { J as JetInput } from "./Input-f9a8cf77.mjs";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { resolveComponent, withCtx, createTextVNode, toDisplayString, createVNode, withKeys, useSSRContext, openBlock, createBlock, createCommentVNode, Fragment, renderList } from "vue";
import { ssrRenderAttrs, ssrRenderSlot, ssrRenderComponent, ssrInterpolate, ssrRenderList } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import { J as JetDangerButton } from "./DangerButton-9c23157c.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./Modal-b2fb4cf2.mjs";
const _sfc_main$1 = {
  components: {
    JetButton,
    JetDialogModal,
    JetInput,
    JetInputError,
    JetSecondaryButton
  },
  props: {
    title: {
      default: "Confirm Password"
    },
    content: {
      default: "For your security, please confirm your password to continue."
    },
    button: {
      default: "Confirm"
    }
  },
  emits: ["confirmed"],
  data() {
    return {
      confirmingPassword: false,
      form: {
        password: "",
        error: ""
      }
    };
  },
  methods: {
    startConfirmingPassword() {
      axios.get(route("password.confirmation")).then((response) => {
        if (response.data.confirmed) {
          this.$emit("confirmed");
        } else {
          this.confirmingPassword = true;
          setTimeout(() => this.$refs.password.focus(), 250);
        }
      });
    },
    confirmPassword() {
      this.form.processing = true;
      axios.post(route("password.confirm"), {
        password: this.form.password
      }).then(() => {
        this.form.processing = false;
        this.closeModal();
        this.$nextTick(() => this.$emit("confirmed"));
      }).catch((error) => {
        this.form.processing = false;
        this.form.error = error.response.data.errors.password[0];
        this.$refs.password.focus();
      });
    },
    closeModal() {
      this.confirmingPassword = false;
      this.form.password = "";
      this.form.error = "";
    }
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_dialog_modal = resolveComponent("jet-dialog-modal");
  const _component_jet_input = resolveComponent("jet-input");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  const _component_jet_button = resolveComponent("jet-button");
  _push(`<span${ssrRenderAttrs(_attrs)}><span>`);
  ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
  _push(`</span>`);
  _push(ssrRenderComponent(_component_jet_dialog_modal, {
    show: $data.confirmingPassword,
    onClose: $options.closeModal
  }, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`${ssrInterpolate($props.title)}`);
      } else {
        return [
          createTextVNode(toDisplayString($props.title), 1)
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`${ssrInterpolate($props.content)} <div class="mt-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_input, {
          ref: "password",
          modelValue: $data.form.password,
          "onUpdate:modelValue": ($event) => $data.form.password = $event,
          type: "password",
          class: "mt-1 block w-3/4",
          placeholder: "Password",
          onKeyup: $options.confirmPassword
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.form.error,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`</div>`);
      } else {
        return [
          createTextVNode(toDisplayString($props.content) + " ", 1),
          createVNode("div", { class: "mt-4" }, [
            createVNode(_component_jet_input, {
              ref: "password",
              modelValue: $data.form.password,
              "onUpdate:modelValue": ($event) => $data.form.password = $event,
              type: "password",
              class: "mt-1 block w-3/4",
              placeholder: "Password",
              onKeyup: withKeys($options.confirmPassword, ["enter"])
            }, null, 8, ["modelValue", "onUpdate:modelValue", "onKeyup"]),
            createVNode(_component_jet_input_error, {
              message: $data.form.error,
              class: "mt-2"
            }, null, 8, ["message"])
          ])
        ];
      }
    }),
    footer: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_secondary_button, { onClick: $options.closeModal }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Cancel `);
            } else {
              return [
                createTextVNode(" Cancel ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_button, {
          class: ["ml-2", { "opacity-25": $data.form.processing }],
          disabled: $data.form.processing,
          onClick: $options.confirmPassword
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`${ssrInterpolate($props.button)}`);
            } else {
              return [
                createTextVNode(toDisplayString($props.button), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_jet_secondary_button, { onClick: $options.closeModal }, {
            default: withCtx(() => [
              createTextVNode(" Cancel ")
            ]),
            _: 1
          }, 8, ["onClick"]),
          createVNode(_component_jet_button, {
            class: ["ml-2", { "opacity-25": $data.form.processing }],
            disabled: $data.form.processing,
            onClick: $options.confirmPassword
          }, {
            default: withCtx(() => [
              createTextVNode(toDisplayString($props.button), 1)
            ]),
            _: 1
          }, 8, ["class", "disabled", "onClick"])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</span>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Jetstream/ConfirmsPassword.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const JetConfirmsPassword = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {
  components: {
    JetActionSection,
    JetButton,
    JetConfirmsPassword,
    JetDangerButton,
    JetSecondaryButton
  },
  data() {
    return {
      enabling: false,
      disabling: false,
      qrCode: null,
      recoveryCodes: []
    };
  },
  computed: {
    twoFactorEnabled() {
      return !this.enabling && this.$page.props.auth.user.two_factor_enabled;
    }
  },
  methods: {
    enableTwoFactorAuthentication() {
      this.enabling = true;
      this.$inertia.post(
        "/user/two-factor-authentication",
        {},
        {
          preserveScroll: true,
          onSuccess: () => Promise.all([
            this.showQrCode(),
            this.showRecoveryCodes()
          ]),
          onFinish: () => this.enabling = false
        }
      );
    },
    showQrCode() {
      return axios.get("/user/two-factor-qr-code").then((response) => {
        this.qrCode = response.data.svg;
      });
    },
    showRecoveryCodes() {
      return axios.get("/user/two-factor-recovery-codes").then((response) => {
        this.recoveryCodes = response.data;
      });
    },
    regenerateRecoveryCodes() {
      axios.post("/user/two-factor-recovery-codes").then((response) => {
        this.showRecoveryCodes();
      });
    },
    disableTwoFactorAuthentication() {
      this.disabling = true;
      this.$inertia.delete("/user/two-factor-authentication", {
        preserveScroll: true,
        onSuccess: () => this.disabling = false
      });
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_action_section = resolveComponent("jet-action-section");
  const _component_jet_confirms_password = resolveComponent("jet-confirms-password");
  const _component_jet_button = resolveComponent("jet-button");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  const _component_jet_danger_button = resolveComponent("jet-danger-button");
  _push(ssrRenderComponent(_component_jet_action_section, _attrs, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Two Factor Authentication `);
      } else {
        return [
          createTextVNode(" Two Factor Authentication ")
        ];
      }
    }),
    description: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Add additional security to your account using two factor authentication. `);
      } else {
        return [
          createTextVNode(" Add additional security to your account using two factor authentication. ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        if ($options.twoFactorEnabled) {
          _push2(`<h3 class="text-lg font-medium text-gray-900"${_scopeId}> You have enabled two factor authentication. </h3>`);
        } else {
          _push2(`<h3 class="text-lg font-medium text-gray-900"${_scopeId}> You have not enabled two factor authentication. </h3>`);
        }
        _push2(`<div class="mt-3 max-w-xl text-sm text-gray-600"${_scopeId}><p${_scopeId}> When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone&#39;s Google Authenticator application. </p></div>`);
        if ($options.twoFactorEnabled) {
          _push2(`<div${_scopeId}>`);
          if ($data.qrCode) {
            _push2(`<div${_scopeId}><div class="mt-4 max-w-xl text-sm text-gray-600"${_scopeId}><p class="font-semibold"${_scopeId}> Two factor authentication is now enabled. Scan the following QR code using your phone&#39;s authenticator application. </p></div><div class="mt-4"${_scopeId}>${$data.qrCode}</div></div>`);
          } else {
            _push2(`<!---->`);
          }
          if ($data.recoveryCodes.length > 0) {
            _push2(`<div${_scopeId}><div class="mt-4 max-w-xl text-sm text-gray-600"${_scopeId}><p class="font-semibold"${_scopeId}> Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost. </p></div><div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg"${_scopeId}><!--[-->`);
            ssrRenderList($data.recoveryCodes, (code) => {
              _push2(`<div${_scopeId}>${ssrInterpolate(code)}</div>`);
            });
            _push2(`<!--]--></div></div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div class="mt-5"${_scopeId}>`);
        if (!$options.twoFactorEnabled) {
          _push2(`<div${_scopeId}>`);
          _push2(ssrRenderComponent(_component_jet_confirms_password, { onConfirmed: $options.enableTwoFactorAuthentication }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(ssrRenderComponent(_component_jet_button, {
                  type: "button",
                  class: { "opacity-25": $data.enabling },
                  disabled: $data.enabling
                }, {
                  default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                    if (_push4) {
                      _push4(` Enable `);
                    } else {
                      return [
                        createTextVNode(" Enable ")
                      ];
                    }
                  }),
                  _: 1
                }, _parent3, _scopeId2));
              } else {
                return [
                  createVNode(_component_jet_button, {
                    type: "button",
                    class: { "opacity-25": $data.enabling },
                    disabled: $data.enabling
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Enable ")
                    ]),
                    _: 1
                  }, 8, ["class", "disabled"])
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<div${_scopeId}>`);
          _push2(ssrRenderComponent(_component_jet_confirms_password, { onConfirmed: $options.regenerateRecoveryCodes }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                if ($data.recoveryCodes.length > 0) {
                  _push3(ssrRenderComponent(_component_jet_secondary_button, { class: "mr-3" }, {
                    default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                      if (_push4) {
                        _push4(` Regenerate Recovery Codes `);
                      } else {
                        return [
                          createTextVNode(" Regenerate Recovery Codes ")
                        ];
                      }
                    }),
                    _: 1
                  }, _parent3, _scopeId2));
                } else {
                  _push3(`<!---->`);
                }
              } else {
                return [
                  $data.recoveryCodes.length > 0 ? (openBlock(), createBlock(_component_jet_secondary_button, {
                    key: 0,
                    class: "mr-3"
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Regenerate Recovery Codes ")
                    ]),
                    _: 1
                  })) : createCommentVNode("", true)
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_confirms_password, { onConfirmed: $options.showRecoveryCodes }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                if ($data.recoveryCodes.length === 0) {
                  _push3(ssrRenderComponent(_component_jet_secondary_button, { class: "mr-3" }, {
                    default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                      if (_push4) {
                        _push4(` Show Recovery Codes `);
                      } else {
                        return [
                          createTextVNode(" Show Recovery Codes ")
                        ];
                      }
                    }),
                    _: 1
                  }, _parent3, _scopeId2));
                } else {
                  _push3(`<!---->`);
                }
              } else {
                return [
                  $data.recoveryCodes.length === 0 ? (openBlock(), createBlock(_component_jet_secondary_button, {
                    key: 0,
                    class: "mr-3"
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Show Recovery Codes ")
                    ]),
                    _: 1
                  })) : createCommentVNode("", true)
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_confirms_password, { onConfirmed: $options.disableTwoFactorAuthentication }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(ssrRenderComponent(_component_jet_danger_button, {
                  class: { "opacity-25": $data.disabling },
                  disabled: $data.disabling
                }, {
                  default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                    if (_push4) {
                      _push4(` Disable `);
                    } else {
                      return [
                        createTextVNode(" Disable ")
                      ];
                    }
                  }),
                  _: 1
                }, _parent3, _scopeId2));
              } else {
                return [
                  createVNode(_component_jet_danger_button, {
                    class: { "opacity-25": $data.disabling },
                    disabled: $data.disabling
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Disable ")
                    ]),
                    _: 1
                  }, 8, ["class", "disabled"])
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div>`);
        }
        _push2(`</div>`);
      } else {
        return [
          $options.twoFactorEnabled ? (openBlock(), createBlock("h3", {
            key: 0,
            class: "text-lg font-medium text-gray-900"
          }, " You have enabled two factor authentication. ")) : (openBlock(), createBlock("h3", {
            key: 1,
            class: "text-lg font-medium text-gray-900"
          }, " You have not enabled two factor authentication. ")),
          createVNode("div", { class: "mt-3 max-w-xl text-sm text-gray-600" }, [
            createVNode("p", null, " When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application. ")
          ]),
          $options.twoFactorEnabled ? (openBlock(), createBlock("div", { key: 2 }, [
            $data.qrCode ? (openBlock(), createBlock("div", { key: 0 }, [
              createVNode("div", { class: "mt-4 max-w-xl text-sm text-gray-600" }, [
                createVNode("p", { class: "font-semibold" }, " Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator application. ")
              ]),
              createVNode("div", {
                class: "mt-4",
                innerHTML: $data.qrCode
              }, null, 8, ["innerHTML"])
            ])) : createCommentVNode("", true),
            $data.recoveryCodes.length > 0 ? (openBlock(), createBlock("div", { key: 1 }, [
              createVNode("div", { class: "mt-4 max-w-xl text-sm text-gray-600" }, [
                createVNode("p", { class: "font-semibold" }, " Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost. ")
              ]),
              createVNode("div", { class: "grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg" }, [
                (openBlock(true), createBlock(Fragment, null, renderList($data.recoveryCodes, (code) => {
                  return openBlock(), createBlock("div", { key: code }, toDisplayString(code), 1);
                }), 128))
              ])
            ])) : createCommentVNode("", true)
          ])) : createCommentVNode("", true),
          createVNode("div", { class: "mt-5" }, [
            !$options.twoFactorEnabled ? (openBlock(), createBlock("div", { key: 0 }, [
              createVNode(_component_jet_confirms_password, { onConfirmed: $options.enableTwoFactorAuthentication }, {
                default: withCtx(() => [
                  createVNode(_component_jet_button, {
                    type: "button",
                    class: { "opacity-25": $data.enabling },
                    disabled: $data.enabling
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Enable ")
                    ]),
                    _: 1
                  }, 8, ["class", "disabled"])
                ]),
                _: 1
              }, 8, ["onConfirmed"])
            ])) : (openBlock(), createBlock("div", { key: 1 }, [
              createVNode(_component_jet_confirms_password, { onConfirmed: $options.regenerateRecoveryCodes }, {
                default: withCtx(() => [
                  $data.recoveryCodes.length > 0 ? (openBlock(), createBlock(_component_jet_secondary_button, {
                    key: 0,
                    class: "mr-3"
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Regenerate Recovery Codes ")
                    ]),
                    _: 1
                  })) : createCommentVNode("", true)
                ]),
                _: 1
              }, 8, ["onConfirmed"]),
              createVNode(_component_jet_confirms_password, { onConfirmed: $options.showRecoveryCodes }, {
                default: withCtx(() => [
                  $data.recoveryCodes.length === 0 ? (openBlock(), createBlock(_component_jet_secondary_button, {
                    key: 0,
                    class: "mr-3"
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Show Recovery Codes ")
                    ]),
                    _: 1
                  })) : createCommentVNode("", true)
                ]),
                _: 1
              }, 8, ["onConfirmed"]),
              createVNode(_component_jet_confirms_password, { onConfirmed: $options.disableTwoFactorAuthentication }, {
                default: withCtx(() => [
                  createVNode(_component_jet_danger_button, {
                    class: { "opacity-25": $data.disabling },
                    disabled: $data.disabling
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Disable ")
                    ]),
                    _: 1
                  }, 8, ["class", "disabled"])
                ]),
                _: 1
              }, 8, ["onConfirmed"])
            ]))
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const TwoFactorAuthenticationForm = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  TwoFactorAuthenticationForm as default
};
