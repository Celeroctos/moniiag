misEngine = (function() {
	var misEngine = {
		config : {},
		modules : [{
            'name' : 'component',
            'script' : null,
            'modules' : [{
                'name' : 'grid',
                'script' : null
            },{
                'name' : 'datetimepicker',
                'script' : null
            }, {
                'name' : 'ajaxloader',
                'script' : null
            }, {
                'name' : 'model',
                'script' : null
            }, {
                'name' : 'modal',
                'script' : null
            }, {
                'name' : 'tabmark',
                'script' : null
            }, {
                 'name' : 'module',
                 'script' : null,
                 'modules' : [{
                     'name' : 'hospital',
                     'script' : null
                 }]
             }]
        }],
        inherits : [], // This uses by objects creating

		getConfig : function(key) {
			if(this.config[key]) {
				return this.config[key];
			} else {
				return null;
			}
		},

        load : function(config) {
            this.config = config ? config : {};
            this.loadScripts();
        },

		loadScripts : function(scriptsList, parentPath) {
			if(this.config.engineUrl) { 
				if(!scriptsList) {
                    scriptsList = this.modules;
                }
                if(!parentPath) {
                    var scriptUrlString = this.config.engineUrl;
                } else {
                    var scriptUrlString = parentPath;
                }

                for(var i = 0; i < scriptsList.length; i++) {
                    var script = $('<script/>').attr('src',  scriptUrlString +  '/' + scriptsList[i].name + '/files/main.js').appendTo('head');
                }

			} else {
				console.log('Not found Engine URL in config');
			}
		},

        t : function(msg) {
            if(misEngine.config.debug) {
                console.log(msg);
            }
        },

        class : function(path, func) {
            var pathParts = path.split('.');
            var currentNode = this.modules;
            var scriptUrlString = this.config.engineUrl;
            for(var i = 0; i < pathParts.length; i++) {
                for(var j = 0; j < currentNode.length; j++) {
                    if(currentNode[j].name == pathParts[i]) {
                        if(i + 1 < pathParts.length) { // Next tree node...
                            if (!currentNode[j].modules) {
                                this.t('Not found modules list in node "' + currentNode[j].name + '"');
                                return -1;
                            }
                            currentNode = currentNode[j].modules;
                            break;
                        } else {
                            currentNode = currentNode[j];
                        }
                    }
                }

                scriptUrlString += '/' + pathParts[i] + '/';
                if(i + 1 < pathParts.length) {
                    scriptUrlString += 'modules';
                }
            }

            currentNode.script = func;
            this.t('Loaded ' + scriptUrlString + 'files/main.js');

            // Load submodules...
            if(currentNode.modules) {
                this.loadScripts(currentNode.modules, scriptUrlString + 'modules')
            }
        },

        create : function(objPath, config) {
            if(!objPath) {
                this.t('Incorrect object create link');
                return -1;
            }
            var parts = objPath.split('.');
            var founded = this.searchObjByPath(parts);
            if(founded != -1) {
                var parent = this.inherits[0];
                for(var i = 1; i < this.inherits.length; i++) {
                    var props = parent();
                    parent = this.extend(this.inherits[i], parent);
                }
                this.inherits = [];
                return new parent();
            } else {
                this.t('Component "' + objPath + '" not created: error by searching component path?..');
                return -1;
            }
        },

        searchObjByPath : function(searchArr, obj) {
            if(!obj) {
                obj = this.modules;
            }

            var partOfPath = searchArr.shift();
            var component = null;
            for(var i = 0; i < obj.length; i++) {
                if(obj[i].name == partOfPath) {
                    this.inherits.push(obj[i].script);
                    if(searchArr.length > 0) {
                        component = this.searchObjByPath(searchArr, obj[i].modules);
                    } else {
                        return obj[i].script;
                    }
                    break;
                }
            }

            return !component ? -1 : component;  // Error, if some goes wrong...
        },

        extend : function(Child, Parent) {
            var F = function () { };
            F.prototype = Parent.prototype;
            Child.prototype = new F();
            Child.prototype.constructor = Child;
            Child.superclass = Parent.prototype;
            return Child;
        }
    };

	return misEngine;
})();

misEngine.load({
	engineUrl : '/js/engine',
	debug : true
});