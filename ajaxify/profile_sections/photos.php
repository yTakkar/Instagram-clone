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
?>

<?php
  $get_id = $universal->getIdFromGet($_GET['u']);
  if (isset($_SESSION['id'])) {
    $session_id = $_SESSION['id'];
  }
?>

<div class="senapati pro_senapati photos_senapati" id="photos_senapati">
  <?php $post->getPhotosPost($get_id); ?>
</div>

<script type="text/javascript">
  $(function(){

    LinkIndicator('photos');

    var options = {
      container: $('.photos_senapati'), // Optional, used for some extra CSS styling
      offset: 10, // Optional, the distance between grid items
      itemWidth: 190 // Optional, the width of a grid item
    };

    var handler = $('.post_photos');
    handler.wookmark(options);

    $('.post_photos').on('mouseover', function(e){
      // $(this).find('.post_photos_overlay').show();
      $(this).find('.post_p_info').show();
      $(this).find('img').css('transform', 'scale(1.05)');
    }).on('mouseout', function(e){
      // $(this).find('.post_photos_overlay').hide();
      $(this).find('.post_p_info').hide();
      $(this).find('img').css('transform', 'scale(1)');
    });

    $('.post_photos > img').imageShow();

    $('.photos_senapati').css('height', 'inherit');

  });
</script>
