<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include '../../config/classesGetter.php';
    $hashtag = new hashtag;

    if (isset($_GET['hahstagFeeds'])) {
      $hashtag->hashtaggedPost($_GET['hashtag'], "ajax", $_GET['hahstagFeeds']);
    }

  }
?>
