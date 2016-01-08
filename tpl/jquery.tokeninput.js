(function(e){var t={method:"GET",contentType:"json",queryParam:"q",searchDelay:300,minChars:1,propertyToSearch:"name",jsonContainer:null,hintText:"Начните вводить ингредиент",noResultsText:"Нет результатов",searchingText:"Поиск...",deleteText:"&times;",animateDropdown:true,tokenLimit:null,tokenDelimiter:",",preventDuplicates:false,tokenValue:"id",prePopulate:null,processPrePopulate:false,idPrefix:"token-input-",resultsFormatter:function(e){return"<li>"+e[this.propertyToSearch]+"</li>"},tokenFormatter:function(e){return"<li><p>"+e[this.propertyToSearch]+"</p></li>"},onResult:null,onAdd:null,onDelete:null,onReady:null};var n={tokenList:"token-input-list",token:"token-input-token",tokenDelete:"token-input-delete-token",selectedToken:"token-input-selected-token",highlightedToken:"token-input-highlighted-token",dropdown:"token-input-dropdown",dropdownItem:"token-input-dropdown-item",dropdownItem2:"token-input-dropdown-item2",selectedDropdownItem:"token-input-selected-dropdown-item",inputToken:"token-input-input-token"};var r={BEFORE:0,AFTER:1,END:2};var i={BACKSPACE:8,TAB:9,ENTER:13,ESCAPE:27,SPACE:32,PAGE_UP:33,PAGE_DOWN:34,END:35,HOME:36,LEFT:37,UP:38,RIGHT:39,DOWN:40,NUMPAD_ENTER:108,COMMA:188};var s={init:function(n,r){var i=e.extend({},t,r||{});return this.each(function(){e(this).data("tokenInputObject",new e.TokenList(this,n,i))})},clear:function(){this.data("tokenInputObject").clear();return this},add:function(e){this.data("tokenInputObject").add(e);return this},remove:function(e){this.data("tokenInputObject").remove(e);return this},get:function(){return this.data("tokenInputObject").getTokens()}};e.fn.tokenInput=function(e){if(s[e]){return s[e].apply(this,Array.prototype.slice.call(arguments,1))}else{return s.init.apply(this,arguments)}};e.TokenList=function(t,s,o){function x(){if(o.tokenLimit!==null&&f>=o.tokenLimit){p.hide();D();return}}function T(){if(h===(h=p.val())){return}var e=h.replace(/&/g,"&").replace(/\s/g," ").replace(/</g,"&lt;").replace(/>/g,"&gt;");E.html(e);p.width(E.width()+30)}function N(e){return e>=48&&e<=90||e>=96&&e<=111||e>=186&&e<=192||e>=219&&e<=222}function C(t){var n=o.tokenFormatter(t);n=e(n).addClass(o.classes.token).insertBefore(b);e("<span>"+o.deleteText+"</span>").addClass(o.classes.tokenDelete).appendTo(n).click(function(){M(e(this).parent());d.change();return false});var r={id:t.id};r[o.propertyToSearch]=t[o.propertyToSearch];e.data(n.get(0),"tokeninput",t);a=a.slice(0,m).concat([r]).concat(a.slice(m));m++;_(a,d);f+=1;if(o.tokenLimit!==null&&f>=o.tokenLimit){p.hide();D()}return n}function k(t){var n=o.onAdd;if(f>0&&o.preventDuplicates){var r=null;y.children().each(function(){var n=e(this);var i=e.data(n.get(0),"tokeninput");if(i&&i.id===t.id){r=n;return false}});if(r){L(r);b.insertAfter(r);p.focus();return}}if(o.tokenLimit==null||f<o.tokenLimit){C(t);x()}p.val("");D();if(e.isFunction(n)){n.call(d,t)}}function L(e){e.addClass(o.classes.selectedToken);v=e.get(0);p.val("");D()}function A(e,t){e.removeClass(o.classes.selectedToken);v=null;if(t===r.BEFORE){b.insertBefore(e);m--}else if(t===r.AFTER){b.insertAfter(e);m++}else{b.appendTo(y);m=f}p.focus()}function O(t){var n=v;if(v){A(e(v),r.END)}if(n===t.get(0)){A(t,r.END)}else{L(t)}}function M(t){var n=e.data(t.get(0),"tokeninput");var r=o.onDelete;var i=t.prevAll().length;if(i>m)i--;t.remove();v=null;p.focus();a=a.slice(0,i).concat(a.slice(i+1));if(i<m)m--;_(a,d);f-=1;if(o.tokenLimit!==null){p.show().val("").focus()}if(e.isFunction(r)){r.call(d,n)}}function _(t,n){var r=e.map(t,function(e){return e[o.tokenValue]});n.val(r.join(o.tokenDelimiter))}function D(){w.hide().empty();g=null}function P(){w.css({position:"absolute",top:e(y).offset().top+e(y).outerHeight(),left:e(y).offset().left,zindex:999}).show()}function H(){if(o.searchingText){w.html("<p>"+o.searchingText+"</p>");P()}}function B(){if(o.hintText){w.html("<p>"+o.hintText+"</p>");P()}}function j(e,t){return e.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)("+t+")(?![^<>]*>)(?![^&;]+;)","gi"),"<b>$1</b>")}function F(e,t,n){return e.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)("+t+")(?![^<>]*>)(?![^&;]+;)","g"),j(t,n))}function I(t,n){if(n&&n.length){w.empty();var r=e("<ul>").appendTo(w).mouseover(function(t){q(e(t.target).closest("li"))}).mousedown(function(t){k(e(t.target).closest("li").data("tokeninput"));d.change();return false}).hide();e.each(n,function(n,i){var s=o.resultsFormatter(i);s=F(s,i[o.propertyToSearch],t);s=e(s).appendTo(r);if(n%2){s.addClass(o.classes.dropdownItem)}else{s.addClass(o.classes.dropdownItem2)}if(n===0){q(s)}e.data(s.get(0),"tokeninput",i)});P();if(o.animateDropdown){r.slideDown("fast")}else{r.show()}}else{if(o.noResultsText){w.html("<p>"+o.noResultsText+"</p>");P()}}}function q(t){if(t){if(g){R(e(g))}t.addClass(o.classes.selectedDropdownItem);g=t.get(0)}}function R(e){e.removeClass(o.classes.selectedDropdownItem);g=null}function U(){var t=p.val().toLowerCase();if(t&&t.length){if(v){A(e(v),r.AFTER)}if(t.length>=o.minChars){H();clearTimeout(c);c=setTimeout(function(){z(t)},o.searchDelay)}else{D()}}}function z(t){var n=t+W();var r=l.get(n);if(r){I(t,r)}else{if(o.url){var i=W();var s={};s.data={};if(i.indexOf("?")>-1){var u=i.split("?");s.url=u[0];var a=u[1].split("&");e.each(a,function(e,t){var n=t.split("=");s.data[n[0]]=n[1]})}else{s.url=i}s.data[o.queryParam]=t;s.type=o.method;s.dataType=o.contentType;if(o.crossDomain){s.dataType="jsonp"}s.success=function(r){if(e.isFunction(o.onResult)){r=o.onResult.call(d,r)}l.add(n,o.jsonContainer?r[o.jsonContainer]:r);if(p.val().toLowerCase()===t){I(t,o.jsonContainer?r[o.jsonContainer]:r)}};e.ajax(s)}else if(o.local_data){var f=e.grep(o.local_data,function(e){return e[o.propertyToSearch].toLowerCase().indexOf(t.toLowerCase())>-1});if(e.isFunction(o.onResult)){f=o.onResult.call(d,f)}l.add(n,f);I(t,f)}}}function W(){var e=o.url;if(typeof o.url=="function"){e=o.url.call()}return e}if(e.type(s)==="string"||e.type(s)==="function"){o.url=s;var u=W();if(o.crossDomain===undefined){if(u.indexOf("://")===-1){o.crossDomain=false}else{o.crossDomain=location.href.split(/\/+/g)[1]!==u.split(/\/+/g)[1]}}}else if(typeof s==="object"){o.local_data=s}if(o.classes){o.classes=e.extend({},n,o.classes)}else if(o.theme){o.classes={};e.each(n,function(e,t){o.classes[e]=t+"-"+o.theme})}else{o.classes=n}var a=[];var f=0;var l=new e.TokenList.Cache;var c;var h;var p=e('<input type="text"  autocomplete="off">').css({outline:"none"}).attr("id",o.idPrefix+t.id).focus(function(){if(o.tokenLimit===null||o.tokenLimit!==f){B()}}).blur(function(){D();e(this).val("")}).bind("keyup keydown blur update",T).keydown(function(t){var n;var s;switch(t.keyCode){case i.LEFT:case i.RIGHT:case i.UP:case i.DOWN:if(!e(this).val()){n=b.prev();s=b.next();if(n.length&&n.get(0)===v||s.length&&s.get(0)===v){if(t.keyCode===i.LEFT||t.keyCode===i.UP){A(e(v),r.BEFORE)}else{A(e(v),r.AFTER)}}else if((t.keyCode===i.LEFT||t.keyCode===i.UP)&&n.length){L(e(n.get(0)))}else if((t.keyCode===i.RIGHT||t.keyCode===i.DOWN)&&s.length){L(e(s.get(0)))}}else{var o=null;if(t.keyCode===i.DOWN||t.keyCode===i.RIGHT){o=e(g).next()}else{o=e(g).prev()}if(o.length){q(o)}return false}break;case i.BACKSPACE:n=b.prev();if(!e(this).val().length){if(v){M(e(v));d.change()}else if(n.length){L(e(n.get(0)))}return false}else if(e(this).val().length===1){D()}else{setTimeout(function(){U()},5)}break;case i.TAB:case i.ENTER:case i.NUMPAD_ENTER:case i.COMMA:if(g){k(e(g).data("tokeninput"));d.change();return false}break;case i.ESCAPE:D();return true;default:if(String.fromCharCode(t.which)){setTimeout(function(){U()},5)}break}});var d=e(t).hide().val("").focus(function(){p.focus()}).blur(function(){p.blur()});var v=null;var m=0;var g=null;var y=e("<ul />").addClass(o.classes.tokenList).click(function(t){var n=e(t.target).closest("li");if(n&&n.get(0)&&e.data(n.get(0),"tokeninput")){O(n)}else{if(v){A(e(v),r.END)}p.focus()}}).mouseover(function(t){var n=e(t.target).closest("li");if(n&&v!==this){n.addClass(o.classes.highlightedToken)}}).mouseout(function(t){var n=e(t.target).closest("li");if(n&&v!==this){n.removeClass(o.classes.highlightedToken)}}).insertBefore(d);var b=e("<li />").addClass(o.classes.inputToken).appendTo(y).append(p);var w=e("<div>").addClass(o.classes.dropdown).appendTo("body").hide();var E=e("<tester/>").insertAfter(p).css({position:"absolute",top:-9999,left:-9999,width:"auto",fontSize:p.css("fontSize"),fontFamily:p.css("fontFamily"),fontWeight:p.css("fontWeight"),letterSpacing:p.css("letterSpacing"),whiteSpace:"nowrap"});d.val("");var S=o.prePopulate||d.data("pre");if(o.processPrePopulate&&e.isFunction(o.onResult)){S=o.onResult.call(d,S)}if(S&&S.length){e.each(S,function(e,t){C(t);x()})}if(e.isFunction(o.onReady)){o.onReady.call()}this.clear=function(){y.children("li").each(function(){if(e(this).children("input").length===0){M(e(this))}})};this.add=function(e){k(e)};this.remove=function(t){y.children("li").each(function(){if(e(this).children("input").length===0){var n=e(this).data("tokeninput");var r=true;for(var i in t){if(t[i]!==n[i]){r=false;break}}if(r){M(e(this))}}})};this.getTokens=function(){return a}};e.TokenList.Cache=function(t){var n=e.extend({max_size:500},t);var r={};var i=0;var s=function(){r={};i=0};this.add=function(e,t){if(i>n.max_size){s()}if(!r[e]){i+=1}r[e]=t};this.get=function(e){return r[e]}}})(jQuery)