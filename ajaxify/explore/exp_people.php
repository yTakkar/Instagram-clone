<?php include '../../config/declare.php'; ?>

<!-- a universal file that has all the classes included -->
<?php include '../../config/classesGetter.php'; ?>

<!-- creating objects -->
<?php
  $explore = new explore;
?>

<div class="exp_find_people">

  <?php $explore->explorePeople(); ?>

</div>

<script type="text/javascript">
  $(function(){
    $('.follow').follow();
    $('.unfollow').unfollow();
  });
</script>
