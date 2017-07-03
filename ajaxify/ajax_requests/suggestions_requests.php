<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    include_once '../../config/class/needy_class.php';
    include '../../config/class/suggestions.class.php';
    $suggestions = new suggestion;

    if (isset($_GET['Homerefresh']) == "yes") {
      include '../../config/class/follow_system.class.php';
      include '../../config/class/universal.class.php';
      include '../../config/class/avatar.class.php';
      include '../../config/class/mutual.class.php';
      $suggestions->HomeSuggestions("ajax");
    }

  }
?>
