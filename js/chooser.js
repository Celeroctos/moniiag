$(document).ready(function() {
    var choosers = $('.chooser');
    $(choosers).each(function() {
        (function(chooser) {
            var current = null;
            var mode = 0; // 0 - стрелки, 1 - ввод в поле руками (нужда запроса)
            var prevVal = null;
            var currentElements = []; // Список строк с элементами. Требуется для того, чтобы связать определённый span  с конкретной строкой
            var choosedElements = []; // Список выбранных элементов для текущего контрола (строки)
            $.fn[$(chooser).attr('id')] = {
                getChoosed: function() {
                    return choosedElements;
                },
                addChoosed: function(li, rowData, withOutInsert) {
                    if(typeof currentElements == 'undefined') {
                        console.log("!");
                        currentElements = [];
                    }
                    currentElements.push(rowData);
                    addVariantToChoosed(li, withOutInsert);
                },
                addExtraParam: function(key, value) {
                    if($('#' + $(chooser).prop('id')).length > 0 && choosersConfig[$(chooser).prop('id')].hasOwnProperty('extraparams')) {
                        choosersConfig[$(chooser).prop('id')].extraparams[key] = value;
                    }
                },
                deleteExtraParam: function(key) {
                    if($('#' + $(chooser).prop('id')).length > 0 && choosersConfig[$(chooser).prop('id')].hasOwnProperty('extraparams') && choosersConfig[$(chooser).prop('id')].extraparams.hasOwnProperty(key)) {
                        delete choosersConfig[$(chooser).prop('id')].extraparams[key];
                    }
                },
                clearAll: function() {
                    choosedElements = [];
                    $(chooser).find('.choosed span').remove();
                },
                disable: function() {
                    $(chooser).find('input').prop('disabled', true);
                    if($(chooser).find('.input-group-addon').length > 0) {
                        $(chooser).find('.input-group-addon').off('click').css('cursor', 'default');
                    }
                },
                enable: function() {
                    $(chooser).find('input').prop('disabled', false);
                    $(chooser).find('.input-group-addon').off('click').css('cursor', 'pointer');
                    if($(chooser).find('.input-group-addon').length > 0) {
                        $(chooser).find('.input-group-addon').on('click', function() {
                            if(typeof choosersConfig[$(chooser).prop('id')].bindedWindowSelector != 'undefined' && $(choosersConfig[$(chooser).prop('id')].bindedWindowSelector).length > 0) {
                                $(choosersConfig[$(chooser).prop('id')].bindedWindowSelector).modal({});
                            }
                        });
                    }
                },
                addConfigParam: function(param, value) {
                    choosersConfig[$(chooser).prop('id')][param] = value;
                }
            };
            $(chooser).find('input').val('');
            $(chooser).find('input').on('keydown', function(e) {
                // Стрелка "Вверх"
                if($.trim($(this).val() != '')) {
                    if(e.keyCode == 38) {
                        $(chooser).find('.variants li.active').removeClass('active');
                        if(current == null) {
                            $(chooser).find('.variants li:last').addClass('active');
                            current = $(chooser).find('.variants li').length - 1;
                        } else {
                            if(current == 0) {
                                current = $(chooser).find('.variants li').length - 1;
                            } else {
                                --current;
                            }
                        }
                        mode = 0;
                        $(chooser).find('.variants li:eq(' + current + ')').addClass('active');
                        if(choosersConfig[$(chooser).prop('id')].hasOwnProperty('displayFunc')) {
                            var toDisplay = choosersConfig[$(chooser).prop('id')].displayFunc(currentElements[current]);
                            $(chooser).find('input').val(toDisplay);
                        } else {
                            $(chooser).find('input').val($(chooser).find('.variants li.active').text());
                        }
                    }
                    // Стрелка "Вниз"
                    if(e.keyCode == 40) {
                        $(chooser).find('.variants li.active').removeClass('active');
                        if(current == null) {
                            $(chooser).find('.variants li:first').addClass('active');
                            current = 0;
                        } else {
                            if(current == $(chooser).find('.variants li').length - 1) {
                                current = 0;
                            } else {
                                ++current;
                            }
                        }
                        mode = 0;
                        $(chooser).find('.variants li:eq(' + current + ')').addClass('active');
                        if(choosersConfig[$(chooser).prop('id')].hasOwnProperty('displayFunc')) {
                            var toDisplay = choosersConfig[$(chooser).prop('id')].displayFunc(currentElements[current]);
                            $(chooser).find('input').val(toDisplay);
                        } else {
                            $(chooser).find('input').val($(chooser).find('.variants li.active').text());
                        }
                    }

                    // Нажатие Enter переносит в список выбранных
                    if(e.keyCode == 13) {
                        if(current != null) {
                            addVariantToChoosed($(chooser).find('.variants li.active'));
                        }
                        return false;
                    }

                    // Нажатие бекспейса на последнем символе закроет список
                    if(e.keyCode == 8) {
                        if($(chooser).find('input').val().length == 1) {
                            $(chooser).find('.variants').hide();
                        }
                    }
                }
            });

            $(chooser).find('input').on('keyup', function(e) {
                // Нажатие бекспейса
                if(!($(this).val().length == 1 && e.keyCode == 8)) {
                    searchByField(this);
                }
                if($(this).val().length == 1) {
                    mode = 1;
                    searchByField(this);
                }
            });

            $(chooser).find('.input-group-addon').on('click', function(e) {
                if(typeof choosersConfig[$(chooser).prop('id')].bindedWindowSelector != 'undefined' && $(choosersConfig[$(chooser).prop('id')].bindedWindowSelector).length > 0) {
                    $(choosersConfig[$(chooser).prop('id')].bindedWindowSelector).modal({});
                }
            });

            function searchByField(field) {
                // Смотрим, введено ли что-то в поле по сравнению с тем, что было. Если да - делаем запрос
                if($.trim($(field).val()) != '') {
                    if(mode == 0) {
                        mode = 1;
                        return false;
                    }

                    if(prevVal != $.trim($(field).val())) {
                        if($(field).val().length > 0) {
                            prevVal = $.trim($(field).val());
                        }
                        // Делаем запрос на сторону сервера
                        var url = choosersConfig[$(chooser).prop('id')].url;
                        choosersConfig[$(chooser).prop('id')].filters.rules[0].data = $.trim($(field).val());
                        url += $.toJSON(choosersConfig[$(chooser).prop('id')].filters);
                        if(choosersConfig[$(chooser).prop('id')].hasOwnProperty('extraparams')) {
                            var extra = $.extend({}, choosersConfig[$(chooser).prop('id')].extraparams);
                            for(var i in extra) {
                                if(typeof extra[i] == 'function') {
                                    extra[i] = extra[i]();
                                }
                            }
                        } else {
                            var extra = {};
                        }

                        $.ajax({
                            'url' : url,
                            'cache' : false,
                            'dataType' : 'json',
                            'data' : extra,
                            'type' : 'GET',
                            'success' : function(data, textStatus, jqXHR) {
                                if(data.success == 'true' || data.success == true) {
                                    $(chooser).find('li').remove();
                                    current = null;
                                    var ul = $(chooser).find('.variants')
                                    var rows = data.rows;
                                    if(rows.length == 0) {
                                        $(ul).hide();
                                    } else {
                                        currentElements = [];
                                        for(var i = 0; i < rows.length; i++) {
                                            choosersConfig[$(chooser).prop('id')].rowAddHandler(ul, rows[i]);
                                            var field = choosersConfig[$(chooser).prop('id')].primary;
                                            $(ul).find('li:eq(' + i + ')').prop('id', 'r' + rows[i][field]);
                                            currentElements.push(rows[i]);
                                        }
                                        $(ul).show();
                                    }
                                } else {

                                }
                            }
                        });
                    }
                }
            }

            $(chooser).on('click', '.choosed span.item span.glyphicon-remove', function(e) {
                // Удаляем из массива предыдущих элементов
                for(var i = 0; i < choosedElements.length; i++) {
                    if('r' + $(choosedElements[i]).prop('id') == $(this).parent().prop('id')) {
                        choosedElements = choosedElements.slice(0, i).concat(choosedElements.slice(i + 1));
                        break;
                    }
                }
                $(this).parent().remove();
                $.fn[$(chooser).attr('id')].enable();
                if(choosersConfig[$(chooser).prop('id')].hasOwnProperty('afterRemove') && typeof choosersConfig[$(chooser).prop('id')].afterRemove == 'function') {
                    choosersConfig[$(chooser).prop('id')].afterRemove();
                }
            });

            $(chooser).on('click', '.choosed span.item span.glyphicon-arrow-down', function(e) {
                for(var i = 0; i < choosedElements.length; i++) {
                    if('r' + $(choosedElements[i]).prop('id') == $(this).parent().prop('id')) {
                        // Размножаем это на все контролы, которые описаны в moving
                        var moving = choosersConfig[$(chooser).prop('id')].moving;
                        if(typeof moving != 'undefined') {
                            for(var j = 0; j < moving.length; j++) {
                                $.fn[moving[j]].addChoosed(choosersConfig[$(chooser).prop('id')].movingFunc(choosedElements[i]), choosedElements[i]);
                            }
                            break;
                        }
                    }
                }
            });


            $(chooser).on('click', '.variants li', function(e) {
                addVariantToChoosed(this);
            });

            function addVariantToChoosed(li, withOutInsert) {
                $(li).parents('ul').hide();
                var id = $(li).prop('id').substr(1);
                var primaryField = choosersConfig[$(chooser).prop('id')].primary;
                for(var i = 0; i < currentElements.length; i++) {
                    if(currentElements[i][primaryField] == id) {
                        // Смотрим, нет ли уже такого элемента в списке. Если есть - добавлять не надо в список выбранных
                        var isFound = false;
                        var foundElement = null;
                        for(var j = 0; j < choosedElements.length; j++) {
                            if(currentElements[i][primaryField] == choosedElements[j][primaryField]) {
                                isFound = true;
                                break;
                            }
                        }
                        // А если найден - повторно добавлять не надо
                        if(!isFound) {
                            if(withOutInsert != 1) {
                                var span = $('<span>').addClass('item');
                                // Возможность копирования в соседние chooser-ы
                                if(choosersConfig[$(chooser).prop('id')].hasOwnProperty('moving') && choosersConfig[$(chooser).prop('id')]['moving'].length > 0) {
                                    // Посмотрим, все ли элементы есть в наличии
                                    var moving = choosersConfig[$(chooser).prop('id')]['moving'];
                                    var isAllowForMoving = true;
                                    for(var j = 0; j < moving.length; j++) {
                                        if($('#' + moving[j]).length == 0) {
                                            isAllowForMoving = false;
                                            break;
                                        }
                                    }
                                    if(isAllowForMoving) {
                                        var innerSpan = $('<span>').addClass('glyphicon glyphicon-arrow-down');
                                    } else {
                                        var innerSpan = $('<span>').addClass('glyphicon glyphicon-remove');
                                    }
                                } else {
                                    var innerSpan = $('<span>').addClass('glyphicon glyphicon-remove');
                                }
                                $(span).append($(li).text()).append(innerSpan);
                                $(span).prop('id', 'r' + currentElements[i][primaryField]);
                                $(chooser).find('.choosed').append(span);
                            }
                            $(chooser).find('input').val('');
                            prevVal = null;
                            choosedElements.push(currentElements[i]);
                            /* Логика работы: если есть настройка о количестве добавляемых максмально вариантов, то нужно блокировать строку, если количество вариантов достигло максимума */
                            if(choosersConfig[$(chooser).prop('id')].hasOwnProperty('maxChoosed') && choosedElements.length >= choosersConfig[$(chooser).prop('id')].maxChoosed) {
                                $.fn[$(chooser).attr('id')].disable();
                            }
                            if(choosersConfig[$(chooser).prop('id')].hasOwnProperty('afterInsert') && typeof choosersConfig[$(chooser).prop('id')].afterInsert == 'function') {
                                choosersConfig[$(chooser).prop('id')].afterInsert(chooser);
                            }
                        } else {
                            // TODO: сделать анимацию на вариант, который уже есть в списке, чтобы показать, что он есть
                        }
                        break;
                    }
                }
            }
        })(this);
    });

    // Конфиг выборщиков
    var choosersConfig = {
        'doctorChooser' : {
            'primary' : 'id', // Первичный ключ у строки
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text(row.last_name + ' ' + row.first_name + ' ' + row.middle_name));
            },
            'url' : '/index.php/guides/employees/get?page=1&rows=10&sidx=id&sord=desc&filters=',
            'filters' : {
                'groupOp' : 'AND',
                'rules': [
                    {
                        'field' : 'fio',
                        'op' : 'bw',
                        'data' : ''
                    }
                ]
            }
        },
        'patientChooser' : {
            'primary' : 'id',
            'rowAddHandler' : function(ul, row) {
                if(row.card_number != null) {
                    $(ul).append($('<li>').text(row.last_name + ' ' + row.first_name + ' ' + row.middle_name + ', дата рождения ' + row.birthday + ', номер ОМС ' + row.oms_number + ', номер карты ' + row.card_number));
                } else {
                    $(ul).append($('<li>').text(row.last_name + ' ' + row.first_name + ' ' + row.middle_name + ', дата рождения ' + row.birthday + ', номер ОМС ' + row.oms_number + ', карты нет '));
                }
            },
            'displayFunc' : function(row) {
                return row.last_name + ' ' + row.first_name + ' ' + row.middle_name;
            },
            'url' : '/index.php/reception/patient/search?page=1&withandwithout=0&rows=10&sidx=id&sord=desc&distinct=1&filters=',
            'filters' : {
                'groupOp' : 'AND',
                'rules': [
                    {
                        'field' : 'fio',
                        'op' : 'bw',
                        'data' : ''
                    }
                ]
            }
        },
        'diagnosisChooser' : {
            'primary' : 'id',
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text(row.description));
            },
            'moving': [ // При задании этой опции и при наличии перечисленных элементов иконка удаления заменяется иконкой копирования id-шников в соответствующие элементы
                'diagnosisDistribChooser'
            ],
            'movingFunc' : function(data) {
                return $('<li>').prop('id', 'r' + data.id).text(data.description);
            },
            'url' : '/index.php/guides/mkb10/get?page=1&rows=10&sidx=id&sord=desc&listview=1&nodeid=0&limit=10&is_chooser=1&onlylevel=3&filters=',
            'filters' : {
                'groupOp' : 'AND',
                'rules': [
                    {
                        'field' : 'description',
                        'op' : 'cn',
                        'data' : ''
                    }
                ]
            }
        },
        'primaryDiagnosisChooser' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text(row.description));
            },
            'url' : '/index.php/guides/mkb10/get?page=1&rows=10&sidx=id&sord=desc&listview=1&nodeid=0&limit=10&is_chooser=1&filters=',
            'extraparams' : {
                'onlylikes' : typeof getOnlyLikes != 'undefined' ? getOnlyLikes : 0
            },
            'filters' : {
                'groupOp' : 'AND',
                'rules': [
                    {
                        'field' : 'description',
                        'op' : 'cn',
                        'data' : ''
                    }
                ]
            }
        },
        'primaryClinicalDiagnosisChooser': {
            'primary': 'id',
            'bindedWindowSelector' : '#addClicnicalDiagnosisPopup',
            'maxChoosed': 1,
            'rowAddHandler': function (ul, row) {
                $(ul).append($('<li>').text(row.description));
            },
            'url': '/index.php/guides/mkb10/getclinical?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
            'filters': {
                'groupOp': 'AND',
                'rules': [
                    {
                        'field': 'description',
                        'op': 'cn',
                        'data': ''
                    }
                ]
            }
        },
        'secondaryDiagnosisChooser' : {
            'primary' : 'id',
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text(row.description));
            },
            'url' : '/index.php/guides/mkb10/get?page=1&rows=10&sidx=id&sord=desc&listview=1&nodeid=0&limit=10&is_chooser=1&filters=',
            'extraparams' : {
                'onlylikes' :  typeof getOnlyLikes != 'undefined' ? getOnlyLikes : 0
            },
            'filters' : {
                'groupOp' : 'AND',
                'rules': [
                    {
                        'field' : 'description',
                        'op' : 'cn',
                        'data' : ''
                    }
                ]
            }
        },
        'secondaryClinicalDiagnosisChooser': {
            'primary': 'id',
            'rowAddHandler': function (ul, row) {
                $(ul).append($('<li>').text(row.description));
            },
            'url': '/index.php/guides/mkb10/getclinical?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
            'filters': {
                'groupOp': 'AND',
                'rules': [
                {
                    'field': 'description',
                    'op': 'cn',
                    'data': ''
                }
            ]
            }
        },
        'diagnosisDistribChooser' : {
            'primary' : 'id',
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text(row.description));
            },
            /* 'url' : '/index.php/guides/mkb10/get?page=1&rows=10&sidx=id&sord=desc&listview=1&nodeid=0&limit=10&onlylikes=1&filters=', */
            'url' : '/index.php/admin/diagnosis/getclinical?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
            'extraparams' : {
                // Здесь - специальность, но она даётся извне
                'medworkerid' : null
            },
            'filters' : {
                'groupOp' : 'AND',
                'rules': [
                    {
                        'field' : 'description',
                        'op' : 'cn',
                        'data' : ''
                    }
                ]
            }
        },
        'regionChooser' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'afterInsert' : function(chooser) {
                if($.fn['regionChooser'].getChoosed().length > 0) {
                    var param = $.fn['regionChooser'].getChoosed()[0].code_cladr;
                    if($('#districtChooser').length > 0) {
                        $.fn['districtChooser'].addExtraParam('region', param);
                    }
                    if($('#settlementChooser').length > 0) {
                        $.fn['settlementChooser'].addExtraParam('region', param);
                    }
                    if($('#streetChooser').length > 0) {
                        $.fn['streetChooser'].addExtraParam('region', param);
                    }
                }
            },
            'afterRemove' : function() {
                if($('#districtChooser').length > 0) {
                    $.fn['districtChooser'].clearAll();
                    $.fn['districtChooser'].enable();
                    $.fn['districtChooser'].deleteExtraParam('region');
                }

                if($('#settlementChooser').length > 0) {
                    $.fn['settlementChooser'].clearAll();
                    $.fn['settlementChooser'].enable();
                    $.fn['settlementChooser'].deleteExtraParam('region');
                    $.fn['settlementChooser'].deleteExtraParam('district');
                }

                if($('#streetChooser').length > 0) {
                    $.fn['streetChooser'].clearAll();
                    $.fn['streetChooser'].enable();
                    $.fn['streetChooser'].deleteExtraParam('region');
                    $.fn['streetChooser'].deleteExtraParam('district');
                    $.fn['streetChooser'].deleteExtraParam('settlement');
                }
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + row.code_cladr + '] ' + row.name));
            },
            'url' : '/index.php/guides/cladr/regionget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
            'filters' : {
                'groupOp' : 'AND',
                'rules': [
                    {
                        'field' : 'name',
                        'op' : 'cn',
                        'data' : ''
                    }
                ]
            }
        },
        'districtChooser' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'afterInsert' : function(chooser) {
                if($.fn['districtChooser'].getChoosed().length > 0) {
                    var param = $.fn['districtChooser'].getChoosed()[0].code_cladr;
                    if($('#settlementChooser').length > 0) {
                        $.fn['settlementChooser'].addExtraParam('district', param);
                    }
                    if($('#streetChooser').length > 0) {
                        $.fn['streetChooser'].addExtraParam('district', param);
                    }
                }
            },
            'afterRemove' : function() {
                if($('#settlementChooser').length > 0) {
                    $.fn['settlementChooser'].clearAll();
                    $.fn['settlementChooser'].enable();
                    $.fn['settlementChooser'].deleteExtraParam('district');
                }

                if($('#streetChooser').length > 0) {
                    $.fn['streetChooser'].clearAll();
                    $.fn['streetChooser'].enable();
                    $.fn['streetChooser'].deleteExtraParam('district');
                    $.fn['streetChooser'].deleteExtraParam('settlement');
                }
            },
            'extraparams' : {
                //'region' : $.fn['regionChooser'].getChoosed()
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + row.code_cladr + '] ' + row.name));
            },
            'url' : '/index.php/guides/cladr/districtget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
            'filters' : {
                'groupOp' : 'AND',
                'rules': [
                    {
                        'field' : 'name',
                        'op' : 'cn',
                        'data' : ''
                    }
                ]
            }
        },
        'settlementChooser' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'extraparams' : {
                //'region' : $.fn['regionChooser'].getChoosed(),
                //'district' : $.fn['districtChooser'].getChoosed()
            },
            'afterInsert' : function(chooser) {
                if($.fn['settlementChooser'].getChoosed().length > 0) {
                    var param = $.fn['settlementChooser'].getChoosed()[0].code_cladr;
                    if($('#streetChooser').length > 0) {
                        $.fn['streetChooser'].addExtraParam('settlement', param);
                    }
                }
            },
            'afterRemove' : function() {
                if($('#streetChooser').length > 0) {
                    $.fn['streetChooser'].clearAll();
                    $.fn['streetChooser'].enable();
                    $.fn['streetChooser'].deleteExtraParam('settlement');
                }
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + row.code_cladr + '] ' + row.name));
            },
            'url' : '/index.php/guides/cladr/settlementget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
            'filters' : {
                'groupOp' : 'AND',
                'rules': [
                    {
                        'field' : 'name',
                        'op' : 'cn',
                        'data' : ''
                    }
                ]
            }
        },
        'streetChooser' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + row.code_cladr + '] ' + row.name));
            },
            'afterInsert': function() {

            },
            'afterRemove' : function() {

            },
            'url' : '/index.php/guides/cladr/streetget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
            'extraparams' : {
                //'region' : $.fn['regionChooser'].getChoosed(),
               // 'district' : $.fn['districtChooser'].getChoosed(),
               // 'settlement' : $.fn['settlementChooser'].getChoosed()
            },
            'filters' : {
                'groupOp' : 'AND',
                'rules': [
                    {
                        'field' : 'name',
                        'op' : 'cn',
                        'data' : ''
                    }
                ]
            }
        },
        'regionChooser2' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'afterInsert' : function(chooser) {
                if($.fn['regionChooser2'].getChoosed().length > 0) {
                    var param = $.fn['regionChooser2'].getChoosed()[0].code_cladr;
                    if($('#districtChooser2').length > 0) {
                        $.fn['districtChooser2'].addExtraParam('region', param);
                    }
                    if($('#settlementChooser2').length > 0) {
                        $.fn['settlementChooser2'].addExtraParam('region', param);
                    }
                    if($('#streetChooser2').length > 0) {
                        $.fn['streetChooser2'].addExtraParam('region', param);
                    }
                }
            },
            'afterRemove' : function() {
                if($('#districtChooser2').length > 0) {
                    $.fn['districtChooser2'].clearAll();
                    $.fn['districtChooser2'].enable();
                    $.fn['districtChooser2'].deleteExtraParam('region');
                }

                if($('#settlementChooser2').length > 0) {
                    $.fn['settlementChooser2'].clearAll();
                    $.fn['settlementChooser2'].enable();
                    $.fn['settlementChooser2'].deleteExtraParam('region');
                    $.fn['settlementChooser2'].deleteExtraParam('district');
                }

                if($('#streetChooser2').length > 0) {
                    $.fn['streetChooser2'].clearAll();
                    $.fn['streetChooser2'].enable();
                    $.fn['streetChooser2'].deleteExtraParam('region');
                    $.fn['streetChooser2'].deleteExtraParam('district');
                    $.fn['streetChooser2'].deleteExtraParam('settlement');
                }
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + row.code_cladr + '] ' + row.name));
            },
            'url' : '/index.php/guides/cladr/regionget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
            'filters' : {
                'groupOp' : 'AND',
                'rules': [
                    {
                        'field' : 'name',
                        'op' : 'cn',
                        'data' : ''
                    }
                ]
            }
        },
        'districtChooser2' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'afterInsert' : function(chooser) {
                if($.fn['districtChooser2'].getChoosed().length > 0) {
                    var param = $.fn['districtChooser2'].getChoosed()[0].code_cladr;
                    if($('#settlementChooser2').length > 0) {
                        $.fn['settlementChooser2'].addExtraParam('district', param);
                    }
                    if($('#streetChooser2').length > 0) {
                        $.fn['streetChooser2'].addExtraParam('district', param);
                    }
                }
            },
            'afterRemove' : function() {
                if($('#settlementChooser2').length > 0) {
                    $.fn['settlementChooser2'].clearAll();
                    $.fn['settlementChooser2'].enable();
                    $.fn['settlementChooser2'].deleteExtraParam('district');
                }

                if($('#streetChooser2').length > 0) {
                    $.fn['streetChooser2'].clearAll();
                    $.fn['streetChooser2'].enable();
                    $.fn['streetChooser2'].deleteExtraParam('district');
                    $.fn['streetChooser2'].deleteExtraParam('settlement');
                }
            },
            'extraparams' : {
                //'region' : $.fn['regionChooser'].getChoosed()
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + row.code_cladr + '] ' + row.name));
            },
            'url' : '/index.php/guides/cladr/districtget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
            'filters' : {
                'groupOp' : 'AND',
                'rules': [
                    {
                        'field' : 'name',
                        'op' : 'cn',
                        'data' : ''
                    }
                ]
            }
        },
        'settlementChooser2' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'extraparams' : {
                //'region' : $.fn['regionChooser'].getChoosed(),
                //'district' : $.fn['districtChooser'].getChoosed()
            },
            'afterInsert' : function(chooser) {
                if($.fn['settlementChooser2'].getChoosed().length > 0) {
                    var param = $.fn['settlementChooser2'].getChoosed()[0].code_cladr;
                    if($('#streetChooser2').length > 0) {
                        $.fn['streetChooser2'].addExtraParam('settlement', param);
                    }
                }
            },
            'afterRemove' : function() {
                if($('#streetChooser2').length > 0) {
                    $.fn['streetChooser2'].clearAll();
                    $.fn['streetChooser2'].enable();
                    $.fn['streetChooser2'].deleteExtraParam('settlement');
                }
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + row.code_cladr + '] ' + row.name));
            },
            'url' : '/index.php/guides/cladr/settlementget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
            'filters' : {
                'groupOp' : 'AND',
                'rules': [
                    {
                        'field' : 'name',
                        'op' : 'cn',
                        'data' : ''
                    }
                ]
            }
        },
        'streetChooser2' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + row.code_cladr + '] ' + row.name));
            },
            'afterInsert': function() {

            },
            'afterDelete' : function() {

            },
            'url' : '/index.php/guides/cladr/streetget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
            'extraparams' : {
                //'region' : $.fn['regionChooser'].getChoosed(),
                // 'district' : $.fn['districtChooser'].getChoosed(),
                // 'settlement' : $.fn['settlementChooser'].getChoosed()
            },
            'filters' : {
                'groupOp' : 'AND',
                'rules': [
                    {
                        'field' : 'name',
                        'op' : 'cn',
                        'data' : ''
                    }
                ]
            }
        }
    };
});