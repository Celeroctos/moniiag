$(document).ready(function () {
    $("#elements").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/elements/get',
        datatype: "json",
        colNames: ['Код', 'Тип', 'Справочник', 'Категория', 'Метка до', 'Метка после', 'Метка для администратора', 'Размер', 'Перенос строки', 'Позиция', 'Полный путь', '', '', '', '', ''],
        colModel: [
            {
                name: 'id',
                index: 'id',
                width: 50
            },
            {
                name: 'type',
                index: 'type',
                width: 150
            },
            {
                name: 'guide',
                index: 'guide',
                width: 150
            },
            {
                name: 'categorie',
                index: 'categorie',
                width: 120
            },
            {
                name: 'label',
                index: 'label',
                width: 150
            },
            {
                name: 'label_after',
                index: 'label_after',
                width: 150
            },
            {
                name: 'label_display',
                index: 'label_display',
                width: 150
            },
            {
                name: 'size',
                index: 'size',
                width: 80
            },
            {
                name: 'is_wrapped_name',
                index: 'is_wrapped_name',
                width: 130
            },
            {
                name: 'position',
                index: 'position',
                width: 80
            },
            {
                name: 'path',
                index: 'path',
                width: 150
            },
            {
                name: 'categorie_id',
                index: 'categorie_id',
                hidden: true
            },
            {
                name: 'guide_id',
                index: 'guide_id',
                hidden: true
            },
            {
                name: 'type_id',
                index: 'type_id',
                hidden: true
            },
            {
                name: 'allow_add',
                index: 'allow_add',
                hidden: true
            },
            {
                name: 'is_wrapped',
                index: 'is_wrapped',
                hidden: true
            }
        ],
        rowNum: 10,
        rowList: [10, 20, 30],
        pager: '#elementsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Врачебные справочники",
        height: 300,
        ondblClickRow: editElement,
        onSelectRow: function (rowId, status, e) {
            var row = $("#elements").jqGrid('getRowData', rowId);
            if (row.type_id == 2 || row.type_id == 3) {
                $('#editElementDependences').removeClass('disabled');
            } else {
                $('#editElementDependences').addClass('disabled');
            }

            if (row.type_id == 4) {
                $('.table-config-container').removeClass('no-display');
                // Также покажем таблицу значений по умолчанию
                $('.defaultValuesTable').removeClass('no-display');

            } else {
                $('.table-config-container').addClass('no-display');

                // Скроем таблицу значений по умолчанию
                $('.defaultValuesTable').addClass('no-display');

            }
        }
    });

    $("#elements").jqGrid('navGrid', '#elementsPager', {
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

    var url = globalVariables.baseUrl + '/index.php/admin/elements/getdependenceslist';
    $("#dependences").jqGrid({
        url: url,
        datatype: "local",
        colNames: ['Код', 'Контрол ("метка до")', 'Значение', 'Зависимый контрол', 'Действие'],
        colModel: [
            {
                name: 'id',
                index: 'id',
                width: 50
            },
            {
                name: 'element',
                index: 'element',
                width: 150
            },
            {
                name: 'value',
                index: 'value',
                width: 150
            },
            {
                name: 'dep_element',
                index: 'dep_element',
                width: 150
            },
            {
                name: 'action',
                index: 'action',
                width: 120
            },
        ],
        rowNum: 10,
        rowList: [10, 20, 30],
        pager: '#dependencesPager',
        sortname: 'id',
        loadComplete: testDirection,
        viewrecords: true,
        sortorder: "desc",
        caption: "Список добавленных зависимостей",
        height: 300
    });

    $("#dependences").jqGrid('navGrid', '#dependencesPager', {
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


    $("#addElement").click(function () {

        printDefaultValuesTable(0, 0);

        $('#addElementPopup').modal({

    });
});

$("#element-add-form").on('success', function (eventObj, ajaxData, status, jqXHR) {
    var ajaxData = $.parseJSON(ajaxData);
    if (ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
        $('#addElementPopup').modal('hide');
        // Перезагружаем таблицу
        $("#elements").trigger("reloadGrid");
        $("#element-add-form").find("#guideId, #allowAdd, #defaultValue").prop('disabled', true);
        $("#element-add-form")[0].reset(); // Сбрасываем форму
        $('.table-config-container').addClass('no-display').find('#numCols, #numRows').attr('disabled', false);
        $('.table-config-headers tbody tr').remove();
    } else {
        // Удаляем предыдущие ошибки
        $('#errorAddElementPopup .modal-body .row p').remove();
        // Вставляем новые
        for (var i in ajaxData.errors) {
            for (var j = 0; j < ajaxData.errors[i].length; j++) {
                $('#errorAddElementPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
            }
        }

        $('#errorAddElementPopup').modal({

    });
}
});

function writeDefValuesFromConfig(defValues) {
    // Вычисляем макиммальный индекс по строке и столбцу в таблице значений по умолчанию
    var tableOfDefaults = $('div.defaultValuesTable table.controltable tbody');
    var maxRowIndex = 0;
    var maxColIndex = 0;

    // Делим на два, так как две таблицы
    maxRowIndex = tableOfDefaults.find('tr').length / 2;

    if (maxRowIndex > 0) {
        maxColIndex = $(tableOfDefaults.find('tr')[0]).find('td').length;
    }
    // Есди максимальный индекс - нулевой в строке или столбце, то выходим
    if (maxRowIndex <= 0 || maxColIndex <= 0) {
        return;
    }

    // Перебираем ячейки таблицы по индексам
    for (i = 0; i < maxRowIndex; i++) {
        for (j = 0; j < maxColIndex; j++) {
            // Смотрим - определён ли индекс в конфигурации. Если да - берём его значение и запихиваем в контрол
            oneValue = defValues[i.toString() + '_' + j.toString()];
            if (oneValue != undefined && oneValue != null) {

                // Подготовим селектор целевой ячейки
                var strToSelect = 'tr:eq(' + i.toString() +
                    ') td:eq(' + j.toString() + ') input';

                // Переберём таблички со значениями по умолчанию (всего две на странице)
                for (k = 0; k < tableOfDefaults.length; k++) {
                    // Возьмём ячейку
                    var cellToSelect =
                    $(tableOfDefaults[k]).find(strToSelect);
                    //  Запихнём туда значение
                    cellToSelect.val(oneValue);
                }
            }
        }

    }

    /*
    for (j = 0; j < tableOfDefaults.length; j++) {
    // Перебираем значения defValues
    for (i = 0; i < defValues.length; i++) {
    // Берём индексы
    var Indexes = defValues[i].indexes.split('_');

    // Проверяем - не больше ли индекс строки и столбца, чем индекс таблицы
    if ((Indexes[0] < maxRowIndex) && (Indexes[1] < maxColIndex)) {
    var strToSelect = 'tr:eq(' + Indexes[0] +
    ') td:eq(' + Indexes[1] + ') input';

    var cellToSelect =
    $(tableOfDefaults[j]).find(strToSelect);

    cellToSelect.val(defValues[i].def_val);
    }

    }
    }
    */

}

// Функция пробегает по строкам грида с зависимостями и проверяет,
//    какое направление было задано для зависимостей - скрывать, или показывать.
//   Если скрывать - то нельзя указывать "Показывать" при выборе действия - и наоборот
function testDirection() {
    $("#controlActions option[value='1']").removeClass('no-display');
    $("#controlActions option[value='2']").removeClass('no-display');
    var idsList = jQuery("#dependences").getDataIDs();
    for (i = 0; i < idsList.length; i++) {
        var rowData = jQuery("#dependences").getRowData(idsList[i]);
        //console.log(rowData.action);    
        if (rowData.action == "Показать") {
            // Прячем опцию "Скрыть"
            $("#controlActions option[value='1']").addClass('no-display');
        }
        else {
            // Прячем опцию "Показать"
            $("#controlActions option[value='2']").addClass('no-display');
        }
        break;
    }
}

$("#element-edit-form").on('success', function (eventObj, ajaxData, status, jqXHR) {
    var ajaxData = $.parseJSON(ajaxData);
    if (ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
        $('#editElementPopup').modal('hide');
        // Перезагружаем таблицу
        $("#elements").trigger("reloadGrid");
        $("#element-edit-form")[0].reset();

    } else {
        // Удаляем предыдущие ошибки
        $('#errorAddElementPopup .modal-body .row p').remove();
        // Вставляем новые
        for (var i in ajaxData.errors) {
            for (var j = 0; j < ajaxData.errors[i].length; j++) {
                $('#errorAddElementPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
            }
        }

        $('#errorAddElementPopup').modal({

    });
}
});


function editElement() {
    var currentRow = $('#elements').jqGrid('getGridParam', 'selrow');
    if (currentRow != null) {
        // Надо вынуть данные для редактирования
        $.ajax({
            'url': '/index.php/admin/elements/getone?id=' + currentRow,
            'cache': false,
            'dataType': 'json',
            'type': 'GET',
            'success': function (data, textStatus, jqXHR) {
                if (data.success == true) {
                    // Заполняем форму значениями
                    var form = $('#editElementPopup form')
                    // Соответствия формы и модели
                    var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'type',
                                formField: 'type'
                            },
                            {
                                modelField: 'categorie_id',
                                formField: 'categorieId'
                            },
                            {
                                modelField: 'label',
                                formField: 'label'
                            },
                            {
                                modelField: 'guide_id',
                                formField: 'guideId'
                            },
                            {
                                modelField: 'allow_add',
                                formField: 'allowAdd'
                            },
                            {
                                modelField: 'is_required',
                                formField: 'isRequired'
                            },
                            {
                                modelField: 'label_after',
                                formField: 'labelAfter'
                            },
                            {
                                modelField: 'size',
                                formField: 'size'
                            },
                            {
                                modelField: 'is_wrapped',
                                formField: 'isWrapped'
                            },
                            {
                                modelField: 'position',
                                formField: 'position'
                            },
                            {
                                modelField: 'config',
                                formField: 'config'
                            },
                            {
                                modelField: 'default_value',
                                formField: 'defaultValue'
                            },
                             {
                                 modelField: 'default_value',
                                 formField: 'defaultValueText'
                             },
                            {
                                modelField: 'label_display',
                                formField: 'labelDisplay'
                            }
                        ];
                    for (var i = 0; i < fields.length; i++) {
                        // Подгрузка значений справочника для дефолтного значения
                        if (fields[i].formField == 'defaultValue' && (data.data['type'] == 2 || data.data['type'] == 3)) {
                            // Это значение ставится асинхронно
                            $('select#guideId').trigger('change', [data.data[fields[i].modelField]]);
                            continue;
                        }
                        form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        // Таблица
                        if (fields[i].formField == 'config' && data.data['type'] == 4) {
                            var config = $.parseJSON(data.data['config']);
                            if (config.cols.length > 0) {
                                $('#editElementPopup .colsHeaders').prop('checked', true);
                            }
                            if (config.rows.length > 0) {
                                $('#editElementPopup .rowsHeaders').prop('checked', true);
                            }
                            var max = config.cols.length > config.rows.length ? config.cols.length : config.rows.length;
                            var tbody = $('#editElementPopup .table-config-headers tbody');
                            $('#editElementPopup #numRows').val(config.numRows);
                            $('#editElementPopup #numCols').val(config.numCols);
                            if (config.rows.length > 0) {
                                $('#editElementPopup #numRows').prop('disabled', true);
                            }
                            if (config.cols.length > 0) {
                                $('#editElementPopup #numCols').prop('disabled', true);
                            }
                            $(tbody).find('tr').remove();
                            console.log(config);
                            for (var j = 0; j < max; j++) {
                                if (j < config.rows.length) {
                                    var newInput1 = $('<input>').prop({
                                        'id': 'r' + j,
                                        'type': 'text',
                                        'class': 'form-control',
                                        'value': config.rows[j]
                                    });
                                } else {
                                    var newInput1 = null;
                                }
                                if (j < config.cols.length) {
                                    var newInput2 = $('<input>').prop({
                                        'id': 'c' + j,
                                        'type': 'text',
                                        'class': 'form-control',
                                        'value': config.cols[j]
                                    });
                                } else {
                                    var newInput2 = null;
                                }

                                var newTr = $('<tr>');
                                var newTdOne = $('<td>');
                                $(newTdOne).append(newInput1);
                                var newTdTwo = $('<td>');
                                $(newTdTwo).append(newInput2);

                                $(newTr).append(newTdOne, newTdTwo);
                                console.log(newTr);
                                $(tbody).append(newTr);
                            }
                            printDefaultValuesTable(config.numCols, config.numRows);
                            if (config.values != undefined && config.values != null) {
                                writeDefValuesFromConfig(config.values);
                            }
                        }
                    }

                    $.proxy(form.find("select#type").trigger('change'), form.find("select#type")); // $.proxy - вызов контекста

                    $("#editElementPopup").modal({

                });
            }
        }
    })
}
}

// Создаёт таблицу для значений по умолчанию для редактируемых таблиц
function printDefaultValuesTable(numberOfCols, numberOfRows) {
    // Берём таблицу из соответствующего контейнера
    var tableOfDefaults = $('div.defaultValuesTable table.controltable tbody');
    // Очищаем таблицу
    $(tableOfDefaults).find('tr').remove('');
    // Во внешнем цикле пробегаемся сначала по строкам
    for (i = 0; i < numberOfRows; i++) {
        var newTr = $('<tr>');
        // Во внутреннем цикле пробегаемся по столбцам
        for (j = 0; j < numberOfCols; j++) {
            var newTd = $('<td>');
            //newTd.html(i.toString()+'_'+j.toString());

            newTd.html(
						'<input type="text" id="' + i.toString() + '_' + j.toString() + '" value=""/>'
				);
            newTr.append(newTd);
        }

        // Добавляем строку в таблицу
        $(tableOfDefaults).append(newTr);
    }

    // Повесим на все инпуты в табоице значений обработчик события изменения значений

    $(tableOfDefaults.find('input')).change(onDefaultValuesTableChanged);
}

// Обработчик изменения значений в таблице значений по умолчанию
function onDefaultValuesTableChanged(e) {
    readConfigFromInterface(this);

}

$("#editElement").click(editElement);

$("#deleteElement").click(function () {
    var currentRow = $('#elements').jqGrid('getGridParam', 'selrow');
    if (currentRow != null) {
        // Надо вынуть данные для редактирования
        $.ajax({
            'url': '/index.php/admin/elements/delete?id=' + currentRow,
            'cache': false,
            'dataType': 'json',
            'type': 'GET',
            'success': function (data, textStatus, jqXHR) {
                if (data.success == 'true') {
                    $("#elements").trigger("reloadGrid");
                } else {
                    // Удаляем предыдущие ошибки
                    $('#errorAddElementPopup .modal-body .row p').remove();
                    $('#errorAddElementPopup .modal-body .row').append("<p>" + data.error + "</p>")

                    $('#errorAddElementPopup').modal({

                });
            }
        }
    })
}
});

// Открытие списка справочников
$("select#type").on('change', function (e) {
    // Если это список с выбором
    var form = $(this).parents('form');
    if ($(this).val() == 2 || $(this).val() == 3) {
        form.find("select#guideId").prop('disabled', false);
        form.find("select#allowAdd").prop('disabled', false);
        form.find("select#defaultValue").prop('disabled', false);

        form.find("#defaultValueText").parents('div.form-group').addClass('no-display');
        form.find("#defaultValue").parents('div.form-group').removeClass('no-display');

        //form.find("select#defaultValueText")
        //    .val('')

    } else {

        if ($(this).val() == 0 || $(this).val() == 1) {

            form.find("#defaultValueText").parents('div.form-group').removeClass('no-display');
            form.find("#defaultValue").parents('div.form-group').addClass('no-display');

        }
        else {
            form.find("#defaultValueText").parents('div.form-group').addClass('no-display');
            form.find("#defaultValue").parents('div.form-group').removeClass('no-display');
        }

        // Поставить на дефолт
        form.find("select#guideId")
                .val(-1)
                .prop('disabled', true);

        form.find("select#allowAdd")
                .val(0)
                .prop('disabled', true);

        form.find("select#defaultValue option:not([value=-1])").remove();
        form.find("select#defaultValue")
                .val(-1)
                .prop('disabled', true);

        form.find("select#defaultValueText")
                .val('')
    }

    if ($(this).val() == 4) {
        $('.table-config-container').removeClass('no-display');
        $('#numRows').parents('.form-group').removeClass('no-display');
        $('#numCols').parents('.form-group').removeClass('no-display');
        $(this).parents().find('.defaultValuesTable').removeClass('no-display');
    } else {
        $('.table-config-container').addClass('no-display');
        $('#numRows').parents('.form-group').addClass('no-display');
        $('#numCols').parents('.form-group').addClass('no-display');
        $(this).parents().find('.defaultValuesTable').addClass('no-display');
    }
});

var currentRow = null;
// Редактирование зависимостей элементов от значений
$('#editElementDependences').on('click', function () {
    if ($(this).hasClass('disabled')) {
        return false;
    }
    currentRow = $('#elements').jqGrid('getGridParam', 'selrow');

    $("#dependences").jqGrid('setGridParam', {
        url: url + '?id=' + currentRow,
        datatype: 'json'
    });
    $("#dependences").trigger('reloadGrid');

    if (currentRow != null) {
        // Надо вынуть данные для редактирования зависимостей
        $.ajax({
            'url': '/index.php/admin/elements/getdependences?id=' + currentRow,
            'cache': false,
            'dataType': 'json',
            'type': 'GET',
            'success': function (data, textStatus, jqXHR) {
                if (data.success == true) {
                    var data = data.data;
                    $('#controlValues option').remove();
                    for (var i = 0; i < data.comboValues.length; i++) {
                        var option = $('<option>').prop({
                            'value': data.comboValues[i].id
                        }).text(data.comboValues[i].value);
                        $('#controlValues').append(option);
                    }

                    // Ставим список всех контролов. Он обновляется всякий раз.
                    $('#controlDependencesList option').remove();
                    for (var i = 0; i < data.controls.length; i++) {
                        var option = $('<option>').prop({
                            'value': data.controls[i].id
                        }).text(data.controls[i].label);
                        $('#controlDependencesList').append(option);
                    }
                    $('#controlValues').trigger('change');

                    // Ставим список действий
                    if ($('#controlActions option').length == 0) {
                        $('#controlActions option').remove();
                        for (var i = 0; i < data.actions.length; i++) {
                            var option = $('<option>').prop({
                                'value': i
                            }).text(data.actions[i]);
                            if (i == 0) {
                                $(option).prop('selected', true);
                            }
                            $('#controlActions').append(option);
                        }
                    }
                    // По событию shown - вызов функции, которая спрячет запрещённые для данного элемента направления
                    $('#editDependencesPopup').on('shown.bs.modal', function (e) {
                        testDirection();
                    });
                    $('#editDependencesPopup').modal({});
                } else {

                }
            }
        })
    }
});

$('#controlValues').on('change', function (e) {
    if ($(this).val() != null && $(this).val().length > 0) {
        $('#controlDependencesPanel').removeClass('no-display');
    } else {
        $('#controlDependencesPanel').addClass('no-display');
    }
    $('#controlDependencesPanel').find('h5:eq(1), .row:eq(1)').addClass('no-display');
    $('#saveDependencesBtn').addClass('no-display');
    $('#controlDependencesList').val([]);
    $('#controlActions').val([]);
});

function is_int(mixed_var) {
    var result = false;
    var integer = parseInt(mixed_var);
    if (!isNaN(integer))
        result = true;

    return result;
}

$('#numCols, #numRows').on('change', function (e) {
    // Проверим - являются ли значения цифрами
    if (
		(is_int($(e.currentTarget).parents('.modal-body').find('#numCols').val()))
		&&
		(is_int($(e.currentTarget).parents('.modal-body').find('#numRows').val()))

		) {
        printDefaultValuesTable($(e.currentTarget).parents('.modal-body').find('#numCols').val(), $(e.currentTarget).parents('.modal-body').find('#numRows').val());
        configStr = $($(e.currentTarget).parents('.modal-body').find('#config')[0]).val();
        config = $.parseJSON(configStr);
        if (config.values != undefined && config.values != null) {
            writeDefValuesFromConfig(config.values);
        }
    }
    else {
        printDefaultValuesTable(0, 0);
    }



});



$('#controlDependencesList').on('change', function (e) {
    if ($(this).val().length > 0) {
        $('#controlDependencesPanel').find('h5:eq(1), .row:eq(1)').removeClass('no-display');
        $('#controlActions').val([]);
    } else {
        $('#controlDependencesPanel').find('h5:eq(1), .row:eq(1)').addClass('no-display');
    }
    $('#saveDependencesBtn').addClass('no-display');
});

$('#controlActions').on('change', function () {
    $('#saveDependencesBtn').removeClass('no-display');
})

$('#saveDependencesBtn').on('click', function (e) {
    var controlValues = $('#controlValues').val();
    var controlDependencesList = $('#controlDependencesList').val();
    var controlAction = $('#controlActions').val();

    $.ajax({
        'url': '/index.php/admin/elements/savedependences',
        'data': {
            'values': $.toJSON(controlValues),
            'dependenced': $.toJSON(controlDependencesList),
            'action': controlAction,
            'controlId': currentRow
        },
        'cache': false,
        'dataType': 'json',
        'type': 'GET',
        'success': function (data, textStatus, jqXHR) {
            if (data.success == true) {
                $("#dependences").trigger('reloadGrid');
                $('#controlValues').trigger('change');
            } else {

            }
        }
    });
});

$('.rowsHeaders').on('click', function (e) {
    var tbody = $(this).parents('.modal-body').find('.table-config-headers').find('tbody');
    if (!$(this).prop('checked')) {
        // В том случае, если в колонке заголовков столбцов нет текстовых полей, то нужно удалить строки таблицы
        var rowsHeaders = $(tbody).find('tr').find('td:eq(0)');
        for (var i = 0; i < rowsHeaders.length; i++) {
            $(rowsHeaders[i]).find('input').remove();

            if ($(rowsHeaders[i]).parent().find('td:eq(1)').find('input').length == 0) {
                $(rowsHeaders[i]).parents('tr').remove();
            }
        }
        $(this).parents('.modal-body').find('#numRows').attr('disabled', false);
        return;
    }
    var numRows = $(this).parents('.modal-body').find('#numRows').val();
    $(this).parents('.modal-body').find('#numRows').attr('disabled', true);
    var trs = $(tbody).find('tr');
    if (trs.length < numRows || typeof trs.length == 'undefined') {
        for (var i = 0; i < numRows; i++) {
            var newInput = $('<input>').prop({
                'id': 'r' + i,
                'type': 'text',
                'class': 'form-control'
            });

            if ($(tbody).find('tr:eq(' + i + ')').length == 0) {
                var newTr = $('<tr>');
                var newTdOne = $('<td>');
                $(newTdOne).append(newInput);
                var newTdTwo = $('<td>');

                $(newTr).append(newTdOne, newTdTwo);

                $(tbody).append(newTr);
            } else {
                var td = $(tbody).find('tr:eq(' + i + ')').find('td:eq(0)');
                $(td).append(newInput);
            }
        }
    } else if (trs >= numRows) {
        for (var i = 0; i < numRows; i++) {
            var newInput = $('<input>').prop({
                'id': 'r' + i,
                'type': 'text',
                'class': 'form-control'
            });
            console.log(trs[i]);
            $(trs[i]).find('td:eq(0)').append(newInput);
        }
    } else { // В противном случае ничего не менять

    }
});

$('.colsHeaders').on('click', function () {
    var tbody = $(this).parents('.modal-body').find('.table-config-headers').find('tbody');
    if (!$(this).prop('checked')) {
        // В том случае, если в колонке заголовков столбцов нет текстовых полей, то нужно удалить строки таблицы
        var colsHeaders = $(tbody).find('tr').find('td:eq(1)');
        for (var i = 0; i < colsHeaders.length; i++) {
            $(colsHeaders[i]).find('input').remove();

            if ($(colsHeaders[i]).parent().find('td:eq(0)').find('input').length == 0) {
                $(colsHeaders[i]).parents('tr').remove();
            }

        }
        $(this).parents('.modal-body').find('#numCols').attr('disabled', false);
        return;
    }

    var numCols = $(this).parents('.modal-body').find('#numCols').val();
    $(this).parents('.modal-body').find('#numCols').attr('disabled', true);
    var trs = $(tbody).find('tr');

    if (trs.length < numCols || typeof trs.length == 'undefined') {
        for (var i = 0; i < numCols; i++) {
            var newInput = $('<input>').prop({
                'id': 'c' + i,
                'type': 'text',
                'class': 'form-control'
            });
            if ($(tbody).find('tr:eq(' + i + ')').length == 0) {
                var newTr = $('<tr>');

                var newTdOne = $('<td>');
                var newTdTwo = $('<td>');
                $(newTdTwo).append(newInput);

                $(newTr).append(newTdOne, newTdTwo);

                $(tbody).append(newTr);
            } else {
                var td = $(tbody).find('tr:eq(' + i + ')').find('td:eq(1)');
                $(td).append(newInput);
            }
        }
    } else if (trs.length >= numCols) {
        for (var i = 0; i < numCols; i++) {
            var newInput = $('<input>').prop({
                'id': 'c' + i,
                'type': 'text',
                'class': 'form-control'
            });
            $(trs[i]).find('td:eq(1)').append(newInput);
        }
    } else { // В противном случае ничего не менять

    }
});

$('.table-config-headers').on('change', 'input', function (e) {
    readConfigFromInterface(this);
    /* var hiddenConfig = $(this).parents('.modal-body').find('#config');
    var configTable = $(this).parents('.table-config-headers');
    var rowsHeaders = $(configTable).find('tbody tr').find('td:eq(0) input');
    var colsHeaders = $(configTable).find('tbody tr').find('td:eq(1) input');
    var tempConfig = {
    cols: [],
    rows: [],
    numCols : $(this).parents('.modal-body').find('#numCols').val(),
    numRows : $(this).parents('.modal-body').find('#numRows').val()
    };

    for(var i = 0; i < rowsHeaders.length; i++) {
    if($.trim($(rowsHeaders[i]).val()) != '') {
    tempConfig.rows.push($(rowsHeaders[i]).val());
    }
    }
    for(var i = 0; i < colsHeaders.length; i++) {
    if($.trim($(colsHeaders[i]).val()) != '') {
    tempConfig.cols.push($(colsHeaders[i]).val());
    }
    }

    $(hiddenConfig).val($.toJSON(tempConfig));*/
});


// Прочитать конфигурацию из интерфейса в контрол
function readConfigFromInterface(sender) {

    var container = $(sender).parents('.modal-body');

    var hiddenConfig = $(container).find('#config');
    var configTable = $(container).find('.table-config-headers');
    var rowsHeaders = $(configTable).find('tbody tr').find('td:eq(0) input');
    var colsHeaders = $(configTable).find('tbody tr').find('td:eq(1) input');
    var tempConfig = {
        cols: [],
        rows: [],
        values: {},
        numCols: $(container).find('#numCols').val(),
        numRows: $(container).find('#numRows').val()
    };

    for (var i = 0; i < rowsHeaders.length; i++) {
        if ($.trim($(rowsHeaders[i]).val()) != '') {
            tempConfig.rows.push($(rowsHeaders[i]).val());
        }
    }
    for (var i = 0; i < colsHeaders.length; i++) {
        if ($.trim($(colsHeaders[i]).val()) != '') {
            tempConfig.cols.push($(colsHeaders[i]).val());
        }
    }


    // Берём таблицу из соответствующего контейнера
    var defaultInputs = $(container).find('div.defaultValuesTable table.controltable tbody input');
    var defaultValues = [];
    for (i = 0; i < defaultInputs.length; i++) {
        // Берём id инпута, разделяем его на части, означающие строку и столбец
        var rawId = $(defaultInputs[i]).attr('id');
        //console.log (strIndexes)
        if ($($(defaultInputs[i])[0]).val() != '') {
            //console.log (strIndexes);
            //	defaultValues[rawId] = $($(defaultInputs[i])[0]).val();
            /*var oneValue = 
            {
            indexes: rawId,
            def_val: $($(defaultInputs[i])[0]).val()
            };
			
            tempConfig.values.push(oneValue);
            */

            tempConfig.values[rawId] = $($(defaultInputs[i])[0]).val();


        }
    }

    $(hiddenConfig).val($.toJSON(tempConfig));
}

$('select#guideId').on('change', function (e, currentValue) {
    var form = $(this).parents('form');
    var defaultSelect = $(form).find('select#defaultValue');
    $(defaultSelect).find('option:not([value=-1])').remove();
    $(defaultSelect).val(-1);
    if ($(this).val() == -1) {
        return false;
    }
    $(defaultSelect).attr('disabled', true);

    $.ajax({
        'url': '/index.php/admin/guides/getvalues',
        'data': {
            'id': $(this).val()
        },
        'cache': false,
        'dataType': 'json',
        'type': 'GET',
        'success': function (data, textStatus, jqXHR) {
            if (data.success == true) {
                for (var i = 0; i < data.rows.length; i++) {
                    $(defaultSelect).append($('<option>').prop({
                        'value': data.rows[i].id
                    }).text(data.rows[i].value));
                }
                $(defaultSelect).attr('disabled', false);
                if (typeof currentValue != 'undefined') {
                    $(defaultSelect).val(currentValue);
                }
            }
        }
    });
})
});
