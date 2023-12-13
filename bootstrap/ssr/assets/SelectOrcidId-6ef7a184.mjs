import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { L as LoadingButton } from "./LoadingButton-8ae902b0.mjs";
import { resolveComponent, mergeProps, withCtx, createTextVNode, openBlock, createBlock, createVNode, createCommentVNode, Fragment, renderList, toDisplayString, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderStyle, ssrRenderList, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  components: {
    JetSecondaryButton,
    JetDialogModal,
    LoadingButton
  },
  props: ["orcidId", "affiliation"],
  emits: ["update:orcidId", "update:affiliation"],
  data() {
    return {
      show: false,
      selectedOrcidId: this.orcidId,
      selectedAffiliation: this.affiliation,
      loading: false,
      orcidIdSearchResults: []
    };
  },
  methods: {
    findOrcidID(first_name, last_name) {
      this.orcidIdSearchResults = [];
      if (first_name && last_name) {
        this.loading = true;
        this.show = true;
        axios.get(this.$page.props.orcidSearchApi, {
          headers: {
            accept: "application/json"
          },
          params: {
            q: "given-names:" + first_name + " AND family-name:" + last_name
          }
        }).then((res) => {
          if (res.data && res.data.result.length > 0) {
            this.getPersonData(res.data.result);
          } else {
            this.loading = false;
          }
        }).catch((error) => {
          console.log(error);
        }).finally(() => {
        });
      }
    },
    getPersonData(results) {
      if (results) {
        var personData = {};
        var employmentdata = {};
        var element = {};
        var orcidId = "";
        this.orcidIdSearchResults = [];
        results.forEach((item) => {
          orcidId = item["orcid-identifier"] ? item["orcid-identifier"].path : null;
          if (orcidId) {
            let personDataAPI = this.$page.props.orcidPersonApi.replace(
              "{orcid_id}",
              orcidId
            );
            let employmentDataAPI = this.$page.props.orcidEmploymentApi.replace(
              "{orcid_id}",
              item["orcid-identifier"].path
            );
            const requestPersonData = axios.get(personDataAPI, {
              headers: {
                accept: "application/json"
              }
            });
            const requestEmploymentData = axios.get(
              employmentDataAPI,
              {
                headers: {
                  accept: "application/json"
                }
              }
            );
            axios.all([requestPersonData, requestEmploymentData]).then(
              axios.spread((...responses) => {
                personData = responses[0].data;
                employmentdata = responses[1].data;
              })
            ).catch((error) => {
              console.log(error);
            }).finally(() => {
              if (personData) {
                element.firstName = personData["name"] ? personData["name"]["given-names"].value : "";
                element.lastName = personData["name"] ? personData["name"]["family-name"].value : "";
                if (personData["emails"]) {
                  element.email = personData["emails"]["email"][0] ? personData["emails"]["email"][0].email : "";
                }
              }
              if (employmentdata) {
                if (employmentdata["affiliation-group"]) {
                  if (employmentdata["affiliation-group"][0]) {
                    if (employmentdata["affiliation-group"][0].summaries) {
                      element.employer = employmentdata["affiliation-group"][0].summaries[0] ? employmentdata["affiliation-group"][0].summaries[0]["employment-summary"].organization.name : "";
                    }
                  }
                }
              }
              element.orcidId = item["orcid-identifier"] ? item["orcid-identifier"].uri : "";
              this.orcidIdSearchResults.push(element);
              element = {};
              this.loading = false;
            });
          }
        });
      }
    },
    selectOrcidId(item) {
      this.selectedOrcidId = item.orcidId ? item.orcidId.substr(18, item.orcidId.length) : "";
      this.selectedAffiliation = item.employer ? item.employer : "";
      this.$emit("update:orcidId", this.selectedOrcidId);
      this.$emit("update:affiliation", this.selectedAffiliation);
      this.show = false;
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_dialog_modal = resolveComponent("jet-dialog-modal");
  const _component_loading_button = resolveComponent("loading-button");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  _push(ssrRenderComponent(_component_jet_dialog_modal, mergeProps({
    show: $data.show,
    onClose: ($event) => $data.show = false
  }, _attrs), {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Select ORCID iD `);
      } else {
        return [
          createTextVNode(" Select ORCID iD ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        if ($data.loading) {
          _push2(`<div class="sm:col-span-9 mt-4 align-centre"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_loading_button, { loading: $data.loading }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        if ($data.orcidIdSearchResults.length == 0 && !$data.loading) {
          _push2(`<div class="sm:col-span-9 mt-4 align-centre text-red-500"${_scopeId}><p${_scopeId}>Something went wrong. Please enter the id manually.</p></div>`);
        } else {
          _push2(`<!---->`);
        }
        if (!$data.loading && $data.orcidIdSearchResults.length > 0) {
          _push2(`<div style="${ssrRenderStyle({ "min-height": "10vh", "max-height": "60vh" })}" class="overflow-auto p-1"${_scopeId}><!--[-->`);
          ssrRenderList($data.orcidIdSearchResults, (item) => {
            _push2(`<div class="relative flex items-start mt-2"${_scopeId}><div class="cursor-pointer flex-1 border rounded-md p-2 bg-white-200 hover:bg-gray-200"${_scopeId}><div class="text-gray-900"${_scopeId}><p class="text-sm font-medium text-teal-900"${_scopeId}>${ssrInterpolate(item.firstName)} ${ssrInterpolate(item.lastName)}</p>`);
            if (item.employer) {
              _push2(`<p class="text-xs text-gray-500 mt-1"${_scopeId}><b class="text-gray-500"${_scopeId}>Employer:</b> ${ssrInterpolate(item.employer)}</p>`);
            } else {
              _push2(`<!---->`);
            }
            if (item.email) {
              _push2(`<p class="text-xs text-gray-500 mt-1"${_scopeId}><b class="text-gray-500"${_scopeId}>Email:</b> ${ssrInterpolate(item.email)}</p>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="flex items-center text-xs text-gray-600 mt-1 underline"${_scopeId}><img alt="ORCID logo" src="https://orcid.org/assets/vectors/orcid.logo.icon.svg" width="15" height="15"${_scopeId}><p class="ml-1"${_scopeId}>${ssrInterpolate(item.orcidId)}</p></div></div></div></div>`);
          });
          _push2(`<!--]--></div>`);
        } else {
          _push2(`<!---->`);
        }
      } else {
        return [
          $data.loading ? (openBlock(), createBlock("div", {
            key: 0,
            class: "sm:col-span-9 mt-4 align-centre"
          }, [
            createVNode(_component_loading_button, { loading: $data.loading }, null, 8, ["loading"])
          ])) : createCommentVNode("", true),
          $data.orcidIdSearchResults.length == 0 && !$data.loading ? (openBlock(), createBlock("div", {
            key: 1,
            class: "sm:col-span-9 mt-4 align-centre text-red-500"
          }, [
            createVNode("p", null, "Something went wrong. Please enter the id manually.")
          ])) : createCommentVNode("", true),
          !$data.loading && $data.orcidIdSearchResults.length > 0 ? (openBlock(), createBlock("div", {
            key: 2,
            style: { "min-height": "10vh", "max-height": "60vh" },
            class: "overflow-auto p-1"
          }, [
            (openBlock(true), createBlock(Fragment, null, renderList($data.orcidIdSearchResults, (item) => {
              return openBlock(), createBlock("div", {
                key: item.orcidId,
                class: "relative flex items-start mt-2"
              }, [
                createVNode("div", {
                  class: "cursor-pointer flex-1 border rounded-md p-2 bg-white-200 hover:bg-gray-200",
                  onClick: ($event) => $options.selectOrcidId(item)
                }, [
                  createVNode("div", { class: "text-gray-900" }, [
                    createVNode("p", { class: "text-sm font-medium text-teal-900" }, toDisplayString(item.firstName) + " " + toDisplayString(item.lastName), 1),
                    item.employer ? (openBlock(), createBlock("p", {
                      key: 0,
                      class: "text-xs text-gray-500 mt-1"
                    }, [
                      createVNode("b", { class: "text-gray-500" }, "Employer:"),
                      createTextVNode(" " + toDisplayString(item.employer), 1)
                    ])) : createCommentVNode("", true),
                    item.email ? (openBlock(), createBlock("p", {
                      key: 1,
                      class: "text-xs text-gray-500 mt-1"
                    }, [
                      createVNode("b", { class: "text-gray-500" }, "Email:"),
                      createTextVNode(" " + toDisplayString(item.email), 1)
                    ])) : createCommentVNode("", true),
                    createVNode("div", { class: "flex items-center text-xs text-gray-600 mt-1 underline" }, [
                      createVNode("img", {
                        alt: "ORCID logo",
                        src: "https://orcid.org/assets/vectors/orcid.logo.icon.svg",
                        width: "15",
                        height: "15"
                      }),
                      createVNode("p", { class: "ml-1" }, toDisplayString(item.orcidId), 1)
                    ])
                  ])
                ], 8, ["onClick"])
              ]);
            }), 128))
          ])) : createCommentVNode("", true)
        ];
      }
    }),
    footer: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_secondary_button, {
          onClick: ($event) => $data.show = false
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Cancel `);
            } else {
              return [
                createTextVNode(" Cancel ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_jet_secondary_button, {
            onClick: ($event) => $data.show = false
          }, {
            default: withCtx(() => [
              createTextVNode(" Cancel ")
            ]),
            _: 1
          }, 8, ["onClick"])
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/SelectOrcidId.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const SelectOrcidId = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  SelectOrcidId as S
};
