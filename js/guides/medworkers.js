$(document).ready(function() {
    $("#medworkers").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/medworkers/get',
        datatype: "json",
        colNames:['Код', 'Наименование', 'Тип персонала', 'Тип оплаты', 'Принимает беременных', 'Меддолжность', '', '', ''],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'name',
                index: 'name',
                width: 200,
            },
            {
                name: 'medpersonal_type',
                index:'medpersonal_type',
                width: 140
            },
            {
                name: 'payment_type_desc',
                index:'payment_type_desc',
                width: 140
            },
            {
                name: 'pregnants',
                index:'pregnants',
                width: 220
            },
            {
                name: 'is_medworker_desc',
                index: 'is_medworker_desc',
                width: 110
            },
            {
                name: 'is_for_pregnants',
                index: 'is_for_pregnants',
                width: 150,
                hidden: true
            },
            {
                name: 'payment_type',
                index:'payment_type',
                hidden: true
            },
            {
                name: 'is_medworker',
                index: 'is_medworker',
                hidden: true
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#medworkersPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Медицинские работники",
        height: 300,
        ondblClickRow: editMedworker
    });

    $("#medworkers").jqGrid('navGrid','#medworkersPager',{
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

    $("#addMedworker").click(function() {
        $('#addMedworkerPopup').modal({

        });
    });

    $("#medworker-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addMedworkerPopup').modal('hide');
            // Перезагружаем таблицу
            $("#medworkers").trigger("reloadGrid");
            $("#medworker-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddMedworkerPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddMedworkerPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddMedworkerPopup').modal({

            });
        }
    });

    $("#medworker-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editMedworkerPopup').modal('hide');
            // Перезагружаем таблицу
            $("#medworkers").trigger("reloadGrid");
            $("#medworker-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddMedworkerPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddMedworkerPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddMedworkerPopup').modal({

            });
        }
    });

    function editMedworker() {
        if(Boolean(globalVariables.guideEdit) == false) {
            return false;
        }
        var currentRow = $('#medworkers').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/medworkers/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editMedworkerPopup form')
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
                                modelField: 'type',
                                formField: 'type'
                            },
                            {
                                modelField: 'is_for_pregnants',
                                formField: 'isForPregnants'
                            },
                            {
                                modelField: 'payment_type',
                                formField: 'paymentType'
                            },
                            {
                                modelField: 'is_medworker',
                                formField: 'isMedworker'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        
                        // Вытаскиваем список разрешённых шаблонов
                        
                        // Проставим шаблоны
                        form.find('input[type="checkbox"]').prop('checked', false); // Сбрасываем все галочки
                        var templates = data.data.templates;
                        for(var i = 0; i < templates.length; i++) {
                            form.find('input[name="template' + templates[i] + '"]').prop('checked', true);
                        }
                        
                        $("#editMedworkerPopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#editMedworker").click(editMedworker);

    $("#deleteMedworker").click(function() {
        var currentRow = $('#medworkers').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/medworkers/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#medworkers").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddMedworkerPopup .modal-body .row p').remove();
                        $('#errorAddMedworkerPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddMedworkerPopup').modal({

                        });
                    }
                }
            })
        }
    });
});
