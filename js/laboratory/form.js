var Laboratory = Laboratory || {};

(function(Laboratory) {

	"use strict";

    var Form = function(properties, selector) {
        Laboratory.Component.call(this, properties, {
            opacity: 0.4,
            animation: 100,
            url: null,
            parent: null
        }, selector);
    };

    Laboratory.extend(Form, Laboratory.Component);

    Form.prototype.render = function() {
        this.update();
    };

    Form.prototype.before = function() {
        this.selector().find(".form-group").animate({
            opacity: this.property("opacity")
        }, this.property("animation"));
        this.property("parent").find(".refresh-button").replaceWith(
            $("<img>", {
                src: url("/images/ajax-loader.gif"),
                width: "25px",
                class: "refresh-image"
            })
        );
    };

    Form.prototype.after = function() {
        this.selector().find(".form-group").animate({
            opacity: 1
        }, this.property("animation"));
        this.property("parent").find(".refresh-image").replaceWith(
            $("<span>", {
                class: "glyphicon glyphicon-refresh refresh-button",
                style: "font-size: 25px; cursor: pointer"
            })
        );
    };

    Form.prototype.update = function() {
        var me = this;
        var form = this.selector();
        if (!this.property("url")) {
            return Laboratory.createMessage({
                message: "Missed 'url' property for Form component"
            });
        }
        var url = this.property("url").substring(
            0, this.property("url").lastIndexOf("/")
        ) + "/getWidget";
        this.before();
        console.log(form.serialize());
        $.get(url, {
            class: form.data("widget"),
            form: form.serialize(),
            id: form.attr("id"),
            model: form.data("form"),
            url: form.attr("action")
        }, function(json) {
            if (!json.status) {
                me.after();
                me.activate();
                return Laboratory.createMessage({
                    message: json.message
                });
            }
            me.selector().replaceWith(
                me.selector($(json["component"]))
            );
            me.selector().find(".form-group").css("opacity",
                me.property("opacity")
            );
            me.after();
            me.activate();
        }, "json");
    };

    Form.prototype.activate = function() {
        var me = this;
        this.property("parent").find(".refresh-button").click(function() {
            $(this).replaceWith(
                $("<img>", {
                    src: url("/images/ajax-loader.gif"),
                    width: "25px",
                    class: "refresh-image"
                })
            );
            me.update();
        });
    };

    Laboratory.createForm = function(selector, properties) {
        Laboratory.create(new Form(properties, $(selector)), selector, false);
    };

    $(document).ready(function() {
        $("[id$='-panel'], [id$='-modal']").each(function(i, item) {
            Laboratory.createForm($(item).find("form")[0], {
                url: $(item).find("form").attr("action"),
                parent: $(item)
            });
        });
        $("[id$='-modal']").on("show.bs.modal", function() {
            $(this).draggable("disable");
        });
    });

})(Laboratory);

$(document).ready(function() {
    $("#test-form-modal").modal();
});