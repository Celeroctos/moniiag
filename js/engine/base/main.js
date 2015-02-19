misEngine = (function() {
	var misEngine = {
		config : {},
		modules : {
			'component' : {
				'grid' : { },
				'module' : { }, // Module is component, really
				'datetimepicker' : { },
				'ajaxLoader' : {},
                'model' : {},
                'modal' : {},
                'tabmark' : {}
			},
			'modules'  : {
				'hospital' : {
					// subcomponents list...?
				}
			}
		},
		loadedScripts : {}, // Loaded Scripts per base req
		queue : [], // This is queue for loading after engine was loaded
		numLoadedModules : 0,
		getConfig : function(key) {
			if(this.config[key]) {
				return this.config[key];
			} else {
				return null;
			}
		},
		loadScripts : function() {
			if(this.config.engineUrl) { 
				var counter = 0;
				for(var i in this.modules) {
					if(i != 'modules') {
						($.proxy(function(i) {
							this.loadModule(i);
						}, this))(i);
						counter++;
					}
				}
				this.modules.length = counter;
			} else {
				console.log('Not found Engine URL in config');
			}
		},
		
		loadModule : function(i) {
			$.getScript(this.config.engineUrl + '/base/' + i + '.js')
				.done(
					$.proxy(function(data) {
						this.t('Loaded ' + this.config.engineUrl + '/base/' + i + '.js');
						if(!this.loadedScripts[i]) {
							this.loadedScripts[i] = {};
						}
						var baseObj = function() { };
						baseObj.prototype = eval(data);
						this.loadedScripts[i].baseObj = baseObj;
						// Load submodules...
						var counter = 0;
						for(var k in this.modules[i]) {
							counter++;
						}
						this.modules[i].length = counter;
						this.loadSubmodules(i); 
					}, this)
				)
				.fail(
					function( jqxhr, settings, exception ) {
						console.log(exception);
					}
				);
		},
		
		loadSubmodules : function(i) {
			var numLoadedSubmodules = 0;
			var numSubmodules = 0;
			
			for(var j in this.modules[i]) {
				if(j == 'length') {
					continue; 
				}
				numSubmodules++;
			}

			for(var j in this.modules[i]) {
				if(j == 'length') {
					continue; 
				}
				($.proxy(function(j) {
					$.getScript(this.config.engineUrl + '/' + i +  's/' + j + '.js', 
						$.proxy(function(data) {
							this.t('Loaded ' + this.config.engineUrl + '/' + i +  's/' + j + '.js');
							if(!this.loadedScripts[i]) {
								this.loadedScripts[i] = {};
							}
							
							var inData = eval(data);
							var childClass = function() { 
								for(var prop in inData) {
									this[prop] = inData[prop];
								}
							};
							childClass.prototype = Object.create(this.loadedScripts[i].baseObj.prototype);
							this.loadedScripts[i][j] = childClass;

							numLoadedSubmodules++;
							($.proxy(function(numSubmodules) {
								if(numLoadedSubmodules == numSubmodules) {
									numLoadedSubmodules = 0;	
									this.numLoadedModules++;
									if(this.numLoadedModules == this.modules.length) { // All modules was loaded.. 
										this.t('All modules was loaded. Beginning of enabling modules...');
										this.parseQueue();
									}
								}	
							}, this))(numSubmodules);								

						}, this)
					);
				}, this))(j);
			} 
		},

		load : function(config) {
			this.config = config ? config : {};
			this.loadScripts();
		},
		
		create : function(objPath, config) {
			if(!objPath) {
				this.t('Incorrect object create link');
				return -1;
			} 
			var parts = objPath.split('.');
			var founded = this.searchObjByPath(parts);
			if(founded != -1) {
                var obj = new founded();
                obj.setConfig(config);
				return obj.init(config);
			} else {
				this.t('Component "' + objPath + '" not created: error by searching component path?..');
				return -1;
			}
		},
		
		searchObjByPath : function(searchArr, obj) {
			if(typeof obj == 'undefined') {
				obj = this.loadedScripts;
			}

			var partOfPath = searchArr.shift();
			var currentNode = obj[partOfPath];

			if(currentNode) {
				if(searchArr.length > 0) {
					component = this.searchObjByPath(searchArr, currentNode);
				} else {
					return currentNode;
				}
			}

			return !component ? -1 : component;  // Error, if some goes wrong...
		},
		
		addToQueue : function(something) {
			this.queue.push(something);
		},
		
		parseQueue : function() {
			// System component "module" must be loaded...
			if(!this.modules.component.module) {
				this.t('Not found system component "module". Creating of module has broken.');
				return -1;
			} 

			this.t('Found system component "module"....');

			var el;
			while(el = this.queue.shift()) {
				var parentClassProps = new this.loadedScripts.component.module();
				var parentClass = function() { };
				parentClass.prototype = parentClassProps;
				this.modules.modules[el().config.name] = function() {
					var props = el();
					for(var prop in props) {
						this[prop] = props[prop]; // In moment, when object creates, this == current object
					}
				};
				this.modules.modules[el().config.name].prototype = Object.create(parentClass.prototype); // Loading modules...
				(new this.modules.modules[el().config.name]).init();
			}
		},
		
		t : function(msg) {
			if(misEngine.config.debug) {
				console.log(msg);
			}
		},

        extends : function(parentPath) {
            var parts = parentPath.split('.');
            var founded = this.searchObjByPath(parts);
        }
	}
	return misEngine;
})();

misEngine.load({
	engineUrl : '/js/engine',
	debug : true
});