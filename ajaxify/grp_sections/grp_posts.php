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

  <div class="srajkumar grp_srajkumar">

    <div class="recomm_teaser inst grp_r_t">
      <div class="header_of_divs">
        <span>Groups's bio</span>
      </div>
      <div class='grp_r_t_main'>
        <span><?php echo $groups->GETgrp($grp, "grp_bio"); ?></span>
        <?php if($groups->isGrpAdmin($grp, $session)){ ?>
          <a href="<?php echo DIR; ?>/groups/<?php echo $grp; ?>?ask=grp_edit" class="sec_btn">Not satisified</a>
        <?php } ?>
    </div>
    </div>

    <div class="grp_mem_teaser inst">
      <div class="g_m_t header_of_divs">
        <span>Newest members</span>
      </div>
      <div class="g_m_imgs">
        <?php $groups->newestMembers($grp); ?>
        <div class="g_m_b">
          <a href="<?php echo DIR; ?>/groups/<?php echo $grp; ?>?ask=grp_members" class="sec_btn">View all</a>
        </div>
      </div>
    </div>

    <?php if($groups->mutualGrpMemCount($grp) != 0){ ?>
    <div class="grp_mem_teaser inst">
      <div class="g_m_t header_of_divs">
        <span>Members you know</span>
      </div>
      <div class="g_m_imgs">
        <?php $groups->mutualGrpMembers($grp); ?>
        <div class="g_m_b">
          <a href="<?php echo DIR; ?>/groups/<?php echo $grp; ?>?ask=grp_members" class="sec_btn">View all</a>
        </div>
      </div>
    </div>
    <?php } ?>

    <!-- <div class="c_g_div grp_c_grp inst">
      <span>You can create a public or private group of your interest with people you know by clicking on the options.</span>
    </div> -->

    <div class="c_g_div inst">
      <span>You can create a public or private group of your interest with people you know by clicking on the options.</span>
      <div class="grp_c_we">
        <a href="<?php echo DIR; ?>/explore?ask=exp_groups" class="sec_btn grp_post_c_grp">Create</a>
      </div>
    </div>

    <div class="c_g_div inst">
      <span>Explore more groups from all around Instagram.</span>
      <div class="grp_c_we">
        <a href="<?php echo DIR; ?>/explore?ask=exp_groups" class="sec_btn">Explore</a>
      </div>
    </div>

  </div>

  <div class="prajkumar a_m_prajkumar">

    <?php if($groups->memberOrNot($grp, $session)){ ?>
    <div class="post_it inst">
      <img src="<?php echo DIR."/".$avatar->DisplayAvatar($session); ?>" alt="<?php echo $universal->getUsernameFromSession(); ?>'s avatar">
      <div class="post_teaser">
        <span class="p_whats_new grp_whats_new" data-grp='<?php echo $grp; ?>' data-when='group'>What's new with you, @<?php echo $universal->nameShortener($universal->getUsernameFromSession(), 20); ?>? #cool</span>
        <span data-description="More"><i class="material-icons">expand_more</i></span>
      </div>
    </div>
    <div class="post_extra grp_post_extra">
      <div class="p_img_div">
        <form id="p_img_form" class="p_img_form" action="" method="post" enctype="multipart/form-data">
          <input type="file" name="p_img_file" value="" id="p_img_file" class="grp_img_file">
          <label for="p_img_file">Image</label>
        </form>
      </div>
      <div class="p_vid_div">
        <form class="p_vid_form" action="" method="post" enctype="multipart/form-data">
          <input type="file" name="p_vid_file" value="" id="p_vid_file" class="grp_vid_file">
          <label for="p_vid_file">Video</label>
        </form>
      </div>
      <div class="p_doc_div">
        <form class="p_doc_form" action="" method="post" enctype="multipart/form-data">
          <input type="file" name="p_doc_file" value="" id="p_doc_file" class="grp_doc_file">
          <label for="p_doc_file">Document</label>
        </form>
      </div>
      <div class="p_u_link p_grp_link">
        <span>Link</span>
      </div>
      <div class="p_u_map p_grp_map">
        <span>Location</span>
      </div>
    </div>
    <div class="grp_useless"></div>
    <?php } ?>

    <?php $groups->getGrpPost($grp, "direct", "0"); ?>

  </div>

