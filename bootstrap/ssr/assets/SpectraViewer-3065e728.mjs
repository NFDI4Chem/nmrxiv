import { ssrRenderAttrs, ssrInterpolate, ssrRenderStyle, ssrRenderAttr, ssrRenderList } from "vue/server-renderer";
import { useSSRContext } from "vue";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  props: {
    dataset: Object,
    project: Object,
    study: Object
  },
  data() {
    return {
      spectraError: null,
      selectedSpectraData: null,
      currentMolecules: [],
      info: null
    };
  },
  computed: {
    url() {
      return String(this.$page.props.url);
    },
    nmriumURL() {
      return this.$page.props.nmriumURL ? String(this.$page.props.nmriumURL + "&id=" + Math.random()) : "https://nmriumdev.nmrxiv.org?defaultEmptyMessage=''&workspace=embedded&id=" + Math.random();
    }
  },
  watch: {
    // dataset: {
    //     immediate: true,
    //     handler() {
    //        this.loadSpectra();
    //     },
    // },
  },
  methods: {
    loadSpectra() {
      if (this.study) {
        const iframe = window.frames.NMRiumIframe;
        this.spectraError = null;
        this.currentMolecules = [];
        this.updateLoadingStatus(true);
        if (iframe) {
          if (this.dataset && this.dataset.has_nmrium) {
            this.infoLog("Loading Spectra from NMRium JSON..");
            axios.get("/datasets/" + this.dataset.id + "/nmriumInfo").then((response) => {
              let nmrium_info = response.data;
              if (nmrium_info) {
                let data = {
                  data: nmrium_info,
                  type: "nmrium"
                };
                iframe.postMessage(
                  { type: `nmr-wrapper:load`, data },
                  "*"
                );
              } else {
                let urls = [];
                urls.push(this.study.download_url);
                this.loadFromURL(urls);
              }
            });
          } else {
            if (this.study.has_nmrium) {
              this.infoLog("Loading Spectra from NMRium JSON..");
              axios.get(
                "/studies/" + this.study.id + "/nmriumInfo"
              ).then((response) => {
                let nmrium_info = response.data;
                if (nmrium_info) {
                  let data = {
                    data: nmrium_info,
                    type: "nmrium"
                  };
                  iframe.postMessage(
                    { type: `nmr-wrapper:load`, data },
                    "*"
                  );
                } else {
                  let urls = [];
                  urls.push(this.study.download_url);
                  this.loadFromURL(urls);
                }
              });
            } else {
              if (this.study.download_url) {
                let urls = [];
                urls.push(this.study.download_url);
                this.loadFromURL(urls);
              }
            }
          }
        }
      }
    },
    loadFromURL(urls) {
      this.infoLog("Loading Spectra from URL..");
      const iframe = window.frames.NMRiumIframe;
      let data = {
        data: urls,
        type: "url"
      };
      iframe.postMessage({ type: `nmr-wrapper:load`, data }, "*");
    },
    loadMol(molFile) {
      let svgString = null;
      let mol = OCL.Molecule.fromMolfile(molFile);
      if (mol.toIsomericSmiles() != "") {
        svgString = mol.toSVG(300, 300);
      }
      return svgString;
    },
    getSVGString(molecule) {
      if (molecule.MOL) {
        let mol = OCL.Molecule.fromMolfile(
          "\n  " + molecule.MOL.replaceAll('"', "")
        );
        return mol.toSVG(200, 200);
      }
    },
    updateLoadingStatus(status) {
      this.$emit("loading", status);
    },
    infoLog(message, reset) {
      this.info = message;
      if (reset) {
        setTimeout(() => {
          this.info = "";
        }, 5e3);
      }
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  if ($data.spectraError) {
    _push(`<div><div class="rounded-md bg-red-50 p-4"><div class="flex"><div class="flex-shrink-0"><svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"></path></svg></div><div class="ml-3"><h3 class="text-sm font-medium text-red-800"> There were errors loading spectra </h3><div class="mt-2 text-sm text-red-700"><ul role="list" class="list-disc space-y-1 pl-5"><li>${ssrInterpolate($data.spectraError)}</li></ul></div></div></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<iframe name="NMRiumIframe" frameborder="0" allowfullscreen class="rounded-md border" style="${ssrRenderStyle({ "width": "100%", "height": "75vh", "max-height": "600px" })}"${ssrRenderAttr("src", $options.nmriumURL)}></iframe>`);
  if ($data.currentMolecules.length > 0) {
    _push(`<div><ul role="list" class="grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 sm:gap-x-6 lg:grid-cols-4 xl:gap-x-8"><!--[-->`);
    ssrRenderList($data.currentMolecules, (molecule) => {
      _push(`<li class="relative">`);
      if (molecule.svg) {
        _push(`<div class="group flex justify-center block w-full aspect-w-10 aspect-h-7 rounded-lg bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-teal-500 overflow-hidden"><div class="p-4 object-cover pointer-events-none group-hover:opacity-75">${molecule.svg}</div></div>`);
      } else {
        _push(`<div><div class="rounded-md border my-3 flex justify-center items-center"><span>${$options.loadMol(molecule.molfile)}</span></div></div>`);
      }
      _push(`</li>`);
    });
    _push(`<!--]--></ul></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<div class="grid grid-cols-2 mt-3">`);
  if ($data.selectedSpectraData) {
    _push(`<div class="p-1 pr-2"><label id="tour-step-spectra-info" for="location" class="block text-sm font-medium text-gray-700">Info</label>`);
    if ($data.selectedSpectraData) {
      _push(`<div class="overflow-hidden ring-1 mt-3 ring-black ring-opacity-5 md:rounded-lg"><!--[-->`);
      ssrRenderList($data.selectedSpectraData, (spectra) => {
        _push(`<table class="min-w-full border divide-y divide-gray-300"><thead class="bg-gray-50"><tr><th scope="col" colspan="2" class="py-3.5 pl-4 pr-3 text-left text-sm font-bold text-blue-900 sm:pl-6 lg:pl-8"> Spectra :: ${ssrInterpolate(spectra.id)}</th></tr></thead><thead class="bg-gray-50"><tr><th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 lg:pl-8"> Field </th><th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"> Value </th></tr></thead><tbody class="divide-y divide-gray-200 bg-white"><!--[-->`);
        ssrRenderList(Object.keys(spectra.info), (key) => {
          _push(`<tr><td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8">${ssrInterpolate(key)}</td><td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">${ssrInterpolate(spectra.info[key])}</td></tr>`);
        });
        _push(`<!--]--><tr><td class="whitespace-nowrap py-2 pl-2 pr-3 text-sm font-medium text-gray-900 sm:pl-6 bg-gray-100 lg:pl-8" colspan="2"> Meta </td></tr><!--[-->`);
        ssrRenderList(Object.keys(spectra.meta), (key) => {
          _push(`<tr><td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8">${ssrInterpolate(key)}</td><td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">${ssrInterpolate(spectra.meta[key])}</td></tr>`);
        });
        _push(`<!--]--></tbody></table>`);
      });
      _push(`<!--]--></div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  if ($data.selectedSpectraData) {
    _push(`<div class="p-1 pr-2">`);
    if ($data.selectedSpectraData[0]["peaks"] && $data.selectedSpectraData[0]["peaks"]["values"].length) {
      _push(`<span><label id="tour-step-spectra-info" for="location" class="block text-sm font-medium text-gray-700">Peaks</label><div class="overflow-hidden shadow mt-3 ring-1 ring-black ring-opacity-5 md:rounded-lg"><table class="min-w-full divide-y divide-gray-300"><thead class="bg-gray-50"><tr><th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"> # </th><th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"> δ (ppm) </th><th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"> Intensity </th></tr></thead><tbody class="bg-white"><!--[-->`);
      ssrRenderList($data.selectedSpectraData[0]["peaks"]["values"], (peak, $index) => {
        _push(`<tr><td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">${ssrInterpolate($index + 1)}</td><td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">${ssrInterpolate(peak.x)}</td><td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">${ssrInterpolate(peak.y)}</td></tr>`);
      });
      _push(`<!--]--></tbody></table></div></span>`);
    } else {
      _push(`<!---->`);
    }
    _push(`<div> </div>`);
    if ($data.selectedSpectraData[0]["ranges"] && $data.selectedSpectraData[0]["ranges"]["values"].length) {
      _push(`<span><label id="tour-step-spectra-info" for="location" class="block text-sm font-medium text-gray-700">Ranges</label><div class="overflow-hidden shadow mt-3 ring-1 ring-black ring-opacity-5 md:rounded-lg"><table class="min-w-full divide-y divide-gray-300"><thead class="bg-gray-50"><tr><th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"> # </th><th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"> δ (ppm) </th></tr></thead><tbody class="bg-white"><!--[-->`);
      ssrRenderList($data.selectedSpectraData[0]["ranges"]["values"], (range, $index) => {
        _push(`<tr><td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">${ssrInterpolate($index + 1)}</td><td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">${ssrInterpolate(range.from)} - ${ssrInterpolate(range.to)}</td></tr>`);
      });
      _push(`<!--]--></tbody></table></div></span>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/SpectraViewer.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const SpectraViewer = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  SpectraViewer as S
};
