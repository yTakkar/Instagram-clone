<?php include 'config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include 'config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $universal = new universal;
  $avatar = new Avatar;
?>

<?php
  if ($universal->isLoggedIn()) {
    header('Location: '.DIR);
  }
?>

<?php
  $title = "Login | Instagram";
  $keywords = "Instagram, Share and capture world's moments, share, capture, share, login, signup";
  $desc = "Instagram lets you capture, follow, like and share world's moments in a better way and tell your story with photos, messages, posts and everything in between";
?>

<?php include 'index_include/index_header.php'; ?>

<div class="container login_container">

  <div class="log_sign">
    <a href="signup" class="pri_btn">Need an account?</a>
  </div>

  <div class="input_wrapper">
    <div class="display_text">
      <span>Get started again</span>
    </div>
    <div class="sign_up_div">
      <form class="login" action="" method="post">
        <input type="text" name="s_username" value="" autocomplete="off" placeholder="Username" class="s_username big_input" spellcheck="false" mssg="" maxlength="32" required autofocus>
        <div class="username_checker u_c">
          <span class="checker_text">username not available</span>
          <span class="checker_icon">
            <i class="fa fa-frown-o" aria-hidden="true"></i>
          </span>
        </div>
        <input type="password" name="s_password" value="" autocomplete="off" placeholder="Password" id="login_password" class="s_password big_input" required maxlength="32">
        <span class="show_psswrd log_show_psswrd" id="show_psswrd">
          <i class="fa fa-lock" aria-hidden="true"></i>
        </span>
        <input type="submit" name="s_submit" value="Log in to continue" class="s_submit">
      </form>
      <div class="forgot_psswrd">
        <a href="forgot_psswrd" class="a_pri" alt="Forgot your password">Forgot your password?</a>
      </div>
    </div>
  </div>

</div>

  <div class="quick_login">
    <?php
      if (isset($_COOKIE['ids'])) {
        $array = @json_decode($_COOKIE['ids']);

        echo "<a class='sec_btn clear_all_ql' href='#'>Remove all quick logins</a>";

        $cookie = @array_slice(array_reverse($array), 0, 15);
        foreach ($cookie as $elem) {
          echo "<div class='q_l_div'>
          <img src='". DIR ."/{$avatar->GETsAvatar($elem)}' alt='' data-description='{$universal->GETsDetails($elem, "username")}' data-username='{$universal->GETsDetails($elem, "username")}' data-id='{$elem}'>
          </div>";
        }
      }
    ?>
  </div>

  <div class="q_l_model">
    <div class="q_l_m_cancel_div">
      <span class="q_l_m_cancel"><i class="material-icons">clear</i></span>
    </div>
    <div class="q_l_m_main">
      <div class="q_l_m_img_div">
        <img src="<?php echo DIR; ?>/images/avatars/voldemort.jpg" alt="">
        <span class="q_l_username">Mirza ghalib</span>
      </div>
      <form class="q_l_m_form" method="post">
        <input type="password" name="q_password" value="" autocomplete="off" placeholder="Password" id="q_l_password" class="" spellcheck="false" required maxlength="32">
        <input type="submit" name="" value="Login to continue">
        <div class="q_l_m_bottom">
          <input type="hidden" name="" value="" class="_id">
          <a href="forgot_psswrd" class="a_pri" alt="Forgot your password">Forgot your password?</a>
          <!-- <span class="show_psswrd q_l_show_psswrd" id="show_psswrd">
            <i class="fa fa-lock" aria-hidden="true"></i>
          </span> -->
        </div>
      </form>
      <a href="#" class="sec_btn q_l_remove">Remove all</a>
    </div>
  </div>

  <div class="notify">
    <span></span>
  </div>

</div>

<div class="overlay-2"></div>
<div class="overlay"></div>
<?php include 'index_include/index_footer.php'; ?>

<script type="text/javascript">
  $(function(e){
    $('form.login').on('submit', (function(e){
      e.preventDefault();
      $('.s_submit').prop('disabled', true);
      $('.overlay-2').show();
      login($('.s_username').val(), $('.s_password').val(), $('.s_submit'));
    }));

    $('.log_show_psswrd').togglePassword({
      input: document.getElementById('login_password')
    });

    $('.q_l_div > img').description({extraTop: -30});

    $('.q_l_div > img').quickLogin();

  });
</script>
