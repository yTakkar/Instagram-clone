<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include_once '../../config/classesGetter.php';
    include '../../config/class/edit_profile.class.php';
    $edit = new editProfile;

    if (isset($_POST['username'])) {
      $username = preg_replace("#[<> ]#i", "", $_POST['username']);
      $firstname = preg_replace("#[<> ]#i", "", $_POST['firstname']);
      $surname = preg_replace("#[<> ]#i", "", $_POST['surname']);
      $bio = preg_replace("#[<>]#i", "", $_POST['bio']);
      $instagram = preg_replace("#[<>]#i", "", $_POST['instagram']);
      $youtube = preg_replace("#[<>]#i", "", $_POST['youtube']);
      $facebook = preg_replace("#[<>]#i", "", $_POST['facebook']);
      $twitter = preg_replace("#[<>]#i", "", $_POST['twitter']);
      $website = preg_replace("#[<>]#i", "", $_POST['website']);
      $mobile = preg_replace("#[^0-9]#i", "", $_POST['mobile']);
      $tags = preg_replace("#[\s]#", "-", $_POST['tags']);

      $session = $_SESSION['id'];

      $m=$edit->saveProfileEditing($username, $firstname, $surname, $bio, $instagram, $youtube, $facebook, $twitter, $website, $mobile, $tags);
      $array = array("mssg" => $m);
      echo json_encode($array);
    }

    if(isset($_POST['resend_vl'])){
      $m = $edit->resend_vl();
      $array = array("mssg" => $m);
      echo json_encode($array);
    }

  }
?>
