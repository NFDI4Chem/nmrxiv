import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import { B as BreadCrumbs } from "./BreadCrumbs-a9158bdf.mjs";
import UserProfile from "./UserProfile-92fe5504.mjs";
import UserPassword from "./UserPassword-13bf1654.mjs";
import { resolveComponent, mergeProps, withCtx, createVNode, toDisplayString, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
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
import "./FormSection-d2d52d64.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./Input-f9a8cf77.mjs";
import "./Label-c5144ba4.mjs";
import "./ActionMessage-3207b224.mjs";
import "./SelectOrcidId-6ef7a184.mjs";
import "./LoadingButton-8ae902b0.mjs";
const _sfc_main = {
  metaInfo() {
    return {
      title: `${this.form.first_name} ${this.form.last_name}`
    };
  },
  components: {
    UserProfile,
    UserPassword,
    AppLayout,
    BreadCrumbs
  },
  props: {
    edituser: Object
  },
  remember: "form",
  data() {
    return {
      pages: [
        { name: "Console", route: "console", current: false },
        { name: "Users", route: "console.users", current: false },
        {
          name: this.edituser.first_name + " " + this.edituser.last_name,
          route: "console.users.update",
          current: true,
          id: this.edituser.id
        }
      ]
    };
  },
  methods: {
    update() {
      this.form.post(
        this.route("console.users.update", this.edituser.id),
        {
          onSuccess: () => this.form.reset("password", "photo")
        }
      );
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_bread_crumbs = resolveComponent("bread-crumbs");
  const _component_user_profile = resolveComponent("user-profile");
  const _component_user_password = resolveComponent("user-password");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Users & Permissions" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><div${_scopeId}>`);
        _push2(ssrRenderComponent(_component_bread_crumbs, { pages: $data.pages }, null, _parent2, _scopeId));
        _push2(`<div class="mt-2 md:flex md:items-center md:justify-between"${_scopeId}><div class="flex-1 min-w-0"${_scopeId}><div class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3"${_scopeId}>${ssrInterpolate($props.edituser.first_name)} ${ssrInterpolate($props.edituser.last_name)}</div></div></div></div></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-12" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between py-6" }, [
                createVNode("div", null, [
                  createVNode(_component_bread_crumbs, { pages: $data.pages }, null, 8, ["pages"]),
                  createVNode("div", { class: "mt-2 md:flex md:items-center md:justify-between" }, [
                    createVNode("div", { class: "flex-1 min-w-0" }, [
                      createVNode("div", { class: "flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3" }, toDisplayString($props.edituser.first_name) + " " + toDisplayString($props.edituser.last_name), 1)
                    ])
                  ])
                ])
              ])
            ])
          ])
        ];
      }
    }),
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8"${_scopeId}><div${_scopeId}>`);
        _push2(ssrRenderComponent(_component_user_profile, { user: $props.edituser }, null, _parent2, _scopeId));
        _push2(`<div class="hidden sm:block"${_scopeId}><div class="py-8"${_scopeId}><div class="border-t border-gray-200"${_scopeId}></div></div></div>`);
        _push2(ssrRenderComponent(_component_user_password, { user: $props.edituser }, null, _parent2, _scopeId));
        _push2(`</div></div>`);
      } else {
        return [
          createVNode("div", { class: "max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" }, [
            createVNode("div", null, [
              createVNode(_component_user_profile, { user: $props.edituser }, null, 8, ["user"]),
              createVNode("div", { class: "hidden sm:block" }, [
                createVNode("div", { class: "py-8" }, [
                  createVNode("div", { class: "border-t border-gray-200" })
                ])
              ]),
              createVNode(_component_user_password, { user: $props.edituser }, null, 8, ["user"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Console/Users/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Edit = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Edit as default
};
