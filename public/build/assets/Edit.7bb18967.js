import{_ as l,v as m,l as t,o as _,c as p,w as o,m as e,a as r,t as a}from"./app.e0b65b09.js";import{B as f}from"./BreadCrumbs.22f0134f.js";import h from"./UserProfile.0c038736.js";import x from"./UserPassword.dfbfa3c3.js";import"./FormSection.46da2062.js";const b={metaInfo(){return{title:`${this.form.first_name} ${this.form.last_name}`}},components:{UserProfile:h,UserPassword:x,AppLayout:m,BreadCrumbs:f},props:{edituser:Object},remember:"form",data(){return{pages:[{name:"Console",route:"console",current:!1},{name:"Users",route:"console.users",current:!1},{name:this.edituser.first_name+" "+this.edituser.last_name,route:"console.users.update",current:!0,id:this.edituser.id}]}},methods:{update(){this.form.post(this.route("console.users.update",this.edituser.id),{onSuccess:()=>this.form.reset("password","photo")})}}},v={class:"bg-white border-b"},w={class:"px-12"},y={class:"flex flex-nowrap justify-between py-6"},g={class:"mt-2 md:flex md:items-center md:justify-between"},B={class:"flex-1 min-w-0"},k={class:"flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3"},C={class:"max-w-7xl mx-auto py-10 sm:px-6 lg:px-8"},U=e("div",{class:"hidden sm:block"},[e("div",{class:"py-8"},[e("div",{class:"border-t border-gray-200"})])],-1);function j(P,N,s,S,i,V){const n=t("bread-crumbs"),d=t("user-profile"),c=t("user-password"),u=t("app-layout");return _(),p(u,{title:"Users & Permissions"},{header:o(()=>[e("div",v,[e("div",w,[e("div",y,[e("div",null,[r(n,{pages:i.pages},null,8,["pages"]),e("div",g,[e("div",B,[e("div",k,a(s.edituser.first_name)+" "+a(s.edituser.last_name),1)])])])])])])]),default:o(()=>[e("div",C,[e("div",null,[r(d,{user:s.edituser},null,8,["user"]),U,r(c,{user:s.edituser},null,8,["user"])])])]),_:1})}var O=l(b,[["render",j]]);export{O as default};