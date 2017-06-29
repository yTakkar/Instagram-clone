<?php
  class editProfile{

    protected $db;
    protected $e;

    public function __construct(){
      try {
        $db = new PDO('mysql:host=host;dbname=instagram;charset=utf8mb4', 'user', 'password');
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $ee = $this->e;
      } catch (PDOException $ee) {
        echo $ee->getMessage();
      }
    }

    public function saveProfileEditing($username, $firstname, $surname, $bio, $instagram, $youtube, $facebook, $twitter, $website, $mobile, $tags){
      $session = $_SESSION['id'];
      $my = array();
      $users = $this->db->prepare("UPDATE users SET username = :username, firstname = :firstname, surname = :surname, bio = :bio, instagram = :instagram, youtube = :youtube, facebook = :facebook, twitter = :twitter, website = :website, mobile = :mobile WHERE id = :id");
      $users->execute(array(":username" => $username, ":firstname" => $firstname, ":surname" => $surname, ":bio" => $bio, ":instagram" => $instagram, ":youtube" => $youtube, ":facebook" => $facebook, ":twitter" => $twitter, ":website" => $website, ":mobile" => $mobile, ":id" => $session));
      $array = explode(",", $tags);
      foreach ($array as $value) {
        $my[$value] = trim($value);
      }
      $delete = $this->db->prepare("DELETE FROM tags WHERE user_id = :id");
      $delete->execute(array(":id" => $session));
      foreach ($my as $key => $value) {
        $insert = $this->db->prepare("INSERT INTO tags(user_id, tags) VALUES (:session, :each)");
        $insert->execute(array(":session" => $session, ":each" => $value));
      }

      $q = $this->db->prepare("UPDATE follow_system SET follow_by_u = :new WHERE follow_by = :me");
      $q->execute(array(":new" => $username, ":me" => $session));

      $r = $this->db->prepare("UPDATE follow_system SET follow_to_u = :new WHERE follow_to = :me");
      $r->execute(array(":new" => $username, ":me" => $session));

    }

    public function getProfileEditing(){
      $session = $_SESSION['id'];
      include 'universal.class.php';
      $universal = new universal;
      $array = array(
        "username" => $universal->GETsDetails($session, "username"),
        "firstname" => $universal->GETsDetails($session, "firstname"),
        "surname" => $universal->GETsDetails($session, "surname"),
        "bio" => $universal->GETsDetails($session, "bio"),
        "instagram" => $universal->GETsDetails($session, "instagram"),
        "youtube" => $universal->GETsDetails($session, "youtube"),
        "facebook" => $universal->GETsDetails($session, "facebook"),
        "twitter" => $universal->GETsDetails($session, "twitter"),
        "website" => $universal->GETsDetails($session, "website"),
        "mobile" => $universal->GETsDetails($session, "mobile")
      );
      echo json_encode($array);
    }

  }
?>
