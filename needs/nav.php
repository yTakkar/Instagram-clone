<div class="m_n_wrapper">
  <div class="m_n">
    <ul class="m_n_ul">
      <?php if($universal->isLoggedIn()){ ?>
      <li class="m_n_li">
        <a href="<?php echo DIR; ?>/profile/<?php echo $universal->getUsernameFromSession(); ?>" class="m_n_a profile" data-link="profile">
          <span class="m_n_text">@<?php echo $universal->nameShortener($universal->getUsernameFromSession(), 20); ?></span>
          <span class="m_n_new"></span>
        </a>
      </li>
      <li class="m_n_li">
        <a href="<?php echo DIR; ?>" class="m_n_a index" data-link="index">
          <!-- <span class="m_n_icon"><i class="material-icons">home</i></span> -->
          <span class="m_n_text">Home</span>
          <span class="m_n_new"></span>
        </a>
      </li>
      <li class="m_n_li">
        <a href="<?php echo DIR; ?>/explore" class="m_n_a explore" data-link="explore">
          <!-- <span class="m_n_icon"><i class="material-icons">toys</i></span> -->
          <span class="m_n_text">Explore</span>
          <span class="m_n_new"></span>
        </a>
      </li>
      <li class="m_n_li">
        <a href="<?php echo DIR; ?>/notifications" class="m_n_a notifications" data-link="notifications">
          <!-- <span class="m_n_icon"><i class="material-icons">notifications</i></span> -->
          <span class="m_n_text">Notifications</span>
          <span class="m_n_new"><?php echo $noti->unreadCount(); ?></span>
        </a>
      </li>
      <li class="m_n_li">
        <a href="<?php echo DIR; ?>/messages" class="m_n_a messages" data-link="messages">
          <!-- <span class="m_n_icon"><i class="material-icons">message</i></span> -->
          <span class="m_n_text">Messages</span>
          <span class="m_n_new"><?php echo $message->getAllUnreadMssg(); ?></span>
        </a>
      </li>
      <li class="m_n_li">
        <a href="<?php echo DIR; ?>/profile/<?php echo $universal->getUsernameFromSession(); ?>?ask=bookmarks" class="m_n_a bookmarks" data-link="bookmarks">
          <!-- <span class="m_n_icon"><i class="material-icons">message</i></span> -->
          <span class="m_n_text">Bookmarks</span>
          <span class="m_n_new"></span>
        </a>
      </li>
      <li class="m_n_li">
        <a href="<?php echo DIR; ?>/profile/<?php echo $universal->getUsernameFromSession(); ?>?ask=favourites" class="m_n_a favourites" data-link="favourites">
          <span class="m_n_text">Favourites</span>
          <span class="m_n_new"></span>
        </a>
      </li>
      <li class="m_n_li">
        <a href="<?php echo DIR; ?>/profile/<?php echo $universal->getUsernameFromSession(); ?>?ask=groups" class="m_n_a groups" data-link="groups">
          <!-- <span class="m_n_icon"><i class="material-icons">message</i></span> -->
          <span class="m_n_text">Groups</span>
          <span class="m_n_new"></span>
        </a>
      </li>
      <li class="m_n_li">
        <a href="<?php echo DIR; ?>/profile/<?php echo $universal->getUsernameFromSession(); ?>?ask=photos" class="m_n_a photos" data-link="photos">
          <!-- <span class="m_n_icon"><i class="material-icons">message</i></span> -->
          <span class="m_n_text">Photos</span>
          <span class="m_n_new"></span>
        </a>
      </li>
      <li class="m_n_li">
        <a href="<?php echo DIR; ?>/profile/<?php echo $universal->getUsernameFromSession(); ?>?ask=videos" class="m_n_a videos" data-link="videos">
          <!-- <span class="m_n_icon"><i class="material-icons">message</i></span> -->
          <span class="m_n_text">Videos</span>
          <span class="m_n_new"></span>
        </a>
      </li>
      <li class="m_n_li">
        <a href="<?php echo DIR; ?>/profile/<?php echo $universal->getUsernameFromSession(); ?>?ask=audios" class="m_n_a audios" data-link="audios">
          <!-- <span class="m_n_icon"><i class="material-icons">message</i></span> -->
          <span class="m_n_text">Audios</span>
          <span class="m_n_new"></span>
        </a>
      </li>
      <li class="m_n_li">
        <a href="<?php echo DIR; ?>/edit" class="m_n_a edit" data-link="edit">
          <!-- <span class="m_n_icon"><i class="material-icons">edit</i></span> -->
          <span class="m_n_text">Edit profile</span>
          <span class="m_n_new"></span>
        </a>
      </li>
      <li class="m_n_li">
        <a href="<?php echo DIR; ?>/settings" class="m_n_a settings" data-link="settings">
          <!-- <span class="m_n_icon"><i class="material-icons">settings_applications</i></span> -->
          <span class="m_n_text">Settings</span>
          <span class="m_n_new"></span>
        </a>
      </li>
      <?php } ?>
    </ul>
  </div>

  <div class="m_n_bottom">
    <ul>
      <?php if ($universal->isLoggedIn() == true) { ?>
      <li><a href="<?php echo DIR; ?>/logout">Logout</a></li>
      <li><a href="<?php echo DIR; ?>/help">Help</a></li>
      <li><a href="#" class=""><i class="material-icons">more_horiz</i></a></li>
      <?php } ?>
    </ul>
  </div>

  <div class="options nav_options">
    <ul>
      <li><a href="<?php echo DIR; ?>/about">About</a></li>
      <li><a href="<?php echo DIR; ?>/developer">Developer</a></li>
    </ul>
  </div>

</div>
