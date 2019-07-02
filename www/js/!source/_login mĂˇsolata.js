// If JavaScript is enabled remove 'no-js' class and give 'js' class
jQuery('html').removeClass('no-js').addClass('js');
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
    $('li').removeClass('current');
    $('a[href="'+href+'"]').parent().addClass('current');
}
// When DOM is fully loaded
jQuery(document).ready(function($) {

    showMatch(window.location.pathname);
    $('body').on('click', '.dropdown-menu li', function(e) {
        e.preventDefault();
        alert('hey, it works!');
    });


    //for dropdown w/o bootstrap
    var _thisState = true;
    var toggle = $('[data-toggle="dropdown"]').live('click', function () {
        _thisState = $(this).parent().hasClass('open');
//console.log('index toggle: '+ _thisState );
        $(toggle).each(function () {
            $(this).parent().removeClass('open');
        })
        _thisState ? $(this).parent().removeClass('open') : $(this).parent().toggleClass('open');
        _thisState = _thisState == false ? true : false;
//console.log('index toggle: '+ _thisState );
    });

    $("body:not(dropdown)").on('click', function () {
        $('.dropdown input, .dropdown label').click(function (e) {
            e.stopPropagation();
        });
//console.log( 'click on body: ' + $(this).attr('class') );
        $('[data-toggle="dropdown"]').each(function () {
            $(this).parent().removeClass('open');
        });
    });
    /* ---------------------------------------------------------------------- */
    /*    Custom Functions
    /* ---------------------------------------------------------------------- */

    // Slide effects for #portfolio-items-filter
    $.fn.slideHorzShow = function( speed, easing, callback ) { this.animate( { marginLeft : 'show', marginRight : 'show', paddingLeft : 'show', paddingRight : 'show', width : 'show' }, speed, easing, callback ); };
    $.fn.slideHorzHide = function( speed, easing, callback ) { this.animate( { marginLeft : 'hide', marginRight : 'hide', paddingLeft : 'hide', paddingRight : 'hide', width : 'hide' }, speed, easing, callback ); };


    /* end Custom Functions */

    /* ---------------------------------------------------------------------- */
    /*    Detect touch device
    /* ---------------------------------------------------------------------- */

    (function() {

        if( Modernizr.touch )
            $('body').addClass('touch-device');

    })();

    /* end Detect touch device */

    /* ---------------------------------------------------------------------- */
    /*    Main Navigation
    /* ---------------------------------------------------------------------- */
    
    (function() {

        var $mainNav    = $('#main-nav').children('ul'),
            optionsList = '<option value="" selected>Navigate...</option>';
        
        // Regular nav
        $mainNav.on('mouseenter', 'li', function() {
            var $this    = $(this),
                $subMenu = $this.children('ul');
            if( $subMenu.length ) $this.addClass('hover');
            $subMenu.hide().stop(true, true).fadeIn(200);
        }).on('mouseleave', 'li', function() {
            $(this).removeClass('hover').children('ul').stop(true, true).fadeOut(50);
        });

        // Responsive nav
        $mainNav.find('li').each(function() {
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

            optionsList += '<option value="' + $anchor.attr('href') + '">' + indent + ' ' + $anchor.text() + '</option>';
        }).end()
          .after('<select class="responsive-nav">' + optionsList + '</select>');

        $('.responsive-nav').on('change', function() {
            window.location = $(this).val();
        });
        
    })();

    /* end Main Navigation */

    /* ---------------------------------------------------------------------- */
    /*    Min-height
    /* ---------------------------------------------------------------------- */

    (function() {

        // Set minimum height so footer will stay at the bottom of the window, even if there isn't enough content
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
        
        }

        // Init
        setMinHeight();

        // Window resize
        $(window).on('resize', function() {

            var timer = window.setTimeout( function() {
                window.clearTimeout( timer );
                setMinHeight();
            }, 30 );

        });

    })();

    /* end Min-height */

    /* ---------------------------------------------------------------------- */
    /*    Fancybox
    /* ---------------------------------------------------------------------- */
/*
    (function() {

        var $fancyboxItems = $('.single-image, .image-gallery, .iframe');

        // Images
        $('.single-image, .image-gallery').fancybox({
            type        : 'image',
            openEffect  : 'fade',
            closeEffect    : 'fade',
            nextEffect  : 'fade',
            prevEffect  : 'fade',
            helpers     : {
                title   : {
                    type : 'inside'
                },
                buttons  : {},
                media    : {}
            },
            afterLoad   : function() {
                this.title = this.group.length > 1 ? 'Image ' + ( this.index + 1 ) + ' of ' + this.group.length + ( this.title ? ' - ' + this.title : '' ) : this.title;
            }
        });

        // Iframe
        $('.iframe').fancybox({
            type        : 'iframe',
            openEffect  : 'fade',
            closeEffect    : 'fade',
            nextEffect  : 'fade',
            prevEffect  : 'fade',
            helpers     : {
                title   : {
                    type : 'inside'
                },
                buttons  : {},
                media    : {}
            },
            width       : '70%',
            height      : '70%',
            maxWidth    : 800,
            maxHeight   : 600,
            fitToView   : false,
            autoSize    : false,
            closeClick  : false
        });

        // Insert zoom icons, once page is fully loaded
        $(window).load(function() {

            $fancyboxItems.each(function() {

                var $this = $(this);

                if( !$this.hasClass('none') && !$this.children('.entry-image').length && !$this.parents('.image-gallery-slider').length )
                    $this.css({
                        'height' : $this.children().height() !== 0 ? $this.children().height() : 'auto',
                        'width'  : $this.children().width()  !== 0 ? $this.children().width()  : 'auto'
                    });

                $this.append('<span class="zoom">&nbsp;</span>');

            });

        });
        
    })();
*/
    /* end Fancybox */

    /* ---------------------------------------------------------------------- */
    /*	Projects Carousel & Post Carousel
     /* ---------------------------------------------------------------------- */

    (function() {

        var $carousel = $('.projects-carousel, .post-carousel');

        if( $carousel.length ) {

            var scrollCount;

            function getWindowWidth() {

                var windowWidth = $(window).width();

                if( windowWidth < 480 ) {
                    scrollCount = 1;
                } else if( windowWidth < 768 ) {
                    scrollCount = 2;
                } else if( windowWidth < 960 ) {
                    scrollCount = 3;
                } else {
                    scrollCount = 4;
                }

            }

            function initCarousel( $carousels ) {

                $carousels.each(function() {

                    var $this       = $(this),
                        windowWidth = $(window).width();

                    $this.jcarousel({
                        animation           : 600,
                        easing              : 'easeOutCubic',
                        scroll              : ( $this.attr('data-scroll_count') ? ( windowWidth < 960 ? 1 : parseInt( $this.attr('data-scroll_count') ) ) : scrollCount ),
                        itemVisibleInCallback : function() {
                            onBeforeAnimation : resetPosition( $this );
                            onAfterAnimation  : resetPosition( $this );
                        },
                        auto                : ( $this.attr('data-auto') ? parseInt( $this.attr('data-auto') ) : 0 ),
                        wrap                : ( $this.attr('data-auto') ? 'both' : null )
                    });

                });

            }

            function adjustCarousel() {

                $carousel.each(function() {

                    var $this    = $(this),
                        $lis     = $this.children('li')
                    newWidth = $lis.length * $lis.first().outerWidth( true ) + 100;

                    getWindowWidth();

                    // Resize only if width has changed
                    if( $this.width() !== newWidth ) {

                        $this.css('width', newWidth )
                            .data('resize','true');

                        initCarousel( $this );

                        $this.jcarousel('scroll', 1);

                        var timer = window.setTimeout( function() {
                            window.clearTimeout( timer );
                            $this.data('resize', null);
                        }, 600 );

                    }

                });

            }

            function resetPosition( elem, resizeEvent ) {
                if( elem.data('resize') )
                    elem.css('left', '0');
            }

            getWindowWidth();

            initCarousel( $carousel );

            // Detect swipe gestures support
            if( Modernizr.touch ) {

                function swipeFunc( e, dir ) {

                    var $carousel = $(e.currentTarget);

                    if( dir === 'left' ) {
                        $carousel.parent('.jcarousel-clip').siblings('.jcarousel-next').trigger('click');
                    }

                    if( dir === 'right' ) {
                        $carousel.parent('.jcarousel-clip').siblings('.jcarousel-prev').trigger('click');
                    }

                }

                $carousel.swipe({
                    swipeLeft       : swipeFunc,
                    swipeRight      : swipeFunc,
                    allowPageScroll : 'auto'
                });

            }

            // Window resize
            $(window).on('resize', function() {

                var timer = window.setTimeout( function() {
                    window.clearTimeout( timer );
                    adjustCarousel();
                }, 30 );

            });

        }

    })();

    /* ---------------------------------------------------------------------- */
    /*    Features Slider
    /* ---------------------------------------------------------------------- */

    (function() {

        var $slider = $('#slider1');

        if( $slider.length ) {
/*
            $slider.responsiveSlides({
                auto: false,
                pagination: true,
                nav: true,
                fade: 500,
                maxwidth: 1000,
                manualControls: '#slider3-pager'
            });
*/
            $('#slider1').flexslider({
                animation: "slide",
                controlNav: false,
                animationLoop: false,
                slideshow: false,
                //slideshowSpeed: 7000,
                //animationSpeed: 600,
                itemWidth: 290,
                itemMargin: 10,
                asNavFor: '#slider'
            });

            $('#slider').flexslider({
                animation: "slide",
                controlNav: false,
                animationLoop: false,
                //slideshowSpeed: 7000,
                //animationSpeed: 600,
                slideshow: false,

                sync: "#slider1"
            });
        }


    })();

    /* end Features Slider */

    /* end Image Gallery Slider */

    /* ---------------------------------------------------------------------- */
    /*    Portfolio Filter
    /* ---------------------------------------------------------------------- */

    (function() {

        var $container = $('#portfolio-items');

        if( $container.length ) {

            var $itemsFilter = $('#portfolio-items-filter'),
                mouseOver;

            // Copy categories to item classes
            $('article', $container).each(function(i) {
                var $this = $(this);
                $this.addClass( $this.attr('data-categories') );
            });

            // Run Isotope when all images are fully loaded
            $(window).on('load', function() {

                $container.isotope({
                    itemSelector : 'article',
                    layoutMode   : 'fitRows'
                });

            });

            // Filter projects
            $itemsFilter.on('click', 'a', function(e) {
                var $this         = $(this),
                    currentOption = $this.attr('data-categories');

                $itemsFilter.find('a').removeClass('active');
                $this.addClass('active');

                if( currentOption ) {
                    if( currentOption !== '*' ) currentOption = currentOption.replace(currentOption, '.' + currentOption)
                    $container.isotope({ filter : currentOption });
                }

                e.preventDefault();
            });

            $itemsFilter.find('a').first().addClass('active');
            $itemsFilter.find('a').not('.active').hide();

            // On mouseover (hover)
            $itemsFilter.on('mouseenter', function() {
                var $this = $(this);

                clearTimeout( mouseOver );

                // Wait 100ms before animating to prevent unnecessary flickering
                mouseOver = setTimeout( function() {
                    if( $(window).width() >= 960 )
                        $this.find('li a').stop(true, true).slideHorzShow(300);
                }, 100);
            }).on('mouseleave', function() {
                clearTimeout( mouseOver );

                if( $(window).width() >= 960 )
                    $(this).find('li a').not('.active').stop(true, true).slideHorzHide(150);
            });

        }

    })();

    /* end Portfolio Filter */


    /* ---------------------------------------------------------------------- */
    /*    VideoJS
    /* ---------------------------------------------------------------------- */
/*
    (function() {

        var $player = $('.video-js');

        if( $player.length ) {

            function adjustPlayer() {
            
                $player.each(function( i ) {

                    var $this        = $(this)
                        playerWidth  = $this.parent().width(),
                        playerHeight = playerWidth / ( $this.children('.vjs-tech').data('aspect-ratio') || 1.78 );

                    if( playerWidth <= 300 ) {
                        $this.addClass('vjs-player-width-300');
                    } else {
                        $this.removeClass('vjs-player-width-300');
                    }

                    if( playerWidth <= 250 ) {
                        $this.addClass('vjs-player-width-250');
                    } else {
                        $this.removeClass('vjs-player-width-250');
                    }

                    $this.css({
                        'height' : playerHeight,
                        'width'  : playerWidth
                    })
                    .attr('height', playerHeight )
                    .attr('width', playerWidth );

                });

            }

            adjustPlayer();

            $(window).on('resize', function() {

                var timer = window.setTimeout( function() {
                    window.clearTimeout( timer );
                    adjustPlayer();
                }, 30 );

            });

        }

    })();
*/
    /* end VideoJS */

    /* ---------------------------------------------------------------------- */
    /*    FitVids
    /* ---------------------------------------------------------------------- */
/*
    (function() {

        function adjustVideos() {

            var $videos = $('.fluid-width-video-wrapper');

            $videos.each(function() {

                var $this        = $(this)
                    playerWidth  = $this.parent().width(),
                    playerHeight = playerWidth / $this.data('aspectRatio');

                $this.css({
                    'height' : playerHeight,
                    'width'  : playerWidth
                });

            });

        }

        $('.container').each(function(){

            var selectors  = [
                "iframe[src^='http://player.vimeo.com']",
                "iframe[src^='http://www.youtube.com']",
                "iframe[src^='http://blip.tv']",
                "iframe[src^='http://www.kickstarter.com']", 
                "object",
                "embed"
            ],
                $allVideos = $(this).find(selectors.join(','));

            $allVideos.each(function(){

                var $this = $(this);

                if ( $this.hasClass('vjs-tech') || this.tagName.toLowerCase() == 'embed' && $this.parent('object').length || $this.parent('.fluid-width-video-wrapper').length )
                    return;

                var videoHeight = $this.attr('height') || $this.height(),
                    videoWidth  = $this.attr('width') || $this.width();

                $this.css({
                    'height' : '100%',
                    'width'  : '100%'
                })
                .removeAttr('height').removeAttr('width')
                .wrap('<div class="fluid-width-video-wrapper"></div>').parent('.fluid-width-video-wrapper').css({
                    'height' : videoHeight,
                    'width'  : videoWidth
                })
                .data( 'aspectRatio', videoWidth / videoHeight )
                .addClass( $(this).attr('class') );

                adjustVideos();

            });

        });

        $(window).on('resize', function() {

            var timer = window.setTimeout( function() {
                window.clearTimeout( timer );
                adjustVideos();
            }, 30 );

        });

    })();
*/
    /* end FitVids */

    /* ---------------------------------------------------------------------- */
    /*    AudioPlayerV1
    /* ---------------------------------------------------------------------- */
/*
    (function() {

        var $player = $('.APV1_wrapper');

        if( $player.length ) {

            $player.each(function( i ) {

                var $this = $(this);

                $this.prev('audio').hide().end()
                     .wrap('<div class="entry-audio" />');

            });

            function adjustPlayer( resize ){
            
                $player.each(function( i ) {

                    var $this            = $(this),
                        $lis             = $this.children('li'),
                        $progressBar     = $this.children('li.APV1_container'),
                        playerWidth      = $this.parent().width(),
                        lisWidth         = 0;

                    if( !resize )
                        $this.prev('audio').hide()

                    if( playerWidth <= 300 ) {
                        $this.addClass('APV1_player_width_300');
                    } else {
                        $this.removeClass('APV1_player_width_300');
                    }

                    if( playerWidth <= 250 ) {
                        $this.addClass('APV1_player_width_250');
                    } else {
                        $this.removeClass('APV1_player_width_250');
                    }

                    if( playerWidth <= 200 ) {
                        $this.addClass('APV1_player_width_200');
                    } else {
                        $this.removeClass('APV1_player_width_200');
                    }

                    $lis.each(function() {

                        var $li = $(this);
                        lisWidth += $li.width()

                    });

                    $this.width( $this.parent().width() );
                    $progressBar.width( playerWidth - ( lisWidth - $progressBar.width() ) );
                    
                });

            }

            adjustPlayer();

            $(window).on('resize', function() {

                var timer = window.setTimeout( function() {
                    window.clearTimeout( timer );
                    adjustPlayer( resize = true );
                }, 30 );

            });

        }

    })();
*/

    /* ---------------------------------------------------------------------- */
    /*    Contact Form
    /* ---------------------------------------------------------------------- */

    (function() {

        // Setup any needed variables.
        var $form   = $('.contact-form'),
            $loader = '<img src="img/loader.gif" height="11" width="16" alt="Loading..." />';

        $form.append('<div id="response" class="hidden">');
        var $response = $('#response');
        
        // Do what we need to when form is submitted.
        $form.on('click', 'input[type=submit]', function(e){

            // Hide any previous response text and show loader
            $response.hide().html( $loader ).show();
            
            // Make AJAX request 
            $.post('php/contact-send.php', $form.serialize(), function( data ) {
            
                // Show response message
                $response.html( data );

                // Scroll to bottom of the form to show respond message
                var bottomPosition = $form.offset().top + $form.outerHeight() - $(window).height();
                
                if( $(document).scrollTop() < bottomPosition )
                    $('html, body').animate({ scrollTop : bottomPosition });
                
                // If form has been sent succesfully, clear it
                if( data.indexOf('success') !== -1 )
                    $form.find('input:not(input[type="submit"]), textarea, select').val('').attr( 'checked', false );
                
            });
            
            // Cancel default action
            e.preventDefault();
        });

    })();

    /* end Contact Form */
    
    /* ---------------------------------------------------- */
    /*    UItoTop (Back to Top)
    /* ---------------------------------------------------- */

    (function() {

        var settings = {
                button      : '#back-to-top',
                text        : 'Back to Top',
                min         : 200,
                fadeIn      : 400,
                fadeOut     : 400,
                scrollSpeed : 800,
                easingType  : 'easeInOutExpo'
            },
            oldiOS     = false,
            oldAndroid = false;

        // Detect if older iOS device, which doesn't support fixed position
        if( /(iPhone|iPod|iPad)\sOS\s[0-4][_\d]+/i.test(navigator.userAgent) )
            oldiOS = true;

        // Detect if older Android device, which doesn't support fixed position
        if( /Android\s+([0-2][\.\d]+)/i.test(navigator.userAgent) )
            oldAndroid = true;
    
        $('body').append('<a href="#" id="' + settings.button.substring(1) + '" title="' + settings.text + '">' + settings.text + '</a>');

        $( settings.button ).click(function( e ){
                $('html, body').animate({ scrollTop : 0 }, settings.scrollSpeed, settings.easingType );

                e.preventDefault();
            });

        $(window).scroll(function() {
            var position = $(window).scrollTop();

            if( oldiOS || oldAndroid ) {
                $( settings.button ).css({
                    'position' : 'absolute',
                    'top'      : position + $(window).height()
                });
            }

            if ( position > settings.min ) 
                $( settings.button ).fadeIn( settings.fadeIn );
            else 
                $( settings.button ).fadeOut( settings.fadeOut );
        });

    })();

    /* end UItoTop (Back to Top) */

});