<?php
  class taggings{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function getTaggings($post){
      $query = $this->db->prepare("SELECT tagging_id FROM taggings WHERE post_id = :post");
      $query->execute(array(":post" => $post));
      $count = $query->rowCount();
      if ($count == 0) {
        return "No tags";
      } else if ($count == 1) {
        return "1 tag";
      } else {
        return "$count tags";
      }
    }

    public function taggers($post){
      $session = $_SESSION['id'];

      include 'universal.class.php';
      include 'avatar.class.php';
      include 'follow_system.class.php';

      $universal = new universal;
      $avatar = new Avatar;
      $follow = new follow_system;

      $query = $this->db->prepare("SELECT taggings_id FROM taggings WHERE post_id = :post ORDER BY tagging_id DESC");
      $query->execute(array(":post" => $post));
      if ($query->rowCount() == 0) {
        echo "<div class='no_display'><img src='{$this->DIR}/images/needs/large.jpg'></div>";
      } else if ($query->rowCount() != 0) {
        while ($fetch = $query->fetch(PDO::FETCH_OBJ)) {
          $userid = $fetch->taggings_id;
          echo "<div class='display_items' data-getid='$userid'><div class='d_i_img'>";
          echo "<img src='{$this->DIR}/". $avatar->DisplayAvatar($userid) ."' alt='profile'>";
          echo "</div><div class='d_i_content'><div class='d_i_info'>";
          echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($userid, "username") ."' class='d_i_username username'>". $universal->nameShortener($universal->GETsDetails($userid, "username"), 20) ."</a>";
          echo "<span class='d_i_name'>". $universal->nameShortener($universal->GETsDetails($userid, "firstname")." ".$universal->GETsDetails($userid, "surname"), 20) ."</span></div><div class='d_i_act display_ff' data-getid='$userid'>";

          $mquery = $this->db->prepare("SELECT user_id FROM post WHERE post_id = :post LIMIT 1");
          $mquery->execute(array(":post" => $post));
          $row = $mquery->fetch(PDO::FETCH_OBJ);
          $user = $row->user_id;

          if ($session == $user) {
            echo "<a href='#' class='sec_btn delete_tag' data-postid='{$post}'>Remove tag</a>";
          } else if ($session != $user) {
            if ($session == $userid) {
              echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($userid, "username") ."' class='sec_btn '>Profile</a>";
            } else {
              if ($follow->isFollowing($userid)) {
                echo "<a href='#' class='pri_btn display_unfollow unfollow'>Unfollow</a>";
              } else if ($follow->isFollowing($userid) == false) {
                echo "<a href='#' class='pri_btn display_follow follow'>Follow</a>";
              }
            }
          }

          echo "</div></div><hr></div>";
        }
      }
    }

    public function untag($post){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("DELETE FROM taggings WHERE post_id = :post AND taggings_id = :id");
      $query->execute(array(":post" => $post, ":id" => $session));
    }

    public function AmITagged($post){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT tagging_id FROM taggings WHERE post_id = :post AND taggings_id = :id");
      $query->execute(array(":post" => $post, ":id" => $session));
      $count = $query->rowCount();
      if ($count == 0) {
        return false;
      } else if ($count > 0) {
        return true;
      }
    }

    public function deleteTag($post, $id){
      $query = $this->db->prepare("DELETE FROM taggings WHERE post_id = :post AND taggings_id = :taggings");
      $query->execute(array(":post" => $post, ":taggings" => $id));
    }

  }
?>
