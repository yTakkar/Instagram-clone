<?php
  class settings{

    protected $db;
    protected $DIR;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;

      $this->db = $db;
      $this->DIR = $DIR;
    }

    public function settingsDefaults($id){
      $hint = "public";

      $fquery = $this->db->prepare("INSERT INTO email_private(user_id, options) VALUES(:id, :options)");
      $fquery->execute(array(":id" => $id, ":options" => $hint));

      $fquery = $this->db->prepare("INSERT INTO mobile_private(user_id, options) VALUES(:id, :options)");
      $fquery->execute(array(":id" => $id, ":options" => $hint));
    }

    public function changePassword($cur__, $new__, $new_a__){
      $cur_ = trim(preg_replace("#[<>]#i", "", $cur__));
      $new_ = trim(preg_replace("#[<>]#i", "", $new__));
      $new_a_ = trim(preg_replace("#[<>]#i", "", $new_a__));
      $session = $_SESSION['id'];

      $cur = password_hash($cur_, PASSWORD_DEFAULT);
      $new = password_hash($new_, PASSWORD_DEFAULT);
      $new_a = password_hash($new_a_, PASSWORD_DEFAULT);

      $query = $this->db->prepare("SELECT password FROM users WHERE id = :id LIMIT 1");
      $query->execute(array(":id" => $session));
      $row = $query->fetch(PDO::FETCH_OBJ);
      $password = $row->password;
      // return $password;

      if (!password_verify($cur_, $password)) {
        return "Incorrect password";
      } else if($new_ != $new_a_) {
        return "New passwords don't match";
      } else {
        $mquery = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        $mquery->execute(array(":password" => $new_a, ":id" => $session));
        return "Password changed";
      }

    }

    public function changeAccountType($value){
      include 'universal.class.php';
      $universal = new universal;

      $session = $_SESSION['id'];
      $query = $this->db->prepare("UPDATE users SET type = :type WHERE id = :id");
      $query->execute(array(":type" => $value, ":id" => $session));
      return $universal->GETsDetails($session, "type");
    }

    public function block($user){
      $session = $_SESSION['id'];
      if (self::isBlocked($user) == false) {
        include 'universal.class.php';
        $universal = new universal;

        $query = $this->db->prepare("INSERT INTO block(block_by, block_to, time) VALUES (:by, :to, now())");
        $query->execute(array(":by" => $session, ":to" => $user));

        $q = $this->db->prepare("DELETE FROM follow_system WHERE follow_by = :user AND follow_to = :me");
        $q->execute(array(":user" => $user, ":me" => $session));

        echo $universal->GETsDetails($user, "username");
      }
    }

    public function isBlocked($user){
      if (isset($_SESSION['id'])) {
        $session = $_SESSION['id'];
        $query = $this->db->prepare("SELECT block_id FROM block WHERE block_by = :by AND block_to = :to");
        $query->execute(array(":by" => $session, ":to" => $user));
        $count = $query->rowCount();
        if ($count == 0) {
          return false;
        } else if($count > 0) {
          return true;
        }
      }
    }

    public function AmIBlocked($user){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("SELECT block_id FROM block WHERE block_by = :by AND block_to = :to");
      $query->execute(array(":by" => $user, ":to" => $session));
      $count = $query->rowCount();
      if ($count == 0) {
        return false;
      } else if($count > 0){
        return true;
      }
    }

    public function unblock($id){
      $session = $_SESSION['id'];

      include 'universal.class.php';
      $universal = new universal;

      $query = $this->db->prepare("DELETE FROM block WHERE block_by = :by AND block_to = :to");
      $query->execute(array(":by" => $session, ":to" => $id));
      echo $universal->GETsDetails($id, "username");
    }

    public function blockedUsers(){
      $session = $_SESSION['id'];

      $universal = new universal;
      $avatar = new Avatar;
      $mutual = new mutual;

      $query = $this->db->prepare("SELECT * FROM block WHERE block_by = :by");
      $query->execute(array(":by" => $session));
      if ($query->rowCount() == 0) {
        echo "<div class='home_last_mssg pro_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'>
        <span>No blocked members</span></div>";
      } else if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $id = $row->block_id;
          $to = $row->block_to;
          $time = $row->time;

          echo "<div class='blocked_users' data-blockid='{$to}'><img src='{$this->DIR}/{$avatar->DisplayAvatar($to)}' alt=''>
            <div class='blocked_u_content'><div class='blocked_info'>
                <a href='{$this->DIR}/profile/{$universal->GETsDetails($to, "username")}' class='blocked_username'>{$universal->GETsDetails($to, 'username')}</a>
                <span class='blocked_mutual'>{$mutual->eMutual($to)}</span></div>
              <a href='#' class='unblock sec_btn'>Unblock</a></div></div>";

        }
      }

    }

    public function emailPrivacy($user){
      $query = $this->db->prepare("SELECT options FROM email_private WHERE user_id = :user LIMIT 1");
      $query->execute(array(":user" => $user));
      if ($query->rowCount() == 1) {
        $row = $query->fetch(PDO::FETCH_OBJ);
        return $row->options;
      }
    }

    public function mobilePrivacy($user){
      $query = $this->db->prepare("SELECT options FROM mobile_private WHERE user_id = :user LIMIT 1");
      $query->execute(array(":user" => $user));
      if ($query->rowCount() == 1) {
        $row = $query->fetch(PDO::FETCH_OBJ);
        return $row->options;
      }
    }

    public function changeEmailPrivacy($hint){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("UPDATE email_private SET options = :options WHERE user_id = :user");
      $query->execute(array(":options" => $hint, ":user" => $session));
    }

    public function changeMobilePrivacy($hint){
      $session = $_SESSION['id'];
      $query = $this->db->prepare("UPDATE mobile_private SET options = :options WHERE user_id = :user");
      $query->execute(array(":options" => $hint, ":user" => $session));
    }

    public function loginDetails(){
      $session = $_SESSION['id'];

      $query = $this->db->prepare("SELECT * FROM login WHERE user_id = :me ORDER BY login_id DESC");
      $query->execute(array(":me" => $session));
      if ($query->rowCount() == 0) {
        echo "<div class='home_last_mssg login_det_last_mssg'><img src='{$this->DIR}/images/needs/large.jpg'>
        <span>You got no login details</span></div>";
      } else {

        echo "<table>

        <tr>
          <td class='td_bold'>Login</td>
          <td class='td_bold'>OS</td>
          <td class='td_bold'>Browser</td>
          <td class='td_bold'>IP</td>
          <td class='td_bold'>Logout</td>
        </tr>";

        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
          $login = $row->time;
          $ip = $row->ip;
          $os = $row->os;
          $browser = $row->browser;
          $logout = $row->logout;

          echo "<tr>
            <td>{$login}</td>
            <td>{$os}</td>
            <td>{$browser}</td>
            <td>{$ip}</td>
            <td>";
              if(substr($logout, 0, 4) == "0000"){ echo 'By the browser'; } else { echo $logout; }
              "</td>
          </tr>";

        }

        echo "</table>";

      }

    }

  }
?>
