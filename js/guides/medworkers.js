$(document).ready(function() {
    $("#medworkers").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/medworkers/get',
        datatype: "json",
        colNames:['Код', 'Наименование', 'Тип персонала'],
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
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#medworkersPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Медицинские работники",
        height: 300
    });

    $("#medworkers").jqGrid('navGrid','#wardsPager',{
        edit: true,
        add: true,
        del: true
    });
});
