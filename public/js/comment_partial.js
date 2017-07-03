// POST COMMENTS
(function($){
  $.fn.postComment = function(options){
    this.each(function(e){
      var defaults = { refresh: "no" };
      var settings = $.extend({}, defaults, options);

      var elem = $(this), div;

      elem.on('click', function(e){
        div = elem.parent().parent().parent();

        $(window).on('keypress', function(e){
          var code = ((e.which) ? e.which : e.keyCode);
          if (code == "13") {
            var value = elem.val();
            var post = div.data('postid');

            $.ajax({
              url: DIR+"/ajaxify/ajax_requests/post_comment_requests.php",
              type: "GET",
              dataType: "JSON",
              data: {
                value: value,
                post: post
              },
              success: function(data){
                console.log(data);
                elem.val('');
                elem.blur();
                if (data.st == "ok") {
                  div.find('.p_comments').text(data.comments);
                  $('.notify').notify({ value: "You just commented" });
                  if (settings.refresh == "yes") {
                    setTimeout(function(e){
                      location.reload();
                    }, 300);
                  }

                }
              }
            });
          }
        });

        elem.on('blur', function(e){
          $(window).off('keypress');
        });

      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO IMAGE COMMENT
(function($){
  $.fn.imageComment = function(options){
    this.each(function(e){
      var defaults = { refresh: "no" };
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('change', function(e){
        var file = this.files[0];
        var name = file.name;
        var type = file.type;
        var allowed = ['image/png', 'image/jpeg', 'image/gif'];

        if (!((type == allowed[0]) || (type == allowed[1]) || (type == allowed[2]))) {
          $('.notify').notify({ value: "Only images" });
        } else {

          var parent = $(this).parent().parent().parent().parent();
          var form = new FormData();
          form.append("image_comment", file);
          form.append('post', parent.data('postid'));

          $.ajax({
            url : DIR+"/ajaxify/ajax_requests/post_comment_requests.php",
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "JSON",
            data: form,
            success: function(data){
              console.log(data);
              if (data.message == "ok") {
                parent.find('.p_comments').text(data.comments);
                $('.notify').notify({
                  value: "Commented"
                });
                if (settings.refresh == "yes") {
                  setTimeout(function () {
                    location.reload();
                  }, 300);
                }
              }
            }
          });

        }

      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO DELETE COMMENTS
(function($){
  $.fn.deleteComment = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        $('.prompt').myPrompt({
          title: "Delete comment",
          value: "This comment will be deleted. There's no undo so you won't be able to find it.",
          doneText: "Delete",
          type: "delete_comment",
          post: $(this)
        });
        // delete__comment($(this));
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO EDIT COMMENTS
(function($){
  $.fn.editComment = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        var parent = elem.parent().parent();
        var comment = parent.data('commentid');
        var div = parent.find('.ce');
        var cancel = parent.find('.comment_cancel');
        var save = parent.find('.comment_save');

        $('.ce').prop('contenteditable', false).removeClass('editable_toggle').blur();
        $('.comment_edit_tools').slideUp(100);

        div.prop('contenteditable', true).addClass('editable_toggle').focus();
        parent.find('.comment_edit_tools').slideDown(100);

        cancel.on('click', function(e){
          e.preventDefault();
          div.prop('contenteditable', false).removeClass('editable_toggle').blur();
          parent.find('.comment_edit_tools').slideUp(100);
        });

        save.on('click', function(e){
          e.preventDefault();
          var text = div.text();
          var post = parent.parent().parent().data('postid');
          console.log(post);
          if (text != "") {
            $.ajax({
              url: DIR+"/ajaxify/ajax_requests/post_comment_requests.php",
              data: {
                cedit: "yes",
                text: text,
                comment: comment,
                post: post
              },
              success: function(data){
                console.log(data);
                div.prop('contenteditable', false).removeClass('editable_toggle').blur();
                parent.find('.comment_edit_tools').slideUp(100);
                setTimeout(function () {
                  location.reload();
                }, 100);
                save.off('click');
                $('.notify').notify({value: "Post edited"});
              }
            });
          } else if (text == "") {
            div.focus();
          }
        });

      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO LIKE COMMENTS
(function($){
  $.fn.likeComments = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, settings);

      var elem = $(this);

      elem.on('click', function(e){
        var parent = $(this).parent();
        var id = parent.data('commentid');
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/post_comment_requests.php",
          dataType: "JSON",
          data: {likeComment: id},
          success: function(data){
            console.log(data);
            parent.siblings().filter('.comment_likes').text(data.likes);
            elem.fadeOut();
            elem.remove();
            parent.html("<span class='comment_lu comment_unlike' title='Unlike'><i class='material-icons'>thumb_down</i></span>");
            parent.find('.comment_unlike').unlikeComments();
          }
        });
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO UNLIKE COMMENTS
(function($){
  $.fn.unlikeComments = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, settings);

      var elem = $(this);

      elem.on('click', function(e){
        var parent = $(this).parent();
        var id = parent.data('commentid');
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/post_comment_requests.php",
          dataType: "JSON",
          data: {unlikeComment: id},
          success: function(data){
            console.log(data);
            parent.siblings().filter('.comment_likes').text(data.likes);
            elem.fadeOut();
            elem.remove();
            parent.html("<span class='comment_like comment_lu' title='Like'><i class='material-icons'>thumb_up</i></span>");
            parent.find('.comment_like').likeComments();
          }
        });
      });

    });
    return this;
  }
}(jQuery));

// FUNCTION TO GET COMMENT LIKERS
(function($){
  $.fn.commentLikers = function(options){
    this.each(function(e){
      var defaults = {};
      var settings = $.extend({}, defaults, options);

      var elem = $(this);

      elem.on('click', function(e){
        e.preventDefault();
        var parent = $(this).parent().parent().parent();
        var id = parent.data('commentid');
        $.ajax({
          url: DIR+"/ajaxify/ajax_requests/post_comment_requests.php",
          data: {commentLikers: id},
          beforeSend: function(){
            $('.display_content').html("<div class='spinner'><span></span><span></span><span></span></div>");
          },
          success: function(data){
            console.log(data);
            $('.display_content')
              .html(data)
              .hide().slideDown(100)
              .children().eq($('.display_content').children().length-1).children().filter('hr').remove();
            $('.display_follow').follow();
            $('.display_unfollow').unfollow();
            $('.display').displayOptions({
              title: "Comment liked by"
            });
          }
        });
      });

    });
    return this;
  }
}(jQuery));
