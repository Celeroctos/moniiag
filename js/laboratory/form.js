var Laboratory = Laboratory || {};

(function(Laboratory) {

	"use strict";

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
        //$("select[multiple]").each(function() {
        //    Multiple.load(this);
        //});
        //$("select[multiple][value!='']").each(function() {
        //    $(this).val($(this).attr("value"));
        //});
        $("select.multiple-value[value!='']").each(function() {
            $(this).val($(this).attr("value"));
        });
    });

    $(document).ready(function() {
        //$("select[multiple]").each(function() {
        //    Multiple.load(this);
        //});
    });

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

    var Search = function(parent, selector) {
        Laboratory.Sub.call(this, parent, selector);
    };

    Laboratory.extend(Search, Laboratory.Sub);

    Search.prototype.render = function() {
        console.log(this.selector()[0]);
    };

    var Form = function(properties, selector) {
        Laboratory.Component.call(this, properties, {
            opacity: 0.4,
            animation: 100,
            url: null,
            parent: null
        }, selector);
        this.property("parent") && this.property("parent").find(".form-search-button").each(function(i, item) {
            $(item).popover({
                content: "Hello, World"
            });
        });
    };

    Laboratory.extend(Form, Laboratory.Component);

    Form.prototype.render = function() {
        this.update();
    };

    Form.prototype.before = function() {
        this.selector().find(".form-group").animate({
            opacity: this.property("opacity")
        }, this.property("animation"));
        this.property("parent") && this.property("parent").find(".refresh-button").replaceWith(
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
        this.property("parent") && this.property("parent").find(".refresh-image").replaceWith(
            $("<span>", {
                class: "glyphicon glyphicon-refresh refresh-button",
                style: "font-size: 25px; cursor: pointer"
            })
        );
    };

    Form.prototype.update = function(after) {
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
            after && after(me);
        }, "json");
    };

    Form.prototype.activate = function() {
        var me = this;
        this.property("parent") && this.property("parent").find(".refresh-button").click(function() {
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

    Laboratory.postFormErrors = function(where, json) {
        var html = $("<ul>");
        for (var i in json["errors"] || []) {
            $(where.find("input#" + i + "[value=''], select#" + i + "[value='-1'], #" + i + "textarea[value='']")
                .parents(".form-group")[0]).addClass("has-error");
            for (var j in json["errors"][i]) {
                $("<li>", {
                    text: json["errors"][i][j]
                }).appendTo(html);
            }
        }
        return Laboratory.createMessage({
            message: json["message"] + html.html(),
            delay: 10000
        });
    };

    Form.prototype.send = function(after) {
        this.selector().find(".form-group").removeClass("has-error");
        var form = this.selector();
        if (!this.property("url")) {
            return Laboratory.createMessage({
                message: "Missed 'url' property for Form component"
            });
        }
        var me = this;
        $.get(this.property("url"), {
            "model": form.serialize()
        }, function(json) {
            me.after();
            if (!json["status"]) {
                after && after(me, false);
                var html = $("<ul>");
                for (var i in json["errors"] || []) {
                    $($("#" + i).parents(".form-group")[0]).addClass("has-error");
                    for (var j in json["errors"][i]) {
                        $("<li>", {
                            text: json["errors"][i][j]
                        }).appendTo(html);
                    }
                }
                return Laboratory.createMessage({
                    message: json["message"] + html.html(),
                    delay: 10000
                });
            } else {
                if (me.property("success")) {
                    me.property("success").call(me, json);
                }
                after && after(me, true);
            }
            if (json["message"]) {
                Laboratory.createMessage({
                    type: "success",
                    sign: "ok",
                    message: json["message"]
                });
            }
            $("#" + me.selector().attr("id")).trigger("success", json);
        }, "json");
        form.serialize();
        return true;
    };

    Laboratory.createForm = function(selector, properties) {
        return Laboratory.create(new Form(properties, $(selector)), selector, false);
    };

    $(document).ready(function() {
        Multiple.construct();
        $("[id$='-panel'], [id$='-modal']").each(function(i, item) {
            if (!$(item).find("form").length) {
                return void 0;
            }
            var f = Laboratory.createForm($(item).find("form")[0], {
                url: $(item).find("form").attr("action"),
                parent: $(item)
            });
            $(item).find("button.btn[type='submit']").click(function() {
                var btn = this;
                var c = function(me, status) {
                    $(btn).button("reset");
                    if (status) {
                        $(item).modal("hide");
                    }
                };
                if (f.send(c)) {
                    $(this).data("loading-text", "Загрузка ...").button("loading");
                }
            });
        });
        $("[id$='-modal']").on("show.bs.modal", function() {
            $(this).draggable("disable");
            var form = $(this).find("form");
            if (!form.length) {
                return void 0;
            }
            form.find("input, textarea").val("");
            form.find("select", -1);
            form.find(".form-group").removeClass("has-error");
        });
    });

})(Laboratory);