<?php include '../../config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include '../../config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $explore = new explore;
?>

<div class="exp_find_people">

  <?php $explore->exploreVideos(); ?>

</div>

<script type="text/javascript">
//calling videoControls plugin
$('video').videoControls();
</script>
