<?php include '../../config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include '../../config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $explore = new explore;
?>

<div class="exp_find_photos">

  <?php echo $explore->explorePhotos(); ?>

</div>

<script type="text/javascript">
  $(function(){

    // $('.exp_finds_ph').on('mouseover', function(e){
    //   $(this).find('.exp_f_ph_open').show();
    // }).on('mouseout', function(e){
    //   $(this).find('.exp_f_ph_open').hide();
    // });

    // var options = {
    //   container: $('.exp_find_photos'), // Optional, used for some extra CSS styling
    //   offset: 5, // Optional, the distance between grid items
    //   itemWidth: 190 // Optional, the width of a grid item
    // };
    //
    // var handler = $('.exp_finds_ph ');
    // handler.wookmark(options);

    $('.exp_f_ph_img > img').imageShow({info: "yes"});

  });
</script>
