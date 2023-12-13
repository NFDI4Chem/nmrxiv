import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import DeleteTeamForm from "./DeleteTeamForm-35c36e28.mjs";
import { J as JetSectionBorder } from "./SectionBorder-7d1f222a.mjs";
import TeamMemberManager from "./TeamMemberManager-2afa9a12.mjs";
import UpdateTeamNameForm from "./UpdateTeamNameForm-4a1b69f7.mjs";
import { resolveComponent, mergeProps, withCtx, createVNode, openBlock, createBlock, Fragment, createCommentVNode, useSSRContext } from "vue";
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
const _sfc_main = {
  components: {
    AppLayout,
    DeleteTeamForm,
    JetSectionBorder,
    TeamMemberManager,
    UpdateTeamNameForm
  },
  props: ["team", "availableRoles", "permissions"]
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_update_team_name_form = resolveComponent("update-team-name-form");
  const _component_team_member_manager = resolveComponent("team-member-manager");
  const _component_jet_section_border = resolveComponent("jet-section-border");
  const _component_delete_team_form = resolveComponent("delete-team-form");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Team Settings" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><h2 class="font-semibold text-xl text-gray-800 leading-tight"${_scopeId}> Team Settings </h2></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-12" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between py-6" }, [
                createVNode("h2", { class: "font-semibold text-xl text-gray-800 leading-tight" }, " Team Settings ")
              ])
            ])
          ])
        ];
      }
    }),
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div${_scopeId}><div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_update_team_name_form, {
          team: $props.team,
          permissions: $props.permissions
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_team_member_manager, {
          class: "mt-10 sm:mt-0",
          team: $props.team,
          "available-roles": $props.availableRoles,
          "user-permissions": $props.permissions
        }, null, _parent2, _scopeId));
        if ($props.permissions.canDeleteTeam && !$props.team.personal_team) {
          _push2(`<!--[-->`);
          _push2(ssrRenderComponent(_component_jet_section_border, null, null, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_delete_team_form, {
            class: "mt-10 sm:mt-0",
            team: $props.team
          }, null, _parent2, _scopeId));
          _push2(`<!--]-->`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div>`);
      } else {
        return [
          createVNode("div", null, [
            createVNode("div", { class: "max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" }, [
              createVNode(_component_update_team_name_form, {
                team: $props.team,
                permissions: $props.permissions
              }, null, 8, ["team", "permissions"]),
              createVNode(_component_team_member_manager, {
                class: "mt-10 sm:mt-0",
                team: $props.team,
                "available-roles": $props.availableRoles,
                "user-permissions": $props.permissions
              }, null, 8, ["team", "available-roles", "user-permissions"]),
              $props.permissions.canDeleteTeam && !$props.team.personal_team ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                createVNode(_component_jet_section_border),
                createVNode(_component_delete_team_form, {
                  class: "mt-10 sm:mt-0",
                  team: $props.team
                }, null, 8, ["team"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Teams/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Show = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Show as default
};
