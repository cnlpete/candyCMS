window.addEvent("domready",function(){$each($$(".js-image_overlay"),function(a){var b=a.getSize();a.setStyle("margin-top","-"+b.y+"px");a.fade("hide")});$each($$("input[type=submit]"),function(a){a.addEvent("click",function(b){this.disabled=1;this.set("value",LANG_SENDING)}.bind(a))})});function hideDiv(a){new Fx.Slide(a).slideOut()}function fadeDiv(a){$(a).fade("toggle")}function imageOverlay(a,b){fadeDiv(a);$(a).setStyle("margin-top",b)}function showDiv(a){$(a).setStyle("display","inline");if($("js-flash_success")||$("js-flash_error")){(function(){hideDiv(a)}).delay(5000)}}if($("js-flash_success")||$("js-flash_error")){showDiv("js-flash_message")}function quoteMessage(d,b){var a=$(b).get("html");var c="[quote="+d+"]"+a+"[/quote]\n";var e=$("js-create_commment_text").get("value");$("js-create_commment_text").set("html",e+c);return false}function resetContent(a){$(a).set("html","")}function confirmDelete(a){if(confirm(LANG_DELETE_FILE_OR_CONTENT)){parent.location.href=a}}if($$(".js-tooltip")){$$(".js-tooltip").each(function(b,a){var c=b.get("title").split("::");b.store("tip:title",c[0]);b.store("tip:text",c[1])});var myTips=new Tips(".js-tooltip");myTips.addEvent("show",function(a){a.fade("in")});myTips.addEvent("hide",function(a){a.fade("out")})}function stripNoAlphaChars(a){a=a.replace(/ /g,"_");a=a.replace(/Ä/g,"Ae");a=a.replace(/ä/g,"ae");a=a.replace(/Ö/g,"Oe");a=a.replace(/ö/g,"oe");a=a.replace(/Ü/g,"Ue");a=a.replace(/ü/g,"ue");a=a.replace(/ß/g,"ss");a=a.replace(/\W/g,"_");return a}function stripSlash(a){a=a.replace(/\//g,"&frasl;");return a}function reloadPage(c,b){var a="js-ajax_reload";$(a).setStyle("display","block").addClass("center");$(a).set("html","<img src='"+b+"/loading.gif' alt='"+LANG_LOADING+"' />");$(a).load(c)}function checkPasswords(){if($("password")&&$("password2")){if($("password").value==$("password2").value){$("icon").set("class","icon-success")}else{$("icon").set("class","icon-close")}}};