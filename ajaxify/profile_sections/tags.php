<?php include '../../config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include '../../config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $universal = new universal;
  $tags = new tags;
  $avatar = new Avatar;
  $follow = new follow_system;
  $general = new general;
  $post = new post;
  $suggestions = new suggestion;
?>

<?php
  $get_id = $universal->getIdFromGet($_GET['u']);
  if (isset($_SESSION['id'])) {
    $session_id = $_SESSION['id'];
  }
?>

<div class="senapati pro_senapati">
  <div class="srajkumar tag_srajkumar">

    <?php include 'sugg.php'; ?>

    <?php if($universal->MeOrNot($get_id) == false){ ?>
      <div class="recomm_teaser padder inst">
        <span>Wanna recommend or invite <?php echo $universal->GETsDetails($get_id, "username"); ?> to someone, so they can get to know about <?php echo $universal->GETsDetails($get_id, "username") ?>.</span>
        <a href="#" class="sec_btn recomm_t">Recommend</a>
      </div>
    <?php } ?>

    <div class="c_g_div padder inst">
      <span>Create public or private group of your interest with people you know.</span>
      <a href="#" class="sec_btn c_g">Create group</a>
    </div>

  </div>

  <div class="prajkumar">
    <?php echo  $post->getTaggedPost($get_id, "direct", "0"); ?>
  </div>
</div>

<script type="text/javascript">
LinkIndicator('profile');
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
  $('.unshare').unshare();
  $('.un__share').removeShare();
  $('.p_img').imageShow({info: "yes"});
  $('.delete_post').deletePost();
  $('.simple_unfollow').simpleUnfollow();

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
  $('.recomm_refresh, .recomm_all').description({ extraTop: 5 });
  $('.recomm_t').recommend();
  $('.c_g').createGroup();

  $('.load_more_text').load_more_of_post({ type: "text" });
  $('.load_more_not_text').load_more_of_post({ type: "not_text" });

  // tagFeeds();
  $(window).commonUserFeeds({ when: "tag" });

</script>
