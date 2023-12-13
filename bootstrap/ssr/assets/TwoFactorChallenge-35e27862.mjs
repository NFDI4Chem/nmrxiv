import { Head } from "@inertiajs/vue3";
import { J as JetAuthenticationCard } from "./AuthenticationCard-dde9a31c.mjs";
import { J as JetAuthenticationCardLogo } from "./AuthenticationCardLogo-26af12ea.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { J as JetInput } from "./Input-f9a8cf77.mjs";
import { J as JetLabel } from "./Label-c5144ba4.mjs";
import { J as JetValidationErrors } from "./ValidationErrors-17412073.mjs";
import { resolveComponent, withCtx, createVNode, createTextVNode, openBlock, createBlock, Fragment, withModifiers, useSSRContext } from "vue";
import { ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./ApplicationLogo-88e5413e.mjs";
const _sfc_main = {
  components: {
    Head,
    JetAuthenticationCard,
    JetAuthenticationCardLogo,
    JetButton,
    JetInput,
    JetLabel,
    JetValidationErrors
  },
  data() {
    return {
      recovery: false,
      form: this.$inertia.form({
        code: "",
        recovery_code: ""
      })
    };
  },
  methods: {
    toggleRecovery() {
      this.recovery ^= true;
      this.$nextTick(() => {
        if (this.recovery) {
          this.$refs.recovery_code.focus();
          this.form.code = "";
        } else {
          this.$refs.code.focus();
          this.form.recovery_code = "";
        }
      });
    },
    submit() {
      this.form.post(this.route("two-factor.login"));
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_jet_authentication_card = resolveComponent("jet-authentication-card");
  const _component_jet_authentication_card_logo = resolveComponent("jet-authentication-card-logo");
  const _component_jet_validation_errors = resolveComponent("jet-validation-errors");
  const _component_jet_label = resolveComponent("jet-label");
  const _component_jet_input = resolveComponent("jet-input");
  const _component_jet_button = resolveComponent("jet-button");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_Head, { title: "Two-factor Confirmation" }, null, _parent));
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
        _push2(`<div class="mb-4 text-sm text-gray-600"${_scopeId}>`);
        if (!$data.recovery) {
          _push2(`<!--[--> Please confirm access to your account by entering the authentication code provided by your authenticator application. <!--]-->`);
        } else {
          _push2(`<!--[--> Please confirm access to your account by entering one of your emergency recovery codes. <!--]-->`);
        }
        _push2(`</div>`);
        _push2(ssrRenderComponent(_component_jet_validation_errors, { class: "mb-4" }, null, _parent2, _scopeId));
        _push2(`<form${_scopeId}>`);
        if (!$data.recovery) {
          _push2(`<div${_scopeId}>`);
          _push2(ssrRenderComponent(_component_jet_label, {
            for: "code",
            value: "Code"
          }, null, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_input, {
            id: "code",
            ref: "code",
            modelValue: $data.form.code,
            "onUpdate:modelValue": ($event) => $data.form.code = $event,
            type: "text",
            inputmode: "numeric",
            class: "mt-1 block w-full",
            autofocus: "",
            autocomplete: "one-time-code"
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<div${_scopeId}>`);
          _push2(ssrRenderComponent(_component_jet_label, {
            for: "recovery_code",
            value: "Recovery Code"
          }, null, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_input, {
            id: "recovery_code",
            ref: "recovery_code",
            modelValue: $data.form.recovery_code,
            "onUpdate:modelValue": ($event) => $data.form.recovery_code = $event,
            type: "text",
            class: "mt-1 block w-full",
            autocomplete: "one-time-code"
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
        }
        _push2(`<div class="flex items-center justify-end mt-4"${_scopeId}><button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"${_scopeId}>`);
        if (!$data.recovery) {
          _push2(`<!--[--> Use a recovery code <!--]-->`);
        } else {
          _push2(`<!--[--> Use an authentication code <!--]-->`);
        }
        _push2(`</button>`);
        _push2(ssrRenderComponent(_component_jet_button, {
          class: ["ml-4", { "opacity-25": $data.form.processing }],
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
        _push2(`</div></form>`);
      } else {
        return [
          createVNode("div", { class: "mb-4 text-sm text-gray-600" }, [
            !$data.recovery ? (openBlock(), createBlock(Fragment, { key: 0 }, [
              createTextVNode(" Please confirm access to your account by entering the authentication code provided by your authenticator application. ")
            ], 64)) : (openBlock(), createBlock(Fragment, { key: 1 }, [
              createTextVNode(" Please confirm access to your account by entering one of your emergency recovery codes. ")
            ], 64))
          ]),
          createVNode(_component_jet_validation_errors, { class: "mb-4" }),
          createVNode("form", {
            onSubmit: withModifiers($options.submit, ["prevent"])
          }, [
            !$data.recovery ? (openBlock(), createBlock("div", { key: 0 }, [
              createVNode(_component_jet_label, {
                for: "code",
                value: "Code"
              }),
              createVNode(_component_jet_input, {
                id: "code",
                ref: "code",
                modelValue: $data.form.code,
                "onUpdate:modelValue": ($event) => $data.form.code = $event,
                type: "text",
                inputmode: "numeric",
                class: "mt-1 block w-full",
                autofocus: "",
                autocomplete: "one-time-code"
              }, null, 8, ["modelValue", "onUpdate:modelValue"])
            ])) : (openBlock(), createBlock("div", { key: 1 }, [
              createVNode(_component_jet_label, {
                for: "recovery_code",
                value: "Recovery Code"
              }),
              createVNode(_component_jet_input, {
                id: "recovery_code",
                ref: "recovery_code",
                modelValue: $data.form.recovery_code,
                "onUpdate:modelValue": ($event) => $data.form.recovery_code = $event,
                type: "text",
                class: "mt-1 block w-full",
                autocomplete: "one-time-code"
              }, null, 8, ["modelValue", "onUpdate:modelValue"])
            ])),
            createVNode("div", { class: "flex items-center justify-end mt-4" }, [
              createVNode("button", {
                type: "button",
                class: "text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer",
                onClick: withModifiers($options.toggleRecovery, ["prevent"])
              }, [
                !$data.recovery ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                  createTextVNode(" Use a recovery code ")
                ], 64)) : (openBlock(), createBlock(Fragment, { key: 1 }, [
                  createTextVNode(" Use an authentication code ")
                ], 64))
              ], 8, ["onClick"]),
              createVNode(_component_jet_button, {
                class: ["ml-4", { "opacity-25": $data.form.processing }],
                disabled: $data.form.processing
              }, {
                default: withCtx(() => [
                  createTextVNode(" Log in ")
                ]),
                _: 1
              }, 8, ["class", "disabled"])
            ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Auth/TwoFactorChallenge.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const TwoFactorChallenge = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  TwoFactorChallenge as default
};
