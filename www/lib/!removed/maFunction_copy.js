(function (myAccount, $, undefined) {
    var myAccountElements = {
        $passwordSubmit: document.getElementById('submit') || '',
        $upload: document.getElementById('upload') || '',
        $profilePicture: document.getElementById('profilePicture') || '',
        statisticHolder: document.getElementById("statisticHolder") || ''
    };

    //ajax load settings
    var values = {};
    var settings = {
        url: "",
        data: values,
        responseType: 'json'
    };
    var maxW = 160;
    var maxH = 160;

    var changeData = {

        init : function(){
            this.attachEditable();
            this.attachSpecialEditable();
        },
        attachEditable : function(){
            $('#detailsForm').find('a').editable({
                success: function (response, newValue) {
                    sendMessage('alert-' + response.type, response.message);
                }
            });

            $('#cv, #description').editable({
                success: function (response, newValue) {
                    sendMessage('alert-'+response.type, response.message);
                }
            });
        },

        attachSpecialEditable : function(){
            $('#gender').editable({
                //prepend: "not selected",
                source: [
                    {value: 1, text: 'Male'},
                    {value: 2, text: 'Female'}
                ],

                success: function (response, newValue) {
                    sendMessage('alert-' + response.type, response.message);
                }
            });

            $('#birth').editable({
                success: function (response, newValue) {
                    sendMessage('alert-'+response.type, response.message);
                }
            });
            $('#language').editable({
                source: [
                    {value: 'hungarian', text: 'hungarian'},
                    {value: 'english', text: 'english'},
                    {value: 'german', text: 'german'},
                    {value: 'italian', text: 'italian'},
                    {value: 'spanish', text: 'spanish'}
                ],
                success: function (response, newValue) {
                    sendMessage('alert-' + response.type, response.message);
                }
            });

            $(".myPass").editable({
                emptytext: 'Change here',
                send: 'never'
            });
        },

        submitPassword : function(e){
            e.preventDefault();
            /**/
            if ($('#pwd').attr('data-pwd') !== $('#pwd2').attr('data-pwd'))
            {
                $('.help-block').html('Passwords must be the same').addClass('alert alert-error').show();
                return false;
            }
            if ($('#pwd').attr('data-pwd').length < 5)
            {
                $('.help-block').html('Passwords must be min 5 chars').addClass('alert alert-error').show();
                return false;
            }
            $('.help-block').hide();

            values = {};
            values['action'] = 'changePassword';
            values['pk'] = $('#pwd').attr('data-pk');
            values['value'] = $('#pwd').attr('data-pwd');

            settings.url = '/crawl?/process/myaccount/handelmyaccount/';
            settings.data = values;
            var data = getJsonData(settings);
        }

    };

    var statistic = {
        userid: 0,
        init: function () {
            userid = document.getElementById("userid").value;
            this.getTrainingStat()
        },
        getTrainingStat: function () {
            j = [];
            m = {};
            m.form = $(myAccountElements.form).serializeArray();
            m.userid = userid;
            settings.url = "/crawl?/process/statistic/handeluserstat/";
            settings.data = m;
            var data = getJsonData(settings);
            if (!data) {
                return false
            }
            j.result = [];
            var q = [],
                r = [];
            for (var o in data.result) {
                q.push(o)
            }
            for (var n = 0, o; o = data.main[n]; n++) {
                r.push(o.title)
            }
            for (var n = 0, o; o = q[n]; n++) {
                data.result[o].title = r[n];
                j.result.push(data.result[o])
            }
            //$(myAccountElements.statisticHolder).append(tmpl("tmpl-statpanel2", j));
            this.publishSummarized(j)
            return false
        },
        publishSummarized: function (n) {
            $(myAccountElements.statisticHolder).html(tmpl("tmpl-statpanel2", n))
        }
    };

    myAccount.init = function () {
        $.fn.editable.defaults.mode = 'inline';
        $.fn.editable.defaults.url = '/crawl?/process/myaccount/handelmyaccount/';
        $.fn.editable.defaults.dataType = 'json';
        $.fn.editable.defaults.emptytext = 'Please, fill this';
        $.fn.editable.defaults.params = function (params) {
            params.action = 'changeData';
            return params;
        };
        changeData.init();
        statistic.init()

        addEventO(myAccountElements.$passwordSubmit, 'click', changeData.submitPassword, true, _eventHandlers);

        addEventO(myAccountElements.$upload, 'change', function (event) {
            resizeAndUpload(event, myAccountElements.$profilePicture);
        }, false, _eventHandlers);

        $(myAccountElements.$profilePicture).on('click', function () {
            $(myAccountElements.$upload).trigger('click');
        })
        $('.accordion').on('show', function (e) {

            $(e.target).prev('.accordion-heading').find('.accordion-toggle').addClass('active');
            $(e.target).prev('.accordion-heading').find('.accordion-toggle i').removeClass('icon-plus');
            $(e.target).prev('.accordion-heading').find('.accordion-toggle i').addClass('icon-minus');
        });

        $('.accordion').on('hide', function (e) {
            $(this).find('.accordion-toggle').not($(e.target)).removeClass('active');
            $(this).find('.accordion-toggle i').not($(e.target)).removeClass('icon-minus');
            $(this).find('.accordion-toggle i').not($(e.target)).addClass('icon-plus');
        });


    }

    function handleFileSelect(evt, object) {
        var files = evt.target.files; // FileList object
        for (var i = 0, f; f = files[i]; i++) {
            if (!f.type.match('image.*')) {
                continue;
            }
            var reader = new FileReader();
            reader.onload = (function (theFile) {
                return function (e) {
                    object.src = e.target.result;
                };
            })(f);
            reader.readAsDataURL(f);
        }
    }

    function resizeAndUpload(evt, obj) {
        var file = evt.target.files[0];
        var reader = new FileReader();
        reader.onloadend = function () {

            var tempImg = new Image();
            tempImg.src = reader.result;
            tempImg.onload = function () {

                var dataURL = resizeCrop( this, maxW, maxH, -5 ).toDataURL('image/jpg', 90);
                $(obj).attr('src', dataURL);
                values['action'] = 'changeData';
                values['pk'] = $(obj).attr('data-pk');
                values['name'] = $(obj).attr('id');
                values['value'] = dataURL;
                settings.url = '/crawl?/process/myaccount/handelmyaccount/';
                settings.data = values;
                var data = getJsonData(settings);
                //canvas = null;
            }

        }
        reader.readAsDataURL(file);
    }



}(window.myAccount = window.myAccount || {}, jQuery));