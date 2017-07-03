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
  $mutual = new mutual;
  $recommend = new recommend;
  $groups = new group;
?>

<?php
  $get_id = $universal->getIdFromGet($_GET['u']);
  if (isset($_SESSION['id'])) {
    $session_id = $_SESSION['id'];
  }
?>

<div class="senapati pro_senapati">
  <div class="srajkumar user_grps_srajkumar">

    <div class="c_g_div grp_c_grp inst">
      <span>Create public or private group of your interest with people you know.</span>
      <a href="#" class="sec_btn c_g">Create group</a>
    </div>

    <div class="c_g_div inst">
      <span>Explore more groups from all around Instagram.</span>
      <div class="grp_c_we">
        <a href="<?php echo DIR; ?>/explore?ask=exp_groups" class="sec_btn">Explore</a>
      </div>
    </div>

  </div>

  <div class="prajkumar">
    <!-- <div class='home_last_mssg pro_last_mssg'>
      <img src='<?php echo DIR; ?>/images/needs/large.jpg'>
      <span>You have no groups</span>
    </div> -->

    <div class="your_grps">

      <!-- <div class="t_g_header">
        <span>You've joined 13 groups</span>
      </div> -->
      <div class="y_g_divs">

          <?php echo $groups->myGroups($get_id); ?>

      </div>

    </div>

  </div>

</div>

<script type="text/javascript">
  LinkIndicator('groups');
  $('.c_g').createGroup();
  $('.join_grp').joinGrp();
  $('.leave_grp').leaveGrp();

</script>
