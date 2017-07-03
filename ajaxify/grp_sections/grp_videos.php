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
?>

<?php
  $grp = $_GET['grp'];
  $session = $_SESSION['id'];
?>

<div class="senapati pro_senapati">
  <div class="srajkumar">
    <?php
    // include 'sugg.php';
    ?>
    <div class="c_g_div grp_c_grp inst">
      <span>Explore more recommended videos from all around Instagram.</span>
      <div class="grp_c_we">
        <a href="<?php echo DIR; ?>/explore?ask=exp_videos" class="sec_btn">Explore</a>
      </div>
    </div>

  </div>

  <div class="prajkumar">
    <?php echo $groups->getGrpVideos($grp); ?>
  </div>
</div>

<script type="text/javascript">
  LinkIndicator('profile');

  //calling videoControls plugin
  $('video').videoControls();
</script>
