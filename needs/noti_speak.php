<?php
  $count = $noti->unreadCount();
  if($count == 0){
    $return = "";
  } else {
    $return = "<span class='ns_bold'>@{$universal->GETsDetails($session, "username")}</span>, you got {$count} notifications";
  }
?>

<div class="noti_speak">
  <input type="hidden" name="" value="<?php echo $return; ?>" class="noti_hidden">
  <img src="<?php echo DIR."/".$avatar->SESSIONsAvatar(); ?>" alt="">
  <div class="n_s_sn_div">
    <span><b>@Modi</b>, you got 12 notifications.</span>
  </div>
</div>
