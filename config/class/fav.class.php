<?php
  class favourite{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function isFav($id){
      if (isset($_SESSION['id'])) {
        $session = $_SESSION['id'];
        $query = $this->db->prepare("SELECT user FROM favourites WHERE fav_by = :me AND user = :user");
        $query->execute(array(":me" => $session, ":user" => $id));
        if ($query->rowCount() > 0) {
          return true;
        } else if($query->rowCount() == 0) {
          return false;
        }
      }
    }

    public function addUserFav($id){
      $session = $_SESSION['id'];
      if (self::isFav($id) == false) {
        $query = $this->db->prepare("INSERT INTO favourites(fav_by, user, time) VALUES (:by, :user, now())");
        $query->execute(array(":by" => $session, ":user" => $id));
      }
    }

    public function noOfFavs($id){
      $query = $this->db->prepare("SELECT * FROM favourites WHERE fav_by = :me");
      $query->execute(array(":me" => $id));
      $count = $query->rowCount();
      if($count == 0){
        return "No";
      } else {
        return $count;
      }
    }

    public function userFavs($id){
      $session = $_SESSION['id'];

      $universal = new universal;
      $avatar = new Avatar;
      $mutual = new mutual;
      $Time = new time;
      $follow = new follow_system;

      $query = $this->db->prepare("SELECT * FROM favourites WHERE fav_by = :me ORDER BY time DESC");
      $query->execute(array(":me" => $id));
      $count = $query->rowCount();

      if ($count == 0) {
        echo "<div class='home_last_mssg fav_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'>
        <span>";
        if ($id == $session) {
          echo "You have no favourites.";
        } else {
          echo $universal->GETsDetails($id, "username")." got no favourites.";
        }
        echo "</span></div>";
      } else if ($count > 0) {
        echo "<div class='m_wrapper'>";
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $user = $row->user;
          $time = $row->time;

          echo "<div class='m_on inst'><div class='m_top'>
              <img src='{$this->DIR}/{$avatar->DisplayAvatar($user)}' alt=''>
              <div class='m_top_right'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($user, "username")}'>". $universal->nameShortener($universal->GETsDetails($user, "username"), 18) ."</a>
                <span>";
                echo $mutual->eMutual($user);
              echo "</span>
              </div></div>
              <span class='recommend_time'>{$Time->timeAgo($time)}</span>
              <div class='m_bottom'>";
              if($id == $session){
                echo "<a href='#' class='sec_btn rem_fav' data-userid='{$user}' data-username='{$universal->GETsDetails($user, "username")}'>Remove</a>";
              }
              echo "<div data-getid='$user' class='fav_ff'>";
              if ($session == $user) {
                echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($user, "username") ."' class='sec_btn '>Profile</a>";
              } else {
                if ($follow->isFollowing($user)) {
                  echo "<a href='#' class='pri_btn display_unfollow unfollow'>Unfollow</a>";
                } else if ($follow->isFollowing($user) == false) {
                  echo "<a href='#' class='pri_btn display_follow follow'>Follow</a>";
                }
              }
            echo "</div></div></div>";

        }
        echo "</div>";
        echo "<div class='post_end'>Looks like you've reached the end</div>";
      }

    }

    public function remFav($user){
      $session = $_SESSION['id'];
      if (self::isFav($user)) {
        $query = $this->db->prepare("DELETE FROM favourites WHERE fav_by = :me AND user = :user");
        $query->execute(array(":me" => $session, ":user" => $user));
        return "ok";
      } else {
        return "Favourite not found";
      }
    }

  }
?>
