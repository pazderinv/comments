<?php

require_once "../functions/captcha/DMT-captcha-gen.php";
$captcha = new DMTcaptcha();

?>
<span onclick="document.getElementById('captcha-img').src='http://comments.loc/functions/captcha/DMT-captcha-gen.php?<?php echo session_name(); ?>=<?php echo session_id(); ?>&rnd='+ Math.random(); return false;" class="reload-captcha"><i class="fa fa-refresh" aria-hidden="true"></i></span>