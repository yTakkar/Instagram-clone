<?php

  class Avatar{

    protected $db;

    public function __construct(){
      $db = N::_DB();
      $this->db = $db;
    }

    public function SESSIONsAvatar(){
      $session = $_SESSION['id'];
      $src = glob("users/$session/avatar/*");
      return $src[0];
    }

    public function GETsAvatar($get){
      $src = glob("users/$get/avatar/*");
      return $src[0];
    }

    public function DisplayAvatar($get){
      $src = glob("../../users/$get/avatar/*");
      $path = substr($src[0], 5);
      return $path;
    }

    public function deleteAvatars($when, $grp){
      $session = $_SESSION['id'];
      if ($when == "user") {
        $src = glob("../../users/$session/avatar/*");
      } else if ($when == "group") {
        $src = glob("../../group/$grp/*");
      }
      foreach ($src as $key => $value) {
        if (is_file($value)) {
          @unlink($value);
        }
      }
    }

    public function copyAvatar($og_file, $when, $grp){
      $session = $_SESSION['id'];
      $ext = pathinfo($og_file, PATHINFO_EXTENSION);
      $file = substr($_GET['change_avatar'], strrpos($_GET['change_avatar'], "/")+1);
      $new_name = time().".".$ext;

      $from = "../../images/avatars/$file";
      if ($when == "user") {
        $to = "../../users/$session/avatar/Instagram_".$new_name;
      } else if ($when == "group") {
        $to = "../../group/$grp/Instagram_".$new_name;

        $query = $this->db->prepare("UPDATE groups SET grp_avatar = :name WHERE group_id = :grp");
        $query->execute(array(":name" => $new_name, ":grp" => $grp));

      }

      @copy($from, $to);
      return substr($to, 6);
    }

    public function uploadedAndResize(){
      $name = $_FILES['pro_ch_ava']['name'];
      $tmp_name = $_FILES['pro_ch_ava']['tmp_name'];
      $error = $_FILES['pro_ch_ava']['error'];

      $ext = strtolower(end(explode('.', $name)));
      $allowed = array('jpg', 'png', 'gif', 'jpeg');

      if (in_array($ext, $allowed)) {
        if ($error == 0) {
          if (move_uploaded_file($tmp_name, "../../temp/uploaded/Uploaded_$name")) {
            include_once 'gd_library.class.php';
            $gd = new gd_library;
            $old = "../../temp/uploaded/Uploaded_$name";
            $new = "../../temp/resized/Resized_$name";
            $wmax = 400;
            $hmax = 450;
            $gd->resize($old, $new, $wmax, $hmax, $ext);
            $array = array(
              "name"   => $name
            );
            return json_encode($array);
          }
        }
      }
    }

    public function cropAvatar($when, $grp){
      $top = $_POST['top'];
      $left = $_POST['left'];
      $width = $_POST['width'];
      $height = $_POST['height'];
      $name = "../".$_POST['name'];

      $dst_x = 0;
      $dst_y = 0;
      $src_x = $left;
      $src_y = $top;
      $dst_w = $width;
      $dst_h = $height;
      $src_w = $width;
      $src_h = $height;

      $ext = strtolower(end(explode('.', $name)));
      $session = $_SESSION['id'];

      $dst_image = imagecreatetruecolor($dst_w, $dst_h);
      if ($ext == "gif"){
        $src_image = imagecreatefromgif($name);
      } else if($ext =="png"){
        $src_image = imagecreatefrompng($name);
      } else {
        $src_image = imagecreatefromjpeg($name);
      }

      imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

      $new_name = time();

      if ($ext == "gif"){
        if ($when == "user") {
          self::deleteAvatars("user", "");
          imagegif($dst_image, "../../users/$session/avatar/Instagram_".$new_name.".gif");
          return "$session/avatar/Instagram_".$new_name.".gif";

        } else if ($when == "group") {
          self::deleteAvatars("group", $grp);
          imagegif($dst_image, "../../group/$grp/Instagram_".$new_name.".gif");

          $query = $this->db->prepare("UPDATE groups SET grp_avatar = :name WHERE group_id = :grp");
          $query->execute(array(":name" => $new_name.".gif", ":grp" => $grp));

          return "$grp/Instagram_".$new_name.".gif";
        }

      } else if($ext =="png"){
        if ($when == "user") {
          self::deleteAvatars("user", "");
          imagepng($dst_image, "../../users/$session/avatar/Instagram_".$new_name.".png");
          return "$session/avatar/Instagram_".$new_name.".png";

        } else if ($when == "group") {
          self::deleteAvatars("group", $grp);
          imagepng($dst_image, "../../group/$grp/Instagram_".$new_name.".png");

          $query = $this->db->prepare("UPDATE groups SET grp_avatar = :name WHERE group_id = :grp");
          $query->execute(array(":name" => $new_name.".png", ":grp" => $grp));

          return "$grp/Instagram_".$new_name.".png";
        }

      } else {
        if ($when == "user") {
          self::deleteAvatars("user", "");
          imagejpeg($dst_image, "../../users/$session/avatar/Instagram_".$new_name.".jpg");
          return "$session/avatar/Instagram_".$new_name.".jpg";

        } else if ($when == "group") {
          self::deleteAvatars("group", $grp);
          imagejpeg($dst_image, "../../group/$grp/Instagram_".$new_name.".jpg");

          $query = $this->db->prepare("UPDATE groups SET grp_avatar = :name WHERE group_id = :grp");
          $query->execute(array(":name" => $new_name.".jpg", ":grp" => $grp));

          return "$grp/Instagram_".$new_name.".jpg";
        }

      }

    }

  }

  function getAva($get){
    $src = glob("users/$get/avatar/*");
    echo $src[0];
  }

?>
