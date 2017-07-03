<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include_once '../../config/class/needy_class.php';
    include '../../config/class/fav.class.php';
    $fav = new favourite;

    if (isset($_GET['userFav'])) {
      $fav->addUserFav($_GET['userFav']);
    }

    if (isset($_GET['remFav'])) {
      $rem = $fav->remFav($_GET['remFav']);
      $rem;
      $count = $fav->noOfFavs($_GET['getId']);
      $array = array("mssg" => $rem, "count" => $count);
      echo json_encode($array);
    }

  }

?>
