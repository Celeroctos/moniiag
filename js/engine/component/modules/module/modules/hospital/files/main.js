misEngine.class('component.module.hospital', function() {
	return {
        comissionGrid : null,
        queueGrid : null,
        historyGrid : null,
        hospitalizationGrid : null,
        comissionGridModal : null,
        tabmarks : [],
		config : {
			name : 'hospital'
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

        displayTabmarks : function() {
            this.tabmarks = [
                misEngine.create('component.tabmark', {
                    selector : '#queueTabmark',
                    renderConfig : {
                        mode : 'ajax',
                        ajaxConf : {
                            url : '/hospital/hospitalization/components/tabmark',
                            error : function(jqXHR, status) { },
                            success : function(jqXHR, status, errorThrown) { },
                            data : { }
                        }
                    }
                }),
                misEngine.create('component.tabmark', {
                    selector : '#comissionTabmark'
                }),
                misEngine.create('component.tabmark', {
                    selector : '#hospitalizationTabmark'
                }),
                misEngine.create('component.tabmark', {
                    selector : '#historyTabmark'
                })
            ];
            $(this.tabmarks).each(function(index, element) {
                $(element).trigger('show');
            });
        },
		
		displayGrids : function() {
			this.displayQueueGrid();
			this.displayComissionGrid();
			this.displayHospitalizationGrid();
			this.displayHistoryGrid();
		},
		
		displayQueueGrid : function() {
			this.queueGrid = misEngine.create('component.grid');
			var queueGridRequestData = { 
				returnAsJson : true,
				id : 'queueGrid',
                serverModel : 'QueueGrid',
                container : '#queue'
			};
			
			this.queueGrid
				.setConfig({
					renderConfig : {
						mode : 'ajax',
						ajaxConf : {
							url : '/hospital/components/grid',
							data : queueGridRequestData,
							dataType : 'json',
							success : function(data, status, jqXHR) {
								if(data.success) {
									$(queueGridRequestData.container)
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
				id : 'comissionGrid',
                serverModel : 'ComissionGrid',
                container : '#comission'
			};
			this.comissionGrid
				.setConfig({
					renderConfig : {
						mode : 'ajax',
						ajaxConf : {
							url : '/hospital/components/grid',
							data : comissionGridRequestData,
							dataType : 'json',
                            type : 'GET',
							success : function(data, status, jqXHR) {
								if(data.success) {
									$(comissionGridRequestData.container).css({
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
			this.hospitalizationGrid = misEngine.create('component.grid');
			var hospitalizationGridRequestData = { 
				returnAsJson : true,
                serverModel : 'HospitalizationGrid',
                container : '#hospitalization',
                id : 'hospitalizationGrid'
			};
			
			this.hospitalizationGrid
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
			this.historyGrid = misEngine.create('component.grid');
			var historyGridRequestData = { 
				returnAsJson : true,
				id : 'historyGrid',
                serverModel : 'HistoryGrid',
                container : '#history'
			};
			this.historyGrid
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
                selector : '#changeHospitalizationDatePopup',
                renderConfig : {
                    mode : 'internal'
                }
            }).render().on();
        },

        bindHandlers : function() {
            $('#hospitalizationNavbar a').click(function (e) {
                e.preventDefault();
            });

            this.changeComissionDateHandler();
            this.reloadGridsHandler();
            return this;
        },

        /**
         * Handlers
         */
        changeComissionDateHandler : function() {
            var selector = '.changeHospitalizationDate'
            var comissionGridModal = this.comissionGridModal;
            $(document).on('click', selector, function(e) {
                var directionId = $(this).prop('id').substr(2);
                var gridId = $(this).parents('.grid-view').prop('id');
                $.ajax({
                    'url': '/hospital/hospitalization/getdirectiondata?id=' + directionId,
                    'cache': false,
                    'dataType': 'json',
                    'type': 'GET',
                    'success': function (data, textStatus, jqXHR) {
                        if(data.success) {
                            if(data.data.hospitalization_date) {
                                $('input[name="FormHospitalizationDateChange[hospitalization_date]"]').val(data.data.hospitalization_date);
                            }
                            $('input[name="FormHospitalizationDateChange[id]"]').val(directionId);

                            $('input[name="FormHospitalizationDateChange[grid_id]"]').val(gridId);
                            $(comissionGridModal).trigger('show');
                        }
                    }
                });
            });
        },

        reloadGridsHandler : function() {
            var comissionGrid = this.comissionGrid;
            var queueGrid = this.queueGrid;
            var gridModal = this.comissionGridModal;
            $(document).on('reload', '#queueGrid, #comissionGrid', function(e) {
                switch($(this).prop('id')) {
                   case 'comissionGrid': comissionGrid.reloadGrid(); break;
                   case 'queueGrid': queueGrid.reloadGrid(); break;
                }
                $(gridModal).trigger('hide');
            });
        },

        init : function() {
			this.displayDatetimepickers();
			this.displayGrids();
            this.displayTabmarks();
            this.renderModals();
			this.bindHandlers();
			return this;
		}
	};
});