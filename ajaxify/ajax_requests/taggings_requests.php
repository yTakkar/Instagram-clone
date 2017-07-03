<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include_once '../../config/class/needy_class.php';
    include '../../config/class/taggings.class.php';
    $tag = new taggings;

    if (isset($_POST['post'])) {
      $post = preg_replace("#[^0-9]#i", "", $_POST['post']);
      $tag->taggers($post);
    }

    if (isset($_GET['untag'])) {
      $tag->untag($_GET['untag']);
      $tags = $tag->getTaggings($_GET['untag']);
      $array = array('tags' => $tags);
      echo json_encode($array);
    }

    if (isset($_GET['delete_tag'])) {
      $tag = new taggings;
      $a = $tag->deleteTag($_GET['post'], $_GET['delete_tag']);
      $a;
      $b = $tag->getTaggings($_GET['post']);
      $carray = array('nooftags' => $b);
      echo json_encode($carray);
    }

  }
?>
