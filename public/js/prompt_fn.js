/*
  THIS FILE INCLUDES PROMPT FUNCTION
  AND ALL THE FUNCTIONS ASSOCIATED WHEN
  CLICKED OK BUTTON
*/

// PLUGIN FOR PROMPT
(function($){
  $.fn.myPrompt = function(options){
    var defaults = {
      title   : "Instagram",
      value   : "Instagram",
      doneText: "Done",
      type    : null,
      post    : null
    };
    var settings = $.extend({}, defaults, options);

    var elem = this;
    var overlay = $('.overlay');

    elem.find('.prompt_title').text(settings.title);
    elem.find('.prompt_content').text(settings.value);
    elem.find('.prompt_done').text(settings.doneText);

    overlay.show();
    blur.addBlur();
    elem.fadeIn("fast");
    elem.find('.prompt_done').focus();

    var array = [elem.find('.prompt_top > span:last-of-type'), elem.find('.prompt_cancel')];
    for (item of array) {
      item.on('click', function(e){
        e.preventDefault();
        blur.removeBlur();
        overlay.hide();
        elem.fadeOut("fast");
        elem.find('.prompt_title').text('');
        elem.find('.prompt_content').text('');
      });
    }

    elem.find('.prompt_done').on('click', function(e){
      e.preventDefault();
      if (settings.type == "delete_post") {
        delete__post(settings.post);
      } else if (settings.type == "untag_post") {
        un__tag(settings.post);
      } else if (settings.type == "unshare_post") {
        un__share(settings.post);
      } else if (settings.type == "block_user") {
        bl__ock(settings.post);
      } else if (settings.type == "dlt_all_mssg") {
        delete_all_mssg(settings.post, "user");
      } else if (settings.type == "grp_dlt_all_mssg") {
        delete_all_mssg(settings.post, "group");
      } else if (settings.type == "dlt_con") {
        delete__con(settings.post, "user");
      } else if (settings.type == "grp_dlt_con") {
        delete__con(settings.post, "group");
      } else if (settings.type == "delete_comment") {
        delete__comment(settings.post);
      } else if (settings.type == "leave_con_grp") {
        leave__ConGrp(settings.post);
      } else if (settings.type == "remove_con_grp") {
        remove__GrpMem(settings.post);
      } else if (settings.type == "dlt_grp") {
        dlt__grp(settings.post);
      } else if (settings.type == "rem_mem") {
        rem__mem(settings.post);
      } else if (settings.type == "dlt_acc") {
        dlt__acc(settings.post);
      } else if (settings.type == "change_grp_admin") {
        change__grp__admin();
      } else if (settings.type == "change_grp_con_admin") {
        change__con__grp__admin(settings.post);
      } else if (settings.type == "rem_fav") {
        rem__from__fav(settings.post);
      }

      overlay.hide();
      blur.removeBlur();
      elem.fadeOut('fast');
      elem.find('.prompt_title').text('');
      elem.find('.prompt_content').text('');
    });

    return this;

  }
}(jQuery));

// FUNCTION TO BE USED IN PROMPT
function delete__post(elem){
  // var post = elem.parent().parent().parent().parent().parent();
  $.ajax({
    url     : DIR+"/ajaxify/ajax_requests/post_requests.php",
    data    : {delete_post: elem.data('postid')},
    dataType: "JSON",
    success: function(data){
      console.log(data);
      $('.pro_post > .pro_hg').text(data.posts);
      $('.notify').notify({
        value: "Post deleted"
      });
    }
  });
  elem.slideUp('fast', function(){
    elem.remove();
    setTimeout(function () {
      location.reload();
    }, 200);
  });
}

function un__tag(elem){
  var tags_text = elem.find('.p_tags');
  $.ajax({
    url     : DIR+"/ajaxify/ajax_requests/taggings_requests.php",
    data    : {untag: elem.data('postid')},
    dataType: "JSON",
    success: function(data){
      console.log(data);
      tags_text.text(data.tags);
      $('.notify').notify({
        value: "Untagged"
      });
      elem.find('.untag').hide();
    }
  });
}

function un__share(elem){
  $.ajax({
    url  : DIR+"/ajaxify/ajax_requests/share_requests.php",
    data : {unshare: elem.data('postid')},
    success: function(data){
      console.log(data);
      // elem.slideUp('fast');
      $('.notify').notify({
        value: "Unshared"
      });
      elem.find('.unshare').hide();
    }
  });
}

