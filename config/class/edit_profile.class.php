<?php
  class editProfile{

    protected $db;

    public function __construct(){
      $db = N::_DB();
      $this->db = $db;
    }

    public function saveProfileEditing($username, $firstname, $surname, $bio, $instagram, $youtube, $facebook, $twitter, $website, $mobile, $tags){
      $session = $_SESSION['id'];
      $my = array();
      $universal = new universal;
      $susername = $universal->GETsDetails($session, "username");

      if((!$username || $firstname || $surname ) == ""){
        return "Some values are missing!";
      } else {
        $uCount = $this->db->prepare('SELECT id FROM users WHERE username = :username');
        $uCount->execute(array(":username" => $username));
        
        if($uCount->rowCount() == 1 && $username != $susername){
          return "Username already exists!!";
        } else {
          
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

          return "Profile updated!!";

        }

      }

    }

  }
?>
