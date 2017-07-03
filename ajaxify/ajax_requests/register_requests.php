<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    if (isset($_POST['username']) && isset($_POST['firstname']) && isset($_POST['surname']) &&
    isset($_POST['email']) && isset($_POST['password']) && isset($_POST['terms'])) {

      include_once '../../config/class/needy_class.php'; 
      include '../../config/class/login.class.php';
      include '../../config/class/random.class.php';

      $login = new login_class;
      $random = new random;

      $ip_add = $random->getIP();

      $username = trim(preg_replace("#[<> ]#i", '', $_POST['username']));
      $firstname = trim(preg_replace("#[<> ]#i", '', $_POST['firstname']));
      $surname = trim(preg_replace("#[<> ]#i", '', $_POST['surname']));
      $emai = trim(preg_replace("#[<> ]#i", '', $_POST['email']));
      $email = trim(preg_replace("#(https:\/\/|http:/\/\|www.)#i", "", $emai));
      $password = trim($_POST['password']);
      $pri_ip = trim(preg_replace("#[^0-9.]#", "", $ip_add));

      echo $login->REGISTER($username, $firstname, $surname, $email, $password, $pri_ip);

    }

  }
?>
