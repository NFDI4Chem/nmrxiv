import{_ as P,a7 as k,aa as D,ab as b,ac as C,ad as x,af as B,L as V,o as a,c as _,w as e,a as c,e as n,aD as v,a3 as J,d as m,b as l,az as M,F as S,f as s,g as t}from"./app.640fc03a.js";import{L}from"./LoadingButton.c70cd9d1.js";/* empty css            */const N={components:{JetActionSection:k,JetDangerButton:D,JetDialogModal:b,JetInput:C,JetInputError:x,JetSecondaryButton:B,LoadingButton:L,Link:V},data(){return{loading:!1,confirmingProjectDeletion:!1,confirmingProjectDeletionWithoutPassword:!1,form:this.$inertia.form({password:""}),hasPassword:!1}},props:["project"],methods:{confirmProjectDeletion(){this.loading=!0,this.confirmingProjectDeletion=!0,axios.get(route("auth.checkPassword",this.project.id)).then(i=>{this.hasPassword=i.data.hasPassword}).finally(()=>{this.loading=!1})},deleteProject(){this.form.delete(route("dashboard.project.destroy",this.project.id),{preserveScroll:!0,onSuccess:()=>this.closeModal(),onError:()=>this.$refs.password.focus(),onFinish:()=>this.form.reset()})},closeModal(){this.confirmingProjectDeletion=!1,this.confirmingProjectDeletionWithoutPassword=!1,this.form.reset()}}},E=s(" Delete Project "),F=s(" Permanently delete your project. "),K=c("div",{class:"max-w-xl text-sm text-gray-600"}," Once your project is deleted, all of its resources and data will be permanently deleted. Before deleting your project, please download any data or information that you wish to retain. ",-1),z={class:"mt-5"},A=s(" Delete Project "),I=s(" Delete Project "),O={key:0},W=s(" Are you sure you want to delete your project? Once your project is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your project. "),T={class:"mt-4"},U={key:1},Y=s(" Cancel "),q=s(" Delete Project ");function G(i,d,H,Q,o,r){const u=t("jet-danger-button"),p=t("loading-button"),f=t("jet-input"),h=t("jet-input-error"),j=t("jet-secondary-button"),g=t("jet-dialog-modal"),y=t("jet-action-section");return a(),_(y,null,{title:e(()=>[E]),description:e(()=>[F]),content:e(()=>[K,c("div",z,[n(u,{onClick:r.confirmProjectDeletion},{default:e(()=>[A]),_:1},8,["onClick"])]),n(g,{show:o.confirmingProjectDeletion,onClose:r.closeModal},v({title:e(()=>[I]),content:e(()=>[n(p,{loading:this.loading},null,8,["loading"]),this.loading?m("",!0):(a(),l(S,{key:0},[o.hasPassword?(a(),l("span",O,[W,c("div",T,[n(f,{type:"password",class:"mt-1 block w-3/4",placeholder:"Password",ref:"password",modelValue:o.form.password,"onUpdate:modelValue":d[0]||(d[0]=w=>o.form.password=w),onKeyup:M(r.deleteProject,["enter"])},null,8,["modelValue","onKeyup"]),n(h,{message:o.form.errors.password,class:"mt-2"},null,8,["message"])])])):(a(),l("span",U," You need to set your password first before deleting the project. "))],64))]),_:2},[this.loading?void 0:{name:"footer",fn:e(()=>[n(j,{onClick:r.closeModal},{default:e(()=>[Y]),_:1},8,["onClick"]),o.hasPassword?(a(),_(u,{key:0,class:J(["ml-2",{"opacity-25":o.form.processing}]),onClick:r.deleteProject,disabled:o.form.processing},{default:e(()=>[q]),_:1},8,["onClick","class","disabled"])):m("",!0)])}]),1032,["show","onClose"])]),_:1})}var $=P(N,[["render",G]]);export{$ as default};