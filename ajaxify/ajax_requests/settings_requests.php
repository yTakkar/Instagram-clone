<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include_once '../../config/class/needy_class.php';
    include '../../config/class/settings.class.php';
    $settings = new settings;

    if (isset($_POST['change_password'])) {
      echo $settings->changePassword($_POST['current'], $_POST['new'], $_POST['new_again']);
    }

    if (isset($_GET['accountType'])) {
      $value = preg_replace("#[^a-z]#i", "", $_GET['accountType']);
      echo $settings->changeAccountType($value);
    }

    if (isset($_GET['block'])) {
      $settings->block($_GET['block']);
    }

    if (isset($_GET['unblock'])) {
      // echo $_GET['unblock'];
      $settings->unblock($_GET['unblock']);
    }

    if (isset($_POST['dltAcc'])) {
      $dlt = $settings->deleteAccount($_POST['dltAcc']);
      $dlt;
      $array = array("dlt" => $dlt);
      echo json_encode($array);
    }

  }
?>
