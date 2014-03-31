$(document).ready(function() {
    var currentFile = null;
    $('#tableList').change(function() {
        if($(this).val() == -1) {
            $('#tableFields, #tableKey').parent().addClass('no-display');
            return;
        }

        $('.submitImport input').removeClass('disabled');
        $('#tableFields').trigger('update');
        $('#tableKey').trigger('update');
        $('.fieldsBox').removeClass('no-display');
        $('.keyBox').removeClass('no-display');
        $('#keyTemplatesList').trigger('reload');
        $('#fieldTemplatesList').trigger('reload');
    });

    $('#filesList').on('click', 'a.fileName', function() {
        $(this).parents('tbody').find('tr').removeClass('success');
        $(this).parents('tr').addClass('success');
        currentFile = $(this).text();
        $('#chooseTable').removeClass('no-display');
        $('#tableList').removeAttr('disabled');
        $('h4.fileName').text('Файл ' + currentFile);
        // Вызываем апдейт трёх комбо
        if($('#tableList').val() != -1) {
            $('#tableFields').trigger('update');
            $('#tableKey').trigger('update');
            $('.fieldsBox').removeClass('no-display');
            $('.keyBox').removeClass('no-display');
        }
    });

    $('#tasuIn').on('afterUpload', function() {
        $.ajax({
            'url' : '/index.php/admin/tasu/reloadfileslist',
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == 'true' || data.success == true) {
                    var data = data.data;
                    $('#chooseTable').addClass('no-display');
                    $('#filesList').find('tbody tr').remove();
                    for(var i = 0; i < data.length; i++) {
                        var td1 = $('<td>').append($('<a>').prop({
                            'href' : '#i' + data[i].id
                        }).text(data[i].realName));
                        var td2 = $('<td>').append($('<img>').prop({
                            'src' : globalVariables.baseUrl + '/images/icons/' + data[i].icon,
                            'width' : 24,
                            'height' : 24,
                            'title' : 'CSV',
                            'alt' : 'CSV'
                        }));
                        $('#filesList tbody').append($('<tr>').append(td1, td2));
                    }
                } else {

                }
            }
        });
    });

    $('.saveAsKey').on('click', function() {
        // Переносим все поля
        var dbFields = $('#tableKey .dbField');
        var jsonResult = [];
        console.log(dbFields);
        for(var i = 0; i < dbFields.length; i++) {
            jsonResult.push({
                'dbField' : $(dbFields[i]).val()
            });
        }
        console.log(jsonResult);
        $('#addKeyTemplate input[id="template"]').val($.toJSON(jsonResult));
        $('#addKeyTemplate input[id="table"]').val($('#tableList').val());
        $('#addKeyTemplate').modal({});
    });
    $('.saveAsTemplate').on('click', function() {
        var dbFields = $('#tableFields .dbField');
        var tasuFields = $('#tableFields .tasuField');
        var jsonResult = [];
        for(var i = 0; i < dbFields.length; i++) {
            jsonResult.push({
                'dbField' : $(dbFields[i]).val(),
                'fileField' : $(tasuFields[i]).val()
            });
        }
        $('#addFieldTemplate input[id="template"]').val($.toJSON(jsonResult));
        $('#addFieldTemplate input[id="table"]').val($('#tableList').val());
        $('#addFieldTemplate').modal({});
    });


    $("#field-template-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) {
            $('#addFieldTemplate').modal('hide');
            // Перезагружаем таблицу
            $('#fieldTemplatesList').trigger('reload');
            $("#field-template-add-form")[0].reset();
        } else {
            addErrors(ajaxData.errors);
        }
    });

    $("#key-template-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) {
            $('#addKeyTemplate').modal('hide');
            // Перезагружаем таблицу
            $('#keyTemplatesList').trigger('reload');
            $("#key-template-add-form")[0].reset();
        } else {
            addErrors(ajaxData.errors);
        }
    });

    function addErrors(errors) {
        // Удаляем предыдущие ошибки
        $('#errorPopup .modal-body .row p').remove();
        // Вставляем новые
        for(var i in errors) {
            for(var j = 0; j < errors[i].length; j++) {
                $('#errorPopup .modal-body .row').append("<p>" + errors[i][j] + "</p>")
            }
        }

        $('#errorPopup').modal({

        });
    }

    $('#keyTemplatesList').on('reload', function() {
        $.ajax({
            'url' : '/index.php/admin/tasu/getkeystemplates',
            'data' : {
                'table' : $('#tableList').val()
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == 'true' || data.success == true) {
                    updateList('keyTemplatesList', data.data, 'keysTemplatesName');
                } else {

                }
            }
        });
    });

    $('#fieldTemplatesList').on('reload', function() {
        $.ajax({
            'url' : '/index.php/admin/tasu/getfieldstemplates',
            'data' : {
                'table' : $('#tableList').val()
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == 'true' || data.success == true) {
                    updateList('fieldTemplatesList', data.data, 'fieldsTemplatesName');
                } else {

                }
            }
        });
    });

    function updateList(listId, data, className) {
        $('#' + listId).find('tbody tr').remove();
        for(var i = 0; i < data.length; i++) {
            $('#' + listId).find('tbody').append($('<tr>').append(
                $('<td>').append(
                    $('<a>').prop({
                        'class' : 'text-danger',
                        'href' : '#d' + data[i].id,
                        'id' : '#d' + data[i].id
                    }).append(
                        $('<span>').prop({
                            'class' : 'glyphicon glyphicon-remove',
                            'title' : 'Удалить'
                        })
                    )
                )
            ).append(
                $('<td>').append(
                    $('<a>').prop({
                        'class' : className,
                        'href' : '#i' + data[i].id,
                        'id' : '#i' + data[i].id
                    }).append(
                        data[i].name
                    )
                )
            ));
        }
    }

    // Удаление шаблонов
    $('#fieldTemplatesList tbody').on('click', 'tr td a span', function() {
        $.ajax({
            'url' : '/index.php/admin/tasu/deletefieldstemplate',
            'data' : {
              'id' : $(this).parents('a').prop('id').substr(2)
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == 'true' || data.success == true) {
                    $('#fieldTemplatesList').trigger('reload');
                } else {

                }
            }
        });
    });
    $('#keyTemplatesList tbody').on('click', 'tr td a span', function() {
        $.ajax({
            'url' : '/index.php/admin/tasu/deletekeystemplate',
            'data' : {
                'id' : $(this).parents('a').prop('id').substr(2)
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == 'true' || data.success == true) {
                    $('#keyTemplatesList').trigger('reload');
                } else {

                }
            }
        });
    });

    // Применение шаблона
    $(document).on('click', '.fieldsTemplatesName, .keysTemplatesName', function() {
        var id = $(this).prop('id').substr(2);
        if($(this).hasClass('fieldsTemplatesName')) {
            var urlPart = 'getonefieldtemplate';
        } else {
            var urlPart = 'getonekeytemplate';
        }
        var _this = this;
        $.ajax({
            'url' : '/index.php/admin/tasu/' + urlPart,
            'data' : {
                'id' : id
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == 'true' || data.success == true) {
                    $(_this).parents('tbody').find('tr.success').removeClass('success');
                    $(_this).parents('tr').addClass('success');
                    if($(_this).hasClass('fieldsTemplatesName')) {
                        $('#tableFields').trigger('templateAccept',[data.data.template]);
                    } else {
                        $('#tableKey').trigger('templateAccept', [data.data.template]);
                    }
                } else {

                }
            }
        });
    });

    // Импорт файла
    $('.submitImport input').on('click', function() {
        // Отрубаем кнопку импорта, чтобы нельзя было нажать во второй раз
        if(!$(this).hasClass('disabled')) {
            $(this).addClass('disabled');
            // И схлопываем контейнеры для импорта полей
            $('.fieldsBox').slideToggle(500, function() {
                $(this).addClass('no-display').removeAttr('style');
            });
            $('.keyBox').slideToggle(500, function() {
                $(this).addClass('no-display').removeAttr('style');
            });
            $('.progressBox').removeClass('no-display');
            $('#tableList').prop('disabled', true);
        } else {
            return false;
        }
        var dbFields = $('#tableFields select.dbField');
        var tasuFields = $('#tableFields select.tasuField');
        var keyFields = $('#tableKey select');
        var table = $('#tableList').val();

        // Формируем JSON
        var fieldsJson = [];
        var keyJson = [];
        for(var i = 0; i < dbFields.length; i++) {
            fieldsJson.push({
                'dbField' : $(dbFields[i]).val(),
                'tasuField' : $(tasuFields[i]).val()
            });
        }
        for(var i = 0; i < keyFields.length; i++) {
            keyJson.push($(keyFields[i]).val());
        }

        var processed = 0; // Кол-во обработанных байт
        var isPaused = false; // Флаг, который скажет о том, запаузен ли импорт в таблицу
        function Import() {
            if(!isPaused) {
                $.ajax({
                    'url' : '/index.php/admin/tasu/import',
                    'data' : {
                        'table' : table,
                        'fields' : $.toJSON(fieldsJson),
                        'key' : $.toJSON(keyJson),
                        'file' : $('#filesList tr.success a').prop('id').substr(2),
                        'per_query' : 100, // Кол-во строк, которые нужно выгрузить за раз. В идеале, это можно сделать отдельным полем
                        'rows_numall' : $.trim($('.numStringsAll').text()),
                        'rows_num' : $.trim($('.numStrings').text()),
                        'rows_accepted' : $.trim($('.numStringsAdded').text()),
                        'rows_discarded' : $.trim($('.numStringsDiscarded').text()),
                        'rows_error' : $.trim($('.numStringsError').text()),
                        'processed' : processed
                    },
                    'cache' : false,
                    'dataType' : 'json',
                    'type' : 'GET',
                    'success' : function(data, textStatus, jqXHR) {
                        if(data.success == 'true' || data.success == true) {
                            // Двигаем прогрессбар
                            // Считаем процент выполнения
                            var procent = (data.data.processed / data.data.filesize) * 100;
                            if(procent > 100) {
                                procent = 100;
                            }
                            processed = data.data.processed;
                            $('#importProgressbar').prop({
                                'aria-valuenow' : procent,
                                'style' : 'width: ' + procent + '%'
                            });

                            $('.numStrings').text(data.data.rowsNum);
                            $('.numStringsAll').text(data.data.rowsNumAll);
                            $('.numStringsAdded').text(data.data.rowsAccepted);
                            $('.numStringsDiscarded').text(data.data.rowsDiscarded);
                            $('.numStringsError').text(data.data.rowsError);
                            if(procent < 100) {
                                setTimeout(Import, 500)
                            } else {
                                alert('Импорт успешно выполнен!');
                                $('.successImport').removeClass('no-display');
                                $('.pauseImport').addClass('no-display');
                                $('.continueImport').addClass('no-display');
                            }
                        } else {

                        }
                    }
                });
            } else {
                setTimeout(Import, 500);
            }
        };
        Import();


        $('.successImport').on('click',function() {
            $('.fieldsBox').slideToggle(500, function() {
                $(this).addClass('no-display').removeAttr('style');
            });
            $('.keyBox').slideToggle(500, function() {
                $(this).addClass('no-display').removeAttr('style');
            });
            $('.progressBox').addClass('no-display');
            $('#chooseTable').addClass('no-display');
            $('#tableList').removeAttr('disabled').val(-1);
            $('#tableFields').trigger('update');
            $('#tableKey').trigger('update');
            $('#keyTemplatesList').trigger('reload');
            $('#fieldTemplatesList').trigger('reload');
            $('.numStrings, .numStringsAll, .numStringsAdded, .numStringsDiscarded, .numStringsError').text('0');
            $('#importProgressbar').prop({
                'aria-valuenow' : 0,
                'style' : 'width: 0%'
            });
            processed = 0;
            $('#filesList tr.success').removeClass('success');
            $('h4.fileName').text('');
        });

        $('.pauseImport').on('click',function() {
            if(!$(this).hasClass('disabled')) {
                isPaused = true;
                $(this).addClass('disabled');
                $('.continueImport').removeClass('disabled');
            }
        });

        $('.continueImport').on('click',function() {
            if(!$(this).hasClass('disabled')) {
                isPaused = false;
                $(this).addClass('disabled');
                $('.pauseImport').removeClass('disabled');
            }
        });
    });

    $('.syncBtn').on('click', function(e) {
        $(this).attr({
            'disabled' : true,
            'value' : 'Идёт синхронизация...'
        });
        $(this).parents('.accordion-inner').find('.progressBox').trigger('begin')
    });
});