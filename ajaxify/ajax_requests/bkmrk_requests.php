<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include_once '../../config/class/needy_class.php';
    include '../../config/class/bkmrk.class.php';
    $bookmark = new bookmark;

    if (isset($_GET['bkmrk'])) {
      $post = preg_replace("#[^0-9]#i", "", $_GET['bkmrk']);
      $bookmark->bkmrk($post);
    }

    if (isset($_GET['unbkmrk'])) {
      $post = preg_replace("#[^0-9]#i", "", $_GET['unbkmrk']);
      $bookmark->unbkmrk($post);
      // echo $post;
    }

    // // FOR TAGGINGS REQUEST JUST BECAUSE THERE'S ONLY ONY REQUEST
    // if (isset($_POST['post'])) {
    //   include '../../config/class/taggings.class.php';
    //   $tag = new taggings;
    //   $post = preg_replace("#[^0-9]#i", "", $_POST['post']);
    //   $tag->taggers($post);
    // }
    //
    // if (isset($_GET['untag'])) {
    //   include '../../config/class/taggings.class.php';
    //   $tag = new taggings;
    //   $tag->untag($_GET['untag']);
    //   $tags = $tag->getTaggings($_GET['untag']);
    //   $array = array('tags' => $tags);
    //   echo json_encode($array);
    // }
    //
    // if (isset($_GET['delete_tag'])) {
    //   // echo $_GET['post'];
    //   include '../../config/class/taggings.class.php';
    //   $tag = new taggings;
    //   $tag->deleteTag($_GET['post'], $_GET['delete_tag']);
    // }

  }
?>
