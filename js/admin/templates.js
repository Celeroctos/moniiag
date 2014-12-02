$(document).ready(function() {

	var countOfItemsToSave = 0;
	var totalSavedItems = 0;

	var elementFormModel = new FormModelManager(
		"id," +
		"/type," +
		"/categorie_id," +
		"label," +
		"guide_id," +
		"allow_add," +
		"is_required," +
		"label_after," +
		"size," +
		"is_wrapped," +
		"/position," +
		"config," +
		"default_value," +
		"label_display," +
		"show_dynamic," +
		"hide_label_before"
	);

	var categoryFormModel = new FormModelManager(
		"id," +
		"name," +
		"/parent_id," +
		"is_dynamic," +
		"/position," +
		"is_wrapped"
	);

	var editCategoryPopup = $("#editCategoriePopup");
	var designTemplatePopup = $("#designTemplatePopup");
	var editElementPopup = $("#editElementPopup");
	var addCategoriePopup = $("#addCategoriePopup");

	$("#templates").jqGrid({
        url: globalVariables.baseUrl + '/admin/templates/get',
        datatype: "json",
        colNames:['Код', 'Название', 'Страница', 'Категории', 'Обязательность диагноза', 'Порядок', '', '', ''],
        colModel:[
			{
                name:'id',
                index:'id',
                width: 150
            }, {
                name: 'name',
                index:'name',
                width: 150
            }, {
                name: 'page',
                index:'page',
                width: 150
            }, {
                name: 'categories',
                index:'categories',
                width: 150,
                searchoptions: {
                    searchhidden: true
                }
            }, {
                name: 'primary_diagnosis_desc',
                index:'primary_diagnosis_desc',
                width: 200
            }, {
                name: 'index',
                index: 'index',
                width: 80
            }, {
                name: 'page_id',
                index: 'pageId',
                hidden: true
            }, {
                name: 'categorie_ids',
                index: 'categorie_ids',
                hidden: true
            }, {
                name: 'primary_diagnosis',
                index: 'primary_diagnosis',
                hidden: true
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#templatesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Шаблоны",
        height: 300,
        ondblClickRow: showTemplate
    });

    $("#templates").jqGrid('navGrid','#templatesPager',{
            edit: false,
            add: false,
            del: false
        }, {}, {}, {}, {
            closeOnEscape:true,
            multipleSearch :true,
            closeAfterSearch: true
        }
    );

    $("#addTemplate").click(function() {
        $('#addTemplatePopup').modal().draggable("disable");
    });

    $("#template-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) {
        	// Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addTemplatePopup').modal('hide');
            // Перезагружаем таблицу
            $("#templates").trigger("reloadGrid");
            $("#template-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddTemplatePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddTemplatePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }
            $('#errorAddTemplatePopup').modal();
        }
    });

    $("#template-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) {
        	// Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editTemplatePopup').modal('hide');
            // Перезагружаем таблицу
            $("#templates").trigger("reloadGrid");
            $("#template-edit-form")[0].reset();

        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddTemplatePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddTemplatePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }
            $('#errorAddTemplatePopup').modal();
        }
    });

    function editTemplate() {
        var currentRow = $('#templates').jqGrid('getGridParam','selrow');
		if(currentRow == null) {
			return false;
		}
		var success = function(data) {
			if(data.success != true) {
				return false;
			}
			// Заполняем форму значениями
			var form = $('#editTemplatePopup form')
			// Соответствия формы и модели
			var fields = [
				{
					modelField: 'id',
					formField: 'id'
				}, {
					modelField: 'name',
					formField: 'name'
				}, {
					modelField: 'categorie_ids',
					formField: 'categorieIds'
				}, {
					modelField: 'page_id',
					formField: 'pageId'
				}, {
					modelField: 'primary_diagnosis',
					formField: 'primaryDiagnosisFilled'
				}, {
					modelField: 'index',
					formField: 'index'
				}
			];
			for(var i = 0; i < fields.length; i++) {
				var node = form.find('#' + fields[i].formField);
				// Выпадающий список с несколькими значениями
				if(node.attr('multiple') == 'multiple') {
					data.data[fields[i].modelField] = $.parseJSON(
						data.data[fields[i].modelField]
					);
				}
				node.val(data.data[fields[i].modelField]);
			}
			$("#editTemplatePopup").removeData("modal").modal();
		};
		// Надо вынуть данные для редактирования
		$.ajax({
			'url': globalVariables.baseUrl + '/admin/templates/getone?id=' + currentRow,
			'cache': false,
			'dataType': 'json',
			'type': 'GET',
			'success': success
		})
    }

    $("#editTemplate").click(editTemplate);

    $("#deleteTemplate").click(function() {
        var currentRow = $('#templates').jqGrid('getGridParam','selrow');
		if(currentRow == null) {
			return true;
		}
		var success = function(data) {
			if(data.success == 'true') {
				$("#templates").trigger("reloadGrid");
			} else {
				// Удаляем предыдущие ошибки
				$('#errorAddTemplatePopup .modal-body .row p').remove();
				$('#errorAddTemplatePopup .modal-body .row').append("<p>" + data.error + "</p>")
				// Отображаем модальное окно
				$('#errorAddTemplatePopup').modal();
			}
		};
		// Надо вынуть данные для редактирования
		$.ajax({
			'url': globalVariables.baseUrl + '/admin/templates/delete?id=' + currentRow,
			'cache': false,
			'dataType': 'json',
			'type': 'GET',
			'success': success
		});
    });

    $('#showTemplate').on('click', function(e) {
        showTemplate();
    });

    function showTemplate() {
        var currentRow = $('#templates').jqGrid('getGridParam','selrow');
		if(currentRow == null) {
			return false;
		}
		var success = function(data) {
			if(data.success) {
				$('#showTemplatePopup .modal-body .row').html(data.data);
				$('#showTemplatePopup .btn-sm').prop('disabled', true);
				$('#showTemplatePopup').modal({});
				$("#templates").trigger("reloadGrid");
				$('#showTemplate').attr({
					'disabled' : false
				}).text('Просмотр шаблона');
			} else {
				// Удаляем предыдущие ошибки
				$('#errorAddTemplatePopup .modal-body .row p').remove();
				$('#errorAddTemplatePopup .modal-body .row').append("<p>" + data.error + "</p>")
				$('#errorAddTemplatePopup').modal();
			}
		};
		$('#showTemplate').prop({
			'disabled' : true
		}).text('Подождите, шаблон вызывается...');
		// Надо вынуть данные для редактирования
		$.ajax({
			'url': globalVariables.baseUrl + '/admin/templates/show?id=' + currentRow,
			'cache': false,
			'type': 'GET',
			'dataType': 'json',
			'success': success
		})
    }

    var collection = null;

    $("#categorie-add-form").on('success', function(eventObj, ajaxData) {
        // parse response
        ajaxData = $.parseJSON(ajaxData);
        // check status
        if (ajaxData.success != true) {
            console.log(ajaxData);
            throw new Error("Assert");
        }
        // update data
        collection.model(ajaxData.category, true);
        collection.update();
		// update parent_id list
		appendParentSelectID(collection);
    });

	var updatePercents = function() {
		// update percents value
		$("#savingTemplatePopup .template-engine-saving-pc").text(
			(++totalSavedItems / countOfItemsToSave * 100).toFixed(0) + "%"
		);
		// destroy modal
		if (countOfItemsToSave != 0 && totalSavedItems >= countOfItemsToSave) {
			$("#savingTemplatePopup").modal("hide");
		}
	};

    $("#categorie-edit-form").on('success', function(eventObj, ajaxData) {
		// check for empty collection
		if (!collection) {
			return true;
		}
        // parse response
        var ajaxData = $.parseJSON(ajaxData);
        // update category after edit
        collection.model(ajaxData.category, true);
        collection.update();
		// update percents
		updatePercents();
    });

    $("#element-add-form").on('success', function (eventObj, ajaxData) {
		// check for empty collection
		if (!collection) {
			return true;
		}
		// parse response
        var ajaxData = $.parseJSON(ajaxData);
		// set element's model
		collection.model(ajaxData.element, true);
		collection.update();
    });

    $("#element-edit-form").on('success', function (eventObj, ajaxData) {
		// check for empty collection
		if (!collection) {
			return true;
		}
		// parse response
		var ajaxData = $.parseJSON(ajaxData);
		// set element's model
		collection.model(ajaxData.element, true);
		collection.update();
		// update percents
		updatePercents();
    });

	designTemplatePopup.find(".btn-primary").click(function() {
		var cc = TemplateEngine.getCategoryCollection();
		cc.compute(true);
		countOfItemsToSave = 0;
		totalSavedItems = 0;
		var update = function(item) {
			if (!item.has("id")) {
				return false;
			}
			for (var i in item.children()) {
				if (!item.children(i)) {
					continue;
				}
				update(item.children(i));
			}
			if (TemplateEngine.isCategory(item)) {
				if (!item.parent() || !TemplateEngine.isCategory(item.parent())) {
					item.field("parent_id", -1);
				}
				if (item.test("position") && item.test("parent_id")) {
					return true;
				}
				categoryFormModel.append($('#editCategoriePopup form'), function(field, info) {
					if (!item.has(info.native)) {
						return null;
					}
					return item.field(info.native);
				});
				$.post(categoryFormModel.form().attr("action"), categoryFormModel.form().serialize(), updatePercents);
				++countOfItemsToSave;
			}
			if (TemplateEngine.isItem(item)) {
				if (item.test("position") && item.test("categorie_id")) {
					return true;
				}
				elementFormModel.append($('#editElementPopup form'), function(field, info) {
					if (!item.has(info.native)) {
						return null;
					}
					return item.field(info.native);
				});
				$.post(elementFormModel.form().attr("action"), elementFormModel.form().serialize(), updatePercents);
				++countOfItemsToSave;
			}
			return true;
		};
		update(cc);
		designTemplatePopup.modal("hide");
		var json = "[";
		for (var i in cc.children()) {
			if (!cc.children(i) || !cc.children(i).has("id")) {
				continue;
			}
			json += cc.children(i).field("id") + ",";
		}
		if (json.length > 1) {
			json = json.substring(0, json.length - 1);
		}
		json += "]";
		// set request on server to update template categories
		$.ajax({
		    'url': globalVariables.baseUrl + "/admin/templates/utc?tid="
		        + cc.field("id") + "&cids=" + json,
		    'cache': false,
		    'dataType': 'json',
		    'type': 'GET'
		});
		if (countOfItemsToSave > 0) {
			setTimeout(function() {
				if (totalSavedItems < countOfItemsToSave) {
					$("#savingTemplatePopup").modal({
						backdrop: 'static',
						keyboard: false
					}).draggable("disable");
				}
			}, 400);
		}
	});

	var appendParentSelectID = function(category) {
		if (!category.has("id") || !category.has("name")) {
			return false;
		}
		$('#addCategoriePopup form').find("#parentId").append(
			$("<option></option>", {
				value: category.field("id"),
				html: category.field("name")
			})
		);
		$('#editCategoriePopup form').find("#parentId").append(
			$("<option></option>", {
				value: category.field("id"),
				html: category.field("name")
			})
		);
		$('#addElementPopup form').find("#categorieId").append(
			$("<option></option>", {
				value: category.field("id"),
				html: category.field("name")
			})
		);
		$('#editElementPopup form').find("#categorieId").append(
			$("<option></option>", {
				value: category.field("id"),
				html: category.field("name")
			})
		);
		$('#findCategoryPopup form').find("#categorieId").append(
			$("<option></option>", {
				value: category.field("id"),
				html: category.field("name")
			})
		);
	};

	var removeParentSelectID = function(category) {
		if (!category.has("id")) {
			return false;
		}
		$('#addCategoriePopup form').find("#parentId").children(
			"option[value=\"" + category.field("id") + "\"]"
		).remove();
		$('#editCategoriePopup form').find("#parentId").children(
			"option[value=\"" + category.field("id") + "\"]"
		).remove();
		$('#addElementPopup form').find("#parentId").children(
			"option[value=\"" + category.field("id") + "\"]"
		).remove();
		$('#editElementPopup form').find("#parentId").children(
			"option[value=\"" + category.field("id") + "\"]"
		).remove();
		$('#findCategoryPopup form').find("#parentId").children(
			"option[value=\"" + category.field("id") + "\"]"
		).remove();
	};

	var onCloneCategory = function(that) {
		var _item = function(item) {
			item.field("categorie_id", that.field("id"));
			elementFormModel.append($('#addElementPopup form'), function(field, info) {
				if (item.has(info.native)) {
					return item.field(info.native);
				}
				return null;
			});
			$.post(elementFormModel.form().attr("action"), elementFormModel.form().serialize(), function(ajaxData) {
				// parse response
				var ajaxData = $.parseJSON(ajaxData);
				// check status
				if (ajaxData.success != true) {
					console.log(ajaxData); throw new Error("Assert");
				}
				// set element's model
				item.model(ajaxData.element, true);
				item.update();
			});
		};
		var _category = function(item) {
			if (!item.parent() || !TemplateEngine.isCategory(item.parent())) {
				item.field("parent_id", -1);
			}
			categoryFormModel.append($('#addCategoriePopup form'), function(field, info) {
				if (item.has(info.native)) {
					return item.field(info.native);
				}
				return null;
			});
			$.post(categoryFormModel.form().attr("action"), categoryFormModel.form().serialize(), function(ajaxData) {
				// parse response
				ajaxData = $.parseJSON(ajaxData);
				// check status
				if (ajaxData.success != true) {
					console.log(ajaxData); throw new Error("Assert");
				}
				// update data
				item.model(ajaxData.category, true);
				item.update();
				// update parent_id list
				appendParentSelectID(item);
				// save other categories and elements
				for (var i in item.children()) {
					if (!item.children(i)) {
						continue;
					}
					_save(item.children(i));
				}
			});
		};
		var _save = function(item) {
			if (!item.has("id")) {
				return false;
			}
			if (TemplateEngine.isCategory(item)) {
				_category(item);
			}
			if (TemplateEngine.isItem(item)) {
				_item(item);
			}
			return true;
		};
		_save(that);
	};

    var onAppendCategory = function(that) {
        var parent = that.parent();
		if (!parent.length()) {
			return false;
		}
		if (parent.has("id")) {
			if (TemplateEngine.isCategory(parent)) {
				that.field("parent_id", parent.field("id"));
			} else {
				that.field("parent_id", -1);
			}
		}
		collection = that;
		categoryFormModel.append($('#addCategoriePopup form'), function(field, info) {
			if (info.hidden) {
				field.parent(".col-xs-9").parent(".form-group")
					.css("visibility", "hidden")
					.css("position", "absolute");
				return that.field(info.native);
			} else {
				if (that.has(info.native)) {
					return that.field(info.native);
				}
				return null;
			}
		});
		$('#addCategoriePopup').modal({
			backdrop: 'static',
			keyboard: false
		}).draggable("disable").css("z-index", 1051);
    };

    var onEditCategory = function(that) {
		// save this instance
        collection = that;
		categoryFormModel.append($('#editCategoriePopup form'), function(field, info) {
			if (info.hidden) {
				field.parent(".col-xs-9").parent(".form-group")
					.css("visibility", "hidden")
					.css("position", "absolute");
			}
			return that.field(info.native);
		});
		// display modal window
		editCategoryPopup.modal({
			backdrop: 'static',
			keyboard: false
		}).draggable("disable").css("z-index", 1051);
    };

    var onRemoveCategory = function(that) {
		if (!that.has("id")) {
			return false;
		}
        $.ajax({
            'url' : globalVariables.baseUrl + '/admin/categories/delete?id=' + that.field("id"),
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET'
        });
		// update parent_id list
		removeParentSelectID(that);
    };

    var onAppendElement = function(that) {
		var parent = that.parent();
		if (!parent.length()) {
			return false;
		}
		if (!that.has("position") || !+that.field("position")) {
			that.field("position", 1);
		}
		if (parent.has("id")) {
			that.field("categorie_id", parent.field("id"));
		} else {
			return false;
		}
		collection = that;
		elementFormModel.append($('#addElementPopup form'), function(field, info) {
			if (info.hidden) {
				field.parent(".col-xs-9").parent(".form-group")
					.css("visibility", "hidden")
					.css("position", "absolute");
			}
			if (that.has(info.native)) {
				return that.field(info.native);
			}
			return null;
		});
		$('#addElementPopup').modal({
			backdrop: 'static',
			keyboard: false
		}).draggable("disable");
    };

    var onEditElement = function(that) {
        var data = {
            data: that.model()
        };
        if (!TemplateEngine.isItem(that)) {
            return false;
        }
        collection = that;
        // Заполняем форму значениями
        var form = $('#editElementPopup form')
        // Соответствия формы и модели
        var fields = [{
            modelField: 'id',
            formField: 'id'
        }, {
            modelField: 'type',
            formField: 'type',
			hidden: true
        }, {
            modelField: 'categorie_id',
            formField: 'categorieId',
            hidden: true
        }, {
            modelField: 'label',
            formField: 'label'
        }, {
            modelField: 'guide_id',
            formField: 'guideId'
        }, {
            modelField: 'allow_add',
            formField: 'allowAdd'
        }, {
            modelField: 'is_required',
            formField: 'isRequired'
        }, {
            modelField: 'label_after',
            formField: 'labelAfter'
        }, {
            modelField: 'size',
            formField: 'size'
        }, {
            modelField: 'is_wrapped',
            formField: 'isWrapped'
        }, {
            modelField: 'position',
            formField: 'position',
            hidden: true
        }, {
            modelField: 'config',
            formField: 'config'
        }, {
            modelField: 'default_value',
            formField: 'defaultValue'
        }, {
            modelField: 'default_value',
            formField: 'defaultValueText'
        }, {
            modelField: 'label_display',
            formField: 'labelDisplay'
        }, {
            modelField: 'show_dynamic',
            formField: 'showDynamic'
        }, {
            modelField: 'hide_label_before',
            formField: 'hideLabelBefore'
        }
        ];
        $('#editElementPopup #showDynamic').prop('disabled', data.data['type'] == 4);
        for (var i = 0; i < fields.length; i++) {
            // Подгрузка значений справочника для дефолтного значения
            if (fields[i].formField == 'defaultValue' && (data.data['type'] == 2 || data.data['type'] == 3)) {
                // Это значение ставится асинхронно
                $('select#guideId').trigger('change', [data.data[fields[i].modelField]]);
                continue;
            }
            var formField = form.find('#' + fields[i].formField).val(
                data.data[fields[i].modelField]
            );
            if (fields[i].hidden) {
				formField.parent(".col-xs-9").parent(".form-group")
					.css("visibility", "hidden")
					.css("position", "absolute");
            }
            // Таблица
            if (fields[i].formField == 'config') {
                if(typeof data.data['config'] != 'object') {
                    var config = $.parseJSON(data.data['config']);
                } else {
                    var config = data.data['config'];
                }
                if (data.data['type'] == 4) {
                    printHeadersTable(config,
                        $('#editElementPopup .table-config-headers tbody'),
                        $('#editElementPopup .colsHeaders'),
                        $('#editElementPopup .rowsHeaders'),
                        $('#editElementPopup #numRows'),
                        $('#editElementPopup #numCols')
                    );
                    printDefaultValuesTable(config.numCols, config.numRows);
                    if (config.values != undefined && config.values != null) {
                        writeDefValuesFromConfig(config.values);
                    }
                }
                if (data.data['type'] == 5) {
                    $('#editElementPopup').find('#numberFieldMaxValue, #numberFieldMinValue, #numberStep').parents('.form-group').removeClass('no-display');
                    $('#editElementPopup #numberFieldMaxValue').val(config.maxValue);
                    $('#editElementPopup #numberFieldMinValue').val(config.minValue);
                    $('#editElementPopup #numberStep').val(config.step);
                }
                if (data.data['type'] == 6) {
                    $('#editElementPopup').find('#dateFieldMaxValue, #dateFieldMinValue').parents('.form-group').removeClass('no-display');
                    if (config != null && config != '') {
                        $('#editElementPopup #dateFieldMaxValue').val(config.maxValue);
                        $('#editElementPopup #dateFieldMinValue').val(config.minValue);
                    } else {
                        // Если конфига нет - надо просто поставить пустое значение
                        $('#editElementPopup #dateFieldMaxValue').val('');
                        $('#editElementPopup #dateFieldMinValue').val('');
                    }
                    // Затриггерим контрол, чтобы данные подкачались в видимые поля контрола
                    $('#editElementPopup #dateFieldMaxValue').trigger('change');
                    $('#editElementPopup #dateFieldMinValue').trigger('change');
                }
            }
        }
        // Теперь нужно проверить - если взведён флаг "есть зависимость" - нужно выключить некоторые опции в
        //    в изменении типа
        if (data.data.is_dependencies == 1) {
            $('#element-edit-form select#type option:not([value=2]):not([value=3])').addClass('no-display');
        } else {
            $('#element-edit-form select#type option').removeClass('no-display');
        }
        $.proxy(form.find("select#type").trigger('change'), form.find("select#type")); // $.proxy - вызов контекста
        editElementPopup.modal({
			backdrop: 'static',
			keyboard: false
		}).draggable("disable").disableSelection();
    };

	var onRemoveElement = function(that) {
		if (!that.has("id")) {
			return false;
		}
		$.ajax({
			'url' : globalVariables.baseUrl + '/admin/elements/delete?id=' + that.field("id"),
			'cache' : false,
			'dataType' : 'json',
			'type' : 'GET'
		});
	};

	var registerTemplateEngine = function(template) {
		// restart template engine to remove all
		// categories and it's elements
		TemplateEngine.registerTemplate(template)
            // Categories actions
            .onAppend("category", function() {
				if (this.template() && this.template().key() == "clone") {
					onCloneCategory(this);
				} else {
					onAppendCategory(this);
				}
            })
			.onEdit("category", function() {
				onEditCategory(this);
			})
            .onRemove("category", function() {
                onRemoveCategory(this);
            })
            // Elements actions
            .onAppend(null, function() {
				if (!TemplateEngine.isItem(this)) {
					return false;
				}
				if (this.template().key() === "static") {
					return false;
				}
				onAppendElement(this);
            })
			.onEdit(null, function() {
				if (!TemplateEngine.isItem(this)) {
					return false;
				}
				if (this.template().key() === "static") {
					return false;
				}
				onEditElement(this);
			})
            .onRemove(null, function() {
				if (!TemplateEngine.isItem(this)) {
					return false;
				}
				if (this.template().key() === "static") {
					return false;
				}
				onRemoveElement(this);
            })
	};

    function designTemplate() {
        var currentRow;
        if((currentRow = $('#templates').jqGrid('getGridParam','selrow')) == null) {
            return false;
        }
		var success = function(data) {
			// check data for success and terminate execution
			// if we have any errors
			if(data.success != true) {
				console.log(data);
				return false;
			}
			// register template engine with some template, it
			// will restart engine and append current categories
			registerTemplateEngine(data.template);
			// display template engine designer modal window
			$('#designTemplatePopup').modal({
				backdrop: 'static',
				keyboard: false
			}).draggable("disable").disableSelection();
		};
        $.ajax({
            'url': globalVariables.baseUrl + '/admin/templates/getcategories?id=' + currentRow,
            'cache': false,
            'dataType': 'json',
            'type': 'GET',
            'success': success
        });
    }

    $("#designTemplate").click(designTemplate);
});