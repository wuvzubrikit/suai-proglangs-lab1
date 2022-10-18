<?php

session_start();

//Input validation
if (strlen($_POST['username']) < 4 || strlen($_POST['username'] > 32))
{
  $_SESSION['error-message'] = "Username must be between 4 and 32 symbols length";
  header("Location: ../login.php");
  exit();
}

//Fetch user from database
require_once __DIR__.'/dbconnect.php';
$STH = dbConnect()->prepare("SELECT  `username`, `email` FROM `user-data` WHERE `username` = :username");
$STH->bindParam(':username', $_POST['username']);
$STH->execute();
$_SESSION['user'] = $STH->fetch(PDO::FETCH_ASSOC);

//Check for existing users
if (!$_SESSION['user'])
{
  unset($_SESSION['user']);
  $_SESSION['error-message'] = "Wrong username.";
  header("Location: ../forgot_password.php");
  exit();
}

if ($_SESSION['user']['email'] != $_POST['email'])
{
  unset($_SESSION['user']);
  $_SESSION['error-message'] = "Wrong email.";
  header("Location: ../forgot_password.php");
  exit();
}

//Creating reset token
$_SESSION['user']['reset_token_start'] = time();
$_SESSION['user']['reset_token_end'] = $_SESSION['user']['reset_token_start'] + 600;
$token = hash("md5", session_id().$_SESSION['user']['username'].$_SESSION['user']['reset_token_start']);

//Creating an e-mail
if (!is_dir($_SERVER['DOCUMENT_ROOT']."/emails/".$_POST['email']))
  mkdir($_SERVER['DOCUMENT_ROOT']."/emails/".$_POST['email']);

date_default_timezone_set('Europe/Moscow');
if (!$mail = fopen($_SERVER['DOCUMENT_ROOT']."/emails/".$_POST['email']."/".date("Y-m-d-H-i-s", time()).".txt", 'w'))
{
  unset($_SESSION['user']);
  $_SESSION['error-message'] = "Failed to send an email.";
  header("Location: ../forgot_password.php");
  exit();
}

fwrite($mail, "To reset password follow this link: http://localhost/reset_password.php?token=".$token);
fclose($mail);

unset($token);
$_SESSION['success-message'] = "Instructions to reset password were sent to an email";

header("Location: ../login.php");
