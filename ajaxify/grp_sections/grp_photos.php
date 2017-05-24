<?php include '../../config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include '../../config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $universal = new universal;
  $avatar = new Avatar;
  $settings = new settings;
  $mutual = new mutual;
  $noti = new notifications;
  $message = new message;
  $groups = new group;
?>

<?php
  $grp = $_GET['grp'];
  $session = $_SESSION['id'];
?>

<div class="senapati pro_senapati photos_senapati">
  <?php $groups->getGrpPhotos($grp); ?>
</div>

<script type="text/javascript">
  $(function(){

    var options = {
      container: $('.photos_senapati'), // Optional, used for some extra CSS styling
      offset: 10, // Optional, the distance between grid items
      itemWidth: 190 // Optional, the width of a grid item
    };

    var handler = $('.post_photos')
    handler.wookmark(options);

    $('.post_photos').on('mouseover', function(e){
      // $(this).find('.post_photos_overlay').show();
      $(this).find('.post_p_info').show();
      $(this).find('img').css('transform', 'scale(1.1)');
    }).on('mouseout', function(e){
      // $(this).find('.post_photos_overlay').hide();
      $(this).find('.post_p_info').hide();
      $(this).find('img').css('transform', 'scale(1)');
    });

    $('.post_photos > img').imageShow({info: "yes"});

  });
</script>
