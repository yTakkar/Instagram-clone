<?php
  class group{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function validGrp($grp){
      $query = $this->db->prepare('SELECT group_id FROM groups WHERE group_id = :grp LIMIT 1');
      $query->execute(array(":grp" => $grp));
      if ($query->rowCount() == 1) {
        return true;
      } else if ($query->rowCount() == 0) {
        return false;
      }
    }

    public function grpAvatar($grp){
      $avatar = self::GETgrp($grp, "grp_avatar");
      if ($avatar == "") {
        return "{$this->DIR}/images/Default_group_con/Epic-Circle-31m3ldalla6v0uqb8ne6mi.png";
      } else {
        return "{$this->DIR}/group/$grp/Instagram_$avatar";
      }
    }

    public function GETgrp($grp, $what){
      $query = $this->db->prepare("SELECT $what FROM groups WHERE group_id = :grp");
      $query->execute(array(":grp" => $grp));
      if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          return $row->$what;
        }
      }
    }

    public function create_group($ename, $ebio){
      $name = preg_replace("#[<>]#i", "", $ename);
      $bio = preg_replace("#[<>]#", "", $ebio);

      $session = $_SESSION['id'];

      $q = $this->db->prepare("SELECT group_id FROM groups WHERE grp_name = :name");
      $q->execute(array(":name" => $name));
      $query = $this->db->prepare("INSERT INTO groups(grp_name, grp_bio, grp_avatar, grp_admin, grp_time) VALUES (:name, :bio, :avatar, :admin, now())");
      $query->execute(array(':name' => $name, ":bio" => $bio, ":avatar" => "", ":admin" => $session));

      $id = $this->db->lastInsertId();

      $query = $this->db->prepare("INSERT INTO group_members(group_id, group_member, added_by, time) VALUES (:grp, :member, :by, now())");
      $query->execute(array(':grp' => $id, ":member" => $session, ":by" => $session));

      mkdir("../../group/$id");
      return $id;

    }

    public function memberOrNot($grp, $id){
      $query = $this->db->prepare("SELECT group_member FROM group_members WHERE group_id = :grp AND group_member = :mem");
      $query->execute(array(":grp" => $grp, ":mem" => $id));
      $count = $query->rowCount();
      if ($count == 1) {
        return true;
      } else if($count == 0){
        return false;
      }
    }

    public function joinGrp($grp){
      $session = $_SESSION['id'];
      $q = $this->db->prepare("SELECT group_id FROM group_members WHERE group_id = :grp AND group_member = :member AND added_by = :by");
      $q->execute(array(":grp" => $grp, ":member" => $session, ":by" => $session));
      if ($q->rowCount() == 0) {
        $query = $this->db->prepare("INSERT INTO group_members(group_id, group_member, added_by, time) VALUES (:grp, :member, :by, now())");
        $query->execute(array(':grp' => $grp, ":member" => $session, ":by" => $session));
      }
    }

    public function leaveGrp($grp){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("DELETE FROM group_members WHERE group_id = :grp AND group_member = :mem");
      $query->execute(array(':grp' => $grp, ":mem" => $session));
    }

    public function noOfGrpMembers($grp){
      $query = $this->db->prepare('SELECT group_member FROM group_members WHERE group_id = :grp');
      $query->execute(array(":grp" => $grp));
      return $query->rowCount();
    }

    public function noOfGrpPosts($grp){
      $query = $this->db->prepare("SELECT post_id FROM post WHERE grp_id = :grp");
      $query->execute(array(":grp" => $grp));
      return $query->rowCount();
    }

    public function getGrpMembers($value, $grp){
      $session = $_SESSION['id'];

      // include 'universal.class.php';
      // include 'avatar.class.php';
      // include 'settings.class.php';

      $universal = new universal;
      $avatar = new Avatar;
      $settings = new settings;

      $text = preg_replace("#[<>]#", "", $value);
      $query = $this->db->prepare("SELECT follow_to_u, follow_to FROM follow_system WHERE follow_by = :me AND follow_to_u LIKE :l");
      $query->execute(array(":me" => $session, ":l" => "%$text%"));
      if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $id = $row->follow_to;
          $username = $row->follow_to_u;
          if (self::memberOrNot($grp, $id) == false && $settings->AmIBlocked($id) == false) {
            echo "<li class='grp_to_select_u select_user_to_add' data-user='{$id}' data-name='{$username}'><img src='{$this->DIR}/{$avatar->DisplayAvatar($id)}' alt=''><span>{$universal->nameShortener($universal->GETsDetails($id, "username"), 25)}</span></li>";
          }
        }
      }
    }

    public function addGrpMembers($user, $grp){
      $session = $_SESSION['id'];

      // include 'notifications.class.php';
      $noti = new notifications;

      $query = $this->db->prepare("INSERT INTO group_members(group_id, group_member, added_by, time) VALUES (:grp, :member, :by, now())");
      $query->execute(array(':grp' => $grp, ":member" => $user, ":by" => $session));

      $name = self::GETgrp($grp, "grp_name");
      $noti->actionNotify($user, $grp, "grp_add");
    }

    public function isGrpAdmin($grp, $id){
      $query = $this->db->prepare("SELECT grp_admin FROM groups WHERE group_id = :grp AND grp_admin = :user LIMIT 1");
      $query->execute(array(":grp" => $grp, ":user" => $id));
      if ($query->rowCount() == 1) {
        return true;
      } else if ($query->rowCount() == 0) {
        return false;
      }
    }

    public function myGroups($id){
      $Time = new time;
      $universal = new universal;
      $session = $_SESSION['id'];

      $query = $this->db->prepare("SELECT groups.group_id, groups.grp_name, groups.grp_avatar, groups.grp_admin, group_members.time FROM groups, group_members WHERE group_members.group_member = :user AND group_members.group_id = groups.group_id ORDER BY group_members.time DESC");
      $query->execute(array(":user" => $id));
      if ($query->rowCount() == 0) {
        echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'><span>";
        if ($session == $id) {
          echo "You have no groups";
        } else if ($session != $id) {
          echo $universal->GETsDetails($id, "username")." have no groups";
        }
        echo "</span></div>";
      } else if ($query->rowCount() > 0) {

        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $grp = $row->group_id;
          $name = $row->grp_name;
          $av = $row->grp_avatar;
          $admin = $row->grp_admin;
          $time = $row->time;

          echo "<div class='y_g inst'><div class='y_g_left'>
              <img src='". self::grpAvatar($grp). "' alt=''>
              <div class='y_g_content'>
                <a href='{$this->DIR}/groups/{$grp}'>". $universal->nameShortener($name, 30) ."</a>";
                echo "<span class='y_g_light'>";
                if (self::mutualGrpMemCount($grp) == 0) {
                  echo self::noOfGrpMembers($grp)." members";
                } else {
                  echo self::mutualGrpMemCount($grp)." mutual members";
                }
                echo"</span>";
                if (self::isGrpAdmin($grp, $session)) {
                  echo "<span class='grp_admin'>You admin</span>";
                } else if (self::isGrpAdmin($grp, $id)) {
                  echo "<span class='grp_admin'>admin</span>";
                }
              echo "</div>
            </div>
            <div class='y_g_right' data-grp='{$grp}'>";
            if (self::isGrpAdmin($grp, $session) == false) {
              if (self::memberOrNot($grp, $session)) {
                echo "<a href='#' class='pri_btn leave_grp'>Leave group</a>";
              } else if (self::memberOrNot($grp, $session) == false) {
                echo "<a href='#' class='pri_btn join_grp'>Join group</a>";
              }
            }
            echo "</div></div>";

        }

      }
    }

    public function newestMembers($grp){
      $universal = new universal;
      $avatar = new Avatar;
      $session = $_SESSION['id'];

      $query = $this->db->prepare('SELECT group_member, time FROM group_members WHERE group_id = :grp ORDER BY time DESC LIMIT 10');
      $query->execute(array(":grp" => $grp));
      while ($row = $query->fetch(PDO::FETCH_OBJ)) {
        $member = $row->group_member;
        $time = $row->time;
        if($member == $session){ $x = "You"; } else { $x = $universal->nameShortener($universal->GETsDetails($member, "username"), 20); }
        echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($member)}' alt='' data-description='{$x}' data-user='{$universal->GETsDetails($member, "username")}'>";

      }
    }

    public function grpMembers($grp, $way, $limit){
      $avatar = new Avatar;
      $universal = new universal;
      $follow = new follow_system;
      $mutual = new mutual;
      $timing = new time;

      $session = $_SESSION['id'];

      if ($way == "direct") {
        $query = $this->db->prepare("SELECT group_mem_id, group_member, added_by, time FROM group_members WHERE group_id = :grp ORDER BY time DESC LIMIT 18");
        $query->execute(array(":grp" => $grp));

      } else if ($way == "ajax") {
        $start = intval($limit);
        $query = $this->db->prepare("SELECT group_mem_id, group_member, added_by, time FROM group_members WHERE group_id = :grp AND group_mem_id < :start ORDER BY time DESC LIMIT 18");
        $query->execute(array(":grp" => $grp, ":start" => $start));
      }

      $count = $query->rowCount();

      if ($count == 0) {
        if ($way == "direct") {
          echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'><span>No members</span></div>";
        }
      } else if ($count > 0) {

        echo "<div class='m_wrapper'>";

        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $memid = $row->group_mem_id;
          $member = $row->group_member;
          $by = $row->added_by;
          $time = $row->time;

          echo "<div class='m_on inst grp_m_on' data-memid='{$memid}'><div class='m_top'>
              <img src='{$this->DIR}/{$avatar->DisplayAvatar($member)}' alt=''>
              <div class='m_top_right'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($member, "username")}' title='{$universal->GETsDetails($member, "username")}'>". $universal->nameShortener($universal->GETsDetails($member, "username"), 16) ."</a>";
                if (self::isGrpAdmin($grp, $member)) {
                  echo "<span class='grp_admin'>admin</span>";
                }
                echo "<span class='dk'>";
                echo $mutual->eMutual($member);
              echo "</span>
              </div></div>
              <span class='recommend_time'>{$timing->timeAgo($time)}</span>
              <div class='m_bottom'>
              <span class='recommend_by'>";
              if ($by != $member) {
                if ($by == $session) {
                  echo "by <a href='{$this->DIR}/profile/{$universal->GETsDetails($session, "username")}' title='You'>You</a>";
                } else {
                  echo "by <a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' title='{$universal->GETsDetails($by, "username")}'>{$universal->nameShortener($universal->GETsDetails($by, "username"), 20)}</a>";
                }
              }
              echo "</span>";
              if (self::isGrpAdmin($grp, $session) && $member != $session) {
                echo "<span class='rem_mem' data-description='Remove' data-member='{$member}'><i class='material-icons'>close</i></span>";
              }
              echo "<div data-getid='$member'>";
              if ($session == $member) {
                echo "<a href='{$this->DIR}/profile/$session". $universal->GETsDetails($session, "username") ."' class='sec_btn '>Profile</a>";
              } else {
                if ($follow->isFollowing($member)) {
                  echo "<a href='#' class='pri_btn display_unfollow unfollow'>Unfollow</a>";
                } else if ($follow->isFollowing($member) == false) {
                  echo "<a href='#' class='pri_btn display_follow follow'>Follow</a>";
                }
              }
            echo "</div></div></div>";

        }

        echo "</div>";
        echo "<div class='feed_inserted_members'></div>";
        // echo "<div class='load_more_'><a href='#' class='pri_btn load_more_btn'>Load more</a></div>";

      }

    }

    public function getGrpPost($grp, $way, $count){
      $avatar = new Avatar;
      $universal = new universal;
      $Time = new time;
      $post_like = new postLike;
      $bookmark = new bookmark;
      $share = new share;
      $comment = new postComment;
      $follow = new follow_system;
      $settings = new settings;
      $Post = new post;

      $session = $_SESSION['id'];

      if ($way == "direct") {
        $query = $this->db->prepare("SELECT * FROM post WHERE post_of = :grp AND grp_id = :id ORDER BY time DESC LIMIT 5");
        $query->execute(array(":grp" => "group", ":id" => $grp));

      } else if ($way == "ajax") {
        $start = intval($count);
        $end = $start+10;

        $query = $this->db->prepare("SELECT * FROM post WHERE post_of = :grp AND grp_id = :id AND post_id < :start ORDER BY time DESC LIMIT 5");
        $query->execute(array(":grp" => "group", ":id" => $grp, ":start" => $start));

      }


      $count = $query->rowCount();
      if ($count == 0) {
        if ($way == "direct") {
          echo "<div class='home_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'><span>";
          echo self::GETgrp($grp, "grp_name")." got no posts";
          echo "</span></div>";
        }
      } else if ($count > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $post_id = $row->post_id;
          $user_id = $row->user_id;
          $type = $row->type;
          $time = $row->time;
          $size = $row->font_size;
          $address = $row->address;

          echo "<div class='posts inst' data-postid='{$post_id}' data-time='{$time}'><div class='p_i'><div class='p_i_img'>";
          echo "<img src='". DIR ."/{$avatar->DisplayAvatar($user_id)}' alt='{$universal->GETsDetails($user_id, "username")}'s avatar'>";
          echo "</div><div class='p_i_1'>";
          echo "<a href='". DIR ."/profile/{$universal->GETsDetails($user_id, "username")}' title='{$universal->GETsDetails($user_id, "username")}'>{$universal->nameShortener($universal->GETsDetails($user_id, "username"), 25)}</a><span title='". $Post->addressTitle($address, $user_id) ."'>";
          echo $Post->addressN($address, $user_id);
          echo "</span>";
          echo "</div><div class='p_i_2'><div class='p_time'>";
          echo "<span class=''>". $Time->timeAgo($time) ."</span></div><div class='p_h_opt'>";

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
              echo "<li><a href='#' data-getid='{$user_id}' class='block'>Block {$universal->GETsDetails($user_id, "username")}</a></li>";
            }
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
          echo "<div class='p_actual' spellcheck='false'>";
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
          echo "</div><div class='p_did'><span class='p_likes likes' data-description='{$post_like->simpleGetPostLikes($post_id)} likes'>". $post_like->getPostLikes($post_id) ."</span></div></div><hr>";
          echo "<div class='p_comments'>". $comment->getComments($post_id) ."</div>";
          echo "</div>";

        }
        echo "<div class='feed_inserted post_end'>Looks like you've reached the end</div>";
        // echo "<div class='post_end'>Looks like you've reached the end</div>";
      }

    }

    public function getGrpPhotos($grp){
      $universal = new universal;
      $like = new postLike;
      $Time = new time;
      $comment = new postComment;

      $query = $this->db->prepare("SELECT post.post_id, image_post.image, image_post.filter, post.user_id, post.time FROM post, image_post WHERE post.grp_id = :user AND post.type = :type AND post.post_id = image_post.post_id AND post.post_of = :grp ORDER BY post.time DESC");
      $query->execute(array(":user" => $grp, ":type" => "image", ":grp" => "group"));

      $count = $query->rowCount();
      if ($count == 0) {
        echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'><span>Group has no photos</span></div>";
      } else if ($count > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $post_id = $row->post_id;
          $userid = $row->user_id;
          $image = $row->image;
          $time = $row->time;
          $filter = $row->filter;
          echo "<div class='post_photos'>
          <div class='post_p_info'>
          <span><i class='material-icons'>favorite</i> <span>{$like->simpleGetPostLikes($post_id)}</span></span>
          <span><i class='material-icons'>chat_bubble</i> <span>{$comment->simpleGetComments($post_id)}</span></span>
          </div>
          <img src='{$this->DIR}/media/Instagram_{$image}' alt='' data-imgby='{$universal->GETsDetails($userid, "username")}' data-postid='{$post_id}' data-time='{$Time->timeAgo($time)}' data-filter='{$filter}' class='{$filter}'></div>";
        }
      }
    }

    public function getGrpVideos($grp){
      $avatar = new Avatar;
      $universal = new universal;
      $Time = new time;
      $post_like = new postLike;
      $bookmark = new bookmark;
      $taggings = new taggings;
      $share = new share;
      $comment = new postComment;

      $session = $_SESSION['id'];

      $query = $this->db->prepare("SELECT post.post_id, post.user_id, video_post.video FROM post, video_post WHERE post.grp_id = :user AND post.type = :type AND post.post_id = video_post.post_id AND post.post_of = :grp ORDER BY post.time DESC");
      $query->execute(array(":user" => $grp, ":type" => "video", ":grp" => "group"));
      $count = $query->rowCount();
      if ($count == 0) {
        echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'><span>Group has no videos</span></div>";
      }

      while ($row = $query->fetch(PDO::FETCH_OBJ)) {
        $post_id = $row->post_id;
        $video = $row->video;

        echo "<div class='p_vid video_vid'>
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
    }

    public function editGrp($ename, $ebio, $pri, $grp){
      $name = trim(preg_replace("#[<>]#i", "", $ename));
      $bio = trim(preg_replace("#[<>]#i", "", $ebio));

      $query = $this->db->prepare("UPDATE groups SET grp_name = :name, grp_bio = :bio, grp_privacy = :pri WHERE group_id = :grp");
      $query->execute(array(":name" => $name, ":bio" => $bio, ":pri" => $pri, ":grp" => $grp));

    }

    public function dltGrp($grp){
      $session = $_SESSION['id'];

      // include 'post.class.php';
      $Post = new post;

      $query = $this->db->prepare("SELECT post_id FROM post WHERE grp_id = :grp");
      $query->execute(array(":grp" => $grp));
      if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $post = $row->post_id;
          $Post->deletePost($post);
        }
      }

      $aq = $this->db->prepare("DELETE FROM group_members WHERE group_id = :grp");
      $aq->execute(array(":grp" => $grp));


      $av = self::GETgrp($grp, "grp_avatar");
      if ($av != "") {
        unlink("../../group/{$grp}/Instagram_$av");
      }

      rmdir("../../group/{$grp}");

      $aq = $this->db->prepare("DELETE FROM groups WHERE group_id = :grp");
      $aq->execute(array(":grp" => $grp));

    }

    public function mutualGrpMemCount($grp){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT group_members.group_member FROM follow_system, group_members WHERE follow_system.follow_by = :me AND group_members.group_id = :grp AND follow_system.follow_to = group_members.group_member");
      $query->execute(array(":me" => $session, ":grp" => $grp));
      $count = $query->rowCount();
      return $count;
    }

    public function mutualGrpMembers($grp){
      $session = $_SESSION['id'];
      $universal = new universal;
      $avatar = new Avatar;
      $query = $this->db->prepare("SELECT group_members.group_member FROM follow_system, group_members WHERE follow_system.follow_by = :me AND group_members.group_id = :grp AND follow_system.follow_to = group_members.group_member ORDER BY group_mem_id DESC LIMIT 10");
      $query->execute(array(":me" => $session, ":grp" => $grp));
      $count = $query->rowCount();
      if ($count > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $member = $row->group_member;
          if($member == $session){ $x = "You"; } else { $x = $universal->nameShortener($universal->GETsDetails($member, "username"), 20); }
          echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($member)}' alt='' data-description='{$x}' data-user='{$universal->GETsDetails($member, "username")}'>";
        }
      }
    }

    public function removeMember($mem, $grp){
      $query = $this->db->prepare("DELETE FROM group_members WHERE group_id = :grp AND group_member = :mem");
      $query->execute(array(":grp" => $grp, ":mem" => $mem));
    }

    public function mutualGroups($id){
      $session = $_SESSION['id'];
      $universal = new universal;

      $array1 = array();
      $array2 = array();
      $array3 = array();

      $query = $this->db->prepare("SELECT group_id FROM group_members WHERE group_member = :mem");
      $query->execute(array(":mem" => $id));
      if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $array1[] = $row->group_id;
        }
      }

      $q = $this->db->prepare("SELECT group_id FROM group_members WHERE group_member = :mem");
      $q->execute(array(":mem" => $session));
      if ($q->rowCount() > 0) {
        while ($r = $q->fetch(PDO::FETCH_OBJ)) {
          $array2[] = $r->group_id;
        }
      }

      foreach ($array1 as $value) {
        if (in_array($value, $array2)) {
          $array3[] = $value;
        }
      }

      if (count($array3) > 0) {
        foreach ($array3 as $value) {
          $qq = $this->db->prepare("SELECT grp_name, grp_avatar FROM groups WHERE group_id = :grp");
          $qq->execute(array(":grp" => $value));
          while ($rr = $qq->fetch(PDO::FETCH_OBJ)) {
            $name = $rr->grp_name;
            $av = $rr->grp_avatar;
            echo "<img src='". self::grpAvatar($value) ."' alt='' data-description='{$universal->nameShortener($name, 20)}' data-grp='{$value}' class='m_grps'>";
          }
        }
      } else {
        echo "<div class='no_mutual_grp'><span>No mutual groups</span></div>";
      }

    }

    public function didIInviteHimTo($grp, $user){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT inviteGrpId FROM inviteGrp WHERE inviteGrp = :grp AND inviteGrpBy = :by AND inviteGrpTo = :to");
      $query->execute(array(":grp" => $grp, ":by" => $session, ":to" => $user));
      $count = $query->rowCount();
      if ($count == 0) {
        return false;
      } else if ($count > 0) {
        return true;
      }
    }

    public function selectToInvite($grp){
      $session = $_SESSION['id'];

      // include 'universal.class.php';
      // include 'avatar.class.php';

      $universal = new universal;
      $avatar = new Avatar;

      $query = $this->db->prepare("SELECT follow_to FROM follow_system WHERE follow_by = :by ORDER BY follow_id DESC");
      $query->execute(array(":by" => $session));
      $count = $query->rowCount();
      if ($count == 0) {
        echo "<div class='no_display'><img src='{$this->DIR}/images/needs/large.jpg'></div>";
      } else if ($count > 0) {
        echo "<input type='hidden' class='share_postid'>";
        echo "<input type='hidden' class='share_userid'>";
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $userid = $row->follow_to;
          if (self::memberOrNot($grp, $userid) == false) {
            echo "<div class='display_items select_receiver ";
            if (self::didIInviteHimTo($grp, $userid)) {
              echo "already_shared";
            }
            echo "' data-userid='{$userid}'><div class='d_i_img'>
            <img src='{$this->DIR}/". $avatar->DisplayAvatar($userid) ."' alt='profile'></div><div class='d_i_content'><div class='d_i_info'>
            <span class='d_i_username username'>". $universal->nameShortener($universal->GETsDetails($userid, "username"), 12). "</span>
            <span class='d_i_name'>". $universal->nameShortener($universal->GETsDetails($userid, "firstname")." ". $universal->GETsDetails($userid, "surname"), 17) ."</span></div></div></div>";
          }

        }
      }
    }

    public function inviteGrp($to, $grp){
      $by = $_SESSION['id'];

      // include 'notifications.class.php';
      // include 'universal.class.php';

      $noti = new notifications;
      $universal = new universal;

      if (self::didIInviteHimTo($grp, $to) == false) {
        $query = $this->db->prepare("INSERT INTO inviteGrp (inviteGrpBy, inviteGrpTo, inviteGrp, inviteGrpTime) VALUES (:by, :to, :grp, now())");
        $query->execute(array(":by" => $by, ":to" => $to, ":grp" => $grp));
        $noti->actionNotify($to, $grp, "inviteGrp");
        return "Invited ".$universal->GETsDetails($to, "username");
      } else {
        return "Already invited";
      }

    }

    public function selectForGrpAdmin($grp){
      $session = $_SESSION['id'];

      $universal = new universal;
      $avatar = new Avatar;

      $query = $this->db->prepare("SELECT group_member FROM group_members WHERE group_id = :grp AND group_member <> :me ORDER BY time DESC");
      $query->execute(array(":grp" => $grp, ":me" => $session));
      $count = $query->rowCount();
      if ($count == 0) {
        echo "<div class='no_display'><img src='{$this->DIR}/images/needs/large.jpg'></div>";
      } else if ($count > 0) {
        echo "<input type='hidden' class='share_postid'>";
        echo "<input type='hidden' class='share_userid'>";
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $userid = $row->group_member;
          if (self::isGrpAdmin($grp, $userid) == false) {
            echo "<div class='display_items select_receiver";
            echo "' data-userid='{$userid}'><div class='d_i_img'>
            <img src='{$this->DIR}/". $avatar->DisplayAvatar($userid) ."' alt='profile'></div><div class='d_i_content'><div class='d_i_info'>
            <span class='d_i_username username'>". $universal->nameShortener($universal->GETsDetails($userid, "username"), 12) ."</span>
            <span class='d_i_name'>". $universal->nameShortener($universal->GETsDetails($userid, "firstname")." ". $universal->GETsDetails($userid, "surname"), 15) ."</span></div></div></div>";
          }

        }
      }
    }

    public function changeGrpAdmin($user, $grp){
      $noti = new notifications;
      $session = $_SESSION['id'];
      if (self::isGrpAdmin($grp, $user)  == false) {
        $query = $this->db->prepare("UPDATE groups SET grp_admin = :user WHERE group_id = :grp AND grp_admin = :me");
        $query->execute(array(":user" => $user, ":grp" => $grp, ":me" => $session));
        $noti->actionNotify($user, $grp, "changeGrpAdmin");
        return "ok";
      }
    }

  }

?>
