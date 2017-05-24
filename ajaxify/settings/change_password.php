<div class="change_pass">
  <div class="c_p_header">
    <span>Change password</span>
  </div>

  <div class="c_p_main">
    <div class="c_p_old">
      <span>Current password</span>
      <input type="password" name="" value="" placeholder="Current password" autofocus spellcheck="false">
    </div>
    <div class="c_p_new">
      <span>New password</span>
      <input type="password" name="" value="" placeholder="New password" spellcheck="false">
    </div>
    <div class="c_p_new_a">
      <span>Confirm new password</span>
      <input type="password" name="" value="" placeholder="Confirm current password" spellcheck="false">
    </div>
    <a href="#" class="no_focus c_p_btn">Change password</a>
  </div>

</div>

<script type="text/javascript">
  $(function(){
    $('.c_p_old > input[type="password"]').focus();
    $('.change_pass').changePassword();
  });
</script>
