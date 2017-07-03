<?php

  class postComment{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function simpleGetComments($post){
      $query = $this->db->prepare("SELECT post_comments_id FROM post_comments WHERE post_id = :post");
      $query->execute(array(":post" => $post));
      $count = $query->rowCount();
      return $count;
    }

    public function getComments($post){
      $array1 = array();
      $array2 = array();
      $final = array();
      $array3 = array();

      $session = $_SESSION['id'];
      $universal = new universal;

      $query = $this->db->prepare("SELECT DISTINCT user_id FROM post_comments WHERE post_id = :post");
      $query->execute(array(":post" => $post));
      while ($row = $query->fetch(PDO::FETCH_OBJ)) {
        $array1[] = $row->user_id;
      }

      $query = $this->db->prepare("SELECT follow_to FROM follow_system WHERE follow_by = :get");
      $query->execute(array(":get" => $session));
      while ($row = $query->fetch(PDO::FETCH_OBJ)) {
        $array2[] = $row->follow_to;
      }

      foreach ($array1 as $value) {
        if (in_array($value, $array2)) {
          $final[] = $value;
        }
      }

      $return = array_diff($array1, $final);

      foreach ($return as $key => $value) {
        $array3[] = $value;
      }

      $mine = array_reverse($final);

      foreach ($mine as $key => $value) {
        array_unshift($array3, $value);
      }

      $count = count($array3);

      if ($count == 0) {
        return "No comments";

      } else if ($count == 1) {
        if ($array3[0] == $session) {
          return "You commented";
        } else{
          return $universal->nameShortener($universal->GETsDetails($array3[0], "username"), 15)." commented";
        }

      } else if ($count == 2) {
        if (in_array($session, $array3)) {
          return "You and ".$universal->nameShortener($universal->GETsDetails($array3[0], "username"), 15)." commented";
        } else {
          return $universal->nameShortener($universal->GETsDetails($array3[0], "username"), 15)." and ".$universal->nameShortener($universal->GETsDetails($array3[1], "username"), 15)." commented";
        }

      } else if ($count == 3) {
        if (in_array($session, $array3)) {
          return "You, ".$universal->nameShortener($universal->GETsDetails($array3[0], "username"), 15)." and 1 other commented";
        } else {
          return $universal->nameShortener($universal->GETsDetails($array3[0], "username"), 15).", ".$universal->nameShortener($universal->GETsDetails($array3[1], "username"), 15)." and 1 other commented";
        }

      } else if ($count > 3) {
        $slice = array_slice($array3, 2);
        if (in_array($session, $array3)) {
          return "You, ".$universal->nameShortener($universal->GETsDetails($array3[0], "username"), 15)." and ". count($slice) ." others commented";
        } else {
          return $universal->nameShortener($universal->GETsDetails($array3[0], "username"), 15).", ".$universal->nameShortener($universal->GETsDetails($array3[1], "username"), 15)." and ". count($slice) ." others commented";
        }
      }

      // return " commented (".self::simpleGetComments($post).")";

    }

    public function commentMention($value, $post){
      $universal = new universal;
      $noti = new notifications;
      $array = explode(" ", trim($value));
      foreach ($array as $key) {
        $key = trim($key);
        if (@$key[0] == "@") {
          $user = substr($key, 1);
          $query = $this->db->prepare("SELECT username FROM users WHERE username = :username");
          $query->execute(array(":username" => $user));

          if ($query->rowCount() == 1) {
            $to = $universal->getIdFromGet($user);
            $noti->actionNotify($to, $post, "comment_mention");
          }

        }
      }
    }

    public function comment($value, $post){
      $session = $_SESSION['id'];
      include 'notifications.class.php';
      include 'post.class.php';
      include 'settings.class.php';

      $noti = new notifications;
      $Post = new post;
      $universal = new universal;
      $settings = new settings;

      $to = $Post->postDetails($post, "user_id");

      if ($settings->AmIBlocked($to) == false) {

        // $q = $this->db->prepare("SELECT post_id FROM post_comments WHERE post_id = :post AND user_id = :user AND data = :data AND type = :type");
        // $q->execute(array(":post" => $post, ":user" => $session, ":data" => $value, ":type" => "text"));
        // if ($q->rowCount() == 0) {
          $query = $this->db->prepare("INSERT INTO post_comments (post_id, user_id, data, type, time) VALUES(:post, :user, :data, :type, now())");
          $query->execute(array(":post" => $post, ":user" => $session, ":data" => $value, ":type" => "text"));
          $noti->actionNotify($to, $post, "comment");
          self::commentMention($value, $post);
          return "ok";
        // }
      }

    }

    public function imageComment($file, $post){
      $session = $_SESSION['id'];

      include 'notifications.class.php';
      include 'post.class.php';
      include 'settings.class.php';
      include 'universal.class.php';

      $noti = new notifications;
      $Post = new post;
      $universal = new universal;
      $settings = new settings;

      $name = $file['name'];
      $size = $file['size'];
      $tmp_name = $file['tmp_name'];
      $error = $file['error'];
      $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
      $allowed = array("jpg", "png", "gif", "jpeg");

      $to = $Post->postDetails($post, "user_id");
      if($settings->AmIBlocked($to) == false){
        if (in_array($ext, $allowed)) {
          if ($error == 0) {
            $new_name = time().".".$ext;
            if (move_uploaded_file($tmp_name, "../../comments/Instagram_".$new_name)) {

              $q = $this->db->prepare("SELECT post_id FROM post_comments WHERE post_id = :post AND user_id = :user AND data = :data AND type = :type");
              $q->execute(array(":post" => $post, ":user" => $session, ":data" => $new_name, ":type" => "image"));
              if ($q->rowCount() == 0) {
                $query = $this->db->prepare("INSERT INTO post_comments (post_id, user_id, data, type, time) VALUES(:post, :user, :data, :type, now())");
                $query->execute(array(":post" => $post, ":user" => $session, ":data" => $new_name, ":type" => "image"));
                $to = $Post->postDetails($post, "user_id");
                $noti->actionNotify($to, $post, "comment");
                return "ok";
              }
            }
          }
        }
      }

    }

    public function comment_likes($comment){
      $query = $this->db->prepare("SELECT comment_like_id FROM comment_likes WHERE comment_id = :comment");
      $query->execute(array(":comment" => $comment));
      $count = $query->rowCount();
      if ($count == 0) {
        return "No likes";
      } else if ($count == 1) {
        return "1 like";
      } else {
        return "$count likes";
      }
    }

    public function commentIsLiked($comment){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT comment_like_id FROM comment_likes WHERE comment_id = :comment AND like_by = :by");
      $query->execute(array(":comment" => $comment, ":by" => $session));
      $count = $query->rowCount();
      if ($count == 0) {
        return false;
      } else if ($count == 1) {
        return true;
      }
    }

    public function likeComments($comment){
      include 'notifications.class.php';
      $noti = new notifications;

      $session = $_SESSION['id'];

      $cquery = $this->db->prepare("SELECT comment_like_id FROM comment_likes WHERE like_by = :by AND comment_id = :id LIMIT 1");
      $cquery->execute(array(":by" => $session, ":id" => $comment));
      if ($cquery->rowCount() == 0) {

        $query = $this->db->prepare("INSERT INTO comment_likes(like_by, comment_id, time) VALUES (:by, :comment, now())");
        $query->execute(array(":by" => $session, ":comment" => $comment));

        $query = $this->db->prepare("SELECT post_id, user_id FROM post_comments WHERE post_comments_id = :comment LIMIT 1");
        $query->execute(array(":comment" => $comment));
        $row = $query->fetch(PDO::FETCH_OBJ);
        $post = $row->post_id;
        $to = $row->user_id;

        if ($to != $session) {
          $noti->cLikeNotify($session, $to, $post, $comment);
        }

      }
    }

    public function unlikeComments($comment){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("DELETE FROM comment_likes WHERE like_by = :by AND comment_id = :comment");
      $query->execute(array(":by" => $session, ":comment" => $comment));
    }

    public function commentLikers($comment){
      $session = $_SESSION['id'];

      include 'universal.class.php';
      include 'avatar.class.php';
      include 'follow_system.class.php';

      $universal = new universal;
      $avatar = new Avatar;
      $follow = new follow_system;

      $query = $this->db->prepare("SELECT like_by FROM comment_likes WHERE comment_id = :comment ORDER BY time DESC");
      $query->execute(array(":comment" => $comment));
      if ($query->rowCount() == 0) {
        echo "<div class='no_display'><img src='{$this->DIR}/images/needs/large.jpg'></div>";
      } else if ($query->rowCount() != 0) {
        while ($fetch = $query->fetch(PDO::FETCH_OBJ)) {
          $userid = $fetch->like_by;
          echo "<div class='display_items' data-getid='$userid'><div class='d_i_img'>";
          echo "<img src='{$this->DIR}/". $avatar->DisplayAvatar($userid) ."' alt='profile'>";
          echo "</div><div class='d_i_content'><div class='d_i_info'>";
          echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($userid, "username") ."' class='d_i_username username'>". $universal->nameShortener($universal->GETsDetails($userid, "username"), 20) ."</a>";
          echo "<span class='d_i_name'>". $universal->nameShortener($universal->GETsDetails($userid, "firstname")." ".$universal->GETsDetails($userid, "surname"), 30) ."</span></div><div class='d_i_act display_ff' data-getid='$userid'>";
          if ($session == $userid) {
            echo "<a href='{$this->DIR}/profile/". $universal->GETsDetails($userid, "username") ."' class='sec_btn '>Profile</a>";
          } else {
            if ($follow->isFollowing($userid)) {
              echo "<a href='#' class='pri_btn display_unfollow unfollow'>Unfollow</a>";
            } else if ($follow->isFollowing($userid) == false) {
              echo "<a href='#' class='pri_btn display_follow follow'>Follow</a>";
            }
          }
          echo "</div></div><hr></div>";
        }
      }
    }

    public function MyCommentOrNot($comment){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT user_id FROM post_comments WHERE post_comments_id = :comment AND user_id = :user");
      $query->execute(array(":comment" => $comment, ":user" => $session));
      $count = $query->rowCount();
      if ($count == 0) {
        return false;
      } else if ($count > 0) {
        return true;
      }
    }

    public function comments($post){
      $universal = new universal;
      $avatar = new Avatar;
      $Time = new time;
      $hashtag = new hashtag;
      $mention = new mention_class;

      $session = $_SESSION['id'];

      $query = $this->db->prepare("SELECT * FROM post_comments WHERE post_id = :post ORDER BY time DESC");
      $query->execute(array(":post" => $post));
      $count = $query->rowCount();
      if ($count > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $comment_id = $row->post_comments_id;
          $post_id = $row->post_id;
          $user_id = $row->user_id;
          $data = $row->data;
          $type = $row->type;
          $time = $row->time;

          if ($type == "text") {
            $data = $hashtag->toHashtag($data, "comment");
            $data = $mention->mention($data);
            $data = $universal->toAbsURL($data);

            $return = "<p class='ce' spellcheck='false'>". nl2br($data) ."</p>";

          } else if ($type == "image") {
            if($user_id == $session){ $z = "You"; } else { $z = $universal->GETsDetails($user_id, "username");}
            $return = "<img src='{$this->DIR}/comments/Instagram_{$data}' class='comments_img' data-imgby='{$z}' data-time='{$Time->timeAgo($time)}'>";

          } else if ($type == "sticker") {
            $return = "<img src='{$this->DIR}/comments/Instagram_{$data}' class='comment_sticker'>";
          }

          echo "<div class='comments ";
          if (self::MyCommentOrNot($comment_id)) {
            echo "my_comment";
          }
          echo "' data-commentid='{$comment_id}'><img class='comments_avatar' src='{$this->DIR}/{$avatar->GETsAvatar($user_id)}'>
            <div class='comments_content'><a href='{$this->DIR}/profile/{$universal->GETsDetails($user_id, "username")}' class='comments_user'>{$universal->GETsDetails($user_id, "username")}</a>";

            if ($type == "text") {
              echo "<div class='comment_edit_tools'><a href='#' class='comment_cancel sec_btn'>Cancel</a>
              <a href='#' class='comment_save pri_btn'>Save</a></div>";
            }

              echo "{$return}<div class='comments_bottom'><div class='comment_' data-commentid='{$comment_id}'>";
              if (self::commentIsLiked($comment_id)) {
                echo "<span class='comment_unlike comment_lu' title='Unlike'><i class='material-icons'>thumb_down</i></span>";
              } else if (self::commentIsLiked($comment_id) == false) {
                echo "<span class='comment_like comment_lu' title='Like'><i class='material-icons'>thumb_up</i></span>";
              }
              echo "</div><a class='comment_likes' href='#' title='Likes'>". self::comment_likes($comment_id) ."</a>
                <span class='comments_time' title='{$Time->normalTime($time)}'>{$Time->timeAgo($time)} ago</span>
              </div>
            </div>";
            if (self::MyCommentOrNot($comment_id)) {
              echo "<div class='comment_tools'>";
              if ($type == "text") {
                echo "<span class='comment_edit'><i class='material-icons'>mode_edit</i></span>";
              }
              echo "<span class='comment_delete'><i class='material-icons'>delete</i></span></div>";
            }
          echo "</div>";

        }
      }
    }

    public function deleteComment($id){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("DELETE FROM post_comments WHERE post_comments_id = :id AND user_id = :user");
      $query->execute(array(":id" => $id, ":user" => $session));
    }

    public function editComment($text, $comment, $post){
      include 'universal.class.php';
      include 'notifications.class.php';
      $session = $_SESSION['id'];
      if ($text == "") {
        self::deleteComment($comment);
      } else {
        $query = $this->db->prepare("UPDATE post_comments SET data = :data WHERE post_comments_id = :comment");
        $query->execute(array(":data" => $text, ":comment" => $comment));
        self::commentMention($text, $post);
      }
    }

    public function commSticker($sticker, $post){
      $session = $_SESSION['id'];

      include 'notifications.class.php';
      include 'post.class.php';
      include 'universal.class.php';
      include 'settings.class.php';

      $noti = new notifications;
      $Post = new post;
      $universal = new universal;
      $settings = new settings;

      $to = $Post->postDetails($post, "user_id");
      if($settings->AmIBlocked($to) == false){
        $ext = pathinfo($sticker, PATHINFO_EXTENSION);
        $image = substr($sticker, strrpos($sticker, "/")+1);
        $from = "../../images/stickers/$image";
        $to = "../../comments/Instagram_".time().".".$ext;
        $new_name = substr($to, 25);
        @copy($from, $to);
        $query = $this->db->prepare("INSERT INTO post_comments (post_id, user_id, data, type, time) VALUES(:post, :user, :data, :type, now())");
        $query->execute(array(":post" => $post, ":user" => $session, ":data" => $new_name, ":type" => "sticker"));
        $to = $Post->postDetails($post, "user_id");
        $noti->actionNotify($to, $post, "comment");
      }

    }

  }

?>
