// PLUGIN FOR GROUP NAVIGATION
(function($){
  $.fn.groupNav = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);
      var elem = $(this);

      var link = $('.inst_grp_nav');
      var username = $('.user_info').data('grpname');
      var grp = $('.user_info').data('grp');

      var fetchAndInsert = function(href){
        $.ajax({
          url: DIR+'/ajaxify/grp_sections/'+href.split('=').pop(),
          method: "GET",
          data: {grp: grp},
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

            $('.inst_grp_nav').removeClass('pro_nav_active');
            $(".inst_grp_nav[href='"+ main +"']").addClass('pro_nav_active');
            $('.hmm').html(data).hide().fadeIn(100);
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
          fetchAndInsert("grp_posts.php");
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
        var l = location.search;
        console.log(l.substr(l.indexOf("=")+1));
        $("html, body").animate({ scrollTop: 280 }, "slow");
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION FOR CREATING A GROUP
(function($){
  $.fn.createGroup = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var div = $('.create_group');
      var cancel = div.find('.c_t_cancel');
      var done = div.find('.c_t_done');
      var name = div.find('.c_t_name_div > input[type="text"]');
      var ta = div.find('.c_t_name_div > textarea');
      var overlay = $('.overlay');

      elem.on('click', function(e){
        e.preventDefault();
        overlay.show();
        blur.addBlur();
        div.fadeIn('fast');
        name.focus();

        cancel.on('click', function(e){
          e.preventDefault();
          overlay.hide();
          blur.removeBlur();
          div.fadeOut('fast');
          name.val('');
          ta.val('');
        });

        done.on('click', function(e){
          e.preventDefault();
          var grpname = name.val();
          var bio = ta.val();
          if (grpname == "") {
            name.focus();
          } else if (grpname != "") {
            $.ajax({
              url: DIR+"/ajaxify/ajax_requests/groups_requests.php",
              data: {
                create_group: grpname,
                grp_bio: bio
              },
              dataType: "json",
              success: function(data){
                console.log(data);
                $('.notify').notify({ value: "Group created" });
                overlay.hide();
                blur.removeBlur();
                div.fadeOut('fast');
                name.val('');
                ta.val('');
                setTimeout(function(){
                  window.location.href = DIR+"/groups/"+data.mssg;
                }, 500);
              }
            });
          }
        });

      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO INVITE TO GROUP
(function($){
  $.fn.inviteToGrp = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = $(this);
    var grp = elem.data('grp');

    elem.on('click', function(e){
      e.preventDefault();
      $.ajax({
        url: DIR+"/ajaxify/ajax_requests/groups_requests.php",
        method: "POST",
        data: {inviteToGrp: grp},
        beforeSend: function(){
          $('.display_content').html("<div class='spinner'><span></span><span></span><span></span></div>");
        },
        success: function(data){
          $('.display_content').html(data);
          $('.display_content').hide().slideDown(100);
          $('.display').displayOptions({
            title: "Select to invite"
          });

          var user = $('.share_userid');
          var post = $('.share_postid');

          $('.select_receiver').on('click', function(e){
            $('.select_receiver').removeClass('select_receiver_toggle');
            $(this).addClass('select_receiver_toggle');
            var data = $(this).data('userid');
            var username = $(this).find('.d_i_username').text();
            user.val(data);
            post.val(grp);

            $.ajax({
              url: DIR+"/ajaxify/ajax_requests/groups_requests.php",
              dataType: "JSON",
              method: "POST",
              data: {
                inviteTo: user.val(),
                inviteGrp: post.val()
              },
              success: function(data){
                console.log(data);
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

  }
  return this;
}(jQuery));

// FUNCTION TO JOIN GROUPS
(function($){
  $.fn.joinGrp = function(options){
    this.each(function(e){
      var defaults = {
        when: null
      };
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var parent = elem.parent();
      var to = parent.data('grp');

      elem.on('click', function(e){
        e.preventDefault();
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/groups_requests.php",
          method: "GET",
          data: {joinGrp: to},
          beforeSend: function(){
            elem.text('Wait..');
          },
          success: function(data){
            elem.text('Join group');
            if (settings.when == "main") {
              location.reload();
            }
            elem.remove();
            parent.html('<a href="#" class="pri_btn leave_grp">Leave group</a>');
            $('.leave_grp').leaveGrp();
          }
        });
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO LEAVE GROUPS
(function($){
  $.fn.leaveGrp = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);
      var parent = elem.parent();
      var to = parent.data('grp');

      elem.on('click', function(e){
        e.preventDefault();
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/groups_requests.php",
          method: "GET",
          data: {leaveGrp: to},
          beforeSend: function(){
            elem.text('Wait..');
          },
          success: function(data){
            elem.text('Leave group');
            elem.remove();
            parent.html("<a href='#' class='pri_btn join_grp'>Join group</a>");
            $('.join_grp').joinGrp({when: "main"});
          }
        });
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO ADD MEMBERS
(function($){
  $.fn.addGroupMembers = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = this;
    var div = $('.a_m_selector');

    elem.on('keyup', function(e){
      var value = $(this).val();
      var grp = $(this).data('grp');
      if (value != "" && value != " ") {
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/groups_requests.php",
          data: {getGrpMem: value, getGrpGrp: grp},
          success: function(data){
            div.find('.grp_to_ul').html(data);
            div.show();
            div.perfectScrollbar();

            $('.select_user_to_add').on('click',function(e){
              var id = $(this).data('user');
              var name = $(this).data('name');
              div.hide();
              elem.val('');
              $.ajax({
                url: DIR+"/ajaxify/ajax_requests/groups_requests.php",
                data: {grpAddMem: id, grpAdd: grp},
                success: function(data){
                  elem.focus();
                  $('.notify').notify({ value: name+" added" });
                }
              });
            });

          }
        });
      } else if (value == "") {
        div.hide();
      }
    });

  }
  return this;
}(jQuery));

// FUNCTION TO EDIT GROUP
(function($){
  $.fn.editGrp = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = $(this);
    var save = elem.find('.g_e_save_btn');
    var grpname = elem.find('.g_e_name > input[type="text"]');
    var grpbio = elem.find('textarea');
    var grppr = elem.find('#grp_private');

    save.on('click', function(e){
      e.preventDefault();

      var checked = $('#grp_private:checked').length > 0;
      var options = ((checked) ? "private" : "public");

      $.ajax({
        url: DIR+"/ajaxify/ajax_requests/groups_requests.php",
        data: {
          editname: grpname.val(),
          editbio: grpbio.val(),
          editpri: options,
          editGrp: elem.data('grp')
        },
        dataType: "json",
        method: "POST",
        success: function(data){
          console.log(data);
          save.blur();
          $('.notify').notify({value: "Updated"});
          $('.pro_username > a').text(data.name);
          $('.pro_name > span').text(data.pri+" group");
        }
      });

    });

  }
  return this;
}(jQuery));

// FUNCTION TO CHANGE GROUP ADMIN
(function($){
  $.fn.changeGrpAdmin = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = this;

    elem.on('click', function(e){
      e.preventDefault();
      // change__grp__admin();
      $('.prompt').myPrompt({
        title: "Change group admin",
        value: "Group admin will be changed. And you will no longer be the admin of this group.",
        doneText: "Change",
        type: "change_grp_admin"
        // post: $(this)
      });
    });

  }
  return this;
}(jQuery));

// FUNCTION TO DELETE GROUP
(function($){
  $.fn.deleteGrp = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = $(this);

    elem.on('click', function(e){
      e.preventDefault();
      // dlt__grp($(this));
      $('.prompt').myPrompt({
        title: "Delete group",
        value: "This group will be premanently deleted. And you won't be able to find the group.",
        doneText: "Delete",
        type: "dlt_grp",
        post: $(this)
      });
    });

  }
  return this;
}(jQuery));

// FUNCTION TO REMOVE MEMBERS
(function($){
  $.fn.removeMember = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        // rem_mem($(this));
        $('.prompt').myPrompt({
          title: "Remove from group",
          value: "This member will be premanently removed. Member would have to re-join the group.",
          doneText: "Remove",
          type: "rem_mem",
          post: $(this)
        });
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION FOR ALL GRP FEEDS EXCEPT MEMBERS PAGE
(function($){
  $.fn.commonGrpFeeds = function(options){
    var defaults = {
      when: null
    };
    var settings = $.extend({}, defaults, options);

    $(window).on('scroll', function(e){
      if ($(window).scrollTop() + $(window).height() == $(document).height()) {

        if (settings.when == "post") {
          var data = {
            grpFeeds: $('.posts:last').data('postid'),
            grpGrp: $('.user_info').data('grp')
          };
        } else if (settings.when == "members") {
          var data = {
            grpMFeeds: $('.grp_m_on:last').data('memid'),
            grpMGrp: $('.user_info').data('grp')
          };
        }

        if(settings.when == "post"){
          $('.feed_inserted').html('Looking for more posts..');
        } else if (settings.when == "members") {
          $('.feed_inserted').html('Looking for more members..');
        }

        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/groups_requests.php",
          data: data,
          beforeSend: function(){

            if(settings.when == "post"){
              $('.feed_inserted').html('Looking for more posts..');
            } else if (settings.when == "members") {
              $('.feed_inserted').html('Looking for more members..');
            }

          },
          success: function(resp){
            if (settings.when == "post") {
              s(resp);

            } else if (settings.when == "members") {
              $('.feed_inserted').html('Looks like you\'ve reached the end');
              $('.feed_inserted').after(resp);
              $('.feed_inserted').not(':last').remove();
              $('.post_end').on('click', function(e){
                $('html, body').animate({scrollTop: 0}, 450);
              });
            }

          }
        });
      }
    });

  }
  return this;
}(jQuery));

// FUNCTION TO FETCH FEED WHEN REACHED THE END
// function grpFeeds(){
//   $(window).on('scroll', function(e){
//     if ($(window).scrollTop() + $(window).height() == $(document).height()) {
//       $('.feed_inserted').html('Looking for more posts..');
//       $.ajax({
//         url: DIR+"/ajaxify/ajax_requests/groups_requests.php",
//         data: {grpFeeds: $('.posts:last').data('postid'), grpGrp: $('.user_info').data('grp')},
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

// FUNCTION TO FETCH MEMBERS WHEN REACHED THE END
function grpMemFeeds(elem){
  elem.addClass('a_disabled');
  elem.text('Loading members..');
  $.ajax({
    url: DIR+"/ajaxify/ajax_requests/groups_requests.php",
    data: {grpMFeeds: $('.grp_m_on:last').data('memid'), grpMGrp: $('.user_info').data('grp')},
    beforeSend: function(){
      elem.addClass('a_disabled');
      elem.text('Loading members..');
    },
    success: function(data){
      if (data != "") {

        $('.feed_inserted_members').after(data);
        $('.feed_inserted_members').not(':last').remove();
        elem.removeClass('a_disabled');
        elem.text('Load more').blur();

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

      } else if (data == "") {
        elem.remove();
      }

    }
  });
}
