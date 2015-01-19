(function() { 
	return {
		config : {
			name : 'component.grid',
			renderConfig : {
				mode : 'ajax',
				ajaxConf : {
					url : null,
					error : function(jqXHR, status) { },
					success : function(jqXHR, status, errorThrown) { },
					data : { }
				}
			}
		},
		
		bindHandlers : function() {
		
		},
	
		init : function() {
			return this;
		}
	};
})();