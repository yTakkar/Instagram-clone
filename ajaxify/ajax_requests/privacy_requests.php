<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include '../../config/class/settings.class.php';
    $settings = new settings;

    if ($_GET['emailPrivacy']) {
      $settings->changeEmailPrivacy($_GET['emailPrivacy']);
    }

    if ($_GET['mobilePrivacy']) {
      // $_GET['mobilePrivacy'];
      $settings->changeMobilePrivacy($_GET['mobilePrivacy']);
    }
  }

?>
