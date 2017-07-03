<div class="pro_banner inst">

  <div class="pro_top">
    <div class="pro_more">
      <span class="pro_more_horiz" data-description="More options"><i class="material-icons">more_horiz</i></span>
    </div>
    <div class="options pro_banner_options">
      <ul>
        <?php if($universal->isLoggedIn()){ ?>
        <?php if ($universal->MeOrNot($get_id) == false) { ?>
          <li><a href="#" data-getid="<?php echo $get_id; ?>" class="pro_recommend">Recommend</a></li>
          <?php if ($settings->isBlocked($get_id) == false) { ?>
            <li><a href="#" data-getid="<?php echo $get_id; ?>" data-username="<?php echo $universal->nameShortener($universal->GETsDetails($get_id, "username"), 20); ?>" class="block">Block</a></li>
          <?php } ?>
          <?php if ($fav->isFav($get_id) == false) { ?>
            <li><a href="#" data-getid="<?php echo $get_id; ?>" class="add_fav">Add to favourites</a></li>
          <?php } ?>
          <li><a href="<?php echo DIR; ?>/messages" class="">Message</a></li>
        <?php } ?>
        <?php if ($universal->MeOrNot($get_id) == true) { ?>
          <li><a href="<?php echo DIR; ?>/edit">Edit profile</a></li>
        <?php } ?>
        <?php } else { ?>
          <li><a href="<?php echo DIR; ?>/login?next=<?php echo DIR; ?>/profile/<?php echo $universal->GETsDetails($get_id, "username") ?>">Login</a></li>
        <?php } ?>
        <li><a href="#" class="p_copy_link" data-link='<?php echo $universal->urlChecker(DIR); ?>/profile/<?php echo $universal->GETsDetails($get_id, "username"); ?>' >Copy profile link</a></li>
      </ul>
    </div>
    <div class="pro_ff" data-getid="<?php echo $get_id; ?>">
      <?php
        if($universal->isLoggedIn()){
        if ($universal->MeOrNot($get_id) == false) {
        if ($follow->isFollowing($get_id)) {
      ?>
        <a href="#" class="pri_btn ff pro_unfollow unfollow">Unfollow</a>
      <?php } else if ($follow->isFollowing($get_id) == false) { ?>
        <a href="#" class="pri_btn ff pro_follow follow">Follow</a>
      <?php } ?>
      <?php } else { ?>
        <a href="<?php echo DIR; ?>/edit" class="pri_btn ff">Edit profile</a>
      <?php } ?>
      <?php } else { ?>
        <a href="<?php echo DIR; ?>/login?next=<?php echo DIR; ?>/profile/<?php echo $universal->GETsDetails($get_id, "username") ?>" class="pri_btn">Login</a>
      <?php } ?>
    </div>

  </div>

  <div class="pro_avatar">
    <img src="<?php echo DIR."/".$avatar->GETsAvatar($get_id); ?>" alt="<?php echo $universal->GETsDetails($get_id, "username") ?>'s avatar">
    <div class="pro_avatar_ch_teaser">
      <span class="view_avatar">View</span>
      <?php
        if ($universal->MeOrNot($get_id)) {
      ?>
      <span class="change_pro">Change</span>
      <?php } ?>
    </div>
  </div>

  <div class="pro_info">
    <div class="pro_username">
      <a href="<?php echo DIR; ?>/profile/<?php echo $universal->GETsDetails($get_id, "username"); ?>" class="username"><?php echo $universal->GETsDetails($get_id, 'username'); ?></a>
    </div>
    <div class="pro_name">
      <span><?php echo $universal->GETsDetails($get_id, "firstname") ?> <?php echo $universal->GETsDetails($get_id, "surname"); ?></span>
    </div>
    <div class="pro_bio">
      <span><?php echo $universal->GETsDetails($get_id, "bio"); ?></span>
    </div>
  </div>

  <div class="pro_exp_more">
    <span data-description="Tags"><i class="material-icons">expand_more</i></span>
  </div>

  <div class="pro_tags">
    <!-- <a href="#" class="tags">programmer</a> -->
    <?php
      if ($universal->isPrivate($get_id) == false) {
        $tags->get_tags($get_id);
      }
    ?>
  </div>

  <hr>

  <?php
    if($universal->isOnline($get_id)){
      echo "<span class='user_status'>online</span>";
    }
  ?>

  <div class="pro_bottom">
    <div class="pro_post">
      <span class="pro_hg"><?php echo $post->postCount($get_id); ?></span>
      <span class="pro_nhg">Posts</span>
    </div>
    <div class="<?php if($universal->isPrivate($get_id) == false && $universal->isLoggedIn()){ echo "pro_followers"; } ?>">
      <span class="pro_hg no_of_followers"><?php echo $follow->getFollowers($get_id); ?></span>
      <span class="pro_nhg">Followers</span>
    </div>
    <div class="<?php if($universal->isPrivate($get_id) == false && $universal->isLoggedIn()){ echo "pro_followings"; } ?>">
      <span class="pro_hg no_of_followings"><?php echo $follow->getFollowings($get_id); ?></span>
      <span class="pro_nhg">Following</span>
    </div>
    <?php if($universal->MeOrNot($get_id)){ ?>
    <div class="pro_recomm">
      <span class="pro_hg"><?php echo $recommend->getRecommends($get_id); ?></span>
      <span class="pro_nhg">Recommendations</span>
    </div>
    <?php } ?>
    <div class="pro_views">
      <span class="pro_hg"><?php echo $follow->getViewers($get_id); ?></span>
      <span class="pro_nhg">Profile views</span>
    </div>
    <?php if($universal->MeOrNot($get_id) == false){ ?>
    <div class="pro_fav">
      <span class="pro_hg"><?php echo $fav->noOfFavs($get_id); ?></span>
      <span class="pro_nhg">Favourites</span>
    </div>
    <?php } ?>
  </div>

</div>
