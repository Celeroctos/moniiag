var GuideRegister = {
	construct: function() {
		$(document).on("click", ".guide-remove-column", function() {
			var me = $(this).parent("a").parent("div");
			me.slideUp("slow", function() {
				me.remove();
			});
		});
		this.button = $("#guide-append-column").click(function() {
			if ($(this).children(".glyphicon-plus").length) {
				GuideRegister.add();
			}
		});
		//$("#guide_id").parents(".form-group").addClass("hidden");
	},
	before: function() {
		this.button.find("span").replaceWith($("<img>", {
			width: 15,
			src: url("/images/ajax-loader.gif")
		}));
	},
	after: function() {
		this.button.find("img").replaceWith($("<span>", {
			class: "glyphicon glyphicon-plus"
		}));
	},
	render: function(component) {
		$(component.find("#guide_id").parents(".form-group")[0])
			.addClass("hidden");
		var p;
		$(".column-container").append(
			p = $("<div>").append(
				$("<a>", {
					href: "javascript:void(0)"
				}).append(
					$("<span>", {
						class: "glyphicon glyphicon-remove guide-remove-column",
						style: "color: #af1010"
					})
				)
			).append(component).append("<hr>")
		);
		p.hide().slideDown("slow");
	},
	add: function() {
		GuideRegister.before();
		$.get(url("/laboratory/guide/getWidget"), {
			class: "LForm",
			model: "LGuideColumnForm"
		}, function(json) {
			GuideRegister.after();
			if (!json.status) {
				return Laboratory.createMessage({
					message: json["message"]
				});
			}
			GuideRegister.render($(json["component"]));
		}, "json");
	},
	button: null
};

var ConfirmDelete = {
	construct: function() {
		$(".confirm").popover({
			placement: "bottom",
			html: true,
			title: "Подтвердить действие?",
			trigger: "manual",
			content: ConfirmDelete.render()
		}).on("show.bs.popover", function() {
			ConfirmDelete.activate($(this));
		});
		$(document).on("click", ".confirm", function() {
			console.log(123);
		});
	},
	render: function() {
		return $("<div>").append(
			$("<button>", {
				class: "btn btn-default",
				text: "Отмена",
				id: "confirm-cancel-button"
			})
		).append(
			$("<button>", {
				class: "btn btn-danger",
				text: "Удалить",
				style: "margin-left: 5px",
				id: "confirm-delete-button"
			})
		);
	},
	activate: function(popover) {
		console.log(popover[0]);
		popover.find(".btn-default").click(function() {
			console.log("cancel");
		});
		popover.find(".btn-danger").click(function() {
			console.log("delete");
		});
	}
};

var Table = {
	construct: function() {

	}
};

$(document).ready(function() {
	GuideRegister.construct();
	Table.construct();
	ConfirmDelete.construct();
});