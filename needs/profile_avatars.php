<div class="pro_avatars model-shadow">
  <div class="unclickable"></div>
  <div class="pro_ava_top">
    <div class="pro_ava_info">
      <span>Change your avatar</span>
    </div>
    <span class="pro_ava_close"><i class="material-icons">close</i></span>
  </div>
  <div class="pro_ava_middle">
    <div class="pro_ava_content">
      <?php
        $images = glob('images/avatars/*');
        foreach ($images as $key => $value) {
          echo "<img src='". DIR ."/$value' class='pro_ava_avts'>";
        }
      ?>
    </div>
  </div>
  <div class="pro_ava_bottom">
    <form class="pro_ch_form" action="" method="post" enctype="multipart/form-data">
      <input type="file" name="pro_ch_ava" value="" id="pro_ch_ava" class="">
      <label for="pro_ch_ava" class="sec_btn">Upload avatar</label>
      <div class="pro_preview model-shadow">
        <div class="pro_pre_top">
          <div class="pro_pre_info">
            <span>Select this avatar</span>
          </div>
        </div>
        <div class="pro_pre_img">
          <img src="<?php echo DIR; ?>/images/needs/17455538fd839328f5606d284d0c360d.jpg" alt="">
        </div>
        <div class="pro_pre_bottom">
          <a href="#" class="sec_btn pro_pre_cancel">Cancel</a>
          <input type="submit" name="" value="Select" class="pri_btn pro_pre_select">
        </div>
      </div>
    </form>
    <div class="pro_ava_bottom_act">
      <a href="#" class="sec_btn">Cancel</a>
      <a href="#" class="pri_btn">Apply</a>
    </div>
  </div>
</div>

<div class="pro_crop model-shadow">
  <div class="pro_crop_img">
    <img src="<?php echo DIR; ?>/images/needs/17455538fd839328f5606d284d0c360d.jpg" alt="" class="crop_img">
    <div class="pro_crop_tool"></div>
  </div>
  <div class="pro_crop_act">
    <a href="#" class="sec_btn pro_crop_cancel">Cancel</a>
    <a href="#" class="pri_btn pro_crop_done">Apply</a>
  </div>
</div>
