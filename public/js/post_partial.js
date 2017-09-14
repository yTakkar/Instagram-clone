var watch;

function notification(success, error){
  if (navigator.geolocation) {
    watch = navigator.geolocation.watchPosition(success, error);
    $(this).addClass('add_tag_toggle');
  } else {
    $('.notify').notify({
      value: "Geolocation not supported"
    });
  }
}

// LOCATION ERRORS FUNCTION
function showError(err){
  if (err.code == 1) {
    $('.notify').notify({
      value: "Location permission denied"
    });
  } else if (err.code == 2) {
    $('.notify').notify({
      value: "Location signal lost"
    });
  } else if (err.code == 3) {
    $('.notify').notify({
      value: "Location request timed out"
    });
  } else if (err.code == 0) {
    $('.notify').notify({
      value: "Unknown location error"
    });
  }
  console.log(err);
}

// PLUGIN FOR POST DIV BASIC FUNCTIONALITY
(function($){
  $.fn.postDefaults = function(options){
    this.each(function(e){
      var defaults = {
        when: "user"
      };
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var textarea = elem.find('textarea');
      var emoji = $('.emoji');
      var items = emoji.find('li');
      var add_tag_btn = elem.find('.tag_add');
      var add_emoji_btn = elem.find('.emoji_add');
      var tag_input = elem.find('.p_add_taggings > input[type="text"]');
      var hidden_input = elem.find('.p_hidden');
      var textarea = elem.find('textarea');
      var font_add = elem.find('.font_add');
      var loc_add = elem.find('.loc_add');
      var loc_text = elem.find('.loc_text');
      var font_sizes = elem.find('.font_sizes');
      var sizes = font_sizes.find('li');
      var font_value = elem.find('.font_value');
      var loc_value = elem.find('.loc_value');
      var cancel = elem.find('.p_cancel');

      if (settings.when == "group") {
        tag_input.hide();
        add_tag_btn.hide();
        elem.find('.space').css('width', '120px');
        elem.find('.font_sizes').css('left', '38px');
      }

      function getTags(){
        var array = [];
        var ff = elem.find('.p_tagging > span');
        for (var i = 0; i < ff.length; i++) {
          array[i] = ff[i].innerHTML;
        }
        // var ooh = unique(array);
        var string = array.join(',');
        hidden_input.val(string);
        // console.log(hidden_input.val());
      }

      function blurHide(){
        if ($('.p_taggings').length == 0) {
          $('.p_tagging').css('height', '0px');
        } else {
          $('.p_tagging').css('height', 'auto');
        }
        var height = elem.find('.p_tagging').height();
        if (height%2 != 0) {
          var newHeight = parseInt(elem.find('.p_tagging').height())+1+"px";
          elem.find('.p_tagging').css('height', newHeight);
        }
      }

      blurHide();

      var cLick = function(elem){
        elem.fadeOut('fast');
        elem.remove();
        getTags();
        blurHide();
      }

      sizes.removeClass('font_size_active');
      font_sizes.find('.one').addClass('font_size_active');

      $('.emoji_add, .tag_add, .font_add, .loc_add').description({
        extraLeft: 2
      });

      font_add.on('mouseover', function(e){
        font_sizes.show();
        emoji.hide();
      });
      font_sizes.on('mouseover', function(e){
        font_sizes.show();
        font_sizes.on('mouseout', function(e){
          font_sizes.hide();
        });
      });

      var elements = [loc_add, add_tag_btn, elem.find('.space'), elem.find('textarea'), elem.find('.p_main'), $('.p_act > a')];

      for (var el of elements) {
        el.on('mouseover', function(e){
          font_sizes.hide();
          emoji.hide();
        });
      }

      loc_add.on('click', function(e){
        notification(showPosition, showError);
      });

      function showPosition(pos){
        var lat = pos.coords.latitude;
        var long = pos.coords.longitude;
        $.ajax({
          url: "https://maps.googleapis.com/maps/api/geocode/json?latlng="+ lat+","+long +"&key=AIzaSyCXKFAtXLGEJH7bu2yvwlUxVufc1ZIrO78",
          method: "GET",
          dataType: "JSON",
          success: function(data){
            // console.log(data.results[2].formatted_address);
            var loc = data.results[2].formatted_address;
            var first = loc.substr(0, loc.lastIndexOf(','));
            var second = first.substr(0, first.lastIndexOf(','));
            loc_text.text(second.substr(0, 20)+"..");
            loc_value.val(second);
          }
        });
      }

      font_value.val('14');

      elem.find('.emoji_add').on('mouseover', function(e){
        font_sizes.hide();
      });

      sizes.on('click', function(e){
        sizes.removeClass('font_size_active');
        $(this).addClass('font_size_active');
        var data = $(this).data('size');
        font_value.val(data);
        font_sizes.hide();
        textarea.css("font-size", data+"px");
      });

      add_tag_btn.on('click', function(e){
        $(this).toggleClass('add_tag_toggle');
        elem.find('.p_add_taggings').slideToggle('fast');
        tag_input.focus();
      });

      tag_input.on('keyup', function (){
        var newValue = $(this).val();
        // console.log(hidden_input.val());
        if (newValue != "") {
          $.ajax({
            url: "ajaxify/ajax_requests/get_taggers.php",
            method: "GET",
            data: {
              value: newValue,
              except: hidden_input.val()
            },
            success: function(data){
              console.log(data);
              elem.find('.p_tagging_ul').html(data);
              elem.find('.p_tagging_list').show();
              elem.find('.tag_hmm').on('click', function(e){
                var username = $(this).find('span').text();
                elem.find('.p_tag_ins_help').after("<span class='p_taggings knowing' data-show='remove' data-name='"+ username.trim() +"'>"+ username.trim() +"</span>");
                getTags();
                elem.find('.p_tagging_list').hide();
                tag_input.val('');
                tag_input.focus();
                blurHide();
                $('.knowing').on('click', function(e){
                  cLick($(this));
                });
              });
            }
          });
        } else if (newValue == "") {
          elem.find('.p_tagging_list').hide();
        }
      });

      getTags();

      elem.find('.p_taggings').on('click', function(e){
        cLick($(this));
      });

      cancel.on('click', function(e){
        e.preventDefault();
        navigator.geolocation.clearWatch(watch);
        $('.overlay').fadeOut('fast');
        blur.removeBlur();
        elem.html('<div class="post_spinner"><div class="spinner"><span></span><span></span><span></span></div></div>');
        elem.fadeOut('fast');
        emoji.fadeOut('fast');
        font_sizes.fadeOut('fast');
        navigator.geolocation.clearWatch(watch);
        $('#p_img_file, #p_vid_file, #p_aud_file, #p_doc_file').val('');
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION FOR SUCCESS WHEN FINISHED POSTING
function successOfPost(el, div, data, when){
  // el.removeClass('a_disabled')
  el.text('Done');
  // console.log(data);
  $('.overlay').hide();
  blur.removeBlur();
  div.html('<div class="post_spinner"><div class="spinner"><span></span><span></span><span></span></div></div>');
  div.fadeOut('fast');
  $('.emoji').fadeOut('fast');
  div.find('.font_sizes').fadeOut('fast');
  var username = $('.user_info').data('username');
  $('.home_notify').homeNotify({
    value: "Check your profile page to see the post",
    btnLink: DIR+"/profile/"+username+"?ask=posts",
    btnValue: "Check out",
    timeOut: true
  });
  $('.notify').notify({
    value: "Posted"
  });
  if (when == "group") {
    setTimeout(function () {
      location.reload();
    }, 400);
  }
}

// PLUGIN FOR POSTING TEXT
(function($){
  $.fn.textPost = function(options){
    var defaults = {
      when: "user"
    };
    var settings = $.extend({}, defaults, options);

    var elem = this;
    var div = $('.text_post');
    var emoji = $('.emoji');
    var items = $('.emoji').find('li');

    elem.on('click', function(e){
      $.ajax({
        url: DIR+"/ajaxify/post/text_post.php",
        beforeSend: function(){
          $('.overlay').show();
          blur.addBlur();
          div.show();
        },
        success: function(data){

          div.html(data);

          var textarea = div.find('textarea');
          var add_emoji_btn = div.find('.emoji_add');
          var add_tag_btn = div.find('.tag_add');
          var cancel = div.find('.p_cancel');
          var post_btn = div.find('.p_post');

          if (settings.when == "group") {
            post_btn.addClass('post_grp_text');
            post_btn.removeClass('post_user_text');
            post_btn.attr('data-when', 'group');
            post_btn.attr('data-grp', $('.user_info').data('grp'));
          } else if (settings.when == "user") {
            post_btn.addClass('post_user_text');
            post_btn.removeClass('post_grp_text');
            post_btn.attr('data-when', 'user');
            post_btn.attr('data-grp', "");
          }

          $('.overlay').show();
          blur.addBlur();
          div.fadeIn('fast');
          textarea.focus();
          div.postDefaults({when: settings.when});

          if (settings.when == "user") {
            var top = "319px";
            var left= "365px";
          } else if (settings.when == "group") {
            var top = "319px";
            var left= "365px";
          }

          add_emoji_btn.emoji({
            pseudo: null,
            textarea: textarea,
            top: top,
            left: left,
            event: "hover"
          });

          // TEXT POST FUNCTION
          function textPost(el){
            var value = textarea;
            var hidden_input = div.find('.p_hidden');
            var font = div.find('.font_value');
            var loc = div.find('.loc_value');
            el.addClass('a_disabled').text('Wait');
            if (value.val() != "") {

              $.ajax({
                url: DIR+"/ajaxify/ajax_requests/post_requests.php",
                data: {
                  text: value.val(),
                  tags: hidden_input.val(),
                  font: font.val(),
                  loc: loc.val(),
                  tpwhen: el.data('when'),
                  tpgrp: el.data('grp')
                },
                method: "POST",
                success: function(data){
                  successOfPost(el, div, data, settings.when);
                }
              });
            } else if (value.val() == "") {
              value.focus();
              post_btn.removeClass('a_disabled');
              post_btn.text('Post');
            }
          }

          div.find('.post_user_text').on('click', function(e){
            e.preventDefault();
            textPost($(this));
          });

          div.find('.post_grp_text').on('click', function(e){
            e.preventDefault();
            textPost($(this));
          });

        }
      });

    });

  }
}(jQuery));

// PLUGIN FOR IMAGE POST
(function($){
  $.fn.imagePost = function(options){
    var defaults = {
      when: "user"
    };
    var settings = $.extend({}, defaults, options);

    var elem = this;
    var div = $('.image_post');
    var emoji = $('.emoji');

    elem.on('change', function(e){
      var file = this.files[0];
      var type = file.type;
      var allowed = ["image/jpeg", "image/png", "image/gif"];
      if (!((type == allowed[0]) || (type == allowed[1]) || (type == allowed[2]))) {
        $('.notify').notify({
          value: "Please select an image only"
        });
        elem.val('');
      } else {

        var reader = new FileReader();
        reader.onload = function(e){

          $.ajax({
            url: DIR+"/ajaxify/post/image_post.php",
            beforeSend: function(){
              $('.overlay').show();
              blur.addBlur();
              div.show();
            },
            success: function(data){

              div.html(data);

              var img = div.find('.i_p_img > img');
              var textarea = div.find('textarea');
              var add_emoji_btn = div.find('.emoji_add');
              var add_tag_btn = div.find('.tag_add');
              var fltr = div.find('.filter_value');
              var loc_value = div.find('.loc_value');
              var post_btn = div.find('.p_post');

              $('.overlay').show();
              blur.addBlur();
              div.fadeIn('fast');
              img.attr('src', e.target.result);
              textarea.focus();
              $('.add_filters').perfectScrollbar();
              div.postDefaults({when: settings.when});

              if (settings.when == "group") {
                post_btn.addClass('post_grp_img');
                post_btn.removeClass('post_user_img');
                post_btn.attr('data-when', 'group');
                post_btn.attr('data-grp', $('.user_info').data('grp'));
              } else if (settings.when == "user") {
                post_btn.addClass('post_user_img');
                post_btn.removeClass('post_grp_img');
                post_btn.attr('data-when', 'user');
                post_btn.attr('data-grp', "");
              }

              if (settings.when == "user") {
                var top = "389px";
                var left= "259px";
              } else if (settings.when == "group") {
                var top = "389px";
                var left= "259px";
                // left: "365px"
              }

              div.find('.filter_div > img').attr('src', e.target.result);

              div.find('.filter_div').on('click', function(e){
                var filter = $(this).data('filter');
                $('.filter_div').removeClass('select_receiver_toggle');
                $(this).addClass('select_receiver_toggle');
                fltr.val(filter);
                img.removeClass();
                img.addClass(filter);
              });

              add_emoji_btn.emoji({
                pseudo: null,
                textarea: textarea,
                top: top,
                left: left,
                event: "hover"
              });

              // IMAGE POST FUNCTION
              function imagePost(el){
                var value = textarea;
                var hidden_input = div.find('.p_hidden');
                var font = div.find('.font_value');
                var loc = div.find('.loc_value');

                var file = $('#p_img_file').prop('files')[0];
                var form = new FormData();

                form.append("image_post", file);
                form.append('font', font.val());
                form.append('tags', hidden_input.val());
                form.append('value', value.val());
                form.append('loc', loc.val());
                form.append('filter', fltr.val());
                form.append('ipwhen', el.data('when'));
                form.append('ipgrp', el.data('grp'));

                el.addClass('a_disabled').text('Wait');

                $.ajax({
                  url : DIR+"/ajaxify/ajax_requests/post_requests.php",
                  type: "POST",
                  processData: false,
                  contentType: false,
                  data: form,
                  success: function(data){
                    $('#p_img_file').val('');
                    successOfPost(el, div, data, settings.when);
                  }
                });
              }

              div.find('.post_user_img').on('click', function(e){
                e.preventDefault();
                imagePost($(this));
              });

              div.find('.post_grp_img').on('click', function(e){
                e.preventDefault();
                imagePost($(this));
              });

            }
          });

        }
        reader.readAsDataURL(this.files[0]);
      }
    });

  }
}(jQuery));

// PLUGIN FOR VIDEO POST
(function($){
  $.fn.videoPost = function(options){
    var defaults = {
      when: "user"
    };
    var settings = $.extend({}, defaults, options);

    var elem = this;
    var div = $('.video_post');
    var emoji = $('.emoji');

    elem.on('change', function(e){
      var file = this.files[0];
      var size = file.size;
      var type = file.type;
      var allowed = ['video/mp4', 'video/ogg', 'video/webm'];
      if (!((type == allowed[0]) || (type == allowed[1]) || (type == allowed[2]))) {
        $('.notify').notify({
          value: "Please select a video only"
        });
        elem.val('');
      } else if(size >= 10485760) {
        $('.notify').notify({
          value: "Video should be less than 10MB"
        });
        elem.val('');
      } else {
        var reader = new FileReader();
        reader.onload = function(e){

          $.ajax({
            url: DIR+"/ajaxify/post/video_post.php",
            beforeSend: function(){
              $('.overlay').show();
              blur.addBlur();
              div.show();
            },
            success: function(data){

              div.html(data);

              var textarea = div.find('textarea');
              var video = div.find('video');
              var add_emoji_btn = div.find('.emoji_add');
              var post_btn = div.find('.p_post');

              $('.overlay').show();
              blur.addBlur();
              div.fadeIn('fast');
              video[0].preload = "auto";
              video.attr('src', e.target.result);
              video[0].muted = true;
              video[0].play();
              textarea.focus();
              div.postDefaults({when: settings.when});

              if (settings.when == "group") {
                post_btn.addClass('post_grp_vid');
                post_btn.removeClass('post_user_vid');
                post_btn.attr('data-when', 'group');
                post_btn.attr('data-grp', $('.user_info').data('grp'));
              } else if (settings.when == "user") {
                post_btn.addClass('post_user_vid');
                post_btn.removeClass('post_grp_vid');
                post_btn.attr('data-when', 'user');
                post_btn.attr('data-grp', "");
              }

              if (settings.when == "user") {
                var top = "390px";
                var left= "365px";
              } else if (settings.when == "group") {
                var top = "390px";
                var left= "365px";
              }

              add_emoji_btn.emoji({
                pseudo: null,
                textarea: textarea,
                top: top,
                left:left,
                event: "hover"
              });

              // VIDEO POST FUNCTION
              function vidPost(el){
                var value = textarea;
                var hidden_input = div.find('.p_hidden');
                var font = div.find('.font_value');
                var loc = div.find('.loc_value');

                var file = $('#p_vid_file').prop('files')[0];
                var form = new FormData();

                form.append("video_post", file);
                form.append('font', font.val());
                form.append('tags', hidden_input.val());
                form.append('value', value.val());
                form.append('loc', loc.val());
                form.append('vpwhen', el.data('when'));
                form.append('vpgrp', el.data('grp'));

                el.addClass('a_disabled').text('Wait');

                $.ajax({
                  url : DIR+"/ajaxify/ajax_requests/post_requests.php",
                  type: "POST",
                  processData: false,
                  contentType: false,
                  data: form,
                  success: function(data){
                    $('#p_vid_file').val('');
                    successOfPost(el, div, data, settings.when);
                  }
                });
              }

              div.find('.post_user_vid').on('click', function(e){
                e.preventDefault();
                vidPost($(this));
              });

              div.find('.post_grp_vid').on('click', function(e){
                e.preventDefault();
                vidPost($(this));
              });

            }
          });

        }
        reader.readAsDataURL(this.files[0]);
      }
    });

  }
}(jQuery));

// PLUGIN FOR AUDIO POST
(function($){
  $.fn.audioPost = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, settings);

    var elem = this;
    var div = $('.audio_post');
    var emoji = $('.emoji');

    elem.on('change', function(e){
      var file = this.files[0];
      var name = file.name;
      var type = file.type;
      var size = file.size;
      var allowed = ['audio/mp3', 'audio/mpeg'];

      console.log(file);

      if (!((type == allowed[0]) || (type == allowed[1]))) {
        $('.notify').notify({
          value: "Only mp3 is allowed"
        });
        elem.val('');
      } else if (size >= 10485760) {
        $('.notify').notify({
          value: "Mp3 should be less than 10MB"
        });
        elem.val('');
      } else {

        $.ajax({
          url: DIR+"/ajaxify/post/audio_post.php",
          beforeSend: function(){
            $('.overlay').show();
            blur.addBlur();
            div.show();
          },
          success: function(data){

            div.html(data);

            var textarea = div.find('textarea');
            var add_emoji_btn = div.find('.emoji_add');
            var add_tag_btn = div.find('.tag_add');
            var audio = div.find('.i_p_audio');
            var post_btn = div.find('.p_post');

            $('.overlay').show();
            blur.addBlur();
            div.fadeIn('fast');
            textarea.focus();
            div.postDefaults();

            var first = name.substr(0, name.lastIndexOf('.'));
            var second = name.substr(name.indexOf('.')+1);
            var main = first.substr(0,46);

            audio.text(first);

            add_emoji_btn.emoji({
              pseudo: null,
              textarea: textarea,
              top: "305px",
              left: "365px",
              event: "hover"
            });

            // AUDIO POST FUNCTION
            function audPost(el){
              var value = textarea;
              var hidden_input = div.find('.p_hidden');
              var font = div.find('.font_value');
              var loc = div.find('.loc_value');

              var file = $('#p_aud_file').prop('files')[0];
              var form = new FormData();

              form.append("audio_post", file);
              form.append('font', font.val());
              form.append('tags', hidden_input.val());
              form.append('value', value.val());
              form.append('loc', loc.val());

              el.addClass('a_disabled').text('Wait');

              $.ajax({
                url : DIR+"/ajaxify/ajax_requests/post_requests.php",
                type: "POST",
                processData: false,
                contentType: false,
                data: form,
                success: function(data){
                  console.log(data);
                  $('#p_aud_file').val('');
                  if (data == "Error!!"){
                    el.text('Done');
                    $('.overlay').hide();
                    blur.removeBlur();
                    div.html('<div class="post_spinner"><div class="spinner"><span></span><span></span><span></span></div></div>');
                    div.fadeOut('fast');
                    $('.emoji').fadeOut('fast');
                    div.find('.font_sizes').fadeOut('fast');
                    $('.notify').notify({ value: "Error" });
                  } else {
                    successOfPost(el, div, data, "user");
                  }
                }
              });
            }

            post_btn.on('click', function(e){
              e.preventDefault();
              audPost($(this));
            });

          }
        });

      }

    });

  }
}(jQuery));

// PLUGIN FOR DOCUMENT POST
(function($){
  $.fn.docPost = function(options){
    var defaults = {
      when: "user"
    };
    var settings = $.extend({}, defaults, options);

    var elem = this;
    var div = $('.doc_post');
    var emoji = $('.emoji');

    elem.on('change', function(e){

      var file = this.files[0];
      var name = file.name;
      var type = file.type;
      var size = file.size;
      var allowed = ['image/jpeg', 'image/png', 'image/gif', 'audio/mp3', 'audio/mpeg', 'video/mp4', 'video/ogg', 'video/webm'];

      if (((type == allowed[0]) || (type == allowed[1]) || (type == allowed[2]) || (type == allowed[3]) || (type == allowed[4]) || (type == allowed[5]) || (type == allowed[6]) || (type == allowed[7]))) {
        $('.notify').notify({
          value: "Please select documents only"
        });
        elem.val('');
      } else if (size >= 10485760) {
        $('.notify').notify({
          value: "Document should not be larger than 10MB"
        });
        elem.val('');
      } else {

        $.ajax({
          url: DIR+"/ajaxify/post/document_post.php",
          beforeSend: function(){
            $('.overlay').show();
            blur.addBlur();
            div.show();
          },
          success: function(data){

            div.html(data);

            var textarea = div.find('textarea');
            var add_emoji_btn = div.find('.emoji_add');
            var add_tag_btn = div.find('.tag_add');
            var doc = div.find('.i_p_doc_info');
            var post_btn = div.find('.p_post');

            $('.overlay').show();
            blur.addBlur();
            div.fadeIn('fast');
            textarea.focus();
            div.postDefaults({when: settings.when});

            if (settings.when == "group") {
              post_btn.addClass('post_grp_doc');
              post_btn.removeClass('post_user_doc');
              post_btn.attr('data-when', 'group');
              post_btn.attr('data-grp', $('.user_info').data('grp'));
            } else if (settings.when == "user") {
              post_btn.addClass('post_user_doc');
              post_btn.removeClass('post_grp_doc');
              post_btn.attr('data-when', 'user');
              post_btn.attr('data-grp', "");
            }

            var first = name.substr(0, name.lastIndexOf('.'));
            var second = name.substr(name.indexOf('.')+1);
            var main = first.substr(0,46);

            if (name.length >= 52) {
              doc.text(main+"..."+second);
            } else {
              doc.text(name);
            }

            if (settings.when == "user") {
              var top = "305px";
              var left= "365px";
            } else if (settings.when == "group") {
              var top = "305px";
              var left= "365px";
            }

            add_emoji_btn.emoji({
              pseudo: null,
              textarea: textarea,
              top: top,
              left: left,
              event: "hover"
            });

            // DOC POST FUNCTION
            function doc__post(el){
              var value = textarea;
              var hidden_input = div.find('.p_hidden');
              var font = div.find('.font_value');
              var loc = div.find('.loc_value');

              var file = $('#p_doc_file').prop('files')[0];
              var form = new FormData();

              form.append("doc_post", file);
              form.append('font', font.val());
              form.append('tags', hidden_input.val());
              form.append('value', value.val());
              form.append('loc', loc.val());
              form.append('dpwhen', el.data('when'));
              form.append('dpgrp', el.data('grp'));

              el.addClass('a_disabled').text('Wait');

              $.ajax({
                url : DIR+"/ajaxify/ajax_requests/post_requests.php",
                type: "POST",
                processData: false,
                contentType: false,
                data: form,
                success: function(data){
                  $('#p_doc_file').val('');
                  successOfPost(el, div, data, settings.when);
                }
              });
            }

            div.find('.post_user_doc').on('click', function(e){
              e.preventDefault();
              doc__post($(this));
            });

            div.find('.post_grp_doc').on('click', function(e){
              e.preventDefault();
              doc__post($(this));
            });

          }
        });

      }

    });

  }
}(jQuery));

// LOCATION POST
(function($){
  $.fn.locationPost = function(options){
    var defaults = {
      when: "user"
    };
    var settings = $.extend({}, defaults, options);

    var elem = $(this);
    var div = $('.loc_post');
    var emoji = $('.emoji');

    elem.on('click', function(e){

      $.ajax({
        url: DIR+"/ajaxify/post/location_post.php",
        beforeSend: function(){
          $('.overlay').show();
          blur.addBlur();
          div.show();
        },
        success: function(data){

          div.html(data);

          var textarea = div.find('textarea');
          var add_emoji_btn = div.find('.emoji_add');
          var add_tag_btn = div.find('.tag_add');
          var loc = div.find('.i_p_loc > img');
          var address_text = div.find('.address_text');
          var address_value = div.find('.address_value');
          var post_btn = div.find('.p_post');

          $('.overlay').show();
          blur.addBlur();
          div.fadeIn('fast');
          textarea.focus();
          div.postDefaults({when: settings.when});

          if (settings.when == "group") {
            post_btn.addClass('post_grp_link');
            post_btn.removeClass('post_user_link');
            post_btn.attr('data-when', 'group');
            post_btn.attr('data-grp', $('.user_info').data('grp'));
            $('.space').css('width', '155px');
          } else if (settings.when == "user") {
            post_btn.addClass('post_user_link');
            post_btn.removeClass('post_grp_link');
            post_btn.attr('data-when', 'user');
            post_btn.attr('data-grp', "");
          }

          if (settings.when == "user") {
            var top = "390px";
            var left = "365px";
          } else if (settings.when == "group") {
            var top = "390px";
            var left= "365px";
          }

          add_emoji_btn.emoji({
            pseudo: null,
            textarea: textarea,
            top: top,
            left: left,
            event: "hover"
          });

          notification(showPosition, showError);

          function showPosition(pos){
            var lat = pos.coords.latitude;
            var long = pos.coords.longitude;
            // console.log("Lat: "+lat);
            // console.log("Long: "+long);

            var img = "https://maps.googleapis.com/maps/api/staticmap?zoom=13&size=500x400&scale=2&maptype=roadmap&markers=color:red%7Clabel:S%7C" + lat + "," + long+"&key=AIzaSyDOPJdgCIHzaQ4VH0w8ngbRUtf2oBu2Y5c";
            div.find('.i_p_loc > img').attr('src', img);

            $.ajax({
              url: "https://maps.googleapis.com/maps/api/geocode/json?latlng="+ lat+","+long +"&key=AIzaSyCXKFAtXLGEJH7bu2yvwlUxVufc1ZIrO78",
              method: "GET",
              dataType: "JSON",
              success: function(data){
                // console.log(data.results[2].formatted_address);
                var loc = data.results[2].formatted_address;
                var first = loc.substr(0, loc.lastIndexOf(','));
                var second = first.substr(0, first.lastIndexOf(','));
                div.find('.address_text').text(second.substr(0,20)+"..");
                div.find('.address_value').val(second);
              }
            });
          }

          // LOC POST FUNCTION
          function loPost(el){
            var value = textarea;
            var hidden_input = div.find('.p_hidden');
            var font = div.find('.font_value');
            var src = loc.attr('src');
            var loc_value = div.find('.address_value');

            console.log(loc_value);
            el.addClass('a_disabled').text('Wait');

            $.ajax({
              url : DIR+"/ajaxify/ajax_requests/post_requests.php",
              type: "POST",
              data: {
                src: src,
                value: value.val(),
                font: font.val(),
                tags: hidden_input.val(),
                loc: loc_value.val(),
                loc_when: el.data('when'),
                loc_grp: el.data('grp')
              },
              success: function(data){
                successOfPost(el, div, data, settings.when);
              }
            });
          }

          div.find('.post_user_link').on('click', function(e){
            e.preventDefault();
            loPost($(this));
          });

          div.find('.post_grp_link').on('click', function(e){
            e.preventDefault();
            loPost($(this));
          });

        }
      });

    });

  }
}(jQuery));

// LINK POST
(function($){
  $.fn.linkPost = function(options){
    var defaults = {
      when: "user"
    };
    var settings = $.extend({}, defaults, options);

    var elem = this;
    var div = $('.link_post');
    var emoji = $('.emoji');
    var items = $('.emoji').find('li');

    elem.on('click', function(e){

      $.ajax({
        url: DIR+"/ajaxify/post/link_post.php",
        beforeSend: function(){
          $('.overlay').show();
          blur.addBlur();
          div.show();
        },
        success: function(data){

          div.html(data);

          var add_emoji_btn = div.find('.emoji_add');
          var add_tag_btn = div.find('.tag_add')
          var textarea = div.find('textarea');
          var cancel = div.find('.p_cancel');
          var post_btn = div.find('.p_post');
          var link_enter = div.find('.link_enter');
          var link_src = div.find('.link_src');
          var link_title = div.find('.link_title');
          var link_url = div.find('.link_url');
          var get_link = div.find('.find_link');

          $('.overlay').show();
          blur.addBlur();
          div.fadeIn('fast');
          link_enter.focus();
          div.postDefaults({when: settings.when});

          if (settings.when == "group") {
            post_btn.addClass('post_grp_link');
            post_btn.removeClass('post_user_link');
            post_btn.attr('data-when', 'group');
            post_btn.attr('data-grp', $('.user_info').data('grp'));
          } else if (settings.when == "user") {
            post_btn.addClass('post_user_link');
            post_btn.removeClass('post_grp_link');
            post_btn.attr('data-when', 'user');
            post_btn.attr('data-grp', "");
          }

          if (settings.when == "user") {
            var top = "336px";
            var left = "365px";
          } else if (settings.when == "group") {
            var top = "336px";
            var left = "365px";
          }

          add_emoji_btn.emoji({
            pseudo: null,
            textarea: textarea,
            top: top,
            left: left,
            event: "hover"
          });

          get_link.on('click', function(e){
            e.preventDefault();
            var value = link_enter.val();
            $(this).addClass('sec_btn_toggle');
            if (value != "") {

              $.ajax({
                 url: DIR+"/ajaxify/ajax_requests/post_requests.php",
                 dataType: "JSON",
                 type: "POST",
                 data: {value: value},
                 beforeSend: function(e){
                   $(this).addClass('sec_btn_toggle');
                   div.find('.link_t_img').fadeOut('fast');
                   div.find('.link_t_info').fadeOut('fast');
                   div.find('.spinner').fadeIn('fast');
                 },
                 success: function(data){
                   console.log(data);
                   get_link.removeClass('sec_btn_toggle');
                   var src = data.src;
                   var url = data.url;
                   var title = data.title;
                   link_src.val(src);
                   link_title.val(title);
                   link_url.val(url);
                   div.find('.link_t_img > img').attr('src', src);
                   div.find('.link_t_info > .title').text(title.substr(0, 44));
                   div.find('.link_t_info > .url').text(url.substr(0, 46));
                   div.find('.spinner').fadeOut('fast');
                   div.find('.link_t_img').fadeIn('fast').css('display', 'inline-block')
                   div.find('.link_t_info').fadeIn('fast').css('display', 'inline-block');
                 }
              });

            } else if (value == "") {
              link_enter.focus();
            }
          });

          link_enter.on('click, focus', function(e){
            get_link.removeClass('sec_btn_toggle');
          });

          function lPost(el){
            var link_value = textarea;
            var link_hidden_input = div.find('.p_hidden');
            var link_font = div.find('.font_value');
            var link_loc = div.find('.loc_value');
            var link_src = div.find('.link_src');
            var link_title = div.find('.link_title');
            var link_url = div.find('.link_url');

            var hint = link_url.val();

            el.addClass('a_disabled').text('Wait');

            if (hint == "") {
              link_enter.focus();
            } else if (hint != ""){
              $.ajax({
                url: DIR+"/ajaxify/ajax_requests/post_requests.php",
                type: "POST",
                data: {
                  link_text: link_value.val(),
                  link_tags: link_hidden_input.val(),
                  link_font: link_font.val(),
                  link_loc: link_loc.val(),
                  link_src: link_src.val(),
                  link_title: link_title.val(),
                  link_url: link_url.val(),
                  link_when: el.data('when'),
                  link_grp: el.data('grp')
                },
                success: function(data){
                  successOfPost(el, div, data, settings.when);
                }
              });
            }
          }

          div.find('.post_user_link').on('click', function(e){
            e.preventDefault();
            lPost($(this));
          });

          div.find('.post_grp_link').on('click', function(e){
            e.preventDefault();
            lPost($(this));
          });

        }
      });

    });

  }
}(jQuery));

// EDIT POST
(function($){
  $.fn.editPost = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var parent = elem.parent().parent().parent().parent().parent();
      var post = parent.data('postid');
      var type = parent.data('type');
      var div = parent.find('.e');
      var cancel = parent.find('.p_edit_cancel');
      var save = parent.find('.p_edit_save');
      var tip = parent.find('.p_edit_tip');

      elem.on('click', function(e){
        e.preventDefault();
        if ($('.p_actual').length > 1) {
          $('.p_actual').prop('contenteditable', false).removeClass('editable_toggle').blur();
          $('.p_edit_tools').slideUp(100);
        }

        if(isPostLengthy(div.text())){
           div.removeClass('isLengthy');
           parent.find('.load_more_div').hide();
        }
        div.prop('contenteditable', true).addClass('editable_toggle').focus();
        parent.find('.p_edit_tools').slideDown(100);
        parent.find('.p_options').css('opacity', '0');

        cancel.on('click', function(e){
          e.preventDefault();
          div.prop('contenteditable', false).removeClass('editable_toggle').blur();
          parent.find('.p_edit_tools').slideUp(100);
          if(isPostLengthy(div.text())){
             div.addClass('isLengthy');
             parent.find('.load_more_div').show();
          }
          parent.find('.p_options').css('opacity', '1');
        });

        save.on('click', function(e){
          var text = div.text();
          // console.log(text);
          e.preventDefault();
          $.ajax({
            url: DIR+"/ajaxify/ajax_requests/edit_post_requests.php",
            type: "POST",
            data: {
              edit: "yes",
              text: text,
              type: type,
              post: post
            },
            beforeSend: function(){
              tip.text('Editing post');
            },
            success: function(data){
              console.log(data);
              tip.text('Post edited');
              div.prop('contenteditable', false).removeClass('editable_toggle').blur();
              parent.find('.p_edit_tools').slideUp(100);
              $('.notify').notify({value: "Post edited"});
              if(isPostLengthy(div.text())){
                 div.addClass('isLengthy');
                 parent.find('.load_more_div').show();
              }
              setTimeout(function () {
                location.reload();
              }, 200);
              save.off('click');
            }
          });
        });

      });

    });
    return this;
  }
}(jQuery));

// POST LIKE
(function($){
  $.fn.postLike = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var user = $('.user_info').data('sessionid');

      elem.on('click', function(e){
        $(this).addClass('post_like_toggle');
        var div = $(this).parent().parent().parent().parent();
        var post = div.data('postid');
        var update = div.find('.likes');
        var parent = $(this).parent();
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/post_like_requests.php",
          dataType: "JSON",
          data: {
            // like: user,
            like: post
          },
          success: function(data){
            $(this).removeClass('post_like_toggle');
            console.log(data);
            update.text(data.likes);
            update.attr('data-description', data.simpleLikes+" likes");
            elem.remove();
            parent.html("<span class='p_unlike' data-description='Unlike'><i class='material-icons'>favorite</i></span>");
            parent.find('.p_unlike').postUnlike();
            parent.find('.p_unlike').description();
          }
        });
      });

    });
    return this;
  }
}(jQuery));

