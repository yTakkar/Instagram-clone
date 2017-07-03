<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {
    if (isset($_POST['input'])) {

      include_once '../../config/class/needy_class.php';
      include '../../config/class/forgot.class.php';

      $forgot = new forgot;
      echo $forgot->retrieve($_POST['input']);

    }
  }
?>
