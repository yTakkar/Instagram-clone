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
    <span>Developed by <a href="<?php echo DIR; ?>/profile/takkar007">Faiyaz shaikh</a> currently 18 living in Dharavi, Mumbai.</span>
    <span>He's a full-stack developer and mostly writes code in JavaScript as it is his favourite language. And why he is programmer - 'coz he thinks programming languages can make anyone a magician!!</span>
    <span>Other Interests:</span>
    <span>Travelling</span>
    <span>Qawwali, Shayari & Ghazals</span>
    <span>Fantasy & Imagination</span>
    <span>UI/UX</span>
    <span>Designing</span>
    <span>Simplicity</span>
    <span>Paintings</span>
    <span>Nature - (Reason why I love travelling)</span>
    <span>Motivation-Oriented stuffs</span>
    <span>... and much more</span>
    <div class="dev_div_links">
      <a href="https://www.facebook.com/profile.php?id=100009110960262" class="sec_btn">Facebook</a>
      <a href="https://www.instagram.com/_faiyaz_shaikh/" class="sec_btn">Real Instagram</a>
      <a href="https://twitter.com/shtakkar" class="sec_btn">Twitter</a>
    </div>
  </div>

</div>

<?php
if ($universal->isLoggedIn()) {
  include 'includes/footer.php';
} else if ($universal->isLoggedIn() == false) {
  include 'index_include/index_footer.php';
}
?>
