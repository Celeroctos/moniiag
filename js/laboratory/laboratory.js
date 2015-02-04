var GuideEdit = {
	construct: function() {
		$(document).on("click", ".guide-remove-column", function() {
			var me = $(this).parent("a").parent("div");
			me.slideUp("normal", function() {
				me.remove();
			});
		});
		$(document).on("click", "#guide-append-column", function() {
			if ($(this).children(".glyphicon-plus").length) {
				GuideEdit.add($(this));
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
		GuideEdit.before(button);
		$.get(url("/laboratory/guide/getWidget"), {
			class: "LForm",
			model: "LGuideColumnForm",
			form: { guide_id: GuideEdit.id }
		}, function(json) {
			GuideEdit.after(button);
			if (!json.status) {
				return Laboratory.createMessage({
					message: json["message"]
				});
			}
			GuideEdit.render($(json["component"]));
		}, "json");
	},
	id: -1
};

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

var GuideTable = {
	construct: function() {
		$("#guide-register-form").on("success", function() {
			GuideTable.update();
		});
		$(document).on("click", ".table-edit", function() {
			var id = $(this).parents("tr").data("id");
			GuideEdit.id = id;
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
				$("#guide-edit-modal .modal-body .row").slideUp("normal", function() {
					$(this).empty().append(component).hide().slideDown("normal");
				});
			}, "json");
			$("#guide-edit-modal").modal().find(".modal-body .row").empty().append(
				$("<div>", {
					style: "width: 100%; text-align: center"
				}).append(
					$("<img>", {
						src: url("/images/ajax-loader.gif")
					})
				)
			);
		});
		$(document).on("click", ".table-remove", function() {
			$.get(url("/laboratory/guide/delete"), {
				id: $(this).parents("tr").data("id")
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
		});
		var modal = $("#guide-edit-modal");
		$("#guide-edit-modal #update").click(function() {
			modal.find(".form-group").removeClass("has-error");
			var serialized = [];
			$("#guide-edit-modal form").each(function(i, form) {
				serialized.push($(form).serialize());
			});
			$.get(url("/laboratory/guide/update"), {
				model: serialized
			}, function(json) {
				if (!json["status"]) {
					return Laboratory.postFormErrors($("#guide-edit-modal"), json);
				} else {
					Message.display(json);
				}
				modal.modal("hide");
				GuideTable.update();
			}, "json");
		});
	},
	refresh: function(component) {
		$("#guide-table").fadeOut("fast", function() {
			var t;
			$("#guide-table").replaceWith(
				t = component
			);
			console.log(component[0]);
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

$(document).ready(function() {
	GuideEdit.construct();
	ConfirmDelete.construct();
	Panel.construct();
	GuideTable.construct();
});