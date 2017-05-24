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

<div class="senapati pro_senapati">
  <div class="m_div">

    <div class='m_header'><span><?php echo $groups->noOfGrpMembers($grp); ?> members</span></div>

    <?php $groups->grpMembers($grp, "direct", "0"); ?>

    <?php if($groups->noOfGrpMembers($grp) >= 18 ){ ?>
      <div class='load_more_'><a href='#' class='pri_btn load_more_btn'>Load more</a></div>
    <?php } ?>

  </div>
</div>

<script type="text/javascript">

  $('.rem_mem').description({extraTop: 3});

  $('.m_on').on('mouseover', function(e){
    $(this).find('.recommend_time').show();
    $(this).find('.rem_mem').show();
  }).on('mouseleave', function(e){
    $(this).find('.recommend_time').hide();
    $(this).find('.rem_mem').hide();
  });

  $('.follow').follow();
  $('.unfollow').unfollow();

  $('.rem_mem').removeMember();
  // $(window).commonGrpFeeds({when: "members"});

  $('.load_more_btn').on('click', function(e){
    e.preventDefault();
    grpMemFeeds($(this));
  });

</script>
