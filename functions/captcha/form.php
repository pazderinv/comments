<?php
//Copyright DMTSOFT (c) - http://dmtsoft.ru
//Make by DMT 
session_start(); ?>
<form action="" method="post">
<p>Введите текст с картинки:</p>
<p><img src="./index.php?<?php echo session_name()?>=<?php echo session_id()?>">	
<!-- Активная, индексируемая ссылка на http://DMTSOFT.ru - обязательна!!!
	Если вы переделали механизм и есть ссылка _только_(одна ссылка) на DMTSOFT.ru, то имеете право добавить одну свою ссылку.
-->
</noindex><small><br><a href="http://DMTSoft.ru/"><b>Программирование C/Си++, PHP, Pascal, алгоритмы и исходники</b></a></small></p>

<p><input type="text" name="keystring"><input type="submit" value="Check"></p>
</form>
<?php
if(count($_POST)>0){
	if(isset($_SESSION['captcha_keystring']) && strtolower($_SESSION['captcha_keystring']) == strtolower($_POST['keystring'])){
		echo "Правильно";
	}else{
		echo "Ошибка - неправильный ввод числа";
	}
}
unset($_SESSION['captcha_keystring']);
?>