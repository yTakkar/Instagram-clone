<?php include '../../config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include '../../config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $explore = new explore;
?>

<div class="exp_find_people">

  <?php $explore->exploreAudios(); ?>

</div>

<script type="text/javascript">
//calling audioControls plugin
$('.p_aud').audioControls();
</script>
