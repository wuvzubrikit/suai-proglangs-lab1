<?php

session_start();

//Input validation
if (strlen($_POST['username']) < 4 || strlen($_POST['username'] > 32))
{
  $_SESSION['error-message'] = "Username must be between 4 and 32 symbols length";
  header("Location: ../signup.php");
  exit();
}

if (!$_POST['email'])
{
  $_SESSION['error-message'] = "Email required";
  header("Location: ../signup.php");
  exit();
}

if (strlen($_POST['password']) < 8)
{
  $_SESSION['error-message'] = "Password must be at least 8 characters.";
  header("Location: ../signup.php");
  exit();
}

if (strcmp($_POST['password'], $_POST['password_confirmation']) != 0)
{
  $_SESSION['error-message'] = "Passwords don't match.";
  header("Location: ../signup.php");
  exit();
}

//Fetch user from database
require_once __DIR__.'/dbconnect.php';
$STH = dbConnect()->prepare("SELECT * FROM `user-data` WHERE `username` = :username");
$STH->bindParam(':username', $_POST['username']);
$STH->execute();

//Check for existing users
if ($STH->fetch(PDO::FETCH_ASSOC))
{
    $_SESSION['error-message'] = "This username is busy.";
    header("Location: ../signup.php");
    exit();
}

//Adding new user
$user = array(
  'username' => filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING),
  'password' => filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING),
  'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_STRING)
);

//Creating pseudo e-mail box
mkdir($_SERVER['DOCUMENT_ROOT']."/emails/".$_POST['email']);

$salt = bin2hex(random_bytes(16));
$password = hash_pbkdf2("sha256", $user['password'], $salt, 1000, 32);
date_default_timezone_set('Europe/Moscow');
$date = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m")+3, date("d"), date("Y")));

$STH = dbConnect()->prepare("INSERT INTO  `user-data` (`username`, `password`, `salt`, `password_expiration`, `email`) VALUES (:username, :password, :salt, :expiration, :email)");
$STH->bindParam(':username', $user['username']);
$STH->bindparam(':salt', $salt);
$STH->bindParam(':password', $password);
$STH->bindParam(':expiration', $date);
$STH->bindParam(':email', $user['email']);
$STH->execute();
unset($user, $salt, $password, $date);

header("Location: ../login.php");
