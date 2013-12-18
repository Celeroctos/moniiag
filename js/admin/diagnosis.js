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

    $("#diagnosisList").jqGrid({
        datatype: "json",
        colNames:['', 'Диагноз'],
        colModel:[
            {
                name:'id',
                index:'id',
                hidden: true
            },
            {
                name: 'description',
                index: 'description',
                width: 560
            }
        ],
        rowNum: 10,
        rowList:[10, 20, 30],
        pager: '#diagnosisListPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Диагнозы в списке",
        height: 150,
        ondblClickRow: deleteFromLikeDiagnosis
    });

    function deleteFromLikeDiagnosis(rowid, iRow, iCol, e) {
        $('#diagnosisList').jqGrid('delRowData', rowid);
        --numRows;
    }

    $("#diagnosisList").jqGrid('navGrid','#diagnosisListPager',{
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
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/diagnosis/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        var form = $('#editDiagnosisPopup form');
                        var rowData = $('#diagnosiss').jqGrid('getRowData',currentRow);
                        numRows = data.data.length;

                        for(var i = 0; i < data.data.length; i++) {
                            $('#diagnosisList').jqGrid('addRowData', i, {'id' : data.data[i].id, 'description' : data.data[i].description});
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

    });
});
