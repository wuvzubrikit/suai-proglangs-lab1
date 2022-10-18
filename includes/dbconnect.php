<?php

function dbConnect(): PDO
{
  static $DBH;

  if (!$DBH)
  {
    $config = include __DIR__.'/dbconfig.php';
    try
    {
      $DSN = "mysql:dbname=".$config['db_name'].";host=".$config['db_host'];
      $DBH = new PDO($DSN, $config['db_user'], $config['db_password']);
      $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) { echo $e->getMessage(); }
  }

  return $DBH;

}
