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
     * @constructor
     */
    Laboratory.Sub = function(component) {
        this.component = function() {
            return component;
        };
        Laboratory.Component.call(this, {}, {}, true);
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
     * Generate url based on Yii's base url
     * @param url {string} - Relative url
     * @returns {string} - Absolute url
     */
    window.url = function(url) {
        return window["globalVariables"]["baseUrl"] + url;
    };

})(Laboratory);