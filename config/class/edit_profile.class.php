<?php
  class editProfile{

    protected $db;
    protected $DIR;
    protected $gmail;
    protected $gmail_password;

    public function __construct(){
      $db = N::_DB();
      $DIR = N::$DIR;
      $GMAIL = N::$GMAIL;
      $GMAIL_PASS = N::$GMAIL_PASSWORD;

      $this->db = $db;
      $this->DIR = $DIR;
      $this->gmail = $GMAIL;
      $this->gmail_password = $GMAIL_PASS;
    }

    public function saveProfileEditing($username, $firstname, $surname, $bio, $instagram, $youtube, $facebook, $twitter, $website, $mobile, $tags){
      $session = $_SESSION['id'];
      $my = array();
      $universal = new universal;
      $susername = $universal->GETsDetails($session, "username");

      if((!$username || $firstname || $surname ) == ""){
        return "Some values are missing!";
      } else {
        $uCount = $this->db->prepare('SELECT id FROM users WHERE username = :username');
        $uCount->execute(array(":username" => $username));
        
        if($uCount->rowCount() == 1 && $username != $susername){
          return "Username already exists!!";
        } else {
          
          $users = $this->db->prepare("UPDATE users SET username = :username, firstname = :firstname, surname = :surname, bio = :bio, instagram = :instagram, youtube = :youtube, facebook = :facebook, twitter = :twitter, website = :website, mobile = :mobile WHERE id = :id");
          $users->execute(array(":username" => $username, ":firstname" => $firstname, ":surname" => $surname, ":bio" => $bio, ":instagram" => $instagram, ":youtube" => $youtube, ":facebook" => $facebook, ":twitter" => $twitter, ":website" => $website, ":mobile" => $mobile, ":id" => $session));
          $array = explode(",", $tags);
          foreach ($array as $value) {
            $my[$value] = trim($value);
          }
          $delete = $this->db->prepare("DELETE FROM tags WHERE user_id = :id");
          $delete->execute(array(":id" => $session));
          foreach ($my as $key => $value) {
            $insert = $this->db->prepare("INSERT INTO tags(user_id, tags) VALUES (:session, :each)");
            $insert->execute(array(":session" => $session, ":each" => $value));
          }

          $q = $this->db->prepare("UPDATE follow_system SET follow_by_u = :new WHERE follow_by = :me");
          $q->execute(array(":new" => $username, ":me" => $session));

          $r = $this->db->prepare("UPDATE follow_system SET follow_to_u = :new WHERE follow_to = :me");
          $r->execute(array(":new" => $username, ":me" => $session));

          return "Profile updated!!";

        }

      }

    }

    public function resend_vl(){
      include 'PHPMailerAutoload.php';

      $universal = new universal;
      $mail = new PHPMailer;
      $id = $_SESSION['id'];

      $email = $universal->GETsDetails($id, "email");
      $url = $universal->urlChecker($this->DIR);

      // $mail->SMTPDebug = 3;                               // Enable verbose debug output
      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = $this->gmail;                 // SMTP username
      $mail->Password = $this->gmail_password;                           // SMTP password
      $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 587;                                    // TCP port to connect to

      $mail->From = $this->gmail;
      $mail->FromName = "Team Instagram";
      $mail->addAddress($email);               // Name is optional
      $mail->addReplyTo($this->gmail, 'Team Instagram');
      // $mail->addCC('cc@example.com');
      // $mail->addBCC('bcc@example.com');

      $mail->addCC($this->gmail);
      $mail->addBCC($this->gmail);

      $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
      $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
      $mail->isHTML(true);                                  // Set email format to HTML

      $mail->Subject = 'Verify your Instagram account';

      $mail->Body = "<span>Hello, You received this message because you created an account on INSTAGRAM.<span><br>
      <span>Click on button below to verify your Instagram account and explore.</span><br><br>
      <a href='{$url}/ajaxify/deep/most/topmost/activate.php?id={$id}' style='border: 1px solid #1b9be9; font-weight: 600; color: #fff; border-radius: 3px; cursor: pointer; outline: none; background: #1b9be9; padding: 4px 15px; display: inline-block; text-decoration: none;'>Activate</a>";

      if($mail->send()){
        return "Verification link sent!!";
      } else {
        return "Error sending verification link!!";
      }

    }

  }
?>
