<?php
  ob_start();
  session_start();
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Activating your account</title>
    <link rel="shortcut icon" href="../../../../images/favicon/favicon.png" type="image/png">
  </head>

<?php

  include '../../../../config/class/universal.class.php';
  include '../../../../config/class/random.class.php';

  $universal = new universal;
  $random = new random;

  if (!isset($_GET['id']) || $universal->isLoggedIn()) {
    header("Location: /faiyaz/Instagram/");

  } else {

    try {
      $db = new PDO('mysql:host=127.0.0.1;dbname=instagram;charset=utf8mb4', 'root', 'iamaprogrammer');
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo $e->getMessage();
    }

    $id = $_GET['id'];

    $os = $random->getOS();
    $browser = $random->getBrowser();

    $ip_add;
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip_add = $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWADED_FOR'])) {
      $ip_add = $_SERVER['HTTP_X_FORWADED_FOR'];
    } else {
      $ip_add = $_SERVER['REMOTE_ADDR'];
    }

    $sinsert = $db->prepare("INSERT INTO login (user_id, ip, time, os, browser) VALUES (:id, :ip, now(), :os, :browser)");
    $sinsert->execute(array(":id" => $id, ":ip" => $ip_add, ":os" => $os, ":browser" => $browser));

    $_SESSION['id'] = $id;

    header("Location: /faiyaz/Instagram/");

  }

  include '../../../../index_include/index_footer.php';

?>
