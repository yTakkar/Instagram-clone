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
    <?php
      if ($get_id != $session_id){
        $mutual->peopleMightKnow($get_id);
      } else if ($get_id == $session_id) {
        echo "<div class='home_last_mssg rec_last_mssg'><img src='". DIR ."/images/needs/large.jpg'>
        <span>You know yourself the most</span></div>";
      }
     ?>
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
</script>
