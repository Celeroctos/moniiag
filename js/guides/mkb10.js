$(document).ready(function() {
    $("#mkb10").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/mkb10/get?nodeid=0',
        datatype: "json",
        colNames:['Код', 'Описание'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 50,
                key: true,
                hidden: true
            },
            {
                name: 'description',
                index: 'name',
                width: 1000
            }
        ],
        rowNum: 30,
        rowList:[10,20,30],
        pager: '#mkb10Pager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "asc",
        caption:"Справочник диагнозов",
        height: 'auto',
        treeGrid: true,
        treeGridModel: 'adjacency',
        ExpandColumn: 'description',
        ExpandColClick: true
});

    $("#mkb10").jqGrid('navGrid','#mkb10Pager',{
        edit: false,
        add: false,
        del: false
    });
});
