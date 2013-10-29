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
        edit: true,
        add: true,
        del: true
    });
});
