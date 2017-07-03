<div class="notifications_div">

  <div class="notifications_header">
    <span class="noti_count">
      <?php
        $count = $noti->notiCount();
        if($count == 0){
          echo "No notifications";
        } else if($count == 1){
          echo "1 notification";
        } else {
          echo "$count notifications";
        }
      ?>
    </span>
      <span class="clear_noti" data-description='Clear notifications'>
        <?php if($noti->notiCount() != 0){ ?>
        <i class="material-icons">clear_all</i>
        <?php } ?>
      </span>
  </div>

  <?php $noti->getNotifications("direct", "0"); ?>

  <!-- <div class="noti action_noti">
    <img src="<?php echo DIR; ?>/images/avatars/voldemort.jpg" alt="" class="noti_avatar">
    <div class="noti_left">
      <a href="#" class="noti_bold noti_username">Voldemort</a>
      <span>liked your photo</span><span class="noti_time">1 day</span>
    </div>
    <div class="noti_right action_noti_right">
      <a href="#"><img src="<?php echo DIR; ?>/images/avatars/hrithik.jpg" alt=""></a>
    </div>
  </div> -->

</div>
