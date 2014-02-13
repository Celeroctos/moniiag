$(document).ready(function() {
    $("#logs").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/logs/get',
        datatype: "json",
        colNames:['Дата', 'Пользователь', 'Дополнительные данные', 'Действие'],
        colModel:[
            {
                name:'date',
                index:'date',
                width: 150
            },
            {
                name: 'user',
                index:'user',
                width: 250
            },
            {
                name: 'description',
                index: 'description',
                width: 250
            },
            {
                name: 'action',
                index: 'action',
                hidden: true
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#logsPager',
        sortname: 'date',
        viewrecords: true,
        sortorder: "desc",
        caption: "Логи",
        height: 300
    });

    $("#logs").jqGrid('navGrid','#logsPager',{
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
});