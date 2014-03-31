$(document).ready(function(e) {
    var config = getConfig();

    $('.progressBox').each(function(index, element) {
        var current = 0;
        var rowsPerQuery = 50;
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
                    $(element).trigger('process');
                });
            }
        });

        $(element).on('process', function(e) {
            var data =  {
                current : current,
                rowsPerQuery : rowsPerQuery,
                totalMaked : totalMaked,
                totalRows : totalRows
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
                                'style' : '100%',
                                'aria-valuenow' : '100'
                            });
                        } else {
                            current = data.lastId;
                            totalMaked += data.processed;
                            $(element).find('.numStrings').text(totalMaked);
                            if(totalRows == null) {
                                totalRows = data.totalRows;
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
        });

        $(element).find('.pauseImport').on('click', function(e) {
            if($(this).attr('disabled')) {
                return false;
            }

            $(element).find('.continueImport').removeAttr('disabled');
            $(this).attr('disabled', true);
            isPaused = true;
        });

        $(element).find('.continueImport').on('click', function(e) {
            if($(this).attr('disabled')) {
                return false;
            }
            $(element).find('.pauseImport').attr('disabled', false);
            $(this).attr('disabled', true);
            isPaused = false;
            $(element).trigger('process');
        });

        $(element).find('.successImport').on('click', function(e) {
            totalRows = null;
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
                'url' : '/index.php/guides/service/syncwithtasu',
                'successMsg' : 'Синхронизация с ТАСУ завершена!',
                'successFunc' : function() {

                }
            },
            '#syncLands' : {
                'url' : '/index.php/guides/cladr/synclands',
                'successMsg' : 'Синхронизация с ТАСУ завершена!',
                'successFunc' : function() {

                }
            },
            '#syncRegions' : {
                'url' : '/index.php/guides/cladr/syncregions',
                'successMsg' : 'Синхронизация с ТАСУ завершена!',
                'successFunc' : function() {

                }
            },
            '#syncDistricts' : {
                'url' : '/index.php/guides/cladr/syncdistricts',
                'successMsg' : 'Синхронизация с ТАСУ завершена!',
                'successFunc' : function() {

                }
            },
            '#syncSettlements' : {
                'url' : '/index.php/guides/cladr/syncsettlements',
                'successMsg' : 'Синхронизация с ТАСУ завершена!',
                'successFunc' : function() {

                }
            },
            '#syncStreets' : {
                'url' : '/index.php/guides/cladr/syncstreets',
                'successMsg' : 'Синхронизация с ТАСУ завершена!',
                'successFunc' : function() {

                }
            }
        };
        return config;
    }
});