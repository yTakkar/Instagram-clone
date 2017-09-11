var DIR = "/faiyaz/Instagram";

//FUNCTION FOR LINK INDICATOR WHEN CLICKING ON DIFFERENT PAGES FROM FIXED NAVIGATION
function LinkIndicator(elem){
  $('.m_n_a').removeClass('active');
  $("."+elem).addClass('active');
  // $('a[data-link="+' elem '+"]').addClass('active');
}

//FUNCTION FOR REPLACING ILLEGAL CHARACTERS
function replacer(ele){
  var regex = /[<> ]/i;
  var value = ele.val();
  ele.val(value.replace(regex, ""));
}

// PROFILE PAGE CHECKER
function notMM(){
  var a = location.pathname;
  var b = a.substr(a.indexOf('profile/')+8);
  var sp = $('.sp_span');
  if (b == sp.text()) {
    $('.sp').css({
      'background': '#f7f9fa'
    });
    LinkIndicator('profile');
  }
}

// if (l.indexOf('&') > -1) {
//   var f = l.substr(l.indexOf('=')+1);
//   var get = f.substr(0, f.indexOf('&'));
// } else {
//   var get = l.substr(l.indexOf('=')+1);
// }

// FUNCTION TO CHECK IF THE SPECIFIED GET IS PRESENT
function checkGET(variable){
  var l = location.search;
  var query = l.substr(1);
  var array = query.split("&");
  for (elem of array) {
    var part = elem.split('=');

    if(part[0] === variable){
      var value = part[1];
      var has = true;
    } else {
      var value = "";
      var has = false;
    }

    return {
      has  : has,
      value: value
    };

  }
}

// FUNCTION FOR SHORTENING
function nameShortener(elem, length){
  if (!parseInt(length)) { return; }
  if (elem.length >= parseInt(length)) {
    return elem.substr(0, length-2)+"..";
  } else if (elem.length < parseInt(length)) {
    return elem;
  }
}

// FUNCTION FOR BLURRING
var blur_page = function(){
  this.addBlur = function(){
    $('.badshah, .m_n_wrapper, div.header, .login_container, .index_header, .quick_login').addClass('blur_page');
  }
  this.removeBlur = function(){
    $('.badshah, .m_n_wrapper, div.header, .login_container, .index_header, .quick_login').removeClass('blur_page');
  }
}
var blur = new blur_page();

// FUNCTION TO COPY SPECIFIED TEXT TO CLIPBOARD
function copyTextToClipboard(text){
  var
    textArea = document.createElement("textarea"),
    st = textArea.style;

  st.position = 'fixed';
  st.top = 0;
  st.left = 0;
  st.width = '2em';
  st.height = '2em';
  st.padding = 0;
  st.border = 'none';
  st.outline = 'none';
  st.boxShadow = 'none';
  st.background = 'transparent';

  textArea.value = text;
  document.body.appendChild(textArea);
  textArea.select();

  try {
    var successful = document.execCommand('copy');
    var msg = successful ? 'Link copied!' : 'Unable to copy';
    console.log(msg);
    $('.notify').notify({ value: msg })
  } catch (err) {
    console.log('Unable to copy');
    $('.notify').notify({ value: 'Unable to copy' });
  }

  document.body.removeChild(textArea);

}

//PLUGIN FOR UNIVERSAL NOTIFICATION
(function($){
  $.fn.notify = function(options){
    var defaults = {
      beforeTop: "105%",
      afterTop : "90%",
      value    : "Hiii",
      action   : null
    }
    var settings = $.extend({}, defaults, options);

    this.children().filter('span').html(settings.value);

    var div = this;

    this.animate({
      top: settings.afterTop
    }, "fast", function(){
      setTimeout(function(){
        div.animate({
          top: settings.beforeTop
        });
      }, 3000);
     });

    this.on('click', function(e){
      if (settings.action != null) {
        window.location.href = settings.action;
      }
      div.animate({
        top: settings.beforeTop
      });
    });

    return this;

  }
}(jQuery));

// FUNCTION FOR HOME DISPLAY
(function($){
  $.fn.homeNotify = function(options){
    this.each(function(e){
      var defaults = {
        value: "Span for writing something",
        btnValue: "Instagram",
        btnLink: "#",
        timeOut: true
      };
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.slideDown('fast');

      elem.find('span').text(settings.value);
      elem.find('a')
        .prop('value', settings.btnValue)
        .attr('href', settings.btnLink);

      // elem.find('a').on('click', function(e){
      //   e.preventDefault();
      //   if(elem.slideUp('fast')){
      //     window.location.href = settings.btnLink;
      //   }
      // });

      if (settings.timeOut == true) {
        setTimeout(function(){
          elem.slideUp('fast');
        }, 3000);
      }

    });
    return this;
  }
}(jQuery));

// PLUGIN FOR MENU TOGGLE
(function($){
  $.fn.toggleMenu = function(options){
    var div = this;
    var defaults = {
      menu: $('.options')
    }
    var settings = $.extend({}, defaults, options);
    this.on('click', function(e){
      e.preventDefault();
      settings.menu.toggle();
      div.toggleClass('show_more_toggle');
    });
  }
}(jQuery));

// FOR MENU TOGGLE OF MULTIPLE ITEMS LIKE POSTS
(function($){
  $.fn.ToggleMenu = function(options){
    this.each(function(e){
      var defaults = {
        btn: null,
        menu: null
      };
      var settings = $.extend({}, defaults, options);
      var elem = $(this);

      elem.find(settings.btn).on('click', function(e){
        elem.find(settings.btn).toggleClass('exp_p_menu_toggle');
        // settings.menu.hide();
        elem.find(settings.menu).toggle();

        var items = settings.menu.find('ul');

        if (items.children().length == 0) {
          settings.menu.css('opacity', '0');
        }

      });

      var dd = elem.find(settings.menu);
      var link = dd.find('a');
      link.on('click', function(e){
        elem.find('.exp_p_menu').removeClass('exp_p_menu_toggle');
        settings.menu.hide();
      });

    });
    return this;
  }
}(jQuery));

// PLUGIN FOR HOVER OVER DESCRIPTION
(function($){
  $.fn.description = function(options){
    this.each(function(){

      var elem = $(this);

      var defaults = {
        extraTop: null,
        extraLeft: null,
        text: null
      }
      var settings = $.extend({}, defaults, options);

      $('#hoverdiv').remove();

      if($('#hoverdiv').length == 0){
        var body = $('body').children();
        $(body[0]).before('<div id="hoverdiv" class=""></div>');
      }

      elem.on('mouseover', function(e){

        if(settings.text == null){
          var value = elem.data('description');
        } else if (settings.text == "innerHTML"){
          var value = elem.text();
        }
        $('#hoverdiv').text(value);

        var top = elem.offset().top;
        var left = elem.offset().left;

        var width = elem.width()/2;
        var dwidth = $('#hoverdiv').width()/2;

        var padding = parseInt(elem.css('padding-left'));
        var dpadding = parseInt($('#hoverdiv').css('padding-left'));

        var height = parseInt(elem.outerHeight());
        var dheight = parseInt($('#hoverdiv').outerHeight());

        $('#hoverdiv').css({
          left: left+width-dwidth+padding-dpadding+settings.extraLeft,
          display: "block"
        });

        if(top < (dheight)+16){
          $('#hoverdiv')
            .removeClass('after')
            .addClass('before')
            .css('top', top+height+10+settings.extraTop);
        } else {
          $('#hoverdiv')
            .removeClass('before')
            .addClass('after')
          .css('top', top-height-10-settings.extraTop);
        }

      }).on('mouseleave', function(e){
        $('#hoverdiv').css('display', 'none');
      });

    });
    return this;
  }
}(jQuery));

