(function() { 
	return {
		getValue : function(key) {
			return this[key] ? this[key] : -1;
		},
		
		render : function() {
            console.log(this);
            if(!this.config.renderConfig) {
				misEngine.t('Not found renderConfig property. Rendering is not aviable');
				return -1;
			}
			var renderConfig = this.config.renderConfig;
			/* 
				Template is local access to component's view,
				Ajax - remote
				External - external lib must make all operation with render
			*/
			if(renderConfig.mode == 'template') {
				/*
					Template object or html-code must be declared
				*/
				if(!renderConfig.template) {
					misEngine.t('Not found template property. Local rendering is not aviable');
					return -1;
				}
			}
			if(renderConfig.mode == 'external') {				
				if(this.config.widget) {
					/*
						If declared selector for external extension, try to apply it!
					*/
					return this.getExternalRender(this.config.widget);
				} else {
					misEngine.t('Not exists external lib in module ' + this.config.name); 
					return -1;
				}
			}
			if(renderConfig.mode == 'ajax') {
				if(!renderConfig.ajaxConf) {
					misEngine.t('Not exists ajax config for render in module ' + this.config.name);
					return -1;
				} else {
					$.ajax(renderConfig.ajaxConf);
				}
			}
			
			return this;
		},
		
		on : function() {
			return this;
		},
		
		off : function() {
			return this;
		},
		
		setConfig : function(config) {
			for(var prop in config) {
				this.config[prop] = config[prop];
			}
			return this;
		},
		
		setValue : function(key, value) {
			if(typeof this[key] != 'undefined') {
				this[key] = value;
				return this[key];
			} else {
				misEngine.t('Not found property ' + key + '.');
				return -1;
			}
		},
	
		init : function() {
			
		}
	};
})();