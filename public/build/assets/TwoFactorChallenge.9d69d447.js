import{_ as b,H as x,a8 as j,ac as k,ae as C,o as c,b as s,e as o,w as m,a as d,F as a,ai as f,a3 as V,f as i,g as n}from"./app.640fc03a.js";import{J}from"./AuthenticationCard.d8731990.js";import{J as w}from"./AuthenticationCardLogo.78a564bc.js";import{J as B}from"./ValidationErrors.c5548fe2.js";/* empty css            */const H={components:{Head:x,JetAuthenticationCard:J,JetAuthenticationCardLogo:w,JetButton:j,JetInput:k,JetLabel:C,JetValidationErrors:B},data(){return{recovery:!1,form:this.$inertia.form({code:"",recovery_code:""})}},methods:{toggleRecovery(){this.recovery^=!0,this.$nextTick(()=>{this.recovery?(this.$refs.recovery_code.focus(),this.form.code=""):(this.$refs.code.focus(),this.form.recovery_code="")})},submit(){this.form.post(this.route("two-factor.login"))}}},R={class:"mb-4 text-sm text-gray-600"},T=i(" Please confirm access to your account by entering the authentication code provided by your authenticator application. "),U=i(" Please confirm access to your account by entering one of your emergency recovery codes. "),F={key:0},L={key:1},N={class:"flex items-center justify-end mt-4"},A=i(" Use a recovery code "),E=i(" Use an authentication code "),P=i(" Log in ");function z(I,t,M,S,e,l){const y=n("Head"),p=n("jet-authentication-card-logo"),v=n("jet-validation-errors"),u=n("jet-label"),_=n("jet-input"),h=n("jet-button"),g=n("jet-authentication-card");return c(),s(a,null,[o(y,{title:"Two-factor Confirmation"}),o(g,null,{logo:m(()=>[o(p)]),default:m(()=>[d("div",R,[e.recovery?(c(),s(a,{key:1},[U],64)):(c(),s(a,{key:0},[T],64))]),o(v,{class:"mb-4"}),d("form",{onSubmit:t[3]||(t[3]=f((...r)=>l.submit&&l.submit(...r),["prevent"]))},[e.recovery?(c(),s("div",L,[o(u,{for:"recovery_code",value:"Recovery Code"}),o(_,{ref:"recovery_code",id:"recovery_code",type:"text",class:"mt-1 block w-full",modelValue:e.form.recovery_code,"onUpdate:modelValue":t[1]||(t[1]=r=>e.form.recovery_code=r),autocomplete:"one-time-code"},null,8,["modelValue"])])):(c(),s("div",F,[o(u,{for:"code",value:"Code"}),o(_,{ref:"code",id:"code",type:"text",inputmode:"numeric",class:"mt-1 block w-full",modelValue:e.form.code,"onUpdate:modelValue":t[0]||(t[0]=r=>e.form.code=r),autofocus:"",autocomplete:"one-time-code"},null,8,["modelValue"])])),d("div",N,[d("button",{type:"button",class:"text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer",onClick:t[2]||(t[2]=f((...r)=>l.toggleRecovery&&l.toggleRecovery(...r),["prevent"]))},[e.recovery?(c(),s(a,{key:1},[E],64)):(c(),s(a,{key:0},[A],64))]),o(h,{class:V(["ml-4",{"opacity-25":e.form.processing}]),disabled:e.form.processing},{default:m(()=>[P]),_:1},8,["class","disabled"])])],32)]),_:1})],64)}var Q=b(H,[["render",z]]);export{Q as default};