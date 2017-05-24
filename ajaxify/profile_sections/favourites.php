<?php include '../../config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include '../../config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $universal = new universal;
  $avatar = new Avatar;
  $follow = new follow_system;
  $post = new post;
  $mutual = new mutual;
  $fav = new favourite;
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
      if($fav->noOfFavs($get_id) != 0){
        echo "<div class='m_header fav_header'><span>{$fav->noOfFavs($get_id)} favourites</span></div>";
      }
    ?>
    <?php $fav->userFavs($get_id); ?>
  </div>
</div>

<script type="text/javascript">
  LinkIndicator('favourites');
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
  $('.rem_fav').remFav();
</script>
