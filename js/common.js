/*
  ___ ___  ___ __  __         __  __  ___  ___  ___ _            __  __   _   _  _   _   ___ ___ ___
 | __/ _ \| _ \  \/  |  ___  |  \/  |/ _ \|   \| __| |     ___  |  \/  | /_\ | \| | /_\ / __| __| _ \
 | _| (_) |   / |\/| | |___| | |\/| | (_) | |) | _|| |__  |___| | |\/| |/ _ \| .` |/ _ \ (_ | _||   /
 |_| \___/|_|_\_|  |_|       |_|  |_|\___/|___/|___|____|       |_|  |_/_/ \_\_|\_/_/ \_\___|___|_|_\

 <example>
	 var categoryFormModel = new FormModelManager(
		"id, name, /parent_id, is_dynamic, /position, is_wrapped"
	 );
	 var elementFormModel = new FormModelManager(
		 "id," +
		 "type," +
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
	 elementFormModel.append($("#editElementPopup form"), function(field) {
		 field.parent(".col-xs-9").parent(".form-group")
			.css("visibility", "hidden")
			.css("position", "absolute");
	 });
 </example>
 */

/**
 * Класс, реализующий базовый функционал для работы с моделями CActiveForm
 * и другими, в частности отвечает за преобразование имен из обычной нотации
 * таблиц к нотации JavaScript и создает готовые формы для отправки запросов
 * @constructor - Ничего не принимает
 * @param [fields] {String} - Строка, в которой (через запятую) указываются
 *      названия всех полей столбцов таблицы
 */
var FormModelManager = function(fields) {
    if (arguments.length > 0) {
        this._fieldMap = this.add(fields);
    } else {
        this._fieldMap = [];
    }
	this._form = null;
};

/**
 * Статический метод, преобразует имя из обычной нотации названия столбца
 * в таблице базы данных к нотации JavaScript, например столбец "parent_id"
 * будет преобразован к "parentId" и т.д.
 * @static - Статический метод, не может имеет контекста "this"
 * @param name {String} - Название столбца таблицы, например "parent_id"
 * @returns {String} - Преобразованную стороку к JavaScript нотации
 */
FormModelManager.convertField = function(name) {
    var words = name.split("_");
    var result = "";
    for (var i in words) {
        if (i > 0) {
            result += words[i].charAt(0).toUpperCase() + words[i].substr(1);
        } else {
            result = words[i];
        }
    }
    return result;
};

/**
 * Удалить все элементы из менеджера моделей формы
 */
FormModelManager.prototype.clear = function() {
    this._fieldMap = [];
};

/**
 * Добавить новый элементы или элементы, добавляются как срока
 * разделенные запятой. Если перед именем элемента указать
 * символ /, то он по умолчанию получит модификатор "hidden"
 * @param fields {String} - Строка, в которой (через запятую) указываются
 *      названия всех полей столбцов таблицы
 * @returns {Array} - Созданных массив со всеми элементами
 */
FormModelManager.prototype.add = function(fields) {
    this._fieldMap = this._fieldMap || [];
    var fieldArray = fields.split(",");
    for (var i in fieldArray) {
        var native = fieldArray[i].trim();
        var hidden = false;
        if (native[0] == '/') {
            native = native.substr(1);
            hidden = true;
        }
        this._fieldMap[native] = {
            native: native,
            name: FormModelManager.convertField(native),
            hidden: hidden
        };
    }
    return this._fieldMap;
};

/**
 * Удалить элементы из менеджера, удаляются таким же образом
 * как и добавляются (список имен столбцов указывается через
 * запятую)
 * @param fields {String} - Строка, в которой (через запятую) указываются
 *      названия всех полей столбцов таблицы
 * @returns {Array} - Созданных массив со всеми элементами
 */
FormModelManager.prototype.remove = function(fields) {
    this._fieldMap = this._fieldMap || [];
    var fieldArray = fields.split(",");
    for (var i in fieldArray) {
        var native = fieldArray[i].trim();
        if (!this._fieldMap[native]) {
            continue;
        }
        this._fieldMap.splice(native, 1);
    }
    return this._fieldMap;
};

/**
 * Спрятать указанные элементы, если указанные элементы не существуют, то
 * они будут добавлены
 * @param fields {String} - Строка, в которой (через запятую) указываются
 *      названия всех полей столбцов таблицы
 * @returns {Array} - Созданных массив со всеми элементами
 */
FormModelManager.prototype.hide = function(fields) {
    this._fieldMap = this._fieldMap || [];
    var fieldArray = fields.split(",");
    for (var i in fieldArray) {
        var native = fieldArray[i].trim();
        if (!this._fieldMap[native]) {
            this.add(native);
        }
        this._fieldMap[native].hidden = true;
    }
    return this._fieldMap;
};

/**
 * Отобразить указанные элементы, если указанные элементы не существуют, то
 * они будут добавлены
 * @param fields {String} - Строка, в которой (через запятую) указываются
 *      названия всех полей столбцов таблицы
 * @returns {Array} - Созданных массив со всеми элементами
 */
FormModelManager.prototype.show = function(fields) {
    this._fieldMap = this._fieldMap || [];
    var fieldArray = fields.split(",");
    for (var i in fieldArray) {
        var native = fieldArray[i].trim();
        if (!this._fieldMap[native]) {
            this.add(native);
        }
        this._fieldMap[native].hidden = false;
    }
    return this._fieldMap;
};

/**
 * Возвращает список всех зарегестрированных полей
 * @param [index] - Если индекс не указан, то возвращается массив со
 *      всеми элементами, иначе значение элемента по индексу
 * @returns {Array|*} - Массив со всеми элементами или элемент по индексу
 */
FormModelManager.prototype.fields = function(index) {
    if (arguments.length > 0) {
        if (this._fieldMap) {
            return this._fieldMap[index];
        } else {
            return null;
        }
    } else {
        return this._fieldMap || [];
    }
};

/**
 * Привязывает созданные переменные к существующей форме через селектор
 * @param form {jQuery} - Селектор формы для привязки
 * @param [setField] {Function} - Функция, которая прячет объект, принимает
 *      текущее поле и инофрмацию о поле
 */
FormModelManager.prototype.append = function(form, setField) {
    var fields = this.fields();
	this._form = form;
    for(var key in fields) {
        var formField = form.find('#' + fields[key].name);
		if (setField && formField.length) {
			var v = (setField(
				formField, fields[key]
			));
			if (v) {
				formField.val(v);
			}
		}
    }
};

FormModelManager.prototype.form = function() {
	return this._form;
};