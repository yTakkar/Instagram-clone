<?php
  include '../../../../config/declare.php';
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Activating your account</title>
    <link rel="shortcut icon" href="../../../../images/favicon/favicon.png" type="image/png">
  </head>

<?php

  include_once '../../../../config/class/needy_class.php';
  include '../../../../config/class/universal.class.php';
  include '../../../../config/class/login.class.php';
  include '../../../../config/class/settings.class.php';

  $universal = new universal;
  $login = new login_class;
  $settings = new settings;

  if (!isset($_GET['id'])) {
    header("Location: ".DIR);
  }

  if (!$universal->e_verified($_SESSION['id']) || isset($_GET['id']) != isset($_SESSION['id'])) {

    $db = N::_DB();
    $id = $_GET['id'];

    $query = $db->prepare("UPDATE users SET email_activated = :act WHERE id = :user");
    $query->execute(array(":act" => "yes", ":user" => $id));

    $_SESSION['id'] = $id;
    
    header("Location: ". DIR. "/activated");

  } else {
    header("Location: ".DIR);
  }

  include '../../../../index_include/index_footer.php';

?>
