!function(e){var t={};function n(i){if(t[i])return t[i].exports;var r=t[i]={i:i,l:!1,exports:{}};return e[i].call(r.exports,r,r.exports,n),r.l=!0,r.exports}n.m=e,n.c=t,n.d=function(e,t,i){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:i})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var i=Object.create(null);if(n.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)n.d(i,r,function(t){return e[t]}.bind(null,r));return i},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=20)}([function(e,t){!function(){e.exports=this.wp.element}()},function(e,t){!function(){e.exports=this.wp.data}()},function(e,t){!function(){e.exports=this.wp.components}()},function(e,t){!function(){e.exports=this.wp.compose}()},function(e,t){!function(){e.exports=this.wp.i18n}()},function(e,t){e.exports=function(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}},function(e,t,n){var i=n(15),r=n(16),s=n(17),o=n(19);e.exports=function(e,t){return i(e)||r(e,t)||s(e,t)||o()}},function(e,t){!function(){e.exports=this.regeneratorRuntime}()},function(e,t){!function(){e.exports=this.wp.blockEditor}()},function(e,t){!function(){e.exports=this.wp.plugins}()},function(e,t){!function(){e.exports=this.wp.dataControls}()},function(e,t){!function(){e.exports=this.wp.a11y}()},function(e,t){!function(){e.exports=this.wp.hooks}()},function(e,t){!function(){e.exports=this.lodash}()},function(e,t){!function(){e.exports=this.wp.coreData}()},function(e,t){e.exports=function(e){if(Array.isArray(e))return e}},function(e,t){e.exports=function(e,t){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(e)){var n=[],i=!0,r=!1,s=void 0;try{for(var o,c=e[Symbol.iterator]();!(i=(o=c.next()).done)&&(n.push(o.value),!t||n.length!==t);i=!0);}catch(e){r=!0,s=e}finally{try{i||null==c.return||c.return()}finally{if(r)throw s}}return n}}},function(e,t,n){var i=n(18);e.exports=function(e,t){if(e){if("string"==typeof e)return i(e,t);var n=Object.prototype.toString.call(e).slice(8,-1);return"Object"===n&&e.constructor&&(n=e.constructor.name),"Map"===n||"Set"===n?Array.from(e):"Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?i(e,t):void 0}}},function(e,t){e.exports=function(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,i=new Array(t);n<t;n++)i[n]=e[n];return i}},function(e,t){e.exports=function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}},function(e,t,n){"use strict";n.r(t);var i={};n.r(i),n.d(i,"getSettings",(function(){return O})),n.d(i,"getCustom",(function(){return h})),n.d(i,"getFormInfo",(function(){return j})),n.d(i,"getSections",(function(){return y})),n.d(i,"getModifiedSettings",(function(){return _}));var r={};n.r(r),n.d(r,"updateSetting",(function(){return w})),n.d(r,"updateCustom",(function(){return x})),n.d(r,"resetFormSaveState",(function(){return C})),n.d(r,"saveSettings",(function(){return P}));var s=n(0),o=n(4),c=n(3),a=n(2),l=n(1),u=n(9),f=n(10),g=n(5),b=n.n(g);function d(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(e);t&&(i=i.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,i)}return n}function p(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?d(Object(n),!0).forEach((function(t){b()(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):d(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}
/**
 * Reducer for the Genesis Blocks data store.
 *
 * The reducer handles actions sent to the data store. Reducers must be pure so
 * they should have no side effects. Do not put API calls into the reducer.
 *
 * @since   1.0.0
 * @author  StudioPress
 * @license GPL-2.0-or-later
 */var m=p({form:{fail:!1,success:!1,is_saving:!1},custom:[],modifiedSettings:[]},genesisBlocksSettingsData),v=function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:m,t=arguments.length>1?arguments[1]:void 0;return"UPDATE_CUSTOM"===t.type?p(p({},e),{},{custom:p(p({},e.custom),{},b()({},t.setting.key,t.setting.value))}):"UPDATE_SETTING"===t.type?p(p({},e),{},{settings:p(p({},e.settings),{},b()({},t.setting.key,t.setting.value)),modifiedSettings:p(p({},e.modifiedSettings),{},b()({},t.setting.key,t.setting.value))}):"SAVING"===t.type?p(p({},e),{},{form:p(p({},e.form),{},{fail:!1,success:!1,is_saving:!0})}):"SAVED"===t.type?p(p({},e),{},{form:p(p({},e.form),{},{success:!0===t.success,fail:!0!==t.success,is_saving:!1}),modifiedSettings:t.success?[]:e.modifiedSettings}):"RESET"===t.type?p(p({},e),{},{form:p(p({},e.form),{},{fail:!1,success:!1,is_saving:!1})}):e},O=function(e){return e.settings||{}},h=function(e){return e.custom||{}},j=function(e){return e.form||{}};function y(e){return e.hasOwnProperty("sections")?e.sections:{}}var _=function(e){return e.modifiedSettings||[]},S=n(7),k=n.n(S),E=(n(14),k.a.mark(P));
/**
 * Actions let components change store state by sending a payload of data.
 *
 * This file exposes methods to send actions of a given type to the Genesis Blocks
 * data store. The reducer (reducer.js) then determines how to update store
 * state based on the type of action it receives.
 *
 * @since   1.0.0
 * @author  StudioPress
 * @license GPL-2.0-or-later
 */
function w(e){return{type:"UPDATE_SETTING",setting:e}}function x(e){return{type:"UPDATE_CUSTOM",setting:e}}function C(){return{type:"RESET"}}function P(e){var t;return k.a.wrap((function(n){for(;;)switch(n.prev=n.next){case 0:return n.next=2,{type:"SAVING"};case 2:t=null;try{wp.data.dispatch("core").saveSite(e),t=!0}catch(e){t=!1}return n.abrupt("return",{type:"SAVED",success:t});case 5:case"end":return n.stop()}}),E)}
/**
 * Registers the 'genesis-blocks/global-settings' WordPress data store.
 *
 * @see docs/modules/settings.md
 * @since   1.0.0
 * @author  StudioPress
 * @license GPL-2.0-or-later
 */var T={selectors:i,actions:r,reducer:v,controls:f.controls},D=(Object(l.registerStore)("genesis-blocks/global-settings",T),n(11)),U=n(12);var A=Object(c.compose)([Object(l.withSelect)((function(e){return{form:e("genesis-blocks/global-settings").getFormInfo(),settings:e("genesis-blocks/global-settings").getModifiedSettings(),custom:e("genesis-blocks/global-settings").getCustom()}}))])((
/**
 * SaveButton component
 *
 * Shows an adjacent notice with the result of the save operation.
 *
 * @since   1.0.0
 * @author  StudioPress
 * @license GPL-2.0-or-later
 */
function(e){var t=e.form,n=e.settings,i=e.custom,r=e.children,o=e.successMessage,c=e.failMessage,u=e.messageDuration,f=Object(s.useRef)();return Object(s.createElement)(s.Fragment,null,Object(s.createElement)(a.Button,{isPrimary:!0,isBusy:t.is_saving,disabled:t.is_saving,onClick:function(){Object(U.doAction)("genesisBlocks.savingSettings",n,i),clearTimeout(f.current),Object(l.dispatch)("genesis-blocks/global-settings").saveSettings(n)},className:"genesis-blocks-settings-save has-notices"},r),t.success||t.fail?function(){f.current=setTimeout((function(){return Object(l.dispatch)("genesis-blocks/global-settings").resetFormSaveState()}),1e3*u);var e=t.success?o:c;Object(D.speak)(e,"polite");var n="genesis-blocks-save-notice"+"".concat(t.success?" success":"")+"".concat(t.fail?" fail":"");return Object(s.createElement)("span",{className:n},e)}():"")}));
/**
 * Checkbox field
 *
 * @since   1.0.0
 * @author  StudioPress
 * @license GPL-2.0-or-later
 */var I=Object(c.compose)([Object(l.withDispatch)((function(){return{onUpdate:function(e){Object(l.dispatch)("genesis-blocks/global-settings").updateSetting({key:e.key,value:e.value})}}}))])((function(e){var t=e.settings,n=e.field,i=e.onUpdate;return Object(s.createElement)(a.CheckboxControl,{heading:n.heading,label:n.label,help:n.help,checked:!!t[n.id]&&t[n.id],onChange:function(e){return i({key:n.id,value:e})}})}));var M=
/**
 * Html field
 *
 * @since   1.0.0
 * @author  StudioPress
 * @license GPL-2.0-or-later
 */
function(e){var t=e.field;return Object(s.createElement)("div",{dangerouslySetInnerHTML:{__html:t.content}})},N=n(13),F=n(8),R=["image"],B=Object(o.__)("Image"),z=Object(o.__)("Select an image"),G=Object(o.__)("Choose image"),V=Object(o.__)("Replace image"),L=Object(o.__)("Remove image");var H=Object(l.withSelect)((function(e,t){var n=e("core").getMedia,i=t.settings[t.field.id];return{media:i?n(i):null,imageId:i}})),W=Object(l.withDispatch)((function(e,t){var n=e("genesis-blocks/global-settings").updateSetting;return{onUpdateImage:function(e){n({key:t.field.id,value:e.id})},onRemoveImage:function(){n({key:t.field.id,value:null})}}})),$=Object(c.compose)(H,W)((function(e){var t,n,i,r=e.field,o=e.imageId,c=e.media,l=e.onUpdateImage,u=e.onRemoveImage;if(c){var f=r.image_size||"thumbnail";Object(N.has)(c,["media_details","sizes",f])?(t=c.media_details.sizes[f].width,n=c.media_details.sizes[f].height,i=c.media_details.sizes[f].source_url):(t=c.media_details.width,n=c.media_details.height,i=c.source_url)}return Object(s.createElement)(s.Fragment,null,Object(s.createElement)("div",{className:"genesis-blocks-settings-image"},Object(s.createElement)("p",{className:"components-base-control__label"},r.label||B),Object(s.createElement)(F.MediaUpload,{title:r.label_media_modal||z,onSelect:l,allowedTypes:R,render:function(e){var l=e.open;return Object(s.createElement)("div",{className:"genesis-blocks-settings-image__container"},Object(s.createElement)(a.Button,{className:o?"genesis-blocks-settings-image__preview":"genesis-blocks-settings-image__toggle",onClick:l,"aria-label":r.label_button_aria||null,isSecondary:!0},!!o&&c&&Object(s.createElement)(a.ResponsiveWrapper,{naturalWidth:t,naturalHeight:n,isInline:!0},Object(s.createElement)("img",{src:i,alt:""})),!!o&&!c&&Object(s.createElement)(a.Spinner,null),!o&&(r.label_button||G)))},value:o}),!!o&&c&&!c.isLoading&&Object(s.createElement)(F.MediaUpload,{title:r.label_media_modal||z,onSelect:l,allowedTypes:R,modalClass:"genesis-blocks-settings-image__media-modal",render:function(e){var t=e.open;return Object(s.createElement)(a.Button,{onClick:t,isSecondary:!0,"aria-label":r.label_replace_aria||null},r.label_replace||V)}}),!!o&&Object(s.createElement)(a.Button,{onClick:u,isLink:!0,isDestructive:!0,"aria-label":r.label_remove_aria||null},r.label_remove||L),!!r.help&&Object(s.createElement)("p",{id:r.id+"__help",className:"components-base-control__help"},r.help)))})),q=n(6),J=n.n(q);var K={checkbox:I,html:M,image:$,radio:Object(c.compose)([Object(l.withDispatch)((function(){return{onUpdate:function(e){Object(l.dispatch)("genesis-blocks/global-settings").updateSetting({key:e.key,value:e.value})}}}))])((
/**
 * Radio field
 *
 * @since   1.0.0
 * @author  StudioPress
 * @license GPL-2.0-or-later
 */
function(e){var t=e.settings,n=e.field,i=e.onUpdate;return Object(s.createElement)(a.RadioControl,{label:n.label,help:n.help,selected:!!t[n.id]&&t[n.id],options:function(e){for(var t=[],n=0,i=Object.entries(e);n<i.length;n++){var r=J()(i[n],2),s=r[0],o=r[1];t.push({value:s,label:o})}return t}(n.options),onChange:function(e){return i({key:n.id,value:e})}})})),select:Object(c.compose)([Object(l.withDispatch)((function(){return{onUpdate:function(e){Object(l.dispatch)("genesis-blocks/global-settings").updateSetting({key:e.key,value:e.value})}}}))])((
/**
 * Select field
 *
 * @since   1.0.0
 * @author  StudioPress
 * @license GPL-2.0-or-later
 */
function(e){var t=e.settings,n=e.field,i=e.onUpdate;return Object(s.createElement)(a.SelectControl,{label:n.label,value:!!t[n.id]&&t[n.id],options:function(e){for(var t=[],n=0,i=Object.entries(e);n<i.length;n++){var r=J()(i[n],2),s=r[0],o=r[1];t.push({value:s,label:o})}return t}(n.options),onChange:function(e){return i({key:n.id,value:e})}})})),text:Object(c.compose)([Object(l.withDispatch)((function(){return{onUpdate:function(e){Object(l.dispatch)("genesis-blocks/global-settings").updateSetting({key:e.key,value:e.value})}}}))])((
/**
 * Text field
 *
 * @since   1.0.0
 * @author  StudioPress
 * @license GPL-2.0-or-later
 */
function(e){var t=e.settings,n=e.field,i=e.onUpdate;return Object(s.createElement)(a.TextControl,{label:n.label?n.label:"",help:n.help?n.help:"",onChange:function(e){return i({key:n.id,value:e})},value:t[n.id]?t[n.id]:""})})),textarea:Object(c.compose)([Object(l.withDispatch)((function(){return{onUpdate:function(e){Object(l.dispatch)("genesis-blocks/global-settings").updateSetting({key:e.key,value:e.value})}}}))])((
/**
 * Textarea field
 *
 * @since   1.0.0
 * @author  StudioPress
 * @license GPL-2.0-or-later
 */
function(e){var t=e.settings,n=e.field,i=e.onUpdate;return Object(s.createElement)(a.TextareaControl,{label:n.label?n.label:"",help:n.help?n.help:"",onChange:function(e){return i({key:n.id,value:e})},value:t[n.id]?t[n.id]:""})}))};var Q=Object(c.compose)([Object(l.withSelect)((function(){return{settings:Object(l.select)("genesis-blocks/global-settings").getSettings(),sections:Object(l.select)("genesis-blocks/global-settings").getSections()}}))])((function(e){var t=e.settings,n=e.sections;return Object(s.createElement)(s.Fragment,null,Object(s.createElement)(a.TabPanel,{className:"genesis-blocks-settings-sections",activeClass:"is-active",onSelect:function(){Object(l.dispatch)("genesis-blocks/global-settings").resetFormSaveState()},tabs:Object.values(n)},(function(e){return Object(s.createElement)(s.Fragment,null,function(e){if(e.hasOwnProperty("fields")&&Array.isArray(e.fields)){var n=e.fields.map((function(e,n){if(!K.hasOwnProperty(e.type))return"";var i=K[e.type];return Object(s.createElement)(i,{key:n,settings:t,field:e})}));if(n.length>0)return Object(s.createElement)(s.Fragment,null,n)}return Object(s.createElement)("p",null,Object(o.__)("No fields found for this section."))}(e),Object(s.createElement)(a.SlotFillProvider,null,Object(s.createElement)(a.Slot,{name:"GenesisBlocksSettings_"+e.name.replace("genesis_blocks_settings_","")}),Object(s.createElement)(u.PluginArea,null)),Object(s.createElement)(A,{successMessage:Object(o.__)("Settings saved"),failMessage:Object(o.__)("Saving failed"),messageDuration:"2"},Object(o.__)("Save All")))})))}));
/**
 * The React application for the Genesis Blocks settings page.
 *
 * @since   1.0.0
 * @author  StudioPress
 * @license GPL-2.0-or-later
 */Object(s.render)(Object(s.createElement)(Q,null),document.getElementById("root"))}]);