$(document).ready(function() {
    $("#insurances").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/insurances/get',
        datatype: "json",
        colNames:['Код', 'Наименование'],
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
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#insurancesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Страховые компании",
        height: 300,
        ondblClickRow: editInsurance
    });

    $("#medworkers").jqGrid('navGrid','#insurancesPager',{
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


    $("#insurance-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addInsurancePopup').modal('hide');
            // Перезагружаем таблицу
            $("#insurances").trigger("reloadGrid");
            $("#insurance-add-form")[0].reset(); // Сбрасываем форму
        } else {

            // Удаляем предыдущие ошибки
            $('#errorAddInsurancePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddInsurancePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddInsurancePopup').modal({

            });
        }
    });



    $("#insurance-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editInsurancePopup').modal('hide');
            // Перезагружаем таблицу
            $("#insurances").trigger("reloadGrid");
            $("#insurance-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddInsurancePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddInsurancePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddInsurancePopup').modal({

            });
        }
    });

    $("#addInsurance").click(function() {
        $('#addInsurancePopup').modal({
        });
    });

    $("#editInsurance").click(editInsurance);

    function editInsurance() {
        var currentRow = $('#insurances').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/insurances/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editInsurancePopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'name',
                                formField: 'name'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editInsurancePopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#deleteInsurance").click(function() {
        var currentRow = $('#insurances').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/insurances/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#insurances").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddInsurancePopup .modal-body .row p').remove();
                        $('#errorAddInsurancePopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddInsurancePopup').modal({

                        });
                    }
                }
            })
        }
    });

});