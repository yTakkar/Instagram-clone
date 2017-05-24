<div class="login_details inst">

  <?php
    session_start();
    $session = $_SESSION['id'];

    include '../../config/class/settings.class.php';
    $settings = new settings;
  ?>

  <?php echo $settings->loginDetails(); ?>

</div>
