$(document).ready(function() {

    var choosers = $('.chooser');
    $(choosers).each(function() {
        (function(chooser) {
            var current = null;
            var mode = 0; // 0 - стрелки, 1 - ввод в поле руками (нужда запроса)
            var prevVal = null;
            var currentElements = []; // Список строк с элементами. Требуется для того, чтобы связать определённый span  с конкретной строкой
            var choosedElements = []; // Список выбранных элементов для текущего контрола (строки)
			var lastUserInput = '';
            var numRecords = null;
            $.fn[$(chooser).attr('id')] = {
                getChoosed: function() {
                    return choosedElements;
                },
                addChoosed: function(li, rowData, withOutInsert) {
                    if(typeof currentElements == 'undefined') {
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
				getLastUserInput: function() {
					return lastUserInput;
				},
                clearAll: function() {
					current = null;
                    choosedElements = [];
                    $(chooser).find('.choosed span').remove();
                    $(chooser).find('input').prop('disabled', false);
					if(choosersConfig[$(chooser).prop('id')].hasOwnProperty('hideEmpty') && choosersConfig[$(chooser).prop('id')].hideEmpty) {
						$(chooser).find('.choosed').css('display', 'none');
					}
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
                                if(typeof choosersConfig[$(chooser).prop('id')].beforeWindowShow != 'undefined') {
                                    choosersConfig[$(chooser).prop('id')].beforeWindowShow(function() {
                                        $(choosersConfig[$(chooser).prop('id')].bindedWindowSelector).modal({});
                                    });
                                } else {
                                    $(choosersConfig[$(chooser).prop('id')].bindedWindowSelector).modal({});
                                    if(typeof choosersConfig[$(chooser).prop('id')].afterWindowShow != 'undefined') {
                                        choosersConfig[$(chooser).prop('id')].afterWindowShow();
                                    }
                                }
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
                            $(chooser).find('.variants li:not(.navigation):last').addClass('active');
                            current = $(chooser).find('.variants li:not(.navigation)').length - 1;
                        } else {
                            if(current == 0) {
                                current = $(chooser).find('.variants li:not(.navigation)').length - 1;
                            } else {
                                --current;
                            }
                        }
                        mode = 0;
                        $(chooser).find('.variants li:eq(' + current + ')').addClass('active');
                        if(choosersConfig[$(chooser).prop('id')].hasOwnProperty('displayFunc')) {
                            var toDisplay = choosersConfig[$(chooser).prop('id')].displayFunc(currentElements[current]);
                            //$(chooser).find('input').val(toDisplay);
                        } else {
                            //$(chooser).find('input').val($(chooser).find('.variants li.active').text());
                        }
                    }
                    // Стрелка "Вниз"
                    if(e.keyCode == 40) {
                        $(chooser).find('.variants li.active').removeClass('active');
                        if(current == null) {
                            $(chooser).find('.variants li:first').addClass('active');
                            current = 0;
                        } else {
                            if(current == $(chooser).find('.variants li:not(.navigation)').length - 1) {
                                current = 0;
                            } else {
                                ++current;
                            }
                        }
                        mode = 0;
                        $(chooser).find('.variants li:eq(' + current + ')').addClass('active');
                        if(choosersConfig[$(chooser).prop('id')].hasOwnProperty('displayFunc')) {
                            var toDisplay = choosersConfig[$(chooser).prop('id')].displayFunc(currentElements[current]);
                           // $(chooser).find('input').val(toDisplay);
                        } else {
                          //  $(chooser).find('input').val($(chooser).find('.variants li.active').text());
                        }
                    }

                    // Стрелка влево
                    if(e.keyCode == 37) {
                        navPrev();
                        searchByField($(chooser).find('input'), 1);
                    }
                    // Стрелка вправо
                    if(e.keyCode == 39) {
                        navNext();
                        searchByField($(chooser).find('input'), 1);
                    }

                    // Нажатие Enter переносит в список выбранных
                    if(e.keyCode == 13) {
						console.log(current);
                        // Если в чюююузер ничего не вбито - переходим в другой контрол
                        wasChangedFocus = false;
                        if ($(chooser).find('input').val()=='')
                        {
                            wasChangedFocus = true;
                            $.fn.switchFocusToNext();
                        }
                        if(current != null) {
                            // Смотрим - если текущая длина больше
                            if (choosersConfig[$(chooser).prop('id')].hasOwnProperty('maxChoosed'))
                            {
                                if ($.fn[$(chooser).prop('id')].getChoosed().length+1>= choosersConfig[$(chooser).prop('id')]['maxChoosed'] ){
                                    $.fn.switchFocusToNext();
                                }
                            }
                            addVariantToChoosed($(chooser).find('.variants li.active'));
							current = null;
                        }
                        else
                        {
                            // Переводим фокус на следующий элемент
                            if (!wasChangedFocus)
                            {
								$.fn.switchFocusToNext();
                            }
                        }

                        e.preventDefault();
						e.stopPropagation();
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

            function initPageParam(reset) {
                if(typeof choosersConfig[$(chooser).prop('id')].extraparams == 'undefined') {
                    choosersConfig[$(chooser).prop('id')].extraparams = {};
                }
                if(typeof choosersConfig[$(chooser).prop('id')].extraparams.page == 'undefined' || reset == 1) {
                    choosersConfig[$(chooser).prop('id')].extraparams.page = 1;
                }
            }

            function navPrev() {
                if(choosersConfig[$(chooser).prop('id')].extraparams.page == 1) {
                    choosersConfig[$(chooser).prop('id')].extraparams.page = Math.ceil(numRecords / 10);
                } else {
                    choosersConfig[$(chooser).prop('id')].extraparams.page--;
                }
            }

            function navNext() {
                if(choosersConfig[$(chooser).prop('id')].extraparams.page ==  Math.ceil(numRecords / 10)) {
                    choosersConfig[$(chooser).prop('id')].extraparams.page = 1;
                } else {
                    choosersConfig[$(chooser).prop('id')].extraparams.page++;
                }
            }

            $(chooser).on('click', '.navigation .prev', function(e) {
                initPageParam();
                navPrev();
                searchByField($(chooser).find('input'), 1);
                e.stopPropagation();
                return false;
            });

            $(chooser).on('click', '.navigation .next', function(e) {
                initPageParam();
                navNext();
                searchByField($(chooser).find('input'), 1);
                e.stopPropagation();
                return false;
            });

            // Как поменять местами порядок исполнения блюр и клик..?
            $(chooser).find('input').on('blur', function(e) {
               //$(this).parent().find('.variants').hide();
            });

            $(chooser).find('input').on('keyup', function(e) {
                // Нажатие бекспейса
                if(!($(this).val().length == 1 && e.keyCode == 8)) {
                    if(e.keyCode != 37 && e.keyCode != 39) {
                        initPageParam(1);
                    }
                    searchByField(this);
                }
                if($(this).val().length >= 1) {
                    mode = 1;
                    if(e.keyCode != 37 && e.keyCode != 39) {
                        initPageParam(1);
                    }
                    searchByField(this);
                }
            });

            $(chooser).find('.input-group-addon').on('click', function(e) {
                if(typeof choosersConfig[$(chooser).prop('id')].bindedWindowSelector != 'undefined' && $(choosersConfig[$(chooser).prop('id')].bindedWindowSelector).length > 0) {
                    if(typeof choosersConfig[$(chooser).prop('id')].beforeWindowShow != 'undefined') {
                        choosersConfig[$(chooser).prop('id')].beforeWindowShow(function() {
                            $(choosersConfig[$(chooser).prop('id')].bindedWindowSelector).modal({});
                        });
                    } else {
                        $(choosersConfig[$(chooser).prop('id')].bindedWindowSelector).modal({});
                        if(typeof choosersConfig[$(chooser).prop('id')].afterWindowShow != 'undefined') {
                            choosersConfig[$(chooser).prop('id')].afterWindowShow();
                        }
                    }
                }
            });

            function searchByField(field, isNavigation) {
                // Смотрим, введено ли что-то в поле по сравнению с тем, что было. Если да - делаем запрос
                if($.trim($(field).val()) != '') {
                    if(mode == 0) {
                        mode = 1;
                        return false;
                    }
					
					// Переводим, если надо, на другой язык, если стоит опция
					if(choosersConfig[$(chooser).prop('id')].hasOwnProperty('alwaysLanguage')) {
						var fieldValue = $.trim(changeLanguage($(field).val().toLowerCase(), choosersConfig[$(chooser).prop('id')].alwaysLanguage));
					} else {
						var fieldValue = $.trim($(field).val().toLowerCase());
					}

                    if(prevVal != $.trim($(field).val()) || isNavigation == 1) {
                        if($(field).val().length > 0) {
                            prevVal = $.trim($(field).val());
                        }
                        // Делаем запрос на сторону сервера
                        var url = choosersConfig[$(chooser).prop('id')].url;
                        choosersConfig[$(chooser).prop('id')].filters.rules[0].data = fieldValue;
                        var urlFilters = choosersConfig[$(chooser).prop('id')].filters;
                        var urlJSON = $.toJSON(urlFilters);
                        url += urlJSON;
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
                        $(field).css('display', 'inline');
                        var _field = field;
                        var ajaxGif = generateAjaxGif(16, 16);
                        $(ajaxGif).css({
                            'position' : 'absolute',
                            'left' : '100%',
                            'top' : '5px'
                        });
                        $(ajaxGif).insertAfter($(field));
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
                                    numRecords = data.records;

                                    if(rows.length == 0 || $.trim($(chooser).find('input').val()) == '') {
                                        $(ul).hide();
                                    } else {
                                        currentElements = [];
                                        // Берём обработчик, чтобы проверить его на непустоту в цикле
                                        chooserRowAddHandler = choosersConfig[$(chooser).prop('id')].rowAddHandler;
                                        for(var i = 0; i < rows.length; i++) {
                                            if (chooserRowAddHandler != undefined)
                                                chooserRowAddHandler(ul, rows[i]);
                                            var field = choosersConfig[$(chooser).prop('id')].primary;
                                            $(ul).find('li:eq(' + i + ')').prop('id', 'r' + rows[i][field]);
                                            currentElements.push(rows[i]);
                                        }
                                        if(data.records > rows.length) {
                                            $(ul).append($('<li>').prop({
                                                'class' : 'navigation'
                                            }).append(
                                                $('<a>').prop({
                                                    'href' : '#',
                                                    'class' : 'prev'
                                                }).append($('<span>').prop({
                                                    'class' : 'glyphicon glyphicon-arrow-left'
                                                })),
                                                $('<a>').prop({
                                                    'href' : '#',
                                                    'class' : 'next'
                                                }).append($('<span>').prop({
                                                    'class' : 'glyphicon glyphicon-arrow-right'
                                                }))
                                            ));
                                        }
                                        $(ul).show();
                                    }
                                    $(_field).focus();
                                    $(ajaxGif).remove();
                                    // Вызываем обработчик завершения загрузки, если он указан в конфигурации
                                    chooserLoadComplete = choosersConfig[$(chooser).prop('id')].loadCompleteHandler;
                                    if (chooserLoadComplete!=undefined && chooserLoadComplete!=null)
                                    {
                                        chooserLoadComplete($(chooser).prop('id'));
                                    }

                                } else {

                                }
                            }
                        });
                    }
                }
            }

            function generateAjaxGif(width, height) {
                return $('<img>').prop({
                    'src' : '/images/ajax-loader.gif',
                    'width' : width,
                    'height' : height,
                    'alt' : 'Загрузка...'
                });
            }
			
			// Перевод с английского на русский и с русского на английский
			function changeLanguage(fieldValue, language) {
				var replacer = null;
				if($.trim(language) == 'ru') { 
					replacer = {
						"q":"й", "w":"ц"  , "e":"у" , "r":"к" , "t":"е", "y":"н", "u":"г", 
						"i":"ш", "o":"щ", "p":"з" , "[":"х" , "]":"ъ", "a":"ф", "s":"ы", 
						"d":"в" , "f":"а"  , "g":"п" , "h":"р" , "j":"о", "k":"л", "l":"д", 
						";":"ж" , "'":"э"  , "z":"я", "x":"ч", "c":"с", "v":"м", "b":"и", 
						"n":"т" , "m":"ь"  , ",":"б" , ".":"ю"
					};   
				}
				if($.trim(language) == 'en') { 	
					replacer = {
						"й":"q", "ц":"w"  ,"у":"e" , "к":"r" , "е":"t", "н":"y", "г":"u", 
						"ш":"i", "щ":"o", "з":"p" , "х":"[" , "ъ":"]", "ф":"a", "ы":"s", 
						"в":"d" , "а":"f"  , "п":"g" , "р":"h" , "о":"j", "л":"k", "д":"l", 
						"ж":";" , "э":"'"  , "я":"z", "ч":"x", "с":"c", "м":"v", "и":"b", 
						"т":"n" , "ь":"m"  , "б":"," , "ю":"."
					};   
				}				
				
				for(var i = 0; i < fieldValue.length; i++){                        
					if(replacer[fieldValue[i].toLowerCase()] != undefined){
						if(fieldValue[i] == fieldValue[i].toLowerCase()){
							replace = replacer[fieldValue[i].toLowerCase()];    
						} else if(str[i] == fieldValue[i].toUpperCase()){
							replace = replacer[fieldValue[i].toLowerCase()].toUpperCase();
						} 
						
						fieldValue = fieldValue.replace(fieldValue[i], replace);
					}
				}
				return fieldValue;
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
				if(choosersConfig[$(chooser).prop('id')].hasOwnProperty('hideEmpty') && choosersConfig[$(chooser).prop('id')].hideEmpty) {
					if($.fn[$(chooser).attr('id')].getChoosed().length == 0) { 
						$(chooser).find('.choosed').css('display', 'none');
					} else {
						$(chooser).find('.choosed').css('display', 'block');
					}
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


            $(chooser).on('click', '.variants li:not(.navigation)', function(e) {
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
							lastUserInput = $(chooser).find('input').val();
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

                                // А вот теперь со спокойной совестью блокируем чюзер
                                $.fn[$(chooser).attr('id')].disable();
                            }
							if(choosersConfig[$(chooser).prop('id')].hasOwnProperty('hideEmpty') && choosersConfig[$(chooser).prop('id')].hideEmpty) {
								$(chooser).find('.choosed').css('display', 'block');
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

    // Перебросить фокус на следующий элемент управления
    function selectNextElement(currentElement)
    {

    }

    // Конфиг выборщиков
    var choosersConfig = {
        'doctorChooser' : {
            'primary' : 'id', // Первичный ключ у строки,
            'extraparams' : {

            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text(row.last_name + ' ' + row.first_name + ' ' + row.middle_name));
            },
            'url' : '/guides/employees/get?page=1&rows=10&sidx=id&sord=desc&filters=',
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
        'doctorChooser2' : {
            'maxChoosed' : 1,
            'primary' : 'id', // Первичный ключ у строки,
            'extraparams' : {

            },
            'rowAddHandler' : function(ul, row) {
                var text = row.last_name + ' ' + row.first_name + ' ' + row.middle_name;
                if(row.ward != null) {
                    text += ', ' + row.ward;
                }
                if(row.enterprise != null) {
                    text += ', ' + row.enterprise;
                }
                $(ul).append($('<li>').text(text));
            },
            'url' : '/guides/employees/get?page=1&rows=10&sidx=id&sord=desc&filters=',
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
            'url' : '/reception/patient/search?onlyingreetings=1&page=1&rows=10&sidx=id&sord=desc&distinct=1&filters=',
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
        'cancelledPatientChooser' : {
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
            'url' : '/reception/patient/search?cancelled=1&page=1&rows=10&sidx=id&sord=desc&distinct=1&filters=',
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
        'mediateChooser' : {
            'primary' : 'id',
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text(row.last_name + ' ' + row.first_name + ' ' + row.middle_name + ', телефон ' + row.phone));
            },
            'displayFunc' : function(row) {
                return row.last_name + ' ' + row.first_name + ' ' + row.middle_name;
            },
            'url' : '/reception/patient/searchmediate/?page=1&rows=10&sidx=id&sord=desc&filters=',
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
        'writtenPatientChooser' : {
            'primary' : 'id',
            'rowAddHandler' : function(ul, row) {
                if (row.is_mediate == 0)
                {
                    // Не опосредованный пациент
                    if(row.card_number != null) {
                        $(ul).append($('<li>').text(row.last_name + ' ' + row.first_name + ' ' + row.middle_name + ', дата рождения ' + row.birthday + ', номер ОМС ' + row.oms_number + ', номер карты ' + row.card_number));
                    } else {
                        $(ul).append($('<li>').text(row.last_name + ' ' + row.first_name + ' ' + row.middle_name + ', дата рождения ' + row.birthday + ', номер ОМС ' + row.oms_number + ', карты нет '));
                    }
                }
                else
                {
                    // Опосредованный пациент
                    if (row.is_mediate == 1)
                    {
                        $(ul).append($('<li>').text(row.last_name + ' ' + row.first_name + ' ' + row.middle_name + ', телефон ' + row.phone));
                    }

                }
            },
            'displayFunc' : function(row) {
                    return row.last_name + ' ' + row.first_name + ' ' + row.middle_name;
            },
            'url' : '/reception/patient/SearchAllWritten/?page=1&rows=10&sidx=last_name&sord=desc&filters=',
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
        'monPatientChooser' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'rowAddHandler' : function(ul, row) {
                if(row.card_number != null) {
                    $(ul).append($('<li>').text(row.last_name + ' ' + row.first_name + ' ' + row.middle_name + ', дата рождения ' + row.birthday + ', номер ОМС ' + row.oms_number + ', номер карты ' + row.card_number));
                } else {
                    $(ul).append($('<li>').text(row.last_name + ' ' + row.first_name + ' ' + row.middle_name + ', дата рождения ' + row.birthday + ', номер ОМС ' + row.oms_number + ', карты нет '));
                }
            },
            'afterInsert' : function()
            {

            },
            'displayFunc' : function(row) {
                return row.last_name + ' ' + row.first_name + ' ' + row.middle_name;
            },
            'url' : '/reception/patient/search?page=1&rows=10&sidx=id&sord=desc&distinct=1&filters=',
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
            'url' : '/guides/mkb10/get?page=1&rows=10&sidx=id&sord=desc&listview=1&nodeid=0&limit=10&is_chooser=1&onlylevel=3&filters=',
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
			//'alwaysLanguage' : 'en',
			'hideEmpty' : true, // Если выбранных значений нет, скрывать блок выбора
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text(row.description));
            },
            'url' : '/guides/mkb10/get?page=1&rows=10&sidx=id&sord=desc&listview=1&nodeid=0&limit=10&is_chooser=1&filters=',
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
		'primaryDiagnosisChooser2' : {
            'primary' : 'id',
            'maxChoosed' : 1,
			//'alwaysLanguage' : 'en',
			'hideEmpty' : true, // Если выбранных значений нет, скрывать блок выбора
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text(row.description));
            },
            'url' : '/guides/mkb10/get?page=1&rows=10&sidx=id&sord=desc&listview=1&nodeid=0&limit=10&is_chooser=1&filters=',
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
            'bindedWindowSelector' : '#addClinicalDiagnosisPopup',
            'afterWindowShow' : function() {
                $('#chooserId').val('primaryClinicalDiagnosisChooser');
            },
            'maxChoosed': 1,
            'rowAddHandler': function (ul, row) {
                $(ul).append($('<li>').text(row.description));
            },
            'url': '/guides/mkb10/getclinical?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
			'hideEmpty' : true, // Если выбранных значений нет, скрывать блок выбора
			//'alwaysLanguage' : 'en',
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text(row.description));
            },
            'url' : '/guides/mkb10/get?page=1&rows=10&sidx=id&sord=desc&listview=1&nodeid=0&limit=10&is_chooser=1&filters=',
            'extraparams' : {
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
		'secondaryDiagnosisChooser2' : {
            'primary' : 'id',
			'hideEmpty' : true, // Если выбранных значений нет, скрывать блок выбора
			//'alwaysLanguage' : 'en',
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text(row.description));
            },
            'url' : '/guides/mkb10/get?page=1&rows=10&sidx=id&sord=desc&listview=1&nodeid=0&limit=10&is_chooser=1&filters=',
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
        'complicationsDiagnosisChooser' : {
            'primary' : 'id',
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text(row.description));
            },
            'url' : '/guides/mkb10/get?page=1&rows=10&sidx=id&sord=desc&listview=1&nodeid=0&limit=10&is_chooser=1&filters=',
            'extraparams' : {
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
        'insuranceRegionsChooserAdd' : {
            'primary' : 'id',
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode( row.code_cladr ) + '] ' +row.name));
            },
            'afterInsert': function()
            {
                $('#insuranceRegionsChooserAdd').trigger('change');
            },
            'afterRemove': function()
            {
                $('#insuranceRegionsChooserAdd').trigger('change');
            },
            'url' :'/guides/cladr/regionget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
        'insuranceRegionsChooserEdit' : {
            'primary' : 'id',
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode( row.code_cladr ) + '] ' +row.name));
            },
            'afterInsert': function()
            {
                $('#insuranceRegionsChooserEdit').trigger('change');
            },
            'afterRemove': function()
            {
                $('#insuranceRegionsChooserEdit').trigger('change');
            },
            'url' :'/guides/cladr/regionget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
        'secondaryClinicalDiagnosisChooser': {
            'primary': 'id',
            'bindedWindowSelector' : '#addClinicalDiagnosisPopup',
            'afterWindowShow' : function() {
                $('#chooserId').val('secondaryClinicalDiagnosisChooser');
            },
            'rowAddHandler': function (ul, row) {
                $(ul).append($('<li>').text(row.description));
            },
            'url': '/guides/mkb10/getclinical?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
            /* 'url' : '/guides/mkb10/get?page=1&rows=10&sidx=id&sord=desc&listview=1&nodeid=0&limit=10&onlylikes=1&filters=', */
            'url' : '/admin/diagnosis/getclinical?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
            'bindedWindowSelector' : $('#addNewCladrRegion'),
            'beforeWindowShow' : function(callback) {
                $.ajax({
                    'url' : '/reception/address/getregionform',
                    'cache' : false,
                    'dataType' : 'json',
                    'type' : 'GET',
                    'success' : function(data, textStatus, jqXHR) {
                        if(data.success == true) {
                            callback();
                        } else {

                        }
                    }
                });
            },
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
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode( row.code_cladr) + '] ' + row.name));

                // Переходим на следующий контрол на странице
            },
            'url' : '/guides/cladr/regionget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
        'regionChooserForDistrict' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'bindedWindowSelector' : $('#addNewCladrRegion'),
            'beforeWindowShow' : function(callback) {
                $.ajax({
                    'url' : '/reception/address/getregionform',
                    'cache' : false,
                    'dataType' : 'json',
                    'type' : 'GET',
                    'success' : function(data, textStatus, jqXHR) {
                        if(data.success == true) {
                            callback();
                        } else {

                        }
                    }
                });
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode( row.code_cladr ) + '] ' + row.name));

                // Переходим на следующий контрол на странице

            },
            'url' : '/guides/cladr/regionget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
        'regionChooserForSettlement' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'bindedWindowSelector' : $('#addNewCladrRegion'),
            'beforeWindowShow' : function(callback) {
                $.ajax({
                    'url' : '/reception/address/getregionform',
                    'cache' : false,
                    'dataType' : 'json',
                    'type' : 'GET',
                    'success' : function(data, textStatus, jqXHR) {
                        if(data.success == true) {
                            callback();
                        } else {

                        }
                    }
                });
            },
            'afterInsert' : function(chooser) {
                if($.fn['regionChooserForSettlement'].getChoosed().length > 0) {
                    var param = $.fn['regionChooserForSettlement'].getChoosed()[0].code_cladr;
                    if($('#districtChooserForSettlement').length > 0) {
                        $.fn['districtChooserForSettlement'].addExtraParam('region', param);
                    }
                }
            },
            'afterRemove' : function() {
                if($('#districtChooserForSettlement').length > 0) {
                    $.fn['districtChooserForSettlement'].clearAll();
                    $.fn['districtChooserForSettlement'].enable();
                    $.fn['districtChooserForSettlement'].deleteExtraParam('region');
                }
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode( row.code_cladr) + '] ' + row.name));

                // Переходим на следующий контрол на странице

            },
            'url' : '/guides/cladr/regionget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
            'bindedWindowSelector' : $('#addNewCladrDistrict'),
            'beforeWindowShow' : function(callback) {
                $.ajax({
                    'url' : '/reception/address/getdistrictform',
                    'cache' : false,
                    'dataType' : 'json',
                    'type' : 'GET',
                    'success' : function(data, textStatus, jqXHR) {
                        if(data.success == true) {
                            callback();
                        } else {

                        }
                    }
                });
            },
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
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode( row.code_cladr) + '] ' + row.name));
            },
            'loadCompleteHandler' : function (chooserId)
            {
                $(  '#'+chooserId).trigger('rowadd');
            },
            'url' : '/guides/cladr/districtget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
        'districtChooserForSettlement' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'bindedWindowSelector' : $('#addNewCladrDistrict'),
            'beforeWindowShow' : function(callback) {
                $.ajax({
                    'url' : '/reception/address/getdistrictform',
                    'cache' : false,
                    'dataType' : 'json',
                    'type' : 'GET',
                    'success' : function(data, textStatus, jqXHR) {
                        if(data.success == true) {
                            callback();
                        } else {

                        }
                    }
                });
            },
            'extraparams' : {
                //'region' : $.fn['regionChooser'].getChoosed()
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode(row.code_cladr )+ '] ' + row.name));
            },
            'url' : '/guides/cladr/districtget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
        'districtChooserForStreet' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'bindedWindowSelector' : $('#addNewCladrDistrict'),
            'beforeWindowShow' : function(callback) {
                $.ajax({
                    'url' : '/reception/address/getdistrictform',
                    'cache' : false,
                    'dataType' : 'json',
                    'type' : 'GET',
                    'success' : function(data, textStatus, jqXHR) {
                        if(data.success == true) {
                            callback();
                        } else {

                        }
                    }
                });
            },
            'afterInsert' : function(chooser) {
                if($.fn['districtChooserForStreet'].getChoosed().length > 0) {
                    var param = $.fn['districtChooserForStreet'].getChoosed()[0].code_cladr;
                    if($('#settlementChooserForStreet').length > 0) {
                        $.fn['settlementChooserForStreet'].addExtraParam('district', param);
                    }
                }
            },
            'afterRemove' : function() {
                if($('#settlementChooserForStreet').length > 0) {
                    $.fn['settlementChooserForStreet'].clearAll();
                    $.fn['settlementChooserForStreet'].enable();
                    $.fn['settlementChooserForStreet'].deleteExtraParam('district');
                }
            },
            'extraparams' : {
                //'region' : $.fn['regionChooser'].getChoosed()
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode( row.code_cladr) + '] ' + row.name));
            },
            'url' : '/guides/cladr/districtget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
        'regionChooserForStreet' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'bindedWindowSelector' : $('#addNewCladrRegion'),
            'beforeWindowShow' : function(callback) {
                $.ajax({
                    'url' : '/reception/address/getregionform',
                    'cache' : false,
                    'dataType' : 'json',
                    'type' : 'GET',
                    'success' : function(data, textStatus, jqXHR) {
                        if(data.success == true) {
                            callback();
                        } else {

                        }
                    }
                });
            },
            'afterInsert' : function(chooser) {
                if($.fn['regionChooserForStreet'].getChoosed().length > 0) {
                    var param = $.fn['regionChooserForStreet'].getChoosed()[0].code_cladr;
                    if($('#districtChooserForStreet').length > 0) {
                        $.fn['districtChooserForStreet'].addExtraParam('region', param);
                    }
                    if($('#settlementChooserForStreet').length > 0) {
                        $.fn['settlementChooserForStreet'].addExtraParam('region', param);
                    }
                }
            },
            'afterRemove' : function() {
                if($('#districtChooserForStreet').length > 0) {
                    $.fn['districtChooserForStreet'].clearAll();
                    $.fn['districtChooserForStreet'].enable();
                    $.fn['districtChooserForStreet'].deleteExtraParam('region');
                }

                if($('#settlementChooserForStreet').length > 0) {
                    $.fn['settlementChooserForStreet'].clearAll();
                    $.fn['settlementChooserForStreet'].enable();
                    $.fn['settlementChooserForStreet'].deleteExtraParam('region');
                    $.fn['settlementChooserForStreet'].deleteExtraParam('district');
                }
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode( row.code_cladr ) + '] ' + row.name));

                // Переходим на следующий контрол на странице

            },
            'url' : '/guides/cladr/regionget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
            'bindedWindowSelector' : $('#addNewCladrSettlement'),
            'beforeWindowShow' : function(callback) {
                $.ajax({
                    'url' : '/reception/address/getsettlementform',
                    'cache' : false,
                    'dataType' : 'json',
                    'type' : 'GET',
                    'success' : function(data, textStatus, jqXHR) {
                        if(data.success == true) {
                            callback();
                        } else {

                        }
                    }
                });
            },
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
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode(row.code_cladr ) + '] ' + row.name));
            },
            'loadCompleteHandler' : function (chooserId)
            {
                $(  '#'+chooserId).trigger('rowadd');
            },
            'url' : '/guides/cladr/settlementget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
        'settlementChooserForStreet' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'bindedWindowSelector' : $('#addNewCladrSettlement'),
            'beforeWindowShow' : function(callback) {
                $.ajax({
                    'url' : '/reception/address/getsettlementform',
                    'cache' : false,
                    'dataType' : 'json',
                    'type' : 'GET',
                    'success' : function(data, textStatus, jqXHR) {
                        if(data.success == true) {
                            callback();
                        } else {

                        }
                    }
                });
            },
            'extraparams' : {
                //'region' : $.fn['regionChooser'].getChoosed(),
                //'district' : $.fn['districtChooser'].getChoosed()
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode(row.code_cladr) + '] ' + row.name));
            },
            'url' : '/guides/cladr/settlementget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
            'bindedWindowSelector' : $('#addNewCladrStreet'),
            'beforeWindowShow' : function(callback) {
                $.ajax({
                    'url' : '/reception/address/getstreetform',
                    'cache' : false,
                    'dataType' : 'json',
                    'type' : 'GET',
                    'success' : function(data, textStatus, jqXHR) {
                        if(data.success == true) {
                            callback();
                        } else {

                        }
                    }
                });
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode( row.code_cladr )+ '] ' + row.name));
            },
            'afterInsert': function() {

            },
            'afterRemove' : function() {

            },
            'url' : '/guides/cladr/streetget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
        'regionPolicyChooser' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'afterInsert' : function()
            {
                $('#policyRegionHidden input').val($.fn['regionPolicyChooser'].getChoosed()[0].id);
                // Добавляем в поле доп параметров чюююзера "Страховая компания" id региона
                $.fn['insuranceChooser'].addExtraParam('region_insurance',
                    $.fn['regionPolicyChooser'].getChoosed()[0].id);
            },
            'afterRemove' : function() {
                $('#policyRegionHidden input').val('');
                $.fn['insuranceChooser'].deleteExtraParam('region_insurance');
                // Зануляем поле дополнительных параметров чюююзера "Страховая компания"
                if($('#insuranceChooser').length > 0) {
                    $.fn['insuranceChooser'].clearAll();
                    $.fn['insuranceChooser'].enable();
                    $('#insuranceChooser input').val('');
                    $('#insuranceChooser .variants').addClass('no-display');
                    $('#insuranceChooser .variants').css('display', '');
                    $.fn['insuranceChooser'].deleteExtraParam('region');
                }

            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode(row.code_cladr) + '] ' + row.name));
            },
            'url' : '/guides/cladr/regionget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode(row.code_cladr) + '] ' + row.name));
            },
            'url' : '/guides/cladr/regionget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode( row.code_cladr )+ '] ' + row.name));
            },
            'url' : '/guides/cladr/districtget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode(row.code_cladr) + '] ' + row.name));
            },
            'url' : '/guides/cladr/settlementget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
                $(ul).append($('<li>').text('[' + $.fn.reduceCladrCode(row.code_cladr) + '] ' + row.name));
            },
            'afterInsert': function() {

            },
            'afterDelete' : function() {

            },
            'url' : '/guides/cladr/streetget?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
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
        'insuranceChooser' : {
            'primary' : 'id',
            'maxChoosed' : 1,
            'url' : '/guides/insurances/get?page=1&rows=10&sidx=id&sord=desc&listview=1&nodeid=0&limit=10&is_chooser=1&filters=',
            'extraparams' : {

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
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text(row.name));
            },
            'afterInsert' : function()
            {
                $('#insuranceHidden input').val($.fn['insuranceChooser'].getChoosed()[0].id);
            },
            'afterRemove' : function() {
                $('#insuranceHidden input').val('');
            }
        },
        'userChooser' : {
            'primary' : 'id',
            'url' : '/admin/users/get?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
            'extraparams' : {
            },
            'filters' : {
                'groupOp' : 'AND',
                'rules': [
                    {
                        'field' : 'login',
                        'op' : 'cn',
                        'data' : ''
                    }
                ]
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text('ID ' + row.id + ', логин ' + row.login));
            },
            'afterInsert' : function() {

            },
            'afterRemove' : function() {

            }
        },
        'wardChooser' : {
            'primary' : 'id',
            'url' : '/guides/wards/get?page=1&rows=10&sidx=id&sord=desc&limit=10&filters=',
            'extraparams' : {
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
            },
            'rowAddHandler' : function(ul, row) {
                $(ul).append($('<li>').text(row.name));
            },
            'afterInsert' : function() {

            },
            'afterRemove' : function() {

            }
        }
    };
});