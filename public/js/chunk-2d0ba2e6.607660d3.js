(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2d0ba2e6"],{"35b0":function(t,e,n){"use strict";n.r(e);var o=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"admin-login"},[n("div",{staticClass:"login-box"},[n("h2",[t._v("文档管理系统")]),n("el-tabs",{model:{value:t.active,callback:function(e){t.active=e},expression:"active"}},[n("el-tab-pane",{attrs:{label:"账号登录",name:"first"}},[n("div",{staticClass:"login-form"},[n("el-input",{attrs:{"prefix-icon":"el-icon-user-solid",placeholder:"用户名/手机号"},model:{value:t.formData.username,callback:function(e){t.$set(t.formData,"username",e)},expression:"formData.username"}}),n("el-input",{attrs:{type:"password","prefix-icon":"el-icon-s-goods",placeholder:"输入密码"},model:{value:t.formData.userpass,callback:function(e){t.$set(t.formData,"userpass",e)},expression:"formData.userpass"}}),n("el-input",{staticClass:"code-input",attrs:{"prefix-icon":"el-icon-s-goods",placeholder:"输入图形验证码"},nativeOn:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.login(e)}},model:{value:t.formData.code,callback:function(e){t.$set(t.formData,"code",e)},expression:"formData.code"}},[n("img",{attrs:{slot:"append",src:t.code,alt:""},on:{click:t.getCode},slot:"append"})])],1),t.thirdPartyList.length?n("div",{staticClass:"login-thirdParty"},[n("span",{staticClass:"title"},[t._v("第三方账号登录")]),n("div",{staticClass:"icon-list"},t._l(t.thirdPartyList,(function(e){return n("img",{key:e.name,staticClass:"icon-block",attrs:{src:e.logo,title:e.name},on:{click:function(n){return t.thirdPartyIconClick(e.redirect_url)}}})})),0)]):t._e(),n("el-button",{staticClass:"login-btn",on:{click:t.login}},[t._v("登录")])],1)],1)],1),t._m(0)])},i=[function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"footer"},[t._v("\n    Powered by "),n("a",{attrs:{href:"https://www.w7.cc"}},[t._v("微擎云计算©www.w7.cc")])])}],a=n("1c1e"),s={name:"adminLogin",data:function(){return{autofocus:!1,active:"first",code:"",formData:{username:"",userpass:"",code:""},thirdPartyList:[]}},beforeRouteEnter:function(t,e,n){var o=t.query.code,i=t.query.redirect_url,s=t.query.app_id;o?a["a"].post("/common/auth/third-party-login",{code:o,app_id:s}).then((function(t){t&&t.is_need_bind?n("/bind"):i?window.open(i,"_self"):n("/admin/document")})).catch((function(){n("/login")})):a["a"].post("/common/auth/default-login-url").then((function(t){t?window.open(t,"_self"):n()}))},created:function(){this.getCode(),this.getThirdParty()},methods:{showFind:function(){this.$message({message:"请联系管理员修改或使用密码找回工具修改"})},getCode:function(){var t=this;this.$post("/common/verifycode/image").then((function(e){e.img&&(t.code=e.img)}))},login:function(){var t=this;for(var e in this.formData)if(!this.formData[e])return this.$message("请填写完整表单"),!1;this.$post("/common/auth/login",this.formData).then((function(){var e=t.$message("登录成功");setTimeout((function(){e.close(),t.$route.query&&t.$route.query.redirect_url?window.open(t.$route.query.redirect_url,"_self"):t.$router.push("/admin/document")}),500)})).catch((function(){t.formData.code="",document.getElementsByClassName("el-input__inner")[2].focus(),t.getCode()}))},getThirdParty:function(){var t=this;this.$post("/common/auth/method",{redirect_url:this.$route.query.redirect_url}).then((function(e){t.thirdPartyList=e||[]}))},thirdPartyIconClick:function(t){window.open(t,"_self")}}},r=s,c=n("2877"),l=Object(c["a"])(r,o,i,!1,null,null,null);e["default"]=l.exports}}]);