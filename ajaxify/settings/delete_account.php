<div class="dlt_acc inst">

  <div class="c_p_header">
    <span>Delete account</span>
  </div>

  <form class="dlt_acc_form" action="" method="post">
    <input type="password" name="" value="" placeholder="Your password.." autofocus>
    <input type="submit" name="" value="Delete">
  </form>

  <div class="dlt_acc_info">
    <span class="dlt_acc_bold">Note:</span>
    <span>All of your <span class="dlt_acc_bold">posts, followers, followings, recommendations, messages, groups, settings, notifications, bookmarks info will be permanently deleted.</span> And you won't be able to find it again.</span>
    <span>Also every messages group and social group <span class="dlt_acc_bold">created by you</span> will be permanently deleted.</span>
  </div>

</div>

<script type="text/javascript">
  $(function(){
    $('input[type="password"]').focus();
    $('.dlt_acc_form').deleteAccount();
  });
</script>
