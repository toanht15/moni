if (typeof(CommentPluginService) === 'undefined') {
    var $moniJq = $moniJq || jQuery.noConflict(true);
    var CommentPluginService = (function () {
        var EmbedPlugin = function () {
            var pluginId, pluginOrigin;

            this.getPluginId = function () { return pluginId };
            this.setPluginId = function (plugin_id) {
                pluginId = plugin_id;
            };

            this.getPluginOrigin = function () { return pluginOrigin };
            this.setPluginOrigin = function (plugin_origin) {
                pluginOrigin = plugin_origin;
            };
        };
        EmbedPlugin.prototype = {
            initEmbedPlugin: function (pluginId, pluginOrigin) {
                this.setPluginId(pluginId);
                this.setPluginOrigin(pluginOrigin);
            }
        };

        return {
            embedPlugin: new EmbedPlugin(),
            user_info: null,
            init_flg: true,
            comments: [],
            initPlugin: function (data) {
                if (data.pluginId && data.pluginId.length !== 0) {
                    CommentPluginService.embedPlugin.initEmbedPlugin(data.pluginId, data.origin)
                    CommentPluginService.loadComments();
                }
            },
            fetchRequestUrl: function () {
                if (CommentPluginUtil.isFramed()) {
                    return CommentPluginService.embedPlugin.getPluginOrigin();
                }

                return location.href;
            },
            openAuthPopup: function () {
                var loginUrl = document.getElementsByName('loading_url')[0].value;
                return Brandco.unit.windowOpenWrap(loginUrl, '新モニ', '565', '600');
            },
            redirectAuthPage: function (redirect_url) {
                if (CommentPluginUtil.isFramed()) {
                    window.top.location.href = redirect_url;
                } else {
                    location.href = redirect_url;
                }
            },
            reloadBrowser: function (anchor_hash) {
                if (CommentPluginUtil.isFramed()) {
                    sessionStorage.setItem('pluginId', CommentPluginService.embedPlugin.getPluginId());
                    sessionStorage.setItem('pluginOrigin', CommentPluginService.embedPlugin.getPluginOrigin());
                }
                location.hash = anchor_hash;
                location.reload(true);
            },
            loadComments: function () {
                var csrf_token = document.getElementsByName("csrf_token")[0].value,
                    comment_plugin_id = document.getElementsByName("comment_plugin_id")[0].value,
                    cmt_container = $moniJq('.jsCommentContainer'),
                    load_more_cmt = $moniJq(cmt_container).find('.jsLoadMoreCmt'),
                    anchor_cur_id = CommentPluginUtil.fetchLocationHash(),
                    prev_min_id = CommentPluginService.comments.length ? CommentPluginService.comments[CommentPluginService.comments.length - 1]['id'] : 0,
                    params = {
                        data: {
                            csrf_token: csrf_token,
                            prev_min_id: prev_min_id,
                            anchor_cur_id: anchor_cur_id,
                            comment_plugin_id: comment_plugin_id
                        },
                        url: 'plugin/comments.json',
                        type: 'GET',
                        beforeSend: function () {
                            if (typeof load_more_cmt !== 'undefined') {
                                $moniJq(load_more_cmt).remove();
                            }
                            CommentPluginUtil.showLoading(cmt_container);
                        },
                        success: function (response) {
                            if (response.result == 'ok') {
                                CommentPluginService.user_info = response.data.user;

                                CommentPluginService.updateCommentData(response.data.comments);

                                if (CommentPluginService.init_flg === true) { // update first time only
                                    CommentPluginUtil.updateCounter($moniJq('.jsCommentCounter'), response.data.comment_count, 500);

                                    var cu_input_block = CommentPluginService.bindCommentInputTemplate();
                                    $moniJq(cmt_container).append(cu_input_block);
                                }

                                $moniJq.each(response.data.comments, function (index, value) {
                                    var cu_container = CommentPluginService.bindCommentTemplate(value);
                                    $moniJq(cmt_container).append(cu_container);
                                });

                                if (response.data.load_more_flg) {
                                    $moniJq(cmt_container).append($moniJq('#cmt_load_more_template').html());
                                }
                            }
                        },
                        complete: function () {
                            CommentPluginUtil.hideLoading();
                            var location_hash = CommentPluginUtil.fetchLocationHash();
                            if (location_hash !== "" && CommentPluginService.init_flg === true) {
                                CommentPluginService.autoScroll($moniJq(document.location.hash));
                            }

                            CommentPluginService.init_flg = false;
                            CommentPluginService.sendIframeSize();
                        }
                    };
                Brandco.api.callAjaxWithParam(params, false, false);
            },
            loadReplies: function (target) {
                var load_more_container = $moniJq(target).closest('.jsLoadMoreReplyContainer'),
                    csrf_token = document.getElementsByName("csrf_token")[0].value,
                    cu_index = $moniJq('.jsCUContainer').index($moniJq(target).closest('.jsCUContainer')),
                    cu_relation_id = CommentPluginService.comments[cu_index]['id'],
                    anchor_cur_id = CommentPluginUtil.fetchLocationHash(),
                    prev_min_id = CommentPluginService.comments[cu_index]['replies'].length ? CommentPluginService.comments[cu_index]['replies'][0]['id'] : 0,
                    params = {
                        data: {
                            csrf_token: csrf_token,
                            prev_min_id: prev_min_id,
                            anchor_cur_id: anchor_cur_id,
                            cu_relation_id: cu_relation_id
                        },
                        url: 'plugin/replies.json',
                        type: 'GET',
                        beforeSend: function () {
                            $moniJq(load_more_container).hide();
                            CommentPluginUtil.showLoading(load_more_container);
                        },
                        success: function (response) {
                            if (response.result == 'ok') {
                                if (typeof response.data.remaining_reply_count !== 'undefined' && response.data.remaining_reply_count != 0) {
                                    $moniJq(load_more_container).find('.jsRemainingReplyCounter').html(response.data.remaining_reply_count);
                                    $moniJq(load_more_container).show();
                                }

                                if (typeof response.data.replies !== 'undefined') {
                                    $moniJq.each(response.data.replies, function (index, value) {
                                        var reply_data = [];
                                        reply_data['id'] = value.id;

                                        CommentPluginService.comments[cu_index]['replies'].unshift(reply_data);

                                        var cur_content = CommentPluginService.bindCommentContent(value);
                                        $moniJq(cur_content).insertAfter($moniJq(load_more_container));
                                    });
                                }
                            }
                        },
                        complete: function () {
                            CommentPluginUtil.hideLoading();
                            CommentPluginService.sendIframeSize();
                        }
                    };
                Brandco.api.callAjaxWithParam(params, false, false);
            },
            bindCommentInputTemplate: function () {
                var cu_input_block = $moniJq('<div/>').html($moniJq('#cu_input_template').html()),
                    user_img = $moniJq(cu_input_block).find('.jsUserImage img');

                $moniJq(user_img).attr('src', CommentPluginService.user_info.profile_img_url);
                $moniJq(user_img).attr('alt', CommentPluginService.user_info.name);

                if (CommentPluginService.user_info.is_login) {
                    $moniJq(cu_input_block).find('.jsUserNameInput').attr('value', CommentPluginService.user_info.name);
                } else {
                    $moniJq(cu_input_block).find('.jsShareAction').remove();
                }

                if (CommentPluginService.user_info.share_sns_list.length) {
                    $moniJq.each(CommentPluginService.user_info.share_sns_list, function (index, value) {
                        $moniJq(cu_input_block).find('.jsShare' + value).css('display', 'inline-block');
                    });
                } else {
                    $moniJq(cu_input_block).find('.jsShareAction').remove();
                }

                return $moniJq(cu_input_block).html();
            },
            bindReplyInputTemplate: function (mention, object_id) {
                var cur_input_block = $moniJq('<div/>').html($moniJq('#cur_input_template').html()),
                    user_img = $moniJq(cur_input_block).find('.jsUserImage img');

                $moniJq(cur_input_block).find('.jsCURInputBlock').attr('data-object_id', object_id);
                $moniJq(user_img).attr('src', CommentPluginService.user_info.profile_img_url);
                $moniJq(user_img).attr('alt', CommentPluginService.user_info.name);

                if (!CommentPluginService.user_info.is_login) {
                    $moniJq(cur_input_block).find('.jsShareAction').remove();
                }

                if (CommentPluginService.user_info.share_sns_list.length) {
                    $moniJq.each(CommentPluginService.user_info.share_sns_list, function (index, value) {
                        $moniJq(cur_input_block).find('.jsShare' + value).css('display', 'inline-block');
                    });
                } else {
                    $moniJq(cur_input_block).find('.jsShareAction').remove();
                }

                if (mention != "") {
                    $moniJq(cur_input_block).find('.jsCommentText').removeClass('empty');
                    $moniJq(cur_input_block).find('.jsCommentText').html(mention);
                }

                return $moniJq(cur_input_block).html();
            },
            bindCommentTemplate: function (comment_data) {
                var cu_container = $moniJq('<div/>').html($moniJq('#cu_container_template').html()),
                    cur_container = $moniJq(cu_container).find('.jsCURContainer'),
                    cmt_content = CommentPluginService.bindCommentContent(comment_data);

                $moniJq(cu_container).find('.jsCUContainer').prepend(cmt_content);

                if (typeof comment_data.replies !== 'undefined') {
                    $moniJq.each(comment_data.replies, function (index, value) {
                        var cur_content = CommentPluginService.bindCommentContent(value);
                        $moniJq(cur_container).prepend(cur_content);
                    });
                }

                // Add load more btn
                if (typeof comment_data.remaining_reply_count !== 'undefined' && comment_data.remaining_reply_count != 0) {
                    var load_more_template = $moniJq('<div/>').html($moniJq('#reply_load_more_template').html());

                    $moniJq(load_more_template).find('.jsRemainingReplyCounter').html(comment_data.remaining_reply_count);
                    $moniJq(cur_container).prepend($moniJq(load_more_template).html());
                }

                if (comment_data.is_hidden) {
                    $moniJq(cur_container).hide();
                }

                return $moniJq(cu_container).html();
            },
            bindCommentContent: function (comment_data) {
                if (comment_data.is_hidden) {
                    return $moniJq('#hide_cu_content_template').html();
                }

                var cmt_content = $moniJq('<div/>');
                $moniJq(cmt_content).html($moniJq('#cu_content_container_template').html());

                var like_class = comment_data.likes.is_liked ? "innerOn" : "innerOff",
                    user_img = $moniJq(cmt_content).find('.jsUserImage img');

                $moniJq(cmt_content).find('.jsContentContainer').attr('id', 'cur_id_' + comment_data.id);

                $moniJq(user_img).attr('src', comment_data.from.profile_img_url);
                $moniJq(user_img).attr('alt', comment_data.from.name);

                $moniJq(cmt_content).find('.jsCommentText').attr('data-original_text', comment_data.original_text);
                $moniJq(cmt_content).find('.jsCommentText').html(comment_data.comment_text);
                $moniJq(cmt_content).find('.jsCommentText').removeClass("empty");

                $moniJq(cmt_content).find('.jsUserName').html(comment_data.from.name);
                $moniJq(cmt_content).find('.jsCreatedTime').html(comment_data.created_time);
                $moniJq(cmt_content).find('.jsLikeBtn').addClass(like_class);
                $moniJq(cmt_content).find('.jsCURLink').attr('data-mention', comment_data.from.name);

                if (comment_data.likes.like_count == 0) {
                    $moniJq(cmt_content).find('.jsLikeCount').hide();
                } else {
                    $moniJq(cmt_content).find('.jsLikeCount').html(comment_data.likes.like_count);
                }

                if (!comment_data.is_owner) {
                    $moniJq(cmt_content).find('.jsActions').remove();
                }

                if (!CommentPluginService.user_info.is_login) {
                    $moniJq(cmt_content).find('.jsLikeLink').remove();
                    $moniJq(cmt_content).find('.jsOtherActions').remove();
                    $moniJq(cmt_content).find('.jsEditActionContainer').remove();
                } else {
                    var other_action = $moniJq(cmt_content).find('.jsOtherActions');

                    $moniJq(cmt_content).find('.jsLikeSpan').remove();
                    $moniJq(other_action).find('.checkToggle').attr('for', 'postOption_' + comment_data.id);
                    $moniJq(other_action).find('.jsOtherActionCheckbox').attr('id', 'postOption_' + comment_data.id);
                    $moniJq(cmt_content).find('.jsEditActionContainer').hide();
                }

                return $moniJq(cmt_content).html();
            },
            comment: function (target) {
                $moniJq(document).off('click', '.jsCommentSubmitBtn');

                var cu_input_block = $moniJq(target).closest('.jsCUInputBlock'),
                    cu_input_field = $moniJq(cu_input_block).find('.jsCommentText'),
                    social_media_ids = $moniJq(cu_input_block).find('input[name="social_media_ids"]:checked').map(function () {
                        if ($moniJq(this).is(':visible')) return $moniJq(this).val();
                    }).get(),
                    nickname = $moniJq(cu_input_block).find('.jsUserNameInput').val(),
                    csrf_token = document.getElementsByName("csrf_token")[0].value,
                    comment_plugin_id = document.getElementsByName("comment_plugin_id")[0].value,
                    params = {
                        data: {
                            nickname: nickname,
                            social_media_ids: social_media_ids,
                            comment_text: CommentPluginUtil.parseText(cu_input_field),
                            csrf_token: csrf_token,
                            comment_plugin_id: comment_plugin_id,
                            request_url: CommentPluginService.fetchRequestUrl()
                        },
                        url: 'plugin/comment.json',
                        type: 'POST',
                        beforeSend: function () {
                            CommentPluginUtil.showLoading(cu_input_block);
                        },
                        success: function (response) {
                            if (response.result == 'ok') {
                                CommentPluginService.user_info.name = response.data.nickname;

                                var cu_container = CommentPluginService.bindCommentTemplate(response.data.comment),
                                    temp_container = $moniJq('<div/>').html(cu_container),
                                    hidden_container = document.createElement('div');
                                temp_container.find('.jsContentContainer').addClass('current');

                                hidden_container.className = "commentPostWrap jsCUContainer";
                                hidden_container.innerHTML = $moniJq(temp_container).find('.jsCUContainer').html();
                                hidden_container.style.opacity = 0;

                                $moniJq(cu_input_block).after(hidden_container);
                                $moniJq(hidden_container).animate({
                                    opacity: 1
                                }, 100);
                                setTimeout(function () {
                                    $moniJq(hidden_container).find('.jsContentContainer').removeClass('current');
                                }, 3000);

                                CommentPluginService.addNewComment(response.data.comment);
                                CommentPluginService.resetInputBlock(cu_input_block);

                                var comment_counter = $moniJq('.jsCommentCounter'),
                                    to_value = parseInt($moniJq(comment_counter).html()) + 1;

                                CommentPluginUtil.updateCounter($moniJq(comment_counter), to_value, 100);
                                CommentPluginService.updateUserName();
                            } else if (response.result == 'ng') {

                                if (typeof response.errors.auth_error !== 'undefined') {
                                    if (response.data.device == 'pc') {
                                        if (typeof auth_window !== 'undefined') {
                                            auth_window.location.href = response.data.redirect_url;
                                        } else {
                                            var undefined_msg = (function () {
                                                return;
                                            })();
                                            CommentPluginService.resetErrorMsg();
                                            CommentPluginService.showErrorMsg(undefined_msg, cu_input_field);
                                        }
                                    } else {
                                        CommentPluginService.redirectAuthPage(response.data.redirect_url);
                                    }
                                } else if (typeof response.errors.nickname !== 'undefined') {
                                    CommentPluginService.showErrorMsg(response.errors.nickname, $moniJq(cu_input_block).find('.jsUserNameInput').parent('p'));
                                } else {
                                    CommentPluginService.showErrorMsg(response.errors.comment_text, cu_input_field);
                                    if (typeof auth_window !== 'undefined') { auth_window.close(); }
                                }
                            }
                            $moniJq(document).on('click', '.jsCommentSubmitBtn', function () { CommentPluginService.comment(this); });
                        },
                        error: function () {
                            $moniJq(document).on('click', '.jsCommentSubmitBtn', function () { CommentPluginService.comment(this); });
                            CommentPluginService.showErrorMsg('投稿時にエラーが発生しました。 ブラウザを変更して再度お試しください。', cu_input_field);
                        },
                        complete: function () {
                            CommentPluginUtil.hideLoading();
                            CommentPluginService.sendIframeSize();
                        }
                    };

                CommentPluginService.hideCommentEditBlock($moniJq('.jsContentContainer.jsEditing'));
                CommentPluginService.hideCommentReplyInputBlock();
                CommentPluginService.resetErrorMsg();
                CommentPluginService.sendIframeSize();

                var device = document.getElementsByName("device")[0].value;
                if (device != 'sp' && !CommentPluginService.user_info.is_login) {
                    if (!CommentPluginUtil.validateText(CommentPluginUtil.parseText(cu_input_field))) {
                        CommentPluginService.showErrorMsg('必ず入力してください', cu_input_field);
                        CommentPluginService.sendIframeSize();
                        $moniJq(document).on('click', '.jsCommentSubmitBtn', function () { CommentPluginService.comment(this); });
                        return;
                    }

                    var auth_window = CommentPluginService.openAuthPopup();
                }

                CommentPluginUtil.callAjaxWithParam(params);
            },
            reply: function (target) {
                $moniJq(document).off('click', '.jsCURSubmit');

                var cur_input_block = $moniJq(target).closest('.jsCURInputBlock'),
                    cur_input_field = $moniJq(cur_input_block).find('.jsCommentText'),
                    csrf_token = document.getElementsByName("csrf_token")[0].value,
                    cu_index = $moniJq('.jsCUContainer').index($moniJq(target).closest('.jsCUContainer')),
                    mentioned_object_id = $moniJq(cur_input_block).attr('data-object_id'),
                    cu_relation_id = CommentPluginService.comments[cu_index]['id'],
                    social_media_ids = $moniJq(cur_input_block).find('input[name="social_media_ids"]:checked').map(function () {
                        if ($moniJq(this).is(':visible')) return $moniJq(this).val();
                    }).get(),
                    params = {
                        data: {
                            social_media_ids: social_media_ids,
                            comment_text: CommentPluginUtil.parseText(cur_input_field),
                            csrf_token: csrf_token,
                            cu_relation_id: cu_relation_id,
                            mentioned_object_id: mentioned_object_id,
                            request_url: CommentPluginService.fetchRequestUrl()
                        },
                        url: 'plugin/reply.json',
                        type: 'POST',
                        beforeSend: function () {
                            CommentPluginUtil.showLoading(cur_input_block, true);
                        },
                        success: function (response) {
                            if (response.result == 'ok') {
                                var cur_content = CommentPluginService.bindCommentContent(response.data.reply),
                                    temp_container = $moniJq('<div/>').html(cur_content),
                                    hidden_container = document.createElement('div');

                                hidden_container.className = "commentPost jsContentContainer current";
                                hidden_container.innerHTML = $moniJq(temp_container).find('.jsContentContainer').html();
                                hidden_container.style.opacity = 0;

                                $moniJq(cur_input_block).before(hidden_container);
                                $moniJq(hidden_container).animate({
                                    opacity: 1
                                }, 100);
                                setTimeout(function () {
                                    $moniJq(hidden_container).removeClass('current');
                                }, 3000);

                                CommentPluginService.addNewReply(cu_index, response.data.reply);
                                CommentPluginService.resetInputBlock(cur_input_block);
                            } else if (response.result == 'ng') {
                                if (typeof response.errors.auth_error !== 'undefined') {
                                    if (response.data.device == 'pc') {
                                        if (typeof auth_window !== 'undefined') {
                                            auth_window.location.href = response.data.redirect_url;
                                        } else {
                                            var undefined_msg = (function () {
                                                return;
                                            })();
                                            CommentPluginService.resetErrorMsg();
                                            CommentPluginService.showErrorMsg(undefined_msg, cur_input_field);
                                        }
                                    } else {
                                        CommentPluginService.redirectAuthPage(response.data.redirect_url);
                                    }
                                } else {
                                    CommentPluginService.showErrorMsg(response.errors.comment_text, cur_input_field);
                                    if (typeof auth_window !== 'undefined') { auth_window.close(); }
                                }
                            }
                            $moniJq(document).on('click', '.jsCURSubmit', function () { CommentPluginService.reply(this); });
                        },
                        error: function () {
                            $moniJq(document).on('click', '.jsCURSubmit', function () { CommentPluginService.reply(this); });
                            CommentPluginService.showErrorMsg('投稿時にエラーが発生しました。 ブラウザを変更して再度お試しください。', cur_input_field);
                        },
                        complete: function () {
                            CommentPluginUtil.hideLoading();
                            CommentPluginService.sendIframeSize();
                        }
                    };
                CommentPluginService.resetErrorMsg();

                var device = document.getElementsByName("device")[0].value;
                if (device != 'sp' && !CommentPluginService.user_info.is_login) {
                    if (!CommentPluginUtil.validateText(CommentPluginUtil.parseText(cur_input_field))) {
                        CommentPluginService.showErrorMsg('必ず入力してください', cur_input_field);
                        CommentPluginService.sendIframeSize();
                        $moniJq(document).on('click', '.jsCURSubmit', function () { CommentPluginService.reply(this); });
                        return;
                    }

                    var auth_window = CommentPluginService.openAuthPopup();
                }

                CommentPluginUtil.callAjaxWithParam(params);
            },
            getObjectData: function (target) {
                var content_container = $moniJq(target).closest('.jsContentContainer'),
                    parent = $moniJq(content_container).parent(),
                    is_reply = $moniJq(parent).hasClass('jsCURContainer'),
                    cu_index = $moniJq('.jsCUContainer').index($moniJq(target).closest('.jsCUContainer')),
                    object_id = null;

                if (is_reply) {
                    var cur_index = $moniJq($moniJq(parent).find('.jsContentContainer')).index($moniJq(content_container));
                    object_id = CommentPluginService.comments[cu_index]['replies'][cur_index]['id'];
                } else {
                    object_id = CommentPluginService.comments[cu_index]['id'];
                }

                return [object_id, cu_index, cur_index];
            },
            like: function (target) {
                if (!CommentPluginService.user_info.is_login) {
                    return;
                }

                CommentPluginService.updateLikeCount(target);
                CommentPluginService.toggleLikeStatus(target);

                var cu_relation_id = CommentPluginService.getObjectData(target)[0],
                    csrf_token = document.getElementsByName("csrf_token")[0].value,
                    params = {
                        data: {
                            csrf_token: csrf_token,
                            cu_relation_id: cu_relation_id
                        },
                        url: 'plugin/like.json',
                        type: 'POST',
                        success: function (response) {
                            if (response.result == 'ok') {
                            }
                        }
                    };
                Brandco.api.callAjaxWithParam(params, false, false);
            },
            remove: function (target) {
                $moniJq(document).off('click', '.jsRemoveLink');

                var content_container = $moniJq(target).closest('.jsContentContainer'),
                    csrf_token = document.getElementsByName("csrf_token")[0].value,
                    is_comment = $moniJq(content_container).parent().hasClass('jsCUContainer'),
                    object_data = CommentPluginService.getObjectData(target),
                    params = {
                        data: {
                            csrf_token: csrf_token,
                            cu_relation_id: object_data[0]
                        },
                        url: 'plugin/remove.json',
                        type: 'POST',
                        success: function (response) {
                            if (response.result == 'ok') {

                                var hideText = document.createElement('div');
                                hideText.className = 'commentNotdisplay';
                                hideText.innerHTML = '<p class="innerText">この投稿は削除されました。</p>';
                                hideText.style.opacity = 0;

                                var removal_container = null;
                                if (is_comment) {
                                    CommentPluginService.comments.splice(object_data[1], 1);
                                    removal_container = $moniJq(content_container).parent();

                                    var comment_counter = $moniJq('.jsCommentCounter'),
                                        to_value = parseInt($moniJq(comment_counter).html()) - 1;

                                    CommentPluginUtil.updateCounter($moniJq(comment_counter), to_value, 100);
                                } else {
                                    CommentPluginService.comments[object_data[1]]['replies'].splice(object_data[2], 1);
                                    removal_container = $moniJq(content_container);
                                }

                                $moniJq(removal_container).fadeOut({
                                    duration: 100,
                                    queue: false,
                                    complete: function () {
                                        $moniJq(removal_container).replaceWith(hideText);
                                        $moniJq(hideText).animate({
                                            opacity: 1
                                        }, 100, function () {
                                            CommentPluginService.sendIframeSize();
                                        });
                                    }
                                });
                            }

                            $moniJq(document).on('click', '.jsRemoveLink', function () {
                                if (confirm('このコメントを削除してもいいですか？')) {
                                    CommentPluginService.remove(this);
                                }
                            });
                        }
                    };

                $moniJq('#scrollTarget').insertAfter($moniJq('#pinAction'));
                CommentPluginUtil.callAjaxWithParam(params);
            },
            edit: function (target) {
                $moniJq(document).off('click', '.jsSaveEditLink');

                var content_container = $moniJq(target).closest('.jsContentContainer'),
                    csrf_token = document.getElementsByName("csrf_token")[0].value,
                    comment_text_block = $moniJq(content_container).find('.jsCommentText'),
                    object_data = CommentPluginService.getObjectData(target),
                    params = {
                        data: {
                            comment_text: CommentPluginUtil.parseText(comment_text_block),
                            csrf_token: csrf_token,
                            cu_relation_id: object_data[0]
                        },
                        url: 'plugin/edit.json',
                        type: 'POST',
                        success: function (response) {
                            if (response.result == 'ok') {
                                CommentPluginService.hideCommentEditBlock(target, response.data.comment_text);
                            } else if (response.result == 'ng') {
                                CommentPluginService.showErrorMsg(response.errors.comment_text, comment_text_block);
                            }
                            $moniJq(document).on('click', '.jsSaveEditLink', function () { CommentPluginService.edit(this); });
                        },
                        complete: function () {
                            CommentPluginService.sendIframeSize();
                        }
                    };
                CommentPluginService.resetErrorMsg();
                CommentPluginUtil.callAjaxWithParam(params);
            },
            hide: function (target) {
                $moniJq(document).off('click', '.jsHideLink');

                var content_container = $moniJq(target).closest('.jsContentContainer'),
                    csrf_token = document.getElementsByName("csrf_token")[0].value,
                    object_data = CommentPluginService.getObjectData(target),
                    is_comment = $moniJq(content_container).parent().hasClass('jsCUContainer'),
                    params = {
                        data: {
                            csrf_token: csrf_token,
                            cu_relation_id: object_data[0]
                        },
                        url: 'plugin/hide.json',
                        type: 'POST',
                        success: function (response) {
                            if (response.result == 'ok') {
                                var hideText = document.createElement('div');
                                hideText.className = 'commentNotdisplay jsContentContainer';
                                hideText.innerHTML = '<p class="innerText">この投稿は非表示になりました。<a href="javascript:void(0);" class="jsShowLink">元に戻す</a></p>';
                                hideText.style.opacity = 0;

                                $moniJq(content_container).fadeOut({
                                    duration: 100,
                                    queue: false,
                                    complete: function () {
                                        $moniJq(content_container).replaceWith(hideText);
                                        $moniJq(hideText).animate({
                                            opacity: 1
                                        }, 100, function () {
                                            CommentPluginService.sendIframeSize();
                                        });
                                    }
                                });
                                if (is_comment) {
                                    $moniJq(content_container).parent('.jsCUContainer').find('.jsCURContainer').fadeOut({
                                        duration: 100,
                                        queue: false
                                    });
                                }
                            }

                            $moniJq(document).on('click', '.jsHideLink', function () { CommentPluginService.hide(this); });
                        }
                    };
                CommentPluginUtil.callAjaxWithParam(params);
            },
            show: function (target) {
                $moniJq(document).off('click', '.jsShowLink');

                var content_container = $moniJq(target).closest('.jsContentContainer'),
                    csrf_token = document.getElementsByName("csrf_token")[0].value,
                    object_data = CommentPluginService.getObjectData(target),
                    is_comment = $moniJq(content_container).parent().hasClass('jsCUContainer'),
                    params = {
                        data: {
                            csrf_token: csrf_token,
                            cu_relation_id: object_data[0]
                        },
                        url: 'plugin/show.json',
                        type: 'POST',
                        success: function (response) {
                            if (response.result == 'ok') {
                                var cur_container = $moniJq(content_container).parent().find('.jsCURContainer'),
                                    cu_content = CommentPluginService.bindCommentContent(response.data.comment),
                                    temp_container = $moniJq('<div/>').html(cu_content),
                                    hidden_container = document.createElement('div');

                                hidden_container.className = "commentPost jsContentContainer";
                                hidden_container.innerHTML = $moniJq(temp_container).find('.jsContentContainer').html();
                                hidden_container.style.opacity = 0;

                                $moniJq(content_container).fadeOut({
                                    duration: 100,
                                    complete: function () {
                                        $moniJq(content_container).replaceWith(hidden_container);
                                        $moniJq(hidden_container).animate({
                                            opacity: 1
                                        }, 100, function () {
                                            CommentPluginService.sendIframeSize();
                                        });
                                    }
                                });

                                if (is_comment) {
                                    $moniJq(cur_container).show().css('opacity', 0);
                                    $moniJq(cur_container).animate({
                                            opacity: 1
                                        }, {
                                            queue: false,
                                            duration: 100
                                        }
                                    );
                                }
                            }

                            $moniJq(document).on('click', '.jsShowLink', function () { CommentPluginService.show(this); });
                        }
                    };
                CommentPluginUtil.callAjaxWithParam(params);
            },
            showMore: function (target) {
                $moniJq(target).closest('.jsCommentText').find('.exposed_text_show').fadeIn(100);
                $moniJq(target).remove();
            },
            toggleLikeStatus: function (target) {
                $moniJq(target).find('.jsLikeBtn').toggleClass('innerOn innerOff');
            },
            updateLikeCount: function (target) {
                var like_container = $moniJq(target).closest('.jsLikeContainer'),
                    like_counter = $moniJq(like_container).find('.jsLikeCount'),
                    is_liked = $moniJq(like_container).find('.jsLikeBtn').hasClass('innerOn'),
                    cur_count = $moniJq(like_counter).is(':visible') ? parseInt($moniJq(like_counter).html()) : 0,
                    post_count = cur_count;

                if (is_liked) {
                    if (cur_count == 1) {
                        $moniJq(like_counter).hide();
                    }

                    post_count -= 1;
                } else {
                    if (cur_count == 0) {
                        $moniJq(like_counter).show();
                    }

                    post_count += 1;
                }

                CommentPluginUtil.updateCounter(like_counter, post_count);
            },
            updateUserName: function () {
                var content_container = $moniJq('.jsActions').closest('.jsContentContainer');

                $moniJq(content_container).find('.jsUserName').html(CommentPluginService.user_info.name);
                $moniJq(content_container).find('.jsCURLink').attr('data-mention', CommentPluginService.user_info.name);
            },
            showCommentInputBlock: function (target) {
                var cu_container = $moniJq(target).closest('.jsCUContainer'),
                    cur_container = $moniJq(cu_container).find('.jsCURContainer'),
                    cu_relation_id = CommentPluginService.getObjectData(target)[0],
                    cur_input_block = $moniJq(cur_container).find('.jsCURInputBlock'),
                    mention = "";

                CommentPluginService.hideCommentEditBlock($moniJq('.jsContentContainer.jsEditing'));

                if ($moniJq(target).attr('data-mention')) {
                    mention = '<p><span class="mention" contenteditable="false">' + $moniJq(target).attr('data-mention') + '</span><br></p>';
                }

                CommentPluginService.autoScroll($moniJq(cu_container).find('.jsContentContainer:last'));

                if ($moniJq(cur_input_block).length) {
                    $moniJq(cur_input_block).find('.jsCommentText').html(mention);
                    if (mention != "") {
                        $moniJq(cur_input_block).find('.jsCommentText').removeClass('empty');
                    }
                    return;
                }

                var cur_input_template = CommentPluginService.bindReplyInputTemplate(mention, cu_relation_id);
                $moniJq(cur_container).append(cur_input_template);
            },
            showCommentEditBlock: function (target) {
                var content_container = $moniJq(target).closest('.jsContentContainer'),
                    comment_text_block = $moniJq(content_container).find('.jsCommentText');

                CommentPluginService.hideCommentReplyInputBlock();
                CommentPluginService.hideCommentEditBlock($moniJq('.jsContentContainer.jsEditing'));

                $moniJq(content_container).addClass('jsEditing');

                $moniJq(comment_text_block).attr('contenteditable', true);
                $moniJq(comment_text_block).html($moniJq(comment_text_block).attr('data-original_text'));
                $moniJq(content_container).find('.jsDefaultActionContainer').hide();
                $moniJq(content_container).find('.jsEditActionContainer').show();

                CommentPluginService.autoScroll($moniJq(content_container));
            },
            hideCommentReplyInputBlock: function () {
                $moniJq('.jsCURInputBlock').remove();
            },
            hideCommentEditBlock: function (target, comment_text) {
                var content_container = null;
                if ($moniJq(target).hasClass('jsContentContainer')) {
                    content_container = target;
                } else {
                    content_container = $moniJq(target).closest('.jsContentContainer');
                }

                var comment_text_block = $moniJq(content_container).find('.jsCommentText');

                if (typeof comment_text === 'undefined') {
                    comment_text = $moniJq(comment_text_block).attr('data-original_text')
                }

                $moniJq(content_container).removeClass('jsEditing');
                $moniJq(comment_text_block).html(comment_text)
                    .attr('data-original_text', comment_text)
                    .attr('contenteditable', false)
                    .removeClass('empty');
                $moniJq(content_container).find('.jsDefaultActionContainer').show();
                $moniJq(content_container).find('.jsEditActionContainer').hide();

                CommentPluginService.resetErrorMsg();
            },
            resetInputBlock: function (target) {
                $moniJq(target).find('.jsCommentText').html('<p><br></p>');
                $moniJq(target).find('.jsCommentText').addClass('empty');
                $moniJq(target).find('input[name="social_media_ids"]').prop('checked', true);

                var object_id = $(target).attr('data-object_id');
                if (typeof object_id !== 'undefined' && object_id !== false) {
                    $moniJq(target).removeAttr('data-object_id');
                }
            },
            resetErrorMsg: function () {
                if ($moniJq('.jsInputErrorMsg').length) {
                    $moniJq('.jsInputErrorMsg').remove();
                }
            },
            showErrorMsg: function (err_msg, target) {
                if (typeof err_msg === 'undefined') {
                    err_msg = '投稿する際にエラーが発生しました。時間をおいて再度お試しください';
                }

                var error_msg_template = '<span class="iconError1 jsInputErrorMsg">' + err_msg + '</span>';
                $moniJq(error_msg_template).insertAfter($moniJq(target));
            },
            updateCommentData: function (comments) {
                $moniJq.each(comments, function (index, value) {
                    var comment_data = [];
                    comment_data['id'] = value.id;
                    comment_data['replies'] = [];

                    if (typeof value.replies !== 'undefined') {
                        $moniJq.each(value.replies, function (sub_index, sub_value) {
                            var reply_data = [];
                            reply_data['id'] = sub_value.id;

                            comment_data['replies'].unshift(reply_data);
                        });
                    }

                    CommentPluginService.comments.push(comment_data);
                });
            },
            addNewComment: function (comment) {
                var new_cmt = [];
                new_cmt['id'] = comment.id;
                new_cmt['replies'] = [];

                CommentPluginService.comments.unshift(new_cmt);
            },
            addNewReply: function (comment_index, reply) {
                var new_reply = [];
                new_reply['id'] = reply.id;

                CommentPluginService.comments[comment_index]['replies'].push(new_reply);
            },
            createMessage: function (action_type, message_content) {
                return {
                    pluginId: CommentPluginService.embedPlugin.getPluginId(),
                    actionType: action_type,
                    messageContent: message_content
                };
            },
            sendIframeSize: function () {
                if (!CommentPluginUtil.isFramed()) { return; }

                var iframeHeight = CommentPluginUtil.getDocHeight(),
                    message = CommentPluginService.createMessage(1, iframeHeight);
                parent.postMessage(message, CommentPluginService.embedPlugin.getPluginOrigin());
            },
            sendScrollPosition: function (position) {
                if (!CommentPluginUtil.isFramed()) { return; }

                var message = CommentPluginService.createMessage(2, position);
                parent.postMessage(message, CommentPluginService.embedPlugin.getPluginOrigin());
            },
            autoScroll: function (target) {
                if ($moniJq(target).length === 0) { return; }
                $moniJq('#scrollTarget').insertBefore($moniJq(target));

                var spAccountHeader = $moniJq('input[name="isSP"]:first').val() ? $moniJq('section.account').height() : 0,
                    position = $moniJq('#scrollTarget').offset().top - spAccountHeader,
                    speed = 1000;

                if (CommentPluginUtil.isFramed()) {
                    CommentPluginService.sendScrollPosition(position);
                } else if (position > 0) {
                    $moniJq('body,html').animate({ scrollTop: position }, speed, 'swing');
                }
            }
        };
    })();

    $moniJq(document).ready(function () {
        if (!CommentPluginUtil.isFramed()) {
            CommentPluginService.loadComments();
        } else if (sessionStorage.getItem('pluginId')) {
            CommentPluginService.embedPlugin.initEmbedPlugin(sessionStorage.getItem('pluginId'), sessionStorage.getItem('pluginOrigin'));
            sessionStorage.removeItem('pluginId');
            sessionStorage.removeItem('pluginOrigin');
            CommentPluginService.loadComments();
        }
        CommentPluginUtil.handleEvent('message', function (event) {
            CommentPluginService.initPlugin(event.data);
        });

        /************************************************
         * Comment Action
         ************************************************/
        $moniJq(document).on('click', '.jsCommentSubmitBtn', function () {
            CommentPluginService.comment(this);
        });

        $moniJq(document).on('click', '.jsCURLink', function () {
            CommentPluginService.showCommentInputBlock(this);
            CommentPluginService.sendIframeSize();
        });

        $moniJq(document).on('click', '.jsCURSubmit', function () {
            CommentPluginService.reply(this);
        });

        $moniJq(document).on('click', '.jsLikeLink', function () {
            CommentPluginService.like(this);
        });

        $moniJq(document).on('click', '.jsRemoveLink', function () {
            if (confirm('このコメントを削除してもいいですか？')) {
                CommentPluginService.remove(this);
            }
        });

        $moniJq(document).on('click', '.jsHideLink', function () {
            CommentPluginService.hide(this);
        });

        $moniJq(document).on('click', '.jsShowLink', function () {
            CommentPluginService.show(this);
        });

        $moniJq(document).on('click', '.jsEditLink', function () {
            CommentPluginService.showCommentEditBlock(this);
            CommentPluginService.sendIframeSize();
        });

        $moniJq(document).on('click', '.jsCancelEditLink', function () {
            CommentPluginService.hideCommentEditBlock(this);
            CommentPluginService.sendIframeSize();
        });

        $moniJq(document).on('click', '.jsSaveEditLink', function () {
            CommentPluginService.edit(this);
        });

        $moniJq(document).on('click', '.jsLoadMoreLink', function () {
            CommentPluginService.loadComments();
        });

        $moniJq(document).on('click', '.jsLoadMoreReplyLink', function () {
            CommentPluginService.loadReplies(this);
        });

        $moniJq(document).on('click', '.jsSeeMore', function () {
            CommentPluginService.showMore(this);
            CommentPluginService.sendIframeSize();
        });

        $moniJq(document).on('click', '.jsOtherActionCheckbox', function () {
            if ($moniJq(this).is(':checked')) {
                $moniJq('.jsOtherActionCheckbox').not(this).prop('checked', false);
            }
        });

        /************************************************
         * Comment Text Input Action
         ************************************************/

        $moniJq(document).on('change keydown keypress input', '.postTextEdit', function (event) {
            var target = event.currentTarget;
            if (target.textContent.length <= 0) {
                target.className = 'postTextEdit jsCommentText empty';
            } else {
                target.className = 'postTextEdit jsCommentText';
            }
            CommentPluginService.sendIframeSize();
        });

        $moniJq(document).on('paste', '.postTextEdit', function (event) {
            event.preventDefault();
            var content = '';

            // Support cross browser (Firefox 22+, Chrome, Safari, Opera, IE) - Firefox <= 22, Edge untested
            if ((event.originalEvent || event).clipboardData) {
                content += (event.originalEvent || event).clipboardData.getData('text/plain');
            } else if (window.clipboardData) {
                content += window.clipboardData.getData('Text');
            }

            document.execCommand('insertText', false, content);
            CommentPluginService.sendIframeSize();
        });
    });
}
