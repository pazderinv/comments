<?php defined('WEBIN4') or die(' '); ?>

<?php
	/*require_once "functions/captcha/DMT-captcha-gen.php";*/
	//$captcha = new DMTcaptcha();
?>
<div class="popup form-main">
	<div class="form-header">
		<h4>Добавить комментарий</h4>
	</div>
	<div class="form-block">
		<form action="" method="POST" class="form-order">
			<div class="field-block">
				<div class="field-header">
					<label for="cart_customer">Ваше имя *</label>
					<span class="msg-form" id="msg-cart_customer"></span>
				</div>
				<input type="text" name="customer" id="cart_customer" value="" />
			</div>
			<div class="field-block">
				<div class="field-header">
					<label for="cart_email">Email</label>
					<span class="msg-form" id="msg-cart_email"></span>
				</div>
				<input type="text" name="email" value="" id="cart_email"/>
			</div>
			<div class="field-block">
				<div class="field-header">
					<label for="cart_comment">Комментарий</label>
					<span class="msg-form" id="msg-cart_comment"></span>
				</div>
				<textarea name="comment" id="cart_comment" cols="30" rows="10"></textarea>
			</div>
			<div class="field-block">
				<div class="field-header">
					<label for="cart_address">Код с картинки *</label>
					<span class="msg-form" id="msg-cinput"></span>
				</div>
				<style>
					.captcha-block {
						display:table-cell;
					}
					.captcha-block > input {
						display:inline;
						vertical-align:middle;
						width:25% !important;
					}
					.captcha-block img {
						vertical-align:middle;
						display:inline;
						margin:0px 5px 0 10px;
					}
					.reload-captcha {
						vertical-align:middle;
						color:#555;
						font-size:16px;
						cursor:pointer;
						/*line-height:60px;*/
					}
					
				</style>
				<div class="captcha-block">

					<input type="text" name="keystring" id="cinput" value="">
					<img id="captcha-img" src="../functions/captcha/DMT-captcha-gen.php?<?php echo session_name()?>=<?php echo session_id()?>">
					<span onclick="document.getElementById('captcha-img').src='http://comments.loc/functions/captcha/DMT-captcha-gen.php?<?php echo session_name(); ?>=<?php echo session_id(); ?>&rnd='+ Math.random(); return false;" class="reload-captcha">
						<i class="fa fa-refresh"></i>
					</span>

				</div>
			</div>
		</form>
		<script>
			/*$('#cinput').click(function(){
				//alert(111);
				$.post('/', {'check':true}, function(res){alert(res)});
			});*/
		</script>
		<div class="form-btn">
			<button class="form-close btn-close">&times;</button>
			<button class="form-submit btn-submit">Отправить</button>
		</div>
	</div>
	<img src="<?= SITE ?>loader.gif" alt="" class="loader-form">
</div>
<?php unset($_SESSION['captcha_keystring']); ?>