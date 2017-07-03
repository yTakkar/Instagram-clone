<div class="pro_banner inst">

  <div class="pro_top">
    <div class="pro_more">
      <span class="pro_more_horiz" data-description="More options"><i class="material-icons">more_horiz</i></span>
    </div>
    <div class="options pro_banner_options">
      <ul>
        <?php if($universal->isLoggedIn()){ ?>
        <li><a href="#" class="c_g">Create group</a></li>
        <li><a href="#" class="inv_g" data-grp='<?php echo $grp; ?>'>Invite to group</a></li>
        <?php if($groups->isGrpAdmin($grp, $session)){ ?>
          <li><a href="<?php echo DIR; ?>/groups/<?php echo $grp; ?>?ask=grp_edit">Edit group</a></li>
          <li><a href="#" class='mk_admin_banner'>Make admin</a></li>
          <li><a href="#" class="d_g">Delete group</a></li>
        <?php } ?>
        <?php } else { ?>
          <li><a href="<?php echo DIR; ?>/login?next=<?php echo DIR; ?>/groups/<?php echo $grp; ?>" class="">Login</a></li>
        <?php } ?>
        <li><a href="#" class="p_copy_link" data-link='<?php echo $universal->urlChecker(DIR); ?>/groups/<?php echo $grp; ?>' >Copy group link</a></li>
      </ul>
    </div>
    <div class="pro_ff" data-grp='<?php echo $grp; ?>'>
        <?php
          if($universal->isLoggedIn()){
          if($groups->isGrpAdmin($grp, $session) == false){
          if($groups->memberOrNot($grp, $session)){
        ?>
          <a href="#" class="pri_btn leave_grp pro_leave_grp">Leave group</a>
        <?php } else if($groups->memberOrNot($grp, $session) == false){ ?>
          <a href="#" class="pri_btn join_grp pro_join_grp">Join group</a>
        <?php } ?>
      <?php } else if($groups->isGrpAdmin($grp, $session)) { ?>
        <a href="<?php echo DIR; ?>/groups/3?ask=grp_edit" class="pri_btn">Edit group</a>
      <?php } ?>
      <?php } else { ?>
        <a href="<?php echo DIR; ?>/login?next=<?php echo DIR; ?>/groups/<?php echo $grp; ?>" class="pri_btn">Login</a>
      <?php } ?>

      <?php
        // if($universal->isLoggedIn()){
        //   if($groups->isGrpAdmin($grp, $session) == false){
        //     if($groups->memberOrNot($grp, $session)){
        //       echo "<a href='#' class='pri_btn leave_grp pro_leave_grp'>Leave group</a>";
        //     } else if ($groups->memberOrNot($grp, $session) == false) {
        //       echo "<a href='#' class='pri_btn join_grp pro_join_grp'>Join group</a>";
        //     }
        //   } else if ($groups->isGrpAdmin($grp, $session)) {
        //     echo "<a href='". DIR ."/groups/3?ask=grp_edit' class='pri_btn'>Edit group</a>";
        //   }
        // } else {
        //   echo "<a href='". DIR ."/login' class='pri_btn'>Login</a>";
        // }
      ?>

    </div>

  </div>

  <div class="pro_avatar">
    <img src="<?php echo $groups->grpAvatar($grp); ?>">
    <div class="pro_avatar_ch_teaser">
      <span class="view_grp_avatar">View</span>
      <?php if($universal->isLoggedIn()){ ?>
      <?php if($groups->isGrpAdmin($grp, $session)){ ?>
        <span class="change_grp_ava" data-grp='<?php echo $grp; ?>'>Change</span>
      <?php } ?>
      <?php } ?>
    </div>
  </div>

  <div class="pro_info">
    <div class="pro_username">
      <a href="<?php echo DIR; ?>/groups/<?php echo $grp; ?>" class="username"><?php echo $groups->GETgrp($grp, "grp_name"); ?></a>
    </div>
    <div class="pro_name">
      <span>
        <?php
          $pri = $groups->GETgrp($grp, "grp_privacy");
          if($pri == "private"){
            echo "<i class='fa fa-lock' aria-hidden='true'></i> Private";
          } else if ($pri == "public") {
            echo "<i class='fa fa-globe' aria-hidden='true'></i> Public";
          }
        ?>
        group</span>
    </div>
  </div>

</div>
