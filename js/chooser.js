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
                        $(chooser).find('input').val($(chooser).find('.variants li.active').text());
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
                        $(chooser).find('input').val($(chooser).find('.variants li.active').text());
                    }

                    // Нажатие Enter переносит в список выбранных
                    if(e.keyCode == 13) {
                        if(current != null) {
                            addVariantToChoosed($(chooser).find('.variants li.active'));
                        }
                        return false;
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
                        $.ajax({
                            'url' : url,
                            'cache' : false,
                            'dataType' : 'json',
                            'type' : 'GET',
                            'success' : function(data, textStatus, jqXHR) {
                                if(data.success == 'true') {
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

            $(document).on('click', '.choosed span.item span', function(e) {
                // Удаляем из массива предыдущих элементов
                for(var i = 0; i < choosedElements.length; i++) {
                    if('r' + $(choosedElements[i]).prop('id') == $(this).parent().prop('id')) {
                        choosedElements = choosedElements.slice(0, i).concat(choosedElements.slice(i + 1));
                        break;
                    }
                }
                $(this).parent().remove();
            });


            $(chooser).on('click', '.variants li', function(e) {
                addVariantToChoosed(this);
            });

            function addVariantToChoosed(li) {
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
                            var span = $('<span>').addClass('item');
                            var innerSpan = $('<span>').addClass('glyphicon glyphicon-remove');
                            $(span).append($(li).text()).append(innerSpan);
                            $(span).prop('id', 'r' + currentElements[i][primaryField]);
                            $(chooser).find('.choosed').append(span);
                            $(chooser).find('input').val('');
                            prevVal = null;
                            choosedElements.push(currentElements[i]);
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
                $(ul).append($('<li>').text(row.last_name + ' ' + row.first_name + ' ' + row.middle_name));
            },
            'url' : '/index.php/reception/patient/search?page=1&withandwithout=0&rows=10&sidx=id&sord=desc&filters=',
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
        }
    };
});