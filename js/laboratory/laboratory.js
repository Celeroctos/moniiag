var ConfirmDelete = {
    construct: function() {
        $(document).on("click", ".confirm", function(e) {
            if (ConfirmDelete.lock) {
                return void 0;
            }
            ConfirmDelete.item = $(e.target);
            $("#confirm-delete-modal").modal();
            e.stopImmediatePropagation();
            return false;
        });
        $("#confirm-delete-button").click(function() {
            ConfirmDelete.lock = true;
            if (ConfirmDelete.item != null) {
                ConfirmDelete.item.trigger("click");
            }
            setTimeout(function() {
                ConfirmDelete.lock = false;
            }, 250);
        });
    },
    item: null,
    lock: false
};

var Panel = {
    construct: function() {
        $(document).on("click", ".collapse-button", function() {
            var me = $(this);
            var body = $(me.parents(".panel")[0]).children(".panel-body");
            if ($(this).hasClass("glyphicon-collapse-up")) {
                body.slideUp("normal", function() {
                    me.removeClass("glyphicon-collapse-up")
                        .addClass("glyphicon-collapse-down");
                });
            } else {
                body.slideDown("normal", function() {
                    me.removeClass("glyphicon-collapse-down")
                        .addClass("glyphicon-collapse-up");
                });
            }
        });
    }
};

var Message = {
    display: function(json) {
        if (!json["status"]) {
            Laboratory.createMessage({
                message: json["message"]
            });
            return false
        } else if (json["message"]) {
            Laboratory.createMessage({
                type: "success",
                sign: "ok",
                message: json["message"]
            });
        }
        return true;
    }
};

var GuideColumnEditor = {
	construct: function() {
		$(document).on("click", ".guide-remove-column", function() {
			var me = $(this).parent("a").parent("div");
			me.slideUp("normal", function() {
				me.remove();
			});
		});
		$(document).on("click", "#guide-append-column", function() {
			if ($(this).children(".glyphicon-plus").length) {
				GuideColumnEditor.add($(this));
			}
		});
	},
	before: function(button) {
		button.find("span").replaceWith($("<img>", {
			width: 15,
			src: url("/images/ajax-loader.gif")
		}));
	},
	after: function(button) {
		button.find("img").replaceWith($("<span>", {
			class: "glyphicon glyphicon-plus"
		}));
	},
	render: function(component) {
		$(component.find("#guide_id").parents(".form-group")[0])
			.addClass("hidden");
		var p;
		$(".column-container").append(
			p = $("<div>").append(
				$("<a>", { href: "javascript:void(0)" }).append(
					$("<span>", {
						class: "glyphicon glyphicon-remove guide-remove-column",
						style: "color: #af1010"
					})
				)
			).append(component).append("<hr>")
		);
		p.hide().slideDown("normal");
	},
	add: function(button) {
		GuideColumnEditor.before(button);
		$.get(url("/laboratory/guide/getWidget"), {
			class: "LForm",
			model: "LGuideColumnForm",
			form: { guide_id: GuideColumnEditor.id }
		}, function(json) {
			GuideColumnEditor.after(button);
			if (!json.status) {
				return Laboratory.createMessage({
					message: json["message"]
				});
			}
			GuideColumnEditor.render($(json["component"]));
		}, "json");
	},
	id: -1
};

