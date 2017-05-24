<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include '../../config/classesGetter.php';
    $follow = new follow_system;

    if (isset($_GET['follow'])) {
      $f = $follow->follow($_GET['follow']);
      $f;
      $followers = $follow->getFollowers($_GET['updateid']);
      $followings = $follow->getFollowings($_GET['updateid']);
      $array = array(
        'followers' => $followers,
        'followings'=> $followings,
        'status' => $f
      );
      echo json_encode($array);
    }

    if (isset($_GET['unfollow'])) {
      $follow->unfollow($_GET['unfollow']);
      $followers = $follow->getFollowers($_GET['updateid']);
      $followings = $follow->getFollowings($_GET['updateid']);
      $array = array(
        'followers' => $followers,
        'followings'=> $followings
      );
      echo json_encode($array);
    }

    if (isset($_GET['followers'])) {
      $follow->followers($_GET['followers']);
    }

    if (isset($_GET['followings'])) {
      $follow->followings($_GET['followings']);
    }

    if (isset($_GET['viewCounter'])) {
      $follow->viewCounter($_GET['viewCounter']);
    }

    if (isset($_GET['pro_viewers'])) {
      $follow->profile_viewers($_GET['pro_viewers']);
    }

    if (isset($_GET['simple_unfollow'])) {
      // echo $_GET['simple_unfollow'];
      $follow->simpleUnfollow($_GET['simple_unfollow']);
    }

    if (isset($_GET['followersFeeds'])) {
      $follow->profileFollowers($_GET['followersUser'], "ajax", $_GET['followersFeeds']);
    }

    if (isset($_GET['followingsFeeds'])) {
      $follow->profileFollowings($_GET['followingsUser'], "ajax", $_GET['followingsFeeds']);
    }

  }
?>
