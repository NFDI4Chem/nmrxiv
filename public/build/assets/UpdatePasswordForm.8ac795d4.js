import{_ as f,a6 as w,a8 as g,ac as h,ad as b,ae as j,o as V,c as v,w as r,a as d,e as o,a3 as P,f as n,g as t}from"./app.640fc03a.js";import{J as S}from"./FormSection.2394acf2.js";/* empty css            */const y={components:{JetActionMessage:w,JetButton:g,JetFormSection:S,JetInput:h,JetInputError:b,JetLabel:j},data(){return{form:this.$inertia.form({current_password:"",password:"",password_confirmation:""})}},methods:{updatePassword(){this.form.put(route("user-password.update"),{errorBag:"updatePassword",preserveScroll:!0,onSuccess:()=>this.form.reset(),onError:()=>{this.form.errors.password&&(this.form.reset("password","password_confirmation"),this.$refs.password.focus()),this.form.errors.current_password&&(this.form.reset("current_password"),this.$refs.current_password.focus())}})}}},J=n(" Update Password "),k=n(" Ensure your account is using a long, random password to stay secure. "),x={class:"col-span-6 sm:col-span-4"},B={class:"col-span-6 sm:col-span-4"},C={class:"col-span-6 sm:col-span-4"},U=n(" Saved. "),N=n(" Save ");function E(F,e,I,z,s,m){const c=t("jet-label"),p=t("jet-input"),l=t("jet-input-error"),u=t("jet-action-message"),i=t("jet-button"),_=t("jet-form-section");return V(),v(_,{onSubmitted:m.updatePassword},{title:r(()=>[J]),description:r(()=>[k]),form:r(()=>[d("div",x,[o(c,{for:"current_password",value:"Current Password"}),o(p,{id:"current_password",type:"password",class:"mt-1 block w-full",modelValue:s.form.current_password,"onUpdate:modelValue":e[0]||(e[0]=a=>s.form.current_password=a),ref:"current_password",autocomplete:"current-password"},null,8,["modelValue"]),o(l,{message:s.form.errors.current_password,class:"mt-2"},null,8,["message"])]),d("div",B,[o(c,{for:"password",value:"New Password"}),o(p,{id:"password",type:"password",class:"mt-1 block w-full",modelValue:s.form.password,"onUpdate:modelValue":e[1]||(e[1]=a=>s.form.password=a),ref:"password",autocomplete:"new-password"},null,8,["modelValue"]),o(l,{message:s.form.errors.password,class:"mt-2"},null,8,["message"])]),d("div",C,[o(c,{for:"password_confirmation",value:"Confirm Password"}),o(p,{id:"password_confirmation",type:"password",class:"mt-1 block w-full",modelValue:s.form.password_confirmation,"onUpdate:modelValue":e[2]||(e[2]=a=>s.form.password_confirmation=a),autocomplete:"new-password"},null,8,["modelValue"]),o(l,{message:s.form.errors.password_confirmation,class:"mt-2"},null,8,["message"])])]),actions:r(()=>[o(u,{on:s.form.recentlySuccessful,class:"mr-3"},{default:r(()=>[U]),_:1},8,["on"]),o(i,{class:P({"opacity-25":s.form.processing}),disabled:s.form.processing},{default:r(()=>[N]),_:1},8,["class","disabled"])]),_:1},8,["onSubmitted"])}var T=f(y,[["render",E]]);export{T as default};