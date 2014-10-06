$(document).ready(function() {
    $("#mkb10").jqGrid({
        url: globalVariables.baseUrl + '/guides/mkb10/get?nodeid=0',
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
        var rowData = $('#mkb10').jqGrid('getRowData', rowid);
        if(rowData.isLeaf == 'true') {
            var chooser = $('#diagnosisChooser');
            if($(chooser).length > 0) {
                $.fn['diagnosisChooser'].addChoosed($('<li>').prop('id', 'r' + rowData.id).text(rowData.description), rowData);
            }
        }
    }

    $("#mkb10").jqGrid('navGrid','#mkb10Pager',{
        edit: false,
        add: false,
        del: false
    });
});
