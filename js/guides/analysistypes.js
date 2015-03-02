$(document).ready(function() {
    $("#addAnalysisType").click(function() {
        $('#addAnalysisTypePopup').modal({

        });
    });

    $("#analysistype-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addAnalysisTypePopup').modal('hide');
            // Перезагружаем таблицу
//            $("#analysistypes").trigger("reloadGrid");
            $("#analysistype-add-form")[0].reset(); // Сбрасываем форму
            location.reload();
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddAnalysisTypePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddAnalysisTypePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddAnalysisTypePopup').modal({

            });
        }
    });

    $("#analysistype-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editAnalysisTypePopup').modal('hide');
            // Перезагружаем таблицу
//            $("#analysistypes").trigger("reloadGrid");
            $("#analysistype-edit-form")[0].reset(); // Сбрасываем форму
            location.reload();
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddAnalysisTypePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddAnalysisTypePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddAnalysisTypePopup').modal({

            });
        }
    });

    function editAnalysisType(event, currentRow) {
        if(Boolean(globalVariables.guideEdit) == false) {
            return false;
        }
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/laboratory/getoneanalysistype?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editAnalysisTypePopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'name',
                                formField: 'name'
                            },
                            {
                                modelField: 'short_name',
                                formField: 'short_name'
                            },
                            {
                                modelField: 'automatic',
                                formField: 'automatic'
                            },
                            {
                                modelField: 'manual',
                                formField: 'manual'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
//                            if (document.getElementById(fields[i].formField).type == "checkbox") {
//                                document.getElementById(fields[i].formField).checked = (data.data[fields[i].modelField]);
//                            } else {
//                                document.getElementById(fields[i].formField).val = (data.data[fields[i].modelField]);
//                            }
                        }
                        $("#editAnalysisTypePopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#editAnalysisType").click(editAnalysisType);
});