function bl__ock(elem){
  var get = elem.data('getid');
  $.ajax({
    url  : DIR+"/ajaxify/ajax_requests/settings_requests.php",
    data : {block: get},
    success: function(data){
      console.log(data);
      $('.notify').notify({ value: "Blocked "+data });
      elem.hide();
    }
  });
}

function delete__comment(elem){
  var parent = elem.parent().parent();
  var post = parent.parent().parent().data('postid');
  var id = parent.data('commentid');
  $.ajax({
    url: DIR+"/ajaxify/ajax_requests/post_comment_requests.php",
    data: {
      delete_comment: id,
      post: post
    },
    dataType: "JSON",
    success: function(data){
      $('.p_comments').text(data.comments);
      parent.slideUp('fast', function(){
        $(this).remove();
      });
    }
  });
}

function delete_all_mssg(elem, by){
  if (by == "user") {
    var con = elem.data('conid');
  } else if (by == "group") {
    var con = elem.data('grp_con_id');
  }
  $.ajax({
    url : DIR+"/ajaxify/ajax_requests/message_requests.php",
    data: {deleteAllMssg: con, dltAllBy: by},
    success: function(data){
      $('.my_mm_div').slideUp(100, function(){
        $(this).remove();
      });
      $('.notify').notify({value: "Unsent all messages"});
    }
  });
}

function delete__con(elem, by){
  if (by == "user") {
    var con = elem.data('conid');
  } else if (by == "group") {
    var con = elem.data('grp_con_id');
  }

  $.ajax({
    url : DIR+"/ajaxify/ajax_requests/message_requests.php",
    data: {dlt_con: con, dlt_con_by: by},
    success: function(data){
      console.log(data);

      if (by == "user") {
        $('.mssg_left').find('#c_'+con).slideUp(100);
      } else if(by == "group") {
        $('.mssg_left').find('#cgrp_'+con).slideUp(100);
      }

      var nothing = "<div class='home_last_mssg pro_last_mssg'><img src='"+ DIR +"/images/needs/large.jpg'><span>Please select a conversation</span></div>";
      $('.mssg_messages').slideUp(100);
      $('.mssg_right').html(nothing);

      var n = $('.con_count').data('count');
      var nn = (parseInt(n)-1);
      $('.con_count').text(nn+" conversations");
      $('.con_count').data('count', nn);
      setTimeout(function () {
        location.reload();
      }, 300);

    }
  });
}

function leave__ConGrp(elem){
  var grp = elem.parent().data('grp_con_id');
  $.ajax({
    url : DIR+"/ajaxify/ajax_requests/message_requests.php",
    data: {leaveGrp: grp},
    success: function(data){
      console.log(data);
      $('.mssg_left').find('#cgrp_'+grp).slideUp(100);
      var nothing = "<div class='home_last_mssg pro_last_mssg'><img src='"+ DIR +"/images/needs/large.jpg'><span>Please select a conversation</span></div>";
      $('.mssg_messages').slideUp(100);
      $('.mssg_right').html(nothing);
      location.reload();
    }
  });
}

function remove__GrpMem(elem){
  var grp = elem.parent().data('grp_con_id');
  var user = elem.parent().data('user');
  var name = elem.parent().data('username');
  elem.addClass('sec_btn_disabled');
  $.ajax({
    url     : DIR+"/ajaxify/ajax_requests/message_requests.php",
    data    : {removeGrpMem: grp, removeGrpId: user},
    dataType: "JSON",
    beforeSend: function(){
      elem.text('Removing');
    },
    success: function(data){
      elem.text('Removed');
      elem.parent().parent().parent().slideUp(100);
      $('.sli_with_div > span.sli_label').text(data.membersLeft+" group members");
    }
  });
}

function dlt__grp(elem){
  $.ajax({
    url : DIR+"/ajaxify/ajax_requests/groups_requests.php",
    data: {dltGrp: $('.user_info').data('grp')},
    success: function(data){
      console.log(data);
      window.location.href = DIR;
    }
  });
}

function rem__mem(elem){
  $.ajax({
    url: DIR+"/ajaxify/ajax_requests/groups_requests.php",
    data: {
      remMem: elem.data('member'),
      remG: $('.user_info').data('grp')
    },
    success: function(data){
      console.log(data);
      $('.notify').notify({value: "Member removed"});
      elem
        .parent().parent().slideUp(100)
        .remove();
      // setTimeout(function () {
      //   location.reload();
      // }, 400);
    }
  });
}