// POST UNLIKE
(function($){
  $.fn.postUnlike = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var user = $('.user_info').data('sessionid');

      elem.on('click', function(e){
        $(this).addClass('post_like_toggle');
        var div = $(this).parent().parent().parent().parent();
        var post = div.data('postid');
        var update = div.find('.likes');
        var parent = $(this).parent();
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/post_like_requests.php",
          dataType: "JSON",
          data: {
            // unlike: user,
            unlike: post
          },
          success: function(data){
            $(this).removeClass('post_like_toggle');
            console.log(data);
            update.text(data.likes);
            update.attr('data-description', data.simpleLikes+" likes");
            parent.html("<span class='p_like' data-description='Like'><i class='material-icons'>favorite_border</i></span>");
            parent.find('.p_like').postLike();
            parent.find('.p_like').description();
          }
        });
      });

    });
    return this;
  }
}(jQuery));

// POST BOOKMARK
(function($){
  $.fn.postBookmark = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        $(this).addClass('post_like_toggle');
        var div = $(this).parent().parent().parent().parent();
        var post = div.data('postid');
        var parent = $(this).parent();
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/bkmrk_requests.php",
          data: {bkmrk: post},
          success: function(data){
            console.log(data);
            $(this).removeClass('post_like_toggle');
            elem.remove();
            parent.html("<span class='p_unbookmark' data-description='Unbookmark'><i class='material-icons'>bookmark</i></span>");
            parent.find('.p_unbookmark').postUnbookmark();
            parent.find('.p_unbookmark').description();
          }
        });
      });

    });
    return this;
  }
}(jQuery));

