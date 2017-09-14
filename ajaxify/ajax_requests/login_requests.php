<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    include_once '../../config/class/needy_class.php';
    include '../../config/class/login.class.php';
    include '../../config/class/random.class.php';

    $login = new login_class;
    $random = new random;

    $ip_add = $random->getIP();

    if (isset($_POST['username']) && isset($_POST['password'])) {

      $username = trim(preg_replace("#[^a-z0-9_@.\-]#i", '', $_POST['username']));
      $password = trim($_POST['password']);
      $ip = trim(preg_replace("#[^0-9]#", "", $ip_add));

      $a = $login->LOGIN($username, $password, $ip);
      $a;
      $farray = array("mssg" => $a);
      echo json_encode($farray);

    }

  }
?>
