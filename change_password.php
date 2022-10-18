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
      <form action="/includes/dochangepass.php" method="post">
        <input type="password" name='cur_pass'
        placeholder="Enter current password"><br>
        <input type="password" name='new_pass'
        placeholder="Enter new password"><br>
        <input type="password" name='new_pass_confirmation'
        placeholder="Confirm new password"><br>

        <button type="submit">Submit</button>
      </form>

      <p>
        <a href="/profile.php">Back</a>
      </p>

      <?php
      PrintSessionMessagesA()
      ?>

    </div>
  </body>
</html>