// POST UNBOOKMARK
(function($){
  $.fn.postUnbookmark = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        $(this).addClass('post_like_toggle');
        var div = $(this).parent().parent().parent().parent();
        var post = div.data('postid');
        var parent = $(this).parent();
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/bkmrk_requests.php",
          data: {unbkmrk: post},
          success: function(data){
            console.log(data);
            $(this).removeClass('post_like_toggle');
            elem.remove();
            parent.html("<span class='p_bookmark' data-description='Bookmark'><i class='material-icons'>bookmark_border</i></span>");
            parent.find('.p_bookmark').postBookmark();
            parent.find('.p_bookmark').description();
          }
        });
      });

    });
    return this;
  }
}(jQuery));

// POST SHARE
(function($){
  $.fn.postShare = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var postId, div;

      elem.on('click', function(e){
        $(this).addClass('post_like_toggle');
        div = $(this).parent().parent().parent().parent();
        postId = div.data('postid');
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/share_requests.php",
          method: "POST",
          data: {getFollowings: postId},
          beforeSend: function(){
            $('.display_content').html("<div class='spinner'><span></span><span></span><span></span></div>");
          },
          success: function(data){
            $(this).removeClass('post_like_toggle');
            $('.display_content').html(data);
            $('.display_content').hide().slideDown(100);
            $('.display').displayOptions({
              title: "Send to"
            });

            var user = $('.share_userid');
            var post = $('.share_postid');

            $('.select_receiver').on('click', function(e){
              $('.select_receiver').removeClass('select_receiver_toggle');
              $(this).addClass('select_receiver_toggle');
              var data = $(this).data('userid');
              var username = $(this).find('.d_i_username').text();
              user.val(data);
              post.val(postId);
              console.log(user.val());
              console.log(post.val());

              $.ajax({
                url: DIR+"/ajaxify/ajax_requests/share_requests.php",
                dataType: "JSON",
                method: "POST",
                data: {
                  to: user.val(),
                  post: post.val()
                },
                success: function(data){
                  // console.log(data);
                  div.find('.p_h_opt > .p_comm').text(data.shares);
                  $('.notify').notify({
                    value: data.s
                  });
                  $('.overlay').hide();
                  blur.removeBlur();
                  $('.display').fadeOut('fast');
                }
              });

            });

          }
        });
      });

    });
    return this;
  }
}(jQuery));