var GuideTable = {
    defaults: function() {
        var panel = $("#guide-edit-panel");
        panel.find(".panel-content").slideUp("normal", function() {
            $(this).empty().append(
                $("<h4>", {
                    text: "Не выбран справочник",
                    style: "text-align: center"
                })
            );
            $(this).hide().slideDown("normal");
        });
        panel.find("#guide-panel-button-group").fadeOut("fast");
    },
    load: function(id) {
        if (!id || id < 0) {
            return this.defaults();
        }
        GuideColumnEditor.id = id;
        $.get(url("/laboratory/guide/getWidget"), {
            class: "LGuideEdit",
            form: { id: id },
            model: "LGuideForm"
        }, function(json) {
            if (!Message.display(json)) {
                return void 0;
            }
            var component = $(json["component"]);
            $(component.find("#guide_id").parents(".form-group")[0]).addClass("hidden");
            $("#guide-edit-panel .panel-content").slideUp("normal", function() {
                $(this).empty().append(component);
                $(this).hide().slideDown("normal", function() {
                    $("#guide-panel-button-group").removeClass("hidden").hide().fadeIn("fast");
                });
            });
        }, "json");
        $("#guide-panel-button-group").addClass("hidden");
        $("#guide-edit-panel .panel-content").empty().append($("<div>", {
                style: "width: 100%; text-align: center"
            }).append($("<img>", { src: url("/images/ajax-loader.gif") }))
        );
    },
    remove: function(id) {
        $.get(url("/laboratory/guide/delete"), {
            id: id
        }, function(json) {
            if (!json["status"]) {
                return Laboratory.createMessage({
                    message: json["message"]
                });
            } else if (json["message"]) {
                Laboratory.createMessage({
                    type: "success",
                    sign: "ok",
                    message: json["message"]
                });
            }
            GuideTable.update();
        }, "json");
    },
    save: function() {
        var panel = $("#guide-edit-panel");
        panel.find(".form-group").removeClass("has-error");
        var serialized = [];
        panel.find("form").each(function(i, form) {
            serialized.push($(form).serialize());
        });
        $.get(url("/laboratory/guide/update"), {
            model: serialized
        }, function(json) {
            if (!json["status"]) {
                return Laboratory.postFormErrors(panel, json);
            } else {
                Message.display(json);
            }
            GuideTable.update();
            GuideTable.defaults();
        }, "json");
    },
	construct: function() {
		$("#guide-register-form").on("success", function() {
			GuideTable.update();
		});
        $(document).on("click", "#guide-table tbody tr td:not(:last-child)", function() {
            GuideTable.load($(this).parent("tr").data("id"));
        });
		$(document).on("click", ".table-edit", function() {
            var id = $($(this).parents("tr")[0]).data("id");
            $("#guide-edit-values-modal").modal();
            $("#guide-edit-values-modal .modal-body .row").empty().append(
                $("<div>", { style: "width: 100%; text-align: center" }).append($("<img>", {
                    src: url("/images/ajax-loader.gif")
                }))
            );
            $.get(url("/laboratory/guide/getWidget"), {
                class: "LGuideValues",
                guide_id: id
            }, function(json) {
                if (!Message.display(json)) {
                    return void 0;
                }
                $("#guide-edit-values-modal .modal-body .row").empty().append(
                    $(json["component"])
                );
            }, "json");
		});
		$(document).on("click", ".table-remove", function() {
            GuideTable.remove($($(this).parents("tr")[0]).data("id"));
		});
		$("#guide-edit-panel #panel-update").click(function() {
            GuideTable.save();
		});
        $("#guide-edit-panel #panel-cancel").click(function() {
            GuideTable.defaults();
        });
	},
	refresh: function(component) {
		$("#guide-table").fadeOut("fast", function() {
			var t;
			$("#guide-table").replaceWith(
				t = component
			);
			t.hide().fadeIn("fast");
		});
	},
	update: function() {
		$.get(url("/laboratory/guide/getWidget"), {
			class: "LGuideTable"
		}, function(json) {
			if (!json["status"]) {
				return Laboratory.createMessage({
					message: json["message"]
				});
			}
			GuideTable.refresh($(json["component"]));
		}, "json");
	},
	success: false
};

var GuideValues = {
	reset: function(tr) {
		tr.find("select").each(function(i, f) {
			$(f).val(-1);
			if (!$(f).val()) {
				$(f).val(0);
			}
		});
		tr.find("input, textarea").val("");
	},
	construct: function() {
		$(document).on("click", "#guide-edit-add-fields", function() {
			var item = $(this).parents(".guide-values-container").find("tr:last");
			var tr = item.clone();
			GuideValues.reset(tr);
			item.parent().append(tr);
			tr.hide().slideDown("slow");
		});
		$(document).on("click", ".guide-values-container .remove", function() {
			var tr = $(this).parent("td").parent("tr");
			if (tr.parent("tbody").children().length == 1) {
				GuideValues.reset(tr);
				Laboratory.createMessage({
					message: "Нельзя удалить единственную строку в таблице",
					type: "info"
				});
			} else {
				tr.remove();
			}
		});
	}
};

$(document).ready(function() {
	GuideColumnEditor.construct();
	ConfirmDelete.construct();
	Panel.construct();
	GuideTable.construct();
	GuideValues.construct();
});