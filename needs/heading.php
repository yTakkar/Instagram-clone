<body>
  <div class="header">
    <div class="logo">
      <a href="<?php echo DIR; ?>"><img src="<?php echo DIR; ?>/images/needs/glyph-instagram.jpg" alt="Instagram"></a>
    </div>
    <div class="search_box">
      <?php if($universal->isLoggedIn()){ ?>
      <input type="text" name="" placeholder="Search Instagram" spellcheck="false" autocomplete="off" class="search" autofocus>
      <span class="search_icon">
        <i class="fa fa-search" aria-hidden="true"></i>
      </span>
      <?php } ?>
    </div>
    <div class="header_right">
      <?php if($universal->isLoggedIn()){ ?>
      <a href="<?php echo DIR; ?>/notifications" class="notification">
        <span class="notification_span nav_icon">
          <?php
          if($noti->unreadCount() > 0){
            echo "<i class='material-icons'>notifications_active</i>";
           } else {
            echo "<i class='material-icons'>notifications_none</i>";
          }
          ?>
        </span>
        <span class="links_span">Notifications</span>
      </a>
      <a href="<?php echo DIR; ?>/profile/<?php echo $universal->getUsernameFromSession(); ?>" class="sp">
        <img src="<?php echo DIR."/".$avatar->SESSIONsAvatar(); ?>" alt="<?php echo $universal->getUsernameFromSession(); ?>'s avatar" class="sp_img">
        <span class="sp_span"><?php echo $universal->nameShortener($universal->getUsernameFromSession(), 20) ?></span>
      </a>
      <?php } ?>
      <span class="show_more">
        <i class="material-icons">expand_more</i>
      </span>
    </div>
    <div class="sp_options options">
      <ul class="o_ul">
        <?php if ($universal->isLoggedIn() == true) { ?>
          <li class="o_li"><a href="<?php echo DIR; ?>/settings" class="o_a" alt="Settings">Settings</a></li>
          <li class="o_li"><a href="<?php echo DIR; ?>/edit" class="o_a" alt="Edit">Edit</a></li>
        <?php } ?>
        <li class="o_li"><a href="<?php echo DIR; ?>/help" class="o_a" alt="Help">Help</a></li>
        <li class="o_li"><a href="<?php echo DIR; ?>/about">About</a></li>
        <li class="o_li"><a href="<?php echo DIR; ?>/developer">Developer</a></li>
        <?php if ($universal->isLoggedIn() == true) { ?>
          <li class="o_li o_divider"><hr class="menu_divider"></li>
          <li class="o_li"><a href="<?php echo DIR; ?>/logout" class="o_a" alt="Settings">Logout</a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