// PLUGIN FOR HIDE TIME AND SHOW MENU WHEN HOVER OVER POST
(function($){
  $.fn.postHideAndShow = function(options){
    this.each(function(e){

      var elem = $(this);
      var time = elem.find('.p_time');
      var opt_menu = elem.find('.p_h_opt');

      var defaults = {}
      var settings = $.extend({}, defaults, options);

      elem.on('mouseover', function(e){
        time.hide();
        opt_menu.show();
      }).on('mouseout', function(e){
        time.show();
        opt_menu.hide();
      });

    });
    return this;
  }
}(jQuery));

// PLUGIN FOR COMMENT HEIGHT TOGGLE
(function($){
  $.fn.commentToggle = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        elem.removeClass('textarea_toggle');
      });

      elem.on('blur', function(e){
        elem.addClass('textarea_toggle');
      });

    });
    return this;
  }
}(jQuery));

//PLUGIN FOR POST EXTRA OPTIONS TOGGLE
(function($){
  $.fn.postExtraToggle = function(options){
    var defaults = {}
    var settings = $.extend({}, defaults, options);

    var elem = this;
    var div = $('.post_extra');

    elem.on('click', function(e){
      elem.toggleClass('post_extra_toggle');
      div.slideToggle('fast');
      $('.post_it').toggleClass('post_it_br_toggle');
    });
  }
}(jQuery));

//PLUGIN FOR VIDEO CONTROLS
(function($){
  $.fn.videoControls = function(options){
    this.each(function(e){
      var defaults = {}
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      var video = $(this)[0];
      var ctrls = elem.siblings().filter('.p_vid_ctrls');
      var pp_large = elem.siblings().filter('.p_vid_pp_large');
      var play = ctrls.find('.p_vid_pp');
      var volume = ctrls.find('.p_vid_vup');
      var settings = ctrls.find('.p_vid_setting');
      var range = ctrls.find('.p_vid_seek').children();
      var current = ctrls.find('.p_vid_cur');
      var duration = ctrls.find('.p_vid_dur');
      var time_teaser = elem.siblings().filter('.p_vid_time_teaser');
      var volDiv = ctrls.find('.p_vid_vol_div');
      var volSlider = volDiv.children();
      var pbrDiv = ctrls.find('.p_vid_pbr_div');
      var pbr = pbrDiv.find('li');
      var timeBubble = elem.siblings().filter('.p_vid_time_bubble');
      var parent = elem.parent().filter('.p_vid');

      if (video.currentTime == 0) {
        pp_large.html('<i class="material-icons">play_arrow</i>');
        pp_large.show();
        time_teaser.hide();
        pp_large.on('click', function(e){
          play.html('<i class="material-icons">pause</i>');
          video.play();
          pp_large.hide();
          ctrls.show();
        });
      }

      parent.on('mouseover', function(e){
        ctrls.show();
        time_teaser.hide();
      }).on('mouseout', function(e){
        ctrls.hide();
        time_teaser.show();
      });

      play.on('click', function(e){
        if (video.paused) {
          video.play();
          play.html('<i class="material-icons">pause</i>');
          pp_large.hide();
        } else {
          video.pause();
          play.html('<i class="material-icons">play_arrow</i>');
          pp_large.show();
        }
      });

      $(this).on('click', function(e){
        if (video.paused) {
          video.play();
          play.html('<i class="material-icons">pause</i>');
          pp_large.hide();
        } else {
          video.pause();
          play.html('<i class="material-icons">play_arrow</i>');
          pp_large.show();
        }
      });

      range.on('change', function(e){
        var seekto = video.duration * (range.val()/100);
        video.currentTime = seekto;
      });

      $(this).on('loadedmetadata', function(e){
        var durmins = parseInt(Math.floor(video.duration / 60));
        var dursecs = parseInt(Math.floor(video.duration - durmins * 60));
        if (dursecs < 10) {
          dursecs = "0"+dursecs;
        }
        duration.text(durmins+":"+dursecs);
      });

      $(this).on('timeupdate', function(e){
        var nt = video.currentTime * (100/video.duration);
        range.val(nt);
        var curmins = parseInt(Math.floor(video.currentTime / 60));
        var cursecs = parseInt(Math.floor(video.currentTime - curmins * 60));
        var durmins = parseInt(Math.floor(video.duration / 60));
        var dursecs = parseInt(Math.floor(video.duration - durmins * 60));
        if (cursecs < 10) {
          cursecs = "0"+cursecs;
        }
        if (dursecs < 10) {
          dursecs = "0"+dursecs;
        }
        current.text(curmins+":"+cursecs);
        duration.text(durmins+":"+dursecs);
        time_teaser.text(curmins+":"+cursecs+" | "+durmins+":"+dursecs);
      });

      var readableTime = function(t) {
        theMinutes = "0" + Math.floor(t / 60); // Divide seconds to get minutes, add leading zero
        theSeconds = "0" + parseInt(t % 60); // Get remainder seconds
        theTime = theMinutes.slice(-2) + ":" + theSeconds.slice(-2); // Slice to two spots to remove excess zeros
        return theTime;
      }

      var input = elem.siblings().filter('.p_vid_ctrls').find(('input[type="range"]'))[0];

      $(input).on('mousemove', function(e){
        var cursorPos = (e.clientX - (input.getBoundingClientRect().left)) / (input.offsetWidth);
        var seekTime = cursorPos * video.duration;
        if(seekTime) {
          timeBubble.text(readableTime(seekTime));
          var left = e.clientX - input.getBoundingClientRect().left-10;
          timeBubble.css('left', (left) + "px");
          timeBubble.css('display', 'block');
          if (left >= 408) { timeBubble.css('left', (left-20) + "px"); }
        }
      }).on('mouseout', function(e) {
        timeBubble.css('display', 'none');
      });

      volSlider.on('change', function(e){
        var vol = volSlider.val() / 100;
        video.volume = vol;
        if (vol == 0) {
          volume.html('<i class="material-icons">volume_off</i>');
        } else if (vol <= 0.3) {
          volume.html('<i class="material-icons">volume_mute</i>');
        } else if (vol <= 0.6){
          volume.html('<i class="material-icons">volume_down</i>');
        } else {
          volume.html('<i class="material-icons">volume_up</i>');
        }
      });

      volume.on('click', function(e){
        if (video.muted) {
          video.muted = false;
          volume.html('<i class="material-icons">volume_up</i>');
          video.volume = 1;
          volSlider.prop('value', '100');
        } else {
          video.muted = true;
          volume.html('<i class="material-icons">volume_off</i>');
        }
      });

      settings.on('click', function(e){
        pbrDiv.toggle();
      });

      pbr.on('click', function(e){
        var data = $(this).data('pbr');
        $(this).siblings().removeClass('pbr_class');
        $(this).addClass('pbr_class');
        video.playbackRate = data;
        settings.text(data+"x");
        pbrDiv.hide();
      });

    });
    return this;
  }
}(jQuery));

