<div class="stickers model-shadow">
  <div class="sti_top">
    <span class="sti_heading">Choose a sticker</span>
  </div>
  <div class="sti_main">
    <div class="sti_con">
      <?php
        $glob = glob("images/stickers/*");
        foreach ($glob as $value) {
          echo "<img src='". DIR ."/$value' class='sti_img'>";
        }
      ?>
    </div>
  </div>
  <div class="sti_bottom">
    <input type="hidden" name="" value="" class='sti_hidden'>
    <span class="sticker_mssg"></span>
    <a href="#" class="sec_btn sti_cancel">Cancel</a>
    <a href="#" class="pri_btn sti_done">Choose</a>
  </div>
</div>