function dlt__acc(el){
  var input = el.find('input[type="password"]');
  var value = input.val();

  $.ajax({
    url     : DIR+"/ajaxify/ajax_requests/settings_requests.php",
    data    : {dltAcc: value},
    method  : "POST",
    dataType: "JSON",
    success : function(data){
      console.log(data);
      if (data.dlt == "yes") {
        window.location.href = DIR+"/ajaxify/dlt/delete_acc.php";
      } else {
        $('.notify').notify({ value: data.dlt });
      }
    }
  });
}

// FUNCTION TO CHANGE GROUP ADMIN
function change__grp__admin(){
  var grp = $('.user_info').data('grp');
  $.ajax({
    url: DIR+"/ajaxify/ajax_requests/groups_requests.php",
    data: { selectForGrpAdmin: grp },
    beforeSend: function(){
      $('.display_content').html("<div class='spinner'><span></span><span></span><span></span></div>");
    },
    success: function(data){
      // console.log(data);
      $('.display_content').html(data);
      $('.display_content').hide().slideDown(100);
      $('.display').displayOptions({
        title: "Change group admin"
      });

      var user = $('.share_userid');
      var post = $('.share_postid');

      function cgaFinal(user, post){
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/groups_requests.php",
          dataType: "JSON",
          method: "GET",
          data: {
            cgaUser: user.val(),
            cgaGrp: post.val()
          },
          success: function(data){
            console.log(data);
            $('.notify').notify({ value: "Admin changed" });
            $('.overlay').hide();
            blur.removeBlur();
            $('.display').fadeOut('fast');
            if (data.mssg == "ok") { window.location.href = DIR+"/groups/"+grp; }
          }
        });
      }

      $('.select_receiver').on('click', function(e){
        $('.select_receiver').removeClass('select_receiver_toggle');
        $(this).addClass('select_receiver_toggle');
        var data = $(this).data('userid');
        user.val(data);
        post.val(grp);
        cgaFinal(user, post);
      });

    }
  });
}

// FUNCTION TO CHANGE GROUP CONVERSATION ADMIN
function change__con__grp__admin(elem){
  var grp = elem.data('grp');
  $.ajax({
    url: DIR+"/ajaxify/ajax_requests/message_requests.php",
    data: { selectForGrpConAdmin: grp },
    beforeSend: function(){
      $('.display_content').html("<div class='spinner'><span></span><span></span><span></span></div>");
    },
    success: function(data){
      // console.log(data);
      $('.display_content').html(data);
      $('.display_content').hide().slideDown(100);
      $('.display').displayOptions({
        title: "Change group admin"
      });

      var user = $('.share_userid');
      var post = $('.share_postid');

      function cgcaFinal(user, post){
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/message_requests.php",
          dataType: "JSON",
          method: "GET",
          data: {
            cgcaUser: user.val(),
            cgcaGrp: post.val()
          },
          success: function(data){
            console.log(data);
            $('.notify').notify({ value: "Admin changed" });
            $('.overlay').hide();
            blur.removeBlur();
            $('.display').fadeOut('fast');
            if (data.mssg == "ok") {
              setTimeout(function () {
                location.reload();
              }, 500);
            }
          }
        });
      }

      $('.select_receiver').on('click', function(e){
        $('.select_receiver').removeClass('select_receiver_toggle');
        $(this).addClass('select_receiver_toggle');
        var data = $(this).data('userid');
        user.val(data);
        post.val(grp);
        cgcaFinal(user, post);
      });

    }
  });
}

// FUNCTION FOR REMOVING FROM FAVOURITES
function rem__from__fav(el){
  var user = el.data('userid');
  var username = el.data('username');
  var parent = el.parent().parent();
  var getid = $('.user_info').data('userid');
  $.ajax({
    url: DIR+"/ajaxify/ajax_requests/fav_requests.php",
    method: "GET",
    dataType: "JSON",
    data: { remFav: user, getId: getid },
    success: function(data){
      if(data.mssg == "ok"){
        $('.notify').notify({ value: username+" removed" });
        $('.fav_header > span').text(data.count+" favourites");
        parent.slideUp(100);
        parent.remove();
        // setTimeout(function () {
        //   location.reload();
        // }, 100);
      } else {
        $('.notify').notify({ value: data.mssg });
      }
    }
  });
}
