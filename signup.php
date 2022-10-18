<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require_once __DIR__."/includes/utils.php";
?>

<!doctype html>
<html lang="ru" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/css/main.css">
  </head>
  <body>
    <?php
    if ($_COOKIE['usersession'])
      header("Location: /profile.php");
    ?>
    <div>
      <h1>Sign Up</h1>
      <form action="/includes/dosignup.php" method="post">
        <input type="text" name='username'
        placeholder="Enter username"><br>
        <input type="email" name='email'
        placeholder="Enter email"><br>
        <input type="password" name='password'
        placeholder="Enter password"><br>
        <input type="password" name='password_confirmation'
        placeholder="Confirm password"><br>
        <button type="submit">Sign Up</button><br>
        <p>
          Or <a href="/login.php">log in</a>
        </p>
        <?php
        PrintSessionMessagesU();
        ?>
      </form>
    </div>
  </body>
</html>
