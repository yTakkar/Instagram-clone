<?php
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    if (isset($_GET['value'])) {
      $value = trim($_GET['value']);

      include_once '../../config/class/needy_class.php';
      include '../../config/class/login.class.php';
      $login = new login_class;

      $login->usernameChecker($value);
    }

  }
?>
