<?php include 'config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include 'config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $universal = new universal;
?>

<?php
  if ($universal->isLoggedIn()) {
    header('Location: '.DIR);
  }
?>

<?php
  $title = "Instagram";
  $keywords = "Instagram, Share and capture world's moments, share, capture, share, login, signup";
  $desc = "Instagram lets you capture, follow, like and share world's moments in a better way and tell your story with photos, messages, posts and everything in between";
?>

<?php  include 'index_include/index_header.php'; ?>

<div class="index_wrapper">

  <div class="index_sign log_sign">
    <a href="signup" class="pri_btn">Need an account?</a>
  </div>

  <div class="github">
  <iframe src="https://ghbtns.com/github-btn.html?user=yTakkar&type=follow&count=false&size=large" frameborder="0" scrolling="0" width="180px" height="30px"></iframe>
    <iframe src="https://ghbtns.com/github-btn.html?user=yTakkar&repo=Instagram-Clone&type=fork&count=true&size=large" frameborder="0" scrolling="0" width="125px" height="30px"></iframe>
    <iframe src="https://ghbtns.com/github-btn.html?user=yTakkar&repo=Instagram-Clone&type=star&count=true&size=large" frameborder="0" scrolling="0" width="160px" height="30px"></iframe>
  </div>

  <div class="banner">
    <img src="<?php echo DIR; ?>/images/needs/dbx_29.png" alt="">
    <!-- <img src="http://chicohq.com/images/bg.svg" alt=""> -->
  </div>

  <div class="username_checker i_c">
    <span class="checker_text">username not available</span>
    <span class="checker_icon">
      <i class="fa fa-frown-o" aria-hidden="true"></i>
    </span>
  </div>

  <div class="feature">
    <div class="feature_desc">
      <h1>It's awesome to capture and share world's moments</h1>
      <h3>Instagram helps you connect with people with your amazing photos, videos and everything in between.</h3>
      <a href="<?php echo DIR; ?>/about" class="pri_btn">View more</a>
    </div>
    <div class="index_login sign_up_div">
      <form class="index_form login" action="" method="post">
        <input type="text" name="s_username" value="" autocomplete="off" placeholder="Username" class="s_username big_input" spellcheck="false" mssg="" maxlength="32" required autofocus>
        <input type="password" name="s_password" value="" autocomplete="off" placeholder="Password" id="password" class="s_password big_input" required maxlength="32">
        <span class="show_psswrd home_show_psswrd" id="show_psswrd">
          <i class="fa fa-lock" aria-hidden="true"></i>
        </span>
        <input type="submit" name="s_submit" value="Log in to continue" class="s_submit">
      </form>
      <div class="forgot_psswrd">
        <a href="forgot_psswrd" class="a_pri hover" alt="Forgot your password" data-description="Forgot your password">Forgot password?</a>
        <a href="login" class="a_pri" alt="Open in another page">Separate Page</a>
      </div>
    </div>
  </div>

  <div class="notify">
    <span></span>
  </div>

</div>

<div class="overlay-2"></div>
<?php include 'index_include/index_footer.php'; ?>

<script type="text/javascript">
  $(function(e){
    // $('.s_username').username_checker({
    //   url: "reg_process/@username_checker.php"
    // });

    $('#show_psswrd').togglePassword({
      input: document.getElementById('password')
    });

    $('form').on('submit', (function(e){
      e.preventDefault();
      $('.s_submit').prop('disabled', true);
      $('.overlay-2').show();
      login($('.s_username').val(), $('.s_password').val(), $('.s_submit'));
    }))

  });
</script>