//PLUGIN FOR AUDIO CONTROLS
(function($){
  $.fn.audioControls = function(options){
    this.each(function(e){
      var defaults = {}
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var song = elem.data('song');

      var ctrls = elem.find('.p_aud_ctrls');
      var title = elem.find('.p_aud_name');
      var play = elem.find('.p_aud_pp');
      var range = elem.find('.p_aud_seek_range');
      var current = elem.find('.p_aud_cur');
      var duration = elem.find('.p_aud_dur');
      var volDiv = elem.find('.p_aud_vol_div');
      var volSlider = volDiv.children();
      var volume = elem.find('.p_aud_vup');
      var timeBubble = elem.find('.p_aud_time_bubble');

      var audio = new Audio(song);
      audio.loop = true;

      var simple = song.substr(song.indexOf('media/')+6);
      var first = simple.substr(0, simple.lastIndexOf('.'));
      title.text(first);

      // if (settings.hover == "no") {
      //   elem.on('mouseover', function(e){
      //     $('.p_aud_ctrls').addClass('p_aud_toggle');
      //   }).on('mouseout', function(e){
      //     $('.p_aud_ctrls').removeClass('p_aud_toggle');
      //   });
      // }

      play.on('click', function(e){
        if (audio.paused) {
          audio.play();
          play.html('<i class="material-icons">pause</i>');
        } else {
          audio.pause();
          play.html('<i class="material-icons">play_arrow</i>');
        }
      });

      $('.p_aud_img').on('click', function(e){
        if (audio.paused) {
          audio.play();
          play.html('<i class="material-icons">pause</i>');
        } else {
          audio.pause();
          play.html('<i class="material-icons">play_arrow</i>');
        }
      });

      range.on('change', function(e){
        var seekto = audio.duration * (range.val()/100);
        audio.currentTime = seekto;
      });

      $(audio).on('loadedmetadata', function(e){
        var durmins = parseInt(Math.floor(audio.duration / 60));
        var dursecs = parseInt(Math.floor(audio.duration - durmins * 60));
        if (dursecs < 10) {
          dursecs = "0"+dursecs;
        }
        duration.text(durmins+":"+dursecs);
      });

      $(audio).on('timeupdate', function(e){
        var nt = audio.currentTime * (100/audio.duration);
        range.val(nt);
        var curmins = parseInt(Math.floor(audio.currentTime / 60));
        var cursecs = parseInt(Math.floor(audio.currentTime - curmins * 60));
        var durmins = parseInt(Math.floor(audio.duration / 60));
        var dursecs = parseInt(Math.floor(audio.duration - durmins * 60));
        if (cursecs < 10) {
          cursecs = "0"+cursecs;
        }
        if (dursecs < 10) {
          dursecs = "0"+dursecs;
        }
        current.text(curmins+":"+cursecs);
        duration.text(durmins+":"+dursecs);
      });

      var readableTime = function(t) {
        theMinutes = "0" + Math.floor(t / 60);
        theSeconds = "0" + parseInt(t % 60);
        theTime = theMinutes.slice(-2) + ":" + theSeconds.slice(-2);
        return theTime;
      };

      var input = elem.find(('input[type="range"]'))[0];

      $(input).on('mousemove', function(e){
        var cursorPos = (e.clientX - (input.getBoundingClientRect().left)) / (input.offsetWidth);
        var seekTime = cursorPos * audio.duration;
        if(seekTime) {
          timeBubble.text(readableTime(seekTime));
          var left = e.clientX - input.getBoundingClientRect().left+13;
          timeBubble.css('left', (left) + "px");
          timeBubble.css('display', 'block');
        }
      }).on('mouseout', function(e) {
        timeBubble.css('display', 'none');
      });

      volume.on('click', function(e){
        volDiv.toggle();
      });

      volSlider.on('change', function(e){
        var vol = volSlider.val() / 100;
        audio.volume = vol;
        if (vol == 0) {
          volume.html('<i class="material-icons">volume_off</i>');
        } else if (vol <= 0.3) {
          volume.html('<i class="material-icons">volume_mute</i>');
        } else if (vol <= 0.6){
          volume.html('<i class="material-icons">volume_down</i>');
        } else {
          volume.html('<i class="material-icons">volume_up</i>');
        }
      });

    });
    return this;
  }
}(jQuery));

// PLUGIN FOR VIEWING AVATAR
(function($){
  $.fn.viewAvatar = function(options){
    this.each(function(e){

      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var overlay = $('.overlay');
      var div = $('.view_profile');
      var input = div.find('input[type="range"]');
      var img = div.find('.v_p_img > img');

      input.on('change', function(e){
        img.css('width', $(this).val());
      });

      elem.on('click', function(e){
        blur.addBlur();
        overlay.show();
        div.fadeIn('fast');
        overlay.addClass('overlay_cursor');
        overlay.one('click', function(e){
          overlay.hide();
          blur.removeBlur();
          div.fadeOut('fast');
          overlay.removeClass('overlay_cursor');
          input.val('200');
          img.css('width', '200px');
        });
      });

    });
    return this;
  }
}(jQuery));

//PLUGIN FOR CHANGING AVATAR
(function($){
  $.fn.changeAvatar = function(options){
    this.each(function(e){
      var defaults = {
        when: "user"
      }
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var src;
      var div = $('.pro_avatars');
      var preview = $('.pro_preview');
      var img = div.find('.pro_ava_avts');
      var cross = div.find('.pro_ava_close');
      var close = div.find('.pro_ava_bottom_act').children().eq(0);
      var apply = div.find('.pro_ava_bottom_act').children().eq(1);
      var overlay = $('.overlay');
      var input = $('.pro_ch_ava');

      elem.on('click', function(e){
        blur.addBlur();
        $('.overlay').show();
        $('.pro_avatars').fadeIn("fast");
      });

      if (settings.when == "group") {
        div.find("input[type='file']")
          .removeClass('pro_ch_ava')
          .addClass('grp_ch_ava')
          .attr('data-grp', elem.data('grp'));
      } else if (settings.when == "user") {
        div.find("input[type='file']")
          .removeClass('grp_ch_ava')
          .addClass('pro_ch_ava');
      }

      function commonC(){
        div.fadeOut('fast');
        overlay.hide();
        blur.removeBlur();
        preview.fadeOut('fast');
        $('.pro_ch_ava').val('');
        $('.pro_ava_middle').stop().animate({scrollTop: -20}, "fast");
      }

      var closes = [cross, close];
      for (ele of closes) {
        ele.on('click', function(e){
          e.preventDefault();
          commonC();
        });
      }

      img.on('click', function(e){
        $(this).siblings().removeClass('pro_ava_active');
        $(this).addClass('pro_ava_active');
        src = $(this).prop('src');
        apply.focus();
      });

      apply.on('click', function(e){
        e.preventDefault();
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/change_avatar.php",
          method: "GET",
          data: {change_avatar: src, change_when: settings.when, change_grp: elem.data('grp')},
          success: function(data){
            if (data != "") {

              if (settings.when == "user") {
                $('.pro_avatar > img').prop('src', DIR+'/'+data);
                $('.sp > img').prop('src', DIR+'/'+data);
                $('.v_p_img > img').prop('src', DIR+'/'+data);
              } else if (settings.when == "group") {
                $('.pro_avatar > img').prop('src', DIR+'/'+data);
              }

              $('.notify').notify({ value: 'Avatar changed!' });
              commonC();

            } else if (data == "") {
              $('.notify').notify({ value: 'Please select a avatar' });
              apply.blur();
            }
          }
        });
      });

    });
    return this;
  }
}(jQuery));

