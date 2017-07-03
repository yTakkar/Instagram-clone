<?php
  class follow_system{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function isFollowing($get){
      if (isset($_SESSION['id'])) {
        $session = $_SESSION['id'];
        $query = $this->db->prepare("SELECT follow_to FROM follow_system WHERE follow_by = :session AND follow_to = :get LIMIT 1");
        $query->execute(array(":session" => $session, ":get" => $get));
        if ($query->rowCount() != 0 || $query->rowCount() != null) {
          return true;
        } else if ($query->rowCount() == 0) {
          return false;
        }
      }
    }

    public function follow($get){
      $universal = new universal;
      $settings = new settings;
      $noti = new notifications;

      $session = $_SESSION['id'];
      $session_u = $universal->GETsDetails($session, "username");
      $get_u = $universal->GETsDetails($get, "username");

      if ($settings->AmIBlocked($get) == false) {
        if (self::isFollowing($get) == false) {
          $query = $this->db->prepare("INSERT INTO follow_system(follow_by, follow_by_u, follow_to, follow_to_u, time) VALUES (:session, :session_u, :get, :get_u, now())");
          $query->execute(array(":session" => $session, ":session_u" => $session_u, ":get" => $get, ":get_u" => $get_u));
          $noti->followNotify($get, "follow");
          return "ok";
        } else {
          return "Already followed";
        }
      }
    }

    public function unfollow($get){
      if (self::isFollowing($get)) {
        $session = $_SESSION['id'];
        $query = $this->db->prepare("DELETE FROM follow_system WHERE follow_by = :session AND follow_to = :get LIMIT 1");
        $query->execute(array(":session" => $session, ":get" => $get));
      }
    }

    public function simpleUnfollow($user){
      $universal = new universal;
      $id = $universal->getIdFromGet($user);
      $session = $_SESSION['id'];
      $query = $this->db->prepare("DELETE FROM follow_system WHERE follow_by = :session AND follow_to = :get LIMIT 1");
      $query->execute(array(":session" => $session, ":get" => $id));
    }

    public function getFollowers($get){
      // $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT follow_by FROM follow_system WHERE follow_to = :get");
      $query->execute(array(":get" => $get));
      $count = $query->rowCount();
      return $count;
    }

    public function getFollowings($get){
      // $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT follow_to FROM follow_system WHERE follow_by = :get");
      $query->execute(array(":get" => $get));
      $count = $query->rowCount();
      return $count;
    }

    public function followers($get){
      $session = $_SESSION['id'];

      $universal = new universal;
      $avatar = new Avatar;
      $mutual = new mutual;

      $query = $this->db->prepare("SELECT follow_by FROM follow_system WHERE follow_to = :get ORDER BY time DESC");
      $query->execute(array(":get" => $get));
      if ($query->rowCount() == 0) {
        echo "<div class='no_display'><img src='{$this->DIR}/images/needs/large.jpg'></div>";
      } else if ($query->rowCount() != 0) {
        while ($fetch = $query->fetch(PDO::FETCH_OBJ)) {
          $userid = $fetch->follow_by;
          echo "<div class='display_items' data-getid='$userid'><div class='d_i_img'>";
          echo "<img src='{$this->DIR}/". $avatar->DisplayAvatar($userid) ."' alt='profile'>";
          echo "</div><div class='d_i_content'><div class='d_i_info'>";
          echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($userid, "username") ."' class='d_i_username username'>". $universal->nameShortener($universal->GETsDetails($userid, "username"), 20) ."</a>";
          echo "<span class='d_i_name'>". $mutual->eMutual($userid) ."</span></div><div class='d_i_act display_ff' data-getid='$userid'>";
          if ($session == $userid) {
            echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($userid, "username") ."' class='sec_btn '>Profile</a>";
          } else {
            if (self::isFollowing($userid)) {
              echo "<a href='#' class='pri_btn display_unfollow unfollow'>Unfollow</a>";
            } else if (self::isFollowing($userid) == false) {
              echo "<a href='#' class='pri_btn display_follow follow'>Follow</a>";
            }
          }
          echo "</div></div><hr></div>";
        }
      }
    }

    public function followings($get){
      $session = $_SESSION['id'];

      $universal = new universal;
      $avatar = new Avatar;
      $mutual = new mutual;

      $query = $this->db->prepare("SELECT follow_to FROM follow_system WHERE follow_by = :get ORDER BY time DESC");
      $query->execute(array(":get" => $get));
      if ($query->rowCount() == 0) {
        echo "<div class='no_display'><img src='{$this->DIR}/images/needs/large.jpg'></div>";
      } else if ($query->rowCount() != 0) {
        while ($fetch = $query->fetch(PDO::FETCH_OBJ)) {
          $userid = $fetch->follow_to;
          echo "<div class='display_items'><div class='d_i_img' data-getid='$userid'>";
          echo "<img src='{$this->DIR}/". $avatar->DisplayAvatar($userid) ."' alt='profile'>";
          echo "</div><div class='d_i_content'><div class='d_i_info'>";
          echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($userid, "username") ."' class='d_i_username username'>". $universal->nameShortener($universal->GETsDetails($userid, "username"), 20) ."</a>";
          echo "<span class='d_i_name'>". $mutual->eMutual($userid) ."</span></div><div class='d_i_act display_ff' data-getid='$userid'>";
          if ($session == $userid) {
            echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($userid, "username") ."' class='sec_btn'>Profile</a>";
          } else {
            if (self::isFollowing($userid)) {
              echo "<a href='#' class='pri_btn display_unfollow unfollow'>Unfollow</a>";
            } else if (self::isFollowing($userid) == false) {
              echo "<a href='#' class='pri_btn display_follow follow'>Follow</a>";
            }
          }
          echo "</div></div><hr></div>";
        }
      }
    }

    public function viewCounter($get){
      $session = $_SESSION['id'];
      if ($session != $get) {
        $query = $this->db->prepare("INSERT INTO profile_views(view_from, view_to, time) VALUES(:session, :get, now())");
        $query->execute(array(":session" => $session, ":get" => $get));
      }
    }

    public function getViewers($get){
      // $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT view_from FROM profile_views WHERE view_to = :get");
      $query->execute(array(":get" => $get));
      $count = $query->rowCount();
      return $count;
    }

    public function profile_viewers($get){
      $session = $_SESSION['id'];

      $universal = new universal;
      $avatar = new Avatar;
      $mutual = new mutual;

      $query = $this->db->prepare("SELECT view_from FROM profile_views WHERE view_to = :get ORDER BY view_id DESC");
      $query->execute(array(":get" => $get));
      if ($query->rowCount() == 0) {
        echo "<div class='no_display'><img src='{$this->DIR}/images/needs/large.jpg'></div>";
      } else if ($query->rowCount() != 0) {
        while ($fetch = $query->fetch(PDO::FETCH_OBJ)) {
          $userid = $fetch->view_from;
          echo "<div class='display_items' data-getid='$userid'><div class='d_i_img'>";
          echo "<img src='{$this->DIR}/". $avatar->DisplayAvatar($userid) ."' alt='profile'>";
          echo "</div><div class='d_i_content'><div class='d_i_info'>";
          echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($userid, "username") ."' class='d_i_username username'>". $universal->nameShortener($universal->GETsDetails($userid, "username"), 20) ."</a>";
          echo "<span class='d_i_name'>". $mutual->eMutual($userid) ."</span></div><div class='d_i_act display_ff' data-getid='$userid'>";
          if ($session == $userid) {
            echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($userid, "username") ."' class='sec_btn'>Profile</a>";
          } else {
            if (self::isFollowing($userid)) {
              echo "<a href='#' class='pri_btn display_unfollow unfollow'>Unfollow</a>";
            } else if (self::isFollowing($userid) == false) {
              echo "<a href='#' class='pri_btn display_follow follow'>Follow</a>";
            }
          }
          echo "</div></div><hr></div>";
        }
      }
    }

    public function profileFollowers($id, $way, $limit){
      $session = $_SESSION['id'];

      $universal = new universal;
      $avatar = new Avatar;
      $mutual = new mutual;
      $Time = new time;

      if ($way == "direct") {
        $query = $this->db->prepare("SELECT follow_by, follow_id, time FROM follow_system WHERE follow_to = :to ORDER BY time DESC LIMIT 12");
        $query->execute(array(":to" => $id));

      } else if ($way == "ajax") {
        $start = intval($limit);
        $query = $this->db->prepare("SELECT follow_by, follow_id, time FROM follow_system WHERE follow_to = :to AND follow_id < :start ORDER BY time DESC LIMIT 12");
        $query->execute(array(":to" => $id, ":start" => $start));
      }

      $count = $query->rowCount();

      if ($count == 0) {
        if ($way == "direct") {
          echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'>
          <span>No followers of</span></div>";
        }
      } else if ($count > 0) {
        echo "<div class='m_wrapper'>";

        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $followers = $row->follow_by;
          $fid = $row->follow_id;
          $time = $row->time;

          echo "<div class='m_on followers_m_on inst' data-fid='{$fid}'><div class='m_top'>
              <img src='{$this->DIR}/{$avatar->DisplayAvatar($followers)}' alt=''>
              <div class='m_top_right'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($followers, "username")}'>". $universal->nameShortener($universal->GETsDetails($followers, "username"), 18) ."</a>
                <span>";
                echo $mutual->eMutual($followers);
              echo "</span>
              </div></div>
              <span class='recommend_time'>{$Time->timeAgo($time)}</span>
              <div class='m_bottom' data-getid='$followers'>";
              if ($session == $followers) {
                echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($followers, "username") ."' class='sec_btn '>Profile</a>";
              } else {
                if (self::isFollowing($followers)) {
                  echo "<a href='#' class='pri_btn display_unfollow unfollow'>Unfollow</a>";
                } else if (self::isFollowing($followers) == false) {
                  echo "<a href='#' class='pri_btn display_follow follow'>Follow</a>";
                }
              }
            echo "</div></div>";

        }
        echo "</div>";
        echo "<div class='post_end feed_inserted'>Looks like you've reached the end</div>";
      }

    }

    public function profileFollowings($id, $way, $limit){
      $session = $_SESSION['id'];

      $universal = new universal;
      $avatar = new Avatar;
      $mutual = new mutual;
      $Time = new time;

      if ($way == "direct") {
        $query = $this->db->prepare("SELECT follow_to, follow_id, time FROM follow_system WHERE follow_by = :to ORDER BY time DESC LIMIT 12");
        $query->execute(array(":to" => $id));

      } else if ($way == "ajax") {
        $start = intval($limit);
        $query = $this->db->prepare("SELECT follow_to, follow_id, time FROM follow_system WHERE follow_by = :to AND follow_id < :start ORDER BY time DESC LIMIT 12");
        $query->execute(array(":to" => $id, ":start" => $start));
      }

      $count = $query->rowCount();

      if ($count == 0) {
        if ($way == "direct") {
          echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'>
          <span>No followings</span></div>";
        }
      } else if ($count > 0) {
        echo "<div class='m_wrapper'>";

        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $followers = $row->follow_to;
          $fid = $row->follow_id;
          $time = $row->time;

          echo "<div class='m_on followings_m_on inst' data-fid='{$fid}'><div class='m_top'>
              <img src='{$this->DIR}/{$avatar->DisplayAvatar($followers)}' alt=''>
              <div class='m_top_right'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($followers, "username")}'>". $universal->nameShortener($universal->GETsDetails($followers, "username"), 18) ."</a>
                <span>";
                echo $mutual->eMutual($followers);
              echo "</span>
              </div></div>
              <span class='recommend_time'>{$Time->timeAgo($time)}</span>
              <div class='m_bottom' data-getid='$followers'>";
              if ($session == $followers) {
                echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($followers, "username") ."' class='sec_btn '>Profile</a>";
              } else {
                if (self::isFollowing($followers)) {
                  echo "<a href='#' class='pri_btn display_unfollow unfollow'>Unfollow</a>";
                } else if (self::isFollowing($followers) == false) {
                  echo "<a href='#' class='pri_btn display_follow follow'>Follow</a>";
                }
              }
              // if (self::isFollowing($followers)) {
              //   echo "<a href='#' class='pri_btn unfollow'>Unfollow</a>";
              // } else if (self::isFollowing($followers) == false) {
              //   echo "<a href='#' class='pri_btn follow'>Follow</a>";
              // }
            echo "</div></div>";

        }
        echo "</div>";
        echo "<div class='post_end feed_inserted'>Looks like you've reached the end</div>";
      }

    }

  }

?>
