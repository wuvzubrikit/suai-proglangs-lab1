<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require_once __DIR__."/includes/utils.php";
?>

<!doctype html>
<html lang="ru" dir="ltr">
<?php
if (CookieIsResourceForbidden())
  exit();
?>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/css/main.css">
  </head>
  <body>
    <div>
      <form action="/includes/doresetprep.php" method="post">
        <input type="text" name="username" placeholder="Enter username"><br>
        <input type="email" name="email" placeholder="Enter email"><br>
        <button type="submit">Reset password</button>
    		<p>
    			<a href="/login.php">Back</a>
    		</p>
        <?php
        PrintSessionMessagesU();
        ?>
      </form>
    </div>
  </body>
</html>
