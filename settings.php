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
  if ($universal->isLoggedIn() == false) {
    header('Location:'. DIR .'/welcome');
  }
  $session = $_SESSION['id'];
?>

<?php
  $title = "{$noti->titleNoti()} Account settings • Instagram";
  $keywords = "Instagram, Share and capture world's moments, share, capture, settings";
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

    <div class="srajkumar settings_srajkumar">
      <div class="settings_nav_div">
        <ul>
          <li><a href="<?php echo DIR; ?>/settings/change_password" class="settings_nav change_password" data-url="change_password.php">Change password</a></li>
          <li><a href="<?php echo DIR; ?>/settings/profile_settings" class="settings_nav profile_settings" data-url="profile_settings.php">Profile settings</a></li>
          <!-- <li><a href="<?php echo DIR; ?>/settings/delete_account" class="settings_nav delete_account" data-url="delete_account.php">Delete account</a></li> -->
          <li><a href="<?php echo DIR; ?>/settings/login_details" class="settings_nav login_details" data-url="login_details.php">Login details</a></li>
        </ul>
      </div>
    </div>

    <div class="prajkumar settings_rajkumar">

      <div class="settings_loader">
        <div class="spinner">
          <span></span><span></span><span></span>
        </div>
      </div>

    </div>
  </div>
</div>

<?php include_once 'needs/display.php'; ?>
<?php include_once 'needs/prompt.php'; ?>
<?php include_once 'needs/search.php'; ?>

<!-- including the footer of the document -->
<?php include_once 'includes/footer.php'; ?>

<script type="text/javascript">
  $(function(){

    function fetch(elem){
      var username = $('.user_info').data('username');
      $.ajax({
        url: DIR+"/ajaxify/settings/"+elem+".php",
        method: "GET",
        cache: false,
        beforeSend: function(e){
          $('.settings_loader').html('<div class="spinner"><span></span><span></span><span></span></div>');
          $('.settings_rajkumar > .settings_loader > .spinner').addClass('hmm_spinner_show');
        },
        success: function(data){
          $('.settings_rajkumar > .settings_loader > .spinner').removeClass('hmm_spinner_show');
          $('.settings_nav').removeClass('settings_nav_active');
          $("."+ elem).addClass('settings_nav_active');
          $('.hmm > .spinner').fadeOut('fast');
          $('.settings_rajkumar > .settings_loader').html(data).hide().fadeIn(100);
          // $('html, body').animate({scrollTop: 380}, "slow");
        }
      });
    }

    var get = checkGET("ask");

    if (get.has) {
      fetch(get.value);
    } else {
      fetch("change_password");
    }

    LinkIndicator("settings");

  });
</script>
