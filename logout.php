<?php
  include 'config/declare.php';
  include_once 'config/class/needy_class.php';
  include 'config/class/universal.class.php';
  include 'config/class/login.class.php';
?>

<?php
  $universal = new universal;
  $login = new login_class;
?>

<?php
  if(isset($_SESSION['id'])){

    if (!isset($_COOKIE['ids'])) {
      setcookie("ids", json_encode(array($_SESSION['id'])), time()+30*24*60*60);

    } else if (isset($_COOKIE['ids'])) {

      $arr = array();
      $ids = json_decode($_COOKIE['ids']);

      foreach ($ids as $value) {
        $arr[] = $value;
      }

      array_push($arr, $_SESSION['id']);
      setcookie("ids", json_encode(array_unique($arr)), time()+30*24*60*60);

    }

    // setcookie("ids", null, time()-30*24*60*60);

  }
?>

<?php
  if ($universal->isLoggedIn()) {
    $login->LOGOUT();
  } else if ($universal->isLoggedIn() == false) {
    header('Location: welcome');
  }
?>
