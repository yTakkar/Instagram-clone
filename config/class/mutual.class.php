<?php

  class mutual{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }
    
    public function mutualCount($get){
      $session = $_SESSION['id'];
      $first = array();
      $second = array();
      $final = array();

      $users = $this->db->prepare("SELECT follow_by FROM follow_system WHERE follow_to = :to");
      $users->execute(array(":to" => $get));
      while ($row = $users->fetch(PDO::FETCH_OBJ)) {
        $first[] = $row->follow_by;
      }

      $mine = $this->db->prepare("SELECT follow_to FROM follow_system WHERE follow_by = :to");
      $mine->execute(array(":to" => $session));
      while ($row = $mine->fetch(PDO::FETCH_OBJ)) {
        $second[] = $row->follow_to;
      }

      foreach ($first as $key => $value) {
        if (in_array($value, $second)) {
          $final[] = $value;
        }
      }

      $count = count($final);
      return $count;

    }

    public function propleYouKnow($get){
      if (isset($_SESSION['id'])) {
        $avatar = new Avatar;
        $universal = new universal;

        $session = $_SESSION['id'];
        $first = array();
        $second = array();
        $final = array();

        $users = $this->db->prepare("SELECT follow_by FROM follow_system WHERE follow_to = :to");
        $users->execute(array(":to" => $get));
        while ($row = $users->fetch(PDO::FETCH_OBJ)) {
          $first[] = $row->follow_by;
        }

        $mine = $this->db->prepare("SELECT follow_to FROM follow_system WHERE follow_by = :to");
        $mine->execute(array(":to" => $session));
        while ($row = $mine->fetch(PDO::FETCH_OBJ)) {
          $second[] = $row->follow_to;
        }

        foreach ($first as $key => $value) {
          if (in_array($value, $second)) {
            $final[] = $value;
          }
        }

        $final = array_slice($final, 0, 10);
        $count = count($final);

        if ($count == 0) {
          echo "<div class='no_such_mutual'><span>No followers you know</span></div>";
        } else if ($count > 0) {
          echo "<div class='mutual_info header_of_divs'>
          <span>{$count} followers you might know</span>
          <a href='{$this->DIR}/profile/{$universal->GETsDetails($get, "username")}?ask=people_you_know' data-description='view all' class='view_all_yk'><i class='fa fa-chevron-right' aria-hidden='true'></i></a>
          </div><div class='mutual_main'>";
          foreach ($final as $key => $value) {
            echo "<a href='{$this->DIR}/profile/{$universal->GETsDetails($value, "username")}' data-description='{$universal->nameShortener($universal->GETsDetails($value, "username"), 20)}' class='mutual_links'><img src='{$this->DIR}/{$avatar->DisplayAvatar($value)}' alt=''></a>";
          }

          echo "</div>";
        }

      }
    }

    public function peopleMightKnow($get){
      $avatar = new Avatar;
      $universal = new universal;
      $follow = new follow_system;

      $session = $_SESSION['id'];
      $first = array();
      $second = array();
      $final = array();

      $users = $this->db->prepare("SELECT follow_by FROM follow_system WHERE follow_to = :to");
      $users->execute(array(":to" => $get));
      while ($row = $users->fetch(PDO::FETCH_OBJ)) {
        $first[] = $row->follow_by;
      }

      $mine = $this->db->prepare("SELECT follow_to FROM follow_system WHERE follow_by = :to");
      $mine->execute(array(":to" => $session));
      while ($row = $mine->fetch(PDO::FETCH_OBJ)) {
        $second[] = $row->follow_to;
      }

      foreach ($first as $key => $value) {
        if (in_array($value, $second)) {
          $final[] = $value;
        }
      }

      $count = count($final);

      if ($count == 0) {
        echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'>
        <span>No mutual followers</span></div>";
      } else if ($count > 0) {

        echo "<div class='m_header'><span>{$count} {$universal->GETsDetails($get, "username")}'s followers you might know</span></div><div class='m_wrapper'>";

        foreach ($final as $value) {
          echo "<div class='m_on inst'><div class='m_top'>
              <img src='{$this->DIR}/{$avatar->DisplayAvatar($value)}' alt=''>
              <div class='m_top_right'>
                <a href='#'>{$universal->nameShortener($universal->GETsDetails($value, "username"), 20)}</a>
                <span>";
                // if (self::mutualCount($value) == 0) {
                //   echo $universal->GETsDetails($value, "firstname")." ".$universal->GETsDetails($value, "surname");
                // } else if (self::mutualCount($value) == 1) {
                //   echo "1 mutual follower";
                // } else if(self::mutualCount($value) > 1){
                //   echo self::mutualCount($value)." mutual followers";
                // }
                echo self::eMutual($value);
              echo "</span>
              </div></div><div class='m_bottom' data-getid='$value'>";
              if ($follow->isFollowing($value)) {
                echo "<a href='#' class='pri_btn unfollow'>Unfollow</a>";
              } else if ($follow->isFollowing($value) == false) {
                echo "<a href='#' class='pri_btn follow'>Follow</a>";
              }
            echo "</div></div>";
        }
        echo "</div>";

      }

    }

    public function eMutual($user){
      $session = $_SESSION['id'];
      $count = self::mutualCount($user);
      $universal = new universal;
      $follow = new follow_system;
      if ($user == $session) {
        return $universal->nameShortener($universal->GETsDetails($user, "firstname")." ".$universal->GETsDetails($user, "surname"), 20);
      } else {
        if ($follow->isFollowing($user)) {
          return "Following";
        } else {
          if ($count == 0) {
            return $universal->nameShortener($universal->GETsDetails($user, "firstname")." ".$universal->GETsDetails($user, "surname"), 20);
          } else if ($count == 1) {
            return "1 mutual follower";
          } else if ($count > 1) {
            return $count." mutual followers";
          }
        }
      }
    }

  }

?>
