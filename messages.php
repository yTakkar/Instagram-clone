<?php include 'config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include 'config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $universal = new universal;
  $avatar = new Avatar;
  $post = new post;
  $noti = new notifications;
  $message = new message;
?>

<?php
  if ($universal->isLoggedIn() == false) {
    header('Location:'. DIR .'/welcome');
  }
  $session = $_SESSION['id'];
?>

<?php
  $title = "{$noti->titleNoti()} Messages • Instagram";
  $keywords = "Instagram, Share and capture world's moments, share, capture, messages";
  $desc = "Instagram lets you capture, follow, like and share world's moments in a better way and tell your story with photos, messages, posts and everything in between";
?>

<!-- including files for header of document -->
<?php include_once 'includes/header.php'; ?>
<?php include_once 'needs/heading.php'; ?>
<?php include_once 'needs/nav.php'; ?>

<div class="user_info" data-userid="<?php echo $session; ?>" data-sessionid="<?php echo $session; ?>" data-username="<?php echo $universal->getUsernameFromSession(); ?>"></div>

<div class="overlay"></div>
<div class="overlay-2"></div>
<div class="notify"><span></span></div>

<div class="badshah mssg_badshah">

  <div class="mssg_left">

    <div class="mssg_new">
      <a href="#" class="pri_btn new_congrp"><i class="fa fa-plus" aria-hidden="true"></i><span>Create group</span></a>
      <a href="#" class="pri_btn new_con"><span>New conversation</span></a>
    </div>

    <div class="mssg_add_persons">
      <input type="text" name="" value="" placeholder="Search to message" spellcheck="false">
    </div>

    <div class="mssg_persons">
      <div class="mssg_persons_inner">
        <ul></ul>
      </div>
    </div>

    <span class='con_count' data-count='<?php echo $message->conCount(); ?>'><?php echo $message->conCount(); ?> conversations</span>
    <?php $message->groupsCon(); ?>
    <?php $message->conversations(); ?>

  </div>

  <div class="mssg_right">
    <div class='home_last_mssg mssg_last_mssg'>
      <img src='<?php echo DIR; ?>/images/needs/large.jpg'>
      <span>Please select a conversation or group</span>
    </div>
  </div>

</div>

<div class="grp_to model-shadow">
  <div class="grp_t_top">
    <div class="grp_t_top_left">
      <span>Create group conversation</span>
      <form class="" action="" method="post" enctype="multipart/form-data">
        <input type="file" name="" value="grp_to_avatar_file" id="grp_to_avatar_file">
        <label for="grp_to_avatar_file" class="grp_to_avatar sec_btn">Choose avatar</label>
      </form>
    </div>
  </div>
  <div class="grp_t_main">
    <input type="hidden" name="" value="" class="grp_to_holder">
    <input type="hidden" name="" value="" class="grp_to_avatar">
    <div class="grp_to_img">
      <img src="<?php echo DIR; ?>/images/Default_group_con/Epic-Circle-31m3ldalla6v0uqb8ne6mi.png" alt="">
    </div>
    <div class="grp_t_name_div">
      <span>Name your group</span>
      <input type="text" name="" value="" placeholder="Name.." class="grp_t_name" spellcheck="false">
    </div>
    <div class="grp_t_add_div">
      <span>Add members</span>
      <input type="text" name="" value="" placeholder="Search.." class="grp_t_add" spellcheck="false">
    </div>

    <div class="grp_t_members">
      <div class="grp_t_helper"></div>
      <!-- <span class="grp_t_added">voldemort</span> -->
    </div>

    <div class="grp_to_persons">
      <div class="grp_to_persons_inner">
        <ul class="grp_to_ul">
          <!-- <li class='grp_to_select_u'><img src='<?php echo DIR; ?>/images/avatars/voldemort.jpg' alt=''><span>Voldemort</span></li> -->
        </ul>
      </div>
    </div>
  </div>

  <div class="grp_t_bottom">
    <!-- <span class="grp_t_emoji"><i class="material-icons">sentiment_very_satisfied</i></span> -->
    <a href="#" class="sec_btn grp_t_cancel">Cancel</a>
    <a href="#" class="pri_btn grp_t_done">Done</a>
  </div>
</div>


<?php include 'needs/message_div.php'; ?>
<?php include 'needs/emojis.php'; ?>
<?php include_once 'needs/display.php'; ?>
<?php include_once 'needs/prompt.php'; ?>
<?php include 'needs/image_show.php'; ?>
<?php include 'needs/stickers.php'; ?>
<?php include_once 'needs/search.php'; ?>

<!-- including the footer of the document -->
<?php include_once 'includes/footer.php'; ?>

<script type="text/javascript">
  $(function(e){
    LinkIndicator("messages");
    // $('.messages').find('.m_n_new').text('');
    // $('.mssg_last_mssg').on('click', function(e){
    //   if (window.Notification) {
    //     Notification.requestPermission(function(status){
    //       console.log('status: '+status);
    //       var n = new Notification('Instagram', {
    //         body: "Wlecome to Instagram!",
    //         icon: DIR+"/images/avatars/voldemort.jpg"
    //       });
    //     });
    //   }
    // });
  });
</script>
