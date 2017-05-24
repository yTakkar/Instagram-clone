<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include '../../config/classesGetter.php';
    $post = new post;

    if (isset($_POST['edit']) == "yes") {
      $text = preg_replace("#[<>]#i", "", $_POST['text']);
      // $text = $_GET['text'];
      $post->editPost($text, $_POST['post'], $_POST['type']);
    }

  }
?>
