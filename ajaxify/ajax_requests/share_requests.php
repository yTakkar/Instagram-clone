<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include_once '../../config/class/needy_class.php';
    include '../../config/class/share.class.php';
    $share = new share;

    if (isset($_POST['getFollowings'])) {
      $share->getFollowingsShare($_POST['getFollowings']);
    }

    if (isset($_POST['to'])) {
      $to = preg_replace("#[^0-9]#i", "", $_POST['to']);
      $post = preg_replace("#[^0-9]#i", "", $_POST['post']);

      // include '../../config/class/universal.class.php';

      $s = $share->postShare($to, $post);
      $s;
      $shares = $share->getShares($post);

      $array = array('shares' => $shares, 's' => $s);
      echo json_encode($array);
    }

    if (isset($_POST['posting'])) {
      $posting = preg_replace("#[^0-9]#i", "", $_POST['posting']);
      $share->sharers($posting);
    }

    if (isset($_GET['unshare'])) {
      $share->unshare($_GET['unshare']);
      $s = $share->getShares($_GET['unshare']);
    }

    if (isset($_POST['getShareTos'])) {
      // echo $_GET['getShareTos'];
      $share->getShareTos($_POST['getShareTos']);
    }

    if (isset($_GET['remove_share'])) {
      // echo $_GET['remove_share'];
      // echo $_GET['post'];
      $share->removeShare($_GET['post'], $_GET['remove_share']);
      $shares = $share->getShares($_GET['post']);
      $array = array("shares" => $shares);
      echo json_encode($array);
      // print_r($array);
    }

  }
?>
