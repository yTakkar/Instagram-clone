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
  $recommend = new recommend;
?>

<?php
  $get_id = $universal->getIdFromGet($_GET['u']);
  if (isset($_SESSION['id'])) {
    $session_id = $_SESSION['id'];
  }
?>

<div class="senapati pro_senapati">
  <div class="m_div">
    <?php
      if ($universal->MeOrNot($get_id)){
        $recommend->profileRecommends($get_id);
      } else if ($universal->MeOrNot($get_id) == false) {
        echo "<div class='home_last_mssg pro_last_mssg'><img src='". DIR ."/images/needs/large.jpg'>
        <span>You should recommend yourself the most</span></div>";
      }
     ?>
  </div>
</div>

<script type="text/javascript">
LinkIndicator('profile');
  $('.m_on').on('mouseover', function(e){
    $(this).find('.recommend_time').show();
  }).on('mouseleave', function(e){
    $(this).find('.recommend_time').hide();
  });

  $('.follow').follow({
    update: true
  });
  $('.unfollow').unfollow({
    update: true
  });
</script>
