$(document).ready(function() {
    var numRows = 0; // Количество строк в таблице

    $("#diagnosiss").jqGrid({
        url: globalVariables.baseUrl + '/guides/medworkers/get',
        datatype: "json",
        colNames:['Код', 'Наименование', 'Тип персонала', '', ''],
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
                width: 240,
                hidden: true
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
                'url' : '/admin/diagnosis/getlikes?id=' + rowData.id,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        var numRows = data.data.length;
                        $.fn['diagnosisChooser'].clearAll();

                        for(var i = 0; i < numRows; i++) {
                            $.fn['diagnosisChooser'].addChoosed($('<li>').prop('id', 'r' + data.data[i].id).text(data.data[i].description), data.data[i]);
                        }
                        $('#editPopup .spec').text(rowData.name);
                        $("#editPopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#editDiagnosis").click(editDiagnosis);

    $('#likeDiagnosisSubmit').click(function(e) {
        var choosed = $.fn['diagnosisChooser'].getChoosed();
        var choosedArr = [];
        for(var i = 0; i < choosed.length; i++) {
            choosedArr.push(choosed[i].id);
        }
        var currentRow = $('#diagnosiss').jqGrid('getGridParam','selrow');
        var rowData = $('#diagnosiss').jqGrid('getRowData',currentRow);

        if(currentRow != null) {
            // Надо передать данные, которые были установлены в качестве любимых диагнозов..
            $.ajax({
                'url' : '/admin/diagnosis/setlikes',
                'data' : {
                   'medworker_id' : rowData.id,
                   'diagnosis_ids' : $.toJSON(choosedArr)
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
