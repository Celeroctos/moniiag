$(document).ready(function() {
    $("#insurances").jqGrid({
        url: globalVariables.baseUrl + '/guides/insurances/get',
        datatype: "json",
        colNames:['Код', 'Наименование', 'Код СМО'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'name',
                index: 'name',
                width: 200
            },
			{
				name: 'code',
                index: 'code',
                width: 200
			}
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#insurancesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Страховые компании",
        height: 300,
        ondblClickRow: editInsurance
    });

    $("#medworkers").jqGrid('navGrid','#insurancesPager',{
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


    function getSelectedRegionsIds(chooserId)
    {
        result = new Array();
        ids = $.fn[chooserId].getChoosed();
        console.log(ids);
        // Перебираем массив ids и записываем ид-шники из него в массив result
        for (i=0;i<ids.length;i++)
        {
            result.push(ids[i].id);
        }

        return result;
    }

    $("#insurance-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addInsurancePopup').modal('hide');
            // Перезагружаем таблицу
            $("#insurances").trigger("reloadGrid");
            $("#insurance-add-form")[0].reset(); // Сбрасываем форму
            // очищаем чюююююузер
            $('#insuranceRegionsChooserAdd .choosed').empty();
            // очищаем варианты
            $('#insuranceRegionsChooserAdd .variants').empty();
        } else {

            // Удаляем предыдущие ошибки
            $('#errorAddInsurancePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddInsurancePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddInsurancePopup').modal({

            });
        }
    });

    $('#insuranceRegionsChooserAdd').on('change', function(){
        console.log('Значение чюзера изменилось');
        valuesArray = getSelectedRegionsIds('insuranceRegionsChooserAdd');
        console.log(valuesArray);
        $('#insuranceRegionsHiddenAdd').val(   $.toJSON(valuesArray) );
        // Теперь надо заэнкодить значение чюзера и записать в специальное скрытое поле
    });

    $('#insuranceRegionsChooserEdit').on('change', function(){
        console.log('Значение чюзера изменилось');
        valuesArray = getSelectedRegionsIds('insuranceRegionsChooserEdit');
        console.log(valuesArray);
        // Теперь надо заэнкодить значение чюзера и записать в специальное скрытое поле
        $('#insuranceRegionsHiddenEdit').val(   $.toJSON(valuesArray) );
    });

    $("#insurance-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editInsurancePopup').modal('hide');
            // Перезагружаем таблицу
            $("#insurances").trigger("reloadGrid");
            $("#insurance-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddInsurancePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddInsurancePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddInsurancePopup').modal({

            });
        }
    });

    $("#addInsurance").click(function() {
        $('#addInsurancePopup').modal({
        });
    });

    $("#editInsurance").click(editInsurance);

    function editInsurance() {
        var currentRow = $('#insurances').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/insurances/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editInsurancePopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'name',
                                formField: 'name'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }

                        // Очищаем чюююузер
                        $('#insuranceRegionsChooserEdit .choosed').empty();
                        // очищаем варианты
                        $('#insuranceRegionsChooserEdit .variants').empty();

                        // Вставляем регионы в чюююююузер
                        if (data.data.regions!=undefined)
                        {
                            regionsArr = data.data.regions;
                            // Перебираем регионы
                            for (i=0;i<regionsArr.length;i++)
                            {
                               // $('#insuranceRegionsChooserEdit .choosed ul').append($('<li>').text('[' + reduceCladrCode( row.code_cladr ) + '] ' +row.name));
                                $('#insuranceRegionsChooserEdit .choosed').html(
                                    $('#insuranceRegionsChooserEdit .choosed').html()+
                                    "<span class=\"item\"" +
                                        "id=\"r"+ regionsArr[i].region_id +"\">" + '['+$.fn.reduceCladrCode( regionsArr[i].code_cladr )+ '] ' +
                                        regionsArr[i].name +
                                        "<span class=\"glyphicon glyphicon-remove\"></span></span>"
                                );
                            }
                        }
                        $("#editInsurancePopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#deleteInsurance").click(function() {
        var currentRow = $('#insurances').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/insurances/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#insurances").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddInsurancePopup .modal-body .row p').remove();
                        $('#errorAddInsurancePopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddInsurancePopup').modal({

                        });
                    }
                }
            })
        }
    });

});