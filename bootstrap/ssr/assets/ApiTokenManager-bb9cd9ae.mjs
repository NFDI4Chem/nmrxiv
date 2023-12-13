import { J as JetActionMessage } from "./ActionMessage-3207b224.mjs";
import { J as JetActionSection } from "./ActionSection-c1b53bcc.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { J as JetConfirmationModal } from "./ConfirmationModal-d0b47856.mjs";
import { J as JetDangerButton } from "./DangerButton-9c23157c.mjs";
import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { J as JetFormSection } from "./FormSection-d2d52d64.mjs";
import { J as JetInput } from "./Input-f9a8cf77.mjs";
import { J as JetCheckbox } from "./Checkbox-ed79f759.mjs";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { J as JetLabel } from "./Label-c5144ba4.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetSectionBorder } from "./SectionBorder-7d1f222a.mjs";
import { resolveComponent, withCtx, createTextVNode, createVNode, openBlock, createBlock, Fragment, renderList, toDisplayString, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderList, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./Modal-b2fb4cf2.mjs";
const _sfc_main = {
  components: {
    JetActionMessage,
    JetActionSection,
    JetButton,
    JetConfirmationModal,
    JetDangerButton,
    JetDialogModal,
    JetFormSection,
    JetInput,
    JetCheckbox,
    JetInputError,
    JetLabel,
    JetSecondaryButton,
    JetSectionBorder
  },
  props: ["tokens", "availablePermissions", "defaultPermissions"],
  data() {
    return {
      createApiTokenForm: this.$inertia.form({
        name: "",
        permissions: this.defaultPermissions
      }),
      updateApiTokenForm: this.$inertia.form({
        permissions: []
      }),
      deleteApiTokenForm: this.$inertia.form({}),
      displayingToken: false,
      managingPermissionsFor: null,
      apiTokenBeingDeleted: null
    };
  },
  methods: {
    createApiToken() {
      this.createApiTokenForm.post(route("api-tokens.store"), {
        preserveScroll: true,
        onSuccess: () => {
          this.displayingToken = true;
          this.createApiTokenForm.reset();
        }
      });
    },
    manageApiTokenPermissions(token) {
      this.updateApiTokenForm.permissions = token.abilities;
      this.managingPermissionsFor = token;
    },
    updateApiToken() {
      this.updateApiTokenForm.put(
        route("api-tokens.update", this.managingPermissionsFor),
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => this.managingPermissionsFor = null
        }
      );
    },
    confirmApiTokenDeletion(token) {
      this.apiTokenBeingDeleted = token;
    },
    deleteApiToken() {
      this.deleteApiTokenForm.delete(
        route("api-tokens.destroy", this.apiTokenBeingDeleted),
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => this.apiTokenBeingDeleted = null
        }
      );
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_form_section = resolveComponent("jet-form-section");
  const _component_jet_label = resolveComponent("jet-label");
  const _component_jet_input = resolveComponent("jet-input");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_jet_checkbox = resolveComponent("jet-checkbox");
  const _component_jet_action_message = resolveComponent("jet-action-message");
  const _component_jet_button = resolveComponent("jet-button");
  const _component_jet_section_border = resolveComponent("jet-section-border");
  const _component_jet_action_section = resolveComponent("jet-action-section");
  const _component_jet_dialog_modal = resolveComponent("jet-dialog-modal");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  const _component_jet_confirmation_modal = resolveComponent("jet-confirmation-modal");
  const _component_jet_danger_button = resolveComponent("jet-danger-button");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  _push(ssrRenderComponent(_component_jet_form_section, { onSubmitted: $options.createApiToken }, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Create API Token `);
      } else {
        return [
          createTextVNode(" Create API Token ")
        ];
      }
    }),
    description: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` API tokens allow third-party services to authenticate with our application on your behalf. `);
      } else {
        return [
          createTextVNode(" API tokens allow third-party services to authenticate with our application on your behalf. ")
        ];
      }
    }),
    form: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="col-span-6 sm:col-span-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, {
          for: "name",
          value: "Name"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input, {
          id: "name",
          modelValue: $data.createApiTokenForm.name,
          "onUpdate:modelValue": ($event) => $data.createApiTokenForm.name = $event,
          type: "text",
          class: "mt-1 block w-full",
          autofocus: ""
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.createApiTokenForm.errors.name,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`</div>`);
        if ($props.availablePermissions.length > 0) {
          _push2(`<div class="col-span-6"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_jet_label, {
            for: "permissions",
            value: "Permissions"
          }, null, _parent2, _scopeId));
          _push2(`<div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4"${_scopeId}><!--[-->`);
          ssrRenderList($props.availablePermissions, (permission) => {
            _push2(`<div${_scopeId}><label class="flex items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_jet_checkbox, {
              checked: $data.createApiTokenForm.permissions,
              "onUpdate:checked": ($event) => $data.createApiTokenForm.permissions = $event,
              value: permission
            }, null, _parent2, _scopeId));
            _push2(`<span class="ml-2 text-sm text-gray-600"${_scopeId}>${ssrInterpolate(permission)}</span></label></div>`);
          });
          _push2(`<!--]--></div></div>`);
        } else {
          _push2(`<!---->`);
        }
      } else {
        return [
          createVNode("div", { class: "col-span-6 sm:col-span-4" }, [
            createVNode(_component_jet_label, {
              for: "name",
              value: "Name"
            }),
            createVNode(_component_jet_input, {
              id: "name",
              modelValue: $data.createApiTokenForm.name,
              "onUpdate:modelValue": ($event) => $data.createApiTokenForm.name = $event,
              type: "text",
              class: "mt-1 block w-full",
              autofocus: ""
            }, null, 8, ["modelValue", "onUpdate:modelValue"]),
            createVNode(_component_jet_input_error, {
              message: $data.createApiTokenForm.errors.name,
              class: "mt-2"
            }, null, 8, ["message"])
          ]),
          $props.availablePermissions.length > 0 ? (openBlock(), createBlock("div", {
            key: 0,
            class: "col-span-6"
          }, [
            createVNode(_component_jet_label, {
              for: "permissions",
              value: "Permissions"
            }),
            createVNode("div", { class: "mt-2 grid grid-cols-1 md:grid-cols-2 gap-4" }, [
              (openBlock(true), createBlock(Fragment, null, renderList($props.availablePermissions, (permission) => {
                return openBlock(), createBlock("div", { key: permission }, [
                  createVNode("label", { class: "flex items-center" }, [
                    createVNode(_component_jet_checkbox, {
                      checked: $data.createApiTokenForm.permissions,
                      "onUpdate:checked": ($event) => $data.createApiTokenForm.permissions = $event,
                      value: permission
                    }, null, 8, ["checked", "onUpdate:checked", "value"]),
                    createVNode("span", { class: "ml-2 text-sm text-gray-600" }, toDisplayString(permission), 1)
                  ])
                ]);
              }), 128))
            ])
          ])) : createCommentVNode("", true)
        ];
      }
    }),
    actions: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_action_message, {
          on: $data.createApiTokenForm.recentlySuccessful,
          class: "mr-3"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Created. `);
            } else {
              return [
                createTextVNode(" Created. ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_button, {
          class: { "opacity-25": $data.createApiTokenForm.processing },
          disabled: $data.createApiTokenForm.processing
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
          createVNode(_component_jet_action_message, {
            on: $data.createApiTokenForm.recentlySuccessful,
            class: "mr-3"
          }, {
            default: withCtx(() => [
              createTextVNode(" Created. ")
            ]),
            _: 1
          }, 8, ["on"]),
          createVNode(_component_jet_button, {
            class: { "opacity-25": $data.createApiTokenForm.processing },
            disabled: $data.createApiTokenForm.processing
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
  if ($props.tokens.length > 0) {
    _push(`<div>`);
    _push(ssrRenderComponent(_component_jet_section_border, null, null, _parent));
    _push(`<div class="mt-10 sm:mt-0">`);
    _push(ssrRenderComponent(_component_jet_action_section, null, {
      title: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(` Manage API Tokens `);
        } else {
          return [
            createTextVNode(" Manage API Tokens ")
          ];
        }
      }),
      description: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(` You may delete any of your existing tokens if they are no longer needed. `);
        } else {
          return [
            createTextVNode(" You may delete any of your existing tokens if they are no longer needed. ")
          ];
        }
      }),
      content: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`<div class="space-y-6"${_scopeId}><!--[-->`);
          ssrRenderList($props.tokens, (token) => {
            _push2(`<div class="flex items-center justify-between"${_scopeId}><div${_scopeId}>${ssrInterpolate(token.name)}</div><div class="flex items-center"${_scopeId}>`);
            if (token.last_used_ago) {
              _push2(`<div class="text-sm text-gray-400"${_scopeId}> Last used ${ssrInterpolate(token.last_used_ago)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if ($props.availablePermissions.length > 0) {
              _push2(`<button class="cursor-pointer ml-6 text-sm text-gray-400 underline"${_scopeId}> Permissions </button>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<button class="cursor-pointer ml-6 text-sm text-red-500"${_scopeId}> Delete </button></div></div>`);
          });
          _push2(`<!--]--></div>`);
        } else {
          return [
            createVNode("div", { class: "space-y-6" }, [
              (openBlock(true), createBlock(Fragment, null, renderList($props.tokens, (token) => {
                return openBlock(), createBlock("div", {
                  key: token.id,
                  class: "flex items-center justify-between"
                }, [
                  createVNode("div", null, toDisplayString(token.name), 1),
                  createVNode("div", { class: "flex items-center" }, [
                    token.last_used_ago ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-sm text-gray-400"
                    }, " Last used " + toDisplayString(token.last_used_ago), 1)) : createCommentVNode("", true),
                    $props.availablePermissions.length > 0 ? (openBlock(), createBlock("button", {
                      key: 1,
                      class: "cursor-pointer ml-6 text-sm text-gray-400 underline",
                      onClick: ($event) => $options.manageApiTokenPermissions(token)
                    }, " Permissions ", 8, ["onClick"])) : createCommentVNode("", true),
                    createVNode("button", {
                      class: "cursor-pointer ml-6 text-sm text-red-500",
                      onClick: ($event) => $options.confirmApiTokenDeletion(token)
                    }, " Delete ", 8, ["onClick"])
                  ])
                ]);
              }), 128))
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
  _push(ssrRenderComponent(_component_jet_dialog_modal, {
    show: $data.displayingToken,
    onClose: ($event) => $data.displayingToken = false
  }, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` API Token `);
      } else {
        return [
          createTextVNode(" API Token ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div${_scopeId}> Please copy your new API token. For your security, it won&#39;t be shown again. </div>`);
        if (_ctx.$page.props.jetstream.flash.token) {
          _push2(`<div class="mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500"${_scopeId}>${ssrInterpolate(_ctx.$page.props.jetstream.flash.token)}</div>`);
        } else {
          _push2(`<!---->`);
        }
      } else {
        return [
          createVNode("div", null, " Please copy your new API token. For your security, it won't be shown again. "),
          _ctx.$page.props.jetstream.flash.token ? (openBlock(), createBlock("div", {
            key: 0,
            class: "mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500"
          }, toDisplayString(_ctx.$page.props.jetstream.flash.token), 1)) : createCommentVNode("", true)
        ];
      }
    }),
    footer: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_secondary_button, {
          onClick: ($event) => $data.displayingToken = false
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Close `);
            } else {
              return [
                createTextVNode(" Close ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_jet_secondary_button, {
            onClick: ($event) => $data.displayingToken = false
          }, {
            default: withCtx(() => [
              createTextVNode(" Close ")
            ]),
            _: 1
          }, 8, ["onClick"])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_jet_dialog_modal, {
    show: $data.managingPermissionsFor,
    onClose: ($event) => $data.managingPermissionsFor = null
  }, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` API Token Permissions `);
      } else {
        return [
          createTextVNode(" API Token Permissions ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="grid grid-cols-1 md:grid-cols-2 gap-4"${_scopeId}><!--[-->`);
        ssrRenderList($props.availablePermissions, (permission) => {
          _push2(`<div${_scopeId}><label class="flex items-center"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_jet_checkbox, {
            checked: $data.updateApiTokenForm.permissions,
            "onUpdate:checked": ($event) => $data.updateApiTokenForm.permissions = $event,
            value: permission
          }, null, _parent2, _scopeId));
          _push2(`<span class="ml-2 text-sm text-gray-600"${_scopeId}>${ssrInterpolate(permission)}</span></label></div>`);
        });
        _push2(`<!--]--></div>`);
      } else {
        return [
          createVNode("div", { class: "grid grid-cols-1 md:grid-cols-2 gap-4" }, [
            (openBlock(true), createBlock(Fragment, null, renderList($props.availablePermissions, (permission) => {
              return openBlock(), createBlock("div", { key: permission }, [
                createVNode("label", { class: "flex items-center" }, [
                  createVNode(_component_jet_checkbox, {
                    checked: $data.updateApiTokenForm.permissions,
                    "onUpdate:checked": ($event) => $data.updateApiTokenForm.permissions = $event,
                    value: permission
                  }, null, 8, ["checked", "onUpdate:checked", "value"]),
                  createVNode("span", { class: "ml-2 text-sm text-gray-600" }, toDisplayString(permission), 1)
                ])
              ]);
            }), 128))
          ])
        ];
      }
    }),
    footer: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_secondary_button, {
          onClick: ($event) => $data.managingPermissionsFor = null
        }, {
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
          class: ["ml-2", { "opacity-25": $data.updateApiTokenForm.processing }],
          disabled: $data.updateApiTokenForm.processing,
          onClick: $options.updateApiToken
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
          createVNode(_component_jet_secondary_button, {
            onClick: ($event) => $data.managingPermissionsFor = null
          }, {
            default: withCtx(() => [
              createTextVNode(" Cancel ")
            ]),
            _: 1
          }, 8, ["onClick"]),
          createVNode(_component_jet_button, {
            class: ["ml-2", { "opacity-25": $data.updateApiTokenForm.processing }],
            disabled: $data.updateApiTokenForm.processing,
            onClick: $options.updateApiToken
          }, {
            default: withCtx(() => [
              createTextVNode(" Save ")
            ]),
            _: 1
          }, 8, ["class", "disabled", "onClick"])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_jet_confirmation_modal, {
    show: $data.apiTokenBeingDeleted,
    onClose: ($event) => $data.apiTokenBeingDeleted = null
  }, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Delete API Token `);
      } else {
        return [
          createTextVNode(" Delete API Token ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Are you sure you would like to delete this API token? `);
      } else {
        return [
          createTextVNode(" Are you sure you would like to delete this API token? ")
        ];
      }
    }),
    footer: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_secondary_button, {
          onClick: ($event) => $data.apiTokenBeingDeleted = null
        }, {
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
        _push2(ssrRenderComponent(_component_jet_danger_button, {
          class: ["ml-2", { "opacity-25": $data.deleteApiTokenForm.processing }],
          disabled: $data.deleteApiTokenForm.processing,
          onClick: $options.deleteApiToken
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Delete `);
            } else {
              return [
                createTextVNode(" Delete ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_jet_secondary_button, {
            onClick: ($event) => $data.apiTokenBeingDeleted = null
          }, {
            default: withCtx(() => [
              createTextVNode(" Cancel ")
            ]),
            _: 1
          }, 8, ["onClick"]),
          createVNode(_component_jet_danger_button, {
            class: ["ml-2", { "opacity-25": $data.deleteApiTokenForm.processing }],
            disabled: $data.deleteApiTokenForm.processing,
            onClick: $options.deleteApiToken
          }, {
            default: withCtx(() => [
              createTextVNode(" Delete ")
            ]),
            _: 1
          }, 8, ["class", "disabled", "onClick"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/API/Partials/ApiTokenManager.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const ApiTokenManager = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  ApiTokenManager as default
};
