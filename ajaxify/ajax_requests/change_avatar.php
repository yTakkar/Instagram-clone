<?php
  session_start();

  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    include_once '../../config/class/needy_class.php';
    include '../../config/class/avatar.class.php';
    include '../../config/class/general.class.php';
    $avatar = new Avatar;
    $general = new general;

    if (isset($_GET['change_avatar'])) {
      if($_GET['change_when'] == "group"){
        $a = $_GET['change_grp'];
      } else if ($_GET['change_when'] == "user") {
        $a = "";
      }
      $avatar->deleteAvatars($_GET['change_when'], $a);
      echo $avatar->copyAvatar($_GET['change_avatar'], $_GET['change_when'], $a);
    }

    if (isset($_FILES['pro_ch_ava'])) {
      echo $avatar->uploadedAndResize();
    }

    if (isset($_POST['top']) && isset($_POST['left']) && isset($_POST['width']) && isset($_POST['height']) && isset($_POST['name'])) {
      if ($_POST['upload_when'] == "group") {
        $b = $_POST['upload_grp'];
      } else if ($_POST['upload_when'] == "user") {
        $b = "";
      }
      echo $avatar->cropAvatar($_POST['upload_when'], $b);
    }

  }

?>
