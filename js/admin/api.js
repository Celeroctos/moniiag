/**
 * Created by dmitry on 2014-12-10.
 */

var ApiPager = {
    grid: function() {
        this._grid = $("#apiGrid").jqGrid({
            url: globalVariables.baseUrl + '/admin/api/get',
            datatype: "json",
            colNames: ['Ключ', 'Описание'],
            colModel: [
                { name: 'key',         index: 'key',         width: 250 },
                { name: 'description', index: 'description', width: 300 }
            ],
            rowNum: 25,
            rowList: [25, 50, 100],
            pager: '#apiGridPager',
            sortname: 'key',
            viewrecords: true,
            sortorder: "key",
            caption: "Ключи",
            height: 300
        }).jqGrid('navGrid','#apiGridPager', {
                edit: false,
                add: false,
                del: false
            }
        );
    },
    add: function() {
        $("#addApiPopup").modal().draggable("disable");
    },
    edit: function(key) {
        $.get(globalVariables.baseUrl + "/admin/api/one", {
            key: key
        }, function(data) {
            var json = $.parseJSON(data);
            if (!json.success) {
                console.log(json);
                return true;
            }
            var editModal = $("#editApiPopup");
            editModal.modal().draggable("disable");
            editModal.find("form #key").val(json.key);
            editModal.find("form #description").val(json.description);
        });
    },
    drop: function(key) {
        $.get(globalVariables.baseUrl + "/admin/api/delete", {
            key: key
        }, function(data) {
            var json = $.parseJSON(data);
            if (!json.success) {
                console.log(json);
                return true;
            }
            ApiPager.update(null, data);
        });
    },
    update: function(event, data) {
        var json = $.parseJSON(data);
        if (json.success) {
            $("#addApiPopup").modal("hide");
            $("#editApiPopup").modal("hide");
            $("#apiGrid").trigger("reloadGrid");
            $("#api-add-form")[0].reset();
            $("#api-edit-form")[0].reset();
        } else {
            $('#errorAddCategoriePopup .modal-body .row p').remove();
            for(var i in json.errors) {
                for(var j = 0; j < json.errors[i].length; j++) {
                    $('#errorAddCategoriePopup .modal-body .row').append("<p>" + json.errors[i][j] + "</p>")
                }
            }
            $('#errorAddCategoriePopup').modal({
            }).css("z-index", 1052).disableSelection();
        }
    },
    construct: function() {
        ApiPager.grid();
        $("#addApi").click(function() {
			ApiPager.add();
        });
        $("#editApi").click(function() {
            var rowIndex = ApiPager._grid.jqGrid('getGridParam', 'selrow');
            if (rowIndex != null) {
				ApiPager.edit(ApiPager._grid.jqGrid('getRowData', rowIndex).key);
            }
        });
        $("#deleteApi").click(function() {
            var rowIndex = apiPager._grid.jqGrid('getGridParam', 'selrow');
            if (rowIndex != null) {
                apiPager.drop(apiPager._grid.jqGrid('getRowData', rowIndex).key);
            }
        });
        $("#api-add-form").on("success", ApiPager.update);
        $("#api-edit-form").on("success", ApiPager.update);
    },
    _grid: null
};

var ApiRulePager = {
	grid: function() {
		this._grid = $("#apiRuleGrid").jqGrid({
			url: globalVariables.baseUrl + '/admin/apiRule/get',
			datatype: "json",
			colNames: ['#', 'Ключ', 'Контроллер', 'Запись', 'Чтение'],
			colModel: [
				{ name: 'id',         index: 'id',         width: 20  },
				{ name: 'api_key',    index: 'api_key',    width: 250 },
				{ name: 'controller', index: 'controller', width: 200 },
				{ name: 'writable',   index: 'writable',   width: 60  },
				{ name: 'readable',   index: 'readable',   width: 60  }
			],
			rowNum: 25,
			rowList: [25, 50, 100],
			pager: '#apiRuleGridPager',
			sortname: 'id',
			viewrecords: true,
			sortorder: "id",
			caption: "Правила доступа",
			height: 300
		}).jqGrid('navGrid','#apiRuleGridPager', {
				edit: false,
				add: false,
				del: false
			}
		);
	},
	add: function() {
		$("#addApiRulePopup").modal().draggable("disable");
	},
	edit: function(key) {
		$.get(globalVariables.baseUrl + "/admin/apiRule/one", {
			key: key
		}, function(data) {
			var json = $.parseJSON(data);
			if (!json.success) {
				console.log(json);
				return true;
			}
			var editModal = $("#editApiPopup");
			editModal.modal().draggable("disable");
			editModal.find("form #key").val(json.key);
			editModal.find("form #description").val(json.description);
		});
	},
	drop: function(id) {
		$.get(globalVariables.baseUrl + "/admin/apiRule/delete", {
			id: id
		}, function(data) {
			var json = $.parseJSON(data);
			if (!json.success) {
				console.log(json);
				return true;
			}
			ApiRulePager.update(null, data);
		});
	},
	update: function(event, data) {
		var json = $.parseJSON(data);
		if (json.success) {
			$("#addApiRulePopup").modal("hide");
			$("#editApiRulePopup").modal("hide");
			$("#apiRuleGrid").trigger("reloadGrid");
			$("#rule-add-form")[0].reset();
		} else {
			$('#errorAddCategoriePopup .modal-body .row p').remove();
			for(var i in json.errors) {
				for(var j = 0; j < json.errors[i].length; j++) {
					$('#errorAddCategoriePopup .modal-body .row').append("<p>" + json.errors[i][j] + "</p>")
				}
			}
			if (json.message) {
				$('#errorAddCategoriePopup .modal-body .row').append("<p>" + json.message + "</p>")
			}
			$('#errorAddCategoriePopup').modal({
			}).css("z-index", 1052).disableSelection();
		}
	},
	construct: function() {
		ApiRulePager.grid();
		$("#addApiRule").click(function() {
			ApiRulePager.add();
		});
		$("#editApiRule").click(function() {
			var rowIndex = ApiRulePager._grid.jqGrid('getGridParam', 'selrow');
			if (rowIndex != null) {
				ApiRulePager.edit(ApiRulePager._grid.jqGrid('getRowData', rowIndex).key);
			}
		});
		$("#deleteApiRule").click(function() {
			var rowIndex = ApiRulePager._grid.jqGrid('getGridParam', 'selrow');
			if (rowIndex != null) {
				ApiRulePager.drop(ApiRulePager._grid.jqGrid('getRowData', rowIndex).id);
			}
		});
		$("#rule-add-form").on("success", ApiRulePager.update);
	},
	_grid: null
};

$(document).ready(function() {
    ApiPager.construct();
	ApiRulePager.construct();
});