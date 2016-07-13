<?php 

	define('WEBIN4', true);
	
	define('SITE', "http://comments.loc/");
	
	session_start();
	//echo session_name();
	//echo session_id();
	//unset($_SESSION['captcha_keystring']);
	//$_SESSION['captcha_keystring'] = "123465789";
	
	require_once "functions/functions.php";
		
	
	
	if ($_POST['loadCommentsForm_a']) { //Загрузка формы для добавления нового комментария
		//exit("******");
		loadCommentsForm();
	}
	
	if ($_POST['getCommentsProcess_a']) { //Если пришел запрос на добавление комментария в БД
		//exit("******");
		
		//Из id ссылки извлекаем id комментария-родителя
		//(id ссылки отправляется ajax-запросом из плагина jquery.modal-window)
		//$_POST['parentID'] = post-<id-поста>_cid-<id-комментария>
		list($postid_str, $parentid_str) = explode("_", $_POST['parentID']);
		list(,$postid) = explode("-", $postid_str);
		list(,$parentid) = explode("-", $parentid_str);
		
		$comments_data = array(
								'cart_customer'=>$_POST['cart_customer'],//автор комментприя
								'cart_comment'=>$_POST['cart_comment'],//текст
								'cart_email'=>$_POST['cart_email'],
								'cinput'=>$_POST['cinput'],
								'post_id'=>clear_admin($postid, "int"),//id поста
								'parent_id'=>clear_admin($parentid, "int"),//id родителя
								'link_id'=>$_POST['parentID']//post-<id-поста>_cid-<id-комментария>
								);
								
		getCommentsProcess($comments_data);//Добавление комментария в БД
	}
	
	
	if ($_POST['check']) {
		exit($_SESSION['captcha_keystring']."***");
	}
	
	$_comments = show_comments();
	$tree_comments = build_tree($_comments);
	//print_arr($tree_comments);
	$comments = getCommentsTemplate($tree_comments);
	
	//require_once "functions/captcha/DMT-captcha-gen.php";
	//$captcha = new DMTcaptcha();
		
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Comments</title>
	<link rel="stylesheet" href="libs/font-awesome-4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/jquery.modal-window.css">
	<script src="js/jquery-1.11.2.min.js"></script>
	<script src="js/jquery.modal-window.js"></script>
	<script>
		$(function(){
			
			
			/*$.ajaxSetup({
				
				url: '/',
				type: "POST",
				dataType: "json"
				
			});*/
			
			//Очистка сообщений формы
			function clearMsg() {
				$('.form-order input[type=text], .form-order textarea').focus(function(){
					var id_field = $(this).attr('id');
					$('#msg-' + id_field).empty();
				});
			}
			
			$(".add-comments-btn, .recomment-link a").modalWindow({//Загрузка модального окна с формой
		
				orderType: 3, //3 - значит комментарии
				
				beforeOpen: function(){},
				
				afterOpen: function(){//после открытия формы иниц-руем событие
										//для отправки формы на сервер ajax'ом
					commentsProcess();
				}
				
			});
			
			function commentsProcess(order_type){//отправки формы на сервер ajax'ом

				
				orderTypeObj = order_type || 3;
				
				$('.btn-submit').on('click', function(){
					$(this).attr('disabled','disabled');
					//alert(orderTypeObj); return false;
					var customer = $('#cart_customer').val();
					var comment = $('#cart_comment').val();
					var email = $('#cart_email').val();
					var cinput = $('#cinput').val();
					
					$('.loader-form').fadeIn(200);
					//alert($.modalWindow.parentID);
					//return false;
					$.ajax({
						url: "/",
						type: "POST",
						dataType: "json",
						data: {'getCommentsProcess_a'  : true,
								 'cart_customer': customer, 
								 'cart_comment' : comment,
								 'cart_email'   : email,
								 'cinput'       : cinput,
								 'parentID'   	: $.modalWindow.parentID},
						success: commentsRes,
						error: function(){ 
									alert("Ошибка при добавлении комментария.\nПопробуйте позже!");
									//$('#cart-shudow').css({'display':'none'});
								}
					});
					
				});
			}

			//Оформление заказа (результат ajax-запроса)
			function commentsRes(res) {
				
				//alert(res); return false;
				
				$('.btn-submit').removeAttr('disabled');
				
				if (res.error === true && !res.errorComment) { //Вывод сообщений валидации
					$('.loader-form').fadeOut(100);
					$("span.msg-form").empty();
					$.each(res.arrMess, function(index){
						var mess = this.mess;
						$.each(this.fields, function(index){
							$("#msg-" + this).text(mess);
						});
					});
					clearMsg();
					return false;
				} else if (res.error === false) { //Если нет ошибок при заказе
					
					//alert(res.html);
					$.modalWindow.close(200);
					if (res.append == 'li') { //Если комментарий уже содержит дочерние комментарии
											  //то в ul добавляем li с дочерним комментарием
						//$(res.html).hide();
						$(res.html).hide().prependTo('#'+res.link +' ul').fadeIn(900);
						//alert(res.linkID);
						$('#'+res.linkID).modalWindow({
							orderType: 3,
							afterOpen: function(){
								commentsProcess();
							}
						});
					} else if (res.append == 'ul') {//Если комментарий не имеет потомков
													//то в li-родитель добавляем ul с дочерним комментарием
													//res.link - comment-container-<$comment_vars['parent_id']>
						$(res.html).hide().appendTo('#'+res.link).fadeIn(900);
						//$().append(res.html);
						//alert(res.link);
						$('#'+res.linkID).modalWindow({
							orderType: 3,
							afterOpen: function(){
								commentsProcess();
							}
						});
					}  else if (!res.append) { //Если комментарие не содержит ни родителей ни потомков
												// т.е. новый комментарий верхнего уровня, тогда добавляем
												// li с новы комметарием в корневой ul
						$(res.html).hide().prependTo('#'+res.link).fadeIn(900);
						//alert(res.linkID);
						$('#'+res.linkID).modalWindow({
							orderType: 3,
							afterOpen: function(){
								commentsProcess();
							}
						});
					}
					
				} else if (res.errorComment === true) { //На всякий случай (Если сервер не ответил) 
					
					if (res.mess) {
						alert(res.mess);
						return false;
					}
						
					alert("Ошибка при добавлении комментария.\nПопробуйте позже!");
					return false;
				}
			}
			
		});
	</script>
	<style>
		.recomment-link {
			text-align:right;
		}
	</style>
</head>
<body>
	<div class="add-comments-block clearfix">
		<button class="add-comments-btn" id="post-1_cid-0">Добавить комментарий</button>
	</div>
	<div class="comments_wrap">
	   <ul id="comment-container-0">
		    <?= $comments ?>
		</ul> 
	</div> 

</body>
</html>