$(document).ready(function() {

	function Shedule() {
		var obj = {
			container : null,
			idOfComponent : null,
			url : null,
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
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			},
			getCurrentDays : function() {
				var days = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
				var months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
				var dayData = [];
				var currentMonth = (new Date()).getMonth();
				var prev = null;
				if(this.loadedData.length > 0) {
					for(var i in this.loadedData[0].shedule) {
						if(prev && prev > parseInt(this.loadedData[0].shedule[i].day)) {
							// Get next month...
							currentMonth = (currentMonth + 1) % 11;
						}
						prev = parseInt(this.loadedData[0].shedule[i].day);
						dayData.push({
							'dayname' : days[this.loadedData[0].shedule[i].weekday],
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
				if(this.ajaxLoadGif) {
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
						'sidx' : 'd.last_name',
						'rows' : 15,
						'page' : 1,
						'sord' : 'desc'
					},
					'type' : 'GET',
					'async' : async,
					'success' : $.proxy(this.parseResponse, this)
				});
			},
			
			parseResponse : function(data) {
				var preparedData = [];
				if(data.success) {
					this.loadedData = data.data;
				} 
				
				if(this.ajaxLoadGif && this.ajaxLoadGifLink != null) {
					$(this.ajaxLoadGifLink).remove();
				}
				
				$(this.container).prepend(
					$('<table>').addClass('table').append(
						this.theadLink = this.getHeader(), 
						this.prepareData(this.getFirstPage())
					).prop('id', this.idOfComponent != null ? this.idOfComponent : 'sheduleTable')
				);
				this.setUpdateTimer();
			},
			
			getFirstPage : function() {
				this.currentPage = 0;
				return this.getPage(this.currentPage);
			},
			
			getPage : function(page) {
				return this.loadedData.slice(this.currentPage * this.perPage, this.currentPage * this.perPage + this.perPage + 1); 
			},
			
			getNextPage : function() {
				if((this.perPage * this.currentPage + this.perPage) > this.loadedData.length) {
					this.currentPage = 0;
				} else {
					this.currentPage++;
				}
				return this.getPage(this.currentPage);
			},

			prepareData : function(doctors) {
				var tbody = $('<tbody>');

				for(var i = 0; i < doctors.length; i++) {
					var newTr = $('<tr>').addClass('sheduleTr');
					var fioTd, cabTd;
					var fio = doctors[i].last_name + ' ' + doctors[i].first_name + (doctors[i].middle_name == null ? '' : ' ' + doctors[i].middle_name);
					
					$(newTr).append(
						fioTd = $('<td>').addClass('col-xs-3').append(fio),
						postTd = $('<td>').addClass('col-xs-1').append($('<span>').addClass('profession').text(doctors[i].post.toUpperCase())),
						cabTd = $('<td>').addClass('col-xs-1').text(doctors[i].cabinet)
					);

					for(var j = 0; j < doctors[i].shedule.length; j++) {
						if(typeof doctors[i].shedule[j].beginTime != 'undefined' && typeof doctors[i].shedule[j].endTime != 'undefined') {
							$(newTr).append(
								$('<td>').text(doctors[i].shedule[j].beginTime + ' - ' + doctors[i].shedule[j].endTime)
							);
						} else {
							$(newTr).append(
								$('<td>').css({
									'background' : '#f2f5f6'
								})
							);
						}
					}
					$(tbody).append(newTr);
				}
				return tbody;
			},
			getHeader : function() {
				var thead = $('<thead>');
				var days = this.getCurrentDays();
				var headTr;
				$(thead).append(
					headTr = $('<tr>').append(
						$('<td>').addClass('col-xs-3').text('Врач'),
						$('<td>').addClass('col-xs-1').text('Специальность'),
						$('<td>').addClass('col-xs-1').text('Каб.')
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
				setTimeout($.proxy(function() {
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
			
			reload : function() {
				this.createRequest();
			},
			
			goToNextPage : function() {
				$(this.container).find('table').append(
					this.prepareData(this.getNextPage())
				);
				this.setUpdateTimer();
			},
		}

		return obj;
	}
	
	
	var sheduleTable = new Shedule();
	sheduleTable.setOptions({
		container : $('#sheduleRow'),
		idOfComponent : 'publicSheduleTable',
		url : '/reception/doctors/search',
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
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			},
			go : function() {
				setTimeout($.proxy(function() {
					this.makeStep();
					this.go();
				}, this), this.updateTimeout);
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
	
	marquee = new Marquee().setOptions({
		updateTimeout : 40,
	}).go();
});