<?php
  class hashtag{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function toHashtag($str, $of){
      $regex = "/#+([^ <>]+)/";
      $two = substr($str, 1);
      $t = preg_replace("#[\#]#", "", $two);

      if ($t != "") {
        if ($of == "post") {
          $str = preg_replace($regex, '<a class="hashtag" href="'. $this->DIR .'/hashtag?tag=$1">$0</a>', $str);
        } else if ($of == "comment") {
          $str = preg_replace($regex, "<span class='hashtag'>$0</span>", $str);
        }
      }

      return $str;
    }

    public function lineBreakHashtag($str, $of){
      $newString = '';
      $array = preg_split('/(\r\n|\r|\n)/', $str);

      foreach ($array as $line) {
        $word = explode(' ', $line);
        foreach ($word as $each) {
          $each = trim($each);
          if($each[0] == "#"){

            $two = substr($each, 1);
            $t = preg_replace("#[\#]#", "", $two);

            if($t != ""){
              if($of == "post"){
                $newString .= "<a class='hashtag' href='{$this->DIR}/hashtag?tag={$t}'>{$each}</a> ";
              } else if($of == "comment") {
                $newString .= "<span class='hashtag'>{$each}</span>";
              }
            }

          } else {
            $newString .= "$each ";
          }

        }
      }

      return $newString;

    }

    public function getHashtags($text, $post){
      $session = $_SESSION['id'];

      // $array = explode(" ", $text);
      $array = preg_split('/(\r\n|\r|\n| )/', $text);

      foreach ($array as $value) {
        if ($value[0] == "#") {
          $sec = substr($value, 1);
          $t = trim(preg_replace("#[\#]#", "", $sec));
          if ($t != "") {
            // $mquery = $this->db->prepare("SELECT hashtag_id FROM hashtag WHERE post_id = :post AND hashtag = :hashtag AND user_id = :user AND src = :src");
            // $mquery->execute(array(":post" => $post, ":hashtag" => $value, ":user" => $session, ":src" => "post"));
            // if ($mquery->rowCount() == 0) {
            $query = $this->db->prepare("INSERT INTO hashtag(hashtag, src, post_id, user_id, time) VALUES (:hashtag, :src, :post, :user, now())");
            $query->execute(array(":hashtag" => "#$t", ":src" => "post", ":post" => $post, ":user" => $session));
            // }
          }
        }
      }
    }

    public function noOfHashTags($hash){
      $query = $this->db->prepare("SELECT hashtag_id FROM hashtag WHERE hashtag LIKE :hash");
      $query->execute(array(":hash" => "%#$hash%"));
      $count = $query->rowCount();
      if ($count == 0) {
        return "No";
      } else {
        return $count;
      }
    }

    public function usersHashtags($user){
      $universal = new universal;

      if (isset($_SESSION['id'])) {
        $session = $_SESSION['id'];
      }

      $query = $this->db->prepare("SELECT DISTINCT hashtag FROM hashtag WHERE user_id = :user ORDER BY hashtag_id DESC LIMIT 20");
      $query->execute(array(":user" => $user));
      if ($query->rowCount() > 0) {
        echo "<div class='my_hashtags inst'><div class='header_of_divs'><span class='my_h_header'>";
        if($user == @$session){ echo "Your"; } else { echo $universal->GETsDetails($user, "username")."'s"; }
        echo " recent hashtags</span></div><div class='my_h_main'>";
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $hashtag = $row->hashtag;
          echo "<a href='{$this->DIR}/hashtag?tag=". substr($hashtag, 1) ."'>{$hashtag}</a>";
        }
        echo "</div></div>";
      }
    }

    public function popularHashtags(){
      $session = $_SESSION['id'];
      $universal = new universal;

      $query = $this->db->query("SELECT hashtag, COUNT(hashtag) as c FROM hashtag GROUP BY hashtag ORDER BY c DESC LIMIT 10");
      if ($query->rowCount() > 0) {
        echo "<div class='my_hashtags inst'><div class='header_of_divs'><span class='my_h_header'>Popular trends</span></div>
        <div class='my_h_main'>";
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $hashtag = $row->hashtag;
          echo "<a href='{$this->DIR}/hashtag?tag=". substr($hashtag, 1) ."'>{$hashtag}</a>";
        }
        echo "</div></div>";
      }
    }

    public function noOfHashTagPosts($tag){
      $query = $this->db->prepare("SELECT post.post_id, post.user_id, post.type, post.post_of, post.grp_id, post.time, post.font_size, post.address FROM post, hashtag WHERE hashtag.hashtag = :hashtag AND hashtag.post_id = post.post_id ORDER BY post.time DESC");
      $query->execute(array(":hashtag" => "#$tag"));
      $count = $query->rowCount();
      if ($count == 0) {
        return "No";
      } else {
        return $count;
      }
    }

    public function hashtaggedPost($tag, $way, $limit){
      $session = $_SESSION['id'];

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
      $Post = new post;

      if ($way == "direct") {
        $query = $this->db->prepare("SELECT post.post_id, post.user_id, post.type, post.post_of, post.grp_id, post.time, post.font_size, post.address, hashtag.hashtag_id FROM post, hashtag WHERE hashtag.hashtag = :hashtag AND hashtag.post_id = post.post_id ORDER BY hashtag.hashtag_id DESC LIMIT 10");
        $query->execute(array(":hashtag" => "#$tag"));

      } else if ($way == "ajax") {
        $start = intval($limit);
        $query = $this->db->prepare("SELECT post.post_id, post.user_id, post.type, post.post_of, post.grp_id, post.time, post.font_size, post.address, hashtag.hashtag_id FROM post, hashtag WHERE hashtag.hashtag = :hashtag AND hashtag.post_id = post.post_id AND hashtag.hashtag_id < :start ORDER BY hashtag.hashtag_id DESC LIMIT 5");
        $query->execute(array(":hashtag" => "#$tag", ":start" => $start));
      }

      $count = $query->rowCount();
      if ($count == 0) {
        if ($way == "direct") {
          echo "<div class='home_last_mssg hashtag_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'><span>No tagged posts found</span></div>";
        }
      } else if ($count > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $hashid = $row->hashtag_id;
          $post_id = $row->post_id;
          $user_id = $row->user_id;
          $type = $row->type;
          $time = $row->time;
          $size = $row->font_size;
          $address = $row->address;
          $of = $row->post_of;
          $grp = $row->grp_id;

          echo "<div class='posts inst' data-postid='{$post_id}' data-type='{$type}' data-hashid='{$hashid}'><div class='p_i'><div class='p_i_img'>";
          if ($way == "direct") {
            echo "<img src='{$this->DIR}/". $avatar->GETsAvatar($user_id) ."' alt=''>";
          } else if ($way == "ajax") {
            echo "<img src='{$this->DIR}/". $avatar->DisplayAvatar($user_id) ."' alt=''>";
          }

          echo "</div><div class='p_i_1 ";
          if($of == "group"){ echo "grp_p_i_1"; }
          echo "'>";
          echo "<a href='{$this->DIR}/profile/{$universal->GETsDetails($user_id, "username")}'>{$universal->GETsDetails($user_id, "username")}</a>";
          if ($of == "group") {
            echo "<span class='to_grp_arrow'><i class='material-icons'>arrow_drop_up</i></span><a href='{$this->DIR}/groups/{$grp}' class='to_grp_name'>{$groups->GETgrp($grp, "grp_name")}</a>";
          }
          echo "<span>";
          echo $Post->addressN($address, $user_id);
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
              echo "<li><a href='#' data-getid='{$user_id}' data-username='{$universal->nameShortener($universal->GETsDetails($user_id, "username"), 20)}' class='block'>Block {$universal->GETsDetails($user_id, "username")}</a></li>";
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
          echo "<div class='p_edit_tools'><span class='p_edit_tip'>*Click on tag btn to remove tags</span>
          <a href='#' class='p_edit_cancel sec_btn'>Cancel</a>
          <a href='#' class='p_edit_save pri_btn'>Save</a></div>";
          echo "<div class='p_actual'>";
          echo $Post->getDifferentPost($type, $post_id, $size);
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

  }
?>
