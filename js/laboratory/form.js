var Laboratory = Laboratory || {};

(function(Laboratory) {

	"use strict";

    var Form = function(properties, selector) {
        console.log(selector);
        Laboratory.Component.call(this, properties, {

        }, selector);
    };

    Laboratory.extend(Form, Laboratory.Component);

    Form.prototype.render = function() {
        /* Not Implemented */
    };

    Laboratory.createForm = function(selector, properties) {
        Laboratory.create(new Form(properties, $(selector)), selector, false);
    };

    $(document).ready(function() {
        $('*[data-form]').each(function(i, item) {
            Laboratory.createForm(item);
        });
    });

})(Laboratory);