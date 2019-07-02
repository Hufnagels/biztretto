/*
 * jQuery File Upload Plugin JS Example 6.11
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global $, window, document */

$(function () {
    'use strict';
    
    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: 'server/php/',
    filesContainer: $('.files'),
    uploadTemplateId: null,
    downloadTemplateId: null,
    uploadTemplate: function (o) {
        var rows = $();
        $.each(o.files, function (index, file) {

            var row = $('<li class="template-upload span2 fade">' +
                '<div class="thumbnail">' +
                  '<div class="preview"><span class="fade"></span></div>' +
                  
                  '<div class="caption">' +
                    '<p class="error"></p>' +
                    '<p class="title"></p>' + 
                    '<p>Size:<span class="size"></span></p>' +
                    '<p>ID: label</p>' + 
                    '<p><div class="progress"><div class="bar" style="width:0%;"></div></div></p>' +
                    '<p class="start"><button class="btn btn-primary"><i class="icon-upload icon-white"></i>Start</button><button>Cancel</button></p>' +
                  '</div>' +
                '</div>' +
              '</li>');
/**/
/*
            var row = $('<tr class="template-upload fade">' +
                '<td class="preview"><span class="fade"></span></td>' +
                '<td class="name"></td>' +
                '<td class="size"></td>' +
                (file.error ? '<td class="error" colspan="2"></td>' :
                        '<td><div class="progress">' +
                            '<div class="bar" style="width:0%;"></div></div></td>' +
                            '<td ><button class="btn btn-primary"> ' +
                    '<i class="icon-upload icon-white"></i>Start</button></td>'
                ) + '<td class="cancel"><button>Cancel</button></td></tr>');
                //alert( row );
*/
            row.find('.title').text(file.name);
            row.find('.size').text(o.formatFileSize(file.size));
            if (file.error) {
                row.find('.error').text(
                    file.error//locale.fileupload.errors[file.error] || file.error
                );
            }
            rows = rows.add(row);
            //$('.files1').append(row.html());
        });
        return rows;
        //$('.files1').append(rows);
    },
    downloadTemplate: function (o) {
        var rows = $();
        $.each(o.files, function (index, file) {

            var row = $('<li class="template-upload span2 fade">' +
                '<div class="thumbnail">' +
                  '<div class="preview"><span class="fade"></span></div>' +
                  
                  '<div class="caption">' +
                    '<p class="error"></p>' +
                    '<p class="title"></p>' + 
                    '<p>Size:<span class="size"></span></p>' +
                    '<p>ID: label</p>' + 
                    '<p><div class="progress"><div class="bar" style="width:0%;"></div></div></p>' +
                    '<p class="delete"><button class="btn btn-danger">Delete</button><input type="checkbox" name="delete" value="1"></p>' +
                  '</div>' +
                '</div>' +
              '</li>');
/**/
/*
            var row = $('<tr class="template-download fade">' +
                (file.error ? '<td></td><td class="name"></td>' +
                    '<td class="size"></td><td class="error" colspan="2"></td>' :
                        '<td class="preview"></td>' +
                            '<td class="name"></td>' +
                            '<td class="size"></td><td colspan="2"></td>'
                ) + '<td class="delete"><button class="btn btn-danger">Delete</button> ' +
                    '<input type="checkbox" name="delete" value="1"></td></tr>');
*/
            row.find('.size').text(o.formatFileSize(file.size));
            if (file.error) {
                row.find('.name').text(file.name);
                row.find('.error').text(
                    file.error//locale.fileupload.errors[file.error] || file.error
                );
            } else {
                row.find('.name').text(file.name);
                if (file.thumbnail_url) {
                    row.find('.preview').append('<a class="gallery"><img></a>')
                        .find('img').prop('src', file.thumbnail_url);
                    row.find('a').prop('rel', 'gallery');
                }
                row.find('a').prop('href', file.url);
                row.find('.delete button')
                    .attr('data-type', file.delete_type)
                    .attr('data-url', file.delete_url);
            }
            rows = rows.add(row);
        });
        
        return rows;
    }
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );
/*    
    $('#fileupload').bind('fileuploaddone', function (e, data) {
           
           //alert( 'papo' );
             $('.gallery').fancybox({
                //'type' : 'iframe',
                'width':'100%',
                'height':'100%',
                'autoScale':true,
                'transitionIn'	: 'none',
                'transitionOut'	: 'none'
              });
            //$('a.gallery').live('click', function(){ });
        });
*/        
//alert( window.location.hostname );
    if (window.location.hostname === 'admin.skillbuilder.local') {
        // Demo settings:
        $('#fileupload').fileupload('option', {
            url: 'server/php/',
            maxFileSize : 50000000,
            maxChunkSize: 2000000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png|zip|pdf|avi|wmv)$/i
            ,process: [
                {
                    action: 'load',
                    fileTypes: /^image\/(gif|jpeg|png|zip|pdf|avi|wmv)$/,
                    maxFileSize: 20000000 // 20MB
                },
                {
                    action: 'resize',
                    maxWidth: 1440,
                    maxHeight: 900
                },
                {
                    action: 'save'
                }
            ]
            
        });
        
        // Upload server status check for browsers with CORS support:
        if ($.support.cors) {
            $.ajax({
                url: 'server/php/',
                type: 'HEAD'
            }).fail(function () {
                $('<span class="alert alert-error"/>')
                    .text('Upload server currently unavailable - ' +
                            new Date())
                    .appendTo('#fileupload');
            });
        }
    } else {
        // Load existing files:
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: $('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: $('#fileupload')[0]
        }).done(function (result) {
          //alert( 'papo' );
            if (result && result.length) {
                $(this).fileupload('option', 'done')
                    .call(this, null, {result: result});
            }
        });
    }

});
