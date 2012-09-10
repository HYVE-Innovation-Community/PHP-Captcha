<?php
session_start();
include('captcha.class.php');
$captcha = new Captcha();

$enteredCaptcha = $_POST['captcha'];

echo '<pre>';

var_dump($captcha->compareCaptcha($enteredCaptcha));
echo '</pre>';
?>
<a href="index.php">Zur&uuml;ck</a>