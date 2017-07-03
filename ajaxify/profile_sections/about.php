<?php include '../../config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include '../../config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $universal = new universal;
  $tags = new tags;
  $avatar = new Avatar;
  $follow = new follow_system;
  $general = new general;
  $post = new post;
  $Time = new time;
  $settings = new settings;
?>

<?php
  $get_id = $universal->getIdFromGet($_GET['u']);
  if (isset($_SESSION['id'])) {
    $session_id = $_SESSION['id'];
  }
?>

<div class="senapati pro_senapati">
  <div class="about">

    <div class="sabout">

      <div class="sabout_one inst">
        <img src="<?php echo DIR; ?>/images/needs/tree.png" alt="">
        <div class="sabout_one_info">
          <span>Update or edit you profile to make it look more attractive</span>
          <a href="<?php echo DIR; ?>/" class="sec_btn">Update profile</a>
          <a href="<?php echo DIR; ?>/edit" class="pri_btn">Edit profile</a>
        </div>
      </div>

      <div class="social_div inst">
        <?php
          if($universal->GETsDetails($get_id, "instagram") != ""){
            echo "<a href='{$universal->GETsDetails($get_id, "instagram")}' target='_blank'><i class='fa fa-instagram' aria-hidden='true'></i></a>";
          }
        ?>
        <?php
          if($universal->GETsDetails($get_id, "facebook") != ""){
            echo "<a href='{$universal->GETsDetails($get_id, "facebook")}' target='_blank'><i class='fa fa-facebook-official' aria-hidden='true'></i></a>";
          }
        ?>
        <?php
          if($universal->GETsDetails($get_id, "youtube") != ""){
            echo "<a href='{$universal->GETsDetails($get_id, "youtube")}' target='_blank'><i class='fa fa-youtube-play' aria-hidden='true'></i></a>";
          }
        ?>
        <?php
          if($universal->GETsDetails($get_id, "twitter") != ""){
            echo "<a href='{$universal->GETsDetails($get_id, "twitter")}' target='_blank'><i class='fa fa-twitter' aria-hidden='true'></i></a>";
          }
        ?>
        <?php
          if($universal->GETsDetails($get_id, "website") != ""){
            echo "<a href='{$universal->GETsDetails($get_id, "website")}' target='_blank'><i class='fa fa-globe' aria-hidden='true'></i></a>";
          }
        ?>
      </div>

    </div>

    <div class="fabout">
      <div class="a_edit">
        <?php if($universal->MeOrNot($get_id)){ ?>
          <a href="<?php echo DIR; ?>/edit"><i class="material-icons">mode_edit</i></a>
        <?php } ?>
      </div>

      <div class="a_username">
        <span class="a_label">Username</span>
        <span class="a_info"><?php echo $universal->GETsDetails($get_id, "username"); ?></span>
      </div>

      <div class="a_name">
        <span class="a_label">Name</span>
        <span class="a_info"><?php echo $universal->GETsDetails($get_id, "firstname"); ?> <?php echo $universal->GETsDetails($get_id, "surname"); ?></span>
      </div>

      <?php if($settings->emailPrivacy($get_id) == "public"){ ?>
      <div class="a_email">
        <span class="a_label">Email</span>
        <span class="a_info"><?php echo $universal->GETsDetails($get_id, "email"); ?></span>
      </div>
      <?php } else if($settings->emailPrivacy($get_id) == "private" && $universal->MeOrNot($get_id)){ ?>
        <div class="a_email">
          <span class="a_label">Email</span>
          <span class="a_info"><?php echo $universal->GETsDetails($get_id, "email"); ?></span>
        </div>
        <?php } ?>

      <div class="a_bio">
        <span class="a_label">Bio</span>
        <span class="a_info"><?php echo $universal->GETsDetails($get_id, "bio"); ?></span>
      </div>

      <div class="a_type">
        <span class="a_label">Account type</span>
        <span class="a_info"><?php echo $universal->GETsDetails($get_id, "type"); ?></span>
      </div>

      <div class="a_facebook">
        <span class="a_label">Facebook</span>
        <?php
          if($universal->GETsDetails($get_id, "facebook") == ""){
            if ($universal->MeOrNot($get_id)) {
        ?>
              <a href="<?php echo DIR; ?>/edit">Add Facebook account</a>
        <?php
            }
          } else {
        ?>
        <a class="a_info" href="<?php echo $universal->GETsDetails($get_id, "facebook"); ?>" target="_blank"><?php echo $universal->GETsDetails($get_id, "facebook"); ?></a>
        <?php } ?>
      </div>

      <div class="a_instagram">
        <span class="a_label">Instagram</span>
        <?php
          if($universal->GETsDetails($get_id, "instagram") == ""){
            if ($universal->MeOrNot($get_id)) {
        ?>
              <a href="<?php echo DIR; ?>/edit">Add Instagram account</a>
        <?php
            }
          } else {
        ?>
        <a class="a_info" href="<?php echo $universal->GETsDetails($get_id, "instagram"); ?>" target="_blank"><?php echo $universal->GETsDetails($get_id, "instagram"); ?></a>
        <?php } ?>
      </div>

      <div class="a_youtube">
        <span class="a_label">Youtube</span>
        <?php
          if($universal->GETsDetails($get_id, "youtube") == ""){
            if ($universal->MeOrNot($get_id)) {
        ?>
              <a href="<?php echo DIR; ?>/edit">Add Youtube account</a>
        <?php
            }
          } else {
        ?>
        <a class="a_info" href="<?php echo $universal->GETsDetails($get_id, "youtube"); ?>" target="_blank"><?php echo $universal->GETsDetails($get_id, "youtube"); ?></a>
        <?php } ?>
      </div>

      <div class="a_Twitter">
        <span class="a_label">Twitter</span>
        <?php
          if($universal->GETsDetails($get_id, "twitter") == ""){
            if ($universal->MeOrNot($get_id)) {
        ?>
              <a href="<?php echo DIR; ?>/edit">Add Twitter account</a>
        <?php
            }
          } else {
        ?>
        <a class="a_info" href="<?php echo $universal->GETsDetails($get_id, "twitter"); ?>" target="_blank"><?php echo $universal->GETsDetails($get_id, "twitter"); ?></a>
        <?php } ?>
      </div>

      <div class="a_website">
        <span class="a_label">Website</span>
        <?php
          if($universal->GETsDetails($get_id, "website") == ""){
            if ($universal->MeOrNot($get_id)) {
        ?>
              <a href="<?php echo DIR; ?>/edit">Add website</a>
        <?php
            }
          } else {
        ?>
        <a class="a_info" href="<?php echo $universal->GETsDetails($get_id, "website"); ?>" target="_blank"><?php echo $universal->GETsDetails($get_id, "website"); ?></a>
        <?php } ?>
      </div>

      <?php if($settings->emailPrivacy($get_id) == "public"){ ?>
      <div class="a_mobile">
        <span class="a_label">Mobile</span>
        <?php
          if($universal->GETsDetails($get_id, "mobile") == ""){
            if ($universal->MeOrNot($get_id)) {
        ?>
              <a href="<?php echo DIR; ?>/edit">Add Mobile</a>
        <?php
        }
          } else {?>
        <span class="a_info"><?php echo $universal->GETsDetails($get_id, "mobile"); ?></span>
        <?php } ?>
      </div>
      <?php } else if($settings->emailPrivacy($get_id) == "private" && $universal->MeOrNot($get_id)){ ?>
        <div class="a_mobile">
          <span class="a_label">Mobile</span>
          <?php
            if($universal->GETsDetails($get_id, "mobile") == ""){
              if ($universal->MeOrNot($get_id)) {
          ?>
                <a href="<?php echo DIR; ?>/edit">Add Mobile</a>
          <?php
          }
            } else {?>
          <span class="a_info"><?php echo $universal->GETsDetails($get_id, "mobile"); ?></span>
          <?php } ?>
        </div>
        <?php } ?>

      <div class="a_joined">
        <span class="a_label">Joined</span>
        <span class="a_info" href="#">
          <?php
            $time = $universal->GETsDetails($get_id, "signup");
            $str = strtotime($time);
            echo date("d-M-Y h:i:s", $str);
          ?>
        </span>
      </div>

      <?php
        if ($universal->MeOrNot($get_id)) {
      ?>
      <div class="a_browser">
        <span class="a_label">Browser when you logged in</span>
        <span class="a_info" href="#"><?php echo $universal->GETsDetails($get_id, "pri_browser"); ?></span>
      </div>

      <div class="a_os">
        <span class="a_label">OS when you logged in</span>
        <span class="a_info" href="#"><?php echo $universal->GETsDetails($get_id, "pri_os"); ?></span>
      </div>

      <div class="a_ip">
        <span class="a_label">IP when you logged in</span>
        <span class="a_info" href="#"><?php echo $universal->GETsDetails($get_id, "pri_ip"); ?></span>
      </div>
      <?php
        }
      ?>

    </div>

  </div>
</div>

<script type="text/javascript">

  LinkIndicator('profile');

  $('.about > .fabout').on('mouseover', function(e){
    $(this).find('.a_edit > a').css('display', 'block');
  }).on('mouseout', function(e){
    $(this).find('.a_edit > a').css('display', 'none');
  });

  if($('.social_div').find('a').length == 0){
    $('.social_div').remove();
  }

</script>
