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

                        numRows = data.distrib.length;
                        for(var i = 0; i < numRows; i++) {
                            $.fn['diagnosisDistribChooser'].addChoosed($('<li>').prop('id', 'r' + data.distrib[i].id).text(data.distrib[i].description), data.distrib[i]);
                        }

                        $('#editPopup .spec').text(rowData.name);
                        $("#editPopup").modal({

                        });
                    }
                }
            });
        }
    }

    $("#editDiagnosis").click(editDiagnosis);

    $('#distribDiagnosisSubmit').click(function(e) {
        var choosed = $.fn['diagnosisDistribChooser'].getChoosed();
        var currentRow = $('#diagnosiss').jqGrid('getGridParam','selrow');
        var rowData = $('#diagnosiss').jqGrid('getRowData',currentRow);

        if(currentRow != null) {
            // Надо передать данные, которые были установлены в качестве любимых диагнозов..
            $.ajax({
                'url' : '/index.php/admin/diagnosis/setdistrib',
                'data' : {
                    'medworker_id' : rowData.id,
                    'diagnosis_ids' : $.toJSON(choosed)
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
