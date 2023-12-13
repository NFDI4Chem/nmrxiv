import { J as JetActionMessage } from "./ActionMessage-3207b224.mjs";
import { J as JetActionSection } from "./ActionSection-c1b53bcc.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { J as JetInput } from "./Input-f9a8cf77.mjs";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { resolveComponent, withCtx, createTextVNode, createVNode, withKeys, openBlock, createBlock, Fragment, renderList, toDisplayString, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderList, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./Modal-b2fb4cf2.mjs";
const _sfc_main = {
  components: {
    JetActionMessage,
    JetActionSection,
    JetButton,
    JetDialogModal,
    JetInput,
    JetInputError,
    JetSecondaryButton
  },
  props: ["sessions"],
  data() {
    return {
      confirmingLogout: false,
      form: this.$inertia.form({
        password: ""
      })
    };
  },
  methods: {
    confirmLogout() {
      this.confirmingLogout = true;
      setTimeout(() => this.$refs.password.focus(), 250);
    },
    logoutOtherBrowserSessions() {
      this.form.delete(route("other-browser-sessions.destroy"), {
        preserveScroll: true,
        onSuccess: () => this.closeModal(),
        onError: () => this.$refs.password.focus(),
        onFinish: () => this.form.reset()
      });
    },
    closeModal() {
      this.confirmingLogout = false;
      this.form.reset();
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_action_section = resolveComponent("jet-action-section");
  const _component_jet_button = resolveComponent("jet-button");
  const _component_jet_action_message = resolveComponent("jet-action-message");
  const _component_jet_dialog_modal = resolveComponent("jet-dialog-modal");
  const _component_jet_input = resolveComponent("jet-input");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  _push(ssrRenderComponent(_component_jet_action_section, _attrs, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Browser Sessions `);
      } else {
        return [
          createTextVNode(" Browser Sessions ")
        ];
      }
    }),
    description: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Manage and log out your active sessions on other browsers and devices. `);
      } else {
        return [
          createTextVNode(" Manage and log out your active sessions on other browsers and devices. ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="max-w-xl text-sm text-gray-600"${_scopeId}> If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password. </div>`);
        if ($props.sessions.length > 0) {
          _push2(`<div class="mt-5 space-y-6"${_scopeId}><!--[-->`);
          ssrRenderList($props.sessions, (session, i) => {
            _push2(`<div class="flex items-center"${_scopeId}><div${_scopeId}>`);
            if (session.agent.is_desktop) {
              _push2(`<svg fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor" class="w-8 h-8 text-gray-500"${_scopeId}><path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"${_scopeId}></path></svg>`);
            } else {
              _push2(`<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-gray-500"${_scopeId}><path d="M0 0h24v24H0z" stroke="none"${_scopeId}></path><rect x="7" y="4" width="10" height="16" rx="1"${_scopeId}></rect><path d="M11 5h2M12 17v.01"${_scopeId}></path></svg>`);
            }
            _push2(`</div><div class="ml-3"${_scopeId}><div class="text-sm text-gray-600"${_scopeId}>${ssrInterpolate(session.agent.platform)} - ${ssrInterpolate(session.agent.browser)}</div><div${_scopeId}><div class="text-xs text-gray-500"${_scopeId}>${ssrInterpolate(session.ip_address)}, `);
            if (session.is_current_device) {
              _push2(`<span class="text-green-500 font-semibold"${_scopeId}>This device</span>`);
            } else {
              _push2(`<span${_scopeId}>Last active ${ssrInterpolate(session.last_active)}</span>`);
            }
            _push2(`</div></div></div></div>`);
          });
          _push2(`<!--]--></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div class="flex items-center mt-5"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_button, { onClick: $options.confirmLogout }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Log Out Other Browser Sessions `);
            } else {
              return [
                createTextVNode(" Log Out Other Browser Sessions ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_action_message, {
          on: $data.form.recentlySuccessful,
          class: "ml-3"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Done. `);
            } else {
              return [
                createTextVNode(" Done. ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div>`);
        _push2(ssrRenderComponent(_component_jet_dialog_modal, {
          show: $data.confirmingLogout,
          onClose: $options.closeModal
        }, {
          title: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Log Out Other Browser Sessions `);
            } else {
              return [
                createTextVNode(" Log Out Other Browser Sessions ")
              ];
            }
          }),
          content: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices. <div class="mt-4"${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_jet_input, {
                ref: "password",
                modelValue: $data.form.password,
                "onUpdate:modelValue": ($event) => $data.form.password = $event,
                type: "password",
                class: "mt-1 block w-3/4",
                placeholder: "Password",
                onKeyup: $options.logoutOtherBrowserSessions
              }, null, _parent3, _scopeId2));
              _push3(ssrRenderComponent(_component_jet_input_error, {
                message: $data.form.errors.password,
                class: "mt-2"
              }, null, _parent3, _scopeId2));
              _push3(`</div>`);
            } else {
              return [
                createTextVNode(" Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices. "),
                createVNode("div", { class: "mt-4" }, [
                  createVNode(_component_jet_input, {
                    ref: "password",
                    modelValue: $data.form.password,
                    "onUpdate:modelValue": ($event) => $data.form.password = $event,
                    type: "password",
                    class: "mt-1 block w-3/4",
                    placeholder: "Password",
                    onKeyup: withKeys($options.logoutOtherBrowserSessions, ["enter"])
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
              _push3(ssrRenderComponent(_component_jet_button, {
                class: ["ml-2", { "opacity-25": $data.form.processing }],
                disabled: $data.form.processing,
                onClick: $options.logoutOtherBrowserSessions
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Log Out Other Browser Sessions `);
                  } else {
                    return [
                      createTextVNode(" Log Out Other Browser Sessions ")
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
                createVNode(_component_jet_button, {
                  class: ["ml-2", { "opacity-25": $data.form.processing }],
                  disabled: $data.form.processing,
                  onClick: $options.logoutOtherBrowserSessions
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Log Out Other Browser Sessions ")
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
          createVNode("div", { class: "max-w-xl text-sm text-gray-600" }, " If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password. "),
          $props.sessions.length > 0 ? (openBlock(), createBlock("div", {
            key: 0,
            class: "mt-5 space-y-6"
          }, [
            (openBlock(true), createBlock(Fragment, null, renderList($props.sessions, (session, i) => {
              return openBlock(), createBlock("div", {
                key: i,
                class: "flex items-center"
              }, [
                createVNode("div", null, [
                  session.agent.is_desktop ? (openBlock(), createBlock("svg", {
                    key: 0,
                    fill: "none",
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-width": "2",
                    viewBox: "0 0 24 24",
                    stroke: "currentColor",
                    class: "w-8 h-8 text-gray-500"
                  }, [
                    createVNode("path", { d: "M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" })
                  ])) : (openBlock(), createBlock("svg", {
                    key: 1,
                    xmlns: "http://www.w3.org/2000/svg",
                    viewBox: "0 0 24 24",
                    "stroke-width": "2",
                    stroke: "currentColor",
                    fill: "none",
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    class: "w-8 h-8 text-gray-500"
                  }, [
                    createVNode("path", {
                      d: "M0 0h24v24H0z",
                      stroke: "none"
                    }),
                    createVNode("rect", {
                      x: "7",
                      y: "4",
                      width: "10",
                      height: "16",
                      rx: "1"
                    }),
                    createVNode("path", { d: "M11 5h2M12 17v.01" })
                  ]))
                ]),
                createVNode("div", { class: "ml-3" }, [
                  createVNode("div", { class: "text-sm text-gray-600" }, toDisplayString(session.agent.platform) + " - " + toDisplayString(session.agent.browser), 1),
                  createVNode("div", null, [
                    createVNode("div", { class: "text-xs text-gray-500" }, [
                      createTextVNode(toDisplayString(session.ip_address) + ", ", 1),
                      session.is_current_device ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "text-green-500 font-semibold"
                      }, "This device")) : (openBlock(), createBlock("span", { key: 1 }, "Last active " + toDisplayString(session.last_active), 1))
                    ])
                  ])
                ])
              ]);
            }), 128))
          ])) : createCommentVNode("", true),
          createVNode("div", { class: "flex items-center mt-5" }, [
            createVNode(_component_jet_button, { onClick: $options.confirmLogout }, {
              default: withCtx(() => [
                createTextVNode(" Log Out Other Browser Sessions ")
              ]),
              _: 1
            }, 8, ["onClick"]),
            createVNode(_component_jet_action_message, {
              on: $data.form.recentlySuccessful,
              class: "ml-3"
            }, {
              default: withCtx(() => [
                createTextVNode(" Done. ")
              ]),
              _: 1
            }, 8, ["on"])
          ]),
          createVNode(_component_jet_dialog_modal, {
            show: $data.confirmingLogout,
            onClose: $options.closeModal
          }, {
            title: withCtx(() => [
              createTextVNode(" Log Out Other Browser Sessions ")
            ]),
            content: withCtx(() => [
              createTextVNode(" Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices. "),
              createVNode("div", { class: "mt-4" }, [
                createVNode(_component_jet_input, {
                  ref: "password",
                  modelValue: $data.form.password,
                  "onUpdate:modelValue": ($event) => $data.form.password = $event,
                  type: "password",
                  class: "mt-1 block w-3/4",
                  placeholder: "Password",
                  onKeyup: withKeys($options.logoutOtherBrowserSessions, ["enter"])
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
              createVNode(_component_jet_button, {
                class: ["ml-2", { "opacity-25": $data.form.processing }],
                disabled: $data.form.processing,
                onClick: $options.logoutOtherBrowserSessions
              }, {
                default: withCtx(() => [
                  createTextVNode(" Log Out Other Browser Sessions ")
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const LogoutOtherBrowserSessionsForm = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  LogoutOtherBrowserSessionsForm as default
};
