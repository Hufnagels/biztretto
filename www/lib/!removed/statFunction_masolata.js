(function (Statistic, $, undefined) {
    var statisticElements = {
            form: document.getElementById("statform") || '',
            folderSelect: document.getElementById('themeRow') || '',
            leftContainer: document.getElementById('mySlideshowsContainer') || '',
            diskAreaId: document.getElementsByName('diskArea_id').value,
            sumStatisticFrame : document.getElementById('sumStatisticFrame') || '',
            instanceStatisticFrame : document.getElementById('instanceStatisticFrame') || '',
            statisticHolder : document.getElementById('statisticHolder') || '',
            userTable : document.getElementById('userTable') || '',
            myUsersList : document.getElementById('myUsersList') || '',
            instanceHolder : document.getElementById('bc') || '',
            instanceList : document.getElementById('instanceList') || '',
            poleButtons : {
                buttons :document.querySelectorAll('.pole') || '',
                init : function(){
                    $('.pole').each(function(){
                        addEventO(this, 'click', function () {
                            var index = $(this).attr('data-pole');
                            $('.summaryzedData  h1').each(function(){ $(this).removeClass('btn-dark active'); }).end();
                            if(0 == userRateData[index].length)
                                return false;
                            $('.pole').each(function(){ $(this).removeClass('active'); }).end();
                            $(this).addClass('active');
                            $(statisticElements.myUsersList).html( tmpl("tmpl-usertable2", userRateData[index]) );
                            return false;
                        }, true, _eventHandlers);
                    })
                }
            },
            inprogressButton:{
                $button : $('.inprogressTraining').find('h1') || '',
                init : function(){
                    statisticElements.inprogressButton.$button.live('click', function () {
                        $(statisticElements.myUsersList).empty();
                        $('.pole').each(function(){ $(this).removeClass('active'); }).end();
                        $('.summaryzedData  h1').each(function(){ $(this).removeClass('btn-dark active'); }).end();
                        if(0 == inProgressData.result.length)
                            return false;
                        $(this).addClass('btn-dark active');
                        $(statisticElements.myUsersList).html( tmpl("tmpl-usertable2", inProgressData) );
                        return false;
                    });
                }
            },
            finishedButton : {
                $button : $('.finishedTraining').find('h1') || '',
                init : function(){
                    statisticElements.finishedButton.$button.live('click', function () {
                        $(statisticElements.myUsersList).empty();
                        $('.pole').each(function(){ $(this).removeClass('active'); }).end();
                        $('.summaryzedData  h1').each(function(){ $(this).removeClass('btn-dark active'); }).end();
                        if(0 == finishedData.result.length)
                            return false;
                        $(this).addClass('btn-dark active');
                        $(statisticElements.myUsersList).html( tmpl("tmpl-usertable2", finishedData) );
                        return false;
                    });
                }
            },
            allusersButton : {
                $button : $('.totalUsers').find('h1') || '',
                init : function(){
                    statisticElements.allusersButton.$button.live('click', function () {
                        $(statisticElements.myUsersList).empty();
                        $('.pole').each(function(){ $(this).removeClass('active'); }).end();
                        $('.summaryzedData  h1').each(function(){ $(this).removeClass('btn-dark active'); }).end();
                        if(0 == allUsersData.result.length)
                            return false;
                        $(this).addClass('btn-dark active');
                        $(statisticElements.myUsersList).html( tmpl("tmpl-usertable2", allUsersData) );
                        return false;
                    });
                }
            }
        },

        trainingElements = {
            trainingList: document.getElementById('trainingList') || '',
            folderSelector: document.getElementById('daForTrainings') || ''
        },

        trainingGroupElements = {
            groupList: document.getElementById('GroupList') || ''
        },

        userElements = {
            userList: document.getElementById('UsersList') || ''
        },

        values = {},
        settings = {
            url: "",
            data: values,
            responseType: 'json'
        },
        userRateData = {},
        allUsersData = {},
        inProgressData = {},
        finishedData = {},
        tempObj = [],
        actions = {
            trainings: {0: 'load', 1: 'list'},
            instances: {0: 'load', 1: 'list'},
            stat : {0: 'load'}
        },
        affixElements = {
            0: statisticElements.leftContainer
        };



    var Trainings = {
        selectedId: 0,

        isSlideshowSaved: false,

        init: function () {
            this.setTrainingActions();
        },

        viewTrainings: function () {
            values = {};
            values['action'] = actions.trainings[1];//'list',
            values['form'] = $(statisticElements.form).serializeArray();
            values['folderId'] = $(trainingElements.folderSelector).attr('data-diskarea-id');
            settings.url = "/crawl?/process/statistic/handeltraining/";
            settings.data = values;
            var data = getJsonData(settings);
            if (!data.result)
                return false;
            $(trainingElements.trainingList).html(tmpl("tmpl-miniTrainings", data));
            this.setTrainingActions();
        },

        setTrainingActions : function(){
            $(trainingElements.trainingList).find('li.slideElement').live('click', function (e) {
                e.preventDefault();
                Trainings.clearSelection();
                TrainingGroups.clearSelection();
                TrainingGroups.clearInstances();
                Users.clearSelection();
                statistic.clear();
                $(this).find('.dataHolder').addClass('selected');
                Trainings.loadTraining(this);
            });
        },

        loadTraining: function (entry) {
            userRateData = {};
            allUsersData = {};
            inProgressData = {};
            finishedData = {};
            values = {};
            values['action'] = actions.trainings[0];
            values['form'] = $(statisticElements.form).serializeArray();
            values['trainingId'] = entry.id;
            settings.url = "/crawl?/process/statistic/handeltrainingstat/";
            settings.data = values;
            var data = getJsonData(settings);
            if (!data.main) {
                return false;
            }
            var headData = data.main[0];
            this.selectedId = headData.id;
            $(statisticElements.form).find('input[name="id"]').val(headData.id);

            if (data.result) {
                statistic.publishSummarized(data);
            }
        },

        clearSelection : function(){
            $(trainingElements.trainingList ).find('.selected' ).removeClass('selected');
            statistic.clear();
            TrainingGroups.clearSelection();
        }
    };

    var statistic = {

        clear : function() {
            $(statisticElements.myUsersList).empty();
            //$(statisticElements.statisticHolder).html(tmpl("tmpl-statpanel", data));
            $(statisticElements.statisticHolder ).find('.strdata' ).html('');
            $(statisticElements.statisticHolder ).find('.intdata' ).html('0');
            userRateData = 0;
            allUsersData = 0;
            inProgressData = 0;
            finishedData = 0;
        },

        publishSummarized : function(data,type) {
            $(statisticElements.myUsersList).empty();
            if(type === true){
                $(statisticElements.statisticHolder ).addClass('users' ).removeClass('well well-small')
                $(statisticElements.statisticHolder).append(tmpl("tmpl-statpanel2", data));
            } else{
                $(statisticElements.statisticHolder ).removeClass('users').addClass('well well-small')

                $(statisticElements.statisticHolder).html(tmpl("tmpl-statpanel", data));
            }

            userRateData = data.result[0].trainingrates;
            allUsersData = data.result[0].traininguser;
            inProgressData = data.result[0].in_progressuser;
            finishedData = data.result[0].finisheduser;
            statisticElements.poleButtons.init();
            statisticElements.inprogressButton.init();
            statisticElements.finishedButton.init();
            statisticElements.allusersButton.init();
        }
    };

    var TrainingGroups = {

        selectedInstanceId : 0,
        selectedGroupId : 0,

        init : function(){
            this.selectGroup();
        },

        selectGroup : function(){
            $(trainingGroupElements.groupList).find('li.mbcholder').live('click', function () {
                Trainings.clearSelection();
                TrainingGroups.clearSelection();
                TrainingGroups.clearInstances();
                Users.clearSelection();
                $(this ).addClass('selected');

                statistic.clear();
                TrainingGroups.selectedGroupId = $(this ).attr('data-object-id');
                TrainingGroups.listInstances( TrainingGroups.selectedGroupId );
                $(statisticElements.statisticHolder).html(tmpl("tmpl-statpanel_empty", ''));
            });
        },

        selectInstances : function(instanceId){
            var responseData = [];
            responseData['result'] = [];
            responseData['result'].push(tempObj.result[instanceId]);
            statistic.clear();
            statistic.publishSummarized(responseData);
        },

        listInstances : function(id){
            tempObj = [];
            values = {};
            values['action'] = actions.instances[1];//'list',
            values['form'] = $(statisticElements.form).serializeArray();
            values['groupid'] = id;
            settings.url = "/crawl?/process/statistic/handelgroupstat/";
            settings.data = values;
            var data = getJsonData(settings);

            if (!data){
                this.clearInstances();
                return false;
            }

            tempObj = data;
            var instances = [];
            for (var inst in data.result)
                instances.push(inst);

            $(statisticElements.instanceHolder ).removeClass('hiddenClass');
            for (var i=0, inst; inst=data.main[i]; i++) {
                $(statisticElements.instanceList ).append('<li class=""><a href="#" data-id="'+instances[i]+'">'+inst.title+'</a></li>')
            }
            $(statisticElements.instanceList ).find('a' ).bind('click', function(e){
                e.preventDefault();
                $(this ).parent().siblings().removeClass('active' ).end();
                $(this ).parent().addClass('active');
                TrainingGroups.selectInstances($(this ).attr('data-id'));
            })
        },

        clearInstances : function(){
            $(statisticElements.instanceHolder ).addClass('hiddenClass');
            $(statisticElements.instanceList ).empty();
            statistic.clear();
        },

        clearSelection : function(){
            $(trainingGroupElements.groupList ).find('.selected' ).removeClass('selected');
        }
    };

    var Users = {
        selectedUserId : 0,

        init:function(){
            this.selectUser();
        },

        viewUsers : function(){},

        selectUser: function(){
            $(userElements.userList).find('li.mbcholder').live('click', function () {
                $(this ).siblings().removeClass('selected' ).end();
                $(this ).addClass('selected');
                Trainings.clearSelection();
                TrainingGroups.clearInstances();
                statistic.clear();
                Users.selectedUserId = $(this ).attr('data-object-id');
                Users.listInstances( Users.selectedUserId );
            });
        },

        selectInstances : function(instanceId){
            var responseData = [];
            responseData['result'] = [];
            responseData['result'].push(tempObj.result[instanceId]);
            statistic.publishSummarized(responseData, true);
        },

        listInstances : function(id){
            tempObj = [];
            values = {};
            values['action'] = actions.instances[1];//'list',
            values['form'] = $(statisticElements.form).serializeArray();
            values['userid'] = id;
            settings.url = "/crawl?/process/statistic/handeluserstat/";
            settings.data = values;
            var data = getJsonData(settings);
            if (!data){
                TrainingGroups.clearInstances();
                return false;
            }

            tempObj = data;
            var instances = [];
            for (var inst in data.result)
                instances.push(inst);

            $(statisticElements.statisticHolder ).html('');
            for (var i=0, inst; inst=data.main[i]; i++) {
                Users.selectInstances(instances[i]);
            };
            return false;

            for (var i=0, inst; inst=data.main[i]; i++) {
                $(statisticElements.instanceList ).append('<li class=""><a href="#" data-id="'+instances[i]+'">'+inst.title+'</a></li>')
            };

            $(statisticElements.instanceHolder ).removeClass('hiddenClass');

            $(statisticElements.instanceList ).find('a' ).bind('click', function(e){
                e.preventDefault();
                $(this ).parent().siblings().removeClass('active' ).end();
                $(this ).parent().addClass('active');
                Users.selectInstances($(this ).attr('data-id'));
            })
        },

        clearInstances : function(){
            $(statisticElements.instanceHolder ).addClass('hiddenClass');
            $(statisticElements.instanceList ).empty();
            statistic.clear();
        },

        clearSelection : function(){
            $(userElements.userList ).find('.selected' ).removeClass('selected');
        }
    };


    Statistic.init = function (iframeurl) {

        $(trainingElements.trainingList).slimScroll({position: 'left', height: '300px', allowPageScroll: false, width: '190px'});
        $(trainingGroupElements.groupList).slimScroll({position: 'left', height: '300px', allowPageScroll: false, width: '190px'});
        $(userElements.userList).slimScroll({position: 'left', height: '300px', allowPageScroll: false, width: '190px'});

        for (var key in affixElements) {
            $(affixElements[key]).affix({
                offset: {
                    top: function () {
                        return $window.width() <= 1200 ? 150 : 180
                    },
                    bottom: 0
                }
            });
        }

        initAccordion();

        Trainings.init();
        TrainingGroups.init();
        Users.init();

    };

    this.initAccordion = function() {
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
    };

}(window.Statistic = window.Statistic || {}, jQuery));