<?php
  class tags extends universal{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function filterTags(){
      $delete = $this->db->exec("DELETE FROM tags WHERE tags = ''");
    }

    public function get_tags($get){
      $query = $this->db->prepare("SELECT tags FROM tags WHERE user_id = :id");
      $query->execute(array(":id" => $get));
      if ($query->rowCount() == 0 || $query->rowCount() == null) {
        if (parent::MeOrNot($get)) {
          echo "You got no tags! <a href='{$this->DIR}/edit' class='add_tags'>add</a>";
        } else {
          echo parent::GETsDetails($get, "username")." got no tags";
        }
      } else if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $each = $row->tags;
          $tag = "<a href='{$this->DIR}/tag?tag={$each}' class='tags'>$each</a>";
          echo $tag;
        }
      }
    }

    public function userTags($user, $when){
      if ($when == "user") { $limit = "LIMIT 6"; } else if ($when == "page") { $limit = ""; }
      $query = $this->db->prepare("SELECT tags FROM tags WHERE user_id = :id ORDER BY tag_id $limit");
      $query->execute(array(":id" => $user));
      if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $each = $row->tags;
          $tag = "<a href='{$this->DIR}/tag?tag={$each}' class='tags'>{$each}</a>";
          echo $tag;
        }
      }
    }

    public function popularTags(){
      $query = $this->db->query("SELECT tags, COUNT(tags) as c FROM tags GROUP BY tags ORDER BY c DESC LIMIT 15");
      if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $tags = $row->tags;
          echo "<a href='{$this->DIR}/tag?tag={$tags}' class='tags'>{$tags}</a>";
        }
      }
    }

    public function getTagsEdit($get){
      $query = $this->db->prepare("SELECT tags FROM tags WHERE user_id = :id");
      $query->execute(array(":id" => $get));
      if ($query->rowCount() != null) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $each = $row->tags;
          $tag = "<span class='t_a_tag'>". $each ."</span>";
          echo $tag;
        }
      }
    }

    public function noOfTagPeople($tag){
      $query = $this->db->prepare("SELECT user_id, tags FROM tags WHERE tags = :tag");
      $query->execute(array(":tag" => $tag));
      $count = $query->rowCount();
      if ($count == 0) {
        return "No";
      } else {
        return $count;
      }
    }

    public function sameTagPeople($tag){
      $session = $_SESSION['id'];

      $universal = new universal;
      $avatar = new Avatar;
      $follow = new follow_system;
      $mutual = new mutual;

      $query = $this->db->prepare("SELECT user_id, tags FROM tags WHERE tags = :tag");
      $query->execute(array(":tag" => $tag));
      if ($query->rowCount() == 0) {
        echo "<div class='home_last_mssg hashtag_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'><span>No one with {$tag} found</span></div>";
      } else if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $user = $row->user_id;
          $tags = $row->tags;

          echo
          "<div class='m_on inst tag_peo'><div class='m_top'>
            <img src='{$this->DIR}/{$avatar->GETsAvatar($user)}' alt=''>
            <div class='m_top_right'>
              <a href='{$this->DIR}/profile/'>";
              if ($user == $session) {
                echo "You";
              } else {
                echo $universal->GETsDetails($user, "username");
              }
              echo "</a>
              <span>{$mutual->eMutual($user)}</span>
            </div>
          </div>

          <div data-getid='{$user}' class='tag_peo_ff'>";
          if ($user != $session) {
            if ($follow->isFollowing($user)) {
              echo "<a href='#' class='pri_btn unfollow'>Unfollow</a>";
            } else if ($follow->isFollowing($user) == false) {
              echo "<a href='#' class='pri_btn follow'>Follow</a>";
            }
          }

          echo "</div>
          <div class='m_bottom'>";
          self::userTags($user, "user");
          echo "</div>
        </div>";

        }
      }

    }

  }
?>
