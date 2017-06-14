<?php
  class search{

    protected $db;
    protected $e;

    public function __construct(){
      try {
        $db = new PDO('mysql:host=host;dbname=instagram;charset=utf8mb4', 'root', 'user');
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $e = $this->e;
      } catch (PDOException $e) {
        echo $e->getMessage();
      }
    }

    public function searchInstagram($value){
      $session = $_SESSION['id'];

      $universal = new universal;
      $avatar = new Avatar;
      $mutual = new mutual;
      $groups = new group;
      $Post = new post;
      $hashtag = new hashtag;

      echo "<div class='s_d_people s_d'><span class='s_header'>People</span><div class='s_d_peo'>";

      $q1 = $this->db->prepare("SELECT id, username FROM users WHERE username LIKE :username ORDER BY id LIMIT 8");
      $q1->execute(array(":username" => "%$value%"));
      if ($q1->rowCount() > 0) {
        while ($r1 = $q1->fetch(PDO::FETCH_OBJ)) {
          $userid = $r1->id;
          $user = $r1->username;

          echo
            "<a class='s_d_p' href='/faiyaz/Instagram/profile/{$user}'><img src='/faiyaz/Instagram/{$avatar->DisplayAvatar($userid)}' alt=''>
            <div class='s_d_c'><span class='s_d_username'>". $universal->nameShortener($user, 30). "</span>
            <span>{$mutual->eMutual($userid)}</span></div></a>";

        }
      }

      echo "</div></div>";
      echo "<div class='s_d_groups s_d'><span class='s_header'>Groups</span><div class='s_d_peo'>";

      $q2 = $this->db->prepare("SELECT group_id, grp_name FROM groups WHERE grp_name LIKE :group ORDER BY group_id LIMIT 8");
      $q2->execute(array(":group" => "%$value%"));
      if ($q2->rowCount() > 0) {
        while ($r2 = $q2->fetch(PDO::FETCH_OBJ)) {
          $grpid = $r2->group_id;
          $grpname = $r2->grp_name;

          echo
            "<a class='s_d_p' href='/faiyaz/Instagram/groups/{$grpid}'><img src='{$groups->grpAvatar($grpid)}' alt=''>
            <div class='s_d_c'><span class='s_d_username'>". $universal->nameShortener($grpname, 30). "</span>
            <span>{$groups->noOfGrpMembers($grpid)} members</span></div></a>";

        }
      }

      echo "</div></div>";
      echo " <div class='s_d_hashtags s_d'><span class='s_header'>Hashtags</span><div class='s_d_peo'>";

      $tag = preg_replace("#[\#]#", "", $value);
      $q3 = $this->db->prepare("SELECT DISTINCT hashtag FROM hashtag WHERE hashtag LIKE :hashtag ORDER BY hashtag_id LIMIT 12");
      $q3->execute(array(":hashtag" => "%#$tag%"));
      if ($q3->rowCount() > 0) {
        while ($r3 = $q3->fetch(PDO::FETCH_OBJ)) {
          $hashname = $r3->hashtag;

          echo
            "<a class='s_d_p h_d_p' href='/faiyaz/Instagram/hashtag?tag=". substr($hashname, 1) ."'><span class='s_d_username'>". $universal->nameShortener($hashname, 30) ."</span>";
            // echo "<span class='s_d_light'>";
            // if ("#".$tag == $hashname) {
            //   echo $hashtag->noOfHashTags("$tag")." posts";
            // } else {
            //   echo "Working..";
            // }
            // echo "</span>
            echo "</a>";

        }
      }

      echo "</div></div>";

    }

  }
?>
