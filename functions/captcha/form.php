<?php
//Copyright DMTSOFT (c) - http://dmtsoft.ru
//Make by DMT 
session_start(); ?>
<form action="" method="post">
<p>������� ����� � ��������:</p>
<p><img src="./index.php?<?php echo session_name()?>=<?php echo session_id()?>">	
<!-- ��������, ������������� ������ �� http://DMTSOFT.ru - �����������!!!
	���� �� ���������� �������� � ���� ������ _������_(���� ������) �� DMTSOFT.ru, �� ������ ����� �������� ���� ���� ������.
-->
</noindex><small><br><a href="http://DMTSoft.ru/"><b>���������������� C/��++, PHP, Pascal, ��������� � ���������</b></a></small></p>

<p><input type="text" name="keystring"><input type="submit" value="Check"></p>
</form>
<?php
if(count($_POST)>0){
	if(isset($_SESSION['captcha_keystring']) && strtolower($_SESSION['captcha_keystring']) == strtolower($_POST['keystring'])){
		echo "���������";
	}else{
		echo "������ - ������������ ���� �����";
	}
}
unset($_SESSION['captcha_keystring']);
?>