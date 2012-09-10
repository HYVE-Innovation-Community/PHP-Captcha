<?php
session_start();
include('captcha.class.php');

$captcha = new Captcha('FreeSans.ttf');
$captcha->generateImage();
?>
