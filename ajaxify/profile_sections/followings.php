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
  $mutual = new mutual;
?>

<?php
  $get_id = $universal->getIdFromGet($_GET['u']);
  if (isset($_SESSION['id'])) {
    $session_id = $_SESSION['id'];
  }
?>

<div class="senapati pro_senapati">
  <div class="m_div">

    <div class='m_header'><span><?php echo $follow->getFollowings($get_id); ?> followings</span></div>

    <?php $follow->profileFollowings($get_id, "direct", "0"); ?>
  </div>
</div>

<script type="text/javascript">
LinkIndicator('profile');
  $('.follow').follow({
    update: true
  });
  $('.unfollow').unfollow({
    update: true
  });
  $('.m_on').on('mouseover', function(e){
    $(this).find('.recommend_time').show();
  }).on('mouseleave', function(e){
    $(this).find('.recommend_time').hide();
  });
  followersFeeds("followings");
</script>
