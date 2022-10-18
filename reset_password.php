<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require_once __DIR__."/includes/utils.php";
?>

<!doctype html>
<html lang="ru" dir="ltr">
<?php
if (!$_GET['token'] || $_COOKIE['usersession'])
{
  header('HTTP/1.1 403 Forbidden');
  ?>
  <head>
    <!-- <meta http-equiv="refresh" content="2; /index.php" /> -->
    <h1 align="center">403 Forbidden</h1>
  </head>
  <?php
  exit();
}

if ($_SESSION['user']['reset_token_end'] - time() < 0)
{
  unset($_SESSION['user']);
  $_SESSION['error-message'] = "Time to reset password is over.";
  header("Location: /login.php");
  exit();
}

if (strcmp($_GET['token'], hash("md5", session_id().$_SESSION['user']['username'].$_SESSION['user']['reset_token_start'])) != 0)
{
  unset($_SESSION['user']);
  header("Location: /login.php");
  exit();
}

$_SESSION['user']['pass_reset_token'] = $_GET['token'];
?>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/css/main.css">
  </head>
  <body>
    <div>
      <form action="/includes/doresetpass.php" method="post">
        <input type="password" name="new_pass"
        placeholder="Enter new password"><br>
        <input type="password" name="new_pass_confirmation"
        placeholder="Confirm new password"><br>

        <button type="submit">Submit</button>
      </form>

      <p>
        <a href="/login.php">Back</a>
      </p>

      <?php
      PrintSessionMessagesU();
      ?>

    </div>
  </body>
</html>
