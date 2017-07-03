<?php
  include '../../config/declare.php';
  include_once '../../config/class/needy_class.php';
  include '../../config/class/avatar.class.php';
  include '../../config/class/universal.class.php';

  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {
    if (isset($_GET['value'])) {
      $value = preg_replace("#[^a-z0-9_@.]#i", "", $_GET['value']);
      $array = explode(",", $_GET['except']);
      $session = $_SESSION['id'];
      $new = array();
      $my = array();

      if ($value != "") {

        $avatar = new Avatar;
        $universal = new universal;
        $db = N::_DB();

        // QUERY FOR SELECTING FOLLOWERS ONLY
        $query = $db->prepare("SELECT DISTINCT follow_to_u FROM follow_system WHERE follow_to_u LIKE :username AND follow_by = :whome");
        $query->execute(array(":username" => "%$value%", ":whome" => $session));
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
          $new[] = $row['follow_to_u'];
        }

        // QUERY FOR SELECTING NON-FOLLOWERS ALSO
        // $query = $db->prepare("SELECT username FROM users WHERE username LIKE :username AND id != :mine");
        // $query->execute(array(":username" => "%$value%", ":mine" => $session));
        // while($row = $query->fetch(PDO::FETCH_ASSOC)){
        //   $new[] = $row['username'];
        // }

        foreach ($new as $value) {
          if (!in_array($value, $array)) {
            $my[] = $value;
          }
        }

        foreach ($my as $value) {
          $nquery = $db->prepare("SELECT id, username FROM users WHERE username = :what");
          $nquery->execute(array(":what" => $value));
          $row = $nquery->fetch(PDO::FETCH_OBJ);
          $id = $row->id;
          $username = $row->username;
          echo "<li class='tag_hmm'><img src='". DIR ."/" .$avatar->DisplayAvatar($id) ."' alt=''>";
          echo "<span>". $universal->nameShortener($username, 25) ."</span></li>";
        }

      }

    }
  }
?>
