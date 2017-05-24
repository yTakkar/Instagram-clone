<?php include 'config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include 'config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $universal = new universal;
  $avatar = new Avatar;
  $follow = new follow_system;
  $Post = new post;
  $Time = new time;
  $noti = new notifications;
  $message = new message;
?>

<?php
  if ($universal->isLoggedIn() == false) {
    header('Location:'. DIR .'/welcome');
  }
  $session = $_SESSION['id'];
?>

<?php
  $title = "Notifications • Instagram";
  $keywords = "Instagram, Share and capture world's moments, share, capture, Notifications";
  $desc = "Instagram lets you capture, follow, like and share world's moments in a better way and tell your story with photos, messages, posts and everything in between";
?>

<!-- including files for header of document -->
<?php include_once 'includes/header.php'; ?>
<?php include_once 'needs/heading.php'; ?>
<?php include_once 'needs/nav.php'; ?>

<div class="user_info" data-userid="<?php echo $session; ?>" data-sessionid="<?php echo $session; ?>" data-username="<?php echo $universal->getUsernameFromSession(); ?>"></div>

<!-- TO MARK NOTIFICATIONS AS READ -->
<?php $noti->markRead(); ?>

<div class="overlay"></div>
<div class="notify"><span></span></div>
<div class="badshah">
  <?php include 'ajaxify/notifications/notifications.php'; ?>
</div>

<?php include_once 'needs/prompt.php'; ?>
<?php include_once 'needs/search.php'; ?>

<!-- including the footer of the document -->
<?php include_once 'includes/footer.php'; ?>

<script type="text/javascript">
  $(function(){
    LinkIndicator("notifications");
    $('.notifications').children().filter('.m_n_new').text('');
    $('.notification_span').html("<i class='material-icons'>notifications_none</i>");
  });
</script>
