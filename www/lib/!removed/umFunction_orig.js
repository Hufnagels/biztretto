var openedClass = 'icon-eye-close opener';
var closedClass = 'icon-eye-open opener';
var scrollContainerHeight = 425;
var scrollContainerInnerHeight = 350;

var selectedClass = 'selectedElement';
var xpos;
var ypos;

var $optionSet = $('#sortBy');
var ascOrder = true;
var currentFilter, sortValue;

(function (userManager, $, undefined) {
    "use strict";
    var userManagerElements = {
            userForm: document.getElementById("usersform") || '',
            newUserform : $("#newuser"),
            myUsers: document.getElementById("myUsersList") || '',
            addUserButton: document.getElementById("addUserButton") || '',
            $addUserAction: $('#addUser').find('li.action a') || '',
            upload: document.getElementById('upload') || '',
            fileupload: document.getElementById('fileupload') || '',
            selectionMenu: document.getElementById('selectionMenu') || '',
            select: function (entry) {
                switch (entry.attr('class')) {
                    case 'selectall' :
                        $(userManagerElements.myUsers).find('li.rootClass:not(.hiddenClass, .hidden)').addClass('selected');//.find('.icon-ok').addClass('icon-white');
                        break;
                    case 'deselectall' :
                        $(userManagerElements.myUsers).find('li.rootClass:not(.hiddenClass, .hidden)').removeClass('selected');//.find('.icon-ok').removeClass('icon-white');
                        break;
                    case 'invertselection' :
                        $(userManagerElements.myUsers).find('li.rootClass:not(.hiddenClass, .hidden)').toggleClass('selected');//.find('.icon-ok').toggleClass('icon-white');
                        break;
                }
                User.countSelected();
                User.addDND();
            }
        },

        companyElements = {
            input: document.getElementById("newDepartmentName") || '',
            addButton: document.getElementById("addNewDepartment") || '',
            departmentList: document.getElementById('usersGroupList') || '',
            container: document.getElementById('myusersGroupsContainer') || '',
            row: []
        },

        groupElements = {
            input: document.getElementById("newGroupName") || '',
            addButton: document.getElementById("addNewGroup") || '',
            groupList: document.getElementById('trainingGroupList'),
            container: document.getElementById('myOptionsContainer') || '',
            row: []
        },

        optionElements = {
            buttonGroup: document.getElementsByClassName('optionButtons') || '',
            selectedCount: document.getElementById('selectedCount') || '',
            $optionButton: $('.optionButtons').find('button') || '',
            init: function () {
                this.$optionButton.bind('click', function () {
                    if ($(this).hasClass('disabled')) return false;
                    var action = $(this).attr('data-option-value');
                    var ids = []
                    $(userManagerElements.myUsers).find('li.rootClass.selected:not(.hiddenClass, .hidden)').each(function () {
                        ids.push($(this).attr('id'))
                    })
                    User.setAction(action, ids);
                    $(userManagerElements.myUsers).find('li.rootClass.selected').removeClass('selected');//.find('.icon-ok').removeClass('icon-white');
                    User.countSelected();

                })

            }
        },

        sortingElements = {
            $sortbyButtons: $('#sortBy > button') || '',
            $wordFilterButton: $('#filterWord') || '',
            $orderButtons: $('#sortOrder') || '',
            $filterButton: $('#filterByIA > button') || '',
            order: 'asc',
            init: function () {
                this.$wordFilterButton.bind('keyup', function () {
                    if ($(this).val().length < 2) {
                        $(userManagerElements.myUsers).find('li.rootClass').removeClass('hidden');
                        return false;
                    }
                    var searchText = $(this).val().toLowerCase();
                    $(userManagerElements.myUsers).find('li.rootClass').filter(function () {
                        return $(this).attr('data-name').toLowerCase().indexOf(searchText) == -1;
                    }).addClass('hidden');
                    sortingElements.sort();
                });

                this.$filterButton.bind('click', function (e) {

                    e.preventDefault();
                    $(this).hasClass('active') ? $(this).removeClass('active') : $(this).addClass('active');
                    var type = '.colorBar.' + $(this).attr('data-option-value');
                    var elements = $(userManagerElements.myUsers).find('li.rootClass').find('div' + type).parent();
                    elements.toggleClass('hidden');
                    sortingElements.sort();
                });

                $('#sortBy > button').bind('click', function (e) {
                    e.preventDefault();
                    $('#sortBy').find('button').removeClass('active');
                    $(this).addClass('active');
                    sortingElements.sort();
                })

                $('#sortOrder > button').bind('click', function (e) {
                    e.preventDefault();
                    $('#sortOrder').find('button').removeClass('active');
                    $(this).addClass('active');
                    sortingElements.sort();
                })
                sortingElements.sort();
            },
            sort: function () {

                $('#detailRow').remove();
                var att = $('#sortBy').find('.btn.active').attr('data-option-value');
                var order = $('#sortOrder').find('.btn.active').attr('data-option-value');

                var filterArray = [];
                sortingElements.$filterButton.filter(function () {
                    return !$(this).hasClass('active')
                }).each(function () {
                        filterArray.push('colorBar ' + $(this).attr('data-option-value'))
                    });
                $(userManagerElements.myUsers).find('li.rootClass > .colorBar').filter(function () {
                    return filterArray.indexOf(this.className) !== -1;
                }).parent().addClass('hidden');
//console.log($(userManagerElements.myUsers).find('li.rootClass > .colorBar').filter(function(){ return filterArray.indexOf(this.className);}).attr('class'))
                $(userManagerElements.myUsers)
                    .find('li.rootClass:not(.hiddenClass, .hidden)')
                    .tsort({attr: 'data-' + att, order: order});
                $(userManagerElements.myUsers)
                    .find('li.rootClass.hiddenClass, li.rootClass.hidden')
                    .appendTo(userManagerElements.myUsers);
            }
        },

        users = [],
        usersRow = [],
        groups = [],
        departments = [],
        trainers = [],
        rowData = [],
        values = {},
        settings = {
            url: "",
            data: values,
            responseType: 'json'
        },
        actions = {
            department: {0: 'load', 1: 'delete', 2: 'rename', 3: 'new', 4: 'addto', 5: 'removefrom'},
            groups: {0: 'load', 1: 'delete', 2: 'rename', 3: 'new', 4: 'addto', 5: 'removefrom'},
            users: {0: 'load', 1: 'delete', 2: 'update', 3: 'new', 4: 'activate', 5: 'deactivate', 6: 'loadone', 7: 'changeData'}
        },

        isTraining = 0, //department

        fastMessage = {
            fastMessage: document.getElementsByClassName("fastMessage")[0],
            sendfastMessageButton: document.getElementById('sendFastMessage'),

            init: function () {
                addEventO(this.sendfastMessageButton, 'click', function (event) {
                    fastMessage.send(fastMessage.sendfastMessageButton);
                }, true, _eventHandlers)
            },

            send: function (event) {
                if ($(event).hasClass('disabled')) return false;
                var $selected = $(userManagerElements.myUsers).find('li.selected');
                var users = [];
                $selected.each(function (i, e) {
                    users.push({
                        'id': $(e).attr('id')
                    });
                });
                values = {};
                values['form'] = $(userManagerElements.userForm).serializeArray();
                values['message'] = fastMessage.fastMessage.value;
                values['users'] = users;
                values['action'] = 'fastmessage';
                settings.data = values;
                var response = sendEmail(values, settings);
                if (response.type == 'success')
                    fastMessage.fastMessage.value = '';
                return false;
            }
        },

        preventObject = {
            0: companyElements.input,
            1: groupElements.input
        },

        affixElements = {
            0: companyElements.container,
            1: groupElements.container
        },
        firstLoadedDepartment = 1,

        defaults = {
            isLazy : false,
            regEx : {
                'characterReg': /^\s*[a-zA-Z0-9,\s]+\s*$/,
                'emailExp'    : /^[^\s()<>@,;:\/]+@\w[\w\.-]+\.[a-z]{2,}$/i
            }
        };

    var User = {
        index: 1,

        init: function () {
            //this.userView();
            this.setUserAction();
            this.upload();
            userManagerElements.$addUserAction.each(function () {
                addEventO(this, 'click', function () {
                    User.userAdd($(this).attr('data-action'))
                }, true, _eventHandlers)
            })


        },

        userAdd: function (action) {

            switch (action) {
                case 'one' :
                    var data = [];
                    data['result'] = [];
                    data['result'].push();
                    var bodytext = tmpl("tmpl-individuser", data);
                    $("#confirmDiv").confirmModal({
                        heading: 'Add individual user',
                        body: bodytext,
                        text: 'Add new user',
                        page: 'users',
                        cancel: true,
                        callback: function () {

                            if(!defaults.regEx.emailExp.test( $('[name="email"]' ).val() )) {
                                $('[name="email"]' ).closest('.control-group' ).addClass('error');
                            } else $('[name="email"]' ).closest('.control-group' ).removeClass('error');

                            if( !defaults.regEx.characterReg.test( $('[name="vezeteknev"]' ).val() )) {
                                $('[name="vezeteknev"]' ).closest('.control-group' ).addClass('error');
                            } else $('[name="vezeteknev"]' ).closest('.control-group' ).removeClass('error');

                            if(!defaults.regEx.characterReg.test( $('[name="keresztnev"]' ).val() )) {
                                $('[name="keresztnev"]' ).closest('.control-group' ).addClass('error');
                            } else $('[name="keresztnev"]' ).closest('.control-group' ).removeClass('error');

                            if(userManagerElements.newUserform.find('.error' ).length > 0)
                                return false;

                            values = {};
                            values['action'] = 'new',
                                values['form'] = $(userManagerElements.userForm).serializeArray();
                            values['id'] = $('#newuser').serializeArray();
                            settings.url = "/crawl?/process/users/handelusers/";
                            settings.data = values;
                            var data = getJsonData(settings);
                            if (data.type == 'success') {
                                $('#confirmDiv').modal('hide');
                                $(userManagerElements.myUsers).prepend(tmpl("tmpl-users", data));
                                sortingElements.sort();
                            }
                            Department.init();
                            return false;

                        }
                    });

                    break;
                case 'any' :

                    $('#upload').trigger('click');
                    return false;

                    break;
            }
        },

        userInlineEdit: function (formId) {
            $.fn.editable.defaults.url = '/crawl?/process/users/handelusers/';
            $.fn.editable.defaults.params = function (params) {
                params.action = actions.users[7];
                params.form = $(userManagerElements.userForm).serializeArray()
                return params;
            };
            $('#email').editable({
                //selector: 'a',
                success: function (response, newValue) {
                    sendMessage('alert-' + response.type, response.message);
                }
            });
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
                    sendMessage('alert-' + response.type, response.message);
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

            $('#userlevel').editable({
                //prepend: "not selected",
                source: [
                    {value: 3, text: 'user'},
                    {value: 5, text: 'editor (can\'t public)'},
                    {value: 7, text: 'admin'}
                ],

                success: function (response, newValue) {
                    sendMessage('alert-' + response.type, response.message);
                }
            });

            $(".myPass").editable({
                emptytext: 'Not shown',
                send: 'never'
            });
        },

        submitPassword: function (e) {
            e.preventDefault();

            if (!$('#pwd').attr('data-pwd') || !$('#pwd2').attr('data-pwd')) {
                sendMessage('alert-warning', 'Enter new password')
                return false;
            }

            if ($('#pwd').attr('data-pwd') !== $('#pwd2').attr('data-pwd')) {
                $('.help-block').html('Passwords must be the same').addClass('alert alert-error').show();
                return false;
            }
            if ($('#pwd').attr('data-pwd').length < 5) {
                $('.help-block').html('Passwords must be min 5 chars').addClass('alert alert-error').show();
                return false;
            }
            $('.help-block').hide();

            values = {};
            values['action'] = 'changePassword';
            values['pk'] = $('#pwd').attr('data-pk');
            values['value'] = $('#pwd').attr('data-pwd');

            settings.url = '/crawl?/process/users/handelusers/';
            settings.data = values;
            var data = getJsonData(settings);
        },

        userDelete: function (settings, $selected) {
            $("#confirmDiv").confirmModal({
                heading: 'Question',
                body: isTraining ? 'Remove from training group?' : 'Delete user?',
                type: 'question',
                text: isTraining ? 'Remove' : 'Delete',
                cancel: true,
                callback: function () {
                    if (isTraining) {
                        settings.url = '/crawl?/process/users/handeltraininggroups/';
                        settings.data.action = 'removefrom';
                        settings.data.groupid = $(groupElements.groupList).find('li.mediaBox.selected').attr('data-object-id');
                    } else {

                        settings.url = settings.data.action == 'removefrom' ? '/crawl?/process/users/handelgroups/' : '/crawl?/process/users/handelusers/';
                    }

                    var data = getJsonData(settings);
                    $selected.each(function () {
                        $(this).remove();
                    });
                    isTraining ? TrainingGroup.viewGroups() : Department.departmentView();
                }
            });
            return false;
        },

        userSelect: function (entry) {
            //entry.find('.icon-ok').toggleClass('icon-white');
            entry.toggleClass('selected');
            User.countSelected();
            User.addDND();
        },

        countSelected: function () {
            var count = $(userManagerElements.myUsers).find('li.selected').length;
            $(optionElements.selectedCount).text(count);
            count == 0 ? $(fastMessage.sendfastMessageButton).addClass('disabled') : $(fastMessage.sendfastMessageButton).removeClass('disabled');
            count == 0 ? optionElements.$optionButton.addClass('disabled') : optionElements.$optionButton.removeClass('disabled');
        },

        userView: function () {
            values = {};
            values['action'] = actions.users[0];
            values['form'] = $(userManagerElements.userForm).serializeArray();
            values['from'] = defaults.isLazy ? $(userManagerElements.myUsers ).find('li.userElement' ).length : -1;
            settings.url = "/crawl?/process/users/handelusers/";
            settings.data = values;
            var data = getJsonData(settings);
            if (!data.result)
                return false;
            $('#loading' ).show();
            for (var i = 0; i < data.result.length; i++) {
                users.push(parseInt(data.result[i].id));
            }

            $(userManagerElements.myUsers).append(tmpl("tmpl-users", data));
            $('#loading' ).hide();

            this.setUserAction();
        },

        setUserAction : function(){
            rowData = calculateLIsInRow(userManagerElements.myUsers);

            //open detail row
            $(userManagerElements.myUsers).find('.detailButton').live('click', function (e) {
                e.preventDefault();
                var entry = $(this).closest('li.rootClass');
                var id = entry.attr('id');
                var detail = $('#detailRow' );
                if(detail.length && id == detail.attr('data-detail-id')){
                    $('span.icon-remove').trigger("click");
                    //detail.remove();
                } else {

                    User.userDetailRow(entry);
                }
            });

            //select element
            $(userManagerElements.myUsers).find('.selectButton').live('click', function () {
                var entry = $(this).closest('li.rootClass');
                User.userSelect(entry);
            });

            User.addDND();

            setTimeout(function () {
                $('#loading').hide();
            }, 100);

            var $actionElements = $('.userAction').find('li:not(.divider)');
            $actionElements.find('a').live('click', function (e) {
                e.preventDefault();
                User.setAction($(this).attr('data-action'), $(this).closest('li.rootClass').attr('id'));
            });
        },

        userDetailRow: function (entry) {

            var parent = entry;//$(this).closest('li');
            var row = parseInt(parent.index() / rowData[1]) + 1;
            var pointerleft = parent.position().left - parent.parent().position().left + 85 + 'px';
            //createDetailRow(parent, row, rowData, pointerleft, '240px');
            values = {};
            values['action'] = actions.users[6],//'loadOne',
                values['form'] = $(userManagerElements.userForm).serializeArray(),
                values['id'] = parent.attr('id');
            settings.url = "/crawl?/process/users/handelusers/";
            settings.data = values;
            var data = getJsonData(settings);
            if (!data.result)
                return false;
            $('#myDetailsLabel').text(entry.find('span.name').text());
            $("#myDetails" ).find('.modal-body').html(tmpl("tmpl-userDetail", data));
            $("#myDetails").modal('show');
            User.userInlineEdit($('#myDetails').find('form').attr('id'));
            //$('#detailRow').find('.inner').append(tmpl("tmpl-userDetail", data));
            //User.userInlineEdit($('#detailRow').find('form').attr('id'));
            $('#submit').live('click', function (e) {
                e.preventDefault();
                User.submitPassword(e);
            });
        },

        upload: function () {
            $(userManagerElements.fileupload).fileupload({
                url: '/crawl?/process/users/uploadusers/',
                acceptFileTypes: /(\.|\/)(csv)$/i,
                singleFileUploads: true,
                dataType: 'json',
                uploadTemplateId: null,
                downloadTemplateId: null,
                start: function (e, data) {
                    $('#loading').show();
                },
                done: function (e, data) {
                    var response = data.result;
                    if (response.result && response.result.length > 0) {
                        var data = [];
                        data['result'] = [];
                        data['result'].push(response.users);
                        $(userManagerElements.myUsers).prepend(tmpl("tmpl-users", response));
                        Department.init();

                    }
                    $('#loading').hide();
                    sendMessage('alert-'+ response.type, response.message);
                }
            });
        },

        addDND: function () {
            var $elem = $(userManagerElements.myUsers).find('li:data(draggable)');
            if ($elem.length > 0)
                $elem.draggable('destroy');
            $(userManagerElements.myUsers).find('li.rootClass.selected').draggable(User.dragUser);//
        },

        setAction: function (action, id) {
            //alert($('.userAction').find('li:not(.divider)').length)
            values = {};
            values['form'] = $(userManagerElements.userForm).serializeArray(),
                values['action'] = action,
                values['users'] = id;
            values['groupid'] = '';
            settings.url = "/crawl?/process/users/handelusers/";
            settings.data = values;

            var $selected = $(userManagerElements.myUsers).find('li.rootClass.selected');
            if ($selected.length < 2) {
                $selected = $(userManagerElements.myUsers).find('li[id="' + id + '"]');
            }

            if (action == 'delete' || action == 'removefrom') {
                User.userDelete(settings, $selected);
                return false;
            } else {
                var data = getJsonData(settings);
                $selected.each(function () {
                    $(this).find('.colorBar ').attr('class', 'colorBar ' + action);
                });
                sortingElements.sort();
            }
        },

        newUserValidate: {
            rules: {
                keresztnev: "required",
                vezeteknev: "required",
                email: {required: true, email: true},
                neme: "required"
            },
            messages: {
                keresztnev: "Enter your last name",
                vezeteknev: "Enter your first name",
                email: {
                    required: "Enter your email address",
                    email: "Enter valid email address"
                },
                neme: "Select Gender"
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            },
            submitHandler: function (form) {
                values = {};
                values['action'] = 'new',
                    values['form'] = $(userManagerElements.userForm).serializeArray();
                values['id'] = $('#newuser').serializeArray();
                settings.url = "/crawl?/process/users/handelusers/";
                settings.data = values;
                var data = getJsonData(settings);
                if (data.type == 'success') {
                    $('#confirmDiv').modal('hide');
                    $(userManagerElements.myUsers).prepend(tmpl("tmpl-users", data));
                    sortingElements.sort();
                }
                Department.init();
                return false;
                /*
                 var response = um_handelUsers('save', $('#newuser').serializeArray());
                 if (response.message.type == 'error') {
                 sendMessage('alert-' + response.message.type, response.message.message);
                 return false;
                 } else */
                if (data.type == 'success') {
                    $('#confirmDiv').modal('hide');
                    sendMessage('alert-' + response.message.type, response.message.message);
//nem jön vissza a jó adat

                    $(userManagerElements.myUsers).prepend(tmpl("tmpl-users", data));
                    var badge;
                    if (data.group.id) {
                        switch (data.group.type) {
                            case 'new':
                                var resp = um_handelUserGroups('load');
                                $userGroupList.html(tmpl("tmpl-userGroupList", resp));
                                break;
                            case 'exist':
                                badge = $userGroupList.find('[data-object-id="' + response.group.id + '"]').find('span.badge');
                                badge.text(parseInt(badge.text()) + 1);
                                break;
                        }
                    } else {
                        badge = $userGroupList.find('[data-object-name="notInList"]').find('span.badge');
                        badge.text(parseInt(badge.text()) + 1);
                        badge = $userGroupList.find('[data-object-name="viewAll"]').find('span.badge');
                        badge.text(parseInt(badge.text()) + 1);
                    }
                }
                return false;
                //$(form).submit();
            }
        },

        dragUser: {
            appendTo: 'body',
            handle: ".thumbnail",
            revert: true,
            greedy: true,
            opacity: 0.7,
            helper: function (event, ui) {
                var selected = $(userManagerElements.myUsers).find('li.rootClass.selected');
                if (selected.length === 0 || selected.length === 1) {
                    selected = $(this);
                }
                if ($('#draggingContainer2').length === 0)
                    var container = $('<ul class="span2 userElement" />').appendTo('body').attr('id', 'draggingContainer2');
                else {
                    container = $('#draggingContainer2');
                    container.empty();
                }
                container.append('<li><h1>' + selected.length + ' selected</h1></li>').css({'position': '', 'left': '', 'top': '', 'z-index': '10000'});
                $('li', container).css({'position': '', 'left': '', 'top': '', 'z-index': '10000'});
                return container;
            },
            start: function (event, ui) {
                $('#draggingContainer2').css({'position': 'absolute'});
            },
            stop: function (event, ui) {
                var pos = ui.position;
                pos = Math.abs(pos.left);
            }
        }
    };

    var Department = {
        index: 1,

        init: function () {
            $(companyElements.input).preventEnter();

            this.departmentView();

            addEventO(companyElements.addButton, 'click', function () {
                var entry = {
                    departmentName: companyElements.input.value || '',
                    post: 'save'
                };
                Department.addDepartment(entry);
            }, true, _eventHandlers)


        },

        addDepartment: function (entry) {
            if (entry.departmentName === '') {
                sendMessage('alert-warning', 'Give some text')
                return false;
            }
            values = {};
            values['action'] = actions.department[3],
                values['form'] = $(userManagerElements.userForm).serializeArray();
            values['groupname'] = entry.departmentName;
            settings.url = "/crawl?/process/users/handelgroups/";
            settings.data = values;
            var data = getJsonData(settings);

            if (data.result.id) {
                companyElements.input.value = '';
                var dep = [];
                dep['result'] = [];
                dep['result'].push({
                    'id': data.result.id,
                    'name': entry.departmentName,
                    'doname': convertDoname(entry.departmentName),
                    'badge': 0
                });

                var resHtml = tmpl("tmpl-newDepartment", dep, true);
                $(companyElements.departmentList).append(resHtml);
                $(companyElements.departmentList).find('li.mediaBox').tsort({attr: 'data-object-name'});
                this.gatherDepartments();
            }
        },

        editDepartment: function (entry) {
            $.fn.editable.defaults.url = '/crawl?/process/users/handelgroups/';
            $.fn.editable.defaults.params = function (params) {
                params.action = 'rename';
                params.form = $(userManagerElements.userForm).serializeArray()
                return params;
            };
            var selectedDepartment = $(companyElements.departmentList).find('li.selected');
            if (selectedDepartment.hasClass('viewAll'))
                return false;
            $(entry).editable({
                success: function (response, newValue) {
                    if (newValue.match(/^\s+$/) === null && newValue.length === 0) {
                        sendMessage('alert-error', 'Give a name');
                        return false;
                    }
                    selectedDepartment.attr('data-object-name', convertDoname(newValue));
                    selectedDepartment.find('div.name').text(newValue);
                    sendMessage('alert-' + response.type, response.message);
                }
            });

        },

        /*
         entry : user id
         */
        removeDepartment: function (entry) {
            addEventO(entry, 'click', function () {
                $("#confirmDiv").confirmModal({
                    heading: 'Question',
                    body: 'Do you really want to remo this department?',
                    type: 'question',
                    text: 'Delete',
                    cancel: true,
                    callback: function () {
                        values = {};
                        values['action'] = 'delete',
                            values['form'] = $(userManagerElements.userForm).serializeArray();
                        values['id'] = $(entry).attr('data-id');
                        values['users'] = [];
                        var selectedDepartment = $(companyElements.departmentList).find('li.selected');
                        //if(selectedDepartment.hasClass('viewAll'))
                        //    return false;
                        var $selected = $(userManagerElements.myUsers).find('li[data-category="' + selectedDepartment.attr('data-object-name') + '"]');
                        $selected.each(function () {
                            values['users'].push({'id': this.id});
                        });
                        settings.url = "/crawl?/process/users/handelgroups/";
                        settings.data = values;
                        var data = getJsonData(settings);
                        if (data) {

                            $('#bc').html('');
                            $(userManagerElements.myUsers).find('li.rootClass').removeClass('hidden, hiddenClass');
                            $(companyElements.departmentList).find('li.selected').remove();
                            $(companyElements.departmentList).find('li[data-object-name="viewAll"]').addClass('selected');
                            TrainingGroup.init();

                            Department.init();
                            User.init();
                        }

                    }
                });


            }, true, _eventHandlers)

        },

        /*
         select deparment and displya user in it
         */
        listDepartmentUsers: function (entry) {
            $('.mbcholder').removeClass('selected');
            $('#detailRow').remove();
            if (isSupported())
                localStorage.Department = entry.attr('data-object-name');
            entry.addClass('selected');
            isTraining = 0;
            var ugName = entry.find('.name').html();
            var ugDoname = entry.attr('data-object-name');
            var ugId = entry.attr('data-object-id');
            var bcHtml = (ugDoname == 'viewAll' ) ? 'Company all' : ((ugDoname == 'notInList' ) ? '<span>' + ugName + '</span>' : '<span class="pull-left1"><a class="editable" id="department" data-type="text" data-pk="' + ugId + '" data-value="' + ugName + '" data-original-title="Rename">' + ugName + '</a></span>' +
                '<span class="btn pull-left1 btn-empty btn-dark" id="deleteDep" data-id="' + ugId + '"><i class="icon-trash"></i></span>'/*
             '<span class="dropdown"><span class="dropdown-toggle btn-l" role="button" data-toggle="dropdown" id="" data-href="#"><span class="caret"></span></span>'+
             '<ul class="dropdown-menu" id=""><li class="action"><a href="#" data-action="one"><span class="editable" id="ugName_'+ugId+'" data-type="text" data-pk="'+ugId+'">' +ugName+'</span></a></li>'+
             '<li class="action"><a href="#" data-action="any">Users from csv file</a></li></ul></span>'+
             '<span class="editable" id="ugName_'+ugId+'" data-type="text" data-pk="'+ugId+'">' +ugName+'</span><span class="caret"></span>'*/ );

            $('#bc').html(bcHtml);
            switch (ugDoname) {
                case 'viewAll':
                    $(userManagerElements.myUsers).find('li.rootClass').removeClass('hiddenClass hidden');
                    break;
                case 'notInList':
                    $(userManagerElements.myUsers).find('li.rootClass').removeClass('hiddenClass').filter(function () {
                        return $(this).attr('data-category') !== '0';
                    }).addClass('hiddenClass');
                    break;
                default:
                    $(userManagerElements.myUsers).find('li.rootClass').removeClass('hiddenClass').filter(function () {
                        return $(this).attr('data-category') !== ugDoname;
                    }).addClass('hiddenClass');
                    this.editDepartment(document.getElementById('department'));
                    this.removeDepartment(document.getElementById('deleteDep'));
                    break;
            }

            //move hidden to end to make visible detailrow in right place
            $(userManagerElements.myUsers)
                .find('li.rootClass.hiddenClass')
                .appendTo(userManagerElements.myUsers);
            firstLoadedDepartment = 0;
            //rowData = calculateLIsInRow(userManagerElements.myUsers);
            sortingElements.sort();
        },

        updateGroupData: function (entry) {

        },

        departmentView: function () {
            values = {};
            values['action'] = actions.department[0],//'load',
                values['form'] = $(userManagerElements.userForm).serializeArray();
            settings.url = "/crawl?/process/users/handelgroups/";
            settings.data = values;

            var data = getJsonData(settings);
            if (!data)
                return false;
            $(companyElements.departmentList).html(tmpl("tmpl-userGroupList", data));
            $(userManagerElements.myUsers).find('li.rootClass').removeClass('hiddenClass');
            this.gatherDepartments();
            sortingElements.sort();
            //this.addDND();

        },

        gatherDepartments: function () {
            companyElements.row = [];
            $(companyElements.departmentList).find('li.mbcholder').each(function () {
                companyElements.row.push({'data-object-id': $(this).attr('data-object-id'), 'data-object-name': $(this).attr('data-object-name')});
            });
            $(companyElements.departmentList).find('li.mbcholder').live('click', function () {
                Department.listDepartmentUsers($(this));
                Department.addDND();
            });
            this.addDND();
        },

        addDND: function () {
            $(companyElements.departmentList).find('li.mediaBox:not(.hiddenClass, hidden)').droppable(dropOnGroup);
        }/*,

         usergroupDelete: {
         //snap: "#mediboxList",
         //snapMode: "inner",
         distance: 15,
         axis: "x",
         revert: 'invalid',
         handel: '.name',
         stop: function (event, ui) {
         var element2delete = $(this);
         if (!$(this).hasClass('selected')) {
         var pos = ui.position;
         pos = Math.abs(pos.left);
         if (pos > 30) {
         switch (element2delete.parent().attr('id')) {
         case 'usersGroupList':
         var users = userManagerElements.myusers.find('li[data-category="' + element2delete.attr('data-object-name') + '"]');
         var bodytext = '<p>Do you really want to delete Department <strong>' + element2delete.find('.name').text() + '</strong> ?</p>';
         if (users.length > 0)
         bodytext += '<p>All user will set to UNORGANIZED.</p>';
         $("#confirmDiv").confirmModal({
         heading: 'Question',
         body: bodytext,
         type: 'question',
         text: 'Delete',
         cancel: true,
         callback: function () {
         var data = {};
         data['users'] = [];
         data.groupid = (element2delete.attr('data-object-id'));
         users.each(function (i, e) {
         data['users'].push({'id': $(e).attr('data-id')});
         });
         var response = um_handelUserGroups('delete', data);

         if (response.type == 'success') {
         //var response = um_handelUserGroups('load');
         //$(companyElements.departmentList).html(tmpl("tmpl-userGroupList", response));
         var unorg = $(companyElements.departmentList).find('li[data-object-name="notInList"]'),
         count = unorg.find('span.badge').text();

         unorg.find('span.badge').text(parseInt(count) + users.length);
         element2delete.remove();
         users.each(function (i, e) {
         $(e).attr('data-category', '').find('span.department').text('');
         });
         }
         sendMessage('alert-' + response.type, response.message);
         }
         });
         break;
         case 'trainingGroupList':
         var data = [];
         data.push({'id': element2delete.attr('data-object-id')});
         var response = um_handelTrainingGroups('delete', data);
         if (response.type == 'success')
         element2delete.remove();
         sendMessage('alert-' + response.type, response.message);

         break;
         }
         }

         }
         }
         }*/
    };

    var TrainingGroup = {
        index: 1,

        init: function () {
            $(groupElements.input).preventEnter();
            this.viewGroups();
            addEventO(groupElements.addButton, 'click', function () {
                var entry = {
                    groupName: groupElements.input.value || '',
                    post: actions.groups[3]
                };
                TrainingGroup.addGroup(entry);
            }, true, _eventHandlers)
        },

        addGroup: function (entry) {
            if (entry.groupName == '') {
                sendMessage('alert-warning', 'Give some text')
                return false;
            }
            values = {};
            values['action'] = entry.post;
            values['form'] = $(userManagerElements.userForm).serializeArray();
            values['groupname'] = entry.groupName;
            settings.url = "/crawl?/process/users/handeltraininggroups/";
            settings.data = values;
            var data = getJsonData(settings);

            if (data.result.id) {
                groupElements.input.value = '';
                var group = [];
                group['result'] = [];
                group['result'].push({
                    'id': data.result.id,
                    'name': entry.groupName,
                    'doname': convertDoname(entry.groupName),
                    'badge': 0
                });

                var resHtml = tmpl("tmpl-trainingGroupList", group, true);
                $(groupElements.groupList).append(resHtml);
                $(groupElements.groupList).find('li.mediaBox').tsort({attr: 'data-object-name'});
                this.gatherGroups();
            }
        },

        /*
         entry : user id
         editType : activate, deactivate, details
         */
        editGroup: function (entry) {
            $.fn.editable.defaults.url = '/crawl?/process/users/handeltraininggroups/';
            $.fn.editable.defaults.params = function (params) {
                params.action = 'rename';
                params.form = $(userManagerElements.userForm).serializeArray()
                return params;
            };

            var selectedGroup = $(groupElements.groupList).find('li.selected');

            $(entry).editable({
                success: function (response, newValue) {
                    if (newValue.match(/^\s+$/) === null && newValue.length === 0) {
                        sendMessage('alert-error', 'Give a name');
                        return false;
                    }
                    selectedGroup.attr('data-object-name', convertDoname(newValue));
                    selectedGroup.find('div.name').text(newValue);
                    sendMessage('alert-' + response.type, response.message);
                }
            });
        },

        /*
         entry : user id
         */
        removeGroup: function (entry) {
            addEventO(entry, 'click', function () {
                $("#confirmDiv").confirmModal({
                    heading: 'Question',
                    body: 'Do you really want to delete this training group?',
                    type: 'question',
                    text: 'Delete',
                    cancel: true,
                    callback: function () {
                        values = {};
                        values['action'] = actions.groups[1],
                            values['form'] = $(userManagerElements.userForm).serializeArray();
                        values['id'] = $(entry).attr('data-id');
                        settings.url = "/crawl?/process/users/handeltraininggroups/";
                        settings.data = values;
                        var data = getJsonData(settings);

                        if (data) {
                            $('#bc').html('');
                            $(userManagerElements.myUsers).find('li.rootClass').removeClass('hidden, hiddenClass');
                            $(groupElements.groupList).find('li.selected').remove();
                            $(companyElements.departmentList).find('li[data-object-name="viewAll"]').addClass('selected');
                            TrainingGroup.init();
                        }

                    }
                });

            }, false, _eventHandlers)

        },

        listGroupUsers: function (entry) {
            $('#detailRow').remove();
            $('.mbcholder').removeClass('selected');
            entry.addClass('selected');
            isTraining = 1;
            var ugName = entry.find('.name').html();
            var ugDoname = entry.attr('data-object-name');
            var ugId = entry.attr('data-object-id');
            var bcHtml = (ugDoname == 'viewAll' ) ? '' : ((ugDoname == 'notInList' ) ? '<span>' + ugName + '</span>' : '<span class="pull-left"><a class="editable" id="group" data-type="text" data-pk="' + ugId + '" data-value="' + ugName + '" data-original-title="Rename">' + ugName + '</a></span>' +
                '<span class="btn btn-empty btn-dark" id="deleteGrp" data-id="' + ugId + '"><i class="icon-trash"></i></span>'/*
             '<span class="dropdown"><span class="dropdown-toggle btn-l" role="button" data-toggle="dropdown" id="" data-href="#"><span class="caret"></span></span>'+
             '<ul class="dropdown-menu" id=""><li class="action"><a href="#" data-action="one"><span class="editable" id="ugName_'+ugId+'" data-type="text" data-pk="'+ugId+'">' +ugName+'</span></a></li>'+
             '<li class="action"><a href="#" data-action="any">Users from csv file</a></li></ul></span>'+
             '<span class="editable" id="ugName_'+ugId+'" data-type="text" data-pk="'+ugId+'">' +ugName+'</span><span class="caret"></span>'*/ );
            $('#bc').html(bcHtml);
            var members = entry.attr('data-member-list');
            var data = members.split(',');
            $(userManagerElements.myUsers).find('li.rootClass').removeClass('hiddenClass').filter(function () {
                return data.indexOf($(this).attr('id')) == -1;
            }).addClass('hiddenClass');

            //move hidden to end to make visible detailrow in right place
            $(userManagerElements.myUsers).find('li.rootClass.hiddenClass').appendTo(userManagerElements.myUsers);
            this.editGroup(document.getElementById('group'));
            this.removeGroup(document.getElementById('deleteGrp'));
        },

        updateGroupData: function (entry) {
        },

        viewGroups: function () {
            values = {};
            values['action'] = actions.groups[0];//'load',
            values['form'] = $(userManagerElements.userForm).serializeArray();
            settings.url = "/crawl?/process/users/handeltraininggroups/";
            settings.data = values;
            var data = getJsonData(settings);

            if (!data.result)
                return false;
            $(groupElements.groupList).html(tmpl("tmpl-trainingGroupList", data));
            this.gatherGroups();
        },

        gatherGroups: function () {
            groupElements.row = [];
            $(groupElements.groupList).find('li.mbcholder').each(function () {
                groupElements.row.push({'data-object-id': $(this).attr('data-object-id'), 'data-object-name': $(this).attr('data-object-name')});
            });
            $(groupElements.groupList).find('li.mbcholder').live('click', function () {
                TrainingGroup.listGroupUsers($(this))
                TrainingGroup.addDND();
            });
            this.addDND();
        },

        addDND: function () {
            $(groupElements.groupList).find('li.mediaBox:not(.hiddenClass, hidden)').droppable(dropOnGroup);
        }
    };

    userManager.init = function () {

        $.fn.editable.defaults.mode = 'inline';
        $.fn.editable.defaults.dataType = 'json';
        $.fn.editable.defaults.emptytext = 'Please, fill this';
        $.fn.editable.defaults.url = '';

        $(companyElements.departmentList).slimScroll({position: 'left', height: '300px', allowPageScroll: false, width: 'auto'});
        $(groupElements.groupList).slimScroll({position: 'right', height: '300px', allowPageScroll: false, width: '190px'});

        initAffix(affixElements);

        $(userManagerElements.upload).bind('change', function (e) {
            if (/\.csv$/gi.test($(this).val()) == false)
                sendMessage('alert-warning', 'Only csv allowed');
            else {
                $(userManagerElements.fileupload).fileupload('send', {
                    files: e.target.files || [
                        {name: this.value}
                    ],
                    fileInput: $(this),
                    formData: {form: $(userManagerElements.userForm).serialize()}
                });
            }
            $(this).val('');
        });

        //prevent enter key
        /*for (var key in preventObject){
         $(preventObject[key]).bind('keypress', function(e){
         var code = (e.keyCode ? e.keyCode : e.which);
         if (code == 13) return false;
         });
         };*/

        Department.init();
        TrainingGroup.init();
        User.init();

        fastMessage.init();
        sortingElements.init();

        $(userManagerElements.selectionMenu).find('a').bind('click', function (e) {
            e.preventDefault();
            userManagerElements.select($(this));
        });
        optionElements.init();

        //scrollToWorkingArea($('.row.special'));

        $(window).scroll(function(){
            if( ($(window).scrollTop() == $(document).height() - $(window).height()) && defaults.isLazy ){
                User.userView();
            }
        });

    };


    var dropOnGroup = {
        tolerance: 'pointer',
        accept: "li",
        //activeClass: "ui-state-highlight",
        hoverClass: "ui-state-highlight",
        greedy: true,
        drop: function (e, ui) {
            var group = $(this),
                groupId = group.attr('data-object-name'),
                groupName = group.find('div.name').text(),
                groupHolder = group.parent().attr('id'),
                elements = $(userManagerElements.myUsers).find('li.selected:not(.hiddenClass, hidden)');

            var selected = {
                action: '',
                users: [],
                form: $(userManagerElements.userForm).serializeArray(),
                groupid: group.attr('data-object-id'),
                groupname: groupName
            };

            elements.each(function (i, e) {
                selected['users'].push($(this).attr('id'));
            });
            settings.url = '';
            switch (groupHolder) {
                case 'usersGroupList':
                    settings.url = '/crawl?/process/users/handelgroups/';
                    selected['action'] = actions.department[4];
                    break;
                case 'trainingGroupList':
                    settings.url = '/crawl?/process/users/handeltraininggroups/';
                    selected['action'] = actions.groups[4];
                    break;
            }
            settings.data = selected;

            var data = getJsonData(settings);

            if (data) {
                $('#draggingContainer2').animate(
                    {
                        width: ['toggle', 'swing'],
                        height: ['toggle', 'swing'],
                        opacity: 'toggle'
                    }, 700, 'linear', function () {
                        if (groupHolder == 'usersGroupList') {
                            elements.each(function (i, e) {
                                $(e).find('span.department').text(groupName);
                                $(e).attr('data-category', groupId);//convertDoname(groupName));
                            });
                        }
                        $('#draggingContainer2').remove();
                        $(userManagerElements.myUsers).find('.selected').each(function () {
                            User.userSelect($(this));
                        });
                    });
                switch (groupHolder) {
                    case 'usersGroupList':
                        Department.init();
                        break;
                    case 'trainingGroupList':
                        TrainingGroup.init();
                        break;
                }
            }
            return false;

        }
    };

}(window.userManager = window.userManager || {}, jQuery));