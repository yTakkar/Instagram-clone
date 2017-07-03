<div class="login_details inst">

  <?php
    session_start();
    $session = $_SESSION['id'];

    include_once '../../config/class/needy_class.php';
    include '../../config/class/settings.class.php';
    $settings = new settings;
  ?>

  <?php echo $settings->loginDetails(); ?>

</div>
