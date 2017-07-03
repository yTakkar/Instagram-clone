// FUNCTION TO SEND MESSAGE VIA BUTTON
(function($){
  $.fn.sendMssgViaBtn = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = this;
    var search = $('.mssg_add_persons').find('input[type="text"]');

    elem.on('click', function(e){
      e.preventDefault();
      $('.mssg_add_persons').slideToggle('fast');
      search.focus();

      search.on('keyup', function(e){
        var value = $(this).val();
        var people = $('.mssg_persons');
        if(value != ""){
          $.ajax({
            url: DIR+"/ajaxify/ajax_requests/message_requests.php",
            data: {getPeople: value},
            success: function(data){
              // console.log(data);
              people.find('ul').html(data);
              people.show();

              $('.select_u').on('click', function(e){
                var id  = $(this).data('userid');
                var username = $(this).find('span').text();

                people.hide();
                $('.mssg_add_persons').slideUp('fast');
                search.val('');

                var mssg_to = $('.mssg_to');
                var hidden = mssg_to.find('.to_holder');
                var textarea = $('.m_t_ta');
                var name = $('.con_name');
                var send_cancel = mssg_to.find('.m_t_cancel');
                var send_done = mssg_to.find('.m_t_done');

                hidden.val(id);
                $('.m_t_username').text(username);
                $('.overlay').show();
                blur.addBlur();
                mssg_to.fadeIn('fast');
                name.focus();

                send_cancel.on('click', function(e){
                  e.preventDefault();
                  $('.overlay').hide();
                  blur.removeBlur();
                  mssg_to.fadeOut('fast');
                  hidden.val('');
                  name.val('');
                  textarea.val('');
                });

                send_done.on('click', function(e){
                  e.preventDefault();
                  var value = textarea.val();
                  var cname = name.val();
                  var to = hidden.val();
                  $.ajax({
                    url: DIR+"/ajaxify/ajax_requests/message_requests.php",
                    data: {
                      mssgViaBtn: value,
                      viaTo: to,
                      cname: name.val()
                    },
                    success: function(data){
                      send_done.off('click');
                      console.log(data);
                      $('.overlay').hide();
                      blur.removeBlur();
                      mssg_to.fadeOut('fast');
                      if (data == "exists") {
                        $('.notify').notify({value: "Name already exists!"});
                        hidden.val('');
                        name.val('');
                        textarea.val('');
                      } else if (data == "ok") {
                        setTimeout(function () {
                          location.reload();
                        }, 500);
                      }
                    }
                  });
                });

              });

            }
          });
        } else if (value == "") {
          people.hide();
        }
      });

    });

  }
  return this;
}(jQuery));

// FUNCTION TO CONSTANTLY UPDATE UNREAD CONVERSATION
(function($){
  $.fn.constantUpdateCon = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('mousemove', function(e){
        var insert = $(this).find('.m_sr_unread');
        var con = $(this).data('cid');
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/message_requests.php",
          data: {conUpdateCon: con},
          dataType: "JSON",
          success: function(data){
            insert.text(data.uC);
          }
        });
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO CONSTANTLY UPDATE UNREAD GROUP CONVERSATION
(function($){
  $.fn.constantUpdateGrpCon = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('mousemove', function(e){
        var insert = $(this).find('.m_sr_unread');
        var con = $(this).data('gcid');
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/message_requests.php",
          data: {conUpdateGrpCon: con},
          dataType: "JSON",
          success: function(data){
            insert.text(data.uC);
          }
        });
      });

    });
    return this;
  }
}(jQuery));

function delete_mssg(elem, by){
  var parent = elem.parent().siblings();
  if (by == "user") {
    var conid = parent.data('conid');
  } else if (by == "group") {
    var conid = parent.data('grp_con_id');
  }
  var mssgid = parent.data('mssgid');
  var type = parent.data('type');
  $.ajax({
    url: DIR+"/ajaxify/ajax_requests/message_requests.php",
    data: {dltmssg: mssgid, dltconid: conid, mssgType: type, dltmssgby: by},
    success: function(data){
      parent.parent().slideUp(100, function(){
        $(this).slideUp(100).remove();
      });
    }
  });
}

