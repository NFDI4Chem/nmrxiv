import{_ as f,a6 as w,a8 as g,ac as h,ad as j,ae as b,o as v,c as V,w as e,a as m,e as o,a3 as S,f as a,g as r}from"./app.640fc03a.js";import{J}from"./FormSection.2394acf2.js";/* empty css            */const P={components:{JetActionMessage:w,JetButton:g,JetFormSection:J,JetInput:h,JetInputError:j,JetLabel:b},props:["user"],data(){return{form:this.$inertia.form({password:"",password_confirmation:""})}},methods:{updatePassword(){this.form.put(route("console.users.update-password",this.user.id),{errorBag:"updatePassword",preserveScroll:!0,onSuccess:()=>this.form.reset(),onError:()=>{this.form.errors.password&&(this.form.reset("password","password_confirmation"),this.$refs.password.focus())}})}}},x=a(" Update Password "),y=a(" Ensure password is long, random password to stay secure. "),B={class:"col-span-6 sm:col-span-4"},k={class:"col-span-6 sm:col-span-4"},C=a(" Saved. "),N=a(" Save ");function U(E,t,I,z,s,i){const c=r("jet-label"),p=r("jet-input"),d=r("jet-input-error"),l=r("jet-action-message"),u=r("jet-button"),_=r("jet-form-section");return v(),V(_,{onSubmitted:i.updatePassword},{title:e(()=>[x]),description:e(()=>[y]),form:e(()=>[m("div",B,[o(c,{for:"password",value:"New Password"}),o(p,{id:"password",type:"password",class:"mt-1 block w-full",modelValue:s.form.password,"onUpdate:modelValue":t[0]||(t[0]=n=>s.form.password=n),ref:"password",autocomplete:"new-password"},null,8,["modelValue"]),o(d,{message:s.form.errors.password,class:"mt-2"},null,8,["message"])]),m("div",k,[o(c,{for:"password_confirmation",value:"Confirm Password"}),o(p,{id:"password_confirmation",type:"password",class:"mt-1 block w-full",modelValue:s.form.password_confirmation,"onUpdate:modelValue":t[1]||(t[1]=n=>s.form.password_confirmation=n),autocomplete:"new-password"},null,8,["modelValue"]),o(d,{message:s.form.errors.password_confirmation,class:"mt-2"},null,8,["message"])])]),actions:e(()=>[o(l,{on:s.form.recentlySuccessful,class:"mr-3"},{default:e(()=>[C]),_:1},8,["on"]),o(u,{class:S({"opacity-25":s.form.processing}),disabled:s.form.processing},{default:e(()=>[N]),_:1},8,["class","disabled"])]),_:1},8,["onSubmitted"])}var M=f(P,[["render",U]]);export{M as default};