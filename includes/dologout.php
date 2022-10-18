<?php
  session_start();

  unset($_SESSION['user']);
  unset($_COOKIE['usersession']);
  setcookie('usersession', null, -1, '/');

  header("Location: /index.php");
  exit();
