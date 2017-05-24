<?php
  class forgot{

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

    public function retrieve($input){
      include 'random.class.php';
      include 'universal.class.php';
      include 'PHPMailerAutoload.php';

      $random = new random;
      $universal = new universal;
      $mail = new PHPMailer;

      $os = $random->getOS();
      $browser = $random->getBrowser();

      $ip_add;
      if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip_add = $_SERVER['HTTP_CLIENT_IP'];
      } else if (!empty($_SERVER['HTTP_X_FORWADED_FOR'])) {
        $ip_add = $_SERVER['HTTP_X_FORWADED_FOR'];
      } else {
        $ip_add = $_SERVER['REMOTE_ADDR'];
      }
      $text = preg_replace("#[<>]#i", "", $input);

      $second = $this->db->prepare("SELECT email FROM users WHERE email = :email");
      $second->execute(array(":email" => $text));
      $scount = $second->rowCount();
      if ($scount == 0) {
        echo "No such user exists";
      } else if ($scount == 1) {
        $sget = $this->db->prepare("SELECT id, username FROM users WHERE email = :email LIMIT 1");
        $sget->execute(array(":email" => $text));
        $srow = $sget->fetch(PDO::FETCH_OBJ);
        $uid = $srow->id;

        $e = $universal->GETsDetails($uid, "email");
        if (strrpos($text, "@gmail.com")) {
          $email = "www.".$e;
        } else {
          $email = $e;
        }

        // $url = 'https://gypsum.000webhostapp.com/activate?id='.$uid;

        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'teaminstagramme@gmail.com';                 // SMTP username
        $mail->Password = 'iamfaiyaz786';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        $mail->setFrom('teaminstagramme@gmail.com', 'Team Instagram');
        $mail->addAddress($email);               // Name is optional
        $mail->addReplyTo('teaminstagramme@gmail.com', 'Team Instagram');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        $mail->addCC('teaminstagramme@gmail.com');
        $mail->addBCC('teaminstagramme@gmail.com');

        $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Activate your Instagram account';

        $mail->Body = "<span>Hello, You received this message because you created an account on INSTAGRAM.<span><br>
        <span>Click on button below to retrieve your Instagram account and explore.</span><br><br>
        <a href='http://localhost/faiyaz/Instagram/ajaxify/deep/most/topmost/retrieve.php?id={$uid}' style='border: 1px solid #1b9be9; font-weight: 600; color: #fff; border-radius: 3px; cursor: pointer; outline: none; background: #1b9be9; padding: 4px 15px; display: inline-block; text-decoration: none;'>Retrieve</a>";

        // $mail->AltBody = "<span>Hello, You're receiving this message because you created an account on INSTAGRAM.<span><br>
        // <span>Click on button below to activate your Instagram account and explore.</span><br><br>
        // <a href='http://localhosthttps://gypsum.000webhostapp.com/activate?id={$uid}' style='border: 1px solid #1b9be9; font-weight: 600; color: #fff; border-radius: 3px; cursor: pointer; outline: none; background: #1b9be9; padding: 4px 15px; display: inline-block; text-decoration: none;'>Activate</a>";

        if($mail->send()) {
          return 'ok';
        }

        // $suser = $srow->username;
        // $_SESSION['id'] = $sid;
        // $sinsert = $this->db->prepare("INSERT INTO login (user_id, ip, time, os, browser) VALUES (:id, :ip, now(), :os, :browser)");
        // $sinsert->execute(array(":id" => $uid, ":ip" => $ip_add, ":os" => $os, ":browser" => $browser));
        // echo "ok".$suser;

      }
  }

  }
?>
