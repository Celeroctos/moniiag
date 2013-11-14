$(document).ready(function() {
    $("#cabinets").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/cabinets/get',
        datatype: "json",
        colNames:['Код',
                  'Учреждение',
                  'Отделение',
                  'Номер',
                  'Описание',
                  '',
                  ''],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 60
            },
            {
                name: 'enterprise',
                index: 'enterprise',
                width: 150
            },
            {
                name: 'ward',
                index: 'ward',
                width: 150
            },
            {
                name: 'cab_number',
                index: 'cab_number',
                width: 70
            },
            {
                name: 'description',
                index: 'description',
                width: 150
            },
            {
                name: 'enterprise_id',
                index: 'enterprise_id',
                hidden: true
            },
            {
                name: 'ward_id',
                index: 'ward_id',
                hidden: true
            },
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#cabinetsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Кабинеты",
        height: 300,
        editurl:"someurl.php",
        ondblClickRow: editCabinet
    });

    $("#cabinets").jqGrid('navGrid','#cabinetsPager',{
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

    $("#addCabinet").click(function() {
        $('#addCabinetPopup').modal({

        });
    });


    $("#cabinet-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#addCabinetPopup').modal('hide');
            // Перезагружаем таблицу
            $("#cabinets").trigger("reloadGrid");
            $("#cabinet-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddCabinetPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddCabinetPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddCabinetPopup').modal({

            });
        }
    });

    // Редактирование строки
    $("#cabinet-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#editCabinetPopup').modal('hide');
            // Перезагружаем таблицу
            $("#cabinets").trigger("reloadGrid");
            $("#cabinet-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddCabinetPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddCabinetPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddCabinetPopup').modal({

            });
        }
    });

    function editCabinet() {
        var currentRow = $('#cabinets').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/cabinets/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editCabinetPopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'enterprise_id',
                                formField: 'enterpriseId'
                            },
                            {
                                modelField: 'ward_id',
                                formField: 'wardId'
                            },
                            {
                                modelField: 'cab_number',
                                formField: 'cabNumber'
                            },
                            {
                                modelField: 'description',
                                formField: 'description'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            console.log(fields[i].formField);
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editCabinetPopup").modal({

                        });
                    }
                }
            })
        }
    }


    $("#editCabinet").click(editCabinet);

    $("#deleteCabinet").click(function() {
        var currentRow = $('#cabinets').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/cabinets/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#cabinets").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddCabinetPopup .modal-body .row p').remove();
                        $('#errorAddCabinetPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddCabinetPopup').modal({

                        });
                    }
                }
            })
        }
    });

    // Форма добавления кабинета: подгрузка отделений учреждения
    $("form #enterpriseId").on('change', function(e) {
        var enterpriseCode = $(this).val();
        if(enterpriseCode != -1) { // В том случае, если это не "Нет учреждения", подгрузим отделения его..
            $.ajax({
                'url' : '/index.php/guides/wards/getbyenterprise?id=' + enterpriseCode,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        $("#wardId option[value != -1]").remove(); // Удалить все, кроме отсутствующего
                        $("#wardId").val('-1'); // По дефолту - Нет
                        // Заполняем из пришедших данных
                        for(var i = 0; i < data.data.length; i++) {
                            $("#wardId").append('<option value="' + data.data[i].id + '">' + data.data[i].name + '</option>')
                        }
                        $("#wardId").attr('disabled', false);
                    }
                }
            });
        } else {
            $("#wardId option[value != -1]").remove(); // Удалить все, кроме отсутствующего
            $("#wardId").val('-1'); // По дефолту - Нет
            $("#wardId").attr('disabled', true);
        }
    });
});
