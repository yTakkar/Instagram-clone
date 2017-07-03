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

<div class="senapati pro_senapati grp_senapati a_m_senapti">

  <?php if ($groups->isGrpAdmin($grp, $session) == false){
    echo "<div class='home_last_mssg add_mem_last_mssg'><img src='". DIR ."/images/needs/large.jpg'><span>You're not the admin of this group to add members.</span></div>";
  } else { ?>

  <div class="srajkumar grp_srajkumar">

    <div class="sabout inst">
      <div class="sabout_one">
        <img src="<?php echo DIR; ?>/images/needs/tree.png" alt="">
        <div class="sabout_one_info">
          <span>Update or edit group to make it look more attractive</span>
          <a href="<?php echo DIR; ?>/groups/<?php echo $grp; ?>?ask=grp_posts" class="sec_btn">Update group</a>
          <a href="<?php echo DIR; ?>/groups/<?php echo $grp; ?>?ask=grp_edit" class="pri_btn">Edit group</a>
        </div>
      </div>
    </div>

  </div>

  <div class="prajkumar a_m_prajkumar">
    <div class="a_m inst">
      <div class="a_m_header">
        <span>Add members</span>
      </div>
      <div class="a_m_main">
        <div class="a_m_input">
          <input type="text" name="" value="" placeholder="Search to add.." data-grp='<?php echo $grp; ?>'>
        </div>
        <div class="mssg_persons a_m_selector">
          <div class="mssg_persons_inner">
            <ul class="grp_to_ul">
            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>

  <?php } ?>

</div>

<script type="text/javascript">
  $(function(){
    $('.a_m_input > input[type="text"]').focus().addGroupMembers();
  });
</script>
