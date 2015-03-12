var Laboratory = Laboratory || {};

(function(Laboratory) {

    "use strict";

    /**
     * Extend child class with parent
     * @param child {function} - Child class
     * @param parent {function} - Parent class
     * @returns {function} - Child class
     */
    Laboratory.extend = function(child, parent) {
        var F = function() {};
        F.prototype = parent.prototype;
        child.prototype = new F();
        child.prototype.constructor = child;
        child.superclass = parent.prototype;
        return child;
    };

    /**
     * Construct component
     * @param properties {{}} - Object with properties
     * @param [defaults] {{}|null|undefined} - Default component's properties
     * @param [selector] {jQuery|null|undefined} - Component's selector or nothing
     * @constructor
     */
    Laboratory.Component = function(properties, defaults, selector) {
        this._properties = $.extend(
            defaults || {}, properties || {}
        );
        this._selector = selector || this.render();
    };

    /**
     * Override that method to return jquery item
     */
    Laboratory.Component.prototype.render = function() {
        throw new Error("Component/render() : Not-Implemented");
    };

    /**
     * Override that method to activate just created jquery item
     */
    Laboratory.Component.prototype.activate = function() {
        /* Ignored */
    };

    /**
     * Override that method to provide some actions before update
     */
    Laboratory.Component.prototype.before = function() {
        /* Ignored */
    };

    /**
     * Override that method to provide some actions after update
     */
    Laboratory.Component.prototype.after = function() {
        /* Ignored */
    };

    /**
     * Set/Get component's jquery selector
     * @param [selector] {jQuery} - New jquery to set
     * @returns {jQuery} - Component's jquery
     */
    Laboratory.Component.prototype.selector = function(selector) {
        if (arguments.length > 0) {
            if (!selector.data("laboratory")) {
                selector.data("laboratory", this);
            }
            this._selector = selector;
        }
        return this._selector;
    };

    /**
     * Get/Set some property
     * @param key {string} - Property key
     * @param value  {*} - Property value
     * @returns {*} - New or old property's value
     */
    Laboratory.Component.prototype.property = function(key, value) {
        if (arguments.length > 1) {
            this._properties[key] = value;
        }
        return this._properties[key];
    };

    /**
     * Override that method to destroy you component or
     * it will simply remove selector
     */
    Laboratory.Component.prototype.destroy = function() {
        this.selector().remove();
    };

    /**
     * Update method, will remove all selector, render
     * new, activate it and append to previous parent
     */
    Laboratory.Component.prototype.update = function() {
        this.before();
        this.selector().replaceWith(
            this.selector(this.render())
        );
        this.after();
        this.activate();
    };

    /**
     * Sub-Component class, use it to declare sub component, that instance
     * won't be rendered automatically, you shall manually invoke render method
     * @param component {Component} - Parent component
     * @param [selector] {jQuery} - jQuery's selector or null
     * @constructor
     */
    Laboratory.Sub = function(component, selector) {
        this.component = function() {
            return component;
        };
        Laboratory.Component.call(this, {}, {}, selector || true);
    };

    Laboratory.extend(Laboratory.Sub, Laboratory.Component);

    /**
     * That method will fetch properties values from
     * parent's component
     * @param key {String} - Property name
     * @param value {*} - Property value
     */
    Laboratory.Sub.prototype.property = function(key, value) {
        return this.component().property.apply(this.component(), arguments);
    };

    /**
     * Create new component's instance and render to DOM
     * @param component {Laboratory.Component|Object} - Component's instance
     * @param selector {HTMLElement|string} - Parent's selector
     * @param [update] {Boolean} - Update component or not (default yes)
     */
    Laboratory.create = function(component, selector, update) {
        $(selector).data("laboratory", component).append(
            component.selector()
        );
        if (update !== false) {
            component.update();
        } else {
            component.activate();
        }
        return component;
    };
    
    /**
     * Is string ends with some suffix
     * @param suffix {string} - String suffix
     * @returns {boolean} - True if string has suffix
     */
    String.prototype.endsWith = function(suffix) {
        return this.indexOf(suffix, this.length - suffix.length) !== -1;
    };

    /**
     * Is string starts with some prefix
     * @param prefix {string} - String prefix
     * @returns {boolean} - True if string has prefix
     */
    String.prototype.startsWidth = function(prefix) {
        return this.indexOf(prefix, 0) !== -1;
    };

    /**
     * Construct message as component to render
     * Properties: {
     *  delay {int} - Close timeout
     *  open {function} - Event on after open
     *  close {function} - Event on after close
     *  type {string} - Bootstrap type (danger, warning, info, success)
     *  message {string} - Message to display,
     *  sign {string} - Bootstrap sign (ok, question, info, exclamation, warning, plus, minus, remove)
     * }
     * @param properties {{}} - Properties
     * @constructor
     */
    var Message = function(properties) {
        Laboratory.Component.call(this, properties, {
            type: "danger",
            message: "Not-Initialized",
            sign: "info",
            delay: 5000
        });
    };

    Laboratory.extend(Message, Laboratory.Component);

    /**
     * Render message component
     * @returns {jQuery}
     */
    Message.prototype.render = function() {
        return $("<div></div>", {
            class: "alert " + ("alert-" + this.property("type")) + " jaw-message-wrapper",
            role: "alert"
        }).append(
            $("<span></span>", {
                class: "glyphicon glyphicon-" + this.property("sign") + "-sign",
                style: "margin-right: 10px"
            })
        ).append(
            $("<span></span>", {
                class: "jaw-message",
                html: this.property("message")
            })
        );
    };

    /**
     * Activate message component, it will add click event
     * and animate message opening from left edge
     */
    Message.prototype.activate = function() {
        var me = this;
        this.selector().click(function() {
            me.destroy();
        }).css("left", (-this.selector().width() * 2) + "px");
        this.open();
    };

    /**
     * Open message (animate from left edge)
     * @param [after] {function|null|undefined} - Callback after open
     */
    Message.prototype.open = function(after) {
        var me = this;
        if (parseInt(this.selector().css("left")) < 0) {
            this.selector().animate({
                "left": "5px"
            }, "slow", null, function() {
                if (me.property("open")) {
                    me.property("open").call(me);
                }
                if (after) {
                    after(me);
                }
            });
            setTimeout(function() {
                me.close();
            }, this.property("delay"));
        }
    };

    /**
     * Close message component, if it hasn't been opened yet
     * @param [after] {function|null|undefined} - Callback after close
     */
    Message.prototype.close = function(after) {
        var me = this;
        if (parseInt(this.selector().css("left")) > 0) {
            this.selector().animate({
                "left": "-" + parseInt(this.selector().css("width")) + "px"
            }, "slow", null, function() {
                if (me.property("close")) {
                    me.property("close").call(me);
                }
                if (after) {
                    after(me);
                }
            });
            Collection.destroy(me);
        }
    };

    /**
     * Overridden destroy method, it will close current component (move
     * to left edge) and invoke super destroy method
     */
    Message.prototype.destroy = function() {
        this.close(function(me) {
            Laboratory.Component.prototype.destroy.call(me);
        });
    };

    /**
     * Collection is a singleton, which stores active messages and
     * will put new message after previous (with new top offset)
     * @type {{create: Function, destroy: Function, _components: Array}}
     */
    var Collection = {
        create: function(properties) {
            var message = new Message(properties);
            Laboratory.create(message, document.body);
            message.selector().css("top", parseInt(message.selector().css("top")) + "px");
            for (var i in this._components) {
                this._components[i].selector().animate({
                    top: parseInt(this._components[i].selector().css("top")) + message.selector().height() + 37
                });
            }
            this._components.push(message);
            return message;
        },
        destroy: function(component) {
            var move = [];
            for (var i in this._components) {
                if (this._components[i] == component) {
                    this._components.splice(i, 1);
                    break;
                } else {
                    move.push(this._components[i]);
                }
            }
            for (i in move) {
                move[i].selector().animate({
                    top: parseInt(move[i].selector().css("top")) - component.selector().height() - 37
                });
            }
        },
        _components: []
    };

    /**
     * Create new message instance with some properties
     * @param properties {{}} - Message component's properties
     */
    Laboratory.createMessage = function(properties) {
        Collection.create(properties);
    };

    /**
     * Generate url based on Yii's base url
     * @param url {string} - Relative url
     * @returns {string} - Absolute url
     */
    window.url = function(url) {
		if (url.charAt(0) != "/") {
			url = "/" + url;
		}
        return window["globalVariables"]["baseUrl"] + url;
    };

})(Laboratory);

$(document).ready(function() {
    $("input[data-regexp][type='text']").each(function(i, item) {
        var regexp = new RegExp($(item).data("regexp"));
        $(item).keydown(function(e) {
            console.log($(item).val());
            console.log(regexp.test($(item).val()));
        });
    });
});

/*
 var isStrValid = function(str) {
 return ((str.match(/[^\d^.]/) === null)
 && (str.replace(/\d+\.?\d?\d?/, "") === ""));
 };

 var node = dojo.byId("txt");
 dojo.connect(node, "onkeyup", function() {
 if (!isStrValid(node.value)) {
 node.value = node.value.substring(0, node.value.length-1);
 }
 });
* */