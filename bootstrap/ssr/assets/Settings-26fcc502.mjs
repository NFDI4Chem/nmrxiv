import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import { Link } from "@inertiajs/vue3";
import ProjectDelete from "./Delete-ae109cde.mjs";
import ProjectRestore from "./Restore-b3a07c73.mjs";
import ProjectArchive from "./Archive-62426840.mjs";
import ProjectUnarchive from "./Unarchive-c4dd8c4d.mjs";
import { InformationCircleIcon } from "@heroicons/vue/24/solid";
import { resolveComponent, mergeProps, withCtx, createTextVNode, toDisplayString, createVNode, openBlock, createBlock, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
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
import "./LoadingButton-8ae902b0.mjs";
import "./SuccessButton-6c2ecb3a.mjs";
const _sfc_main = {
  components: {
    Link,
    AppLayout,
    ProjectDelete,
    ProjectRestore,
    ProjectArchive,
    ProjectUnarchive,
    InformationCircleIcon
  },
  props: ["project", "studies", "schema"]
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_Link = resolveComponent("Link");
  const _component_InformationCircleIcon = resolveComponent("InformationCircleIcon");
  const _component_project_delete = resolveComponent("project-delete");
  const _component_project_restore = resolveComponent("project-restore");
  const _component_project_archive = resolveComponent("project-archive");
  const _component_project_unarchive = resolveComponent("project-unarchive");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({
    title: $props.project.name
  }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><div${_scopeId}><div class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("dashboard.projects", $props.project.id)
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`${ssrInterpolate($props.project.name)}`);
            } else {
              return [
                createTextVNode(toDisplayString($props.project.name), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(` / Settings </div></div></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-12" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between py-6" }, [
                createVNode("div", null, [
                  createVNode("div", { class: "flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest" }, [
                    createVNode(_component_Link, {
                      href: _ctx.route("dashboard.projects", $props.project.id)
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString($props.project.name), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createTextVNode(" / Settings ")
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
        _push2(`<div class="py-12 px-10"${_scopeId}><div${_scopeId}><div class="md:grid mb-10 md:grid-cols-3 md:gap-6"${_scopeId}><div class="md:col-span-1 flex justify-between"${_scopeId}><div class="px-4 sm:px-0"${_scopeId}><h3 class="text-lg font-medium text-gray-900"${_scopeId}> Schema </h3><p class="mt-1 text-sm text-gray-600"${_scopeId}> Project&#39;s repository schema version and validations details </p></div><div class="px-4 sm:px-0"${_scopeId}></div></div><div class="mt-5 md:mt-0 md:col-span-2"${_scopeId}><div class="px-4 py-5 sm:p-6 bg-white shadow sm:rounded-lg"${_scopeId}><div class="max-w-xl text-sm text-gray-600"${_scopeId}> Some of the nmrXiv functionality might not be available if your project is not migrated to our latest schema version. Where ever possible, we automatically migrate your data to be compatible with our latest schema version. Automatic migration might not be possible in some cases due to major nmrXiv schema version updates or missing metadata. Please get in touch with us to find out if it&#39;s possible to update your project to the latest schema version. You can find all information related to the nmrXiv schema on our documentation site. </div><div class="mt-5"${_scopeId}><span type="button" class="inline-flex items-center justify-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:outline-none focus:border-yellow-700 focus:ring focus:ring-yellow-200 active:bg-yellow-600 disabled:opacity-25 transition"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_InformationCircleIcon, { class: "flex-shrink-0 mr-1.5 h-5 w-5 mr-4 inline" }, null, _parent2, _scopeId));
        _push2(` ${ssrInterpolate($props.project.schema_version)}</span>`);
        if ($props.project.schema_version == $props.schema) {
          _push2(`<span class="ml-4"${_scopeId}> Migrate now? </span>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div></div></div></div>`);
        if (!$props.project.is_public && !$props.project.is_deleted) {
          _push2(`<div${_scopeId}>`);
          _push2(ssrRenderComponent(_component_project_delete, { project: $props.project }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        if ($props.project.is_deleted) {
          _push2(`<div${_scopeId}>`);
          _push2(ssrRenderComponent(_component_project_restore, { project: $props.project }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        if ($props.project.is_public && !$props.project.is_archived) {
          _push2(`<div${_scopeId}>`);
          _push2(ssrRenderComponent(_component_project_archive, { project: $props.project }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        if ($props.project.is_public && $props.project.is_archived) {
          _push2(`<div${_scopeId}>`);
          _push2(ssrRenderComponent(_component_project_unarchive, { project: $props.project }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "py-12 px-10" }, [
            createVNode("div", null, [
              createVNode("div", { class: "md:grid mb-10 md:grid-cols-3 md:gap-6" }, [
                createVNode("div", { class: "md:col-span-1 flex justify-between" }, [
                  createVNode("div", { class: "px-4 sm:px-0" }, [
                    createVNode("h3", { class: "text-lg font-medium text-gray-900" }, " Schema "),
                    createVNode("p", { class: "mt-1 text-sm text-gray-600" }, " Project's repository schema version and validations details ")
                  ]),
                  createVNode("div", { class: "px-4 sm:px-0" })
                ]),
                createVNode("div", { class: "mt-5 md:mt-0 md:col-span-2" }, [
                  createVNode("div", { class: "px-4 py-5 sm:p-6 bg-white shadow sm:rounded-lg" }, [
                    createVNode("div", { class: "max-w-xl text-sm text-gray-600" }, " Some of the nmrXiv functionality might not be available if your project is not migrated to our latest schema version. Where ever possible, we automatically migrate your data to be compatible with our latest schema version. Automatic migration might not be possible in some cases due to major nmrXiv schema version updates or missing metadata. Please get in touch with us to find out if it's possible to update your project to the latest schema version. You can find all information related to the nmrXiv schema on our documentation site. "),
                    createVNode("div", { class: "mt-5" }, [
                      createVNode("span", {
                        type: "button",
                        class: "inline-flex items-center justify-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:outline-none focus:border-yellow-700 focus:ring focus:ring-yellow-200 active:bg-yellow-600 disabled:opacity-25 transition"
                      }, [
                        createVNode(_component_InformationCircleIcon, { class: "flex-shrink-0 mr-1.5 h-5 w-5 mr-4 inline" }),
                        createTextVNode(" " + toDisplayString($props.project.schema_version), 1)
                      ]),
                      $props.project.schema_version == $props.schema ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "ml-4"
                      }, " Migrate now? ")) : createCommentVNode("", true)
                    ])
                  ])
                ])
              ])
            ]),
            !$props.project.is_public && !$props.project.is_deleted ? (openBlock(), createBlock("div", { key: 0 }, [
              createVNode(_component_project_delete, { project: $props.project }, null, 8, ["project"])
            ])) : createCommentVNode("", true),
            $props.project.is_deleted ? (openBlock(), createBlock("div", { key: 1 }, [
              createVNode(_component_project_restore, { project: $props.project }, null, 8, ["project"])
            ])) : createCommentVNode("", true),
            $props.project.is_public && !$props.project.is_archived ? (openBlock(), createBlock("div", { key: 2 }, [
              createVNode(_component_project_archive, { project: $props.project }, null, 8, ["project"])
            ])) : createCommentVNode("", true),
            $props.project.is_public && $props.project.is_archived ? (openBlock(), createBlock("div", { key: 3 }, [
              createVNode(_component_project_unarchive, { project: $props.project }, null, 8, ["project"])
            ])) : createCommentVNode("", true)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Project/Settings.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Settings = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Settings as default
};
