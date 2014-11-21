/**
 * @type {TemplateEngine} - Base template engine API object
 */
var TemplateEngine = TemplateEngine || {
        getWidgets     : function() {},
        getFactory     : function() {},
        createCategory : function() {},
        createElement  : function() {}
    };

(function(TemplateEngine) {

    "use strict";

     /**
      * @param [message] {String} - Error message to throw
      * @param [condition] {...Boolean} - Assertion expression
     */
    var assert = function(message, condition) {
        if (arguments.length <= 0) {
            throw new Error("Assertion Failed : \"?\"");
        } else {
            for (var i in arguments) {
                if (!arguments.hasOwnProperty(i)) {
                    continue;
                }
                if (arguments[i] !== false && i != arguments.length - 1) {
                    throw new Error("Assertion Failed : " + message);
                }
            }
        }
    };

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
            if (!this._childrenNode.hasOwnProperty(i)) {
                continue;
            }
            if (node === this._childrenNode[i]) {
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
            if (!this._childrenNode.hasOwnProperty(i)) {
                continue;
            }
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
     * @param [selector] {jQuery} - Some selector
     * @constructor - Selectable is an object that can
     *      store jQuery's selector
     */
    var Selectable = function(selector) {
        Selectable.prototype.selector.call(this, selector ||  assert(true, "Selector can't be null or undefined"));
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
        } else {
            return this._jqSelector;
        }
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
     * @param [parent] {Element|Node|null|undefined} - Element's parent
     * @param [selector] {jQuery|undefined} - jQuery's selector, need only for
     *      classes which extends Element
     * @param [template] {{}} - If element is string, then element
     *      is basic abstract template, else it's element's child
     * @constructor - Basic TemplateNode's element, which extends
     *      Node and implements all manipulations with elements
     */
    var Element = function(parent, selector, template) {
        Selectable.call(this, selector || this.createItem(
            template ? template.title : null
        ));
        Node.call(this, parent || null);
        Flaggable.call(this);
        this._elementTemplate = template;
        this._tagBefore = null;
        this._tagAfter = null;
        this._defaultValue = null;
    };

    $.extend(Element.prototype, Node.prototype);
    $.extend(Element.prototype, Selectable.prototype);
    $.extend(Element.prototype, Flaggable.prototype);

    /**
     * @param [title] {String} - Item's title
     * @returns {jQuery}
     */
    Element.prototype.createItem = function(title) {
        var template = this.template();
        var item = $("<div></div>", {
            html: title ? "<div>" + title + "</div>" : "",
            style: "cursor: default;"
        });
        if (!template) {
            return item;
        }
        this.selector(item);
        return item;
    };

    /**
     * This function will clone current element with
     * it's selector and append just created element to
     * previous parent
     *
     * @param parent {Node} - New container, where node will
     *      be places
     * @param [selector] {jQuery} - Element's selector, might be
     *      undefined or null (will be created automatically)
     * @param [template] {Element} - Template element, if
     *      undefined, then we will fetch template current instance
     * @returns {HTMLElement}
     */
    Element.prototype.clone = function(parent, selector, template) {
        var e = new Element(parent, selector, template ||
            this.template()
        );
        if (parent && false) {
            parent.append(e);
        }
        return e;
    };

    /**
     * @param value {String} - String value, which will
     *      be rendered before element's body
     * @returns {String} - Also can work as getter to
     *      get just set value
     */
    Element.prototype.before = function(value) {
        if (value !== undefined) {
            this._tagBefore = value;
        } else {
            return this._tagBefore;
        }
    };

    /**
     * @param value {String} - String value, which will
     *      be rendered after element's body
     * @returns {String} - Also can work as getter to
     *      get just set value
     */
    Element.prototype.after = function(value) {
        if (value !== undefined) {
            this._tagAfter = value;
        } else {
            return this._tagAfter;
        }
    };

    /**
     * @param value {String} - Every element can store
     *      it's default value
     * @returns {String} - Also can work as getter to
     *      get just set default value
     */
    Element.prototype.value = function(value) {
        if (value !== undefined) {
            this._defaultValue = value;
        } else {
            return this._defaultValue;
        }
    };

    /**
     * @param [value] {Element|String|undefined} - Element or
     *      it's name (for abstract type)
     * @returns {Element|String} - For getter template element
     */
    Element.prototype.template = function(value) {
        if (value !== undefined) {
            this._elementTemplate = value;
        } else {
            return this._elementTemplate;
        }
    };

    /**
     * Locked method, cuz elements can't store something
     *
     * @param element {Element} - Element to append
     *      with it's selector
     */
    Element.prototype.append = function(element) {
        if (this === element) {
            return false;
        }
        if (Node.prototype.append.call(this, element)) {
            Selectable.prototype.append.call(this, element);
        }
        Factory.invoke("append", this, element);
        return true;
    };

    /**
     * Locked method, cuz elements can't store something
     *
     * @param [element] {Element} - Element to remove
     *      with it's selector. If element is undefined then
     *      element will be removed from it's parent
     */
    Element.prototype.remove = function(element) {
        if (this === element) {
            return false;
        }
        if (Node.prototype.remove.call(this, element)) {
            this.selector().detach(element);
        }
        Factory.invoke("detach", this, element);
        return true;
    };

    /**
     * @constructor - Class, which provides generation
     *      of templates for every element
     */
    var Factory = function() {
        this._templateList = [];
    };

    /**
     * @param instance {Element}
     * @param method {String}
     * @param [argument] {*}
     */
    Factory.invoke = function(method, instance, argument) {
        if (!instance || !instance.template || !instance.template()) {
            return false;
        }
        if (instance.template()[method]) {
            return instance.template()[method].call(
                instance, argument
            );
        }
        return null;
    };

    /**
     * @param parent {Element|Widget|Node} - Where to declare element
     * @param template {String} - Template's name
     * @returns {Element}
     */
    Factory.prototype.create = function(parent, template) {
        var templateElement;
        if (!(templateElement = this._templateList[template])) {
            throw new Error("Factory/create() : \"Unresolved template name (" + template + ")\"");
        }
        if (templateElement.render) {
            var selector = templateElement.render();
        }
        var itemElement = new Element(
            parent instanceof jQuery ? null : parent,
            selector || null,
            templateElement
        );
        if ($.isArray(templateElement.class)) {
            for (var i in templateElement.class) {
                itemElement.selector().addClass(
                    templateElement.class[i]
                );
            }
        } else {
            itemElement.selector().addClass(templateElement.class);
        }
        Factory.invoke("construct", itemElement);
        if (parent) {
            if (parent instanceof jQuery) {
                parent.append(itemElement.selector());
            } else {
                parent.append(itemElement);
            }
        }
        return itemElement;
    };

    /**
     * Register template in factory
     *
     * @param index {String} - Template's index
     * @param template {{}} - Template implementation
     */
    Factory.prototype.register = function(index, template) {
        assert(this._templateList[index] !== undefined,
            "Template \"" + index + "\" is already declared in current factory");
        var instance = this;
        var superTemplate = {
            factory: function() {
                return instance;
            }
        };
        for (var i in superTemplate) {
            if (!superTemplate.hasOwnProperty(i)) {
                continue;
            }
            template[i] = superTemplate[i];
        }
        this._templateList[index] = template;
    };

    /**
     * Find template in factory
     *
     * @param index {String} - Template's index
     */
    Factory.prototype.find = function(index) {
        assert(this._templateList[index] === undefined,
            "Template \"" + index + "\" wasn't declared in current factory");
        return this._templateList[index];
    };

    var factory = new Factory();

    /**
     * Factory Md Structure: {
     *  - model: {
     *   - class - Array with element classes
     *  }
     *  - delegate: {
     *   - append - If element has been appended
     *   - remove - If element has been removed
     *   - detach - If element has been detached
     *  }
     * }
     */

    var resultContainer = null;
    var treeContainer = null;
    var templateContainer = null;
    var lastCategory = null;
    var searchContainer = null;

    /**
     * @param category {Element}
     */
    var makeCategoryActive = function(category) {
        if (category === lastCategory) {
            return false;
        }
        if (lastCategory != null) {
            lastCategory.selector().removeClass(
                "template-engine-selected"
            );
        }
        category.selector()
            .addClass("template-engine-selected");
        lastCategory = category;
        return true;
    };

    var createCategory = function() {
        var c = factory.create(treeContainer, "category")
            .selector().detach().appendTo(
            treeContainer.selector().children(".dd-list")
        ).data("instance");
        if (!lastCategory) {
            makeCategoryActive(c);
        }
    };

    factory.register("widget", {
        class: [
            "template-engine-widget"
        ],
        construct: function() {
            factory.create(
                this, "widget-tree"
            );
            factory.create(
                this, "container-result"
            );
            factory.create(
                this, "container-template"
            );
            factory.create(
                this, "widget-search"
            );
        }
    });

    factory.register("container-tree", {
        class: [
            "template-engine-tree",
            "template-engine-container"
        ],
        construct: function() {
            treeContainer = this;
            var loadElements = function(item) {
                var category = $(item).data(
                    "instance"
                );
                if (!makeCategoryActive(category)) {
                    return false;
                }
                makeCategoryActive(category);
                resultContainer.selector().children().each(
                    function(i, item) {
                        $(item).detach();
                    }
                );
                category.children().forEach(function(element) {
                    if (element) {
                        element.selector().appendTo(
                            resultContainer.selector()
                        );
                    }
                });
            };
            this.selector().nestable({
                finish: loadElements
            }).disableSelection();
        },
        render: function() {
            return $("<div></div>", {
                class: "dd"
            }).append(
                $("<ol></ol>", {
                    class: "dd-list"
                })
            );
        }
    });

    factory.register("widget-tree", {
        class: [
            "template-engine-tree-widget",
            "template-engine-container"
        ],
        construct: function() {
            var bar = $("<div></div>", {
                style: "text-align: center;"
            }).appendTo(this.selector());
            factory.create(this, "container-tree");
            $("<button>Добавить</button>").appendTo(bar).click(function() {
                createCategory();
            });
        }
    });

    factory.register("container-result", {
        class: [
            "template-engine-container",
            "template-engine-droppable",
            "template-engine-result"
        ],
        construct: function() {
            resultContainer = this;
            var appendElement = function(ui) {
                var element = ui.helper.data("instance");
                var parent = $(this).data("instance");
                if (ui.helper.data("sign")) {
                    return false;
                } else {
                    ui.helper.data("sign", true);
                }
                element = element.clone(
                    parent, ui.helper
                );
                ui.helper.data(
                    "instance", element
                );
                if (!lastCategory) {
                    var c = factory.create(treeContainer, "category")
                        .selector().detach().appendTo(
                        treeContainer.selector().children(".dd-list")
                    ).data("instance");
                    makeCategoryActive(c);
                }
                if (!lastCategory.contains(element)) {
                    Node.prototype.append.call(
                        lastCategory, element
                    );
                } else {
                    return false;
                }
                ui.helper.data("instance", element);
                $("<button></button>", {
                    html: "x"
                }).click(function() {
                    var parent = $(this).parent(
                        ".template-engine-draggable"
                    );
                    if (parent.hasClass("template-engine-category")) {
                        parent.data("instance")
                            .truncate();
                        parent.parent(".template-engine-container")
                            .detach();
                    } else {
                        parent.data("instance")
                            .remove();
                    }
                    console.log(lastCategory);
                }).appendTo(ui.helper);
                ui.helper
                    .mouseenter(function() {
                        $(this).children("button").css(
                            "visibility", "visible"
                        );
                    })
                    .mouseleave(function() {
                        $(this).children("button").css(
                            "visibility", "hidden"
                        );
                    });
                element.selector(ui.helper);
            };
            this.selector().sortable()
                .droppable({
                    drop: function(e, ui) {
                        appendElement(ui);
                    }
                });
        }
    });

    factory.register("container-template", {
        class: [
            "template-engine-container",
            "template-engine-template"
        ],
        construct: function() {
            factory.create(this, "text");
            factory.create(this, "text-area");
            factory.create(this, "number");
            factory.create(this, "drop-down");
            factory.create(this, "auto-complete");
            factory.create(this, "table");
            factory.create(this, "dictionary");
            factory.create(this, "date");
            factory.create(this, "comma");
            factory.create(this, "dot");
            factory.create(this, "dash");
            factory.create(this, "colon");
            factory.create(this, "semicolon");
        },
        append: function(e) {
            templateContainer = this;
            e.selector().draggable({
                revert: function(e) {
                    return !e || !e.hasClass("template-engine-droppable") ||
                        e === this;
                },
                helper: function(e) {
                    return $(this).clone(false)
                        .data("instance", templateContainer);
                },
                start: function() {
                    $(this).css("visibility", "hidden");
                },
                stop: function() {
                    $(this).css("visibility", "visible");
                },
                cursor: "default",
                connectToSortable: ".template-engine-droppable"
            });
        }
    });

    factory.register("widget-search", {
        class: [
            "template-engine-container",
            "template-engine-search"
        ],
        construct: function() {
            searchContainer = factory.create(this,
                "container-search"
            );
        },
        render: function() {
            return $("<div></div>").append(
                $("<input type=\"text\"/>", {
                    class: "template-engine-search-category-name"
                }).on("keyup", function(e) {
                    console.log(e.keyCode);
                })
            );
        }
    });

    factory.register("container-search", {
        class: [
            "template-engine-container"
        ],
        construct: function() {
            searchContainer = this;
        },
        render: function() {
            return $("<div></div>", {
            });
        }
    });

    factory.register("category", {
        class: [
            "template-engine-category"
        ],
        construct: function() {
        },
        render: function() {
            return $("<li></li>", {
                class: "dd-item",
                style: "display: block"
            }).append(
                $("<div></div>", {
                    class: "dd-handle",
                    html: "Категория"
                })
            );
        },
        title: "Категория"
    });

    factory.register("text", {
        class: [
            "template-engine-element",
            "template-engine-draggable"
        ],
        title: "Текстовое поле"
    });

    factory.register("text-area", {
        class: [
            "template-engine-element",
            "template-engine-draggable"
        ],
        title: "Текстовая область"
    });

    factory.register("number", {
        class: [
            "template-engine-element",
            "template-engine-draggable"
        ],
        title: "Числовое поле"
    });

    factory.register("drop-down", {
        class: [
            "template-engine-element",
            "template-engine-draggable"
        ],
        title: "Выпадающий список"
    });

    factory.register("auto-complete", {
        class: [
            "template-engine-element",
            "template-engine-draggable"
        ],
        title: "Автодополнение"
    });

    factory.register("table", {
        class: [
            "template-engine-element",
            "template-engine-draggable"
        ],
        title: "Таблица"
    });

    factory.register("dictionary", {
        class: [
            "template-engine-element",
            "template-engine-draggable"
        ],
        title: "Двухколонный список"
    });

    factory.register("date", {
        class: [
            "template-engine-element",
            "template-engine-draggable"
        ],
        title: "Дата"
    });

    factory.register("comma", {
        class: [
            "template-engine-element",
            "template-engine-mark",
            "template-engine-draggable"
        ],
        title: ","
    });

    factory.register("dot", {
        class: [
            "template-engine-element",
            "template-engine-mark",
            "template-engine-draggable"
        ],
        title: "."
    });

    factory.register("dash", {
        class: [
            "template-engine-element",
            "template-engine-mark",
            "template-engine-draggable"
        ],
        title: "-"
    });

    factory.register("colon", {
        class: [
            "template-engine-element",
            "template-engine-mark",
            "template-engine-draggable"
        ],
        title: ":"
    });

    factory.register("semicolon", {
        class: [
            "template-engine-element",
            "template-engine-mark",
            "template-engine-draggable"
        ],
        title: ";"
    });

    $(document).ready(function() {
        $(".template-engine-widget").each(function(i, w) {
            factory.create($(w), "widget");
        });
    });

    TemplateEngine.getWidgets = function() {
        return null;
    };

    TemplateEngine.getFactory = function() {
        return factory;
    };

    TemplateEngine.createCategory = function(categoryInfo) {
        createCategory();
        console.log(categoryInfo);
    };

    TemplateEngine.createElement = function() {

    };

})(TemplateEngine);