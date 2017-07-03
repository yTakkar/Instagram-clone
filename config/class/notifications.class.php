<?php
  class notifications{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function notiCount(){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT noti_id FROM notifications WHERE notify_to = :to");
      $query->execute(array(":to" => $session));
      $count = $query->rowCount();
      return $count;
    }

    public function unreadCount(){
      if (isset($_SESSION['id'])) {
        $session = $_SESSION['id'];
        $query = $this->db->prepare("SELECT noti_id FROM notifications WHERE notify_to = :to AND status = :status");
        $query->execute(array(":to" => $session, ":status" => "unread"));
        $count = $query->rowCount();
        if ($count != 0) {
          if ($count < 9) {
            return $count;
          } else if ($count >= 9) {
            return "+";
          }
        }
      }
    }

    public function titleNoti(){
      if(self::unreadCount() != 0){
        return "(".self::unreadCount().")";
      }
    }

    public function markRead(){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("UPDATE notifications SET status = :status WHERE notify_to = :to");
      $query->execute(array(":status" => "read", ":to" => $session));
    }

    public function followNotify($to, $type){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("INSERT INTO notifications (notify_by, notify_to, type, time) VALUES (:by, :to, :type, now())");
      $query->execute(array(":by" => $session, ":to" => $to, ":type" => $type));
    }

    public function recommendNotify($to, $of){
      $by = $_SESSION['id'];
      $query = $this->db->prepare("INSERT INTO notifications(notify_by, notify_to, notify_of, type, time) VALUES(:by, :to, :of, :type, now())");
      $query->execute(array(":by" => $by, ":to" => $to, ":of" => $of, ":type" => "recommend"));
    }

    public function actionNotify($to, $post, $type){
      $by = $_SESSION['id'];
      if ($by != $to) {
        $query = $this->db->prepare("INSERT INTO notifications (notify_by, notify_to, post_id, type, time) VALUES (:by, :to, :post, :type, now())");
        $query->execute(array(":by" => $by, ":to" => $to, ":post" => $post, ":type" => $type));
      }
    }

    public function cLikeNotify($by, $to, $post, $comment){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("INSERT INTO notifications (notify_by, notify_to, post_id, comment_id, type, time) VALUES(:by, :to, :post, :comment, 'commentLike', now())");
      $query->execute(array(":by" => $by, ":to" => $to, ":post" => $post, ":comment" => $comment,));
    }

    public function clearNotifications(){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("DELETE FROM notifications WHERE notify_to = :to");
      $query->execute(array(":to" => $session));
    }

    public function getNotifications($way, $limit){
      $universal = new universal;
      $avatar = new Avatar;
      $Time = new time;
      $follow = new follow_system;
      $group = new group;
      $message = new message;

      $session = $_SESSION['id'];

      if ($way == "direct") {
        $query = $this->db->prepare("SELECT * FROM notifications WHERE notify_to = :to ORDER BY noti_id DESC LIMIT 10");
        $query->execute(array(":to" => $session));

      } else if ($way == "ajax") {
        $start = intval($limit);
        $query = $this->db->prepare("SELECT * FROM notifications WHERE notify_to = :to AND noti_id < :start ORDER BY noti_id DESC LIMIT 10");
        $query->execute(array(":to" => $session, ":start" => $start));
      }

      $count = $query->rowCount();
      if ($count == 0) {
        if ($way == "direct") {
          echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'>
          <span>You got no notifications</span></div>";
        }
      } else if ($count > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $nid = $row->noti_id;
          $by = $row->notify_by;
          $type = $row->type;
          $to = $row->notify_to;
          $of = $row->notify_of;
          $postid = $row->post_id;
          $time = $row->time;

          if ($type == "follow") {
            echo "<div class='noti follow_noti' data-notiid='{$nid}'>";
            if ($way == "direct") {
              echo "<img src='{$this->DIR}/{$avatar->GETsAvatar($by)}' alt='' class='noti_avatar'>";
            } else if ($way == "ajax") {
              echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($by)}' alt='' class='noti_avatar'>";
            }
            echo "<div class='noti_left'><a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='noti_bold noti_username' title='{$universal->GETsDetails($by, "username")}'>". $universal->nameShortener($universal->GETsDetails($by, "username"), 20) ."</a>
              <span>started following you</span><span class='noti_time'>{$Time->timeAgo($time)}</span></div>
            <div class='noti_right follow_noti_right' data-getid='{$by}'>";
            if ($follow->isFollowing($by)) {
              echo "<a href='#' class='noti_ff pri_btn unfollow'>Unfollow</a>";
            } else if ($follow->isFollowing($by) == false) {
              echo "<a href='#' class='noti_ff pri_btn follow'>Follow</a>";
            }
            echo "</div></div>";

          } else if ($type == "recommend") {
            echo "<div class='noti follow_noti' data-notiid='{$nid}'>";
            if ($way == "direct") {
              echo "<img src='{$this->DIR}/{$avatar->GETsAvatar($by)}' alt='' class='noti_avatar'>";
            } else if ($way == "ajax") {
              echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($by)}' alt='' class='noti_avatar'>";
            }
            echo "<div class='noti_left'><a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='noti_bold noti_username' title='{$universal->GETsDetails($by, "username")}'>". $universal->nameShortener($universal->GETsDetails($by, "username"), 20) ."</a>
            <span>recommended <a href='{$this->DIR}/profile/{$universal->GETsDetails($of, "username")}' class='noti_bold' title='{$universal->GETsDetails($of, "username")}'>". $universal->nameShortener($universal->GETsDetails($of, "username"), 20) ."</a> to you</span>
            <span class='noti_time'>{$Time->timeAgo($time)}</span></div>
            <div class='noti_right follow_noti_right' data-getid='{$by}'>";
            if ($follow->isFollowing($of)) {
              echo "<a href='#' class='noti_ff pri_btn unfollow'>Unfollow {$universal->nameShortener($universal->GETsDetails($of, "username"), 10)}</a>";
            } else if ($follow->isFollowing($of) == false) {
              echo "<a href='#' class='noti_ff pri_btn follow'>Follow {$universal->nameShortener($universal->GETsDetails($of, "username"), 10)}</a>";
            }
            echo "</div></div>";

          } else if ($type == "like") {
              echo "<div class='noti follow_noti' data-notiid='{$nid}'>";
              if ($way == "direct") {
                echo "<img src='{$this->DIR}/{$avatar->GETsAvatar($by)}' alt='' class='noti_avatar'>";
              } else if ($way == "ajax") {
                echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($by)}' alt='' class='noti_avatar'>";
              }
                echo "<div class='noti_left'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='noti_bold noti_username' title='{$universal->GETsDetails($by, "username")}'>". $universal->nameShortener($universal->GETsDetails($by, "username"), 20) ."</a>
                  <span>liked your <a href='{$this->DIR}/view_post/{$postid}' class='noti_bold'>post</a></span>
                  <span class='noti_time'>{$Time->timeAgo($time)}</span>
                </div>
                <div class='noti_right follow_noti_right'>
                  <a href='{$this->DIR}/view_post/{$postid}' class='noti_ff sec_btn'>View post</a>
                </div></div>";

          } else if ($type == "comment") {
            echo "<div class='noti follow_noti' data-notiid='{$nid}'>";
                if ($way == "direct") {
                  echo "<img src='{$this->DIR}/{$avatar->GETsAvatar($by)}' alt='' class='noti_avatar'>";
                } else if ($way == "ajax") {
                  echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($by)}' alt='' class='noti_avatar'>";
                }
                echo "<div class='noti_left'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='noti_bold noti_username' title='{$universal->GETsDetails($by, "username")}'>". $universal->nameShortener($universal->GETsDetails($by, "username"), 20) ."</a>
                  <span>commented on your <a href='{$this->DIR}/view_post/{$postid}' class='noti_bold'>post</a></span>
                  <span class='noti_time'>{$Time->timeAgo($time)}</span>
                </div>
                <div class='noti_right follow_noti_right'>
                  <a href='{$this->DIR}/view_post/{$postid}' class='noti_ff sec_btn'>View post</a>
                </div></div>";

          } else if ($type == "shareto") {
            echo "<div class='noti follow_noti' data-notiid='{$nid}'>";
                if ($way == "direct") {
                  echo "<img src='{$this->DIR}/{$avatar->GETsAvatar($by)}' alt='' class='noti_avatar'>";
                } else if ($way == "ajax") {
                  echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($by)}' alt='' class='noti_avatar'>";
                }
                echo "<div class='noti_left'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='noti_bold noti_username' title='{$universal->GETsDetails($by, "username")}'>". $universal->nameShortener($universal->GETsDetails($by, "username"), 20) ."</a>
                  <span>shared you a <a href='{$this->DIR}/view_post/{$postid}' class='noti_bold'>post</a></span>
                  <span class='noti_time'>{$Time->timeAgo($time)}</span>
                </div>
                <div class='noti_right follow_noti_right'>
                  <a href='{$this->DIR}/view_post/{$postid}' class='noti_ff sec_btn'>View post</a>
                </div></div>";

          } else if($type == "shareyour"){
            echo "<div class='noti follow_noti' data-notiid='{$nid}'>";
                if ($way == "direct") {
                  echo "<img src='{$this->DIR}/{$avatar->GETsAvatar($by)}' alt='' class='noti_avatar'>";
                } else if ($way == "ajax") {
                  echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($by)}' alt='' class='noti_avatar'>";
                }
                echo "<div class='noti_left'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='noti_bold noti_username' title='{$universal->GETsDetails($by, "username")}'>". $universal->nameShortener($universal->GETsDetails($by, "username"), 20) ."</a>
                  <span>shared your <a href='{$this->DIR}/view_post/{$postid}' class='noti_bold'>post</a></span>
                  <span class='noti_time'>{$Time->timeAgo($time)}</span>
                </div>
                <div class='noti_right follow_noti_right'>
                  <a href='{$this->DIR}/view_post/{$postid}' class='noti_ff sec_btn'>View post</a>
                </div></div>";

          } else if ($type == "commentLike") {
            echo "<div class='noti follow_noti' data-notiid='{$nid}'>";
                if ($way == "direct") {
                  echo "<img src='{$this->DIR}/{$avatar->GETsAvatar($by)}' alt='' class='noti_avatar'>";
                } else if ($way == "ajax") {
                  echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($by)}' alt='' class='noti_avatar'>";
                }
                echo "<div class='noti_left'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='noti_bold noti_username' title='{$universal->GETsDetails($by, "username")}'>". $universal->nameShortener($universal->GETsDetails($by, "username"), 20) ."</a>
                  <span>liked your <span class='noti_bold'>comment</span></span>
                  <span class='noti_time'>{$Time->timeAgo($time)}</span>
                </div>
                <div class='noti_right follow_noti_right'>
                  <a href='{$this->DIR}/view_post/{$postid}' class='noti_ff sec_btn'>View post</a>
                </div></div>";

          } else if ($type == "tag") {
            echo "<div class='noti follow_noti' data-notiid='{$nid}'>";
                if ($way == "direct") {
                  echo "<img src='{$this->DIR}/{$avatar->GETsAvatar($by)}' alt='' class='noti_avatar'>";
                } else if ($way == "ajax") {
                  echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($by)}' alt='' class='noti_avatar'>";
                }
                echo "<div class='noti_left'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='noti_bold noti_username' title='{$universal->GETsDetails($by, "username")}'>". $universal->nameShortener($universal->GETsDetails($by, "username"), 20) ."</a>
                  <span>tagged you in a <a href='{$this->DIR}/view_post/{$postid}' class='noti_bold'>post</a></span>
                  <span class='noti_time'>{$Time->timeAgo($time)}</span>
                </div>
                <div class='noti_right follow_noti_right'>
                  <a href='{$this->DIR}/view_post/{$postid}' class='noti_ff sec_btn'>View post</a>
                </div></div>";

          } else if ($type == "post_mention") {
            echo "<div class='noti follow_noti' data-notiid='{$nid}'>";
                if ($way == "direct") {
                  echo "<img src='{$this->DIR}/{$avatar->GETsAvatar($by)}' alt='' class='noti_avatar'>";
                } else if ($way == "ajax") {
                  echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($by)}' alt='' class='noti_avatar'>";
                }
                echo "<div class='noti_left'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='noti_bold noti_username' title='{$universal->GETsDetails($by, "username")}'>". $universal->nameShortener($universal->GETsDetails($by, "username"), 20) ."</a>
                  <span>mentioned you in a <a href='{$this->DIR}/view_post/{$postid}' class='noti_bold'>post</a></span>
                  <span class='noti_time'>{$Time->timeAgo($time)}</span>
                </div>
                <div class='noti_right follow_noti_right'>
                  <a href='{$this->DIR}/view_post/{$postid}' class='noti_ff sec_btn'>View post</a>
                </div></div>";

          } else if ($type == "comment_mention") {
            echo "<div class='noti follow_noti' data-notiid='{$nid}'>";
                if ($way == "direct") {
                  echo "<img src='{$this->DIR}/{$avatar->GETsAvatar($by)}' alt='' class='noti_avatar'>";
                } else if ($way == "ajax") {
                  echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($by)}' alt='' class='noti_avatar'>";
                }
                echo "<div class='noti_left'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='noti_bold noti_username' title='{$universal->GETsDetails($by, "username")}'>". $universal->nameShortener($universal->GETsDetails($by, "username"), 20) ."</a>
                  <span>mentioned you in a <a href='{$this->DIR}/view_post/{$postid}' class='noti_bold'>comment</a></span>
                  <span class='noti_time'>{$Time->timeAgo($time)}</span>
                </div>
                <div class='noti_right follow_noti_right'>
                  <a href='{$this->DIR}/view_post/{$postid}' class='noti_ff sec_btn'>View post</a>
                </div></div>";

          } else if ($type == "grp_con") {
            echo "<div class='noti follow_noti' data-notiid='{$nid}'>";
                if ($way == "direct") {
                  echo "<img src='{$this->DIR}/{$avatar->GETsAvatar($by)}' alt='' class='noti_avatar'>";
                } else if ($way == "ajax") {
                  echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($by)}' alt='' class='noti_avatar'>";
                }
                echo "<div class='noti_left'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='noti_bold noti_username' title='{$universal->GETsDetails($by, "username")}'>". $universal->nameShortener($universal->GETsDetails($by, "username"), 20) ."</a>
                  <span>added you in conversation <a href='{$this->DIR}/messages' class='noti_bold' title='{$message->getGrpCon($postid, "name")}'>". $universal->nameShortener($message->getGrpCon($postid, "name"), 20) ."</a></span>
                  <span class='noti_time'>{$Time->timeAgo($time)}</span>
                </div>
                <div class='noti_right follow_noti_right'>
                  <a href='{$this->DIR}/messages' class='noti_ff sec_btn'>Open group</a>
                </div></div>";

          } else if ($type == "grp_add") {
            echo "<div class='noti follow_noti' data-notiid='{$nid}'>";
                if ($way == "direct") {
                  echo "<img src='{$this->DIR}/{$avatar->GETsAvatar($by)}' alt='' class='noti_avatar'>";
                } else if ($way == "ajax") {
                  echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($by)}' alt='' class='noti_avatar'>";
                }
                echo "<div class='noti_left'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='noti_bold noti_username' title='{$universal->GETsDetails($by, "username")}'>". $universal->nameShortener($universal->GETsDetails($by, "username"), 20) ."</a>
                  <span>added you in <a href='{$this->DIR}/groups/{$postid}' class='noti_bold' title='{$group->GETgrp($postid, "grp_name")}'>". $universal->nameShortener($group->GETgrp($postid, "grp_name"), 20) ."</a></span>
                  <span class='noti_time'>{$Time->timeAgo($time)}</span>
                </div>
                <div class='noti_right follow_noti_right'>
                  <a href='{$this->DIR}/groups/{$postid}' class='noti_ff sec_btn'>Open group</a>
                </div></div>";

          } else if ($type == "inviteGrp") {
            echo "<div class='noti follow_noti' data-notiid='{$nid}'>";
                if ($way == "direct") {
                  echo "<img src='{$this->DIR}/{$avatar->GETsAvatar($by)}' alt='' class='noti_avatar'>";
                } else if ($way == "ajax") {
                  echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($by)}' alt='' class='noti_avatar'>";
                }
                echo "<div class='noti_left'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='noti_bold noti_username' title='{$universal->GETsDetails($by, "username")}'>". $universal->nameShortener($universal->GETsDetails($by, "username"), 20) ."</a>
                  <span>invited you to <a href='{$this->DIR}/groups/{$postid}' class='noti_bold' title='{$group->GETgrp($postid, "grp_name")}'>". $universal->nameShortener($group->GETgrp($postid, "grp_name"), 20) ."</a></span>
                  <span class='noti_time'>{$Time->timeAgo($time)}</span>
                </div>
                <div class='noti_right follow_noti_right'>
                  <a href='{$this->DIR}/groups/{$postid}' class='noti_ff sec_btn'>Open group</a>
                </div></div>";

          } else if ($type == "changeGrpAdmin") {
            echo "<div class='noti follow_noti' data-notiid='{$nid}'>";
                if ($way == "direct") {
                  echo "<img src='{$this->DIR}/{$avatar->GETsAvatar($by)}' alt='' class='noti_avatar'>";
                } else if ($way == "ajax") {
                  echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($by)}' alt='' class='noti_avatar'>";
                }
                echo "<div class='noti_left'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='noti_bold noti_username' title='{$universal->GETsDetails($by, "username")}'>". $universal->nameShortener($universal->GETsDetails($by, "username"), 20) ."</a>
                  <span>made you admin of <a href='{$this->DIR}/groups/{$postid}' class='noti_bold' title='{$group->GETgrp($postid, "grp_name")}'>". $universal->nameShortener($group->GETgrp($postid, "grp_name"), 20) ."</a></span>
                  <span class='noti_time'>{$Time->timeAgo($time)}</span>
                </div>
                <div class='noti_right follow_noti_right'>
                  <a href='{$this->DIR}/groups/{$postid}' class='noti_ff sec_btn'>Open group</a>
                </div></div>";

          } else if ($type == "changeGrpConAdmin") {
            echo "<div class='noti follow_noti' data-notiid='{$nid}'>";
                if ($way == "direct") {
                  echo "<img src='{$this->DIR}/{$avatar->GETsAvatar($by)}' alt='' class='noti_avatar'>";
                } else if ($way == "ajax") {
                  echo "<img src='{$this->DIR}/{$avatar->DisplayAvatar($by)}' alt='' class='noti_avatar'>";
                }
                echo "<div class='noti_left'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='noti_bold noti_username' title='{$universal->GETsDetails($by, "username")}'>". $universal->nameShortener($universal->GETsDetails($by, "username"), 20) ."</a>
                  <span>made you admin of conversation <a href='{$this->DIR}/messages' class='noti_bold' title='{$message->getGrpCon($postid, "name")}'>". $universal->nameShortener($message->getGrpCon($postid, "name"), 20) ."</a></span>
                  <span class='noti_time'>{$Time->timeAgo($time)}</span>
                </div>
                <div class='noti_right follow_noti_right'>
                  <a href='{$this->DIR}/groups/{$postid}' class='noti_ff sec_btn'>Open group</a>
                </div></div>";
          }

        }
        echo "<div class='post_end feed_inserted'>Looks like you've reached the end</div>";
      }

    }

  }
?>