// PLUGIN FOR UPLOADING AVATAR
(function($){
  $.fn.uploadAvatar = function(options){
    this.each(function(e){
      var defaults = {when: "user"};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var preview = $('.pro_preview');
      var form = $('.pro_ch_form');
      var cancel = $('.pro_pre_cancel');
      var input = $('.pro_ch_ava');
      var cropper = $('.pro_crop');
      var crop_tool = $('.pro_crop_tool');
      var un = $('.unclickable');

      elem.on('change', function(e){
        var file = this.files[0];
        var type = file.type;
        var allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if (((type == allowed[0]) || (type == allowed[1]) || (type == allowed[2]))) {
          var reader = new FileReader();

          reader.onload = function(e){

            $('.pro_pre_img > img').prop('src', e.target.result);
            un.show();
            preview.fadeIn('fast');
            form.find('input[type="submit"]').focus();

            var height = $('.pro_pre_img > img').height();
            if (height%2 != 0) {
              $('.pro_pre_img > img').height(height+1+"px");
            }

          }
          reader.readAsDataURL(file);
        } else {
          input.val('');
        }
      });

      cancel.on('click', function(e){
        e.preventDefault();
        un.hide();
        preview.fadeOut('fast');
        input.val('');
      });

      form.on('submit', (function(e){
        e.preventDefault();
        $.ajax({
          url: '../ajaxify/ajax_requests/change_avatar.php',
          contentType: false,
          processData: false,
          dataType: "json",
          method: "POST",
          data: new FormData(this),
          success: function(data){

            var img_name = data.name;
            $('.pro_avatars').fadeOut('fast');
            $('.overlay').show();
            blur.addBlur();
            $('.pro_crop_img > img').attr('src', '../temp/resized/Resized_'+img_name);
            cropper.fadeIn('fast');
            if ($('.pro_crop_img > img').width() < 400) {
              var hmm = $('.pro_crop_img > img').css('width');
              $('.pro_crop_img').css('width', hmm+"px");
            }

            $('.pro_crop_cancel').on('click', function(e){
              e.preventDefault();
              cropper.fadeOut('fast');
              $('.overlay').hide();
              blur.removeBlur();
              input.val('');
              un.hide();
              preview.hide();
              cropper.hide();
            });

            $('.pro_crop_done').on('click', function(e){
              e.preventDefault();
              var name = $('.pro_crop_img > img').attr('src');
              var top = crop_tool.position().top;
              var left = crop_tool.position().left;
              var width = crop_tool.width();
              var height = crop_tool.height();

              $.ajax({
                url: "../ajaxify/ajax_requests/change_avatar.php",
                method: "POST",
                data: {
                  top: top,
                  left: left,
                  width: width,
                  height: height,
                  name: name,
                  upload_when: settings.when,
                  upload_grp: elem.data('grp')
                },
                success: function(data){

                  if (settings.when == "user") {
                    $('.pro_avatar > img').prop('src', DIR+'/users/'+data);
                    $('.sp > img').prop('src', DIR+'/users/'+data);
                    $('.v_p_img > img').prop('src', DIR+'/users/'+data);

                  } else if (settings.when == "group") {
                    $('.pro_avatar > img').prop('src', DIR+'/group/'+data);
                  }

                  un.hide();
                  cropper.fadeOut('fast');
                  $('.overlay').hide();
                  blur.removeBlur();
                  input.val('');
                  preview.hide();
                  cropper.hide();
                  $('.notify').notify({
                    value: 'Your avatar is changed'
                  });
                }
              });

            });

          }
        });
      }));

    });
    return this;
  }
}(jQuery));

//FUNCTION FOR UPDATING NO OF FOLLOWERS AND NO OF FOLLOWINGS
function getFF(data){
  var followers = data.followers;
  var followings = data.followings;
  var followersDiv = $('.no_of_followers');
  var followingsDiv = $('.no_of_followings');
  console.log(followers+" "+followings);
  followersDiv.text(followers);
  followingsDiv.text(followings);
}

//PLUGIN FOR FOLLOW
(function($){
  $.fn.follow = function(options){
    this.each(function(e){
      var defaults = {
        update: false
      };
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var parent = elem.parent();
      var to = parent.data('getid');
      var update_id = $('.user_info').data('userid');

      elem.on('click', function(e){
        e.preventDefault();
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/follow_requests.php",
          method: "GET",
          dataType: "json",
          data: {follow: to, updateid: update_id},
          beforeSend: function(){
            elem.text('Wait..');
          },
          success: function(data){
            console.log(data);
            if (data.status == "ok") {
              elem.text('Follow').remove();
              parent.html("<a href='#' class='pri_btn unfollow'>Unfollow</a>");
              $('.unfollow').unfollow({
                update: settings.update
              });
              if (settings.update) {
                getFF(data);
              }
            } else if(data.status == "Already followed") {
              elem.text('Followed');
            } else {
              elem.text('Follow');
            }
          }
        });
      });

    });
    return this;
  }
}(jQuery));

//PLUGIN FOR UNFOLLOW
(function($){
  $.fn.unfollow = function(options){
    this.each(function(e){
      var defaults = {
        update: false
      };
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var parent = elem.parent();
      var to = parent.data('getid');
      var update_id = $('.user_info').data('userid');

      elem.on('click', function(e){
        e.preventDefault();
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/follow_requests.php",
          method: "GET",
          dataType: "json",
          data: {unfollow: to, updateid: update_id},
          beforeSend: function(){
            elem.text('Wait..');
          },
          success: function(data){
            elem.remove().text('Unfollow');
            parent.html("<a href='#' class='pri_btn follow'>Follow</a>");
            if (settings.update){
              getFF(data);
            }
            $('.follow').follow({
              update: settings.update
            });
          }
        });
      });

    });
    return this;
  }
}(jQuery));

// SIMPLE UNFOLLOW
(function($){
  $.fn.simpleUnfollow = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = $(this);

    elem.on('click', function(e){
      e.preventDefault();
      var post = $(this).parent().parent().parent().parent().parent();
      var user = post.find('.p_i_1 > a').text();
      $.ajax({
        url: DIR+"/ajaxify/ajax_requests/follow_requests.php",
        data: {simple_unfollow: user},
        success: function(data){
          console.log(data);
          $('.notify').notify({
            value: "Unfollowed "+user
          });
        }
      });
    });

  }
}(jQuery));

// FUNCTION FOR GETTING ALL FOLLOWERS
(function($){
  $.fn.followers = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);
      var elem = $(this);

      var userid = $('.user_info').data('userid');
      var sessionid = $('.user_info').data('sessionid');
      var username = $('.user_info').data('username');
      if (userid == sessionid) {
        var info = "Your followers";
      } else {
        var info = nameShortener(username, 20)+"'s followers";
      }

      elem.on('click', function(e){
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/follow_requests.php",
          method: "GET",
          data: {followers: userid},
          beforeSend: function(){
            $('.display_content').html("<div class='spinner'><span></span><span></span><span></span></div>");
          },
          success: function(data){
            $('.display_content')
              .html(data)
              .hide().slideDown(100)
              .children().eq($('.display_content').children().length-1).children().filter('hr').remove();
            $('.display_follow').follow({ update: true });
            $('.display_unfollow').unfollow({ update: true });
            $('.display').displayOptions({
              title: info,
              separateLink: true,
              separateLinkURL: DIR+"/profile/"+username+"?ask=followers"
            });
          }
        });
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION FOR GETTING ALL FOLLOWINGS
(function($){
  $.fn.followings = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);
      var elem = $(this);

      var userid = $('.user_info').data('userid');
      var sessionid = $('.user_info').data('sessionid');
      var username = $('.user_info').data('username');
      if (userid == sessionid) {
        var info = "You following";
      } else {
        var info = nameShortener(username, 20)+"'s followings";
      }

      elem.on('click', function(e){
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/follow_requests.php",
          method: "GET",
          data: {followings: userid},
          beforeSend: function(){
            $('.display_content').html("<div class='spinner'><span></span><span></span><span></span></div>");
          },
          success: function(data){
            $('.display_content')
              .html(data)
              .hide().slideDown(100)
              .children().eq($('.display_content').children().length-1).children().filter('hr').remove();
            $('.display_follow').follow({ update: true });
            $('.display_unfollow').unfollow({ update: true });
            $('.display').displayOptions({
              title: info,
              separateLink: true,
              separateLinkURL: DIR+"/profile/"+username+"?ask=followings"
            });
          }
        });
      });

    });
    return this;
  }
}(jQuery));

//PLUGIN FOR DISPLAY FUNCTIONALITY
(function($){
  $.fn.displayOptions = function(options){
    var defaults = {
      title: "Instagram",
      separateLink:  false,
      separateLinkURL: "#"
    };
    var settings = $.extend({}, defaults, options);

    var elem = $(this);
    var info = elem.find('.display_info > span');
    var cancel = elem.find('.display_cancel');
    var done = elem.find('.display_done');
    var overlay = $('.overlay');

    var arr = [cancel, done];
    for (ele of arr) {
      ele.on('click', function(e){
        e.preventDefault();
        blur.removeBlur();
        overlay.hide();
        elem.fadeOut('fast');
        elem.find('.display_content').html('');
        elem.find('.display_bottom').html('<span class="display_sep"></span><a href="#" class="pri_btn display_done">Exit</a>');
      });
    }

    info.text(settings.title);

    if (settings.separateLink == true) {
      done.before("<a href='"+ settings.separateLinkURL +"' class='sec_btn display_separate'>Open separately</a>");
    }

    blur.addBlur();
    overlay.show();
    elem.fadeIn('fast');
    done.focus();
    // elem.show().addClass('animated slideInDown');
  }
}(jQuery));

