$(document).ready(function () {

    // Инитим контролы в первой строке в таблице дней-исключений
    InitOneDateControl($('#shedule-exp-table tr:eq(1) input[id^=day]').parents('div.input-group'));
    InitOneTimeControl($('#shedule-exp-table tr:eq(1) input[id^=timeBegin]').parents('div.input-group'));
    InitOneTimeControl($('#shedule-exp-table tr:eq(1) input[id^=timeEnd]').parents('div.input-group'));

    InitPaginationList('searchDoctorsResult',
        'd.middle_name',
        'desc', updateDoctorList);

    $("#shiftsEmployee").jqGrid({
        datatype: "json",
        colNames: ['Начало смены', 'Конец смены', ''],
        colModel: [
            {
                name: 'date_begin',
                index: 'date_begin',
                width: 200
            },
            {
                name: 'date_end',
                index: 'date_end',
                width: 150
            },
            {
                name: 'id',
                index: 'id',
                width: 150,
                hidden: true
            },
        ],
        rowNum: 10,
        rowList: [10, 20, 30],
        pager: '#shiftsEmployeePager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Смены сотрудника",
        height: 100,
        ondblClickRow: editSheduleEmployee
    });


    $("#addSheduleEmployee").click(function () {
        $('#addShedulePopup').modal({

        });
    });

// Отобразить ошибки формы добавления пациента
    $("#add-shedule-employee").on('success', function (eventObj, ajaxData, status, jqXHR) {
        $('#addShedulePopup').modal('hide');
        var ajaxData = $.parseJSON(ajaxData);

        refreshEmployeeShifts();
        if (ajaxData.success == 'true') { // Запрос прошёл удачно

            $('#addShedulePopup').find('form').trigger('reset');
            $('#addShedulePopup #dateBegin').val('');
            $('#addShedulePopup #dateEnd').val('');
            $('#successAddEmployeeShedule').modal({

            });

            if (ajaxData.unwritedPatients <= 0)
            {
                $('#successAddEmployeeShedule #messageRewritePatients').addClass('no-display');
            }
            else
            {
                $('#successAddEmployeeShedule #numberPatientsToRewrite').text(ajaxData.unwritedPatients);
                $('#successAddEmployeeShedule #messageRewritePatients').removeClass('no-display');
            }

        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddShedulePopup .modal-body .row p').remove();
            // Вставляем новые
            //  for (var j = 0; j < ajaxData.errors.length; j++) {
            $('#errorAddShedulePopup .modal-body .row').append("<p>" + ajaxData.errors + "</p>")
            //  }

            $('#errorAddShedulePopup').modal({

            });
        }
    });

// Отобразить ошибки формы добавления пациента
    $("#edit-shedule-employee").on('success', function (eventObj, ajaxData, status, jqXHR) {
        $('#editSheduleEmployeePopup').modal('hide');
        var ajaxData = $.parseJSON(ajaxData);
        refreshEmployeeShifts();
        if (ajaxData.success == 'true') { // Запрос прошёл удачно

            $('#editSheduleEmployeePopup').find('form').trigger('reset');
            $('#editSheduleEmployeePopup #dateBegin').val('');
            $('#editSheduleEmployeePopup #dateEnd').val('');



            $('#successEditEmployeeShedule').modal({

            });

            if (ajaxData.unwritedPatients <= 0)
            {
                $('#successEditEmployeeShedule #messageRewritePatients').addClass('no-display');
            }
            else
            {
                $('#successEditEmployeeShedule #numberPatientsToRewrite').text(ajaxData.unwritedPatients);
                $('#successEditEmployeeShedule #messageRewritePatients').removeClass('no-display');
            }

        } else {
            // Удаляем предыдущие ошибки
            $('#errorEditShedulePopup .modal-body .row p').remove();
            // Вставляем новые
            //for (var i in ajaxData.errors) {
            // for (var j = 0; j < ajaxData.errors.length; j++) {
            $('#errorEditShedulePopup .modal-body .row').append("<p>" + ajaxData.errors + "</p>");
            //   }

            $('#errorEditShedulePopup').modal({

            });
        }
    });

    $('#editSheduleEmployee').on('click', function(e) {
        editSheduleEmployee();
    });

  /*  $("#doctor-shedule-add-submit").on('click',
        function () {

            var cancelation;
            cancelation = false;
            var begin = '';
            if ($("#addShedulePopup #dateBegin").val() != '' && $("#addShedulePopup #dateBegin").val() != undefined) {
                begin = $("#addShedulePopup #dateBegin").val();
            }

            var end = '';
            if ($("#addShedulePopup #dateEnd").val() != '' && $("#addShedulePopup #dateEnd").val() != undefined) {
                end = $("#addShedulePopup #dateEnd").val();
            }
            var doctorId = '';
            if ($("#addShedulePopup #doctorId").val() != '' && $("#addShedulePopup #doctorId").val() != undefined) {
                doctorId = $("#addShedulePopup #doctorId").val();
            }

            if (begin != '' && end != '') {
                $.ajax({
                    'url': '/index.php/admin/shedule/isgreeting?begin=' + begin + '&end=' + end + '&doctor_id=' + doctorId,
                    'cache': false,
                    'dataType': 'json',
                    'type': 'GET',
                    'async': false,
                    'success': function (data, textStatus, jqXHR) {

                        if (data.success == true) {
                            if (data.data > 0) {
                                console.log(data.data);
                                // Выводим список пациентов
                                // alert('Есть записанные паценты');
                                cancelation = true;
                            }

                        }
                    }
                });
            }

            if (cancelation) {
                // Открываем модалку
                openViewWritedPatient($('#doctorId').val(), begin, end);
            }
            return !cancelation;

        }
    );*/

    // Возвращает JSON c временами режима работы
    function getTimesObject(parentContainer) {
        var modeTimes =
        {
            timesBegin: Array(),
            timesEnd: Array()
        }
        for (i = 0; i < 7; i++) {
            modeTimes.timesBegin[i] = $(parentContainer).find('#timeBegin' + i.toString()).val();
            modeTimes.timesEnd[i] = $(parentContainer).find('#timeEnd' + i.toString()).val();
        }

        return modeTimes;
    }

    $("#deleteSheduleEmployee").click(function () {
        var currentRow = $('#shiftsEmployee').jqGrid('getGridParam', 'selrow');
        if (currentRow != null) {

                            // Удаляем расписание
                            // Надо вынуть данные для редактирования
                            $.ajax({
                                'url': '/index.php/admin/shedule/delete?id=' + currentRow,
                                'cache': false,
                                'dataType': 'json',
                                'type': 'GET',
                                'success': function (data, textStatus, jqXHR) {
                                    if (data.success == 'true') {
                                        $('#shiftsEmployee').trigger("reloadGrid");
                                        // Выводим сообщение об отписанных приёмах
                                        if (data.unwritedPatients <= 0)
                                        {
                                            $('#successDeleteEmployeeShedule #messageRewritePatients').addClass('no-display');
                                        }
                                        else
                                        {
                                            $('#successDeleteEmployeeShedule #numberPatientsToRewrite').text(data.unwritedPatients);
                                            $('#successDeleteEmployeeShedule #messageRewritePatients').removeClass('no-display');
                                        }
                                        $('#successDeleteEmployeeShedule').modal({});

                                    } else {
                                        // Удаляем предыдущие ошибки
                                        $('#errorPopup .modal-body .row p').remove();
                                        $('#errorPopup .modal-body .row').append("<p>" + data.error + "</p>")

                                        $('#errorPopup').modal({});
                                    }
                                }
                            });

        }
    });

    function openViewWritedPatientEdit(doctor, shedule, beginDate, endDate, times) {
        // Сначала инициализируем jqGrid
        jQuery("#writtenPatients").jqGrid(
            'setGridParam',
            {
                url: globalVariables.baseUrl +
                    '/index.php/admin/shedule/getwrittenpatientsedit?' +
                    'doctor_id=' + doctor +
                    '&date_begin=' + beginDate +
                    '&date_end=' + endDate +
                    '&shedule_id=' + shedule +
                    '&times=' + $.toJSON(getTimesObject($("#editSheduleEmployeePopup"))),
                page: 1
            }
        );
        jQuery("#writtenPatients").trigger('reloadGrid');
        $('#viewWritedPatient').modal({});
    }

    // Открыть модалку с
    function openViewWritedPatient(doctor, beginDate, endDate) {
        // Сначала инициализируем jqGrid
        jQuery("#writtenPatients").jqGrid(
            'setGridParam',
            {
                url: globalVariables.baseUrl +
                    '/index.php/admin/shedule/getwrittenpatients?' +
                    'doctor_id=' + doctor +
                    '&begin=' + beginDate +
                    '&end=' + endDate,
                page: 1
            }
        );
        jQuery("#writtenPatients").trigger('reloadGrid');
        $('#viewWritedPatient').modal({});

    }


    function editSheduleEmployee() {
        var currentRow = $('#shiftsEmployee').jqGrid('getGridParam', 'selrow');
        if (currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url': '/index.php/admin/shedule/getone?id=' + currentRow,
                'cache': false,
                'dataType': 'json',
                'type': 'GET',
                'success': function (data, textStatus, jqXHR) {
                    if (data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editSheduleEmployeePopup form');
                        $("#editSheduleEmployeePopup").modal({

                        });
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'date_begin',
                                formField: 'dateBegin'
                            },
                            {
                                modelField: 'date_end',
                                formField: 'dateEnd'
                            },
                            {
                                modelField: 'employee_id',
                                formField: 'doctorId'
                            },
                            {
                                modelField: 'id',
                                formField: 'sheduleEmployeeId'
                            },

                        ];
                        for (var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                            form.find('#' + fields[i].formField).trigger('change');
                        }

                        // Допишем времена для каждого из рабочих дней
                        for (var i = 0; i < 7; i++) {
                            if (data.data['timeBegin' + i.toString()] != undefined) {
                                form.find('#timeBegin' + i.toString()).val(data.data['timeBegin' + i.toString()]);
                                form.find('#timeBegin' + i.toString()).trigger('change');

                                form.find('#timeEnd' + i.toString()).val(data.data['timeEnd' + i.toString()]);
                                form.find('#timeEnd' + i.toString()).trigger('change');

                                form.find('#cabinet' + i.toString()).val(data.data['cabinet' + i.toString()]);
                                form.find('#cabinet' + i.toString()).trigger('change');

                            }
                            else {
                                // Если данных нет - всё сбрасываем
                                form.find('#timeBegin' + i.toString()).val('');
                                form.find('#timeBegin' + i.toString()).trigger('change');

                                form.find('#timeEnd' + i.toString()).val('');
                                form.find('#timeEnd' + i.toString()).trigger('change');

                                form.find('#cabinet' + i.toString()).val('');
                                form.find('#cabinet' + i.toString()).trigger('change');
                            }
                        }


                    }
                }
            })
        }
    }

    $("#shiftsAdd").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/modules/getshifts',
        datatype: "json",
        colNames: ['Код', 'Начало приёма', 'Конец приёма'],
        colModel: [
            {
                name: 'id',
                index: 'id',
                width: 150
            },
            {
                name: 'time_begin',
                index: 'time_begin',
                width: 200
            },
            {
                name: 'time_end',
                index: 'time_end',
                width: 150
            },
        ],
        rowNum: 10,
        rowList: [10, 20, 30],
        pager: '#shiftsPagerAdd',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Смены",
        height: 100,
        ondblClickRow: installShift
    });


    $("#shiftsEdit").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/modules/getshifts',
        datatype: "json",
        colNames: ['Код', 'Начало приёма', 'Конец приёма'],
        colModel: [
            {
                name: 'id',
                index: 'id',
                width: 150
            },
            {
                name: 'time_begin',
                index: 'time_begin',
                width: 200
            },
            {
                name: 'time_end',
                index: 'time_end',
                width: 150
            },
        ],
        rowNum: 10,
        rowList: [10, 20, 30],
        pager: '#shiftsPagerEdit',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Смены",
        height: 100,
        ondblClickRow: installShift
    });

    $("#shifts").jqGrid('navGrid', '#editSheduleEmployeePopup #shiftsPager', {
            edit: false,
            add: false,
            del: false
        },
        {},
        {},
        {},
        {
            closeOnEscape: true,
            multipleSearch: true,
            closeAfterSearch: true
        }
    );

    function getDoctorsFilter() {
        var Result = {
            'groupOp': 'AND',
            'rules': [
                {
                    'field': 'ward_code',
                    'op': 'eq',
                    'data': $('#ward').val()
                },
                {
                    'field': 'post_id',
                    'op': 'eq',
                    'data': $('#post').val()
                },
                {
                    'field': 'middle_name',
                    'op': 'cn',
                    'data': $('#middleName').val().toLowerCase()
                },
                {
                    'field': 'last_name',
                    'op': 'cn',
                    'data': $('#lastName').val().toLowerCase()
                },
                {
                    'field': 'first_name',
                    'op': 'cn',
                    'data': $('#firstName').val().toLowerCase()
                }
            ]
        };

        return Result;
    }

    function updateDoctorList() {
        var filters = getDoctorsFilter();
        var PaginationData = getPaginationParameters('searchDoctorsResult');
        if (PaginationData != '') {
            PaginationData = '&' + PaginationData;
        }
        // Делаем поиск
        $.ajax({
            'url': '/index.php/reception/doctors/searchcommon/?filters=' + $.toJSON(filters) + PaginationData,
            'cache': false,
            'dataType': 'json',
            'type': 'GET',
            'success': function (data, textStatus, jqXHR) {
                $('#sheduleEditCont').hide();
                if (data.success == true) {
                    // Изначально таблицы скрыты
                    $('#withoutCardCont').addClass('no-display');

                    if (data.data.length == 0) {
                        $('#notFoundPopup').modal({
                        });
                    } else {
                        displayAllDoctors(data.data);
                        printPagination('searchDoctorsResult', data.total);
                    }
                } else {
                    $('#errorSearchPopup .modal-body .row p').remove();
                    $('#errorSearchPopup .modal-body .row').append('<p>' + data.data + '</p>')
                    $('#errorSearchPopup').modal({

                    });
                }
                return;
            }
        });
    }

    $('#doctor-search-submit').click(function (e) {
        updateDoctorList()
        return false;
    });

    $('#errorAddShedulePopup').on('hide.bs.modal', function (e) {
        // По закрытию ошибки - открываем снова поп-ап
        $('#addShedulePopup').modal({

        });
    });

    $('#errorEditShedulePopup').on('hide.bs.modal', function (e) {
        // По закрытию ошибки - открываем снова поп-ап
        $('#editSheduleEmployeePopup').modal({

        });
    });



    function displayAllDoctors(data) {
        var table = $('#searchDoctorsResult tbody');
        table.find('tr').remove();
        for (var i = 0; i < data.length; i++) {
            table.append(
                '<tr>' +
                    '<td>' +
                    '<a title="Установить для данного сотрудника расписание" href="#employee' + data[i].id + '">' +
                    data[i].last_name + ' ' + data[i].first_name + ' ' + data[i].middle_name +
                    '</a>' +
                    '</td>' +
                    '<td>' +
                    data[i].post +
                    '</td>' +
                    '<td>' +
                    data[i].ward +
                    '</td>' +
                    '</tr>'
            );
        }
        table.parents('div.no-display').removeClass('no-display');
    }