// POST LIKERS
(function($){
  $.fn.likes = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        var div = $(this).parent().parent().parent();
        var post = div.data('postid');

        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/post_like_requests.php",
          method: "POST",
          data: {post: post},
          beforeSend: function(){
            $('.display_content').html("<div class='spinner'><span></span><span></span><span></span></div>");
          },
          success: function(data){
            $('.display_content').html(data);
            $('.display_content').hide().slideDown(100);
            $('.display_content').children().eq($('.display_content').children().length-1).children().filter('hr').remove();
            $('.display_follow').follow({
              update: true
            });
            $('.display_unfollow').unfollow({update: true});
            $('.display').displayOptions({
              title: "Post liked by"
            });
          }
        });

      });

    });
    return this;
  }
}(jQuery));

// POST TAGGERS
(function($){
  $.fn.taggers = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        var div = $(this).parent().parent().parent().parent();
        var post = div.data('postid');

        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/taggings_requests.php",
          method: "POST",
          data: {post: post},
          beforeSend: function(){
            $('.display_content').html("<div class='spinner'><span></span><span></span><span></span></div>");
          },
          success: function(data){
            $('.display_content')
              .html(data)
              .hide().slideDown(100)
              .children().eq($('.display_content').children().length-1).children().filter('hr').remove();
            $('.delete_tag').deleteTag();
            $('.display_follow').follow({update: true});
            $('.display_unfollow').unfollow({update: true});
            $('.display').displayOptions({
              title: "Tagged in this post"
            });
          }
        });

      });

    });
    return this;
  }
}(jQuery));

