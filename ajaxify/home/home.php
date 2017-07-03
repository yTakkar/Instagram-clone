<div class="senapati">

  <div class="prajkumar">

    <div class="post_it inst">
      <img src="<?php echo DIR."/".$avatar->SESSIONsAvatar(); ?>" alt="<?php echo $universal->getUsernameFromSession(); ?>'s avatar">
      <div class="post_teaser">
        <span class="p_whats_new user_whats_new" data-when='user'>What's new with you, @<?php echo $universal->nameShortener($universal->getUsernameFromSession(), 20); ?>? #cool</span>
        <span data-description="More"><i class="material-icons">expand_more</i></span>
      </div>
    </div>
    <div class="post_extra">
      <div class="p_img_div">
        <form id="p_img_form" class="p_img_form" action="" method="post" enctype="multipart/form-data">
          <input type="file" name="p_img_file" value="" id="p_img_file" class="user_img_file">
          <label for="p_img_file">Image</label>
        </form>
      </div>
      <div class="p_vid_div">
        <form class="p_vid_form" action="" method="post" enctype="multipart/form-data">
          <input type="file" name="p_vid_file" value="" id="p_vid_file" class="user_vid_file">
          <label for="p_vid_file">Video</label>
        </form>
      </div>
      <div class="p_u_link p_user_link">
        <span>Link</span>
      </div>
      <div class="p_aud_div">
        <form class="p_aud_form" action="" method="post" enctype="multipart/form-data">
          <input type="file" name="p_aud_file" value="" id="p_aud_file" class="user_aud_file">
          <label for="p_aud_file">Audio</label>
        </form>
      </div>
      <div class="p_doc_div">
        <form class="p_doc_form" action="" method="post" enctype="multipart/form-data">
          <input type="file" name="p_doc_file" value="" id="p_doc_file" class="user_doc_file">
          <label for="p_doc_file">Document</label>
        </form>
      </div>
      <div class="p_u_map p_user_map">
        <span>Location</span>
      </div>
    </div>

    <div class="posts_div">
      <div class="home_notify inst">
        <div class="home_notify_img">
          <img src="<?php echo DIR; ?>/images/needs/large.jpg" alt="">
        </div>
        <div class="home_notify_info">
          <span>Check your post on profile page</span>
          <a href="#" class="pri_btn">Check out</a>
        </div>
      </div>

      <?php echo $post->getHomePost("direct", "nolimit", "0"); ?>

    </div>

    <!-- <div class='post_end'>
      <span>No more updates for you</span>
    </div> -->

  </div>

  <div class="srajkumar home_rajkumar">

    <div class="recomm home_recomm inst">
      <div class="recomm_top header_of_divs">
        <span>Suggested</span>
        <a href="#" class="recomm_refresh" data-description='refresh'><i class="fa fa-refresh" aria-hidden="true"></i></a>
        <a href="<?php echo DIR; ?>/explore?ask=exp_people" class="recomm_all" data-description='view all'><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
      </div>
      <div class="recomm_main">
        <?php $suggestions->HomeSuggestions("direct"); ?>
      </div>
    </div>

    <?php $hashtag->popularHashtags(); ?>

    <div class="c_g_div inst">
      <span>Create public or private group of your interest with people you know.</span>
      <a href="#" class="sec_btn c_g">Create group</a>
    </div>

  </div>

</div>
