<?php include 'config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include 'config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $universal = new universal;
  $avatar = new Avatar;
  $post = new post;
  $noti = new notifications;
  $message = new message;
  $suggestions = new suggestion;
  $hashtag = new hashtag;
?>

<?php
  if ($universal->isLoggedIn() == false) {
    header('Location:'. DIR .'/welcome');
  }
  $session = $_SESSION['id'];
?>

<?php
  $title = "{$noti->titleNoti()} Instagram";
  $keywords = "Instagram, Share and capture world's moments, share, capture, home";
  $desc = "Instagram lets you capture, follow, like and share world's moments in a better way and tell your story with photos, messages, posts and everything in between";
?>

<!-- including files for header of document -->
<?php include_once 'includes/header.php'; ?>
<?php include_once 'needs/heading.php'; ?>
<?php include_once 'needs/nav.php'; ?>

<div class="user_info" data-userid="<?php echo $session; ?>" data-sessionid="<?php echo $session; ?>" data-username="<?php echo $universal->getUsernameFromSession(); ?>"></div>

<div class="overlay"></div>
<div class="notify"><span></span></div>
<div class="badshah">
  <?php include_once 'ajaxify/home/home.php'; ?>
</div>

<?php include 'needs/post.php'; ?>
<?php include 'needs/emojis.php'; ?>
<?php include_once 'needs/display.php'; ?>
<?php include_once 'needs/image_show.php'; ?>
<?php include_once 'needs/prompt.php'; ?>
<?php include_once 'needs/stickers.php'; ?>
<?php include_once 'needs/search.php'; ?>
<?php include_once 'needs/noti_speak.php'; ?>
<?php include 'needs/create_group.php'; ?>

<?php
  // $help = array(
  //   "If getting <b>irritated</b>, then you're a <b>refresh</b> away"
  // );
  // include_once 'needs/tip.php';
?>

<!-- including the footer of the document -->
<?php include_once 'includes/footer.php'; ?>
<script type="text/javascript">
  $(function(){
    $('.m_n_a').removeClass('active');
    LinkIndicator('index');

    $(window).commonUserFeeds({when: "home"});
    // getFeedAtEnd();

  });
</script>
