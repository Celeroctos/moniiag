$(document).ready(function() {
    var choosers = $('.chooser');
    $(choosers).each(function() {
        (function(chooser) {
            var current = null;
            var mode = 0; // 0 - стрелки, 1 - ввод в поле руками (нужда запроса)
            var prevVal = null;
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
                            var span = $('<span>').addClass('item');
                            var innerSpan = $('<span>').addClass('glyphicon glyphicon-remove');
                            $(span).append($(chooser).find('.variants li.active').text()).append(innerSpan);
                            $(chooser).find('.choosed').append(span);
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
                    console.log(prevVal);
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
                                        for(var i = 0; i < rows.length; i++) {
                                            $(ul).append($('<li>').text(rows[i].last_name + ' ' + rows[i].first_name + ' ' + rows[i].middle_name));
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

            $(document).on('click', '.chooser .variants li', function(e) {
                var span = $('<span>').addClass('item');
                var innerSpan = $('<span>').addClass('glyphicon glyphicon-remove');
                $(span).append($(this).text()).append(innerSpan);
                $(chooser).find('.choosed').append(span);
                $(this).parents('ul').hide();
            });

            $(document).on('click', '.choosed span.item', function(e) {
                $(this).remove();
            });
        })(this);
    });

    // Конфиг выборщиков
    var choosersConfig = {
        'doctorChooser' : {
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
        }
    };
});