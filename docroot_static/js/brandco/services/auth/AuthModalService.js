if (typeof(AuthModalService) === 'undefined') {
    var AuthModalService = function() {
        return {
            getCsrfToken: function() {
                return $('.jsAuthModal > input[name=csrf_token]').val();
            },

            callTemplate: function(template_id, callback) {
                var param = {
                    url: 'auth/api_call_template.json',
                    data: {
                        template_id: template_id,
                        csrf_token: AuthModalService.getCsrfToken()
                    },
                    type: 'POST',
                    success: function(json) {
                        if (json.result === 'ok') {
                            if (typeof(callback) === 'function') {
                                callback(json.html);
                            }
                        } else {
                            alert('エラーが発生しました。更新後、再度お試しください。');
                        }
                    },
                    error: function() {
                        alert('エラーが発生しました。更新後、再度お試しください。');
                    }
                }

                Brandco.api.callAjaxWithParam(param);
            },

            slideIn: function(parent_class, direction, html, callback) {
                var $parent = $(parent_class);
                if ($parent.children().size() !== 1) {
                    // 入れ替える要素が1つでない場合、divで囲む
                    var $tmp_node = $('div').html($parent.html());
                    $parent.html($tmp_node);
                }

                var $new_node = $(html).css(direction, $parent.width()).hide();
                var $old_node = $parent.children().first();
                $old_node.css({left: '', right: ''});

                var property = [];
                property[direction] = -$parent.width();

                $old_node.animate(property, 250, 'swing', function() {
                    $parent.append($new_node);

                    $old_node.slideUp(100, function() {
                        $(this).remove();
                    });

                    property[direction] = 0;

                    $new_node.slideDown(100);
                    $new_node.animate(property, 250, 'swing', callback);
                });
            },
        };
    }();
}

$(document).ready(function() {
    $(document).on('click', '.jsCallAuthForm', function() {
        AuthModalService.callTemplate(/* template_id: AuthForm */ 1, /* callback: function(html) */  function(html) {
            AuthModalService.slideIn(/* parent_class */ '.jsAuthModalSliderScreen', /* direction */ 'right', /* new_node */ html);
        });

        return false;
    });

    $(document).on('click', '.jsCallMailAuthFormWrap', function() {
        AuthModalService.callTemplate(/* template_id: MailAuthForm */ 2, /* callback: function(html) */ function(html) {
            AuthModalService.slideIn(/* parent_class */ '.jsAuthModalSliderScreen', /* direction */ 'left', /* new_node */ html);
        });

        return false;
    });

    $(document).on('click', '.jsOpenAuthModal', function() {
        AuthModalService.callTemplate(/* template_id: AuthForm */ 1, /* callback: function(html) */  function(html) {
            $('.jsAuthModalSliderScreen').html(html);

            Brandco.unit.openModal('#modalAuth');
        });

        return false;
    });

    $(document).on('click', '.jsCloseAuthModal', function() {
        Brandco.unit.closeModal('Auth');

        return false;
    });
});