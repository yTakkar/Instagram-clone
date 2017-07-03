<?php if(isset($_SESSION['id'])){ ?>
<?php if ($session_id == $get_id){ ?>
  <div class="recomm home_recomm inst">
    <div class="recomm_top header_of_divs user_recomm_header">
      <span>Suggested</span>
      <!-- <a href="#" class="recomm_refresh" data-description='refresh'><i class="fa fa-refresh" aria-hidden="true"></i></a> -->
      <a href="<?php echo DIR; ?>/explore?ask=exp_people" class="recomm_all" data-description='view all'><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
    </div>
    <div class="recomm_main">
      <?php $suggestions->HomeSuggestions("ajax"); ?>
    </div>
  </div>
<?php } else if ($session_id != $get_id){ ?>

<?php if($suggestions->userSuggCount($get_id) != 0){ ?>
<div class="recomm user_recomm inst">
  <div class="recomm_top header_of_divs user_recomm_header">
    <span>Suggested</span>
    <!-- <a href="#" class="recomm_refresh" data-description='refresh'><i class="fa fa-refresh" aria-hidden="true"></i></a> -->
    <a href="<?php echo DIR; ?>/explore?ask=exp_people" class="recomm_all" data-description='view all'><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
  </div>
  <div class="recomm_main">
    <?php
      $suggestions->UserSuggestions($get_id);
    ?>
  </div>
</div>
<?php } ?>

<?php } ?>
<?php } ?>
