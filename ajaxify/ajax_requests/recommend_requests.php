<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include_once '../../config/class/needy_class.php';
    include '../../config/class/recommend.class.php';
    $recommend = new recommend;

    if (isset($_POST['getFollowings'])) {
      $recommend->getFollowingsRecommend($_POST['getFollowings']);
    }

    if (isset($_GET['recommend'])) {
      // echo $_GET['get'];
      include '../../config/class/universal.class.php';

      $c = $recommend->recommendUser($_GET['recommend'], $_GET['get']);
      $c;
      $count = $recommend->getRecommends($_GET['get']);
      $array = array("count" => $count, "info" => $c);
      echo json_encode($array);
    }

  }
?>
