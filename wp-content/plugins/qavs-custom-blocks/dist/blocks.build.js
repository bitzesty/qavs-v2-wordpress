!function(e){function t(r){if(n[r])return n[r].exports;var a=n[r]={i:r,l:!1,exports:{}};return e[r].call(a.exports,a,a.exports,t),a.l=!0,a.exports}var n={};t.m=e,t.c=n,t.d=function(e,n,r){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:r})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=4)}([function(e,t){e.exports=wp.blockEditor},function(e,t){e.exports=wp.blocks},function(e,t){e.exports=wp.components},function(e,t){e.exports=wp.data},function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});n(5),n(8),n(11),n(14),n(17),n(20),n(21),n(22),n(24),n(26)},function(e,t,n){"use strict";var r=n(6),a=(n.n(r),n(7)),l=(n.n(a),n(2)),i=(n.n(l),n(0)),c=(n.n(i),Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e}),__=wp.i18n.__,o=wp.blocks.registerBlockType,m=wp.editor,s=m.MediaUpload,u=m.MediaUploadCheck,p=m.InspectorControls,d=wp.data.withSelect,w=function(e,t){i.useBlockProps.save();return wp.element.createElement("div",{className:"commitee-member "+t},0!=e.mediaUrl&&wp.element.createElement("img",{src:e.mediaUrl,alt:"","aria-hidden":"true",className:"commitee-member__image"}),wp.element.createElement("div",{className:"commitee-member__details"},wp.element.createElement("h4",{className:"commitee-member__name"},e.name),wp.element.createElement("dl",{className:"commitee-member__detail"},wp.element.createElement("dt",null,"Areas of interest"),wp.element.createElement("dd",{className:"commitee-member__areas-of-interest"},e.areas_of_interest)),wp.element.createElement("dl",{className:"commitee-member__detail"},wp.element.createElement("dt",null,"About"),wp.element.createElement("dd",{className:"commitee-member__about"},wp.element.createElement(i.RichText.Content,{className:"",value:e.about})))))},v=function(e){var t=e.attributes,n=e.setAttributes,r=e.className;if(!e.isSelected)return w(t,r);var a=Object(i.useBlockProps)(),o=function(t){e.setAttributes({mediaId:t.id,mediaUrl:t.url})},m=function(){e.setAttributes({mediaId:0,mediaUrl:""})},d=wp.element.createElement("p",null,__("Choose an image"));return wp.element.createElement("div",{className:r},wp.element.createElement(p,null,wp.element.createElement(l.PanelBody,{title:__("Commitee member details"),initialOpen:!0},wp.element.createElement(u,{fallback:d},wp.element.createElement(s,{title:__("Picture"),onSelect:o,value:t.mediaId,allowedTypes:["image"],render:function(e){var n=e.open;return wp.element.createElement(l.Button,{className:0==t.mediaId?"editor-post-featured-image__toggle":"editor-post-featured-image__preview",onClick:n},0==t.mediaId&&__("Choose an image"))}})),void 0!=e.media&&wp.element.createElement(l.ResponsiveWrapper,{naturalWidth:e.media.media_details.width,naturalHeight:e.media.media_details.height},wp.element.createElement("img",{src:e.media.source_url})),0!=t.mediaId&&wp.element.createElement("div",null,wp.element.createElement("br",null),wp.element.createElement(u,null,wp.element.createElement(s,{title:__("Replace picture"),value:t.mediaId,onSelect:o,allowedTypes:["image"],render:function(e){var t=e.open;return wp.element.createElement(l.Button,{onClick:t,isDefault:!0,isLarge:!0},__("Replace picture"))}}))),0!=t.mediaId&&wp.element.createElement("div",null,wp.element.createElement("br",null),wp.element.createElement(u,null,wp.element.createElement(l.Button,{onClick:m,isLink:!0,isDestructive:!0},__("Remove picture")))))),wp.element.createElement(l.TextControl,{label:__("Name"),value:t.name,onChange:function(e){return n({name:e})}}),wp.element.createElement(l.TextControl,{label:__("Areas of interest"),value:t.areas_of_interest,onChange:function(e){return n({areas_of_interest:e})}}),wp.element.createElement("label",null,__("About")),wp.element.createElement("div",{className:"rich-text-wrapper"},wp.element.createElement(i.RichText,c({},a,{tagName:"div",label:__("About"),value:t.about,onChange:function(e){return n({about:e})}}))))};o("qavs/commitee-member",{title:__("Commitee member"),icon:"shield",category:"common",keywords:[],attributes:{name:{type:"string",source:"text",selector:".commitee-member__name"},areas_of_interest:{type:"string",source:"text",selector:".commitee-member__areas-of-interest"},about:{type:"string",source:"html",selector:".commitee-member__about"},mediaId:{type:"number",default:0},mediaUrl:{type:"string",default:""}},edit:d(function(e,t){return{media:t.attributes.mediaId?e("core").getMedia(t.attributes.mediaId):void 0}})(v),save:function(e){var t=e.className,n=e.attributes;return w(n,t)}})},function(e,t){},function(e,t){},function(e,t,n){"use strict";var r=n(9),a=(n.n(r),n(10)),l=(n.n(a),n(1)),i=(n.n(l),n(0)),c=(n.n(i),Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e}),__=wp.i18n.__,o=function(e,t){return wp.element.createElement("div",{className:"notice "+t},wp.element.createElement("div",{className:"notice__content"},wp.element.createElement(i.RichText.Content,{value:e.content})))};Object(l.registerBlockType)("qavs/notice",{title:__("Notice"),icon:"megaphone",category:"common",keywords:[],styles:[{name:"gray",label:__("Gray"),isDefault:!0},{name:"purple",label:__("Purple")}],attributes:{content:{type:"string",source:"html",selector:".notice__content"}},edit:function(e){var t=e.className,n=e.attributes,r=e.setAttributes;if(!e.isSelected)return o(n,t);var a=Object(i.useBlockProps)();return wp.element.createElement("div",null,wp.element.createElement("label",null,__("Notice text")),wp.element.createElement("div",{className:"rich-text-wrapper"},wp.element.createElement(i.RichText,c({},a,{value:n.content,onChange:function(e){return r({content:e})}}))))},save:function(e){var t=e.className,n=e.attributes;return o(n,t)}})},function(e,t){},function(e,t){},function(e,t,n){"use strict";var r=n(12),a=(n.n(r),n(13)),l=(n.n(a),n(1)),i=(n.n(l),n(0)),c=(n.n(i),n(2)),o=(n.n(c),Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e}),__=wp.i18n.__,m=function(e,t){var n=i.useBlockProps.save();return wp.element.createElement("div",{className:"expander "+t},wp.element.createElement("button",{className:"expander__heading",ariaExpanded:!1},wp.element.createElement("span",null,e.heading)),wp.element.createElement("div",{className:"expander__content"},wp.element.createElement(i.RichText.Content,o({},n,{tagName:"div",value:e.content}))))};Object(l.registerBlockType)("qavs/expander",{title:__("Expander"),icon:"megaphone",category:"common",keywords:[],attributes:{heading:{type:"string",source:"text",selector:".expander__heading span"},content:{type:"string",source:"html",selector:".expander__content"}},edit:function(e){var t=e.className,n=e.attributes,r=e.setAttributes;if(!e.isSelected)return m(n,t);var a=Object(i.useBlockProps)();return wp.element.createElement("div",null,wp.element.createElement(c.TextControl,{label:__("Heading"),value:n.heading,onChange:function(e){return r({heading:e})}}),wp.element.createElement("label",null,__("Resource text")),wp.element.createElement("div",{className:"rich-text-wrapper"},wp.element.createElement(i.RichText,o({},a,{tagName:"div",label:__("Resource text"),value:n.content,onChange:function(e){return r({content:e})}}))))},save:function(e){var t=e.className,n=e.attributes;return m(n,t)}})},function(e,t){},function(e,t){},function(e,t,n){"use strict";var r=n(15),a=(n.n(r),n(16)),l=(n.n(a),n(1)),i=(n.n(l),n(0)),c=(n.n(i),n(2)),o=(n.n(c),Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e}),__=wp.i18n.__,m=wp.data.withSelect,s=wp.editor,u=s.MediaUpload,p=s.MediaUploadCheck,d=s.InspectorControls,w=function(e,t){i.useBlockProps.save();return wp.element.createElement("div",{className:"resource "+t},0!=e.mediaUrl&&wp.element.createElement("img",{src:e.mediaUrl,alt:"","aria-hidden":"true",className:"resource__image"}),wp.element.createElement("div",{className:"resource__details"},wp.element.createElement("h3",{className:"resource__name"},e.name),wp.element.createElement("div",{className:"resource__content"},wp.element.createElement(i.RichText.Content,{value:e.content}),e.url&&wp.element.createElement("a",{href:e.url,rel:"noopener nofollow","aria-label":"Visit "+e.name+"'s website",className:"resource__cta"},"Visit website"))))},v=function(e){var t=e.attributes,n=e.setAttributes,r=e.className;if(!e.isSelected)return w(t,r);var a=Object(i.useBlockProps)(),l=function(t){e.setAttributes({mediaId:t.id,mediaUrl:t.url})},m=function(){e.setAttributes({mediaId:0,mediaUrl:""})},s=wp.element.createElement("p",null,__("Choose an image"));return wp.element.createElement("div",{className:r},wp.element.createElement(d,null,wp.element.createElement(c.PanelBody,{title:__("Volunteering resource details"),initialOpen:!0},wp.element.createElement(p,{fallback:s},wp.element.createElement(u,{title:__("Picture"),onSelect:l,value:t.mediaId,allowedTypes:["image"],render:function(e){var n=e.open;return wp.element.createElement(c.Button,{className:0==t.mediaId?"editor-post-featured-image__toggle":"editor-post-featured-image__preview",onClick:n},0==t.mediaId&&__("Choose an image"))}})),void 0!=e.media&&wp.element.createElement(c.ResponsiveWrapper,{naturalWidth:e.media.media_details.width,naturalHeight:e.media.media_details.height},wp.element.createElement("img",{src:e.media.source_url})),0!=t.mediaId&&wp.element.createElement("div",null,wp.element.createElement("br",null),wp.element.createElement(p,null,wp.element.createElement(u,{title:__("Replace picture"),value:t.mediaId,onSelect:l,allowedTypes:["image"],render:function(e){var t=e.open;return wp.element.createElement(c.Button,{onClick:t,isDefault:!0,isLarge:!0},__("Replace picture"))}}))),0!=t.mediaId&&wp.element.createElement("div",null,wp.element.createElement("br",null),wp.element.createElement(p,null,wp.element.createElement(c.Button,{onClick:m,isLink:!0,isDestructive:!0},__("Remove picture")))))),wp.element.createElement(c.TextControl,{label:__("Name"),value:t.name,onChange:function(e){return n({name:e})}}),wp.element.createElement(c.TextControl,{label:__("URL"),value:t.url,onChange:function(e){return n({url:e})}}),wp.element.createElement("label",null,__("Resource text")),wp.element.createElement("div",{className:"rich-text-wrapper"},wp.element.createElement(i.RichText,o({},a,{tagName:"div",label:__("Resource text"),value:t.content,onChange:function(e){return n({content:e})}}))))};Object(l.registerBlockType)("qavs/resource",{title:__("Volunteering resource"),icon:"megaphone",category:"common",keywords:[],attributes:{name:{type:"string",source:"text",selector:".resource__name"},content:{type:"string",source:"html",selector:".resource__content"},url:{type:"string",default:""},mediaId:{type:"number",default:0},mediaUrl:{type:"string",default:""}},edit:m(function(e,t){return{media:t.attributes.mediaId?e("core").getMedia(t.attributes.mediaId):void 0}})(v),save:function(e){var t=e.className,n=e.attributes;return w(n,t)}})},function(e,t){},function(e,t){},function(e,t,n){"use strict";var r=n(18),a=(n.n(r),n(19)),__=(n.n(a),wp.i18n.__),l=wp.blocks.registerBlockType,i=wp.editor.InnerBlocks;l("qavs/section",{title:__("Section"),icon:"shield",category:"common",keywords:[],styles:[{name:"default",label:__("Gray"),isDefault:!0},{name:"white",label:__("White")}],edit:function(e){var t="undefined"!==typeof e.insertBlocksAfter?wp.element.createElement(i,null):"";return wp.element.createElement("div",{className:"section "+e.className},wp.element.createElement("div",{className:"container"},t))},save:function(e){return wp.element.createElement("div",{className:"section "+e.className},wp.element.createElement("div",{className:"container"},wp.element.createElement(i.Content,null)))}})},function(e,t){},function(e,t){},function(e,t,n){"use strict";var r=n(1),a=(n.n(r),n(3)),l=(n.n(a),n(0)),i=(n.n(l),n(2));n.n(i),wp.i18n.__;Object(r.registerBlockType)("qavs/parental-tabs",{apiVersion:2,title:"Parental tabs",icon:"megaphone",category:"widgets",attributes:{categoryID:{type:"string"}},edit:function(e){var t=(e.attributes,e.setAttributes,Object(l.useBlockProps)());return wp.element.createElement("div",t,wp.element.createElement("h3",null,"Tabs based on parent page"))},save:function(){return null}})},function(e,t,n){"use strict";var r=n(1),a=(n.n(r),n(3)),l=(n.n(a),n(0)),i=(n.n(l),n(2));n.n(i),wp.i18n.__;Object(r.registerBlockType)("qavs/parental-pagination",{apiVersion:2,title:"Parental pagination",icon:"megaphone",category:"widgets",edit:function(e){var t=(e.attributes,e.setAttributes,Object(l.useBlockProps)());return wp.element.createElement("div",t,wp.element.createElement("h3",null,"Pagination based on parent page"))},save:function(){return null}})},function(e,t,n){"use strict";var r=n(23),a=(n.n(r),n(1)),l=(n.n(a),n(3)),i=(n.n(l),n(0));n.n(i);Object(a.registerBlockType)("qavs/featured-news",{apiVersion:2,title:"Featured news",icon:"megaphone",category:"widgets",edit:Object(l.withSelect)(function(e){return{posts:e("core").getEntityRecords("postType","post")}})(function(e){var t=(e.posts,Object(i.useBlockProps)());return wp.element.createElement("div",t,wp.element.createElement("div",{className:"dynamic-block-placeholder"},wp.element.createElement("h3",null,"Featured news")))}),save:function(){return null}})},function(e,t){},function(e,t,n){"use strict";var r=n(25),a=(n.n(r),n(1)),l=(n.n(a),n(3)),i=(n.n(l),n(0)),c=(n.n(i),n(2)),__=(n.n(c),wp.i18n.__);Object(a.registerBlockType)("qavs/featured-awardees",{apiVersion:2,title:"Featured awardees",icon:"megaphone",category:"widgets",attributes:{categoryID:{type:"string"}},edit:Object(l.withSelect)(function(e){return{posts:e("core").getEntityRecords("postType","post")}})(function(e){var t=(e.posts,e.attributes),n=e.setAttributes,r=Object(i.useBlockProps)();return wp.element.createElement("div",r,wp.element.createElement("h3",null,"Featured awardees configuration"),wp.element.createElement(c.TextControl,{label:__("Category ID"),value:t.categoryID,onChange:function(e){return n({categoryID:e})}}))}),save:function(){return null}})},function(e,t){},function(e,t,n){"use strict";var r=n(27),a=(n.n(r),n(1)),l=(n.n(a),n(3)),i=(n.n(l),n(0));n.n(i);Object(a.registerBlockType)("qavs/promoted-article",{apiVersion:2,title:"Promoted article",icon:"megaphone",category:"widgets",edit:Object(l.withSelect)(function(e){return{posts:e("core").getEntityRecords("postType","post")}})(function(e){var t=(e.posts,Object(i.useBlockProps)());return wp.element.createElement("div",t,wp.element.createElement("div",{className:"dynamic-block-placeholder"},wp.element.createElement("h3",null,"Promoted article")))}),save:function(){return null}})},function(e,t){}]);