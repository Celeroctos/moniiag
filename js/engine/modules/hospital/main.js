misEngine.addToQueue(function() {
	return {
		config : {
			name : 'hospital'
		},
		
		bindHandlers : function() {
			$('#hospitalizationNavbar a').click(function (e) {
				e.preventDefault()
				$(this).tab('show')
			});
		},
		
		displayDatetimepickers : function() {
			var datetimepicker = misEngine.create('component.datetimepicker');
			datetimepicker
				.setConfig({
					widget : { // Property "widget" uses if component includes other component, in external libs
						language: 'ru',
						format: 'yyyy-mm-dd',
						weekStart: 1,
						todayBtn:  1,
						autoclose: 1,
						todayHighlight: 1,
						startView: 2,
						minView: 2,
						forceParse: 0,
						selector : '#sideCalendar'
					}
				})
				.render()
				.on();
		},
		
		displayGrids : function() {
			this.displayQueueGrid();
			this.displayComissionGrid();
			this.displayHospitalizationGrid();
			this.displayHistoryGrid();
		},
		
		displayQueueGrid : function() {
			var queueGrid = misEngine.create('component.grid');
			var queueGridRequestData = { 
				returnAsJson : true,
				id : 'queueGrid'
			};
			
			queueGrid
				.setConfig({
					renderConfig : {
						mode : 'ajax',
						ajaxConf : {
							url : '/hospital/components/grid',
							data : queueGridRequestData,
							dataType : 'json',
							success : function(data, status, jqXHR) {
								if(data.success) {
									$('#queue')
										.css({
											'textAlign' : 'left'
										})
										.html(data.data);
								} 
							},
							error: function(jqXHR, status, errorThrown) {
								misEngine.t(jqXHR, status, errorThrown);
							}
						}
					}
				})
				.render()
				.on();
		},
		
		displayComissionGrid : function() {
			var comissionGrid = misEngine.create('component.grid');
			var comissionGridRequestData = { 
				returnAsJson : true,
				id : 'comissionGrid'
			};
			
			comissionGrid
				.setConfig({
					renderConfig : {
						mode : 'ajax',
						ajaxConf : {
							url : '/hospital/components/grid',
							data : comissionGridRequestData,
							dataType : 'json',
							success : function(data, status, jqXHR) {
								if(data.success) {
									$('#comission')
										.css({
											'textAlign' : 'left'
										})
										.html(data.data);
								} 
							},
							error: function(jqXHR, status, errorThrown) {
								misEngine.t(jqXHR, status, errorThrown);
							}
						}
					}
				})
				.render()
				.on();
		},
		
		displayHospitalizationGrid : function() {
			var hospitalizationGrid = misEngine.create('component.grid');
			var hospitalizationGridRequestData = { 
				returnAsJson : true,
				id : 'hospitalizationGrid'
			};
			
			hospitalizationGrid
				.setConfig({
					renderConfig : {
						mode : 'ajax',
						ajaxConf : {
							url : '/hospital/components/grid',
							data : hospitalizationGridRequestData,
							dataType : 'json',
							success : function(data, status, jqXHR) {
								if(data.success) {
									$('#hospitalization')
										.css({
											'textAlign' : 'left'
										})
										.html(data.data);
								} 
							},
							error: function(jqXHR, status, errorThrown) {
								misEngine.t(jqXHR, status, errorThrown);
							}
						}
					}
				})
				.render()
				.on();
		},
		
		displayHistoryGrid : function() {
			var historyGrid = misEngine.create('component.grid');
			var historyGridRequestData = { 
				returnAsJson : true,
				id : 'historyGrid'
			};
			
			historyGrid
				.setConfig({
					renderConfig : {
						mode : 'ajax',
						ajaxConf : {
							url : '/hospital/components/grid',
							data : historyGridRequestData,
							dataType : 'json',
							success : function(data, status, jqXHR) {
								if(data.success) {
									$('#history')
										.css({
											'textAlign' : 'left'
										})
										.html(data.data);
								} 
							},
							error: function(jqXHR, status, errorThrown) {
								misEngine.t(jqXHR, status, errorThrown);
							}
						}
					}
				})
				.render()
				.on();
		},
		
		init : function() {
			this.displayDatetimepickers();
			this.displayGrids();
			this.bindHandlers();
			return this;
		}
	};
});