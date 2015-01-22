var Laboratory = Laboratory || {};

(function(Laboratory) {

	"use strict";

    var url = window["globalVariables"]["baseUrl"];

    var Form = function(properties, selector) {
        Laboratory.Component.call(this, properties, {}, selector);
    };

    Laboratory.extend(Form, Laboratory.Component);

    Form.prototype.render = function() {
        /* Not Implemented */
    };

    Form.prototype.update = function() {
        var me = this;
        var form = this.selector().find("[data-form]");
        $.get(url + "/laboratory/test/getWidget", {
            class: form.data("form"),
            model: form.serialize()
        }, function(json) {
            if (!json.status) {
                return Laboratory.createMessage({
                    message: json.message
                });
            }
            var parent = me.selector().parent();
            me.selector().remove();
            me.selector($(json["component"])).appendTo(parent);
            me.activate();
        }, "json");
    };

    Form.prototype.activate = function() {
        var me = this;
        this.selector().find(".refresh").click(function() {
            $(this).replaceWith(
                $("<img>", {
                    src: url + "/images/ajax-loader.gif",
                    width: "25px"
                })
            );
            me.update();
        });
    };

    Laboratory.createForm = function(selector, properties) {
        Laboratory.create(new Form(properties, $(selector)), selector, false);
    };

    $(document).ready(function() {
        $("[id$='-panel']").each(function(i, item) {
            Laboratory.createForm(item);
        });
    });

})(Laboratory);