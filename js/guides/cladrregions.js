$(document).ready(function() {
    $("#regions").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/cladr/regionget',
        datatype: "json",
        colNames:['ID',
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
        pager: '#regionsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Районы",
        height: 600,
        ondblClickRow: editRegion
    });

    $("#regions").jqGrid('navGrid','#regionsPager',{
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

    $("#addRegion").click(function() {
        $('#addRegionPopup').modal({
        });
    });


    $("#region-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#addRegionPopup').modal('hide');
            // Перезагружаем таблицу
            $("#regions").trigger("reloadGrid");
            $("#region-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddRegionPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddRegionPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddRegionPopup').modal({
            });
        }
    });

    // Редактирование строки
    $("#region-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        if(Boolean(globalVariables.guideEdit) == false) {
            return false;
        }
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#editRegionPopup').modal('hide');
            // Перезагружаем таблицу
            $("#regions").trigger("reloadGrid");
            $("#region-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddRegionPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddRegionPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddRegionPopup').modal({
            });
        }
    });

    function editRegion() {
        var currentRow = $('#regions').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/cladr/regiongetone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editRegionPopup form')
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
                        $("#editRegionPopup").modal({
                        });
                    }
                }
            })
        }
    }


    $("#editRegion").click(editRegion);

    $("#deleteRegion").click(function() {
        var currentRow = $('#regions').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/cladr/regiondelete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#regions").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddRegionPopup .modal-body .row p').remove();
                        $('#errorAddRegionPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddRegionPopup').modal({

                        });
                    }
                }
            });
        }
    });
});
