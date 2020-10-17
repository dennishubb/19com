<?php
	include(__DIR__ . '/../classes/Captcha.class.php');

	$_vc = new Captcha();
	$_vc->doimg();

    $_SESSION['captcha'] = $_vc->getCode();
?>