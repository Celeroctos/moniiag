$(document).ready(function () {

    $("#diagnosiss").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/diagnosis/getclinical',
        datatype: "json",
        colNames: ['', 'Название'],
        colModel: [
            {
                name: 'id',
                index: 'id',
                hidden: true
            },
            {
                name: 'description',
                index: 'description',
                width: 500
            },
        ],
        rowNum: 10,
        rowList: [10, 20, 30],
        pager: '#diagnosissPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Клинические диагнозы",
        height: 300,
        ondblClickRow: editClinicalDiagnosisFn
    });

    $("#diagnosiss").jqGrid('navGrid', '#diagnosissPager', {
        edit: false,
        add: false,
        del: false
    },
        {},
        {},
        {},
        {
            closeOnEscape: true,
            multipleSearch: true,
            closeAfterSearch: true
        }
    );


        $("#clinic-add-form").on('success', function (eventObj, ajaxData, status, jqXHR) {
            var ajaxData = $.parseJSON(ajaxData);
            if (ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
                $('#addClinicalDiagnosisPopup').modal('hide');
                // Перезагружаем таблицу
                $("#diagnosiss").trigger("reloadGrid");
                $("#clinic-add-form")[0].reset(); // Сбрасываем форму
            } else {
                // Удаляем предыдущие ошибки
                $('#errorPopup .modal-body .row p').remove();
                // Вставляем новые
                for (var i in ajaxData.errors) {
                    for (var j = 0; j < ajaxData.errors[i].length; j++) {
                        $('#errorPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                    }
                }

                $('#errorPopup').modal({

            });
        }
    });


    $("#clinic-edit-form").on('success', function (eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if (ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editClinicalDiagnosisPopup').modal('hide');
            // Перезагружаем таблицу
            $("#diagnosiss").trigger("reloadGrid");
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddEmployeePopup .modal-body .row p').remove();
            // Вставляем новые
            for (var i in ajaxData.errors) {
                for (var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorPopup').modal({

        });
    }
});


    function editClinicalDiagnosisFn() {
        var currentRow = $('#diagnosiss').jqGrid('getGridParam', 'selrow');
        if (currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url': '/index.php/admin/diagnosis/getoneclinical?id=' + currentRow,
                'cache': false,
                'dataType': 'json',
                'type': 'GET',
                'success': function (data, textStatus, jqXHR) {
                    if (data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editClinicalDiagnosisPopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'description',
                                formField: 'description'
                            }
                        ];

                        for (var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }

                        $("#editClinicalDiagnosisPopup").modal({});
                    }
                }
            })
        }
    }

    function deleteClinicalDiagnosisFn() {
        var currentRow = $('#diagnosiss').jqGrid('getGridParam', 'selrow');
        if (currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url': '/index.php/admin/diagnosis/deleteclinical?id=' + currentRow,
                'cache': false,
                'dataType': 'json',
                'type': 'GET',
                'success': function (data, textStatus, jqXHR) {
                    if (data.success == true) {
                        $("#diagnosiss").trigger("reloadGrid");
                    }
                }
            })
        }
    }

    $('#addClinicalDiagnosis').on('click', function () {
        $('#addClinicalDiagnosisPopup').modal({});
    });

    $('#editClinicalDiagnosis').on('click', function () {
        editClinicalDiagnosisFn();
    });

    $('#deleteClinicalDiagnosis').on('click', function () {
        deleteClinicalDiagnosisFn();
    });

});
