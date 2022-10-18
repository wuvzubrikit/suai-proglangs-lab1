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
$STH = dbConnect()->prepare("SELECT * FROM `user-data` WHERE `username` = :username");
$STH->bindParam(':username', $_POST['username']);
$STH->execute();
$_SESSION['user'] = $STH->fetch(PDO::FETCH_ASSOC);

//Check for existing users
if (!$_SESSION['user'])
{
  unset($_SESSION['user']);
  $_SESSION['error-message'] = "Wrong username.";
  header("Location: ../login.php");
  exit();
}

//Password check
$entered_password = hash_pbkdf2("sha256", filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING), $_SESSION['user']['salt'], 1000, 32);
if (strcmp($entered_password, $_SESSION['user']['password']) != 0)
{
  unset($_SESSION['user']);
  $_SESSION['error-message'] = "Wrong password.";
  header("Location: ../login.php");
  exit();
}

//Check password for expiration deadline
date_default_timezone_set('Europe/Moscow');
if (strtotime($_SESSION['user']['password_expiration']) - time() < 0)
  $_SESSION['settings-message'] = "Your password is expired. Please, change your password.";

//Cookie handling
$_SESSION['user_startup_time'] = time();
$_SESSION['user_token'] = hash("sha256", $_SESSION['user']['id'].$_SESSION['user']['username']);
setcookie("usersession", hash("sha256", $_SESSION['user_startup_time'].$_SESSION['user_token']), $_SESSION['user_startup_time'] + 3600, "/");
unset($_POST['username'], $_POST['password']);

header("Location: ../profile.php");
