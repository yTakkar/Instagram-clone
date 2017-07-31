<div class="edit_profile">

  <div class="edit_info">
    <img src="<?php echo DIR."/".$avatar->GETsAvatar($session); ?>" alt="">
    <span>@<?php echo $universal->getUsernameFromSession(); ?></span>
  </div>

  <div class="edit_main">
    <div class="edit_un_div">
      <span class='edit_span'>Username</span>
      <input type="text" name="edit_un_text" value="<?php echo $universal->GETsDetails($session, "username"); ?>" class="edit_un_text" placeholder="Username" spellcheck="false" autofocus>
    </div>
    <div class="edit_fn_div edit_small">
      <span class='edit_span'>Firstname</span>
      <input type="text" name="edit_fn_text" value="<?php echo $universal->GETsDetails($session, "firstname"); ?>" class="edit_fn_text" placeholder="Firstname" spellcheck="false">
    </div>
    <div class="edit_sn_div edit_small">
      <span class='edit_span'>Surname</span>
      <input type="text" name="edit_sn_text" value="<?php echo $universal->GETsDetails($session, "surname"); ?>" class="edit_sn_text" placeholder="Surname" spellcheck="false">
    </div>
    <div class="edit_bio_div">
      <span class='edit_span'>Bio</span>
      <textarea name="edit_ta" class="edit_ta" placeholder="Bio" spellcheck="false"><?php echo $universal->GETsDetails($session, "bio"); ?></textarea>
    </div>
    <div class="edit_update">
      <span data-description="Add emojis to bio"><i class="material-icons">sentiment_very_satisfied</i></span>
      <a href="#" class="pri_btn edit_update_a">Update profile</a>
      <?php 
        if(!$universal->e_verified($_SESSION['id'])){
          echo "<a href='#' class='sec_btn resend_vl'>Resend verification link</a>";
        }
      ?>
    </div>
  </div>

  <div class="edit_tags">
    <div class="edit_sm_div">
      <span class='edit_span'>Connections</span>
      <input type="text" name="edit_em_instagram" value="<?php echo $universal->GETsDetails($session, "instagram"); ?>" class="edit_em_instagram sm" placeholder="Instagram" spellcheck="false">
      <input type="text" name="edit_em_youtube" value="<?php echo $universal->GETsDetails($session, "youtube"); ?>" class="edit_em_youtube sm" placeholder="Youtube" spellcheck="false">
      <input type="text" name="edit_em_facebook" value="<?php echo $universal->GETsDetails($session, "facebook"); ?>" class="edit_em_facebook sm" placeholder="Facebook" spellcheck="false">
      <input type="text" name="edit_em_twitter" value="<?php echo $universal->GETsDetails($session, "twitter"); ?>" class="edit_em_twitter sm" placeholder="Twitter" spellcheck="false">
      <input type="text" name="edit_em_website" value="<?php echo $universal->GETsDetails($session, "website"); ?>" class="edit_em_website sm" placeholder="Website" spellcheck="false">
      <input type="text" name="edit_em_mobile" value="<?php echo $universal->GETsDetails($session, "mobile"); ?>" class="edit_em_mobile" placeholder="Phone Number" spellcheck="false">
    </div>
    <div class="edit_tags_info">
      <span>Edit tags (click tags to remove)</span>
      <input type="hidden" class="tags_hidden" name="tags_hidden" value="">
    </div>
    <div class="add_tag">
      <input type="text" name="add_tag_text" value="" class="add_tag_text" placeholder="Add a tag" spellcheck="false">
      <a href="#" class="sec_btn add_tag_add">Add</a>
    </div>
    <div class="tags_all">
      <div class="insert_helper"></div>
      <!-- <span class="t_a_tag">universal</span> -->
      <?php $tags->getTagsEdit($session); ?>
    </div>
  </div>
</div>
