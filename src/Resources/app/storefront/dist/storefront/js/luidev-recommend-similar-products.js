"use strict";(self.webpackChunk=self.webpackChunk||[]).push([["luidev-recommend-similar-products"],{437:(t,e,i)=>{var r=i(6285);class s extends r.Z{init(){let t=this;const e=this.el.querySelectorAll(".product-slider-item");e&&0!==e.length&&e.forEach((function(e){e.addEventListener("click",(()=>{t._saveClickedProduct(e)}))}))}_saveClickedProduct(t){const e=t.querySelector("input[name='product-id']");if(!e)return;const i=e.value;if(!i)return;let r=[];const s=localStorage.getItem("luidevClickedProducts");s&&(r=[...r,...JSON.parse(s)]),r.push(i),r=r.filter(((t,e,i)=>i.indexOf(t)===e)),localStorage.setItem("luidevClickedProducts",JSON.stringify(r))}}var n,o,c,l=i(8254);class d extends r.Z{init(){this.client=new l.Z,this.options.url&&this.options.productId&&this._isClickedProduct()}_isClickedProduct(){const t=localStorage.getItem("luidevClickedProducts");if(!t)return;let e=JSON.parse(t);if(0===e.length)return;if(this.options.parentId){const t=e.indexOf(this.options.parentId);-1!==t&&this._sendClickStatistics().then((()=>{e.splice(t,1),localStorage.setItem("luidevClickedProducts",JSON.stringify(e))}))}const i=e.indexOf(this.options.productId);-1!==i&&this._sendClickStatistics().then((()=>{e.splice(i,1),localStorage.setItem("luidevClickedProducts",JSON.stringify(e))}))}async _sendClickStatistics(){const t={productId:this.options.productId};this.client.post(this.options.url,JSON.stringify(t))}}n=d,c={url:null,productId:null,parentId:null},(o=function(t){var e=function(t,e){if("object"!=typeof t||null===t)return t;var i=t[Symbol.toPrimitive];if(void 0!==i){var r=i.call(t,e||"default");if("object"!=typeof r)return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===e?String:Number)(t)}(t,"string");return"symbol"==typeof e?e:String(e)}(o="options"))in n?Object.defineProperty(n,o,{value:c,enumerable:!0,configurable:!0,writable:!0}):n[o]=c;const u=window.PluginManager;u.register("LuidevSimilarProductsClicks",s,"[data-luidev-similar-products-clicks]"),u.register("LuidevProductStatistics",d,"[data-luidev-product-statistics]")}},t=>{t.O(0,["vendor-node","vendor-shared"],(()=>{return e=437,t(t.s=e);var e}));t.O()}]);