// Отобразить форму редактирования расписания при клике на ссылку
    $(document).on('click', 'a[href^=#employee]', function (e) {
        var attr = $(this).attr('href');
        var id = parseInt(attr.substr(9));
        $('input[id^=doctorId]').val(id);
        globalVariables.doctorSheduleEdit = id;
        // Здесь - запрос к базе: мб, уже есть установленное раписание, тогда его надо вывести
        $.ajax({
            'url': '/index.php/admin/shedule/get?id=' + id,
            'cache': false,
            'dataType': 'json',
            'type': 'GET',
            'success': function (data, textStatus, jqXHR) {
                if (data.success == 'true') {
                    // Формирование раписания из уже имеющихся данных
                    var shedule = data.data.data;
                    // Здесь удаляем все строки кроме первой
                    $('#shedule-exp-form tbody').find('tr:not(:first)').remove();

                    var numExps = 0;
                    for (var i = 0; i < shedule.length; i++) {
                        // Если конкретного дня нет - значит, это расписание общее (0)
                        /*  if(shedule[i].type == 0) {
                         var form = $('#shedule-by-day-form');
                         $(form).find('#timeBegin' + shedule[i].weekday).val(shedule[i].timeBegin);
                         $(form).find('#timeBegin' + shedule[i].weekday).trigger('change');
                         $(form).find('#timeEnd' + shedule[i].weekday).val(shedule[i].timeEnd);
                         $(form).find('#timeEnd' + shedule[i].weekday).trigger('change');
                         $(form).find('#cabinet' + shedule[i].weekday).val(shedule[i].cabinetId);
                         } */
                        if (shedule[i].type == 1) {
                            var form = $('#shedule-exp-form');
                            $(form).find('#id' + numExps).val(shedule[i].id);
                            $(form).find('#doctorId' + numExps).val(shedule[i].employeeId);
                            $(form).find('#day' + numExps).val(shedule[i].day);
                            $(form).find('#day' + numExps).trigger('change');
                            $(form).find('#timeBegin' + numExps).val(shedule[i].timeBegin);
                            $(form).find('#timeBegin' + numExps).trigger('change');
                            $(form).find('#timeEnd' + numExps).val(shedule[i].timeEnd);
                            $(form).find('#timeEnd' + numExps).trigger('change');
                            $(form).find('#cabinet' + numExps).val(shedule[i].cabinetId);
                            $('#doctor-exp-add').trigger('click');
                            ++numExps;
                        }
                    }
                    $('#sheduleEditCont').slideDown(500);
                    refreshEmployeeShifts();


                    // Переиничиваем контролы дат и времени
                    //ReInitDateControls();
                    //ReInitTimeControls();
                }
                return;
            }
        });
    });

