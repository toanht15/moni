if (typeof(UserActionPopularVoteService) === 'undefined') {
    var UserActionPopularVoteService = (function() {
        var FILE_TYPE_IMAGE = 1;
        var FILE_TYPE_MOVIE = 2;

        var alreadyRead = [];

        return {
            alreadyRead: alreadyRead,

            executeAction: function (target) {
                // Remove the event to prevent duplicate submission.
                $('.jsExecutePopularVoteAction').off('click');

                var form = $(target).parents().filter('.executePopularVoteActionForm');
                var section = $(target).parents().filter('.jsMessage');
                var csrf_token = document.getElementsByName('csrf_token')[0].value;
                var url = form.attr('action');
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var cp_popular_vote_candidate_id = $('input[name=cp_popular_vote_candidate_id]:checked', form).val();
                var share_url = $('input[name=share_url]', form).val();
                var share_url_type = $('input[name=share_url_type]', form).val();
                var share_text = $('textarea[name=share_text]', form).val();
                var fb_share_flg = $('input[name=fb_share_flg]:checked', form).val();
                var tw_share_flg = $('input[name=tw_share_flg]:checked', form).val();

                var param = {
                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id,
                        cp_popular_vote_candidate_id: cp_popular_vote_candidate_id,
                        share_text: share_text,
                        share_url: share_url,
                        share_url_type: share_url_type,
                        fb_share_flg: fb_share_flg,
                        tw_share_flg: tw_share_flg
                    },
                    url: url,

                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },

                    success: function (json) {
                         try {
                             var parent = $(target).parents().filter('.jsMessage');
                             parent.find('.jsShareText').prev().remove();

                             if (json.result === 'ok') {
                                 if (json.data.next_action === true) {
                                     var message = $(json.html);
                                     message.hide();
                                     section.after(message);

                                     Brandco.helper.facebookParsing(json.data.sns_action);

                                     $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                         Brandco.unit.createAndJumpToAnchor();
                                     });
                                 }

                                 Brandco.unit.disableForm(parent.find('form'));

                                 UserActionPopularVoteService.setResult(target);
                                 $(target).replaceWith('<span class="large1">' + $(target).html() + '</span>');
                             } else {
                                 if (json.errors.share_text) {
                                     parent.find('.jsShareText').before($('<span></span>').addClass('iconError1').text(json.errors.share_text));
                                 } else if (json.errors.cp_popular_vote_candidate_id) {
                                     parent.find('.jsCpPopularVoteCandidateId').before($('<span></span>').addClass('iconError1').text(json.errors.cp_popular_vote_candidate_id));
                                 }
                             }
                         } finally {
                             // Add the event.
                             $('.jsExecutePopularVoteAction').on('click', function(event) {
                                 UserActionPopularVoteService.executeAction(this);

                                 return false;
                             });
                         }
                    },

                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };

                Brandco.api.callAjaxWithParam(param, false, false);
            },

            setResult: function(target) {
                var parent = $(target).parents().filter('.jsMessage');
                var file_type = parent.find('.jsCandidateList').attr('data-file_type');
                var candidate = parent.find('input[name=cp_popular_vote_candidate_id]:checked').parents().filter('.jsCandidate');
                var result = parent.find('.jsPopularVoteResult');

                result.find('.jsCandidateTitle').text(candidate.find('.jsCandidateTitle').text());
                if (parseInt(file_type) == FILE_TYPE_IMAGE) {
                    result.find('.jsCandidateImage').attr('src', candidate.find('.jsCandidateImage').attr('data-modal_content'));
                } else {
                    result.find('.jsCandidateMovie').html('<iframe frameborder="0" allowfullscreen></iframe>');
                    result.find('.jsCandidateMovie iframe').attr({
                        src: candidate.find('.jsCandidateImage').attr('data-modal_content'),
                    });
                }
                result.find('.jsCandidateDescription').text(candidate.find('.jsCandidateDescription').text());

                result.show();
            },

            updateCandidateModal: function (src, index) {
                var parent = $(src).parents().filter('li');
                var file_type = $(src).parents().filter('.jsCandidateList').attr('data-file_type');

                UserActionPopularVoteService.refreshCandidateModal(file_type);
                $(index + ' .jsModalTitle').text(parent.find('.jsCandidateTitle').text());
                if (parseInt(file_type) == FILE_TYPE_IMAGE) {
                    $(index + ' .jsModalImage').attr('src', parent.find('.jsCandidateImage').attr('data-modal_content'));
                } else if (parseInt(file_type) == FILE_TYPE_MOVIE) {
                    $(index + ' .jsModalMovie').html('<iframe frameborder="0" allowfullscreen></iframe>');
                    $(index + ' .jsModalMovie iframe').attr({
                        src: parent.find('.jsCandidateImage').attr('data-modal_content'),
                    });
                }
                $(index + ' .jsModalDescription').text(parent.find('.jsCandidateDescription').text());
            },

            cleanCandidateModalMovie: function (index) {
                $(index + ' .jsModalMovie iframe').attr('src', '');
                $(index + ' .jsModalMovie').empty();
            },

            refreshCandidateModal: function(file_type) {
                if (parseInt(file_type) == FILE_TYPE_IMAGE) {
                    $('.jsModalImagePreview').show();
                    $('.jsModalMoviePreview').hide();
                } else if (parseInt(file_type) == FILE_TYPE_MOVIE) {
                    $('.jsModalImagePreview').hide();
                    $('.jsModalMoviePreview').show();
                }
            },

            countShareText: function(src) {
                var parent = $(src).parents().filter('.jsMessage');
                var counter = parent.find('.jsLimitText');
                var limitCount = counter.data('limit_count');

                counter.text('（' + $(src).val().length + '文字/' + limitCount + '文字）');
            }
        };
    })();
}

$(document).ready(function() {
    $('.jsCandidateList').on('click', '.jsOpenModal', function(event) {
        Brandco.unit.openModal('#modal' + $(this).attr('data-open_modal_type'));

        UserActionPopularVoteService.updateCandidateModal(this, '#modal' + $(this).attr('data-open_modal_type'));
        return false;
    });

    $('.jsModalPopularVote').on('click', '.jsCloseModal', function(event) {
        Brandco.unit.closeModal($(this).attr('data-close_modal_type'));

        UserActionPopularVoteService.cleanCandidateModalMovie('#modal' + $(this).attr('data-close_modal_type'));
        return false;
    });

    $('.jsExecutePopularVoteAction').on('click', function(event) {
        UserActionPopularVoteService.executeAction(this);

        return false;
    });

    $('.jsSetPopularVoteResult').each(function() {
        UserActionPopularVoteService.setResult(this);
        $(this).remove();
    });

    $('.jsShareText').on('input', function(event) {
        UserActionPopularVoteService.countShareText(this);
    });

    $('.jsFbConnect').on('click', function(event) {
        var form = $(this).parents().filter('.executePopularVoteActionForm');
        location.href = $('input[name=connect_fb_url]', form).val();

        return false;
    });

    $('.jsTwConnect').on('click', function(event) {
        var form = $(this).parents().filter('.executePopularVoteActionForm');
        location.href = $('input[name=connect_tw_url]', form).val();

        return false;
    });

    if (!window.FormData) {
        $('label img').click(function () {
            $('#' + $(this).parents().filter('label').attr('for')).focus().click();
        });
    }
});