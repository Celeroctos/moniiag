/**
 * Created by dmitry on 2014-12-10.
 */

var ApiPager = {
    grid: function() {
        this._grid = $("#apiGrid").jqGrid({
            url: globalVariables.baseUrl + '/admin/api/get',
            datatype: "json",
            colNames: ['Ключ', 'Путь', 'Описание'],
            colModel: [
                { name: 'key',         index: 'key',         width: 135 },
				{ name: 'path',        index: 'path',        width: 175 },
                { name: 'description', index: 'description', width: 250 }
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
            editModal.find("form #key").val(json.api.key);
            editModal.find("form #description").val(json.api.description);
			editModal.find("form #path").val(json.api.path);
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
            var rowIndex = ApiPager._grid.jqGrid('getGridParam', 'selrow');
            if (rowIndex != null) {
				ApiPager.drop(ApiPager._grid.jqGrid('getRowData', rowIndex).key);
            }
        });
        $("#api-add-form").on("success", ApiPager.update);
        $("#api-edit-form").on("success", ApiPager.update);
    },
    _grid: null
};

$(document).ready(function() {
    ApiPager.construct();
});