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

<?php
  $get_id = $universal->getIdFromGet($_GET['u']);
  $session_id = $_SESSION['id'];
?>

<div class="senapati pro_senapati">

  <div class="srajkumar">

    <div class="recomm inst">
      <div class="recomm_top header_of_divs">
        <span>Suggested</span>
        <a href="#" class="recomm_refresh" data-description='refresh'><i class="fa fa-refresh" aria-hidden="true"></i></a>
        <a href="<?php echo DIR; ?>/explore?ask=exp_people" class="recomm_all" data-description='view all'><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
      </div>
      <div class="recomm_main">
        <?php $suggestions->HomeSuggestions("ajax"); ?>
      </div>
    </div>

  </div>

  <div class="prajkumar">
    <?php $post->getBookmarksPost("direct", "0"); ?>
  </div>
</div>

<script type="text/javascript">
  LinkIndicator("bookmarks");
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

  $('.p_img').imageShow({info: "yes"});
  $('.untag').untag();
  $('.un__share').removeShare();
  $('.simple_unfollow').simpleUnfollow();
  $('.unshare').unshare();
  $('.simple_unfollow').simpleUnfollow();
  $('.delete_post').deletePost();
  $('.edit_post').editPost();
  $('.block').block();
  $('.p_copy_link').copyPostLink();

  $('.p_comments').on('click', function(e){
    var post = $(this).parent().data('postid');
    window.location.href = DIR+"/view_post/"+post;
  });

  $('.post_end').on('click', function(e){
    $('html, body').animate({scrollTop: 0}, 450);
  });

  $('.recomm').HomeSuggestions();
  $('.recomm_refresh, .recomm_all, .view_all_yk').description({ extraTop: 5 });

  $('.load_more_text').load_more_of_post({ type: "text" });
  $('.load_more_not_text').load_more_of_post({ type: "not_text" });

  // bookmarkFeeds();
  $(window).commonUserFeeds({ when: "bookmark" });
</script>
