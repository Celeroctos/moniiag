$(document).ready(function(e) {
    var config = getConfig();

    $('.progressBox').each(function(index, element) {
        var current = 0;
        var rowsPerQuery = 500;
        var totalMaked = 0; // Сколько уже обработано строк
        var totalRows = null;
        var isPaused = false;
        var numAdded = 0;
        var numErrors = 0;
        var currentConfig = config.hasOwnProperty('#' + $(element).attr('id')) ? config['#' + $(element).attr('id')] : {};

        $(element).on('begin', function(e) {
            if($(element).hasClass('no-display')) {
                $(element).slideDown(500, function(e) {
                    $(element).removeClass('no-display');
					var beginFrom = $(element).parent().find('.beginFrom');
					if($(beginFrom).length > 0) {
						if(typeof parseInt($(beginFrom).val()) == 'number') {
							totalMaked = parseInt($(beginFrom).val());
						}

						$(beginFrom).attr('disabled', true);
					}

					$(element).trigger('process');
                });
            }
        });

        $(element).on('process', function(e) {
            var data =  {
                current : current,
                rowsPerQuery : rowsPerQuery,
                totalMaked : totalMaked,
                totalRows : totalRows == null ? 0 : totalRows
            };
            data = currentConfig.hasOwnProperty('extraParams') ? data + currentConfig.extraParams : data;

            $.ajax({
                'url' : currentConfig.url,
                'cache' : false,
                'data' : data,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success) {
                        data = data.data;

                        // Выполнение такого условия означает, что количество строки подошли к концу
                        if(data.processed < rowsPerQuery) {
                            if(totalRows == null) {
                                totalRows = data.totalRows;
                            }
                            $(element).find('.progress-bar').prop({
                                'style' : 'width: 100%',
                                'aria-valuenow' : '100'
                            });
                        } else {
                            current = data.lastId;
                            totalMaked += data.processed;
							if($(element).parent().find('.beginFrom').length > 0) {
								$(element).parent().find('.beginFrom').val(totalMaked);
							}
							
                            $(element).find('.numStrings').text(totalMaked);
                            if(totalRows == null) {
                                totalRows = parseInt(data.totalRows);
                                $(element).find('.numStringsAll').text(totalRows);
                            }

                            if(data.hasOwnProperty('numAdded')) {
                                numAdded += parseInt(data.numAdded);
                                $('.numStringsAdded').text(numAdded);
                            }

                            if(data.hasOwnProperty('numErrors')) {
                                numErrors += parseInt(data.numErrors);
                                $('.numStringsError').text(numErrors);
                            }

                            var currentProzent = Math.floor((totalMaked / totalRows) * 100);
                            $(element).find('.progress-bar').prop({
                                'style' : 'width: ' + currentProzent + '%',
                                'aria-valuenow' : currentProzent
                            });

                            if(totalRows > totalMaked) {
                                if(!isPaused) {
                                    $(element).trigger('process');
                                } else {
                                    return;
                                }
                            } else {
                                alert(currentConfig.successMsg);
                                if(currentConfig.hasOwnProperty('successFunc')) {
                                    currentConfig.successFunc();
                                }
								current = 0;
								totalMaked = 0;
								totalRows = 0;
                                $(element).trigger('end');
                            }
                        }
                    } else {
                        alert(data.data.error);
                        $(element).find('.successImport').trigger('click');
                        return false;
                    }
                }
            });
        });

        $(element).on('end', function(e) {
            $(element).find('.continueImport, .pauseImport').addClass('no-display');
            $(element).find('.successImport').removeClass('no-display');
			var beginFrom = $(element).parent().find('.beginFrom');
			if($(beginFrom).length > 0) {
				$(beginFrom).attr('disabled', false);
				$(beginFrom).find('.beginFrom').val(0);
			}
        });

        $(element).find('.pauseImport').on('click', function(e) {
            if($(this).attr('disabled')) {
                return false;
            }

			if($(element).parent().find('.beginFrom').length > 0) {
				$(element).parent().find('.beginFrom').attr('disabled', false);
			}
			
			
            $(element).find('.continueImport').removeAttr('disabled');
            $(this).attr('disabled', true);
            isPaused = true;
        });

        $(element).find('.continueImport').on('click', function(e) {
            if($(this).attr('disabled')) {
                return false;
            }
			var beginFrom = $(element).parent().find('.beginFrom');
			if($(beginFrom).length > 0) {
				if(typeof parseInt($(beginFrom).val()) == 'number') {
					totalMaked = parseInt($(beginFrom).val());
				}

				$(beginFrom).attr('disabled', true);
			}
            $(element).find('.pauseImport').attr('disabled', false);
            $(this).attr('disabled', true);
            isPaused = false;
            $(element).trigger('process');
        });

        $(element).find('.successImport').on('click', function(e) {
            totalRows = null;
			$(element).find('.progress-bar').prop({
				'style' : 'width: 0%',
				'aria-valuenow' : '0'
			});
            $(element).find('.numStringsAll').text(0);
            $(element).find('.numStrings').text(0);
            $(element).slideUp(500, function(e) {
                $(element).addClass('no-display');
                if($(element).parent().find('.syncBtn').length != 0) {
                    $(element).parent().find('.syncBtn').attr({
                        'disabled' : false,
                        'value' : 'Синхронизировать'
                    });
                }
            });
        });
    });

    function getConfig() {
        var config = {
            '#syncMedservices' : {
                'url' : '/guides/service/syncwithtasu',
                'successMsg' : 'Синхронизация с ТАСУ завершена!',
                'successFunc' : function() {

                }
            },
            '#syncLands' : {
                'url' : '/guides/cladr/synclands',
                'successMsg' : 'Синхронизация с ТАСУ завершена!',
                'successFunc' : function() {

                }
            },
            '#syncRegions' : {
                'url' : '/guides/cladr/syncregions',
                'successMsg' : 'Синхронизация с ТАСУ завершена!',
                'successFunc' : function() {

                }
            },
            '#syncDistricts' : {
                'url' : '/guides/cladr/syncdistricts',
                'successMsg' : 'Синхронизация с ТАСУ завершена!',
                'successFunc' : function() {

                }
            },
            '#syncSettlements' : {
                'url' : '/guides/cladr/syncsettlements',
                'successMsg' : 'Синхронизация с ТАСУ завершена!',
                'successFunc' : function() {

                }
            },
            '#syncStreets' : {
                'url' : '/guides/cladr/syncstreets',
                'successMsg' : 'Синхронизация с ТАСУ завершена!',
                'successFunc' : function() {

                }
            },
            '#syncPatients' : {
                'url' : '/admin/tasu/syncpatients',
                'successMsg' : 'Синхронизация с ТАСУ завершена!',
                'successFunc' : function() {

                }
            },
            '#syncDoctors' : {
                'url' : '/admin/tasu/syncdoctors',
                'successMsg' : 'Синхронизация с ТАСУ завершена!',
                'successFunc' : function() {

                }
            },
			'#syncOms' : {
                'url' : '/admin/tasu/syncoms',
                'successMsg' : 'Синхронизация с ТАСУ-ОМС завершена!',
                'successFunc' : function() {

                }
            },
			'#syncInsurances' : {
                'url' : '/admin/tasu/syncinsurances',
                'successMsg' : 'Синхронизация с ТАСУ завершена!',
                'successFunc' : function() {

                }
            },
			'#genPoliciesOnlySymbols' : {
				'url' : '/admin/tasu/createomssearchfield',
                'successMsg' : 'Создание поисковых полей завершено!',
                'successFunc' : function() {

                }
			}
        };
        return config;
    }
});