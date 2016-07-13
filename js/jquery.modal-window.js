(function($) {
	
	$.modalWindow = {};
	
	//Координаты для позиционирования блока success
	$.modalWindow.sizeSuccess = {};
	
	//Флаг того что была вызвана функция закрытия формы
	//Чтобы форма лишний раз не появлялась
	$.modalWindow.closedFlag = false;
	
	//Строка с характеристиками товара составленная из селектов
	$.modalWindow.selectStr = '';
	
	//ID Родительского комментария 
	$.modalWindow.parentID = '';
	
	//$.modalWindow.orderType = null;
	
	//Вспомогательная функция для закрытия формы
	$.modalWindow.close = function(fadeOutTime) {
								
								$('.hide-layout, .loader, .popup, .success').fadeOut(fadeOutTime); // плавно скрываем окно/фон
								setTimeout(function(){
									$(".hide-layout, .loader, .popup, .success").remove();
									//settings.afterClose.call(); //callback после закрытия
								}, fadeOutTime);
								$.modalWindow.closedFlag = false;
							}
	
	$.fn.modalWindow=function(options){
		
		//Настройки плагина по-умолчанию
		var settings = $.extend({
							
							//Флаг откуда делается заказ
							//1 - из детального просмотра
							//0 - из корзины
							orderType: null,
							
							//Адрес сайта
							siteName: "http://comments.loc/",
							
							//Время анимации при открытии и закрытии формы popup
							fadeInTime: 200,
							fadeOutTime: 300,
							
							//Высотаи ширина картинки-loader
							loaderImgWidth: 30,
							loaderImgHeight: 30,
							
							//Высотаи ширина popup блока
							popupWidth: 440,
							//popupHeight: 640,//Высота вычисляется динамически
							
							//callbacks
							beforeOpen:  function(){},
							afterOpen:   function(){},
							beforeClose: function(){},
							afterClose:  function(){}
							
							}, options || {});
							
		var make = function(){
			
			var id_target = $(this).attr('id');
			//При нажатии на кнопку открытия модального окна
			$(this).click(function(event) {
				
				event.preventDefault();
				
				//Если callback вернула false, то ничего не делаем и выходм из процедуры
				if ( settings.beforeOpen.call() === false ) {
					return false;  //callback перед открытием
				}
				
				/*if ( settings.afterClose.call() === false ) {
					return false;  //callback перед открытием
				}*/
				
				//Центрирование popup и loader при ресайзе окна
				$(window).resize(function() {
					var rszFormHight = $('.popup').height();
					$('.popup, .success')
						.css({
							"top":($(window).height()/2 - rszFormHight/2)+"px",
							"left":($(window).width()/2 - settings.popupWidth/2)+"px"
						});
						
					$('.loader')
						.css({
							"top":($(window).height()/2 - settings.loaderImgHeight/2)+"px",
							"left":($(window).width()/2 - settings.loaderImgWidth/2)+"px"
						});
					
				});
				
				//Вставка полупрозрачного фона
				var hideLayout = "<div class='hide-layout'></div>";

				$(hideLayout)
					.css({"opacity": .5})
					.appendTo('body')
					.click(function() {
						$('.hide-layout, .loader, .popup, .success').fadeOut(settings.fadeOutTime); // плавно скрываем окно/фон
						setTimeout(function(){
								$(".hide-layout, .loader, .popup, .success").remove();
								settings.afterClose.call(); //callback после закрытия
								$.modalWindow.closedFlag = true;
							}, settings.fadeOutTime); //Здесь окончание закрытия лайтбокса
						
					});
				
				//Вставка картинки-loader
				var loaderImg = "<img src='" + settings.siteName + "loader.gif' class='loader' alt=''/>";
				//var loaderImgWidth = 30, loaderImgHeight = 30;
				$(loaderImg)
					.css({
							"opacity": .6,
							"top":($(window).height()/2 - settings.loaderImgHeight/2)+"px",
							"left":($(window).width()/2 - settings.loaderImgWidth/2)+"px"
						})
					.appendTo('body');
				
				//var elemLoader = $('.loader');
				//alignCenter(elemLoader);
				
				//Плавное появление фона и картинки-loader
				$('.hide-layout').fadeIn(settings.fadeInTime, function(){ 
					$('.loader').fadeIn(settings.fadeInTime);
					$.modalWindow.closedFlag = false;
				});  // плавно показываем окно/фон
				
				//определение строки запроса в зависимости от флага orderType
				if (settings.orderType === 1) {
					var dataQuery = {'loadOrderNow_a': true, 'id_product_now': id_target, 'params': $.modalWindow.selectStr};
				} else if (settings.orderType === 0) {
					var dataQuery = {'loadOrderCart_a': true};
				}  else if (settings.orderType === 3) {
					var dataQuery = {'loadCommentsForm_a': true, 'parentIDstr': id_target};
					//ID Родительского комментария 
					$.modalWindow.parentID = id_target;
				}
				
				//$.post("/",{loadCommentsForm_a: "param1"}, function(res){ alert(res); });
				
				//Загрузка формы с сервера
				$.ajax({
					url: "/",
					type: "POST",
					dataType: "json",
					data: dataQuery,
					success: function(res){
						//var formWidth = 300, formHeight = 300;
						
						//alert(res); return;
						
						if (!$.modalWindow.closedFlag) {
							$('.loader').hide();
							
							$(res.form).appendTo('body');
							var resFormHight = $('.popup').height();
							$.modalWindow.sizeSuccess.height = resFormHight;
							$.modalWindow.sizeSuccess.width = settings.popupWidth;

							$('.popup')
								.css({
										"top":($(window).height()/2 - resFormHight/2)+"px",
										"left":($(window).width()/2 - settings.popupWidth/2)+"px"
									})
								.fadeIn(100)
								.find('.btn-close')
								.click(function(){ $.modalWindow.close(settings.fadeOutTime); });
								
								/*var objOpen = { 
									top: settings.popupHeight,
									left: settings.popupWidth
								};*/// !!!!!!!!!!!!!!!!
								
								settings.afterOpen.call(/*objOpen*/);
						}
					}
				});
				
			});
		};
		
		return this.each(make);
	
	} // блок $.fn.modalWindow
	
})(jQuery);