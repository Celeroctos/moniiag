$(document).ready(function() {
    $("#analysistypes").jqGrid({
        url: globalVariables.baseUrl + '/guides/analysistypes/get',
        datatype: "json",
        colNames:['Код', 'Наименование', 'Тип учреждения', 'Правило создания номеров карт', ''],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'name',
                index: 'name',
                width: 200
            },
            {
                name: 'enterprise_name',
                index:'enterprise_name',
                width: 150
            },
            {
                name: 'rule',
                index: 'rule',
                width: 250
            },
            {
                name: 'rule_id',
                index: 'rule_d',
                hidden: true
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#analysistypesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Отделения",
        height: 300,
        ondblClickRow: editAnalysisType
    });

    $("#analysistypes").jqGrid('navGrid','#analysistypesPager',{
        edit: false,
        add: false,
        del: false
        },
        {},
        {},
        {},
        {
            closeOnEscape:true,
            multipleSearch :true,
            closeAfterSearch: true
        }
    );

    $("#addAnalysisType").click(function() {
        $('#addAnalysisTypePopup').modal({

        });
    });

    $("#analysistype-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addAnalysisTypePopup').modal('hide');
            // Перезагружаем таблицу
            $("#analysistypes").trigger("reloadGrid");
            $("#analysistype-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddAnalysisTypePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddAnalysisTypePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddAnalysisTypePopup').modal({

            });
        }
    });

    $("#analysistype-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        if(Boolean(globalVariables.guideEdit) == false) {
            return false;
        }
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editAnalysisTypePopup').modal('hide');
            // Перезагружаем таблицу
            $("#analysistypes").trigger("reloadGrid");
            $("#analysistype-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddAnalysisTypePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddAnalysisTypePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddAnalysisTypePopup').modal({

            });
        }
    });

    function editAnalysisType() {
        var currentRow = $('#analysistypes').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/analysistypes/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editAnalysisTypePopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'name',
                                formField: 'name'
                            },
                            {
                                modelField: 'enterprise_id',
                                formField: 'enterprise'
                            },
                            {
                                modelField: 'rule_id',
                                formField: 'ruleId'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editAnalysisTypePopup").modal({

                        });
                    }
                }
            })
        }
    }


    $("#editAnalysisType").click(editAnalysisType);

    $("#deleteAnalysisType").click(function() {
        var currentRow = $('#analysistypes').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            $.ajax({
                'url' : '/guides/analysistypes/issetDoctorPerAnalysisType?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success) {
                        if(data.doctors.length > 0) {
                            $('#noticeIssetDoctorPopup .listOfDoctors').html('');
                            for(var i in data.doctors) {
                                $('#noticeIssetDoctorPopup .listOfDoctors').append(
                                    $('<strong>').append(
                                        data.doctors[i].last_name + ' ' + data.doctors[i].first_name + ' ' + (data.doctors[i].middle_name ? data.doctors[i].middle_name : '') + ((data.doctors.length - 1 == i) ? '' : ', ')
                                    )
                                );
                            }
                            $('#noticeIssetDoctorPopup').modal();
                        } else {
                            $.ajax({
                                'url' : '/guides/analysistypes/delete?id=' + currentRow,
                                'cache' : false,
                                'dataType' : 'json',
                                'type' : 'GET',
                                'success' : function(data, textStatus, jqXHR) {
                                    if(data.success == 'true') {
                                        $("#analysistypes").trigger("reloadGrid");
                                    } else {
                                        // Удаляем предыдущие ошибки
                                        $('#errorAddAnalysisTypePopup .modal-body .row p').remove();
                                        $('#errorAddAnalysisTypePopup .modal-body .row').append("<p>" + data.error + "</p>")

                                        $('#errorAddAnalysisTypePopup').modal({

                                        });
                                    }
                                }
                            });
                        }
                    }
                }
            });
        }
    });
});
