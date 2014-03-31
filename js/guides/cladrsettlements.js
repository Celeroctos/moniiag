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
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
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
});
