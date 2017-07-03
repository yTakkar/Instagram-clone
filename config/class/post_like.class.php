<?php

  class postLike{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function simpleGetPostLikes($post){
      $query = $this->db->prepare("SELECT post_likes_id FROM post_likes WHERE post_id  = :post");
      $query->execute(array(":post" => $post));
      $count = $query->rowCount();
      if ($count == 0) {
        return "No";
      } else {
        return $count;
      }
    }

    public function getPostLikes($post){
      if (isset($_SESSION['id'])){
        $array1 = array();
        $array2 = array();
        $final = array();
        $array3 = array();

        $session = $_SESSION['id'];
        $universal = new universal;

        $query = $this->db->prepare("SELECT post_like_by FROM post_likes WHERE post_id  = :post");
        $query->execute(array(":post" => $post));
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $array1[] = $row->post_like_by;
        }

        $query = $this->db->prepare("SELECT follow_to FROM follow_system WHERE follow_by = :get");
        $query->execute(array(":get" => $session));
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $array2[] = $row->follow_to;
        }

        foreach ($array1 as $value) {
          if (in_array($value, $array2)) {
            $final[] = $value;
          }
        }

        $return = array_diff($array1, $final);

        foreach ($return as $key => $value) {
          $array3[] = $value;
        }

        $mine = array_reverse($final);

        foreach ($mine as $key => $value) {
          array_unshift($array3, $value);
        }

        $count = count($array3);

        if ($count == 0) {
          return "No likes";

        } else if ($count == 1) {
          if ($array3[0] == $session) {
            return "You liked";
          } else{
            return $universal->nameShortener($universal->GETsDetails($array3[0], "username"), 15)." liked";
          }

        } else if ($count == 2) {
          if (in_array($session, $array3)) {
            return "You and ".$universal->nameShortener($universal->GETsDetails($array3[0], "username"), 15)." liked";
          } else {
            return $universal->nameShortener($universal->GETsDetails($array3[0], "username"), 15)." and ".$universal->nameShortener($universal->GETsDetails($array3[1], "username"), 15)." liked";
          }

        } else if ($count == 3) {
          if (in_array($session, $array3)) {
            return "You, ".$universal->nameShortener($universal->GETsDetails($array3[0], "username"), 15)." and 1 ther liked";
          } else {
            return $universal->nameShortener($universal->GETsDetails($array3[0], "username"), 15).", ".$universal->nameShortener($universal->GETsDetails($array3[1], "username"), 15)." and 1 other liked";
          }

        } else if ($count > 3) {
          $slice = array_slice($array3, 2);
          if (in_array($session, $array3)) {
            return "You, ".$universal->nameShortener($universal->GETsDetails($array3[0], "username"), 15)." and ". count($slice) ." others liked";
          } else {
            return $universal->nameShortener($universal->GETsDetails($array3[0], "username"), 15).", ".$universal->nameShortener($universal->GETsDetails($array3[1], "username"), 15)." and ". count($slice) ." others liked";
          }
        }
      }
    }

    public function post_like($post){
      include 'notifications.class.php';
      include 'post.class.php';

      $noti = new notifications;
      $Post = new post;

      $id = $_SESSION['id'];
      // $squery = $this->db->prepare("SELECT post_likes_id FROM post_likes WHERE post_like_by = :first AND post_id = :second");
      // $squery->execute(array(":first" => $id, ":second" => $post));
      // if ($squery->rowCount() == 0) {
      if (self::likedOrNot($post) == false) {
        $query = $this->db->prepare("INSERT INTO post_likes (post_like_by, post_id, time) VALUES (:id, :post, now())");
        $query->execute(array(":id" => $id, ":post" => $post));
        $to = $Post->postDetails($post,"user_id");
        $noti->actionNotify($to, $post, "like");
      }
      // }
    }

    public function post_unlike($post){
      $id = $_SESSION['id'];
      $query = $this->db->prepare("DELETE FROM post_likes WHERE post_like_by = :id AND post_id = :post");
      $query->execute(array(":id" => $id, ":post" => $post));
    }

    public function likedOrNot($post){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT post_likes_id FROM post_likes WHERE post_like_by = :by AND post_id = :post LIMIT 1");
      $query->execute(array(":by" => $session, ":post" => $post));
      $row = $query->rowCount();
      if ($row == null || $row == 0) {
        return false;
      } else if ($row == 1) {
        return true;
      }
    }

    public function likers($post){
      $session = $_SESSION['id'];

      include 'universal.class.php';
      include 'avatar.class.php';
      include 'follow_system.class.php';

      $universal = new universal;
      $avatar = new Avatar;
      $follow = new follow_system;

      $query = $this->db->prepare("SELECT post_like_by FROM post_likes WHERE post_id = :post ORDER BY time DESC");
      $query->execute(array(":post" => $post));
      if ($query->rowCount() == 0) {
        echo "<div class='no_display'><img src='{$this->DIR}/images/needs/large.jpg'></div>";
      } else if ($query->rowCount() != 0) {
        while ($fetch = $query->fetch(PDO::FETCH_OBJ)) {
          $userid = $fetch->post_like_by;
          echo "<div class='display_items' data-getid='$userid'><div class='d_i_img'>";
          echo "<img src='{$this->DIR}/". $avatar->DisplayAvatar($userid) ."' alt='profile'>";
          echo "</div><div class='d_i_content'><div class='d_i_info'>";
          echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($userid, "username") ."' class='d_i_username username'>". $universal->nameShortener($universal->GETsDetails($userid, "username"), 20) ."</a>";
          echo "<span class='d_i_name'>". $universal->nameShortener($universal->GETsDetails($userid, "firstname")." ".$universal->GETsDetails($userid, "surname"), 30) ."</span></div><div class='d_i_act display_ff' data-getid='$userid'>";
          if ($session == $userid) {
            echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($userid, "username") ."' class='sec_btn '>Profile</a>";
          } else {
            if ($follow->isFollowing($userid)) {
              echo "<a href='#' class='pri_btn display_unfollow unfollow'>Unfollow</a>";
            } else if ($follow->isFollowing($userid) == false) {
              echo "<a href='#' class='pri_btn display_follow follow'>Follow</a>";
            }
          }
          echo "</div></div><hr></div>";
        }
      }
    }

  }


?>
