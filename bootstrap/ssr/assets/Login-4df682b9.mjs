import { J as JetAuthenticationCard } from "./AuthenticationCard-dde9a31c.mjs";
import { J as JetAuthenticationCardLogo } from "./AuthenticationCardLogo-26af12ea.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { J as JetInput } from "./Input-f9a8cf77.mjs";
import { J as JetCheckbox } from "./Checkbox-ed79f759.mjs";
import { J as JetLabel } from "./Label-c5144ba4.mjs";
import { J as JetValidationErrors } from "./ValidationErrors-17412073.mjs";
import { mergeProps, useSSRContext, resolveComponent, withCtx, createVNode, createTextVNode, openBlock, createBlock, toDisplayString, createCommentVNode, withModifiers } from "vue";
import { ssrRenderAttrs, ssrRenderStyle, ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import { Head, Link } from "@inertiajs/vue3";
import { A as AnnouncementBanner } from "./AnnouncementBanner-3ebe3621.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@heroicons/vue/24/outline";
import "popper.js";
const _sfc_main$1 = {
  computed: {
    hasTwitterClientID() {
      return String(this.$page.props.twitter).toLowerCase() == "true";
    },
    hasGithubClientID() {
      return String(this.$page.props.github).toLowerCase() == "true";
    },
    hasOrcidClientID() {
      return String(this.$page.props.orcid).toLowerCase() == "true";
    },
    hasSingleSingOn() {
      if (this.hasTwitterClientID || this.hasGithubClientID || this.hasOrcidClientID) {
        return true;
      } else {
        return false;
      }
    }
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(mergeProps({
    style: $options.hasSingleSingOn ? null : { display: "none" },
    class: "mt-6"
  }, _attrs))}><div class="relative"><div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-300"></div></div><div class="relative flex justify-center text-sm"><span class="px-2 bg-white text-gray-500"> Or continue with </span></div></div><div class="flex mt-3">`);
  if ($options.hasOrcidClientID) {
    _push(`<div class="flex-1 w-64 px-1"><a href="/auth/login/orcid" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium hover:bg-gray-50"><span class="sr-only">Sign in with ORCID</span><img alt="ORCID logo" src="https://orcid.org/assets/vectors/orcid.logo.icon.svg" width="20" height="20"></a></div>`);
  } else {
    _push(`<!---->`);
  }
  if ($options.hasTwitterClientID) {
    _push(`<div class="flex-1 w-64 px-1"><a href="/auth/login/twitter" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-sky-500 hover:bg-gray-50"><span class="sr-only">Sign in with Twitter</span><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><svg style="${ssrRenderStyle({ "height": "20px" })}" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Twitter</title><path d="M23.643 4.937c-.835.37-1.732.62-2.675.733.962-.576 1.7-1.49 2.048-2.578-.9.534-1.897.922-2.958 1.13-.85-.904-2.06-1.47-3.4-1.47-2.572 0-4.658 2.086-4.658 4.66 0 .364.042.718.12 1.06-3.873-.195-7.304-2.05-9.602-4.868-.4.69-.63 1.49-.63 2.342 0 1.616.823 3.043 2.072 3.878-.764-.025-1.482-.234-2.11-.583v.06c0 2.257 1.605 4.14 3.737 4.568-.392.106-.803.162-1.227.162-.3 0-.593-.028-.877-.082.593 1.85 2.313 3.198 4.352 3.234-1.595 1.25-3.604 1.995-5.786 1.995-.376 0-.747-.022-1.112-.065 2.062 1.323 4.51 2.093 7.14 2.093 8.57 0 13.255-7.098 13.255-13.254 0-.2-.005-.402-.014-.602.91-.658 1.7-1.477 2.323-2.41z"></path></svg></svg></a></div>`);
  } else {
    _push(`<!---->`);
  }
  if ($options.hasGithubClientID) {
    _push(`<div class="flex-1 w-32 px-1"><a href="/auth/login/github" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium hover:bg-gray-50"><span class="sr-only">Sign in with GitHub</span><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z" clip-rule="evenodd"></path></svg></a></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div></div>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/App/SingleSignOn.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const SingleSignOn = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
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
    SingleSignOn,
    Link,
    AnnouncementBanner
  },
  props: {
    canResetPassword: Boolean,
    status: String
  },
  data() {
    return {
      form: this.$inertia.form({
        email: "",
        password: "",
        remember: false
      })
    };
  },
  methods: {
    submit() {
      this.form.transform((data) => ({
        ...data,
        remember: this.form.remember ? "on" : ""
      })).post(this.route("login"), {
        onFinish: () => this.form.reset("password")
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
  const _component_jet_checkbox = resolveComponent("jet-checkbox");
  const _component_Link = resolveComponent("Link");
  const _component_jet_button = resolveComponent("jet-button");
  const _component_single_sign_on = resolveComponent("single-sign-on");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_Head, { title: "Log in" }, null, _parent));
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
        if (_ctx.$page.props.flash.message) {
          _push2(`<div class="alert"${_scopeId}>${ssrInterpolate(_ctx.$page.props.flash.message)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        if ($props.status) {
          _push2(`<div class="mb-4 font-medium text-sm text-green-600"${_scopeId}>${ssrInterpolate($props.status)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<form${_scopeId}><div${_scopeId}>`);
        if (_ctx.$page.props.environment.toLowerCase() != "production") {
          _push2(`<div class="pb-4"${_scopeId}><div class="border border-teal-500 rounded px-4 py-3 mt-3 max-w-2xl text-sm text-gray-700 font-bold"${_scopeId}><p${_scopeId}> Warning: This site is for demonstration purpose only. You can test most of the nmrXiv features but DO NOT use the current site for your work. All the data stored here can be reset anytime. </p></div></div>`);
        } else {
          _push2(`<!---->`);
        }
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
          required: "",
          autofocus: ""
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
          autocomplete: "current-password"
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="flex items-center justify-between mt-4"${_scopeId}><div class="flex items-center"${_scopeId}><label class="flex items-center"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_checkbox, {
          checked: $data.form.remember,
          "onUpdate:checked": ($event) => $data.form.remember = $event,
          name: "remember"
        }, null, _parent2, _scopeId));
        _push2(`<span class="ml-2 text-sm text-gray-600"${_scopeId}>Remember me</span></label></div><div class="text-sm"${_scopeId}>`);
        if ($props.canResetPassword) {
          _push2(ssrRenderComponent(_component_Link, {
            href: _ctx.route("password.request"),
            class: "underline text-sm text-gray-600 hover:text-gray-900"
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Forgot your password? `);
              } else {
                return [
                  createTextVNode(" Forgot your password? ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="mt-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_button, {
          class: ["w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500", { "opacity-25": $data.form.processing }],
          disabled: $data.form.processing
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Log in `);
            } else {
              return [
                createTextVNode(" Log in ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div><div class="flex items-center justify-center mt-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("register"),
          class: "underline text-sm text-gray-600 hover:text-gray-900"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Not registered? Register here `);
            } else {
              return [
                createTextVNode(" Not registered? Register here ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div>`);
        _push2(ssrRenderComponent(_component_single_sign_on, null, null, _parent2, _scopeId));
        _push2(`</form>`);
      } else {
        return [
          createVNode(_component_jet_validation_errors, { class: "mb-4" }),
          _ctx.$page.props.flash.message ? (openBlock(), createBlock("div", {
            key: 0,
            class: "alert"
          }, toDisplayString(_ctx.$page.props.flash.message), 1)) : createCommentVNode("", true),
          $props.status ? (openBlock(), createBlock("div", {
            key: 1,
            class: "mb-4 font-medium text-sm text-green-600"
          }, toDisplayString($props.status), 1)) : createCommentVNode("", true),
          createVNode("form", {
            onSubmit: withModifiers($options.submit, ["prevent"])
          }, [
            createVNode("div", null, [
              _ctx.$page.props.environment.toLowerCase() != "production" ? (openBlock(), createBlock("div", {
                key: 0,
                class: "pb-4"
              }, [
                createVNode("div", { class: "border border-teal-500 rounded px-4 py-3 mt-3 max-w-2xl text-sm text-gray-700 font-bold" }, [
                  createVNode("p", null, " Warning: This site is for demonstration purpose only. You can test most of the nmrXiv features but DO NOT use the current site for your work. All the data stored here can be reset anytime. ")
                ])
              ])) : createCommentVNode("", true),
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
                required: "",
                autofocus: ""
              }, null, 8, ["modelValue", "onUpdate:modelValue"])
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
                autocomplete: "current-password"
              }, null, 8, ["modelValue", "onUpdate:modelValue"])
            ]),
            createVNode("div", { class: "flex items-center justify-between mt-4" }, [
              createVNode("div", { class: "flex items-center" }, [
                createVNode("label", { class: "flex items-center" }, [
                  createVNode(_component_jet_checkbox, {
                    checked: $data.form.remember,
                    "onUpdate:checked": ($event) => $data.form.remember = $event,
                    name: "remember"
                  }, null, 8, ["checked", "onUpdate:checked"]),
                  createVNode("span", { class: "ml-2 text-sm text-gray-600" }, "Remember me")
                ])
              ]),
              createVNode("div", { class: "text-sm" }, [
                $props.canResetPassword ? (openBlock(), createBlock(_component_Link, {
                  key: 0,
                  href: _ctx.route("password.request"),
                  class: "underline text-sm text-gray-600 hover:text-gray-900"
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Forgot your password? ")
                  ]),
                  _: 1
                }, 8, ["href"])) : createCommentVNode("", true)
              ])
            ]),
            createVNode("div", { class: "mt-4" }, [
              createVNode(_component_jet_button, {
                class: ["w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500", { "opacity-25": $data.form.processing }],
                disabled: $data.form.processing
              }, {
                default: withCtx(() => [
                  createTextVNode(" Log in ")
                ]),
                _: 1
              }, 8, ["class", "disabled"])
            ]),
            createVNode("div", { class: "flex items-center justify-center mt-4" }, [
              createVNode(_component_Link, {
                href: _ctx.route("register"),
                class: "underline text-sm text-gray-600 hover:text-gray-900"
              }, {
                default: withCtx(() => [
                  createTextVNode(" Not registered? Register here ")
                ]),
                _: 1
              }, 8, ["href"])
            ]),
            createVNode(_component_single_sign_on)
          ], 40, ["onSubmit"])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`<!--]-->`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Auth/Login.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Login = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Login as default
};
