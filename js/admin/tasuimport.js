$(document).ready(function() {
	function initGreetingsGrid(id, pagerId, title) {
		$(id).jqGrid({
			url: globalVariables.baseUrl + '/admin/tasu/getbuffergreetings',
			datatype: "json",
			colNames:['Код', '№ карты', 'ФИО пациента', 'Д-осн', 'Д-2', 'Дата', 'Врач', 'Статус', 'Проведён в МИС', 'Услуга'],
			colModel:[
				{
					name:'id',
					index:'id',
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
					name: 'pr_diag_code',
					index: 'pr_diag_code',
					width: 60
				},
				{
					name: 'sec_diag_codes',
					index: 'sec_diag_codes',
					width: 70
				},
				{
					name: 'patient_day',
					index: 'patient_day',
					width: 70
				},
				{
					name: 'doctor_fio',
					index:'doctor_fio',
					width: 150
				},
				{
					name: 'status',
					index: 'status',
					width: 70
				},
				{
					name: 'in_mis_desc',
					index: 'in_mis_desc',
					width: 130
				},
				{
					name: 'service',
					index: 'service',
					width: 100
				}
			],
			rowNum: 30,
			rowList:[10,20,30],
			pager: pagerId,
			sortname: 'id',
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
	initGreetingsGrid("#greetings", '#greetingsPager', "Список приёмов для выгрузки");
	initGreetingsGrid("#historyGreetings", '#historyGreetingsPager', "Список выгруженных приёмов");

	$("#importHistory").jqGrid({
        url: globalVariables.baseUrl + '/admin/tasu/getbufferhistorygreetings',
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
					'bufferid' : $(el).find('a').prop('id').substr(1)
				},
				'dataType' : 'json',
				'type' : 'GET',
				'success' : function(data, textStatus, jqXHR) {
					if(data.success) {
						alert('Выгрузка успешно отменена!');
						$("#greetings, #importHistory").trigger('reloadGrid');
					}
				}
			});
			return false;
		}
    });
	
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
            'url' : '/admin/tasu/clearbuffer',
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
            'url' : '/admin/tasu/addallgreetings',
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
                    'url' : '/admin/tasu/importgreetings',
                    'cache' : false,
                    'data' : {
                        currentGreeting : currentGreeting,
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
							
							currentGreeting = data.lastGreetingId;
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
								$('#importGreetings').attr({
									'disabled' : false
								}).text('Выгрузить');
								$('#clearGreetings').attr({
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

    $('#addGreeting').on('click', function(e) {
        $.ajax({
            'url' : '/admin/tasu/getnotbufferedgreetings',
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

    $('#addFakeGreeting').on('click', function(e) {
		$('#addFakePopup').modal({
			keyboard: false, // Sic!
			backdrop: 'static'
		});
    });

    $('#editGreeting').on('click', function(e) {

    });

    $('#deleteGreeting').on('click', function(e) {
        var currentRow = $('#greetings').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/admin/tasu/deletefrombuffer?id=' + currentRow,
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

		
	var greetingsTempBuffer = {};
	var lastId = 1;
	
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

	$("#greeting-addfake-submit").on('click', function(e) {
		var primaryDiagnosis = $.fn["primaryDiagnosisChooser"].getChoosed();

        if(typeof $('#doctorId').val() == 'undefined') {
            alert('Не выбран врач!');
            return false;
        }
		
		if(typeof $('#paymentType').val() == 'undefined') {
            alert('Не выбран тип оплаты!');
            return false;
        }

        if(primaryDiagnosis.length == 0) {
            alert('Не выбран первичный диагноз!');
			$('#primaryDiagnosis').focus();
            return false;
        }

        if($.trim($('#cardNumber').val()) == '') {
            alert('Не введён номер медкарты!');
			$('#cardNumber').focus();
            return false;
        }

        if($.trim($('#greetingDate').val()) == '') {
            alert('Не введена дата приёма!');
            return false;
        }
			
		if($.trim($('#serviceCode').val()) == '') {
            alert('Не выбрана услуга!');
            return false;
        }
		
		var secondaryDiagnosisIds = [];
		var secondaryDiagnosisChoosed = $.fn["secondaryDiagnosisChooser"].getChoosed();
		for(var i = 0; i < secondaryDiagnosisChoosed.length; i++) {
			secondaryDiagnosisIds.push(secondaryDiagnosisChoosed[i].id);
		}
		
		// Теперь добавляем в таблицу. Запрашиваем данные у базы, что за пациент и что за врач
		 $.ajax({
            'url' : '/admin/tasu/getfios',
            'data' : {
				'doctor_id' : $('#doctorId').val(),
				'card_number' : $.trim($('#cardNumber').val()),
				'greeting_date' : $.trim($('#greetingDate').val()),
				'pr_diagnosis_id' : primaryDiagnosis[0].id,
				's_diagnosis_ids' : secondaryDiagnosisIds,
				'service_id' : $('#serviceCode').val()
			},
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
				if(data.success) {
					var forAdd = {
						cardNumber : $.trim($('#cardNumber').val()),
						doctorId : $('#doctorId').val(),
						primaryDiagnosis : primaryDiagnosis[0].id,
						secondaryDiagnosis : secondaryDiagnosisIds,
						greetingDate : $.trim($('#greetingDate').val()),
						paymentType : $.trim($('#paymentType').val()),
						primaryDiagnosisData : primaryDiagnosis[0],
						secondaryDiagnosisData : secondaryDiagnosisChoosed,
						serviceCode : $.trim($('#serviceCode').val())
					};
					
					greetingsTempBuffer[(lastId).toString()] = forAdd;
					$('#preGreetings').addRowData((lastId).toString(), {
						'id' : lastId,
						'doctor_fio' : data.data.doctorFio, 
						'doctor_id' : $.trim($('#doctorId').val()),
						'medcard' : $.trim($('#cardNumber').val()),
						'patient_fio' : data.data.patientFio,
						'patient_day' : $.trim($('#greetingDate').val()).split('-').reverse().join('.'),
						'diagnosis_code' : data.data.pr_diagnosis_code,
						'secondary_diagnosis_codes' : data.data.s_diagnosis_codes,
						'payment_type' : $.trim($('#paymentType').val()),
						'service_id' : $.trim($('#serviceCode').val()),
						'service' : $('#serviceCode option[value="' + $.trim($('#serviceCode').val()) + '"]').text()
					});

					lastId++;
					resetAddFakeForm();
				} else {
					// Удаляем предыдущие ошибки
                    var popup = $('#errorPopup');
                    $(popup).find('.modal-body .row p').remove();
                    // Вставляем новые
                    for(var i in data.errors) {
                        for(var j = 0; j < data.errors[i].length; j++) {
                            $(popup).find(' .modal-body .row').append("<p class=\"errorText\">" + data.errors[i][j] + "</p>")
                        }
                    }

                    $(popup).css({
                        'z-index' : '1051'
                    }).modal({});
				}
			}
		});
	});
	
	function resetAddFakeForm() {
		$('#cardNumber').val('');
		/*$('#greetingDate').val('');
		$('#greetingDate-cont').find('.day, .month, .year').val('');
		$('#wardId').val(-1).trigger('change');*/
		$('#deletePreGreeting').removeClass('disabled');
		$('#fioCont').addClass('no-display').text('');
		
		if(!$('#savePrimaryDiag').prop('checked')) {
			$.fn["primaryDiagnosisChooser"].clearAll();
		}
		$.fn["secondaryDiagnosisChooser"].clearAll();
		// Сброс фокуса
		$('#cardNumber').focus();
	}

    $("#greeting-addfakeall-submit").on('click', function(e) {
		console.log(greetingsTempBuffer);
        $.ajax({
            'url' : '/admin/tasu/addfakegreetingtobuffer',
            'data' : {
				'form' : $.toJSON(greetingsTempBuffer),
			},
            'cache' : false,
            'dataType' : 'json',
            'type' : 'POST',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success) {
					$('#clearPreGreetings').trigger('click');
					
                    $('#addFakePopup').modal('hide');
					resetAddFakeForm();
					greetingsTempBuffer = {};
					
                    $("#greetings").trigger("reloadGrid");
                } else {
                   
                }
            }
        });

        return false;
    });
	
	$('#deletePreGreeting').on('click', function(e) {
		var currentRow = $('#preGreetings').jqGrid('getGridParam', 'selrow');
        if(currentRow != null) {
			$('#preGreetings').delRowData(currentRow);
			delete greetingsTempBuffer[currentRow];
		}
	});
	
	$('#clearPreGreetings').on('click', function(e) {
		for(var i in greetingsTempBuffer) {
			$('#preGreetings').delRowData(i);
		}
		greetingsTempBuffer = {};
		lastId = 1;
	});
	
	// Фильтр по отделению
	$('#wardId').on('change', function() {
		$(this).attr('disabled', true);
		$.ajax({
			'url' : '/guides/employees/getbyward?id=' + $(this).val(),
			'cache' : false,
			'dataType' : 'json',
			'success' : function(data, textStatus, jqXHR) {
				if(data.success) {
					$('#wardId').attr('disabled', false);
					$('#doctorId option[value!="-1"]').remove();
					for(var i = 0; i < data.data.length; i++) {
						$('#doctorId').append($('<option>').attr('value', data.data[i].id).text(data.data[i].last_name + ' ' + data.data[i].first_name + ' ' + (data.data[i].middle_name == null ? '' : data.data[i].middle_name + ', ' + data.data[i].ward + ', ' + data.data[i].post + ', табельный номер ' + data.data[i].tabel_number)));
					}
				} else {
				
				}
			}
		});
	});
	
	// Табличка пре-приёмов
	$("#preGreetings").jqGrid({
        datatype: "json",
        colNames:['ID', '№ карты', 'ФИО пациента', 'Д-осн', 'Д-2', 'Дата', 'Врач', 'Услуга', '', '', ''],
        colModel:[
			{
				name: 'id',
				index: 'id',
				width: 40
			},
			{
                name: 'medcard',
                index:'medcard',
                width: 90
            },
			{
                name: 'patient_fio',
                index:'patient_fio',
                width: 130
            },
			{
                name: 'diagnosis_code',
                index: 'diagnosis_code',
                width: 60
            },
			{
				name: 'secondary_diagnosis_codes',
				index: 'secondary_diagnosis_codes',
				width: 60
			},
            {
                name: 'patient_day',
                index: 'patient_day',
                width: 80
            },
			{
                name: 'doctor_fio',
                index:'doctor_fio',
                width: 100
            },
			{
				name: 'service',
				index: 'service',
				width: 60
			},
			{
				name: 'payment_type',
				index: 'payment_type',
				hidden: true
			},
			{
				name: 'doctor_id',
				index: 'doctor_id',
				hidden: true
			},
			{
				name: 'service_id',
				index: 'service_id',
				hidden: true
			}
        ],
        rowNum: 30,
        rowList:[10,20,30],
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Список добавляемых приёмов",
        height: 453,
		ondblClickRow: editPreGreeting
    });
	
	function editPreGreeting(rowid, iRow, iCol, e) {
		var rowData = $('#preGreetings').jqGrid('getRowData',rowid);
		$('#doctorIdEdit').val(rowData.doctor_id);
		$('#paymentTypeEdit').val(rowData.payment_type);
		$('#greetingDateEdit').val(rowData.patient_day.split('.').reverse().join('-')).trigger('change');
		$('#cardNumberEdit').val(rowData.medcard);
		$('#serviceCodeEdit').val(rowData.service_id);

		$.fn['primaryDiagnosisChooser2'].addChoosed($('<li>').prop('id', 'p' + greetingsTempBuffer[rowData.id].primaryDiagnosisData.id).text(greetingsTempBuffer[rowData.id].primaryDiagnosisData.description), greetingsTempBuffer[rowData.id].primaryDiagnosisData);
		var sDiagnosis = greetingsTempBuffer[rowData.id].secondaryDiagnosisData;
		for(var i = 0; i < sDiagnosis.length; i++) {
			$.fn['secondaryDiagnosisChooser2'].addChoosed($('<li>').prop('id', 'p' + sDiagnosis[i].id).text(sDiagnosis[i].description), sDiagnosis[i]);
		}
		
		getFioByCardNumber('cardNumberEdit');
		$('#editPregreetingsRowPopup').modal({
		});
	}

	// Suck. Place for optim.
	$('#saveEditPregreetingRow').on('click', function(e) {
		var currentRow = $('#preGreetings').jqGrid('getGridParam', 'selrow');
		var rowData = $('#preGreetings').jqGrid('getRowData', currentRow);
		
		var primaryDiagnosis = $.fn["primaryDiagnosisChooser2"].getChoosed();

        if(typeof $('#doctorIdEdit').val() == 'undefined') {
            alert('Не выбран врач!');
            return false;
        }
		
		if(typeof $('#paymentTypeEdit').val() == 'undefined') {
            alert('Не выбран тип оплаты!');
            return false;
        }

        if(primaryDiagnosis.length == 0) {
            alert('Не выбран первичный диагноз!');
			$('#primaryDiagnosisEdit').focus();
            return false;
        }

        if($.trim($('#cardNumberEdit').val()) == '') {
            alert('Не введён номер медкарты!');
			$('#cardNumber').focus();
            return false;
        }

        if($.trim($('#greetingDateEdit').val()) == '') {
            alert('Не введена дата приёма!');
            return false;
        }

		
		var secondaryDiagnosisIds = [];
		var secondaryDiagnosisChoosed = $.fn["secondaryDiagnosisChooser2"].getChoosed();
		for(var i = 0; i < secondaryDiagnosisChoosed.length; i++) {
			secondaryDiagnosisIds.push(secondaryDiagnosisChoosed[i].id);
		}
		
		// Теперь добавляем в таблицу. Запрашиваем данные у базы, что за пациент и что за врач
		$.ajax({
            'url' : '/admin/tasu/getfios',
            'data' : {
				'doctor_id' : $('#doctorIdEdit').val(),
				'card_number' : $.trim($('#cardNumberEdit').val()),
				'greeting_date' : $.trim($('#greetingDateEdit').val()),
				'pr_diagnosis_id' : primaryDiagnosis[0].id,
				's_diagnosis_ids' : secondaryDiagnosisIds
			},
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
				if(data.success) {
					var forAdd = {
						cardNumber : $.trim($('#cardNumberEdit').val()),
						doctorId : $('#doctorIdEdit').val(),
						primaryDiagnosis : primaryDiagnosis[0].id,
						secondaryDiagnosis : secondaryDiagnosisIds,
						greetingDate : $.trim($('#greetingDateEdit').val()),
						paymentType : $.trim($('#paymentTypeEdit').val()),
						primaryDiagnosisData : primaryDiagnosis[0],
						secondaryDiagnosisData : secondaryDiagnosisChoosed,
						serviceId : $.trim($('#serviceCodeEdit').val())
					};
					console.log(forAdd);
					greetingsTempBuffer[currentRow] = forAdd;
					
					$('#preGreetings').delRowData(currentRow);
					$('#preGreetings').addRowData(currentRow, {
						'id' : currentRow,
						'doctor_fio' : data.data.doctorFio, 
						'doctor_id' : $.trim($('#doctorIdEdit').val()),
						'medcard' : $.trim($('#cardNumberEdit').val()),
						'patient_fio' : data.data.patientFio,
						'patient_day' : $.trim($('#greetingDateEdit').val()).split('-').reverse().join('.'),
						'diagnosis_code' : data.data.pr_diagnosis_code,
						'secondary_diagnosis_codes' : data.data.s_diagnosis_codes,
						'payment_type' : $.trim($('#paymentTypeEdit').val()),
						'service_id' : $.trim($('#serviceCodeEdit').val()),
						'service' : $('#serviceCodeEdit option[value="' + $.trim($('#serviceCodeEdit').val()) + '"]').text()
					});
					
					$('#editPregreetingsRowPopup').modal('hide');
					$("#preGreetings").trigger('reloadGrid');
					
				} else {
					// Удаляем предыдущие ошибки
                    var popup = $('#errorPopup');
                    $(popup).find('.modal-body .row p').remove();
                    // Вставляем новые
                    for(var i in data.errors) {
                        for(var j = 0; j < data.errors[i].length; j++) {
                            $(popup).find(' .modal-body .row').append("<p class=\"errorText\">" + data.errors[i][j] + "</p>")
                        }
                    }

                    $(popup).css({
                        'z-index' : '1051'
                    }).modal({});
				}
			}
		});
		
	});
	
	$('#tasuimport-filter-btn').on('click', function() {
		var greetingDate = $('#filterGreetingDate').val(); 
		var doctorId = $('#filterDoctorId').val(); 
		var urlParams = '?doctor_id=' + doctorId;
		if($.trim(greetingDate) != '') {
			urlParams += '&date=' + greetingDate;
		}
		// Перезагружаем таблицу
		$('#greetings').jqGrid('setGridParam',
            {
                url: globalVariables.baseUrl + '/admin/tasu/getbuffergreetings' + urlParams,
                page: 1
			}
		);
		$("#greetings").trigger('reloadGrid');
	});
	
	function showHistoryExport() {
		var currentRow = $('#importHistory').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
			var row = $('#importHistory').jqGrid('getRowData', currentRow);
			$('#historyGreetings').jqGrid('setGridParam',
				{
					url: globalVariables.baseUrl + '/admin/tasu/getbuffergreetings?import_id=' + row.import_id,
					page: 1
				}
			);
			$("#historyGreetings").trigger('reloadGrid');
			$('#showHistoryGreetingPopup').modal({});
		}
	}
	
	$('#greetingDate-cont').on('changeDate', function(e){
        $('#greetingDate-cont #greetingDate').val(e.date.getFullYear() + '-' + (e.date.getMonth() + 1) + '-' + e.date.getDate());
	});
	// Первичная установка на текущую дату
	var currentDate = new Date();
	$('#greetingDate-cont #greetingDate').val(currentDate.getFullYear() + '-' + (currentDate.getMonth() + 1) + '-' + currentDate.getDate());
	
	// Зацикливаем беготню по форме
	$('#cardNumber, #primaryDiagnosis, #cardNumberEdit').on('keydown', function(e) {
		if(e.keyCode == 13 || e.keyCode == 9) {
			if(($(this).prop('id') == 'cardNumber' || $(this).prop('id') == 'cardNumberEdit') && $.trim($(this).val()) != '') { // Подгружать ФИО
				getFioByCardNumber($(this).prop('id'));
			} else {
				moveToNextInput(this);
			}
			e.stopPropagation();
		}
	});
	
	$('#cardNumber, #cardNumberEdit').on('blur', function(e) {
		if($.trim($(this).val()) != '') {
			getFioByCardNumber($(this).prop('id'));
		}
	});
	
	function getFioByCardNumber(fieldId) {
		$('#' + fieldId).prop('disabled', true);
		$.ajax({
			'url' : '/admin/tasu/getfio',
			'data' : {
				'card_number' : $.trim($('#' + fieldId).val())
			},
			'cache' : false,
			'dataType' : 'json',
			'type' : 'GET',
			'success' : function(data, textStatus, jqXHR) {
				if(data.success) {
					$('#' + fieldId).prop('disabled', false);
					if(fieldId == 'cardNumber') {
						$('#fioCont').removeClass('no-display').text(data.data.patientFio);
						moveToNextInput($('#cardNumber'));
					}
					if(fieldId == 'cardNumberEdit') {
						$('#fioContEdit').removeClass('no-display').text(data.data.patientFio);
					}
				} else {
					alert(data.error);
					$('#' + fieldId).prop('disabled', false);
					$('#' + fieldId).focus();
				}
			}
		});
	}
	
	function moveToNextInput(input) {
		var id = $(input).prop('id');
		if(id == 'cardNumber') {
			$('#savePrimaryDiag').focus();
		}
		if(id == 'savePrimaryDiag') {
			if($('#primaryDiagnosis').prop('disabled') != 'undefined') {
				$('#primaryDiagnosis').focus();
			} else {
				$('#secondaryDiagnosis').focus();
			}
		}
		if(id == 'primaryDiagnosis') {
			$('#secondaryDiagnosis').focus();
		}
		if(id == 'secondaryDiagnosis') {
			$('#greeting-addfake-submit').focus();
		}
	}
	
	$('#greeting-addfake-submit').on('keydown', function(e) {
		if(e.keyCode == 39) {
			$('#greeting-addfakeall-submit').focus();
		}
	});
	$('#greeting-addfakeall-submit').on('keydown', function(e) {
		if(e.keyCode == 37) {
			$('#greeting-addfake-submit').focus();
		}
		if(e.keyCode == 13) {
			$('#greeting-addfakeall-submit').trigger('click');
		}
	});
	
	
});