// POST SHARERES
(function($){
  $.fn.shares = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        var div = $(this).parent().parent().parent().parent();
        var post = div.data('postid');

        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/share_requests.php",
          type: "POST",
          data: {posting:post},
          beforeSend: function(){
            $('.display_content').html("<div class='spinner'><span></span><span></span><span></span></div>");
          },
          success: function(data){
            $('.display_content')
              .html(data)
              .hide().slideDown(100)
              .children().eq($('.display_content').children().length-1).children().filter('hr').remove();
            $('.display_follow').follow({update: true});
            $('.display_unfollow').unfollow({update: true});
            $('.display').displayOptions({
              title: "Post shared by"
            });
          }
        });

      });

    });
    return this;
  }
}(jQuery));

// POST UNSHARE
(function($){
  $.fn.unshare = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        e.preventDefault();
        $('.prompt').myPrompt({
          title: "Delete this share",
          value: "You were shared this post. And by clicking delete you will be unshared.",
          doneText: "Delete",
          type: "unshare_post",
          post: $(this).parent().parent().parent().parent().parent()
        });
        // un__share($(this).parent().parent().parent().parent().parent());
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO REMOVE SHARE
(function($){
  $.fn.removeShare = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        e.preventDefault();
        var parent = $(this).parent().parent().parent().parent().parent();
        var post = parent.data('postid');
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/share_requests.php",
          method: "POST",
          data: {getShareTos: post},
          beforeSend: function(){
            $('.display_content').html("<div class='spinner'><span></span><span></span><span></span></div>");
          },
          success: function(data){
            $('.display_content')
              .html(data)
              .hide().slideDown(100);
            $('.display').displayOptions({
              title: "Unshare to"
            });

            $('.select_receiver').on('click', function(e){
              $('.select_receiver').removeClass('select_receiver_toggle');
              $(this).addClass('select_receiver_toggle');
              var user = $(this).data('userid');
              var username = $(this).find('.d_i_username').text();

              $.ajax({
                url: DIR+"/ajaxify/ajax_requests/share_requests.php",
                dataType: "JSON",
                data: {
                  remove_share: user,
                  post: post
                },
                success: function(data){
                  parent.find('.p_comm').text(data.shares);
                  $('.notify').notify({ value: "Unshared to "+username });
                  $('.overlay').hide();
                  blur.removeBlur();
                  $('.display').fadeOut('fast');
                }
              });

            });

          }
        });

      });

    });
    return this;
  }
}(jQuery));

