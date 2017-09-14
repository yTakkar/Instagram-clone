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
?>

<?php
  $title = "{$noti->titleNoti()} Developer • Instagram";
  $keywords = "Instagram, Share and capture world's moments, share, capture, about";
  $desc = "Instagram lets you capture, follow, like and share world's moments in a better way and tell your story with photos, messages, posts and everything in between";
?>

<!-- including files for header of document -->
<?php
  if ($universal->isLoggedIn()) {
    include 'includes/header.php';
    include 'needs/heading.php';
    include 'needs/nav.php';
    include_once 'needs/search.php';
  } else if ($universal->isLoggedIn() == false) {
    include 'index_include/index_header.php';
  }
?>

<div class="overlay"></div>
<div class="notify"><span></span></div>
<div class="badshah">

  <div class="dev_div inst">
    <img src="<?php echo DIR; ?>/images/needs/tumblr_ne4s7eGNYj1snc5kxo1_500.gif" alt="">
    <span>Developed by <a href="<?php echo DIR; ?>/profile/takkar007">Faiyaz shaikh</a> currently 18 living in Mumbai.</span>
    <span>He's a full-stack developer and mostly writes code in JavaScript & Golang as these are his favourite languages. And why he is programmer - 'coz he thinks programming languages can make anyone a magician!!</span>
    <div class="dev_div_links">
      <a href="https://www.quora.com/profile/Shaikh-Takkar/" class="sec_btn">Quora</a>
      <a href="https://www.instagram.com/_faiyaz_shaikh/" class="sec_btn">Real Instagram</a>
      <a href="https://github.com/yTakkar" class="sec_btn">GitHub</a>
    </div>
    <iframe src="https://ghbtns.com/github-btn.html?user=yTakkar&type=follow&count=false&size=large" frameborder="0" scrolling="0" width="220px" height="30px"></iframe>
  </div>

</div>

<?php
if ($universal->isLoggedIn()) {
  include 'includes/footer.php';
} else if ($universal->isLoggedIn() == false) {
  include 'index_include/index_footer.php';
}
?>
