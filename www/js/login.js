(function(g,i,d){var m={mainSlider:document.getElementById("nivo-slider")||"",recommendationSlider:document.getElementById("mycarousel")||"",freeTrainings:document.getElementById("projects")||"",contactMap:document.getElementsByClassName("map")||"",contactForm:document.getElementById("contactform")||""};var n={},f={form:{0:"order",1:"contact"}},e={type:"post",url:"",data:n,responseType:"json"},o={characterReg:/^\s*[a-zA-Z0-9,\s]+\s*$/,emailExp:/^[^\s()<>@,;:\/]+@\w[\w\.-]+\.[a-z]{2,}$/i};var h={init:function(){if(m.mainSlider!==""){this.setNivo()}if(m.recommendationSlider!==""){this.setBx()}},setNivo:function(){i(".nivo-slider").nivoSlider({effect:"fade",slices:15,boxCols:8,boxRows:4,animSpeed:500,pauseTime:5000,startSlide:0,directionNav:true,controlNav:false,controlNavThumbs:false,pauseOnHover:true,manualAdvance:false,prevText:"",nextText:"",randomStart:false,beforeChange:function(){},afterChange:function(){},slideshowEnd:function(){},lastSlide:function(){},afterLoad:function(){}})},setBx:function(){i(m.recommendationSlider).bxSlider({minSlides:4,maxSlides:4,slideWidth:240,slideMargin:0,pager:false,nextText:"",prevText:""})}},p={init:function(){if(m.freeTrainings!==""){this.setQuicksand()}},setQuicksand:function(){var q=i(".portfolio").clone();i(".filter li").click(function(s){i(".filter li").removeClass("active");var t=i(this).attr("class").split(" ").slice(-1)[0];if(t=="all"){var r=q.find(".item-thumbs")}else{var r=q.find(".item-thumbs[data-type="+t+"]")}i(".portfolio").quicksand(r,{duration:600,adjustHeight:"auto"},function(){});i(this).addClass("active");return false});i(".filter li:first").addClass("active")}},l={orderButton:document.querySelectorAll(".order")||"",init:function(){if(this.orderButton!==""){i(".order").live("click",function(){i("#sendmessage").html("").removeClass("show");i("#myOrder").find("form").removeClass("hidden").find("input[type=text]").val("");i("#myOrder").find("form").prepend('<input type="hidden" name="type" value="'+i(this).data("type")+'" />')});i("#myOrder").find("form").submit(function(q){q.preventDefault();q.stopPropagation();if(!o.emailExp.test(i("#inputEmail").val())){i("#inputEmail").closest(".control-group").addClass("error")}else{i("#inputEmail").closest(".control-group").removeClass("error")}if(!o.characterReg.test(i("#inputCompany").val())){i("#inputCompany").closest(".control-group").addClass("error")}else{i("#inputCompany").closest(".control-group").removeClass("error")}if(!o.characterReg.test(i("#inputContact").val())){i("#inputContact").closest(".control-group").addClass("error")}else{i("#inputContact").closest(".control-group").removeClass("error")}if(i("#myOrder").find(".error").length){return false}n={};n.action=f.form[0];n.form=i(this).serializeArray();e.url="/contact/";e.data=n;e.success=function(r){i("#sendmessage").html(r.message);if(r.type=="success"){i("#sendmessage").addClass("show");i("#myOrder").find("form").addClass("hidden");i(".order").remove()}};i.ajax(e);return false})}}},j={init:function(){if(m.contactForm!==""){this.setContact()}},setContact:function(){i("form.validateform").submit(function(){var s=i(this).find(".field"),r=false,q=/^[^\s()<>@,;:\/]+@\w[\w\.-]+\.[a-z]{2,}$/i;s.children("input").each(function(){var u=i(this);var v=u.attr("data-rule");if(v!=d){var y=false;var x=v.indexOf(":",0);if(x>=0){var w=v.substr(x+1,v.length);v=v.substr(0,x)}else{v=v.substr(x+1,v.length)}switch(v){case"required":if(u.val()==""){r=y=true}break;case"maxlen":if(u.val().length<parseInt(w)){r=y=true}break;case"email":if(!o.emailExp.test(u.val())){r=y=true}break;case"checked":if(!u.attr("checked")){r=y=true}break;case"regexp":w=new RegExp(w);if(!w.test(u.val())){r=y=true}break}u.next(".validation").html((y?(u.attr("data-msg")!=d?u.attr("data-msg"):"wrong Input"):"")).show("blind")}});s.children("textarea").each(function(){var u=i(this);var v=u.attr("data-rule");if(v!=d){var y=false;var x=v.indexOf(":",0);if(x>=0){var w=v.substr(x+1,v.length);v=v.substr(0,x)}else{v=v.substr(x+1,v.length)}switch(v){case"required":if(u.val()==""){r=y=true}break;case"maxlen":if(u.val().length<parseInt(w)){r=y=true}break}u.next(".validation").html((y?(u.attr("data-msg")!=d?u.attr("data-msg"):"wrong Input"):"")).show("blind")}});if(r){return false}else{var t=i(this).serializeArray()}n={};n.action=f.form[1];n.form=t;e.url="/contact/";e.data=n;e.success=function(u){i("#sendmessage").html(u.message);if(u.type=="success"){i("#sendmessage").addClass("show");i("#contactform").find(".row").addClass("hidden")}};i.ajax(e);return false})}};g.init=function(){if(Modernizr.touch){i("body").addClass("touch-device")}a(window.location.pathname);h.init();p.init();j.init();l.init();k();i(".social-network li a, .options_box .color a").tooltip();i(window).scroll(function(){if(i(this).scrollTop()>100){i(".scrollup").fadeIn()}else{i(".scrollup").fadeOut()}});i(".scrollup").click(function(){i("html, body").animate({scrollTop:0},1000);return false})};function c(s){var r=jQuery.extend({url:"",responseType:"json",async:true,data:{},type:"POST",onOpen:function(){},onClose:function(){},onSelect:function(){}},s);var q=i.ajax({url:r.url,data:r.data,type:r.type,dataType:r.responseType,async:false}).responseText;if(r.responseType=="json"){q=i.parseJSON(q);q.type?sendMessage("alert-"+q.type,q.message):"";return q.result?q:(q.type=="success"?true:false)}return q}function b(){i("#content").css("min-height",i(window).outerHeight(true)-(i("body").outerHeight(true)-i("body").height())-i("#header").outerHeight(true)-(i("#content").outerHeight(true)-i("#content").height())+(i(".page-title").length?Math.abs(parseInt(i(".page-title").css("margin-top"))):0)-i("#footer").outerHeight(true)-i("#footer-bottom").outerHeight(true))}function a(r){if(r=="/"){r="/index.html"}else{var s=new RegExp(/\b[blog|project]+\b/);var q=s.exec(r);if(q!==null){r="/"+q+".html"}}i("li").removeClass("active");i('a[href="'+r+'"]').parent().addClass("active")}function k(){i(".box").hover(function(){i(this).find(".icon").addClass("animated pulse");i(this).find(".text").addClass("animated fadeInUp");i(this).find(".image").addClass("animated fadeInDown")},function(){i(this).find(".icon").removeClass("animated pulse");i(this).find(".text").removeClass("animated fadeInUp");i(this).find(".image").removeClass("animated fadeInDown")});i(".e_flash").hover(function(){i(this).addClass("animated flash")},function(){i(this).removeClass("animated flash")});i(".e_bounce").hover(function(){i(this).addClass("animated bounce")},function(){i(this).removeClass("animated bounce")});i(".e_shake").hover(function(){i(this).addClass("animated shake")},function(){i(this).removeClass("animated shake")});i(".e_tada").hover(function(){i(this).addClass("animated tada")},function(){i(this).removeClass("animated tada")});i(".e_swing").hover(function(){i(this).addClass("animated swing")},function(){i(this).removeClass("animated swing")});i(".e_wobble").hover(function(){i(this).addClass("animated wobble")},function(){i(this).removeClass("animated wobble")});i(".e_wiggle").hover(function(){i(this).addClass("animated wiggle")},function(){i(this).removeClass("animated wiggle")});i(".e_pulse").hover(function(){i(this).addClass("animated pulse")},function(){i(this).removeClass("animated pulse")});i(".e_flip").hover(function(){i(this).addClass("animated flip")},function(){i(this).removeClass("animated flip")});i(".e_flipInX").hover(function(){i(this).addClass("animated flipInX")},function(){i(this).removeClass("animated flipInX")});i(".e_flipOutX").hover(function(){i(this).addClass("animated flipOutX")},function(){i(this).removeClass("animated flipOutX")});i(".e_flipInY").hover(function(){i(this).addClass("animated flipInY")},function(){i(this).removeClass("animated flipInY")});i(".e_flipOutY").hover(function(){i(this).addClass("animated flipOutY")},function(){i(this).removeClass("animated flipOutY")});i(".e_fadeIn").hover(function(){i(this).addClass("animated fadeIn")},function(){i(this).removeClass("animated fadeIn")});i(".e_fadeInUp").hover(function(){i(this).addClass("animated fadeInUp")},function(){i(this).removeClass("animated fadeInUp")});i(".e_fadeInDown").hover(function(){i(this).addClass("animated fadeInDown")},function(){i(this).removeClass("animated fadeInDown")});i(".e_fadeInLeft").hover(function(){i(this).addClass("animated fadeInLeft")},function(){i(this).removeClass("animated fadeInLeft")});i(".e_fadeInRight").hover(function(){i(this).addClass("animated fadeInRight")},function(){i(this).removeClass("animated fadeInRight")});i(".e_fadeInUpBig").hover(function(){i(this).addClass("animated fadeInUpBig")},function(){i(this).removeClass("animated fadeInUpBig")});i(".e_fadeInUpBig").hover(function(){i(this).addClass("animated fadeInUpBig")},function(){i(this).removeClass("animated fadeInUpBig")});i(".e_fadeInDownBig").hover(function(){i(this).addClass("animated fadeInDownBig")},function(){i(this).removeClass("animated fadeInDownBig")});i(".e_fadeInLeftBig").hover(function(){i(this).addClass("animated fadeInLeftBig")},function(){i(this).removeClass("animated fadeInLeftBig")});i(".e_fadeInRightBig").hover(function(){i(this).addClass("animated fadeInRightBig")},function(){i(this).removeClass("animated fadeInRightBig")});i(".e_fadeOut").hover(function(){i(this).addClass("animated fadeOut")},function(){i(this).removeClass("animated fadeOut")});i(".e_fadeOutUp").hover(function(){i(this).addClass("animated fadeOutUp")},function(){i(this).removeClass("animated fadeOutUp")});i(".e_fadeOutDown").hover(function(){i(this).addClass("animated fadeOutDown")},function(){i(this).removeClass("animated fadeOutDown")});i(".e_fadeOutLeft").hover(function(){i(this).addClass("animated fadeOutLeft")},function(){i(this).removeClass("animated fadeOutLeft")});i(".e_fadeOutRight").hover(function(){i(this).addClass("animated fadeOutRight")},function(){i(this).removeClass("animated fadeOutRight")});i(".e_fadeOutUpBig").hover(function(){i(this).addClass("animated fadeOutUpBig")},function(){i(this).removeClass("animated fadeOutUpBig")});i(".e_fadeOutDownBig").hover(function(){i(this).addClass("animated fadeOutDownBig")},function(){i(this).removeClass("animated fadeOutDownBig")});i(".e_fadeOutLeftBig").hover(function(){i(this).addClass("animated fadeOutLeftBig")},function(){i(this).removeClass("animated fadeOutLeftBig")});i(".e_fadeOutRightBig").hover(function(){i(this).addClass("animated fadeOutRightBig")},function(){i(this).removeClass("animated fadeOutRightBig")});i(".e_bounceIn").hover(function(){i(this).addClass("animated bounceIn")},function(){i(this).removeClass("animated bounceIn")});i(".e_bounceInDown").hover(function(){i(this).addClass("animated bounceInDown")},function(){i(this).removeClass("animated bounceInDown")});i(".e_bounceInUp").hover(function(){i(this).addClass("animated bounceInUp")},function(){i(this).removeClass("animated bounceInUp")});i(".e_bounceInLeft").hover(function(){i(this).addClass("animated bounceInLeft")},function(){i(this).removeClass("animated bounceInLeft")});i(".e_bounceInRight").hover(function(){i(this).addClass("animated bounceInRight")},function(){i(this).removeClass("animated bounceInRight")});i(".e_bounceOut").hover(function(){i(this).addClass("animated bounceOut")},function(){i(this).removeClass("animated bounceOut")});i(".e_bounceOutDown").hover(function(){i(this).addClass("animated bounceOutDown")},function(){i(this).removeClass("animated bounceOutDown")});i(".e_bounceOutUp").hover(function(){i(this).addClass("animated bounceOutUp")},function(){i(this).removeClass("animated bounceOutUp")});i(".e_bounceOutLeft").hover(function(){i(this).addClass("animated bounceOutLeft")},function(){i(this).removeClass("animated bounceOutLeft")});i(".e_bounceOutRight").hover(function(){i(this).addClass("animated bounceOutRight")},function(){i(this).removeClass("animated bounceOutRight")});i(".e_rotateIn").hover(function(){i(this).addClass("animated rotateIn")},function(){i(this).removeClass("animated rotateIn")});i(".e_rotateInDownLeft").hover(function(){i(this).addClass("animated rotateInDownLeft")},function(){i(this).removeClass("animated rotateInDownLeft")});i(".e_rotateInDownRight").hover(function(){i(this).addClass("animated rotateInDownRight")},function(){i(this).removeClass("animated rotateInDownRight")});i(".e_rotateInUpRight").hover(function(){i(this).addClass("animated rotateInUpRight")},function(){i(this).removeClass("animated rotateInUpRight")});i(".e_rotateInUpLeft").hover(function(){i(this).addClass("animated rotateInUpLeft")},function(){i(this).removeClass("animated rotateInUpLeft")});i(".e_rotateOut").hover(function(){i(this).addClass("animated rotateOut")},function(){i(this).removeClass("animated rotateOut")});i(".e_rotateOutDownLeft").hover(function(){i(this).addClass("animated rotateOutDownLeft")},function(){i(this).removeClass("animated rotateOutDownLeft")});i(".e_rotateOutDownRight").hover(function(){i(this).addClass("animated rotateOutDownRight")},function(){i(this).removeClass("animated rotateOutDownRight")});i(".e_rotateOutUpLeft").hover(function(){i(this).addClass("animated rotateOutUpLeft")},function(){i(this).removeClass("animated rotateOutUpLeft")});i(".e_rotateOutUpRight").hover(function(){i(this).addClass("animated rotateOutUpRight")},function(){i(this).removeClass("animated rotateOutUpRight")});i(".e_lightSpeedIn").hover(function(){i(this).addClass("animated lightSpeedIn")},function(){i(this).removeClass("animated lightSpeedIn")});i(".e_lightSpeedOut").hover(function(){i(this).addClass("animated lightSpeedOut")},function(){i(this).removeClass("animated lightSpeedOut")});i(".e_hinge").hover(function(){i(this).addClass("animated hinge")},function(){i(this).removeClass("animated hinge")});i(".e_rollIn").hover(function(){i(this).addClass("animated rollIn")},function(){i(this).removeClass("animated rollIn")});i(".e_rollOut").hover(function(){i(this).addClass("animated rollOut")},function(){i(this).removeClass("animated rollOut")})}}(window.myStartpage=window.myStartpage||{},jQuery));jQuery(document).ready(function(a){myStartpage.init();return false});