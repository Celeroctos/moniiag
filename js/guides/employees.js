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
                  'Код отделения'],
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
                width: 70
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
        height: 300
    });

    $("#employees").jqGrid('navGrid','#employeesPager',{
        edit: true,
        add: true,
        del: true
    });
});
