<?php
  session_start();
  $session = $_SESSION['id'];
  include '../../config/classesGetter.php';

  $universal = new universal;
  $settings = new settings;
?>

<div class="pro_settings">

  <div class="acc_type">
    <div class="acc_type_header">
      <span class="c_p_header acc_type_h">Change account type</span>
      <span>Selete your account type, currently it's <span class='type_indicator'><?php echo $universal->GETsDetails($session, "type"); ?></span></span>
    </div>
    <div class="acc_type_main">
      <!-- <span class="s_a"></span> -->
      <select class="acc_select" name="">
        <?php if ($universal->GETsDetails($session, "type") == "public"){ ?>
          <option value="public">Public</option>
          <option value="private">Private</option>
        <?php } else if($universal->GETsDetails($session, "type") == "private"){ ?>
          <option value="private">Private</option>
          <option value="public">Public</option>
        <?php } ?>
      </select>
      <span class="dlt_acc_bold">Note:</span>
      <span>When account is <span class="dlt_acc_bold">private only your followers can interact with with your profile.</span> Others would have to follow you first to interact. This is the <span class="dlt_acc_bold">recommended</span> option as only people you know would interact with your profile.</span>
      <span>And when account is public <span class="dlt_acc_bold">anyone can see your profile and interact with your profile.</span></span>
    </div>
  </div>

  <div class="privacy_thing">
    <div class="email_p_div">
      <input type="checkbox" name="email_private" value="" class="inst_checkbox" id="email_private" <?php if($settings->emailPrivacy($session) == "private"){echo "checked";} ?> >
      <label for="email_private">Email visible to me only</label>
    </div>
    <div class="mobile_p_div">
      <input type="checkbox" name="mobile_private" value="" class="inst_checkbox" id="mobile_private" <?php if($settings->mobilePrivacy($session) == "private"){echo "checked";} ?>>
      <label for="mobile_private">Phone visible to me only</label>
    </div>

  </div>

  <div class="blocking">
    <div class="block_header">
      <span class="c_p_header acc_type_h">Your blocked members</span>
    </div>

    <!-- <div class="block_users">
      <input type="text" name="" value="" placeholder="Search to block..">
    </div> -->

    <?php $settings->blockedUsers(); ?>

  </div>

</div>

<script type="text/javascript">
  $(function(){
    $('.acc_type_main select').changeAccountType();
    $('#email_private').emailPrivacy();
    $('#mobile_private').mobilePrivacy();
    $('.unblock').unblock();
  });
</script>
