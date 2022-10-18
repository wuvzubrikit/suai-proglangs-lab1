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
      <h1>Log In</h1>
      <form action="/includes/dologin.php" method="post">
        <input type="text" name='username'
        placeholder="Enter username"><br>
        <input type="password" name='password'
        placeholder="Enter password"><br>
        <button type="submit">Log In</button><br>
        <p>
          Or <a href="/signup.php">sign up</a>
        </p>
        <p>
          <a href="/forgot_password.php">Forgot password?</a>
        </p>
        <?php
        PrintSessionMessagesU();
        ?>
      </form>
    </div>
  </body>
</html>
