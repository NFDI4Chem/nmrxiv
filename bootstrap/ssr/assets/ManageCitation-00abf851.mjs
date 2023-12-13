import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { PencilIcon, ArrowSmallRightIcon, FolderPlusIcon, PlusIcon, TrashIcon } from "@heroicons/vue/24/solid";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { L as LoadingButton } from "./LoadingButton-8ae902b0.mjs";
import { J as JetDangerButton } from "./DangerButton-9c23157c.mjs";
import { router } from "@inertiajs/vue3";
import { S as SelectRich } from "./SelectRich-b58dcc3f.mjs";
import Draggable from "vuedraggable";
import { resolveComponent, withCtx, createTextVNode, toDisplayString, openBlock, createBlock, createVNode, createCommentVNode, withDirectives, vModelText, Fragment, renderList, useSSRContext, mergeProps } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderClass, ssrRenderStyle, ssrRenderList } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main$1 = {
  components: {
    JetDialogModal,
    JetSecondaryButton,
    JetDangerButton,
    JetButton,
    PencilIcon,
    ArrowSmallRightIcon,
    FolderPlusIcon,
    PlusIcon,
    TrashIcon,
    JetInputError,
    LoadingButton,
    SelectRich,
    Draggable
  },
  props: ["project"],
  data() {
    return {
      displayAddAuthorForms: false,
      isEdit: false,
      form: this.$inertia.form({
        title: "",
        given_name: "",
        family_name: null,
        affiliation: null,
        orcid_id: null,
        email_id: null,
        contributor_type: null
      }),
      authorsForm: this.$inertia.form({
        authors: []
      }),
      updateRoleForm: this.$inertia.form({
        author_id: "",
        role: ""
      }),
      query: "",
      showDialog: false,
      authors: [],
      fetchedAuthors: [],
      selectedAuthor: null,
      loading: false,
      confirmDelete: false,
      error: "",
      showManageRoleDialog: false,
      drag: false,
      // todo : dynamically load this from the config
      contributorType: [
        {
          title: "Researcher",
          description: "A person involved in analysing data or the results of an experiment or formal study.<br> May indicate an intern or assistant to one of the authors who helped with research<br> but who was not so “key” as to be listed as an author."
        },
        {
          title: "ContactPerson",
          description: "Person with knowledge of how to access, troubleshoot, or otherwise field issues<br> related to the resource."
        },
        {
          title: "DataCollector",
          description: "Person/institution responsible for finding or gathering/collecting data under the <br> guidelines of the author(s) or Principal Investigator(PI)."
        },
        {
          title: "DataCurator",
          description: "Person tasked with reviewing, enhancing, cleaning, or standardizing metadata and<br> the associated data submitted for storage, use, and maintenance within a data centre<br> or repository."
        },
        {
          title: "DataManager",
          description: "Person (or organisation with a staff of data managers, such as a data centre) <br> responsible for maintaining the finished resource."
        },
        {
          title: "Distributor",
          description: "Institution tasked with responsibility to generate/disseminate copies of the resource<br> in either electronic or print form."
        },
        {
          title: "Editor",
          description: "A person who oversees the details related to the publication format of the resource."
        },
        {
          title: "HostingInstitution",
          description: "Typically, the organisation allowing the resource to be available on the internet<br> through the provision of its hardware/software/operating support."
        },
        {
          title: "Producer",
          description: "Typically, a person or organisation responsible for the artistry and form of a media<br> product."
        },
        {
          title: "ProjectLeader",
          description: "Person officially designated as head of project team or subproject team<br> instrumental in the work necessary to development of the resource."
        },
        {
          title: "ProjectManager",
          description: "Person on the membership list of a designated project/project team."
        },
        {
          title: "RegistrationAgency",
          description: "Institution/organisation officially appointed by a Registration Authority to handle<br> specific tasks within a defined area of responsibility."
        },
        {
          title: "RelatedPerson",
          description: "A person without a specifically defined role in the development of the resource,<br> but who is someone the author wishes to recognize."
        },
        {
          title: "ResearchGroup",
          description: "Typically refers to a group of individuals with a lab, department, or division that<br> has a specifically defined focus of activity."
        },
        {
          title: "RightsHolder",
          description: "Person or institution owning or managing property rights, including intellectual<br> property rights over the resource."
        },
        {
          title: "Sponsor",
          description: "Person or organisation that issued a contract or under the auspices of which<br> a work has been written,<br> printed, published, developed, etc."
        },
        {
          title: "Supervisor",
          description: "Designated administrator over one or more groups/teams working to produce<br> a resource, or over one or more steps of a development process."
        },
        {
          title: "WorkPackageLeader",
          description: "A Work Package is a recognized data product, not all of which is included in<br> publication. The package, instead, may include notes, discarded documents,<br> etc.The Work Package Leader is responsible for ensuring the comprehensive<br> contents, versioning, and availability of the Work Package during the development<br> of the resource."
        },
        {
          title: "Other",
          description: "Any person or institution making a significant contribution to the development<br> and/or maintenance of the resource, but whose contribution is not adequately<br> described by any of the other values for contributorType."
        }
      ]
    };
  },
  mounted() {
    this.loadInitial();
  },
  methods: {
    /*Return filtered fetched authors list on the basis of selection*/
    selectedFetchedAuthorsList(all = false) {
      if (all) {
        return this.fetchedAuthors;
      } else {
        return this.fetchedAuthors.filter((a) => a.selected);
      }
    },
    /*Add imported authors to selected list*/
    addAuthorToSelectedList(author) {
      if (author.selected) {
        author.selected = false;
      } else {
        author.selected = true;
      }
    },
    /*Fetch and populate initial & default values*/
    loadInitial() {
      if (this.project && this.project.authors) {
        this.authors = this.project.authors.sort(
          (a, b) => a.pivot.sort_order - b.pivot.sort_order
        );
      }
      this.form.contributor_type = {};
      this.form.contributor_type = this.contributorType[0];
    },
    /*Control the display toggling*/
    toggleDialog() {
      this.showDialog = !this.showDialog;
    },
    /*Reset variables on close of dialog*/
    onClose() {
      this.showDialog = false;
      this.form.reset();
      this.fetchedAuthors = [];
      this.query = "";
      this.form.contributor_type = {};
      this.form.contributor_type = this.contributorType[0];
      this.isEdit = false;
      this.error = "";
    },
    /*Edit author*/
    edit(author) {
      this.selectedAuthor = author;
      this.authors = this.authors.filter((author2) => {
        return author2.given_name + author2.family_name != this.selectedAuthor.given_name + this.selectedAuthor.family_name;
      });
      this.form.title = author.title;
      this.form.given_name = author.given_name;
      this.form.family_name = author.family_name;
      this.form.email_id = author.email_id;
      this.form.affiliation = author.affiliation;
      this.form.orcid_id = author.orcid_id;
      this.form.contributor_type = {};
      this.form.contributor_type.title = author.pivot ? author.pivot.contributor_type : null;
      this.displayAddAuthorForms = true;
      this.isEdit = true;
    },
    /*Fetch author from DOI or ORCID id*/
    fetchAuthors() {
      this.loading = true;
      this.error = "";
      this.query = this.extractDoi(this.query);
      this.fetchedAuthors = [];
      let isDOI = new RegExp(/\b(10[.][0-9]{4,}(?:[.][0-9]+)*)\b/g).test(
        this.query
      );
      axios.get(this.$page.props.europemcWSApi, {
        params: {
          query: isDOI ? "DOI:" + this.query : this.query,
          format: "json",
          pageSize: "1",
          resulttype: "core",
          synonym: "true"
        }
      }).then((res) => {
        if (res && res.data && res.data.resultList.result.length > 0) {
          var authors = isDOI ? res.data.resultList.result[0].authorList.author : res.data.resultList.result[0].authorList.author.filter(
            (a) => a.authorId && a.authorId.type == "ORCID" && a.authorId.value == this.query
          );
          this.fetchedAuthors = this.formatAuthorResponse(
            authors,
            "europemc"
          );
        } else {
          this.fetchDataFromCrossref(this.query);
        }
      }).catch((err) => {
        console.log(err);
      }).finally(() => {
        this.loading = false;
      });
    },
    /*Make REST Call to Crossref API */
    fetchDataFromCrossref(query) {
      this.fetchedAuthors = [];
      axios.get(this.$page.props.CROSSREF_API + this.query).then((res) => {
        if (res.data && res.data.message) {
          this.fetchedAuthors = this.formatAuthorResponse(
            res.data.message.author,
            "crossref"
          );
        }
      }).catch((err) => {
        console.log(err);
      }).finally(() => {
        if (this.fetchedAuthors && this.fetchedAuthors.length == 0) {
          this.error = "No data found. Please enter the details manually.";
        }
        this.loading = false;
      });
    },
    /*Format authors response*/
    formatAuthorResponse(authors, apiType) {
      var formattedAuthors = [];
      if (authors && authors.length > 0) {
        switch (apiType) {
          case "europemc":
            authors.forEach((author) => {
              var a = {};
              a.firstName = author.firstName;
              a.lastName = author.lastName;
              a.fullName = author.fullName;
              a.orcidId = author.authorId && author.authorId.type == "ORCID" ? author.authorId.value : "";
              a.affiliation = author.authorAffiliationDetailsList && author.authorAffiliationDetailsList.authorAffiliation[0] ? author.authorAffiliationDetailsList.authorAffiliation[0].affiliation : "";
              formattedAuthors.push(a);
            });
            break;
          case "crossref":
            authors.forEach((author) => {
              var a = {};
              a.firstName = author.given;
              a.lastName = author.family;
              a.orcidId = "";
              a.affiliation = author.affiliation[0] ? author.affiliation[0].name : "";
              formattedAuthors.push(a);
            });
            break;
        }
      }
      return formattedAuthors;
    },
    /*Format the response*/
    formatAuthors(authors) {
      let authorsList = [];
      authors.forEach((author) => {
        authorsList.push({
          given_name: author.firstName,
          family_name: author.lastName,
          orcid_id: author.orcidId,
          affiliation: author.affiliation
        });
      });
      return authorsList;
    },
    /*Make the call to save API on the basis of selection*/
    save(input) {
      switch (input) {
        case "addSelected":
          this.addImportedAuthor(false);
          break;
        case "addAll":
          this.addImportedAuthor(true);
          break;
        case "addManually":
          this.addManually();
          break;
      }
    },
    /*Prepare request form and execute save query for imported authors*/
    addImportedAuthor(addAll) {
      this.authorsForm.reset();
      if (this.authors.length > 0) {
        this.authors = this.authors.concat(
          this.formatAuthors(this.selectedFetchedAuthorsList(addAll))
        );
      } else {
        this.authors = this.formatAuthors(
          this.selectedFetchedAuthorsList(addAll)
        );
      }
      this.executeQuery();
    },
    /*Prepare request form and execute save query for authors added manually*/
    addManually() {
      this.validateForm();
      this.authorsForm.reset();
      if (!this.form.hasErrors) {
        let newAuthor = {
          title: this.form.title ? this.form.title.trim() : null,
          given_name: this.form.given_name ? this.form.given_name.trim() : null,
          family_name: this.form.family_name ? this.form.family_name.trim() : null,
          email_id: this.form.email_id ? this.form.email_id.trim() : null,
          orcid_id: this.form.orcid_id ? this.form.orcid_id.trim() : null,
          affiliation: this.form.affiliation ? this.form.affiliation.trim() : null,
          contributor_type: this.form.contributor_type ? this.form.contributor_type.title.trim() : "Researcher"
        };
        if (this.authors.length > 0) {
          if (this.selectedAuthor) {
            this.authors.splice(
              this.selectedAuthor.pivot.sort_order,
              0,
              newAuthor
            );
          } else {
            this.authors.push(newAuthor);
          }
        } else {
          this.authors = [newAuthor];
        }
        this.executeQuery();
      }
    },
    /*Make request to save API*/
    executeQuery() {
      this.authorsForm.authors = this.authors;
      const keys = ["given_name", "family_name"];
      this.authorsForm.authors = this.authorsForm.authors.filter(
        (value, index, self) => self.findIndex(
          (v) => keys.every((k) => v[k] === value[k])
        ) === index
      );
      this.authorsForm.post(route("author.save", this.project.id), {
        preserveScroll: true,
        onSuccess: () => {
          this.loadInitial();
          this.form.reset();
          this.form.contributor_type = {};
          this.form.contributor_type = this.contributorType[0];
          this.displayAddAuthorForms = false;
          this.isEdit = false;
        },
        onError: (err) => console.error(err)
      });
    },
    /*Confirm delete and prepare request*/
    confirmDeletion(author) {
      this.confirmDelete = true;
      this.authorsForm.reset();
      this.authorsForm.authors = [
        {
          id: author.id,
          given_name: author.given_name,
          family_name: author.family_name,
          orcid_id: author.orcid_id,
          affiliation: author.affiliation
        }
      ];
    },
    /*Make request to delete API*/
    deleteAuthor() {
      this.authorsForm.delete(route("author.delete", this.project.id), {
        preserveScroll: true,
        onSuccess: () => {
          router.reload({ only: ["project"] });
          this.loadInitial();
          this.authorsForm.reset();
          this.confirmDelete = false;
        },
        onError: (err) => console.error(err)
      });
    },
    /*Check form for validation errors*/
    validateForm() {
      this.form.errors = {};
      this.form.hasErrors = false;
      let _hasErrors = false;
      if (this.form.email_id) {
        if (!this.isValidEmail(this.form.email_id)) {
          this.form.errors.email_id = "Valid email required.";
          _hasErrors = true;
        }
      }
      if (_hasErrors) {
        this.form.hasErrors = true;
      }
    },
    /*Make request to update role API*/
    updateRole(role) {
      this.updateRoleForm.role = role.title;
      this.updateRoleForm.post(
        route("author.updateRole", this.project.id),
        {
          preserveScroll: true,
          onSuccess: () => {
            router.reload({ only: ["project"] });
            this.loadInitial();
            this.updateRoleForm.reset();
            this.showManageRoleDialog = false;
            this.authorId = "";
          },
          onError: (err) => console.error(err)
        }
      );
    },
    /*Execute query on sorting of authors*/
    onSort() {
      this.executeQuery();
    },
    /*Reset variables on cancel editing*/
    onCancelEdit() {
      if (this.selectedAuthor) {
        this.authors.splice(
          this.selectedAuthor.pivot.sort_order,
          0,
          this.selectedAuthor
        );
      }
      this.displayAddAuthorForms = false, this.isEdit = false, this.form.reset(), this.form.contributor_type = {};
      this.form.contributor_type = this.contributorType[0];
    },
    onBack() {
      if (this.selectedAuthor) {
        this.authors.splice(
          this.selectedAuthor.pivot.sort_order,
          0,
          this.selectedAuthor
        );
      }
      this.displayAddAuthorForms = false;
      this.isEdit = false;
    },
    /*Add current user as Author*/
    addCurrentUser() {
      if (this.$page.props.auth.user && this.$page.props.auth.user.orcid_id) {
        let user = {};
        let affiliation = {};
        this.fetchedAuthors = [];
        user.firstName = this.$page.props.auth.user.first_name;
        user.lastName = this.$page.props.auth.user.last_name;
        user.authorId = {};
        user.authorId.type = "ORCID";
        user.authorId.value = this.$page.props.auth.user.orcid_id;
        user.authorAffiliationDetailsList = {};
        user.authorAffiliationDetailsList.authorAffiliation = [];
        affiliation.affiliation = this.$page.props.auth.user.affiliation;
        user.authorAffiliationDetailsList.authorAffiliation.push(
          affiliation
        );
        this.fetchedAuthors.push(user);
      }
    }
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_dialog_modal = resolveComponent("jet-dialog-modal");
  const _component_PlusIcon = resolveComponent("PlusIcon");
  const _component_ArrowSmallRightIcon = resolveComponent("ArrowSmallRightIcon");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_select_rich = resolveComponent("select-rich");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  const _component_loading_button = resolveComponent("loading-button");
  const _component_draggable = resolveComponent("draggable");
  const _component_PencilIcon = resolveComponent("PencilIcon");
  const _component_TrashIcon = resolveComponent("TrashIcon");
  const _component_FolderPlusIcon = resolveComponent("FolderPlusIcon");
  const _component_jet_danger_button = resolveComponent("jet-danger-button");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_jet_dialog_modal, {
    show: $data.showDialog,
    "max-width": "6xl",
    onClose: ($event) => $data.showDialog = false
  }, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`${ssrInterpolate($props.project.name)} - Manage Authors `);
        if (!$data.displayAddAuthorForms) {
          _push2(`<button type="button" class="inline-flex float-right items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_PlusIcon, { class: "w-5 h-5 mr-1 text-white" }, null, _parent2, _scopeId));
          _push2(` Add Author </button>`);
        } else {
          _push2(`<button type="button" class="inline-flex float-right items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_ArrowSmallRightIcon, { class: "w-5 h-5 mr-1 text-white" }, null, _parent2, _scopeId));
          _push2(` Back </button>`);
        }
      } else {
        return [
          createTextVNode(toDisplayString($props.project.name) + " - Manage Authors ", 1),
          !$data.displayAddAuthorForms ? (openBlock(), createBlock("button", {
            key: 0,
            type: "button",
            class: "inline-flex float-right items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2",
            onClick: ($event) => $data.displayAddAuthorForms = true
          }, [
            createVNode(_component_PlusIcon, { class: "w-5 h-5 mr-1 text-white" }),
            createTextVNode(" Add Author ")
          ], 8, ["onClick"])) : (openBlock(), createBlock("button", {
            key: 1,
            type: "button",
            class: "inline-flex float-right items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2",
            onClick: $options.onBack
          }, [
            createVNode(_component_ArrowSmallRightIcon, { class: "w-5 h-5 mr-1 text-white" }),
            createTextVNode(" Back ")
          ], 8, ["onClick"]))
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div${_scopeId}>`);
        if ($data.displayAddAuthorForms) {
          _push2(`<div${_scopeId}><div class="relative grid grid-cols-1 gap-x-5 max-w-7xl mx-auto lg:grid-cols-2"${_scopeId}><div class="pb-36 px-4 sm:px-6 lg:pb-5 lg:px-0 lg:row-start-1 lg:col-start-1"${_scopeId}><div${_scopeId}><p class="text-sm leading-6 font-bold text-gray-900"${_scopeId}>`);
          if (!$data.isEdit) {
            _push2(`<span${_scopeId}>Add</span>`);
          } else {
            _push2(`<span${_scopeId}></span>`);
          }
          _push2(` Author </p><div class="mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2"${_scopeId}><div class="sm:col-span-2"${_scopeId}><label for="title" class="block text-sm font-medium text-gray-700"${_scopeId}> Title </label><div class="mt-1"${_scopeId}><input id="title"${ssrRenderAttr("value", $data.form.title)} type="text" name="title" autocomplete="title" class="${ssrRenderClass([
            $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
            "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
          ])}"${_scopeId}></div></div><div class="sm:col-span-2"${_scopeId}><label for="given-name" class="block text-sm font-medium text-gray-700 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId}> First Name </label><div class="mt-1"${_scopeId}><input id="given-name"${ssrRenderAttr("value", $data.form.given_name)} type="text" name="given-name" class="${ssrRenderClass([
            $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
            "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
          ])}"${_scopeId}></div>`);
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.authorsForm.errors.given_name,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          _push2(`</div><div class="sm:col-span-2"${_scopeId}><label for="family-name" class="block text-sm font-medium text-gray-700 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId}> Family Name </label><div class="mt-1"${_scopeId}><input id="family-name"${ssrRenderAttr("value", $data.form.family_name)} type="text" name="family-name" autocomplete="family-name" class="${ssrRenderClass([
            $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
            "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
          ])}"${_scopeId}></div>`);
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.authorsForm.errors.family_name,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          _push2(`</div><div class="sm:col-span-3"${_scopeId}><label for="email" class="block text-sm font-medium text-gray-700"${_scopeId}> Email address </label><div class="mt-1"${_scopeId}><input id="email"${ssrRenderAttr("value", $data.form.email_id)} name="email" type="email" autocomplete="email" class="${ssrRenderClass([
            $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
            "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
          ])}"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.form.errors.email_id,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          _push2(`</div></div><div class="sm:col-span-3"${_scopeId}><label for="orcid" class="block text-sm font-medium text-gray-700"${_scopeId}> ORCID iD </label><div class="mt-1"${_scopeId}><input id="orcid"${ssrRenderAttr("value", $data.form.orcid_id)} name="orcid" autocomplete="orcid" type="text" class="${ssrRenderClass([
            $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
            "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
          ])}"${_scopeId}></div></div><div class="sm:col-span-6"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_select_rich, {
            selected: $data.form.contributor_type,
            "onUpdate:selected": ($event) => $data.form.contributor_type = $event,
            label: "Role",
            items: $data.contributorType
          }, null, _parent2, _scopeId));
          _push2(`</div><div class="sm:col-span-6"${_scopeId}><label for="about" class="block text-sm font-medium text-gray-700"${_scopeId}> Affiliation </label><div class="mt-1"${_scopeId}><textarea id="affiliation" name="affiliation" rows="3" class="${ssrRenderClass([
            $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
            "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
          ])}" placeholder="Name and address of affiliated University and Department. e.g. Institut für Anorganische und Analytische Chemie, Friedrich-Schiller-Universität, Schloßgasse 10, 07743 Jena"${_scopeId}>${ssrInterpolate($data.form.affiliation)}</textarea></div>`);
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.authorsForm.errors.affiliation,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
          if (!$data.isEdit) {
            _push2(`<div class="sm:col-span-6 float-left"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_jet_secondary_button, {
              class: "float-right",
              disabled: !($data.form && $data.form.given_name && $data.form.family_name),
              onClick: ($event) => $options.save("addManually")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` Add `);
                } else {
                  return [
                    createTextVNode(" Add ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_jet_secondary_button, {
              class: "float-right mr-2",
              disabled: !($data.form && $data.form.given_name && $data.form.family_name),
              onClick: ($event) => ($data.form.reset(), $data.authorsForm.reset())
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` Clear `);
                } else {
                  return [
                    createTextVNode(" Clear ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div>`);
          } else {
            _push2(`<div class="sm:col-span-6 float-left"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_jet_secondary_button, {
              class: "float-right",
              disabled: !($data.form && $data.form.given_name && $data.form.family_name),
              onClick: ($event) => $options.save("addManually")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` Update `);
                } else {
                  return [
                    createTextVNode(" Update ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_jet_secondary_button, {
              class: "float-right mr-2",
              disabled: !($data.form && $data.form.given_name && $data.form.family_name),
              onClick: ($event) => $options.onCancelEdit()
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
            _push2(`</div>`);
          }
          _push2(`</div></div></div><div class="pb-36 lg:px-1 lg:row-start-1 lg:col-start-2 border-l"${_scopeId}><div class="pl-2"${_scopeId}><p class="text-sm leading-6 font-bold text-gray-900"${_scopeId}> Import From </p><div class="mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2"${_scopeId}><div class="sm:col-span-2"${_scopeId}><label for="name" class="block text-sm font-medium text-gray-700"${_scopeId}> DOI or ORCID iD </label><div class="mt-1 flex rounded-md shadow-sm"${_scopeId}><input id="name"${ssrRenderAttr("value", $data.query)} type="text" name="name" autocomplete="off" placeholder="DOI or ORCID iD e.g. 10.1186/s19991-022-00987-0 or 0000-0001-6033-8976" class="flex-1 focus:ring-teal-500 focus:border-teal-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"${_scopeId}></div></div></div><div class="sm:col-span-2 mt-4"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_jet_secondary_button, {
            disabled: $data.query == "" || !$data.query,
            onClick: $options.fetchAuthors
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Import `);
              } else {
                return [
                  createTextVNode(" Import ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_secondary_button, {
            class: "ml-2",
            disabled: !($data.fetchedAuthors.length > 0),
            onClick: ($event) => ($data.fetchedAuthors = [], $data.query = null)
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Reset `);
              } else {
                return [
                  createTextVNode(" Reset ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_secondary_button, {
            class: "ml-2 float-right",
            disabled: !_ctx.$page.props.auth.user.orcid_id,
            onClick: $options.addCurrentUser
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Add me `);
              } else {
                return [
                  createTextVNode(" Add me ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div>`);
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.error,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          if ($data.loading) {
            _push2(`<div class="sm:col-span-9 mt-4 align-centre"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_loading_button, { loading: $data.loading }, null, _parent2, _scopeId));
            _push2(`</div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`<div${_scopeId}><div style="${ssrRenderStyle({ "max-height": "40vh" })}" class="overflow-auto p-2 mt-4"${_scopeId}><!--[-->`);
          ssrRenderList($data.fetchedAuthors, (author) => {
            _push2(`<div class="relative flex items-start mt-2"${_scopeId}><div class="${ssrRenderClass([
              author.selected ? "bg-gray-200 text-white" : "",
              "cursor-pointer min-w-0 flex-1 text-sm font-medium border rounded-md p-4"
            ])}"${_scopeId}><label for="items" class="font-medium text-teal-900"${_scopeId}>${ssrInterpolate(author.firstName)} ${ssrInterpolate(author.lastName)}</label>`);
            if (author.affiliation) {
              _push2(`<p id="items-description" class="text-xs font-medium text-gray-900"${_scopeId}>${ssrInterpolate(author.affiliation)}</p>`);
            } else {
              _push2(`<!---->`);
            }
            if (author.orcidId) {
              _push2(`<div class="text-xs leading-6 font-medium text-teal-900"${_scopeId}><b class="text-gray-500"${_scopeId}>ORCID iD:</b> ${ssrInterpolate(author.orcidId)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
          });
          _push2(`<!--]--></div>`);
          if ($data.fetchedAuthors.length > 0) {
            _push2(`<div class="sm:col-span-6 mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_jet_secondary_button, {
              disabled: !($options.selectedFetchedAuthorsList(
                false
              ).length > 0),
              class: "float-right ml-2",
              onClick: ($event) => $options.save("addSelected")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` Add Selected (${ssrInterpolate($options.selectedFetchedAuthorsList(
                    false
                  ).length)}) `);
                } else {
                  return [
                    createTextVNode(" Add Selected (" + toDisplayString($options.selectedFetchedAuthorsList(
                      false
                    ).length) + ") ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_jet_secondary_button, {
              class: "float-right",
              onClick: ($event) => $options.save("addAll")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` Add All `);
                } else {
                  return [
                    createTextVNode(" Add All ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div></div></div></div></div>`);
        } else {
          _push2(`<!---->`);
        }
        if ($data.authors.length > 0 && !$data.displayAddAuthorForms) {
          _push2(`<div style="${ssrRenderStyle({ "height": "60vh" })}" class="sm:rounded-md overflow-y-scroll"${_scopeId}><p class="text-xs font-large text-red-800 mb-1"${_scopeId}> *Click and drag authors to sort order. </p>`);
          _push2(ssrRenderComponent(_component_draggable, {
            modelValue: $data.authors,
            "onUpdate:modelValue": ($event) => $data.authors = $event,
            "item-key": "author.id",
            group: "author",
            onStart: ($event) => $data.drag = true,
            onEnd: ($event) => $data.drag = false,
            onChange: ($event) => $options.onSort()
          }, {
            item: withCtx(({ element }, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(`<div class="overflow-auto"${_scopeId2}><ul role="list" class="divide-y divide-gray-900"${_scopeId2}><li${_scopeId2}><div class="px-4 border cursor-move hover:bg-gray-200 rounded-md mb-1 py-4 sm:px-6"${_scopeId2}><div class="flex items-center cursor justify-between"${_scopeId2}><p class="text-sm font-medium text-teal-900"${_scopeId2}>${ssrInterpolate(element.title)} ${ssrInterpolate(element.given_name)} ${ssrInterpolate(element.family_name)}</p><button class="ml-2 flex flex-shrink-0"${_scopeId2}>`);
                if (element.pivot && element.pivot.contributor_type) {
                  _push3(`<p class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800"${_scopeId2}>${ssrInterpolate(element.pivot.contributor_type ? element.pivot.contributor_type : "Researcher")}</p>`);
                } else {
                  _push3(`<!---->`);
                }
                _push3(`</button></div><div class="mt-1 sm:flex sm:justify-between"${_scopeId2}><div class="sm:flex"${_scopeId2}><p class="flex items-center text-xs font-small text-gray-500 break-words"${_scopeId2}>${ssrInterpolate(element.affiliation)}</p></div><div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0"${_scopeId2}><button type="button" class="inline-flex items-center p-1 border border-transparent"${_scopeId2}>`);
                _push3(ssrRenderComponent(_component_PencilIcon, { class: "w-3.5 h-3.5 mr-1 text-gray-600" }, null, _parent3, _scopeId2));
                _push3(`</button><button type="button" class="inline-flex items-center p-1 border border-transparent"${_scopeId2}>`);
                _push3(ssrRenderComponent(_component_TrashIcon, { class: "w-3.5 h-3.5 mr-1 text-gray-600" }, null, _parent3, _scopeId2));
                _push3(`</button></div></div><div class="sm:flex sm:justify-between"${_scopeId2}>`);
                if (element.orcid_id) {
                  _push3(`<p class="text-xs font-medium text-teal-900"${_scopeId2}><b class="text-gray-500"${_scopeId2}>ORCID iD:</b> ${ssrInterpolate(element.orcid_id)}</p>`);
                } else {
                  _push3(`<!---->`);
                }
                _push3(`</div><div class="sm:flex sm:justify-between"${_scopeId2}>`);
                if (element.email_id) {
                  _push3(`<p class="text-xs font-medium text-gray-900"${_scopeId2}><b class="text-gray-500"${_scopeId2}>Email-Id:</b> ${ssrInterpolate(element.email_id)}</p>`);
                } else {
                  _push3(`<!---->`);
                }
                _push3(`</div></div></li></ul></div>`);
              } else {
                return [
                  createVNode("div", { class: "overflow-auto" }, [
                    createVNode("ul", {
                      role: "list",
                      class: "divide-y divide-gray-900"
                    }, [
                      createVNode("li", null, [
                        createVNode("div", { class: "px-4 border cursor-move hover:bg-gray-200 rounded-md mb-1 py-4 sm:px-6" }, [
                          createVNode("div", { class: "flex items-center cursor justify-between" }, [
                            createVNode("p", { class: "text-sm font-medium text-teal-900" }, toDisplayString(element.title) + " " + toDisplayString(element.given_name) + " " + toDisplayString(element.family_name), 1),
                            createVNode("button", {
                              class: "ml-2 flex flex-shrink-0",
                              onClick: ($event) => ($data.showManageRoleDialog = true, $data.updateRoleForm.author_id = element.id)
                            }, [
                              element.pivot && element.pivot.contributor_type ? (openBlock(), createBlock("p", {
                                key: 0,
                                class: "inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800"
                              }, toDisplayString(element.pivot.contributor_type ? element.pivot.contributor_type : "Researcher"), 1)) : createCommentVNode("", true)
                            ], 8, ["onClick"])
                          ]),
                          createVNode("div", { class: "mt-1 sm:flex sm:justify-between" }, [
                            createVNode("div", { class: "sm:flex" }, [
                              createVNode("p", { class: "flex items-center text-xs font-small text-gray-500 break-words" }, toDisplayString(element.affiliation), 1)
                            ]),
                            createVNode("div", { class: "mt-2 flex items-center text-sm text-gray-500 sm:mt-0" }, [
                              createVNode("button", {
                                type: "button",
                                class: "inline-flex items-center p-1 border border-transparent",
                                onClick: ($event) => $options.edit(element)
                              }, [
                                createVNode(_component_PencilIcon, { class: "w-3.5 h-3.5 mr-1 text-gray-600" })
                              ], 8, ["onClick"]),
                              createVNode("button", {
                                type: "button",
                                class: "inline-flex items-center p-1 border border-transparent",
                                onClick: ($event) => $options.confirmDeletion(
                                  element
                                )
                              }, [
                                createVNode(_component_TrashIcon, { class: "w-3.5 h-3.5 mr-1 text-gray-600" })
                              ], 8, ["onClick"])
                            ])
                          ]),
                          createVNode("div", { class: "sm:flex sm:justify-between" }, [
                            element.orcid_id ? (openBlock(), createBlock("p", {
                              key: 0,
                              class: "text-xs font-medium text-teal-900"
                            }, [
                              createVNode("b", { class: "text-gray-500" }, "ORCID iD:"),
                              createTextVNode(" " + toDisplayString(element.orcid_id), 1)
                            ])) : createCommentVNode("", true)
                          ]),
                          createVNode("div", { class: "sm:flex sm:justify-between" }, [
                            element.email_id ? (openBlock(), createBlock("p", {
                              key: 0,
                              class: "text-xs font-medium text-gray-900"
                            }, [
                              createVNode("b", { class: "text-gray-500" }, "Email-Id:"),
                              createTextVNode(" " + toDisplayString(element.email_id), 1)
                            ])) : createCommentVNode("", true)
                          ])
                        ])
                      ])
                    ])
                  ])
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div>`);
        if ($data.authors.length == 0 && !$data.displayAddAuthorForms) {
          _push2(`<div class="py-5"${_scopeId}><div class="text-center"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_FolderPlusIcon, { class: "mx-auto h-12 w-12 text-gray-400" }, null, _parent2, _scopeId));
          _push2(`<h3 class="mt-2 text-sm font-medium text-gray-900"${_scopeId}> No Authors Listed </h3><p class="mt-1 text-sm text-gray-500"${_scopeId}> Get started by adding a new author. </p><div class="mt-6"${_scopeId}><button type="button" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_PlusIcon, { class: "w-5 h-5 mr-1 text-white" }, null, _parent2, _scopeId));
          _push2(` Add Author </button></div></div></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(ssrRenderComponent(_component_jet_dialog_modal, {
          show: $data.confirmDelete,
          onClose: ($event) => $data.confirmDelete = false
        }, {
          title: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Delete Author `);
            } else {
              return [
                createTextVNode(" Delete Author ")
              ];
            }
          }),
          content: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Are you sure you want to delete this author? <div class="mt-4"${_scopeId2}></div>`);
            } else {
              return [
                createTextVNode(" Are you sure you want to delete this author? "),
                createVNode("div", { class: "mt-4" })
              ];
            }
          }),
          footer: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_jet_secondary_button, {
                onClick: ($event) => $data.confirmDelete = false
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Cancel `);
                  } else {
                    return [
                      createTextVNode(" Cancel ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(ssrRenderComponent(_component_jet_danger_button, {
                class: ["ml-2", { "opacity-25": $data.form.processing }],
                disabled: $data.form.processing,
                onClick: ($event) => $options.deleteAuthor()
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Delete Author `);
                  } else {
                    return [
                      createTextVNode(" Delete Author ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
            } else {
              return [
                createVNode(_component_jet_secondary_button, {
                  onClick: ($event) => $data.confirmDelete = false
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Cancel ")
                  ]),
                  _: 1
                }, 8, ["onClick"]),
                createVNode(_component_jet_danger_button, {
                  class: ["ml-2", { "opacity-25": $data.form.processing }],
                  disabled: $data.form.processing,
                  onClick: ($event) => $options.deleteAuthor()
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Delete Author ")
                  ]),
                  _: 1
                }, 8, ["class", "disabled", "onClick"])
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode("div", null, [
            $data.displayAddAuthorForms ? (openBlock(), createBlock("div", { key: 0 }, [
              createVNode("div", { class: "relative grid grid-cols-1 gap-x-5 max-w-7xl mx-auto lg:grid-cols-2" }, [
                createVNode("div", { class: "pb-36 px-4 sm:px-6 lg:pb-5 lg:px-0 lg:row-start-1 lg:col-start-1" }, [
                  createVNode("div", null, [
                    createVNode("p", { class: "text-sm leading-6 font-bold text-gray-900" }, [
                      !$data.isEdit ? (openBlock(), createBlock("span", { key: 0 }, "Add")) : (openBlock(), createBlock("span", { key: 1 })),
                      createTextVNode(" Author ")
                    ]),
                    createVNode("div", { class: "mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2" }, [
                      createVNode("div", { class: "sm:col-span-2" }, [
                        createVNode("label", {
                          for: "title",
                          class: "block text-sm font-medium text-gray-700"
                        }, " Title "),
                        createVNode("div", { class: "mt-1" }, [
                          withDirectives(createVNode("input", {
                            id: "title",
                            "onUpdate:modelValue": ($event) => $data.form.title = $event,
                            type: "text",
                            name: "title",
                            autocomplete: "title",
                            class: [
                              $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
                              "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            ]
                          }, null, 10, ["onUpdate:modelValue"]), [
                            [vModelText, $data.form.title]
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "sm:col-span-2" }, [
                        createVNode("label", {
                          for: "given-name",
                          class: "block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                        }, " First Name "),
                        createVNode("div", { class: "mt-1" }, [
                          withDirectives(createVNode("input", {
                            id: "given-name",
                            "onUpdate:modelValue": ($event) => $data.form.given_name = $event,
                            type: "text",
                            name: "given-name",
                            class: [
                              $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
                              "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            ]
                          }, null, 10, ["onUpdate:modelValue"]), [
                            [vModelText, $data.form.given_name]
                          ])
                        ]),
                        createVNode(_component_jet_input_error, {
                          message: $data.authorsForm.errors.given_name,
                          class: "mt-2"
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "sm:col-span-2" }, [
                        createVNode("label", {
                          for: "family-name",
                          class: "block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                        }, " Family Name "),
                        createVNode("div", { class: "mt-1" }, [
                          withDirectives(createVNode("input", {
                            id: "family-name",
                            "onUpdate:modelValue": ($event) => $data.form.family_name = $event,
                            type: "text",
                            name: "family-name",
                            autocomplete: "family-name",
                            class: [
                              $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
                              "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            ]
                          }, null, 10, ["onUpdate:modelValue"]), [
                            [vModelText, $data.form.family_name]
                          ])
                        ]),
                        createVNode(_component_jet_input_error, {
                          message: $data.authorsForm.errors.family_name,
                          class: "mt-2"
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "sm:col-span-3" }, [
                        createVNode("label", {
                          for: "email",
                          class: "block text-sm font-medium text-gray-700"
                        }, " Email address "),
                        createVNode("div", { class: "mt-1" }, [
                          withDirectives(createVNode("input", {
                            id: "email",
                            "onUpdate:modelValue": ($event) => $data.form.email_id = $event,
                            name: "email",
                            type: "email",
                            autocomplete: "email",
                            class: [
                              $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
                              "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            ]
                          }, null, 10, ["onUpdate:modelValue"]), [
                            [vModelText, $data.form.email_id]
                          ]),
                          createVNode(_component_jet_input_error, {
                            message: $data.form.errors.email_id,
                            class: "mt-2"
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "sm:col-span-3" }, [
                        createVNode("label", {
                          for: "orcid",
                          class: "block text-sm font-medium text-gray-700"
                        }, " ORCID iD "),
                        createVNode("div", { class: "mt-1" }, [
                          withDirectives(createVNode("input", {
                            id: "orcid",
                            "onUpdate:modelValue": ($event) => $data.form.orcid_id = $event,
                            name: "orcid",
                            autocomplete: "orcid",
                            type: "text",
                            class: [
                              $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
                              "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            ]
                          }, null, 10, ["onUpdate:modelValue"]), [
                            [vModelText, $data.form.orcid_id]
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "sm:col-span-6" }, [
                        createVNode(_component_select_rich, {
                          selected: $data.form.contributor_type,
                          "onUpdate:selected": ($event) => $data.form.contributor_type = $event,
                          label: "Role",
                          items: $data.contributorType
                        }, null, 8, ["selected", "onUpdate:selected", "items"])
                      ]),
                      createVNode("div", { class: "sm:col-span-6" }, [
                        createVNode("label", {
                          for: "about",
                          class: "block text-sm font-medium text-gray-700"
                        }, " Affiliation "),
                        createVNode("div", { class: "mt-1" }, [
                          withDirectives(createVNode("textarea", {
                            id: "affiliation",
                            "onUpdate:modelValue": ($event) => $data.form.affiliation = $event,
                            name: "affiliation",
                            rows: "3",
                            class: [
                              $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
                              "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                            ],
                            placeholder: "Name and address of affiliated University and Department. e.g. Institut für Anorganische und Analytische Chemie, Friedrich-Schiller-Universität, Schloßgasse 10, 07743 Jena"
                          }, null, 10, ["onUpdate:modelValue"]), [
                            [vModelText, $data.form.affiliation]
                          ])
                        ]),
                        createVNode(_component_jet_input_error, {
                          message: $data.authorsForm.errors.affiliation,
                          class: "mt-2"
                        }, null, 8, ["message"])
                      ]),
                      !$data.isEdit ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "sm:col-span-6 float-left"
                      }, [
                        createVNode(_component_jet_secondary_button, {
                          class: "float-right",
                          disabled: !($data.form && $data.form.given_name && $data.form.family_name),
                          onClick: ($event) => $options.save("addManually")
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Add ")
                          ]),
                          _: 1
                        }, 8, ["disabled", "onClick"]),
                        createVNode(_component_jet_secondary_button, {
                          class: "float-right mr-2",
                          disabled: !($data.form && $data.form.given_name && $data.form.family_name),
                          onClick: ($event) => ($data.form.reset(), $data.authorsForm.reset())
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Clear ")
                          ]),
                          _: 1
                        }, 8, ["disabled", "onClick"])
                      ])) : (openBlock(), createBlock("div", {
                        key: 1,
                        class: "sm:col-span-6 float-left"
                      }, [
                        createVNode(_component_jet_secondary_button, {
                          class: "float-right",
                          disabled: !($data.form && $data.form.given_name && $data.form.family_name),
                          onClick: ($event) => $options.save("addManually")
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Update ")
                          ]),
                          _: 1
                        }, 8, ["disabled", "onClick"]),
                        createVNode(_component_jet_secondary_button, {
                          class: "float-right mr-2",
                          disabled: !($data.form && $data.form.given_name && $data.form.family_name),
                          onClick: ($event) => $options.onCancelEdit()
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Cancel ")
                          ]),
                          _: 1
                        }, 8, ["disabled", "onClick"])
                      ]))
                    ])
                  ])
                ]),
                createVNode("div", { class: "pb-36 lg:px-1 lg:row-start-1 lg:col-start-2 border-l" }, [
                  createVNode("div", { class: "pl-2" }, [
                    createVNode("p", { class: "text-sm leading-6 font-bold text-gray-900" }, " Import From "),
                    createVNode("div", { class: "mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2" }, [
                      createVNode("div", { class: "sm:col-span-2" }, [
                        createVNode("label", {
                          for: "name",
                          class: "block text-sm font-medium text-gray-700"
                        }, " DOI or ORCID iD "),
                        createVNode("div", { class: "mt-1 flex rounded-md shadow-sm" }, [
                          withDirectives(createVNode("input", {
                            id: "name",
                            "onUpdate:modelValue": ($event) => $data.query = $event,
                            type: "text",
                            name: "name",
                            autocomplete: "off",
                            placeholder: "DOI or ORCID iD e.g. 10.1186/s19991-022-00987-0 or 0000-0001-6033-8976",
                            class: "flex-1 focus:ring-teal-500 focus:border-teal-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                          }, null, 8, ["onUpdate:modelValue"]), [
                            [vModelText, $data.query]
                          ])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "sm:col-span-2 mt-4" }, [
                      createVNode(_component_jet_secondary_button, {
                        disabled: $data.query == "" || !$data.query,
                        onClick: $options.fetchAuthors
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Import ")
                        ]),
                        _: 1
                      }, 8, ["disabled", "onClick"]),
                      createVNode(_component_jet_secondary_button, {
                        class: "ml-2",
                        disabled: !($data.fetchedAuthors.length > 0),
                        onClick: ($event) => ($data.fetchedAuthors = [], $data.query = null)
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Reset ")
                        ]),
                        _: 1
                      }, 8, ["disabled", "onClick"]),
                      createVNode(_component_jet_secondary_button, {
                        class: "ml-2 float-right",
                        disabled: !_ctx.$page.props.auth.user.orcid_id,
                        onClick: $options.addCurrentUser
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Add me ")
                        ]),
                        _: 1
                      }, 8, ["disabled", "onClick"])
                    ]),
                    createVNode(_component_jet_input_error, {
                      message: $data.error,
                      class: "mt-2"
                    }, null, 8, ["message"]),
                    $data.loading ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "sm:col-span-9 mt-4 align-centre"
                    }, [
                      createVNode(_component_loading_button, { loading: $data.loading }, null, 8, ["loading"])
                    ])) : createCommentVNode("", true),
                    createVNode("div", null, [
                      createVNode("div", {
                        style: { "max-height": "40vh" },
                        class: "overflow-auto p-2 mt-4"
                      }, [
                        (openBlock(true), createBlock(Fragment, null, renderList($data.fetchedAuthors, (author) => {
                          return openBlock(), createBlock("div", {
                            key: author.authorId,
                            class: "relative flex items-start mt-2"
                          }, [
                            createVNode("div", {
                              class: [
                                author.selected ? "bg-gray-200 text-white" : "",
                                "cursor-pointer min-w-0 flex-1 text-sm font-medium border rounded-md p-4"
                              ],
                              onClick: ($event) => $options.addAuthorToSelectedList(
                                author
                              )
                            }, [
                              createVNode("label", {
                                for: "items",
                                class: "font-medium text-teal-900"
                              }, toDisplayString(author.firstName) + " " + toDisplayString(author.lastName), 1),
                              author.affiliation ? (openBlock(), createBlock("p", {
                                key: 0,
                                id: "items-description",
                                class: "text-xs font-medium text-gray-900"
                              }, toDisplayString(author.affiliation), 1)) : createCommentVNode("", true),
                              author.orcidId ? (openBlock(), createBlock("div", {
                                key: 1,
                                class: "text-xs leading-6 font-medium text-teal-900"
                              }, [
                                createVNode("b", { class: "text-gray-500" }, "ORCID iD:"),
                                createTextVNode(" " + toDisplayString(author.orcidId), 1)
                              ])) : createCommentVNode("", true)
                            ], 10, ["onClick"])
                          ]);
                        }), 128))
                      ]),
                      $data.fetchedAuthors.length > 0 ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "sm:col-span-6 mt-4"
                      }, [
                        createVNode(_component_jet_secondary_button, {
                          disabled: !($options.selectedFetchedAuthorsList(
                            false
                          ).length > 0),
                          class: "float-right ml-2",
                          onClick: ($event) => $options.save("addSelected")
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Add Selected (" + toDisplayString($options.selectedFetchedAuthorsList(
                              false
                            ).length) + ") ", 1)
                          ]),
                          _: 1
                        }, 8, ["disabled", "onClick"]),
                        createVNode(_component_jet_secondary_button, {
                          class: "float-right",
                          onClick: ($event) => $options.save("addAll")
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Add All ")
                          ]),
                          _: 1
                        }, 8, ["onClick"])
                      ])) : createCommentVNode("", true)
                    ])
                  ])
                ])
              ])
            ])) : createCommentVNode("", true),
            $data.authors.length > 0 && !$data.displayAddAuthorForms ? (openBlock(), createBlock("div", {
              key: 1,
              style: { "height": "60vh" },
              class: "sm:rounded-md overflow-y-scroll"
            }, [
              createVNode("p", { class: "text-xs font-large text-red-800 mb-1" }, " *Click and drag authors to sort order. "),
              createVNode(_component_draggable, {
                modelValue: $data.authors,
                "onUpdate:modelValue": ($event) => $data.authors = $event,
                "item-key": "author.id",
                group: "author",
                onStart: ($event) => $data.drag = true,
                onEnd: ($event) => $data.drag = false,
                onChange: ($event) => $options.onSort()
              }, {
                item: withCtx(({ element }) => [
                  createVNode("div", { class: "overflow-auto" }, [
                    createVNode("ul", {
                      role: "list",
                      class: "divide-y divide-gray-900"
                    }, [
                      createVNode("li", null, [
                        createVNode("div", { class: "px-4 border cursor-move hover:bg-gray-200 rounded-md mb-1 py-4 sm:px-6" }, [
                          createVNode("div", { class: "flex items-center cursor justify-between" }, [
                            createVNode("p", { class: "text-sm font-medium text-teal-900" }, toDisplayString(element.title) + " " + toDisplayString(element.given_name) + " " + toDisplayString(element.family_name), 1),
                            createVNode("button", {
                              class: "ml-2 flex flex-shrink-0",
                              onClick: ($event) => ($data.showManageRoleDialog = true, $data.updateRoleForm.author_id = element.id)
                            }, [
                              element.pivot && element.pivot.contributor_type ? (openBlock(), createBlock("p", {
                                key: 0,
                                class: "inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800"
                              }, toDisplayString(element.pivot.contributor_type ? element.pivot.contributor_type : "Researcher"), 1)) : createCommentVNode("", true)
                            ], 8, ["onClick"])
                          ]),
                          createVNode("div", { class: "mt-1 sm:flex sm:justify-between" }, [
                            createVNode("div", { class: "sm:flex" }, [
                              createVNode("p", { class: "flex items-center text-xs font-small text-gray-500 break-words" }, toDisplayString(element.affiliation), 1)
                            ]),
                            createVNode("div", { class: "mt-2 flex items-center text-sm text-gray-500 sm:mt-0" }, [
                              createVNode("button", {
                                type: "button",
                                class: "inline-flex items-center p-1 border border-transparent",
                                onClick: ($event) => $options.edit(element)
                              }, [
                                createVNode(_component_PencilIcon, { class: "w-3.5 h-3.5 mr-1 text-gray-600" })
                              ], 8, ["onClick"]),
                              createVNode("button", {
                                type: "button",
                                class: "inline-flex items-center p-1 border border-transparent",
                                onClick: ($event) => $options.confirmDeletion(
                                  element
                                )
                              }, [
                                createVNode(_component_TrashIcon, { class: "w-3.5 h-3.5 mr-1 text-gray-600" })
                              ], 8, ["onClick"])
                            ])
                          ]),
                          createVNode("div", { class: "sm:flex sm:justify-between" }, [
                            element.orcid_id ? (openBlock(), createBlock("p", {
                              key: 0,
                              class: "text-xs font-medium text-teal-900"
                            }, [
                              createVNode("b", { class: "text-gray-500" }, "ORCID iD:"),
                              createTextVNode(" " + toDisplayString(element.orcid_id), 1)
                            ])) : createCommentVNode("", true)
                          ]),
                          createVNode("div", { class: "sm:flex sm:justify-between" }, [
                            element.email_id ? (openBlock(), createBlock("p", {
                              key: 0,
                              class: "text-xs font-medium text-gray-900"
                            }, [
                              createVNode("b", { class: "text-gray-500" }, "Email-Id:"),
                              createTextVNode(" " + toDisplayString(element.email_id), 1)
                            ])) : createCommentVNode("", true)
                          ])
                        ])
                      ])
                    ])
                  ])
                ]),
                _: 1
              }, 8, ["modelValue", "onUpdate:modelValue", "onStart", "onEnd", "onChange"])
            ])) : createCommentVNode("", true)
          ]),
          $data.authors.length == 0 && !$data.displayAddAuthorForms ? (openBlock(), createBlock("div", {
            key: 0,
            class: "py-5"
          }, [
            createVNode("div", { class: "text-center" }, [
              createVNode(_component_FolderPlusIcon, { class: "mx-auto h-12 w-12 text-gray-400" }),
              createVNode("h3", { class: "mt-2 text-sm font-medium text-gray-900" }, " No Authors Listed "),
              createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " Get started by adding a new author. "),
              createVNode("div", { class: "mt-6" }, [
                createVNode("button", {
                  type: "button",
                  class: "inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2",
                  onClick: ($event) => $data.displayAddAuthorForms = true
                }, [
                  createVNode(_component_PlusIcon, { class: "w-5 h-5 mr-1 text-white" }),
                  createTextVNode(" Add Author ")
                ], 8, ["onClick"])
              ])
            ])
          ])) : createCommentVNode("", true),
          createVNode(_component_jet_dialog_modal, {
            show: $data.confirmDelete,
            onClose: ($event) => $data.confirmDelete = false
          }, {
            title: withCtx(() => [
              createTextVNode(" Delete Author ")
            ]),
            content: withCtx(() => [
              createTextVNode(" Are you sure you want to delete this author? "),
              createVNode("div", { class: "mt-4" })
            ]),
            footer: withCtx(() => [
              createVNode(_component_jet_secondary_button, {
                onClick: ($event) => $data.confirmDelete = false
              }, {
                default: withCtx(() => [
                  createTextVNode(" Cancel ")
                ]),
                _: 1
              }, 8, ["onClick"]),
              createVNode(_component_jet_danger_button, {
                class: ["ml-2", { "opacity-25": $data.form.processing }],
                disabled: $data.form.processing,
                onClick: ($event) => $options.deleteAuthor()
              }, {
                default: withCtx(() => [
                  createTextVNode(" Delete Author ")
                ]),
                _: 1
              }, 8, ["class", "disabled", "onClick"])
            ]),
            _: 1
          }, 8, ["show", "onClose"])
        ];
      }
    }),
    footer: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="flex"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_secondary_button, {
          class: "float-left",
          onClick: ($event) => $options.onClose()
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Close `);
            } else {
              return [
                createTextVNode(" Close ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "flex" }, [
            createVNode(_component_jet_secondary_button, {
              class: "float-left",
              onClick: ($event) => $options.onClose()
            }, {
              default: withCtx(() => [
                createTextVNode(" Close ")
              ]),
              _: 1
            }, 8, ["onClick"])
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_jet_dialog_modal, {
    show: $data.showManageRoleDialog,
    onClose: ($event) => $data.showManageRoleDialog = false
  }, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Manage Role `);
      } else {
        return [
          createTextVNode(" Manage Role ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div style="${ssrRenderStyle({ "height": "30vh" })}" class="overflow-auto p-1"${_scopeId}><!--[-->`);
        ssrRenderList($data.contributorType, (item) => {
          _push2(`<div class="relative flex items-start mt-2"${_scopeId}><div class="cursor-pointer flex-1 border rounded-md p-2 bg-white-200 hover:bg-gray-200"${_scopeId}><div class="text-gray-900"${_scopeId}><b${_scopeId}>${ssrInterpolate(item.title)}</b> <br${_scopeId}><p class="text-xs align-top"${_scopeId}>${item.description}</p></div></div></div>`);
        });
        _push2(`<!--]--></div>`);
      } else {
        return [
          createVNode("div", {
            style: { "height": "30vh" },
            class: "overflow-auto p-1"
          }, [
            (openBlock(true), createBlock(Fragment, null, renderList($data.contributorType, (item) => {
              return openBlock(), createBlock("div", {
                key: item.title,
                class: "relative flex items-start mt-2"
              }, [
                createVNode("div", {
                  class: "cursor-pointer flex-1 border rounded-md p-2 bg-white-200 hover:bg-gray-200",
                  onClick: ($event) => $options.updateRole(item)
                }, [
                  createVNode("div", { class: "text-gray-900" }, [
                    createVNode("b", null, toDisplayString(item.title), 1),
                    createTextVNode(),
                    createVNode("br"),
                    createVNode("p", {
                      class: "text-xs align-top",
                      innerHTML: item.description
                    }, null, 8, ["innerHTML"])
                  ])
                ], 8, ["onClick"])
              ]);
            }), 128))
          ])
        ];
      }
    }),
    footer: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_secondary_button, {
          onClick: ($event) => $data.showManageRoleDialog = false
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
            onClick: ($event) => $data.showManageRoleDialog = false
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
  _push(`<!--]-->`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/ManageAuthor.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const ManageAuthor = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {
  components: {
    JetDialogModal,
    JetSecondaryButton,
    JetDangerButton,
    JetButton,
    PencilIcon,
    TrashIcon,
    PlusIcon,
    FolderPlusIcon,
    ArrowSmallRightIcon,
    JetInputError,
    LoadingButton,
    Draggable
  },
  props: ["project"],
  data() {
    return {
      form: this.$inertia.form({
        title: "",
        doi: "",
        title: "",
        authors: "",
        abstract: "",
        citation_text: "",
        errors: {}
      }),
      citationsForm: this.$inertia.form({
        citations: []
      }),
      fetchedCitations: {},
      showDialog: false,
      citations: [],
      confirmDelete: false,
      displayAddCitationForms: false,
      selectedCitation: null,
      query: "",
      loading: false,
      isEdit: false,
      error: ""
    };
  },
  mounted() {
    this.loadInitial();
  },
  methods: {
    loadInitial() {
      if (this.project && this.project.citations) {
        this.citations = this.project.citations;
      }
    },
    /*Control the display toggling*/
    toggleDialog() {
      this.showDialog = !this.showDialog;
    },
    onClose() {
      this.showDialog = false;
    },
    /*Fetch citation from DOI*/
    fetchCitations() {
      this.loading = true;
      this.error = "";
      this.query = this.extractDoi(this.query);
      new RegExp(/\b(10[.][0-9]{4,}(?:[.][0-9]+)*)\b/g).test(
        this.query
      );
      axios.get(this.$page.props.europemcWSApi, {
        params: {
          query: "DOI:" + this.query,
          format: "json",
          pageSize: "1",
          resulttype: "core",
          synonym: "true"
        }
      }).then((res) => {
        if (res && res.data && res.data.resultList.result.length > 0) {
          this.fetchedCitations = this.formatCitationResponse(
            res.data.resultList.result[0],
            "europemc"
          );
        } else {
          this.fetchDataFromCrossref(this.query);
        }
      }).catch((err) => {
        console.log(err);
      }).finally(() => {
        this.loading = false;
      });
    },
    /*Make REST Call to Crossref API */
    fetchDataFromCrossref(query) {
      this.fetchedCitations = [];
      axios.get(this.$page.props.CROSSREF_API + this.query).then((res) => {
        if (res.data && res.data.message) {
          this.fetchedCitations = this.formatCitationResponse(
            res.data.message,
            "crossref"
          );
        }
      }).catch((err) => {
        console.log(err);
      }).finally(() => {
        if (this.fetchedCitations && this.fetchedCitations.length == 0) {
          this.error = "No data found. Please enter the details manually.";
        }
        this.loading = false;
      });
    },
    /*Edit Citation */
    edit(citation) {
      this.selectedCitation = citation;
      this.citations = this.citations.filter((citation2) => {
        return citation2.title != this.selectedCitation.title;
      });
      for (var key in citation) {
        this.form[key] = citation.hasOwnProperty(key) ? citation[key] : null;
      }
      this.displayAddCitationForms = true;
      this.isEdit = true;
      this.fetchedCitations = {};
      this.query = "";
    },
    /*Make the call to save API on the basis of selection*/
    save(input) {
      switch (input) {
        case "addFetched":
          this.addFetchedCitation();
          break;
        case "addManually":
          this.addManually();
          break;
      }
    },
    /*Prepare request form and execute save query for citations added manually*/
    addManually() {
      this.validateForm();
      if (!this.form.hasErrors) {
        this.citationsForm.reset();
        let _citation = {};
        for (var key in this.form) {
          _citation[key] = this.form.hasOwnProperty(key) ? this.form[key] : null;
        }
        this.citationsForm.citations.push(_citation);
        this.executeQuery();
      }
    },
    addFetchedCitation() {
      this.citationsForm.reset();
      if (this.citations.length > 0) {
        this.citations = this.citations.concat(this.fetchedCitations);
      } else {
        this.citations.push(this.fetchedCitations);
      }
      this.executeQuery();
    },
    /*Make request to save API*/
    executeQuery() {
      if (this.citations.length > 0) {
        let _citationObj = {};
        this.citations.forEach((citation) => {
          for (var key in citation) {
            _citationObj[key] = citation.hasOwnProperty(key) ? citation[key] : null;
          }
          this.citationsForm.citations.push(_citationObj);
          _citationObj = {};
        });
      }
      const keys = ["title"];
      this.citationsForm.citations = this.citationsForm.citations.filter(
        (value, index, self) => self.findIndex(
          (v) => keys.every((k) => v[k] === value[k])
        ) === index
      );
      this.citationsForm.post(route("citation.save", this.project.id), {
        preserveScroll: true,
        onSuccess: () => {
          router.reload({ only: ["project"] });
          this.citationsForm.reset();
          this.loadInitial();
          this.form.reset();
          this.displayAddCitationForms = false;
          this.isEdit = false;
        },
        onError: (err) => console.error(err)
      });
    },
    /*Check for validation error*/
    validateForm() {
      this.form.errors = {};
      this.form.hasErrors = false;
      var hasErrors = false;
      if (!this.form.title) {
        this.form.errors.title = "The title field is required.";
        hasErrors = true;
      }
      if (!this.form.authors) {
        this.form.errors.authors = "The authors field is required.";
        hasErrors = true;
      }
      if (hasErrors) {
        this.form.hasErrors = true;
      }
    },
    /*Format response recived from Europemc API*/
    formatCitationResponse(obj, apiType) {
      var journalTitle = "";
      var yearofPublication = "";
      var volume = "";
      var issue = "";
      var pageInfo = "";
      this.formattedCitationRes = {};
      if (obj) {
        switch (apiType) {
          case "europemc":
            if (obj.journalInfo) {
              journalTitle = obj.journalInfo.journal.title ? obj.journalInfo.journal.title : "";
              yearofPublication = obj.journalInfo.yearOfPublication ? obj.journalInfo.yearOfPublication : "";
              volume = obj.journalInfo.volume ? obj.journalInfo.volume : "";
              issue = obj.journalInfo.issue ? obj.journalInfo.issue : "";
            }
            pageInfo = obj.pageInfo ? obj.pageInfo : "";
            this.formattedCitationRes.title = obj.title ? obj.title : "";
            this.formattedCitationRes.authors = obj.authorString ? obj.authorString : "";
            this.formattedCitationRes.citation_text = journalTitle + " " + yearofPublication + " " + volume + " ( " + issue + " ) " + pageInfo;
            this.formattedCitationRes.doi = obj.doi ? obj.doi : "";
            this.formattedCitationRes.abstract = obj.abstractText ? obj.abstractText : "";
            break;
          case "crossref":
            journalTitle = obj.title[0];
            yearofPublication = obj["published-online"]["date-parts"] ? obj["published-online"]["date-parts"][0][0] : "";
            volume = obj.volume ? obj.volume : "";
            issue = obj.issue ? obj.issue : "";
            pageInfo = obj.page ? obj.page : "";
            this.formattedCitationRes.title = journalTitle;
            if (obj.author) {
              this.formattedCitationRes.authors = obj.author.map(
                (author) => author.given + " " + author.family
              ).join(", ");
            }
            this.formattedCitationRes.citation_text = journalTitle + " " + yearofPublication + " " + volume + " ( " + issue + " ) " + pageInfo;
            this.formattedCitationRes.doi = obj.DOI ? obj.DOI : "";
            this.formattedCitationRes.abstract = obj.abstract ? obj.abstract : "";
            break;
        }
      }
      return this.formattedCitationRes;
    },
    /*Confirm delete and prepare request*/
    confirmDeletion(citation) {
      this.confirmDelete = true;
      this.citationsForm.reset();
      this.citationsForm.citations = [
        {
          id: citation.id ? citation.id : null,
          title: citation.tile ? citation.title : null,
          doi: citation.doi ? citation.doi : null,
          title: citation.title ? citation.title : null,
          authors: citation.authors ? citation.authors : null,
          abstract: citation.abstract ? citation.abstract : null,
          citation_text: citation.citation_text ? citation.citation_text : null
        }
      ];
    },
    /*Make request to delete API*/
    deleteCitation() {
      this.citationsForm.delete(
        route("citation.delete", this.project.id),
        {
          preserveScroll: true,
          onSuccess: () => {
            router.reload({ only: ["project"] });
            this.loadInitial();
            this.citationsForm.reset();
            this.confirmDelete = false;
          },
          onError: (err) => console.error(err)
        }
      );
    },
    onCancelEdit() {
      if (this.selectedCitation) {
        this.citations.push(this.selectedCitation);
      }
      this.displayAddCitationForms = false;
      this.isEdit = false;
      this.form.reset();
    },
    onBack() {
      if (this.selectedCitation) {
        this.citations.push(this.selectedCitation);
      }
      this.displayAddCitationForms = false;
      this.isEdit = false;
      this.fetchedCitations = {};
      this.query = "";
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_dialog_modal = resolveComponent("jet-dialog-modal");
  const _component_PlusIcon = resolveComponent("PlusIcon");
  const _component_ArrowSmallRightIcon = resolveComponent("ArrowSmallRightIcon");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  const _component_loading_button = resolveComponent("loading-button");
  const _component_draggable = resolveComponent("draggable");
  const _component_PencilIcon = resolveComponent("PencilIcon");
  const _component_TrashIcon = resolveComponent("TrashIcon");
  const _component_FolderPlusIcon = resolveComponent("FolderPlusIcon");
  const _component_jet_danger_button = resolveComponent("jet-danger-button");
  _push(ssrRenderComponent(_component_jet_dialog_modal, mergeProps({
    show: $data.showDialog,
    "max-width": "6xl",
    onClose: ($event) => $data.showDialog = false
  }, _attrs), {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`${ssrInterpolate($props.project.name)} - Manage Citations `);
        if (!$data.displayAddCitationForms) {
          _push2(`<button type="button" class="inline-flex float-right items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_PlusIcon, { class: "w-5 h-5 mr-1 text-white" }, null, _parent2, _scopeId));
          _push2(` Add Citation </button>`);
        } else {
          _push2(`<button type="button" class="inline-flex float-right items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_ArrowSmallRightIcon, { class: "w-5 h-5 mr-1 text-white" }, null, _parent2, _scopeId));
          _push2(` Back </button>`);
        }
      } else {
        return [
          createTextVNode(toDisplayString($props.project.name) + " - Manage Citations ", 1),
          !$data.displayAddCitationForms ? (openBlock(), createBlock("button", {
            key: 0,
            type: "button",
            class: "inline-flex float-right items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2",
            onClick: ($event) => $data.displayAddCitationForms = true
          }, [
            createVNode(_component_PlusIcon, { class: "w-5 h-5 mr-1 text-white" }),
            createTextVNode(" Add Citation ")
          ], 8, ["onClick"])) : (openBlock(), createBlock("button", {
            key: 1,
            type: "button",
            class: "inline-flex float-right items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2",
            onClick: $options.onBack
          }, [
            createVNode(_component_ArrowSmallRightIcon, { class: "w-5 h-5 mr-1 text-white" }),
            createTextVNode(" Back ")
          ], 8, ["onClick"]))
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div${_scopeId}>`);
        if ($data.displayAddCitationForms) {
          _push2(`<div${_scopeId}><div class="relative grid grid-cols-1 gap-x-4 max-w-7xl mx-auto lg:grid-cols-2"${_scopeId}><div class="px-4 sm:px-6 lg:pb-5 lg:px-0 lg:row-start-1 lg:col-start-1"${_scopeId}><div${_scopeId}><p class="text-sm leading-6 font-bold text-gray-900"${_scopeId}>`);
          if (!$data.isEdit) {
            _push2(`<span${_scopeId}>Add</span>`);
          } else {
            _push2(`<span${_scopeId}>Update</span>`);
          }
          _push2(` Citation </p><div class="mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2"${_scopeId}><div class="sm:col-span-1"${_scopeId}><label for="title" class="block text-sm font-medium text-gray-700"${_scopeId}> DOI </label><div class="mt-1"${_scopeId}><input id="doi"${ssrRenderAttr("value", $data.form.doi)} type="text" name="doi" placeholder="DOI ID e.g. 10.1186/s19991-022-00987-0" class="${ssrRenderClass([
            $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
            "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
          ])}"${_scopeId}></div>`);
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.form.errors.doi,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          _push2(`</div><div class="sm:col-span-4"${_scopeId}><label for="family-name" class="block text-sm font-medium text-gray-700 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId}> Title </label><div class="mt-1"${_scopeId}><input id="title"${ssrRenderAttr("value", $data.form.title)} type="text" name="title" autocomplete="title" class="${ssrRenderClass([
            $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
            "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
          ])}"${_scopeId}></div>`);
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.form.errors.title,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          _push2(`</div><div class="sm:col-span-6"${_scopeId}><label for="given-name" class="block text-sm font-medium text-gray-700 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId}> Authors </label><div class="mt-1"${_scopeId}><textarea id="authors" type="text" name="authors" placeholder="e.g. Pupier S, Nuzillard MK, Wist P, Schlörer AK." class="${ssrRenderClass([
            $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
            "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
          ])}"${_scopeId}>${ssrInterpolate($data.form.authors)}</textarea></div>`);
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.form.errors.authors,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          _push2(`</div><div class="sm:col-span-6"${_scopeId}><label for="abstract" class="block text-sm font-medium text-gray-700"${_scopeId}> Abstract </label><div class="mt-1"${_scopeId}><textarea id="abstract" name="abstract" type="abstract" autocomplete="abstract" class="${ssrRenderClass([
            $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
            "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
          ])}"${_scopeId}>${ssrInterpolate($data.form.abstract)}</textarea>`);
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.form.errors.abstract,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          _push2(`</div></div><div class="sm:col-span-6"${_scopeId}><label for="orcid" class="block text-sm font-medium text-gray-700"${_scopeId}> Citation Text </label><div class="mt-1"${_scopeId}><textarea id="citation-text" name="citation-text" autocomplete="citation-text" placeholder="&lt;journal title&gt; &lt;year of publication&gt; &lt;volume&gt; &lt;(issue)&gt; &lt;page info&gt; e.g. - Magnetic resonance in chemistry : MRC 2018 56 ( 8 ) 703-715 or please provide any relevant information." type="text" class="${ssrRenderClass([
            $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
            "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
          ])}"${_scopeId}>${ssrInterpolate($data.form.citation_text)}</textarea></div></div>`);
          if (!$data.isEdit) {
            _push2(`<div class="sm:col-span-6 float-left"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_jet_secondary_button, {
              class: "float-right",
              disabled: !($data.form && $data.form.title),
              onClick: ($event) => $options.save("addManually")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` Add `);
                } else {
                  return [
                    createTextVNode(" Add ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_jet_secondary_button, {
              class: "float-right mr-2",
              disabled: !($data.form && $data.form.title),
              onClick: ($event) => ($data.form.reset(), $data.citationsForm.reset())
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` Clear `);
                } else {
                  return [
                    createTextVNode(" Clear ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div>`);
          } else {
            _push2(`<div class="sm:col-span-6 float-left"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_jet_secondary_button, {
              class: "float-right",
              disabled: !($data.form && $data.form.title),
              onClick: ($event) => $options.save("addManually")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` Update `);
                } else {
                  return [
                    createTextVNode(" Update ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_jet_secondary_button, {
              class: "float-right mr-2",
              disabled: !($data.form && $data.form.title),
              onClick: ($event) => $options.onCancelEdit()
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
            _push2(`</div>`);
          }
          _push2(`</div></div></div><div class="lg:px-1 lg:row-start-1 lg:col-start-2 border-l"${_scopeId}><div class="pl-2"${_scopeId}><p class="text-sm leading-6 font-bold text-gray-900"${_scopeId}> Import From </p><div class="mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2"${_scopeId}><div class="sm:col-span-2"${_scopeId}><label for="name" class="block text-sm font-medium text-gray-700"${_scopeId}> DOI </label><div class="mt-1 flex rounded-md shadow-sm"${_scopeId}><input id="query"${ssrRenderAttr("value", $data.query)} type="text" name="query" placeholder="DOI ID e.g. 10.1186/s19991-022-00987-0" autocomplete="off" class="flex-1 focus:ring-teal-500 focus:border-teal-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"${_scopeId}></div></div></div><div class="sm:col-span-2 mt-4"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_jet_secondary_button, {
            disabled: $data.query == "" || !$data.query,
            onClick: $options.fetchCitations
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Import `);
              } else {
                return [
                  createTextVNode(" Import ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_secondary_button, {
            class: "ml-2",
            disabled: _ctx.isEmpty($data.fetchedCitations),
            onClick: ($event) => ($data.fetchedCitations = {}, $data.query = null)
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Reset `);
              } else {
                return [
                  createTextVNode(" Reset ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div>`);
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.error,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          if ($data.loading) {
            _push2(`<div class="sm:col-span-9 mt-4 align-centre"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_loading_button, { loading: $data.loading }, null, _parent2, _scopeId));
            _push2(`</div>`);
          } else {
            _push2(`<!---->`);
          }
          if (!(_ctx.isEmpty($data.fetchedCitations) || $options.fetchCitations == null) && !$data.loading) {
            _push2(`<div class="mt-4"${_scopeId}><fieldset class="border rounded border-gray-200 overflow-auto"${_scopeId}><legend class="sr-only"${_scopeId}> Notifications </legend><div style="${ssrRenderStyle({ "height": "40vh" })}" class="divide-y divide-gray-200"${_scopeId}><div class="relative flex items-start p-4"${_scopeId}><div class="min-w-0 flex-1 text-sm"${_scopeId}><label for="citation" class="font-medium text-gray-700"${_scopeId}>${ssrInterpolate($data.fetchedCitations.title)}</label><p id="citation-authors" class="text-teal-500"${_scopeId}>${ssrInterpolate($data.fetchedCitations.authors)}</p><p id="citation-text" class="text-gray-500"${_scopeId}>${ssrInterpolate($data.fetchedCitations.citation_text)}</p><p id="citation-doi" class="text-gray-500 font-bold"${_scopeId}> DOI - ${ssrInterpolate($data.fetchedCitations.doi)}</p>`);
            if ($data.fetchedCitations.abstract) {
              _push2(`<p id="citation-doi" class="font-bold text-gray-500 mt-2"${_scopeId}> Abstract </p>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div id="citation-abstract" class="text-xs text-gray-500"${_scopeId}>${$data.fetchedCitations.abstract}</div></div></div></div></fieldset><div${_scopeId}>`);
            _push2(ssrRenderComponent(_component_jet_secondary_button, {
              class: "float-right text-md font-bold text-teal-900 mt-4",
              onClick: ($event) => $options.save("addFetched")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` Add `);
                } else {
                  return [
                    createTextVNode(" Add ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div></div></div></div>`);
        } else {
          _push2(`<!---->`);
        }
        if ($data.citations.length > 0 && !$data.displayAddCitationForms) {
          _push2(`<div style="${ssrRenderStyle({ "height": "60vh" })}" class="sm:rounded-md overflow-y-scroll"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_draggable, {
            modelValue: $data.citations,
            "onUpdate:modelValue": ($event) => $data.citations = $event,
            "item-key": "citation.id",
            group: "author",
            onStart: ($event) => _ctx.drag = true,
            onEnd: ($event) => _ctx.drag = false
          }, {
            item: withCtx(({ element }, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(`<div class="overflow-auto"${_scopeId2}><ul role="list" class="divide-y divide-gray-900"${_scopeId2}><li${_scopeId2}><div class="px-4 border rounded-md mb-1 py-4 sm:px-6"${_scopeId2}><div class="flex items-center cursor justify-between"${_scopeId2}><p class="text-sm font-medium text-teal-900"${_scopeId2}>${ssrInterpolate(element.title)}</p></div><div class="mt-1 sm:flex sm:justify-between"${_scopeId2}><div class="sm:flex"${_scopeId2}><p class="flex items-center text-xs font-small text-gray-500 break-words"${_scopeId2}>${ssrInterpolate(element.authors)}</p></div><div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0"${_scopeId2}><button type="button" class="inline-flex items-center p-1 border border-transparent"${_scopeId2}>`);
                _push3(ssrRenderComponent(_component_PencilIcon, { class: "w-3.5 h-3.5 mr-1 text-gray-600" }, null, _parent3, _scopeId2));
                _push3(`</button><button type="button" class="inline-flex items-center p-1 border border-transparent"${_scopeId2}>`);
                _push3(ssrRenderComponent(_component_TrashIcon, { class: "w-3.5 h-3.5 mr-1 text-gray-600" }, null, _parent3, _scopeId2));
                _push3(`</button></div></div><div class="sm:flex sm:justify-between"${_scopeId2}>`);
                if (element.doi) {
                  _push3(`<p class="text-xs font-medium text-teal-900"${_scopeId2}><b class="text-gray-500"${_scopeId2}>DOI: </b> ${ssrInterpolate(element.doi)}</p>`);
                } else {
                  _push3(`<!---->`);
                }
                _push3(`</div><div class="sm:flex sm:justify-between"${_scopeId2}>`);
                if (element.abstract) {
                  _push3(`<div class="text-xs font-medium text-gray-900"${_scopeId2}>${element.abstract}</div>`);
                } else {
                  _push3(`<!---->`);
                }
                _push3(`</div></div></li></ul></div>`);
              } else {
                return [
                  createVNode("div", { class: "overflow-auto" }, [
                    createVNode("ul", {
                      role: "list",
                      class: "divide-y divide-gray-900"
                    }, [
                      createVNode("li", null, [
                        createVNode("div", { class: "px-4 border rounded-md mb-1 py-4 sm:px-6" }, [
                          createVNode("div", { class: "flex items-center cursor justify-between" }, [
                            createVNode("p", { class: "text-sm font-medium text-teal-900" }, toDisplayString(element.title), 1)
                          ]),
                          createVNode("div", { class: "mt-1 sm:flex sm:justify-between" }, [
                            createVNode("div", { class: "sm:flex" }, [
                              createVNode("p", { class: "flex items-center text-xs font-small text-gray-500 break-words" }, toDisplayString(element.authors), 1)
                            ]),
                            createVNode("div", { class: "mt-2 flex items-center text-sm text-gray-500 sm:mt-0" }, [
                              createVNode("button", {
                                type: "button",
                                class: "inline-flex items-center p-1 border border-transparent",
                                onClick: ($event) => $options.edit(element)
                              }, [
                                createVNode(_component_PencilIcon, { class: "w-3.5 h-3.5 mr-1 text-gray-600" })
                              ], 8, ["onClick"]),
                              createVNode("button", {
                                type: "button",
                                class: "inline-flex items-center p-1 border border-transparent",
                                onClick: ($event) => $options.confirmDeletion(
                                  element
                                )
                              }, [
                                createVNode(_component_TrashIcon, { class: "w-3.5 h-3.5 mr-1 text-gray-600" })
                              ], 8, ["onClick"])
                            ])
                          ]),
                          createVNode("div", { class: "sm:flex sm:justify-between" }, [
                            element.doi ? (openBlock(), createBlock("p", {
                              key: 0,
                              class: "text-xs font-medium text-teal-900"
                            }, [
                              createVNode("b", { class: "text-gray-500" }, "DOI: "),
                              createTextVNode(" " + toDisplayString(element.doi), 1)
                            ])) : createCommentVNode("", true)
                          ]),
                          createVNode("div", { class: "sm:flex sm:justify-between" }, [
                            element.abstract ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-xs font-medium text-gray-900",
                              innerHTML: element.abstract
                            }, null, 8, ["innerHTML"])) : createCommentVNode("", true)
                          ])
                        ])
                      ])
                    ])
                  ])
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div>`);
        if ($data.citations.length == 0 && !$data.displayAddCitationForms) {
          _push2(`<div class="py-5"${_scopeId}><div class="text-center"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_FolderPlusIcon, { class: "mx-auto h-12 w-12 text-gray-400" }, null, _parent2, _scopeId));
          _push2(`<h3 class="mt-2 text-sm font-medium text-gray-900"${_scopeId}> No Citations Listed </h3><p class="mt-1 text-sm text-gray-500"${_scopeId}> Get started by adding a new citation. </p><div class="mt-6"${_scopeId}><button type="button" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_PlusIcon, { class: "w-5 h-5 mr-1 text-white" }, null, _parent2, _scopeId));
          _push2(` Add Citation </button></div></div></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(ssrRenderComponent(_component_jet_dialog_modal, {
          show: $data.confirmDelete,
          onClose: ($event) => $data.confirmDelete = false
        }, {
          title: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Delete Citation `);
            } else {
              return [
                createTextVNode(" Delete Citation ")
              ];
            }
          }),
          content: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Are you sure you want to delete this citation? <div class="mt-4"${_scopeId2}></div>`);
            } else {
              return [
                createTextVNode(" Are you sure you want to delete this citation? "),
                createVNode("div", { class: "mt-4" })
              ];
            }
          }),
          footer: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_jet_secondary_button, {
                onClick: ($event) => $data.confirmDelete = false
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Cancel `);
                  } else {
                    return [
                      createTextVNode(" Cancel ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(ssrRenderComponent(_component_jet_danger_button, {
                class: ["ml-2", { "opacity-25": $data.form.processing }],
                disabled: $data.form.processing,
                onClick: ($event) => $options.deleteCitation()
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Delete Citation `);
                  } else {
                    return [
                      createTextVNode(" Delete Citation ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
            } else {
              return [
                createVNode(_component_jet_secondary_button, {
                  onClick: ($event) => $data.confirmDelete = false
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Cancel ")
                  ]),
                  _: 1
                }, 8, ["onClick"]),
                createVNode(_component_jet_danger_button, {
                  class: ["ml-2", { "opacity-25": $data.form.processing }],
                  disabled: $data.form.processing,
                  onClick: ($event) => $options.deleteCitation()
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Delete Citation ")
                  ]),
                  _: 1
                }, 8, ["class", "disabled", "onClick"])
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode("div", null, [
            $data.displayAddCitationForms ? (openBlock(), createBlock("div", { key: 0 }, [
              createVNode("div", { class: "relative grid grid-cols-1 gap-x-4 max-w-7xl mx-auto lg:grid-cols-2" }, [
                createVNode("div", { class: "px-4 sm:px-6 lg:pb-5 lg:px-0 lg:row-start-1 lg:col-start-1" }, [
                  createVNode("div", null, [
                    createVNode("p", { class: "text-sm leading-6 font-bold text-gray-900" }, [
                      !$data.isEdit ? (openBlock(), createBlock("span", { key: 0 }, "Add")) : (openBlock(), createBlock("span", { key: 1 }, "Update")),
                      createTextVNode(" Citation ")
                    ]),
                    createVNode("div", { class: "mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2" }, [
                      createVNode("div", { class: "sm:col-span-1" }, [
                        createVNode("label", {
                          for: "title",
                          class: "block text-sm font-medium text-gray-700"
                        }, " DOI "),
                        createVNode("div", { class: "mt-1" }, [
                          withDirectives(createVNode("input", {
                            id: "doi",
                            "onUpdate:modelValue": ($event) => $data.form.doi = $event,
                            type: "text",
                            name: "doi",
                            placeholder: "DOI ID e.g. 10.1186/s19991-022-00987-0",
                            class: [
                              $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
                              "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            ]
                          }, null, 10, ["onUpdate:modelValue"]), [
                            [vModelText, $data.form.doi]
                          ])
                        ]),
                        createVNode(_component_jet_input_error, {
                          message: $data.form.errors.doi,
                          class: "mt-2"
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "sm:col-span-4" }, [
                        createVNode("label", {
                          for: "family-name",
                          class: "block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                        }, " Title "),
                        createVNode("div", { class: "mt-1" }, [
                          withDirectives(createVNode("input", {
                            id: "title",
                            "onUpdate:modelValue": ($event) => $data.form.title = $event,
                            type: "text",
                            name: "title",
                            autocomplete: "title",
                            class: [
                              $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
                              "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            ]
                          }, null, 10, ["onUpdate:modelValue"]), [
                            [vModelText, $data.form.title]
                          ])
                        ]),
                        createVNode(_component_jet_input_error, {
                          message: $data.form.errors.title,
                          class: "mt-2"
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "sm:col-span-6" }, [
                        createVNode("label", {
                          for: "given-name",
                          class: "block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                        }, " Authors "),
                        createVNode("div", { class: "mt-1" }, [
                          withDirectives(createVNode("textarea", {
                            id: "authors",
                            "onUpdate:modelValue": ($event) => $data.form.authors = $event,
                            type: "text",
                            name: "authors",
                            placeholder: "e.g. Pupier S, Nuzillard MK, Wist P, Schlörer AK.",
                            class: [
                              $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
                              "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            ]
                          }, null, 10, ["onUpdate:modelValue"]), [
                            [vModelText, $data.form.authors]
                          ])
                        ]),
                        createVNode(_component_jet_input_error, {
                          message: $data.form.errors.authors,
                          class: "mt-2"
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "sm:col-span-6" }, [
                        createVNode("label", {
                          for: "abstract",
                          class: "block text-sm font-medium text-gray-700"
                        }, " Abstract "),
                        createVNode("div", { class: "mt-1" }, [
                          withDirectives(createVNode("textarea", {
                            id: "abstract",
                            "onUpdate:modelValue": ($event) => $data.form.abstract = $event,
                            name: "abstract",
                            type: "abstract",
                            autocomplete: "abstract",
                            class: [
                              $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
                              "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            ]
                          }, null, 10, ["onUpdate:modelValue"]), [
                            [vModelText, $data.form.abstract]
                          ]),
                          createVNode(_component_jet_input_error, {
                            message: $data.form.errors.abstract,
                            class: "mt-2"
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "sm:col-span-6" }, [
                        createVNode("label", {
                          for: "orcid",
                          class: "block text-sm font-medium text-gray-700"
                        }, " Citation Text "),
                        createVNode("div", { class: "mt-1" }, [
                          withDirectives(createVNode("textarea", {
                            id: "citation-text",
                            "onUpdate:modelValue": ($event) => $data.form.citation_text = $event,
                            name: "citation-text",
                            autocomplete: "citation-text",
                            placeholder: "<journal title> <year of publication> <volume> <(issue)> <page info> e.g. - Magnetic resonance in chemistry : MRC 2018 56 ( 8 ) 703-715 or please provide any relevant information.",
                            type: "text",
                            class: [
                              $data.isEdit ? "shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100" : "",
                              "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            ]
                          }, null, 10, ["onUpdate:modelValue"]), [
                            [vModelText, $data.form.citation_text]
                          ])
                        ])
                      ]),
                      !$data.isEdit ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "sm:col-span-6 float-left"
                      }, [
                        createVNode(_component_jet_secondary_button, {
                          class: "float-right",
                          disabled: !($data.form && $data.form.title),
                          onClick: ($event) => $options.save("addManually")
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Add ")
                          ]),
                          _: 1
                        }, 8, ["disabled", "onClick"]),
                        createVNode(_component_jet_secondary_button, {
                          class: "float-right mr-2",
                          disabled: !($data.form && $data.form.title),
                          onClick: ($event) => ($data.form.reset(), $data.citationsForm.reset())
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Clear ")
                          ]),
                          _: 1
                        }, 8, ["disabled", "onClick"])
                      ])) : (openBlock(), createBlock("div", {
                        key: 1,
                        class: "sm:col-span-6 float-left"
                      }, [
                        createVNode(_component_jet_secondary_button, {
                          class: "float-right",
                          disabled: !($data.form && $data.form.title),
                          onClick: ($event) => $options.save("addManually")
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Update ")
                          ]),
                          _: 1
                        }, 8, ["disabled", "onClick"]),
                        createVNode(_component_jet_secondary_button, {
                          class: "float-right mr-2",
                          disabled: !($data.form && $data.form.title),
                          onClick: ($event) => $options.onCancelEdit()
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Cancel ")
                          ]),
                          _: 1
                        }, 8, ["disabled", "onClick"])
                      ]))
                    ])
                  ])
                ]),
                createVNode("div", { class: "lg:px-1 lg:row-start-1 lg:col-start-2 border-l" }, [
                  createVNode("div", { class: "pl-2" }, [
                    createVNode("p", { class: "text-sm leading-6 font-bold text-gray-900" }, " Import From "),
                    createVNode("div", { class: "mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2" }, [
                      createVNode("div", { class: "sm:col-span-2" }, [
                        createVNode("label", {
                          for: "name",
                          class: "block text-sm font-medium text-gray-700"
                        }, " DOI "),
                        createVNode("div", { class: "mt-1 flex rounded-md shadow-sm" }, [
                          withDirectives(createVNode("input", {
                            id: "query",
                            "onUpdate:modelValue": ($event) => $data.query = $event,
                            type: "text",
                            name: "query",
                            placeholder: "DOI ID e.g. 10.1186/s19991-022-00987-0",
                            autocomplete: "off",
                            class: "flex-1 focus:ring-teal-500 focus:border-teal-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                          }, null, 8, ["onUpdate:modelValue"]), [
                            [vModelText, $data.query]
                          ])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "sm:col-span-2 mt-4" }, [
                      createVNode(_component_jet_secondary_button, {
                        disabled: $data.query == "" || !$data.query,
                        onClick: $options.fetchCitations
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Import ")
                        ]),
                        _: 1
                      }, 8, ["disabled", "onClick"]),
                      createVNode(_component_jet_secondary_button, {
                        class: "ml-2",
                        disabled: _ctx.isEmpty($data.fetchedCitations),
                        onClick: ($event) => ($data.fetchedCitations = {}, $data.query = null)
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Reset ")
                        ]),
                        _: 1
                      }, 8, ["disabled", "onClick"])
                    ]),
                    createVNode(_component_jet_input_error, {
                      message: $data.error,
                      class: "mt-2"
                    }, null, 8, ["message"]),
                    $data.loading ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "sm:col-span-9 mt-4 align-centre"
                    }, [
                      createVNode(_component_loading_button, { loading: $data.loading }, null, 8, ["loading"])
                    ])) : createCommentVNode("", true),
                    !(_ctx.isEmpty($data.fetchedCitations) || $options.fetchCitations == null) && !$data.loading ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "mt-4"
                    }, [
                      createVNode("fieldset", { class: "border rounded border-gray-200 overflow-auto" }, [
                        createVNode("legend", { class: "sr-only" }, " Notifications "),
                        createVNode("div", {
                          style: { "height": "40vh" },
                          class: "divide-y divide-gray-200"
                        }, [
                          createVNode("div", { class: "relative flex items-start p-4" }, [
                            createVNode("div", { class: "min-w-0 flex-1 text-sm" }, [
                              createVNode("label", {
                                for: "citation",
                                class: "font-medium text-gray-700"
                              }, toDisplayString($data.fetchedCitations.title), 1),
                              createVNode("p", {
                                id: "citation-authors",
                                class: "text-teal-500"
                              }, toDisplayString($data.fetchedCitations.authors), 1),
                              createVNode("p", {
                                id: "citation-text",
                                class: "text-gray-500"
                              }, toDisplayString($data.fetchedCitations.citation_text), 1),
                              createVNode("p", {
                                id: "citation-doi",
                                class: "text-gray-500 font-bold"
                              }, " DOI - " + toDisplayString($data.fetchedCitations.doi), 1),
                              $data.fetchedCitations.abstract ? (openBlock(), createBlock("p", {
                                key: 0,
                                id: "citation-doi",
                                class: "font-bold text-gray-500 mt-2"
                              }, " Abstract ")) : createCommentVNode("", true),
                              createVNode("div", {
                                id: "citation-abstract",
                                class: "text-xs text-gray-500",
                                innerHTML: $data.fetchedCitations.abstract
                              }, null, 8, ["innerHTML"])
                            ])
                          ])
                        ])
                      ]),
                      createVNode("div", null, [
                        createVNode(_component_jet_secondary_button, {
                          class: "float-right text-md font-bold text-teal-900 mt-4",
                          onClick: ($event) => $options.save("addFetched")
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Add ")
                          ]),
                          _: 1
                        }, 8, ["onClick"])
                      ])
                    ])) : createCommentVNode("", true)
                  ])
                ])
              ])
            ])) : createCommentVNode("", true),
            $data.citations.length > 0 && !$data.displayAddCitationForms ? (openBlock(), createBlock("div", {
              key: 1,
              style: { "height": "60vh" },
              class: "sm:rounded-md overflow-y-scroll"
            }, [
              createVNode(_component_draggable, {
                modelValue: $data.citations,
                "onUpdate:modelValue": ($event) => $data.citations = $event,
                "item-key": "citation.id",
                group: "author",
                onStart: ($event) => _ctx.drag = true,
                onEnd: ($event) => _ctx.drag = false
              }, {
                item: withCtx(({ element }) => [
                  createVNode("div", { class: "overflow-auto" }, [
                    createVNode("ul", {
                      role: "list",
                      class: "divide-y divide-gray-900"
                    }, [
                      createVNode("li", null, [
                        createVNode("div", { class: "px-4 border rounded-md mb-1 py-4 sm:px-6" }, [
                          createVNode("div", { class: "flex items-center cursor justify-between" }, [
                            createVNode("p", { class: "text-sm font-medium text-teal-900" }, toDisplayString(element.title), 1)
                          ]),
                          createVNode("div", { class: "mt-1 sm:flex sm:justify-between" }, [
                            createVNode("div", { class: "sm:flex" }, [
                              createVNode("p", { class: "flex items-center text-xs font-small text-gray-500 break-words" }, toDisplayString(element.authors), 1)
                            ]),
                            createVNode("div", { class: "mt-2 flex items-center text-sm text-gray-500 sm:mt-0" }, [
                              createVNode("button", {
                                type: "button",
                                class: "inline-flex items-center p-1 border border-transparent",
                                onClick: ($event) => $options.edit(element)
                              }, [
                                createVNode(_component_PencilIcon, { class: "w-3.5 h-3.5 mr-1 text-gray-600" })
                              ], 8, ["onClick"]),
                              createVNode("button", {
                                type: "button",
                                class: "inline-flex items-center p-1 border border-transparent",
                                onClick: ($event) => $options.confirmDeletion(
                                  element
                                )
                              }, [
                                createVNode(_component_TrashIcon, { class: "w-3.5 h-3.5 mr-1 text-gray-600" })
                              ], 8, ["onClick"])
                            ])
                          ]),
                          createVNode("div", { class: "sm:flex sm:justify-between" }, [
                            element.doi ? (openBlock(), createBlock("p", {
                              key: 0,
                              class: "text-xs font-medium text-teal-900"
                            }, [
                              createVNode("b", { class: "text-gray-500" }, "DOI: "),
                              createTextVNode(" " + toDisplayString(element.doi), 1)
                            ])) : createCommentVNode("", true)
                          ]),
                          createVNode("div", { class: "sm:flex sm:justify-between" }, [
                            element.abstract ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-xs font-medium text-gray-900",
                              innerHTML: element.abstract
                            }, null, 8, ["innerHTML"])) : createCommentVNode("", true)
                          ])
                        ])
                      ])
                    ])
                  ])
                ]),
                _: 1
              }, 8, ["modelValue", "onUpdate:modelValue", "onStart", "onEnd"])
            ])) : createCommentVNode("", true)
          ]),
          $data.citations.length == 0 && !$data.displayAddCitationForms ? (openBlock(), createBlock("div", {
            key: 0,
            class: "py-5"
          }, [
            createVNode("div", { class: "text-center" }, [
              createVNode(_component_FolderPlusIcon, { class: "mx-auto h-12 w-12 text-gray-400" }),
              createVNode("h3", { class: "mt-2 text-sm font-medium text-gray-900" }, " No Citations Listed "),
              createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " Get started by adding a new citation. "),
              createVNode("div", { class: "mt-6" }, [
                createVNode("button", {
                  type: "button",
                  class: "inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2",
                  onClick: ($event) => $data.displayAddCitationForms = true
                }, [
                  createVNode(_component_PlusIcon, { class: "w-5 h-5 mr-1 text-white" }),
                  createTextVNode(" Add Citation ")
                ], 8, ["onClick"])
              ])
            ])
          ])) : createCommentVNode("", true),
          createVNode(_component_jet_dialog_modal, {
            show: $data.confirmDelete,
            onClose: ($event) => $data.confirmDelete = false
          }, {
            title: withCtx(() => [
              createTextVNode(" Delete Citation ")
            ]),
            content: withCtx(() => [
              createTextVNode(" Are you sure you want to delete this citation? "),
              createVNode("div", { class: "mt-4" })
            ]),
            footer: withCtx(() => [
              createVNode(_component_jet_secondary_button, {
                onClick: ($event) => $data.confirmDelete = false
              }, {
                default: withCtx(() => [
                  createTextVNode(" Cancel ")
                ]),
                _: 1
              }, 8, ["onClick"]),
              createVNode(_component_jet_danger_button, {
                class: ["ml-2", { "opacity-25": $data.form.processing }],
                disabled: $data.form.processing,
                onClick: ($event) => $options.deleteCitation()
              }, {
                default: withCtx(() => [
                  createTextVNode(" Delete Citation ")
                ]),
                _: 1
              }, 8, ["class", "disabled", "onClick"])
            ]),
            _: 1
          }, 8, ["show", "onClose"])
        ];
      }
    }),
    footer: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="flex"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_secondary_button, {
          class: "float-left",
          onClick: $options.onClose
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Close `);
            } else {
              return [
                createTextVNode(" Close ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "flex" }, [
            createVNode(_component_jet_secondary_button, {
              class: "float-left",
              onClick: $options.onClose
            }, {
              default: withCtx(() => [
                createTextVNode(" Close ")
              ]),
              _: 1
            }, 8, ["onClick"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/ManageCitation.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const ManageCitation = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  ManageAuthor as M,
  ManageCitation as a
};
