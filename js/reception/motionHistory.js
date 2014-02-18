$(document).ready(function() {
   $("#motion-history").jqGrid({
        datatype: "local",
        colNames:['Дата', 'Номер кабинета', 'ФИО врача', 'Номер карты'],
        colModel:[
            {
                name:'greeting_timestamp',
                index:'greeting_timestamp',
                width: 150
            },
            {
                name: 'cab_number',
                index: 'cab_number',
                width: 200
            },
            {
                name: 'doctor_name',
                index:'fio',
                width: 150
            },
            {
                name: 'medcard_id',
                index:'medcard_id',
                width: 150
            },
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#motion-historyPager',
        sortname: 'greeting_timestamp',
        viewrecords: true,
        sortorder: "desc",
        height: 300
    });

   $("#motion-history").jqGrid('navGrid', '#motion-historyPager',{
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