// Выводим список расписания для сотрудника
    function refreshEmployeeShifts() {
        jQuery("#shiftsEmployee").jqGrid(
            'setGridParam',
            {
                url: globalVariables.baseUrl + '/index.php/admin/shedule/getshiftsemployee?doctorId=' + globalVariables.doctorSheduleEdit,
                page: 1
            }
        );
        jQuery("#shiftsEmployee").trigger('reloadGrid');
    }

// Радиокнопка смены типа расписания
    $('input[name=sheduleType]').on('click', function (e) {
        if ($(this).val() == 0) {
            $('#addShedulePopup #sheduleShifts').slideDown(500);
            $('#editSheduleEmployeePopup #sheduleShifts').slideDown(500);
        } else {
            $('#addShedulePopup #sheduleShifts').hide();
            $('#editSheduleEmployeePopup #sheduleShifts').hide();
        }
    });

// Календарь на день-исключение
    function initExpCalendar() {
        // Календари времени на контролы расписания
        $('div[id^=timeBegin], div[id^=timeEnd]').datetimepicker({
            language: 'ru',
            format: 'h:i',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 1,
            minView: 0,
            maxView: 1,
            forceParse: 0
        });

        $('div[id^=day]').datetimepicker({
            language: 'ru',
            format: 'yyyy-mm-dd',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0
        });
    };
    initExpCalendar();

    function installShift(rowId, status, e) {
        var Holidays = $.parseJSON($('#weekEnds').val());
        var currentRow = $('#shiftsAdd').jqGrid('getRowData', rowId);
        console.log(currentRow);
        // Ищем все контролы с
        var parentPopup = $(this).parents('.modal');
        var tBeginControls = $(parentPopup).find('input[id^=timeBegin]');
        var tEndControls = $(parentPopup).find('input[id^=timeEnd]');
        // Перебираем контролы времени
        for (i = 0; i < tBeginControls.length; i++) {
            var weekDayNumber = $(tBeginControls[i]).attr('id');
            weekDayNumber = weekDayNumber.substr(weekDayNumber.length - 1, 1);
            // Проверим, не является ли этот день выходным
            var isHoliday = false;
            for (j = 0; j < Holidays.length; j++) {
                if (Holidays[j] == weekDayNumber) {
                    isHoliday = true;
                    break;
                }
            }
            // Выходным не является - надо вывести время в таблицу
            if (!isHoliday) {
                $(tBeginControls[i]).val(currentRow.time_begin);
                $(tBeginControls[i]).trigger('change');
            }
        }
        for (i = 0; i < tEndControls.length; i++) {
            var weekDayNumber = $(tEndControls[i]).attr('id');
            weekDayNumber = weekDayNumber.substr(weekDayNumber.length - 1, 1);
            var isHoliday = false;
            for (j = 0; j < Holidays.length; j++) {
                if (Holidays[j] == weekDayNumber) {
                    isHoliday = true;
                    break;
                }
            }
            // Выходным не является - надо вывести время в таблицу
            if (!isHoliday) {
                $(tEndControls[i]).val(currentRow.time_end);
                $(tEndControls[i]).trigger('change');
            }
        }



        //

    }

    $("#shedule-exp-form, #shedule-by-day-form").on('success', function (eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if (ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#successPopup').modal({

            });
        } else {
            // Удаляем предыдущие ошибки
            $('#errorPopup .modal-body .row p').remove();
            // Вставляем новые
            for (var i in ajaxData.errors) {
                for (var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorPopup').modal({

            });
        }
    });

    $('#doctor-exp-add').click(function (e) {
        var node = $('#shedule-exp-table tr:eq(1)').clone();
        var collLength = $('#shedule-exp-table tr').size();
        // Заменили имена ноды
        $(node).find('input[id^=id]').prop({
            'name': 'FormSheduleExpAdd[' + (collLength - 1) + '][id]',
            'id': 'id' + (collLength - 1)
        }).val('');
        $(node).find('input[id^=day]').prop({
            'name': 'FormSheduleExpAdd[' + (collLength - 1) + '][day]',
            'id': 'day' + (collLength - 1)
        }).val('');
        $(node).find('input[id^=timeBegin]').prop({
            'name': 'FormSheduleExpAdd[' + (collLength - 1) + '][timeBegin]',
            'id': 'timeBegin' + (collLength - 1)
        }).val('');
        $(node).find('input[id^=timeEnd]').prop({
            'name': 'FormSheduleExpAdd[' + (collLength - 1) + '][timeEnd]',
            'id': 'timeEnd' + (collLength - 1)
        }).val('');
        $(node).find('input.year').val('');
        $(node).find('input.month').val('');
        $(node).find('input.day').val('');
        $(node).find('input.hour').val('');
        $(node).find('input.minute').val('');
        $(node).find('input[id^=doctorId]').prop({
            'name': 'FormSheduleExpAdd[' + (collLength - 1) + '][doctorId]',
            'id': 'doctorId' + (collLength - 1)
        });
        $(node).find('select').prop({
            'name': 'FormSheduleExpAdd[' + (collLength - 1) + '][cabinet]',
            'id': 'cabinet' + (collLength - 1)
        });

        // Инитим контролы в строке таблицы
        InitOneDateControl(($(node).find('input[id^=day]')).parents('div.input-group'));
        InitOneTimeControl(($(node).find('input[id^=timeBegin]')).parents('div.input-group'));
        InitOneTimeControl(($(node).find('input[id^=timeEnd]')).parents('div.input-group'));

        $(node).insertAfter('#shedule-exp-table tr:last');
        initExpCalendar();
    });
});