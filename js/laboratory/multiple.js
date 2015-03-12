var Multiple = {
    load: function(me) {
        var item = $("<div>", {
            class: "multiple"
        }).append($(me).clone().addClass("multiple-value")).append(
            $("<div>", {
                class: "multiple-container form-control"
            })
        );
        $(me).replaceWith(item);
    },
    construct: function() {
        var me = this;
        $(document).on("change", "select.multiple-value", function() {
            me.choose($(this).parents(".multiple"), $.valHooks["select"].get(this));
        });
        $(document).on("click", ".form-down-button", function() {
            $(this).parents(".form-group").find("select.multiple-value").children("option").each(function(i, item) {
                me.choose($(item).parents(".multiple"), $(item).val());
            });
        });
        $(document).on("click", ".form-up-button", function() {
            $(this).parents(".form-group").find(".multiple-chosen").each(function(i, item) {
                me.remove($(item).children("div"));
            });
        });
    },
    remove: function(it) {
        it.parents(".multiple").find("select.multiple-value").append(
            $("<option>", {
                value: it.data("key"),
                text: it.text()
            })
        );
        it.parent(".multiple-chosen").remove();
    },
    choose: function(multiple, key) {
        if (Array.isArray(key)) {
            for (var i in key) {
                this.choose(multiple, key[i]);
            }
            if (!key.length) {
                multiple.find("div.multiple-container").empty();
            }
            return void 0;
        }
        var name = multiple.find("select.multiple-value")
            .find("option[value='" + key + "']").remove().text();
        if (!name.length) {
            return void 0;
        }
        var r, t;
        t = $("<div>", {
            style: "text-align: left; width: 100%",
            class: "multiple-chosen"
        }).append(
            $("<div>", {
                text: name,
                style: "text-align: left; width: calc(100% - 15px); float: left"
            }).data("key", key)
        ).append(
            r = $("<span>", {
                class: "glyphicon glyphicon-remove",
                style: "color: #af1010; font-size: 15px; cursor: pointer"
            })
        );
        multiple.find("div.multiple-container").append(t).disableSelection();
        r.click(function() {
            Multiple.remove($(this).parent("div").children("div"));
        });
    }
};

$.valHooks["select-multiple"] = {
    container: function(item) {
        return $(item).parent(".multiple").children(".multiple-container");
    },
    set: function(item, list) {
        var multiple = $(item).parents(".multiple");
        if (!list.length || list == "[]") {
            multiple.find(".multiple-chosen div").each(function(i, div) {
                Multiple.remove($(div));
            });
            list = "[]";
        }
        Multiple.choose(multiple, $.parseJSON(list));
    },
    get: function(item) {
        var list = [];
        this.container(item).find(".multiple-chosen div").each(function(i, div) {
            list.push($(div).data("key"));
        });
        return list;
    }
};

$(document).bind("ajaxSuccess", function() {
    $("select[multiple]").each(function() {
        if (!$(this).hasClass("multiple-value")) {
            Multiple.load(this);
        }
    });
    $("select[multiple][value!='']").each(function() {
        $(this).val($(this).attr("value"));
    });
    $("select.multiple-value[value!='']").each(function() {
        $(this).val($(this).attr("value"));
    });
});

$(document).ready(function() {
    $("select[multiple]").each(function() {
        Multiple.load(this);
    });
	Multiple.construct();
});