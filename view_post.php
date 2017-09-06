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
?>

<?php
  if ($universal->isLoggedIn() == false) {
    header('Location:'. DIR .'/welcome');
  }
  $session = $_SESSION['id'];
  $get_id = $_GET['post'];
  $post_by = $post->getPost($get_id, "user_id");
?>

<?php
  $title = "{$noti->titleNoti()} Post by {$universal->GETsDetails($post_by, "username")} • Instagram";
  $keywords = "Instagram, Share and capture world's moments, share, capture, view post";
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
  <div class="senapati">
    <div class="prajkumar view_rajkumar">
      <?php $post->viewPost($get_id); ?>
    </div>

    <div class="srajkumar">

      <div class="recomm home_recomm inst">
        <div class="recomm_top header_of_divs">
          <span>Suggested</span>
          <a href="#" class="recomm_refresh" data-description='refresh'><i class="fa fa-refresh" aria-hidden="true"></i></a>
          <a href="<?php echo DIR; ?>/explore?ask=exp_people" class="recomm_all" data-description='view all'><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
        </div>
        <div class="recomm_main">
          <?php $suggestions->HomeSuggestions("direct"); ?>
        </div>
      </div>

      <div class="c_g_div inst">
        <span>Create public or private group of your interest with people you know.</span>
        <a href="#" class="sec_btn c_g">Create group</a>
      </div>

    </div>

  </div>
</div>

<?php include 'needs/create_group.php'; ?>
<?php include_once 'needs/display.php'; ?>
<?php include_once 'needs/image_show.php'; ?>
<?php include_once 'needs/prompt.php'; ?>
<?php include_once 'needs/stickers.php'; ?>
<?php include_once 'needs/search.php'; ?>

<!-- including the footer of the document -->
<?php include_once 'includes/footer.php'; ?>