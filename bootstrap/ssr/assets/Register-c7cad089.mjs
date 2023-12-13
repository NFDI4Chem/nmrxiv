import { J as JetAuthenticationCard } from "./AuthenticationCard-dde9a31c.mjs";
import { J as JetAuthenticationCardLogo } from "./AuthenticationCardLogo-26af12ea.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { J as JetInput } from "./Input-f9a8cf77.mjs";
import { J as JetCheckbox } from "./Checkbox-ed79f759.mjs";
import { J as JetLabel } from "./Label-c5144ba4.mjs";
import { J as JetValidationErrors } from "./ValidationErrors-17412073.mjs";
import { Head, Link } from "@inertiajs/vue3";
import { A as AnnouncementBanner } from "./AnnouncementBanner-3ebe3621.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { L as LoadingButton } from "./LoadingButton-8ae902b0.mjs";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { S as SelectOrcidId } from "./SelectOrcidId-6ef7a184.mjs";
import { ref, resolveComponent, withCtx, createVNode, createTextVNode, withModifiers, openBlock, createBlock, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderStyle } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@heroicons/vue/24/outline";
import "popper.js";
import "./Modal-b2fb4cf2.mjs";
const _sfc_main = {
  components: {
    Head,
    JetAuthenticationCard,
    JetAuthenticationCardLogo,
    JetButton,
    JetInput,
    JetCheckbox,
    JetLabel,
    JetValidationErrors,
    Link,
    AnnouncementBanner,
    JetSecondaryButton,
    JetDialogModal,
    LoadingButton,
    JetInputError,
    SelectOrcidId,
    ref
  },
  setup() {
    const selectOrcidIdElement = ref(null);
    return {
      selectOrcidIdElement
    };
  },
  data() {
    return {
      form: this.$inertia.form({
        first_name: "",
        last_name: "",
        email: "",
        username: "",
        orcid_id: "",
        affiliation: "",
        password: "",
        password_confirmation: "",
        terms: false
      }),
      orcidIdSearchResults: [],
      showOrcidIdDialog: false,
      loading: false,
      error: {}
    };
  },
  methods: {
    findOrcidID() {
      this.error.orcid = "";
      if (this.form.first_name && this.form.last_name) {
        this.selectOrcidIdElement.findOrcidID(
          this.form.first_name,
          this.form.last_name
        );
      } else {
        this.error.orcid = "Please enter first name and last name";
      }
    },
    submit() {
      this.form.post(this.route("register"), {
        onFinish: () => this.form.reset("password", "password_confirmation")
      });
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_announcement_banner = resolveComponent("announcement-banner");
  const _component_jet_authentication_card = resolveComponent("jet-authentication-card");
  const _component_jet_authentication_card_logo = resolveComponent("jet-authentication-card-logo");
  const _component_jet_validation_errors = resolveComponent("jet-validation-errors");
  const _component_jet_label = resolveComponent("jet-label");
  const _component_jet_input = resolveComponent("jet-input");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_jet_checkbox = resolveComponent("jet-checkbox");
  const _component_Link = resolveComponent("Link");
  const _component_jet_button = resolveComponent("jet-button");
  const _component_select_orcid_id = resolveComponent("select-orcid-id");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_Head, { title: "Register" }, null, _parent));
  _push(ssrRenderComponent(_component_announcement_banner, null, null, _parent));
  _push(ssrRenderComponent(_component_jet_authentication_card, { class: "index_beams" }, {
    logo: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_authentication_card_logo, null, null, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_jet_authentication_card_logo)
        ];
      }
    }),
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_validation_errors, { class: "mb-4" }, null, _parent2, _scopeId));
        _push2(`<form${_scopeId}><div${_scopeId}>`);
        if (_ctx.$page.props.environment.toLowerCase() != "production") {
          _push2(`<div class="pb-4"${_scopeId}><div class="border border-teal-500 rounded px-4 py-3 mt-3 max-w-2xl text-sm text-gray-700 font-bold"${_scopeId}><p${_scopeId}><span style="${ssrRenderStyle({ "color": "red" })}"${_scopeId}>Warning:</span> This site is for demonstration purpose only. You can test most of the nmrXiv features but DO NOT use the current site for your work. All the data stored here can be reset anytime. For real data please visit <a href="https://nmrxiv.org" target="_blank" style="${ssrRenderStyle({ "color": "teal" })}"${_scopeId}>nmrxiv.org.</a></p></div></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div><div class="mt-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, {
          for: "first_name",
          value: "First Name"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input, {
          id: "first_name",
          modelValue: $data.form.first_name,
          "onUpdate:modelValue": ($event) => $data.form.first_name = $event,
          type: "text",
          class: "mt-1 block w-full",
          required: "",
          autofocus: "",
          autocomplete: "first_name"
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="mt-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, {
          for: "last_name",
          value: "Last Name"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input, {
          id: "last_name",
          modelValue: $data.form.last_name,
          "onUpdate:modelValue": ($event) => $data.form.last_name = $event,
          type: "text",
          class: "mt-1 block w-full",
          required: "",
          autofocus: "",
          autocomplete: "last_name"
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="mt-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, {
          for: "email",
          value: "Email"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input, {
          id: "email",
          modelValue: $data.form.email,
          "onUpdate:modelValue": ($event) => $data.form.email = $event,
          type: "email",
          class: "mt-1 block w-full",
          required: ""
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="mt-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, {
          for: "username",
          value: "Username"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input, {
          id: "username",
          modelValue: $data.form.username,
          "onUpdate:modelValue": ($event) => $data.form.username = $event,
          type: "text",
          class: "mt-1 block w-full",
          required: ""
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="mt-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, {
          for: "orcid",
          value: "ORCID iD"
        }, null, _parent2, _scopeId));
        _push2(`<div class="mt-1 flex rounded-md shadow-sm"${_scopeId}><div class="relative flex items-stretch flex-grow focus-within:z-10"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_input, {
          id: "orcid",
          modelValue: $data.form.orcid_id,
          "onUpdate:modelValue": ($event) => $data.form.orcid_id = $event,
          type: "text",
          class: "rounded-l-md focus:ring-indigo-200 focus:border-indigo-200 block w-full rounded-none sm:text-medium border-gray-300"
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="tooltip -ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500 cursor-pointer"${_scopeId}><img alt="ORCID logo" src="https://orcid.org/assets/vectors/orcid.logo.icon.svg" class="w-6"${_scopeId}><span class="bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextbottom"${_scopeId}>Click to find ORCID iD</span></div></div>`);
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.error.orcid,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="mt-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, {
          for: "password",
          value: "Password"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input, {
          id: "password",
          modelValue: $data.form.password,
          "onUpdate:modelValue": ($event) => $data.form.password = $event,
          type: "password",
          class: "mt-1 block w-full",
          required: "",
          autocomplete: "new-password"
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="mt-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, {
          for: "password_confirmation",
          value: "Confirm Password"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input, {
          id: "password_confirmation",
          modelValue: $data.form.password_confirmation,
          "onUpdate:modelValue": ($event) => $data.form.password_confirmation = $event,
          type: "password",
          class: "mt-1 block w-full",
          required: "",
          autocomplete: "new-password"
        }, null, _parent2, _scopeId));
        _push2(`</div>`);
        if (_ctx.$page.props.jetstream.hasTermsAndPrivacyPolicyFeature) {
          _push2(`<div class="mt-4"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_jet_label, { for: "terms" }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(`<div class="flex items-center"${_scopeId2}>`);
                _push3(ssrRenderComponent(_component_jet_checkbox, {
                  id: "terms",
                  checked: $data.form.terms,
                  "onUpdate:checked": ($event) => $data.form.terms = $event,
                  name: "terms"
                }, null, _parent3, _scopeId2));
                _push3(`<div class="ml-2"${_scopeId2}> I agree to the `);
                _push3(ssrRenderComponent(_component_Link, {
                  target: "_blank",
                  href: _ctx.route("terms.show"),
                  class: "underline text-sm text-gray-600 hover:text-gray-900"
                }, {
                  default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                    if (_push4) {
                      _push4(`Terms of Service`);
                    } else {
                      return [
                        createTextVNode("Terms of Service")
                      ];
                    }
                  }),
                  _: 1
                }, _parent3, _scopeId2));
                _push3(` and `);
                _push3(ssrRenderComponent(_component_Link, {
                  target: "_blank",
                  href: _ctx.route("policy.show"),
                  class: "underline text-sm text-gray-600 hover:text-gray-900"
                }, {
                  default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                    if (_push4) {
                      _push4(`Privacy Policy`);
                    } else {
                      return [
                        createTextVNode("Privacy Policy")
                      ];
                    }
                  }),
                  _: 1
                }, _parent3, _scopeId2));
                _push3(`</div></div>`);
              } else {
                return [
                  createVNode("div", { class: "flex items-center" }, [
                    createVNode(_component_jet_checkbox, {
                      id: "terms",
                      checked: $data.form.terms,
                      "onUpdate:checked": ($event) => $data.form.terms = $event,
                      name: "terms"
                    }, null, 8, ["checked", "onUpdate:checked"]),
                    createVNode("div", { class: "ml-2" }, [
                      createTextVNode(" I agree to the "),
                      createVNode(_component_Link, {
                        target: "_blank",
                        href: _ctx.route("terms.show"),
                        class: "underline text-sm text-gray-600 hover:text-gray-900"
                      }, {
                        default: withCtx(() => [
                          createTextVNode("Terms of Service")
                        ]),
                        _: 1
                      }, 8, ["href"]),
                      createTextVNode(" and "),
                      createVNode(_component_Link, {
                        target: "_blank",
                        href: _ctx.route("policy.show"),
                        class: "underline text-sm text-gray-600 hover:text-gray-900"
                      }, {
                        default: withCtx(() => [
                          createTextVNode("Privacy Policy")
                        ]),
                        _: 1
                      }, 8, ["href"])
                    ])
                  ])
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div class="flex items-center justify-end mt-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_button, {
          class: ["w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-black-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500", { "opacity-25": $data.form.processing }],
          disabled: $data.form.processing
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Register `);
            } else {
              return [
                createTextVNode(" Register ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div><div class="flex items-center justify-center mt-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("login"),
          class: "underline text-sm text-gray-600 hover:text-gray-900"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Already registered? Login here `);
            } else {
              return [
                createTextVNode(" Already registered? Login here ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div></form>`);
      } else {
        return [
          createVNode(_component_jet_validation_errors, { class: "mb-4" }),
          createVNode("form", {
            onSubmit: withModifiers($options.submit, ["prevent"])
          }, [
            createVNode("div", null, [
              _ctx.$page.props.environment.toLowerCase() != "production" ? (openBlock(), createBlock("div", {
                key: 0,
                class: "pb-4"
              }, [
                createVNode("div", { class: "border border-teal-500 rounded px-4 py-3 mt-3 max-w-2xl text-sm text-gray-700 font-bold" }, [
                  createVNode("p", null, [
                    createVNode("span", { style: { "color": "red" } }, "Warning:"),
                    createTextVNode(" This site is for demonstration purpose only. You can test most of the nmrXiv features but DO NOT use the current site for your work. All the data stored here can be reset anytime. For real data please visit "),
                    createVNode("a", {
                      href: "https://nmrxiv.org",
                      target: "_blank",
                      style: { "color": "teal" }
                    }, "nmrxiv.org.")
                  ])
                ])
              ])) : createCommentVNode("", true)
            ]),
            createVNode("div", { class: "mt-4" }, [
              createVNode(_component_jet_label, {
                for: "first_name",
                value: "First Name"
              }),
              createVNode(_component_jet_input, {
                id: "first_name",
                modelValue: $data.form.first_name,
                "onUpdate:modelValue": ($event) => $data.form.first_name = $event,
                type: "text",
                class: "mt-1 block w-full",
                required: "",
                autofocus: "",
                autocomplete: "first_name"
              }, null, 8, ["modelValue", "onUpdate:modelValue"])
            ]),
            createVNode("div", { class: "mt-4" }, [
              createVNode(_component_jet_label, {
                for: "last_name",
                value: "Last Name"
              }),
              createVNode(_component_jet_input, {
                id: "last_name",
                modelValue: $data.form.last_name,
                "onUpdate:modelValue": ($event) => $data.form.last_name = $event,
                type: "text",
                class: "mt-1 block w-full",
                required: "",
                autofocus: "",
                autocomplete: "last_name"
              }, null, 8, ["modelValue", "onUpdate:modelValue"])
            ]),
            createVNode("div", { class: "mt-4" }, [
              createVNode(_component_jet_label, {
                for: "email",
                value: "Email"
              }),
              createVNode(_component_jet_input, {
                id: "email",
                modelValue: $data.form.email,
                "onUpdate:modelValue": ($event) => $data.form.email = $event,
                type: "email",
                class: "mt-1 block w-full",
                required: ""
              }, null, 8, ["modelValue", "onUpdate:modelValue"])
            ]),
            createVNode("div", { class: "mt-4" }, [
              createVNode(_component_jet_label, {
                for: "username",
                value: "Username"
              }),
              createVNode(_component_jet_input, {
                id: "username",
                modelValue: $data.form.username,
                "onUpdate:modelValue": ($event) => $data.form.username = $event,
                type: "text",
                class: "mt-1 block w-full",
                required: ""
              }, null, 8, ["modelValue", "onUpdate:modelValue"])
            ]),
            createVNode("div", { class: "mt-4" }, [
              createVNode(_component_jet_label, {
                for: "orcid",
                value: "ORCID iD"
              }),
              createVNode("div", { class: "mt-1 flex rounded-md shadow-sm" }, [
                createVNode("div", { class: "relative flex items-stretch flex-grow focus-within:z-10" }, [
                  createVNode(_component_jet_input, {
                    id: "orcid",
                    modelValue: $data.form.orcid_id,
                    "onUpdate:modelValue": ($event) => $data.form.orcid_id = $event,
                    type: "text",
                    class: "rounded-l-md focus:ring-indigo-200 focus:border-indigo-200 block w-full rounded-none sm:text-medium border-gray-300"
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])
                ]),
                createVNode("div", {
                  class: "tooltip -ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500 cursor-pointer",
                  onClick: ($event) => $options.findOrcidID()
                }, [
                  createVNode("img", {
                    alt: "ORCID logo",
                    src: "https://orcid.org/assets/vectors/orcid.logo.icon.svg",
                    class: "w-6"
                  }),
                  createVNode("span", { class: "bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextbottom" }, "Click to find ORCID iD")
                ], 8, ["onClick"])
              ]),
              createVNode(_component_jet_input_error, {
                message: $data.error.orcid,
                class: "mt-2"
              }, null, 8, ["message"])
            ]),
            createVNode("div", { class: "mt-4" }, [
              createVNode(_component_jet_label, {
                for: "password",
                value: "Password"
              }),
              createVNode(_component_jet_input, {
                id: "password",
                modelValue: $data.form.password,
                "onUpdate:modelValue": ($event) => $data.form.password = $event,
                type: "password",
                class: "mt-1 block w-full",
                required: "",
                autocomplete: "new-password"
              }, null, 8, ["modelValue", "onUpdate:modelValue"])
            ]),
            createVNode("div", { class: "mt-4" }, [
              createVNode(_component_jet_label, {
                for: "password_confirmation",
                value: "Confirm Password"
              }),
              createVNode(_component_jet_input, {
                id: "password_confirmation",
                modelValue: $data.form.password_confirmation,
                "onUpdate:modelValue": ($event) => $data.form.password_confirmation = $event,
                type: "password",
                class: "mt-1 block w-full",
                required: "",
                autocomplete: "new-password"
              }, null, 8, ["modelValue", "onUpdate:modelValue"])
            ]),
            _ctx.$page.props.jetstream.hasTermsAndPrivacyPolicyFeature ? (openBlock(), createBlock("div", {
              key: 0,
              class: "mt-4"
            }, [
              createVNode(_component_jet_label, { for: "terms" }, {
                default: withCtx(() => [
                  createVNode("div", { class: "flex items-center" }, [
                    createVNode(_component_jet_checkbox, {
                      id: "terms",
                      checked: $data.form.terms,
                      "onUpdate:checked": ($event) => $data.form.terms = $event,
                      name: "terms"
                    }, null, 8, ["checked", "onUpdate:checked"]),
                    createVNode("div", { class: "ml-2" }, [
                      createTextVNode(" I agree to the "),
                      createVNode(_component_Link, {
                        target: "_blank",
                        href: _ctx.route("terms.show"),
                        class: "underline text-sm text-gray-600 hover:text-gray-900"
                      }, {
                        default: withCtx(() => [
                          createTextVNode("Terms of Service")
                        ]),
                        _: 1
                      }, 8, ["href"]),
                      createTextVNode(" and "),
                      createVNode(_component_Link, {
                        target: "_blank",
                        href: _ctx.route("policy.show"),
                        class: "underline text-sm text-gray-600 hover:text-gray-900"
                      }, {
                        default: withCtx(() => [
                          createTextVNode("Privacy Policy")
                        ]),
                        _: 1
                      }, 8, ["href"])
                    ])
                  ])
                ]),
                _: 1
              })
            ])) : createCommentVNode("", true),
            createVNode("div", { class: "flex items-center justify-end mt-4" }, [
              createVNode(_component_jet_button, {
                class: ["w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-black-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500", { "opacity-25": $data.form.processing }],
                disabled: $data.form.processing
              }, {
                default: withCtx(() => [
                  createTextVNode(" Register ")
                ]),
                _: 1
              }, 8, ["class", "disabled"])
            ]),
            createVNode("div", { class: "flex items-center justify-center mt-4" }, [
              createVNode(_component_Link, {
                href: _ctx.route("login"),
                class: "underline text-sm text-gray-600 hover:text-gray-900"
              }, {
                default: withCtx(() => [
                  createTextVNode(" Already registered? Login here ")
                ]),
                _: 1
              }, 8, ["href"])
            ])
          ], 40, ["onSubmit"])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_select_orcid_id, {
    ref: "selectOrcidIdElement",
    orcidId: $data.form.orcid_id,
    "onUpdate:orcidId": ($event) => $data.form.orcid_id = $event,
    affiliation: $data.form.affiliation,
    "onUpdate:affiliation": ($event) => $data.form.affiliation = $event
  }, null, _parent));
  _push(`<!--]-->`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Auth/Register.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Register = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Register as default
};
