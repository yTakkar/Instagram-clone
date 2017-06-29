<?php
  class login{

    protected $db;
    protected $e;

    public function __construct(){
      try {
        $db = new PDO('mysql:host=host;dbname=instagram;charset=utf8mb4', 'user', 'password');
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $ee = $this->e;
      } catch (PDOException $ee) {
        echo $ee->getMessage();
      }
    }

    public function LOGIN($username, $password, $ip){
      if (($username || $password) == "") {
        return "Your values are missing";
      } else {
        $query = $this->db->prepare("SELECT password FROM users WHERE username = :username LIMIT 1");
        $query->execute(array(":username" => $username));
        if ($query->rowCount() == 0) {
          return "Incorrect details";
        } else {
          $row = $query->fetch(PDO::FETCH_OBJ);
          $pass = $row->password;
          if (password_verify($password, $pass)) {
            $iquery = $this->db->prepare("SELECT id FROM users WHERE username = :username AND password = :password LIMIT 1");
            $iquery->execute(array(":username" => $username, ":password" => $pass));
            $irow = $iquery->fetch(PDO::FETCH_OBJ);
            $id = $irow->id;
            $random = new random;
            $os = $random->getOS();
            $browser = $random->getBrowser();
            $lquery = $this->db->prepare("INSERT INTO login(user_id, ip, time, os, browser) VALUES(:id, :ip, now(), :os, :browser)");
            $lquery->execute(array(":id" => $id, ":ip" => $ip, ":os" => $os, ":browser" => $browser));
            $_SESSION['id'] = $id;
            return "Successfull";
          } else {
            return "Incorrect password";
          }
        }
      }
    }

    public function LOGOUT(){
      $id = $_SESSION['id'];

      $query = $this->db->prepare("SELECT MAX(login_id) AS get FROM login WHERE user_id = :id LIMIT 1");
      $query->execute(array(":id" => $id));
      $row = $query->fetch(PDO::FETCH_OBJ);
      $login_id = $row->get;

      $mquery = $this->db->prepare("UPDATE login SET logout = now() WHERE login_id = :id");
      $mquery->execute(array(":id" => $login_id));
      session_destroy();
      header("Location: login");
    }

    public function REGISTER($username, $firstname, $surname, $email, $password, $pri_ip){
      include 'settings.class.php';
      include 'universal.class.php';
      include 'PHPMailerAutoload.php';

      $mail = new PHPMailer;
      $settings = new settings;
      $universal = new universal;

      $password_h = password_hash($password, PASSWORD_DEFAULT);

      $fquery = $this->db->prepare("SELECT id FROM users WHERE username = :username");
      $fquery->execute(array(":username" => $username));
      $squery = $this->db->prepare("SELECT id FROM users WHERE email = :email");
      $squery->execute(array(":email" => $email));

      if (($username || $firstname || $surname || $email || $password) == "") {
        return "Some values are missing";
      } else if (!filter_var($email, FILTER_VALIDATE_EMAIL) === true) {
        return "Incorrect email";
      } else if ($fquery->rowCount() > 0) {
        return "username already exists";
      } else if ($squery->rowCount() > 0) {
        return "Email already exists";
      } else {

        $random = new random;
        $os = $random->getOS();
        $browser = $random->getBrowser();

        $query = $this->db->prepare("INSERT INTO users(username, firstname, surname, email, password, signup, last_login, pri_ip, pri_os, pri_browser) VALUES(:username, :firstname, :surname, :email, :password, now(), now(), :ip, :os, :browser)");
        $query->execute(array(":username" => $username, ":firstname" => $firstname, ":surname" => $surname, ":email" => $email, ":password" => $password_h, ":ip" => $pri_ip, ":os" => $os, ":browser" => $browser));

        $uid = $this->db->lastInsertId();

        $e = $universal->GETsDetails($uid, "email");
        if (strrpos($email, "@gmail.com")) {
          $email = "www.".$e;
        } else {
          $email = $e;
        }

        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'YOUR_GMAIL';                 // SMTP username
        $mail->Password = 'GMAIL_PASSWORD';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        $mail->setFrom('YOUR_GMAIL', 'Team Instagram');
        $mail->addAddress($email);               // Name is optional
        $mail->addReplyTo('YOUR_GMAIL', 'Team Instagram');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        $mail->addCC('YOUR_GMAIL');
        $mail->addBCC('YOUR_GMAIL');

        $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Activate your Instagram account';

        $mail->Body = "<span>Hello, You received this message because you created an account on INSTAGRAM.<span><br>
        <span>Click on button below to activate your Instagram account and explore.</span><br><br>
        <a href='http://localhost/faiyaz/Instagram/ajaxify/deep/most/topmost/activate.php?id={$uid}' style='border: 1px solid #1b9be9; font-weight: 600; color: #fff; border-radius: 3px; cursor: pointer; outline: none; background: #1b9be9; padding: 4px 15px; display: inline-block; text-decoration: none;'>Activate</a>";

        // $mail->AltBody = "<span>Hello, You're receiving this message because you created an account on INSTAGRAM.<span><br>
        // <span>Click on button below to activate your Instagram account and explore.</span><br><br>
        // <a href='http://localhosthttps://gypsum.000webhostapp.com/activate?id={$uid}' style='border: 1px solid #1b9be9; font-weight: 600; color: #fff; border-radius: 3px; cursor: pointer; outline: none; background: #1b9be9; padding: 4px 15px; display: inline-block; text-decoration: none;'>Activate</a>";

        if($mail->send()) {
          return 'Successfull';
        }

      }

    }

    public function usernameChecker($value){
      $query = $this->db->prepare("SELECT id FROM users WHERE username = :username");
      $query->execute(array(":username" => $value));
      $count = $query->rowCount();
      if ($count == 0) {
        echo "<span class='checker_text'>username is available</span><span class='checker_icon'>
          <i class='fa fa-smile-o' aria-hidden='true'></i></span>";
      } else if ($count > 0) {
        echo "<span class='checker_text'>username already taken</span><span class='checker_icon'>
          <i class='fa fa-frown-o' aria-hidden='true'></i></span>";
      }
    }

    public function activateAccount($id){
      $settings = new settings;

      if (!file_exists("$id")) {
        mkdir("users/$id", 0755);
        mkdir("users/$id/avatar", 0755);
      }
      $avatar = "images/avatars/spacecraft.jpg";
      $dest = "users/$id/avatar/spacecraft.jpg";
      copy($avatar, $dest);

      $settings->settingsDefaults($id);

      $query = $this->db->prepare("UPDATE users SET email_activated = :act WHERE id = :user");
      $query->execute(array(":act" => "yes", ":user" => $id));
    }

  }
?>
