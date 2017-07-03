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
  $fav = new favourite;
  $noti = new notifications;
  $message = new message;
?>

<?php
  if ($universal->checkGet($_GET['u']) == false) {
    header('Location:'.DIR);
  }
  if ($universal->validGET($_GET['u']) == false) {
    header('Location: '.DIR."/no_user");
  }
  if ($universal->isLoggedIn() == false) {
    // header("Location:". DIR ."/login");
  }
  $get_id = $universal->getIdFromGet($_GET['u']);
  if (isset($_SESSION['id'])) {
    $session_id = $_SESSION['id'];
  }
?>

<?php
  if (isset($_SESSION['id'])) {
    // FOR INCREMENTING PROFILE VIEWS
    $follow->viewCounter($get_id);

    // FOR DELETING EMPTY TAGS
    $tags->filterTags();

    // FOR DELETING FILES FROM TEMP FOLDERS
    $general->deleteFiles();
  }
?>


<?php
  $title = "{$noti->titleNoti()} @{$universal->GETsDetails($get_id, "username")} ({$universal->GETsDetails($get_id, "firstname")} {$universal->GETsDetails($get_id, "surname")}) • Instagram";
  $keywords = "{$universal->GETsDetails($get_id, "username")}, {$universal->GETsDetails($get_id, "firstname")}, {$universal->GETsDetails($get_id, "surname")}, Instagram, Share and capture world's moments";
  $desc = "Connect with {$universal->GETsDetails($get_id, "username")}'s profile • Instagram";
?>

<?php include_once 'includes/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo DIR; ?>/public/css/jquery-ui.css">
<?php include_once 'needs/heading.php'; ?>
<?php include_once 'needs/nav.php'; ?>

<div class="user_info" data-userid="<?php echo $get_id; ?>" data-sessionid="<?php if(isset($_SESSION['id'])){ echo $session_id; } ?>"
 data-username="<?php echo $universal->GETsDetails($get_id, 'username'); ?>" data-firstname="<?php  echo $universal->GETsDetails($get_id, "firstname");?>" data-surname="<?php echo $universal->GETsDetails($get_id, "surname") ?>" data-sessionname = "<?php echo $universal->getUsernameFromSession(); ?>"></div>

<div class="notify"><span></span></div>
<div class="overlay"></div>
<div class="badshah">
  <?php include_once 'ajaxify/profile/profile_banner.php'; ?>
  <?php
    if($universal->isLoggedIn() == false){
      echo "<div class='home_last_mssg private_last_mssg'><img src='". DIR ."/images/needs/large.jpg'>
      <span>You are not logged in. Login to connect with {$universal->GETsDetails($get_id, "username")}</span></div>";
    } else if ($universal->isPrivate($get_id)) {
      echo "<div class='home_last_mssg private_last_mssg'><img src='". DIR ."/images/needs/large.jpg'>
      <span>Account is private. Follow to connect with {$universal->GETsDetails($get_id, "username")} and refresh.</span>
      <span>";
      if ($mutual->mutualCount($get_id) != 0) {
        echo $mutual->mutualCount($get_id)." mutual followers";
      }
      echo "</span></div>";
    } else {
  ?>

  <?php include_once 'ajaxify/profile/profile_hori_nav.php'; ?>
  <div class="hmm">
    <div class="spinner">
      <span></span><span></span><span></span>
    </div>
  </div>

  <?php } ?>

  <input type="hidden" name="" value="" class="mn">
</div>
<?php include_once 'ajaxify/profile/view_profile.php'; ?>
<?php include_once 'needs/profile_avatars.php'; ?>
<?php include_once 'needs/display.php'; ?>
<?php include_once 'needs/prompt.php'; ?>
<?php include_once 'needs/image_show.php'; ?>
<?php include_once 'needs/stickers.php'; ?>
<?php include_once 'needs/search.php'; ?>
<?php include 'needs/create_group.php'; ?>

<?php include_once 'includes/footer.php'; ?>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
  $(function(){

    function fetch(elem){
      var username = $('.user_info').data('username');
      $.ajax({
        url: DIR+"/ajaxify/profile_sections/"+elem+".php",
        method: "GET",
        cache: false,
        data: {u: username},
        beforeSend: function(e){
          $('.hmm > .spinner').fadeIn('fast');
        },
        success: function(data){
          var link = $('.inst_nav');
          link.removeClass('pro_nav_active');
          $(".inst_nav[href='"+ elem +"']").addClass('pro_nav_active');
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
      fetch("posts");
    }

    // $('.m_n_a').removeClass('active');

    notMM();

    //Set it Draggable
    $('.pro_crop_tool').draggable({containment: ".crop_img"});

    // var link = $('.inst_nav');

  });
</script>
