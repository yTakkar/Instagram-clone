//FUNCTION TO GET ELEMENTS FROM STARTING TO THE GIVEN LIMIT
function range(no){
  var array = [];
  var div = $('.psswrd_strength > div');
  for (var i = 0; i < div.length; i++) {
    array.push(div[i]);
  }
  array.splice(no, 4);
  for (var i = 0; i < array.length; i++) {
    array[i].style.background = "#2895F1";
  }
}

// PLUGIN FOR PASSWORD RETRIEVAL
(function($){
  $.fn.passwordRetrieval = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = this;
    var form = elem.find('form');

    elem.find('input[type="text"]').on("keyup", function(e){
      var regex = /[^a-zA-Z0-9_@.]/i;
      var input = $(this).val();
      $(this).val(input.replace(regex, ''));
    });

    form.on('submit', (function(e){
      e.preventDefault();
      var submit = elem.find('.f_p_submit');

      submit.prop('disabled', true);
      $('.overlay-2').show();

      var input = elem.find('input[type="text"]');
      $.ajax({
        url   : DIR+"/ajaxify/ajax_requests/forgot_requests.php",
        method: "POST",
        data  : {input: input.val()},
        beforeSend: function(){
          submit.prop('disabled', true);
          submit.prop('value', 'Checking..');
        },
        success: function(data){

          // if (data.substr(0,2) == "ok") {
          //   console.log('yepp');
          //   submit.prop('value', 'Checking..');
          //   submit.prop('disabled', true);
          //   $('.notify').notify({ value: data.substr(2) });
          //   window.location.href = DIR+"/profile/"+data.substr(2);
          // } else {
          //   $('.notify').notify({ value: data });
          //   submit.prop('disabled', false);
          // }

          console.log(data);
          if (data == "ok") {
            console.log('yepp');
            submit.prop('disabled', true);
            submit.prop('value', 'Redirecting..');
            $('.overlay-2').show();
            $('.notify').notify({ value: data });
            input.val('');
            window.location.href = DIR+"/retrieve_ok";
          } else {
            $('.notify').notify({ value: data });
            submit.prop('disabled', false);
            submit.prop('value', 'Recover');
            $('.overlay-2').hide();
          }

        }
      });
    }));

  }
}(jQuery));

//PLUGIN FOR USERNAME CHECKER
(function($){
  $.fn.username_checker = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);
    this.on('keyup', function(e){
      var value = this.value;
      $('.username_checker').show();
      if (value != "") {
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/u_checker_requests.php",
          method: "GET",
          data: {value: value},
          success: function(data){
            $('.username_checker').html(data);
          }
        });
      } else if (value == "") {
        $('.username_checker').hide();
      }
    });
    this.on('blur', function(e){
      $('.username_checker').hide();
    });
  }
}(jQuery));

//PLUGIN FOR PASSWORD STRENGTH
(function($){
  $.fn.psswrd_strength = function(options){
    var defaults = {
      background: "cyan"
    }
    var settings = $.extend({}, defaults, options);
    this.on('keyup', function(e){
      var value = this.value.length;
      if (value == 0) {
        $('.psswrd_strength > div').css('background', settings.background);
      } else if ((value > 0) && (value < 4)) {
        range(1);
      } else if ((value == 4) && (value < 7)) {
        range(2);
      } else if ((value == 7) && (value < 10)) {
        range(3);
      } else if ((value == 10) && (value < 12)) {
        range(4);
      }
    });
  }
}(jQuery));

// PLUGIN FOR VIEWING PASSWORD
(function($){
  $.fn.togglePassword = function(options){
    var defaults = {
      input: null
    }
    var settings = $.extend({}, defaults, options);
    this.on('click', function(e){
      if (settings.input.type == "password") {
        settings.input.type = "text";
        this.innerHTML = '<i class="fa fa-unlock-alt" aria-hidden="true"></i>';
        this.style.color = "#e91e63";
      } else {
        settings.input.type = "password";
        this.innerHTML = '<i class="fa fa-lock" aria-hidden="true"></i>';
        this.style.color = "darkturquoise";
      }
      settings.input.focus();
    });
  }
  return this;
}(jQuery));

// FUNCTION FOR LOGIN
function login(ju, jp, btn){
  $.ajax({
    url: DIR+"/ajaxify/ajax_requests/login_requests.php",
    method: "POST",
    dataType: "json",
    data: {
      username: ju,
      password: jp
    },
    success: function(data){
      $('.notify').notify({
        value: 'Hello ' + ju + '!!'
      });
      console.log(data.mssg);
      if (data.mssg == "Successfull") {
        console.log('yepp');

        var l = location.search;
        var regex = /\?next=\/?[a-zA-Z0-9\/:.]+/;
        var next = l.substr(l.indexOf('=')+1);

        if (regex.test(l)) {
          var url = next;
        } else if (regex.test(l) == false){
          var url = DIR;
        }

        btn.prop('disabled', true);
        btn.prop('value', 'Redirecting..');
        $('.overlay-2').show();
        setTimeout(function(e){
          window.location.href = url;
        }, 1000);
      } else {
        btn.prop('disabled', false);
        $('.overlay-2').hide();
      }
    }
  });
}

// FUNCTION FOR QUICK LOGIN
(function($){
  $.fn.quickLogin = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    this.each(function(){
      var elem = $(this);
      var div = $('.q_l_model');
      var overlay = $('.overlay');
      var overlay_2 = $('.overlay_2');
      var cancel = div.find('.q_l_m_cancel');
      var remove = div.find('.q_l_remove');
      var img = div.find('.q_l_m_img_div > img');
      var username = div.find('.q_l_username');
      var form = div.find('form.q_l_m_form');
      var input = div.find('#q_l_password');
      var btn = div.find('input[type="submit"]');
      var hidden = div.find('input[type="hidden"]._id');

      function clearQL(){
        document.cookie = 'ids' + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        location.reload();
      }

      $('.clear_all_ql').on('click', function(e){
        e.preventDefault();
        clearQL();
      });

      elem.on('click', function(e){
        var d = $(this).data('username');
        var id = $(this).data('id');

        blur.addBlur();
        overlay.show();
        div.fadeIn(100);
        input.focus();
        img.attr('src', $(this).attr('src'));
        username.text("@"+d);
        hidden.val(id);

        // $('.q_l_show_psswrd').togglePassword({
        //   input: document.getElementById('q_l_password')
        // });

        cancel.on('click', function(e){
          overlay.hide();
          blur.removeBlur();
          div.fadeOut(100);
          input.val('');
          hidden.val('');
          img.attr('src', DIR+'/images/avatars/voldemort.jpg');
          username.text('@Instagram');
        });

        remove.on('click', function(e){
          e.preventDefault();
          clearQL();
        });

        form.on('submit', function(e){
          e.preventDefault();
          btn.blur();
          btn.prop('disabled', true);
          overlay_2.show();
          var value = input.val();

          if (value == "") {
            value.focus();
          } else {
            login(d, value, btn);
          }
        });

      });

    });
    return $(this);
  }
}(jQuery));
