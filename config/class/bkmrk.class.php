<?php

  class bookmark{

    protected $db;
    protected $e;

    public function __construct(){
      try {
        $db = new PDO('mysql:host=127.0.0.1;dbname=instagram;charset=utf8mb4', 'root', 'iamaprogrammer');
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $e = $this->e;
      } catch (PDOException $e) {
        echo $e->getMessage();
      }
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
