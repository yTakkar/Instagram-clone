<?php

  class message {

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function getPeople($value){
      if ($value != "") {
        $session = $_SESSION['id'];
        $text = preg_replace("#[^a-zA-Z0-9_@.]#i", "", $value);

        include 'avatar.class.php';
        include 'settings.class.php';
        include 'universal.class.php';

        $avatar = new Avatar;
        $settings = new settings;
        $universal = new universal;

        $query = $this->db->prepare("SELECT follow_to_u, follow_to FROM follow_system WHERE follow_to_u LIKE :value AND follow_by = :by");
        $query->execute(array(":value" => "%$text%", ":by" => $session));
        if ($query->rowCount() != 0) {
          while ($row = $query->fetch(PDO::FETCH_OBJ)) {
            $id = $row->follow_to;
            $user = $row->follow_to_u;

            if ($settings->AmIBlocked($id) == false) {
              echo "<li data-userid='{$id}' class='select_u'><img src='{$this->DIR}/{$avatar->DisplayAvatar($id)}' alt=''>
                <span>{$universal->nameShortener($user, 25)}</span></li>";
            }

          }
        }
      }
    }

    function toMssgURL($str){
      $regex = "#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si";
      $str = preg_replace($regex, '<a class="hashtag" href="$0" target="_blank">$0</a>', $str);
      return $str;
    }

    public function mssgViaBtn($value, $to, $cname){
      $session = $_SESSION['id'];
      $text = preg_replace("#[<>]#i", "", $value);
      $name = preg_replace("#[<>]#", "", $cname);

      include 'settings.class.php';
      $settings = new settings;

      if ($settings->AmIBlocked($to) == false) {
        $query = $this->db->prepare("SELECT name FROM conversations WHERE name = :name AND ((user_one = :one AND user_two = :two) OR (user_one = :two AND user_two = :one))");
        $query->execute(array(":name" => $name, ":one" => $session, ":two" => $to));
        if ($query->rowCount() > 0) {
          echo "exists";
        } else if ($query->rowCount() == 0) {

          $comb = "$session,$to";

          $query = $this->db->prepare("INSERT INTO conversations (name, user_one, user_two, comb_users, time) VALUES(:name, :one, :two, :comb, now())");
          $query->execute(array(":name" => $name, ":one" => $session, ":two" => $to, ":comb" =>$comb));

          $last = $this->db->lastInsertId();

          if ($text != "") {
            $q = $this->db->prepare("INSERT INTO message(con_id, mssg_by, mssg_to, message, type, time, status) VALUES (:id, :by, :to, :mssg, :type, now(), :status)");
            $q->execute(array(":id" => $last, ":by" => $session, ":to" => $to, ":mssg" => $text, ":type" => "text", ":status" => "unread"));
          }

          echo "ok";

        }
      }

    }

    public function mssgCount($con, $by){
      $session = $_SESSION['id'];
      if ($by == "user") {
        $query = $this->db->prepare("SELECT message_id FROM message WHERE con_id = :id AND type <> :type");
        $query->execute(array(":id" => $con, ":type" => "name_change"));
      } else if ($by == "group") {
        $query = $this->db->prepare("SELECT message_id FROM message WHERE grp_con_id = :id AND type <> :type");
        $query->execute(array(":id" => $con, ":type" => "name_change"));
      }

      $count = $query->rowCount();
      return $count;
    }

    public function GETCon($con, $what){
      $query = $this->db->prepare("SELECT $what FROM conversations WHERE con_id = :id");
      $query->execute(array(":id" => $con));
      while ($row = $query->fetch(PDO::FETCH_OBJ)) {
        return $row->$what;
      }
    }

    public function GETmssg($con, $what){
      $query = $this->db->prepare("SELECT $what FROM message WHERE con_id = :id");
      $query->execute(array(":id" => $con));
      while ($row = $query->fetch(PDO::FETCH_OBJ)) {
        return $row->$what;
      }
    }

    public function getGrpCon($grp, $what){
      $query = $this->db->prepare("SELECT $what FROM group_con WHERE grp_con_id = :id");
      $query->execute(array(":id" => $grp));
      if ($query->rowCount() != 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          return $row->$what;
        }
      }
    }

    public function markMssgAsRead($con, $of){
      $session = $_SESSION['id'];
      if ($of == "user") {
        $query = $this->db->prepare("UPDATE message SET status = :status WHERE con_id = :id AND mssg_to = :to");
        $query->execute(array(":status" => "read", ":id" => $con, ":to" => $session));
      }
    }

    public function getAllUnreadMssg(){
      if (isset($_SESSION['id'])) {
        $session = $_SESSION['id'];
        $query = $this->db->prepare("SELECT message_id FROM message WHERE mssg_to = :to AND status = :status");
        $query->execute(array(":to" => $session, ":status" => "unread"));
        $count = $query->rowCount();

        $q = $this->db->prepare("SELECT gru_id FROM grpconunreads WHERE member = :me");
        $q->execute(array(":me" => $session));
        $c = $q->rowCount();

        $count = intval($count);
        $c = intval($c);

        $mc = $count+$c;

        if ($mc > 0) {
          if ($mc < 10) {
            return $mc;
          } else if ($mc >= 10 ) {
            return "+";
          }
        }
      }
    }

    public function insertGrpUnreads($grp, $mssg){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT members FROM group_con_members WHERE grp_con_id = :grp AND members <> :me");
      $query->execute(array(":grp" => $grp, ":me" => $session));
      while ($row = $query->fetch(PDO::FETCH_OBJ)) {
        $mem = $row->members;
        $q = $this->db->prepare("INSERT INTO grpconunreads(grp_con_id, gcu_by, member, gcu_mssg) VALUES(:grp, :by, :member, :mssg)");
        $q->execute(array(":grp" => $grp, ":by" => $session, ":member" => $mem, ":mssg" => $mssg));
      }
    }

    public function grpClearUnreads($grp){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("DELETE FROM grpconunreads WHERE grp_con_id = :grp AND member = :mem");
      $query->execute(array(":grp" => $grp, ":mem" => $session));
    }

    public function conUnreads($con){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT status FROM message WHERE con_id = :id AND status = :status AND mssg_to = :to");
      $query->execute(array(":id" => $con, ":status" => "unread", ":to" => $session));

      $count = $query->rowCount();
      if ($count > 0) {
        if ($count < 10) {
          return $count;
        } else if ($count <= 10) {
          return "+";
        }
      }
    }

    public function GrpConUnreads($grp){
      $session = $_SESSION['id'];
      $q = $this->db->prepare("SELECT gru_id FROM grpconunreads WHERE member = :me AND grp_con_id = :grp");
      $q->execute(array(":me" => $session, ":grp" => $grp));
      if ($q->rowCount() != 0) {
        return $c = $q->rowCount();
      }
    }

    public function getLastMssgTime($con, $by){
      if ($by == "user") {
        $query = $this->db->prepare("SELECT MAX(time) AS ti FROM message WHERE con_id = :id");
        $query->execute(array(":id" => $con));
      } else if ($by == "group") {
        $query = $this->db->prepare("SELECT MAX(time) AS ti FROM message WHERE grp_con_id = :id");
        $query->execute(array(":id" => $con));
      }
      $row = $query->fetch(PDO::FETCH_OBJ);
      return $row->ti;
    }

    public function mssgStatus($mssg){
      $query = $this->db->prepare("SELECT status FROM message WHERE message_id = :mssg LIMIT 1");
      $query->execute(array(":mssg" => $mssg));
      if ($query->rowCount() > 0) {
        $row = $query->fetch(PDO::FETCH_OBJ);
        return $row->status;
      }
    }

    public function conCount(){
      $session = $_SESSION['id'];

      $query = $this->db->prepare("SELECT * FROM conversations WHERE user_one = :me OR user_two = :me ORDER BY time DESC");
      $query->execute(array(":me" => $session));
      $count = $query->rowCount();

      $q = $this->db->prepare("SELECT * FROM group_con_members WHERE group_con_members.members = :me");
      $q->execute(array(":me" => $session));
      $c = $q->rowCount();

      $count = intval($count);
      $c = intval($c);

      $mc = $count+$c;

      if ($mc == 0) {
        return "No";
      } else if ($mc > 0) {
        return $mc;
      }
    }

    public function getLastMssg($con, $of){
      $universal = new universal;
      $session = $_SESSION['id'];
      if ($of == "user") {
        $query = $this->db->prepare("SELECT MAX(message_id) AS last FROM message WHERE con_id = :con AND type <> :no LIMIT 1");
        $query->execute(array(":con" => $con, ":no" => "name_change"));
      } else if ($of == "group") {
        $query = $this->db->prepare("SELECT MAX(message_id) AS last FROM message WHERE grp_con_id = :con AND type <> :no LIMIT 1");
        $query->execute(array(":con" => $con, ":no" => "name_change"));
      }
      // if ($query->rowCount() > 0) {
        $row = $query->fetch(PDO::FETCH_OBJ);
        $id = $row->last;

        $q = $this->db->prepare("SELECT message, type, mssg_by FROM message WHERE message_id = :id LIMIT 1");
        $q->execute(array(":id" => $id));
        if ($q->rowCount() > 0) {
          $row = $q->fetch(PDO::FETCH_OBJ);
          $mssg = $row->message;
          $type = $row->type;
          $by = $row->mssg_by;

          if ($by == $session) {
            echo "<span class='mssg_sent'><i class='material-icons'>done_all</i></span>";
          }
          if ($of == "group" && $by != $session && ($type == "text" || $type == "image" || $type == "sticker")) {
            echo "{$universal->nameShortener($universal->GETsDetails($by, "username"), 15)}: ";
          }

          if ($type == "image") {
            return "<span><i class='fa fa-camera retro' aria-hidden='true'></i>Image</span>";
          } else if ($type == "sticker") {
            return "<span><i class='fa fa-gift retro' aria-hidden='true'></i>Sticker</span>";

          } else if ($type == "text") {
            if (filter_var($mssg, FILTER_VALIDATE_URL) == true) {
              return "<span><i class='fa fa-link retro' aria-hidden='true'></i>Link</span>";
            } else {

              return $universal->nameShortener($mssg, 15);

              // return "Text";

            }

          } else if($type == "avatar_change") {
            return "Avatar changed";
          } else if($type == "name_change") {
            return "Name changed";
          } else if ($type == "member_add") {
            return "Member added";
          } else if($type == "leave_grp_con"){
            return "Member left";
          } else if ($type == "removed_grp_con") {
            return "Member removed";
          } else if ($type == "admin_change") {
            return "Admin changed";
          } else {
            return "";
          }

        }

    }

    public function conversations(){
      $session = $_SESSION['id'];
      $universal = new universal;
      $avatar = new Avatar;
      $Time = new time;

      $query = $this->db->prepare("SELECT * FROM conversations WHERE user_one = :me OR user_two = :me ORDER BY time DESC");
      $query->execute(array(":me" => $session));
      if ($query->rowCount() > 0) {

        // echo "<span class='con_count' data-count='{$query->rowCount()}'>{$query->rowCount()} conversations</span>";

        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $con_id = $row->con_id;
          $name = $row->name;
          $two = $row->user_two;
          $comb = $row->comb_users;
          // if ($two != $session) {

            $array = explode(",", $comb);
            if ($array[0] == $session) {
              $user = intval($array[1]);
            } else if($array[1] == $session) {
              $user = intval($array[0]);
            }

            echo "<div class='mssg_sr mssg_usr inst' data-cid='{$con_id}' data-utwo='{$user}' id='c_{$con_id}' data-of='user'>
            <img src='{$this->DIR}/{$avatar->GETsAvatar($user)}' alt=''>";

            echo "<div class='m_sr_ontent'>
              <span class='m_sr_username'>". $universal->nameShortener($name, 20) ."</span><span class='m_sr_light'>with {$universal->nameShortener($universal->GETsDetails($user, "username"), 12)}: ";
              echo self::getLastMssg($con_id, "user");
              echo "</span>
            </div>";

              echo "<span class='m_sr_time'>";
              $t = self::getLastMssgTime($con_id, "user");
              if ($t == "") { echo ""; } else { echo $Time->timeAgo($t); }
              echo "</span><span class='m_sr_unread'>". self::conUnreads($con_id) ."</span></div>";
        }
      } else if ($query->rowCount() == 0) {
        echo "<div class='home_last_mssg con_last_mssg'>
          <img src='{$this->DIR}/images/needs/tumblr_static_cbyn77qow2ogcgskwcko04c8w.png'><span>No conversations</span></div>";
      }

    }

    public function conInfo($con){
      $session = $_SESSION['id'];

      include 'universal.class.php';
      include 'avatar.class.php';
      include 'mutual.class.php';
      include 'time.class.php';
      include 'follow_system.class.php';

      $universal = new universal;
      $avatar = new Avatar;
      $mutual = new mutual;
      $Time = new time;
      $follow = new follow_system;

      $query = $this->db->prepare("SELECT * FROM conversations WHERE con_id = :con LIMIT 1");
      $query->execute(array(":con" => $con));
      $row = $query->fetch(PDO::FETCH_OBJ);
      $name = $row->name;
      $time = $row->time;
      $comb = $row->comb_users;

      $array = explode(",", $comb);
      if ($array[0] == $session) {
        $user = intval($array[1]);
      } else if($array[1] == $session) {
        $user = intval($array[0]);
      }

      echo
        "<div class='sli_cancel_div'><span class='sli_cancel'><i class='material-icons'>close</i></span>
        </div><div class='sli_name_div'><span class='sli_label'>Conversation name</span>
          <span class='sli_bold'>{$name}</span></div>
        <div class='sli_with_div'><span class='sli_label'>Conversation with</span>
          <div class='sli_with'><img src='{$this->DIR}/{$avatar->DisplayAvatar($user)}'>
            <div class='sli_with_cont'><a href='{$this->DIR}/profile/{$universal->GETsDetails($user, "username")}'>". $universal->nameShortener($universal->GETsDetails($user, "username"), 20) ."</a>
                  <span class='sli_w'>{$mutual->eMutual($user)}</span></div></div></div>

              <div class='sli_time'><span class='sli_label'>Conversation since</span>
                <span class='sli_bold'>{$Time->timeAgo($time)} - {$Time->normalTime($time)}</span></div>

            <div class='sli_mssg_count'><span class='sli_label'>No. of messages</span>
              <span class='sli_bold'>". self::mssgCount($con, "user") ." messages</span></div>

            <div class='sli_media'><span class='sli_label'>Media</span>";

              $q = $this->db->prepare("SELECT message, mssg_by, time FROM message WHERE con_id = :con AND type = :type ORDER BY time DESC");
              $q->execute(array(":con" => $con, ":type" => "image"));
              if ($q->rowCount() > 0) {
                while ($row = $q->fetch(PDO::FETCH_OBJ)) {
                  if ($row->mssg_by == $session) { $sent = "You"; } else { $sent = $universal->GETsDetails($row->mssg_by, "username"); }
                  echo "<img src='{$this->DIR}/message/Instagram_{$row->message}' class='sli_media_img' data-description='{$sent}' title='By {$sent}' data-time='{$Time->timeAgo($row->time)}' data-imgby='{$universal->GETsDetails($row->mssg_by, "username")}'>";
                }
              } else if ($q->rowCount() == 0) {
                echo "<div class='home_last_mssg sli_last_mssg'><img src='{$this->DIR}/images/needs/tumblr_static_cbyn77qow2ogcgskwcko04c8w.png'></div>";
              }
            echo "</div>";

    }

    public function getLastAvatarChanger($grp, $what){
      $query = $this->db->prepare("SELECT MAX(message_id) AS l FROM message WHERE grp_con_id = :grp AND type = :type LIMIT 1");
      $query->execute(array(":grp" => $grp, ":type" => "avatar_change"));
      if ($query->rowCount() != 0) {
        $row = $query->fetch(PDO::FETCH_OBJ);
        $max = $row->l;
        $q = $this->db->prepare("SELECT $what FROM message WHERE message_id = :id LIMIT 1");
        $q->execute(array(":id" => $max));
        return $q->fetch(PDO::FETCH_OBJ)->$what;
      }
    }

    public function grpConInfo($grp){
      $session = $_SESSION['id'];

      include 'universal.class.php';
      include 'avatar.class.php';
      include 'mutual.class.php';
      include 'time.class.php';
      include 'follow_system.class.php';

      $universal = new universal;
      $avatar = new Avatar;
      $mutual = new mutual;
      $Time = new time;
      $follow = new follow_system;

      $query = $this->db->prepare("SELECT * FROM group_con WHERE grp_con_id = :con LIMIT 1");
      $query->execute(array(":con" => $grp));
      $row = $query->fetch(PDO::FETCH_OBJ);
      $name = $row->name;
      $time = $row->time;
      $av = $row->avatar;
      $admin = $row->admin;

      echo
        "<div class='sli_cancel_div'>
        <span class='sli_cancel'><i class='material-icons'>close</i></span>
        </div>";

        echo "<form class='edit_grp_con_ava_form' method='post' action='' enctype='multipart/form-data' data-grp_con_id='{$grp}'>
          <input type='file' class='edit_grp_con_ava' name='edit_grp_con_ava' id='edit_grp_con_ava'>
          <label for='edit_grp_con_ava' class='sec_btn'>Change avatar</label>
        </form>";

        // if ($admin == $session) {
          echo "<div class='sli_add'><a href='#' class='sec_btn sli_add_mem'>Add members</a></div>";
          echo "<div class='sli_add_search' data-grp_con_id='{$grp}'><input type='text' spellcheck='false' placeholder='Seach to add..'></div>";
          echo "<div class='grp_to_persons sli_to_persons'>
            <div class='grp_to_persons_inner'>
              <ul class='grp_to_ul'>
              </ul>
            </div>
          </div>";
        // }

        echo "<div class='sli_avatar'>
        <img src='";
        if ($av == "") {
          echo "{$this->DIR}/images/Default_group_con/Epic-Circle-31m3ldalla6v0uqb8ne6mi.png";
        } else {
          echo "{$this->DIR}/grp_mssg_avatar/Instagram_{$av}";
        }
        $ch = self::getLastAvatarChanger($grp, "mssg_by");
        if($ch == $session){$f = "You";} else {$f = $universal->GETsDetails($ch, "username");}
        echo "' class='sli_avatar_img' data-time='{$Time->timeAgo(self::getLastAvatarChanger($grp, "time"))}' data-imgby='{$f}'>
        </div>

        <div class='sli_name_div'>
          <span class='sli_label'>Group name</span>
          <span class='sli_bold'>{$name}</span>
        </div>

            <div class='sli_with_div'>
              <span class='sli_label'>Group created by</span>";
              if ($admin == $session) {
                echo "<span class='sli_bold'>You</span>";
              } else {
                echo "<div class='sli_with'>
                  <img src='{$this->DIR}/{$avatar->DisplayAvatar($admin)}'>
                  <div class='sli_with_cont'>
                    <a href='{$this->DIR}/profile/{$universal->nameShortener($universal->GETsDetails($admin, "username"), 20)}'>{$universal->GETsDetails($admin, "username")}</a>
                    <span>{$mutual->eMutual($admin)}</span>
                  </div>
                </div>";
              }

            echo "</div>

            <div class='sli_time'><span class='sli_label'>Group created</span>
              <span class='sli_bold'>{$Time->timeAgo($time)} ago - {$Time->normalTime($time)}</span></div>

            <div class='sli_mssg_count'><span class='sli_label'>No. of messages</span>
              <span class='sli_bold'>". self::mssgCount($grp, "group") ." messages</span></div>

            <div class='sli_media'><span class='sli_label'>Media</span>";

              $q = $this->db->prepare("SELECT message, mssg_by, time FROM message WHERE grp_con_id = :con AND type = :type ORDER BY time DESC");
              $q->execute(array(":con" => $grp, ":type" => "image"));
              if ($q->rowCount() > 0) {
                while ($row = $q->fetch(PDO::FETCH_OBJ)) {
                  if ($row->mssg_by == $session) { $sent = "You"; } else { $sent = $universal->GETsDetails($row->mssg_by, "username"); }
                  echo "<img src='{$this->DIR}/message/Instagram_{$row->message}' class='sli_media_img' data-description='{$sent}' title='By {$sent}' data-time='{$Time->timeAgo($row->time)}' data-imgby='{$sent}'>";
                }
              } else if ($q->rowCount() == 0) {
                echo "<div class='home_last_mssg sli_last_mssg'><img src='{$this->DIR}/images/needs/tumblr_static_cbyn77qow2ogcgskwcko04c8w.png'></div>";
              }
              echo "</div>
              <div class='sli_with_div'>
                <span class='sli_label no_of_grp_con_mems'>". self::grpMemCount($grp) ." group members</span>";

                $r = $this->db->prepare("SELECT members FROM group_con_members WHERE grp_con_id = :grp ORDER BY grp_con_mem_id DESC");
                $r->execute(array(":grp" =>$grp));
                while ($rr = $r->fetch(PDO::FETCH_OBJ)) {
                  $member = $rr->members;
                  echo "<div class='sli_with g_sli_with'>
                    <img src='{$this->DIR}/{$avatar->DisplayAvatar($member)}'>
                    <div class='sli_with_cont'>
                      <a href='{$this->DIR}/profile/{$universal->GETsDetails($member, "username")}'>". $universal->nameShortener($universal->GETsDetails($member, "username"), 20) ."</a>";
                      if ($admin == $member) {
                        echo "<span class='grp_admin_indicate'>admin</span>";
                      }
                      echo "<span class='sli_w'>";
                      if($session == $member){
                        echo "You";
                      } else if($universal->isOnline($member)){
                        echo "online";
                      } else {
                        echo $mutual->eMutual($member);
                      }
                      echo "</span>";
                        echo "<div class='sli_with_tools' data-grp_con_id='{$grp}'data-user='{$member}' data-username='{$universal->nameShortener($universal->GETsDetails($member, "username"), 20)}'>";
                        if ($admin == $session && $member != $session) {
                          echo "<a href='#' class='sec_btn sli_with_remove'>Remove</a>";
                        }
                        echo "</div>";
                      // <a href='#' class='sec_btn sli_with_admin'>Admin</a>
                    echo "</div>
                  </div>";
                }

              echo "</div>";

              echo "<div class='sli_with_leave_div' data-grp_con_id='{$grp}'>";
              if ($admin != $session) {
                echo "<a href='#' class='pri_btn sli_with_leave'>Leave group</a></div>";
              }

              echo "</div>";

    }

    public function getMessages($con, $user){
      $session = $_SESSION['id'];

      include 'universal.class.php';
      include 'avatar.class.php';
      include 'time.class.php';

      $avatar = new Avatar;
      $universal = new universal;
      $Time = new time;

      self::markMssgAsRead($con, "user");

      $query = $this->db->prepare("SELECT * FROM message WHERE con_id = :id ORDER BY time");
      $query->execute(array(":id" => $con));

        echo
        "<div class='mssg_messages' data-u='{$user}' data-conid='{$con}'><div class='m_m_top'><img src='{$this->DIR}/{$avatar->DisplayAvatar($user)}' alt=''>
            <div class='m_m_t_c'>
            <span class='con_name' spellcheck='false' maxlength='23'>";
            $n = self::GETCon($con, "name");
            if ($n == "") { echo "Name your conversation"; } else if($n != "") { echo $n; }
            echo "</span>
              <span class='m_m_t_useless'>with</span>
              <a href='{$this->DIR}/profile/{$universal->GETsDetails($user, "username")}' class='m_m_t_a'>". $universal->nameShortener($universal->GETsDetails($user, "username"), 20) ."</a>";
              if($universal->isOnline($user)){
                echo " <span class='user_m_status'>online<span>";
              }
            echo "</div>";
            // <span class='mssg_sticker' data-description='Add sticker'><i class='material-icons'>face</i></span>
            // <form class='mssg_add_img_form' action='' method='post'>
            //   <input type='file' name='mssg_add_img' value='' id='mssg_add_img'><label for='mssg_add_img' class='mssg_img' data-description='Add image'><i class='material-icons'>photo_camera</i></label>
            // </form>
            echo "<span class='m_m_exp'><i class='material-icons'>expand_more</i></span>
            <div class='mssg_options options'><ul>
              <li><a href='#' class='dlt_con'>Delete conversation</a></li>
              <li><a href='#' class='dlt_mssgs'>Unsend all mssgs</a></li>
              <li><a href='#' class='edit_con_name'>Edit name</a></li>
              <li><form class='mssg_add_img_form' action='' method='post'>
                <input type='file' name='mssg_add_img' value='' id='mssg_add_img' data-u='{$user}' data-conid='{$con}'>
                <label for='mssg_add_img' class='mssg_img'>Add image</label>
              </form></li>
              <li><a href='#' class='mssg_sticker'>Add sticker</a></li>
              <li><a href='#' class='m_m_info'>More</a></li>
            </ul></div></div><div class='m_m_wrapper'><div class='m_m_main'>";

            if ($query->rowCount() == 0) {
              echo "<div class='home_last_mssg mssgs_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'></div>";
            }

            while ($row = $query->fetch(PDO::FETCH_OBJ)) {
              $mssg = $row->message_id;
              $data = $row->message;
              $type = $row->type;
              $by = $row->mssg_by;
              $to = $row->mssg_to;
              $time = $row->time;

              // if (filter_var($data, FILTER_VALIDATE_URL) == true) {
              //   echo "<a href='{$data}' class='";
              //   if($session == $by){ echo "my_m_m_link"; }
              //   echo " ";
              //   if ($session == $to) { echo "not_m_m_link"; }
              //   echo "' target='_blank'>{". nl2br(trim($data)) ."}</a>";
              // } else {
              //   echo nl2br(trim($data));
              // }

              if ($type == "text" || $type == "image" || $type == "sticker") {
                echo "<div class='m_m_divs ";
                if ($by == $session) { echo "my_mm_div"; } else { echo "not_my_mm_div"; }
                echo"'>
                  <div title='{$Time->timeAgo($time)} ago' data-mssgid='{$mssg}' data-conid='{$con}' data-type='{$type}' class='m_m ";
                  if ($by == $session) { echo "my_mm"; } else { echo "not_my_mm"; }
                  echo "' spellcheck='false'>";

                  if ($type == "text") {
                    $data = self::toMssgURL($data);
                    echo nl2br(trim($data));
                  } else if($type == "image") {
                    if($by == $session){ $m = "You"; } else { $m = $universal->GETsDetails($by, "username"); }
                    echo "<img src='{$this->DIR}/message/Instagram_{$data}' class='m_m_img' data-imgby='{$m}' data-time='{$Time->timeAgo($time)}'>";
                  } else if ($type == "sticker") {
                    echo "<img src='{$this->DIR}/message/Instagram_{$data}' class='m_m_sticker'>";
                  }
                  echo "</div>
                  <span class='m_m_time'>{$Time->timeAgo($time)}</span>
                  <div class='m_m_tools'>";
                  if ($by == $session) {
                    echo "<span class='m_m_status' data-description='Sent'><i class='material-icons'>check</i></span>";
                    echo "<span class='m_m_dlt' data-description='Unsend'><i class='material-icons'>delete</i></span>";
                    if($type == "text"){
                      echo "<span class='m_m_edit' data-description='Edit'><i class='material-icons'>mode_edit</i></span>";
                    }
                  }
                  echo "</div></div>";

              } else if ($type == "name_change") {
                echo "<div class='m_m_divs m_m_info_div'><span class='mssg_info' title='{$Time->timeAgo($time)}'>";
                if ($by == $session) {
                  echo "You changed conversation name to "."<span class='m_m_name_change'>{$data}</span>";
                } else if ($by != $session) {
                  echo $universal->GETsDetails($by, "username"). " changed conversation name to "."<span class='m_m_name_change'>{$data}</span>";
                }
                echo "</span></div>";
              }

            }

            echo "<span class='mssg_helper'></span></div></div>";
            echo "<div class='m_m_slider'></div>";
            echo "<div class='m_m_bottom' data-u='{$user}' data-conid='{$con}'><form class='add_mssg_form' action='' method='post'>";
        // <input type='text' name='' value='' placeholder='Send message' class='send_mssg' spellcheck='false'>
        echo "<div class='send_mssg_before'>Sending message..</div>";
        echo "<textarea name='' value='' placeholder='Send message..' class='send_mssg' spellcheck='false'></textarea>";
        echo "<span class='mssg_emoji_btn'><i class='material-icons'>sentiment_very_satisfied</i></span>
        <input type='submit' name='' value='Send' class='pri_btn mssg_send'>
      </form></div></div>";

    }

    public function sendMessageText($value, $to, $con, $mssgOf){
      $session = $_SESSION['id'];
      $text = preg_replace("#[<>]#i", "", $value);
      $text = trim($text);

      if ($text != "") {
        if ($mssgOf == "user") {
          $query = $this->db->prepare("INSERT INTO message(con_id, mssg_by, mssg_to, message, type, time, status) VALUES (:con, :by, :to, :mssg, :type, now(), :status)");
          $query->execute(array(":con" => $con,":by" => $session, ":to" => $to, ":mssg" => $text, ":type" => "text", ":status" => "unread"));

        } else if ($mssgOf == "group") {
          $query = $this->db->prepare("INSERT INTO message(grp_con_id, mssg_by, message, type, mssg_of, time, status) VALUES (:con, :by, :mssg, :type, :of, now(), :status)");
          $query->execute(array(":con" => $con,":by" => $session, ":mssg" => $text, ":type" => "text", ":of" => "group", ":status" => "unread"));
          $i = $this->db->lastInsertId();
          self::insertGrpUnreads($con, $i);
        }
      }

    }

    public function sendMessageImage($file, $to, $con, $mssgOf){
      $session = $_SESSION['id'];

      $name = $file['name'];
      $size = $file['size'];
      $tmp_name = $file['tmp_name'];
      $error = $file['error'];
      $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
      $allowed = array("jpg", "png", "gif", "jpeg");

      if (in_array($ext, $allowed)) {
        if ($error == 0) {
          $new_name = time().".".$ext;
          if (move_uploaded_file($tmp_name, "../../message/Instagram_".$new_name)) {

            if ($mssgOf == "user") {
              $query = $this->db->prepare("INSERT INTO message(con_id, mssg_by, mssg_to, message, type, time, status) VALUES (:con, :by, :to, :mssg, :type, now(), :status)");
              $query->execute(array(":con" => $con,":by" => $session, ":to" => $to, ":mssg" => $new_name, ":type" => "image", ":status" => "unread"));

            } else if ($mssgOf == "group") {
              $query = $this->db->prepare("INSERT INTO message(grp_con_id, mssg_by, message, type, mssg_of, time, status) VALUES (:con, :by, :mssg, :type, :of, now(), :status)");
              $query->execute(array(":con" => $con,":by" => $session, ":mssg" => $new_name, ":type" => "image", ":of" => "group", ":status" => "unread"));
              $i = $this->db->lastInsertId();
              self::insertGrpUnreads($con, $i);
            }
            return $new_name;
          }
        }
      }

    }

    public function sendMessageSticker($file, $touser, $con, $mssgOf){
      $session = $_SESSION['id'];
      $ext = pathinfo($file, PATHINFO_EXTENSION);
      $image = substr($file, strrpos($file, "/")+1);
      $from = "../../images/stickers/$image";
      $to = "../../message/Instagram_".time().".".$ext;
      @copy($from, $to);
      $new_name = substr($to, 24);

      if ($mssgOf == "user") {
        $query = $this->db->prepare("INSERT INTO message(con_id, mssg_by, mssg_to, message, type, time, status) VALUES (:con, :by, :to, :mssg, :type, now(), :status)");
        $query->execute(array(":con" => $con,":by" => $session, ":to" => $to, ":mssg" => $new_name, ":type" => "sticker", ":status" => "unread"));

      } else if ($mssgOf == "group") {
        $query = $this->db->prepare("INSERT INTO message(grp_con_id, mssg_by, message, type, mssg_of, time, status) VALUES (:con, :by, :mssg, :type, :of, now(), :status)");
        $query->execute(array(":con" => $con,":by" => $session, ":mssg" => $new_name, ":type" => "sticker", ":of" => "group", ":status" => "unread"));
        $i = $this->db->lastInsertId();
        self::insertGrpUnreads($con, $i);
      }

      return substr($to, 6);
    }

    public function deleteAllMssg($con, $by){
      $session = $_SESSION['id'];

      if ($by == "user") {
        $query = $this->db->prepare("SELECT type, message FROM message WHERE con_id = :con AND mssg_by = :by");
        $query->execute(array(":con" => $con, ":by" => $session));
      } else if ($by == "group") {
        $query = $this->db->prepare("SELECT type, message FROM message WHERE grp_con_id = :con AND mssg_by = :by");
        $query->execute(array(":con" => $con, ":by" => $session));
      }

      if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $type = $row->type;
          $mssg = $row->message;
          if ($type != "text") {
            if(file_exists("../../message/Instagram_{$mssg}")){
              unlink("../../message/Instagram_".$mssg);
            }
          }
        }
      }

      if ($by == "user") {
        $q = $this->db->prepare("DELETE FROM message WHERE con_id = :con AND mssg_by = :by");
        $q->execute(array(":con" => $con, ":by" => $session));
      } else if ($by == "group") {
        $q = $this->db->prepare("DELETE FROM message WHERE grp_con_id = :con AND mssg_by = :by");
        $q->execute(array(":con" => $con, ":by" => $session));

        $r = $this->db->prepare("DELETE FROM grpconunreads WHERE grp_con_id = :grp AND gcu_by = :by");
        $r->execute(array(':grp' => $con, ':by' => $session));

      }

    }

    public function deleteConversation($con, $by){
      self::deleteAllMssg($con, $by);
      if ($by == "user") {
        $query = $this->db->prepare("DELETE FROM conversations WHERE con_id = :id");
        $query->execute(array(":id" => $con));

        $query = $this->db->prepare("DELETE FROM message WHERE con_id = :id");
        $query->execute(array(":id" => $con));

      } else if ($by == "group") {

        $av = self::getGrpCon($con, "avatar");
        $dir = "../../grp_mssg_avatar/Instagram_{$av}";
        if (file_exists($dir)) {
          unlink($dir);
        }

        $query = $this->db->prepare("DELETE FROM group_con_members WHERE grp_con_id = :id");
        $query->execute(array(":id" => $con));

        $query = $this->db->prepare("DELETE FROM grpconunreads WHERE grp_con_id = :id");
        $query->execute(array(":id" => $con));

        $query = $this->db->prepare("DELETE FROM message WHERE grp_con_id = :id");
        $query->execute(array(":id" => $con));

        $query = $this->db->prepare("DELETE FROM group_con WHERE grp_con_id = :id");
        $query->execute(array(":id" => $con));
      }
    }

    public function editConName($ename, $con, $to, $by){
      $session = $_SESSION['id'];
      $name = preg_replace("#[<>]#", "", $ename);
      $name = trim($name);

      if ($by == "user") {
        $query = $this->db->prepare("UPDATE conversations SET name = :name WHERE con_id = :id");
        $query->execute(array(":name" => $name, ":id" => $con));

        $q = $this->db->prepare("INSERT INTO message(con_id, mssg_by, mssg_to, message, type, time, status) VALUES (:con, :by, :to, :mssg, :type, now(), :status)");
        $q->execute(array(":con" => $con, ":by" => $session, ":to" => $to, ":mssg" => $name, ":type" => "name_change", ":status" => "unread"));
      } else if ($by == "group") {
        $query = $this->db->prepare("UPDATE group_con SET name = :name WHERE grp_con_id = :id");
        $query->execute(array(":name" => $name, ":id" => $con));

        $q = $this->db->prepare("INSERT INTO message(grp_con_id, mssg_by, message, type, time, status) VALUES (:con, :by, :mssg, :type, now(), :status)");
        $q->execute(array(":con" => $con, ":by" => $session, ":mssg" => $name, ":type" => "name_change", ":status" => "unread"));
      }

    }

    public function deleteMessage($mssg, $con, $type, $by){
      $session = $_SESSION['id'];
      if ($type != "text") {
        $q = $this->db->prepare("SELECT message FROM message WHERE message_id = :id");
        $q->execute(array(":id" => $mssg));
        if ($q->rowCount() > 0) {
          $row = $q->fetch(PDO::FETCH_OBJ);
          $data = $row->message;
          if (file_exists("../../message/Instagram_{$data}")) {
            unlink("../../message/Instagram_".$data);
          }
        }
      }
      if ($by == "user") {
        $query = $this->db->prepare("DELETE FROM message WHERE message_id = :id AND con_id = :con");
        $query->execute(array(":id" => $mssg, ":con" => $con));
      } else if ($by == "group") {
        $query = $this->db->prepare("DELETE FROM message WHERE message_id = :id AND grp_con_id = :con");
        $query->execute(array(":id" => $mssg, ":con" => $con));

        $r = $this->db->prepare("DELETE FROM grpconunreads WHERE grp_con_id = :grp AND gcu_by = :me AND gcu_mssg = :mssg");
        $r->execute(array(':grp' => $con, ':me' => $session, ':mssg' => $mssg));

      }

    }

    public function editMessage($value, $mssg){
      $text = preg_replace("#[<>]#", "", $value);
      $text = trim($text);
      $query = $this->db->prepare("UPDATE message SET message = :mssg WHERE message_id = :id");
      $query->execute(array(":mssg" => $text, ":id" => $mssg));
      return $text;
    }

    public function getGrpMembersForAdd($evalue, $except){
      $value = preg_replace("#[^a-z0-9_@.]#i", "", $evalue);
      // $except = strtolower($except);
      $array = explode(",", $except);
      $session = $_SESSION['id'];
      $new = array();
      $my = array();

      if ($value != "") {
        include 'avatar.class.php';
        include 'universal.class.php';
        include 'settings.class.php';

        $avatar = new Avatar;
        $universal = new universal;
        $settings = new settings;

        $query = $this->db->prepare("SELECT DISTINCT follow_to_u FROM follow_system WHERE follow_to_u LIKE :username AND follow_by = :whome");
        $query->execute(array(":username" => "%$value%", ":whome" => $session));
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
          $user = $row['follow_to_u'];
          $new[] = $row['follow_to_u'];
        }

        foreach ($new as $value) {
          if (!in_array($value, $array)) {
            $my[] = $value;
          }
        }

        foreach ($my as $value) {
          $nquery = $this->db->prepare("SELECT id, username FROM users WHERE username = :what");
          $nquery->execute(array(":what" => $value));
          $row = $nquery->fetch(PDO::FETCH_OBJ);
          $id = $row->id;
          $username = $row->username;
          if ($settings->AmIBlocked($id) == false) {
            echo "<li class='grp_to_select_u'><img src='{$this->DIR}/" .$avatar->DisplayAvatar($id) ."' alt=''>";
            echo "<span>". $universal->nameShortener($username, 25) ."</span></li>";
          }
        }

      }

    }

    public function addGroup($cname, $members, $file){
      $name = preg_replace("#[<>]#", "", $cname);
      $session = $_SESSION['id'];
      if (isset($file)) {
        $fname = $file['name'];
        $size = $file['size'];
        $tmp_name = $file['tmp_name'];
        $error = $file['error'];
        $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
        $allowed = array("jpg", "png", "gif", "jpeg");
        if (in_array($ext, $allowed)) {
          $new_name = time().".".$ext;
          move_uploaded_file($tmp_name, "../../grp_mssg_avatar/Instagram_".$new_name);
        }
      }

      if (isset($file)) {
        $av = $new_name;
      } else{
        $av = "";
      }

      $query = $this->db->prepare("INSERT INTO group_con(name, avatar, time, admin) VALUES(:name, :avatar, now(), :admin)");
      $query->execute(array(":name" => $name, ":avatar" => $av, ":admin" => $session));

      $last = $this->db->lastInsertId();

      include 'universal.class.php';
      include 'notifications.class.php';

      $universal = new universal;
      $noti = new notifications;

      $r = $this->db->prepare("INSERT INTO group_con_members(grp_con_id, members) VALUES(:id, :members)");
      $r->execute(array(":id" => $last, ":members" => $session));

      $a = $this->db->prepare("INSERT INTO message(grp_con_id, mssg_by, message, type, time, status) VALUES (:con, :by, :mssg, :type, now(), :status)");
      $a->execute(array(":con" => $last, ":by" => $session, ":mssg" => "", ":type" => "avatar_change", ":status" => "unread"));

      $array = explode(",", $members);
      foreach ($array as $value) {
        $id = $universal->getIdFromGet($value);
        $q = $this->db->prepare("INSERT INTO group_con_members(grp_con_id, members) VALUES(:id, :members)");
        $q->execute(array(":id" => $last, ":members" => $id));
        // $noti->followNotify($id, "grp_con");
        $noti->actionNotify($id, $grp, "grp_con");
      }
    }

    public function groupsCon(){
      $Time = new time;
      $universal = new universal;
      $session = $_SESSION['id'];

      $query = $this->db->prepare("SELECT * FROM group_con_members, group_con WHERE group_con_members.members = :mine AND group_con_members.grp_con_id = group_con.grp_con_id ORDER BY time DESC");
      $query->execute(array(":mine" => $session));
      if ($query->rowCount() != 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          // echo "<pre>", print_r($row)."</pre>";
          $name = $row->name;
          $time = $row->time;
          $grpCon = $row->grp_con_id;
          $av = $row->avatar;

          echo "<div class='mssg_sr mssg_gsr inst' data-gcid='{$grpCon}' id='cgrp_{$grpCon}' data-of='group'>";
          if ($av == "") {
            echo "<img src='{$this->DIR}/images/Default_group_con/Epic-Circle-31m3ldalla6v0uqb8ne6mi.png'>";
          } else {
            echo "<img src='{$this->DIR}/grp_mssg_avatar/Instagram_{$av}'>";
          }
            echo "<div class='m_sr_ontent'>
              <span class='m_sr_username'>". $universal->nameShortener($name, 20) ."</span><span class='m_sr_light'>";
              echo self::getLastMssg($grpCon, "group");
              echo "</span>
            </div>
            <span class='m_sr_time'>";
            $t = self::getLastMssgTime($grpCon, "group");
            if ($t == "") { echo ""; } else { echo $Time->timeAgo($t); }
            echo "</span><span class='m_sr_unread'>". self::GrpConUnreads($grpCon) ."</span></div>";

        }
      } else if ($query->rowCount() == 0) {
        echo "<div class='home_last_mssg pro_last_mssg grpp_last_mssg'>
          <img src='{$this->DIR}/images/needs/tumblr_static_cbyn77qow2ogcgskwcko04c8w.png'><span>No group conversation</span></div>";
      }
    }

    public function grpConAvatar($av){
      if ($av == "") {
        return "{$this->DIR}/images/Default_group_con/Epic-Circle-31m3ldalla6v0uqb8ne6mi.png";
      } else {
        return "{$this->DIR}/grp_mssg_avatar/Instagram_".$av;
      }
    }

    public function grpMemCount($grp){
      $query = $this->db->prepare("SELECT members FROM group_con_members WHERE grp_con_id = :grp");
      $query->execute(array(":grp" => $grp));
      return $query->rowCount();
    }

    public function getGrpMessages($grp){
      $session = $_SESSION['id'];

      include 'universal.class.php';
      include 'avatar.class.php';
      include 'time.class.php';

      $avatar = new Avatar;
      $universal = new universal;
      $Time = new time;

      self::grpClearUnreads($grp);

      $query = $this->db->prepare("SELECT * FROM message WHERE grp_con_id = :id ORDER BY time");
      $query->execute(array(":id" => $grp));

      echo
      "<div class='mssg_messages' data-grp_con_id='{$grp}'><div class='m_m_top'><img src='";
      $av = self::getGrpCon($grp, "avatar");
      echo self::grpConAvatar($av);
      echo "' alt=''>
          <div class='m_m_t_c'>
          <span class='con_name' spellcheck='false' maxlength='23'>";
          $n = self::getGrpCon($grp, "name");
          if ($n == "") { echo "Name your conversation"; } else if($n != "") { echo $n; }
          echo "</span>
            <span class='m_m_t_useless'>". self::grpMemCount($grp) ." members in this group</span>
          </div>";
          // <span class='mssg_sticker' data-description='Add sticker'><i class='material-icons'>face</i></span>
          // <form class='mssg_add_img_form' action='' method='post'>
          //   <input type='file' name='mssg_add_img' value='' id='mssg_add_img'><label for='mssg_add_img' class='mssg_img' data-description='Add image'><i class='material-icons'>photo_camera</i></label>
          // </form>";
          echo "<span class='m_m_exp'><i class='material-icons'>expand_more</i></span>
          <div class='mssg_options options'><ul>";
          $a = self::getGrpCon($grp, "admin");
          if ($a == $session) {
            echo "<li><a href='#' class='dlt_con'>Delete group</a></li>";
          }
            echo
            "<li><a href='#' class='dlt_mssgs'>Unsend all mssgs</a></li>
            <li><a href='#' class='edit_con_name'>Edit name</a></li>
            <li><form class='mssg_add_img_form' action='' method='post'>
              <input type='file' name='mssg_add_img' value='' id='mssg_add_img' data-grp_con_id='{$grp}'>
              <label for='mssg_add_img' class='mssg_img'>Add image</label>
            </form></li>
            <li><a href='#' class='mssg_sticker'>Add sticker</a></li>";
            if ($a == $session) {
              echo "<li><a href='#' class='ch_grp_con_admin' data-grp='{$grp}'>Change admin</a></li>";
            }
            echo "<li><a href='#' class='m_m_info'>More</a></li>
          </ul>
          </div></div><div class='m_m_wrapper'><div class='m_m_main'>";

          if ($query->rowCount() == 0) {
            echo "<div class='home_last_mssg mssgs_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'></div>";
          }

          while ($row = $query->fetch(PDO::FETCH_OBJ)) {
            $mssg = $row->message_id;
            $data = $row->message;
            $type = $row->type;
            $by = $row->mssg_by;
            $time = $row->time;

            if ($type == "text" || $type == "image" || $type == "sticker") {
              echo "<div class='m_m_divs ";
              if ($by == $session) { echo "my_mm_div"; } else { echo "not_my_mm_div"; }
              echo"'>
                <div title='{$Time->timeAgo($time)} ago' data-mssgid='{$mssg}' data-grp_con_id='{$grp}' data-type='{$type}' class='m_m ";
                if ($by == $session) { echo "my_mm"; } else { echo "not_my_mm"; }
                echo "' spellcheck='false'>";
                if ($by != $session) { echo "<a class='grp_sent_by' href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}'>{$universal->nameShortener($universal->GETsDetails($by, "username"), 20)}</a>"; }
                if ($type == "text") {
                  if (filter_var($data, FILTER_VALIDATE_URL) == true) {
                    echo "<a href='{$data}' class='";
                    if($session == $by){ echo "my_m_m_link"; }
                    echo " ";
                    if ($session != $by) { echo "not_m_m_link"; }
                    echo "' target='_blank'>". nl2br(trim($data)) ."</a>";
                  } else {
                    echo nl2br(trim($data));
                  }
                } else if($type == "image") {
                  if($by == $session){ $m = "You"; } else { $m = $universal->GETsDetails($by, "username"); }
                  echo "<img src='{$this->DIR}/message/Instagram_{$data}' class='m_m_img' data-imgby='{$m}' data-time='{$Time->timeAgo($time)}'>";
                } else if ($type == "sticker") {
                  echo "<img src='{$this->DIR}/message/Instagram_{$data}' class='m_m_sticker'>";
                }
                echo "</div>
                <span class='m_m_time'>{$Time->timeAgo($time)}</span>
                <div class='m_m_tools'>";
                if ($by == $session) {
                  echo "<span class='m_m_status' data-description='Sent'><i class='material-icons'>check</i></span>";
                  echo "<span class='m_m_dlt' data-description='Unsend'><i class='material-icons'>delete</i></span>";
                  if($type == "text"){
                    echo "<span class='m_m_edit' data-description='Edit'><i class='material-icons'>mode_edit</i></span>";
                  }
                }
                echo "</div></div>";

            } else if ($type == "name_change") {
              echo "<div class='m_m_divs m_m_info_div'><span class='mssg_info' title='{$Time->timeAgo($time)}'>";
              if ($by == $session) {
                echo "You changed conversation name to <span class='m_m_name_change'>{$data}</span>";
              } else if ($by != $session) {
                echo "<a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='m_m_name_change'>{$universal->GETsDetails($by, "username")}</a> changed conversation name to "."<span class='m_m_name_change'>{$data}</span>";
              }
              echo "</span></div>";

            } else if ($type == "avatar_change") {
              echo "<div class='m_m_divs m_m_info_div'><span class='mssg_info' title='{$Time->timeAgo($time)}'>";
              if ($by == $session) {
                echo "You changed the group avatar";
              } else if ($by != $session) {
                echo "<a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='m_m_name_change'>{$universal->GETsDetails($by, "username")}</a> changed the group avatar";
              }
              echo "</span></div>";

            } else if ($type == "member_add") {
              echo "<div class='m_m_divs m_m_info_div'><span class='mssg_info' title='{$Time->timeAgo($time)}'>";
              if ($by == $session) {
                echo "You added <a href='{$this->DIR}/profile/{$universal->GETsDetails($data, "username")}' class='m_m_name_change'>{$universal->GETsDetails($data, "username")}</a> to group";
              } else if ($by != $session) {
                echo "<a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='m_m_name_change'>{$universal->GETsDetails($by, "username")}</a> added <a href='{$this->DIR}/profile/{$universal->GETsDetails($data, "username")}' class='m_m_name_change'>";
                if($data == $session){ echo "you"; } else { echo $universal->GETsDetails($data, "username"); }
                echo "</a> to group";
              }
              echo "</span></div>";

            } else if ($type == "leave_grp_con") {
              echo "<div class='m_m_divs m_m_info_div'><span class='mssg_info' title='{$Time->timeAgo($time)}'>";
              if ($by == $session) {
                echo "You left the group";
              } else if ($by != $session) {
                echo "<a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='m_m_name_change'>{$universal->GETsDetails($by, "username")}</a> left the group";
              }
              echo "</span></div>";

            } else if ($type == "removed_grp_con") {
              echo "<div class='m_m_divs m_m_info_div'><span class='mssg_info' title='{$Time->timeAgo($time)}'>";
              if ($by == $session) {
                echo "You were remved from the group";
              } else if ($by != $session) {
                echo "<a href='{$this->DIR}/profile/{$universal->GETsDetails($by, "username")}' class='m_m_name_change'>{$universal->GETsDetails($by, "username")}</a> removed from the group";
              }
              echo "</span></div>";

            } else if ($type == "admin_change") {
              echo "<div class='m_m_divs m_m_info_div'><span class='mssg_info' title='{$Time->timeAgo($time)}'>";
              if ($data == $session) {
                echo "You were made the group admin";
              } else if ($data != $session) {
                echo "<a href='{$this->DIR}/profile/{$universal->GETsDetails($data, "username")}' class='m_m_name_change'>{$universal->GETsDetails($data, "username")}</a> was made the group admin";
              }
              echo "</span></div>";
            }

          }

          echo "<span class='mssg_helper'></span></div></div>";
          echo "<div class='m_m_slider'></div>";
          echo "<div class='m_m_bottom' data-grp_conid='{$grp}'><form class='add_mssg_form' action='' method='post'>";
          echo "<div class='send_mssg_before'>Sending message..</div>";
      // <input type='text' name='' value='' placeholder='Send message' class='send_mssg' spellcheck='false'>
      echo "<textarea name='' value='' placeholder='Send message..' class='send_mssg' spellcheck='false'></textarea>";
      echo "<span class='mssg_emoji_btn'><i class='material-icons'>sentiment_very_satisfied</i></span>
      <input type='submit' name='' value='Send' class='pri_btn mssg_send'>
      </form></div></div>";
    }

    public function leaveGrpCon($grp, $user, $when){
      $session = $_SESSION['id'];

      if ($when == "leave") {
        $insert = $session;
        $type = "leave_grp_con";
      } else if ($when == "remove") {
        $insert = $user;
        $type = "removed_grp_con";
      }

      $r = $this->db->prepare("INSERT INTO message(grp_con_id, mssg_by, message, type, time, status) VALUES (:con, :by, :mssg, :type, now(), :status)");
      $r->execute(array(":con" => $grp, ":by" => $insert, ":mssg" => "", ":type" => $type, ":status" => "unread"));
      $i = $this->db->lastInsertId();
      self::insertGrpUnreads($grp, $i);

      $query = $this->db->prepare("DELETE FROM group_con_members WHERE grp_con_id = :grp AND members = :mem");
      $query->execute(array(":grp" => $grp, ":mem" => $user));

      $q = $this->db->prepare("DELETE FROM grpconunreads WHERE member = :id");
      $q->execute(array(":id" => $user));

    }

    public function changeGrpConAvatar($file, $grp){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT avatar FROM group_con WHERE grp_con_id = :grp LIMIT 1");
      $query->execute(array(":grp" => $grp));

      // $av = self::getGrpCon($grp, "avatar");
      // if ($av != "") {


      if ($query->rowCount() > 0) {
        $row = $query->fetch(PDO::FETCH_OBJ);
        $av = $row->avatar;

        $name = $file['name'];
        $size = $file['size'];
        $tmp_name = $file['tmp_name'];
        $error = $file['error'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = array("jpg", "png", "gif", "jpeg");
        $new_name = time().".".$ext;

        if ($av != "") {
          unlink("../../grp_mssg_avatar/Instagram_$av");
        }

        $q = $this->db->prepare("UPDATE group_con SET avatar = :avatar WHERE grp_con_id = :grp");
        $q->execute(array(":avatar" => $new_name, ":grp" => $grp));

        if (in_array($ext, $allowed)) {
          if ($error == 0) {
            move_uploaded_file($tmp_name, "../../grp_mssg_avatar/Instagram_".$new_name);
          }
        }

        $r = $this->db->prepare("INSERT INTO message(grp_con_id, mssg_by, message, type, time, status) VALUES (:con, :by, :mssg, :type, now(), :status)");
        $r->execute(array(":con" => $grp, ":by" => $session, ":mssg" => "", ":type" => "avatar_change", ":status" => "unread"));
        $i = $this->db->lastInsertId();
        self::insertGrpUnreads($grp, $i);
        return $new_name;

      }
    // }
    }

    public function grpConMemOrNot($grp, $member){
      $query = $this->db->prepare("SELECT members FROM group_con_members WHERE grp_con_id = :grp AND members = :mem");
      $query->execute(array(":grp" => $grp, ":mem" => $member));
      if ($query->rowCount() > 0) {
        return true;
      } else if ($query->rowCount() == 0) {
        return false;
      }
    }

    public function getGrpConMembers($value, $grp){
      $session = $_SESSION['id'];

      include 'universal.class.php';
      include 'avatar.class.php';
      include 'settings.class.php';

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
          if (self::grpConMemOrNot($grp, $id) == false && $settings->AmIBlocked($id) == false) {
            echo "<li class='grp_to_select_u' data-user='{$id}' data-name='{$username}'><img src='{$this->DIR}/{$avatar->DisplayAvatar($id)}' alt=''><span>{$universal->nameShortener($universal->GETsDetails($id, "username"), 25)}</span></li>";
          }
        }
      }
    }

    public function addGrpConMembers($user, $grp){
      $session = $_SESSION['id'];

      include 'notifications.class.php';
      $noti = new notifications;

      $query = $this->db->prepare("INSERT INTO group_con_members(grp_con_id, members) VALUES (:grp, :mem)");
      $query->execute(array(":grp" => $grp, ":mem" => $user));

      $r = $this->db->prepare("INSERT INTO message(grp_con_id, mssg_by, message, type, time, status) VALUES (:con, :by, :mssg, :type, now(), :status)");
      $r->execute(array(":con" => $grp, ":by" => $session, ":mssg" => $user, ":type" => "member_add", ":status" => "unread"));
      $i = $this->db->lastInsertId();
      self::insertGrpUnreads($grp, $i);
      $name = self::getGrpCon($grp, "name");
      $noti->actionNotify($user, $grp, "grp_con");
    }

    public function selectForGrpConAdmin($grp){
      $session = $_SESSION['id'];

      include 'universal.class.php';
      include 'avatar.class.php';

      $universal = new universal;
      $avatar = new Avatar;

      $query = $this->db->prepare("SELECT members FROM group_con_members WHERE grp_con_id = :grp AND members <> :me ORDER BY grp_con_mem_id DESC");
      $query->execute(array(":grp" => $grp, ":me" => $session));
      $count = $query->rowCount();
      if ($count == 0) {
        echo "<div class='no_display'><img src='{$this->DIR}/images/needs/large.jpg'></div>";
      } else if ($count > 0) {
        echo "<input type='hidden' class='share_postid'>";
        echo "<input type='hidden' class='share_userid'>";
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $userid = $row->members;
          if (self::getGrpCon($grp, "admin") != $userid) {
            echo "<div class='display_items select_receiver";
            echo "' data-userid='{$userid}'><div class='d_i_img'>
            <img src='{$this->DIR}/". $avatar->DisplayAvatar($userid) ."' alt='profile'></div><div class='d_i_content'><div class='d_i_info'>
            <span class='d_i_username username'>". $universal->nameShortener($universal->GETsDetails($userid, "username"), 12) ."</span>
            <span class='d_i_name'>". $universal->nameShortener($universal->GETsDetails($userid, "firstname")." ". $universal->GETsDetails($userid, "surname"), 15) ."</span></div></div></div>";
          }

        }
      }
    }

    public function changeGrpConAdmin($user, $grp){
      include 'notifications.class.php';
      $session = $_SESSION['id'];
      $noti = new notifications;
      if (self::getGrpCon($grp, "admin")  != $user) {
        $query = $this->db->prepare("UPDATE group_con SET admin = :user WHERE grp_con_id = :grp AND admin = :me");
        $query->execute(array(":user" => $user, ":grp" => $grp, ":me" => $session));
        $noti->actionNotify($user, $grp, "changeGrpConAdmin");

        $r = $this->db->prepare("INSERT INTO message(grp_con_id, mssg_by, message, type, time, status) VALUES (:con, :by, :mssg, :type, now(), :status)");
        $r->execute(array(":con" => $grp, ":by" => $session, ":mssg" => $user, ":type" => "admin_change", ":status" => "unread"));
        $i = $this->db->lastInsertId();
        self::insertGrpUnreads($grp, $i);

        return "ok";
      }
    }

  }

?>
