$(document).ready(function() {
    $("#payments").jqGrid({
        url: globalVariables.baseUrl + '/guides/payments/get',
        datatype: "json",
        colNames:['Код',
                  'Название',
                  'Строка для ТАСУ'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 60
            },
            {
                name: 'name',
                index: 'name',
                width: 150
            },
            {
                name: 'tasu_string',
                index: 'tasu_string',
                width: 150
            },
			{
				name: 'is_default_desc',
				index: 'is_default_desc',
				width: 70
			}
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#paymentsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Типы оплат",
        height: 300,
        ondblClickRow: editPayment
    });

    $("#payments").jqGrid('navGrid','#paymentsPager',{
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

    $("#addPayment").click(function() {
        $('#addPaymentPopup').modal({

        });
    });


    $("#payment-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#addPaymentPopup').modal('hide');
            // Перезагружаем таблицу
            $("#payments").trigger("reloadGrid");
            $("#payment-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddPaymentPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddPaymentPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddPaymentPopup').modal({

            });
        }
    });

    // Редактирование строки
    $("#payment-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#editPaymentPopup').modal('hide');
            // Перезагружаем таблицу
            $("#payments").trigger("reloadGrid");
            $("#payment-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddPaymentPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddPaymentPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddPaymentPopup').modal({

            });
        }
    });

    function editPayment() {
        var currentRow = $('#payments').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/payments/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editPaymentPopup form')
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
                                modelField: 'tasu_string',
                                formField: 'tasuString'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editPaymentPopup").modal({
                        });
                    }
                }
            })
        }
    }


    $("#editPayment").click(editPayment);

    $("#deletePayment").click(function() {
        var currentRow = $('#payments').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/payments/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#payments").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddPaymentPopup .modal-body .row p').remove();
                        $('#errorAddPaymentPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddPaymentPopup').modal({

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
                'url' : '/guides/wards/getbyenterprise?id=' + enterpriseCode,
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
