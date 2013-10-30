$(document).ready(function() {
    $("#enterprises").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/enterprises/get',
        datatype: "json",
        colNames:['Код', 'Наименование', 'Тип учреждения', 'Реквизиты'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'fullname',
                index: 'fullname',
                width: 200
            },
            {
                name: 'enterprise_type',
                index:'enterprise_type',
                width: 150
            },
            {
                name: 'requisits',
                index: 'requisits',
                width: 400,
                align: 'left'
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#enterprisesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Список учреждений",
        height: 300
    });

    $("#enterprises").jqGrid('navGrid','#enterprisesPager',{
        edit: false,
        add: false,
        del: false
    });


    $("#addEnterprise").click(function() {
        $('#addEnterprisePopup').modal({

        });
    });

    $("#enterprise-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addEnterprisePopup').modal('hide');
            // Перезагружаем таблицу
            $("#enterprises").trigger("reloadGrid");
            $("#enterprise-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddEnterprisePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddEnterprisePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddEnterprisePopup').modal({

            });
        }
    });

    $("#editEnterprise").click(function() {

    });

    $("#deleteEnterprise").click(function() {

    });
});
