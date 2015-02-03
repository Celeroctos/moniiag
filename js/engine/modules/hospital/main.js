misEngine.addToQueue(function() {
	return {
        comissionGrid : null,
        comissionGridModal : null,
		config : {
			name : 'hospital'
		},
		
		bindHandlers : function() {
            $('#hospitalizationNavbar a').click(function (e) {
				e.preventDefault();
			});

            $(document).on('click', '#comissionGrid .changeHospitalizationDate', $.proxy(function(e) {
                this.comissionGridModal.show();
            }, this));
            return this;
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
                model : $.toJSON(this.getQueueModel().getColumns()),
				id : 'queueGrid',
                serverModel : 'QueueGrid',
                gridServerModel : 'QueueGridView'
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
			this.comissionGrid = misEngine.create('component.grid');
			var comissionGridRequestData = {
				returnAsJson : true,
                model : $.toJSON(this.getComissionModel().getColumns()),
				id : 'comissionGrid',
                serverModel : 'ComissionGrid',
                gridServerModel : 'ComissionGridView'
			};

			this.comissionGrid
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
                .bindEvents({
                    'editComissionDate' : function(e) {
                        alert('editComissionDate event triggered');
                    }
                })
                .render()
                .on();
        },
		
		displayHospitalizationGrid : function() {
			var hospitalizationGrid = misEngine.create('component.grid');
			var hospitalizationGridRequestData = { 
				returnAsJson : true,
                model : $.toJSON(this.getHospitalizationModel().getColumns()),
				id : 'hospitalizationGrid',
                serverModel : 'HospitalizationGrid',
                gridServerModel : 'HospitalizationGridView'
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
				id : 'historyGrid',
                model : $.toJSON(this.getHistoryModel().getColumns()),
                serverModel : 'HistoryGrid',
                gridServerModel : 'HistoryGridView'
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

        getQueueModel : function() {
            var model = misEngine.create('component.model').setConfig({
                columns : [
                    {
                        name: 'id',
                        type: 'raw'
                    }
                ]
            });
            return model;
        },

        getComissionModel : function() {
            var model = misEngine.create('component.model').setConfig({
                columns : [
                    {
                        name : 'direction_id',
                        type : 'raw',
                        value : '%direction_id%'
                    },
                    {
                        name : 'fio',
                        type : 'raw',
                        value : '%fio%'
                    },
                    {
                        name : 'comission_type_desc',
                        type : 'raw',
                        value : '%comission_type_desc%'
                    },
                    {
                        name : 'ward_name',
                        type : 'raw',
                        value : '{{%ward_name%|trim}}'
                    },
                    {
                        name : 'age',
                        type : 'raw',
                        value : '%age%'
                    },
                    {
                        name : 'pregnant_term',
                        type : 'raw',
                        value : '{{%pregnant_term%|int}}." недель"'
                    },
                    {
                        name : 'hospitalization_date',
                        type : 'raw',
                        value : '%hospitalization_date%'
                    }

                ]
            });
            return model;
        },

        getHospitalizationModel : function() {
            var model = misEngine.create('component.model').setConfig({
                columns : [
                    {
                        name : 'id',
                        type : 'raw'
                    }
                ]
            });
            return model;
        },

        getHistoryModel : function() {
            var model = misEngine.create('component.model').setConfig({
                columns : [
                    {
                        name : 'id',
                        type : 'raw'
                    }
                ]
            });
            return model;
        },

        renderModals : function() {
            this.getComissionModal();
        },

        getComissionModal : function() {
            this.comissionGridModal = misEngine.create('component.modal').setConfig({
                selector : '#comissionGridPopup',
                renderConfig : {
                    mode : 'internal'
                }
            }).render().on();
        },
		
		init : function() {
			this.displayDatetimepickers();
			this.displayGrids();
            this.renderModals();
			this.bindHandlers();
			return this;
		}
	};
});