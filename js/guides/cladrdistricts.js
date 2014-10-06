$(document).ready(function() {
    $("#districts").jqGrid({
        url: globalVariables.baseUrl + '/guides/cladr/districtget',
        datatype: "json",
        colNames:['ID',
                  'Регион',
                  'Код КЛАДР',
                  'Название'
                  ],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 60
            },
            {
                name: 'region',
                index: 'region',
                width: 150
            },
            {
                name: 'code_cladr',
                index: 'code_cladr',
                width: 150
            },
            {
                name: 'name',
                index: 'name',
                width: 200
            }
        ],
        rowNum: 30,
        rowList:[10,20,30],
        pager: '#districtsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Районы",
        height: 600,
        ondblClickRow: editDistrict
    });

    $("#districts").jqGrid('navGrid','#districtsPager',{
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

    $("#addDistrict").click(function() {
        $('#addDistrictPopup').modal({
        });
    });


    $("#district-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#addDistrictPopup').modal('hide');
            // Перезагружаем таблицу
            $("#districts").trigger("reloadGrid");
            $.fn['regionChooser'].clearAll();
            $.fn['regionChooser'].enable();
            $("#district-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddDistrictPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddDistrictPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddDistrictPopup').modal({
            });
        }
    });

    // Редактирование строки
    $("#district-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        if(Boolean(globalVariables.guideEdit) == false) {
            return false;
        }
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#editDistrictPopup').modal('hide');
            // Перезагружаем таблицу
            $("#districts").trigger("reloadGrid");
            $.fn['regionChooser2'].clearAll();
            $.fn['regionChooser2'].enable();
            $("#district-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddDistrictPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddDistrictPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddDistrictPopup').modal({
            });
        }
    });

    function editDistrict() {
        var currentRow = $('#districts').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/cladr/districtgetone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editDistrictPopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'code_cladr',
                                formField: 'codeCladr'
                            },
                            {
                                modelField: 'name',
                                formField: 'name'
                            },
                            {
                                modelField: 'code_region',
                                formField: 'codeRegion'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            if(fields[i].formField == 'codeRegion') {
                                $.fn['regionChooser2'].clearAll();
                                $.fn['regionChooser2'].addChoosed($('<li>').prop('id', 'r' + data.data['region_id']).text(data.data['region']), {
                                    'id' : data.data['region_id'],
                                    'code_cladr' : data.data['code_region'],
                                    'name' : data.data['region']
                                });
                                continue;
                            }
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editDistrictPopup").modal({
                        });
                    }
                }
            })
        }
    }


    $("#editDistrict").click(editDistrict);

    $("#deleteDistrict").click(function() {
        var currentRow = $('#districts').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/cladr/districtdelete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#districts").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddDistrictPopup .modal-body .row p').remove();
                        $('#errorAddDistrictPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddDistrictPopup').modal({

                        });
                    }
                }
            });
        }
    });

    $("#district-edit-form").on('beforesend', function(eventObj, settings, jqXHR) {
            if($.fn["regionChooser2"].getChoosed().length == 0) {
                alert('Не выбран регион!');
                return false;
            }
            var region = $.fn["regionChooser2"].getChoosed()[0].code_cladr;
            var strData =  'FormCladrDistrictAdd[name]=' + $("#editDistrictPopup #name").val() + '&FormCladrDistrictAdd[codeCladr]=' + $("#editDistrictPopup #codeCladr").val() + '&FormCladrDistrictAdd[codeRegion]=' + region + '&FormCladrDistrictAdd[id]=' + $("#editDistrictPopup #id").val();
            settings.data = strData;
    });

    $("#district-add-form").on('beforesend', function(eventObj, settings, jqXHR) {

            if($.fn["regionChooserForDistrict"].getChoosed().length == 0) {
                alert('Не выбран регион!');
                return false;
            }
            var region = $.fn["regionChooserForDistrict"].getChoosed()[0].code_cladr;
            var strData =  'FormCladrDistrictAdd[name]=' + $("#addDistrictPopup #name").val() + '&FormCladrDistrictAdd[codeCladr]=' + $("#addDistrictPopup #codeCladr").val() + '&FormCladrDistrictAdd[codeRegion]=' + region;

        settings.data = strData;
    });

});
