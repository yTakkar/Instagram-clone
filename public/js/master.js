console.log(
  "%c%s",
  "color: #1b2733; font-family: Trebuchet MS, sans-serif;font-size: 15px;",
  "Welcome to Instagram 😍😊😎"
);

// console.log('[Looks best in maximum window]');
// mario();

//for replacing illegal characters
$('.s_username, .s_email').on('keyup', function(e){
  replacer($(this));
});

//for dropdown menu on header
$('.show_more').toggleMenu({
  menu: $('.sp_options')
});

// for main nav options toggle
$('.m_n_bottom li:last-of-type').children().filter('a').toggleMenu({
  menu: $('.nav_options')
});

// FOR POST OPTIONS
$('.posts').ToggleMenu({
  btn: $('.exp_p_menu'),
  menu: $('.p_options')
});

//calling the hide and show plugin
$('.posts').postHideAndShow();

$('.load_more_text').load_more_of_post({ type: "text" });
$('.load_more_not_text').load_more_of_post({ type: "not_text" });

//COMMENT HEIGHT TOGGLE
$('.p_cit_area > textarea').commentToggle();

$('.p_cit_area > textarea').on('focus', function(e){
  $(this).parent().siblings().filter('.p_cit_tool').css('display', 'none');
}).on('blur', function(e){
  $(this).parent().siblings().filter('.p_cit_tool').css('display', 'inline-block');
});

//Autosize of textarea
$('.p_cit_area textarea').addClass('textarea_d_height');
autosize($('textarea'));

$('.post_end').on('click', function(e){
  $('html, body').animate({scrollTop: 0}, 450);
});

$('.post_teaser span:last-of-type').description({
  extraTop: -5
});

//hover over description for post
$('.p_cit_more, .p_do > div > span, .c_sticker').description();
$('.p_cit_area span').description({
  extraTop: 5
});
// $('.p_did > span').description({
//   text: "innerHTML"
// });

$('.post_it img').on('click', function(e){
  var username = $('.user_info').data('username');
  window.location.href = DIR+"/profile/"+username;
});

//Toggle Extra Post Toggle
$('.post_it span:last-of-type').postExtraToggle();

//calling videoControls plugin
$('video').videoControls();

//calling audioControls plugin
$('.p_aud').audioControls();

// POSTING
//TEXT POST
$('.user_whats_new').textPost();

// IMAGE POST
$('.user_img_file').imagePost();

// VIDEO POST
$('.user_vid_file').videoPost();

// AUDIO POST
$('.user_aud_file').audioPost();

// DOCUMENT POST
$('.user_doc_file').docPost();

// LOCATION POST
$('.p_user_map').locationPost();

// LINK POST
$('.p_user_link').linkPost();

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

// POST LIKERS
$('.likes').likes();

// POST TAGGERS
$('.p_tags').taggers();

// POST SHARERS
$('.p_h_opt > .p_comm').shares();

// POST UNTAG
$('.untag').untag();

// IMAGE SHOW
$('.p_img').imageShow({info: "yes"});

$('.pro_banner_options').find('a').on('click', function(e){
  $('.pro_banner_options').hide();
});

$('.pro_recommend').recommend();
$('.delete_post').deletePost();
$('.un__share').removeShare();
$('.unshare').unshare();

// POST COMMENT TEASER
$('.comment_teaser').postComment({refresh: "no"});
$('.comment_og').postComment({refresh: "yes"});
$('.p_comm_file_teaser').imageComment({ refresh: "no" });
$('.p_comm_file_og').imageComment({refresh: "yes"});

// $('.comment_og').focus();

$('.c_sticker_trailer').sticker({
  when: "comment",
});
$('.c_sticker_og').sticker({
  when: "comment",
  commRefresh: "yes"
});

$('.comments').on('mouseover', function(e){
  $(this).find('.comment_tools').show();
}).on('mouseout', function(e){
  $(this).find('.comment_tools').hide();
});

$('.comment_delete').deleteComment();
$('.comment_edit').editComment();

// $('.comments_div').perfectScrollbar();

$('.comment_like').likeComments();
$('.comment_unlike').unlikeComments();
$('.comment_likes').commentLikers();
$('.comments_img').imageShow({info: "no_post_yes"});

$('.p_comments').on('click', function(e){
  var post = $(this).parent().data('postid');
  window.location.href = DIR+"/view_post/"+post;
});

