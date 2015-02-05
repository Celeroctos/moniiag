$(document).ready(function() {
	function initGreetingsGrid(id, pagerId, title) {
		$(id).jqGrid({
			url: globalVariables.baseUrl + '/admin/tasu/getbuffermedcards',
			datatype: "json",
			colNames:['Код', '№ карты', 'ФИО пациента', 'Номер док-та', 'Полис', 'Дата выдачи', 'Статус полиса', 'Адрес', 'Страх. к-ния', 'Регион'],
            jsonReader: {
                repeatitems: false,
                id: "buffer_id"
            },
			colModel:[
				{
					name:'buffer_id',
					index:'buffer_id',
					width: 50
				},
				{
					name: 'medcard',
					index:'medcard',
					width: 85
				},
				{
					name: 'patient_fio',
					index:'patient_fio',
					width: 170
				},
				{
					name: 'docdata',
					index: 'docdata',
					width: 100
				},
				{
					name: 'oms_series_number',
					index: 'oms_series_number',
					width: 110
				},
				{
					name: 'givedate',
					index: 'givedate',
					width: 95
				},
				{
					name: 'status',
					index: 'status',
					width: 70
				},
				{
					name: 'address_str',
					index: 'address_str',
					width: 130
				},
				{
					name: 'insurance',
					index: 'insurance',
					width: 100
				},
                {
                    name: 'region',
                    index: 'region',
                    width: 100
                }
			],
			rowNum: 30,
			rowList:[10,20,30],
			pager: pagerId,
			sortname: 'buffer_id',
			viewrecords: true,
			sortorder: "desc",
			caption: title,
			height: 300
		});
		
		
		$(id).jqGrid('navGrid',pagerId,{
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
	
	}
	initGreetingsGrid("#medcards", '#medcardsPager', "Список медкарт для выгрузки");
	initGreetingsGrid("#historyMedcards", '#historyMedcardsPager', "Список выгруженных медкарт");

	$("#importHistory").jqGrid({
        url: globalVariables.baseUrl + '/admin/tasu/getbuffermedcardshistory',
        datatype: "json",
        colNames:['ID', 'Количество выгруженных записей', 'Дата создания', 'Статус', 'Лог', 'Отменить', ''],
        colModel:[
            {
                name: 'id',
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
			{
				name: 'log',
				index: 'log',
				width: 50
			},
			{
				name: 'cancel',
				index: 'cancel',
				width: 70
			},
			{
				name: 'import_id',
				index: 'import_id',
				hidden: true
			}
        ],
        rowNum: 30,
        rowList:[10,20,30],
        pager: '#importHistoryPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "История выгрузок",
        height: 200,
		ondblClickRow: showHistoryExport
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
	
	// Фикс для того, чтобы узнать информацию о столбце: нативно по одиночному клику такая информация не выводится
    $("#importHistory").click(function(e) {
        var el = e.target;
        if (el.nodeName !== "TD") {
            el = $(el, this.rows).closest("td");
        }
		if($(el).find('a').length == 0) {
			return false;
		}

        var iCol = $(el).index();
        var nCol = $(el).siblings().length;
        var row = $(el,this.rows).closest("tr.jqgrow");
        var rowId = row[0].id;
        if(iCol == 5) {
			if(!window.confirm('Вы действительно хотите отменить выгрузку?')) {
				return false;
			}
			$.ajax({
				'url' : '/admin/tasu/cancelimport',
				'cache' : false,
				'data' : {
					'bufferid' : $(el).find('a').prop('id').substr(1),
                    'type' : 1
				},
				'dataType' : 'json',
				'type' : 'GET',
				'success' : function(data, textStatus, jqXHR) {
					if(data.success) {
						alert('Выгрузка успешно отменена!');
						$("#medcards, #importHistory").trigger('reloadGrid');
					}
				}
			});
			return false;
		}
    });
	
	$('#clearMedcards').on('click', function(e) {
	    $('#confirmPopup').modal({});
	});

    $('#submitClearQueue').on('click', function(e) {
        $('#clearMedcards').attr({
            'disabled' : true
        }).text('Подождите, идёт очистка буфера...');
        $('#importMedcards').attr({
            'disabled' : true
        });

        $.ajax({
            'url' : '/admin/tasu/clearmedcardsbuffer',
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success) {
                    $('#clearMedcards').attr({
                        'disabled' : false
                    }).text('Очистить');
                    $('#importMedcards').attr({
                        'disabled' : false
                    });
                    $("#medcards").trigger("reloadGrid");
                }
            }
        });
    });

    $('#addAllMedcards').on('click', function(e) {
        $('#addAllMedcards').attr({
            'disabled' : true
        }).text('Формируется список медкарт...');
        $.ajax({
            'url' : '/admin/tasu/addallmedcards',
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success) {
                    $('#addAllMedcards').attr({
                        'disabled' : false
                    }).text('Добавить все медкарты');
                    $("#medcards").trigger("reloadGrid");
                }
            }
        });
    });
	
	$('#importMedcards').on('click', function(e) {
	    $(this).attr({
            'disabled' : true
        }).text('Совершается выгрузка...');
        $('#clearMedcards').attr({
            'disabled' : true
        });

        $('#importContainer').slideDown(500, function() {
            var currentMedcard = 0;
            var rowsPerQuery = 10;
            var totalMaked = 0; // Сколько уже обработано строк
            var totalRows = null;
            var isPaused = false;
			var numPatientsAdded = 0;
			var numDoctorsAdded = 0;
			var numAdded = 0;
			var numErrors = 0;
            function makeImport() {
                $.ajax({
                    'url' : '/admin/tasu/importmedcards',
                    'cache' : false,
                    'data' : {
                        currentMedcard : currentMedcard,
                        rowsPerQuery : rowsPerQuery,
                        totalMaked : totalMaked,
                        totalRows : totalRows
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
                                }).html(data.logs[i]));
                            }
                            // Выполнение такого условия означает, что количество строки подошли к концу
                            if(data.processed < rowsPerQuery) {
                                $('#importProgressbarP').prop({
                                    'aria-valuenow' : '100'
                                }).css('width', '100%');
								
                            } else {
                                var currentProzent = Math.floor((totalMaked / totalRows) * 100);
                                $('#importProgressbarP').prop({
                                    'style' : 'width: ' + currentProzent + '%',
                                    'aria-valuenow' : currentProzent
                                });
                            }
							
							currentMedcard = data.lastMedcardId;
							totalMaked += data.processed;

							$('.numStrings').text(totalMaked);
							if(totalRows == null) {
								totalRows = data.totalRows;
								$('.numStringsAll').text(totalRows);
							}

							if(data.hasOwnProperty('numAdded')) {
                                numAdded += parseInt(data.numAdded);
                                $('.numStringsAdded').text(numAdded);
                            }
							
							if(data.hasOwnProperty('numAddedPatients')) {
                                numPatientsAdded += parseInt(data.numAddedPatients);
                                $('.numPatientsAdded').text(numPatientsAdded);
                            }
							
							if(data.hasOwnProperty('numAddedDoctors')) {
                                numDoctorsAdded += parseInt(data.numAddedDoctors);
                                $('.numDoctorsAdded').text(numDoctorsAdded);
                            }

                            if(data.hasOwnProperty('numErrors')) {
                                numErrors += parseInt(data.numErrors);
                                $('.numStringsError').text(numErrors);
                            }
							
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
                        } else {
                            alert(data.error);
							$('#importContainer').slideUp(500, function(e) {
								$('#importMedcards').attr({
									'disabled' : false
								}).text('Выгрузить');
								$('#clearMedcards').attr({
									'disabled' : false
								});
								$('#importProgressbarP').prop({
                                    'aria-valuenow' : '0'
                                }).css('width', '0%');
							});
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
                var ul = $('.logWindow .list-group');
                $(ul).prepend($('<li>').prop({
                    'class' : 'list-group-item'
                }).html('<strong class="text-warning">[Выгрузка приостановлена пользователем]</strong>'));
            });

            $('.continueImport').on('click', function(e) {
                if($(this).attr('disabled')) {
                    return false;
                }
                $('.pauseImport').attr('disabled', false);
                $(this).attr('disabled', true);
                isPaused = false;
                var ul = $('.logWindow .list-group');
                $(ul).prepend($('<li>').prop({
                    'class' : 'list-group-item'
                }).html('<strong class="text-warning">[Выгрузка возобновлена пользователем]</strong>'));
                makeImport();
            });

            $('.successImport').on('click', function(e) {
                $('#importContainer').slideUp(500, function() {
					$('#importProgressbarP').prop({
						'aria-valuenow' : '0'
					}).css('width', '0%');
                    $("#medcards").trigger("reloadGrid");
                    $("#importHistory").trigger("reloadGrid");
                    $('.continueImport, .pauseImport').removeClass('no-display');
                    $('.successImport').addClass('no-display');
                    $('.logWindow .list-group li').remove();
                    $('#importMedcards').attr('disabled', false).text('Выгрузить');
                    $('#clearMedcards').attr({
                        'disabled' : false
                    });
                    totalRows = null;
                    $('.numStringsAll').text(0);
                    $('.numStrings').text(0);
					$('.numStringsAdded').text(0);
                    $('.numStringsDiscarded').text(0);
					$('.numStringsError').text(0);
                    $('.numDoctorsAdded').text(0);
					$('.numPatientsAdded').text(0);
                }).addClass('no-display');
            });
        }).removeClass('no-display');
	});

    $('#addMedcard').on('click', function(e) {
        $('#addMedcardsPopup').modal('show');
    });

    $('#deleteMedcard').on('click', function(e) {
        var currentRow = $('#medcards').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/admin/tasu/deletemedcardfrombuffer?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success) {
                        $("#medcards").trigger("reloadGrid");
                    } else {
                        $('#errorPopup .modal-body .row').html("<p>" + data.error + "</p>")
                        $('#errorPopup').modal({ });
                    }
                }
            })
        }
    });

    $("#medcard-add-form").on("success", function(e, ajaxData, textStatus, jqXHR) {
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


	function showHistoryExport() {
		var currentRow = $('#importHistory').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
			var row = $('#importHistory').jqGrid('getRowData', currentRow);
			$('#historyMedcards').jqGrid('setGridParam',
				{
					url: globalVariables.baseUrl + '/admin/tasu/getbuffermedcards?import_id=' + row.import_id,
					page: 1
				}
			);
			$("#historyMedcards").trigger('reloadGrid');
			$('#showHistoryMedcardsPopup').modal({});
		}
	}

    function getFilters() {
        var result = {
            'groupOp' : 'AND',
            'rules' : [
            ]
        };
        if($.trim($('#dateFrom').val()) != '') {
            result.rules.push({
                'field' : 'date_from',
                'op' : 'ge',
                'data' :  $('#dateFrom').val()
            });
        }
        if($.trim($('#dateTo').val()) != '') {
            result.rules.push({
                'field' : 'date_to',
                'op' : 'le',
                'data' :  $('#dateTo').val()
            });
        }
        return result;
    }

    $('#tasuimport-filter-btn').on('click', function(e) {
        $(this).trigger('begin');
        var filters = getFilters();
        if(!filters) {
            return false;
        }

        $.ajax({
            'url' : '/admin/tasu/searchmedcards/?filters=' + $.toJSON(filters),
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success) {
                    $('#addChoosedMedcardsBtn').addClass('no-display');
                    if(data.data.length > 0) {
                        displayAll(data.data);
                    } else {
                        $('#errorPopup .modal-body .row').html('<p class="errorText">Медкарт не найдено. Попробуйте изменить критерии поиска.</p>');
                    }
                } else {
                    $('#errorPopup .modal-body .row').html('<p class="errorText">' + data.data + '</p>');
                    $('#errorPopup').modal({
                    });
                }

                $('#tasuimport-filter-btn').trigger('end');
            }
        });
    });

    function displayAll(data) {
        // Заполняем пришедшими данными таблицу тех, кто без карт
        var table = $('#chooseMedcardsTable tbody');
        table.find('tr').remove();
        for(var i = 0; i < data.length; i++) {
            table.append(
                '<tr>' +
                    '<td>' +
                        '<input type="checkbox" id="r' + data[i].card_number + '" />' +
                    '</td>' +
                    '<td>' + data[i].card_number + '</td>' +
                    '<td>' + data[i].last_name + ' ' + data[i].first_name + ' ' + (data[i].middle_name ? data[i].middle_name : '') + '</td>' +
                    '<td>' + data[i].docdata + '</td>' +
                    '<td>' + data[i].birthday + '</td>' +
                    '<td>' + (data[i].oms_series_number ? data[i].oms_series_number : '') + '</td>' +
                    '<td>' + data[i].address_str + '</td>' +
                    '<td>' + data[i].status + '</td>' +
                    '<td>' + data[i].givedate + '</td>' +
                    '<td>' + (data[i].insurance_name ? data[i].insurance_name : '')+ '</td>' +
                    '<td>' + data[i].region + '</td>' +
                '</tr>'
            );
        }
    }

    $('#checkAll').on('change', function(e) {
       $('#chooseMedcardsTable input[id^=r]').prop('checked', $(this).prop('checked'));
    });

    $(document).on('change', '#chooseMedcardsTable input[id^=r]', function(e) {
        if($('#chooseMedcardsTable input[id^=r]:checked').length > 0) {
            $('#addChoosedMedcardsBtn').removeClass('no-display');
        } else {
            $('#addChoosedMedcardsBtn').addClass('no-display');
        }
    });

    $('#addChoosedMedcardsBtn').on('click', function(e) {
        var medcards = [];
        $('#chooseMedcardsTable input[id^=r]:checked').each(function(index, element) {
            medcards.push($(element).prop('id').substring(1));
            $(element).parents('tr').remove();
        });

        $.ajax({
            'url' : '/admin/tasu/addmedcards',
            'data' : {
                'medcards' : $.toJSON(medcards)
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success) {
                    $('#addMedcardsPopup').modal('hide');
                    $("#medcards").trigger('reloadGrid');
                } else {
                    $('#errorPopup .modal-body .row').html('<p class="errorText">' + data.data + '</p>');
                    $('#errorPopup').modal({
                    });
                }
            }
        });
    });
});