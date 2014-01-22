$(document).ready(function() {
    var numRows = 0; // Количество строк в таблице

    $("#diagnosiss").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/medworkers/get',
        datatype: "json",
        colNames:['Код', 'Наименование', 'Тип персонала', 'Может обслуживать беременных', ''],
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
            },
            {
                name: 'medpersonal_type',
                index:'medpersonal_type',
                width: 150
            },
            {
                name: 'pregnants',
                index:'pregnants',
                width: 240
            },
            {
                name: 'is_for_pregnants',
                index: 'is_for_pregnants',
                width: 150,
                hidden: true
            },
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#diagnosissPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Специальности",
        height: 300,
        ondblClickRow: editDiagnosis
    });

    $("#diagnosiss").jqGrid('navGrid','#diagnosissPager',{
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

    $("#diagnosis-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editDiagnosisPopup').modal('hide');
            // Перезагружаем таблицу
            $("#diagnosiss").trigger("reloadGrid");
            $("#diagnosis-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddDiagnosisPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddDiagnosisPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }
            $('#errorAddDiagnosisPopup').modal({

            });
        }
    });

    function editDiagnosis() {
        var currentRow = $('#diagnosiss').jqGrid('getGridParam','selrow');
        var rowData = $('#diagnosiss').jqGrid('getRowData',currentRow);
        if(currentRow != null) {
            // Надо вынуть данные для редактирования: предпочтения конкретной специальности
            $.ajax({
                'url' : '/index.php/admin/diagnosis/getlikesanddistrib?id=' + rowData.id,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        var data = data.data;
                        var numRows = data.likes.length;
                        $.fn['diagnosisChooser'].clearAll();
                        $.fn['diagnosisDistribChooser'].clearAll();
                        $.fn['diagnosisDistribChooser'].addExtraParam('medworkerid', rowData.id);

                        for(var i = 0; i < numRows; i++) {
                            $.fn['diagnosisChooser'].addChoosed($('<li>').prop('id', 'r' + data.likes[i].id).text(data.likes[i].description), data.likes[i]);
                        }

                        var select = $('select#employeeId');
                        $(select).find('option:not([value="-1"])').remove();

                        numRows = data.employees.length;
                        for(var i = 0; i < numRows; i++) {
                            $(select).append($('<option>').prop({
                                'value' : data.employees[i].id
                            }).text(data.employees[i].last_name + ' ' + data.employees[i].first_name + ' ' + data.employees[i].middle_name + ' (' + data.employees[i].ward + ' отделение, ' + data.employees[i].enterprise + ')'));
                        }

                        $('#editPopup .spec').text(rowData.name);
                        $("#editPopup").modal({

                        });
                    }
                }
            });
        }
    }

    $('#employeeId').on('change', function(e) {
        if($(this).val() == '-1') {
            $('.first').addClass('no-display');
            return;
        }
        $.ajax({
            'url' : '/index.php/admin/diagnosis/getdistrib?employeeid=' + $(this).val(),
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    var numRows = data.data.length;
                    $('.first').removeClass('no-display');
                    $.fn['diagnosisDistribChooser'].clearAll();
                    for(var i = 0; i < numRows; i++) {
                       $.fn['diagnosisDistribChooser'].addChoosed($('<li>').prop('id', 'r' + data.data[i].id).text(data.data[i].description), data.data[i]);
                    }

                } else {

                }
            }
        });
    });

    $("#editPopup").on('hidden.bs.modal', function(e) {
        $('.first').addClass('no-display');
    });

    $("#editDiagnosis").click(editDiagnosis);

    $('#distribDiagnosisSubmit').click(function(e) {
        var choosed = $.fn['diagnosisDistribChooser'].getChoosed();
        var choosedIds = [];
        for(var i = 0; i < choosed.length; i++) {
            choosedIds.push(choosed[i].id)
        }
        var currentRow = $('#diagnosiss').jqGrid('getGridParam','selrow');
        var rowData = $('#diagnosiss').jqGrid('getRowData',currentRow);

        if(currentRow != null) {
            // Надо передать данные, которые были установлены в качестве любимых диагнозов..
            $.ajax({
                'url' : '/index.php/admin/diagnosis/setdistrib',
                'data' : {
                    'employee_id' :  $('#employeeId').val(),
                    'diagnosis_ids' : $.toJSON(choosedIds)
                },
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        $("#editPopup").modal('hide');
                    }
                }
            })
        }
    });

});
