<?php
  class suggestion{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function HomeSuggestions($when){
      $session = $_SESSION['id'];

      $follow = new follow_system;
      $universal = new universal;
      $avatar = new Avatar;
      $mutual = new mutual;

      $query = $this->db->prepare("SELECT id FROM users WHERE id <> :me ORDER BY RAND() LIMIT 5");
      $query->execute(array(":me" => $session));
      while ($row = $query->fetch(PDO::FETCH_OBJ)) {
        $id = $row->id;
        if ($follow->isFollowing($id) == false) {
          echo "<div class='recomms'><img src='{$this->DIR}/";
          if ($when == "direct") {
            echo $avatar->GETsAvatar($id);
          } else if ($when == "ajax") {
            echo $avatar->DisplayAvatar($id);
          }
          echo "' alt=''>
            <div class='recomms_cont'><a href='{$this->DIR}/profile/{$universal->GETsDetails($id, "username")}' class='recomms_username' data-getid='{$id}'>". $universal->nameShortener($universal->GETsDetails($id, "username"), 15) ."</a><span>";
            echo $mutual->eMutual($id);
            echo "</span></div><div class='recomms_ff' data-getid='{$id}'>";
            if ($follow->isFollowing($id)) {
              echo "<a href='#' class='unfollow pri_btn'>Unfollow</a>";
            } else if ($follow->isFollowing($id) == false) {
              echo "<a href='#' class='follow pri_btn'>Follow</a>";
            }
            echo "</div></div>";
        }
      }

    }

    public function userSuggCount($user){
      $session = $_SESSION['id'];
      $follow = new follow_system;
      $array = array();

      $query = $this->db->prepare("SELECT follow_by FROM follow_system WHERE follow_to = :user AND follow_by <> :me");
      $query->execute(array(":user" => $user, ":me" => $session));
      while ($row = $query->fetch(PDO::FETCH_OBJ)) {
        $id = $row->follow_by;
        if ($follow->isFollowing($id) == false) {
          $array[] = $id;
        }
      }

      return count($array);

    }

    public function UserSuggestions($user){
      $session = $_SESSION['id'];

      $follow = new follow_system;
      $universal = new universal;
      $avatar = new Avatar;
      $mutual = new mutual;

      $query = $this->db->prepare("SELECT follow_by FROM follow_system WHERE follow_to = :user AND follow_by <> :me ORDER BY RAND() LIMIT 5");
      $query->execute(array(":user" => $user, ":me" => $session));
      while ($row = $query->fetch(PDO::FETCH_OBJ)) {
        $id = $row->follow_by;
        if ($follow->isFollowing($id) == false) {
          echo "<div class='recomms'><img src='{$this->DIR}/{$avatar->DisplayAvatar($id)}' alt=''>
            <div class='recomms_cont'><a href='{$this->DIR}/profile/{$universal->GETsDetails($id, "username")}' class='recomms_username' class='' data-getid='{$id}'>". $universal->nameShortener($universal->GETsDetails($id, "username"), 15). "</a><span>";
            echo $mutual->eMutual($id);
            echo "</span></div><div class='recomms_ff' data-getid='{$id}'>";
            if ($follow->isFollowing($id)) {
              echo "<a href='#' class='unfollow pri_btn'>Unfollow</a>";
            } else if ($follow->isFollowing($id) == false) {
              echo "<a href='#' class='follow pri_btn'>Follow</a>";
            }
            echo "</div></div>";
        }
      }
    }

  }
?>
