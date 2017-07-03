<?php

  class bookmark{

    protected $db;

    public function __construct(){
      $db = N::_DB();
      $this->db = $db;
    }

    public function bookmarkedOrNot($post){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT bkmrk_id FROM bookmarks WHERE post_id = :post AND user_id = :user");
      $query->execute(array(":post" => $post, ":user" => $session));
      $count = $query->rowCount();
      if ($count == 0 || $count == null) {
        return false;
      } else {
        return true;
      }
    }

    public function bkmrk($post){
      $session = $_SESSION['id'];
      // $query = $this->db->prepare("SELECT post_id FROM bookmarks WHERE post_id = :post AND user_id = :user");
      // $query->execute(array(":post" => $post, ":user" => $session));
      // if ($query->rowCount() == 0) {
      if (self::bookmarkedOrNot($post) == false) {
        $q = $this->db->prepare("INSERT INTO bookmarks (post_id, user_id, bookmark_time) VALUES(:post, :user, now())");
        $q->execute(array(":post" => $post, ":user" => $session));
      }
      // }
    }

    public function unbkmrk($post){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("DELETE FROM bookmarks WHERE post_id = :post AND user_id = :user");
      $query->execute(array(":post" => $post, ":user" => $session));
    }

  }

?>
