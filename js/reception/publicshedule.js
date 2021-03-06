$(document).ready(function() {
	function AuthManager() {
		return {
			serverUrl : '/users/islogged',
			callbackIsLogged : null,
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			},
			isLogged : function() {
				if(this.callbackIsLogged == null || typeof this.callbackIsLogged != 'function') {
					console.log('Not found callback function in auth manager');
					return false;
				}
				$.ajax({
					'url' : this.serverUrl,
					'cache' : false,
					'dataType' : 'json',
					'type' : 'GET',
					'async' : 'true',
					'success' : $.proxy(this.callbackIsLogged, this)
				});
			}
		}
	}

	var completeModel = (function() {
		return {
			counter : 0,
			endcounter : 2,
			numInvokers : 0,
			action : null,
			showModel : function() {
				if(++this.counter == this.endcounter && typeof this.action == 'function') {
					this.action();
					$(this).trigger('reset');
				} 
				this.maybeResetInvokers(true);
			},
			
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			},
			
			incrementInvokers : function() {
				this.numInvokers++;
				this.maybeResetInvokers(false);
			},
			
			resetCounter : function() {
				this.counter = 0;
			},
			
			maybeResetInvokers : function(fromSuccess) {
				if(this.numInvokers >= this.endcounter) {
					this.numInvokers = 0;
					if(typeof fromSuccess == 'undefined' && !fromSuccess) {
						alert('Данные не сохранены, попробуйте ещё раз....');
					}
				}
			},
			
			init : function(options) {
				$(this).on('makethis', $.proxy(this.showModel, this));
				$(this).on('incinvoker', $.proxy(this.incrementInvokers, this));
				$(this).on('reset', $.proxy(this.resetCounter, this));
				this.setOptions(options)
				return this;
			}
		}
	})
	().init({
		action : function() {
			alert('Настройки успешно сохранены!');
		}
	});

	function Shedule() {
		var obj = {
			container : null,
			idOfComponent : null,
			url : null,
			saveSettingsUrl : null,
			ajaxLoaderWidth : 32,
			ajaxLoaderHeight : 32,
			filters : null, // default filters 
			ajaxLoadGif : false, // AjaxGif for loading
			loadedData : null, // Loaded data for page navigation
			currentPage : 0, // Current page in doctorsList
			perPage : 10, // How much rows in shedule page 
			fade : false, // Fade-effect by page navigation
			updateTimeout : 500, // Time for shedule updating
			ajaxLoadGif : null, // This is the link for generated gif
			theadLink : null, // This is the link for thead in shedule
			sortBy : 'last_name', // Sort by field..
			saveSettingsUrl : null, 
			withoutIds : [], // Filter for doctors
			filteredData : [], // Doctors with filter
			numCycles : null, // Num cycles for old data, without new loading
			currentCycle : 0, // Current cycle
			tableLink : null, // This is link for table...
			updateTimer : null, // Timer, SetTimeout
			cabinets : [], // Cabinets array with descriptions..
			datesLimits : [], // Limits in events, in dates
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			},
			getOption : function(optionKey, callback) {
				if(this.hasOwnProperty(optionKey)) {
					if(typeof callback == 'function') {
						return callback(this[optionKey]);
					} else {
						return this[optionKey];
					}
				}
				return null;
			},
			saveOptions : function() {
				$.ajax({
					'url' : this.saveSettingsUrl,
					'cache' : false,
					'dataType' : 'json',
					'data' : {
						'module' : 2,
						'values' : {
							'perPage' : this.getOption('perPage'),
							'updateTimeout' : this.getOption('updateTimeout'),
							'sortBy' : this.getOption('sortBy'),
							'numCycles' : this.getOption('numCycles'),
							'withoutIds' : $.toJSON(this.getOption('withoutIds'))
						}
					},
					'type' : 'GET',
					'async' : 'true',
					'success' : $.proxy(function(data, textStatus, jqXHR) {
						$(completeModel).trigger('incinvoker');
						if(data.success) {
							$(completeModel).trigger('makethis');
						}
					}, this),
					'error' : function(jqXHR, status, errorThrown) {
						$(completeModel).trigger('incinvoker');
					}
				});
			},
			getCurrentDays : function() {
				var days = ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];
				var months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
				var dayData = [];
				var currentMonth = (new Date()).getMonth();
				var prev = null;
				if(this.filteredData.length > 0) {
					for(var i in this.filteredData[0].shedule) {
						if(prev && prev > parseInt(this.filteredData[0].shedule[i].day)) {
							// Get next month...
							currentMonth = (currentMonth + 1) % 12;
						}
						prev = parseInt(this.filteredData[0].shedule[i].day);
						dayData.push({
							'dayname' : days[this.filteredData[0].shedule[i].weekday],
							'day' : prev,
							'monthname' : months[currentMonth]
						});
					}
				}
				return dayData;
			},
			getAjaxGif : function() {
				return $('<img>').prop({
					'src': '/images/ajax-loader.gif',
					'width': this.ajaxLoaderWidth,
					'height': this.ajaxLoaderHeight,
					'alt': 'Загрузка...'
				});
			},
			getCurrentDoctorsList : function() {
				return this.createRequest(false);
			},
			/* This method requests all doctors with their shedule per filters */
			createRequest : function(async) {
				// Create ajaxgif loader 
				if(this.ajaxLoadGif && !this.theadLink) { // This is first cycle...
					$(this.container).prepend(
						this.ajaxLoadGifLink = $(this.getAjaxGif()).css({
							'marginLeft' : '50%',
							'marginBottom' : '10px'
						})
					);
				}
				
				if(typeof async == 'undefined') {
					async = true;
				}
				if(this.url == null) {
					console.log("Not found shedule URL. Script has broken, sorry for fucking hostkeeper, who doesn't setted script normally");
					return false;
				}
				if(this.filters == null) {
					console.log("Not found filters... Default too");
					return false;
				}
				
				$.ajax({
					'url' : this.url,
					'cache' : false,
					'dataType' : 'json',
					'data' : {
						'filters' : $.toJSON(this.filters),
						'sidx' : this.sortBy,
						'rows' : 15,
						'page' : 1,
						'sord' : 'asc'
					},
					'type' : 'GET',
					'async' : async,
					'success' : $.proxy(this.parseResponse, this)
				});
			},
			
			parseResponse : function(inData) {
				var preparedData = [];
				if(inData.success) {
					this.currentCycle = 0;					
					
					var settings = inData.data.settings;

					// Настройки для расписания
					this.setOptions({
						'perPage' : parseInt(settings.perPage),
						'updateTimeout' : parseInt(settings.updateTimeout),
						'sortBy' : settings.sortBy,
						'withoutIds' : $.parseJSON(settings.withoutIds),
						'numCycles' : parseInt(settings.numCycles)
					});

					// Настройки для бегущей строки
					marquee.setOptions({
						'updateTimeout' : parseInt(settings.mUpdateTimeout),
						'text' : settings.text
					});
					
					this.loadedData = inData.data.shedule.data;
					this.cabinets = inData.data.cabinets;
					var dateLimits = inData.data.datesLimits;
					for(var limit in dateLimits) {
						if(typeof this.datesLimits['d' + dateLimits[limit].doctor_id] == 'undefined') {
							this.datesLimits['d' + dateLimits[limit].doctor_id] = {};
						}
						
						if(typeof this.datesLimits['d' + dateLimits[limit].doctor_id]['e' + dateLimits[limit].type] == 'undefined') {
							this.datesLimits['d' + dateLimits[limit].doctor_id]['e' + dateLimits[limit].type] = [];
						}

						this.datesLimits['d' + dateLimits[limit].doctor_id]['e' + dateLimits[limit].type].push(dateLimits[limit].date.split(' ')[0]);
					}

					// Filter elements...
					// fucking JS with their typeof 
					if(this.withoutIds && typeof this.withoutIds == 'object' && this.withoutIds.length > 0) {
						this.filteredData = this.loadedData.filter(function(element) {
							return this.withoutIds.indexOf(element.id.toString()) == -1;
						}, this);
					} else {
						this.filteredData = this.loadedData;
					}
					
					if(this.ajaxLoadGif && this.ajaxLoadGifLink != null) {
						$(this.ajaxLoadGifLink).remove();
					}
					
					if(!this.tableLink) {
						$(this.container).prepend(
							this.tableLink = $('<table>').addClass('table').append(
								this.theadLink = this.getHeader(), 
								this.prepareData(this.getFirstPage())
							).prop('id', this.idOfComponent != null ? this.idOfComponent : 'sheduleTable')
						);
					} else {
						$(this.container).find('table').append(
							this.prepareData(this.getFirstPage())
						);
					}
					this.setUpdateTimer();
				} 
			},
			
			getFirstPage : function() {
				this.currentPage = 0;
				return this.getPage(this.currentPage);
			},
			
			getPage : function(page) {
				return this.filteredData.slice(this.currentPage * this.perPage, this.currentPage * this.perPage + this.perPage); 
			},
			
			getNextPage : function() {			
				if((this.perPage * this.currentPage + this.perPage) >= this.filteredData.length) {
					this.currentPage = 0;
					if(this.currentCycle >= this.numCycles - 1) {
						if(this.updateTimer != null) {
							clearTimeout(this.updateTimer);
						}
						this.reload(true);
						return []; // Fuck.
					} else {
						this.currentCycle++;
					}
				} else {
					this.currentPage++;

				}
				this.setUpdateTimer();
				return this.getPage(this.currentPage);
			},

			prepareData : function(doctors) {
				var tbody = $('<tbody>');

				for(var i = 0; i < doctors.length; i++) {
					var newTr = $('<tr>').addClass('sheduleTr');
					var fioTd, cabTd;
					var fio = doctors[i].last_name + '<br />' + doctors[i].first_name + (doctors[i].middle_name == null ? '' : ' ' + doctors[i].middle_name);
					
					$(newTr).append(
						postTd = $('<td>').addClass('col-xs-1').append($('<span>').addClass('profession').text(doctors[i].post.toUpperCase()), (doctors[i].cabinet ? ', кабинет ' + doctors[i].cabinet : '')),
						fioTd = $('<td>').addClass('col-xs-4').append(fio)
					);
					
					var currentRestType = null; // Current rest-day type: sick, holiday...
					var currentNestedTd = null;
					var numNestedCols = 1;
					var currentRestLimit = null;
					var currentTdType = null;
					var part = '';
					for(var j = 0; j < doctors[i].shedule.length; j++) {
						if(typeof doctors[i].shedule[j].beginTime != 'undefined' && typeof doctors[i].shedule[j].endTime != 'undefined') {
							$(newTr).append(
								$('<td>').prop({
									'id' : 'c' + i + '_' + j
								}).html(doctors[i].shedule[j].beginTime + ' - ' + doctors[i].shedule[j].endTime + '<br />каб. ' + (($.trim(doctors[i].shedule[j].cabinet) != '' && typeof this.cabinets[$.trim(doctors[i].shedule[j].cabinet)] != 'undefined') ? this.cabinets[$.trim(doctors[i].shedule[j].cabinet)].number : 'неизвестен'))
							);
						} else {
							// This is work with rest days and other...
							// Step 1
							if(!currentNestedTd) {
							
								currentRestType = null;
								part = '';									
								numNestedCols = 1;
								
								if(doctors[i].shedule[j].hasOwnProperty('restDayType')) {
									currentTdType = 1;
									currentRestType = doctors[i].shedule[j].restDayType;
									currentNestedTd = $('<td>').prop({
										'id' : 'c' + i + '_' + j
									});
									$(currentNestedTd).append($.proxy(function() {
										switch(parseInt(currentRestType)) {
											case 1 : return 'Отпуск'; break;
											case 2 : return 'Больничный'; break;
											case 3 : return 'Командировка'; break;
											case 4 : return 'Отгул'; break;
											case 5 : return 'Дежурство'; break;
											case 6 : return 'Неприёмный день'; break;
											default : return '';
										}
									}, this));
									$(currentNestedTd).css({
										'background' : function() {
											return [
												'#F6E3CE',
												'#F2F5A9',
												'#F6CEF5',
												'#F5A9A9',
												'#E0F8EC',
												'#F5A9D0'
											][currentRestType - 1];
										}
									});
									
									$(newTr).append(
										$(currentNestedTd)
									);
									
								} else if(!doctors[i].shedule[j].hasOwnProperty('restDayType') && doctors[i].shedule[j].restDay) {
									currentTdType = 2;
									currentRestType = null;
									$(newTr).append(
										$('<td>').css({
											'background' : '#f2f5f6'
										}).prop({
											'id' : 'c' + i + '_' + j
										})
									);
									continue;
								}
							} else {
								if(doctors[i].shedule[j].hasOwnProperty('restDayType') && doctors[i].shedule[j].restDayType == currentRestType) {
									numNestedCols++;
								}
								if(!doctors[i].shedule[j].hasOwnProperty('restDayType') && doctors[i].shedule[j].restDay) {
									numNestedCols++;
								}
							}
							
							// Step 2
							if($.trim(part) == '') {
								currentRestLimit = this.getRestLimit(i, j, currentRestType, doctors);
								if($.trim(currentRestLimit) != '') {
									part = ' до ' + currentRestLimit;
								}
								$(currentNestedTd).text(
									$(currentNestedTd).text() + part
								)
							}

							// Step 3
							var func = function() {
								$(currentNestedTd).prop({
									'colspan' : numNestedCols
								});
							}
							
							if(j < doctors[i].shedule.length - 1) {
								if(doctors[i].shedule[j].hasOwnProperty('restDayType')) {
									if(doctors[i].shedule[j + 1].hasOwnProperty('restDayType') && currentRestType != doctors[i].shedule[j + 1].restDayType) {
										func();
										currentNestedTd = null;
									}
									if(!doctors[i].shedule[j + 1].hasOwnProperty('restDayType') && doctors[i].shedule[j + 1].restDay) {
										func();
									}
									if(doctors[i].shedule[j + 1].worked) {
										func();
										currentNestedTd = null;
									}
								} else if(!doctors[i].shedule[j].hasOwnProperty('restDayType') && doctors[i].shedule[j].restDay) {
									if(doctors[i].shedule[j + 1].hasOwnProperty('restDayType') && currentRestType == doctors[i].shedule[j + 1].restDayType) {
										func();
									}
									if(doctors[i].shedule[j + 1].hasOwnProperty('restDayType') && currentRestType != doctors[i].shedule[j + 1].restDayType) {
										func();
										currentNestedTd = null;
									}
									if(!doctors[i].shedule[j + 1].hasOwnProperty('restDayType') && doctors[i].shedule[j + 1].restDay) {
										func();
									}
									if(doctors[i].shedule[j + 1].worked) {
										func();
										currentNestedTd = null;
									}
								}
							} else {
								if(doctors[i].shedule[j].hasOwnProperty('restDayType') && currentRestType == doctors[i].shedule[j].restDayType) {
									func();
								}
								if(!doctors[i].shedule[j].hasOwnProperty('restDayType') && doctors[i].shedule[j].restDay) {
									func();
								}
							}
						}
					}
					$(tbody).append(newTr);
				}
				return tbody;
			},
			
			getRestLimit : function(i, j, currentRestType, doctors) {					
				if(typeof this.datesLimits['d' + doctors[i].id] != 'undefined' && typeof this.datesLimits['d' + doctors[i].id]['e' + currentRestType] != 'undefined') {
					for(var k in this.datesLimits['d' + doctors[i].id]['e' + currentRestType]) {
						if(this.datesLimits['d' + doctors[i].id]['e' + currentRestType][k] == doctors[i].shedule[j].year + '-' + doctors[i].shedule[j].month + '-' + doctors[i].shedule[j].day) {
							return this.datesLimits['d' + doctors[i].id]['e' + currentRestType][k].split('-').reverse().join('.');
						}
					}
				}
				return '';
			},
			
			getHeader : function() {
				var thead = $('<thead>');
				var days = this.getCurrentDays();
				var headTr;
				$(thead).append(
					headTr = $('<tr>').append(
						$('<td>').addClass('col-xs-1').text('Специальность'),
						$('<td>').addClass('col-xs-2').text('Врач')
					)
				)
				for(var i = 0; i < days.length; i++) {
					var newTd = $('<td>').html(days[i].dayname + '<br />' + days[i].day + ' ' + days[i].monthname).appendTo(headTr);
				}
				return thead;
			},
			display : function() {
				if(this.container == null) {
					return 'Container for table not found...';
				}
				this.reload();
			},
			setUpdateTimer : function() {
				if(this.updateTimer != null) {
					clearTimeout(this.updateTimer);
				}
				this.updateTimer = setTimeout($.proxy(function() {
					if(this.fade) {
						$(this.container).find('tbody tr').fadeOut(700, function() {
							$(this.container).find('tbody tr').remove();
						});
					} else {
						$(this.container).find('tbody tr').remove();
					}
					this.goToNextPage();
				}, this), this.updateTimeout);
			},
			
			reload : function(notAsyncReq) {
				if(typeof notAsyncReq != 'undefined' && notAsyncReq) {
					this.createRequest(false);
				} else {
					this.createRequest();
				}
			},
			
			goToNextPage : function() {
				$(this.container).find('table tbody').remove();
				$(this.container).find('table').append(
					this.prepareData(this.getNextPage())
				);
			},
		}

		return obj;
	}
	
	
	var sheduleTable = new Shedule();
	sheduleTable.setOptions({
		container : $('#sheduleRow'),
		idOfComponent : 'publicSheduleTable',
		url : '/reception/doctors/getpublicshedule',
		saveSettingsUrl : '/settings/system/settingsjsonedit',
		ajaxLoadGif : true,
		fade : false,
		updateTimeout : 3000, 
		perPage : 9,
		filters : {
			"groupOp" : "AND",
			"rules" : [
				{
					"field" : "ward_code",
					"op" : "eq",
					"data" : "-1"
				},
				{
					"field" : "post_id",
					"op" : "eq",
					"data" : "-1"
				},
				{
					"field" : "greeting_type",
					"op" : "eq",
					"data" : "0"
				},
				{
					"field" : "middle_name",
					"op" : "cn",
					"data" : ""
				},
				{
					"field" : "last_name",
					"op" : "cn",
					"data": ""
				},
				{
					"field" : "first_name",
					"op" : "cn",
					"data" : ""
				},
				{
					"field" : "diagnosis",
					"op" : "in",
					"data" : []
				}
			]
		}
	}).display();
	
	
	function Marquee() {
		return {
			updateTimeout : 70, // Timeof updating
			currentMargin : 0,
			step : 0.15,
			text : 'Заданный текст строки...',
			saveSettingsUrl : null,
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			},
			getOption : function(optionKey, callback) {
				if(this.hasOwnProperty(optionKey)) {
					if(typeof callback == 'function') {
						return callback(this[optionKey]);
					} else {
						return this[optionKey];
					}
				}
				return null;
			},
			saveOptions : function() {
				if(this.saveSettingsUrl == null) {
					console.log("Can't save settings");
					return false;
				}
				$.ajax({
					'url' : this.saveSettingsUrl,
					'cache' : false,
					'dataType' : 'json',
					'data' : {
						'module' : 3,
						'values' : {
							'text' : this.getOption('text'),
							'mUpdateTimeout' : this.getOption('updateTimeout')
						}
					},
					'type' : 'GET',
					'async' : 'true',
					'success' : $.proxy(function(data, textStatus, jqXHR) {
						$(completeModel).trigger('incinvoker');
						if(data.success) {
							$(completeModel).trigger('makethis');
						}
					}, this),
					'error' : function(jqXHR, status, errorThrown) {
						$(completeModel).trigger('incinvoker');
					}
				});
			},
			go : function() {
				if(this.hasOwnProperty('text') && this.hasOwnProperty('text') != null) {	
					$('.marquee span').text(this.text);
				}
				setTimeout($.proxy(function() {
					this.makeStep();
					this.go();
				}, this), this.updateTimeout);
				return this;
			},
			makeStep : function() {
				if(this.currentMargin > 100) {
					this.currentMargin = 0;
				} else {
					this.currentMargin += this.step
				}
				$('.marquee span').css({
					'marginLeft' : this.currentMargin + '%'
				});
			},
			init : function() {
			
			
			}
		};
	}
	
	var marquee = new Marquee().setOptions({
		updateTimeout : 40,
		saveSettingsUrl : '/settings/system/settingsjsonedit'
	}).go();
	
	
	function SettingsWindow(options) {
		return {
			iconContainer : null,
			settingsPopover : null,
			submitButton : null,
			form : null,
			settingsFormElements : {},
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			},
			init : function() {
				if(this.iconContainer == null) {
					console.log('Not found icon container');
					return false;
				}
				$(this.iconContainer)
					.on('mouseover', $.proxy(this.showSettingsIcon, this))
					.on('mouseout', $.proxy(this.hideSettingsIcon, this))
					.on('click', $.proxy(this.openWindow, this));
				return this;
			},
			showSettingsIcon : function(e) {
				$(this.iconContainer).animate({
					'opacity' : 1
				}, 200);
			},
			
			hideSettingsIcon : function(e) {
				if(this.settingsPopover != null) {
					return false;
				}
				$(this.iconContainer).animate({
					'opacity' :  0
				}, 200);
			},
			
			openWindow : function(e) {
				if(this.settingsPopover != null) {
					this.settingsPopover.popover('show');
					return true; 
				}
			
			    this.settingsPopover = $(this.iconContainer).popover({
					animation: true,
					html: true,
					placement: 'left',
					title: 'Настройки табло',
					delay: {
						show: 100,
						hide: 100
					},
					container: $(this.iconContainer),
					content: $.proxy(function() {
						return this.createSettingsForm();
					}, this)
				}).on('hidden.bs.popover', $.proxy(function(e) {
					this.settingsPopover = null;
					$(this.iconContainer).trigger('mouseout');
				}, this));
						
				this.settingsPopover.popover('show');
				
				var span = $('<span class="glyphicon glyphicon-remove" title="Закрыть окно"></span>').css({
					marginLeft: '470px',
					position: 'absolute',
					cursor: 'pointer'
				});

				$(span).on('click', $.proxy(function(e) {
					this.settingsPopover.popover('destroy');
				}, this));
				
				$(this.iconContainer).find('.popover').css({
					'top' : '3px',
					'left' : '-500px',
					'minWidth' : '500px',
				}).append(span);
				
				$(this.iconContainer).find('.popover-content').css({
					'fontSize' : '15px',
					'minWidth' : '500px',
				});

				$(this.iconContainer).find('.popover .arrow').remove();
				
				$(this.iconContainer).find('.popover-title').css({
					'fontSize' : '16px',
					'fontWeight' : 'bold'
				});
				
				$(this.iconContainer).on('click', '.popover', function(e) {
					return false;
				});
			},
			
			createSettingsForm : function() {
				this.form = $('<form>').addClass('form-horizontal col-xs-12').prop({
					'role' : 'form'
				});
				this.form.on('keydown', $.proxy(function(e) {
					if(e.keyCode == 13) {
						this.submitButton.trigger('click');
					}
				}, this));

				$(this.form).append(
					$('<div>').addClass('form-group').append(
						$('<label>').addClass('col-xs-5').text('Отображать строк'),
						$('<div>').addClass('col-xs-2').append(
							this.settingsFormElements.perPage = $('<input>').addClass('form-control').prop({
								'type' : 'textfield',
								'value' : sheduleTable.getOption('perPage')
							})
						)
					), 
					$('<div>').addClass('form-group').append(
						$('<label>').addClass('col-xs-5').text('Таймаут между страницами (секунд)'),
						$('<div>').addClass('col-xs-2').append(
						this.settingsFormElements.updateTimeout = $('<input>').addClass('form-control').prop({
								'type' : 'textfield',
								'value' : sheduleTable.getOption('updateTimeout', function(value) {
									return value / 1000; // Секунды
								})
							})
						)
					),
					$('<div>').addClass('form-group').append(
						$('<label>').addClass('col-xs-5').text('Сортировать по'),
						$('<div>').addClass('col-xs-7').append(
							this.settingsFormElements.sortBy = $('<select>').addClass('form-control').append(
								$('<option>').prop({
									'value' : 'last_name'
								}).text('ФИО')
							).val(sheduleTable.getOption('sortBy'))
						)
					),
					$('<div>').addClass('form-group').append(
						$('<label>').addClass('col-xs-5').text('Текст бегущей строки'),
						$('<div>').addClass('col-xs-7').append(
							this.settingsFormElements.text = $('<input>').addClass('form-control').prop({
								'type' : 'textfield',
								'value' : marquee.getOption('text')
							})
						)
					),
					$('<div>').addClass('form-group').append(
						$('<label>').addClass('col-xs-5').text('Скорость бегущей строки (мс)'),
						$('<div>').addClass('col-xs-2').append(
							this.settingsFormElements.mUpdateTimeout = $('<input>').addClass('form-control').prop({
								'type' : 'textfield',
								'value' : marquee.getOption('updateTimeout')
							})
						)
					),
					$('<div>').addClass('form-group').append(
						$('<label>').addClass('col-xs-5').text('Количество циклов без забора данных с сервера'),
						$('<div>').addClass('col-xs-2').append(
							this.settingsFormElements.numCycles = $('<input>').addClass('form-control').prop({
								'type' : 'textfield',
								'value' : sheduleTable.getOption('numCycles')
							})
						)
					),
					$('<div>').addClass('form-group').append(
						$('<label>').addClass('col-xs-5').text('Не показывать врачей'),
						$('<div>').addClass('col-xs-7').append(
							this.settingsFormElements.withoutIds = $('<select>').addClass('form-control').append(
								function() {
									var options = [];
									for(var i in sheduleTable.loadedData) {
										options.push(
											$('<option>').prop({
												'value' : sheduleTable.loadedData[i].id
											}).text(sheduleTable.loadedData[i].last_name + ' ' + sheduleTable.loadedData[i].first_name + ($.trim(sheduleTable.loadedData[i].middle_name) != '' ? ' ' + $.trim(sheduleTable.loadedData[i].middle_name) : ' '))
										);
									}
									return options;
								}
							).prop({
								'multiple' : 'multiple',
								'id' : 'withoutIds'
							}).val(sheduleTable.getOption('withoutIds'))
						)
					),
					$('<div>').addClass('form-group').append(
						$('<div>').addClass('col-xs-2').append(
							this.submitButton = $('<input>').addClass('btn btn-success').prop({
								'value' : 'Сохранить',
								'type' : 'button'
							}).on('click', $.proxy(this.saveSettings, this))
						)
					)
				);
				return this.form;
			},
			
			saveSettings : function(e) {
				if(isNaN(parseInt(this.settingsFormElements.updateTimeout.val() * 1000))) {
					alert('Неверно задан таймайт: введено не числовое значение!');
					return false;
				}
				
				if(isNaN(parseInt(this.settingsFormElements.perPage.val()))) {
					alert('Неверно задано количество результатов на страницу: введено не числовое значение!');
					return false;
				}
				
				if(isNaN(parseInt(this.settingsFormElements.mUpdateTimeout.val()))) {
					alert('Неверно задан таймайт: введено не числовое значение!');
					return false;
				}
				
				var authManager = new AuthManager();
				authManager.setOptions({
					'callbackIsLogged' : $.proxy(function(data, textStatus, jqXHR) {
						if(data.success) {	
							sheduleTable.setOptions({
								'updateTimeout' : parseInt(this.settingsFormElements.updateTimeout.val() * 1000),
								'perPage' : parseInt(this.settingsFormElements.perPage.val()),
								'sortBy' : this.settingsFormElements.sortBy.val(),
								'withoutIds' : this.settingsFormElements.withoutIds.val() === null ? [] : this.settingsFormElements.withoutIds.val(),
								'numCycles' : parseInt(this.settingsFormElements.numCycles.val())
							});
							
							// Recalc all with new filter
							if(sheduleTable.withoutIds.length > 0) {
								sheduleTable.filteredData = sheduleTable.loadedData.filter(function(element) {
									return sheduleTable.withoutIds.indexOf(element.id.toString()) == -1;
								}, this);
							} else {
								sheduleTable.filteredData = sheduleTable.loadedData;
							}
							
							sheduleTable.saveOptions();
							
							marquee.setOptions({
								'updateTimeout' : this.settingsFormElements.mUpdateTimeout.val(),
								'text' : this.settingsFormElements.text.val()
							});
							marquee.saveOptions();
							
							this.settingsPopover.popover('destroy');
						} else {
												
							var authWindow = new AuthWindow().setOptions({
								parentContainer : $(this.settingsPopover).find('.popover'),
								authManager : authManager
							}).init();

							authWindow.open();
						}
					}, this)
				}).isLogged();
			}
		}
	}
	
	var settingWindow = new SettingsWindow().setOptions({
		iconContainer : '#sheduleNavbar .glyphicon-cog'
	}).init();
	
	function AuthWindow() {
		return {
			authPopover : null,
			authManager : null, // Auth Manager for login control
			parentContainer : null,
			loginElement : null, // Textfield for login
			passwordElement : null, // Textfield for password
			serverUrl : '/users/login',
			form : null, // Form for login, DOM element
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			},
			
			createWindow : function() {
				if(this.authPopover != null) {
					this.authPopover.popover('show');
					return true; 
				}

			    this.authPopover = $(this.parentContainer).popover({
					animation: true,
					html: true,
					placement: 'left',
					title: 'Авторизация',
					delay: {
						show: 100,
						hide: 100
					},
					container: $(this.parentContainer),
					content: $.proxy(function() {
						return this.createAuthForm();
					}, this)
				}).on('hidden.bs.popover', $.proxy(function(e) {
					this.authPopover = null;
				}, this));
						
				this.authPopover.popover('show');
				
				var span = $('<span class="glyphicon glyphicon-remove" title="Закрыть окно"></span>').css({
					marginLeft: '570px',
					position: 'absolute',
					cursor: 'pointer'
				});

				$(span).on('click', $.proxy(function(e) {
					this.authPopover.popover('destroy');
				}, this));
				
				$(this.parentContainer).find('.popover').css({
					'top' : '3px',
					'left' : '-600px',
					'minWidth' : '600px',
				}).append(span);
				
				$(this.parentContainer).find('.popover .popover-content').css({
					'fontSize' : '15px',
					'minWidth' : '600px',
				});

				$(this.parentContainer).find('.popover .popover-title').css({
					'fontSize' : '16px',
					'fontWeight' : 'bold'
				});
				
				$(this.parentContainer).on('click', '.popover', function(e) {
					return false;
				});
				
				return this;
			},
			
			createAuthForm : function() {
				this.form = $('<form>').addClass('navbar-form col-xs-12').prop({
					'role' : 'form'
				});
				this.form.on('keydown', $.proxy(function(e) {
					if(e.keyCode == 13) {
						this.submitButton.trigger('click');
					}
				}, this));

				$(this.form).append(
					$('<div>').addClass('form-group').append(
						this.loginElement = $('<input>').addClass('form-control col-xs-2').prop({
							'type' : 'textfield',
							'name' : 'login',
							'id' : 'login',
							'placeholder' : 'Логин'
						})
					), 
					$('<div>').addClass('form-group').append(
						this.passwordElement = $('<input>').addClass('form-control col-xs-2').prop({
							'type' : 'password',
							'name' : 'password',
							'id' : 'password',
							'placeholder' : 'Пароль'
						})
					),
					$('<div>').addClass('form-group').append(
						this.submitButton = $('<input>').addClass('btn btn-success').prop({
							'value' : 'Войти',
							'type' : 'button',
							'id' : 'loginSubmit'
						}).on('click', $.proxy(this.tryLogin, this))
					)
				);
				
				return this.form;
			},
			
			tryLogin : function() {
				if(this.serverUrl == null) {
					console.log('Not found server url for login..');
					return false;
				}
				
				if($.trim(this.loginElement.val()) == '') {
					alert('Вы не ввели логин!');
					return false;
				}
				
				if($.trim(this.passwordElement.val()) == '') {
					alert('Вы не ввели пароль!');
					return false;
				}
				
				$.ajax({
					'url' : this.serverUrl,
					'cache' : false,
					'dataType' : 'json',
					'type' : 'POST',
					'async' : 'true',
					'data' : {
						'FormLogin' : {
							'login' : this.loginElement.val(),
							'password' : this.passwordElement.val()
						}
					},
					'success' : $.proxy(function(data, textStatus, jqXHR) {
						if(data.success == 'true') { // Sick!
							settingWindow.saveSettings(); // Second try to save settings..
						} else {
							alert('Пользователя с такой парой логин-пароль не существует!');
						}
					}, this)
				});
			
			},
			
			open : function() {
				this.createWindow();
			},
			
			init : function() {
				return this;
			},
		}
	}
	
	function RealtimeClock() {
		return {
			timeContainer : null,
			dateContainer : null,
			dateObj : new Date(), // Obj for getTime()
			state : 0, // For ":" 
			days : ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
			months : ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'],
			go : function() {
				if(this.timeContainer == null || $(this.timeContainer).length == 0) {
					console.log('Not found clock container....');
				}
				if(this.dateContainer == null || $(this.dateContainer).length == 0) {
					console.log('Not found date container....');
				}
				setTimeout($.proxy(this.setCurrentDate, this), 950); 
			},
			setCurrentDate : function() {
				$(this.timeContainer).html(this.dateObj.getHours() + (this.state ? ':' : ' ') + (parseInt(this.dateObj.getMinutes()) > 9 ? this.dateObj.getMinutes() : '0' + this.dateObj.getMinutes()));
				this.state = this.state ? 0 : 1;
				$(this.dateContainer).html('Сегодня ' + this.days[this.dateObj.getDay()] + ', ' + this.dateObj.getDate() + ' ' + this.months[this.dateObj.getMonth()] + ' ' + this.dateObj.getFullYear() + ' г.');
				setTimeout($.proxy(this.setCurrentDate, this), 950); 
			},
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			}
		};
	};
	
	var clock = new RealtimeClock().setOptions({
		'timeContainer' : '#timeCont',
		'dateContainer' : '#dateCont'
	}).go();
});