function edit_message(elem){
  var parent = elem.parent().parent();
  var text = parent.find('.m_m');
  var mssgid = text.data('mssgid');

  text.prop('contenteditable', true).addClass('m_editable_toggle').focus();
  elem.parent().slideUp(100);

  $(window).on('keypress', function(e){
    var key = ((e.which) ? e.which : e.keyCode);
    if (key == 13) {
      var value = text.text();
      if(value != ""){
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/message_requests.php",
          data: {editMssg: mssgid, editText:value},
          dataType: "JSON",
          success: function(data){
            text.text(data.return);
            text.prop('contenteditable', false).removeClass('m_editable_toggle');
            $('.notify').notify({value: "Message edited"});
            $(window).off('keypress');
          }
        });
      }
    }
  });

  text.on('blur', function(e){
    text.prop('contenteditable', false).removeClass('m_editable_toggle');
  });
}

function optionsClosure(){
  $('.mssg_options').hide();
  $('.m_m_exp').toggleClass('m_m_exp_toggle');
}

function textChat(elem, by){
  var text = $('.send_mssg');

  if (by == "user") {
    var to = elem.parent().data('u');
    var con = elem.parent().data('conid');
    var d = {
      messageText: text.val(),
      mssgTo: to,
      mssgCon: con,
      mssgOf: "user"
    };

  } else if (by == "group") {
    var grp = elem.parent().data('grp_conid');
    var d = {
      messageText: text.val(),
      mssgCon: grp,
      mssgOf: "group"
    };
  }

  if (text.val() == "") {
    text.focus();
  } else {
    $.ajax({
      url: DIR+"/ajaxify/ajax_requests/message_requests.php",
      data: d,
      type: "POST",
      beforeSend: function(){
        $('.send_mssg_before').fadeIn(50);
      },
      success: function(data){
        $('.send_mssg_before').fadeOut(50);
        var v = text.val().replace(/\n\r?/g, '<br />');
        // /(\.com|http:\/\/|https:\/\/)/i
        // if (v.match(/((http|https):\/\/)?(www.)?[a-zA-Z0-9]+\.[a-zA-Z./?=&]+/gi)){
        //   var ht = "<div class='m_m_divs my_mm_div'><div class='m_m my_mm'><a href='"+ v +"' class='my_m_m_link' target='_blank'>"+ v +"</a></div><span class='m_m_time'>Just now</span></div>";
        // } else {
          var ht = "<div class='m_m_divs my_mm_div'><div class='m_m my_mm'>"+ v +"</div><span class='m_m_time'>Just now</span></div>";
        // }
        $('.mssg_helper').before(ht);
        $(ht).hide().fadeIn(200);
        $('.m_m_dlt, .m_m_edit').description({extraTop: 8});
        $('.m_m_wrapper').animate({scrollTop: 10000000}, 500);
        text.val('');
        text.focus();
      }
    });
  }
}

function imageChat(elem, by){
  optionsClosure();
  var file = elem.prop('files')[0];
  var type = file.type;
  var allowed = ["image/jpeg", "image/png", "image/gif"];
  if (!((type == allowed[0]) || (type == allowed[1]) || (type == allowed[2]))) {
    $('.notify').notify({value: "Select only images"});
  } else {

    var form = new FormData();
    form.append("mssgImage", file);

    if (by == "user") {
      // var to = elem.parent().parent().parent().data('u');
      // var con = elem.parent().parent().parent().data('conid');
      var to = elem.data('u');
      var con = elem.data('conid');
      form.append('mIto', to);
      form.append('conImg', con);
      form.append('conImgBy', 'user');

    } else if (by == "group") {
      // var con = elem.parent().parent().parent().data('grp_con_id');
      var con = elem.data('grp_con_id');
      form.append('conImg', con);
      form.append('conImgBy', 'group');
    }

    $.ajax({
      url: DIR+"/ajaxify/ajax_requests/message_requests.php",
      data: form,
      type: "POST",
      processData: false,
      contentType: false,
      dataType: "JSON",
      beforeSend: function(){
        $('.send_mssg_before').fadeIn(50);
      },
      success: function(data){
        console.log(data);
        $('.send_mssg_before').fadeOut(50);
        elem.val('');
        var ht = "<div class='m_m_divs my_mm_div'><div class='m_m my_mm'><img src='"+ DIR +"/message/Instagram_"+ data.m +"' class='m_m_img'></div><span class='m_m_time'>Just now</span></div>";
        $('.mssg_helper').before(ht);
        $('.m_m_wrapper').animate({scrollTop: 10000000}, 500);
        $('.m_m_img').imageShow();
      }
    });

  }
}

