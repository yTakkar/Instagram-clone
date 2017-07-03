<?php
  class share{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function DidIShareHimTo($post, $user){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT share_id FROM shares WHERE post_id = :post AND share_by = :by AND share_to = :to");
      $query->execute(array(":post" => $post, ":by" => $session, ":to" => $user));
      $count = $query->rowCount();
      if ($count == 0) {
        return false;
      } else if ($count > 0) {
        return true;
      }
    }

    public function getFollowingsShare($post){
      $session = $_SESSION['id'];

      include 'universal.class.php';
      include 'avatar.class.php';
      include 'post.class.php';

      $universal = new universal;
      $avatar = new Avatar;
      $Post = new post;

      $query = $this->db->prepare("SELECT follow_to FROM follow_system WHERE follow_by = :by ORDER BY time DESC");
      $query->execute(array(":by" => $session));
      $count = $query->rowCount();
      if ($count == 0) {
        echo "<div class='no_display'><img src='{$this->DIR}/images/needs/large.jpg'></div>";
      } else if ($count > 0) {
        echo "<input type='hidden' class='share_postid'>";
        echo "<input type='hidden' class='share_userid'>";
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $userid = $row->follow_to;
          if ($Post->getPost($post, "user_id") != $userid) {
            echo "<div class='display_items select_receiver ";
            if (self::DidIShareHimTo($post, $userid)) {
              echo "already_shared";
            }
            echo "' data-userid='{$userid}'><div class='d_i_img'>
            <img src='{$this->DIR}/". $avatar->DisplayAvatar($userid) ."' alt='profile'></div><div class='d_i_content'><div class='d_i_info'>
            <span class='d_i_username username'>". $universal->nameShortener($universal->GETsDetails($userid, "username"), 15) ."</span>
            <span class='d_i_name'>". $universal->nameShortener($universal->GETsDetails($userid, "firstname")." ". $universal->GETsDetails($userid, "surname"), 15) ."</span></div></div></div>";
          }

        }
      }
    }

    public function postShare($to, $post){
      $by = $_SESSION['id'];

      include 'post.class.php';
      include 'notifications.class.php';
      include 'universal.class.php';

      $noti = new notifications;
      $universal = new universal;
      $Post = new post;

      // $mquery = $this->db->prepare("SELECT share_by, share_to, post_id FROM shares WHERE share_by = :b AND share_to = :t AND post_id = :p");
      // $mquery->execute(array(":b" => $by, ":t" => $to, ":p" => $post));
      // $count = $mquery->rowCount();

      if (self::DidIShareHimTo($post, $to) == false) {
        $query = $this->db->prepare("INSERT INTO shares (share_by, share_to, post_id, share_time) VALUES (:by, :to, :post, now())");
        $query->execute(array(":by" => $by, ":to" => $to, ":post" => $post));
        $noti->actionNotify($to, $post, "shareto");

        $too = $Post->postDetails($post, "user_id");
        $noti->actionNotify($too, $post, "shareyour");

        return "Shared to ".$universal->GETsDetails($to, "username");
      } else {
        return "Already shared";
      }

    }

    public function getShares($post){
      $query = $this->db->prepare("SELECT share_id FROM shares WHERE post_id = :id");
      $query->execute(array(":id" => $post));
      $count = $query->rowCount();
      if ($count == 0) {
        return "No shares";
      } else if ($count == 1) {
        return "1 share";
      } else {
        return "$count shares";
      }
    }

    public function sharers($posting){
      $session = $_SESSION['id'];

      include 'universal.class.php';
      include 'avatar.class.php';
      include 'follow_system.class.php';

      $universal = new universal;
      $avatar = new Avatar;
      $follow = new follow_system;

      $query = $this->db->prepare("SELECT share_by, share_to FROM shares WHERE post_id = :post ORDER BY share_id DESC");
      $query->execute(array(":post" => $posting));
      if ($query->rowCount() == 0) {
        echo "<div class='no_display'><img src='{$this->DIR}/images/needs/large.jpg'></div>";
      } else if ($query->rowCount() != 0) {
        while ($fetch = $query->fetch(PDO::FETCH_OBJ)) {
          $userid = $fetch->share_by;
          $to = $fetch->share_to;
          echo "<div class='display_items' data-getid='$userid'><div class='d_i_img'>";
          echo "<img src='{$this->DIR}/". $avatar->DisplayAvatar($userid) ."' alt='profile'>";
          echo "</div><div class='d_i_content'><div class='d_i_info'>";
          echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($userid, "username") ."' class='d_i_username username'>". $universal->nameShortener($universal->GETsDetails($userid, "username"), 20) ."</a>";
          echo "<span class='d_i_name'>to <a href='{$this->DIR}/profile/{$universal->GETsDetails($to, "username")}'>{$universal->nameShortener($universal->GETsDetails($to, "username"), 20)}</a></span></div><div class='d_i_act display_ff' data-getid='$userid'>";

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

    public function AmIsharedTo($post){
      $to = $_SESSION['id'];
      $query = $this->db->prepare("SELECT share_id FROM shares WHERE post_id = :post AND share_to = :to");
      $query->execute(array(":post" => $post, ":to" => $to));
      $count = $query->rowCount();
      if ($count == 0) {
        return false;
      } else if ($count > 0) {
        return true;
      }
    }

    public function AmIsharedBy($post){
      $to = $_SESSION['id'];
      $query = $this->db->prepare("SELECT share_id FROM shares WHERE post_id = :post AND share_by = :to");
      $query->execute(array(":post" => $post, ":to" => $to));
      $count = $query->rowCount();
      if ($count == 0) {
        return false;
      } else if ($count > 0) {
        return true;
      }
    }

    public function unshare($post){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("DELETE FROM shares WHERE post_id = :post AND share_to = :to");
      $query->execute(array(":post" => $post, ":to" => $session));
    }

    public function getShareTos($post){
      $session = $_SESSION['id'];
      include 'universal.class.php';
      include 'avatar.class.php';

      $universal = new universal;
      $avatar = new Avatar;

      $query = $this->db->prepare("SELECT share_to FROM shares WHERE share_by = :by AND post_id = :post");
      $query->execute(array(":by" => $session, ":post" => $post));
      $count = $query->rowCount();
      if ($count == 0) {
        echo "<div class='no_display'><img src='{$this->DIR}/images/needs/large.jpg'></div>";
      } else if ($count > 0) {
        echo "<input type='hidden' class='share_postid'>";
        echo "<input type='hidden' class='share_userid'>";
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $userid = $row->share_to;
          echo "<div class='display_items select_receiver' data-userid='{$userid}'><div class='d_i_img'>
          <img src='{$this->DIR}/". $avatar->DisplayAvatar($userid) ."' alt='profile'></div><div class='d_i_content'><div class='d_i_info'>
          <span class='d_i_username username'>" .$universal->nameShortener($universal->GETsDetails($userid, "username"), 15). "</span>
          <span class='d_i_name'>". $universal->nameShortener($universal->GETsDetails($userid, "firstname")." ". $universal->GETsDetails($userid, "surname"), 15) ."</span></div></div></div>";

        }
      }
    }

    public function removeShare($post, $user){
      $query = $this->db->prepare("DELETE FROM shares WHERE post_id = :post AND share_to = :to");
      $query->execute(array(":post" => $post, ":to" => $user));
    }

  }
?>
