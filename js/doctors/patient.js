$(document).ready(function () {

    globalVariables.isUnsavedUserData = false;  // Есть ли несохранённые данные у пользователя
    globalVariables.wasUserFocused = false; // Был ли фокус на каком-то элементе
    globalVariables.savingProcessing = false;
    //    флаги isUnsavedUserData и wasUserFocused работают в связке
	showMsgs = true;
    showLeaveNotice = false; // Show notice about leaving page

    // Modal window, when page prepares to reload
    var href = null;
    var callback = null;
    $(document).on('click', '.showPatientGreetingLink',  function(e) {
        href = $(this).prop('href');
        $('#noticeLeavePopup').modal({});
        return false;
    });

    $('#leaveYesSubmit').on('click', function(e) {
        if(href) {
            location.href = href;
        }
        if(callback) {
            $('#noticeLeavePopup').modal('hide');
            callback();
            callback = null;
        }
    });

    $('#noticeLeavePopup .btn-default').on('click', function(e) {
        callback = null;
        href = null;
        $('#noticeLeavePopup').modal('hide');
    });

    $('#noticeLeavePopup').on('click', function(e) {
        return false;
    });

    $(document).on('click', '#change-date-form .day', function(e) {
        $('#noticeLeavePopup').modal({});
        e.preventDefault();
        return false;
    });


    $(window).on('beforeunload', function (e) {
        // Если есть несохранённые данные - спрашиваем, нужно ли их сохранить
        return globalVariables.isUnsavedUserData ? 'В приёме остались несохранённые данные. Если Вы хотите их сохранить - нажмите "остаться на странице" и сохраните данные.' : '';
    });

    globalVariables.numCalls = 0; // Одна или две формы вызвались. Делается для того, чтобы не запускать печать два раза
    // Редактирование медкарты
    $(".template-edit-form").on('success', function (eventObj, dataFromQuery, status, jqXHR) {
        var ajaxData = { 'success':false };
        ajaxData = $.parseJSON(dataFromQuery);
        onSectionSave(ajaxData);
    });

    globalVariables.isSavingErrors = false;
    // Вызывается при событии сохранения одной секции приёма (шаблона или диагнозов)
    function onSectionSave(ajaxData) {

        if(!ajaxData.success) {
            // Поднимаем флаг, что есть ошибки
            globalVariables.isSavingErrors = true;
        }

        if ($(".submitEditPatient").length == globalVariables.numCalls) {
            // Сбрасываем, что есть несохранённые данные
            globalVariables.isUnsavedUserData = false;
            globalVariables.savingProcessing = false;
            // Сбрасываем режим на дефолт
            globalVariables.numCalls = 0;
            // Если класс контента приёма имеет класс неотображения, это было сохранение при смене врача
            $('.backDropForSaving').remove();
            $('.modal-backdrop').hide();

            getNewHistory();

            // Проверка на то, есть ли ошибки
            if (!globalVariables.isSavingErrors) {
                if (isThisPrint) {
                    onSaveComplete();
                } else {
                    if(showMsgs) {
                        ///$('#successEditPopup').modal({});
                    } else {
                        showMsgs = true;
                        setTimeout(autoSave, 30000);
                    }
                }
            } else  {
                if(showMsgs || isThisPrint) {
                    // Выводим сообщение, что произошла какая-то ошибка
                    alert ('Извините, при сохранении произошли ошибки. Попробуйте ещё раз сохранить приём');
                }
            }
            globalVariables.isSavingErrors = false;
        } else {
            ++globalVariables.numCalls;
        }

        // Вставляем новую запись в список истории
        if (ajaxData.hasOwnProperty('historyDate')) {
            var newDiv = $('<div>');
            $(newDiv).append($('<a>').prop('href', '#' + globalVariables.medcardNumber + '_' + ajaxData.lastRecordId).attr('class', 'medcard-history-showlink').text(ajaxData.historyDate + ' - ' + ajaxData.templateName));
            var historyContainer = $('#accordionH .accordion-inner div:first');
            if (historyContainer.length == 0) {
                $('#accordionH .accordion-inner').append(newDiv);
            }
            else {
                $('#accordionH .accordion-inner div:first').before(newDiv);
            }
        }
    }

    // Функция печати и самого приёма и рекоммендаций (т.е. всего, что выбрано в поп-апе)
    function printAllPopup() {
        // Если выбран "Приём" - запускаем печать приёма
        if ( $('#greetingPrintNeed input:checked').length>0 ) {
            var id = $('#greetingId').val();
            var printWin = window.open('/doctors/print/printgreeting/?greetingid=' + id, '', 'width=800,height=600,menubar=no,location=no,resizable=no,scrollbars=yes,status=no,left=50,top=50');
            $(printWin).on('load',
                function () {
                    this.focus();
                }
            );
        }

        // Перебираем отмеченные шаблоны из рекоммендаций и по очереди вызываeм печать этих шаблонов
        recommendationsChecboxes = $('#recommendationTemplatesPrintNeed input:checked');
        var left = 170;
        var top = 145;
        for(i = 0; i < recommendationsChecboxes.length; i++) {
            // Вызываем печать каждого шаблона рекоммендаций
            // Берём номер шаблона
            templateId = recommendationsChecboxes[i].value;
            printTemplateRecommendation(templateId, left, top);
            top += top;
            left += left;
        }
    }

    $('#printPopupButton').on('click',function(e){
        printAllPopup();
    });

    function printTemplateRecommendation(templateNumber, left, top) {
        if(typeof left == 'undefined') {
            left = 0;
        }
        if(typeof top == 'undefined') {
            top = 0;
        }
        var id  = $('#greetingId').val();
        var printWin = window.open('/doctors/print/printgreeting/?templateId='+ templateNumber +'&printRecom=1&greetingid=' + id, '', 'width=800,height=600,menubar=no,location=no,resizable=no,scrollbars=yes,status=no,left=' + left + ',top=' + top );
        $(printWin).on('load',
            function () {
                this.focus();
            }
        );
    }

	function onSaveComplete() {
        if (printHandler == 'print-greeting-link') {
            $('.activeGreeting .' + printHandler).trigger('print');
        } else {
            if (printHandler == 'print-recomendation-link') {
                $('.' + printHandler).trigger('print');
            } else {
                // Закрываем приём
                onCloseGreetingStart();
            }
        }
    }

    function getNewHistory()
    {
        // Достанем номер карты
        cardNumber = $('#currentPatientId').val();

        // Отправим аякс
        $.ajax({
            'url' : '/doctors/shedule/gethistorypoints',
            'data' : {
                'medcardid' : cardNumber
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    var data = data.data;
                    var hisArr = data;
                    var historyContainer = $('#accordionH .accordion-inner div:first');
                    $('#accordionH .accordion-inner').text('');
                    for (i = hisArr.length - 1; i >= 0; i--) { // (идём в обратном порядке)
                        var newDiv = $('<div>');
                        $(newDiv).append(
                            $('<a>')
                                .prop('href', '#' + globalVariables.medcardNumber + '_' + hisArr[i].greeting_id +'_'+ hisArr[i].template_id)
                                .attr('class', 'medcard-history-showlink')
                                .text(hisArr[i].date_change + ' - ' + hisArr[i].template_name)
                        );
                        var historyContainer = $('#accordionH .accordion-inner div:first');
                        if (historyContainer.length == 0) {
                            $('#accordionH .accordion-inner').append(newDiv);
                        } else {
                            $('#accordionH .accordion-inner div:first').before(newDiv);
                        }
                    }
                }
                return;
            }
        });
    }
    $('#medcardContentSave, #sideMedcardContentSave').on('click', function (e) {
        isThisPrint = false;
        printHandler = 'accept-greeting-link';
        onStartSave();
		e.stopPropagation();
    });
	
	
	/* Автосохранение */
	setTimeout(autoSave, 30000);
	
	function autoSave() {
		if($('.greetingContentCont').hasClass('no-display')) { // Если ничего не отображается, то и сохранять не надо
			return false;
		}
		isThisPrint = false;
		showMsgs = false;
		onStartSave(true);
	}

    $('.greetingStatusCell input').on('change',function(){
        idOfRadio = $(this).prop('id');
        newValue = $('input[id=' + idOfRadio +  ']:checked')[0].value;
        console.log('Статус поменялся '+ newValue);

        // Читаем id приёма
        idGreeting = idOfRadio.substr(14);
        console.log('ИД приёма='+idGreeting);
        // Вот теперь вообще всё хорошо и больше ничегоот жизни не надо!
        //   Вернее больше ничего не надо для ajax-запроса!
        //   Есть новое значение статуса приёма и ИД самого приёма
        //  Самое время отправить ajax-запрос
        $.ajax({
            'url' : '/doctors/shedule/changegreetingstatus?greetingId='
                +idGreeting
                +'&newValue='
                +newValue,
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {

            }
        });
    });

    // Метод, который выполняет только сохранение. Его использовать при вызове сохранения
    function onStartSave(overlaySuck) { // overlaySuck - флаг, нужен ли оверлей
        if (globalVariables.savingProcessing) {
            return;
        }

        globalVariables.savingProcessing = true;

        // Берём кнопки с классом
        var buttons = $('div.submitEditPatient');
        var buttonsContainers = $('div.submitEditPatient').parents('form.template-edit-form');
        var isError = false;

        // Очищаем поп-ап с ошибками
        $('#errorPopup .modal-body .row').html("");
        // Если кнопок нет - сразу вызываем функцию
        if (buttonsContainers.length == 0) {
            onSaveComplete();
        } else {
            // Show the backdrop
            if (!overlaySuck) {
                $('<div class="modal-backdrop fade in backDropForSaving"></div>').appendTo(document.body);
            }

            for (var i = 0; i < buttonsContainers.length; i++) {
                // Имеем i-тую форму, контролы которой надо провалидировать
                var controlElements = $(buttonsContainers[i]).find('div.form-group:not(.submitEditPatient)').filter(function(index) {
                    return $(this).parents('#patient-medcard-edit-form').length == 0
                        && $(this).parents('#add-greeting-value-form') == 0
                        && $(this).parents('#add-value-form') == 0;
                        // Чтобы не попало окно с данными медкарты и добавления
                }).has('label span.required');

                for (j = 0; j < controlElements.length; j++) {
                    // Внутри контейнера с контролом ищу сам контрол
                    var oneControlElement = $(controlElements[j]).find('input[type=text],input[type=number], textarea, select');
                    // Проверим - есть ли данного контрола значение
                    if ($(oneControlElement[0]).val() == '' || $(oneControlElement[0]).val() == null) {
                        isError = true;
                        $(oneControlElement[0]).animate({
                            backgroundColor: "rgb(255, 196, 196)"
                        });
                        // В следующий раз, когда значение у контрола поменяется - надо сбросить css-совйсто
                        $(oneControlElement[0]).one('change', function () {
                            $(this).css('background-color', '');
                        });
                        // И на keydown тоже самое поставим
                        $(oneControlElement[0]).one('keydown', function () {
                            $(this).css('background-color', '');
                        });

                        // Вытащим метку данного элемента
                        var labelOfControl = ($(controlElements[j]).find('label').text()).trim();
                        // Вытащим заголовок категории, чтобы указать место, где заполнять
                        var categorieTitle = $(oneControlElement).parents('.accordion');

                        // Если последний символ в строке звёздочка - обрезаем её
                        if (labelOfControl[labelOfControl.length - 1] == '*') {
                            labelOfControl = labelOfControl.substring(0, labelOfControl.length - 1);
                        }
                        labelOfControl = labelOfControl.trim();
                        // Если последний символ в строке двоеточие - обрезаем его
                        if (labelOfControl[labelOfControl.length - 1] == ':') {
                            labelOfControl = labelOfControl.substring(0, labelOfControl.length - 1);
                        }
                        // Добавим в поп-ап сообщение из ошибки
                        $('#errorPopup .modal-body .row').append("<p>" + 'Поле \"' + labelOfControl + '\" должно быть заполнено' + "</p>")
                    }
                }
            }

            // Если есть ошибки
            if (isError) {
                // Показываем поп-ап с ошибками
                if(showMsgs) {
                    $('#errorPopup').modal({});
                }
                // Давим событие нажатия клавиши
                return false;
            }
            else {
                // Вызываем сабмит всех кнопок
                $(buttons).find('input[type="submit"]').click();
                $('#submitDiagnosis').click();
            }
        }
    }


    $("#date-cont").on('changeDate', function (e) {
        callback = function() {
            $('#filterDate').val(e.date.getFullYear() + '-' + (e.date.getMonth() + 1) + '-' + e.date.getDate());
            $('#change-date-form').submit();
        }
        $('#noticeLeavePopup').modal({});
    });
	
    $("#date-cont").trigger("refresh");

    $("#date-cont").on('changeMonth', function (e) {
        $("#date-cont").trigger("refresh", [e.date]);
    });

    // Сжатие-расширение селект-контролов
    expandSelectTimer = null;
    selectToExpand = null;

    $('form.template-edit-form select[multiple]').mouseenter(
        function(e) {
            onActivate(this);
        }
    );

    $('form.template-edit-form select[multiple]').mousemove(
        function(e) {
            onActivate(this);
        }
    );

    function onActivate(element) {
        selectToExpand = element;
        // Запускаем таймер
        if(wasScroll) {
            expandSelectTimer = setTimeout(expandSelect,250);
        } else {
            expandSelectTimer = setTimeout(expandSelect,3000);
        }
    }

    $(document).on('focus','form.template-edit-form select[multiple]',
        function(e){
           expandSelect();
        }
    );

    function expandSelect() {
        clearTimeout(expandSelectTimer);
        expandSelectTimer = null;
        if (wasScroll)  {
            expandSelectTimer = setTimeout(expandSelect,2000);
        } else{
            $(selectToExpand).attr("size", $(selectToExpand).find('option').length );
        }
    }

    $(document).on('blur','form.template-edit-form select[multiple]',
        function(e)
        {
            // Нужно удалить расширение
          $(this).removeAttr("size");
          clearTimeout(expandSelectTimer);
            expandSelectTimer = null;
        }
    );

    $('form.template-edit-form select[multiple]').mouseleave(
        function(e)
        {
            clearTimeout(expandSelectTimer);
            expandSelectTimer = null;
            // Убираем расширение только если this не в фокусе
            if ( ! $(e.currentTarget).is(':focus') )
            {
                $(e.currentTarget).removeAttr("size");
            }
        }
    );

    wasScroll = false;

    $(document).on('scroll',
        function(e) {
            wasScroll = true;
            setTimeout(function(){
                wasScroll = false;
            },5000);
        }
    );

    expandingTimer = setInterval(onExpandTimerTick,250);
    isCursorInElement = false;
    elementUnderCursorOld = null;
    elementUnderCursor = null;
    ticksAfterCursor = 0;

    function collapseCursorElement(){
        if (  $(elementUnderCursor).is(elementUnderCursorOld)==false  ){
            $('.expandedElement:not(:focus)').removeAttr("size")
            $('.expandedElement:not(:focus)').removeClass('expandedElement');
        }
    }

    function onExpandTimerTick() {
        ticksAfterCursor--;
        if (ticksAfterCursor>0) {
            return;
        }

        if (isCursorInElement) {
            // Смотрим - если старый элемент не соотносится с новым, то нужно спрятать раскрытые элементы
            //    c классом expandedElement, кроме сфокусированного
            collapseCursorElement();
            if (elementUnderCursor!=null) {
            // Раскрываем элемент, ставим ему класс
                $(elementUnderCursor).attr("size", $(elementUnderCursor).find('option').length );
                $(elementUnderCursor).addClass('expandedElement');
            }
        } else {
            collapseCursorElement();
        }
        elementUnderCursorOld = elementUnderCursor;
    }

    $(document).on('mouseenter','form.template-edit-form select[multiple]',
        function(e) {
            isCursorInElement = true;
            elementUnderCursor = this;
        }
    );

    $(document).on('mouseleave','form.template-edit-form select[multiple]',
        function(e)
        {
            isCursorInElement = false;
            elementUnderCursor = null;
        }
    );

    oldClientX = 0;
    oldClientY = 0;

    $(document).on('mousemove','form.template-edit-form select[multiple]',
        function(e) {
            if  (
                ( (oldClientX- e.clientX>10 )||(oldClientY- e.clientY>10 ) )
                    ||
                ( (oldClientX- e.clientX<-10 )||(oldClientY- e.clientY<-10 ) )
                )
            {
                ticksAfterCursor = 0;
            }

            oldClientX = e.clientX;
            oldClientY = e.clientY;
        }

    );

    $(document).on('scroll',
        function(e) {
            ticksAfterCursor = 36;
        }
    );

    $('form.template-edit-form select').on('keydown',
        function(e)
        {
            // Если кнопка delete
            if (e.keyCode==46 || e.keyCode==8)
            {
                // Если в текущем селекте выделена хотя бы одна опция
                selectedOptions = $(this).find('option:selected');

                //console.log(selectedOptions );
                // У каждой опции хранится в поле value номер элемента, который нужно удалить
                //    Перебираем выделенные опции
                for (i=0;i<selectedOptions.length;i++)
                {
                    deletedFlag = false;
                    valueOfOption = selectedOptions[i].value;
                    // Вызываем удаление опции
                    deletedFlag = deleteOption(valueOfOption);
                    if (deletedFlag)
                    {
                        // Удаляю опцию
                        $(this).find('option[value='+ valueOfOption +']').remove();
                    }
                }
            }
        }
    );

    function deleteOption(valueOfOption)
    {
        deleteResult = false; // false - нифига не удалили

        // Запускаем синхронный айкс-запрос
        $.ajax({
            'url': '/admin/guides/deleteinguidegreeting?id=' + valueOfOption + '&greeting=' + $('#greetingId').val(),
            'cache': false,
            'dataType': 'json',
            'type': 'GET',
            'async': false,
            'success': function (data, textStatus, jqXHR) {
                // Если true - то удаление произошло
                if (data.success == true || data.success == 'true') {
                    deleteResult = true;
                }
            }
        });
        return deleteResult;
    }

    $("#date-cont").on('refresh', function (e, date) {
        if (typeof date == 'undefined') {
            var currentDate = $('#filterDate').val();
            var currentDateParts = currentDate.split('-');
        } else {
            var dateObj = new Date(date);
            var currentDateParts = [dateObj.getFullYear(), dateObj.getMonth() + 1, dateObj.getDay() + 1];
        }

        var daysWithPatients = globalVariables.patientsInCalendar;
        $('.day-with').removeClass('day-with');
        for (var i in daysWithPatients) {
            var parts = daysWithPatients[i].patient_day.split('-'); // Год-месяц-день
            if (parseInt(currentDateParts[0]) == parseInt(parts[0]) && parseInt(currentDateParts[1]) == parseInt(parts[1])) {
                $(".day" + parseInt(parts[2])).filter(':not(.new)').filter(':not(.old)').addClass('day-with');
            }
        }
    });
	
    $('#date-cont').trigger('refresh');

    $(document).on('click', '.accept-greeting-link', function (e) {
        // Выводим сообщение о том, что нужно вывест
        $('#closeGreetingPopup p').html('Вы действительно хотите закончить этот приём?');
        $('#closeGreetingPopup').modal({});
    });

    $('.closeGreetingPopupButton').on('click',function(e){
        startAcceptGreeting();
    });

function startAcceptGreeting() {
    // Ставим анимацию вместо кнопки "Закончить приём"
    var gif = generateAjaxGif(16, 16);
    // Делаем невидимым флажок
    $(this).addClass('no-display');
    $($(this).parents()[0]).append(gif);

    printHandler = 'accept-greeting-link';
    isThisPrint = true;
    acceptGreeting();
}

focusDiagnosisPlease = false;
function acceptGreeting() {
    if($('#primaryDiagnosisChooser').length > 0 && $.fn['primaryDiagnosisChooser'].getChoosed().length == 0) {
        var needDiagMsq = '';
        for (var i in globalVariables.reqDiagnosis) {
            if (globalVariables.reqDiagnosis[i].isReq) {
                needDiagMsq += globalVariables.reqDiagnosis[i].name + ', ';
            }
        }

        if (needDiagMsq) {
            needDiagMsq = 'Основной диагноз не установлен! Следующие шаблоны требуют установки основного диагноза: <strong>' + needDiagMsq.substr(0, needDiagMsq.length - 2) + '</strong>';
            $('#errorPopup .modal-body .row').html("<p>" + needDiagMsq + "</p>");
            $('#errorPopup').modal({});

            // Перебрасываем фокус на диагноз
            destinationAnchor = $('#accordionD')[0].offsetTop;
            ;
            $('body,html').animate({
                scrollTop: destinationAnchor
            }, 599);

            focusDiagnosisPlease = true;
            return false;
        }
    }

    var greetingId = $('.activeGreeting .accept-greeting-link').prop('id').substr(2);
    $.ajax({
        'url' : '/doctors/shedule/acceptcomplete/?id=' + greetingId,
        'cache' : false,
        'dataType' : 'json',
        'type' : 'GET',
        'success' : function(data, textStatus, jqXHR) {
            if(data.success) {
                printHandler = 'print-greeting-link';
                onStartSave();
            } else {
                // Выводим сообщение о том, что нужно вывест
                $('#errorPopup .modal-body .row').html("<p>" + data.text + "</p>");
                $('#errorPopup').modal({
                });
            }
            // Снимаем крутилку с флажка "Закрытия приёма" и блокируем все поля для ввода
            onGreetingClosingEnd();
        }
    });
}

$('#errorPopup').on('hidden.bs.modal', function (e){
    if (focusDiagnosisPlease) {
        $('#primaryDiagnosisChooser input[type=text]').focus();
        focusDiagnosisPlease = false;
    }
});

function onCloseGreetingStart() {
    acceptGreeting();
}

function onGreetingClosingEnd() {
    $('.accept-greeting-link').remove();
    $('.template-edit-form').find('input, button, select, textarea').prop('disabled', true);
}

$(document).on('click', '.medcard-history-showlink', function (e) {
    $(this).parents('.accordion-inner:eq(0)').find('.active').removeClass('active').find('img').remove();
    var gif = generateAjaxGif(16, 16);
    $(this).parent().addClass('active').append(gif);

    var historyPointCoordinate = $(this).attr('href').substr(1);
    var coordinateStrings = historyPointCoordinate.split('_');

    var medcard = coordinateStrings[0];
    var greeting = coordinateStrings[1];
    var template = coordinateStrings[2];
    var date = $(this).text();
    $('#historyPopup .medcardNumber').text('№ ' + medcard);
    $('#historyPopup .historyDate').text(date);
    $.ajax({
        'url': '/doctors/patient/gethistorymedcard',
        'data': {
            medcardId: medcard,
            greetingId: greeting,
            templateId: template
        },
        'cache': false,
        'dataType': 'json',
        'type': 'GET',
        'error': function (data, textStatus, jqXHR) {
            console.log(data);
        },
        'success': function (data, textStatus, jqXHR) {
            console.log(data);
            if (data.success == 'true') {
                // Заполняем медкарту-историю значениями
                var historyContent = data.data;
                $(gif).remove();
                $('#historyPopup .modal-body .row').html(historyContent);
                $('#historyPopup').modal({
            });
        }
    }
});
});

$('#historyPopup').on('shown.bs.modal', function (e) {
    var deps = filteredDeps;
    for (var i = 0; i < deps.length; i++) {
        mainDependenceElementsSet = $('#historyPopup select[id$="_' + deps[i].elementId + '"]');
        // В истории могут быть несколько элементов с одним и тем  же id, поэтому
        //   в переменной mainDependenceElementsSet могут храниться один или больше элемент
        for (j=0;j<mainDependenceElementsSet.length;j++)    {
            // Проверяем - если элемент мультиселектовый, то считаем, что его значение
            //     - это все опшены, которые есть внутри его
            var elementValue = '';

            if ($(mainDependenceElementsSet[j]).attr('multiple')) {
                // Берём все опшены, запихиваем в json
                optionsSelected = $(mainDependenceElementsSet[j]).find('option');
                optionsSelectedArray = [];
                for (j=0;j<optionsSelected.length;j++)  {
                    optionsSelectedArray.push( $($(optionsSelected)[j]).attr('value')  );
                }
                elementValue = $.toJSON(optionsSelectedArray);
            } else {
                elementValue = $(mainDependenceElementsSet[j]).val();
            }
            parentAccordion = $(mainDependenceElementsSet[j]).parents('.accordion:eq(0)');
            changeControlState(deps[i], elementValue, parentAccordion);
        }
    }
});

$('#historyPopup').on('hidden.bs.modal', function (e) {
    $('#historyPopup .modal-body .row').css('text-align', 'left');
});

$('.print-greeting-link').on('click', function (e) {
    printHandler = 'print-greeting-link';
    // После закрытия окна начинать сохранение медкарты и печать листа приёма
    isThisPrint = true;
    // Если нет кнопки "сохранить" - вызываем печать сразу
    if ($('.submitEditPatient input').length<=0) {
        $('.activeGreeting .print-greeting-link').trigger('print');
    } else {  // Иначе вызываем процедуру сохранения
        onStartSave();
    }
});

$('.print-greeting-link').on('print', function (e) {
    printDataToPrintPopup();
    $('#greetingPrintNeed input').attr('checked', '');
    // Отмечаем пункт "Приём", а остальные - нет
    $('#whatPrinting').modal({});
    return false;
});

$('.print-recomendation-link').on('click', function (e) {
    printHandler = 'print-recomendation-link';
    isThisPrint = true;
    onStartSave();
});

var isThisPrint = false;

$('#successEditPopup').on('show.bs.modal', function (e) {
    // Если это режим печати, то показывать окно успешности редактирования не надо
    if (isThisPrint) {
        //isThisPrint = false;
        return false;
    }
});

$('#printContentButton, #sidePrintContentButton').on('click', function (e) {
    $('.print-greeting-link').click();
    e.stopPropagation();
});

$('#printPopup .btn-success').on('click', function (e) {
    $('.activeGreeting .' + printHandler).trigger('print');
    isThisPrint = false;
});

function printDataToPrintPopup() {
    // Делаем синхронный Ajax-запрос, разбираем данные, которые он вернул и выводим в поп-ап
    $.ajax({
        'url': '/doctors/print/getrecommendationtemplatesingreeting?greetingId='  + $('#greetingId').val(),
        'cache': false,
        'dataType': 'json',
        'type': 'GET',
        'async': false,
        'success': function (data, textStatus, jqXHR) {
            // Если true - то удаление произошло
            if (data.success == true || data.success == 'true') {
                console.log(data);
                // Перебираем строки шаблона и выводим по чекбоксу для каждого шаблона
                templates = data.data;
                // Очистим блок с шаблонами
                $('#recommendationTemplatesPrintNeed p').empty();
                for (i=0;i<templates.length;i++)
                {
                    newChecboxRow = $('<input type="checkbox" name="recTemplate'+ templates[i].template_id +'" value="'+
                        templates[i].template_id
                        +'">');

                    $('#recommendationTemplatesPrintNeed p').append(newChecboxRow);
                    $('#recommendationTemplatesPrintNeed p').html(
                        $('#recommendationTemplatesPrintNeed p').html() + templates[i].template_name+'<br>'
                    );
                }
            }
        }
    });


}


// Печать листа приёма, само действие
$('.print-recomendation-link').on('print', function (e) {
    printDataToPrintPopup();
    $('#greetingPrintNeed input').removeAttr('checked');
    $('#recommendationTemplatesPrintNeed input').attr('checked', '');
    // Отмечаем все пункты, кроме "Приём"
    $('#whatPrinting').modal({});
    return false;
});

// Сохранение диагнозов
$('#submitDiagnosis').on('click', function (e) {
    var choosedPrimary = $.fn['primaryDiagnosisChooser'].getChoosed();
    var choosedSecondary = $.fn['secondaryDiagnosisChooser'].getChoosed();

    var choosedClinPrimary = $.fn['primaryClinicalDiagnosisChooser'].getChoosed();
    var choosedClinSecondary = $.fn['secondaryClinicalDiagnosisChooser'].getChoosed();

    var choosedComplicating = $.fn['complicationsDiagnosisChooser'].getChoosed();

    var primaryIds = [];
    var secondaryIds = [];
    for (var i = 0; i < choosedPrimary.length; i++) {
        primaryIds.push(choosedPrimary[i].id);
    }
    for (var i = 0; i < choosedSecondary.length; i++) {
        secondaryIds.push(choosedSecondary[i].id);
    }


    var clinPrimaryIds = [];
    var clinSecondaryIds = [];
    for (var i = 0; i < choosedClinPrimary.length; i++) {
        clinPrimaryIds.push(choosedClinPrimary[i].id);
    }
    for (var i = 0; i < choosedClinSecondary.length; i++) {
        clinSecondaryIds.push(choosedClinSecondary[i].id);
    }

    var complicatingIds = [];
    for (var i = 0; i < choosedComplicating.length; i++) {
        complicatingIds.push(choosedComplicating[i].id);
    }

    $.ajax({
        'url': '/doctors/patient/savediagnosis',
        'data': {
            'primary': $.toJSON(primaryIds),
            'secondary': $.toJSON(secondaryIds),
            'clinPrimary': $.toJSON(clinPrimaryIds),
            'clinSecondary': $.toJSON(clinSecondaryIds),
            'complicating': $.toJSON(complicatingIds),
            'note': $('#diagnosisNote').val(),
            'greeting_id': $('#greetingId').val()
        },
        'cache': false,
        'dataType': 'json',
        'type': 'GET',
        'complete': function (data, textStatus, jqXHR) {
            var ajaxData = {
                'success':false
            };
            try {
                ajaxData = $.parseJSON(data.responseText);
            } catch (e) {

            }
            onSectionSave(ajaxData);
        }
    });
});

globalVariables.onlyLikes = 0;
// Флаг любимых и общих диагнозов
$('#onlyLikeDiagnosis').click(function (e) {
    if (!$(this).prop('checked')) {
        globalVariables.onlyLikes = 0;
    } else {
        globalVariables.onlyLikes = 1;
    }
});

    $('#onlyLikeDiagnosis').click();


// Это для того, чтобы занести в диагнозы всё то, что было при загрузке страницы: первичные
(function (choosers) {
    for (var j = 0; j < choosers.length; j++) {
        var chooser = $('#' + choosers[j]);
        if ($(chooser).length > 0) {
            var preChoosed = $(chooser).find('.choosed span.item');
            for (var i = 0; i < preChoosed.length; i++) {
                var id = $(preChoosed[i]).prop('id').substr(1);
                $.fn[choosers[j]].addChoosed($('<li>').prop('id', 'r' + id).text($(preChoosed[i]).find('span').html()), {
                    'id': id,
                    'description': $(preChoosed[i]).find('span').html()
                }, 1);
            }
        }
    }
})([
        'primaryDiagnosisChooser',
        'secondaryDiagnosisChooser',
        'complicationsDiagnosisChooser',
        'clinicalSecondaryDiagnosis'
    ]);


// Просмотр медкарты в попапе
$(document).on('click', '.editMedcard', function (e) {
    $.ajax({
        'url': '/reception/patient/getmedcarddata',
        'data': {
            'cardid': $(this).prop('href').substr($(this).prop('href').lastIndexOf('#') + 1)
        },
        'cache': false,
        'dataType': 'json',
        'type': 'GET',
        'success': function (data, textStatus, jqXHR) {
            if (data.success == true) {
                var data = data.data.formModel;
                var form = $('#patient-medcard-edit-form');
                for (var i in data) {
                    $(form).find('#' + i).val(data[i]);
                }

                $(form).find('#documentGivedate').trigger('change');
                $(form).find('#privilege').trigger('change');

                $('#editMedcardPopup').modal({});
            } else {
                $('#errorSearchPopup .modal-body .row p').remove();
                $('#errorSearchPopup .modal-body .row').append('<p>' + data.data + '</p>')
                $('#errorSearchPopup').modal({

            });
        }
        return;
    }
});
return false;
});

// Запрет редактирования карты
$('#patient-medcard-edit-form .modal-body').find('input, select, button').prop('disabled', true);
$('#patient-medcard-edit-form .date-control .input-group-addon').remove();

// Здесь будут храниться ID клонов элементов
var clones = {

};

function collapseAccordion(boyjan /*Див с аккордионом :)*/) {
    $(boyjan).find('.accordion-body').removeClass('in');
    $(boyjan).find('.accordion-body').addClass('collapse');
    $(boyjan).find('.accordion-body').css('height', '0px');
    $(boyjan).trigger('hidden.bs.collapse');
}

function unCollapseAccordion(boyjan /*Див с аккордионом :)*/) {
    $(boyjan).find('.accordion-body').removeClass('collapse');
    $(boyjan).find('.accordion-body').addClass('in');
    $(boyjan).find('.accordion-body').css('height', 'auto');
    $(boyjan).trigger('shown.bs.collapse');
}

// Клонирование элементов
/* Клоны считаются, как clone_xx_yy, где xx - ID аккордеона, yy - порядковый номер клона */
$(document).on('click', '.accordion-clone-btn', function (e) {
    var prKey = $(this).find('span.pr-key').text();
    var accParent = $(this).parents('.accordion')[0];
    var accClone = $(accParent).clone();

    // Теперь нужно отклонировать элемент. Для этого мы подадим запрос, результатом которого станет категория (кусок дерева)
    $.ajax({
        'url': '/doctors/patient/cloneelement',
        'data': {
            'pr_key': prKey
        },
        'cache': false,
        'dataType': 'json',
        'type': 'GET',
        'success': function (data, textStatus, jqXHR) {
            if (data.success == true) {
                var toggle = $(accParent).find('.accordion-toggle');
                var body = $(accParent).find('.accordion-body');

                if (!clones.hasOwnProperty($(accParent).prop('id'))) {
                    clones[$(accParent).prop('id')] = 1;
                } else {
                    clones[$(accParent).prop('id')]++;
                }

                var accId = $(accParent).prop('id');
                var accNumberId = accId.substr(accId.lastIndexOf('_') + 1);
                var idCloneCount = clones[$(accParent).prop('id')];

                var toggleDataParent = $(toggle).data()['parent'];
                var toggleDataHref = $(toggle).prop('href');
                $(accClone).find('.accordion-clone-btn:eq(0)')
                        .removeClass('accordion-clone-btn')
                        .addClass('accordion-unclone-btn')
                        .find('span.glyphicon-plus')
                        .removeClass('glyphicon-plus')
                        .addClass('glyphicon-minus');

                $(accClone).find('.accordion-heading button:not(:eq(0))').remove();

                $(accClone).prop('id', $(accParent).prop('id') + '_clone_' + accNumberId + '_' + idCloneCount);
                $(accClone).find('.accordion-body:eq(0)').prop('id', 'collapse_clone_' + accNumberId + '_' + idCloneCount);
                $(accClone).find('.accordion-heading:eq(0)').attr('data-parent', $(accParent).prop('id') + '_clone_' + accNumberId + '_' + idCloneCount);
                $(accClone).find('.accordion-heading:eq(0) a').attr('href', '#collapse_clone_' + accNumberId + '_' + idCloneCount);

                // Дальше пробегаемся по всем вложенным в дерево элементам. Ситуация повторяется: переименовываем
                var inserts = $(accClone).find('.accordion');
                for (var i = 0; i < inserts.length; i++) {
                    if (!clones.hasOwnProperty($(inserts[i]).prop('id'))) {
                        clones[$(inserts[i]).prop('id')] = 1;
                    } else {
                        clones[$(inserts[i]).prop('id')]++;
                    }

                    accId = $(inserts[i]).prop('id');
                    accNumberId = accId.substr(accId.lastIndexOf('_') + 1);
                    idCloneCount = clones[$(inserts[i]).prop('id')];

                    $(inserts[i]).prop('id', $(inserts[i]).prop('id') + '_clone_' + accNumberId + '_' + idCloneCount);
                    $(inserts[i]).find('.accordion-body:eq(0)').prop('id', 'collapse_clone_' + accNumberId + '_' + idCloneCount);
                    $(inserts[i]).find('.accordion-heading:eq(0)').attr('data-parent', $(accParent).prop('id') + '_clone_' + accNumberId + '_' + idCloneCount);
                    $(inserts[i]).find('.accordion-heading:eq(0) a').attr('href', '#collapse_clone_' + accNumberId + '_' + idCloneCount);
                }

                // Ставим кнопке пришедший первичный ключ
                $(accClone).find('span.pr-key').text(data.data.pk_key);

                var children = $('[id^=' + accId + ']');

                //$(accClone).insertAfter($(accParent));
                unCollapseAccordion(accClone);
                $(accClone).insertAfter($(children[children.length-1]));
                // Теперь переименуем все элементы, согласно изменённым путям
                var repath = data.data.repath;
                for (var i in repath) {
                    var undottedPathBefore = i.split('.').join('|');
                    var undottedPathAfter = repath[i].split('.').join('|');
                    // Здесь большое TODO
                    var control = $(accClone).find('[id*="_' + undottedPathBefore + '_"]');
                    if (control.length > 0) {
                        var controlId = $(control).prop('id');
                        var substrFirst = controlId.substr(controlId.lastIndexOf('_'));
                        var tempSubstr = controlId.substr(0, controlId.lastIndexOf('_'));
                        var substrSecond = tempSubstr.substr(0, tempSubstr.lastIndexOf('_') + 1);
                        $(control).prop('id', substrSecond + undottedPathAfter + substrFirst);

                        // Перепишем имя у элемента
                        var arrayMultiselectSign = '';
                        // Если у клонированного элемента стоит multiselect - надо дописать в имени []
                        if ($(control).prop('multiple')==true)
                        {
                            arrayMultiselectSign = '[]';
                        }
                        $(control).prop('name',
                        'FormTemplateDefault['
                         +
                        tempSubstr.substr(0, tempSubstr.lastIndexOf('_') - 1)
                         +
                        undottedPathAfter
                         +
                        substrFirst +
                        ']'+arrayMultiselectSign);
                    }
                }

                // Надо сбросить все значения в склонированной категории,
                //     чтобы новая категория была девственно чиста :)
                $(accClone).find('input,textarea,select').val('');

                // Сбрасываем значения в редактируемых таблицах
                $(accClone).find('.controltable td.controlTableContentCell').text('');

                // Теперь надо подцепить замыкания для контролов, у которых оно есть
                // Выбираем все даты
                dates = $(accClone).find('div.date');
                controltables = $(accClone).find('table.controltable');

                // Для каждого элемента из dates вызываем функцию
                for (i=0;i<dates.length;i++)
                {
                    InitOneDateControl($(dates[i]));
                }

                // Для каждого элемента из dates вызываем функцию
                for (i=0;i<controltables.length;i++)
                {
                    $.fn['tableControl'].init($(controltables[i]));
                }
                // Для селект-контролов вызываем функцию инициализации клика
                selectControls = $(accClone).find('select');
                for(i=0;i<selectControls.length;i++)
                {
                    $.fn['categories'].initSelectOnClick(selectControls[i]);
                }
                // Надо скрыть все категории, кроме только что отклонированной
                // Берём id родительской категории и ищем все аккордеоны, в поле ИД которых входит ИД-шник родительской
                //   и посылаем сигнал "свернись!"
                // Переберём детей
                for (i = 0; i < children.length; i++) {
                    collapseAccordion(children[i]);
                }
                // Теперь надо разобрать зависимость
                var deps = data.data.dependences;

                for (var i = 0; i < deps.length; i++) {
                    // По этому пути вынимаем контрол
                    var undottedPath = deps[i].path.split('.').join('|');
                    if (deps[i].dependences.list.length > 0) {
                        filteredDeps.push(deps[i]);
                        (function (select, dep) {
                            $(select).on('change', function (e) {
                                var elementValue = $(select).val();
                                changeControlState(dep, elementValue, $(select).parents('.accordion:eq(0)'));
                            });
                            $(select).trigger('change');
                        })(getElementForDependences(undottedPath), deps[i]);
                    }
                }
            } else {
                return;
            }
        }
    });
});

    function getElementForDependences(undottedPath)  {
        return $('[id*="_' + undottedPath + '_"]');
    }

// UnКлонирование элементов
$(document).on('click', '.accordion-unclone-btn', function (e) {
    var accParent = $(this).parents('.accordion')[0];
    var prKey = $(this).find('span.pr-key').text();
    $.ajax({
        'url': '/doctors/patient/uncloneelement',
        'data': {
            'pr_key': prKey
        },
        'cache': false,
        'dataType': 'json',
        'type': 'GET',
        'success': function (data, textStatus, jqXHR) {
            if (data.success == true) {
                $(accParent).remove();
            }
        }
    });
});
var filteredDeps = [];
// Зависимости: дефолтные значения
function checkElementsDependences() {
    if (globalVariables.hasOwnProperty('elementsDependences')) {
        var deps = globalVariables.elementsDependences;
        for (var i = 0; i < deps.length; i++) {
            // По этому пути вынимаем контрол
            var undottedPath = deps[i].path.split('.').join('|');
            if (deps[i].dependences.list.length > 0) {
                filteredDeps.push(deps[i]);
                (function (select, dep) {
                    $(select).on('change', function (e) {
                        var elementValue = $(select).val();
                        changeControlState(dep, elementValue, $(select).parents('.accordion:eq(0)'));
                    });
                    $(select).trigger('change');
                })(getElementForDependences(undottedPath), deps[i]);
            }
        }
    }
}

checkElementsDependences();

function hideControl(container, elementId) {
    var elementWithWrapper = getDependenceElementWithWrapper(container,elementId);
    if (typeof container == 'undefined') {
        var next = $(elementWithWrapper).next();
        var prev = $(elementWithWrapper).prev();

        if (typeof next != 'undefined' && ($(next).hasClass('label-after') || $(next).hasClass('btn-sm'))) {
            $(next).hide();
            var next = $(next).next();
            if (typeof next != 'undefined' && $(next).hasClass('btn-sm')) {
                $(next).hide();
            }
        }
        if (typeof prev != 'undefined' && $(prev).hasClass('label-before')) {
            $(prev).hide();
        }
        $('[id$="_' + elementId + '"]').val('');
        $(elementWithWrapper).hide();
    }
    else {
        var next = $(   elementWithWrapper   ).next();
        var prev = $(   elementWithWrapper   ).prev();

        if (typeof next != 'undefined' && ($(next).hasClass('label-after') || $(next).hasClass('btn-sm'))) {
            $(next).hide();
            var next = $(next).next();
            if (typeof next != 'undefined' && $(next).hasClass('btn-sm')) {
                $(next).hide();
            }
        }
        if (typeof prev != 'undefined' && $(prev).hasClass('label-before')) {
            $(prev).hide();
        }
        $(container).find('[id$="_' + elementId + '"]').val('');
        $(elementWithWrapper).hide();

    }
}

function showControl(container, elementId) {
    var elementWithWrapper = getDependenceElementWithWrapper(container,elementId);
    if (typeof container == 'undefined') {
        var next = $(   elementWithWrapper   ).next();
        var prev = $(   elementWithWrapper   ).prev();

        if (typeof next != 'undefined' && ($(next).hasClass('label-after') || $(next).hasClass('btn-sm'))) {
            $(next).show();
            // + у комбо
            var next = $(next).next();
            if (typeof next != 'undefined' && $(next).hasClass('btn-sm')) {
                $(next).show();
            }
        }
        if (typeof prev != 'undefined' && $(prev).hasClass('label-before')) {
            $(prev).show();
        }
        $(elementWithWrapper).show();
    }
    else {
        var next = $(   elementWithWrapper   ).next();
        var prev = $(   elementWithWrapper ).prev();

        if (typeof next != 'undefined' && ($(next).hasClass('label-after') || $(next).hasClass('btn-sm'))) {
            $(next).show();
            var next = $(next).next();
            if (typeof next != 'undefined' && $(next).hasClass('btn-sm')) {
                $(next).show();
            }
            $(next).show();
        }
        if (typeof prev != 'undefined' && $(prev).hasClass('label-before')) {
            $(prev).show();
        }
      //  $(container).find('[id$="_' + elementId + '"]').val('');
        $(elementWithWrapper).show();
    }
}

function getDependenceElementWithWrapper(container, selectorString) {
    var result = undefined;
    // Смотрим - какой элемент. Если элемент скрытый (type=hidden),
    //    то нужно определить верхний контейнер-обёртку для этого элемента
    var targetElement = undefined;
    if (container==undefined) {
        targetElement = $('[id$="_' + selectorString + '"]:not(.btn-sm)');
    } else {
        targetElement = $(container).find('[id$="_' + selectorString + '"]:not(.btn-sm)');
    } if (targetElement!=undefined){
        if ($(targetElement).prop('type')=='hidden') {
            // Выбираем родителя div c классом twoColumnList
            result = $(targetElement).parents('div.twoColumnList');
            // Если результат - пустой - ищем контейнер с датой
            if ($(result).length==0) {
                result = $(targetElement).parents('div.date-control');
            }
        } else {
            result = $(targetElement);
        }
    }
    return result;
}

// Вызывается при совпадении свойства главного контрола
function onEqualValue(container, dependence) {
    if (dependence.action == 1) { // Это "скрыть"
        hideControl(container, dependence.elementId);
    } else if (dependence.action == 2) { // Это "показать"
        showControl(container, dependence.elementId);
    }
}

// Вызывается при НЕсовпадении свойства главного контрола
function onNotEqualValue(container, dependence) {
    if (dependence.action == 1) { // Это "скрыть"
        showControl(container, dependence.elementId);
    } else if (dependence.action == 2) { // Это "показать"
        hideControl(container, dependence.elementId);
    }
}


function changeControlState(dep, elementValue, container) {
    // Проверяем - если тип контрола двухколоночный список - то надо раскодировать JSON-значение контрола в массив
    if ( $('[id$="_' + dep.elementId + '"]').parents('div.twoColumnList').length>0    ) {
        try {
            var parsedObject  = $.parseJSON(elementValue);
            elementValue = parsedObject;
        } catch (e) { }
    }

    // Значение элемента является массивом в том случае, если у нас
    //    список с множественным выбором и двухколоночный список
    if ($.isArray(elementValue)) {
        for (var j = 0; j < dep.dependences.list.length; j++) {
            // Ищем значение зависимости в выбранных значениях элемента
            wasFound = false;
            // Сканируем значения списка
            for (k = 0; k < elementValue.length; k++) {
                if (dep.dependences.list[j].value == elementValue[k]) {
                    wasFound = true;
                    break;
                }
            } if (wasFound) {
                // Если нашли совпадение - выполняем действие, которое указано в зависимости
                onEqualValue(container, dep.dependences.list[j]);
            } else  {
                // Иначе - противоположное по значению
                onNotEqualValue(container, dep.dependences.list[j]);
            }
        }
    } else {
        // Массив с [Номер элемента] = 1
        // Сделан для того, чтобы если зависимость сработала, чтобы потом не
        //      скрывать этот элемент при переборе остальных значений главного контрола
        var switchedOnElements = [];

        for (var j = 0; j < dep.dependences.list.length; j++) {
            if (dep.dependences.list[j].value == elementValue) {
                onEqualValue(container, dep.dependences.list[j]);
                switchedOnElements[dep.dependences.list[j].elementId] = 1;
            }
            else {
                // Противоположное действие экшену по дефолту
                if (switchedOnElements[dep.dependences.list[j].elementId]==undefined)
                {
                    onNotEqualValue(container, dep.dependences.list[j]);
                }
            }
        }
    }

}

$('#templates-choose-form input[type="submit"]').on('click', function (e) {
    var checkboxes = $(this).parents('form').find('input[type="checkbox"]:checked');
    if(checkboxes.length == 0) {
        alert('Вы не выбрали ни одного шаблона для приёма!');
        return false;
    }
    $(this).attr('disabled', true);
	$(this).parents('form').find('.overlayCont').css({
		'position' : 'static'
	}).prepend($('<div>').addClass('overlay'));
    for (var i = 0; i < checkboxes.length; i++) {
        if ($(checkboxes[i]).prop('checked')) {
            $(this).attr('value', 'Подождите, приём начинается...');
            $('#templates-choose-form').submit();
			/*$(checkboxes).each(function(index, element) {
				$(element).prop('disabled', true);
			});*/
            return;
        }
    }

    $(this).attr('disabled', false);
    return false;
});

$('#addClinicalDiagnosisSubmit').on('click', function (e) {
    var diagnosisName = $('#diagnosisName').val();
    if ($.trim(diagnosisName) == '') {
        alert('Введите название диагноза!');
        return false;
    }

    $.ajax({
        'url': '/admin/diagnosis/addclinic',
        'data': {
            'FormClinicalDiagnosisAdd[description]': diagnosisName
        },
        'cache': false,
        'dataType': 'json',
        'type': 'POST',
        'success': function (data, textStatus, jqXHR) {
            $.fn[$('#chooserId').val()].addChoosed($('<li>').prop('id', 'r' + data.data.id).text(data.data.description), data.data);
            $('#diagnosisName').val('');
            $('#addClinicalDiagnosisPopup').modal('hide');
        }
    });
});

function generateAjaxGif(width, height) {
    return $('<img>').prop({
        'src': '/images/ajax-loader.gif',
        'width': width,
        'height': height,
        'alt': 'Загрузка...'
    });
}

function getHistoryPoint(activeLink) {
    // Доим активную ссылку - получаем с неё данные для запроса истории
     var href = ($(activeLink).find('a').attr('href')).substr(1);
     hostoryCoordinates = href.split('_');

     var medcard = hostoryCoordinates[0];
     var greeting = hostoryCoordinates[1];
     var template = hostoryCoordinates[2];
     $('#historyPopup .modal-title .medcardNumber').html('№ ' + medcard);
     $('#historyPopup .modal-title .historyDate').html($(activeLink).find('a').text());
    $.ajax({
        'url': '/doctors/patient/gethistorymedcard',
        'data': {
            medcardId: medcard,
            greetingId: greeting,
            templateId: template
        },
        'cache': false,
        'dataType': 'json',
        'type': 'GET',
        'error': function (data, textStatus, jqXHR) {
            console.log(data);
        },
        'success': function (data, textStatus, jqXHR) {
            if (data.success == 'true') {
                // Заполняем медкарту-историю значениями
                var data = data.data;
                $('#historyPopup .modal-body .row').html(data);

                // Триггерим событие открытия поп-апа с историей
                $('#historyPopup').trigger('shown.bs.modal');

                $('#historyPopup .modal-body .row').css('text-align', 'left');
                $('#nextHistoryPoint, #prevHistoryPoint').attr('disabled', false);
            }
        }
    });
}

$('#prevHistoryPoint').on('click', function () {
    $(this).attr('disabled', true);
    $('#nextHistoryPoint').attr('disabled', true);
    var gif = generateAjaxGif(48, 48);
    $('#historyPopup .modal-body .row').html(gif).css('text-align', 'center');

    var activeDiv = $('#accordionH .accordion-inner .active').removeClass('active');
    if ($(activeDiv).prev().length > 0) {
        activeDiv = $(activeDiv).prev().addClass('active');
    } else {
        activeDiv = $('#accordionH .accordion-inner div:last').addClass('active');
    }
    getHistoryPoint(activeDiv);
});

$('#nextHistoryPoint').on('click', function () {
    $(this).attr('disabled', true);
    $('#prevHistoryPoint').attr('disabled', true);
    var gif = generateAjaxGif(48, 48);
    $('#historyPopup .modal-body .row').html(gif).css('text-align', 'center');

    var activeDiv = $('#accordionH .accordion-inner .active').removeClass('active');
    if ($(activeDiv).next().length > 0) {
        activeDiv = $(activeDiv).next().addClass('active');
    } else {
        activeDiv = $('#accordionH .accordion-inner div:first').addClass('active');
    }
    getHistoryPoint(activeDiv);
});

    var isExpandedList = false;
    $('#expandPatientList').on('click', function(e) {
        // Убираем no-display у пустых строк интерфейса
        if ($(this).find('span').hasClass('glyphicon-resize-full')) {
            $('#writedByTimeCont #doctorPatientList .emptySheduleItem').removeClass('no-display');
            $(this).find('span').removeClass('glyphicon-resize-full');
            $(this).find('span').addClass('glyphicon-resize-small');
        } else {
            if ($(this).find('span').hasClass('glyphicon-resize-small')) {
                $('#writedByTimeCont #doctorPatientList .emptySheduleItem').addClass('no-display');
                $(this).find('span').removeClass('glyphicon-resize-small');
                $(this).find('span').addClass('glyphicon-resize-full');
            }
        }
    });

	// Смена врача
	$('#change-doctor-form select').on('change', function(e) {
        $('#noticeLeavePopup').modal({});
        var choosedDoctor = $(this).val();
        $(this).val(globalVariables.doctorId);
        callback = function() {
            globalVariables.doctorId = choosedDoctor;
            $('#change-doctor-form select')
                .val(choosedDoctor)
                .prop('disabled', true);
            // Вставляем оверлей
            $('.overlayCont').prepend($('<div>').prop('class', 'overlay').css({'marginLeft' : '10px'}));
            $('.changeDate-cont').prepend($('<div>').prop('class', 'overlay'));
            $('#refreshPatientList').trigger('click');
        }
        return false;
	});
	
	// Показ комментария (скрытого)
	$(document).on('mouseover', '#doctorPatientList tr:not(:first), #doctorWaitingList tr:not(:first)', function(e) {
		$('#doctorPatientList tr, #doctorWaitingList tr').popover('destroy');
		$(this).popover({
            animation: true,
            html: true,
            placement: 'left',
            title: '<strong>Комментарий:</strong>',
            delay: {
                show: 250,
                hide: 250
            },
            container: $(this).find('td:first'),
            content: function() {
				return $('<div>').append($(this).find('.hiddenComment').html());
			}
		});
	   $(this).popover('show');
	   return false;
	});
	
	$(document).on('mouseout', '#doctorPatientList tr:not(:first), #doctorWaitingList tr:not(:first)', function(e) {
		$('#doctorPatientList tr, #doctorWaitingList tr').popover('destroy');
	});
	
    function updatePatientList(onlyWaitingList) {
        var url = '/doctors/shedule/updatepatientlist';
        var data = {
            FormSheduleFilter : {
                date : globalVariables.year + '-' + globalVariables.month + '-' + globalVariables.day
            },
            currentPatient : $('#currentPatientId').val(),
            currentGreeting : $('#greetingId').val(),
			currentDoctor : $('#change-doctor-form').length > 0 ? $('#change-doctor-form select').val() : -1
        };
        if(typeof onlyWaitingList != 'undefined') {
            data.onlywaitinglist = 1;
        }

        $.ajax({
            'url': url,
            'data': data,
            'cache': false,
            'dataType': 'json',
            'type': 'POST',
            'error': function (data, textStatus, jqXHR) {
                console.log(data);
            },
            'success': function (data, textStatus, jqXHR) {
                if (data.success) {
                    // Кнопку свёртывания/развёртывания перключим на развёртывание
                    if(typeof onlyWaitingList == 'undefined') {
                        $('#expandPatientList').find('span').removeClass('glyphicon-resize-small');
                        $('#expandPatientList').find('span').addClass('glyphicon-resize-full');

                        // Убиваем старый список пациентов
                        var parentContainer =  $('#doctorPatientList').parents()[0];
                        $('#doctorPatientList').remove();
                        $(parentContainer).append(data.data);
                    } else {
                        var parentContainer = $('#doctorWaitingList').parents()[0];
                        $('#doctorWaitingList').remove();
                        $(parentContainer).append(data.data);
                    }
					if($('#change-doctor-form').length > 0) {
						$('#change-doctor-form select').prop('disabled', false); 
					}
					$('.overlayCont .overlay').remove();
					// А это - приём. Его для начала надо сохранить
					if((($('.template-edit-form').length > 0 && !$('.greetingContentCont').hasClass('no-display')) || $('#accordionT').length > 0) && globalVariables.doctorId && globalVariables.doctorId != $('#change-doctor-form select').val()) { // Внутри есть данные по пациенту, а врач сменён
                        if($('.infoCont div').length > 0) { // Внутри есть данные по пациенту, а врач сменён
                            $('.infoCont div').remove();
                        }
                        globalVariables.doctorId = $('#change-doctor-form select').val();
                        $('.greetingContentCont').addClass('no-display'); // Скрываем, потому что скриптам необходимо цеплять из блока данные
						$('#sideMedcardContentSave').trigger('click');
					}
                }
            }
        });
		
		// Два независимых процесса
		$.ajax({
            'url' : '/doctors/shedule/refreshdayswithpatients',
            'cache': false,
            'dataType': 'json',
            'error': function (data, textStatus, jqXHR) {
                console.log(data);
            },
            'success': function (data, textStatus, jqXHR) {
                if (data.success) {
					globalVariables.patientsInCalendar = $.evalJSON(data.data);
					$('.changeDate-cont').find('.overlay').remove();
					$("#date-cont").trigger('changeMonth');
				}
			}
		});
    }

    function updateExpanded() {
        var url = '/doctors/shedule/getpatientslistbydate';
        var data = {
            'doctorid' : globalVariables.doctorId,
            'year' : globalVariables.year,
            'month' : globalVariables.month,
            'day' :globalVariables.day
        };

        $.ajax({
            'url': url,
            'data': data,
            'cache': false,
            'dataType': 'json',
            'type': 'GET',
            'error': function (data, textStatus, jqXHR) {
                console.log(data);
            },
            'success': function (data, textStatus, jqXHR) {
                if (data.success) {

                }
            }
        });
    }

    $('#refreshPatientList').on('click', function(e) {
		updatePatientList();
    });

    $('.patientListNav li').on('click', function() {
        $('.patientListNav li').removeClass('active');
        $(this).addClass('active');
        $('#writedByOrderCont, #writedByTimeCont').addClass('no-display');
        var tabId = $(this).find('a').attr('id');
        $('#' + tabId + 'Cont').removeClass('no-display');
        if(tabId == 'writedByOrder') {
            $('#refreshWaitingList').trigger('click');
        } else {
            $('#refreshPatientList').trigger('click');
        }
    });

    // Обновить живую очредь, список
    $('#refreshWaitingList').on('click', function(e) {
        updatePatientList(1);
    });

});

// Это сделано для того, чтобы отследить изменение пользователем какого-либо элемента
//     Если пользователь первый раз на чём-то сфокусировался - то надо поставить обработчик на изменение любого контрола
//                 который в случае изменения - поднимает флаг
$('html').on('focus','form[id=patient-edit-form] input[type=text],input[type=number],textarea,select',
    function (e) {
        if (globalVariables.wasUserFocused ==false)  {
            // Ставим обработчик на изменние -
            $('html').on('change','form[id=patient-edit-form] input[type=text],input[type=number],textarea,select',
                function (e) {
                    globalVariables.isUnsavedUserData = true;
                }
            );
        }
        globalVariables.wasUserFocused = true;
    }
);

function getOnlyLikes() {
    return globalVariables.onlyLikes;
}
