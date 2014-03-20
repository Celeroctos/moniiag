$(document).ready(function() {
    // По нажатию клавиши в контроле поиска
    $('#searchValue').on('keyup', function(e)
                         {
                            
                            // В $('#searchValue').val() лежит строка, которую ввёл пользователь
                            // Нужно перебрать все значения из списка
                            var enteredValue = $('#searchValue').val();
                            var itemsStore = ($('.medguide-list')[0]);
                            //console.log(items);
                            var items = $(itemsStore).find('li');
                            // Перебираем items
                            for (i=0;i<items.length;i++) {
                                var currentItem = items[i];
                                
                                // Внутри - берём ссылку и вытаскиваем текст из неё
                                var internalText = ($(currentItem).find ('a')[0]).innerText;
                                
                                if ((internalText.toUpperCase()).indexOf(enteredValue.toUpperCase())>=0) {
                                    // Делаем тэг li - выдимым
                                    $(currentItem).removeClass('no-display');
                                }
                                else
                                {
                                    $(currentItem).addClass('no-display');
                                }
                                
                            }
                            
                            
                         });
    
    
    $("#medguides").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/guides/getvalues?id=' + globalVariables.currentGuideId,
        datatype: "json",
        colNames:['','Значение'],
        colModel:[
            {
                name:'id',
                index:'id',
                hidden: true
            },
            {
                name: 'value',
                index:'value',
                width: 500
            },
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#medguidesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Значения",
        height: 300,
        ondblClickRow: editGuide
    });

    $("#medguides").jqGrid('navGrid','#medguidesPager',{
            edit: false,
            add: false,
            del: false
        },
        {},
        {},
        {},
        {
            closeOnEscape:true,
            multipleSearch :true,
            closeAfterSearch: true
        }
    );


    $("#addMedGuide").click(function() {
        $('#addMedGuidePopup').modal({

        });
    });

    $("#medguide-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addMedGuidePopup').modal('hide');
            // Перезагружаем таблицу
            $("#medguides").trigger("reloadGrid");
            $("#medguide-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddMedGuidePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddMedGuidePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddMedGuidePopup').modal({

            });
        }
    });

    $("#medguide-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editMedGuidePopup').modal('hide');
            // Перезагружаем таблицу
            $("#medguides").trigger("reloadGrid");
            $("#medguide-edit-form")[0].reset();

        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddMedGuidePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddMedGuidePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddMedGuidePopup').modal({

            });
        }
    });


    function editGuide() {
        var currentRow = $('#medguides').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/guides/getonevalue?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editMedGuidePopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'value',
                                formField: 'value'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editMedGuidePopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#editMedGuide").click(editGuide);

    $("#deleteMedGuide").click(function() {
        var currentRow = $('#medguides').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/guides/deleteinguide?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#medguides").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddMedGuidePopup .modal-body .row p').remove();
                        $('#errorAddMedGuidePopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddMedGuidePopup').modal({

                        });
                    }
                }
            })
        }
    });
});
