import Dropzone from "dropzone";
import { router } from "@inertiajs/vue3";
import StudyContent from "./Content-5913e7b1.mjs";
import { a as FileDetails } from "./AppLayout-b93d1c11.mjs";
import axiosRetry from "axios-retry";
import OCL from "openchemlib/minimal.js";
import { FolderIcon, DocumentTextIcon, ChevronRightIcon, HomeIcon } from "@heroicons/vue/24/solid";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { resolveComponent, withCtx, createVNode, openBlock, createBlock, Fragment, renderList, toDisplayString, createCommentVNode, createTextVNode, withModifiers, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderList, ssrRenderAttr, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./Layout-114f561c.mjs";
import "./Details-f3d36d90.mjs";
import "./ActionMessage-3207b224.mjs";
import "@heroicons/vue/24/outline";
import "./InputError-6ae0b65c.mjs";
import "./Activity-90a526a4.mjs";
import "vue3-colorpicker";
/* empty css                 */import "./Button-8d6976fd.mjs";
import "./SelectRich-b58dcc3f.mjs";
import "./ToolTip-cf9882a3.mjs";
import "./SecondaryButton-3cee9e05.mjs";
import "@sipec/vue3-tags-input";
import "./AccessDialogue-1fdd1bde.mjs";
import "./ActionSection-c1b53bcc.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./ConfirmationModal-d0b47856.mjs";
import "./Modal-b2fb4cf2.mjs";
import "./DangerButton-9c23157c.mjs";
import "./DialogModal-f54eea4c.mjs";
import "./Input-f9a8cf77.mjs";
import "./Label-c5144ba4.mjs";
import "./SectionBorder-7d1f222a.mjs";
import "./Citation-80069afd.mjs";
import "axios";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
import "@vueuse/core";
import "./AnnouncementBanner-3ebe3621.mjs";
import "popper.js";
import "./FlashMessages-f0051c19.mjs";
import "vue3-slider";
import "openchemlib/full.js";
import "@heroicons/vue/20/solid";
const _sfc_main = {
  components: {
    StudyContent,
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    FolderIcon,
    DocumentTextIcon,
    FileDetails,
    ChevronRightIcon,
    HomeIcon
  },
  props: [
    "study",
    "project",
    "file",
    "team",
    "members",
    "availableRoles",
    "studyPermissions",
    "studyRole"
  ],
  setup() {
    return {};
  },
  data() {
    return {
      progress: 0,
      status: null,
      selectedFileSystemObject: null,
      svgString: null
    };
  },
  computed: {
    url() {
      return String(this.$page.props.url);
    },
    nmriumURL() {
      return this.$page.props.nmriumURL ? String(this.$page.props.nmriumURL) : "//nmriumdev.nmrxiv.org";
    },
    downloadURL() {
      return this.url + "/" + this.project.owner.username + "/download/" + this.project.slug + "?key=" + this.$page.props.selectedFileSystemObject.name + "&uuid=" + this.$page.props.selectedFileSystemObject.uuid;
    },
    canUpdateFiles() {
      return this.studyPermissions ? this.studyPermissions.canUpdateStudy : false;
    }
  },
  mounted() {
    const vm = this;
    vm.$page.props.selectedFileSystemObject = vm.file;
    vm.$page.props.selectedFolder = vm.file.children[0] ? vm.file.children[0].relative_url : "";
    if (!this.study.is_public) {
      let options = {
        url: "/",
        method: "put",
        sending(file, xhr) {
          let _send = xhr.send;
          xhr.send = () => {
            _send.call(xhr, file);
          };
        },
        autoProcessQueue: false,
        uploadMultiple: false,
        disablePreviews: true,
        parallelUploads: 1,
        maxFiles: 1e4,
        dictDefaultMessage: document.querySelector("#dropzone-message") ? document.querySelector("#dropzone-message").innerHTML : null,
        done() {
        },
        accept(file, done) {
          const url = "/dashboard/storage/signed-storage-url";
          const client = axios.create({
            baseURL: window.location.origin
          });
          axiosRetry(client, {
            retries: 3,
            retryCondition: (error) => {
              return error.response.status === 500;
            }
          });
          client.post(url, {
            file,
            destination: vm.$page.props.selectedFolder,
            project_id: vm.project.id,
            study_id: vm.study.id
          }).catch((err) => {
            if (err.response.status !== 200 || err.response.status !== 201) {
              throw new Error(
                `API call failed with status code: ${err.response.status} after multiple attempts`
              );
            }
          }).then(function(response) {
            let data = response.data;
            let headers = data.headers;
            if ("Host" in headers) {
              delete headers.Host;
            }
            file.uploadURL = data.url;
            setTimeout(() => vm.dropzone.processFile(file));
            done();
          });
        },
        totaluploadprogress: function(progress) {
          vm.progress = Math.ceil(progress);
        },
        queuecomplete: function() {
          vm.status = "UPLOAD COMPLETE";
          router.reload();
          this.$page.props.selectedFileSystemObject = this.files[0];
        }
      };
      this.dropzone = new Dropzone(this.$el, options);
      vm.dropzone.on("processing", (file) => {
        vm.status = "UPLOAD IN PROGRESS";
        vm.dropzone.options.url = file.uploadURL;
      });
    }
    this.$page.props.selectedFileSystemObject = this.file.children[0];
  },
  methods: {
    displaySelected(file) {
      this.$page.props.selectedFileSystemObject = file;
      let sFolder = "/";
      if (this.$page.props.selectedFileSystemObject.name == "/") {
        sFolder = "/";
      } else {
        if (this.$page.props.selectedFileSystemObject.type != "file") {
          sFolder = this.$page.props.selectedFileSystemObject.relative_url;
        } else {
          if (this.$page.props.selectedFileSystemObject.parent_id == null) {
            sFolder = "/";
          } else {
            sFolder = this.$page.props.selectedFileSystemObject.relative_url.replace(
              "/" + this.$page.props.selectedFileSystemObject.name,
              ""
            );
          }
        }
      }
      this.$page.props.selectedFolder = sFolder;
      if (file.has_children && file.level > 0 && !file.children) {
        file.loading = true;
        axios.get(
          "/api/v1/files/children/" + this.study.id + "/" + file.id
        ).then((response) => {
          file.children = response.data.files[0].children;
          file.loading = false;
        });
      }
    },
    loadMol() {
      this.svgString = null;
      axios.get(this.downloadURL).then((response) => {
        if (response && response.data != "") {
          let mol = OCL.Molecule.fromMolfile(response.data);
          if (mol.toIsomericSmiles() != "") {
            this.svgString = mol.toSVG(300, 300);
          }
        }
      });
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_study_content = resolveComponent("study-content");
  const _component_HomeIcon = resolveComponent("HomeIcon");
  const _component_ChevronRightIcon = resolveComponent("ChevronRightIcon");
  const _component_children = resolveComponent("children");
  const _component_FolderIcon = resolveComponent("FolderIcon");
  const _component_DocumentTextIcon = resolveComponent("DocumentTextIcon");
  const _component_File_details = resolveComponent("File-details");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  _push(ssrRenderComponent(_component_study_content, {
    model: "study",
    project: $props.project,
    study: $props.study,
    team: $props.team,
    members: $props.members,
    "available-roles": $props.availableRoles,
    "study-permissions": $props.studyPermissions,
    "study-role": $props.studyRole,
    current: "Files"
  }, {
    "study-section": withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="divide-y divide-gray-200 sm:col-span-9"${_scopeId}>`);
        if ($props.file) {
          _push2(`<div${_scopeId}>`);
          if (_ctx.$page.props.selectedFolder) {
            _push2(`<nav class="flex p-3" aria-label="Breadcrumb"${_scopeId}><ol role="list" class="flex items-center space-x-2"${_scopeId}><li${_scopeId}><div${_scopeId}><a class="text-gray-400 hover:text-gray-900"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_HomeIcon, {
              class: "flex-shrink-0 h-5 w-5",
              "aria-hidden": "true"
            }, null, _parent2, _scopeId));
            _push2(`<span class="sr-only"${_scopeId}>Home</span></a></div></li><!--[-->`);
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
          _push2(`<div class="min-w-0 flex-1 min-h-screen border-t border-gray-200 lg:flex"${_scopeId}><aside class="hidden py-3 px-2 lg:block lg:flex-shrink-0 lg:order-first"${_scopeId}>`);
          if ($props.file != null) {
            _push2(`<div class="h-full relative flex flex-col w-64 border-r border-gray-200 overflow-y-auto"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_children, {
              file: $props.file,
              study: $props.study,
              project: $props.project
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</aside><section class="min-w-0 p-6 flex-1 h-full flex flex-col overflow-y-auto lg:order-last"${_scopeId}>`);
          if (_ctx.$page.props.selectedFileSystemObject && _ctx.$page.props.selectedFileSystemObject.has_children) {
            _push2(`<div class="mb-3"${_scopeId}><div class="py-2 mb-2 block"${_scopeId}><p class="font-bold text-xl"${_scopeId}>${ssrInterpolate(_ctx.$page.props.selectedFileSystemObject.name)} <a${ssrRenderAttr("href", $options.downloadURL)} class="ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 float-right"${_scopeId}> Download </a></p></div><ul role="list" class="mb-3 grid grid-cols-1 gap-5 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4"${_scopeId}><!--[-->`);
            ssrRenderList(_ctx.$page.props.selectedFileSystemObject.children, (file) => {
              _push2(`<li class="relative shadow rounded-lg"${_scopeId}><div class="group block w-full aspect-w-10 aspect-h-7 py-4 bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-indigo-500 overflow-hidden"${_scopeId}>`);
              if (file.type == "directory") {
                _push2(`<span${_scopeId}>`);
                _push2(ssrRenderComponent(_component_FolderIcon, {
                  class: "cursor-pointer h-28 w-28 text-gray-400 flex-shrink-0 mx-auto",
                  "aria-hidden": "true",
                  onDblclick: ($event) => $options.displaySelected(
                    file
                  )
                }, null, _parent2, _scopeId));
                _push2(`</span>`);
              } else {
                _push2(`<span${_scopeId}>`);
                _push2(ssrRenderComponent(_component_DocumentTextIcon, {
                  class: "h-28 w-28 text-gray-400 flex-shrink-0 mx-auto",
                  "aria-hidden": "true"
                }, null, _parent2, _scopeId));
                _push2(`</span>`);
              }
              _push2(`</div><p class="mt-2 px-2 py-1 block truncate text-sm font-medium text-gray-900 pointer-events-none"${_scopeId}>${ssrInterpolate(file.name)}</p><p class="block text-sm font-medium text-gray-500 pointer-events-none"${_scopeId}>${ssrInterpolate(file.size)}</p></li>`);
            });
            _push2(`<!--]--></ul></div>`);
          } else {
            _push2(`<div${_scopeId}><div class=""${_scopeId}>`);
            if (_ctx.$page.props.selectedFileSystemObject && _ctx.$page.props.selectedFileSystemObject.type == "file") {
              _push2(`<span${_scopeId}>`);
              _push2(ssrRenderComponent(_component_File_details, {
                study: $props.study,
                file: _ctx.$page.props.selectedFileSystemObject
              }, null, _parent2, _scopeId));
              _push2(`</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
          }
          _push2(`</section></div></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "divide-y divide-gray-200 sm:col-span-9" }, [
            $props.file ? (openBlock(), createBlock("div", { key: 0 }, [
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
              createVNode("div", { class: "min-w-0 flex-1 min-h-screen border-t border-gray-200 lg:flex" }, [
                createVNode("aside", { class: "hidden py-3 px-2 lg:block lg:flex-shrink-0 lg:order-first" }, [
                  $props.file != null ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "h-full relative flex flex-col w-64 border-r border-gray-200 overflow-y-auto"
                  }, [
                    createVNode(_component_children, {
                      file: $props.file,
                      study: $props.study,
                      project: $props.project
                    }, null, 8, ["file", "study", "project"])
                  ])) : createCommentVNode("", true)
                ]),
                createVNode("section", { class: "min-w-0 p-6 flex-1 h-full flex flex-col overflow-y-auto lg:order-last" }, [
                  _ctx.$page.props.selectedFileSystemObject && _ctx.$page.props.selectedFileSystemObject.has_children ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "mb-3"
                  }, [
                    createVNode("div", { class: "py-2 mb-2 block" }, [
                      createVNode("p", { class: "font-bold text-xl" }, [
                        createTextVNode(toDisplayString(_ctx.$page.props.selectedFileSystemObject.name) + " ", 1),
                        createVNode("a", {
                          href: $options.downloadURL,
                          class: "ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 float-right"
                        }, " Download ", 8, ["href"])
                      ])
                    ]),
                    createVNode("ul", {
                      role: "list",
                      class: "mb-3 grid grid-cols-1 gap-5 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4"
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(_ctx.$page.props.selectedFileSystemObject.children, (file) => {
                        return openBlock(), createBlock("li", {
                          key: file.key,
                          class: "relative shadow rounded-lg"
                        }, [
                          createVNode("div", { class: "group block w-full aspect-w-10 aspect-h-7 py-4 bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-indigo-500 overflow-hidden" }, [
                            file.type == "directory" ? (openBlock(), createBlock("span", { key: 0 }, [
                              createVNode(_component_FolderIcon, {
                                class: "cursor-pointer h-28 w-28 text-gray-400 flex-shrink-0 mx-auto",
                                "aria-hidden": "true",
                                onDblclick: withModifiers(($event) => $options.displaySelected(
                                  file
                                ), ["stop"])
                              }, null, 8, ["onDblclick"])
                            ])) : (openBlock(), createBlock("span", { key: 1 }, [
                              createVNode(_component_DocumentTextIcon, {
                                class: "h-28 w-28 text-gray-400 flex-shrink-0 mx-auto",
                                "aria-hidden": "true"
                              })
                            ]))
                          ]),
                          createVNode("p", { class: "mt-2 px-2 py-1 block truncate text-sm font-medium text-gray-900 pointer-events-none" }, toDisplayString(file.name), 1),
                          createVNode("p", { class: "block text-sm font-medium text-gray-500 pointer-events-none" }, toDisplayString(file.size), 1)
                        ]);
                      }), 128))
                    ])
                  ])) : (openBlock(), createBlock("div", { key: 1 }, [
                    createVNode("div", { class: "" }, [
                      _ctx.$page.props.selectedFileSystemObject && _ctx.$page.props.selectedFileSystemObject.type == "file" ? (openBlock(), createBlock("span", { key: 0 }, [
                        createVNode(_component_File_details, {
                          study: $props.study,
                          file: _ctx.$page.props.selectedFileSystemObject
                        }, null, 8, ["study", "file"])
                      ])) : createCommentVNode("", true)
                    ])
                  ]))
                ])
              ])
            ])) : createCommentVNode("", true)
          ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Study/Files.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Files = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Files as default
};
