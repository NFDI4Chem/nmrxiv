import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import DeleteUserForm from "./DeleteUserForm-043e97fd.mjs";
import { J as JetSectionBorder } from "./SectionBorder-7d1f222a.mjs";
import LogoutOtherBrowserSessionsForm from "./LogoutOtherBrowserSessionsForm-d79075c6.mjs";
import TwoFactorAuthenticationForm from "./TwoFactorAuthenticationForm-1f086230.mjs";
import UpdatePasswordForm from "./UpdatePasswordForm-8ce3ad6e.mjs";
import UpdateProfileInformationForm from "./UpdateProfileInformationForm-94aa0002.mjs";
import { resolveComponent, mergeProps, withCtx, createVNode, openBlock, createBlock, createCommentVNode, Fragment, useSSRContext } from "vue";
import { ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
import "@heroicons/vue/24/solid";
import "@inertiajs/vue3";
import "@vueuse/core";
import "@heroicons/vue/24/outline";
import "@headlessui/vue";
import "./AnnouncementBanner-3ebe3621.mjs";
import "popper.js";
import "./FlashMessages-f0051c19.mjs";
import "./DialogModal-f54eea4c.mjs";
import "./Modal-b2fb4cf2.mjs";
import "./SecondaryButton-3cee9e05.mjs";
import "./Button-8d6976fd.mjs";
import "@sipec/vue3-tags-input";
import "vue3-slider";
import "openchemlib/full.js";
import "./SelectRich-b58dcc3f.mjs";
import "./ToolTip-cf9882a3.mjs";
import "./InputError-6ae0b65c.mjs";
import "dropzone";
import "axios-retry";
import "./ConfirmationModal-d0b47856.mjs";
import "./DangerButton-9c23157c.mjs";
import "@heroicons/vue/20/solid";
import "./ActionSection-c1b53bcc.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./Input-f9a8cf77.mjs";
import "./ActionMessage-3207b224.mjs";
import "./FormSection-d2d52d64.mjs";
import "./Label-c5144ba4.mjs";
import "./SelectOrcidId-6ef7a184.mjs";
import "./LoadingButton-8ae902b0.mjs";
const _sfc_main = {
  components: {
    AppLayout,
    DeleteUserForm,
    JetSectionBorder,
    LogoutOtherBrowserSessionsForm,
    TwoFactorAuthenticationForm,
    UpdatePasswordForm,
    UpdateProfileInformationForm
  },
  props: ["sessions"]
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_update_profile_information_form = resolveComponent("update-profile-information-form");
  const _component_jet_section_border = resolveComponent("jet-section-border");
  const _component_update_password_form = resolveComponent("update-password-form");
  const _component_two_factor_authentication_form = resolveComponent("two-factor-authentication-form");
  const _component_logout_other_browser_sessions_form = resolveComponent("logout-other-browser-sessions-form");
  const _component_delete_user_form = resolveComponent("delete-user-form");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Profile" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><h2 class="font-semibold text-xl text-gray-800 leading-tight"${_scopeId}> Account </h2></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-12" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between py-6" }, [
                createVNode("h2", { class: "font-semibold text-xl text-gray-800 leading-tight" }, " Account ")
              ])
            ])
          ])
        ];
      }
    }),
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div${_scopeId}><div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8"${_scopeId}>`);
        if (_ctx.$page.props.jetstream.canUpdateProfileInformation) {
          _push2(`<div${_scopeId}>`);
          _push2(ssrRenderComponent(_component_update_profile_information_form, {
            user: _ctx.$page.props.auth.user
          }, null, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_section_border, null, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        if (_ctx.$page.props.jetstream.canUpdatePassword) {
          _push2(`<div${_scopeId}>`);
          _push2(ssrRenderComponent(_component_update_password_form, { class: "mt-10 sm:mt-0" }, null, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_section_border, null, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        if (_ctx.$page.props.jetstream.canManageTwoFactorAuthentication) {
          _push2(`<div${_scopeId}>`);
          _push2(ssrRenderComponent(_component_two_factor_authentication_form, { class: "mt-10 sm:mt-0" }, null, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_section_border, null, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(ssrRenderComponent(_component_logout_other_browser_sessions_form, {
          sessions: $props.sessions,
          class: "mt-10 sm:mt-0"
        }, null, _parent2, _scopeId));
        if (_ctx.$page.props.jetstream.hasAccountDeletionFeatures) {
          _push2(`<!--[-->`);
          _push2(ssrRenderComponent(_component_jet_section_border, null, null, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_delete_user_form, { class: "mt-10 sm:mt-0" }, null, _parent2, _scopeId));
          _push2(`<!--]-->`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div>`);
      } else {
        return [
          createVNode("div", null, [
            createVNode("div", { class: "max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" }, [
              _ctx.$page.props.jetstream.canUpdateProfileInformation ? (openBlock(), createBlock("div", { key: 0 }, [
                createVNode(_component_update_profile_information_form, {
                  user: _ctx.$page.props.auth.user
                }, null, 8, ["user"]),
                createVNode(_component_jet_section_border)
              ])) : createCommentVNode("", true),
              _ctx.$page.props.jetstream.canUpdatePassword ? (openBlock(), createBlock("div", { key: 1 }, [
                createVNode(_component_update_password_form, { class: "mt-10 sm:mt-0" }),
                createVNode(_component_jet_section_border)
              ])) : createCommentVNode("", true),
              _ctx.$page.props.jetstream.canManageTwoFactorAuthentication ? (openBlock(), createBlock("div", { key: 2 }, [
                createVNode(_component_two_factor_authentication_form, { class: "mt-10 sm:mt-0" }),
                createVNode(_component_jet_section_border)
              ])) : createCommentVNode("", true),
              createVNode(_component_logout_other_browser_sessions_form, {
                sessions: $props.sessions,
                class: "mt-10 sm:mt-0"
              }, null, 8, ["sessions"]),
              _ctx.$page.props.jetstream.hasAccountDeletionFeatures ? (openBlock(), createBlock(Fragment, { key: 3 }, [
                createVNode(_component_jet_section_border),
                createVNode(_component_delete_user_form, { class: "mt-10 sm:mt-0" })
              ], 64)) : createCommentVNode("", true)
            ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Profile/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Show = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Show as default
};
