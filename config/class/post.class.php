<?php
  class post{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function postCount($id){
      $query = $this->db->prepare("SELECT post_id FROM post WHERE user_id =:id AND post_of <> :grp");
      $query->execute(array(":id" => $id, ":grp" => "group"));
      $count = $query->rowCount();
      return $count;
    }

    public function getPost($post, $what){
      $query = $this->db->prepare("SELECT $what FROM post WHERE post_id = :post");
      $query->execute(array(":post" => $post));
      if ($query->rowCount() > 0) {
        $row = $query->fetch(PDO::FETCH_OBJ);
        return $row->$what;
      }
    }

    public function getLink($link){
      if ($link != "") {
        include 'simple_html_dom.php';

        $content = file_get_html($link);

        foreach ($content->find('title') as $element) {
          $title = $element->plaintext;
        }

        $image = array();
        array_slice($content->find('img'), 0, 15);

        foreach($content->find('img') as $element){
      		if(filter_var($element->src, FILTER_VALIDATE_URL)){
      			list($width,$height) = getimagesize($element->src);
      			if($width>75 && $height>75){
      				$image[] =  $element->src;
      			}
      		}
      	}

        if (sizeof($image) == 0) {
          $image[0] = "{$this->DIR}/images/Default_Link_Cover/world.jpg";
        }

        $array = array(
          "title" => $title,
          "url"   => $link,
          "src"   => $image[0]
        );

        echo json_encode($array);
      }
    }

    public function addressN($add, $id){
      $universal = new universal;
      if ($add == '') {
        return $universal->nameShortener($universal->GETsDetails($id, "firstname")." ".$universal->GETsDetails($id, "surname"), 45);
      } else {
        return $universal->nameShortener($add, 45);
      }
    }

    public function addressTitle($add, $id){
      $universal = new universal;
      if ($add == '') {
        return $universal->GETsDetails($id, "firstname")." ".$universal->GETsDetails($id, "surname");
      } else {
        return $add;
      }
    }

    public function postDetails($post, $what){
      $query = $this->db->prepare("SELECT $what FROM post WHERE post_id = :post LIMIT 1");
      $query->execute(array(":post" => $post));
      $row = $query->fetch(PDO::FETCH_OBJ);
      return $row->$what;
    }

    public function textPost($value, $tags, $font, $loc, $when, $grp){

      $universal = new universal;
      $noti = new notifications;
      $hashtag = new hashtag;
      $mention = new mention_class;

      $session = $_SESSION['id'];

      if ($when == "user") {
        $pquery = $this->db->prepare("INSERT INTO post (user_id, type, time, font_size, address) VALUES (:id, :type, now(), :size, :loc)");
        $pquery->execute(array(":id" => $session, ":type" => "text", ":size" => $font, ":loc" => $loc));
      } else if ($when == "group") {
        $pquery = $this->db->prepare("INSERT INTO post (user_id, type, post_of, grp_id, time, font_size, address) VALUES (:id, :type, :of, :grp, now(), :size, :loc)");
        $pquery->execute(array(":id" => $session, ":type" => "text", ":of" => "group", ":grp" => $grp, ":size" => $font, ":loc" => $loc));
      }

      $last_id = $this->db->lastInsertId();

      $tquery = $this->db->prepare("INSERT INTO text_post (post_id, text) VALUES (:post_id, :post)");
      $tquery->execute(array(":post_id" => $last_id, ":post" => $value));

      $mention->getMentions($value, $last_id);
      $hashtag->getHashtags($value, $last_id);

      if ($when == "user") {
        $array = explode(",", $tags);
        foreach ($array as $value) {
          if ($value != "") {
            $my = $universal->getIdFromGet($value);
            $fquery = $this->db->prepare("INSERT INTO taggings (post_id, taggings, taggings_id, taggings_time) VALUES (:post_id, :tags, :taggs_id, now())");
            $fquery->execute(array(":post_id" => $last_id, ":tags" => $value, ":taggs_id" => $my));
            $noti->actionNotify($my, $last_id, "tag");
          }
        }
      }

    }

    public function imagePost($file, $value, $tags, $font, $loc, $filter, $when, $grp){
      $session = $_SESSION['id'];

      $universal = new universal;
      $noti = new notifications;
      $hashtag = new hashtag;
      $mention = new mention_class;

      $name = $file['name'];
      $size = $file['size'];
      $tmp_name = $file['tmp_name'];
      $error = $file['error'];
      $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
      $allowed = array("jpg", "png", "gif", "jpeg");
      $font_size = preg_replace("#[^0-9]#i", "", $font);

      if (in_array($ext, $allowed)) {
        if ($error == 0) {
          $new_name = time().".".$ext;
          if (move_uploaded_file($tmp_name, "../../media/Instagram_".$new_name)) {

            if ($when == "user") {
              $query = $this->db->prepare("INSERT INTO post (user_id, type, time, font_size, address) VALUES (:user, :type, now(), :font, :loc)");
              $query->execute(array(":user" => $session, ":type" => "image", ":font" => $font_size, ":loc" => $loc));
            } else if ($when == "group") {
              $query = $this->db->prepare("INSERT INTO post (user_id, type, post_of, grp_id, time, font_size, address) VALUES (:user, :type, :of, :grp, now(), :font, :loc)");
              $query->execute(array(":user" => $session, ":type" => "image", ":of" => "group", ":grp" => $grp, ":font" => $font_size, ":loc" => $loc));
            }

            $last_id = $this->db->lastInsertId();

            $mention->getMentions($value, $last_id);
            $hashtag->getHashtags($value, $last_id);

            $iquery = $this->db->prepare("INSERT INTO image_post (post_id, image, about, filter) VALUES (:id, :image, :about, :fltr)");
            $iquery->execute(array(":id" => $last_id, ":image" => $new_name, ":about" => $value, ":fltr" => $filter));

            if ($when == "user") {
              $array = explode(",", $tags);
              foreach ($array as $value) {
                if ($value != "") {
                  $my = $universal->getIdFromGet($value);
                  $tquery = $this->db->prepare("INSERT INTO taggings (post_id, taggings, taggings_id, taggings_time) VALUES (:post_id, :tags, :taggs_id, now())");
                  $tquery->execute(array(":post_id" => $last_id, ":tags" => $value, ":taggs_id" => $my));
                  $noti->actionNotify($my, $last_id, "tag");
                }
              }
            }

          }
        }
      }
    }

    public function videoPost($file, $value, $tags, $font, $loc, $when, $grp){
      $session = $_SESSION['id'];

      $universal = new universal;
      $noti = new notifications;
      $hashtag = new hashtag;
      $mention = new mention_class;

      $name = $file['name'];
      $size = $file['size'];
      $tmp_name = $file['tmp_name'];
      $error = $file['error'];
      $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
      $allowed = array("mp4", "ogg");
      $font_size = preg_replace("#[^0-9]#i", "", $font);

      if (in_array($ext, $allowed)) {
        if ($error == 0) {
          if ($size <= 10485760) {
            $new_name = time().".".$ext;
            if (move_uploaded_file($tmp_name, "../../media/Instagram_".$new_name)) {

              if ($when == "user") {
                $query = $this->db->prepare("INSERT INTO post (user_id, type, time, font_size, address) VALUES (:user, :type, now(), :font, :loc)");
                $query->execute(array(":user" => $session, ":type" => "video", ":font" => $font_size, ":loc" => $loc));

              } else if ($when == "group") {
                $query = $this->db->prepare("INSERT INTO post (user_id, type, post_of, grp_id, time, font_size, address) VALUES (:user, :type, :of, :grp, now(), :font, :loc)");
                $query->execute(array(":user" => $session, ":type" => "video", ":of" => "group", ":grp" => $grp, ":font" => $font_size, ":loc" => $loc));
              }


              $last_id = $this->db->lastInsertId();

              $mention->getMentions($value, $last_id);
              $hashtag->getHashtags($value, $last_id);

              $iquery = $this->db->prepare("INSERT INTO video_post (post_id, video, about) VALUES (:id, :image, :about)");
              $iquery->execute(array(":id" => $last_id, ":image" => $new_name, ":about" => $value));

              if ($when == "user") {
                $array = explode(",", $tags);
                foreach ($array as $value) {
                  if ($value != "") {
                    $my = $universal->getIdFromGet($value);
                    $tquery = $this->db->prepare("INSERT INTO taggings (post_id, taggings, taggings_id, taggings_time) VALUES (:post_id, :tags, :taggs_id, now())");
                    $tquery->execute(array(":post_id" => $last_id, ":tags" => $value, ":taggs_id" => $my));
                    $noti->actionNotify($my, $last_id, "tag");
                  }
                }
              }

            }
          }
        }
      }
    }

    public function audioPost($file, $value, $tags, $font, $loc){
      $session = $_SESSION['id'];

      $universal = new universal;
      $noti = new notifications;
      $hashtag = new hashtag;
      $mention = new mention_class;

      $name = preg_replace("#[\'\"]#i", "", $file['name']);
      $size = $file['size'];
      $tmp_name = $file['tmp_name'];
      $error = $file['error'];
      $ext = strtolower(end(explode(".", $name)));
      $allowed = array("mp3");
      $font_size = preg_replace("#[^0-9]#i", "", $font);

      if (in_array($ext, $allowed)) {
        if ($error == 0) {
          $new_name = time().".".$ext;
          if (move_uploaded_file($tmp_name, "../../media/".$name)) {
            $query = $this->db->prepare("INSERT INTO post (user_id, type, time, font_size, address) VALUES (:user, :type, now(), :font, :loc)");
            $query->execute(array(":user" => $session, ":type" => "audio", ":font" => $font_size,":loc" => $loc));
            $last_id = $this->db->lastInsertId();

            $mention->getMentions($value, $last_id);
            $hashtag->getHashtags($value, $last_id);

            $iquery = $this->db->prepare("INSERT INTO audio_post (post_id, audio, about) VALUES (:id, :image, :about)");
            $iquery->execute(array(":id" => $last_id, ":image" => $name, ":about" => $value));

            $array = explode(",", $tags);
            foreach ($array as $value) {
              if ($value != "") {
                $my = $universal->getIdFromGet($value);
                $tquery = $this->db->prepare("INSERT INTO taggings (post_id, taggings, taggings_id, taggings_time) VALUES (:post_id, :tags, :taggs_id, now())");
                $tquery->execute(array(":post_id" => $last_id, ":tags" => $value, ":taggs_id" => $my));
                $noti->actionNotify($my, $last_id, "tag");
              }
            }
          }
        }
      }
    }

    public function docPost($file, $value, $tags, $font, $loc, $when, $grp){
      $session = $_SESSION['id'];

      $universal = new universal;
      $noti = new notifications;
      $hashtag = new hashtag;
      $mention = new mention_class;

      $name = preg_replace("#[\'\"]#i", "", $file['name']);
      $size = $file['size'];
      $tmp_name = $file['tmp_name'];
      $error = $file['error'];
      $ext = strtolower(end(explode(".", $name)));
      $allowed = array("js", "css", "html", "txt", "php", "cpp", "py", "ini", "zip", "pdf");
      $font_size = preg_replace("#[^0-9]#i", "", $font);

      if (in_array($ext, $allowed)) {
        if ($error == 0) {
          if (move_uploaded_file($tmp_name, "../../media/".$name)) {

            if ($when == "user") {
              $query = $this->db->prepare("INSERT INTO post (user_id, type, time, font_size, address) VALUES (:user, :type, now(), :font, :loc)");
              $query->execute(array(":user" => $session, ":type" => "document", ":font" => $font_size, ":loc" => $loc));
            } else if ($when == "group") {
              $query = $this->db->prepare("INSERT INTO post (user_id, type, post_of, grp_id, time, font_size, address) VALUES (:user, :type, :of, :grp, now(), :font, :loc)");
              $query->execute(array(":user" => $session, ":type" => "document", ":of" => "group", ":grp" => $grp, ":font" => $font_size, ":loc" => $loc));
            }

            $last_id = $this->db->lastInsertId();

            $mention->getMentions($value, $last_id);
            $hashtag->getHashtags($value, $last_id);

            $iquery = $this->db->prepare("INSERT INTO doc_post (post_id, doc, about) VALUES (:id, :image, :about)");
            $iquery->execute(array(":id" => $last_id, ":image" => $name, ":about" => $value));

            if ($when == "user") {
              $array = explode(",", $tags);
              foreach ($array as $value) {
                if ($value != "") {
                  $my = $universal->getIdFromGet($value);
                  $tquery = $this->db->prepare("INSERT INTO taggings (post_id, taggings, taggings_id, taggings_time) VALUES (:post_id, :tags, :taggs_id, now())");
                  $tquery->execute(array(":post_id" => $last_id, ":tags" => $value, ":taggs_id" => $my));
                  $noti->actionNotify($my, $last_id, "tag");
                }
              }
            }

          }
        }
      }
    }

    public function locPost($src, $value, $tags, $font, $loc, $when, $grp){
      $session = $_SESSION['id'];

      $universal = new universal;
      $noti = new notifications;
      $hashtag = new hashtag;
      $mention = new mention_class;

      if ($when == "user") {
        $query = $this->db->prepare("INSERT INTO post (user_id, type, time, font_size, address) VALUES (:id, :type, now(), :font, :loc)");
        $query->execute(array(":id" => $session, ":type" => "location", ":font" => $font, ":loc" => $loc));
      } else if ($when == "group") {
        $query = $this->db->prepare("INSERT INTO post (user_id, type, post_of, grp_id, time, font_size, address) VALUES (:id, :type, :of, :grp, now(), :font, :loc)");
        $query->execute(array(":id" => $session, ":type" => "location", ":of" => "group", ":grp" => $grp, ":font" => $font, ":loc" => $loc));
      }


      $last_id = $this->db->lastInsertId();

      $mention->getMentions($value, $last_id);
      $hashtag->getHashtags($value, $last_id);

      $tquery = $this->db->prepare("INSERT INTO loc_post (post_id, loc, about) VALUES (:post_id, :loc, :about)");
      $tquery->execute(array(":post_id" => $last_id, ":loc" => $src, ":about" => $value));

      if ($when == "user") {
        $array = explode(",", $tags);
        foreach ($array as $value) {
          if ($value != "") {
            $my = $universal->getIdFromGet($value);
            $fquery = $this->db->prepare("INSERT INTO taggings (post_id, taggings, taggings_id, taggings_time) VALUES (:post_id, :tags, :taggs_id, now())");
            $fquery->execute(array(":post_id" => $last_id, ":tags" => $value, ":taggs_id" => $my));
            $noti->actionNotify($my, $last_id, "tag");
          }
        }
      }

    }

    public function linkPost($value, $tags, $font, $loc, $url, $title, $src, $when, $grp){
      $universal = new universal;
      $noti = new notifications;
      $hashtag = new hashtag;
      $mention = new mention_class;

      $session = $_SESSION['id'];

      if ($when == "user") {
        $pquery = $this->db->prepare("INSERT INTO post (user_id, type, time, font_size, address) VALUES (:id, :type, now(), :size, :loc)");
        $pquery->execute(array(":id" => $session, ":type" => "link", ":size" => $font, ":loc" => $loc));
      } else if ($when == "group") {
        $pquery = $this->db->prepare("INSERT INTO post (user_id, type, post_of, grp_id, time, font_size, address) VALUES (:id, :type, :of, :grp, now(), :size, :loc)");
        $pquery->execute(array(":id" => $session, ":type" => "link", ":of" => "group", ":grp" => $grp, ":size" => $font, ":loc" => $loc));
      }

      $last_id = $this->db->lastInsertId();

      $mention->getMentions($value, $last_id);
      $hashtag->getHashtags($value, $last_id);

      $tquery = $this->db->prepare("INSERT INTO link_post (post_id, text, link_url, link_title, link_src) VALUES (:post_id, :text, :url, :title, :src)");
      $tquery->execute(array(":post_id" => $last_id, ":text" => $value, ":url" => $url, ":title" => $title, ":src" => $src));

      if ($when == "user") {
        $array = explode(",", $tags);
        foreach ($array as $value) {
          if ($value != "") {
            $my = $universal->getIdFromGet($value);
            $fquery = $this->db->prepare("INSERT INTO taggings (post_id, taggings, taggings_id, taggings_time) VALUES (:post_id, :tags, :taggs_id, now())");
            $fquery->execute(array(":post_id" => $last_id, ":tags" => $value, ":taggs_id" => $my));
            $noti->actionNotify($my, $last_id, "tag");
          }
        }
      }

    }

    public function isPostLengthy($text){
      if(strlen($text) > 1000){
        return true;
      } else {
        return false;
      }
    }

    public function addMoreClass($text){
      if(strlen($text) > 1000){
        return "isLengthy";
      }
    }

    public function addMoreLink($text, $class){
      if(strlen($text) > 1000){
        return "<a href='#' class='hashtag {$class}'>.. Load more</a>";
      }
    }

    public function getDifferentPost($type, $post_id, $size){
      $universal = new universal;
      $Time = new time;
      $hashtag = new hashtag;
      $mention = new mention_class;

      if ($universal->isLoggedIn()) { $session = $_SESSION['id']; }

      if ($type == "text") {
        $tquery = $this->db->prepare("SELECT text FROM text_post WHERE post_id = :post");
        $tquery->execute(array(":post" => $post_id));
        while ($trow = $tquery->fetch(PDO::FETCH_OBJ)) {
          $text = nl2br(trim($trow->text));
          $text = $hashtag->toHashtag($text, "post");
          // $text = $hashtag->lineBreakHashtag($text, "post");
          $text = $mention->mention($text);
          $text = $universal->toAbsURL($text);
          return
            "<div class='e ". self::addMoreClass($text) ."' spellcheck='false'>
            <span class='p_text hyphenate' style='font-size: {$size}px'>{$text}</span>
            </div>
            <div class='load_more_div'>". self::addMoreLink($text, "load_more_text") ."</div>";
        }
      } else if ($type == "image") {
        $tquery = $this->db->prepare("SELECT image, about, filter FROM image_post WHERE post_id = :post");
        $tquery->execute(array(":post" => $post_id));
        while ($trow = $tquery->fetch(PDO::FETCH_OBJ)) {
          $text = nl2br(trim($trow->about));
          $img = $trow->image;
          $filter = $trow->filter;
          $text = $hashtag->toHashtag($text, "post");
          $text = $mention->mention($text);
          $text = $universal->toAbsURL($text);
          $by = self::postDetails($post_id, "user_id");
          $time = self::postDetails($post_id, "time");
          if($by == $session){ $u = "You"; } else { $u = $universal->GETsDetails($by, "username"); }
          return
            "<div class='p_abt e ". self::addMoreClass($text) ."' style='font-size: {$size}px' spellcheck='false'><p>{$text}</p></div>
            <div class='load_more_div load_more_not_text_div'>". self::addMoreLink($text, "load_more_not_text") ."</div>
            <div class='post_marginer'></div>
            <img src='{$this->DIR}/media/Instagram_{$img}' alt='Instagram_{$img}' class='p_img {$filter}' data-postid='{$post_id}' data-imgby='{$u}' data-time='{$Time->timeAgo($time)}' data-filter='{$filter}'>";
        }
      } else if ($type == "video") {
        $tquery = $this->db->prepare("SELECT video, about FROM video_post WHERE post_id = :post");
        $tquery->execute(array(":post" => $post_id));
        while ($trow = $tquery->fetch(PDO::FETCH_OBJ)) {
          $text = nl2br(trim($trow->about));
          $vid = $trow->video;
          $text = $hashtag->toHashtag($text, "post");
          $text = $mention->mention($text);
          $text = $universal->toAbsURL($text);
           return
             "<div class='p_abt e ". self::addMoreClass($text) ."' style='font-size: {$size}px' spellcheck='false'><p>$text</p></div>
              <div class='load_more_div load_more_not_text_div'>". self::addMoreLink($text, "load_more_not_text") ."</div>
              <div class='post_marginer'></div>
              <div class='p_vid'>
              <video src='{$this->DIR}/media/Instagram_{$vid}' loop preload='auto'></video>
              <span class='p_vid_pp_large'><i class='material-icons'>play_arrow</i></span>
              <span class='p_vid_cur p_vid_time_teaser'>0:00</span>
              <span class='p_vid_time_bubble'>0:00</span><div class='p_vid_ctrls'><div class='p_vid_seek'>
              <input class='p_vid_seek_range' type='range' name='p_vid_range' value='0' min='0' max='100' step='1'>
              </div><span class='p_vid_pp'><i class='material-icons'>play_arrow</i></span>
              <div class='p_vid_duration'><span class='p_vid_cur'>0:00</span><span class='p_vid_dur_sep'>/</span>
              <span class='p_vid_dur'>0:00</span></div><div class='p_vid_vol_div'>
              <input type='range' name='p_vid_vol_slider' value='100' min='0' max='100' step='1'>
              </div><span class='p_vid_vup'><i class='material-icons'>volume_up</i></span>
              <div class='p_vid_pbr_div'><ul><li data-pbr='2'>2x</li><li data-pbr='1.75'>1.75x</li>
              <li data-pbr='1.5'>1.5x</li><li data-pbr='1.25'>1.25x</li><li data-pbr='1' class='pbr_class'>1x</li>
              <li data-pbr='0.75'>0.75x</li><li data-pbr='0.5'>0.5x</li></ul></div>
              <span class='p_vid_setting'>1x</span><div class='p_vid_shadow'></div></div></div>";
        }
      } else if ($type == "audio") {
        $tquery = $this->db->prepare("SELECT audio, about FROM audio_post WHERE post_id = :post");
        $tquery->execute(array(":post" => $post_id));
        while ($trow = $tquery->fetch(PDO::FETCH_OBJ)) {
          $text = nl2br(trim($trow->about));
          $audio = $trow->audio;
          $text = $hashtag->toHashtag($text, "post");
          $text = $mention->mention($text);
          $text = $universal->toAbsURL($text);
          return
            "<div class='p_abt e ". self::addMoreClass($text) ."' style='font-size: {$size}px' spellcheck='false'><p>$text</p></div>
            <div class='load_more_div load_more_not_text_div'>". self::addMoreLink($text, "load_more_not_text") ."</div>
            <div class='post_marginer'></div>
            <div class='p_aud' data-song='{$this->DIR}/media/{$audio}'>
            <span class='p_aud_time_bubble'>0:00</span><div class='p_aud_ctrls'><div class='p_aud_info'>
            <span class='p_aud_name'></span>
            </div><span class='p_aud_pp'><i class='material-icons'>play_arrow</i></span>
            <div class='p_aud_seek'><input class='p_aud_seek_range' type='range' name='p_aud_seek_range' value='0' min='0' max='100' step='1'>
            </div><div class='p_aud_duration'><span class='p_aud_cur'>0:00</span><span class='p_aud_dur_sep'>/</span>
            <span class='p_aud_dur'>0:00</span></div><div class='p_aud_vol_div'>
            <input type='range' name='p_aud_vol_slider' value='100' min='0' max='100' step='1'>
            </div><span class='p_aud_vup'><i class='material-icons'>volume_up</i></span></div></div>";
        }
      } else if ($type == "document") {
        $tquery = $this->db->prepare("SELECT doc, about FROM doc_post WHERE post_id = :post");
        $tquery->execute(array(":post" => $post_id));
        while ($trow = $tquery->fetch(PDO::FETCH_OBJ)) {
          $text = nl2br(trim($trow->about));
          $doc = $trow->doc;
          $text = $hashtag->toHashtag($text, "post");
          $text = $mention->mention($text);
          $text = $universal->toAbsURL($text);
          return
            "<div class='p_abt e ". self::addMoreClass($text) ."' style='font-size: {$size}px' spellcheck='false'><p>{$text}</p></div>
            <div class='load_more_div load_more_not_text_div'>". self::addMoreLink($text, "load_more_not_text") ."</div>
            <div class='post_marginer'></div>
            <div class='p_doc'>
            <div class='p_doc_img'><img src='{$this->DIR}/images/Default_Doc_Cover/20151125_5655085dda190-210x210.png' alt='Document'>
            </div><div class='p_doc_info'>
            <a href='{$this->DIR}/media/{$doc}' class='p_doc_link' download='{$doc}'>". substr($doc, 0, 50) ."</a>
            </div></div>";
        }
      } else if ($type == "location") {
        $tquery = $this->db->prepare("SELECT loc, about FROM loc_post WHERE post_id = :post");
        $tquery->execute(array(":post" => $post_id));
        while ($trow = $tquery->fetch(PDO::FETCH_OBJ)) {
          $loc = $trow->loc;
          $text = nl2br(trim($trow->about));
          $text = $hashtag->toHashtag($text, "post");
          $text = $mention->mention($text);
          $text = $universal->toAbsURL($text);
          return
            "<div class='p_abt e ". self::addMoreClass($text) ."' style='font-size: {$size}px' spellcheck='false'><p>$text</p></div>
            <div class='load_more_div load_more_not_text_div'>". self::addMoreLink($text, "load_more_not_text") ."</div>
            <div class='post_marginer'></div>
            <div class='p_loc'>
            <img src='{$loc}' class='p_loc_img'></div>";
        }
      } else if ($type == "link") {
        $tquery = $this->db->prepare("SELECT text, link_url, link_title, link_src FROM link_post WHERE post_id = :post");
        $tquery->execute(array(":post" => $post_id));
        while ($trow = $tquery->fetch(PDO::FETCH_OBJ)) {
          $text = $trow->text;
          $url = $trow->link_url;
          $title = $trow->link_title;
          $src = $trow->link_src;
          $text = $hashtag->toHashtag($text, "post");
          $text = $mention->mention($text);
          $text = $universal->toAbsURL($text);
          return
            "<div class='p_abt e ". self::addMoreClass($text) ."' style='font-size: {$size}px' spellcheck='false'><p>$text</p></div>
            <div class='load_more_div load_more_not_text_div'>". self::addMoreLink($text, "load_more_not_text") ."</div>
            <div class='post_marginer'></div>
            <div class='p_link'><div class='p_link_img'>
            <img src='{$src}' alt=''></div><div class='p_link_info'>
            <div class='p_link_title'><a href='{$url}' target='_blank'>". substr($title, 0, 50) ."</a>
            </div><div class='p_link_url'>
            <a href='{$url}' target='_blank'>". substr($url, 0, 90) ."</a>
            </div></div></div>";
        }
      }
    }

    public function getHomePost($way, $l, $count){
      $avatar = new Avatar;
      $universal = new universal;
      $Time = new time;
      $post_like = new postLike;
      $bookmark = new bookmark;
      $taggings = new taggings;
      $share = new share;
      $comment = new postComment;
      $follow = new follow_system;
      $settings = new settings;

      $session = $_SESSION['id'];

      if ($l == "nolimit") {
        $query = $this->db->prepare("SELECT post.post_id, post.user_id, post.type, post.time, post.font_size, post.address FROM post, follow_system WHERE follow_system.follow_by = :by AND follow_system.follow_to = post.user_id AND post.post_of <> :grp ORDER BY post.time DESC LIMIT 10");
        $query->execute(array(":by" => $session, ":grp" => "group"));

      } else if ($l == "limit") {

        $start = intval($count);
        $end = $start+10;

        $query = $this->db->prepare("SELECT post.post_id, post.user_id, post.type, post.time, post.font_size, post.address FROM post, follow_system WHERE follow_system.follow_by = :by AND follow_system.follow_to = post.user_id AND post.post_of <> :grp AND post.post_id < :start ORDER BY post.time DESC LIMIT 5");
        $query->execute(array(":by" => $session, ":grp" => "group", ":start" => $start));
      }

      if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $post_id = $row->post_id;
          $user_id = $row->user_id;
          $type = $row->type;
          $time = $row->time;
          $size = $row->font_size;
          $address = $row->address;

          echo "<div class='posts home_posts inst' data-postid='{$post_id}' data-time='{$time}'><div class='p_i'><div class='p_i_img'>";
          if ($way == "get") {
            echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($user_id)}' alt='{$universal->GETsDetails($user_id, "username")}'>";
          } else if ($way == "direct") {
            echo "<img src='". DIR ."/{$avatar->GETsAvatar($user_id)}' alt='{$universal->GETsDetails($user_id, "username")}'s avatar'>";
          }
          echo "</div><div class='p_i_1'>";
          echo "<a href='". DIR ."/profile/{$universal->GETsDetails($user_id, "username")}' data-getid='{$user_id}' class='inst_username' title='{$universal->GETsDetails($user_id, "username")}'>{$universal->nameShortener($universal->GETsDetails($user_id, "username"), 25)}</a><span title='". self::addressTitle($address, $user_id) ."'>";
          echo self::addressN($address, $user_id);
          echo "</span>";
          echo "</div><div class='p_i_2'><div class='p_time'>";
          echo "<span class=''>". $Time->timeAgo($time) ."</span></div><div class='p_h_opt'>";
          echo "<span class='p_tags'>". $taggings->getTaggings($post_id) ."</span>";
          echo "<span class='p_comm'>". $share->getShares($post_id) ."</span>";
          echo "<span class='exp_p_menu'><i class='material-icons'>expand_more</i></span></div></div><div class='options p_options'><ul>";
          echo "<li><a href='{$this->DIR}/view_post/{$post_id}'>Open</a></li>";
          if ($follow->isFollowing($user_id)) {
            echo "<li><a href='#' class='simple_unfollow'>Unfollow</a></li>";
          }
          if ($settings->isBlocked($user_id) == false) {
            echo "<li><a href='#' data-getid='{$user_id}' data-username='{$universal->nameShortener($universal->GETsDetails($user_id, "username"), 20)}' class='block'>Block {$universal->nameShortener($universal->GETsDetails($user_id, "username"), 12)}</a></li>";
          }
          if ($taggings->AmITagged($post_id)) {
            echo "<li><a href='#' class='untag'>Untag</a></li>";
          }
          if ($share->AmIsharedTo($post_id)) {
            echo "<li><a href='#' class='unshare'>Remove share</a></li>";
          }
          if ($share->AmIsharedBy($post_id)) {
            echo "<li><a href='#' class='un__share'>Unshare</a></li>";
          }
          echo "<li><a href='#' data-link='{$universal->urlChecker($this->DIR)}/view_post/{$post_id}' class='p_copy_link'>Copy link</a></li>";
          echo "</ul></div></div><div class='p_o'><div class='p_actual'>";
          echo self::getDifferentPost($type, $post_id, $size);
          echo "</div></div><hr><div class='p_a'><div class='p_do'>";
          echo "<div class='p_Like_wra'>";
          if($post_like->likedOrNot($post_id)){
            echo "<span class='p_unlike' data-description='Unlike'><i class='material-icons'>favorite</i></span>";
          } else if ($post_like->likedOrNot($post_id) == false) {
            echo "<span class='p_like' data-description='Like'><i class='material-icons'>favorite_border</i></span>";
          }
          echo "</div>";
          echo "<div class='p_bmrk_wra'>";
          if ($bookmark->bookmarkedOrNot($post_id)) {
            echo "<span class='p_unbookmark' data-description='Unbookmark'><i class='material-icons'>bookmark</i></span>";
          } else if ($bookmark->bookmarkedOrNot($post_id) == false) {
            echo "<span class='p_bookmark' data-description='Bookmark'><i class='material-icons'>bookmark_border</i></span>";
          }
          echo "</div>";
          echo "<div class='p_send_wra'><span class='p_send' data-description='Share'><i class='material-icons'>send</i></span></div>";
          echo "</div><div class='p_did'><span class='p_likes likes'>". $post_like->getPostLikes($post_id) ."</span>
          </div></div><hr>";
          echo "<div class='p_comments'>". $comment->getComments($post_id) ."</div>";
          // echo "<div class='p_cit'><div class='p_cit_img'>";
          // if ($way == "get") {
          //   echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($session)}' alt='{$universal->GETsDetails($session, "username")}'>";
          // } else if ($way == "direct") {
          //   echo "<img src='". DIR ."/{$avatar->GETsAvatar($session)}' alt='{$universal->GETsDetails($session, "username")}'s avatar'>";
          // }
          // echo "</div><div class='p_cit_area'>";
          // echo "<textarea class='textarea_toggle comment_teaser' name='post_comment' spellcheck='false' placeholder='Wanna comment?'></textarea>";
          // // echo "<span data-description='Add emojis'><i class='material-icons'>sentiment_very_satisfied</i></span>";
          // echo "</div><div class='p_cit_tool' data-postid='{$post_id}'>
          // <span class='c_sticker c_sticker_trailer' data-description='Add sticker'><i class='material-icons'>face</i></span>
          // <form class='p_comment_form' enctype='multipart/form-data' action=''>
          // <input type='file' class='p_comm_file_teaser {$post_id}' name='p_comm_file' id='p_comm_file' data-postid='{$post_id}'>
          // <label for='p_comm_file' class='p_cit_more' data-description='Attach a file'><i class='material-icons'>attach_file</i></label>
          // </form>
          // </div></div>";
          echo "</div>";

        }
        // echo "<div class='feed_inserted'></div>";
        echo "<div class='post_end feed_inserted'>Looks like you've reached the end</div>";
      } else if ($query->rowCount() == 0) {
        if ($way == "direct") {
          echo "<div class='home_last_mssg'>
            <img src='{$this->DIR}/images/needs/large.jpg'>
            <span>Looks like you're new, Follow some to fill up your feed or post from above options</span>
          </div>";
        }
      }

    }

    public function getUserPost($id, $way, $count){
      $avatar = new Avatar;
      $universal = new universal;
      $Time = new time;
      $post_like = new postLike;
      $bookmark = new bookmark;
      $taggings = new taggings;
      $share = new share;
      $comment = new postComment;
      $follow = new follow_system;
      $settings = new settings;

      if ($universal->isLoggedIn()) {
        $session = $_SESSION['id'];
      }

      if ($way == "direct") {
        $get_id = $universal->getIdFromGet($_GET['u']);
      } else if ($way == "ajax") {
        $get_id = $id;
      }

      if ($way == "direct") {
        $pquery = $this->db->prepare("SELECT * FROM post WHERE user_id = :id AND post_of <> :grp ORDER BY time DESC LIMIT 5");
        $pquery->execute(array(":id" => $id, ":grp" => "group"));

      } else if ($way == "ajax") {
        $start = intval($count);
        $end = $start+10;

        $pquery = $this->db->prepare("SELECT * FROM post WHERE user_id = :id AND post_of <> :grp AND post_id < :start ORDER BY time DESC LIMIT 5");
        $pquery->execute(array(":id" => $id, ":grp" => "group", ":start" => $start));

      }

      $count = $pquery->rowCount();
      if ($count == 0) {
        if ($way == "direct") {
          echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'><span>";
          if ($universal->MeOrNot($id)) {
            echo "You have no post";
          } else {
            echo $universal->GETsDetails($id, "username")." has no post";
          }
          echo "</span></div>";
        }
      } else if ($count != 0) {

        while ($prow = $pquery->fetch(PDO::FETCH_OBJ)) {
          $post_id = $prow->post_id;
          $user_id = $prow->user_id;
          $type = $prow->type;
          $time = $prow->time;
          $size = $prow->font_size;
          $address = $prow->address;

          echo "<div class='posts user_posts inst'  data-postid='{$post_id}' data-time='{$time}' data-type='{$type}'><div class='p_i'><div class='p_i_img'>";
          echo "<img src='{$this->DIR}/". $avatar->DisplayAvatar($user_id) ."' alt='{$universal->GETsDetails($user_id, "username")}'s avatar'>";
          echo "</div><div class='p_i_1'>";
          echo "<a href='". DIR ."/profile/{$universal->GETsDetails($user_id, "username")}' title='{$universal->GETsDetails($user_id, "username")}'>{$universal->nameShortener($universal->GETsDetails($user_id, "username"), 25)}</a><span title='". self::addressTitle($address, $user_id) ."'>";
          echo self::addressN($address, $user_id);
          echo "</span>";
          echo "</div><div class='p_i_2'><div class='p_time'>";
          echo "<span class=''>". $Time->timeAgo($time) ."</span></div><div class='p_h_opt'>";
          echo "<span class='p_tags'>". $taggings->getTaggings($post_id) ."</span>";
          echo "<span class='p_comm'>". $share->getShares($post_id) ."</span>";
          echo "<span class='exp_p_menu'><i class='material-icons'>expand_more</i></span></div></div><div class='options p_options'><ul>";
          echo "<li><a href='{$this->DIR}/view_post/{$post_id}'>Open</a></li>";
          if ($universal->MeOrNot($user_id)) {
            echo "<li><a href='#' class='edit_post'>Edit post</a></li>";
            echo "<li><a href='#' class='delete_post'>Delete post</a></li>";
          } else if ($universal->MeOrNot($user_id) == false) {
            if ($follow->isFollowing($user_id)) {
              echo "<li><a href='#' class='simple_unfollow'>Unfollow</li>";
            }
            if ($settings->isBlocked($user_id) == false) {
              echo "<li><a href='#' data-getid='{$user_id}' data-username='{$universal->nameShortener($universal->GETsDetails($user_id, "username"), 20)}' class='block'>Block {$universal->nameShortener($universal->GETsDetails($user_id, "username"), 12)}</a></li>";
            }
          }
          if ($taggings->AmITagged($post_id)) {
            echo "<li><a href='#' class='untag'>Untag</a></li>";
          }
          if ($share->AmIsharedTo($post_id)) {
            echo "<li><a href='#' class='unshare'>Remove share</a></li>";
          }
          if ($share->AmIsharedBy($post_id)) {
            echo "<li><a href='#' class='un__share'>Unshare</a></li>";
          }
          echo "<li><a href='#' data-link='{$universal->urlChecker($this->DIR)}/view_post/{$post_id}' class='p_copy_link'>Copy link</a></li>";
          echo "</ul></div></div><div class='p_o'>";
          echo "<div class='p_edit_tools'>
          <span class='p_edit_tip'><i class='fa fa-info-circle' aria-hidden='true'></i>For hashtag, first remove all the text</span>
          <a href='#' class='p_edit_cancel sec_btn'>Cancel</a>
          <a href='#' class='p_edit_save pri_btn'>Save</a></div>";
          echo "<div class='p_actual' spellcheck='false'>";
          echo self::getDifferentPost($type, $post_id, $size);
          echo "</div></div><hr><div class='p_a'>";
          echo "<div class='p_do'>";
          echo "<div class='p_Like_wra'>";
          if($post_like->likedOrNot($post_id)){
            echo "<span class='p_unlike' data-description='Unlike'><i class='material-icons'>favorite</i></span>";
          } else if ($post_like->likedOrNot($post_id) == false) {
            echo "<span class='p_like' data-description='Like'><i class='material-icons'>favorite_border</i></span>";
          }
          echo "</div>";
          echo "<div class='p_bmrk_wra'>";
          if ($bookmark->bookmarkedOrNot($post_id)) {
            echo "<span class='p_unbookmark' data-description='Unbookmark'><i class='material-icons'>bookmark</i></span>";
          } else if ($bookmark->bookmarkedOrNot($post_id) == false) {
            echo "<span class='p_bookmark' data-description='Bookmark'><i class='material-icons'>bookmark_border</i></span>";
          }
          echo "</div>";
          echo "<div class='p_send_wra'><span class='p_send' data-description='Share'><i class='material-icons'>send</i></span></div>";
          echo "</div>";
          echo "<div class='p_did'><span class='p_likes likes'>" .$post_like->getPostLikes($post_id). "</span></div></div><hr>";
          echo "<div class='p_comments'>" .$comment->getComments($post_id). "</div>";
          // echo "<div class='p_cit'><div class='p_cit_img'>";
          // echo "<img src='{$this->DIR}". $avatar->DisplayAvatar($session) ."' alt='{$universal->GETsDetails($session, "username")}'>";
          // echo "</div><div class='p_cit_area'>";
          // echo "<textarea class='textarea_toggle comment_teaser' name='post_comment' spellcheck='false' placeholder='Wanna comment?'></textarea>";
          // // echo "<span data-description='Add emojis'><i class='material-icons'>sentiment_very_satisfied</i></span>";
          // echo "</div><div class='p_cit_tool'>
          // <span class='c_sticker c_sticker_trailer' data-description='Add sticker'><i class='material-icons'>face</i></span>
          // <form class='p_comment_form' enctype='multipart/form-data'>
          // <input type='file' class='p_comm_file_teaser' name='p_comm_file' id='p_comm_file'>
          // <label for='p_comm_file' class='p_cit_more' data-description='Attach a file'><i class='material-icons'>attach_file</i></label>
          // </form></div></div>";
          echo "</div>";
        }
        echo "<div class='post_end feed_inserted'>Looks like you've reached the end</div>";

      }

    }

    public function getTaggedPost($id, $way, $count){
      $avatar = new Avatar;
      $universal = new universal;
      $Time = new time;
      $post_like = new postLike;
      $bookmark = new bookmark;
      $taggings = new taggings;
      $share = new share;
      $comment = new postComment;
      $follow = new follow_system;
      $settings = new settings;

      $session = $_SESSION['id'];

      if ($way == "direct") {
        $get_id = $universal->getIdFromGet($_GET['u']);
      } else if ($way == "ajax") {
        $get_id = $id;
      }

      if ($way == "direct") {
        $query = $this->db->prepare("SELECT post.post_id, post.user_id, post.type, post.time, post.font_size, post.address, taggings.tagging_id FROM post, taggings WHERE post.post_id = taggings.post_id AND taggings.taggings_id = :by AND post.post_of <> :grp ORDER BY taggings.tagging_id DESC LIMIT 5");
        $query->execute(array(":by" => $id, ":grp" => "group"));

      } else if ($way == "ajax") {

        $start = intval($count);
        $end = $start+10;

        $query = $this->db->prepare("SELECT post.post_id, post.user_id, post.type, post.time, post.font_size, post.address, taggings.tagging_id FROM post, taggings WHERE post.post_id = taggings.post_id AND taggings.taggings_id = :by AND post.post_of <> :grp AND taggings.tagging_id < :start ORDER BY taggings.tagging_id DESC LIMIT 5");
        $query->execute(array(":by" => $id, ":grp" => "group", ":start" => $start));

      }

      if ($query->rowCount() == 0) {
        if ($way == "direct") {
          echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'><span>";
          if ($universal->MeOrNot($id)) {
            echo "You are not tagged in any of the post";
          } else {
            echo $universal->GETsDetails($id, "username")." is not tagged in any of the post";
          }
          echo "</span></div>";
        }

      } else if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $post_id = $row->post_id;
          $user_id = $row->user_id;
          $type = $row->type;
          $time = $row->time;
          $size = $row->font_size;
          $address = $row->address;
          $tag_id = $row->tagging_id;

          echo "<div class='posts tag_posts inst' data-postid='{$post_id}' data-type='{$type}' data-tagid='{$tag_id}'><div class='p_i'><div class='p_i_img'>";
          echo "<img src='{$this->DIR}/". $avatar->DisplayAvatar($user_id) ."' alt='{$universal->GETsDetails($user_id, "username")}'s avatar'>";
          echo "</div><div class='p_i_1'>";
          echo "<a href='". DIR ."/profile/{$universal->GETsDetails($user_id, "username")}' title='{$universal->GETsDetails($user_id, "username")}'>{$universal->nameShortener($universal->GETsDetails($user_id, "username"), 25)}</a><span title='". self::addressTitle($address, $user_id) ."'>";
          echo self::addressN($address, $user_id);
          echo "</span>";
          echo "</div><div class='p_i_2'><div class='p_time'>";
          echo "<span class=''>". $Time->timeAgo($time) ."</span></div><div class='p_h_opt'>";
          echo "<span class='p_tags'>". $taggings->getTaggings($post_id) ."</span>";
          echo "<span class='p_comm'>". $share->getShares($post_id) ."</span>";
          echo "<span class='exp_p_menu'><i class='material-icons'>expand_more</i></span></div></div><div class='options p_options'><ul>";
          echo "<li><a href='{$this->DIR}/view_post/{$post_id}'>Open</a></li>";
          if ($universal->MeOrNot($user_id) == false) {
            if ($follow->isFollowing($user_id)) {
              echo "<li><a href='#' class='simple_unfollow'>Unfollow</li>";
            }
            if ($settings->isBlocked($user_id) == false) {
              echo "<li><a href='#' data-getid='{$user_id}' data-username='{$universal->nameShortener($universal->GETsDetails($user_id, "username"), 20)}' class='block'>Block {$universal->nameShortener($universal->GETsDetails($user_id, "username"), 12)}</a></li>";
            }
          } else if ($universal->MeOrNot($user_id)) {
            echo "<li><a href='#' class='edit_post'>Edit post</a></li>";
            echo "<li><a href='#' class='delete_post'>Delete post</a></li>";
          }
          if ($taggings->AmITagged($post_id)) {
            echo "<li><a href='#' class='untag'>Untag</a></li>";
          }
          if ($share->AmIsharedTo($post_id)) {
            echo "<li><a href='#' class='unshare'>Remove share</a></li>";
          }
          if ($share->AmIsharedBy($post_id)) {
            echo "<li><a href='#' class='un__share'>Unshare</a></li>";
          }
          echo "<li><a href='#' data-link='{$universal->urlChecker($this->DIR)}/view_post/{$post_id}' class='p_copy_link'>Copy link</a></li>";
          echo "</ul></div></div><div class='p_o'>";
          echo "<div class='p_edit_tools'><span class='p_edit_tip'><i class='fa fa-info-circle' aria-hidden='true'></i>For hashtag, first remove all the text</span>
          <a href='#' class='p_edit_cancel sec_btn'>Cancel</a>
          <a href='#' class='p_edit_save pri_btn'>Save</a></div>";
          echo "<div class='p_actual'>";
          echo self::getDifferentPost($type, $post_id, $size);
          echo "</div></div><hr><div class='p_a'><div class='p_do'>";
          echo "<div class='p_Like_wra'>";
          if($post_like->likedOrNot($post_id)){
            echo "<span class='p_unlike' data-description='Unlike'><i class='material-icons'>favorite</i></span>";
          } else if ($post_like->likedOrNot($post_id) == false) {
            echo "<span class='p_like' data-description='Like'><i class='material-icons'>favorite_border</i></span>";
          }
          echo "</div>";
          echo "<div class='p_bmrk_wra'>";
          if ($bookmark->bookmarkedOrNot($post_id)) {
            echo "<span class='p_unbookmark' data-description='Unbookmark'><i class='material-icons'>bookmark</i></span>";
          } else if ($bookmark->bookmarkedOrNot($post_id) == false) {
            echo "<span class='p_bookmark' data-description='Bookmark'><i class='material-icons'>bookmark_border</i></span>";
          }
          echo "</div>";
          echo "<div class='p_send_wra'><span class='p_send' data-description='Share'><i class='material-icons'>send</i></span></div>";
          echo "</div><div class='p_did'><span class='p_likes likes'>". $post_like->getPostLikes($post_id) ."</span></div></div><hr>";
          echo "<div class='p_comments'>". $comment->getComments($post_id) ."</div>";
          echo "</div>";

        }
        echo "<div class='post_end feed_inserted'>Looks like you've reached the end</div>";
      }

    }

    public function getBookmarksPost($way, $count){
      $avatar = new Avatar;
      $universal = new universal;
      $Time = new time;
      $post_like = new postLike;
      $bookmark = new bookmark;
      $taggings = new taggings;
      $share = new share;
      $comment = new postComment;
      $follow = new follow_system;
      $groups = new group;
      $settings = new settings;

      $session = $_SESSION['id'];

      if ($way == "direct") {
        $query = $this->db->prepare("SELECT post.post_id, post.user_id, post.type, post.post_of, post.grp_id, post.time, post.font_size, post.address, bookmarks.bkmrk_id FROM post, bookmarks WHERE bookmarks.user_id = :by AND bookmarks.post_id = post.post_id ORDER BY bookmarks.bkmrk_id DESC LIMIT 5");
        $query->execute(array(":by" => $session));

      } else if ($way == "ajax") {

        $start = intval($count);
        $end = $start+10;

        $query = $this->db->prepare("SELECT post.post_id, post.user_id, post.type, post.post_of, post.grp_id, post.time, post.font_size, post.address, bookmarks.bkmrk_id FROM post, bookmarks WHERE bookmarks.user_id = :by AND bookmarks.post_id = post.post_id AND bookmarks.bkmrk_id < :start ORDER BY bookmarks.bkmrk_id DESC LIMIT 5");
        $query->execute(array(":by" => $session, ":start" => $start));

      }

      if ($query->rowCount() == 0) {
        if ($way == "direct") {
          echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'>
          <span>You have no bookmarked posts</span></div>";
        }
      } else if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $post_id = $row->post_id;
          $user_id = $row->user_id;
          $type = $row->type;
          $time = $row->time;
          $size = $row->font_size;
          $address = $row->address;
          $of = $row->post_of;
          $grp = $row->grp_id;
          $bk = $row->bkmrk_id;

          echo "<div class='posts bkmrk_posts inst' data-postid='{$post_id}' data-type='{$type}' data-bookmarkid='{$bk}'><div class='p_i'><div class='p_i_img'>";
          echo "<img src='{$this->DIR}/". $avatar->DisplayAvatar($user_id) ."' alt='{$universal->GETsDetails($user_id, "username")}'s avatar'>";
          echo "</div><div class='p_i_1 ";
          if($of == "group"){ echo "grp_p_i_1"; }
          echo "'>";
          echo "<a href='". DIR ."/profile/{$universal->GETsDetails($user_id, "username")}' title='{$universal->GETsDetails($user_id, "username")}'>{$universal->nameShortener($universal->GETsDetails($user_id, "username"), 25)}</a>";
          if ($of == "group") {
            echo "<span class='to_grp_arrow'><i class='material-icons'>arrow_drop_up</i></span><a href='{$this->DIR}/groups/{$grp}' class='to_grp_name' title='{$groups->GETgrp($grp, "grp_name")}'>{$universal->nameShortener($groups->GETgrp($grp, "grp_name"), 20)}</a>";
          }
          echo "<span title='". self::addressTitle($address, $user_id) ."'>";
          echo self::addressN($address, $user_id);
          echo "</span>";
          echo "</div><div class='p_i_2'><div class='p_time'>";
          echo "<span class=''>". $Time->timeAgo($time) ."</span></div><div class='p_h_opt'>";
          echo "<span class='p_tags'>". $taggings->getTaggings($post_id) ."</span>";
          echo "<span class='p_comm'>". $share->getShares($post_id) ."</span>";
          echo "<span class='exp_p_menu'><i class='material-icons'>expand_more</i></span></div></div><div class='options p_options'><ul>";
          echo "<li><a href='{$this->DIR}/view_post/{$post_id}'>Open</a></li>";
          if ($universal->MeOrNot($user_id)) {
            echo "<li><a href='#' class='edit_post'>Edit post</li>";
          }
          if ($universal->MeOrNot($user_id) == false) {
            if ($follow->isFollowing($user_id)) {
              echo "<li><a href='#' class='simple_unfollow'>Unfollow</li>";
            }
            if ($settings->isBlocked($user_id) == false) {
              echo "<li><a href='#' data-getid='{$user_id}' data-username='{$universal->nameShortener($universal->GETsDetails($user_id, "username"), 20)}' class='block'>Block {$universal->nameShortener($universal->GETsDetails($user_id, "username"), 12)}</a></li>";
            }
          }
          if ($universal->MeOrNot($user_id)) {
            echo "<li><a href='#' class='delete_post'>Delete post</a></li>";
          }
          if ($share->AmIsharedTo($post_id)) {
            echo "<li><a href='#' class='unshare'>Remove share</a></li>";
          }
          if ($share->AmIsharedBy($post_id)) {
            echo "<li><a href='#' class='un__share'>Unshare</a></li>";
          }
          echo "<li><a href='#' data-link='{$universal->urlChecker($this->DIR)}/view_post/{$post_id}' class='p_copy_link'>Copy link</a></li>";
          echo "</ul></div></div><div class='p_o'>";
          echo "<div class='p_edit_tools'><span class='p_edit_tip'><i class='fa fa-info-circle' aria-hidden='true'></i>For hashtag, first remove all the text</span>
          <a href='#' class='p_edit_cancel sec_btn'>Cancel</a>
          <a href='#' class='p_edit_save pri_btn'>Save</a></div>";
          echo "<div class='p_actual'>";
          echo self::getDifferentPost($type, $post_id, $size);
          echo "</div></div><hr><div class='p_a'><div class='p_do'>";
          echo "<div class='p_Like_wra'>";
          if($post_like->likedOrNot($post_id)){
            echo "<span class='p_unlike' data-description='Unlike'><i class='material-icons'>favorite</i></span>";
          } else if ($post_like->likedOrNot($post_id) == false) {
            echo "<span class='p_like' data-description='Like'><i class='material-icons'>favorite_border</i></span>";
          }
          echo "</div>";
          echo "<div class='p_bmrk_wra'>";
          if ($bookmark->bookmarkedOrNot($post_id)) {
            echo "<span class='p_unbookmark' data-description='Unbookmark'><i class='material-icons'>bookmark</i></span>";
          } else if ($bookmark->bookmarkedOrNot($post_id) == false) {
            echo "<span class='p_bookmark' data-description='Bookmark'><i class='material-icons'>bookmark_border</i></span>";
          }
          echo "</div>";
          echo "<div class='p_send_wra'><span class='p_send' data-description='Share'><i class='material-icons'>send</i></span></div>";
          echo "</div><div class='p_did'><span class='p_likes likes'>". $post_like->getPostLikes($post_id) ."</span></div></div><hr>";
          echo "<div class='p_comments'>". $comment->getComments($post_id) ."</div>";
          echo "</div>";

        }
        echo "<div class='post_end feed_inserted'>Looks like you've reached the end</div>";
      }

    }

    public function getSharedPost($id, $way, $count){
      $avatar = new Avatar;
      $universal = new universal;
      $Time = new time;
      $post_like = new postLike;
      $bookmark = new bookmark;
      $taggings = new taggings;
      $share = new share;
      $comment = new postComment;
      $follow = new follow_system;
      $settings = new settings;
      $groups = new group;

      $session = $_SESSION['id'];
      if ($way == "direct") {
        $get_id = $universal->getIdFromGet($_GET['u']);
      } else if ($way == "ajax") {
        $get_id = $id;
      }

      if ($way == "direct") {
        $query = $this->db->prepare("SELECT * FROM post, shares WHERE post.post_id = shares.post_id AND shares.share_to = :user ORDER BY shares.share_id DESC LIMIT 5");
        $query->execute(array(":user" => $id));

      } else if ($way == "ajax") {

        $start = intval($count);
        $end = $start+10;

        $query = $this->db->prepare("SELECT * FROM post, shares WHERE post.post_id = shares.post_id AND shares.share_to = :user AND shares.share_id < :start ORDER BY shares.share_id DESC LIMIT 5");
        $query->execute(array(":user" => $id, ":start" => $start));

      }

      if ($query->rowCount() == 0) {
        if ($way == "direct") {
          echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'><span>";
          if ($universal->MeOrNot($id)) {
            echo "No one shared posts with you";
          } else {
            echo "No one shared posts with ".$universal->GETsDetails($id, "username");
          }
          echo "</span></div>";
        }
      } else if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $post_id = $row->post_id;
          $share_id = $row->share_id;
          $shareby = $row->share_by;
          $sharetime = $row->share_time;
          $user_id = $row->user_id;
          $type = $row->type;
          $time = $row->time;
          $size = $row->font_size;
          $address = $row->address;
          $of = $row->post_of;
          $grp = $row->grp_id;

          echo "<div class='posts inst share_posts' data-postid='{$post_id}' data-type='{$type}' data-shareid='{$share_id}'>";
          echo "<div class='post_share_info'>by <a href='{$this->DIR}/profile/{$universal->GETsDetails($shareby, "username")}' title='{$universal->GETsDetails($shareby, "username")}'>";
          if ($shareby == $session) { echo "You"; } else { echo $universal->nameShortener($universal->GETsDetails($shareby, "username"), 20); }
          echo "</a> <span>{$Time->timeAgo($sharetime)}</span></div>";
          echo "<div class='p_i'><div class='p_i_img'>";
          echo "<img src='{$this->DIR}/". $avatar->DisplayAvatar($user_id) ."' alt='{$universal->GETsDetails($user_id, "username")}'s avatar'>";
          echo "</div><div class='p_i_1 ";
          if($of == "group"){ echo "grp_p_i_1"; }
          echo "'>";
          echo "<a href='". DIR ."/profile/{$universal->GETsDetails($user_id, "username")}' title='{$universal->GETsDetails($user_id, "username")}'>{$universal->nameShortener($universal->GETsDetails($user_id, "username"), 25)}</a>";
          if ($of == "group") {
            echo "<span class='to_grp_arrow'><i class='material-icons'>arrow_drop_up</i></span><a href='{$this->DIR}/groups/{$grp}' class='to_grp_name' title='{$groups->GETgrp($grp, "grp_name")}'>{$universal->nameShortener($groups->GETgrp($grp, "grp_name"), 20)}</a>";
          }
          echo "<span title='". self::addressTitle($address, $user_id) ."'>";
          echo self::addressN($address, $user_id);
          echo "</span>";
          echo "</div><div class='p_i_2'><div class='p_time'>";
          echo "<span class=''>". $Time->timeAgo($time) ."</span></div><div class='p_h_opt'>";
          echo "<span class='p_tags'>". $taggings->getTaggings($post_id) ."</span>";
          echo "<span class='p_comm'>". $share->getShares($post_id) ."</span>";
          echo "<span class='exp_p_menu'><i class='material-icons'>expand_more</i></span></div></div><div class='options p_options'><ul>";
          echo "<li><a href='{$this->DIR}/view_post/{$post_id}'>Open</a></li>";
          if ($universal->MeOrNot($shareby) == false) {
            if ($follow->isFollowing($user_id)) {
              echo "<li><a href='#' class='simple_unfollow'>Unfollow</li>";
            }
            if ($settings->isBlocked($shareby) == false) {
              echo "<li><a href='#' data-getid='{$shareby}' data-username='{$universal->nameShortener($universal->GETsDetails($shareby, "username"), 20)}' class='block'>Block {$universal->nameShortener($universal->GETsDetails($shareby, "username"), 12)}</a></li>";
            }
          } else if ($universal->MeOrNot($user_id)) {
            echo "<li><a href='#' class='edit_post'>Edit post</a></li>";
            echo "<li><a href='#' class='delete_post'>Delete post</a></li>";
          }
          if ($taggings->AmITagged($post_id)) {
            echo "<li><a href='#' class='untag'>Untag</a></li>";
          }
          if ($universal->MeOrNot($get_id)) {
            echo "<li><a href='#' class='unshare'>Remove share</a></li>";
          }
          // if ($share->AmIsharedTo($post_id)) {
          //   echo "<li><a href='#' class='unshare'>Remove share</a></li>";
          // }
          if ($share->AmIsharedBy($post_id)) {
            echo "<li><a href='#' class='un__share'>Unshare</a></li>";
          }
          echo "<li><a href='#' data-link='{$universal->urlChecker($this->DIR)}/view_post/{$post_id}' class='p_copy_link'>Copy link</a></li>";
          echo "</ul></div></div><div class='p_o'>";
          echo "<div class='p_edit_tools'><span class='p_edit_tip'><i class='fa fa-info-circle' aria-hidden='true'></i>For hashtag, first remove all the text</span>
          <a href='#' class='p_edit_cancel sec_btn'>Cancel</a>
          <a href='#' class='p_edit_save pri_btn'>Save</a></div>";
          echo "<div class='p_actual'>";
          echo self::getDifferentPost($type, $post_id, $size);
          echo "</div></div><hr><div class='p_a'><div class='p_do'>";
          echo "<div class='p_Like_wra'>";
          if($post_like->likedOrNot($post_id)){
            echo "<span class='p_unlike' data-description='Unlike'><i class='material-icons'>favorite</i></span>";
          } else if ($post_like->likedOrNot($post_id) == false) {
            echo "<span class='p_like' data-description='Like'><i class='material-icons'>favorite_border</i></span>";
          }
          echo "</div>";
          echo "<div class='p_bmrk_wra'>";
          if ($bookmark->bookmarkedOrNot($post_id)) {
            echo "<span class='p_unbookmark' data-description='Unbookmark'><i class='material-icons'>bookmark</i></span>";
          } else if ($bookmark->bookmarkedOrNot($post_id) == false) {
            echo "<span class='p_bookmark' data-description='Bookmark'><i class='material-icons'>bookmark_border</i></span>";
          }
          echo "</div>";
          echo "<div class='p_send_wra'><span class='p_send' data-description='Share'><i class='material-icons'>send</i></span></div>";
          echo "</div><div class='p_did'><span class='p_likes likes'>". $post_like->getPostLikes($post_id) ."</span></div></div><hr>";
          echo "<div class='p_comments'>". $comment->getComments($post_id) ."</div>";
          echo "</div>";

        }
        echo "<div class='post_end feed_inserted'>Looks like you've reached the end</div>";
      }

    }

    public function getPhotosPost($id){
      $universal = new universal;
      $like = new postLike;
      $comment = new postComment;
      $Time = new time;

      $session = $_SESSION['id'];

      $query = $this->db->prepare("SELECT post.post_id, post.user_id, image_post.image, image_post.filter, post.time FROM post, image_post WHERE post.user_id = :user AND post.type = :type AND post.post_id = image_post.post_id AND post.post_of <> :grp ORDER BY post.time DESC");
      $query->execute(array(":user" => $id, ":type" => "image", ":grp" => "group"));

      $count = $query->rowCount();
      if ($count == 0) {
        echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'><span>";
        if ($universal->MeOrNot($id)) { echo "You have no photos"; } else { echo $universal->GETsDetails($id, "username")." got no photos"; }
        echo "</span></div>";
      } else if ($count > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $post_id = $row->post_id;
          $user_id = $row->user_id;
          $image = $row->image;
          $time = $row->time;
          $filter = $row->filter;
          if ($user_id == $session) { $r = "You"; } else { $r = $universal->GETsDetails($user_id, "username"); }
          echo "<div class='post_photos'>
          <div class='post_p_info'>
          <span><i class='material-icons'>favorite</i> <span>{$like->simpleGetPostLikes($post_id)}</span></span>
          <span><i class='material-icons'>chat_bubble</i> <span>{$comment->simpleGetComments($post_id)}</span></span>
          </div>
          <img src='{$this->DIR}/media/Instagram_{$image}' alt='' data-postid='{$post_id}' data-imgby='{$r}' data-time='{$Time->timeAgo($time)}' data-filter='{$filter}' class='p_pho_img {$filter}'></div>";
        }
        // echo "<div class='post_end'>Looks like you've reached the end</div>";
      }

    }

    public function getVideosPost($id){
      $avatar = new Avatar;
      $universal = new universal;
      $Time = new time;
      $post_like = new postLike;
      $bookmark = new bookmark;
      $taggings = new taggings;
      $share = new share;
      $comment = new postComment;

      $get_id = $universal->getIdFromGet($_GET['u']);
      $session = $_SESSION['id'];

      $query = $this->db->prepare("SELECT post.post_id, post.user_id, video_post.video FROM post, video_post WHERE post.user_id = :user AND post.type = :type AND post.post_id = video_post.post_id AND post.post_of <> :grp ORDER BY post.time DESC");
      $query->execute(array(":user" => $id, ":type" => "video", ":grp" => "group"));
      $count = $query->rowCount();
      if ($count == 0) {
        echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'><span>";
        if ($universal->MeOrNot($id)) {
          echo "You have no videos";
        } else {
          echo $universal->GETsDetails($id, "username")." has no videos";
        }
      } else if ($query->rowCount() > 0) {

        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $post_id = $row->post_id;
          $user_id = $row->user_id;
          $video = $row->video;

          echo "<div class='p_vid video_vid user_post_vid'>
            <video src='{$this->DIR}/media/Instagram_{$video}' loop preload='auto'></video>
            <span class='p_vid_pp_large'><i class='material-icons'>play_arrow</i></span>
            <span class='p_vid_cur p_vid_time_teaser'>0:00</span>
            <span class='p_vid_time_bubble'>0:00</span><div class='p_vid_ctrls'><div class='p_vid_seek'>
            <input class='p_vid_seek_range' type='range' name='p_vid_range' value='0' min='0' max='100' step='1'>
            </div><span class='p_vid_pp'><i class='material-icons'>play_arrow</i></span>
            <div class='p_vid_duration'><span class='p_vid_cur'>0:00</span><span class='p_vid_dur_sep'>/</span>
            <span class='p_vid_dur'>0:00</span></div><div class='p_vid_vol_div'>
            <input type='range' name='p_vid_vol_slider' value='100' min='0' max='100' step='1'>
            </div><span class='p_vid_vup'><i class='material-icons'>volume_up</i></span>
            <div class='p_vid_pbr_div'><ul><li data-pbr='2'>2x</li><li data-pbr='1.75'>1.75x</li>
            <li data-pbr='1.5'>1.5x</li><li data-pbr='1.25'>1.25x</li><li data-pbr='1' class='pbr_class'>1x</li>
            <li data-pbr='0.75'>0.75x</li><li data-pbr='0.5'>0.5x</li></ul></div>
            <span class='p_vid_setting'>1x</span><div class='p_vid_shadow'></div></div></div>";
        }

        echo "<div class='post_end'>Looks like you've reached the end</div>";
      }

    }

    public function getAudiosPost($id){
      $avatar = new Avatar;
      $universal = new universal;
      $Time = new time;
      $post_like = new postLike;
      $bookmark = new bookmark;
      $taggings = new taggings;
      $share = new share;
      $comment = new postComment;

      $get_id = $universal->getIdFromGet($_GET['u']);
      $session = $_SESSION['id'];

      $query = $this->db->prepare("SELECT post.post_id, post.user_id, audio_post.audio FROM post, audio_post WHERE post.user_id = :user AND post.type = :type AND post.post_id = audio_post.post_id AND post.post_of <> :grp ORDER BY post.time DESC");
      $query->execute(array(":user" => $id, ":type" => "audio", ":grp" => "group"));
      $count = $query->rowCount();
      if ($count == 0) {
        echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'><span>";
        if ($universal->MeOrNot($id)) {
          echo "You have no audios";
        } else {
          echo $universal->GETsDetails($id, "username")." has no audios";
        }
      } else if ($query->rowCount() > 0) {

        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $post_id = $row->post_id;
          $user_id = $row->user_id;
          $audio = $row->audio;

          echo "<div class='p_aud user_aud' data-song='{$this->DIR}/media/{$audio}'>
            <span class='p_aud_time_bubble'>0:00</span><div class='p_aud_ctrls'><div class='p_aud_info'>
            <span class='p_aud_name'></span>
            </div><span class='p_aud_pp'><i class='material-icons'>play_arrow</i></span>
            <div class='p_aud_seek'><input class='p_aud_seek_range' type='range' name='p_aud_seek_range' value='0' min='0' max='100' step='1'>
            </div><div class='p_aud_duration'><span class='p_aud_cur'>0:00</span><span class='p_aud_dur_sep'>/</span><span class='p_aud_dur'>0:00</span></div><div class='p_aud_vol_div'>
            <input type='range' name='p_aud_vol_slider' value='100' min='0' max='100' step='1'>
            </div><span class='p_aud_vup'><i class='material-icons'>volume_up</i></span></div></div>";
        }

        echo "<div class='post_end'>Looks like you've reached the end</div>";
      }

    }

    public function deletePost($post){
      $id = $_SESSION['id'];
      $tagquery = $this->db->prepare("DELETE FROM taggings WHERE post_id = :post");
      $tagquery->execute(array(":post" => $post));

      $sharequery = $this->db->prepare("DELETE FROM shares WHERE post_id = :post");
      $sharequery->execute(array(":post" => $post));

      $bkmrkquery = $this->db->prepare("DELETE FROM bookmarks WHERE post_id = :post");
      $bkmrkquery->execute(array(":post" => $post));

      $likequery = $this->db->prepare("DELETE FROM post_likes WHERE post_id = :post");
      $likequery->execute(array(":post" => $post));

      $cc = $this->db->prepare("SELECT post_comments_id FROM post_comments WHERE post_id = :post");
      $cc->execute(array(":post" => $post));
      if ($cc->rowCount() > 0) {
        while ($row = $cc->fetch(PDO::FETCH_OBJ)) {
          $cid = $row->post_comments_id;
          $cl = $this->db->prepare("DELETE FROM comment_likes WHERE comment_id = :comment");
          $cl->execute(array(":comment" => $cid));
        }
      }

      $comquery = $this->db->prepare("DELETE FROM post_comments WHERE post_id = :post");
      $comquery->execute(array(":post" => $post));

      $notiquery = $this->db->prepare("DELETE FROM notifications WHERE post_id = :post");
      $notiquery->execute(array(":post" => $post));

      $hashquery = $this->db->prepare("DELETE FROM hashtag WHERE post_id = :post");
      $hashquery->execute(array(":post" => $post));

      $query = $this->db->prepare("SELECT type, post_id FROM post WHERE post_id = :post");
      $query->execute(array(":post" => $post));
      if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $type = $row->type;
          $p = $row->post_id;

          if ($type == "text") {
            $textquery = $this->db->prepare("DELETE FROM text_post WHERE post_id = :post");
            $textquery->execute(array(":post" => $p));

          } else if ($type == "image") {
            $imgquery1 = $this->db->prepare("SELECT image FROM image_post WHERE post_id = :post");
            $imgquery1->execute(array(":post" => $p));
            if ($imgquery1->rowCount() > 0) {
              while ($imgrow = $imgquery1->fetch(PDO::FETCH_OBJ)) {
                $image = $imgrow->image;

                if(file_exists("../../media/Instagram_{$image}")){
                  unlink("../../media/Instagram_$image");
                }
                $dltimage = $this->db->prepare('DELETE FROM image_post WHERE post_id = :post');
                $dltimage->execute(array(":post" => $p));
              }
            }

          } else if ($type == "video") {
            $vidquery1 = $this->db->prepare("SELECT video FROM video_post WHERE post_id = :post");
            $vidquery1->execute(array(":post" => $p));
            if ($vidquery1->rowCount() > 0) {
              while ($vidrow = $vidquery1->fetch(PDO::FETCH_OBJ)) {
                $video = $vidrow->video;
                if(file_exists("../../media/Instagram_{$video}")){
                  unlink("../../media/Instagram_$video");
                }
                $dltvideo = $this->db->prepare('DELETE FROM video_post WHERE post_id = :post');
                $dltvideo->execute(array(":post" => $p));
              }
            }

          } else if ($type == "audio") {
            $audquery1 = $this->db->prepare("SELECT audio FROM audio_post WHERE post_id = :post");
            $audquery1->execute(array(":post" => $p));
            if ($audquery1->rowCount() > 0) {
              while ($audrow = $audquery1->fetch(PDO::FETCH_OBJ)) {
                $audio = $audrow->audio;
                if (file_exists("../../media/$audio")) {
                  unlink("../../media/$audio");
                }
                $dltaudio = $this->db->prepare('DELETE FROM audio_post WHERE post_id = :post');
                $dltaudio->execute(array(":post" => $p));
              }
            }

          } else if ($type == "document") {
            $docquery1 = $this->db->prepare("SELECT doc FROM doc_post WHERE post_id = :post");
            $docquery1->execute(array(":post" => $p));
            if ($docquery1->rowCount() > 0) {
              while ($docrow = $docquery1->fetch(PDO::FETCH_OBJ)) {
                $doc = $docrow->doc;
                if(file_exists("../../doc/{$doc}")){
                  unlink("../../doc/$doc");
                }
                $dltdoc = $this->db->prepare('DELETE FROM doc_post WHERE post_id = :post');
                $dltdoc->execute(array(":post" => $p));
              }
            }

          } else if ($type == "link") {
            $linkquery = $this->db->prepare("DELETE FROM link_post WHERE post_id = :post");
            $linkquery->execute(array(":post" => $p));

          } else if ($type == "location") {
            $locquery = $this->db->prepare("DELETE FROM loc_post WHERE post_id = :post");
            $locquery->execute(array(":post" => $p));
          }

        }

        $squery = $this->db->prepare("DELETE FROM post WHERE post_id = :post");
        $squery->execute(array(":post" => $post));
      }

    }

    public function viewPost($post){
      $avatar = new Avatar;
      $universal = new universal;
      $Time = new time;
      $post_like = new postLike;
      $bookmark = new bookmark;
      $taggings = new taggings;
      $share = new share;
      $comment = new postComment;
      $follow = new follow_system;
      $comment = new postComment;
      $settings = new settings;
      $groups = new group;

      $session = $_SESSION['id'];

      $query = $this->db->prepare("SELECT * FROM post WHERE post_id = :post LIMIT 1");
      $query->execute(array(":post" => $post));
      $count = $query->rowCount();

      if ($count == 0) {
        echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'><span>No such post found</span></div>";
      } else if ($count == 1) {

        $row = $query->fetch(PDO::FETCH_OBJ);
        $post_id = $row->post_id;
        $user_id = $row->user_id;
        $type = $row->type;
        $time = $row->time;
        $size = $row->font_size;
        $address = $row->address;
        $of = $row->post_of;
        $grp = $row->grp_id;

        echo "<div class='posts inst view_posts' data-postid='{$post_id}' data-type='{$type}'><div class='p_i'><div class='p_i_img'>";
        echo "<img src='". DIR ."/{$avatar->GETsAvatar($user_id)}' alt='{$universal->GETsDetails($user_id, "username")}'s avatar'>";
        echo "</div><div class='p_i_1 ";
        if($of == "group"){ echo "grp_p_i_1"; }
        echo "'>";
        echo "<a href='". DIR ."/profile/{$universal->GETsDetails($user_id, "username")}'>{$universal->nameShortener($universal->GETsDetails($user_id, "username"), 25)}</a>";
        if ($of == "group") {
          echo "<span class='to_grp_arrow'><i class='material-icons'>arrow_drop_up</i></span><a href='{$this->DIR}/groups/{$grp}' class='to_grp_name'>{$universal->nameShortener($groups->GETgrp($grp, "grp_name"), 20)}</a>";
        }
        echo "<span title='". self::addressTitle($address, $user_id) ."'>";
        echo self::addressN($address, $user_id);
        echo "</span>";
        echo "</div><div class='p_i_2'><div class='p_time'>";
        echo "<span class=''>". $Time->timeAgo($time) ."</span></div><div class='p_h_opt'>";
        if($of == "user"){echo "<span class='p_tags'>". $taggings->getTaggings($post_id) ."</span>";}
        echo "<span class='p_comm'>". $share->getShares($post_id) ."</span>";
        echo "<span class='exp_p_menu'><i class='material-icons'>expand_more</i></span></div></div><div class='options p_options'><ul>";
        if ($universal->MeOrNot($user_id)) {
          echo "<li><a href='#' class='edit_post'>Edit post</a></li>";
          echo "<li><a href='#' class='delete_post'>Delete post</a></li>";
        } else if ($universal->MeOrNot($user_id) == false) {
          if ($follow->isFollowing($user_id)) {
            echo "<li><a href='#' class='simple_unfollow'>Unfollow</li>";
          }
          if ($settings->isBlocked($user_id) == false) {
            echo "<li><a href='#' data-getid='{$user_id}' data-username='{$universal->nameShortener($universal->GETsDetails($user_id, "username"), 20)}' class='block'>Block {$universal->nameShortener($universal->GETsDetails($user_id, "username"), 12)}</a></li>";
          }
        }
        if ($taggings->AmITagged($post_id) && $of == "user") {
          echo "<li><a href='#' class='untag'>Untag</a></li>";
        }
        if ($share->AmIsharedTo($post_id)) {
          echo "<li><a href='#' class='unshare'>Remove share</a></li>";
        }
        if ($share->AmIsharedBy($post_id)) {
          echo "<li><a href='#' class='un__share'>Unshare</a></li>";
        }
        echo "<li><a href='#' data-link='{$universal->urlChecker($this->DIR)}/view_post/{$post_id}' class='p_copy_link'>Copy link</a></li>";
        echo "</ul></div></div><div class='p_o'>";
        echo "<div class='p_edit_tools'><span class='p_edit_tip'><i class='fa fa-info-circle' aria-hidden='true'></i>For hashtag, first remove all the text</span>
        <a href='#' class='p_edit_cancel sec_btn'>Cancel</a>
        <a href='#' class='p_edit_save pri_btn'>Save</a></div>";
        echo "<div class='p_actual'>";
        echo self::getDifferentPost($type, $post_id, $size);
        echo "</div></div><hr><div class='p_a'><div class='p_do'>";
        echo "<div class='p_Like_wra'>";
        if($post_like->likedOrNot($post_id)){
          echo "<span class='p_unlike' data-description='Unlike'><i class='material-icons'>favorite</i></span>";
        } else if ($post_like->likedOrNot($post_id) == false) {
          echo "<span class='p_like' data-description='Like'><i class='material-icons'>favorite_border</i></span>";
        }
        echo "</div>";
        echo "<div class='p_bmrk_wra'>";
        if ($bookmark->bookmarkedOrNot($post_id)) {
          echo "<span class='p_unbookmark' data-description='Unbookmark'><i class='material-icons'>bookmark</i></span>";
        } else if ($bookmark->bookmarkedOrNot($post_id) == false) {
          echo "<span class='p_bookmark' data-description='Bookmark'><i class='material-icons'>bookmark_border</i></span>";
        }
        echo "</div>";
        echo "<div class='p_send_wra'><span class='p_send' data-description='Share'><i class='material-icons'>send</i></span></div>";
        echo "</div><div class='p_did'><span class='p_likes likes'>". $post_like->getPostLikes($post_id) ."</span>
        </div></div><hr>";
        echo "<div class='p_comments'>". $comment->getComments($post_id) ."</div>";

        echo "<div class='p_cit view_cit'><div class='p_cit_img'>";
        echo "<img src='". DIR ."/{$avatar->GETsAvatar($session)}' alt='{$universal->GETsDetails($session, "username")}'>";
        echo "</div><div class='p_cit_area'>";
        echo "<textarea class='textarea_toggle comment_og' name='post_comment' spellcheck='false' placeholder='Wanna comment?'></textarea>";
        // echo "<span data-description='Add emojis'><i class='material-icons'>sentiment_very_satisfied</i></span>";
        echo "</div><div class='p_cit_tool view_cit_tool'>
        <span class='c_sticker c_sticker_og' data-description='Add sticker'><i class='material-icons'>face</i></span>
        <form class='p_comment_form' enctype='multipart/form-data'>
        <input type='file' class='p_comm_file_og' name='p_comm_file' id='p_comm_file'>
        <label for='p_comm_file' class='p_cit_more' data-description='Attach a file'><i class='material-icons'>attach_file</i></label>
        </form></div></div>";

        echo "<div class='comments_div'>";

        $comment->comments($post_id);

        echo "</div>";

        echo "</div>";

        echo "<div class='post_end'>Looks like you've reached the end</div>";

      }

    }

    public function editPost($text, $post, $type){

      $hashtag = new hashtag;
      $mention = new mention_class;

      if ($type == "text") {
        $query = $this->db->prepare("UPDATE text_post SET text = :text WHERE post_id = :post");
        $query->execute(array(":text" => $text, ":post" => $post));
        if ($text == "") {
          self::deletePost($post);
        }

      } else if ($type == "image") {
        $query = $this->db->prepare("UPDATE image_post SET about = :text WHERE post_id = :post");
        $query->execute(array(":text" => $text, ":post" => $post));

      } else if ($type == "video") {
        $query = $this->db->prepare("UPDATE video_post SET about = :text WHERE post_id = :post");
        $query->execute(array(":text" => $text, ":post" => $post));

      } else if ($type == "audio") {
        $query = $this->db->prepare("UPDATE audio_post SET about = :text WHERE post_id = :post");
        $query->execute(array(":text" => $text, ":post" => $post));

      } else if ($type == "link") {
        $query = $this->db->prepare("UPDATE link_post SET text = :text WHERE post_id = :post");
        $query->execute(array(":text" => $text, ":post" => $post));

      } else if ($type == "document") {
        $query = $this->db->prepare("UPDATE doc_post SET about = :text WHERE post_id = :post");
        $query->execute(array(":text" => $text, ":post" => $post));

      } else if ($type == "location") {
        $query = $this->db->prepare("UPDATE loc_post SET about = :text WHERE post_id = :post");
        $query->execute(array(":text" => $text, ":post" => $post));
      }

      $a = $this->db->prepare("DELETE FROM hashtag WHERE post_id = :post");
      $a->execute(array(":post" => $post));

      $mention->getMentions($text, $post);
      $hashtag->getHashtags($text, $post);

    }

  }
?>