$('.edit_post').editPost();

// PROFILE PAGE
$('.pro_more_horiz').toggleMenu({
  menu: $('.pro_banner_options')
});

$('.pro_post').on('click', function(e){
  var username = $('.user_info').data('username');
  window.location.href = DIR+"/profile/"+username+"?ask=posts";
});
$('.pro_recomm').on('click', function(e){
  var username = $('.user_info').data('username');
  window.location.href = DIR+"/profile/"+username+"?ask=recommends";
});
$('.pro_fav').on('click', function(e){
  var username = $('.user_info').data('username');
  window.location.href = DIR+"/profile/"+username+"?ask=favourites";
});

//FOR SLIDE TOGGLE TAGS DIV
$('.pro_exp_more > span').on('click', function(e){
  $(this).toggleClass('pro_exp_more_toggle');
  $('.pro_tags').slideToggle('fast');
});

$('.pro_more_horiz').description({ extraTop: -7 });

$('.pro_exp_more > span').description({ extraTop: -13 });

//FOR AVATAR HOVER FUNCTIONALITY
$('.pro_avatar').on('mouseover', function(e){
  $('.pro_avatar_ch_teaser').show()
}).on('mouseout', function(e){
  $('.pro_avatar_ch_teaser').hide();
});

// USING THE PLUGIN FOR VIEWING AVATAR
$('.view_avatar').viewAvatar();

//USING THE PLUGIN FOR CHANGING AVATAR
$('.change_pro').changeAvatar();
$('.pro_ava_middle').perfectScrollbar();

// USING PLUGIN FOR UPLOADING AVATAR
$('.pro_ch_ava').uploadAvatar();

//FOR FOLLOW AND UNFOLLOW ON THR PROFILE PAGE
$('.follow').follow({
  update: true
});
$('.unfollow').unfollow({
  update: true
});

$('.simple_unfollow').simpleUnfollow();
$('.block').block();
$('.add_fav').userFav();
$('.p_copy_link').copyPostLink();

//PROFILE FOLLOWERS AND FOLLOWINGS FUNCTIONALITY
$('.pro_followers').followers();
$('.pro_followings').followings();

//PROFILE VIEWERS
// $('.pro_views').profileViewers();

$('.display_middle').perfectScrollbar();

// HELP ICON
$('.help').help();

$('.home_rajkumar').sticky();

// PROFILE NAVIGATION
$('.user_nav').profileNav();

//EDIT PROFILE
$('.edit_profile').editProfile();

$('.image_show').find('.img_s_window').description();

// SETTINGS PAGE
$('.settings_nav').settingsNav();

$('body').getUnread();

$('.clear_noti').clearNotifications()
$('.clear_noti').description({extraLeft: 5});
notificationFeeds();

$('.mssg_persons').perfectScrollbar();

$('.sti_main').perfectScrollbar();

$('.home_recomm').HomeSuggestions();

$('.recomm_refresh, .recomm_all').description({ extraTop: 5 });

// SINGLE USER
$('.mssg_usr').selectConversation();
$('.new_con').sendMssgViaBtn();

// GROUP CHAT
$('.new_congrp').addGrpCon();
$('.mssg_gsr').selectGrpCon();


$('body').getAllUnreadMssg();
$('.mssg_usr').constantUpdateCon();
$('.mssg_gsr').constantUpdateGrpCon();

// GROUP
// PROFILE NAVIGATION
$('.grp_nav').groupNav();

$('.c_g').createGroup();
$('.inv_g').inviteToGrp();

$('.view_grp_avatar').viewAvatar();

//USING THE PLUGIN FOR CHANGING AVATAR
$('.change_grp_ava').changeAvatar({ when: "group" });

// USING PLUGIN FOR UPLOADING AVATAR
$('.grp_ch_ava').uploadAvatar({when: "group"});

$('.join_grp').joinGrp({when: "main"});
$('.leave_grp').leaveGrp();
$('.d_g').deleteGrp();
$('.mk_admin_banner').changeGrpAdmin();

// EXPLORE
$('.exp_nav').exploreNav();

// SEARCHING
$('input[type="text"].search').searchInstagram();

// HOVER USER DETAILS
// $('.recomms_cont > a').hoverUser({ extraTop: 15 });

// HASHTAG
hashtagFeeds();

notificationsModel();
