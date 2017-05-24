<?php include 'config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include 'config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $universal = new universal;
  $avatar = new Avatar;
  $settings = new settings;
  $noti = new notifications;
  $message = new message;
  $tags = new tags;
?>

<?php
  if ($universal->checkGet($_GET['tag']) == false) {
    header('Location:'.DIR);
  }
  if ($universal->isLoggedIn() == false) {
    header("Location:". DIR ."/login");
  }
  $tag = $_GET['tag'];
  $session = $_SESSION['id'];
?>

<?php
  $title = "{$noti->titleNoti()} *$tag • Instagram";
  $keywords = "$tag, Instagram, Share and capture world's moments";
  $desc = "View people with $tag tag • Instagram";
?>

<?php include_once 'includes/header.php'; ?>
<?php include_once 'needs/heading.php'; ?>
<?php include_once 'needs/nav.php'; ?>

<div class="user_info" data-tag='<?php echo $tag; ?>' data-userid='<?php echo $session; ?>'></div>

<div class="overlay"></div>
<div class="notify"><span></span></div>
<div class="badshah">
  <div class="senapati">

    <div class="hashtag_info tag_info inst">
      <span>*<?php echo $tag; ?></span>
      <span class="no_of_tag_peop"><?php echo $tags->noOfTagPeople($tag); ?> members</span>
    </div>

    <div class="prajkumar view_rajkumar">
      <div class="tagged_people">

        <?php $tags->sameTagPeople($tag); ?>

      </div>
    </div>

    <div class="srajkumar">

      <div class="your_tags inst">
        <span>Your tags</span>
        <div class="y_tags_main">
          <?php $tags->userTags($session, "page") ?>
        </div>
      </div>

      <div class="your_tags inst pop_tags">
        <span>Most popular tags</span>
        <div class="y_tags_main">
          <?php $tags->popularTags(); ?>
        </div>
      </div>

    </div>

  </div>
</div>

<?php include_once 'needs/profile_avatars.php'; ?>
<?php include_once 'needs/display.php'; ?>
<?php include_once 'needs/prompt.php'; ?>
<?php include_once 'needs/image_show.php'; ?>
<?php include_once 'needs/stickers.php'; ?>
<?php include_once 'needs/search.php'; ?>

<?php include_once 'includes/footer.php'; ?>
