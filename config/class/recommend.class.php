<?php
  class recommend{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function getRecommends($user){
      $query = $this->db->prepare("SELECT recommend_to FROM recommends WHERE recommend_to = :of");
      $query->execute(array(":of" => $user));
      return $query->rowCount();
    }

    public function isRecommended($to, $of){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT recommend_id FROM recommends WHERE recommend_by = :by AND recommend_to = :to AND recommend_of = :of");
      $query->execute(array(":by" => $session, ":to" => $to, ":of" => $of));
      $count = $query->rowCount();
      if ($count == 0) {
        return false;
      } else if ($count > 0) {
        return true;
      }
    }

    public function getFollowingsRecommend($user){
      $session = $_SESSION['id'];
      include 'universal.class.php';
      include 'avatar.class.php';
      include 'settings.class.php';

      $universal = new universal;
      $avatar = new Avatar;
      $settings = new settings;

      if ($settings->AmIBlocked($user) == false) {
        $query = $this->db->prepare("SELECT follow_to FROM follow_system WHERE follow_by = :by AND follow_to != :no ORDER BY time DESC");
        $query->execute(array(":by" => $session, ":no" => $user));
        $count = $query->rowCount();
        if ($count == 0) {
          echo "<div class='no_display'><img src='{$this->DIR}/images/needs/large.jpg'></div>";
        } else if ($count > 0) {
          echo "<input type='hidden' class='share_postid'>";
          echo "<input type='hidden' class='share_userid'>";
          while ($row = $query->fetch(PDO::FETCH_OBJ)) {
            $userid = $row->follow_to;

            echo "<div class='display_items select_receiver ";
            if (self::isRecommended($userid, $user)) {
              echo "already_shared";
            }
            echo "' data-userid='{$userid}'><div class='d_i_img'>
            <img src='{$this->DIR}/". $avatar->DisplayAvatar($userid) ."' alt='profile'></div><div class='d_i_content'><div class='d_i_info'>
            <span class='d_i_username username'>". $universal->nameShortener($universal->GETsDetails($userid, "username"), 15). "</span>
            <span class='d_i_name'>". $universal->nameShortener($universal->GETsDetails($userid, "firstname")." ". $universal->GETsDetails($userid, "surname"), 15) ."</span></div></div></div>";

          }
        }
      }
    }

    public function recommendUser($user, $get){
      include 'notifications.class.php';
      $universal = new universal;
      $noti = new notifications;

      $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT recommend_id FROM recommends WHERE recommend_by = :by AND recommend_to = :to AND recommend_of = :of");
      $query->execute(array(":by" => $session, ":to" => $user, ":of" => $get));
      $count = $query->rowCount();
      if ($count == 0) {
        $querym = $this->db->prepare("INSERT INTO recommends(recommend_by, recommend_to, recommend_of, time) VALUES (:session, :user, :get, now())");
        $querym->execute(array(":session" => $session, ":user" => $user, ":get" => $get));
        $noti->recommendNotify($user, $get);
        return "Recommended";
      } else if ($count == 1) {
        return "Already recommended";
      }
    }

    public function profileRecommends($id){
      $avatar = new Avatar;
      $universal = new universal;
      $follow = new follow_system;
      $mutual = new mutual;
      $timing = new time;

      $session = $_SESSION['id'];

      $query = $this->db->prepare("SELECT * FROM recommends WHERE recommend_to = :to ORDER BY time DESC");
      $query->execute(array(":to" => $id));
      $count = $query->rowCount();

      if ($count == 0) {
        echo "<div class='home_last_mssg rec_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'>
        <span>No recommendations by anyone</span></div>";
      } else if ($count > 0) {

        echo "<div class='m_header'><span>{$count} recommendations to you</span></div><div class='m_wrapper'>";

        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $by = $row->recommend_by;
          $to = $row->recommend_to;
          $of = $row->recommend_of;
          $time = $row->time;

          echo "<div class='m_on inst'><div class='m_top'>
              <img src='{$this->DIR}/{$avatar->DisplayAvatar($of)}' alt=''>
              <div class='m_top_right'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($of, "username")}'>". substr($universal->GETsDetails($of, "username"), 0, 22) ."</a>
                <span>";
                echo $mutual->eMutual($of);
              echo "</span>
              </div></div>
              <span class='recommend_time'>{$timing->timeAgo($time)}</span>
              <div class='m_bottom'>
              <span class='recommend_by'>by";
              if ($by == $session) {
                echo "<a href='{$this->DIR}/profile/{$universal->GETsDetails($session, "username")}'>You</a>";
              } else {
                echo "<a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}'> {$universal->GETsDetails($by, "username")}</a>";
              }
              echo "</span>";
              echo "<div data-getid='$of'>";
              if ($follow->isFollowing($of)) {
                echo "<a href='#' class='pri_btn unfollow'>Unfollow</a>";
              } else if ($follow->isFollowing($of) == false) {
                echo "<a href='#' class='pri_btn follow'>Follow</a>";
              }
            echo "</div></div></div>";

        }

        echo "</div>";

      }

    }

  }
?>
