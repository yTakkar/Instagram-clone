<?php
  include '../../../../config/declare.php';
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Activating your account</title>
    <link rel="shortcut icon" href="../../../../images/favicon/favicon.png" type="image/png">
  </head>

<?php

  include_once '../../../../config/class/needy_class.php';
  include '../../../../config/class/universal.class.php';
  include '../../../../config/class/login.class.php';
  include '../../../../config/class/settings.class.php';

  $universal = new universal;
  $login = new login_class;
  $settings = new settings;

  if (!isset($_GET['id']) || $universal->isLoggedIn()) {
    header("Location: ".DIR);
  }

  if ($universal->GETsDetails($_GET['id'], "email_activated") == "no" || isset($_GET['id']) != isset($_SESSION['id'])) {

    $db = N::_DB();

    $id = $_GET['id'];
    if (!file_exists("../../../../users/$id")) {
      mkdir("../../../../users/$id", 0755);
      mkdir("../../../../users/$id/avatar", 0755);
    }
    $avatar = "../../../../images/avatars/spacecraft.jpg";
    $dest = "../../../../users/$id/avatar/spacecraft.jpg";
    copy($avatar, $dest);

    $settings->settingsDefaults($id);

    $query = $db->prepare("UPDATE users SET email_activated = :act WHERE id = :user");
    $query->execute(array(":act" => "yes", ":user" => $id));

    $_SESSION['id'] = $id;
    $username = $universal->GETsDetails($id, "username");
    header("Location: ". DIR ."/profile/{$username}");

  } else {
    header("Location: ".DIR);
  }

  include '../../../../index_include/index_footer.php';

?>
