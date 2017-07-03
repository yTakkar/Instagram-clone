<?php include 'config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include 'config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $universal = new universal;
  $tags = new tags;
  $avatar = new Avatar;
  $follow = new follow_system;
  $general = new general;
  $post = new post;
  $recommend = new recommend;
  $settings = new settings;
  $mutual = new mutual;
  $noti = new notifications;
  $message = new message;
  $groups = new group;
?>

<?php
  if ($universal->checkGet($_GET['grp']) == false) {
    header('Location:'.DIR);
  }
  if ($groups->validGrp($_GET['grp']) == false) {
    header('Location: '.DIR."/no_group");
  }
  if ($universal->isLoggedIn() == false) {
    // header("Location:". DIR ."/login");
  }
  $grp = $_GET['grp'];
  if (isset($_SESSION['id'])) {
    $session = $_SESSION['id'];
  }
?>

<?php
  $title = "{$noti->titleNoti()} {$groups->GETgrp($grp, 'grp_name')} • Instagram";
  $keywords = "Instagram, Share and capture world's moments";
  $desc = "View group • Instagram";
?>

<?php include_once 'includes/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo DIR; ?>/public/css/jquery-ui.css">
<?php include_once 'needs/heading.php'; ?>
<?php include_once 'needs/nav.php'; ?>

<div class="user_info" data-grpname='<?php echo $groups->GETgrp($grp, "grp_name"); ?>' data-grp='<?php echo $grp; ?>' data-userid='<?php echo $session; ?>' data-username='<?php echo $universal->GETsDetails($session, "username"); ?>'></div>

<div class="notify"><span></span></div>
<div class="overlay"></div>
<div class="badshah">
  <?php include 'ajaxify/groups/grp_banner.php'; ?>

  <?php if($groups->memberOrNot($grp, $session) == false && $groups->GETgrp($grp, "grp_privacy") == "private"){ ?>

    <div class='home_last_mssg grp_last_mssg'><img src='<?php echo DIR; ?>/images/needs/large.jpg'>
    <span>Group is private. Join to connect with this group and refresh.</span>
    <span><?php if($groups->mutualGrpMemCount($grp) != 0){ echo $groups->mutualGrpMemCount($grp)." mutual members"; } ?></span></div>

  <?php } else if($universal->isLoggedIn() == false) { ?>

    <div class='home_last_mssg grp_logout_last_mssg'><img src='<?php echo DIR; ?>/images/needs/large.jpg'>
    <span>You are not logged in. Login to connect with this group.</span></div>

  <?php } else { ?>

    <?php include 'ajaxify/groups/grp_hori_nav.php'; ?>

    <div class="hmm">
      <div class="spinner">
        <span></span><span></span><span></span>
      </div>
    </div>

  <?php } ?>

</div>

<?php include_once 'ajaxify/groups/view_profile.php'; ?>
<?php include_once 'needs/profile_avatars.php'; ?>
<?php include_once 'needs/display.php'; ?>
<?php include_once 'needs/prompt.php'; ?>
<?php include_once 'needs/emojis.php'; ?>
<?php include_once 'needs/image_show.php'; ?>
<?php include_once 'needs/stickers.php'; ?>
<?php include_once 'needs/create_group.php'; ?>
<?php include_once 'needs/post.php'; ?>
<?php include_once 'needs/search.php'; ?>

<?php include_once 'includes/footer.php'; ?>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
$(function(){

  function fetch(elem){
    var username = $('.user_info').data('grpname');
    var grp = $('.user_info').data('grp');
    $.ajax({
      url: DIR+"/ajaxify/grp_sections/"+elem+".php",
      method: "GET",
      cache: false,
      data: {grp: grp},
      beforeSend: function(e){
        $('.hmm > .spinner').fadeIn('fast');
      },
      success: function(data){
        var link = $('.inst_grp_nav');
        link.removeClass('pro_nav_active');
        $(".inst_grp_nav[href='"+ elem +"']").addClass('pro_nav_active');
        $('.hmm > .spinner').fadeOut('fast');
        $('.hmm').html(data).hide().fadeIn(100);
        // $('html, body').animate({scrollTop: 380}, "slow");
      }
    });
  }

  var get = checkGET("ask");

  if (get.has) {
    fetch(get.value);
  } else {
    fetch("grp_posts");
  }

  //Set it Draggable
  $('.pro_crop_tool').draggable({containment: ".crop_img"});

  $('a[href="grp_add_members"]').on('click', function(e){
    e.preventDefault();
    $('.a_m_input > input[type="text"]').focus();
  });

});
</script>
