<?php 

	define('WEBIN4', true);
	
	define('SITE', "http://comments.loc/");
	
	session_start();
	//echo session_name();
	//echo session_id();
	//unset($_SESSION['captcha_keystring']);
	//$_SESSION['captcha_keystring'] = "123465789";
	
	require_once "functions/functions.php";
		
	
	
	if ($_POST['loadCommentsForm_a']) {
		//exit("******");
		loadCommentsForm();
	}
	
	if ($_POST['getCommentsProcess_a']) {
		//exit("******");
		
		list($postid_str, $parentid_str) = explode("_", $_POST['parentID']);
		list(,$postid) = explode("-", $postid_str);
		list(,$parentid) = explode("-", $parentid_str);
		
		$comments_data = array(
								'cart_customer'=>$_POST['cart_customer'],
								'cart_comment'=>$_POST['cart_comment'],
								'cart_email'=>$_POST['cart_email'],
								'cinput'=>$_POST['cinput'],
								'post_id'=>clear_admin($postid, "int"),
								'parent_id'=>clear_admin($parentid, "int"),
								'link_id'=>$_POST['parentID']
								);
								
		getCommentsProcess($comments_data);
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
			
			//Очистка сообщений формы заказа
			function clearMsg() {
				$('.form-order input[type=text], .form-order textarea').focus(function(){
					var id_field = $(this).attr('id');
					$('#msg-' + id_field).empty();
				});
			}
			
			$(".add-comments-btn, .recomment-link a").modalWindow({
		
				orderType: 3,
				
				beforeOpen: function(){},
				
				afterOpen: function(){
					commentsProcess();
				}
				
			});
			
			function commentsProcess(order_type){
				
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
					if (res.append == 'li') {
						//$(res.html).hide();
						$(res.html).hide().prependTo('#'+res.link +' ul').fadeIn(900);
						//alert(res.linkID);
						$('#'+res.linkID).modalWindow({
							orderType: 3,
							afterOpen: function(){
								commentsProcess();
							}
						});
					} else if (res.append == 'ul') {
						$(res.html).hide().appendTo('#'+res.link).fadeIn(900);
						$().append(res.html);
						//alert(res.linkID);
						$('#'+res.linkID).modalWindow({
							orderType: 3,
							afterOpen: function(){
								commentsProcess();
							}
						});
					}  else if (!res.append) {
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