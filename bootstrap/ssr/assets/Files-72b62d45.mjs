import ProjectLayout from "./Layout-3465446f.mjs";
import { F as FileSystemBrowser } from "./AppLayout-b93d1c11.mjs";
import { FolderIcon, DocumentTextIcon, ChevronRightIcon, HomeIcon } from "@heroicons/vue/24/solid";
import { Head } from "@inertiajs/vue3";
import { resolveComponent, withCtx, createVNode, openBlock, createBlock, toDisplayString, Fragment, renderList, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderAttr } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./DOIBadge-164e4f67.mjs";
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
const _sfc_main = {
  components: {
    ProjectLayout,
    FolderIcon,
    DocumentTextIcon,
    ChevronRightIcon,
    HomeIcon,
    FileSystemBrowser,
    Head
  },
  props: ["project", "tab"],
  data() {
    return {
      selectedFileSystemObject: null
    };
  },
  computed: {
    url() {
      return String(this.$page.props.url);
    },
    downloadURL() {
      return this.url + "/" + this.project.data.owner.username + "/download/" + this.project.data.slug + "?key=" + this.$page.props.selectedFileSystemObject.name + "&uuid=" + this.$page.props.selectedFileSystemObject.uuid;
    }
  },
  mounted() {
    this.$page.props.selectedFileSystemObject = this.project.data.files;
    this.$page.props.selectedFolder = "/";
    this.$nextTick(function() {
      if (this.$refs.fsbRef) {
        this.$refs.fsbRef.loadFiles();
      }
    });
  },
  methods: {}
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_project_layout = resolveComponent("project-layout");
  const _component_HomeIcon = resolveComponent("HomeIcon");
  const _component_ChevronRightIcon = resolveComponent("ChevronRightIcon");
  const _component_file_system_browser = resolveComponent("file-system-browser");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_Head, {
    title: "Files - " + $props.project.data.name
  }, null, _parent));
  _push(ssrRenderComponent(_component_project_layout, {
    project: $props.project,
    "selected-tab": $props.tab
  }, {
    "project-content": withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-6"${_scopeId}>`);
        if ($props.project.data.files) {
          _push2(`<div${_scopeId}>`);
          if (_ctx.$page.props.selectedFolder) {
            _push2(`<nav class="flex p-3" aria-label="Breadcrumb"${_scopeId}><ol role="list" class="flex items-center space-x-2"${_scopeId}><li${_scopeId}><div${_scopeId}><a class="text-gray-400 hover:text-gray-900"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_HomeIcon, {
              class: "flex-shrink-0 h-5 w-5",
              "aria-hidden": "true"
            }, null, _parent2, _scopeId));
            _push2(`<span class="sr-only"${_scopeId}>Home</span></a></div></li><li${_scopeId}><div class="flex items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_ChevronRightIcon, {
              class: "flex-shrink-0 h-5 w-5 text-gray-400",
              "aria-hidden": "true"
            }, null, _parent2, _scopeId));
            _push2(`<a class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"${_scopeId}>${ssrInterpolate($props.project.data.name)}</a></div></li><!--[-->`);
            ssrRenderList(_ctx.$page.props.selectedFolder.split(
              "/"
            ), (page) => {
              _push2(`<li${_scopeId}>`);
              if (page != "") {
                _push2(`<div class="flex items-center"${_scopeId}>`);
                _push2(ssrRenderComponent(_component_ChevronRightIcon, {
                  class: "flex-shrink-0 h-5 w-5 text-gray-400",
                  "aria-hidden": "true"
                }, null, _parent2, _scopeId));
                _push2(`<a class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"${ssrRenderAttr(
                  "aria-current",
                  page ? "page" : void 0
                )}${_scopeId}>${ssrInterpolate(page)}</a></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</li>`);
            });
            _push2(`<!--]--></ol></nav>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`<div${_scopeId}>`);
          _push2(ssrRenderComponent(_component_file_system_browser, {
            ref: "fsbRef",
            readonly: true,
            height: "h-[calc(100vh-385px)]"
          }, null, _parent2, _scopeId));
          _push2(`</div></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-6" }, [
            $props.project.data.files ? (openBlock(), createBlock("div", { key: 0 }, [
              _ctx.$page.props.selectedFolder ? (openBlock(), createBlock("nav", {
                key: 0,
                class: "flex p-3",
                "aria-label": "Breadcrumb"
              }, [
                createVNode("ol", {
                  role: "list",
                  class: "flex items-center space-x-2"
                }, [
                  createVNode("li", null, [
                    createVNode("div", null, [
                      createVNode("a", { class: "text-gray-400 hover:text-gray-900" }, [
                        createVNode(_component_HomeIcon, {
                          class: "flex-shrink-0 h-5 w-5",
                          "aria-hidden": "true"
                        }),
                        createVNode("span", { class: "sr-only" }, "Home")
                      ])
                    ])
                  ]),
                  createVNode("li", null, [
                    createVNode("div", { class: "flex items-center" }, [
                      createVNode(_component_ChevronRightIcon, {
                        class: "flex-shrink-0 h-5 w-5 text-gray-400",
                        "aria-hidden": "true"
                      }),
                      createVNode("a", { class: "ml-4 text-sm font-medium text-gray-500 hover:text-gray-700" }, toDisplayString($props.project.data.name), 1)
                    ])
                  ]),
                  (openBlock(true), createBlock(Fragment, null, renderList(_ctx.$page.props.selectedFolder.split(
                    "/"
                  ), (page) => {
                    return openBlock(), createBlock("li", {
                      key: page.name
                    }, [
                      page != "" ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "flex items-center"
                      }, [
                        createVNode(_component_ChevronRightIcon, {
                          class: "flex-shrink-0 h-5 w-5 text-gray-400",
                          "aria-hidden": "true"
                        }),
                        createVNode("a", {
                          class: "ml-4 text-sm font-medium text-gray-500 hover:text-gray-700",
                          "aria-current": page ? "page" : void 0
                        }, toDisplayString(page), 9, ["aria-current"])
                      ])) : createCommentVNode("", true)
                    ]);
                  }), 128))
                ])
              ])) : createCommentVNode("", true),
              createVNode("div", null, [
                createVNode(_component_file_system_browser, {
                  ref: "fsbRef",
                  readonly: true,
                  height: "h-[calc(100vh-385px)]"
                }, null, 8, ["height"])
              ])
            ])) : createCommentVNode("", true)
          ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Project/Files.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Files = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Files as default
};
