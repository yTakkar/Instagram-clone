<?php
  class explore{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function explorePhotos(){
      $session = $_SESSION['id'];

      $avatar = new Avatar;
      $universal = new universal;
      $mutual = new mutual;
      $Time = new time;
      $follow = new follow_system;

      $query = $this->db->prepare("SELECT post.post_id, post.user_id, image_post.image, image_post.filter, post.time FROM post, image_post WHERE post.user_id <> :me AND post.type = :type  AND post.post_id = image_post.post_id ORDER BY RAND() LIMIT 15");
      $query->execute(array(":me" => $session, ":type" => "image"));
      if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $post = $row->post_id;
          $user = $row->user_id;
          $image = $row->image;
          $time = $row->time;
          $filter = $row->filter;

          echo "<div class='exp_finds_ph inst'>
            <div class='exp_f_ph_img'>
              <img src='{$this->DIR}/media/Instagram_{$image}' alt='' data-imgby='{$universal->GETsDetails($user, "username")}' data-postid='{$post}' data-time='{$Time->timeAgo($time)}' data-filter='{$filter}' class='{$filter}'>
            </div>
            <div class='exp_f_ph_bottom'>
              <img src='{$this->DIR}/{$avatar->DisplayAvatar($user)}' alt=''>
              <div class='exp_f_ph_b_right'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($user, "username")}'>{$universal->GETsDetails($user, "username")}</a>
                <span class='exp_f_ph_light'>{$Time->timeAgo($time)}</span>
              </div>
              <a class='exp_f_ph_open' href='{$this->DIR}/view_post/{$post}'><i class='material-icons'>open_in_new</i></a>
            </div>
          </div>";

        }
      } else if ($query->rowCount() == 0) {
        echo "<div class='home_last_mssg exp_p_last'>
          <img src='{$this->DIR}/images/needs/large.jpg'>
          <span>Sorry, no photos to explore</span>
        </div>";
      }

    }

    public function explorePeople(){
      $session = $_SESSION['id'];

      $avatar = new Avatar;
      $universal = new universal;
      $mutual = new mutual;
      $Time = new time;
      $follow = new follow_system;

      $query = $this->db->prepare("SELECT id FROM users WHERE id <> :me ORDER BY RAND() LIMIT 9");
      $query->execute(array(":me" => $session));
      if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $id = $row->id;

          if ($follow->isFollowing($id) == false) {
            echo "<div class='exp_f_ppl inst'>
                <img src='{$this->DIR}/{$avatar->DisplayAvatar($id)}' alt=''>
                <div class='exp_p_ppl_content'>
                  <a href='{$this->DIR}/profile/{$universal->GETsDetails($id, "username")}'>{$universal->GETsDetails($id, "username")}</a>
                  <span>{$mutual->eMutual($id)}</span>
                </div>
                <div class='exp_f_ppl_act' data-getid='{$id}'>";
                  if ($follow->isFollowing($id)) {
                    echo "<a href='#' class='unfollow pri_btn'>Unfollow</a>";
                  } else if ($follow->isFollowing($id) == false) {
                    echo "<a href='#' class='follow pri_btn'>Follow</a>";
                  }
                echo "</div>
              </div>";

          }

        }
      } else if ($query->rowCount() == 0) {
        echo "<div class='home_last_mssg exp_p_last'>
          <img src='{$this->DIR}/images/needs/large.jpg'>
          <span>Sorry, no one to explore</span>
        </div>";
      }

    }

    public function exploreGroups(){
      $session = $_SESSION['id'];

      $groups = new group;

      $query = $this->db->prepare("SELECT group_id, grp_name, grp_avatar FROM groups ORDER BY RAND()");
      $query->execute(array(":me" => $session));
      if($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $grp = $row->group_id;
          $name = $row->grp_name;
          $avatar = $row->grp_avatar;

          if ($groups->memberOrNot($grp, $session) == false) {
            echo "<div class='exp_f_ppl inst'>
                <img src='{$groups->grpAvatar($grp)}' alt=''>
                <div class='exp_p_ppl_content'>
                  <a href='{$this->DIR}/groups/{$grp}'>{$name}</a>
                  <span>";
                  if ($groups->mutualGrpMemCount($grp) == 0) {
                    echo $groups->noOfGrpMembers($grp)." members";
                  } else {
                    echo $groups->mutualGrpMemCount($grp)." mutual members";
                  }
                  echo "</span>
                </div>
                <div class='exp_f_ppl_act' data-grp='{$grp}'>";
                  if ( $groups->memberOrNot($grp, $session)) {
                    echo "<a href='#' class='pri_btn leave_grp'>Leave group</a>";
                  } else if ( $groups->memberOrNot($grp, $session) == false) {
                    echo "<a href='#' class='pri_btn join_grp'>Join group</a>";
                  }
                echo "</div>
              </div>";
          }

        }
      }

    }

    public function exploreAudios(){
      $session = $_SESSION['id'];

      $universal = new universal;
      $avatar = new Avatar;
      $Time = new time;
      $groups = new group;

      $query = $this->db->prepare("SELECT post.post_id, post.user_id, audio_post.audio, post.time, post.post_of, post.grp_id FROM post, audio_post WHERE post.user_id <> :me AND post.type = :type  AND post.post_id = audio_post.post_id ORDER BY RAND() LIMIT 4");
      $query->execute(array(":me" => $session, ":type" => "audio"));
      if ($query->rowCount() == 0) {
        echo "<div class='home_last_mssg'>
          <img src='{$this->DIR}/images/needs/large.jpg'>
          <span>Sorry, no audios to explore</span>
        </div>";
      }
      while ($row = $query->fetch(PDO::FETCH_OBJ)) {
        $audio = $row->audio;
        $post = $row->post_id;
        $user = $row->user_id;
        $time = $row->time;
        $of = $row->post_of;
        $grp = $row->grp_id;

        echo "<div class='exp_audio inst'>
          <div class='exp_aud_top'>
            <img src='{$this->DIR}/{$avatar->DisplayAvatar($user)}' alt=''>
            <div class='exp_aud_con'>
              <a href='{$this->DIR}/profile/{$universal->GETsDetails($user, "username")}'>{$universal->nameShortener($universal->GETsDetails($user, "username"), 30)}</a>";
              if ($of == "group") {
                echo "<span class='to_grp_arrow'><i class='material-icons'>arrow_drop_up</i></span><a href='{$this->DIR}/groups/{$grp}' class='to_grp_name exp_grp_name'>{$universal->nameShortener($groups->GETgrp($grp, "grp_name"), 20)}</a>";
              }
              echo "<span>{$Time->timeAgo($time)}</span>
            </div>
            <a href='{$this->DIR}/view_post/{$post}' class='sec_btn exp_aud_open'>Open post</a>
          </div>
          <hr>

          <div class='p_aud' data-song='{$this->DIR}/media/{$audio}'>
            <span class='p_aud_time_bubble'>0:00</span>
            <div class='p_aud_ctrls'>
              <div class='p_aud_info'>
                <span class='p_aud_name'>The Weeknd - Starboy (official) ft. Daft Punk</span>
              </div>
              <span class='p_aud_pp'><i class='material-icons'>play_arrow</i></span>
              <div class='p_aud_seek'>
                <input class='p_aud_seek_range' type='range' name='p_aud_seek_range' value='0' min='0' max='100' step='1'>
              </div>
              <div class='p_aud_duration'>
                <span class='p_aud_cur'>0:00</span>
                <span class='p_aud_dur_sep'>/</span>
                <span class='p_aud_dur'>0:00</span>
              </div>
              <div class='p_aud_vol_div'>
                <input type='range' name='p_aud_vol_slider' value='100' min='0' max='100' step='1'>
              </div>
              <span class='p_aud_vup'><i class='material-icons'>volume_up</i></span>
            </div>
          </div>
        </div>";

      }

    }

    public function exploreVideos(){
      $session = $_SESSION['id'];

      $universal = new universal;
      $avatar = new Avatar;
      $Time = new time;
      $groups = new group;

      $query = $this->db->prepare("SELECT post.post_id, post.user_id, video_post.video, post.time, post.post_of, post.grp_id FROM post, video_post WHERE post.user_id <> :me AND post.type = :type  AND post.post_id = video_post.post_id ORDER BY RAND() LIMIT 1");
      $query->execute(array(":me" => $session, ":type" => "video"));
      if ($query->rowCount() == 0) {
        echo "<div class='home_last_mssg'>
          <img src='{$this->DIR}/images/needs/large.jpg'>
          <span>Sorry, no videos to explore</span>
        </div>";
      }
      while ($row = $query->fetch(PDO::FETCH_OBJ)) {
        $video = $row->video;
        $post = $row->post_id;
        $user = $row->user_id;
        $time = $row->time;
        $of = $row->post_of;
        $grp = $row->grp_id;

        echo "<div class='exp_find_vid inst'>
          <div class='exp_aud_top'>
            <img src='{$this->DIR}/{$avatar->DisplayAvatar($user)}' alt=''>
            <div class='exp_aud_con'>
              <a href='{$this->DIR}/profile/{$universal->GETsDetails($user, 'username')}'>{$universal->nameShortener($universal->GETsDetails($user, 'username'), 30)}</a>";
              if ($of == "group") {
                echo "<span class='to_grp_arrow'><i class='material-icons'>arrow_drop_up</i></span><a href='{$this->DIR}/groups/{$grp}' class='to_grp_name exp_grp_name'>{$universal->nameShortener($groups->GETgrp($grp, "grp_name"), 20)}</a>";
              }
              echo "<span>{$Time->timeAgo($time)}</span>
            </div>
            <a href='{$this->DIR}/view_post/{$post}' class='sec_btn exp_aud_open'>Open post</a>
          </div>
          <hr>
          <div class='p_vid'>
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
           <span class='p_vid_setting'>1x</span><div class='p_vid_shadow'></div></div></div>
        </div>";

      }

    }

  }
?>
