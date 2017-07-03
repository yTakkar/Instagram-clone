// FUNCTION FOR SETTINGS NAVIGATION
(function($){
  $.fn.settingsNav = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      var fetchAndInsert = function(href){
        $.ajax({
          url: DIR+"/ajaxify/settings/"+href.split('=').pop(),
          beforeSend: function(){
            $('.settings_loader').html('<div class="spinner"><span></span><span></span><span></span></div>');
            $('.settings_rajkumar > .settings_loader > .spinner').addClass('hmm_spinner_show');
          },
          success: function(data){
            $('.settings_rajkumar > .settings_loader > .spinner').removeClass('hmm_spinner_show');
            $('.settings_nav').removeClass('settings_nav_active');

            if (href.indexOf('&') > -1) {
              var f = href.substr(href.indexOf('=')+1);
              var get = f.substr(0, f.indexOf('&'));
            } else {
              var get = href.substr(href.indexOf('=')+1);
            }

            var main = get.substr(0, get.lastIndexOf('.'));
            console.log(get.substr(0, get.lastIndexOf('.')));

            $('.settings_nav_div > ul li > a.'+main).addClass('settings_nav_active');
            $('.settings_rajkumar > .settings_loader').html(data);
          }
        });
      }

      $(window).on('popstate', function(e){
        var main = location.pathname+location.search;
        console.log(main);
        if (location.search) {
          fetchAndInsert(main+".php");
        } else {
          fetchAndInsert("change_password.php");
        }
      });

      elem.on('click', function(e){
        e.preventDefault();
        $('.settings_nav').removeClass('settings_nav_active');
        $(this).addClass('settings_nav_active');
        var url = $(this).data('url');
        var hint = url.substr(0, url.lastIndexOf(".php"));
        history.pushState({}, "", location.pathname+"?ask="+hint);
        fetchAndInsert(url);
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO CHANGE PASSWORD
(function($){
  $.fn.changePassword = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = this;
    var change = elem.find('.c_p_btn');

    // /[<>]/i

    change.on('click', function(e){
      e.preventDefault();

      var current = elem.find('.c_p_old > input[type="password"]').val();
      var new_ = elem.find('.c_p_new > input[type="password"]').val();
      var new_again = elem.find('.c_p_new_a > input[type="password"]').val();

      if (!current || !new_ || !new_again) {
        $('.notify').notify({
          value: "Details are empty"
        });
      } else if (new_ != new_again) {
        $('.notify').notify({
          value: "New passwords don't match"
        });
      } else {
        $(this).addClass('update_toggle');
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/settings_requests.php",
          data: {
            change_password: "yes",
            current: current,
            new: new_,
            new_again: new_again
          },
          method: "POST",
          success: function(data){
            $(this).removeClass('update_toggle');
            console.log(data);
            $('.notify').notify({value: data});
            elem.find('input[type="password"]').val('');
          }
        });

      }
    });

  }
}(jQuery));

// FUNCTION FOR EMAIL PRIVACY
(function($){
  $.fn.emailPrivacy = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = this;

    elem.on('change', function(e){
      var checked = $('#email_private:checked').length > 0;

      var options = ((checked) ? "private" : "public");

      $.ajax({
        url: DIR+"/ajaxify/ajax_requests/privacy_requests.php",
        data: {emailPrivacy: options},
        success: function(data){
          if(options == "private"){
            $('.notify').notify({value: "Changed to private"});
          } else if(options == "public"){
            $('.notify').notify({value: "Changed to public"});
          }
        }
      });
    });

  }
  return this;
}(jQuery));

// FUNCTION FOR MOBILE PRIVACY
(function($){
  $.fn.mobilePrivacy = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = this;

    elem.on('change', function(e){
      var checked = $('#mobile_private:checked').length > 0;

      var hint = ((checked) ? "private" : "public");
      console.log(hint);
      $.ajax({
        url: DIR+"/ajaxify/ajax_requests/privacy_requests.php",
        data: {mobilePrivacy: hint},
        success: function(data){
          if(hint == "private"){
            $('.notify').notify({value: "Changed to private"});
          } else if(hint == "public"){
            $('.notify').notify({value: "Changed to public"});
          }
        }
      });
    });

  }
  return this;
}(jQuery));

// FUNCTION TO CHANGE ACCOUNT TYPE
(function($){
  $.fn.changeAccountType = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = this;

    elem.on('change', function(e){
      var value = $(this).val();
      $.ajax({
        url: DIR+"/ajaxify/ajax_requests/settings_requests.php",
        data: {accountType: value},
        success: function(data){
          $('.notify').notify({  value: "Account set to "+data  });
          $('.type_indicator').text(data);
        }
      });
    });

  }
  return this;
}(jQuery));

// FUNCTION TO DELETE ACCOUNT
(function($){
  $.fn.deleteAccount = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = $(this);

    elem.on('submit', function(e){
      e.preventDefault();
      var input = elem.find('input[type="password"]');
      var value = input.val();
      if (value == "") {
        input.focus();
      } else if (value != "") {
        $('.prompt').myPrompt({
          title: "Delete account",
          value: "Your account and all your data will be premanently deleted. Also groups created by you will be deleted.",
          doneText: "Delete",
          type: "dlt_acc",
          post: $(this)
        });
      }
      // dlt__acc($(this));
    });

  }
  return this;
}(jQuery));