// POST UNTAG
(function($){
  $.fn.untag = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        e.preventDefault();
        $('.prompt').myPrompt({
          title: "Untag post",
          value: "You post will be untagged. There's no undo so you won't be able to find on your profile.",
          doneText: "Untag",
          type: "untag_post",
          post: $(this).parent().parent().parent().parent().parent()
        });
        // un__tag($(this).parent().parent().parent().parent().parent());
      });

    });
    return this;
  }
}(jQuery));

// POST DELETE TAG
(function($){
  $.fn.deleteTag = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        e.preventDefault();
        var parent = $(this).parent().parent().parent();
        var user = $(this).parent().data('getid');
        var post = $(this).data('postid');

        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/taggings_requests.php",
          data: {delete_tag: user, post: post},
          dataType: "json",
          success: function(data){
            parent.slideUp('fast', function(){
              $(this).remove();
              $('.notify').notify({ value: "Tag removed!" });
            });
            parent.find('.p_tags').text(data.nooftags);
          }
        });

      });

    });
    return this;
  }
}(jQuery));

// PLUGIN TO DELETE POST
(function($){
  $.fn.deletePost = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        e.preventDefault();
        $('.prompt').myPrompt({
          title: "Delete post",
          value: "This post will be deleted. There's no undo so you won't be able to find it.",
          doneText: "Delete",
          type: "delete_post",
          post: $(this).parent().parent().parent().parent().parent()
        });
        // delete__post($(this));
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO LOAD MORE TEXT OF POST
(function($){
  $.fn.load_more_of_post = function(options){
    this.each(function(e){
      var defaults = {
        type: "text"
      };
      var settings = $.extend({}, defaults, options);

      var link = $(this);

      link.on('click', function(e){
        e.preventDefault();
        if(settings.type == "text"){
          $(this).parent().siblings().filter('.e').removeClass('isLengthy');
        } else if(settings.type == "not_text"){
          $(this).parent().siblings().filter('.e').removeClass('isLengthy');
        }
        $(this).parent().remove();
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO CHECK IF POST IS LENGTHY
function isPostLengthy(text){
  if(text.length > 1000){
    return true;
  }
}

// FUNCTION TO COPY POST LINK
(function($){
  $.fn.copyPostLink = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        e.preventDefault();
        var link = $(this).data('link');
        copyTextToClipboard(link);
      });

    });
    return this;
  }
}(jQuery));

// COMMON USER SCROLL DOWN FEEDS
(function($){
  $.fn.commonUserFeeds = function(options){
    var defaults = {
      when: null
    };
    var settings = $.extend({}, defaults, options);

    $(window).on('scroll', function(e){
      if ($(window).scrollTop() + $(window).height() == $(document).height()) {

        if (settings.when == "tag") {
          var data = {
            tagFeeds: $('.tag_posts:last').data('tagid'),
            tagForFeeds: $('.user_info').data('userid')
          };
        } else if (settings.when == "user") {
          var data = {
            userFeeds: $('.user_posts:last').data('postid'),
            userForFeeds: $('.user_info').data('userid')
          };
        } else if (settings.when == "bookmark") {
          var data = {
            bookmarkFeeds: $('.bkmrk_posts:last').data('bookmarkid')
          };
        } else if (settings.when == "share") {
          var data = {
            shareFeeds: $('.share_posts:last').data('shareid'),
            shareForFeeds: $('.user_info').data('userid')
          };
        } else if (settings.when == "home") {
          var data = {feedAtBottom: $('.home_posts:last').data('postid')};
        }

        $('.feed_inserted').html('Looking for more posts..');
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/post_requests.php",
          data: data,
          beforeSend: function(){
            $('.feed_inserted').html('Looking for more posts..');
          },
          success: function(resp){
            s(resp);
          }
        });
      }
    });

  }
  return this;
}(jQuery));

// function userFeeds(){
//   $(window).on('scroll', function(e){
//     if ($(window).scrollTop() + $(window).height() == $(document).height()) {
//       $('.feed_inserted').html('Looking for more posts..');
//       $.ajax({
//         url: DIR+"/ajaxify/ajax_requests/post_requests.php",
//         data: {userFeeds: $('.posts:last').data('postid'), userForFeeds: $('.user_info').data('userid')},
//         beforeSend: function(){
//           $('.feed_inserted').html('Looking for more posts..');
//         },
//         success: function(data){
//           s(data);
//         }
//       });
//     }
//   });
// }

// function tagFeeds(){
//   $(window).on('scroll', function(e){
//     $('.feed_inserted').html('Looking for more posts..');
//     if ($(window).scrollTop() + $(window).height() == $(document).height()) {
//       $.ajax({
//         url: DIR+"/ajaxify/ajax_requests/post_requests.php",
//         data: {tagFeeds: $('.posts:last').data('tagid'), tagForFeeds: $('.user_info').data('userid')},
//         beforeSend: function(){
//           $('.feed_inserted').html('Looking for more posts..');
//         },
//         success: function(data){
//           s(data);
//         }
//       });
//     }
//   });
// }

// function bookmarkFeeds(){
//   $(window).on('scroll', function(e){
//     if ($(window).scrollTop() + $(window).height() == $(document).height()) {
//       $('.feed_inserted').html('Looking for more posts..');
//       $.ajax({
//         url: DIR+"/ajaxify/ajax_requests/post_requests.php",
//         data: {bookmarkFeeds: $('.posts:last').data('bookmarkid')},
//         beforeSend: function(){
//           $('.feed_inserted').html('Looking for more posts..');
//         },
//         success: function(data){
//           s(data);
//         }
//       });
//     }
//   });
// }

// function shareFeeds(){
//   $(window).on('scroll', function(e){
//     if ($(window).scrollTop() + $(window).height() == $(document).height()) {
//       $('.feed_inserted').html('Looking for more posts..');
//       $.ajax({
//         url: DIR+"/ajaxify/ajax_requests/post_requests.php",
//         data: {shareFeeds: $('.posts:last').data('shareid'), shareForFeeds: $('.user_info').data('userid')},
//         beforeSend: function(){
//           $('.feed_inserted').html('Looking for more posts..');
//         },
//         success: function(data){
//           s(data);
//         }
//       });
//     }
//   });
// }

// FUNCTION TO FETCH FEED WHEN REACHED THE END
// function getFeedAtEnd(){
//   $(window).on('scroll', function(e){
//     if ($(window).scrollTop() + $(window).height() == $(document).height()) {
//       $('.feed_inserted').html('Looking for more posts..');
//       var post = $('.posts:last').data('postid');
//       $.ajax({
//         url: DIR+"/ajaxify/ajax_requests/post_requests.php",
//         data: {feedAtBottom: post},
//         beforeSend: function(){
//           $('.feed_inserted').html('Looking for more posts..');
//         },
//         success: function(data){
//           s(data);
//         }
//       });
//     }
//   });
// }
