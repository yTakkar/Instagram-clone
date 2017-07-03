<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include_once '../../config/class/needy_class.php';
    include '../../config/class/post_comment.class.php';
    $comment = new postComment;

    if (isset($_GET['value'])) {
      include '../../config/class/universal.class.php';
      $value = trim(preg_replace("#[<>]#i", "", $_GET['value']));
      $post = preg_replace("#[^0-9]#i", "", $_GET['post']);

      $f = $comment->comment($value, $post);
      $f;
      $comments = $comment->getComments($post);

      $array = array('st' => $f, 'comments' => $comments);
      echo json_encode($array);
    }

    if (isset($_FILES['image_comment'])) {
      $file = $_FILES['image_comment'];
      $post = $_POST['post'];
      $com = $comment->imageComment($file, $post);
      $com;
      $comments = $comment->getComments($post);
      $array = array("message" => $com, "comments" => $comments);
      echo json_encode($array);
      // print_r();
    }

    if (isset($_GET['likeComment'])) {
      $comment->likeComments($_GET['likeComment']);
      $likes = $comment->comment_likes($_GET['likeComment']);
      $array = array('likes' => $likes);
      echo json_encode($array);
    }

    if (isset($_GET['unlikeComment'])) {
      $comment->unlikeComments($_GET['unlikeComment']);
      $likes = $comment->comment_likes($_GET['unlikeComment']);
      $array = array('likes' => $likes);
      echo json_encode($array);
    }

    if (isset($_GET['commentLikers'])) {
      $comment->commentLikers($_GET['commentLikers']);
    }

    if (isset($_GET['delete_comment'])) {
      include '../../config/class/universal.class.php';

      $comment->deleteComment($_GET['delete_comment']);
      $com = $comment->getComments($_GET['post']);

      $array = array('comments' => $com);
      echo json_encode($array);
    }

    if (isset($_GET['cedit']) == "yes") {
      $text = preg_replace("#[<>]#i", "", $_GET['text']);
      $comment->editComment($text, $_GET['comment'], $_GET['post']);
    }

    if (isset($_GET['commSticker'])) {
      $comment->commSticker($_GET['commSticker'], $_GET['commStickerPost']);
      $com = $comment->getComments($_GET['commStickerPost']);
      $array = array('comments' => $com);
      echo json_encode($array);
    }

  }
