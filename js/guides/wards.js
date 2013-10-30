$(document).ready(function() {
    $("#wards").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/wards/get',
        datatype: "json",
        colNames:['Код', 'Наименование', 'Тип учреждения'],
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
                name: 'enterprise_name',
                index:'enterprise_name',
                width: 150
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#wardsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Отделения",
        height: 300
    });

    $("#wards").jqGrid('navGrid','#wardsPager',{
        edit: false,
        add: false,
        del: false
    });

    $("#addWard").click(function() {
        $('#addWardPopup').modal({

        });
    });

    $("#ward-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addWardPopup').modal('hide');
            // Перезагружаем таблицу
            $("#wards").trigger("reloadGrid");
            $("#ward-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddWardPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddWardPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddWardPopup').modal({

            });
        }
    });

    $("#editWard").click(function() {

    });

    $("#deleteWard").click(function() {

    });
});
