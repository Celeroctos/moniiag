$(document).ready(function() {
    $("#settlements").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/cladr/settlementget',
        datatype: "json",
        colNames:['ID',
                  'Регион',
                  'Район',
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
                name: 'district',
                index: 'district',
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
        pager: '#settlementsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Районы",
        height: 600,
        ondblClickRow: editSettlement
    });

    $("#settlements").jqGrid('navGrid','#settlementsPager',{
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

    $("#addSettlement").click(function() {
        $('#addSettlementPopup').modal({
        });
    });


    $("#settlement-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#addSettlementPopup').modal('hide');
            // Перезагружаем таблицу
            $("#settlements").trigger("reloadGrid");
            $.fn['regionChooser'].clearAll();
            $.fn['regionChooser'].enable();
            $.fn['districtChooser'].clearAll();
            $.fn['districtChooser'].enable();
            $("#settlement-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddSettlementPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddSettlementPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddSettlementPopup').modal({
            });
        }
    });

    // Редактирование строки
    $("#settlement-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        if(Boolean(globalVariables.guideEdit) == false) {
            return false;
        }
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#editSettlementPopup').modal('hide');
            // Перезагружаем таблицу
            $("#settlements").trigger("reloadGrid");
            $.fn['regionChooser2'].clearAll();
            $.fn['regionChooser2'].enable();
            $.fn['districtChooser2'].clearAll();
            $.fn['districtChooser2'].enable();
            $("#settlement-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddSettlementPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddSettlementPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddSettlementPopup').modal({
            });
        }
    });

    function editSettlement() {
        var currentRow = $('#settlements').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/cladr/settlementgetone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editSettlementPopup form')
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
                            },
                            {
                                modelField: 'code_district',
                                formField: 'codeDistrict'
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
                            if(fields[i].formField == 'codeDistrict') {
                                $.fn['districtChooser2'].clearAll();
                                $.fn['districtChooser2'].addChoosed($('<li>').prop('id', 'r' + data.data['district_id']).text(data.data['district']), {
                                    'id' : data.data['district_id'],
                                    'code_cladr' : data.data['code_district'],
                                    'name' : data.data['district']
                                });
                                continue;
                            }
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editSettlementPopup").modal({
                        });
                    }
                }
            })
        }
    }


    $("#editSettlement").click(editSettlement);

    $("#deleteSettlement").click(function() {
        var currentRow = $('#settlements').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/cladr/settlementdelete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#settlements").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddSettlementPopup .modal-body .row p').remove();
                        $('#errorAddSettlementPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddSettlementPopup').modal({

                        });
                    }
                }
            });
        }
    });

    $("#settlement-add-form, #settlement-edit-form").on('beforesend', function(eventObj, settings, jqXHR) {
        if($(this).prop('id') == 'settlement-add-form') {
            if($.fn["regionChooser"].getChoosed().length == 0) {
                alert('Не выбран регион!');
                return false;
            }
            if($.fn["districtChooser"].getChoosed().length == 0) {
                alert('Не выбран район!');
                return false;
            }
            var region = $.fn["regionChooser"].getChoosed()[0].code_cladr;
            var district = $.fn["districtChooser"].getChoosed()[0].code_cladr;
            var strData =  'FormCladrSettlementAdd[name]=' + $("#addSettlementPopup #name").val() + '&FormCladrSettlementAdd[codeCladr]=' + $("#addSettlementPopup #codeCladr").val() + '&FormCladrSettlementAdd[codeRegion]=' + region + '&FormCladrSettlementAdd[codeDistrict]=' + district + '&FormCladrSettlementAdd[id]=' + $("#addSettlementPopup #id").val();
        } else {
            if($.fn["regionChooser2"].getChoosed().length == 0) {
                alert('Не выбран регион!');
                return false;
            }
            if($.fn["districtChooser2"].getChoosed().length == 0) {
                alert('Не выбран район!');
                return false;
            }
            var region = $.fn["regionChooser2"].getChoosed()[0].code_cladr;
            var district = $.fn["districtChooser2"].getChoosed()[0].code_cladr;
            var strData =  'FormCladrSettlementAdd[name]=' + $("#editSettlementPopup #name").val() + '&FormCladrSettlementAdd[codeCladr]=' + $("#editSettlementPopup #codeCladr").val() + '&FormCladrSettlementAdd[codeRegion]=' + region + '&FormCladrSettlementAdd[codeDistrict]=' + district + '&FormCladrSettlementAdd[id]=' + $("#editSettlementPopup #id").val();
        }
        settings.data = strData;
    });
});