function editConName(by){
  var name = $('.m_m_t_c > span.con_name');
  name.prop('contenteditable', true).focus().addClass('editable_toggle');

  $(window).on('keypress', function(e){
    var key = ((e.which) ? e.which : e.keyCode);
    if (key == 13) {
      var value = name.text();

      if (by == "user") {
        var con = $('.mssg_messages').data('conid');
        var u = $('.mssg_messages').data('u');
      } else if (by == "group") {
        var con = $('.mssg_messages').data('grp_con_id');
      }

        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/message_requests.php",
          data: {
            editValue: value,
            editCon: con,
            editU: u,
            editOf: by
          },
          success: function(data){
            console.log(data);
            var new_name = value.replace(/[<>]/, '');
            name.prop('contenteditable', false).removeClass('editable_toggle');

            if (by == 'user') {
              $('.mssg_left').find('#c_'+con).find('.m_sr_username').text(nameShortener(new_name, 20));
            } else if (by == "group") {
              $('.mssg_left').find('#cgrp_'+con).find('.m_sr_username').text(nameShortener(new_name, 20));
            }

            var ht = "<div class='m_m_divs m_m_info_div'><span class='mssg_info'>You changed conversation name to <span class='m_m_name_change'>"+ new_name +"</span></span></div>";
            $('.mssg_helper').before(ht);
            $(ht).hide().slideDown(100);
            $('.m_m_wrapper').animate({scrollTop: 100000}, 500);
            $(window).off('keypress');
          }
        });
    }
  });

  name.on('blur', function(e){
    name.prop('contenteditable', false).removeClass('editable_toggle');
  });
}

function change__grp__con__avatar(elem, e){
  var file = e.files[0];
  var name = file.name;
  var type = file.type;
  var allowed = ['image/png', 'image/jpeg', 'image/gif'];

  if (!((type == allowed[0]) || (type == allowed[1]) || (type == allowed[2]))) {
    $('.notify').notify({
      value: "Only images"
    });
  } else {

    var grp = elem.parent().data('grp_con_id');

    var form = new FormData();
    form.append("edit_grp_con_ava", file);
    form.append('edit_grp_con_grp', grp);

    $.ajax({
      url : DIR+"/ajaxify/ajax_requests/message_requests.php",
      type: "POST",
      processData: false,
      contentType: false,
      dataType: "JSON",
      data: form,
      beforeSend: function(){
        $('.send_mssg_before').text('changing avatar..');
        $('.send_mssg_before').fadeIn(50);
        $('.overlay-2').show();
      },
      success: function(data){
        console.log(data);
        $('.send_mssg_before').text('Sending message..');
        $('.send_mssg_before').fadeOut(50);
        $('.overlay-2').hide();
        $('.sli_avatar_img').prop('src', DIR+'/grp_mssg_avatar/Instagram_'+data.grp_av);
        $('.mssg_left').find('#cgrp_'+grp).find('img').prop('src', DIR+'/grp_mssg_avatar/Instagram_'+data.grp_av);
        var ht = "<div class='m_m_divs m_m_info_div'><span class='mssg_info'>You changed the group avatar</span></div>";
        $('.mssg_helper').before(ht);
        $(ht).hide().slideDown(100);
        $('.m_m_wrapper').animate({scrollTop: 100000}, 500);
        $('.notify').notify({ value: "Group avatar changed" });
        $('#edit_grp_con_ava').val('');
      }
    });

  }
}

