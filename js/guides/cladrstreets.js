$(document).ready(function() {
    $("#streets").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/cladr/streetget',
        datatype: "json",
        colNames:['ID',
                  'Регион',
                  'Район',
                  'Населённый пункт',
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
                name: 'settlement',
                index: 'settlement',
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
        pager: '#streetsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Районы",
        height: 600,
        ondblClickRow: editStreet
    });

    $("#streets").jqGrid('navGrid','#streetsPager',{
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

    $("#addStreet").click(function() {
        $('#addStreetPopup').modal({
        });
    });


    $("#street-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#addStreetPopup').modal('hide');
            // Перезагружаем таблицу
            $("#streets").trigger("reloadGrid");
            $("#street-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddStreetPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddStreetPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddStreetPopup').modal({
            });
        }
    });

    // Редактирование строки
    $("#street-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        if(Boolean(globalVariables.guideEdit) == false) {
            return false;
        }
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#editStreetPopup').modal('hide');
            // Перезагружаем таблицу
            $("#streets").trigger("reloadGrid");
            $("#street-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddStreetPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddStreetPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddStreetPopup').modal({
            });
        }
    });

    function editStreet() {
        var currentRow = $('#streets').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/cladr/streetgetone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editStreetPopup form')
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
                            },
                            {
                                modelField: 'code_settlement',
                                formField: 'codeSettlement'
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
                            if(fields[i].formField == 'codeSettlement') {
                                $.fn['settlementChooser2'].clearAll();
                                $.fn['settlementChooser2'].addChoosed($('<li>').prop('id', 'r' + data.data['settlement_id']).text(data.data['settlement']), {
                                    'id' : data.data['settlement_id'],
                                    'code_cladr' : data.data['code_settlement'],
                                    'name' : data.data['settlement']
                                });
                                continue;
                            }
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editStreetPopup").modal({
                        });
                    }
                }
            })
        }
    }


    $("#editStreet").click(editStreet);

    $("#deleteStreet").click(function() {
        var currentRow = $('#streets').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/cladr/streetdelete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#streets").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddStreetPopup .modal-body .row p').remove();
                        $('#errorAddStreetPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddStreetPopup').modal({

                        });
                    }
                }
            });
        }
    });

    $("#street-edit-form").on('beforesend', function(eventObj, settings, jqXHR) {
            if($.fn["regionChooser2"].getChoosed().length == 0) {
                alert('Не выбран регион!');
                return false;
            }
           /* if($.fn["districtChooser2"].getChoosed().length == 0) {
                alert('Не выбран район!');
                return false;
            }
            if($.fn["settlementChooser2"].getChoosed().length == 0) {
                alert('Не выбран населённый пункт!');
                return false;
            }*/
        var district = '';
        var settlement = '';

            var region = $.fn["regionChooser2"].getChoosed()[0].code_cladr;

            if ($.fn["districtChooser2"].getChoosed().length>0)
            {
                district = $.fn["districtChooser2"].getChoosed()[0].code_cladr;
            }
            if ($.fn["settlementChooser2"].getChoosed().length>0)
            {
                settlement = $.fn["settlementChooser2"].getChoosed()[0].code_cladr;
            }
            strData =  'FormCladrStreetAdd[name]=' + $("#editStreetPopup #name").val()  + '&FormCladrStreetAdd[codeCladr]=' + $("#editStreetPopup #codeCladr").val() + '&FormCladrStreetAdd[codeRegion]=' + region + '&FormCladrStreetAdd[codeDistrict]=' + district + '&FormCladrStreetAdd[id]=' + $("#editStreetPopup #id").val() + '&FormCladrStreetAdd[codeSettlement]=' + settlement;
            settings.data = strData;
    });

    $("#street-add-form").on('beforesend', function(eventObj, settings, jqXHR) {
            if($.fn["regionChooserForStreet"].getChoosed().length == 0) {
                alert('Не выбран регион!');
                return false;
            }
           /* if($.fn["districtChooserForStreet"].getChoosed().length == 0) {
                alert('Не выбран район!');
                return false;
            }
            if($.fn["settlementChooserForStreet"].getChoosed().length == 0) {
                alert('Не выбран населённый пункт!');
                return false;
            }*/

            var district = '';
            var settlement = '';

            var region = $.fn["regionChooserForStreet"].getChoosed()[0].code_cladr;

            if ($.fn["districtChooserForStreet"].getChoosed().length>0)
            {
                district = $.fn["districtChooserForStreet"].getChoosed()[0].code_cladr;
            }

            if ($.fn["settlementChooserForStreet"].getChoosed().length>0)
            {
                settlement = $.fn["settlementChooserForStreet"].getChoosed()[0].code_cladr;
            }
            var strData =  'FormCladrStreetAdd[name]=' + $("#addStreetPopup #name").val() + '&FormCladrStreetAdd[codeCladr]=' + $("#addStreetPopup #codeCladr").val() + '&FormCladrStreetAdd[codeRegion]=' + region + '&FormCladrStreetAdd[codeDistrict]=' + district + '&FormCladrStreetAdd[id]=' + $("#addStreetPopup #id").val() + '&FormCladrStreetAdd[codeSettlement]=' + settlement;
            settings.data = strData;
    });

});
