$( function () {
    $( '.circle' ).live( 'click', function () {
        console.log( 'click' );
        $( '#upperDiv' ).animate( {'margin-top': '-=540px'}, 1000, function () {
            $( this ).remove()
        } );
        var secondClass = $( this ).attr( 'class' ).replace( 'circle ', '' );
        $( 'input[rel="' + secondClass + '"]' ).attr( 'checked', 'checked' );
        //$('.assessment, .thumbnail, .preselect').toggleClass('in');

        $( '.circle' ).unbind( 'click' );
    } );

    var $form = $( '#assessmentForm' ),
        threshold = 70,
        $captcha = $( '#slider-captcha' ),
        $captcaText = $( '.slider-text' );
    $form.find( '#submit' ).hide();
    $captcha.slider( {
        animate: 'slow',
        stop   : function ( evt, ui ) {
            if ( ui.value > threshold ) {
                $form.find( '#submit' ).show();
                $captcha.hide();
                $captcaText.hide();
            } else {
                $captcha.slider( "value", 0 );
            }
        }
    } );
    $( 'a.ui-slider-handle' ).html( '<i class="icon-chevron-right"></i>' );
    $( 'span.error' ).hide();
    $( '.errorcontainer' ).show();
    $( "#submit" ).bind( 'click', function ( e ) {

        e.stopPropagation();
        e.preventDefault();
        // validate and process form
        // first hide any error messages
        $( '.error' ).hide();

        var name = $( 'input[name="name"]' ).val();
        if ( name == "" ) {
            $( "span#name_error" ).show();
            $( 'input[name="name"]' ).focus();
            return false;
        }

        var firm = $( 'input[name="firm"]' ).val();
        if ( firm == "" ) {
            $( "span#firm_error" ).show();
            $( 'input[name="firm"]' ).focus();
            return false;
        }

        var phone = $( 'input[name="phone"]' ).val();
        if ( phone == "" ) {
            $( "span#phone_error" ).show();
            $( 'input[name="phone"]' ).focus();
            return false;
        }
        var email = $( 'input[name="email"]' ).val();
        if ( email == "" ) {
            $( "span#email_error" ).show();
            $( 'input[name="email"]' ).focus();
            return false;
        }
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if ( !emailReg.test( email ) ) {
            $( "span#email_error" ).show();
            $( 'input[name="email"]' ).focus();
            return false;
        }

        //prepaire html
        $( 'form' ).find( 'input' ).each( function () {
            if ( $( this ).is( ':checkbox:checked' ) )
                $( this ).attr( 'checked', 'checked' )
            $( this ).attr( 'value', $( this ).val() )
        } )

        $( 'form' ).find( 'textarea' ).text( $( 'form' ).find( 'textarea' ).val() )
        $( 'body' ).append( '<div class="mailerDiv hidden" />' )
        $( '.mailerDiv' ).html( $( '.assessmentTable' ).clone() );
        var dataString = {};
        dataString['form'] = $( '#assessmentForm' ).serialize();
        dataString['html'] = $( '.mailerDiv' ).find( '.removable' ).remove().end().html();
        $( '.mailerDiv' ).remove();
        //alert (dataString);
        //return false;

        $.ajax( {
            url     : "/crawl?/send-testrequest/",
            data    : dataString,
            async   : false,
            type    : 'POST',
            dataType: 'json',
            success : function ( data ) {
                if ( !data.type == 'success' )
                    sendMessage( 'alert-' + data.type, data.message );
                else
                    $( '#assessmentForm' ).html( "<div id='message'></div>" );
                $( '#message' ).html( "<h4>Köszönjük érdeklédésük!</h4>" )
                    .append( "<p>Munkatársunk hamarosan megkeresi Önöket a részletekkel!</p>" )
                    .hide()
                    .fadeIn( 1500, function () {
                        $( '#message' );
                    } );
            }
        } );
        return false;
    } );
} );
