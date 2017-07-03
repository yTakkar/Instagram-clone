<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include_once '../../config/class/needy_class.php';
    // include '../../config/class/notifications.class.php';
    include '../../config/classesGetter.php';
    $noti = new notifications;

    if (isset($_GET['getUnread'])) {
      $unread = $noti->unreadCount();
      $array = array("unread" => $unread);
      echo json_encode($array);
    }

    if (isset($_GET['clearAll'])) {
      $noti->clearNotifications();
    }

    if (isset($_GET['notiFeeds'])) {
      $noti->getNotifications("ajax", $_GET['notiFeeds']);
    }

  }
?>
