<?php

session_start();

//Input validation
if (strlen($_POST['new_pass']) < 8)
{
  $_SESSION['error-message'] = "Password must be at least 8 characters.";
  header("Location: /reset_password.php?token=".$_SESSION['user']['pass_reset_token']);
  exit();
}

if (strcmp($_POST['new_pass'], $_POST['new_pass_confirmation']) != 0)
{
  $_SESSION['error-message'] = "Enetered passwords don't match.";
  header("Location: /reset_password.php?token=".$_SESSION['user']['pass_reset_token']);
  exit();
}

//Replacing old password in database
$salt = bin2hex(random_bytes(16));
$password = hash_pbkdf2("sha256", filter_var(trim($_POST['new_pass']), FILTER_SANITIZE_STRING), $salt, 1000, 32);
date_default_timezone_set('Europe/Moscow');
$date = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m")+3, date("d"), date("Y")));

require_once __DIR__.'/dbconnect.php';

$STH = dbConnect()->prepare("UPDATE `user-data` SET `salt`=:salt,`password`=:password,`password_expiration`=:expiration WHERE `username`=:username");
$STH->bindParam(':username', $_SESSION['user']['username']);
$STH->bindparam(':salt', $salt);
$STH->bindParam(':password', $password);
$STH->bindParam(':expiration', $date);
$STH->execute();

$_SESSION['success-message'] = "Password has changed succesfully.";
unset($_SESSION['user']);

header("Location: /login.php");
