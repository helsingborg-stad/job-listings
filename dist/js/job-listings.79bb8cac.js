!function(e){var t={};function n(r){if(t[r])return t[r].exports;var i=t[r]={i:r,l:!1,exports:{}};return e[r].call(i.exports,i,i.exports,n),i.l=!0,i.exports}n.m=e,n.c=t,n.d=function(e,t,r){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var i in e)n.d(r,i,function(t){return e[t]}.bind(null,i));return r},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=1)}([function(e,t,n){var r,i,o=n(2);"undefined"!=typeof window&&(r=window&&window.history&&window.history.replaceState,i=window&&window.history&&window.history.pushState),e.exports=r?function(e,t){t||(t={});var n,a=!e,s=!a&&"string"==typeof e,c=t.clear||s,l=t.pushState?i:r,u=c?{}:o.parse(window.location.search);if(a||s)n=e||"";else{for(var d in e){var f=e[d];f||0===f?u[d]=e[d]:delete u[d]}n=o.stringify(u)}n.length&&"?"!==n.charAt(0)&&(n="?"+n),l.call(window.history,t.state||window.history.state,"",window.location.pathname+(n||""))}:function(){}},function(e,t,n){"use strict";n.r(t);var r=n(0),i=n.n(r);function o(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}new function e(){var t=this;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),o(this,"handleEvents",(function(){for(var e=jobListings.jobId,n=document.getElementsByClassName("job-listings-application"),r=document.getElementById("job-listings-login"),o=0;o<n.length;o++){n.item(o).addEventListener("click",(function(n){i()({rmpage:"apply",rmjob:e},{pushState:!0,clear:!0}),t.renderApplicationIframe()}))}r.addEventListener("click",(function(e){i()({rmpage:"auth"},{pushState:!0,clear:!0}),t.renderApplicationIframe()}))})),o(this,"renderApplicationIframe",(function(){var e,t,n,r="https://web103.reachmee.com/ext/",i="https://helsingborg.se/arbete/lediga-jobb-i-helsingborgs-stad/",o=encodeURIComponent(document.referrer),a="",s="";function c(e){if("rmurl"==e.toLowerCase())var t=decodeURIComponent(window.location.search.substring(1));else t=window.location.search.substring(1);for(var n=t.split("&"),r=0;r<n.length;r++){var i=n[r].split("=");if(i[0]==e)return i.length>2?i[2]:i[1]}}r+="I017/1118/",a=c("rmpage"),e=c("rmjob"),s=c("rmlang"),t=c("rmproj"),n=c("rmihelper"),void 0===a||""==a?a="main?":a+="?","job?"!=a&&"apply?"!=a&&"application?"!=a||(void 0===e||""==e?a="main?":a+="job_id="+e+"&"),"assessment?"==a&&(void 0===t||""==t?a="main?":a+="commseqno="+t+"&"),"booking?"==a&&(void 0===t||""==t||"undefined"==e||""==e?a="main?":a+="commseqno="+t+"&job_id="+e+"&");var l="";void 0!==(l=c("RMURL"))&&l.length>0&&(a="job?job_id="+l+"&"),void 0!==(l=c("rmurl"))&&l.length>0&&(a="job?job_id="+l+"&");var u=c("rmtoken");if("subscription?"!=a&&"subscriptions?"!=a||(void 0===u||""==u?a="main?":a+="skey="+u+"&"),"profileactivate?"==a){var d=c("skey");void 0===d||""==d?a="main?":a+="skey="+d+"&"}r+=a,r+="site=9&validator=9ec24d855e37a090a5e232077f53c593",void 0!==s&&""!=s||(s="SE");var f=c("cantoken");f&&(r+="&cantoken="+f),r+="&lang="+s;var p=("; "+document.cookie).split("; rm_reftracker_1118=");if(2==p.length){var m=p.pop().split(";").shift();""!==m&&(o=m.replace(/^"(.*)"$/,"$1"))}if(null!=o&&null!=o&&""!=o||(o=c("ref")),null!=o&&0!=o.length||(o=""),o.length>0&&(o=(o=o.toLowerCase()).indexOf("t.co")>-1?"Twitter":o),r+="&ref="+o,r+="&ihelper="+i,!n){var h=document.createElement("iframe");h.setAttribute("allowTransparency","true"),h.setAttribute("title","title"),h.setAttribute("id","riframe"),h.setAttribute("name","riframe"),h.setAttribute("width","100%"),h.setAttribute("height","4000"),h.setAttribute("frameborder","0"),h.setAttribute("src",r);var v=document.createElement("div");v.appendChild(h);var b=document.getElementById("job-listings-modal-body");b.innerHTML="",b.appendChild(v);var g=window.addEventListener?"addEventListener":"attachEvent";(0,window[g])("attachEvent"==g?"onmessage":"message",(function(e){if(e.data.indexOf&&"[object Function]"==={}.toString.call(e.data.indexOf)&&-1!=e.data.indexOf("resize::")){var t=e.data.replace("resize::","");document.getElementById("riframe").style.height=parseInt(t)+"px"}}),!1)}})),this.handleEvents(),this.renderApplicationIframe()}},function(e,t,n){"use strict";var r=n(3);t.extract=function(e){return e.split("?")[1]||""},t.parse=function(e){return"string"!=typeof e?{}:(e=e.trim().replace(/^(\?|#|&)/,""))?e.split("&").reduce((function(e,t){var n=t.replace(/\+/g," ").split("="),r=n.shift(),i=n.length>0?n.join("="):void 0;return r=decodeURIComponent(r),i=void 0===i?null:decodeURIComponent(i),e.hasOwnProperty(r)?Array.isArray(e[r])?e[r].push(i):e[r]=[e[r],i]:e[r]=i,e}),{}):{}},t.stringify=function(e){return e?Object.keys(e).sort().map((function(t){var n=e[t];return Array.isArray(n)?n.sort().map((function(e){return r(t)+"="+r(e)})).join("&"):r(t)+"="+r(n)})).filter((function(e){return e.length>0})).join("&"):""}},function(e,t,n){"use strict";e.exports=function(e){return encodeURIComponent(e).replace(/[!'()*]/g,(function(e){return"%"+e.charCodeAt(0).toString(16).toUpperCase()}))}}]);