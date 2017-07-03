<?php include '../../config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include '../../config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $universal = new universal;
  $avatar = new Avatar;
  $settings = new settings;
  $mutual = new mutual;
  $noti = new notifications;
  $message = new message;
  $groups = new group;
  $Time = new time;
?>

<?php
  $grp = $_GET['grp'];
  $session = $_SESSION['id'];
?>

<div class="senapati pro_senapati">
  <div class="about">

    <div class="sabout">
      <div class="sabout_one">
        <img src="<?php echo DIR; ?>/images/needs/tree.png" alt="">
        <?php if($groups->isGrpAdmin($grp, $session)){ ?>
        <div class="sabout_one_info">
          <span>Update or edit group to make it look more attractive</span>
          <a href="<?php echo DIR; ?>/groups/<?php echo $grp; ?>?ask=grp_posts" class="sec_btn">Update group</a>
          <a href="<?php echo DIR; ?>/groups/<?php echo $grp; ?>?ask=grp_edit" class="pri_btn">Edit group</a>
        </div>
        <?php } ?>
      </div>
    </div>

    <div class="fabout">
      <div class="a_edit">
        <?php if($groups->isGrpAdmin($grp, $session)){ ?>
        <a href="<?php echo DIR; ?>/groups/<?php echo $grp; ?>?ask=grp_edit"><i class="material-icons">mode_edit</i></a>
        <?php } ?>
      </div>

      <div class="a_username">
        <span class="a_label">Group name</span>
        <span class="a_info"><?php echo $groups->GETgrp($grp, "grp_name"); ?></span>
      </div>

      <div class="a_name">
        <span class="a_label">No of members</span>
        <span class="a_info"><?php echo $groups->noOfGrpMembers($grp); ?> members</span>
      </div>

      <div class="a_name">
        <span class="a_label">No of posts</span>
        <span class="a_info"><?php echo $groups->noOfGrpPosts($grp); ?> posts</span>
      </div>

      <div class="a_bio">
        <span class="a_label">Bio</span>
        <span class="a_info"><?php echo $groups->GETgrp($grp, "grp_bio"); ?></span>
      </div>

      <div class="a_type">
        <span class="a_label">Group type</span>
        <span class="a_info"><?php echo $groups->GETgrp($grp, "grp_privacy"); ?> group</span>
      </div>

      <div class="a_c_by">
        <span class="a_label">Group created by</span>
        <a class="a_info" href='<?php echo DIR; ?>/profile/<?php echo $universal->GETsDetails($groups->GETgrp($grp, "grp_admin"), "username") ?>'><?php
          if($groups->GETgrp($grp, "grp_admin") == $session){
            echo "You";
          } else {
            echo $universal->GETsDetails($groups->GETgrp($grp, "grp_admin"), "username");
          }
        ?></a>
      </div>

      <div class="a_joined">
        <span class="a_label">Group created</span>
        <span class="a_info" href="#">
          <?php
            $time = $groups->GETgrp($grp, "grp_time");
            $str = strtotime($time);
            echo date("d-M-Y h:i:s", $str)." (". $Time->timeAgo($time) ." ago)";
          ?>
        </span>
      </div>

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
</script>
