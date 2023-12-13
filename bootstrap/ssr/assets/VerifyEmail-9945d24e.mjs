import { J as JetAuthenticationCard } from "./AuthenticationCard-dde9a31c.mjs";
import { J as JetAuthenticationCardLogo } from "./AuthenticationCardLogo-26af12ea.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { Head, Link } from "@inertiajs/vue3";
import { resolveComponent, withCtx, createVNode, createTextVNode, openBlock, createBlock, createCommentVNode, withModifiers, useSSRContext } from "vue";
import { ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./ApplicationLogo-88e5413e.mjs";
const _sfc_main = {
  components: {
    Head,
    JetAuthenticationCard,
    JetAuthenticationCardLogo,
    JetButton,
    Link
  },
  props: {
    status: String
  },
  data() {
    return {
      form: this.$inertia.form({})
    };
  },
  computed: {
    verificationLinkSent() {
      return this.status === "verification-link-sent";
    }
  },
  methods: {
    submit() {
      this.form.post(this.route("verification.send"));
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_jet_authentication_card = resolveComponent("jet-authentication-card");
  const _component_jet_authentication_card_logo = resolveComponent("jet-authentication-card-logo");
  const _component_jet_button = resolveComponent("jet-button");
  const _component_Link = resolveComponent("Link");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_Head, { title: "Email Verification" }, null, _parent));
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
        _push2(`<div class="mb-4 text-sm text-gray-600"${_scopeId}> Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn&#39;t receive the email, we will gladly send you another. </div>`);
        if ($options.verificationLinkSent) {
          _push2(`<div class="mb-4 font-medium text-sm text-green-600"${_scopeId}> A new verification link has been sent to the email address you provided during registration. </div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<form${_scopeId}><div class="mt-4 flex items-center justify-between"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_button, {
          class: { "opacity-25": $data.form.processing },
          disabled: $data.form.processing
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Resend Verification Email `);
            } else {
              return [
                createTextVNode(" Resend Verification Email ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("logout"),
          method: "post",
          as: "button",
          class: "underline text-sm text-gray-600 hover:text-gray-900"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`Log Out`);
            } else {
              return [
                createTextVNode("Log Out")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div></form>`);
      } else {
        return [
          createVNode("div", { class: "mb-4 text-sm text-gray-600" }, " Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another. "),
          $options.verificationLinkSent ? (openBlock(), createBlock("div", {
            key: 0,
            class: "mb-4 font-medium text-sm text-green-600"
          }, " A new verification link has been sent to the email address you provided during registration. ")) : createCommentVNode("", true),
          createVNode("form", {
            onSubmit: withModifiers($options.submit, ["prevent"])
          }, [
            createVNode("div", { class: "mt-4 flex items-center justify-between" }, [
              createVNode(_component_jet_button, {
                class: { "opacity-25": $data.form.processing },
                disabled: $data.form.processing
              }, {
                default: withCtx(() => [
                  createTextVNode(" Resend Verification Email ")
                ]),
                _: 1
              }, 8, ["class", "disabled"]),
              createVNode(_component_Link, {
                href: _ctx.route("logout"),
                method: "post",
                as: "button",
                class: "underline text-sm text-gray-600 hover:text-gray-900"
              }, {
                default: withCtx(() => [
                  createTextVNode("Log Out")
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
  _push(`<!--]-->`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Auth/VerifyEmail.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const VerifyEmail = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  VerifyEmail as default
};