</div>

<script type="text/javascript">
  $(function(){
    //Toggle Extra Post Toggle
    $('.post_it span:last-of-type').postExtraToggle();

    $('.post_it img').on('click', function(e){
      var username = $('.user_info').data('username');
      window.location.href = DIR+"/profile/"+username;
    });

    $('.post_teaser span:last-of-type').description({
      extraTop: -5
    });

    // POSTING
    //TEXT POST
    $('.grp_whats_new').textPost({when: "group"});
    $('.grp_img_file').imagePost({when: "group"});
    $('.grp_vid_file').videoPost({when: "group"});
    $('.p_grp_link').linkPost({when: "group"});
    $('.p_grp_map').locationPost({when: "group"});
    $('.grp_doc_file').docPost({when: "group"});

    // FOR POST OPTIONS
    $('.posts').ToggleMenu({
      btn: $('.exp_p_menu'),
      menu: $('.p_options')
    });

    //callig the hide and show plugin
    $('.posts').postHideAndShow();

    //COMMENT HEIGHT TOGGLE
    $('.p_cit_area > textarea').commentToggle();

    //Autosize of textarea
    $('.p_cit_area textarea').addClass('textarea_d_height');
    autosize($('textarea'));
    //hover over description for post
    $('.p_cit_more, .p_do > div > span, .c_sticker').description();
    $('.p_cit_area span').description({
      extraTop: 5
    });
    // $('.p_did > span').description({
    //   text: "innerHTML"
    // });
    //calling videoControls plugin
    $('video').videoControls();

    //calling audioControls plugin
    $('.p_aud').audioControls();
    $('.display_middle').perfectScrollbar();
    // POST LIKE
    $('.p_like').postLike();

    // POST UNLIKE
    $('.p_unlike').postUnlike();

    // POST BOOKMARK
    $('.p_bookmark').postBookmark();

    // POST UNBOOKMARK
    $('.p_unbookmark').postUnbookmark();

    // POST SHARE
    $('.p_send').postShare();

    // POST COMMENT
    $('.comment_teaser').postComment();
    $('.p_comm_file_teaser').imageComment({refresh: "no"});
    $('.c_sticker_trailer').sticker({
      when: "comment",
    });

    $('.p_cit_area > textarea').on('click', function(e){
      $(this).parent().siblings().filter('.p_cit_tool').css('display', 'none');
    }).on('blur', function(e){
      $(this).parent().siblings().filter('.p_cit_tool').css('display', 'inline-block');
    });

    // POST LIKERS
    $('.likes').likes();

    // POST TAGGERS
    $('.p_tags').taggers();

    // POST SHARERS
    $('.p_h_opt > .p_comm').shares();
    $('.untag').untag();
    $('.delete_post').deletePost();
    $('.p_img').imageShow({info: "yes"});
    $('.unshare').unshare();
    $('.un__share').removeShare();
    $('.simple_unfollow').simpleUnfollow();
    $('.mutual_links').description({extraTop: -20});

    $('.p_comments').on('click', function(e){
      var post = $(this).parent().data('postid');
      window.location.href = DIR+"/view_post/"+post;
    });

    $('.post_end').on('click', function(e){
      $('html, body').animate({scrollTop: 0}, 450);
    });

    $('.block').block();
    $('.p_copy_link').copyPostLink();
    $('.edit_post').editPost();
    $('.follow').follow({ update: true });
    $('.unfollow').unfollow({ update: true });
    $('.home_recomm').HomeSuggestions();
    if ($('.recomm_main').children().length == 0) {
      $('.recomm_main').html("<div class='home_last_mssg suggest_last_mssg'><img src='"+DIR+"/images/needs/large.jpg'></div>");
    }
    $('.recomm_t').recommend();

    $('.g_m_imgs > img')
      .description({extraTop: -20})
      .on('click', function(e){
      var data = $(this).data('user');
      window.location.href = DIR+"/profile/"+data;
    });

    $('.grp_post_c_grp').createGroup();

    $('.load_more_text').load_more_of_post({ type: "text" });
    $('.load_more_not_text').load_more_of_post({ type: "not_text" });

    // grpFeeds();

    $(window).commonGrpFeeds({when: "post"});

  });
</script>
