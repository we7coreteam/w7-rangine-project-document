(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-6c2f4520"],{"02f4":function(t,e,n){var r=n("4588"),o=n("be13");t.exports=function(t){return function(e,n){var a,i,c=String(o(e)),u=r(n),s=c.length;return u<0||u>=s?t?"":void 0:(a=c.charCodeAt(u),a<55296||a>56319||u+1===s||(i=c.charCodeAt(u+1))<56320||i>57343?t?c.charAt(u):a:t?c.slice(u,u+2):i-56320+(a-55296<<10)+65536)}}},"0a49":function(t,e,n){var r=n("9b43"),o=n("626a"),a=n("4bf8"),i=n("9def"),c=n("cd1c");t.exports=function(t,e){var n=1==t,u=2==t,s=3==t,f=4==t,l=6==t,d=5==t||l,h=e||c;return function(e,c,p){for(var m,g,y=a(e),v=o(y),b=r(c,p,3),w=i(v.length),_=0,S=n?h(e,w):u?h(e,0):void 0;w>_;_++)if((d||_ in v)&&(m=v[_],g=b(m,_,y),t))if(n)S[_]=g;else if(g)switch(t){case 3:return!0;case 5:return m;case 6:return _;case 2:S.push(m)}else if(f)return!1;return l?-1:s||f?f:S}}},"0bfb":function(t,e,n){"use strict";var r=n("cb7c");t.exports=function(){var t=r(this),e="";return t.global&&(e+="g"),t.ignoreCase&&(e+="i"),t.multiline&&(e+="m"),t.unicode&&(e+="u"),t.sticky&&(e+="y"),e}},1169:function(t,e,n){var r=n("2d95");t.exports=Array.isArray||function(t){return"Array"==r(t)}},"11e9":function(t,e,n){var r=n("52a7"),o=n("4630"),a=n("6821"),i=n("6a99"),c=n("69a8"),u=n("c69a"),s=Object.getOwnPropertyDescriptor;e.f=n("9e1e")?s:function(t,e){if(t=a(t),e=i(e,!0),u)try{return s(t,e)}catch(n){}if(c(t,e))return o(!r.f.call(t,e),t[e])}},"1c4c":function(t,e,n){"use strict";var r=n("9b43"),o=n("5ca1"),a=n("4bf8"),i=n("1fa8"),c=n("33a4"),u=n("9def"),s=n("f1ae"),f=n("27ee");o(o.S+o.F*!n("5cc5")((function(t){Array.from(t)})),"Array",{from:function(t){var e,n,o,l,d=a(t),h="function"==typeof this?this:Array,p=arguments.length,m=p>1?arguments[1]:void 0,g=void 0!==m,y=0,v=f(d);if(g&&(m=r(m,p>2?arguments[2]:void 0,2)),void 0==v||h==Array&&c(v))for(e=u(d.length),n=new h(e);e>y;y++)s(n,y,g?m(d[y],y):d[y]);else for(l=v.call(d),n=new h;!(o=l.next()).done;y++)s(n,y,g?i(l,m,[o.value,y],!0):o.value);return n.length=y,n}})},"2b83":function(t,e,n){"use strict";n.r(e);var r=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"admin-login"},[n("div",{staticClass:"login-box"},[n("h2",[t._v("文档管理系统")]),n("el-tabs",{model:{value:t.active,callback:function(e){t.active=e},expression:"active"}},[n("el-tab-pane",{attrs:{label:"账号登录",name:"account"}},[n("div",{staticClass:"login-form"},[n("el-input",{staticClass:"user",attrs:{placeholder:"用户名/手机号"},model:{value:t.formData.username,callback:function(e){t.$set(t.formData,"username",e)},expression:"formData.username"}}),n("el-input",{staticClass:"pwd",attrs:{type:"password",placeholder:"输入密码"},model:{value:t.formData.userpass,callback:function(e){t.$set(t.formData,"userpass",e)},expression:"formData.userpass"}}),n("el-input",{staticClass:"code-input vrcode",attrs:{placeholder:"输入图形验证码"},nativeOn:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.login(e)}},model:{value:t.formData.code,callback:function(e){t.$set(t.formData,"code",e)},expression:"formData.code"}},[n("img",{attrs:{slot:"append",src:t.code,alt:""},on:{click:t.getCode},slot:"append"})])],1),t.thirdPartyList.length?n("div",{staticClass:"login-thirdParty"},[n("span",{staticClass:"title"},[t._v("第三方账号登录")]),n("div",{staticClass:"icon-list"},t._l(t.thirdPartyList,(function(e){return n("img",{key:e.name,staticClass:"icon-block",attrs:{src:e.logo,title:e.name},on:{click:function(n){return t.thirdPartyIconClick(e.redirect_url)}}})})),0)]):t._e(),n("el-button",{staticClass:"login-btn",on:{click:t.login}},[t._v("登录")])],1)],1)],1),t._m(0)])},o=[function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"footer"},[t._v("\n      Powered by "),n("a",{attrs:{href:"https://www.w7.cc"}},[t._v("微擎云计算©www.w7.cc")])])}],a=(n("ac6a"),n("ac4d"),n("8a81"),n("5df3"),n("1c4c"),n("7f7f"),n("6b54"),n("7514"),n("1c1e")),i=n("4ec3");function c(t,e){var n;if("undefined"===typeof Symbol||null==t[Symbol.iterator]){if(Array.isArray(t)||(n=u(t))||e&&t&&"number"===typeof t.length){n&&(t=n);var r=0,o=function(){};return{s:o,n:function(){return r>=t.length?{done:!0}:{done:!1,value:t[r++]}},e:function(t){throw t},f:o}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var a,i=!0,c=!1;return{s:function(){n=t[Symbol.iterator]()},n:function(){var t=n.next();return i=t.done,t},e:function(t){c=!0,a=t},f:function(){try{i||null==n.return||n.return()}finally{if(c)throw a}}}}function u(t,e){if(t){if("string"===typeof t)return s(t,e);var n=Object.prototype.toString.call(t).slice(8,-1);return"Object"===n&&t.constructor&&(n=t.constructor.name),"Map"===n||"Set"===n?Array.from(t):"Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?s(t,e):void 0}}function s(t,e){(null==e||e>t.length)&&(e=t.length);for(var n=0,r=new Array(e);n<e;n++)r[n]=t[n];return r}var f={name:"adminLoginPage",data:function(){return{autofocus:!1,active:"account",code:"",formData:{username:"",userpass:"",code:"",phone:"",phoneCode:""},thirdPartyList:[]}},beforeRouteEnter:function(t,e,n){var r=t.query.code,o=t.query.redirect_url,c=t.query.app_id;r?a["a"].post("/common/auth/third-party-login",{code:r,app_id:c}).then((function(t){t&&t.data.is_need_bind?n("/bind"):t&&t.data.has_login?n((function(e){1==t.data.has_login?e.$confirm(t.data.message,"",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then((function(){Object(i["b"])({change_token:t.data.change_token}).then((function(){e.$message({type:"success",message:"切换账户成功!"}),e.$router.push({name:"admin"})}))})).catch((function(){e.$message({type:"info",message:"已取消"}),e.$router.push({name:"admin"})})):2==t.data.has_login?e.$confirm(t.data.message,"",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then((function(){Object(i["a"])({bind_token:t.data.bind_token}).then((function(){e.$message({type:"success",message:"绑定账户成功!"}),e.$router.push({name:"admin"})}))})).catch((function(){e.$message({type:"info",message:"已取消"}),e.$router.push({name:"admin"})})):3==t.data.has_login&&e.$confirm(t.data.message,"",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then((function(){Object(i["A"])({source_token:t.data.source_token}).then((function(){e.$router.push({name:"adminBind"})}))})).catch((function(){e.$message({type:"info",message:"已取消"}),e.$router.push({name:"admin"})}))})):t&&"success"==t.data?n("/admin/document"):o?window.open(o,"_self"):n("/admin/document")})).catch((function(){})):a["a"].post("/common/auth/default-login-url").then((function(t){t.data?window.open(t.data,"_self"):n()})).catch((function(t){}))},created:function(){this.getCode(),this.getThirdParty()},methods:{systemDetection:function(){var t=this;Object(i["y"])().then((function(e){if(200==e.code){var n,r=c(e.data);try{for(r.s();!(n=r.n()).done;){var o=n.value;1!=o.id||o.enable||t.$router.push({name:"install"})}}catch(a){r.e(a)}finally{r.f()}}})).catch((function(t){}))},showFind:function(){this.$message({message:"请联系管理员修改或使用密码找回工具修改"})},getCode:function(){var t=this;this.$post("/common/verifycode/image").then((function(e){200==e.code?t.code=e.data.img:t.$message.error(e.message)}))},login:function(){var t=this;this.$post("/common/auth/login",this.formData).then((function(){var e=t.$message("登录成功");setTimeout((function(){e.close();var n=localStorage.recordHref;n?location.href=n:t.$router.push({name:"personalCenter"})}),500)})).catch((function(){t.formData.code="",document.getElementsByClassName("el-input__inner")[2].focus(),t.getCode()}))},getThirdParty:function(){var t=this;this.$post("/common/auth/method",{redirect_url:localStorage.recordHref||this.$route.query.redirect_url}).then((function(e){if(t.thirdPartyList=e.data||[],200==e.code&&e.data.length){var n=e.data.find((function(t){return 3==t.id}));document.cookie;if(n){var r=location.origin+"/admin-login";Object(i["B"])({appDomain:"api.w7.cc",redirect_type:"ajax",redirect_method:"POST",redirect_url:r}).then((function(){e&&e.data&&e.data.is_online&&window.open(n.redirect_url,"_target")}))}}}))},thirdPartyIconClick:function(t){window.open(t,"_self")}}},l=f,d=(n("8a57"),n("2877")),h=Object(d["a"])(l,r,o,!1,null,"53f0e998",null);e["default"]=h.exports},"37c8":function(t,e,n){e.f=n("2b4c")},3846:function(t,e,n){n("9e1e")&&"g"!=/./g.flags&&n("86cc").f(RegExp.prototype,"flags",{configurable:!0,get:n("0bfb")})},"3a72":function(t,e,n){var r=n("7726"),o=n("8378"),a=n("2d00"),i=n("37c8"),c=n("86cc").f;t.exports=function(t){var e=o.Symbol||(o.Symbol=a?{}:r.Symbol||{});"_"==t.charAt(0)||t in e||c(e,t,{value:i.f(t)})}},"5df3":function(t,e,n){"use strict";var r=n("02f4")(!0);n("01f9")(String,"String",(function(t){this._t=String(t),this._i=0}),(function(){var t,e=this._t,n=this._i;return n>=e.length?{value:void 0,done:!0}:(t=r(e,n),this._i+=t.length,{value:t,done:!1})}))},"67ab":function(t,e,n){var r=n("ca5a")("meta"),o=n("d3f4"),a=n("69a8"),i=n("86cc").f,c=0,u=Object.isExtensible||function(){return!0},s=!n("79e5")((function(){return u(Object.preventExtensions({}))})),f=function(t){i(t,r,{value:{i:"O"+ ++c,w:{}}})},l=function(t,e){if(!o(t))return"symbol"==typeof t?t:("string"==typeof t?"S":"P")+t;if(!a(t,r)){if(!u(t))return"F";if(!e)return"E";f(t)}return t[r].i},d=function(t,e){if(!a(t,r)){if(!u(t))return!0;if(!e)return!1;f(t)}return t[r].w},h=function(t){return s&&p.NEED&&u(t)&&!a(t,r)&&f(t),t},p=t.exports={KEY:r,NEED:!1,fastKey:l,getWeak:d,onFreeze:h}},"6b54":function(t,e,n){"use strict";n("3846");var r=n("cb7c"),o=n("0bfb"),a=n("9e1e"),i="toString",c=/./[i],u=function(t){n("2aba")(RegExp.prototype,i,t,!0)};n("79e5")((function(){return"/a/b"!=c.call({source:"a",flags:"b"})}))?u((function(){var t=r(this);return"/".concat(t.source,"/","flags"in t?t.flags:!a&&t instanceof RegExp?o.call(t):void 0)})):c.name!=i&&u((function(){return c.call(this)}))},7514:function(t,e,n){"use strict";var r=n("5ca1"),o=n("0a49")(5),a="find",i=!0;a in[]&&Array(1)[a]((function(){i=!1})),r(r.P+r.F*i,"Array",{find:function(t){return o(this,t,arguments.length>1?arguments[1]:void 0)}}),n("9c6c")(a)},"75b8":function(t,e,n){},"7bbc":function(t,e,n){var r=n("6821"),o=n("9093").f,a={}.toString,i="object"==typeof window&&window&&Object.getOwnPropertyNames?Object.getOwnPropertyNames(window):[],c=function(t){try{return o(t)}catch(e){return i.slice()}};t.exports.f=function(t){return i&&"[object Window]"==a.call(t)?c(t):o(r(t))}},"8a57":function(t,e,n){"use strict";n("75b8")},"8a81":function(t,e,n){"use strict";var r=n("7726"),o=n("69a8"),a=n("9e1e"),i=n("5ca1"),c=n("2aba"),u=n("67ab").KEY,s=n("79e5"),f=n("5537"),l=n("7f20"),d=n("ca5a"),h=n("2b4c"),p=n("37c8"),m=n("3a72"),g=n("d4c0"),y=n("1169"),v=n("cb7c"),b=n("d3f4"),w=n("4bf8"),_=n("6821"),S=n("6a99"),O=n("4630"),x=n("2aeb"),k=n("7bbc"),$=n("11e9"),C=n("2621"),P=n("86cc"),j=n("0d58"),A=$.f,E=P.f,D=k.f,T=r.Symbol,F=r.JSON,N=F&&F.stringify,B="prototype",I=h("_hidden"),J=h("toPrimitive"),L={}.propertyIsEnumerable,q=f("symbol-registry"),R=f("symbols"),K=f("op-symbols"),M=Object[B],W="function"==typeof T&&!!C.f,H=r.QObject,Y=!H||!H[B]||!H[B].findChild,z=a&&s((function(){return 7!=x(E({},"a",{get:function(){return E(this,"a",{value:7}).a}})).a}))?function(t,e,n){var r=A(M,e);r&&delete M[e],E(t,e,n),r&&t!==M&&E(M,e,r)}:E,G=function(t){var e=R[t]=x(T[B]);return e._k=t,e},Q=W&&"symbol"==typeof T.iterator?function(t){return"symbol"==typeof t}:function(t){return t instanceof T},U=function(t,e,n){return t===M&&U(K,e,n),v(t),e=S(e,!0),v(n),o(R,e)?(n.enumerable?(o(t,I)&&t[I][e]&&(t[I][e]=!1),n=x(n,{enumerable:O(0,!1)})):(o(t,I)||E(t,I,O(1,{})),t[I][e]=!0),z(t,e,n)):E(t,e,n)},V=function(t,e){v(t);var n,r=g(e=_(e)),o=0,a=r.length;while(a>o)U(t,n=r[o++],e[n]);return t},X=function(t,e){return void 0===e?x(t):V(x(t),e)},Z=function(t){var e=L.call(this,t=S(t,!0));return!(this===M&&o(R,t)&&!o(K,t))&&(!(e||!o(this,t)||!o(R,t)||o(this,I)&&this[I][t])||e)},tt=function(t,e){if(t=_(t),e=S(e,!0),t!==M||!o(R,e)||o(K,e)){var n=A(t,e);return!n||!o(R,e)||o(t,I)&&t[I][e]||(n.enumerable=!0),n}},et=function(t){var e,n=D(_(t)),r=[],a=0;while(n.length>a)o(R,e=n[a++])||e==I||e==u||r.push(e);return r},nt=function(t){var e,n=t===M,r=D(n?K:_(t)),a=[],i=0;while(r.length>i)!o(R,e=r[i++])||n&&!o(M,e)||a.push(R[e]);return a};W||(T=function(){if(this instanceof T)throw TypeError("Symbol is not a constructor!");var t=d(arguments.length>0?arguments[0]:void 0),e=function(n){this===M&&e.call(K,n),o(this,I)&&o(this[I],t)&&(this[I][t]=!1),z(this,t,O(1,n))};return a&&Y&&z(M,t,{configurable:!0,set:e}),G(t)},c(T[B],"toString",(function(){return this._k})),$.f=tt,P.f=U,n("9093").f=k.f=et,n("52a7").f=Z,C.f=nt,a&&!n("2d00")&&c(M,"propertyIsEnumerable",Z,!0),p.f=function(t){return G(h(t))}),i(i.G+i.W+i.F*!W,{Symbol:T});for(var rt="hasInstance,isConcatSpreadable,iterator,match,replace,search,species,split,toPrimitive,toStringTag,unscopables".split(","),ot=0;rt.length>ot;)h(rt[ot++]);for(var at=j(h.store),it=0;at.length>it;)m(at[it++]);i(i.S+i.F*!W,"Symbol",{for:function(t){return o(q,t+="")?q[t]:q[t]=T(t)},keyFor:function(t){if(!Q(t))throw TypeError(t+" is not a symbol!");for(var e in q)if(q[e]===t)return e},useSetter:function(){Y=!0},useSimple:function(){Y=!1}}),i(i.S+i.F*!W,"Object",{create:X,defineProperty:U,defineProperties:V,getOwnPropertyDescriptor:tt,getOwnPropertyNames:et,getOwnPropertySymbols:nt});var ct=s((function(){C.f(1)}));i(i.S+i.F*ct,"Object",{getOwnPropertySymbols:function(t){return C.f(w(t))}}),F&&i(i.S+i.F*(!W||s((function(){var t=T();return"[null]"!=N([t])||"{}"!=N({a:t})||"{}"!=N(Object(t))}))),"JSON",{stringify:function(t){var e,n,r=[t],o=1;while(arguments.length>o)r.push(arguments[o++]);if(n=e=r[1],(b(e)||void 0!==t)&&!Q(t))return y(e)||(e=function(t,e){if("function"==typeof n&&(e=n.call(this,t,e)),!Q(e))return e}),r[1]=e,N.apply(F,r)}}),T[B][J]||n("32e9")(T[B],J,T[B].valueOf),l(T,"Symbol"),l(Math,"Math",!0),l(r.JSON,"JSON",!0)},9093:function(t,e,n){var r=n("ce10"),o=n("e11e").concat("length","prototype");e.f=Object.getOwnPropertyNames||function(t){return r(t,o)}},ac4d:function(t,e,n){n("3a72")("asyncIterator")},cd1c:function(t,e,n){var r=n("e853");t.exports=function(t,e){return new(r(t))(e)}},d4c0:function(t,e,n){var r=n("0d58"),o=n("2621"),a=n("52a7");t.exports=function(t){var e=r(t),n=o.f;if(n){var i,c=n(t),u=a.f,s=0;while(c.length>s)u.call(t,i=c[s++])&&e.push(i)}return e}},e853:function(t,e,n){var r=n("d3f4"),o=n("1169"),a=n("2b4c")("species");t.exports=function(t){var e;return o(t)&&(e=t.constructor,"function"!=typeof e||e!==Array&&!o(e.prototype)||(e=void 0),r(e)&&(e=e[a],null===e&&(e=void 0))),void 0===e?Array:e}},f1ae:function(t,e,n){"use strict";var r=n("86cc"),o=n("4630");t.exports=function(t,e,n){e in t?r.f(t,e,o(0,n)):t[e]=n}}}]);