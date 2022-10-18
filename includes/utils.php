<?php

function CookieIsResourceForbidden()
{
  if ($_COOKIE['usersession'])
  {
    header('HTTP/1.1 403 Forbidden');
    ?>
    <head>
      <h1 align="center">403 Forbidden</h1>
    </head>
    <?php
    return 1;
  }
  return 0;
};

  function CookieIsUnauthorized()
{
  if (!$_COOKIE['usersession'])
  {
    header('HTTP/1.1 401 Unauthorized');
    ?>
    <head>
      <h1 align="center">401 Unauthorized</h1>
    </head>
    <?php
    return 1;
  }
  return 0;
};

function CookieIsInvalid()
{
  if (strcmp($_COOKIE['usersession'], hash("sha256", $_SESSION['user_startup_time'].$_SESSION['user_token'])) != 0)
  {
    unset($_SESSION['user']);
    unset($_COOKIE['usersession']);
    setcookie('usersession', null, -1, '/');

    header("Location: /index.php");
    return 1;
  }
  return 0;
}

function PrintSessionMessagesU()
{
  if ($_SESSION['error-message'])
  {
    echo '<p class="error">'.$_SESSION['error-message'].'</p>';
    unset($_SESSION['error-message']);
  }
  if ($_SESSION['warning-message'])
  {
    echo '<p class="warning">'.$_SESSION['warning-message'].'</p>';
    unset($_SESSION['warning-message']);
  }
  if ($_SESSION['success-message'])
  {
    echo '<p class="success">'.$_SESSION['success-message'].'</p>';
    unset($_SESSION['success-message']);
  }
};

function PrintSessionMessagesA()
{
  if ($_SESSION['error-message'])
  {
    echo '<p class="error">'.$_SESSION['error-message'].'</p>';
    unset($_SESSION['error-message']);
  }
  if ($_SESSION['warning-message'])
  {
    echo '<p class="warning">'.$_SESSION['warning-message'].'</p>';
    unset($_SESSION['warning-message']);
  }
  if ($_SESSION['success-message'])
  {
    echo '<p class="success">'.$_SESSION['success-message'].'</p>';
    unset($_SESSION['success-message']);
  }
  if ($_SESSION['settings-message'])
  {
    echo '<p class="warning">'.$_SESSION['settings-message'].'</p>';
  }
};
?>
