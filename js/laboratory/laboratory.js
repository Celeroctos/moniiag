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
            if ($(this).hasClass("glyphicon-chevron-up")) {
                body.slideUp("normal", function() {
                    me.removeClass("glyphicon-chevron-up")
                        .addClass("glyphicon-chevron-down");
                });
            } else {
                body.slideDown("normal", function() {
                    me.removeClass("glyphicon-collapse-down")
                        .addClass("glyphicon-chevron-up");
                });
            }
        });
    }
};

var Table = {
	fetch: function(me, parameters) {
		var table = $(me).parents(".table[data-class]");
		$.get(url("/laboratory/medcard/getWidget"), $.extend(parameters, {
			class: table.data("class"),
			condition: table.data("condition"),
			params: table.data("parameters")
		}), function(json) {
			if (!Message.display(json)) {
				return void 0;
			}
			$(me).parents(".table[data-class]").replaceWith(
				$(json["component"])
			);
		}, "json");
	},
	order: function(row) {
		var parameters = {};
		if ($(this).find(".glyphicon-chevron-down").length) {
			parameters["desc"] = true;
		}
		parameters["sort"] = row;
		Table.fetch(this, parameters);
	},
	page: function(page) {
		var td = $(this).parents(".table[data-class]").find("tr:first-child td .glyphicon").parents("td");
		var parameters = {};
		if (td.find(".glyphicon-chevron-up").length) {
			parameters["desc"] = true;
		}
		parameters["sort"] = td.data("key");
		parameters["page"] = page;
		Table.fetch(this, parameters);
	}
};

var DropDown = {
    change: function(animate, update) {
        const DELAY = 100;
        if (animate === undefined) {
            animate = true;
        }
        if (update === undefined) {
            update = true;
        }
        var hide = function(group) {
            if (!group.hasClass("hidden")) {
                if (animate) {
                    group.slideUp(DELAY, function() {
                        $(this).addClass("hidden");
                    });
                } else {
                    group.addClass("hidden");
                }
            }
        };
        var show = function(group) {
            if (group.hasClass("hidden")) {
                if (animate) {
                    group.removeClass("hidden").hide().slideDown(DELAY);
                } else {
                    group.removeClass("hidden");
                }
            }
        };
        var toggle = function(group, it, wait) {
            setTimeout(function() {
                if (it.val() == "dropdown" || it.val() == "multiple") {
                    show(group);
                } else {
                    hide(group);
                }
            }, wait)
        };
        var group = function(that, id) {
            return $(that).parents("form").find("#" + id).parents(".form-group");
        };
        if ($(this).attr("id") == "type" && !$(this).attr("data-update")) {
            var fields = [
                "lis_guide_id",
                "display_id"
            ];
            if ($(this).val() != "dropdown" && $(this).val() != "multiple") {
                fields = fields.reverse();
            }
            var i;
            for (i in fields) {
                toggle(group(this, fields[i]), $(this), i * DELAY);
            }
            if (update && $(this).attr("data-update") && !this.disable) {
                var me = this;
                setTimeout(function () {
                    DropDown.update($(me));
                }, i * DELAY);
            }
        } else if (update && $(this).attr("data-update") && !this.disable) {
            DropDown.update($(this));
        }
    },
    update: function(select, after) {
        var f;
        var form = $(select.parents("form")[0]);
        if (!form.data("laboratory")) {
            f = Laboratory.createForm(form[0], {
                url: url("/laboratory/guide/getWidget")
            });
        } else {
            f = form.data("laboratory");
        }
        f.update(after);
    },
    disable: false
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
			p = $("<div>", { class: "guide-column-handle" }).append(
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

var GuideTableViewer = {
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
    sortable: function() {
        $(".column-container").sortable().disableSelection();
    },
    calculate: function() {
        var index = 1;
        $(".column-container").find("input[type='number']#position").each(function(i, it) {
            $(it).val(index++);
        });
    },
    load: function(id) {
        if (!id || id < 0) {
            return this.defaults();
        }
        GuideColumnEditor.id = id;
        $.get(url("/laboratory/guide/getWidget"), {
            class: "LGuideColumnEditor",
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
                component.find("select#type").each(function(i, d) {
                    DropDown.change.call(d, false);
                });
                GuideTableViewer.calculate();
                $(this).hide().slideDown("normal", function() {
                    $("#guide-panel-button-group").removeClass("hidden").hide().fadeIn("fast", function() {
                        GuideTableViewer.sortable();
                    });
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
        if (id == GuideColumnEditor.id) {
            this.defaults();
        }
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
            GuideTableViewer.update();
        }, "json");
    },
    save: function() {
        this.calculate();
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
            GuideTableViewer.update();
            GuideTableViewer.defaults();
        }, "json");
    },
	construct: function() {
		$("#guide-register-form").on("success", function() {
			GuideTableViewer.update();
		});
        $(document).on("click", "#guide-table tbody tr td:not(:last-child)", function() {
            GuideTableViewer.load($(this).parent("tr").data("id"));
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
                class: "LGuideValueEditor",
                guide_id: id
            }, function(json) {
                if (!Message.display(json)) {
                    return void 0;
                }
                $("#guide-edit-values-modal .modal-body .row").empty().append(
                    $(json["component"])
                );
            }, "json");
            GuideValueEditor.guideId = id;
		});
		$(document).on("click", ".table-remove", function() {
            GuideTableViewer.remove($($(this).parents("tr")[0]).data("id"));
		});
		$("#guide-edit-panel #panel-update").click(function() {
            GuideTableViewer.save();
		});
        $("#guide-edit-panel #panel-cancel").click(function() {
            GuideTableViewer.defaults();
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
			GuideTableViewer.refresh($(json["component"]));
		}, "json");
	},
	success: false
};

