$(document).ready(function() {
	$("#greetings").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/tasu/getbuffergreetings',
        datatype: "json",
        colNames:['Код', 'Врач', 'Медкарта', 'ФИО пациента', 'Дата приёма', 'Статус'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 50
            },
            {
                name: 'doctor_fio',
                index:'doctor_fio',
                width: 150
            },
            {
                name: 'medcard',
                index:'medcard',
                width: 100
            },
            {
                name: 'patient_fio',
                index:'patient_fio',
                width: 170
            },
            {
                name: 'patient_day',
                index: 'patient_day',
                width: 150
            },
            {
                name: 'status',
                index: 'status',
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
        height: 300
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
        url: globalVariables.baseUrl + '/index.php/admin/tasu/getbufferhistorygreetings',
        datatype: "json",
        colNames:['Дата', 'Количество выгруженных записей', 'Дата создания', 'Статус'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'num_rows',
                index:'num_rows',
                width: 150
            },
            {
                name: 'create_date',
                index:'create_date',
                width: 150
            },
            {
                name: 'status',
                index:'status',
                width: 150
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
	    $('#confirmPopup').modal({});
	});

    $('#submitClearQueue').on('click', function(e) {
        $('#clearGreetings').attr({
            'disabled' : true
        }).text('Подождите, идёт очистка буфера...');
        $('#importGreetings').attr({
            'disabled' : true
        });

        $.ajax({
            'url' : '/index.php/admin/tasu/clearbuffer',
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success) {
                    $('#clearGreetings').attr({
                        'disabled' : false
                    }).text('Очистить');
                    $('#importGreetings').attr({
                        'disabled' : false
                    });
                    $("#greetings").trigger("reloadGrid");
                }
            }
        });
    });

    $('#addAllGreeting').on('click', function(e) {
        $('#addAllGreeting').attr({
            'disabled' : true
        }).text('Формируется список приёмов...');
        $.ajax({
            'url' : '/index.php/admin/tasu/addallgreetings',
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success) {
                    $('#addAllGreeting').attr({
                        'disabled' : false
                    }).text('Добавить все приёмы');
                    $("#greetings").trigger("reloadGrid");
                }
            }
        });
    });
	
	$('#importGreetings').on('click', function(e) {
	    $(this).attr({
            'disabled' : true
        }).text('Совершается выгрузка...');
        $('#clearGreetings').attr({
            'disabled' : true
        });

        $('#importContainer').slideDown(500, function() {
            var currentGreeting = 0;
            var rowsPerQuery = 50;
            var totalMaked = 0; // Сколько уже обработано строк
            var totalRows = null;
            var isPaused = false;
            function makeImport() {
                $.ajax({
                    'url' : '/index.php/admin/tasu/importgreetings',
                    'cache' : false,
                    'data' : {
                        currentGreeting : currentGreeting,
                        rowsPerQuery : rowsPerQuery,
                        totalMaked : totalMaked
                    },
                    'dataType' : 'json',
                    'type' : 'GET',
                    'success' : function(data, textStatus, jqXHR) {
                        if(data.success) {
                            data = data.data;
                            // Заполним лог выгрузки..
                            var ul = $('.logWindow .list-group');
                            for(var i = 0; i < data.logs.length; i++) {
                                $(ul).prepend($('<li>').prop({
                                    'class' : 'list-group-item'
                                }).text(data.logs[i]));
                            }
                            // Выполнение такого условия означает, что количество строки подошли к концу
                            if(data.processed < rowsPerQuery) {
                                if(totalRows == null) {
                                    totalRows = data.totalRows;
                                }
                                $('#importProgressbarP').prop({
                                    'style' : '100%',
                                    'aria-valuenow' : '100'
                                });
                            } else {
                                currentGreeting = data.lastGreetingId;
                                totalMaked += data.processed;
                                $('.numStrings').text(totalMaked);
                                if(totalRows == null) {
                                    totalRows = data.totalRows;
                                    $('.numStringsAll').text(totalRows);
                                }

                                var currentProzent = Math.floor((totalMaked / totalRows) * 100);
                                $('#importProgressbarP').prop({
                                    'style' : 'width: ' + currentProzent + '%',
                                    'aria-valuenow' : currentProzent
                                });

                                if(totalRows > totalMaked) {
                                    if(!isPaused) {
                                        makeImport();
                                    } else {
                                        return;
                                    }
                                } else {
                                    alert('Импорт в ТАСУ завершён!');
                                    $('.continueImport, .pauseImport').addClass('no-display');
                                    $('.successImport').removeClass('no-display');
                                }
                            }
                        }
                    }
                });
            }
            makeImport();

            $('.pauseImport').on('click', function(e) {
                if($(this).attr('disabled')) {
                    return false;
                }

                $('.continueImport').removeAttr('disabled');
                $(this).attr('disabled', true);
                isPaused = true;
            });

            $('.continueImport').on('click', function(e) {
                if($(this).attr('disabled')) {
                    return false;
                }
                $('.pauseImport').attr('disabled', false);
                $(this).attr('disabled', true);
                isPaused = false;
                makeImport();
            });

            $('.successImport').on('click', function(e) {
                $('#importContainer').slideUp(500, function() {
                    $("#greetings").trigger("reloadGrid");
                    $("#importHistory").trigger("reloadGrid");
                    $('.continueImport, .pauseImport').removeClass('no-display');
                    $('.successImport').addClass('no-display');
                    $('.logWindow .list-group li').remove();
                    $('#importGreetings').attr('disabled', false).text('Выгрузить');
                    $('#clearGreetings').attr({
                        'disabled' : false
                    });
                    totalRows = null;
                    $('.numStringsAll').text(0);
                }).addClass('no-display');
            });
        }).removeClass('no-display');
	});

    $('#addGreeting').on('click', function(e) {
        $.ajax({
            'url' : '/index.php/admin/tasu/getnotbufferedgreetings',
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success) {
                    var data = data.data;
                    var select = $('#addPopup #greetingId');
                    $(select).find('option').remove();
                    for(var i in data) {
                        $(select).append($('<option>').prop({
                            'value' : i
                        }).text(data[i]));
                    }
                    $('#addPopup').modal('show');
                } else {

                }
            }
        });
    });

    $('#editGreeting').on('click', function(e) {

    });

    $('#deleteGreeting').on('click', function(e) {
        var currentRow = $('#greetings').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/tasu/deletefrombuffer?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success) {
                        $("#greetings").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorPopup .modal-body .row p').remove();
                        $('#errorPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorPopup').modal({
                        });
                    }
                }
            })
        }
    });

    $("#greeting-add-form").on("success", function(e, ajaxData, textStatus, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addPopup').modal('hide');
            // Перезагружаем таблицу
            $("#greetings").trigger("reloadGrid");
        } else {
            // Удаляем предыдущие ошибки
            $('#errorPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorPopup').modal({

            });
        }
    });
});