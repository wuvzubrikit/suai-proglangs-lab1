<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require_once __DIR__."/includes/utils.php";
?>

<!doctype html>
<html lang="ru" dir="ltr">
<?php
if (CookieIsUnauthorized())
  exit();
else
  if (CookieIsInvalid())
    exit();
?>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/css/main.css">
  </head>
  <body>
    <div>
      <h1>This is <?php echo $_SESSION['user']['username']?>'s profile</h1>
      <p>
        <a href="/includes/dologout.php">Log out</a>
      </p>
      <p>
        <a href="/change_password.php">Change password</a>
      </p>
      <?php
      PrintSessionMessagesA();
      ?>
    </div>
  </body>
</html>
