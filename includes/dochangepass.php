<?php

session_start();

//Input validation
$entered_password = hash_pbkdf2("sha256", filter_var(trim($_POST['cur_pass']), FILTER_SANITIZE_STRING), $_SESSION['user']['salt'], 1000, 32);
if (strcmp($entered_password, $_SESSION['user']['password']) != 0)
{
  $_SESSION['error-message'] = "You entered wrong password.";
  header("Location: ../change_password.php");
  exit();
}

if (strlen($_POST['new_pass']) < 8)
{
  $_SESSION['error-message'] = "Password must be at least 8 characters.";
  header("Location: ../change_password.php");
  exit();
}

if (strcmp($_POST['new_pass'], $_POST['new_pass_confirmation']) != 0)
{
  $_SESSION['error-message'] = "Enetered passwords don't match.";
  header("Location: ../change_password.php");
  exit();
}

if (strcmp($_POST['cur_pass'], $_POST['new_pass']) == 0)
{
  $_SESSION['error-message'] = "Passwords must be distinctive.";
  header("Location: ../change_password.php");
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

if ($_SESSION['settings-message'])
{
  unset($_SESSION['settings-message']);
}

$_SESSION['warning-message'] = "Reauthorization is needed.";

header("Location: /includes/dologout.php");
