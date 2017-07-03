<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include_once '../../config/class/needy_class.php';
    include '../../config/class/post_like.class.php';
    $like = new postLike;

    // if (isset($_GET['like'])) {
    //   $id = preg_replace("#[^0-9]#i", "", $_GET['like']);
    //   $post = preg_replace("#[^0-9]#i", "", $_GET['post']);
    //   $like->post_unlike($id, $post);
    //   $like->post_like($id, $post);
    //   $likes = $like->getPostLikes($post);
    //   $array = array('likes' => $likes);
    //   echo json_encode($array);
    // }

    if (isset($_GET['like'])) {
      include '../../config/class/universal.class.php';
      $post = preg_replace("#[^0-9]#i", "", $_GET['like']);
      $like->post_unlike($post);
      $like->post_like($post);
      $likes = $like->getPostLikes($post);
      $slike = $like->simpleGetPostLikes($post);
      $array = array('likes' => $likes, "simpleLikes" => $slike);
      // print_r($array);
      echo json_encode($array);
    }

    // if (isset($_GET['unlike'])) {
    //   $id = preg_replace("#[^0-9]#i", "", $_GET['unlike']);
    //   $post = preg_replace("#[^0-9]#i", "", $_GET['post']);
    //   $like->post_unlike($id, $post);
    //   $likes = $like->getPostLikes($post);
    //   $array = array('likes' => $likes);
    //   echo json_encode($array);
    // }

    if (isset($_GET['unlike'])) {
      include '../../config/class/universal.class.php';
      $post = preg_replace("#[^0-9]#i", "", $_GET['unlike']);
      $like->post_unlike($post);
      $likes = $like->getPostLikes($post);
      $sunlike = $like->simpleGetPostLikes($post);
      $array = array('likes' => $likes, "simpleLikes" => $sunlike);
      echo json_encode($array);
    }

    if (isset($_POST['post'])) {
      $post = preg_replace("#[^0-9]#i", "", $_POST['post']);
      $like->likers($post);
    }

  }
?>
