(function(n){function e(e){for(var c,a,o=e[0],i=e[1],h=e[2],d=0,f=[];d<o.length;d++)a=o[d],Object.prototype.hasOwnProperty.call(u,a)&&u[a]&&f.push(u[a][0]),u[a]=0;for(c in i)Object.prototype.hasOwnProperty.call(i,c)&&(n[c]=i[c]);l&&l(e);while(f.length)f.shift()();return r.push.apply(r,h||[]),t()}function t(){for(var n,e=0;e<r.length;e++){for(var t=r[e],c=!0,a=1;a<t.length;a++){var o=t[a];0!==u[o]&&(c=!1)}c&&(r.splice(e--,1),n=i(i.s=t[0]))}return n}var c={},a={app:0},u={app:0},r=[];function o(n){return i.p+"js/"+({}[n]||n)+"."+{"chunk-0b180119":"084605dc","chunk-205901c6":"0cfae0c9","chunk-22e40c8e":"f00b8de8","chunk-2d22276a":"edae196b","chunk-3bee7542":"914622eb","chunk-3c222158":"6c81d5cb","chunk-40b8205a":"935a7bdb","chunk-49739a90":"50fa42ee","chunk-4cc002f6":"42489687","chunk-51252774":"d23787b1","chunk-564fade6":"0547c077","chunk-57c6af34":"3a901485","chunk-5e5417a8":"5ce1a65a","chunk-62b58b0f":"2b190554","chunk-6481ec50":"1139d3f3","chunk-68c685f1":"98354f7b","chunk-6b79a672":"3576566b","chunk-71aaa888":"7364b04d","chunk-7fe81284":"b238030c","chunk-1667b697":"2c35fbc8","chunk-25ea96f8":"1b092056","chunk-38d63479":"64711111","chunk-25d3a5b3":"b613e5fe","chunk-8026c636":"f07df868","chunk-93700cae":"a54cb8b2","chunk-a8da51e4":"3a8e093d","chunk-ad55078c":"f265c7f2","chunk-d779deb8":"558b1325","chunk-f4c7013e":"cc239236"}[n]+".js"}function i(e){if(c[e])return c[e].exports;var t=c[e]={i:e,l:!1,exports:{}};return n[e].call(t.exports,t,t.exports,i),t.l=!0,t.exports}i.e=function(n){var e=[],t={"chunk-0b180119":1,"chunk-205901c6":1,"chunk-22e40c8e":1,"chunk-3bee7542":1,"chunk-49739a90":1,"chunk-4cc002f6":1,"chunk-51252774":1,"chunk-564fade6":1,"chunk-57c6af34":1,"chunk-5e5417a8":1,"chunk-62b58b0f":1,"chunk-6481ec50":1,"chunk-68c685f1":1,"chunk-71aaa888":1,"chunk-1667b697":1,"chunk-25ea96f8":1,"chunk-38d63479":1,"chunk-25d3a5b3":1,"chunk-8026c636":1,"chunk-93700cae":1,"chunk-a8da51e4":1,"chunk-d779deb8":1,"chunk-f4c7013e":1};a[n]?e.push(a[n]):0!==a[n]&&t[n]&&e.push(a[n]=new Promise((function(e,t){for(var c="css/"+({}[n]||n)+"."+{"chunk-0b180119":"3c05d3bd","chunk-205901c6":"13a303ef","chunk-22e40c8e":"2d7343f0","chunk-2d22276a":"31d6cfe0","chunk-3bee7542":"8f6be2eb","chunk-3c222158":"31d6cfe0","chunk-40b8205a":"31d6cfe0","chunk-49739a90":"ef80add1","chunk-4cc002f6":"76147de1","chunk-51252774":"3a537419","chunk-564fade6":"0e433876","chunk-57c6af34":"6dff8b53","chunk-5e5417a8":"0866d042","chunk-62b58b0f":"9b50fdb8","chunk-6481ec50":"3bd8ad76","chunk-68c685f1":"3c05d3bd","chunk-6b79a672":"31d6cfe0","chunk-71aaa888":"b923ac67","chunk-7fe81284":"31d6cfe0","chunk-1667b697":"10f209e9","chunk-25ea96f8":"80811403","chunk-38d63479":"0862118e","chunk-25d3a5b3":"4eeefd8a","chunk-8026c636":"2ba6787a","chunk-93700cae":"9f35113f","chunk-a8da51e4":"3334074a","chunk-ad55078c":"31d6cfe0","chunk-d779deb8":"207190f3","chunk-f4c7013e":"39a1833e"}[n]+".css",u=i.p+c,r=document.getElementsByTagName("link"),o=0;o<r.length;o++){var h=r[o],d=h.getAttribute("data-href")||h.getAttribute("href");if("stylesheet"===h.rel&&(d===c||d===u))return e()}var f=document.getElementsByTagName("style");for(o=0;o<f.length;o++){h=f[o],d=h.getAttribute("data-href");if(d===c||d===u)return e()}var l=document.createElement("link");l.rel="stylesheet",l.type="text/css",l.onload=e,l.onerror=function(e){var c=e&&e.target&&e.target.src||u,r=new Error("Loading CSS chunk "+n+" failed.\n("+c+")");r.code="CSS_CHUNK_LOAD_FAILED",r.request=c,delete a[n],l.parentNode.removeChild(l),t(r)},l.href=u;var s=document.getElementsByTagName("head")[0];s.appendChild(l)})).then((function(){a[n]=0})));var c=u[n];if(0!==c)if(c)e.push(c[2]);else{var r=new Promise((function(e,t){c=u[n]=[e,t]}));e.push(c[2]=r);var h,d=document.createElement("script");d.charset="utf-8",d.timeout=120,i.nc&&d.setAttribute("nonce",i.nc),d.src=o(n);var f=new Error;h=function(e){d.onerror=d.onload=null,clearTimeout(l);var t=u[n];if(0!==t){if(t){var c=e&&("load"===e.type?"missing":e.type),a=e&&e.target&&e.target.src;f.message="Loading chunk "+n+" failed.\n("+c+": "+a+")",f.name="ChunkLoadError",f.type=c,f.request=a,t[1](f)}u[n]=void 0}};var l=setTimeout((function(){h({type:"timeout",target:d})}),12e4);d.onerror=d.onload=h,document.head.appendChild(d)}return Promise.all(e)},i.m=n,i.c=c,i.d=function(n,e,t){i.o(n,e)||Object.defineProperty(n,e,{enumerable:!0,get:t})},i.r=function(n){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(n,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(n,"__esModule",{value:!0})},i.t=function(n,e){if(1&e&&(n=i(n)),8&e)return n;if(4&e&&"object"===typeof n&&n&&n.__esModule)return n;var t=Object.create(null);if(i.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:n}),2&e&&"string"!=typeof n)for(var c in n)i.d(t,c,function(e){return n[e]}.bind(null,c));return t},i.n=function(n){var e=n&&n.__esModule?function(){return n["default"]}:function(){return n};return i.d(e,"a",e),e},i.o=function(n,e){return Object.prototype.hasOwnProperty.call(n,e)},i.p="/",i.oe=function(n){throw n};var h=window["webpackJsonp"]=window["webpackJsonp"]||[],d=h.push.bind(h);h.push=e,h=h.slice();for(var f=0;f<h.length;f++)e(h[f]);var l=d;r.push([0,"chunk-vendors"]),t()})({0:function(n,e,t){n.exports=t("56d7")},"1c1e":function(n,e,t){"use strict";var c=t("53ca"),a=t("bc3a"),u=t.n(a),r=t("41cb"),o=t("5c96"),i=t("4328"),h=t.n(i),d=u.a.create({withCredentials:!0});d.interceptors.request.use((function(n){return n.transformRequest=[function(n){return"object"!==Object(c["a"])(n)||n instanceof FormData?n:h.a.stringify(n)}],n})),d.interceptors.response.use((function(n){return n.data.code>=200&&n.data.code<300?n.data:(444==n.data.code?(r["a"].push("/admin-login?redirect_url="+window.location.href),o["Message"].closeAll(),o["Message"].error(n.data.message)):445==n.data.code?(o["Message"].error(n.data.message),r["a"].push("/admin/document")):446==n.data.code?(o["Message"].error(n.data.message),localStorage.recordHref="",r["a"].push("/admin/document")):(o["Message"].closeAll(),o["Message"].error(n.data.message)),Promise.reject(n.data))}),(function(n){return Promise.reject(n.response)})),e["a"]=d},"41cb":function(n,e,t){"use strict";var c=t("2b0e"),a=t("8c4f");c["default"].use(a["a"]);var u=a["a"].prototype.push;a["a"].prototype.push=function(n){return u.call(this,n).catch((function(n){return n}))};var r=new a["a"]({mode:"history",routes:[{path:"/",redirect:"/admin/document"},{path:"/login",name:"adminLogin",component:function(){return t.e("chunk-40b8205a").then(t.bind(null,"35b0"))}},{path:"/admin-login",name:"adminLoginPage",component:function(){return t.e("chunk-6b79a672").then(t.bind(null,"2b83"))}},{path:"/bind",name:"adminBind",component:function(){return t.e("chunk-93700cae").then(t.bind(null,"b863"))}},{path:"/install",name:"install",redirect:"/install/installOne",component:function(){return t.e("chunk-6481ec50").then(t.bind(null,"822d"))},children:[{path:"installOne",name:"installOne",component:function(){return t.e("chunk-205901c6").then(t.bind(null,"385c"))}},{path:"installTwo",name:"installTwo",component:function(){return t.e("chunk-8026c636").then(t.bind(null,"c643"))}},{path:"installTree",name:"installTree",component:function(){return t.e("chunk-4cc002f6").then(t.bind(null,"25d6"))}}]},{path:"/mock/:document_id/:chapter_id",name:"mock",component:function(){return t.e("chunk-3c222158").then(t.bind(null,"6c8c"))}},{path:"/admin",name:"admin",redirect:"/admin/document",component:function(){return t.e("chunk-62b58b0f").then(t.bind(null,"ed3a"))},children:[{path:"document",name:"documentLayout",component:function(){return t.e("chunk-49739a90").then(t.bind(null,"794d"))},children:[{path:"",name:"documentIndex",component:function(){return Promise.all([t.e("chunk-7fe81284"),t.e("chunk-1667b697"),t.e("chunk-38d63479")]).then(t.bind(null,"b56e"))}},{path:"chapter/:id",name:"chapter",meta:{footerClass:"float"},component:function(){return Promise.all([t.e("chunk-7fe81284"),t.e("chunk-1667b697"),t.e("chunk-25ea96f8")]).then(t.bind(null,"1962"))}},{path:"recycle",name:"documentRecycle",component:function(){return t.e("chunk-564fade6").then(t.bind(null,"9095"))}},{path:"star",name:"documentStar",component:function(){return t.e("chunk-2d22276a").then(t.bind(null,"cf5f"))}},{path:"history",name:"documentHistory",component:function(){return t.e("chunk-f4c7013e").then(t.bind(null,"a3b9"))}},{path:"involved",name:"documentInvolved",component:function(){return t.e("chunk-ad55078c").then(t.bind(null,"9be8"))}}]},{path:"user",name:"userIndex",component:function(){return t.e("chunk-57c6af34").then(t.bind(null,"e378"))}},{path:"user/create",name:"baseInfo",component:function(){return t.e("chunk-a8da51e4").then(t.bind(null,"99a4"))}},{path:"user/:id",name:"userInfo",component:function(){return t.e("chunk-a8da51e4").then(t.bind(null,"99a4"))}},{path:"user/:id",name:"baseInfoId",component:function(){return t.e("chunk-a8da51e4").then(t.bind(null,"99a4"))}},{path:"setting",component:function(){return t.e("chunk-d779deb8").then(t.bind(null,"2ccb"))},children:[{path:"",name:"settingIndex",component:function(){return t.e("chunk-71aaa888").then(t.bind(null,"0dd8"))}},{path:"third-party",name:"settingThirdParty",component:function(){return t.e("chunk-0b180119").then(t.bind(null,"091d"))}},{path:"third-party-custom",name:"settingThirdPartyCustom",component:function(){return t.e("chunk-68c685f1").then(t.bind(null,"08d8"))}},{path:"login",name:"settingLogin",component:function(){return t.e("chunk-22e40c8e").then(t.bind(null,"b49c"))}},{path:"nav",name:"settingNav",component:function(){return t.e("chunk-3bee7542").then(t.bind(null,"3c4d"))}}]},{path:"account-info",name:"accountInfo",component:function(){return t.e("chunk-5e5417a8").then(t.bind(null,"3310"))}},{path:"search",name:"searchResults",component:function(){return t.e("chunk-51252774").then(t.bind(null,"6d70"))}}]},{path:"/chapter/:id",name:"home",redirect:"home",component:function(){return t.e("chunk-62b58b0f").then(t.bind(null,"ed3a"))},children:[{path:"",name:"homeChild",component:function(){return Promise.all([t.e("chunk-7fe81284"),t.e("chunk-25d3a5b3")]).then(t.bind(null,"7abe"))}}]}],scrollBehavior:function(n){return n.hash?{selector:n.hash}:{x:0,y:0}}});e["a"]=r},"56d7":function(n,e,t){"use strict";t.r(e);t("7f7f"),t("cadf"),t("551c"),t("f751"),t("097d");var c=t("2b0e"),a=function(){var n=this,e=n.$createElement,t=n._self._c||e;return t("div",{attrs:{id:"app"}},[t("router-view")],1)},u=[],r=(t("5c0b"),t("2877")),o={},i=Object(r["a"])(o,a,u,!1,null,null,null),h=i.exports,d=t("41cb"),f=t("2f62"),l=t("bc3a"),s=t.n(l);c["default"].use(f["a"]);var p=new f["a"].Store({state:{UserInfo:{},NavMenu:{},isSave:!0,saveDialogVisible:!1},getters:{UserInfo:function(n){return n.UserInfo},NavMenu:function(n){return n.NavMenu}},mutations:{setUserInfo:function(n,e){n.UserInfo=e},setNavMenu:function(n,e){n.NavMenu=e}},actions:{getUserInfo:function(n){s.a.post("/common/auth/user").then((function(e){"444"==e.data.code?n.commit("setUserInfo",{has_privilege:"",username:""}):n.commit("setUserInfo",e.data.data)}))},getNavMenu:function(n){s.a.post("/menu/setting").then((function(e){"444"==e.data.code?n.commit("setNavMenu",{theme:"",list:[]}):n.commit("setNavMenu",e.data.data)}))}}}),m=t("1c1e"),b=t("5c96"),k=t.n(b),g=t("b2d8"),v=t.n(g),y=t("4eb5"),w=t.n(y),P=(t("0fae"),t("64e1"),t("e9ff"),t("96eb"));c["default"].use(k.a),c["default"].use(v.a),c["default"].use(w.a),c["default"].prototype.$http=m["a"],c["default"].prototype.$post=m["a"].post,c["default"].prototype.$mock=P,c["default"].config.productionTip=!1;var I=new c["default"]({router:d["a"],store:p,render:function(n){return n(h)}}).$mount("#app");e["default"]=I;d["a"].beforeEach((function(n,e,t){if("adminLoginPage"==n.name){var c=location.href,a=c.indexOf("install");a<0&&(localStorage.recordHref=c)}t()}))},"5c0b":function(n,e,t){"use strict";var c=t("e332"),a=t.n(c);a.a},e332:function(n,e,t){},e9ff:function(n,e,t){}});