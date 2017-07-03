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
  <div class="srajkumar audio_srajkumar">

    <div class="c_g_div vid_c_grp inst">
      <span>Explore more recommended music from all around Instagram.</span>
      <div class="grp_c_we">
        <a href="<?php echo DIR; ?>/explore?ask=exp_audios" class="sec_btn">Explore</a>
      </div>
    </div>

    <?php include 'sugg.php'; ?>
  </div>

  <div class="prajkumar">
    <?php echo $post->getAudiosPost($get_id); ?>
  </div>
</div>

<script type="text/javascript">
  LinkIndicator('audios');

  //calling audioControls plugin
  $('.p_aud').audioControls();
  $('.follow').follow({ update: true });
  $('.unfollow').unfollow({ update: true });
  $('.home_recomm').HomeSuggestions();
  $('.recomm_refresh, .recomm_all').description({ extraTop: 5 });
</script>
