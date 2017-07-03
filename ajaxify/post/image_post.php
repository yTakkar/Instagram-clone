<?php
  include '../../config/declare.php';
  include_once '../../config/class/needy_class.php';
  include '../../config/class/universal.class.php';
  include '../../config/class/avatar.class.php';

  $session = $_SESSION['id'];
  $universal = new universal;
  $avatar = new Avatar;
?>

  <div class="add_filters">
    <div class="add_filters_main">
      <div class="filter_div" data-filter='normal'>
        <img class='normal' src="<?php echo DIR; ?>/images/avatars/3995428-world-famous-landmark-series-taj-mahal-agra-india.jpg" alt="">
        <span>Normal</span>
      </div>
      <div class="filter_div" data-filter='grayscale'>
        <img class='grayscale' src="<?php echo DIR; ?>/images/avatars/3995428-world-famous-landmark-series-taj-mahal-agra-india.jpg" alt="">
        <span>Grayscale</span>
      </div>
      <div class="filter_div" data-filter='sepia'>
        <img class='sepia' src="<?php echo DIR; ?>/images/avatars/3995428-world-famous-landmark-series-taj-mahal-agra-india.jpg" alt="">
        <span>Sepia</span>
      </div>
      <div class="filter_div" data-filter='saturation'>
        <img class='saturation' src="<?php echo DIR; ?>/images/avatars/3995428-world-famous-landmark-series-taj-mahal-agra-india.jpg" alt="">
        <span>Saturation</span>
      </div>
      <div class="filter_div" data-filter='hue-rotate'>
        <img class='hue-rotate' src="<?php echo DIR; ?>/images/avatars/3995428-world-famous-landmark-series-taj-mahal-agra-india.jpg" alt="">
        <span>Hue-rotate</span>
      </div>
      <div class="filter_div" data-filter='invert'>
        <img class='invert' src="<?php echo DIR; ?>/images/avatars/3995428-world-famous-landmark-series-taj-mahal-agra-india.jpg" alt="">
        <span>Invert</span>
      </div>
      <div class="filter_div" data-filter='opacity'>
        <img class='opacity' src="<?php echo DIR; ?>/images/avatars/3995428-world-famous-landmark-series-taj-mahal-agra-india.jpg" alt="">
        <span>Opacity</span>
      </div>
      <div class="filter_div" data-filter='brightness'>
        <img class='brightness' src="<?php echo DIR; ?>/images/avatars/3995428-world-famous-landmark-series-taj-mahal-agra-india.jpg" alt="">
        <span>Brightness</span>
      </div>
      <div class="filter_div" data-filter='contrast'>
        <img class='contrast' src="<?php echo DIR; ?>/images/avatars/3995428-world-famous-landmark-series-taj-mahal-agra-india.jpg" alt="">
        <span>Contrast</span>
      </div>
      <div class="filter_div" data-filter='blur'>
        <img class='blur' src="<?php echo DIR; ?>/images/avatars/3995428-world-famous-landmark-series-taj-mahal-agra-india.jpg" alt="">
        <span>Blur</span>
      </div>
      <div class="filter_div" data-filter='tint'>
        <img class='tint' src="<?php echo DIR; ?>/images/avatars/3995428-world-famous-landmark-series-taj-mahal-agra-india.jpg" alt="">
        <span>Tint</span>
      </div>
      <div class="filter_div" data-filter='mix'>
        <img class='mix' src="<?php echo DIR; ?>/images/avatars/3995428-world-famous-landmark-series-taj-mahal-agra-india.jpg" alt="">
        <span>Mix</span>
      </div>
      <div class="filter_div" data-filter='inkwell'>
        <img class='inkwell' src="<?php echo DIR; ?>/images/avatars/3995428-world-famous-landmark-series-taj-mahal-agra-india.jpg" alt="">
        <span>Inkwell</span>
      </div>
    </div>
  </div>

  <div class="i_p_top p_top">
    <div class="i_p_info p_info">
      <img src="<?php echo DIR; ?>/<?php echo $avatar->DisplayAvatar($session); ?>" alt="s's avatar">
      <span><?php echo $universal->GETsDetails($session, "username"); ?></span>
      <span class="loc_text"></span>
    </div>
    <div class="i_p_opt p_opt">
      <span><i class="material-icons">expand_more</i></span>
    </div>
  </div>

  <div class="i_p_main p_main">
    <div class="i_p_ta">
      <textarea name="name" placeholder="What's new with you, @<?php echo $universal->GETsDetails($session, "username"); ?>? #cool" spellcheck="false" class="t_p_ta"></textarea>
    </div>
    <div class="i_p_img">
      <img src="<?php echo DIR; ?>/images/needs/17455538fd839328f5606d284d0c360d.jpg" alt="">
    </div>
  </div>

  <div class="p_tagging">
    <div class="p_tag_ins_help"></div>
  </div>

  <div class="p_add_taggings">
    <input type="text" name="" value="" placeholder="Search to tag" spellcheck="false">
    <input type="hidden" name="" value="" class="p_hidden">
    <input type="hidden" name="" value="" class="font_value">
    <input type="hidden" name="" value="" class="loc_value">
    <input type="hidden" name="" value="" class="filter_value">
  </div>

  <div class="p_tagging_list">
    <div class="p_tagging_wrapper">
      <ul class="p_tagging_ul">
      </ul>
    </div>
  </div>

  <div class="t_p_bottom p_bottom">
    <div class="t_p_tag p_tag">
      <span class="emoji_add" data-description="Add emojis"><i class="material-icons">sentiment_very_satisfied</i></span>
      <!-- <span class="tag_add" data-description="Tag people"><i class="material-icons">person_add</i></span> -->
      <span class="tag_add" data-description="Tag people"><i class="material-icons">loyalty</i></span>
      <span class="font_add" data-description="Change font size"><i class="material-icons">format_size</i></span>
      <span class="loc_add" data-description="Add location"><i class="material-icons">location_on</i></span>
    </div>
    <div class="font_sizes">
      <ul>
        <li data-size="14" class="one">14px</li>
        <li data-size="15" class="two">15px</li>
        <li data-size="16" class="three">16px</li>
        <li data-size="17" class="four">17px</li>
        <li data-size="18" class="five">18px</li>
        <li data-size="19" class="six">19px</li>
        <li data-size="20" class="seven">20px</li>
      </ul>
    </div>
    <div class="space"></div>
    <div class="t_p_act p_act">
      <a href="#" class="sec_btn p_cancel">Cancel</a>
      <a href="#" class="p_post">Post</a>
    </div>
  </div>
