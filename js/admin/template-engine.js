/*
  _____ ___ __  __ ___ _      _ _____ ___         ___ _  _  ___ ___ _  _ ___
 |_   _| __|  \/  | _ \ |    /_\_   _| __|  ___  | __| \| |/ __|_ _| \| | __|
   | | | _|| |\/| |  _/ |__ / _ \| | | _|  |___| | _|| .` | (_ || || .` | _|
   |_| |___|_|  |_|_| |____/_/ \_\_| |___|       |___|_|\_|\___|___|_|\_|___|

 */

/**
 * @type {TemplateEngine} - Base template engine API object
 */
var TemplateEngine = TemplateEngine || {
		/* API should be Here */
	};

(function(TemplateEngine) {

    "use strict";

    /*
       ___ ___  __  __ __  __  ___  _  _
      / __/ _ \|  \/  |  \/  |/ _ \| \| |
     | (_| (_) | |\/| | |\/| | (_) | .` |
      \___\___/|_|  |_|_|  |_|\___/|_|\_|

     */

    /**
     * @param [condition] {...Boolean} - Assertion expression
     * @param [message] {String} - Error message to throw
     */
    var assert = function(message, condition) {
        if (arguments.length <= 0) {
            throw new Error("Assertion Failed : \"?\"");
        } else {
            if (arguments.length == 1) {
                if (typeof arguments[0] === "string") {
                    throw new Error("Assertion Failed : " + message);
                } else if (arguments[0] !== true) {
                    throw new Error("Assertion Failed : \"?\"");
                }
            }
            for (var i in arguments) {
                if (!arguments.hasOwnProperty(i)) {
                    continue;
                }
                if (arguments[i] !== true && i != arguments.length - 1) {
                    throw new Error("Assertion Failed : " + message);
                }
            }
        }
    };

    /**
     *
     * @param destination
     * @param source
     */
    var extend = function(destination, source) {
        return $.extend(destination.prototype, source.prototype);
    };

    /**
     *
     * @param source
     * @returns {*}
     */
    var clone = function(source) {
        return $.extend(true, {}, source);
    };

    /*
      _  _  ___  ___  ___
     | \| |/ _ \|   \| __|
     | .` | (_) | |) | _|
     |_|\_|\___/|___/|___|

     */

    /**
     * @param parent {Node} - Node's parent
     * @constructor - Basic node class, which implements
     *      node's logic
     */
    var Node = function(parent) {
        this._parentNode = parent || null;
        this._childrenNode = [];
        this._parentIndex = -1;
    };

    /**
     * @param node {Node} - New node, mustn't be the same
     *      as some parent's, cuz it will raise infinite loop
     * @returns {Boolean} - Has node been appended to
     *      his parent, it will return true also if node
     *		has been reappended to another node
     */
    Node.prototype.append = function(node) {
        if (this === node || !node) {
            return false;
        }
        if (this.contains(node)) {
            return false;
        }
        node._parentNode = this;
        return (
            node._parentIndex = this._childrenNode
                .push(node) - 1
        ) !== -1;
    };

    /**
     * @param node {Node} - Check node for existence in
     *      current node
     * @returns {Boolean} - True if node is in it's
     *      parent
     */
    Node.prototype.contains = function(node) {
        if (!node) {
            return false;
        }
        if (node._parentIndex !== -1 && this._childrenNode[node._parentIndex] == node) {
            return true;
        }
        for (var i in this._childrenNode) {
            if (this._childrenNode.hasOwnProperty(i) && node === this._childrenNode[i]) {
                return true;
            }
        }
        return false;
    };

    /**
     * @param [node] {Node} - Node to remove from parent,
     *      it will check node's index and if it has
     *      same instance that in array, then we can
     *      apply fast remove from array, else we
     *      need to look though all elements and
     *      find it's node by instance and remove. If node
     *      is undefined, then we have to remove itself
     *      from parent, but if we havn't parent then
     *      we will truncate this node, cuz we can't
     *      store it's children anymore somewhere
     * @returns {Boolean} - If we have found node
     *      in it's parent and successfully removed from it
     */
    Node.prototype.remove = function(node) {
        var i = 0;
        if (node === undefined) {
            if (this._parentNode !== null) {
                return Node.prototype.remove.call(this._parentNode, this);
            } else {
                this.truncate();
            }
            return true;
        }
        if (!this.contains(node)) {
            return false;
        }
        if (node.index() !== -1) {
            if (this._childrenNode[node.index()] === node) {
                this._childrenNode[node.index()] = undefined;
                return true;
            } else {
                for (i in this._childrenNode) {
                    if (!this._childrenNode.hasOwnProperty(i)) {
                        continue;
                    }
                    if (node === this._childrenNode[i]) {
                        this._childrenNode[i] = undefined;
                        return true;
                    }
                }
            }
        } else {
            for (i in this._childrenNode) {
                if (!this._childrenNode.hasOwnProperty(i)) {
                    continue;
                }
                if (node === this._childrenNode[i]) {
                    this._childrenNode[i] = undefined;
                    return true;
                }
            }
        }
        return false;
    };

    /**
     * @about Truncate current node and all it's
     *      children, also it will remove all dependencies
     *      and remove itself from parent
     */
    Node.prototype.truncate = function() {
        for (var i in this._childrenNode) {
            this._childrenNode[i].truncate();
        }
        if (this._parentNode !== null) {
            this._parentNode.remove(this);
        }
        this._childrenNode = [];
    };

    /**
     * @param [index] {Number} - Parent's index, only for
     *      append method
     * @returns {Number} - Index in parent's
     *      array with all children, need for
     *      fast slice without search
     */
    Node.prototype.index = function(index) {
        if (index === undefined) {
            if (this._parentIndex < 0) {
                return this._childrenNode.length;
            } else {
                return this._parentIndex;
            }
        } else {
            this._parentIndex = index;
        }
    };

    /**
     * @param [parent] {Node} - New node's parent which
     *      should be instead of current
     * @returns {Node} - Get parent of current
     *      node, or null if node is root
     */
    Node.prototype.parent = function(parent) {
        if (parent !== undefined) {
            if (this._parentNode && this._parentNode !== parent) {
                this._parentNode.remove(this);
            }
            this._parentNode = parent;
        } else {
            return this._parentNode;
        }
    };

    /**
     * @param [children] {Array|undefined}
     * @returns {Array} - Get all node's children
     */
    Node.prototype.children = function(children) {
        if (children != undefined) {
            this._childrenNode = children;
        } else {
            return this._childrenNode;
        }
    };

    /**
     * Compute count of elements in node
     * @returns {Number} - Total count of elements
     */
    Node.prototype.count = function() {
        var totalDefined = 0;
        for (var i in this._childrenNode) {
            if (!this._childrenNode[i]) {
                continue;
            }
            ++totalDefined;
        }
        return totalDefined;
    };

    /*
      __  __  ___  ___  ___ _
     |  \/  |/ _ \|   \| __| |
     | |\/| | (_) | |) | _|| |__
     |_|  |_|\___/|___/|___|____|

     */

    var Model = function(model) {
        this._model = model || this.defaults();
    };

    Model.prototype.defaults = function() {
        throw new Error("Model/default() : \"You must override that method and return default model\"");
    };

    Model.prototype.model = function(model) {
        if (model !== undefined) {
            this._model = model;
        }
        return this._model;
    };

	Model.prototype.length = function() {
		return Object.keys(this.model()).length;
	};

	Model.prototype.fetch = function(url) {
		// create this closure
		var that = this;
		// send ajax request
		$.ajax({
			'url' : url,
			'cache' : false,
			'dataType' : 'json',
			'type' : 'GET',
			'success' : function(data, textStatus, jqXHR) {
				// check data for success and terminate execution
				// if we have any errors
				if(data.success != true) {
					console.log(data); return false;
				}
				// update component's model
				that.model(data.data);
				// update element
				that.update();
			}
		});
	};

    Model.prototype.field = function(field, value) {
        if (value !== undefined) {
            this._model[field] = value;
        }
        if (this._model[field] === undefined) {
            throw new Error("Model/field() : \"Field hasn't been declared in model\"");
        }
        return this._model[field];
    };

    /*
      ___ ___ _    ___ ___ _____ _   ___ _    ___
     / __| __| |  | __/ __|_   _/_\ | _ ) |  | __|
     \__ \ _|| |__| _| (__  | |/ _ \| _ \ |__| _|
     |___/___|____|___\___| |_/_/ \_\___/____|___|

     */

    /**
     * @param [selector] {jQuery} - Some selector
     * @constructor - Selectable is an object that can
     *      store jQuery's selector
     */
    var Selectable = function(selector) {
        Selectable.prototype.selector.call(this, selector || assert(true,
            "Selector can't be null or undefined"));
    };

    /**
     * @param [selector] {jQuery} - Getter/Setter for
     *      element's selector
     * @returns {jQuery} - Current element's selector
     */
    Selectable.prototype.selector = function(selector) {
        if (selector !== undefined) {
            if (!selector.data("instance")) {
                selector.data("instance", this);
            }
            this._jqSelector = selector;
        }
        return this._jqSelector;
    };

    /**
     * @param selectable {Selectable} - Another selectable
     *      element to append
     */
    Selectable.prototype.append = function(selectable) {
        if (!$.contains(this._jqSelector, selectable._jqSelector)) {
            this._jqSelector.append(selectable._jqSelector);
        }
    };

    /**
     * @param selectable {Selectable} - Another selectable
     *      element to append
     */
    Selectable.prototype.remove = function(selectable) {
        if (selectable == undefined) {
            Selectable.prototype.remove.call(this.parent(), this);
        } else {
            if ($.contains(this._jqSelector, selectable._jqSelector)) {
                this._jqSelector.detach(selectable._jqSelector);
            }
        }
    };

    /*
       ___ _    ___  _  _ _  _   _   ___ _    ___
      / __| |  / _ \| \| | \| | /_\ | _ ) |  | __|
     | (__| |_| (_) | .` | .` |/ _ \| _ \ |__| _|
      \___|____\___/|_|\_|_|\_/_/ \_\___/____|___|

     */

    var Clonnable = function() {
    };

    Clonnable.prototype.clone = function(parent, model, selector) {
        assert("Clonnable/clone() : \"You must implements 'clone' method\"");
    };

    /*
      ___  ___    _   ___  ___   _   ___ _    ___
     |   \| _ \  /_\ / __|/ __| /_\ | _ ) |  | __|
     | |) |   / / _ \ (_ | (_ |/ _ \| _ \ |__| _|
     |___/|_|_\/_/ \_\___|\___/_/ \_\___/____|___|

     */

    var Draggable = function() {
    };

    Draggable.prototype.drag = function() {
        assert("Draggable/drag() : \"You must implement 'drag' method\"");
    };

    /*
      ___  ___  ___  ___ ___  _   ___ _    ___
     |   \| _ \/ _ \| _ \ _ \/_\ | _ ) |  | __|
     | |) |   / (_) |  _/  _/ _ \| _ \ |__| _|
     |___/|_|_\\___/|_| |_|/_/ \_\___/____|___|

     */

    var Droppable = function() {
    };

    Droppable.prototype.drop = function() {
        assert("Droppable/drop() : \"You must implement 'drop' method\"");
    };

    /*
       ___ ___  __  __ ___  ___  _  _ ___ _  _ _____
      / __/ _ \|  \/  | _ \/ _ \| \| | __| \| |_   _|
     | (_| (_) | |\/| |  _/ (_) | .` | _|| .` | | |
      \___\___/|_|  |_|_|  \___/|_|\_|___|_|\_| |_|

     */

    var Component = function(parent, model, selector) {
		Model.call(this, model);
        Node.call(this, parent);
        Selectable.call(this, selector || this.render());
        if (this.drag) {
            this.drag();
        }
        if (this.drop) {
            this.drop();
        }
    };

    extend(Component, Node);
    extend(Component, Selectable);
    extend(Component, Model);
    extend(Component, Clonnable);

    Component.prototype.render = function() {
        assert("Component/render() : \"You must override 'render' method\"");
    };

	Component.prototype.defaults = function() {
		return {};
	};

	Component.prototype.update = function() {
		// after update we have to detach old selector
		var toDetach = this.selector();
		// and render new selector, set it to it's item
		// and attach to parent
		toDetach.parent().append(
			this.selector(this.render())
		);
		// detach old selector
		toDetach.detach();
	};

    /**
     * Locked method, cuz elements can't store something
     *
     * @param element {Component} - Item to append
     *      with it's selector
     */
    Component.prototype.append = function(element) {
        if (this === element) {
            return false;
        }
        if (Node.prototype.append.call(this, element)) {
            Selectable.prototype.append.call(this, element);
        }
		if (!element.parent()) {
			element.parent(this);
		}
        return true;
    };

    /**
     * Locked method, cuz elements can't store something
     *
     * @param [element] {Item} - Item to remove
     *      with it's selector. If element is undefined then
     *      element will be removed from it's parent
     */
    Component.prototype.remove = function(element) {
        if (this === element) {
            return false;
        }
        if (Node.prototype.remove.call(this, element)) {
            if (element != undefined) {
                Selectable.prototype.remove.call(this, element);
            } else {
                this._jqSelector.detach();
            }
        }
        return true;
    };

    /*
      _____ ___ __  __ ___ _      _ _____ ___
     |_   _| __|  \/  | _ \ |    /_\_   _| __|
       | | | _|| |\/| |  _/ |__ / _ \| | | _|
       |_| |___|_|  |_|_| |____/_/ \_\_| |___|

     */

    var Template = function(collection, key, title, id) {
		// initialize variables first
        this._title = title;
		this._id = id;
        this._key = key;
		// invoke constructors
        Draggable.call(this);
        Component.call(this, collection, null, null);
    };

    extend(Template, Component);
    extend(Template, Draggable);

	Template.prototype.id = function() {
		return this._id;
	};

    Template.prototype.title = function() {
        return this._title;
    };

    Template.prototype.key = function() {
        return this._key;
    };

    Template.prototype.render = function() {
        var title = this.title() ? "<div>" + this.title() + "</div>" : "";
        if (this.title().length <= 2) {
            return $("<div></div>", {
                html: title, class: "template-engine-item",
                style:
                	"cursor: default;" +
                	"float: left;" +
                	"width: 10px"
            });
        } else {
            return $("<div></div>", {
                html: title, class: "template-engine-item",
                style: "cursor: default;"
            });
        }
    };

    Template.prototype.drag = function() {
        this.selector().draggable({
            helper: function(e) {
                return $(this).clone().data("instance",
                    $(this).data("instance")
                );
            },
            start: function() {
                $(this).css("visibility", "hidden");
            },
            stop: function() {
                $(this).css("visibility", "visible");
            },
            revert: "invalid"
        }).disableSelection();
    };

    /*
      _____ ___ __  __ ___ _      _ _____ ___          ___ ___  _    _    ___ ___ _____ ___ ___  _  _
     |_   _| __|  \/  | _ \ |    /_\_   _| __|  ___   / __/ _ \| |  | |  | __/ __|_   _|_ _/ _ \| \| |
       | | | _|| |\/| |  _/ |__ / _ \| | | _|  |___| | (_| (_) | |__| |__| _| (__  | |  | | (_) | .` |
       |_| |___|_|  |_|_| |____/_/ \_\_| |___|        \___\___/|____|____|___\___| |_| |___\___/|_|\_|

     */

    var TemplateCollection = function(selector) {
        Component.call(this, null, null, selector);
    };

    extend(TemplateCollection, Component);

    TemplateCollection.prototype.find = function(key) {
        var children = this.children();
        for (var i in children) {
            if (children[i].key() === key) {
                return children[i];
            }
        }
        assert("TemplateCollection/find() : \"Unresolved template key (" + key + ")\"");
    };

    TemplateCollection.prototype.render = function() {
        return $("<div></div>", {
            class: "template-engine-template"
        });
    };

    /*
      ___ _____ ___ __  __
     |_ _|_   _| __|  \/  |
      | |  | | | _|| |\/| |
     |___| |_| |___|_|  |_|

     */

    /**
     * @param [parent] {Item|Node|null|undefined} - Item's parent
     * @param [selector] {jQuery|undefined} - jQuery's selector, need only for
     *      classes which extends Item
     * @param model
     * @param [template] {{}} - If element is string, then element
     *      is basic abstract template, else it's element's child
     * @constructor - Basic TemplateNode's element, which extends
     *      Node and implements all manipulations with elements
     */
    var Item = function(parent, model, selector, template) {
		// we need to save template before running render
		this._elementTemplate = template;
		// call super constructors
        Component.call(this, parent, model, selector);
        Draggable.call(this);
    };

    extend(Item, Component);

    Item.prototype.render = function() {
		var that = this;
		var editButton = $("<span></span>", {
			class: "glyphicon glyphicon-pencil"
		}).click(function() {
			TemplateEngine._triggerEdit(that);
		});
		var removeButton = $("<span></span>", {
			class: "glyphicon glyphicon-remove",
			style: "margin-right: 1px; margin-left: 3px;"
		}).click(function() {
			that.remove()
		});
        var s = $("<div></div>", {
            style: "cursor: default;",
            class: "template-engine-item"
        }).append(
			$("<div></div>", {
				html: this.template().title()
			})
		).append(
			removeButton
		);
		if (this.template().key() === "static") {
			s.addClass("template-engine-category-static");
		}
		s.dblclick(function() {
			TemplateEngine._triggerEdit(that);
		});
		return s;
    };

    Item.prototype.clone = function(parent, model, selector) {
        return new Item(parent, model, selector || null, this.template());
    };

    Item.prototype.defaults = function() {
        return {
            "type": 0,
            "categorie-id": 0,
            "label": "{label-before}",
            "guide_id": 0,
            "allow_add": false,
            "label_after": "{label-after}",
            "size": 100,
            "is_wrapped": false,
            "path": "",
            "position": 0,
            "config": "",
            "default_value": "{default}",
            "label_display": "{default-label-display}",
            "is_required": true,
            "not_printing_values": "",
            "hide_label-before": false
        };
    };

    /**
     * @param [value] {Item|String|undefined} - Item or
     *      it's name (for abstract type)
     * @returns {Item|String} - For getter template element
     */
    Item.prototype.template = function(value) {
        if (value !== undefined) {
            this._elementTemplate = value;
        }
        return this._elementTemplate;
    };

    /*
       ___   _ _____ ___ ___  ___  _____   __
      / __| /_\_   _| __/ __|/ _ \| _ \ \ / /
     | (__ / _ \| | | _| (_ | (_) |   /\ V /
      \___/_/ \_\_| |___\___|\___/|_|_\ |_|

     */

    var Category = function(parent, model, selector) {
        Component.call(this, parent, model, selector);
    };

    extend(Category, Component);
    extend(Category, Draggable);
    extend(Category, Droppable);

    Category.prototype.clone = function(parent, model, selector) {
        // TODO You must also clone all category elements
        return new Category(parent, model, selector);
    };

    Category.prototype.defaults = function() {
        return {
            "name": "{default-name}",
            "parent_id": -1,
            "position": 0,
            "is_dynamic": 0,
            "path": "",
            "is_wrapped": 1
        };
    };

	Category.prototype.update = function() {
		// after update we have to detach old selector
		var toDetach = this.selector();
		// and render new selector, set it to it's item
		// and attach to parent
		toDetach.parent().append(
			this.selector(this.render(toDetach.children(
				".template-engine-items"
			)))
		);
		// detach old selector
		toDetach.detach();
	};

    Category.prototype.render = function(items) {
		var that = this;
		try {
			var name = this.field("name");
		} catch (ignore) {
			name = "Категория";
		}
		var glyphicon = "glyphicon glyphicon-pencil";
		if (!this.length()) {
			glyphicon = "glyphicon glyphicon-floppy-save";
		}
		var editButton = $("<span></span>", {
			class: glyphicon,
			style: "margin-right: 5px;"
		}).click(function() {
			TemplateEngine._triggerEdit(that);
		});
		var removeButton = $("<span></span>", {
			class: "glyphicon glyphicon-remove",
			style: "margin-right: 5px;"
		}).click(function() {
			that.remove()
		});
        var s = $("<li></li>", {
            class: "template-engine-category"
        }).append(
			$("<div></div>", {
				style: "float: left;"
			}).append(
				$("<div></div>", {
					class: "template-engine-handle-wrapper",
					style: "float: left;"
				}).append(
					$("<div></div>", {
						class: "template-engine-handle",
						style: "float: left;",
						html: name
					})
				).append(
					editButton
				).append(
					removeButton
				)
			)
        ).append(
			items || $("<div></div>", {
                class: "template-engine-items"
            })
        );
		s.find(".template-engine-handle-wrapper").dblclick(function() {
			TemplateEngine._triggerEdit(that);
		});
		return s;
    };

    Category.prototype.append = function(element) {
        // fetch container with items
        var items = this.selector().children(".template-engine-items");
        // check for parent to parent append
        if (this === element) {
            return false;
        }
        // if we've appended node to tree
        if (Node.prototype.append.call(this, element)) {
            items.append(element.selector());
        }
        return true;
    };

    Category.prototype.drag = function() {
        this.selector().draggable();
    };

    Category.prototype.drop = function() {
        // this closure
        var that = this;
        // apply sortable
        this.selector().find(".template-engine-items").sortable({
            appendTo: document.body
        }).droppable({
            accept: function(helper) {
                // get helper's instance
                var me = helper.data("instance");
                // exit if try to check non-template element, but we can
                // move element from another category so we have to
                // accept it and add extra condition in drop event
                if (!(me instanceof Template)) {
                    return true;
                }
                // check template for category type
                return me.key() !== "category";
            },
            drop: function(e, ui) {
                // get selector
                var selector = ui.helper;
                // get received instance
                var me = selector.data("instance");
                // if we met template, then we gonna
                // create another template's instance
                // else we simply has moved item
                // another category
                if (me instanceof Template) {
                    // create new item element (we can't create another one type)
                    var item = new Item(
                        that, clone(me.model()), null, me
                    );
                    // append created item to collection
                    that.append(item);
                } else {
                    // don't append themselves to
                    // it's parents
                    if (me.parent() === that) {
                        return true;
                    }
                    // get original element's instance
                    me = ui.draggable.data("instance");
                    // now we can attach instance's clone
                    // to new category (we can't simply
                    // move item, cuz we don't know kind
                    // of element and every element appended
                    // to it's parent node and selector, so
                    // we can create it's clone and append
                    // to another element, whereupon remove
                    // old item)
                    item = me.clone();
                    that.append(item);
                    // remove element from old parent (it will remove
                    // it from it's parent and detach from parent's
                    // selector)
                    me.remove();
                }
                // add remove button to selector
                //item.selector().append(
                //    $("<button>-</button>").click(function() {
                //        $(this).parent(".template-engine-item").data("instance").remove();
                //    })
                //);
            }
        });
    };

    /*
       ___   _ _____ ___ ___  ___  _____   __          _   ___ _____ _____   ___ _____ ___  ___
      / __| /_\_   _| __/ __|/ _ \| _ \ \ / /  ___    /_\ / __|_   _|_ _\ \ / /_\_   _/ _ \| _ \
     | (__ / _ \| | | _| (_ | (_) |   /\ V /  |___|  / _ \ (__  | |  | | \ V / _ \| || (_) |   /
      \___/_/ \_\_| |___\___|\___/|_|_\ |_|         /_/ \_\___| |_| |___| \_/_/ \_\_| \___/|_|_\

     */

    var CategoryActivator = function() {
        this._activeCategory = null;
        this._activeClass = "template-engine-selected";
    };

    CategoryActivator.prototype.activate = function(category) {
        if (!arguments.length) {
            return this.deactivate();
        }
        if (this._activeCategory) {
            this.deactivate();
        }
        this._activeCategory.selector().addClass(
            this._activeClass
        );
        this._activeCategory = category;
    };

    CategoryActivator.prototype.deactivate = function(category) {
        this._activeCategory.selector().removeClass(
            this._activeClass
        );
        this._activeCategory = null;
    };

    CategoryActivator.prototype.has = function() {
        return this._activeCategory != null;
    };

    CategoryActivator.prototype.active = function() {
        return this._activeCategory;
    };

    /*
       ___   _ _____ ___ ___  ___  _____   __         ___ ___  _    _    ___ ___ _____ ___ ___  _  _
      / __| /_\_   _| __/ __|/ _ \| _ \ \ / /  ___   / __/ _ \| |  | |  | __/ __|_   _|_ _/ _ \| \| |
     | (__ / _ \| | | _| (_ | (_) |   /\ V /  |___| | (_| (_) | |__| |__| _| (__  | |  | | (_) | .` |
      \___/_/ \_\_| |___\___|\___/|_|_\ |_|          \___\___/|____|____|___\___| |_| |___\___/|_|\_|

     */

    var CategoryCollection = function(widget, model, selector) {
        Component.call(this, widget, model, selector);
    };

    extend(CategoryCollection, Component);
    extend(CategoryCollection, Droppable);

    CategoryCollection.prototype.render = function() {
        var dd = $("<div></div>", {
            class: "dd template-engine-nestable"
        }).append(
            $("<ol></ol>", {
                class: "template-engine-list"
            })
        );
        return $("<div></div>", {
            class: "template-engine-category-collection"
        }).append(dd);
    };

    CategoryCollection.prototype.append = function(element) {
        if (this === element) {
            return false;
        }
        if (Node.prototype.append.call(this, element)) {
            this.selector().children(".dd").children(".template-engine-list").append(element.selector());
        }
        return true;
    };

	CategoryCollection.prototype.afterDrop = function(category) {
		// remove draggable option (cuz for sorting we use nestable)
		category.selector().draggable("disable");
		// remove all jquery-ui classes (not needed anymore)
		category.selector()
			.removeClass("ui-draggable")
			.removeClass("ui-draggable-handle")
			.removeClass("ui-draggable-disabled");
		// return self instance
		return this;
	};

    CategoryCollection.prototype.drop = function() {
        // create collection's clojure
        var that = this;
        // activate droppable and nestable events
        this.selector().droppable({
            accept: function(helper) {
                // get helper's instance
                var me = helper.data("instance");
                // exit if try to check non-template element
                if (!(me instanceof Template)) {
                    return false;
                }
                // check template for category type
                return me.key() === "category";
            },
            drop: function(e, ui) {
                // fetch helper's instance from selector
                var template = ui.helper.data("instance");
                // exit if we don't drop template
                if (!(template instanceof Template)) {
                    return false;
                }
                // create new category element (we can't create another one type)
                var category = new Category(
                    that, clone(template.model())
                );
				// apply after drop event (for external elements)
				that.afterDrop(category);
				// append just created category to collection
				that.append(category);
            }
        }).find(".dd").nestable({
            listClass: "template-engine-list",
			itemClass: "template-engine-category",
			handleClass: "template-engine-handle",
			rootClass: "template-engine-nestable",
			expandBtnHTML: "",
			collapseBtnHTML: "",
			maxDepth: 500,
            finish: function(item, parent) {
				// get item and parent instances (to reappend child)
                var itemInstance = $(item).data("instance");
                var parentInstance = $(parent).data("instance") || that;
				// remove from item's parent and append to another
                Node.prototype.remove.call(itemInstance.parent(), itemInstance);
                Node.prototype.append.call(parentInstance, itemInstance);
            }
        });
    };

    /*
        _  _   _ _____ ___           ___ ___  __  __ ___ _    ___ _____ ___
       /_\| | | |_   _/ _ \   ___   / __/ _ \|  \/  | _ \ |  | __|_   _| __|
      / _ \ |_| | | || (_) | |___| | (_| (_) | |\/| |  _/ |__| _|  | | | _|
     /_/ \_\___/  |_| \___/         \___\___/|_|  |_|_| |____|___| |_| |___|

     */

    var AutoComplete = function(widget, selector) {
        Component.call(this, widget, null, selector);
    };

    extend(AutoComplete, Component);

    AutoComplete.prototype.render = function() {
        return $("<div></div>", {
            class: "template-engine-search"
        });
    };

    /*
     __      _____ ___   ___ ___ _____
     \ \    / /_ _|   \ / __| __|_   _|
      \ \/\/ / | || |) | (_ | _|  | |
       \_/\_/ |___|___/ \___|___| |_|

     */

    var Widget = function(widgetSelector, templateCollection) {
		// invoke component constructor
        Component.call(this, null, null, widgetSelector);
		// initialize collections
        this._templateCollection = templateCollection;
        this._categoryCollection = new CategoryCollection(this);
		// append collections to widget
		this.append(this._categoryCollection);
        this.append(this._templateCollection);
    };

    extend(Widget, Component);

	Widget.prototype.getTemplateCollection = function() {
		return this._templateCollection;
	};

	Widget.prototype.getCategoryCollection = function() {
		return this._categoryCollection;
	};

    /*
     __      _____ ___   ___ ___ _____          ___ ___  _    _    ___ ___ _____ ___ ___  _  _
     \ \    / /_ _|   \ / __| __|_   _|  ___   / __/ _ \| |  | |  | __/ __|_   _|_ _/ _ \| \| |
      \ \/\/ / | || |) | (_ | _|  | |   |___| | (_| (_) | |__| |__| _| (__  | |  | | (_) | .` |
       \_/\_/ |___|___/ \___|___| |_|          \___\___/|____|____|___\___| |_| |___\___/|_|\_|

     */

    var WidgetCollection = {
        register: function(selector) {
			if (this._widgetList.length > 1) {
				assert("You can't register more then one TemplateEngine widgets");
			}
            this._widgetList.push(
                new Widget(selector, this._templateCollection)
            );
        },
		widget: function() {
			return this._widgetList[0];
		},
		restart: function() {
			for (var i in this._widgetList) {
				if (!this._widgetList[i].getCategoryCollection()) {
					continue;
				}
				var children = this._widgetList[i].getCategoryCollection()
					.children();
				for (var i in children) {
					if (children[i] == undefined) {
						continue;
					}
					children[i].remove();
				}
			}
		},
        _templateCollection: new TemplateCollection(),
        _widgetList: []
    };

    /*
      __  __   _   _  _____
     |  \/  | /_\ | |/ / __|
     | |\/| |/ _ \| ' <| _|
     |_|  |_/_/ \_\_|\_\___|

     */

    $(document).ready(function() {
        $(".template-engine-widget").each(function(i, w) {
            WidgetCollection.register($(w));
        });
    });

    var collection = WidgetCollection._templateCollection;

	// register basic templates
    collection.append(new Template(collection, "category",      "Категория"),         -2);
	collection.append(new Template(collection, "static",        "Категория"),         -1);
    collection.append(new Template(collection, "text",          "Текстовое поле",      0));
    collection.append(new Template(collection, "text-area",     "Текстовая область",   1));
    collection.append(new Template(collection, "number",        "Числовое поле",       5));
    collection.append(new Template(collection, "drop-down",     "Выпадающий список",   2));
    collection.append(new Template(collection, "auto-complete", "Автодополнение",      3));
    collection.append(new Template(collection, "table",         "Таблица",             4));
    collection.append(new Template(collection, "dictionary",    "Двухколонный список", 7));
    collection.append(new Template(collection, "date",          "Дата",                6));
	// register extra templates
    collection.append(new Template(collection, "comma",     ","));
    collection.append(new Template(collection, "dot",       "."));
    collection.append(new Template(collection, "dash",      "-"));
    collection.append(new Template(collection, "colon",     ":"));
    collection.append(new Template(collection, "semicolon", ";"));

	// highlight static and dynamic categories in template view
    collection.find("category").selector() .css("background-color", "lightcoral");
	collection.find("static").selector().addClass("template-engine-category-static");

	/*
	    _   ___ ___
	   /_\ | _ \_ _|
	  / _ \|  _/| |
	 /_/ \_\_| |___|

	 */

	var _getTemplateByID = function(id) {
		// get template collection's children
		var children = collection.children();
		// look though all templates in collection
		// and find template with necessary identifier
		for (var i in children) {
			if (children[i].id() === parseInt(id)) {
				return children[i];
			}
		}
		// throw an exception, if we can't find
		// template by identifier
		assert("TemplateEngine/getTemplateByID(): \"Unresolved template id (" + id + ")\"");
	};

	var _registerCategory = function(collection, model) {
		// set default model name (bug on server)
		model["name"] = model["name"] || "Категория";
		// create new category without parent and selector
		var c = new Category(null, model, null);
		// look though elements in category's model and
		// append it to just created category
		for (var i in model["elements"]) {
			c.append(new Item(c, model["elements"][i], null,
				_getTemplateByID(model["elements"][i]["type"])
			))
		}
		// append category to category collection
		collection.afterDrop(c).append(c);
		// return self
		return TemplateEngine;
	};

	TemplateEngine.registerTemplate = function(model) {
		// restart current collection
		TemplateEngine.restart();
		// initialize collection with template model
		WidgetCollection.widget().getCategoryCollection()
			.model(model);
		// attach categories to collection
		for (var i in model.categories) {
			_registerCategory(WidgetCollection.widget().getCategoryCollection(),
				model.categories[i]
			);
		}
		// return self
		return TemplateEngine;
	};

	var actionMapEdit = [];
	var actionMapAppend = [];

	var _onAction = function(key, action, map) {
		var actions;
		if (key != null) {
			actions = map[collection.find(key).key()];
			if (!actions) {
				map[collection.find(key).key()] = [
					action
				];
			} else {
				actions.push(action);
			}
		} else {
			for (var i in collection.children()) {
				actions = map[collection.children()[i].key()];
				if (!actions) {
					map[collection.children()[i].key()] = [
						action
					];
				} else {
					actions.push(action);
				}
			}
		}
		return TemplateEngine;
	};

	TemplateEngine.onEdit = function(key, action) {
		return _onAction(key, action, actionMapEdit);
	};

	TemplateEngine.onAppend = function(key, action) {
		return _onAction(key, action, actionMapAppend);
	};

	var _trigger = function(item, map) {
		var template;
		if (!(item instanceof Item)) {
			template = collection.find("category");
		} else {
			template = item.template();
		}
		var actions = map[template.key()];
		if (!actions) {
			return false;
		}
		for (var i in actions) {
			actions[i].call(item);
		}
	};

	TemplateEngine._triggerEdit = function(item) {
		if (!item.length()) {
			this._triggerAppend(item);
		} else {
			_trigger(item, actionMapEdit);
		}
	};

	TemplateEngine._triggerAppend = function(item) {
		_trigger(item, actionMapAppend);
	};

	TemplateEngine.isCategory = function(item) {
		return item instanceof Category;
	};

	TemplateEngine.isItem = function(item) {
		return !(item instanceof Category) && item.template().key() !== "static";
	};

	TemplateEngine.restart = function() {
		// reset widget collection (it will
		// remove all categories with elements)
		WidgetCollection.restart();
		// reset action hooks
		actionMapEdit = [];
		actionMapAppend = [];
	};

})(TemplateEngine);