import { J as JetApplicationLogo } from "./ApplicationLogo-88e5413e.mjs";
import { computed, ref, resolveComponent, mergeProps, withCtx, createVNode, openBlock, createBlock, createTextVNode, createCommentVNode, toDisplayString, Fragment, renderList, useSSRContext } from "vue";
import { Combobox, ComboboxInput, ComboboxOptions, ComboboxOption, Dialog, DialogPanel, TransitionChild, TransitionRoot } from "@headlessui/vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import { DocumentPlusIcon, FolderIcon, FolderPlusIcon, HashtagIcon, TagIcon, ArrowTopRightOnSquareIcon } from "@heroicons/vue/24/outline";
import axios from "axios";
import { router } from "@inertiajs/vue3";
import { ssrRenderComponent, ssrRenderAttr, ssrInterpolate, ssrRenderList } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  components: {
    JetApplicationLogo,
    computed,
    ref,
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
    MagnifyingGlassIcon,
    DocumentPlusIcon,
    FolderIcon,
    FolderPlusIcon,
    HashtagIcon,
    TagIcon,
    ArrowTopRightOnSquareIcon
  },
  props: [],
  data() {
    return {
      open: ref(false),
      steps: [
        { id: 1, name: "Step 1", href: "#", status: "current" },
        { id: 2, name: "Step 2", href: "#", status: "upcoming" },
        { id: 3, name: "Step 3", href: "#", status: "upcoming" },
        { id: 4, name: "Step 4", href: "#", status: "upcoming" }
      ]
    };
  },
  computed: {
    currentStep() {
      return this.steps.find((s) => s.status == "current").id;
    },
    mailFromAddress() {
      return String(this.$page.props.mailFromAddress);
    },
    mailTo() {
      return "mailto:" + String(this.$page.props.mailFromAddress);
    }
  },
  mounted() {
    if (!this.$page.props.auth.user.onboarded) {
      this.open = true;
    }
  },
  methods: {
    selectStep(id) {
      this.steps.forEach((step) => {
        if (parseInt(step.id) < id) {
          step.status = "complete";
        } else if (parseInt(step.id) == id) {
          step.status = "current";
          this.$nextTick(() => {
            this.$refs.next.focus();
          });
        } else {
          step.status = "upcoming";
        }
      });
    },
    onboardingComplete() {
      this.open = false;
      axios.post("/onboarding/complete").then(() => {
        router.reload({
          only: ["user", "user.permissions", "user.roles"]
        });
      });
    },
    onboardAndStartTour() {
      this.onboardingComplete();
      this.$tours["appTour"].start();
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_TransitionRoot = resolveComponent("TransitionRoot");
  const _component_Dialog = resolveComponent("Dialog");
  const _component_TransitionChild = resolveComponent("TransitionChild");
  const _component_DialogPanel = resolveComponent("DialogPanel");
  const _component_jet_application_logo = resolveComponent("jet-application-logo");
  const _component_ArrowTopRightOnSquareIcon = resolveComponent("ArrowTopRightOnSquareIcon");
  _push(ssrRenderComponent(_component_TransitionRoot, mergeProps({
    show: $data.open,
    as: "template",
    appear: ""
  }, _attrs), {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_Dialog, {
          as: "div",
          class: "relative z-10"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_TransitionChild, {
                as: "template",
                enter: "ease-out duration-300",
                "enter-from": "opacity-0",
                "enter-to": "opacity-100",
                leave: "ease-in duration-200",
                "leave-from": "opacity-100",
                "leave-to": "opacity-0"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(`<div class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity"${_scopeId3}></div>`);
                  } else {
                    return [
                      createVNode("div", { class: "fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" })
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`<div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20"${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_TransitionChild, {
                as: "template",
                enter: "ease-out duration-300",
                "enter-from": "opacity-0 scale-95",
                "enter-to": "opacity-100 scale-100",
                leave: "ease-in duration-200",
                "leave-from": "opacity-100 scale-100",
                "leave-to": "opacity-0 scale-95"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(ssrRenderComponent(_component_DialogPanel, { class: "mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all" }, {
                      default: withCtx((_4, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(`<div${_scopeId4}>`);
                          if ($options.currentStep == 1) {
                            _push5(`<div${_scopeId4}><section class="h-1/2 overflow-hidden bg-gray-50 overflow-hidden py-12"${_scopeId4}><div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"${_scopeId4}><svg class="absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2" width="404" height="404" fill="none" viewBox="0 0 404 404" role="img" aria-labelledby="svg-nmrxiv"${_scopeId4}><title id="svg-nmrxiv"${_scopeId4}> nmrxiv </title><defs${_scopeId4}><pattern id="ad119f34-7694-4c31-947f-5c9d249b21f3" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"${_scopeId4}><rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor"${_scopeId4}></rect></pattern></defs><rect width="404" height="404" fill="url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"${_scopeId4}></rect></svg><div class="relative"${_scopeId4}><div class="flex justify-center"${_scopeId4}>`);
                            _push5(ssrRenderComponent(_component_jet_application_logo, { class: "block h-14 w-auto" }, null, _parent5, _scopeId4));
                            _push5(`</div><blockquote class="mt-10"${_scopeId4}><div class="max-w-3xl mx-auto text-center text-xl leading-9 font-medium text-gray-900"${_scopeId4}><p class="mt-3 text-lg"${_scopeId4}> Welcome! <span class="text-xl"${_scopeId4}>nmr<b${_scopeId4}>Xiv</b></span> support scientists in their efforts to collect, store, process, analyse, disclose and re-use Nuclear Magnetic Resonance Spectroscopy research data. Ready to get started? </p></div></blockquote></div></div></section></div>`);
                          } else {
                            _push5(`<!---->`);
                          }
                          if ($options.currentStep == 2) {
                            _push5(`<div${_scopeId4}><section class="h-1/2 overflow-hidden py-12"${_scopeId4}><div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"${_scopeId4}><svg class="absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2" width="404" height="404" fill="none" viewBox="0 0 404 404" role="img" aria-labelledby="svg-nmrxiv"${_scopeId4}><title id="svg-nmrxiv"${_scopeId4}> nmrxiv </title><defs${_scopeId4}><pattern id="ad119f34-7694-4c31-947f-5c9d249b21f3" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"${_scopeId4}><rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor"${_scopeId4}></rect></pattern></defs><rect width="404" height="404" fill="url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"${_scopeId4}></rect></svg><div class="relative text-center"${_scopeId4}><blockquote class="mt-10"${_scopeId4}><div class="max-w-3xl mx-auto text-center text-xl leading-9 font-medium text-gray-900"${_scopeId4}><p class="mt-3 text-lg"${_scopeId4}><span class="text-xl"${_scopeId4}>nmr<b${_scopeId4}>Xiv</b></span> as a part of <a class="text-indigo-600 font-bold" href="https://nfdi4chem.de" target="_blank"${_scopeId4}>NFDI4Chem</a>, focuses on the implementation and adaptation of existing and new infrastructure, necessary to capture data early in the life cycle​ and to further manage, analyse and store associated information. Checkout <a class="text-indigo-600" target="_blank" href="https://www.chemotion.net/chemotionsaurus/index.html"${_scopeId4}>Chemotion</a> (Electronic Lab Notebook) or <a href="https://github.com/NFDI4Chem/nmrxiv-cli" class="text-indigo-600" target="_blank"${_scopeId4}>nmrXiv CLI </a>(Command Line Interface) for a seamless integration into your Lab infrastructure. </p></div></blockquote></div></div></section></div>`);
                          } else {
                            _push5(`<!---->`);
                          }
                          if ($options.currentStep == 3) {
                            _push5(`<div${_scopeId4}><section class="h-1/2 overflow-hidden py-12"${_scopeId4}><div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"${_scopeId4}><svg class="absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2" width="404" height="404" fill="none" viewBox="0 0 404 404" role="img" aria-labelledby="svg-nmrxiv"${_scopeId4}><title id="svg-nmrxiv"${_scopeId4}> nmrxiv </title><defs${_scopeId4}><pattern id="ad119f34-7694-4c31-947f-5c9d249b21f3" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"${_scopeId4}><rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor"${_scopeId4}></rect></pattern></defs><rect width="404" height="404" fill="url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"${_scopeId4}></rect></svg><div class="relative text-center"${_scopeId4}><p class="mt-3 text-lg"${_scopeId4}><span class="text-xl"${_scopeId4}>nmr<b${_scopeId4}>Xiv</b></span> is 100% open source, so you’re free to dig through the source to see exactly how it works. See something that needs to be improved? Just send us a request.<br${_scopeId4}><small${_scopeId4}>Chat bubble is on the bottom left corner or email us at <a${ssrRenderAttr("href", $options.mailTo)}${_scopeId4}>${ssrInterpolate($options.mailFromAddress)}</a></small></p><div class="mt-8"${_scopeId4}><div class="inline-flex"${_scopeId4}><a target="_blank" href="https://github.com/NFDI4Chem/nmrxiv" class="inline-flex mr-4 items-center justify-center px-5 py-3 border text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"${_scopeId4}> Source code `);
                            _push5(ssrRenderComponent(_component_ArrowTopRightOnSquareIcon, {
                              class: "-mr-1 ml-3 h-5 w-5 text-gray-400",
                              "aria-hidden": "true"
                            }, null, _parent5, _scopeId4));
                            _push5(`</a><a target="_blank" href="https://docs.nmrxiv.org" class="inline-flex items-center justify-center px-5 py-3 border text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"${_scopeId4}> Visit the docs `);
                            _push5(ssrRenderComponent(_component_ArrowTopRightOnSquareIcon, {
                              class: "-mr-1 ml-3 h-5 w-5 text-gray-400",
                              "aria-hidden": "true"
                            }, null, _parent5, _scopeId4));
                            _push5(`</a></div></div></div></div></section></div>`);
                          } else {
                            _push5(`<!---->`);
                          }
                          if ($options.currentStep == 4) {
                            _push5(`<div${_scopeId4}><div class="max-w-7xl mx-auto text-center py-12 px-4 sm:px-6 lg:py-16 lg:px-8"${_scopeId4}><h2 class="text-base font-semibold uppercase tracking-wider"${_scopeId4}> All set to get started! </h2><p class="my-2 font-bold tracking-tight sm:text-2xl"${_scopeId4}> Start uploading your spectral data today. </p><p${_scopeId4}> Click &quot;Get started&quot; and upload your data or if you want a guided tour of the dashboard press the &quot;Dashboard Tour&quot; button below. </p><div class="mt-8 flex justify-center"${_scopeId4}><div class="inline-flex rounded-md shadow"${_scopeId4}><a refs="next" class="cursor-pointer inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"${_scopeId4}> Get started </a></div></div></div></div>`);
                          } else {
                            _push5(`<!---->`);
                          }
                          _push5(`<div class="grid grid-cols-4 gap-4"${_scopeId4}><div class="p-4 py-4"${_scopeId4}>`);
                          if ($options.currentStep != 1) {
                            _push5(`<button type="button" class="py-2 px-3 border rounded-md"${_scopeId4}><div class="flex flex-row align-middle"${_scopeId4}><svg class="w-5 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"${_scopeId4}><path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd"${_scopeId4}></path></svg><p class="ml-2"${_scopeId4}>Prev</p></div></button>`);
                          } else {
                            _push5(`<button type="button" class="py-2 px-3 border rounded-md"${_scopeId4}><div class="flex flex-row align-middle"${_scopeId4}><p class="mx-2"${_scopeId4}>Skip</p></div></button>`);
                          }
                          _push5(`</div><div class="col-span-2"${_scopeId4}><nav class="flex items-center justify-center p-4 py-6" aria-label="Progress"${_scopeId4}><p class="text-sm font-medium"${_scopeId4}> Step ${ssrInterpolate($data.steps.findIndex(
                            (step) => step.status === "current"
                          ) + 1)} of ${ssrInterpolate($data.steps.length)}</p><ol role="list" class="ml-8 flex items-center space-x-5"${_scopeId4}><!--[-->`);
                          ssrRenderList($data.steps, (step) => {
                            _push5(`<li${_scopeId4}><a class="cursor-pointer"${_scopeId4}>`);
                            if (step.status === "complete") {
                              _push5(`<span${ssrRenderAttr("href", step.href)} class="block w-2.5 h-2.5 bg-indigo-600 rounded-full hover:bg-indigo-600"${_scopeId4}><span class="sr-only"${_scopeId4}>${ssrInterpolate(step.name)}</span></span>`);
                            } else if (step.status === "current") {
                              _push5(`<span${ssrRenderAttr("href", step.href)} class="relative flex items-center justify-center" aria-current="step"${_scopeId4}><span class="absolute w-5 h-5 p-px flex" aria-hidden="true"${_scopeId4}><span class="w-full h-full rounded-full bg-indigo-200"${_scopeId4}></span></span><span class="relative block w-2.5 h-2.5 bg-indigo-600 rounded-full" aria-hidden="true"${_scopeId4}></span><span class="sr-only"${_scopeId4}>${ssrInterpolate(step.name)}</span></span>`);
                            } else {
                              _push5(`<span${ssrRenderAttr("href", step.href)} class="block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400"${_scopeId4}><span class="sr-only"${_scopeId4}>${ssrInterpolate(step.name)}</span></span>`);
                            }
                            _push5(`</a></li>`);
                          });
                          _push5(`<!--]--></ol></nav></div><div class="p-4 py-4 text-right"${_scopeId4}>`);
                          if ($options.currentStep < $data.steps.length) {
                            _push5(`<div${_scopeId4}>`);
                            if ($options.currentStep == 1) {
                              _push5(`<div${_scopeId4}><button type="button" class="py-2 px-3 bg-indigo-600 text-white rounded-md" autofocus${_scopeId4}><div class="flex flex-row align-middle"${_scopeId4}><span class="mr-1"${_scopeId4}>Let&#39;s go</span><svg class="w-5 ml-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"${_scopeId4}><path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"${_scopeId4}></path></svg></div></button></div>`);
                            } else {
                              _push5(`<div${_scopeId4}><button type="button" class="py-2 px-3 bg-indigo-600 text-white rounded-md" autofocus${_scopeId4}><div class="flex flex-row align-middle"${_scopeId4}><span class="mr-1"${_scopeId4}>Next</span><svg class="w-5 ml-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"${_scopeId4}><path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"${_scopeId4}></path></svg></div></button></div>`);
                            }
                            _push5(`</div>`);
                          } else {
                            _push5(`<!---->`);
                          }
                          _push5(`</div></div></div>`);
                        } else {
                          return [
                            createVNode("div", null, [
                              $options.currentStep == 1 ? (openBlock(), createBlock("div", { key: 0 }, [
                                createVNode("section", { class: "h-1/2 overflow-hidden bg-gray-50 overflow-hidden py-12" }, [
                                  createVNode("div", { class: "relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" }, [
                                    (openBlock(), createBlock("svg", {
                                      class: "absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2",
                                      width: "404",
                                      height: "404",
                                      fill: "none",
                                      viewBox: "0 0 404 404",
                                      role: "img",
                                      "aria-labelledby": "svg-nmrxiv"
                                    }, [
                                      createVNode("title", { id: "svg-nmrxiv" }, " nmrxiv "),
                                      createVNode("defs", null, [
                                        createVNode("pattern", {
                                          id: "ad119f34-7694-4c31-947f-5c9d249b21f3",
                                          x: "0",
                                          y: "0",
                                          width: "20",
                                          height: "20",
                                          patternUnits: "userSpaceOnUse"
                                        }, [
                                          createVNode("rect", {
                                            x: "0",
                                            y: "0",
                                            width: "4",
                                            height: "4",
                                            class: "text-gray-200",
                                            fill: "currentColor"
                                          })
                                        ])
                                      ]),
                                      createVNode("rect", {
                                        width: "404",
                                        height: "404",
                                        fill: "url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                                      })
                                    ])),
                                    createVNode("div", { class: "relative" }, [
                                      createVNode("div", { class: "flex justify-center" }, [
                                        createVNode(_component_jet_application_logo, { class: "block h-14 w-auto" })
                                      ]),
                                      createVNode("blockquote", { class: "mt-10" }, [
                                        createVNode("div", { class: "max-w-3xl mx-auto text-center text-xl leading-9 font-medium text-gray-900" }, [
                                          createVNode("p", { class: "mt-3 text-lg" }, [
                                            createTextVNode(" Welcome! "),
                                            createVNode("span", { class: "text-xl" }, [
                                              createTextVNode("nmr"),
                                              createVNode("b", null, "Xiv")
                                            ]),
                                            createTextVNode(" support scientists in their efforts to collect, store, process, analyse, disclose and re-use Nuclear Magnetic Resonance Spectroscopy research data. Ready to get started? ")
                                          ])
                                        ])
                                      ])
                                    ])
                                  ])
                                ])
                              ])) : createCommentVNode("", true),
                              $options.currentStep == 2 ? (openBlock(), createBlock("div", { key: 1 }, [
                                createVNode("section", { class: "h-1/2 overflow-hidden py-12" }, [
                                  createVNode("div", { class: "relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" }, [
                                    (openBlock(), createBlock("svg", {
                                      class: "absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2",
                                      width: "404",
                                      height: "404",
                                      fill: "none",
                                      viewBox: "0 0 404 404",
                                      role: "img",
                                      "aria-labelledby": "svg-nmrxiv"
                                    }, [
                                      createVNode("title", { id: "svg-nmrxiv" }, " nmrxiv "),
                                      createVNode("defs", null, [
                                        createVNode("pattern", {
                                          id: "ad119f34-7694-4c31-947f-5c9d249b21f3",
                                          x: "0",
                                          y: "0",
                                          width: "20",
                                          height: "20",
                                          patternUnits: "userSpaceOnUse"
                                        }, [
                                          createVNode("rect", {
                                            x: "0",
                                            y: "0",
                                            width: "4",
                                            height: "4",
                                            class: "text-gray-200",
                                            fill: "currentColor"
                                          })
                                        ])
                                      ]),
                                      createVNode("rect", {
                                        width: "404",
                                        height: "404",
                                        fill: "url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                                      })
                                    ])),
                                    createVNode("div", { class: "relative text-center" }, [
                                      createVNode("blockquote", { class: "mt-10" }, [
                                        createVNode("div", { class: "max-w-3xl mx-auto text-center text-xl leading-9 font-medium text-gray-900" }, [
                                          createVNode("p", { class: "mt-3 text-lg" }, [
                                            createVNode("span", { class: "text-xl" }, [
                                              createTextVNode("nmr"),
                                              createVNode("b", null, "Xiv")
                                            ]),
                                            createTextVNode(" as a part of "),
                                            createVNode("a", {
                                              class: "text-indigo-600 font-bold",
                                              href: "https://nfdi4chem.de",
                                              target: "_blank"
                                            }, "NFDI4Chem"),
                                            createTextVNode(", focuses on the implementation and adaptation of existing and new infrastructure, necessary to capture data early in the life cycle​ and to further manage, analyse and store associated information. Checkout "),
                                            createVNode("a", {
                                              class: "text-indigo-600",
                                              target: "_blank",
                                              href: "https://www.chemotion.net/chemotionsaurus/index.html"
                                            }, "Chemotion"),
                                            createTextVNode(" (Electronic Lab Notebook) or "),
                                            createVNode("a", {
                                              href: "https://github.com/NFDI4Chem/nmrxiv-cli",
                                              class: "text-indigo-600",
                                              target: "_blank"
                                            }, "nmrXiv CLI "),
                                            createTextVNode("(Command Line Interface) for a seamless integration into your Lab infrastructure. ")
                                          ])
                                        ])
                                      ])
                                    ])
                                  ])
                                ])
                              ])) : createCommentVNode("", true),
                              $options.currentStep == 3 ? (openBlock(), createBlock("div", { key: 2 }, [
                                createVNode("section", { class: "h-1/2 overflow-hidden py-12" }, [
                                  createVNode("div", { class: "relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" }, [
                                    (openBlock(), createBlock("svg", {
                                      class: "absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2",
                                      width: "404",
                                      height: "404",
                                      fill: "none",
                                      viewBox: "0 0 404 404",
                                      role: "img",
                                      "aria-labelledby": "svg-nmrxiv"
                                    }, [
                                      createVNode("title", { id: "svg-nmrxiv" }, " nmrxiv "),
                                      createVNode("defs", null, [
                                        createVNode("pattern", {
                                          id: "ad119f34-7694-4c31-947f-5c9d249b21f3",
                                          x: "0",
                                          y: "0",
                                          width: "20",
                                          height: "20",
                                          patternUnits: "userSpaceOnUse"
                                        }, [
                                          createVNode("rect", {
                                            x: "0",
                                            y: "0",
                                            width: "4",
                                            height: "4",
                                            class: "text-gray-200",
                                            fill: "currentColor"
                                          })
                                        ])
                                      ]),
                                      createVNode("rect", {
                                        width: "404",
                                        height: "404",
                                        fill: "url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                                      })
                                    ])),
                                    createVNode("div", { class: "relative text-center" }, [
                                      createVNode("p", { class: "mt-3 text-lg" }, [
                                        createVNode("span", { class: "text-xl" }, [
                                          createTextVNode("nmr"),
                                          createVNode("b", null, "Xiv")
                                        ]),
                                        createTextVNode(" is 100% open source, so you’re free to dig through the source to see exactly how it works. See something that needs to be improved? Just send us a request."),
                                        createVNode("br"),
                                        createVNode("small", null, [
                                          createTextVNode("Chat bubble is on the bottom left corner or email us at "),
                                          createVNode("a", { href: $options.mailTo }, toDisplayString($options.mailFromAddress), 9, ["href"])
                                        ])
                                      ]),
                                      createVNode("div", { class: "mt-8" }, [
                                        createVNode("div", { class: "inline-flex" }, [
                                          createVNode("a", {
                                            target: "_blank",
                                            href: "https://github.com/NFDI4Chem/nmrxiv",
                                            class: "inline-flex mr-4 items-center justify-center px-5 py-3 border text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                                          }, [
                                            createTextVNode(" Source code "),
                                            createVNode(_component_ArrowTopRightOnSquareIcon, {
                                              class: "-mr-1 ml-3 h-5 w-5 text-gray-400",
                                              "aria-hidden": "true"
                                            })
                                          ]),
                                          createVNode("a", {
                                            target: "_blank",
                                            href: "https://docs.nmrxiv.org",
                                            class: "inline-flex items-center justify-center px-5 py-3 border text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                                          }, [
                                            createTextVNode(" Visit the docs "),
                                            createVNode(_component_ArrowTopRightOnSquareIcon, {
                                              class: "-mr-1 ml-3 h-5 w-5 text-gray-400",
                                              "aria-hidden": "true"
                                            })
                                          ])
                                        ])
                                      ])
                                    ])
                                  ])
                                ])
                              ])) : createCommentVNode("", true),
                              $options.currentStep == 4 ? (openBlock(), createBlock("div", { key: 3 }, [
                                createVNode("div", { class: "max-w-7xl mx-auto text-center py-12 px-4 sm:px-6 lg:py-16 lg:px-8" }, [
                                  createVNode("h2", { class: "text-base font-semibold uppercase tracking-wider" }, " All set to get started! "),
                                  createVNode("p", { class: "my-2 font-bold tracking-tight sm:text-2xl" }, " Start uploading your spectral data today. "),
                                  createVNode("p", null, ' Click "Get started" and upload your data or if you want a guided tour of the dashboard press the "Dashboard Tour" button below. '),
                                  createVNode("div", { class: "mt-8 flex justify-center" }, [
                                    createVNode("div", { class: "inline-flex rounded-md shadow" }, [
                                      createVNode("a", {
                                        refs: "next",
                                        class: "cursor-pointer inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700",
                                        onClick: $options.onboardingComplete
                                      }, " Get started ", 8, ["onClick"])
                                    ])
                                  ])
                                ])
                              ])) : createCommentVNode("", true),
                              createVNode("div", { class: "grid grid-cols-4 gap-4" }, [
                                createVNode("div", { class: "p-4 py-4" }, [
                                  $options.currentStep != 1 ? (openBlock(), createBlock("button", {
                                    key: 0,
                                    type: "button",
                                    class: "py-2 px-3 border rounded-md",
                                    onClick: ($event) => $options.selectStep($options.currentStep - 1)
                                  }, [
                                    createVNode("div", { class: "flex flex-row align-middle" }, [
                                      (openBlock(), createBlock("svg", {
                                        class: "w-5 mr-1",
                                        fill: "currentColor",
                                        viewBox: "0 0 20 20",
                                        xmlns: "http://www.w3.org/2000/svg"
                                      }, [
                                        createVNode("path", {
                                          "fill-rule": "evenodd",
                                          d: "M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z",
                                          "clip-rule": "evenodd"
                                        })
                                      ])),
                                      createVNode("p", { class: "ml-2" }, "Prev")
                                    ])
                                  ], 8, ["onClick"])) : (openBlock(), createBlock("button", {
                                    key: 1,
                                    type: "button",
                                    class: "py-2 px-3 border rounded-md",
                                    onClick: $options.onboardingComplete
                                  }, [
                                    createVNode("div", { class: "flex flex-row align-middle" }, [
                                      createVNode("p", { class: "mx-2" }, "Skip")
                                    ])
                                  ], 8, ["onClick"]))
                                ]),
                                createVNode("div", { class: "col-span-2" }, [
                                  createVNode("nav", {
                                    class: "flex items-center justify-center p-4 py-6",
                                    "aria-label": "Progress"
                                  }, [
                                    createVNode("p", { class: "text-sm font-medium" }, " Step " + toDisplayString($data.steps.findIndex(
                                      (step) => step.status === "current"
                                    ) + 1) + " of " + toDisplayString($data.steps.length), 1),
                                    createVNode("ol", {
                                      role: "list",
                                      class: "ml-8 flex items-center space-x-5"
                                    }, [
                                      (openBlock(true), createBlock(Fragment, null, renderList($data.steps, (step) => {
                                        return openBlock(), createBlock("li", {
                                          key: step.name
                                        }, [
                                          createVNode("a", {
                                            class: "cursor-pointer",
                                            onClick: ($event) => $options.selectStep(step.id)
                                          }, [
                                            step.status === "complete" ? (openBlock(), createBlock("span", {
                                              key: 0,
                                              href: step.href,
                                              class: "block w-2.5 h-2.5 bg-indigo-600 rounded-full hover:bg-indigo-600"
                                            }, [
                                              createVNode("span", { class: "sr-only" }, toDisplayString(step.name), 1)
                                            ], 8, ["href"])) : step.status === "current" ? (openBlock(), createBlock("span", {
                                              key: 1,
                                              href: step.href,
                                              class: "relative flex items-center justify-center",
                                              "aria-current": "step"
                                            }, [
                                              createVNode("span", {
                                                class: "absolute w-5 h-5 p-px flex",
                                                "aria-hidden": "true"
                                              }, [
                                                createVNode("span", { class: "w-full h-full rounded-full bg-indigo-200" })
                                              ]),
                                              createVNode("span", {
                                                class: "relative block w-2.5 h-2.5 bg-indigo-600 rounded-full",
                                                "aria-hidden": "true"
                                              }),
                                              createVNode("span", { class: "sr-only" }, toDisplayString(step.name), 1)
                                            ], 8, ["href"])) : (openBlock(), createBlock("span", {
                                              key: 2,
                                              href: step.href,
                                              class: "block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400"
                                            }, [
                                              createVNode("span", { class: "sr-only" }, toDisplayString(step.name), 1)
                                            ], 8, ["href"]))
                                          ], 8, ["onClick"])
                                        ]);
                                      }), 128))
                                    ])
                                  ])
                                ]),
                                createVNode("div", { class: "p-4 py-4 text-right" }, [
                                  $options.currentStep < $data.steps.length ? (openBlock(), createBlock("div", { key: 0 }, [
                                    $options.currentStep == 1 ? (openBlock(), createBlock("div", { key: 0 }, [
                                      createVNode("button", {
                                        type: "button",
                                        class: "py-2 px-3 bg-indigo-600 text-white rounded-md",
                                        autofocus: "",
                                        onClick: ($event) => $options.selectStep($options.currentStep + 1)
                                      }, [
                                        createVNode("div", { class: "flex flex-row align-middle" }, [
                                          createVNode("span", { class: "mr-1" }, "Let's go"),
                                          (openBlock(), createBlock("svg", {
                                            class: "w-5 ml-2",
                                            fill: "currentColor",
                                            viewBox: "0 0 20 20",
                                            xmlns: "http://www.w3.org/2000/svg"
                                          }, [
                                            createVNode("path", {
                                              "fill-rule": "evenodd",
                                              d: "M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z",
                                              "clip-rule": "evenodd"
                                            })
                                          ]))
                                        ])
                                      ], 8, ["onClick"])
                                    ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                      createVNode("button", {
                                        ref: "next",
                                        type: "button",
                                        class: "py-2 px-3 bg-indigo-600 text-white rounded-md",
                                        autofocus: "",
                                        onClick: ($event) => $options.selectStep($options.currentStep + 1)
                                      }, [
                                        createVNode("div", { class: "flex flex-row align-middle" }, [
                                          createVNode("span", { class: "mr-1" }, "Next"),
                                          (openBlock(), createBlock("svg", {
                                            class: "w-5 ml-2",
                                            fill: "currentColor",
                                            viewBox: "0 0 20 20",
                                            xmlns: "http://www.w3.org/2000/svg"
                                          }, [
                                            createVNode("path", {
                                              "fill-rule": "evenodd",
                                              d: "M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z",
                                              "clip-rule": "evenodd"
                                            })
                                          ]))
                                        ])
                                      ], 8, ["onClick"])
                                    ]))
                                  ])) : createCommentVNode("", true)
                                ])
                              ])
                            ])
                          ];
                        }
                      }),
                      _: 1
                    }, _parent4, _scopeId3));
                  } else {
                    return [
                      createVNode(_component_DialogPanel, { class: "mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all" }, {
                        default: withCtx(() => [
                          createVNode("div", null, [
                            $options.currentStep == 1 ? (openBlock(), createBlock("div", { key: 0 }, [
                              createVNode("section", { class: "h-1/2 overflow-hidden bg-gray-50 overflow-hidden py-12" }, [
                                createVNode("div", { class: "relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" }, [
                                  (openBlock(), createBlock("svg", {
                                    class: "absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2",
                                    width: "404",
                                    height: "404",
                                    fill: "none",
                                    viewBox: "0 0 404 404",
                                    role: "img",
                                    "aria-labelledby": "svg-nmrxiv"
                                  }, [
                                    createVNode("title", { id: "svg-nmrxiv" }, " nmrxiv "),
                                    createVNode("defs", null, [
                                      createVNode("pattern", {
                                        id: "ad119f34-7694-4c31-947f-5c9d249b21f3",
                                        x: "0",
                                        y: "0",
                                        width: "20",
                                        height: "20",
                                        patternUnits: "userSpaceOnUse"
                                      }, [
                                        createVNode("rect", {
                                          x: "0",
                                          y: "0",
                                          width: "4",
                                          height: "4",
                                          class: "text-gray-200",
                                          fill: "currentColor"
                                        })
                                      ])
                                    ]),
                                    createVNode("rect", {
                                      width: "404",
                                      height: "404",
                                      fill: "url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                                    })
                                  ])),
                                  createVNode("div", { class: "relative" }, [
                                    createVNode("div", { class: "flex justify-center" }, [
                                      createVNode(_component_jet_application_logo, { class: "block h-14 w-auto" })
                                    ]),
                                    createVNode("blockquote", { class: "mt-10" }, [
                                      createVNode("div", { class: "max-w-3xl mx-auto text-center text-xl leading-9 font-medium text-gray-900" }, [
                                        createVNode("p", { class: "mt-3 text-lg" }, [
                                          createTextVNode(" Welcome! "),
                                          createVNode("span", { class: "text-xl" }, [
                                            createTextVNode("nmr"),
                                            createVNode("b", null, "Xiv")
                                          ]),
                                          createTextVNode(" support scientists in their efforts to collect, store, process, analyse, disclose and re-use Nuclear Magnetic Resonance Spectroscopy research data. Ready to get started? ")
                                        ])
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ])) : createCommentVNode("", true),
                            $options.currentStep == 2 ? (openBlock(), createBlock("div", { key: 1 }, [
                              createVNode("section", { class: "h-1/2 overflow-hidden py-12" }, [
                                createVNode("div", { class: "relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" }, [
                                  (openBlock(), createBlock("svg", {
                                    class: "absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2",
                                    width: "404",
                                    height: "404",
                                    fill: "none",
                                    viewBox: "0 0 404 404",
                                    role: "img",
                                    "aria-labelledby": "svg-nmrxiv"
                                  }, [
                                    createVNode("title", { id: "svg-nmrxiv" }, " nmrxiv "),
                                    createVNode("defs", null, [
                                      createVNode("pattern", {
                                        id: "ad119f34-7694-4c31-947f-5c9d249b21f3",
                                        x: "0",
                                        y: "0",
                                        width: "20",
                                        height: "20",
                                        patternUnits: "userSpaceOnUse"
                                      }, [
                                        createVNode("rect", {
                                          x: "0",
                                          y: "0",
                                          width: "4",
                                          height: "4",
                                          class: "text-gray-200",
                                          fill: "currentColor"
                                        })
                                      ])
                                    ]),
                                    createVNode("rect", {
                                      width: "404",
                                      height: "404",
                                      fill: "url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                                    })
                                  ])),
                                  createVNode("div", { class: "relative text-center" }, [
                                    createVNode("blockquote", { class: "mt-10" }, [
                                      createVNode("div", { class: "max-w-3xl mx-auto text-center text-xl leading-9 font-medium text-gray-900" }, [
                                        createVNode("p", { class: "mt-3 text-lg" }, [
                                          createVNode("span", { class: "text-xl" }, [
                                            createTextVNode("nmr"),
                                            createVNode("b", null, "Xiv")
                                          ]),
                                          createTextVNode(" as a part of "),
                                          createVNode("a", {
                                            class: "text-indigo-600 font-bold",
                                            href: "https://nfdi4chem.de",
                                            target: "_blank"
                                          }, "NFDI4Chem"),
                                          createTextVNode(", focuses on the implementation and adaptation of existing and new infrastructure, necessary to capture data early in the life cycle​ and to further manage, analyse and store associated information. Checkout "),
                                          createVNode("a", {
                                            class: "text-indigo-600",
                                            target: "_blank",
                                            href: "https://www.chemotion.net/chemotionsaurus/index.html"
                                          }, "Chemotion"),
                                          createTextVNode(" (Electronic Lab Notebook) or "),
                                          createVNode("a", {
                                            href: "https://github.com/NFDI4Chem/nmrxiv-cli",
                                            class: "text-indigo-600",
                                            target: "_blank"
                                          }, "nmrXiv CLI "),
                                          createTextVNode("(Command Line Interface) for a seamless integration into your Lab infrastructure. ")
                                        ])
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ])) : createCommentVNode("", true),
                            $options.currentStep == 3 ? (openBlock(), createBlock("div", { key: 2 }, [
                              createVNode("section", { class: "h-1/2 overflow-hidden py-12" }, [
                                createVNode("div", { class: "relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" }, [
                                  (openBlock(), createBlock("svg", {
                                    class: "absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2",
                                    width: "404",
                                    height: "404",
                                    fill: "none",
                                    viewBox: "0 0 404 404",
                                    role: "img",
                                    "aria-labelledby": "svg-nmrxiv"
                                  }, [
                                    createVNode("title", { id: "svg-nmrxiv" }, " nmrxiv "),
                                    createVNode("defs", null, [
                                      createVNode("pattern", {
                                        id: "ad119f34-7694-4c31-947f-5c9d249b21f3",
                                        x: "0",
                                        y: "0",
                                        width: "20",
                                        height: "20",
                                        patternUnits: "userSpaceOnUse"
                                      }, [
                                        createVNode("rect", {
                                          x: "0",
                                          y: "0",
                                          width: "4",
                                          height: "4",
                                          class: "text-gray-200",
                                          fill: "currentColor"
                                        })
                                      ])
                                    ]),
                                    createVNode("rect", {
                                      width: "404",
                                      height: "404",
                                      fill: "url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                                    })
                                  ])),
                                  createVNode("div", { class: "relative text-center" }, [
                                    createVNode("p", { class: "mt-3 text-lg" }, [
                                      createVNode("span", { class: "text-xl" }, [
                                        createTextVNode("nmr"),
                                        createVNode("b", null, "Xiv")
                                      ]),
                                      createTextVNode(" is 100% open source, so you’re free to dig through the source to see exactly how it works. See something that needs to be improved? Just send us a request."),
                                      createVNode("br"),
                                      createVNode("small", null, [
                                        createTextVNode("Chat bubble is on the bottom left corner or email us at "),
                                        createVNode("a", { href: $options.mailTo }, toDisplayString($options.mailFromAddress), 9, ["href"])
                                      ])
                                    ]),
                                    createVNode("div", { class: "mt-8" }, [
                                      createVNode("div", { class: "inline-flex" }, [
                                        createVNode("a", {
                                          target: "_blank",
                                          href: "https://github.com/NFDI4Chem/nmrxiv",
                                          class: "inline-flex mr-4 items-center justify-center px-5 py-3 border text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                                        }, [
                                          createTextVNode(" Source code "),
                                          createVNode(_component_ArrowTopRightOnSquareIcon, {
                                            class: "-mr-1 ml-3 h-5 w-5 text-gray-400",
                                            "aria-hidden": "true"
                                          })
                                        ]),
                                        createVNode("a", {
                                          target: "_blank",
                                          href: "https://docs.nmrxiv.org",
                                          class: "inline-flex items-center justify-center px-5 py-3 border text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                                        }, [
                                          createTextVNode(" Visit the docs "),
                                          createVNode(_component_ArrowTopRightOnSquareIcon, {
                                            class: "-mr-1 ml-3 h-5 w-5 text-gray-400",
                                            "aria-hidden": "true"
                                          })
                                        ])
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ])) : createCommentVNode("", true),
                            $options.currentStep == 4 ? (openBlock(), createBlock("div", { key: 3 }, [
                              createVNode("div", { class: "max-w-7xl mx-auto text-center py-12 px-4 sm:px-6 lg:py-16 lg:px-8" }, [
                                createVNode("h2", { class: "text-base font-semibold uppercase tracking-wider" }, " All set to get started! "),
                                createVNode("p", { class: "my-2 font-bold tracking-tight sm:text-2xl" }, " Start uploading your spectral data today. "),
                                createVNode("p", null, ' Click "Get started" and upload your data or if you want a guided tour of the dashboard press the "Dashboard Tour" button below. '),
                                createVNode("div", { class: "mt-8 flex justify-center" }, [
                                  createVNode("div", { class: "inline-flex rounded-md shadow" }, [
                                    createVNode("a", {
                                      refs: "next",
                                      class: "cursor-pointer inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700",
                                      onClick: $options.onboardingComplete
                                    }, " Get started ", 8, ["onClick"])
                                  ])
                                ])
                              ])
                            ])) : createCommentVNode("", true),
                            createVNode("div", { class: "grid grid-cols-4 gap-4" }, [
                              createVNode("div", { class: "p-4 py-4" }, [
                                $options.currentStep != 1 ? (openBlock(), createBlock("button", {
                                  key: 0,
                                  type: "button",
                                  class: "py-2 px-3 border rounded-md",
                                  onClick: ($event) => $options.selectStep($options.currentStep - 1)
                                }, [
                                  createVNode("div", { class: "flex flex-row align-middle" }, [
                                    (openBlock(), createBlock("svg", {
                                      class: "w-5 mr-1",
                                      fill: "currentColor",
                                      viewBox: "0 0 20 20",
                                      xmlns: "http://www.w3.org/2000/svg"
                                    }, [
                                      createVNode("path", {
                                        "fill-rule": "evenodd",
                                        d: "M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z",
                                        "clip-rule": "evenodd"
                                      })
                                    ])),
                                    createVNode("p", { class: "ml-2" }, "Prev")
                                  ])
                                ], 8, ["onClick"])) : (openBlock(), createBlock("button", {
                                  key: 1,
                                  type: "button",
                                  class: "py-2 px-3 border rounded-md",
                                  onClick: $options.onboardingComplete
                                }, [
                                  createVNode("div", { class: "flex flex-row align-middle" }, [
                                    createVNode("p", { class: "mx-2" }, "Skip")
                                  ])
                                ], 8, ["onClick"]))
                              ]),
                              createVNode("div", { class: "col-span-2" }, [
                                createVNode("nav", {
                                  class: "flex items-center justify-center p-4 py-6",
                                  "aria-label": "Progress"
                                }, [
                                  createVNode("p", { class: "text-sm font-medium" }, " Step " + toDisplayString($data.steps.findIndex(
                                    (step) => step.status === "current"
                                  ) + 1) + " of " + toDisplayString($data.steps.length), 1),
                                  createVNode("ol", {
                                    role: "list",
                                    class: "ml-8 flex items-center space-x-5"
                                  }, [
                                    (openBlock(true), createBlock(Fragment, null, renderList($data.steps, (step) => {
                                      return openBlock(), createBlock("li", {
                                        key: step.name
                                      }, [
                                        createVNode("a", {
                                          class: "cursor-pointer",
                                          onClick: ($event) => $options.selectStep(step.id)
                                        }, [
                                          step.status === "complete" ? (openBlock(), createBlock("span", {
                                            key: 0,
                                            href: step.href,
                                            class: "block w-2.5 h-2.5 bg-indigo-600 rounded-full hover:bg-indigo-600"
                                          }, [
                                            createVNode("span", { class: "sr-only" }, toDisplayString(step.name), 1)
                                          ], 8, ["href"])) : step.status === "current" ? (openBlock(), createBlock("span", {
                                            key: 1,
                                            href: step.href,
                                            class: "relative flex items-center justify-center",
                                            "aria-current": "step"
                                          }, [
                                            createVNode("span", {
                                              class: "absolute w-5 h-5 p-px flex",
                                              "aria-hidden": "true"
                                            }, [
                                              createVNode("span", { class: "w-full h-full rounded-full bg-indigo-200" })
                                            ]),
                                            createVNode("span", {
                                              class: "relative block w-2.5 h-2.5 bg-indigo-600 rounded-full",
                                              "aria-hidden": "true"
                                            }),
                                            createVNode("span", { class: "sr-only" }, toDisplayString(step.name), 1)
                                          ], 8, ["href"])) : (openBlock(), createBlock("span", {
                                            key: 2,
                                            href: step.href,
                                            class: "block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400"
                                          }, [
                                            createVNode("span", { class: "sr-only" }, toDisplayString(step.name), 1)
                                          ], 8, ["href"]))
                                        ], 8, ["onClick"])
                                      ]);
                                    }), 128))
                                  ])
                                ])
                              ]),
                              createVNode("div", { class: "p-4 py-4 text-right" }, [
                                $options.currentStep < $data.steps.length ? (openBlock(), createBlock("div", { key: 0 }, [
                                  $options.currentStep == 1 ? (openBlock(), createBlock("div", { key: 0 }, [
                                    createVNode("button", {
                                      type: "button",
                                      class: "py-2 px-3 bg-indigo-600 text-white rounded-md",
                                      autofocus: "",
                                      onClick: ($event) => $options.selectStep($options.currentStep + 1)
                                    }, [
                                      createVNode("div", { class: "flex flex-row align-middle" }, [
                                        createVNode("span", { class: "mr-1" }, "Let's go"),
                                        (openBlock(), createBlock("svg", {
                                          class: "w-5 ml-2",
                                          fill: "currentColor",
                                          viewBox: "0 0 20 20",
                                          xmlns: "http://www.w3.org/2000/svg"
                                        }, [
                                          createVNode("path", {
                                            "fill-rule": "evenodd",
                                            d: "M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z",
                                            "clip-rule": "evenodd"
                                          })
                                        ]))
                                      ])
                                    ], 8, ["onClick"])
                                  ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                    createVNode("button", {
                                      ref: "next",
                                      type: "button",
                                      class: "py-2 px-3 bg-indigo-600 text-white rounded-md",
                                      autofocus: "",
                                      onClick: ($event) => $options.selectStep($options.currentStep + 1)
                                    }, [
                                      createVNode("div", { class: "flex flex-row align-middle" }, [
                                        createVNode("span", { class: "mr-1" }, "Next"),
                                        (openBlock(), createBlock("svg", {
                                          class: "w-5 ml-2",
                                          fill: "currentColor",
                                          viewBox: "0 0 20 20",
                                          xmlns: "http://www.w3.org/2000/svg"
                                        }, [
                                          createVNode("path", {
                                            "fill-rule": "evenodd",
                                            d: "M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z",
                                            "clip-rule": "evenodd"
                                          })
                                        ]))
                                      ])
                                    ], 8, ["onClick"])
                                  ]))
                                ])) : createCommentVNode("", true)
                              ])
                            ])
                          ])
                        ]),
                        _: 1
                      })
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`</div>`);
            } else {
              return [
                createVNode(_component_TransitionChild, {
                  as: "template",
                  enter: "ease-out duration-300",
                  "enter-from": "opacity-0",
                  "enter-to": "opacity-100",
                  leave: "ease-in duration-200",
                  "leave-from": "opacity-100",
                  "leave-to": "opacity-0"
                }, {
                  default: withCtx(() => [
                    createVNode("div", { class: "fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" })
                  ]),
                  _: 1
                }),
                createVNode("div", { class: "fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20" }, [
                  createVNode(_component_TransitionChild, {
                    as: "template",
                    enter: "ease-out duration-300",
                    "enter-from": "opacity-0 scale-95",
                    "enter-to": "opacity-100 scale-100",
                    leave: "ease-in duration-200",
                    "leave-from": "opacity-100 scale-100",
                    "leave-to": "opacity-0 scale-95"
                  }, {
                    default: withCtx(() => [
                      createVNode(_component_DialogPanel, { class: "mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all" }, {
                        default: withCtx(() => [
                          createVNode("div", null, [
                            $options.currentStep == 1 ? (openBlock(), createBlock("div", { key: 0 }, [
                              createVNode("section", { class: "h-1/2 overflow-hidden bg-gray-50 overflow-hidden py-12" }, [
                                createVNode("div", { class: "relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" }, [
                                  (openBlock(), createBlock("svg", {
                                    class: "absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2",
                                    width: "404",
                                    height: "404",
                                    fill: "none",
                                    viewBox: "0 0 404 404",
                                    role: "img",
                                    "aria-labelledby": "svg-nmrxiv"
                                  }, [
                                    createVNode("title", { id: "svg-nmrxiv" }, " nmrxiv "),
                                    createVNode("defs", null, [
                                      createVNode("pattern", {
                                        id: "ad119f34-7694-4c31-947f-5c9d249b21f3",
                                        x: "0",
                                        y: "0",
                                        width: "20",
                                        height: "20",
                                        patternUnits: "userSpaceOnUse"
                                      }, [
                                        createVNode("rect", {
                                          x: "0",
                                          y: "0",
                                          width: "4",
                                          height: "4",
                                          class: "text-gray-200",
                                          fill: "currentColor"
                                        })
                                      ])
                                    ]),
                                    createVNode("rect", {
                                      width: "404",
                                      height: "404",
                                      fill: "url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                                    })
                                  ])),
                                  createVNode("div", { class: "relative" }, [
                                    createVNode("div", { class: "flex justify-center" }, [
                                      createVNode(_component_jet_application_logo, { class: "block h-14 w-auto" })
                                    ]),
                                    createVNode("blockquote", { class: "mt-10" }, [
                                      createVNode("div", { class: "max-w-3xl mx-auto text-center text-xl leading-9 font-medium text-gray-900" }, [
                                        createVNode("p", { class: "mt-3 text-lg" }, [
                                          createTextVNode(" Welcome! "),
                                          createVNode("span", { class: "text-xl" }, [
                                            createTextVNode("nmr"),
                                            createVNode("b", null, "Xiv")
                                          ]),
                                          createTextVNode(" support scientists in their efforts to collect, store, process, analyse, disclose and re-use Nuclear Magnetic Resonance Spectroscopy research data. Ready to get started? ")
                                        ])
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ])) : createCommentVNode("", true),
                            $options.currentStep == 2 ? (openBlock(), createBlock("div", { key: 1 }, [
                              createVNode("section", { class: "h-1/2 overflow-hidden py-12" }, [
                                createVNode("div", { class: "relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" }, [
                                  (openBlock(), createBlock("svg", {
                                    class: "absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2",
                                    width: "404",
                                    height: "404",
                                    fill: "none",
                                    viewBox: "0 0 404 404",
                                    role: "img",
                                    "aria-labelledby": "svg-nmrxiv"
                                  }, [
                                    createVNode("title", { id: "svg-nmrxiv" }, " nmrxiv "),
                                    createVNode("defs", null, [
                                      createVNode("pattern", {
                                        id: "ad119f34-7694-4c31-947f-5c9d249b21f3",
                                        x: "0",
                                        y: "0",
                                        width: "20",
                                        height: "20",
                                        patternUnits: "userSpaceOnUse"
                                      }, [
                                        createVNode("rect", {
                                          x: "0",
                                          y: "0",
                                          width: "4",
                                          height: "4",
                                          class: "text-gray-200",
                                          fill: "currentColor"
                                        })
                                      ])
                                    ]),
                                    createVNode("rect", {
                                      width: "404",
                                      height: "404",
                                      fill: "url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                                    })
                                  ])),
                                  createVNode("div", { class: "relative text-center" }, [
                                    createVNode("blockquote", { class: "mt-10" }, [
                                      createVNode("div", { class: "max-w-3xl mx-auto text-center text-xl leading-9 font-medium text-gray-900" }, [
                                        createVNode("p", { class: "mt-3 text-lg" }, [
                                          createVNode("span", { class: "text-xl" }, [
                                            createTextVNode("nmr"),
                                            createVNode("b", null, "Xiv")
                                          ]),
                                          createTextVNode(" as a part of "),
                                          createVNode("a", {
                                            class: "text-indigo-600 font-bold",
                                            href: "https://nfdi4chem.de",
                                            target: "_blank"
                                          }, "NFDI4Chem"),
                                          createTextVNode(", focuses on the implementation and adaptation of existing and new infrastructure, necessary to capture data early in the life cycle​ and to further manage, analyse and store associated information. Checkout "),
                                          createVNode("a", {
                                            class: "text-indigo-600",
                                            target: "_blank",
                                            href: "https://www.chemotion.net/chemotionsaurus/index.html"
                                          }, "Chemotion"),
                                          createTextVNode(" (Electronic Lab Notebook) or "),
                                          createVNode("a", {
                                            href: "https://github.com/NFDI4Chem/nmrxiv-cli",
                                            class: "text-indigo-600",
                                            target: "_blank"
                                          }, "nmrXiv CLI "),
                                          createTextVNode("(Command Line Interface) for a seamless integration into your Lab infrastructure. ")
                                        ])
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ])) : createCommentVNode("", true),
                            $options.currentStep == 3 ? (openBlock(), createBlock("div", { key: 2 }, [
                              createVNode("section", { class: "h-1/2 overflow-hidden py-12" }, [
                                createVNode("div", { class: "relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" }, [
                                  (openBlock(), createBlock("svg", {
                                    class: "absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2",
                                    width: "404",
                                    height: "404",
                                    fill: "none",
                                    viewBox: "0 0 404 404",
                                    role: "img",
                                    "aria-labelledby": "svg-nmrxiv"
                                  }, [
                                    createVNode("title", { id: "svg-nmrxiv" }, " nmrxiv "),
                                    createVNode("defs", null, [
                                      createVNode("pattern", {
                                        id: "ad119f34-7694-4c31-947f-5c9d249b21f3",
                                        x: "0",
                                        y: "0",
                                        width: "20",
                                        height: "20",
                                        patternUnits: "userSpaceOnUse"
                                      }, [
                                        createVNode("rect", {
                                          x: "0",
                                          y: "0",
                                          width: "4",
                                          height: "4",
                                          class: "text-gray-200",
                                          fill: "currentColor"
                                        })
                                      ])
                                    ]),
                                    createVNode("rect", {
                                      width: "404",
                                      height: "404",
                                      fill: "url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                                    })
                                  ])),
                                  createVNode("div", { class: "relative text-center" }, [
                                    createVNode("p", { class: "mt-3 text-lg" }, [
                                      createVNode("span", { class: "text-xl" }, [
                                        createTextVNode("nmr"),
                                        createVNode("b", null, "Xiv")
                                      ]),
                                      createTextVNode(" is 100% open source, so you’re free to dig through the source to see exactly how it works. See something that needs to be improved? Just send us a request."),
                                      createVNode("br"),
                                      createVNode("small", null, [
                                        createTextVNode("Chat bubble is on the bottom left corner or email us at "),
                                        createVNode("a", { href: $options.mailTo }, toDisplayString($options.mailFromAddress), 9, ["href"])
                                      ])
                                    ]),
                                    createVNode("div", { class: "mt-8" }, [
                                      createVNode("div", { class: "inline-flex" }, [
                                        createVNode("a", {
                                          target: "_blank",
                                          href: "https://github.com/NFDI4Chem/nmrxiv",
                                          class: "inline-flex mr-4 items-center justify-center px-5 py-3 border text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                                        }, [
                                          createTextVNode(" Source code "),
                                          createVNode(_component_ArrowTopRightOnSquareIcon, {
                                            class: "-mr-1 ml-3 h-5 w-5 text-gray-400",
                                            "aria-hidden": "true"
                                          })
                                        ]),
                                        createVNode("a", {
                                          target: "_blank",
                                          href: "https://docs.nmrxiv.org",
                                          class: "inline-flex items-center justify-center px-5 py-3 border text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                                        }, [
                                          createTextVNode(" Visit the docs "),
                                          createVNode(_component_ArrowTopRightOnSquareIcon, {
                                            class: "-mr-1 ml-3 h-5 w-5 text-gray-400",
                                            "aria-hidden": "true"
                                          })
                                        ])
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ])) : createCommentVNode("", true),
                            $options.currentStep == 4 ? (openBlock(), createBlock("div", { key: 3 }, [
                              createVNode("div", { class: "max-w-7xl mx-auto text-center py-12 px-4 sm:px-6 lg:py-16 lg:px-8" }, [
                                createVNode("h2", { class: "text-base font-semibold uppercase tracking-wider" }, " All set to get started! "),
                                createVNode("p", { class: "my-2 font-bold tracking-tight sm:text-2xl" }, " Start uploading your spectral data today. "),
                                createVNode("p", null, ' Click "Get started" and upload your data or if you want a guided tour of the dashboard press the "Dashboard Tour" button below. '),
                                createVNode("div", { class: "mt-8 flex justify-center" }, [
                                  createVNode("div", { class: "inline-flex rounded-md shadow" }, [
                                    createVNode("a", {
                                      refs: "next",
                                      class: "cursor-pointer inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700",
                                      onClick: $options.onboardingComplete
                                    }, " Get started ", 8, ["onClick"])
                                  ])
                                ])
                              ])
                            ])) : createCommentVNode("", true),
                            createVNode("div", { class: "grid grid-cols-4 gap-4" }, [
                              createVNode("div", { class: "p-4 py-4" }, [
                                $options.currentStep != 1 ? (openBlock(), createBlock("button", {
                                  key: 0,
                                  type: "button",
                                  class: "py-2 px-3 border rounded-md",
                                  onClick: ($event) => $options.selectStep($options.currentStep - 1)
                                }, [
                                  createVNode("div", { class: "flex flex-row align-middle" }, [
                                    (openBlock(), createBlock("svg", {
                                      class: "w-5 mr-1",
                                      fill: "currentColor",
                                      viewBox: "0 0 20 20",
                                      xmlns: "http://www.w3.org/2000/svg"
                                    }, [
                                      createVNode("path", {
                                        "fill-rule": "evenodd",
                                        d: "M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z",
                                        "clip-rule": "evenodd"
                                      })
                                    ])),
                                    createVNode("p", { class: "ml-2" }, "Prev")
                                  ])
                                ], 8, ["onClick"])) : (openBlock(), createBlock("button", {
                                  key: 1,
                                  type: "button",
                                  class: "py-2 px-3 border rounded-md",
                                  onClick: $options.onboardingComplete
                                }, [
                                  createVNode("div", { class: "flex flex-row align-middle" }, [
                                    createVNode("p", { class: "mx-2" }, "Skip")
                                  ])
                                ], 8, ["onClick"]))
                              ]),
                              createVNode("div", { class: "col-span-2" }, [
                                createVNode("nav", {
                                  class: "flex items-center justify-center p-4 py-6",
                                  "aria-label": "Progress"
                                }, [
                                  createVNode("p", { class: "text-sm font-medium" }, " Step " + toDisplayString($data.steps.findIndex(
                                    (step) => step.status === "current"
                                  ) + 1) + " of " + toDisplayString($data.steps.length), 1),
                                  createVNode("ol", {
                                    role: "list",
                                    class: "ml-8 flex items-center space-x-5"
                                  }, [
                                    (openBlock(true), createBlock(Fragment, null, renderList($data.steps, (step) => {
                                      return openBlock(), createBlock("li", {
                                        key: step.name
                                      }, [
                                        createVNode("a", {
                                          class: "cursor-pointer",
                                          onClick: ($event) => $options.selectStep(step.id)
                                        }, [
                                          step.status === "complete" ? (openBlock(), createBlock("span", {
                                            key: 0,
                                            href: step.href,
                                            class: "block w-2.5 h-2.5 bg-indigo-600 rounded-full hover:bg-indigo-600"
                                          }, [
                                            createVNode("span", { class: "sr-only" }, toDisplayString(step.name), 1)
                                          ], 8, ["href"])) : step.status === "current" ? (openBlock(), createBlock("span", {
                                            key: 1,
                                            href: step.href,
                                            class: "relative flex items-center justify-center",
                                            "aria-current": "step"
                                          }, [
                                            createVNode("span", {
                                              class: "absolute w-5 h-5 p-px flex",
                                              "aria-hidden": "true"
                                            }, [
                                              createVNode("span", { class: "w-full h-full rounded-full bg-indigo-200" })
                                            ]),
                                            createVNode("span", {
                                              class: "relative block w-2.5 h-2.5 bg-indigo-600 rounded-full",
                                              "aria-hidden": "true"
                                            }),
                                            createVNode("span", { class: "sr-only" }, toDisplayString(step.name), 1)
                                          ], 8, ["href"])) : (openBlock(), createBlock("span", {
                                            key: 2,
                                            href: step.href,
                                            class: "block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400"
                                          }, [
                                            createVNode("span", { class: "sr-only" }, toDisplayString(step.name), 1)
                                          ], 8, ["href"]))
                                        ], 8, ["onClick"])
                                      ]);
                                    }), 128))
                                  ])
                                ])
                              ]),
                              createVNode("div", { class: "p-4 py-4 text-right" }, [
                                $options.currentStep < $data.steps.length ? (openBlock(), createBlock("div", { key: 0 }, [
                                  $options.currentStep == 1 ? (openBlock(), createBlock("div", { key: 0 }, [
                                    createVNode("button", {
                                      type: "button",
                                      class: "py-2 px-3 bg-indigo-600 text-white rounded-md",
                                      autofocus: "",
                                      onClick: ($event) => $options.selectStep($options.currentStep + 1)
                                    }, [
                                      createVNode("div", { class: "flex flex-row align-middle" }, [
                                        createVNode("span", { class: "mr-1" }, "Let's go"),
                                        (openBlock(), createBlock("svg", {
                                          class: "w-5 ml-2",
                                          fill: "currentColor",
                                          viewBox: "0 0 20 20",
                                          xmlns: "http://www.w3.org/2000/svg"
                                        }, [
                                          createVNode("path", {
                                            "fill-rule": "evenodd",
                                            d: "M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z",
                                            "clip-rule": "evenodd"
                                          })
                                        ]))
                                      ])
                                    ], 8, ["onClick"])
                                  ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                    createVNode("button", {
                                      ref: "next",
                                      type: "button",
                                      class: "py-2 px-3 bg-indigo-600 text-white rounded-md",
                                      autofocus: "",
                                      onClick: ($event) => $options.selectStep($options.currentStep + 1)
                                    }, [
                                      createVNode("div", { class: "flex flex-row align-middle" }, [
                                        createVNode("span", { class: "mr-1" }, "Next"),
                                        (openBlock(), createBlock("svg", {
                                          class: "w-5 ml-2",
                                          fill: "currentColor",
                                          viewBox: "0 0 20 20",
                                          xmlns: "http://www.w3.org/2000/svg"
                                        }, [
                                          createVNode("path", {
                                            "fill-rule": "evenodd",
                                            d: "M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z",
                                            "clip-rule": "evenodd"
                                          })
                                        ]))
                                      ])
                                    ], 8, ["onClick"])
                                  ]))
                                ])) : createCommentVNode("", true)
                              ])
                            ])
                          ])
                        ]),
                        _: 1
                      })
                    ]),
                    _: 1
                  })
                ])
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_Dialog, {
            as: "div",
            class: "relative z-10"
          }, {
            default: withCtx(() => [
              createVNode(_component_TransitionChild, {
                as: "template",
                enter: "ease-out duration-300",
                "enter-from": "opacity-0",
                "enter-to": "opacity-100",
                leave: "ease-in duration-200",
                "leave-from": "opacity-100",
                "leave-to": "opacity-0"
              }, {
                default: withCtx(() => [
                  createVNode("div", { class: "fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" })
                ]),
                _: 1
              }),
              createVNode("div", { class: "fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20" }, [
                createVNode(_component_TransitionChild, {
                  as: "template",
                  enter: "ease-out duration-300",
                  "enter-from": "opacity-0 scale-95",
                  "enter-to": "opacity-100 scale-100",
                  leave: "ease-in duration-200",
                  "leave-from": "opacity-100 scale-100",
                  "leave-to": "opacity-0 scale-95"
                }, {
                  default: withCtx(() => [
                    createVNode(_component_DialogPanel, { class: "mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all" }, {
                      default: withCtx(() => [
                        createVNode("div", null, [
                          $options.currentStep == 1 ? (openBlock(), createBlock("div", { key: 0 }, [
                            createVNode("section", { class: "h-1/2 overflow-hidden bg-gray-50 overflow-hidden py-12" }, [
                              createVNode("div", { class: "relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" }, [
                                (openBlock(), createBlock("svg", {
                                  class: "absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2",
                                  width: "404",
                                  height: "404",
                                  fill: "none",
                                  viewBox: "0 0 404 404",
                                  role: "img",
                                  "aria-labelledby": "svg-nmrxiv"
                                }, [
                                  createVNode("title", { id: "svg-nmrxiv" }, " nmrxiv "),
                                  createVNode("defs", null, [
                                    createVNode("pattern", {
                                      id: "ad119f34-7694-4c31-947f-5c9d249b21f3",
                                      x: "0",
                                      y: "0",
                                      width: "20",
                                      height: "20",
                                      patternUnits: "userSpaceOnUse"
                                    }, [
                                      createVNode("rect", {
                                        x: "0",
                                        y: "0",
                                        width: "4",
                                        height: "4",
                                        class: "text-gray-200",
                                        fill: "currentColor"
                                      })
                                    ])
                                  ]),
                                  createVNode("rect", {
                                    width: "404",
                                    height: "404",
                                    fill: "url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                                  })
                                ])),
                                createVNode("div", { class: "relative" }, [
                                  createVNode("div", { class: "flex justify-center" }, [
                                    createVNode(_component_jet_application_logo, { class: "block h-14 w-auto" })
                                  ]),
                                  createVNode("blockquote", { class: "mt-10" }, [
                                    createVNode("div", { class: "max-w-3xl mx-auto text-center text-xl leading-9 font-medium text-gray-900" }, [
                                      createVNode("p", { class: "mt-3 text-lg" }, [
                                        createTextVNode(" Welcome! "),
                                        createVNode("span", { class: "text-xl" }, [
                                          createTextVNode("nmr"),
                                          createVNode("b", null, "Xiv")
                                        ]),
                                        createTextVNode(" support scientists in their efforts to collect, store, process, analyse, disclose and re-use Nuclear Magnetic Resonance Spectroscopy research data. Ready to get started? ")
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ])
                          ])) : createCommentVNode("", true),
                          $options.currentStep == 2 ? (openBlock(), createBlock("div", { key: 1 }, [
                            createVNode("section", { class: "h-1/2 overflow-hidden py-12" }, [
                              createVNode("div", { class: "relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" }, [
                                (openBlock(), createBlock("svg", {
                                  class: "absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2",
                                  width: "404",
                                  height: "404",
                                  fill: "none",
                                  viewBox: "0 0 404 404",
                                  role: "img",
                                  "aria-labelledby": "svg-nmrxiv"
                                }, [
                                  createVNode("title", { id: "svg-nmrxiv" }, " nmrxiv "),
                                  createVNode("defs", null, [
                                    createVNode("pattern", {
                                      id: "ad119f34-7694-4c31-947f-5c9d249b21f3",
                                      x: "0",
                                      y: "0",
                                      width: "20",
                                      height: "20",
                                      patternUnits: "userSpaceOnUse"
                                    }, [
                                      createVNode("rect", {
                                        x: "0",
                                        y: "0",
                                        width: "4",
                                        height: "4",
                                        class: "text-gray-200",
                                        fill: "currentColor"
                                      })
                                    ])
                                  ]),
                                  createVNode("rect", {
                                    width: "404",
                                    height: "404",
                                    fill: "url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                                  })
                                ])),
                                createVNode("div", { class: "relative text-center" }, [
                                  createVNode("blockquote", { class: "mt-10" }, [
                                    createVNode("div", { class: "max-w-3xl mx-auto text-center text-xl leading-9 font-medium text-gray-900" }, [
                                      createVNode("p", { class: "mt-3 text-lg" }, [
                                        createVNode("span", { class: "text-xl" }, [
                                          createTextVNode("nmr"),
                                          createVNode("b", null, "Xiv")
                                        ]),
                                        createTextVNode(" as a part of "),
                                        createVNode("a", {
                                          class: "text-indigo-600 font-bold",
                                          href: "https://nfdi4chem.de",
                                          target: "_blank"
                                        }, "NFDI4Chem"),
                                        createTextVNode(", focuses on the implementation and adaptation of existing and new infrastructure, necessary to capture data early in the life cycle​ and to further manage, analyse and store associated information. Checkout "),
                                        createVNode("a", {
                                          class: "text-indigo-600",
                                          target: "_blank",
                                          href: "https://www.chemotion.net/chemotionsaurus/index.html"
                                        }, "Chemotion"),
                                        createTextVNode(" (Electronic Lab Notebook) or "),
                                        createVNode("a", {
                                          href: "https://github.com/NFDI4Chem/nmrxiv-cli",
                                          class: "text-indigo-600",
                                          target: "_blank"
                                        }, "nmrXiv CLI "),
                                        createTextVNode("(Command Line Interface) for a seamless integration into your Lab infrastructure. ")
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ])
                          ])) : createCommentVNode("", true),
                          $options.currentStep == 3 ? (openBlock(), createBlock("div", { key: 2 }, [
                            createVNode("section", { class: "h-1/2 overflow-hidden py-12" }, [
                              createVNode("div", { class: "relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" }, [
                                (openBlock(), createBlock("svg", {
                                  class: "absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2",
                                  width: "404",
                                  height: "404",
                                  fill: "none",
                                  viewBox: "0 0 404 404",
                                  role: "img",
                                  "aria-labelledby": "svg-nmrxiv"
                                }, [
                                  createVNode("title", { id: "svg-nmrxiv" }, " nmrxiv "),
                                  createVNode("defs", null, [
                                    createVNode("pattern", {
                                      id: "ad119f34-7694-4c31-947f-5c9d249b21f3",
                                      x: "0",
                                      y: "0",
                                      width: "20",
                                      height: "20",
                                      patternUnits: "userSpaceOnUse"
                                    }, [
                                      createVNode("rect", {
                                        x: "0",
                                        y: "0",
                                        width: "4",
                                        height: "4",
                                        class: "text-gray-200",
                                        fill: "currentColor"
                                      })
                                    ])
                                  ]),
                                  createVNode("rect", {
                                    width: "404",
                                    height: "404",
                                    fill: "url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                                  })
                                ])),
                                createVNode("div", { class: "relative text-center" }, [
                                  createVNode("p", { class: "mt-3 text-lg" }, [
                                    createVNode("span", { class: "text-xl" }, [
                                      createTextVNode("nmr"),
                                      createVNode("b", null, "Xiv")
                                    ]),
                                    createTextVNode(" is 100% open source, so you’re free to dig through the source to see exactly how it works. See something that needs to be improved? Just send us a request."),
                                    createVNode("br"),
                                    createVNode("small", null, [
                                      createTextVNode("Chat bubble is on the bottom left corner or email us at "),
                                      createVNode("a", { href: $options.mailTo }, toDisplayString($options.mailFromAddress), 9, ["href"])
                                    ])
                                  ]),
                                  createVNode("div", { class: "mt-8" }, [
                                    createVNode("div", { class: "inline-flex" }, [
                                      createVNode("a", {
                                        target: "_blank",
                                        href: "https://github.com/NFDI4Chem/nmrxiv",
                                        class: "inline-flex mr-4 items-center justify-center px-5 py-3 border text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                                      }, [
                                        createTextVNode(" Source code "),
                                        createVNode(_component_ArrowTopRightOnSquareIcon, {
                                          class: "-mr-1 ml-3 h-5 w-5 text-gray-400",
                                          "aria-hidden": "true"
                                        })
                                      ]),
                                      createVNode("a", {
                                        target: "_blank",
                                        href: "https://docs.nmrxiv.org",
                                        class: "inline-flex items-center justify-center px-5 py-3 border text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                                      }, [
                                        createTextVNode(" Visit the docs "),
                                        createVNode(_component_ArrowTopRightOnSquareIcon, {
                                          class: "-mr-1 ml-3 h-5 w-5 text-gray-400",
                                          "aria-hidden": "true"
                                        })
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ])
                          ])) : createCommentVNode("", true),
                          $options.currentStep == 4 ? (openBlock(), createBlock("div", { key: 3 }, [
                            createVNode("div", { class: "max-w-7xl mx-auto text-center py-12 px-4 sm:px-6 lg:py-16 lg:px-8" }, [
                              createVNode("h2", { class: "text-base font-semibold uppercase tracking-wider" }, " All set to get started! "),
                              createVNode("p", { class: "my-2 font-bold tracking-tight sm:text-2xl" }, " Start uploading your spectral data today. "),
                              createVNode("p", null, ' Click "Get started" and upload your data or if you want a guided tour of the dashboard press the "Dashboard Tour" button below. '),
                              createVNode("div", { class: "mt-8 flex justify-center" }, [
                                createVNode("div", { class: "inline-flex rounded-md shadow" }, [
                                  createVNode("a", {
                                    refs: "next",
                                    class: "cursor-pointer inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700",
                                    onClick: $options.onboardingComplete
                                  }, " Get started ", 8, ["onClick"])
                                ])
                              ])
                            ])
                          ])) : createCommentVNode("", true),
                          createVNode("div", { class: "grid grid-cols-4 gap-4" }, [
                            createVNode("div", { class: "p-4 py-4" }, [
                              $options.currentStep != 1 ? (openBlock(), createBlock("button", {
                                key: 0,
                                type: "button",
                                class: "py-2 px-3 border rounded-md",
                                onClick: ($event) => $options.selectStep($options.currentStep - 1)
                              }, [
                                createVNode("div", { class: "flex flex-row align-middle" }, [
                                  (openBlock(), createBlock("svg", {
                                    class: "w-5 mr-1",
                                    fill: "currentColor",
                                    viewBox: "0 0 20 20",
                                    xmlns: "http://www.w3.org/2000/svg"
                                  }, [
                                    createVNode("path", {
                                      "fill-rule": "evenodd",
                                      d: "M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z",
                                      "clip-rule": "evenodd"
                                    })
                                  ])),
                                  createVNode("p", { class: "ml-2" }, "Prev")
                                ])
                              ], 8, ["onClick"])) : (openBlock(), createBlock("button", {
                                key: 1,
                                type: "button",
                                class: "py-2 px-3 border rounded-md",
                                onClick: $options.onboardingComplete
                              }, [
                                createVNode("div", { class: "flex flex-row align-middle" }, [
                                  createVNode("p", { class: "mx-2" }, "Skip")
                                ])
                              ], 8, ["onClick"]))
                            ]),
                            createVNode("div", { class: "col-span-2" }, [
                              createVNode("nav", {
                                class: "flex items-center justify-center p-4 py-6",
                                "aria-label": "Progress"
                              }, [
                                createVNode("p", { class: "text-sm font-medium" }, " Step " + toDisplayString($data.steps.findIndex(
                                  (step) => step.status === "current"
                                ) + 1) + " of " + toDisplayString($data.steps.length), 1),
                                createVNode("ol", {
                                  role: "list",
                                  class: "ml-8 flex items-center space-x-5"
                                }, [
                                  (openBlock(true), createBlock(Fragment, null, renderList($data.steps, (step) => {
                                    return openBlock(), createBlock("li", {
                                      key: step.name
                                    }, [
                                      createVNode("a", {
                                        class: "cursor-pointer",
                                        onClick: ($event) => $options.selectStep(step.id)
                                      }, [
                                        step.status === "complete" ? (openBlock(), createBlock("span", {
                                          key: 0,
                                          href: step.href,
                                          class: "block w-2.5 h-2.5 bg-indigo-600 rounded-full hover:bg-indigo-600"
                                        }, [
                                          createVNode("span", { class: "sr-only" }, toDisplayString(step.name), 1)
                                        ], 8, ["href"])) : step.status === "current" ? (openBlock(), createBlock("span", {
                                          key: 1,
                                          href: step.href,
                                          class: "relative flex items-center justify-center",
                                          "aria-current": "step"
                                        }, [
                                          createVNode("span", {
                                            class: "absolute w-5 h-5 p-px flex",
                                            "aria-hidden": "true"
                                          }, [
                                            createVNode("span", { class: "w-full h-full rounded-full bg-indigo-200" })
                                          ]),
                                          createVNode("span", {
                                            class: "relative block w-2.5 h-2.5 bg-indigo-600 rounded-full",
                                            "aria-hidden": "true"
                                          }),
                                          createVNode("span", { class: "sr-only" }, toDisplayString(step.name), 1)
                                        ], 8, ["href"])) : (openBlock(), createBlock("span", {
                                          key: 2,
                                          href: step.href,
                                          class: "block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400"
                                        }, [
                                          createVNode("span", { class: "sr-only" }, toDisplayString(step.name), 1)
                                        ], 8, ["href"]))
                                      ], 8, ["onClick"])
                                    ]);
                                  }), 128))
                                ])
                              ])
                            ]),
                            createVNode("div", { class: "p-4 py-4 text-right" }, [
                              $options.currentStep < $data.steps.length ? (openBlock(), createBlock("div", { key: 0 }, [
                                $options.currentStep == 1 ? (openBlock(), createBlock("div", { key: 0 }, [
                                  createVNode("button", {
                                    type: "button",
                                    class: "py-2 px-3 bg-indigo-600 text-white rounded-md",
                                    autofocus: "",
                                    onClick: ($event) => $options.selectStep($options.currentStep + 1)
                                  }, [
                                    createVNode("div", { class: "flex flex-row align-middle" }, [
                                      createVNode("span", { class: "mr-1" }, "Let's go"),
                                      (openBlock(), createBlock("svg", {
                                        class: "w-5 ml-2",
                                        fill: "currentColor",
                                        viewBox: "0 0 20 20",
                                        xmlns: "http://www.w3.org/2000/svg"
                                      }, [
                                        createVNode("path", {
                                          "fill-rule": "evenodd",
                                          d: "M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z",
                                          "clip-rule": "evenodd"
                                        })
                                      ]))
                                    ])
                                  ], 8, ["onClick"])
                                ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                  createVNode("button", {
                                    ref: "next",
                                    type: "button",
                                    class: "py-2 px-3 bg-indigo-600 text-white rounded-md",
                                    autofocus: "",
                                    onClick: ($event) => $options.selectStep($options.currentStep + 1)
                                  }, [
                                    createVNode("div", { class: "flex flex-row align-middle" }, [
                                      createVNode("span", { class: "mr-1" }, "Next"),
                                      (openBlock(), createBlock("svg", {
                                        class: "w-5 ml-2",
                                        fill: "currentColor",
                                        viewBox: "0 0 20 20",
                                        xmlns: "http://www.w3.org/2000/svg"
                                      }, [
                                        createVNode("path", {
                                          "fill-rule": "evenodd",
                                          d: "M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z",
                                          "clip-rule": "evenodd"
                                        })
                                      ]))
                                    ])
                                  ], 8, ["onClick"])
                                ]))
                              ])) : createCommentVNode("", true)
                            ])
                          ])
                        ])
                      ]),
                      _: 1
                    })
                  ]),
                  _: 1
                })
              ])
            ]),
            _: 1
          })
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/App/Onboarding.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Onboarding = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Onboarding as O
};