var GuideValueEditor = {
	reset: function(tr) {
		tr.find("select").each(function(i, f) {
            if ($(f).hasClass("multiple-value")) {
                $(f).val("");
            } else {
                $(f).val(-1);
                if (!$(f).val()) {
                    $(f).val(0);
                }
            }
		});
		tr.find("input, textarea").val("");
        tr.removeAttr("data-id");
        tr.find("td").each(function(i, f) {
            $(f).removeAttr("data-id", null);
        });
	},
	construct: function() {
		$(document).on("click", "#guide-edit-add-fields", function() {
			var item = $(this).parents(".guide-values-container").find("tr:last");
			var tr = item.clone(true);
			GuideValueEditor.reset(tr);
			item.parent().append(tr);
			tr.hide().slideDown("slow");
		});
		$(document).on("click", ".guide-values-container .remove", function() {
			var tr = $(this).parent("td").parent("tr");
			if (tr.parent("tbody").children().length == 1) {
				tr.remove();
				$("#guide-edit-values-modal #register").trigger("click");
				$("#guide-edit-values-modal").modal("hide");
			} else {
				tr.remove();
			}
		});
        $("#guide-edit-values-modal").on("click", "#register", function() {
            var data = [];
            $(".guide-values-container tbody tr").each(function(i, tr) {
                var row = [];
                $(tr).find("input, select, textarea").each(function(i, item) {
                    row.push({
                        position: $(this).parents("td").data("position"),
                        id: $(this).parents("td").data("id"),
                        value: $(item).val()
                    });
                });
                data.push({
                    id: $(tr).data("id"),
                    model: row
                });
            });
            $.post(url("/laboratory/guide/apply"), {
                guide_id: GuideValueEditor.guideId,
                data: data
            }, function(json) {
                if (!Message.display(json)) {
                    return void 0;
                }
                $("#guide-edit-values-modal").modal("hide");
            }, "json");
        });
	},
    guideId: null
};

var MedcardSearch = {
	construct: function() {
		$("#medcard-search-button").click(function() {
			MedcardSearch.search();
		});
	},
	search: function() {
		$.post(url("/laboratory/medcard/search"), {
			model: [
				$("#medcard-search-form").serialize(),
				$("#medcard-range-form").serialize()
			]
		}, function(json) {
			if (!Message.display(json)) {
				return void 0;
			}
			$("#medcard-table").replaceWith(
				$(json["component"])
			);
		}, "json");
	}
};

$(document).ready(function() {
	GuideColumnEditor.construct();
	ConfirmDelete.construct();
	Panel.construct();
	GuideTableViewer.construct();
	GuideValueEditor.construct();
	MedcardSearch.construct();
});