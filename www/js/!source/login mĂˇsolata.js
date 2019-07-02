//(function(d,e,c){var i={mainSlider:document.getElementById("nivo-slider")||"",recommendationSlider:document.getElementById("mycarousel")||"",freeTrainings:document.getElementById("projects")||"",contactMap:document.getElementsByClassName("map")||"",contactForm:document.getElementById("contactform")||""};var f={init:function(){if(i.mainSlider!==""){this.setNivo()}if(i.recommendationSlider!==""){this.setBx()}},setNivo:function(){e(".nivo-slider").nivoSlider({effect:"fade",slices:15,boxCols:8,boxRows:4,animSpeed:500,pauseTime:5000,startSlide:0,directionNav:true,controlNav:false,controlNavThumbs:false,pauseOnHover:true,manualAdvance:false,prevText:"",nextText:"",randomStart:false,beforeChange:function(){},afterChange:function(){},slideshowEnd:function(){},lastSlide:function(){},afterLoad:function(){}})},setBx:function(){e(i.recommendationSlider).bxSlider({minSlides:4,maxSlides:4,slideWidth:240,slideMargin:0,pager:false,nextText:"",prevText:""})}},j={init:function(){if(i.freeTrainings!==""){this.setQuicksand()}},setQuicksand:function(){var k=e(".portfolio").clone();e(".filter li").click(function(m){e(".filter li").removeClass("active");var n=e(this).attr("class").split(" ").slice(-1)[0];if(n=="all"){var l=k.find(".item-thumbs")}else{var l=k.find(".item-thumbs[data-type="+n+"]")}e(".portfolio").quicksand(l,{duration:600,adjustHeight:"auto"},function(){});e(this).addClass("active");return false});e(".filter li:first").addClass("active")}},g={init:function(){if(i.contactForm!==""){this.setContact()}},setContact:function(){e("form.validateform").submit(function(){var m=e(this).find(".field"),l=false,k=/^[^\s()<>@,;:\/]+@\w[\w\.-]+\.[a-z]{2,}$/i;m.children("input").each(function(){var o=e(this);var p=o.attr("data-rule");if(p!=c){var s=false;var r=p.indexOf(":",0);if(r>=0){var q=p.substr(r+1,p.length);p=p.substr(0,r)}else{p=p.substr(r+1,p.length)}switch(p){case"required":if(o.val()==""){l=s=true}break;case"maxlen":if(o.val().length<parseInt(q)){l=s=true}break;case"email":if(!k.test(o.val())){l=s=true}break;case"checked":if(!o.attr("checked")){l=s=true}break;case"regexp":q=new RegExp(q);if(!q.test(o.val())){l=s=true}break}o.next(".validation").html((s?(o.attr("data-msg")!=c?o.attr("data-msg"):"wrong Input"):"")).show("blind")}});m.children("textarea").each(function(){var o=e(this);var p=o.attr("data-rule");if(p!=c){var s=false;var r=p.indexOf(":",0);if(r>=0){var q=p.substr(r+1,p.length);p=p.substr(0,r)}else{p=p.substr(r+1,p.length)}switch(p){case"required":if(o.val()==""){l=s=true}break;case"maxlen":if(o.val().length<parseInt(q)){l=s=true}break}o.next(".validation").html((s?(o.attr("data-msg")!=c?o.attr("data-msg"):"wrong Input"):"")).show("blind")}});if(l){return false}else{var n=e(this).serialize()}e.ajax({type:"POST",url:"/contact/",data:n,success:function(o){e("#sendmessage").addClass("show");e("#errormessage").ajaxComplete(function(r,q,p){if(o=="OK"){e("#sendmessage").addClass("show")}else{e("#sendmessage").removeClass("show")}e(this).html(o)})}});return false})}};d.init=function(){if(Modernizr.touch){e("body").addClass("touch-device")}a(window.location.pathname);f.init();j.init();g.init();h();e(".social-network li a, .options_box .color a").tooltip();e(window).scroll(function(){if(e(this).scrollTop()>100){e(".scrollup").fadeIn()}else{e(".scrollup").fadeOut()}});e(".scrollup").click(function(){e("html, body").animate({scrollTop:0},1000);return false})};function b(){e("#content").css("min-height",e(window).outerHeight(true)-(e("body").outerHeight(true)-e("body").height())-e("#header").outerHeight(true)-(e("#content").outerHeight(true)-e("#content").height())+(e(".page-title").length?Math.abs(parseInt(e(".page-title").css("margin-top"))):0)-e("#footer").outerHeight(true)-e("#footer-bottom").outerHeight(true))}function a(l){if(l=="/"){l="/index.html"}else{var n=new RegExp(/\b[blog|project]+\b/);var k=n.exec(l);if(k!==null){l="/"+k+".html"}}e("li").removeClass("active");e('a[href="'+l+'"]').parent().addClass("active")}function h(){e(".box").hover(function(){e(this).find(".icon").addClass("animated pulse");e(this).find(".text").addClass("animated fadeInUp");e(this).find(".image").addClass("animated fadeInDown")},function(){e(this).find(".icon").removeClass("animated pulse");e(this).find(".text").removeClass("animated fadeInUp");e(this).find(".image").removeClass("animated fadeInDown")});e(".e_flash").hover(function(){e(this).addClass("animated flash")},function(){e(this).removeClass("animated flash")});e(".e_bounce").hover(function(){e(this).addClass("animated bounce")},function(){e(this).removeClass("animated bounce")});e(".e_shake").hover(function(){e(this).addClass("animated shake")},function(){e(this).removeClass("animated shake")});e(".e_tada").hover(function(){e(this).addClass("animated tada")},function(){e(this).removeClass("animated tada")});e(".e_swing").hover(function(){e(this).addClass("animated swing")},function(){e(this).removeClass("animated swing")});e(".e_wobble").hover(function(){e(this).addClass("animated wobble")},function(){e(this).removeClass("animated wobble")});e(".e_wiggle").hover(function(){e(this).addClass("animated wiggle")},function(){e(this).removeClass("animated wiggle")});e(".e_pulse").hover(function(){e(this).addClass("animated pulse")},function(){e(this).removeClass("animated pulse")});e(".e_flip").hover(function(){e(this).addClass("animated flip")},function(){e(this).removeClass("animated flip")});e(".e_flipInX").hover(function(){e(this).addClass("animated flipInX")},function(){e(this).removeClass("animated flipInX")});e(".e_flipOutX").hover(function(){e(this).addClass("animated flipOutX")},function(){e(this).removeClass("animated flipOutX")});e(".e_flipInY").hover(function(){e(this).addClass("animated flipInY")},function(){e(this).removeClass("animated flipInY")});e(".e_flipOutY").hover(function(){e(this).addClass("animated flipOutY")},function(){e(this).removeClass("animated flipOutY")});e(".e_fadeIn").hover(function(){e(this).addClass("animated fadeIn")},function(){e(this).removeClass("animated fadeIn")});e(".e_fadeInUp").hover(function(){e(this).addClass("animated fadeInUp")},function(){e(this).removeClass("animated fadeInUp")});e(".e_fadeInDown").hover(function(){e(this).addClass("animated fadeInDown")},function(){e(this).removeClass("animated fadeInDown")});e(".e_fadeInLeft").hover(function(){e(this).addClass("animated fadeInLeft")},function(){e(this).removeClass("animated fadeInLeft")});e(".e_fadeInRight").hover(function(){e(this).addClass("animated fadeInRight")},function(){e(this).removeClass("animated fadeInRight")});e(".e_fadeInUpBig").hover(function(){e(this).addClass("animated fadeInUpBig")},function(){e(this).removeClass("animated fadeInUpBig")});e(".e_fadeInUpBig").hover(function(){e(this).addClass("animated fadeInUpBig")},function(){e(this).removeClass("animated fadeInUpBig")});e(".e_fadeInDownBig").hover(function(){e(this).addClass("animated fadeInDownBig")},function(){e(this).removeClass("animated fadeInDownBig")});e(".e_fadeInLeftBig").hover(function(){e(this).addClass("animated fadeInLeftBig")},function(){e(this).removeClass("animated fadeInLeftBig")});e(".e_fadeInRightBig").hover(function(){e(this).addClass("animated fadeInRightBig")},function(){e(this).removeClass("animated fadeInRightBig")});e(".e_fadeOut").hover(function(){e(this).addClass("animated fadeOut")},function(){e(this).removeClass("animated fadeOut")});e(".e_fadeOutUp").hover(function(){e(this).addClass("animated fadeOutUp")},function(){e(this).removeClass("animated fadeOutUp")});e(".e_fadeOutDown").hover(function(){e(this).addClass("animated fadeOutDown")},function(){e(this).removeClass("animated fadeOutDown")});e(".e_fadeOutLeft").hover(function(){e(this).addClass("animated fadeOutLeft")},function(){e(this).removeClass("animated fadeOutLeft")});e(".e_fadeOutRight").hover(function(){e(this).addClass("animated fadeOutRight")},function(){e(this).removeClass("animated fadeOutRight")});e(".e_fadeOutUpBig").hover(function(){e(this).addClass("animated fadeOutUpBig")},function(){e(this).removeClass("animated fadeOutUpBig")});e(".e_fadeOutDownBig").hover(function(){e(this).addClass("animated fadeOutDownBig")},function(){e(this).removeClass("animated fadeOutDownBig")});e(".e_fadeOutLeftBig").hover(function(){e(this).addClass("animated fadeOutLeftBig")},function(){e(this).removeClass("animated fadeOutLeftBig")});e(".e_fadeOutRightBig").hover(function(){e(this).addClass("animated fadeOutRightBig")},function(){e(this).removeClass("animated fadeOutRightBig")});e(".e_bounceIn").hover(function(){e(this).addClass("animated bounceIn")},function(){e(this).removeClass("animated bounceIn")});e(".e_bounceInDown").hover(function(){e(this).addClass("animated bounceInDown")},function(){e(this).removeClass("animated bounceInDown")});e(".e_bounceInUp").hover(function(){e(this).addClass("animated bounceInUp")},function(){e(this).removeClass("animated bounceInUp")});e(".e_bounceInLeft").hover(function(){e(this).addClass("animated bounceInLeft")},function(){e(this).removeClass("animated bounceInLeft")});e(".e_bounceInRight").hover(function(){e(this).addClass("animated bounceInRight")},function(){e(this).removeClass("animated bounceInRight")});e(".e_bounceOut").hover(function(){e(this).addClass("animated bounceOut")},function(){e(this).removeClass("animated bounceOut")});e(".e_bounceOutDown").hover(function(){e(this).addClass("animated bounceOutDown")},function(){e(this).removeClass("animated bounceOutDown")});e(".e_bounceOutUp").hover(function(){e(this).addClass("animated bounceOutUp")},function(){e(this).removeClass("animated bounceOutUp")});e(".e_bounceOutLeft").hover(function(){e(this).addClass("animated bounceOutLeft")},function(){e(this).removeClass("animated bounceOutLeft")});e(".e_bounceOutRight").hover(function(){e(this).addClass("animated bounceOutRight")},function(){e(this).removeClass("animated bounceOutRight")});e(".e_rotateIn").hover(function(){e(this).addClass("animated rotateIn")},function(){e(this).removeClass("animated rotateIn")});e(".e_rotateInDownLeft").hover(function(){e(this).addClass("animated rotateInDownLeft")},function(){e(this).removeClass("animated rotateInDownLeft")});e(".e_rotateInDownRight").hover(function(){e(this).addClass("animated rotateInDownRight")},function(){e(this).removeClass("animated rotateInDownRight")});e(".e_rotateInUpRight").hover(function(){e(this).addClass("animated rotateInUpRight")},function(){e(this).removeClass("animated rotateInUpRight")});e(".e_rotateInUpLeft").hover(function(){e(this).addClass("animated rotateInUpLeft")},function(){e(this).removeClass("animated rotateInUpLeft")});e(".e_rotateOut").hover(function(){e(this).addClass("animated rotateOut")},function(){e(this).removeClass("animated rotateOut")});e(".e_rotateOutDownLeft").hover(function(){e(this).addClass("animated rotateOutDownLeft")},function(){e(this).removeClass("animated rotateOutDownLeft")});e(".e_rotateOutDownRight").hover(function(){e(this).addClass("animated rotateOutDownRight")},function(){e(this).removeClass("animated rotateOutDownRight")});e(".e_rotateOutUpLeft").hover(function(){e(this).addClass("animated rotateOutUpLeft")},function(){e(this).removeClass("animated rotateOutUpLeft")});e(".e_rotateOutUpRight").hover(function(){e(this).addClass("animated rotateOutUpRight")},function(){e(this).removeClass("animated rotateOutUpRight")});e(".e_lightSpeedIn").hover(function(){e(this).addClass("animated lightSpeedIn")},function(){e(this).removeClass("animated lightSpeedIn")});e(".e_lightSpeedOut").hover(function(){e(this).addClass("animated lightSpeedOut")},function(){e(this).removeClass("animated lightSpeedOut")});e(".e_hinge").hover(function(){e(this).addClass("animated hinge")},function(){e(this).removeClass("animated hinge")});e(".e_rollIn").hover(function(){e(this).addClass("animated rollIn")},function(){e(this).removeClass("animated rollIn")});e(".e_rollOut").hover(function(){e(this).addClass("animated rollOut")},function(){e(this).removeClass("animated rollOut")})}}(window.myStartpage=window.myStartpage||{},jQuery));jQuery(document).ready(function(a){myStartpage.init();return false});jQuery(document).ready(function(a){(function(){var b=a(".navigation nav"),c='<option value="" selected>Go to..</option>';b.find("li").each(function(){var f=a(this),e=f.children("a"),g=f.parents("ul").length-1,d="";if(g){while(g>0){d+=" - ";g--}}a(".nav li").parent().addClass("bold");c+='<option value="'+e.attr("href")+'">'+d+" "+e.text()+"</option>"}).end().after('<select class="selectmenu">'+c+"</select>");a("select.selectmenu").on("change",function(){window.location=a(this).val()})})();a(".toggle-link").each(function(){a(this).click(function(){var c="open";var d=a(this).attr("data-target");var b=a(this).attr("data-target-state");if(typeof b!=="undefined"&&b!==false){c=b}if(c=="undefined"){c="open"}a(d).toggleClass("toggle-link-"+c);a(this).toggleClass(c)})});a(".accordion").on("show",function(b){a(b.target).prev(".accordion-heading").find(".accordion-toggle").addClass("active");a(b.target).prev(".accordion-heading").find(".accordion-toggle i").removeClass("icon-plus");a(b.target).prev(".accordion-heading").find(".accordion-toggle i").addClass("icon-minus")});a(".accordion").on("hide",function(b){a(this).find(".accordion-toggle").not(a(b.target)).removeClass("active");a(this).find(".accordion-toggle i").not(a(b.target)).removeClass("icon-minus");a(this).find(".accordion-toggle i").not(a(b.target)).addClass("icon-plus")});a("ul.nav li.dropdown").hover(function(){a(this).find(".dropdown-menu").stop(true,true).delay(200).fadeIn()},function(){a(this).find(".dropdown-menu").stop(true,true).delay(200).fadeOut()});a(".social-network li a, .options_box .color a").tooltip();a(window).scroll(function(){if(a(this).scrollTop()>100){a(".scrollup").fadeIn()}else{a(".scrollup").fadeOut()}});a(".scrollup").click(function(){a("html, body").animate({scrollTop:0},1000);return false})});
(function (myStartpage, $, undefined) {
    "use strict";

    var pageElements = {
        mainSlider : document.getElementById('nivo-slider') || '',
        recommendationSlider : document.getElementById('mycarousel') || '',
        freeTrainings : document.getElementById('projects') || '',
        contactMap : document.getElementsByClassName('map') || '',
        contactForm : document.getElementById('contactform') || ''
    };

    var values = {},
        actions = {
            form     : {0: 'order', 1: 'contact'}
        },
        settings = {
            type:"post",
            url: "",
            data: values,
            responseType: 'json'
        },
        regEx = {
            'characterReg': /^\s*[a-zA-Z0-9,\s]+\s*$/,
            'emailExp'    : /^[^\s()<>@,;:\/]+@\w[\w\.-]+\.[a-z]{2,}$/i
        };

    var indexPage = {
            init: function(){
                if(pageElements.mainSlider !== '')
                    this.setNivo();
                if(pageElements.recommendationSlider !== '')
                    this.setBx();
            },
            setNivo : function(){
                $('.nivo-slider').nivoSlider({
                    effect: 'fade', // Specify sets like: 'fold,fade,sliceDown'
                    slices: 15, // For slice animations
                    boxCols: 8, // For box animations
                    boxRows: 4, // For box animations
                    animSpeed: 500, // Slide transition speed
                    pauseTime: 5000, // How long each slide will show
                    startSlide: 0, // Set starting Slide (0 index)
                    directionNav: true, // Next & Prev navigation
                    controlNav: false, // 1,2,3... navigation
                    controlNavThumbs: false, // Use thumbnails for Control Nav
                    pauseOnHover: true, // Stop animation while hovering
                    manualAdvance: false, // Force manual transitions
                    prevText: '', // Prev directionNav text
                    nextText: '', // Next directionNav text
                    randomStart: false, // Start on a random slide
                    beforeChange: function(){}, // Triggers before a slide transition
                    afterChange: function(){}, // Triggers after a slide transition
                    slideshowEnd: function(){}, // Triggers after all slides have been shown
                    lastSlide: function(){}, // Triggers when last slide is shown
                    afterLoad: function(){} // Triggers when slider has loaded
                });
            },
            setBx : function(){
                $(pageElements.recommendationSlider).bxSlider({
                    minSlides: 4,
                    maxSlides: 4,
                    slideWidth: 240,
                    slideMargin: 0,
                    pager : false,
                    nextText : '',
                    prevText : ''
                });
            }
        },

        trainingsPage = {
            init: function(){
                if(pageElements.freeTrainings !== '')
                    this.setQuicksand();
            },

            setQuicksand : function(){
                // Clone applications to get a second collection
                var $data = $(".portfolio" ).clone();

//NOTE: Only filter on the main portfolio page, not on the subcategory pages
                $('.filter li').click(function(e) {
                    $(".filter li").removeClass("active");
// Use the last category class as the category to filter by. This means that multiple categories are not supported (yet)
                    var filterClass=$(this).attr('class').split(' ').slice(-1)[0];

                    if (filterClass == 'all') {
                        var $filteredData = $data.find('.item-thumbs');
                    } else {
                        var $filteredData = $data.find('.item-thumbs[data-type=' + filterClass + ']');
                    }
                    $(".portfolio").quicksand($filteredData, {
                        duration: 600,
                        adjustHeight: 'auto'
                    }, function () {

// Portfolio fancybox
                        /*
                         $(".fancybox").fancybox({
                         padding : 0,
                         beforeShow: function () {
                         this.title = $(this.element).attr('title');
                         this.title = '<h4>' + this.title + '</h4>' + '<p>' +
                         $(this.element).parent().find('img').attr('alt') + '</p>';
                         },
                         helpers : {
                         title : { type: 'inside' },
                         }
                         });
                         */

                    });
                    $(this).addClass("active");
                    return false;
                });

                $(".filter li:first" ).addClass("active");
            }
        },

        pricePage = {
            orderButton : document.querySelectorAll('.order') || '',

            init : function(){

                if(this.orderButton !== ''){
                    $('.order').live('click', function(){
                        $("#sendmessage" ).html('' ).removeClass('show');
                        $('#myOrder' ).find('form' ).removeClass('hidden').find("input[type=text]").val("");
                        $('#myOrder' ).find('form' ).prepend('<input type="hidden" name="type" value="'+$(this ).data('type')+'" />');
                    });

                    $('#myOrder' ).find('form' ).submit(function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if(!regEx.emailExp.test( $('#inputEmail' ).val() )) {
                            $('#inputEmail' ).closest('.control-group' ).addClass('error');
                        } else $('#inputEmail' ).closest('.control-group' ).removeClass('error');

                        if( !regEx.characterReg.test( $('#inputCompany' ).val() )) {
                            $('#inputCompany' ).closest('.control-group' ).addClass('error');
                        } else $('#inputCompany' ).closest('.control-group' ).removeClass('error');

                        if(!regEx.characterReg.test( $('#inputContact' ).val() )) {
                            $('#inputContact' ).closest('.control-group' ).addClass('error');
                        } else $('#inputContact' ).closest('.control-group' ).removeClass('error');

                        if($('#myOrder' ).find('.error' ).length)
                            return false;

                        values = {};
                        values['action'] = actions.form[0];
                        values['form'] = $( this ).serializeArray();
                        settings.url = "/contact/";
                        settings.data = values;
                        settings.success = function(msg){
                            $("#sendmessage" ).html(msg.message);
                            if(msg.type == 'success'){
                                $("#sendmessage").addClass("show");
                                $('#myOrder' ).find('form' ).addClass('hidden');
                                $('.order' ).remove();
                            }
                        };

                        $.ajax(settings);
                        return false;
                    })
                }
            }
        },

        contactPage = {
            init: function(){
                if(pageElements.contactForm !== '')
                    this.setContact();
            },

            setContact : function(){
                $('form.validateform').submit(function(){

                    var f = $(this).find('.field'),
                        ferror = false,
                        emailExp = /^[^\s()<>@,;:\/]+@\w[\w\.-]+\.[a-z]{2,}$/i;

                    f.children('input').each(function(){ // run all inputs

                        var i = $(this); // current input
                        var rule = i.attr('data-rule');

                        if( rule != undefined ){
                            var ierror=false; // error flag for current input
                            var pos = rule.indexOf( ':', 0 );
                            if( pos >= 0 ){
                                var exp = rule.substr( pos+1, rule.length );
                                rule = rule.substr(0, pos);
                            }else{
                                rule = rule.substr( pos+1, rule.length );
                            }

                            switch( rule ){
                                case 'required':
                                    if( i.val()=='' ){ ferror=ierror=true; }
                                    break;

                                case 'maxlen':
                                    if( i.val().length<parseInt(exp) ){ ferror=ierror=true; }
                                    break;

                                case 'email':
                                    if( !regEx.emailExp.test(i.val()) ){ ferror=ierror=true; }
                                    break;


                                case 'checked':
                                    if( !i.attr('checked') ){ ferror=ierror=true; }
                                    break;

                                case 'regexp':
                                    exp = new RegExp(exp);
                                    if( !exp.test(i.val()) ){ ferror=ierror=true; }
                                    break;
                            }
                            i.next('.validation').html( ( ierror ? (i.attr('data-msg') != undefined ? i.attr('data-msg') : 'wrong Input') : '' ) ).show('blind');
                        }
                    });
                    f.children('textarea').each(function(){ // run all inputs

                        var i = $(this); // current input
                        var rule = i.attr('data-rule');

                        if( rule != undefined ){
                            var ierror=false; // error flag for current input
                            var pos = rule.indexOf( ':', 0 );
                            if( pos >= 0 ){
                                var exp = rule.substr( pos+1, rule.length );
                                rule = rule.substr(0, pos);
                            }else{
                                rule = rule.substr( pos+1, rule.length );
                            }

                            switch( rule ){
                                case 'required':
                                    if( i.val()=='' ){ ferror=ierror=true; }
                                    break;

                                case 'maxlen':
                                    if( i.val().length<parseInt(exp) ){ ferror=ierror=true; }
                                    break;
                            }
                            i.next('.validation').html( ( ierror ? (i.attr('data-msg') != undefined ? i.attr('data-msg') : 'wrong Input') : '' ) ).show('blind');
                        }
                    });
                    if( ferror ) return false;
                    else var str = $(this).serializeArray();

                    values = {};
                    values['action'] = actions.form[1];
                    values['form'] = str;
                    settings.url = "/contact/";
                    settings.data = values;
                    settings.success = function(msg){
                        $("#sendmessage" ).html(msg.message);

                        if(msg.type == 'success'){
                            $("#sendmessage").addClass("show");
                            $('#contactform' ).find('.row' ).addClass('hidden');
                        }
                    }

                    $.ajax(settings);
                    /*
                     $.ajax({
                     type: "POST",
                     url: "/contact/",
                     data: str,
                     success: function(msg){
                     $("#sendmessage" ).html(msg.message);
                     $("#sendmessage").addClass("show");
                     if(msg.type == 'success'){
                     $('#contactform' ).find('.row' ).addClass('hidden');
                     }
                     }
                     });
                     */
                    return false;
                });
            }
        };

    myStartpage.init = function() {
        if( Modernizr.touch )
            $('body').addClass('touch-device');

        showMatch(window.location.pathname);

        indexPage.init();
        trainingsPage.init();
        contactPage.init();
        pricePage.init();

        setAnimation();

        $('.social-network li a, .options_box .color a').tooltip();

        //scroll to top
        $(window).scroll(function(){
            if ($(this).scrollTop() > 100) {
                $('.scrollup').fadeIn();
            } else {
                $('.scrollup').fadeOut();
            }
        });
        $('.scrollup').click(function(){
            $("html, body").animate({ scrollTop: 0 }, 1000);
            return false;
        });

    };

    function getJsonData(settings) {
        var options = jQuery.extend({
            url: '',
            responseType: 'json',
            async: true,
            data: {},
            type: 'POST',
            onOpen: function () {
            },
            onClose: function () {
            },
            onSelect: function () {
            }
        }, settings);

        var response = $.ajax({
            url: options.url,
            data: options.data,
            type: options.type,
            dataType: options.responseType,
            async: false
        }).responseText;

        if (options.responseType == 'json') {
            response = $.parseJSON(response);
            response.type ? sendMessage('alert-' + response.type, response.message) : '';
            return response.result ? response : (response.type == 'success' ? true : false);
        }

        return response;
    };

    function setMinHeight() {

        $('#content').css('min-height',
            $(window).outerHeight(true)
                - ( $('body').outerHeight(true)
                - $('body').height() )
                - $('#header').outerHeight(true)
                - ( $('#content').outerHeight(true) - $('#content').height() )
                + ( $('.page-title').length ? Math.abs( parseInt( $('.page-title').css('margin-top') ) ) : 0 )
                - $('#footer').outerHeight(true)
                - $('#footer-bottom').outerHeight(true)
        );

    };

    function showMatch(href) {
        if (href == '/') {
            href = '/index.html';
        } else {
            var re = new RegExp(/\b[blog|project]+\b/);
            var m = re.exec(href);
            if (m !== null) {
                href = '/' + m + '.html';
            }
        };
        $('li').removeClass('active');
        $('a[href="'+href+'"]').parent().addClass('active');

    };

    function setAnimation(){
        $(".box").hover(
            function () {
                $(this).find('.icon').addClass("animated pulse");
                $(this).find('.text').addClass("animated fadeInUp");
                $(this).find('.image').addClass("animated fadeInDown");
            },
            function () {
                $(this).find('.icon').removeClass("animated pulse");
                $(this).find('.text').removeClass("animated fadeInUp");
                $(this).find('.image').removeClass("animated fadeInDown");
            }
        );
        //animate effect
        $(".e_flash").hover(
            function () {
                $(this).addClass("animated flash");
            },
            function () {
                $(this).removeClass("animated flash");
            }
        );
        $(".e_bounce").hover(
            function () {
                $(this).addClass("animated bounce");
            },
            function () {
                $(this).removeClass("animated bounce");
            }
        );

        $(".e_shake").hover(
            function () {
                $(this).addClass("animated shake");
            },
            function () {
                $(this).removeClass("animated shake");
            }
        );
        $(".e_tada").hover(
            function () {
                $(this).addClass("animated tada");
            },
            function () {
                $(this).removeClass("animated tada");
            }
        );
        $(".e_swing").hover(
            function () {
                $(this).addClass("animated swing");
            },
            function () {
                $(this).removeClass("animated swing");
            }
        );
        $(".e_wobble").hover(
            function () {
                $(this).addClass("animated wobble");
            },
            function () {
                $(this).removeClass("animated wobble");
            }
        );
        $(".e_wiggle").hover(
            function () {
                $(this).addClass("animated wiggle");
            },
            function () {
                $(this).removeClass("animated wiggle");
            }
        );
        $(".e_pulse").hover(
            function () {
                $(this).addClass("animated pulse");
            },
            function () {
                $(this).removeClass("animated pulse");
            }
        );


        $(".e_flip").hover(
            function () {
                $(this).addClass("animated flip");
            },
            function () {
                $(this).removeClass("animated flip");
            }
        );
        $(".e_flipInX").hover(
            function () {
                $(this).addClass("animated flipInX");
            },
            function () {
                $(this).removeClass("animated flipInX");
            }
        );
        $(".e_flipOutX").hover(
            function () {
                $(this).addClass("animated flipOutX");
            },
            function () {
                $(this).removeClass("animated flipOutX");
            }
        );
        $(".e_flipInY").hover(
            function () {
                $(this).addClass("animated flipInY");
            },
            function () {
                $(this).removeClass("animated flipInY");
            }
        );
        $(".e_flipOutY").hover(
            function () {
                $(this).addClass("animated flipOutY");
            },
            function () {
                $(this).removeClass("animated flipOutY");
            }
        );

        //Fading entrances
        $(".e_fadeIn").hover(
            function () {
                $(this).addClass("animated fadeIn");
            },
            function () {
                $(this).removeClass("animated fadeIn");
            }
        );
        $(".e_fadeInUp").hover(
            function () {
                $(this).addClass("animated fadeInUp");
            },
            function () {
                $(this).removeClass("animated fadeInUp");
            }
        );
        $(".e_fadeInDown").hover(
            function () {
                $(this).addClass("animated fadeInDown");
            },
            function () {
                $(this).removeClass("animated fadeInDown");
            }
        );
        $(".e_fadeInLeft").hover(
            function () {
                $(this).addClass("animated fadeInLeft");
            },
            function () {
                $(this).removeClass("animated fadeInLeft");
            }
        );
        $(".e_fadeInRight").hover(
            function () {
                $(this).addClass("animated fadeInRight");
            },
            function () {
                $(this).removeClass("animated fadeInRight");
            }
        );
        $(".e_fadeInUpBig").hover(
            function () {
                $(this).addClass("animated fadeInUpBig");
            },
            function () {
                $(this).removeClass("animated fadeInUpBig");
            }
        );
        $(".e_fadeInUpBig").hover(
            function () {
                $(this).addClass("animated fadeInUpBig");
            },
            function () {
                $(this).removeClass("animated fadeInUpBig");
            }
        );
        $(".e_fadeInDownBig").hover(
            function () {
                $(this).addClass("animated fadeInDownBig");
            },
            function () {
                $(this).removeClass("animated fadeInDownBig");
            }
        );
        $(".e_fadeInLeftBig").hover(
            function () {
                $(this).addClass("animated fadeInLeftBig");
            },
            function () {
                $(this).removeClass("animated fadeInLeftBig");
            }
        );
        $(".e_fadeInRightBig").hover(
            function () {
                $(this).addClass("animated fadeInRightBig");
            },
            function () {
                $(this).removeClass("animated fadeInRightBig");
            }
        );


        //Fading exits
        $(".e_fadeOut").hover(
            function () {
                $(this).addClass("animated fadeOut");
            },
            function () {
                $(this).removeClass("animated fadeOut");
            }
        );
        $(".e_fadeOutUp").hover(
            function () {
                $(this).addClass("animated fadeOutUp");
            },
            function () {
                $(this).removeClass("animated fadeOutUp");
            }
        );
        $(".e_fadeOutDown").hover(
            function () {
                $(this).addClass("animated fadeOutDown");
            },
            function () {
                $(this).removeClass("animated fadeOutDown");
            }
        );
        $(".e_fadeOutLeft").hover(
            function () {
                $(this).addClass("animated fadeOutLeft");
            },
            function () {
                $(this).removeClass("animated fadeOutLeft");
            }
        );
        $(".e_fadeOutRight").hover(
            function () {
                $(this).addClass("animated fadeOutRight");
            },
            function () {
                $(this).removeClass("animated fadeOutRight");
            }
        );
        $(".e_fadeOutUpBig").hover(
            function () {
                $(this).addClass("animated fadeOutUpBig");
            },
            function () {
                $(this).removeClass("animated fadeOutUpBig");
            }
        );
        $(".e_fadeOutDownBig").hover(
            function () {
                $(this).addClass("animated fadeOutDownBig");
            },
            function () {
                $(this).removeClass("animated fadeOutDownBig");
            }
        );
        $(".e_fadeOutLeftBig").hover(
            function () {
                $(this).addClass("animated fadeOutLeftBig");
            },
            function () {
                $(this).removeClass("animated fadeOutLeftBig");
            }
        );
        $(".e_fadeOutRightBig").hover(
            function () {
                $(this).addClass("animated fadeOutRightBig");
            },
            function () {
                $(this).removeClass("animated fadeOutRightBig");
            }
        );


        //Bouncing entrances
        $(".e_bounceIn").hover(
            function () {
                $(this).addClass("animated bounceIn");
            },
            function () {
                $(this).removeClass("animated bounceIn");
            }
        );
        $(".e_bounceInDown").hover(
            function () {
                $(this).addClass("animated bounceInDown");
            },
            function () {
                $(this).removeClass("animated bounceInDown");
            }
        );
        $(".e_bounceInUp").hover(
            function () {
                $(this).addClass("animated bounceInUp");
            },
            function () {
                $(this).removeClass("animated bounceInUp");
            }
        );
        $(".e_bounceInLeft").hover(
            function () {
                $(this).addClass("animated bounceInLeft");
            },
            function () {
                $(this).removeClass("animated bounceInLeft");
            }
        );
        $(".e_bounceInRight").hover(
            function () {
                $(this).addClass("animated bounceInRight");
            },
            function () {
                $(this).removeClass("animated bounceInRight");
            }
        );


        //Bouncing exits
        $(".e_bounceOut").hover(
            function () {
                $(this).addClass("animated bounceOut");
            },
            function () {
                $(this).removeClass("animated bounceOut");
            }
        );
        $(".e_bounceOutDown").hover(
            function () {
                $(this).addClass("animated bounceOutDown");
            },
            function () {
                $(this).removeClass("animated bounceOutDown");
            }
        );
        $(".e_bounceOutUp").hover(
            function () {
                $(this).addClass("animated bounceOutUp");
            },
            function () {
                $(this).removeClass("animated bounceOutUp");
            }
        );
        $(".e_bounceOutLeft").hover(
            function () {
                $(this).addClass("animated bounceOutLeft");
            },
            function () {
                $(this).removeClass("animated bounceOutLeft");
            }
        );
        $(".e_bounceOutRight").hover(
            function () {
                $(this).addClass("animated bounceOutRight");
            },
            function () {
                $(this).removeClass("animated bounceOutRight");
            }
        );


        //Rotating entrances
        $(".e_rotateIn").hover(
            function () {
                $(this).addClass("animated rotateIn");
            },
            function () {
                $(this).removeClass("animated rotateIn");
            }
        );
        $(".e_rotateInDownLeft").hover(
            function () {
                $(this).addClass("animated rotateInDownLeft");
            },
            function () {
                $(this).removeClass("animated rotateInDownLeft");
            }
        );
        $(".e_rotateInDownRight").hover(
            function () {
                $(this).addClass("animated rotateInDownRight");
            },
            function () {
                $(this).removeClass("animated rotateInDownRight");
            }
        );
        $(".e_rotateInUpRight").hover(
            function () {
                $(this).addClass("animated rotateInUpRight");
            },
            function () {
                $(this).removeClass("animated rotateInUpRight");
            }
        );
        $(".e_rotateInUpLeft").hover(
            function () {
                $(this).addClass("animated rotateInUpLeft");
            },
            function () {
                $(this).removeClass("animated rotateInUpLeft");
            }
        );


        //Rotating exits
        $(".e_rotateOut").hover(
            function () {
                $(this).addClass("animated rotateOut");
            },
            function () {
                $(this).removeClass("animated rotateOut");
            }
        );
        $(".e_rotateOutDownLeft").hover(
            function () {
                $(this).addClass("animated rotateOutDownLeft");
            },
            function () {
                $(this).removeClass("animated rotateOutDownLeft");
            }
        );
        $(".e_rotateOutDownRight").hover(
            function () {
                $(this).addClass("animated rotateOutDownRight");
            },
            function () {
                $(this).removeClass("animated rotateOutDownRight");
            }
        );
        $(".e_rotateOutUpLeft").hover(
            function () {
                $(this).addClass("animated rotateOutUpLeft");
            },
            function () {
                $(this).removeClass("animated rotateOutUpLeft");
            }
        );
        $(".e_rotateOutUpRight").hover(
            function () {
                $(this).addClass("animated rotateOutUpRight");
            },
            function () {
                $(this).removeClass("animated rotateOutUpRight");
            }
        );


        //Lightspeed
        $(".e_lightSpeedIn").hover(
            function () {
                $(this).addClass("animated lightSpeedIn");
            },
            function () {
                $(this).removeClass("animated lightSpeedIn");
            }
        );
        $(".e_lightSpeedOut").hover(
            function () {
                $(this).addClass("animated lightSpeedOut");
            },
            function () {
                $(this).removeClass("animated lightSpeedOut");
            }
        );

        //specials
        $(".e_hinge").hover(
            function () {
                $(this).addClass("animated hinge");
            },
            function () {
                $(this).removeClass("animated hinge");
            }
        );
        $(".e_rollIn").hover(
            function () {
                $(this).addClass("animated rollIn");
            },
            function () {
                $(this).removeClass("animated rollIn");
            }
        );
        $(".e_rollOut").hover(
            function () {
                $(this).addClass("animated rollOut");
            },
            function () {
                $(this).removeClass("animated rollOut");
            }
        );
    }

}(window.myStartpage = window.myStartpage || {}, jQuery));

