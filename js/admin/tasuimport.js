$(document).ready(function() {
	$("#greetings").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/tasu/getbuffergreetings',
        datatype: "json",
        colNames:['Код', 'Врач', 'Медкарта', 'ФИО пациента', 'Дата приёма'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 50
            },
            {
                name: 'type',
                index:'type',
                width: 150
            },
            {
                name: 'guide',
                index:'guide',
                width: 150
            },
            {
                name: 'categorie',
                index:'categorie',
                width: 120
            },
            {
                name: 'label',
                index: 'label',
                width: 150
            }
        ],
        rowNum: 30,
        rowList:[10,20,30],
        pager: '#greetingsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Список приёмов для выгрузки",
        height: 300,
    });

    $("#greetings").jqGrid('navGrid','#greetingsPager',{
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
	
	$("#importHistory").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/tasu/getbuffergreetings',
        datatype: "json",
        colNames:['Дата', 'Количество выгруженных записей'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'type',
                index:'type',
                width: 250
            },
        ],
        rowNum: 30,
        rowList:[10,20,30],
        pager: '#importHistoryPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "История выгрузок",
        height: 200,
    });

    $("#importHistory").jqGrid('navGrid','#importHistoryPager',{
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
	
	$('#clearGreetings').on('click', function(e) {
	
	});
	
	$('#importGreetings').on('click', function(e) {
	
	});
});