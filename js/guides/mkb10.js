$(document).ready(function() {
    $("#mkb10").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/mkb10/get?nodeid=0',
        datatype: "json",
        colNames:['Код', 'Описание', 'Родитель'],
        colModel:[
            {
                name:'id',
                index:'id',
                key: true,
                hidden: true
            },
            {
                name: 'description',
                index: 'description',
                width: 1000
            },
            {
                name: 'parent',
                index: 'parent',
                hidden: true
            }
        ],
        rowNum: 30,
        rowList:[10,20,30],
        pager: '#mkb10Pager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "asc",
        caption:"Справочник диагнозов",
        height: 350,
        treeGrid: true,
        treeGridModel: 'adjacency',
        ExpandColumn: 'description',
        ExpandColClick: true,
        ondblClickRow: moveToLikeDiagnoses
    });

    function moveToLikeDiagnoses(rowid, iRow, iCol, e) {
        var diagnosisList = $('#diagnosisList')
        if(typeof $(diagnosisList).attr('id') != 'undefined') {
            var rowData = $('#mkb10').jqGrid('getRowData', rowid);
            if(rowData.isLeaf == 'true') {
                // Если строка с таким диагнозом уже существует, добавлять не надо
                var checkedRow = $(diagnosisList).jqGrid('getRowData', rowid);
                if(!checkedRow.hasOwnProperty('id')) {
                    $(diagnosisList).jqGrid('addRowData', rowid, rowData);
                }
            }
        }
    }

    $("#mkb10").jqGrid('navGrid','#mkb10Pager',{
        edit: false,
        add: false,
        del: false
    });
});
