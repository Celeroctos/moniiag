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

    $("#editEnterprise").click(function() {

    });

    $("#deleteEnterprise").click(function() {

    });
});
