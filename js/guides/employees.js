$(document).ready(function() {
    $("#employees").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/employees/get',
        datatype: "json",
        colNames:['Код',
                  'ФИО',
                  'Медработник',
                  'Табельный номер',
                  'Код списка контактов',
                  'Степень',
                  'Звание',
                  'Дата начала действия',
                  'Дата окончания действия',
                  'Отделение'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 60
            },
            {
                name: 'fio',
                index: 'fio',
                width: 200
            },
            {
                name: 'post',
                index:'post',
                width: 120
            },
            {
                name: 'tabel_number',
                index: 'tabel_number',
                width: 130
            },
            {
                name: 'contact_code',
                index: 'contact_code',
                width: 155
            },
            {
                name: 'degree',
                index: 'degree',
                width: 90
            },
            {
                name: 'titul',
                index: 'titul',
                width: 70
            },
            {
                name: 'date_begin',
                index: 'date_begin',
                width: 160
            },
            {
                name: 'date_end',
                index: 'date_end',
                width: 180
            },
            {
                name: 'ward',
                index: 'ward',
                width: 110
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#employeesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Сотрудники",
        height: 300,
        editurl:"someurl.php"
    });

    $("#employees").jqGrid('navGrid','#employeesPager',{
        edit: true,
        add: true,
        del: true
    });

    $("#addEmployee").click(function() {
        $('#addEmployeePopup').modal({

        });
    });

    // Инициализация дейтпикеров
    $('#dateBegin-cont').datetimepicker({
        format: 'yyyy-MM-dd hh:mm:ss'
    });
    $('#dateEnd-cont').datetimepicker({
        format: 'yyyy-MM-dd hh:mm:ss'
    });

    $("#employee-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addEmployeePopup').modal('hide');
            // Перезагружаем таблицу
            $("#employees").trigger("reloadGrid");
            $("#employee-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddEmployeePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddEmployeePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddEmployeePopup').modal({

            });
        }
    });

    $("#editEmployee").click(function() {

    });

    $("#deleteEmployee").click(function() {

    });
});
