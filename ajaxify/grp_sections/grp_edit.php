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

<div class="senapati pro_senapati a_m_senapti">

  <?php if ($groups->isGrpAdmin($grp, $session) == false){
    echo "<div class='home_last_mssg pro_last_mssg'><img src='". DIR ."/images/needs/large.jpg'><span>You're not the admin of this group to edit.</span></div>";
  } else { ?>

  <div class="srajkumar grp_srajkumar">

    <div class="c_g_div grp_c_grp inst">
      <span>To change the avatar hover over the avatar.</span><br>
      <span>You can also add members to this group.</span>
      <div class="grp_c_we">
        <a href="<?php echo DIR; ?>/groups/<?php echo $grp; ?>?ask=grp_add_members" class="sec_btn">Add</a>
      </div>
    </div>

    <div class="c_g_div inst">
      <span>Make some other member the admin of this group.</span><br>
      <span>But you will no longer be the admin of this group.</span>
      <div class="grp_c_we">
        <a href="#" class="sec_btn make_grp_admin">Make admin</a>
      </div>
    </div>

  </div>

  <div class="prajkumar a_m_prajkumar">

    <div class="grp_edit inst" data-grp='<?php echo $grp; ?>'>
      <div class="g_e_name">
        <span class="g_e_span">Group name</span>
        <input type="text" name="" value="<?php echo $groups->GETgrp($grp, "grp_name"); ?>" placeholder="Group name.." spellcheck="false" autofocus>
      </div>
      <div class="g_e_bio">
        <span class="g_e_span">Group bio</span>
        <textarea name="g_e_ta" placeholder="Group bio.." spellcheck="false"><?php echo $groups->GETgrp($grp, "grp_bio"); ?></textarea>
      </div>
      <div class="g_e_pri">
        <input type="checkbox" name="grp_private" value="" class="inst_checkbox" id="grp_private" <?php if($groups->GETgrp($grp, "grp_privacy") == "private"){echo "checked";} ?>>
        <label for="grp_private">Private group</label>
        <span class="g_e_p_info">Private: Only members can interact with group</span>
      </div>
      <div class="g_e_save">
        <a href="#" class="sec_btn g_e_save_btn">Update</a>
      </div>
    </div>

  </div>

  <?php } ?>

</div>

<script type="text/javascript">
  $(function(){
    $('.g_e_name > input').focus();
    $('.grp_edit').editGrp();
    $('.make_grp_admin').changeGrpAdmin();
  });
</script>