jQuery(document).ready(function($) {
    myStartpage.init();
    return false;
});

/*global jQuery:false
 jQuery(document).ready(function($) {
 "use strict";

 (function() {

 var $menu = $('.navigation nav'),
 optionsList = '<option value="" selected>Go to..</option>';

 $menu.find('li').each(function() {
 var $this   = $(this),
 $anchor = $this.children('a'),
 depth   = $this.parents('ul').length - 1,
 indent  = '';

 if( depth ) {
 while( depth > 0 ) {
 indent += ' - ';
 depth--;
 }

 }
 $(".nav li").parent().addClass("bold");

 optionsList += '<option value="' + $anchor.attr('href') + '">' + indent + ' ' + $anchor.text() + '</option>';
 }).end()
 .after('<select class="selectmenu">' + optionsList + '</select>');

 $('select.selectmenu').on('change', function() {
 window.location = $(this).val();
 });

 })();


 $('.toggle-link').each(function() {
 $(this).click(function() {
 var state = 'open'; //assume target is closed & needs opening
 var target = $(this).attr('data-target');
 var targetState = $(this).attr('data-target-state');

 //allows trigger link to say target is open & should be closed
 if (typeof targetState !== 'undefined' && targetState !== false) {
 state = targetState;
 }

 if (state == 'undefined') {
 state = 'open';
 }

 $(target).toggleClass('toggle-link-'+ state);
 $(this).toggleClass(state);
 });
 });

 $('.accordion').on('show', function (e) {

 $(e.target).prev('.accordion-heading').find('.accordion-toggle').addClass('active');
 $(e.target).prev('.accordion-heading').find('.accordion-toggle i').removeClass('icon-plus');
 $(e.target).prev('.accordion-heading').find('.accordion-toggle i').addClass('icon-minus');
 });

 $('.accordion').on('hide', function (e) {
 $(this).find('.accordion-toggle').not($(e.target)).removeClass('active');
 $(this).find('.accordion-toggle i').not($(e.target)).removeClass('icon-minus');
 $(this).find('.accordion-toggle i').not($(e.target)).addClass('icon-plus');
 });*/
/*
 // fancybox
 $(".fancybox").fancybox({
 padding : 0,
 autoResize: true,
 beforeShow: function () {
 this.title = $(this.element).attr('title');
 this.title = '<h4>' + this.title + '</h4>' + '<p>' + $(this.element).parent().find('img').attr('alt') + '</p>';
 },
 helpers : {
 title : { type: 'inside' }
 }
 });

 $('.fancybox-media').fancybox({
 openEffect  : 'none',
 closeEffect : 'none',
 helpers : {
 media : {}
 }
 });
 */

/*
 //Navi hover
 $('ul.nav li.dropdown').hover(function () {
 $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn();
 }, function () {
 $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut();
 });

 // tooltip
 $('.social-network li a, .options_box .color a').tooltip();

 //scroll to top
 $(window).scroll(function(){
 if ($(this).scrollTop() > 100) {
 $('.scrollup').fadeIn();
 } else {
 $('.scrollup').fadeOut();
 }
 });
 $('.scrollup').click(function(){
 $("html, body").animate({ scrollTop: 0 }, 1000);
 return false;
 });


 });
 */

