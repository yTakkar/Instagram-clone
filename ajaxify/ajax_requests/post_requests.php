<?php
  include '../../config/declare.php';
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include '../../config/classesGetter.php';
    $post = new post;

    if (isset($_POST['text'])) {
      $value = preg_replace("#[<>]#i", "", $_POST['text']);
      $tags = $_POST['tags'];
      $font = preg_replace("#[^0-9]#i", "", $_POST['font']);
      $post->textPost($value, $tags, $font, $_POST['loc'], $_POST['tpwhen'], $_POST['tpgrp']);
    }

    if (isset($_FILES['image_post'])) {
      $value = preg_replace("#[<>]#i", "", $_POST['value']);
      $post->imagePost($_FILES['image_post'], $value, $_POST['tags'], $_POST['font'], $_POST['loc'], $_POST['filter'], $_POST['ipwhen'], $_POST['ipgrp']);
    }

    if (isset($_FILES['video_post'])) {
      $value = preg_replace("#[<>]#i", "", $_POST['value']);
      $post->videoPost($_FILES['video_post'], $value, $_POST['tags'], $_POST['font'], $_POST['loc'], $_POST['vpwhen'], $_POST['vpgrp']);
    }

    if (isset($_FILES['audio_post'])) {
      $value = preg_replace("#[<>]#i", "", $_POST['value']);
      echo $post->audioPost($_FILES['audio_post'], $value, $_POST['tags'], $_POST['font'], $_POST['loc']);
    }

    if (isset($_FILES['doc_post'])) {
      $value = preg_replace("#[<>]#i", "", $_POST['value']);
      $post->docPost($_FILES['doc_post'], $value, $_POST['tags'], $_POST['font'], $_POST['loc'], $_POST['dpwhen'], $_POST['dpgrp']);
    }

    if (isset($_POST['src'])) {
      $value = preg_replace("#[<>]#i", "", $_POST['value']);
      $tags = $_POST['tags'];
      $font = preg_replace("#[^0-9]#i", "", $_POST['font']);
      $address = preg_replace("#[^a-z0-9, ]#i", "", $_POST['loc']);
      $post->locPost($_POST['src'], $value, $tags, $font, $address, $_POST['loc_when'], $_POST['loc_grp']);
    }

    if (isset($_POST['value'])) {
      // $value = preg_replace("#[<>]#i", "", $_POST['value']);
      $value = $_POST['value'];
      echo $post->getLink($value);
    }

    if (isset($_POST['link_url'])) {
      $value = preg_replace("#[<>]#i", "", $_POST['link_text']);
      $tags = $_POST['link_tags'];
      $font = preg_replace("#[^0-9]#i", "", $_POST['link_font']);
      $title = preg_replace("#[<>]#i", "", $_POST['link_title']);
      $url = preg_replace("#[<>]#i", "", $_POST['link_url']);
      $src = preg_replace("#[<>]#i", "", $_POST['link_src']);
      $post->linkPost($value, $tags, $font, $_POST['link_loc'], $url, $title, $src, $_POST['link_when'], $_POST['link_grp']);
    }

    if (isset($_GET['delete_post'])) {
      $session = $_SESSION['id'];
      $post->deletePost($_GET['delete_post']);
      $z = $post->postCount($session);
      $x = array('posts' => $z);
      echo json_encode($x);
    }

    if (isset($_GET['feedAtBottom'])) {
      $post->getHomePost("get", "limit", $_GET['feedAtBottom']);
    }

    if (isset($_GET['userFeeds'])) {
      $post->getUserPost($_GET['userForFeeds'], "ajax", $_GET['userFeeds']);
    }

    if (isset($_GET['tagFeeds'])) {
      $post->getTaggedPost($_GET['tagForFeeds'], "ajax", $_GET['tagFeeds']);
    }

    if (isset($_GET['shareFeeds'])) {
      $post->getSharedPost($_GET['shareForFeeds'], "ajax", $_GET['shareFeeds']);
    }

    if (isset($_GET['bookmarkFeeds'])) {
      $post->getBookmarksPost("ajax", $_GET['bookmarkFeeds']);
    }

  }
?>