//PLUGIN FOR PROFILE VIEWERS
(function($){
  $.fn.profileViewers = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var userid = $('.user_info').data('userid');
      var sessionid = $('.user_info').data('sessionid');
      var username = $('.user_info').data('username');
      if (userid == sessionid) {
        var info = "Your profile views";
      } else {
        var info = username+"'s profile views";
      }

      elem.on('click', function(e){
        $.ajax({
          url: "../ajaxify/ajax_requests/follow_requests.php",
          method: "GET",
          data: {pro_viewers: userid},
          beforeSend: function(){
            $('.display_content').html("<div class='spinner'><span></span><span></span><span></span></div>");
          },
          success: function(data){
            $('.display_content').html(data);
            $('.display_content').hide().slideDown(100);
            $('.display_content').children().eq($('.display_content').children().length-1).children().filter('hr').remove();
            $('.display_follow').follow({
              to: $(this).parent().data('byid')
            });
            $('.display_unfollow').unfollow({
              to: $(this).parent().data('byid')
            });
            $('.display').displayOptions({
              title: info,
              separateLink: true,
              separateLinkURL: "#"
            });
          }
        });
      });

    });
    return this;
  }
}(jQuery));

//PLUGIN FOR EMOJIS
(function($){
  $.fn.emoji = function(options){
    this.each(function(e){
      var defaults = {
        pseudo: "right",
        textarea: $('.t_p_main > textarea'),
        top: null,
        left: null,
        event: "click"
      };
      var settings = $.extend({}, defaults, options);
      var elem = $(this);
      var emoji = $('.emoji');
      var items = emoji.find('li');

      $('.emoji > .emoji_wrapper').perfectScrollbar();

      emoji.css({
        "top": settings.top,
        "left": settings.left
      });

      function emojiClassAdder(classObj){
        emoji.removeClass('emoji_before');
        emoji.removeClass('emoji_after');
        emoji.removeClass('emoji_right');
        emoji.removeClass('emoji_right_bnright');
        if(classObj.empty == false){
          emoji.addClass(classObj.cl);
        }
      }

      if (settings.pseudo == "after") {
        emojiClassAdder({ empty: false, cl: "emoji_after" });

      } else if (settings.pseudo == "before") {
        emojiClassAdder({ empty: false, cl: "emoji_before" });

      } else if (settings.pseudo == "right") {
        emojiClassAdder({ empty: false, cl: "emoji_right" });

      } else if (settings.pseudo == "right_bn") {
        emoji.css("border", "none");
        emojiClassAdder({ empty: false, cl: "emoji_right_bn" });

      } else if (settings.pseudo == null) {
        emojiClassAdder({ empty: true, cl: "" });
      }

      if (settings.event == "click") {
        elem.on('click', function(e){
          $('.emoji').toggle();
        });
      } else if (settings.event == "hover") {
        elem.on('mouseover', function(e){
          emoji.show();
        });
        emoji.on('mouseover', function(){
          $(this).show().on('mouseout', function(){
            $(this).hide();
          })
        });

      }

      items.on('click', function(e){
        items.removeClass('emoji_toggle');
        $(this).addClass('emoji_toggle');
        var start = settings.textarea.prop('selectionStart');
        var text = $(this).text();
        var value = settings.textarea.val();
        var textBefore = value.substr(0, start);
        var textAfter = value.substr(start, value.length);
        settings.textarea.val(textBefore+text+textAfter);
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION FOR STICKERS
(function($){
  $.fn.sticker = function(options){
    var defaults = {
      when: null,
      mssgTo: null,
      mssgId: null,
      commRefresh: "no"
    };
    var settings = $.extend({}, defaults, options);

    var elem = this;
    var div = $('.stickers');
    var sticker = div.find('img');
    var overlay = $('.overlay');
    var hidden = div.find('.sti_hidden');

    elem.on('click', function(e){
      overlay.show();
      blur.addBlur();
      div.fadeIn('fast');

      sticker.on('click', function(e){
        sticker.removeClass('sti_img_active');
        $(this).addClass('sti_img_active');
        var src = $(this).prop('src');
        hidden.val('');
        hidden.val(src);
        div.find('.sti_done').focus();
      });

      function stickerCommonSuccess(){
        div.find('.sticker_mssg').text('');
        div.find('.sti_done').removeClass('a_disabled');
        sticker.removeClass('sti_img_active');
        overlay.hide();
        blur.removeBlur();
        div.fadeOut('fast');
        hidden.val('');
      }

      function stickerNoData(){
        div.find('.sti_done').blur();
        div.find('.sti_done').removeClass('a_disabled');
        div.find('.sticker_mssg').text('Please select a sticker');
      }

      function rr(data){
        div.find('.sti_done').addClass('a_disabled');
        var value = hidden.val();
        if (value != "") {
          $.ajax({
            url: DIR+"/ajaxify/ajax_requests/message_requests.php",
            data: data,
            dataType: "JSON",
            beforeSend: function(){
              div.find('.sticker_mssg').text('Posting selected sticker..');
            },
            success: function(data){
              console.log(data);
              stickerCommonSuccess();
              var ht = "<div class='m_m_divs my_mm_div'><div class='m_m my_mm'><img src='"+ DIR +"/"+ data.sticker +"' class='m_m_sticker'></div><span class='m_m_time'>Just now</span></div>";
              $('.mssg_helper').before(ht);
              $('.sti_done').off('click');
              sticker.removeClass('sti_img_active');
              $('.m_m_wrapper').animate({scrollTop: 100000}, 500);
            }
          });
        } else if (value == "") {
          stickerNoData();
        }
      }

      if (settings.when == "message") {
        div.find('.sti_done').on('click', function(e){
          e.preventDefault();
          var d = {
            sticker: hidden.val(),
            stickerTo: settings.mssgTo,
            stickerCon: settings.mssgId,
            stickerBy: "user"
          };
          rr(d);
        });

      } else if (settings.when == "group_message") {
        div.find('.sti_done').on('click', function(e){
          e.preventDefault();
          var d = {
            sticker: hidden.val(),
            stickerCon: settings.mssgId,
            stickerBy: "group"
          };
          rr(d);
        });

      } else if (settings.when == "comment") {
        var post = $(this).parent().parent().parent();
        var commCount = post.find('.p_comments')
        var postid = post.data('postid');

        div.find('.sti_done').on('click', function(e){
          e.preventDefault();
          $(this).addClass('a_disabled');
          var value = hidden.val();
          if (value != "") {
            $.ajax({
              url: DIR+"/ajaxify/ajax_requests/post_comment_requests.php",
              data: {
                commSticker: value,
                commStickerPost: postid
              },
              dataType: "JSON",
              beforeSend: function(data){
                div.find('.sticker_mssg').text('Posting selected sticker..');
              },
              success: function(data){
                console.log(data);
                stickerCommonSuccess();
                commCount.text(data.comments);
                $('.notify').notify({value: "You commented"});
                if (settings.commRefresh == "yes") {
                  setTimeout(function () { location.reload(); }, 300);
                }
              }
            });
          } else if (value == "") {
            stickerNoData();
          }
        });

      }

      div.find('.sti_cancel').on('click', function(e){
        e.preventDefault();
        stickerCommonSuccess();
      });

    });

  }
  return this;
}(jQuery));

//PLUGIN FOR EDITING PROFILE
(function($){
  $.fn.editProfile = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = this;
    var basic_input = $('.edit_main').find('input[type="text"]');
    // var sm = elem.find('.edit_sm_div > input[type="text"]');
    var textarea = elem.find('textarea');
    var tags;
    var add_text = elem.find('.add_tag_text');
    var add_btn = elem.find('.add_tag_add');
    var update = elem.find('.edit_update_a');
    var emoji = $('.emoji');
    var items = emoji.find('li');
    var vl = elem.find('.resend_vl');
    var o2 = $('.overlay-2');

    function getTags(){
      var ff = $('.tags_all > span');
      var array = [];
      for (var i = 0; i < ff.length; i++) {
        console.log(ff[i]);
        array[i] = ff[i].innerHTML;
        // console.log(array);
      }
      var string = array.join(',');
      $('.tags_hidden').val(string);
    }

    var cLick = function(elem){
      elem.fadeOut('fast');
      elem.remove();
      getTags();
    }

    var funWithTags = function(){
      var value = add_text.val();
      if (value == "") {
        add_text.focus();
      } else if (value != "") {
        $('.insert_helper').after("<span class='t_a_tag know'>"+ value.trim() +"</span>");
        add_text.val('');
        add_text.focus();
        getTags();
        $('.know').on('click', function(e){
          cLick($(this));
        });
      }
    }

    $('.edit_update span').emoji({
      pseudo: null,
      textarea: textarea,
      top: "319px",
      left: "180px",
      event: "click"
    });

    $('.edit_em_mobile').on('keyup', function(e){
      var regex = /[^0-9]/i;
      var value = $(this).val();
      $(this).val(value.replace(regex, ""));
    });

    $('.add_tag_text').on('keyup', function(e){
      var regex = /[\s]/i;
      var value = $(this).val();
      $(this).val(value.replace(regex, "-"));
    });

    add_text.on('focus', function(e){
      $(window).on('keypress', function(e){
        var event = ((e.which) ? e.which : e.keyCode);
        if (event == "13") {
          funWithTags();
        }
      });
    });

    add_btn.on('click', function(e){
      e.preventDefault();
      funWithTags();
    });

    $('.t_a_tag').on('click', function(e){
      cLick($(this));
    });

    getTags();
    var tags = $('.tags_hidden').val();

    function updating(e){
      e.preventDefault();
      var username = elem.find('.edit_un_text');
      var firstname = elem.find('.edit_fn_text');
      var surname = elem.find('.edit_sn_text');
      var bio = elem.find('.edit_ta');
      var instagram = elem.find('.edit_em_instagram');
      var youtube = elem.find('.edit_em_youtube');
      var facebook = elem.find('.edit_em_facebook');
      var twitter = elem.find('.edit_em_twitter');
      var website = elem.find('.edit_em_website');
      var mobile = elem.find('.edit_em_mobile');
      tags = elem.find('.tags_hidden');

      if (username.val() == "") {
        $('.notify').notify({value: "Username is empty"});
      } else if (firstname.val() == "") {
        $('.notify').notify({value: "Firstname is empty"});
      } else if (surname.val() == "") {
        $('.notify').notify({value: "Surname is empty"});
      } else {

        update
          .text('Updating..')
          .addClass('a_disabled');
        o2.show();
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/edit_requests.php",
          method: "POST",
          dataType: "JSON",
          data: {
            username: username.val(),
            firstname: firstname.val(),
            surname: surname.val(),
            bio: bio.val(),
            instagram: instagram.val(),
            youtube: youtube.val(),
            facebook: facebook.val(),
            twitter: twitter.val(),
            website: website.val(),
            mobile: mobile.val(),
            tags: tags.val()
          },
          success: function(data){
            update
              .text('Update profile')
              .removeClass('a_disabled');
            o2.hide();
            $('.notify').notify({ value: data.mssg });
            if (data.mssg == "Profile updated!!"){ location.reload(); }

          }
        });

      }

    }

    update.on('click', function(e){
      updating(e);
    });

    vl.on('click', function(e){
      vl
        .text('Sending verification link..')
        .addClass('sec_btn_disabled')
        .blur();

      o2.show();

      e.preventDefault();
      $.ajax({
        url: DIR + "/ajaxify/ajax_requests/edit_requests.php",
        method: "POST",
        dataType: "JSON",
        data: { resend_vl: "yes" },
        success: function(data){
          console.log(data);
          $('.notify').notify({ value: data.mssg });
          vl
            .text('Send verification link')
            .removeClass('sec_btn_disabled');

          o2.hide();
        }

      })
    });

  }
}(jQuery));

// HELP ICON
(function($){
  $.fn.help = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);
    var icon = this.find('.help_icon');
    var info = this.find('.help_info');
    icon.on('click', function(e){
      info.toggle();
      $(this).find('span').toggleClass('help_toggle');
    });
  }
}(jQuery));

// SUCCESS FUNCTION AFTER FETCHING DATA
function s(data){
  $('.feed_inserted').html('Looks like you\'ve reached the end');
  $('.feed_inserted').after(data);
  $('.feed_inserted').not(':last').remove();

  // FOR POST OPTIONS
  $('.posts').ToggleMenu({
    btn: $('.exp_p_menu'),
    menu: $('.p_options')
  });

  //callig the hide and show plugin
  $('.posts').postHideAndShow();

  //COMMENT HEIGHT TOGGLE
  $('.p_cit_area > textarea').commentToggle();

  //Autosize of textarea
  $('.p_cit_area textarea').addClass('textarea_d_height');
  autosize($('textarea'));
  //hover over description for post
  $('.p_cit_more, .p_do > div > span, .c_sticker').description();
  $('.p_cit_area span').description({
    extraTop: 5
  });
  //calling videoControls plugin
  $('video').videoControls();

  //calling audioControls plugin
  $('.p_aud').audioControls();
  $('.display_middle').perfectScrollbar();
  // POST LIKE
  $('.p_like').postLike();

  // POST UNLIKE
  $('.p_unlike').postUnlike();

  // POST BOOKMARK
  $('.p_bookmark').postBookmark();

  // POST UNBOOKMARK
  $('.p_unbookmark').postUnbookmark();

  // POST SHARE
  $('.p_send').postShare();

  // POST COMMENT
  $('.comment_teaser').postComment();
  $('.p_comm_file_teaser').imageComment({refresh: "no"});
  $('.c_sticker_trailer').sticker({
    when: "comment",
  });

  // POST LIKERS
  $('.likes').likes();

  // POST TAGGERS
  $('.p_tags').taggers();

  // POST SHARERS
  $('.p_h_opt > .p_comm').shares();
  $('.untag').untag();
  $('.delete_post').deletePost();
  $('.p_img').imageShow();
  $('.unshare').unshare();
  $('.un__share').removeShare();
  $('.simple_unfollow').simpleUnfollow();
  $('.p_copy_link').copyPostLink();
  $('.mutual_links').description({extraTop: -20});

  $('.p_comments').on('click', function(e){
    var post = $(this).parent().data('postid');
    window.location.href = DIR+"/view_post/"+post;
  });

  $('.post_end').on('click', function(e){
    $('html, body').animate({scrollTop: 0}, 450);
  });

  $('.block').block();
  $('.edit_post').editPost();
  $('.follow').follow({ update: true });
  $('.unfollow').unfollow({ update: true });
  $('.home_recomm').HomeSuggestions();
  if ($('.recomm_main').children().length == 0) {
    $('.recomm_main').html("<div class='home_last_mssg suggest_last_mssg'><img src='"+ DIR +"/images/needs/large.jpg'></div>");
  }
  $('.load_more_text').load_more_of_post({ type: "text" });
}

// PLUGIN FOR PROFILE NAVIGATION
(function($){
  $.fn.profileNav = function(options){
    this.each(function(e){
      var defaults = {

      };
      var settings = $.extend({}, defaults, options);
      var elem = $(this);

      var link = $('.inst_nav');
      var username = $('.user_info').data('username');

      var fetchAndInsert = function(href){
        $.ajax({
          url: DIR+'/ajaxify/profile_sections/'+href.split('=').pop(),
          method: "GET",
          data: {u: username},
          beforeSend: function(e){
            $('.hmm').html('<div class="spinner"><span></span><span></span><span></span></div>');
            $('.hmm > .spinner').addClass('hmm_spinner_show');
          },
          success: function(data){
            $('.hmm > .spinner').removeClass('hmm_spinner_show');
            // link.removeClass('pro_nav_active');

            if (href.indexOf('&') > -1) {
              var f = href.substr(href.indexOf('=')+1);
              var get = f.substr(0, f.indexOf('&'));
            } else {
              var get = href.substr(href.indexOf('=')+1);
            }

            var main = get.substr(0, get.lastIndexOf("."));
            console.log(main);
            // var p = $('.inst_nav[href="'+ main +'"]').parent().parent();

            $('.inst_nav').removeClass('pro_nav_active');
            $(".inst_nav[href='"+ main +"']").addClass('pro_nav_active');
            $('.hmm').html(data).hide().fadeIn(100);
          }
        });
      }

      $(window).on('popstate', function(e){
        // console.log(location.pathname+location.search);
        // console.log((location.pathname+location.search).spilt('/').pop());
        var main = location.pathname+location.search;
        if (location.search) {
          fetchAndInsert(main+".php");
        } else {
          fetchAndInsert("posts.php");
        }
      });

      link.on('click', function(e){
        e.preventDefault();
        link.removeClass('pro_nav_active');
        $(this).addClass('pro_nav_active');
        var url = $(this).attr('href');
        // var hint = $(this).data('hint');
        var vAr = $(this).data('src');
        fetchAndInsert(url+".php");
        history.pushState({}, '', location.pathname+"?"+vAr+"="+url);
        // console.log('hint: '+hint);
        // var l = location.search;
        // console.log(l.substr(l.indexOf("=")+1));
        $("html, body").animate({ scrollTop: 380 }, "slow");
      });

    });
    return this;
  }
}(jQuery));

// PLUGIN FOR IMAGE SHOW
(function($){
  $.fn.imageShow = function(options){
    this.each(function(e){
      var defaults = {
        info: "no"
      };
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var overlay = $('.overlay');
      var show = $('.image_show');

      elem.on('click', function(e){

        var src = $(this).attr('src');
        show.find('img').attr('src', src).addClass(elem.data('filter'));

        if (settings.info == "yes") {
          $('.img_s_by').text('By '+nameShortener(elem.data('imgby'), 20)+" ("+ elem.data('time') +")");
          $('.img_s_bottom').show();
          show.find('.img_s_window')
            .show()
            .attr('href', DIR+'/view_post/'+elem.data('postid'));

        } else if (settings.info = "no_post_yes") {
          $('.img_s_by')
            .text('By '+nameShortener(elem.data('imgby'), 20)+" ("+ elem.data('time') +")")
            .show();
          show.find('.img_s_window').hide();

        } else if(settings.info == "no") {
          $('.img_s_bottom').hide();
        }

        overlay.addClass('overlay_toggle').show();
        blur.addBlur();
        show.fadeIn('fast');
        overlay
          .addClass('overlay_cursor')
          .one('click', function(e){
            $(this)
              .hide()
              .removeClass('overlay_toggle');
            blur.removeBlur();
            show
              .fadeOut('fast')
              .find('img').removeClass();
            $(this).removeClass('overlay_cursor');
          });

      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO RECOMMED
(function($){
  $.fn.recommend = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var get = $('.user_info').data('userid');

      elem.on('click', function(e){
        e.preventDefault();

        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/recommend_requests.php",
          method: "POST",
          data: {getFollowings: get},
          beforeSend: function(){
            $('.display_content').html("<div class='spinner'><span></span><span></span><span></span></div>");
          },
          success: function(data){
            if (data != "") {
              $('.display_content')
                .html(data)
                .hide().slideDown(100);
              $('.display').displayOptions({
                title: "Recommend  to"
              });
            }

            function recommendFinal(user, get){
              $.ajax({
                url: DIR+"/ajaxify/ajax_requests/recommend_requests.php",
                dataType: "JSON",
                data: {
                  recommend: user,
                  get: get
                },
                success: function(data){
                  console.log(data);
                  $('.pro_recomm > .pro_hg').text(data.count);
                  $('.notify').notify({
                    value: data.info
                  });
                  $('.overlay').hide();
                  blur.removeBlur();
                  $('.display').fadeOut('fast');
                }
              });
            }

            $('.select_receiver').on('click', function(e){
              $('.select_receiver').removeClass('select_receiver_toggle');
              $(this).addClass('select_receiver_toggle');
              var user = $(this).data('userid');
              var username = $(this).find('.d_i_username').text();
              recommendFinal(user, get);
            });

          }
        });

      });

    });
    return this;
  }
}(jQuery));

//FUNCTION TO BLOCK USERS
(function($){
  $.fn.block = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        e.preventDefault();
        var username = $(this).data('username');
        $('.prompt').myPrompt({
          title: "Block "+username,
          value: username+" will no longer be able to follow, message, comment, recommend or add you in any group. Instagram won't let "+ username+" know you blocked him/her. You can unblock from settings.",
          doneText: "Block",
          type: "block_user",
          post: $(this)
        });
        // bl__ock($(this));
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO UNBLOCK USERS
(function($){
  $.fn.unblock = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var blockid = parent.data('blockid');
        $.ajax({
          url    : DIR+"/ajaxify/ajax_requests/settings_requests.php",
          data   : {unblock: blockid},
          success: function(data){
            console.log(data);
            parent.slideUp('fast');
            if($('.blocked_users').length == 1){
              var ht = "<div class='home_last_mssg pro_last_mssg'><img src='"+ DIR +"/images/needs/large.jpg'><span>No blocked members</span></div>";
              setTimeout(function () {
                $('.block_header').after(ht).hide().slideDown("fast");
              }, 300);
            }
            $('.notify').notify({value: "Unblocked "+data});
          }
        });

      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO CONSTANTLY CHECK ANY NEW OR UNREAD NOTIFICATION
(function($){
  $.fn.getUnread = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = this;

      setInterval(function () {
        $.ajax({
          url     : DIR+"/ajaxify/ajax_requests/notifications_requests.php",
          method  : "GET",
          dataType: "JSON",
          data    : {getUnread: "Faiyaz"},
          success : function(data){
            $('.notifications').children().filter('.m_n_new').text(data.unread);
          }
        });
      }, 1000);

  }
  return this;
}(jQuery));

// FUNCTION TO DELETE ALL NOTIFICATIONS
(function($){
  $.fn.clearNotifications = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = this;
    elem.on('click', function(e){
      $.ajax({
        url    : DIR+"/ajaxify/ajax_requests/notifications_requests.php",
        method : "GET",
        data   : {clearAll: "Faiyaz"},
        success: function(data){
          $('.noti, .post_end').animate({
            left   : "+300px",
            opacity: 0,
            display: "none"
          }, 250);
          $('.noti_count').text('No notifications');
          $('.clear_noti').css('display', 'none');
          $('.notifications_header').after("<div class='home_last_mssg pro_last_mssg'><img src='"+ DIR +"/images/needs/large.jpg'><span>You got no notifications</span></div>").hide().slideDown("fast");
          setTimeout(function () { location.reload(); }, 500);
        }
      });
    });

  }
  return this;
}(jQuery));

// SUGGESTIONS FOR SESSION USER
(function($){
  $.fn.HomeSuggestions = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = this;
    var refresh = elem.find('.recomm_refresh')

    refresh.on('click', function(e){
      e.preventDefault();
      $.ajax({
        url : DIR+"/ajaxify/ajax_requests/suggestions_requests.php",
        data: {Homerefresh: "yes"},
        // beforeSend: function(){
        //   elem.find('.recomm_main').html('<div class="spinner home_recomm_"><span></span><span></span><span></span></div>');
        // },
        success: function(data){
          elem.find('.recomm_main').html(data).hide().slideDown(100);
          $('.follow').follow({ update: true });
          $('.unfollow').unfollow({ update: true });
          // $('.recomms_cont > a').hoverUser({ extraTop: 10 });
        }
      });
    });

  }
  return this;
}(jQuery));

// PLUGIN FOR EXPLORE NAVIGATION
(function($){
  $.fn.exploreNav = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);
      var elem = $(this);

      var link = $('.exp_nav_link');

      var fetchAndInsert = function(href){
        $.ajax({
          url: DIR+'/ajaxify/explore/'+href.split('=').pop(),
          method: "GET",
          beforeSend: function(e){
            $('.exp_hmm').html('<div class="spinner"><span></span><span></span><span></span></div>');
            $('.exp_hmm').addClass('hmm_spinner_show');
          },
          success: function(data){
            $('.exp_hmm').removeClass('hmm_spinner_show');
            // link.removeClass('pro_nav_active');

            if (href.indexOf('&') > -1) {
              var f = href.substr(href.indexOf('=')+1);
              var get = f.substr(0, f.indexOf('&'));
            } else {
              var get = href.substr(href.indexOf('=')+1);
            }

            var main = get.substr(0, get.lastIndexOf("."));
            console.log(main);
            // var p = $('.inst_nav[href="'+ main +'"]').parent().parent();

            $('.exp_nav_link').removeClass('exp_nav_active');
            $(".exp_nav_link[href='"+ main +"']").addClass('exp_nav_active');
            $('.exp_hmm').html(data).hide().fadeIn(100);
          }
        });
      }

      $(window).on('popstate', function(e){
        console.log(location.pathname+location.search);
        // console.log((location.pathname+location.search).spilt('/').pop());
        var main = location.pathname+location.search;
        if (location.search) {
          fetchAndInsert(main+".php");
        } else {
          fetchAndInsert("exp_people.php");
        }
      });

      link.on('click', function(e){
        e.preventDefault();
        link.removeClass('exp_nav_active');
        $(this).addClass('exp_nav_active');
        var url = $(this).attr('href');
        // var hint = $(this).data('hint');
        var vAr = $(this).data('src');
        fetchAndInsert(url+".php");
        history.pushState({}, '', location.pathname+"?"+vAr+"="+url);
        // console.log('hint: '+hint);
        var l = location.search;
        console.log(l.substr(l.indexOf("=")+1));
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION FOR SEARCHING
(function($){
  $.fn.searchInstagram = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = this;
    var div = $('.search_div');
    var spinner = '<div class="spinner"><span></span><span></span><span></span></div>';

    elem.on('keyup', function(e){
      var value = $(this).val().trim();

      if (value == "") {
        div.fadeOut(100);

      } else if (value != "") {
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/search_requests.php",
          data: {searchInstagram: value},
          method: "POST",
          beforeSend: function(){
            div.html(spinner);
          },
          success: function(data){
            div.fadeIn(100);
            div.html(data);
          }
        });
      }

    });

  }
  return this;
}(jQuery));

// FUNCTION TO ADD TO FAVOURITES
(function($){
  $.fn.userFav = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        e.preventDefault();
        var get = elem.data('getid');
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/fav_requests.php",
          data: {userFav: get},
          success: function(data){
            console.log(data);
            $('.notify').notify({ value: "Added to favourites" });
            elem.hide();
          }
        });
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO REMOVE FAVOURITES
(function($){
  $.fn.remFav = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);
      var elem = $(this);

      elem.on('click', function(e){
        var username = $(this).data('username');
        e.preventDefault();
        $('.prompt').myPrompt({
          title: "Remove from favourites",
          value: "Remove "+ username +" from favourites. You would have to re-add "+ username+ " to the favourites list.",
          doneText: "Remove",
          type: "rem_fav",
          post: $(this)
        });
        // rem__from__fav($(this));
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION FOR STICKY
(function($){
  $.fn.sticky = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);
      var elem = $(this);

      $(document).on('scroll', function(e){
        var top = $(this).scrollTop();
        if (top >= 285) {
          elem.find('.home_recomm').fadeOut(100);
          elem.css({"position": "fixed", "top": "45px"});
        }
        else if (top == 0) {
          elem.find('.home_recomm').fadeIn(100);
          elem.css({"position": "relative", "top": "0px"});
        }
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION FOR SCROLL BOTTOM NOTIFICATIONS FEEDS
function notificationFeeds(){
  $(window).on('scroll', function(e){
    if ($(window).scrollTop() + $(window).height() == $(document).height()) {
      $('.feed_inserted').html('Looking for more notifications..');
      $.ajax({
        url: DIR+"/ajaxify/ajax_requests/notifications_requests.php",
        data: {notiFeeds: $('.noti:last').data('notiid')},
        beforeSend: function(){
          $('.feed_inserted').html('Looking for more notifications..');
        },
        success: function(data){
          $('.feed_inserted').html('Looks like you\'ve reached the end');
          $('.feed_inserted').after(data);
          $('.feed_inserted').not(':last').remove();
          $('.follow').follow();
          $('.unfollow').unfollow();
          $('.post_end').on('click', function(e){
            $('html, body').animate({scrollTop: 0}, 450);
          });
        }
      });
    }
  });
}

// FUNCTION FOR SCROLL BOTTOM HASHTAGGED POSTS
function hashtagFeeds(){
  $(window).on('scroll', function(e){
    if ($(window).scrollTop() + $(window).height() == $(document).height()) {
      $('.feed_inserted').html('Looking for more posts..');
      $.ajax({
        url: DIR+"/ajaxify/ajax_requests/hashtag_requests.php",
        data: {
          hahstagFeeds: $('.posts:last').data('hashid'),
          hashtag: $('.user_info').data('tag')
        },
        beforeSend: function(){
          $('.feed_inserted').html('Looking for more posts..');
        },
        success: function(data){
          s(data);
        }
      });
    }
  });
}

// FUNCTION FOR SCROLL BOTTOM FOLLOWERS FEEDS
function followersFeeds(when){
  $(window).on('scroll', function(e){
    if ($(window).scrollTop() + $(window).height() == $(document).height()) {
      if (when == "followers") {
        var data = {
          followersFeeds: $('.followers_m_on:last').data('fid'),
          followersUser: $('.user_info').data('userid')
        };

      } else if (when == "followings") {
        var data = {
          followingsFeeds: $('.followings_m_on:last').data('fid'),
          followingsUser: $('.user_info').data('userid')
        }
      }
      $('.feed_inserted').html('Looking for more followers..');
      $.ajax({
        url: DIR+"/ajaxify/ajax_requests/follow_requests.php",
        data: data,
        beforeSend: function(){
          $('.feed_inserted').html('Looking for more followers..');
        },
        success: function(data){
          $('.feed_inserted').html('Looks like you\'ve reached the end');
          $('.feed_inserted').after(data);
          $('.feed_inserted').not(':last').remove();
          $('.follow').follow();
          $('.unfollow').unfollow();
          $('.post_end').on('click', function(e){
            $('html, body').animate({scrollTop: 0}, 450);
          });
        }
      });
    }
  });
}

// FUNCTION FOR DISPLAYING NOTIFICATIONS MODEL IF HAS HAS UNREAD NOTIFICATIONS
function notificationsModel(){
  var div = $('.noti_speak');
  var span = div.find('span');
  var val = div.find('.noti_hidden').val();
  if(val){
    span.html(val);
    div.fadeIn(100);
    setTimeout(function () {
      div.fadeOut(200);
    }, 10000);
  }
}