function add__grp__con__members(){
  var search = $('.sli_add_search').find('input[type="text"]');
  var div = $('.sli_to_persons');
  search.parent().slideToggle(100);
  search.focus();

  search.on('keyup', function(e){
    var value = $(this).val();
    var grp = $(this).parent().data('grp_con_id');
    if (value != "") {
      $.ajax({
        url: DIR+"/ajaxify/ajax_requests/message_requests.php",
        data: {getGrpConMem: value, getGrpConMemGrp: grp},
        success: function(data){
          console.log(data);
          div.find('.grp_to_ul').html(data);
          div.show();
          div.perfectScrollbar();

          $('.grp_to_select_u').on('click',function(e){
            var id = $(this).data('user');
            var name = $(this).data('name');
            div.hide();
            search.val('');
            $.ajax({
              url: DIR+"/ajaxify/ajax_requests/message_requests.php",
              data: {grpConAddMem: id, grpConAdd: grp},
              beforeSend: function(){
                $('.send_mssg_before').text('Adding..');
                $('.send_mssg_before').fadeIn(50);
              },
              success: function(data){
                console.log(data);
                $('.send_mssg_before').text('Added successfully');
                $('.send_mssg_before').fadeOut(50);
                search.focus();
                var ht = "<div class='m_m_divs m_m_info_div'><span class='mssg_info'>You added <a class='m_m_name_change' href='"+ DIR +"/profile/"+ name +"'>"+ name +"</a> to group</span></div>";
                $('.mssg_helper').before(ht);
                $(ht).hide().slideDown(100);
                $('.m_m_wrapper').animate({scrollTop: 100000}, 500);
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

function mmSlider(elem, by){
  var slider = $('.m_m_slider');
  var con = elem.parent().parent().parent().parent().parent();

  if (by == "user") {
    var d = {conInfo: con.data('conid')};
  } else if (by == "group") {
    var d = {grpConInfo: con.data('grp_con_id')};
  }

  $.ajax({
    url: DIR+"/ajaxify/ajax_requests/message_requests.php",
    data: d,
    success: function(data){
      // console.log(data);
      $('.m_m_slider').html(data);
      slider.animate({ right: "0px"}, 200);
      $('.sli_media_img').imageShow({info: "no_post_yes"});
      // $('.sli_media_img').description();
      $('.sli_avatar > img').imageShow();
      $('.sli_cancel').on('click', function(e){
        slider.animate({ right: "-380px"}, 200);
      });

      $('.sli_with_leave').on('click', function(e){
        e.preventDefault();
        $('.prompt').myPrompt({
          title: "Leave group",
          value: "Are you sure you want to leave group? You won't be receiving any messages from this group.",
          doneText: "Leave",
          type: "leave_con_grp",
          post: $(this)
        });
        // leaveConGrp($(this));
      });

      $('.sli_with_remove').on('click', function(e){
        e.preventDefault();
        var username = $(this).parent().siblings().filter('a').text();
        $('.prompt').myPrompt({
          title: "Remove "+ username +" from group",
          value: "Are you sure you want to permanently remove "+username+" from group.",
          doneText: "Remove",
          type: "remove_con_grp",
          post: $(this)
        });
        // removeGrpMem($(this));
      });

      $('#edit_grp_con_ava').on('change', function(e){
        change__grp__con__avatar($(this), this);
      });

      $('.sli_add_mem').on('click', function(e){
        e.preventDefault();
        add__grp__con__members();
      });

    }
  });
}

// FUNCTION TO SELECT A CONVERSATION
(function($){
  $.fn.selectConversation = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        var id = $(this).data('cid');
        var user = $(this).data('utwo');
        $(this).find('.m_sr_unread').text('');

        $('.mssg_sr').removeClass('mssg_sr_toggle');
        $(this).addClass('mssg_sr_toggle');

        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/message_requests.php",
          data: {
            selectC: id,
            user: user
          },
          beforeSend: function(){
            $('.mssg_right').html('<div class="spinner"><span></span><span></span><span></span></div>');
          },
          success: function(data){
            // console.log(data);
            $('.mssg_right').html(data).hide().fadeIn(100);
            $('.send_mssg').focus();

            $('.m_m_img').imageShow({info: "no_post_yes"});
            $('.m_m_wrapper').animate({scrollTop: 100000}, 500);
            $('.m_m_exp').toggleMenu({
              menu: $('.mssg_options')
            });
            $('.m_m_wrapper').perfectScrollbar();
            $('.m_m_exp').on('click', function(e){
              $(this).toggleClass('m_m_exp_toggle');
            });

            $('.m_m').on('click', function(e){
              $(this).parent().siblings().find('.m_m_tools').slideUp(100);
              $(this).siblings().filter('.m_m_tools').slideToggle(100);
            });
            $('.m_m_dlt, .m_m_edit, .m_m_status').description({extraTop: 8});
            // $('.mssg_sticker, .mssg_img').description();

            $('.emoji').addClass('emoji_fixed');
            $('.mssg_emoji_btn').emoji({
              pseudo: null,
              textarea: $('.send_mssg'),
              top: "65%",
              left: "64.6%",
              event: "hover"
            });

            $('.mssg_send, .m_m_wrapper, .send_mssg').on('mouseover', function(e){
              $('.emoji').hide();
            });

            $('.mssg_sticker').sticker({
              when: "message",
              mssgTo: $('.mssg_messages').data('u'),
              mssgId: $('.mssg_messages').data('conid')
            });

            $('.m_m_dlt').on('click', function(e){
              delete_mssg($(this), "user");
            });

            $('.m_m_edit').on('click', function(e){
              edit_message($(this));
            });

            $('.add_mssg_form').on('submit', (function(e){
              e.preventDefault();
              textChat($(this), "user");
            }));

            $('#mssg_add_img').on('change', function(e){
              imageChat($(this), "user");
            });

            $('.mssg_options').find('a').on('click', function(e){
              e.preventDefault();
              optionsClosure();
            });

            $('.dlt_mssgs').on('click', function(e){
              var l = $('.my_mm_div').length;
              $('.prompt').myPrompt({
                title: "Unsend all message",
                value: "All "+ l +" message(s) sent by you will be permanently deleted from both sides.",
                doneText: "Delete",
                type: "dlt_all_mssg",
                post: $(this).parent().parent().parent().parent().parent()
              });
              // delete_all_mssg($(this).parent().parent().parent().parent().parent());
            });

            $('.dlt_con').on('click', function(e){
              $('.prompt').myPrompt({
                title: "Delete this conversation",
                value: "This conversation will be premanently deleted from both sides.",
                doneText: "Delete",
                type: "dlt_con",
                post: $(this).parent().parent().parent().parent().parent()
              });
              // delete__con($(this).parent().parent().parent().parent().parent());
            });

            $('.m_m_slider').perfectScrollbar();
            $('.m_m_info').on('click', function(e){
              mmSlider($(this), "user");
            });

            $('.edit_con_name').on('click', function(e){
              editConName("user");
            });

            $('.mssg_messages').on('mousemove', function(e){
              var con = $(this).data('conid');
              $.ajax({
                url: DIR+"/ajaxify/ajax_requests/message_requests.php",
                data: {updateCon: con},
                dataType: "JSON",
                success: function(data){
                  // console.log(data);
                  $('#c_'+con).find('.m_sr_unread').text(data.cons);
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

// SELECT GROUP CONVERSATION
(function($){
  $.fn.selectGrpCon = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        // console.log($(this));
        var id = $(this).data('gcid');
        $(this).find('.m_sr_unread').text('');

        $('.mssg_sr').siblings().removeClass('mssg_sr_toggle');
        $(this).addClass('mssg_sr_toggle');

        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/message_requests.php",
          data: { selectGrpCon: id },
          beforeSend: function(){
            $('.mssg_right').html('<div class="spinner"><span></span><span></span><span></span></div>');
          },
          success: function(data){
            // console.log(data);
            $('.mssg_right').html(data);
            $('.send_mssg').focus();

            $('.m_m_img').imageShow({info: "no_post_yes"});
            $('.m_m_wrapper').animate({scrollTop: 100000}, 500);
            $('.m_m_exp').toggleMenu({
              menu: $('.mssg_options')
            });
            $('.m_m_wrapper').perfectScrollbar();
            $('.m_m_exp').on('click', function(e){
              $(this).toggleClass('m_m_exp_toggle');
            });

            $('.m_m').on('click', function(e){
              $(this).parent().siblings().find('.m_m_tools').slideUp(100);
              $(this).siblings().filter('.m_m_tools').slideToggle(100);
            });
            $('.m_m_dlt, .m_m_edit, .m_m_status').description({extraTop: 8});
            // $('.mssg_sticker, .mssg_img').description();

            $('.emoji').addClass('emoji_fixed');
            $('.mssg_emoji_btn').emoji({
              pseudo: null,
              textarea: $('.send_mssg'),
              top: "65%",
              left: "64.6%",
              event: "hover"
            });

            $('.mssg_send, .m_m_wrapper, .send_mssg').on('mouseover', function(e){
              $('.emoji').hide();
            });

            $('.add_mssg_form').on('submit', (function(e){
              e.preventDefault();
              textChat($(this), "group");
            }));

            $('#mssg_add_img').on('change', function(e){
              imageChat($(this), "group");
            });

            $('.mssg_sticker').sticker({
              when: "group_message",
              mssgId: $('.mssg_messages').data('grp_con_id')
            });

            $('.mssg_options').find('a').on('click', function(e){
              e.preventDefault();
              optionsClosure();
            });

            $('.dlt_mssgs').on('click', function(e){
              var l = $('.my_mm_div').length;
              $('.prompt').myPrompt({
                title: "Unsend all message",
                value: "All "+ l +" message(s) sent by you will be permanently deleted from both sides.",
                doneText: "Delete",
                type: "grp_dlt_all_mssg",
                post: $(this).parent().parent().parent().parent().parent()
              });
              // delete_all_mssg($(this).parent().parent().parent().parent().parent());
            });

            $('.dlt_con').on('click', function(e){
              $('.prompt').myPrompt({
                title: "Delete group",
                value: "This group will be premanently deleted from both sides. And you won't be able to find it.",
                doneText: "Delete",
                type: "grp_dlt_con",
                post: $(this).parent().parent().parent().parent().parent()
              });
              // delete__con($(this).parent().parent().parent().parent().parent());
            });

            $('.edit_con_name').on('click', function(e){
              editConName("group");
            });

            $('.m_m_dlt').on('click', function(e){
              delete_mssg($(this), "group");
            });

            $('.m_m_edit').on('click', function(e){
              edit_message($(this));
            });

            $('.ch_grp_con_admin').on('click', function(e){
              e.preventDefault();
              $('.prompt').myPrompt({
                title: "Change group admin",
                value: "Group admin will be changed. And you will no longer be the admin of this group.",
                doneText: "Change",
                type: "change_grp_con_admin",
                post: $(this)
              });
              // change__con__grp__admin($(this));
            });

            $('.m_m_slider').perfectScrollbar();
            $('.m_m_info').on('click', function(e){
              mmSlider($(this), "group");
            });

            $('.mssg_messages').on('mousemove', function(e){
              var con = $(this).data('grp_con_id');
              $.ajax({
                url: DIR+"/ajaxify/ajax_requests/message_requests.php",
                data: {grpUpdateCon: con},
                dataType: "JSON",
                success: function(data){
                  // console.log(data);
                  $('#cgrp_'+con).find('.m_sr_unread').text(data.cons);
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

// FUNCTION TO CONSTANTLY GET ALL UNREAD MESSAGES
(function($){
  $.fn.getAllUnreadMssg = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    setInterval(function(){
      $.ajax({
        url: DIR+"/ajaxify/ajax_requests/message_requests.php",
        data: {getAllUnreadMssg:"user"},
        dataType: "json",
        success: function(data){
          // console.log(data);
          $('.messages').find('.m_n_new').text(data.count);
        }
      });
    }, 1000);

  }
  return this;
}(jQuery));

// FUNCTION FOR CREATING A GROUP CONVERSATION
(function($){
  $.fn.addGrpCon = function(options){
    var defaults = {};
    var settings = $.extend({}, defaults, options);

    var elem = $(this);
    var div = $('.grp_to');
    var name = div.find('.grp_t_name');
    var search = div.find('.grp_t_add');
    var ch_avatar = div.find('#grp_to_avatar_file');
    var avatar = div.find('.grp_to_img > img');
    var send_cancel = div.find('.grp_t_cancel');
    var send = div.find('.grp_t_done');
    var hidden_input = div.find('.grp_to_holder');
    var avatar_hidden = div.find('.grp_to_avatar');

    elem.on('click', function(e){
      e.preventDefault();
      $('.overlay').show();
      blur.addBlur();
      div.fadeIn('fast');
      name.focus();

      function getTags(){
        var array = [];
        var ff = div.find('.grp_t_members > span');
        for (var i = 0; i < ff.length; i++) {
          array[i] = ff[i].innerHTML;
        }
        // var ooh = unique(array);
        var string = array.join(',');
        hidden_input.val(string);
        console.log(hidden_input.val());
      }

      function blurHide(){
        if ($('.grp_t_added').length == 0) {
          $('.grp_t_members').css('height', '0px');
        } else {
          $('.grp_t_members').css('height', 'auto');
        }
        var height = div.find('.grp_t_members').height();
        if (height%2 != 0) {
          var newHeight = parseInt(div.find('.grp_t_members').height())+1+"px";
          div.find('.grp_t_members').css('height', newHeight);
        }
      }

      blurHide();

      var cLick = function(elem){
        elem.fadeOut('fast');
        elem.remove();
        getTags();
        blurHide();
      }

      ch_avatar.on('change', function(e){
        var file = this.files[0];
        var type = file.type;
        var allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if (!((type == allowed[0]) || (type == allowed[1]) || (type == allowed[2]))) {
          $('.notify').notify({ value:"Only image is allowed" });
          ch_avatar.val('');
        } else {
          var reader = new FileReader();
          reader.onload = function(e){
            avatar.prop('src', e.target.result)
          }
          reader.readAsDataURL(file);
          avatar_hidden.val(avatar.prop('src'));
        }
      });

      search.on('keyup', function(e){
        var value = $(this).val();
        if (value != "") {
          $.ajax({
            url: DIR+"/ajaxify/ajax_requests/message_requests.php",
            method: "GET",
            data: {
              addGrpValue: value,
              except: hidden_input.val()
            },
            success: function(data){
              div.find('.grp_to_ul').html(data);
              div.find('.grp_to_persons').show();
              div.find('.grp_to_persons').perfectScrollbar();
              div.find('.grp_to_select_u').on('click', function(e){
                var username = $(this).find('span').text();
                div.find('.grp_t_helper').after("<span class='grp_t_added knowing' data-show='remove' data-name='"+ username.trim() +"'>"+ username.trim() +"</span>");
                getTags();
                div.find('.grp_to_persons').hide();
                search.val('');
                search.focus();
                blurHide();
                $('.knowing').on('click', function(e){
                  cLick($(this));
                });
              });
            }
          });
        } else if (value == "") {
          div.find('.grp_to_persons').hide();
        }
      });

      getTags();

      send_cancel.on('click', function(e){
        e.preventDefault();
        $('.overlay').hide();
        blur.removeBlur();
        div.fadeOut('fast');
        hidden_input.val('');
        name.val('');
        search.val('');
        $('.grp_t_added').remove();
      });

      send.on('click', function(e){
        e.preventDefault();
        var f = $('#grp_to_avatar_file').prop('files')[0];
        var name_value = name.val();
        var h_value = hidden_input.val();
        var form = new FormData();
        form.append('grpAvatar', f);
        form.append('addGrpName', name_value);
        form.append('addGrpMembers', h_value);
        if (name_value != "" && h_value != "") {
          $.ajax({
            url: DIR+"/ajaxify/ajax_requests/message_requests.php",
            type: "POST",
            processData: false,
            contentType: false,
            data: form,
            success: function(data){
              $('.overlay').hide();
              blur.removeBlur();
              div.fadeOut('fast');
              hidden_input.val('');
              name.val('');
              search.val('');
              $('.grp_t_added').remove();
              setTimeout(function () {
                location.reload();
              }, 400);
            }
          });
        } else if (name_value == "") {
          name.focus();
        } else if (h_value == "") {
          search.focus();
        }
      });

    });

  }
  return this;
}(jQuery));
