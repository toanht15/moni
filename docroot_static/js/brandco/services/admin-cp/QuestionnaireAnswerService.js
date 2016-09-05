var QuestionnaireCampaignService = (function() {
    var cur_page = 1;
    return {
        changeActionPanelHiddenFlg: function() {
            var change_hidden_flg_url = $('input[name="questionnaire_action_panel_hidden_url"]').val(),
                questionnaire_panel_hidden_flg = $('.jsQuestionnairePanelHiddenFlg:checked').val(),
                action_id = $('input[name="action_id"]').val(),
                param = {
                    url: change_hidden_flg_url,
                    data: {
                        action_id: action_id,
                        panel_hidden_flg: questionnaire_panel_hidden_flg
                    },
                    success: function(response) {
                        if (response && response.result == 'ok') {
                            // success
                        } else {
                            alert('エラーが発生しました、もう一度やり直してください')
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(param);
        },
        getQuestionnaireList: function(page) {
            // Paging
            if (page == null) page = cur_page;
            else cur_page = page;

            // Targeting Question
            var targeted_question_ids = new Array();
            $('.jsTargetingQuestion:checked').each(function() {
                targeted_question_ids.push($(this).val());
            });

            var get_questionnaire_list_url = $('input[name="questionnaire_list_url"]').val(),
                cp_id = $('input[name="cp_id"]').val(),
                brand_id = $('input[name="brand_id"]').val(),
                action_id = $('input[name="action_id"]').val(),
                page_limit = $('.jsQuestionnaireAnswerPageLimit').val();
                order_kind = $('.jsQuestionnaireAnswerOrderKind').val(),
                order_type = $('.jsQuestionnaireAnswerOrderType:checked').val(),
                approval_status= $('.jsQuestionnaireAnswerApprovalStatus:checked').val(),
                param = {
                    url: get_questionnaire_list_url,
                    data: {
                        page: page,
                        cp_id: cp_id,
                        brand_id: brand_id,
                        action_id: action_id,
                        approval_status: approval_status,
                        order_kind: order_kind,
                        order_type: order_type,
                        page_limit: page_limit,
                        targeted_question_ids: targeted_question_ids
                    },
                    type: 'GET',
                    success: function(response) {
                        if (response && response.result == 'ok') {
                            $('.jsQuestionnaireAnswerList').html(response.html);
                        } else {
                            alert('エラーが発生しました、もう一度やり直してください')
                        }
                    }
                };
            console.log(get_questionnaire_list_url);

            Brandco.api.callAjaxWithParam(param);
        },
        exportAPIUrl: function() {
            // Targeting Question
            var export_question_ids = new Array();
            $('.jsExportingQuestion:checked').each(function() {
                export_question_ids.push($(this).val());
            });

            var cp_id = $('input[name="cp_id"]').val(),
                action_id = $('input[name="action_id"]').val(),
                csrf_token = document.getElementsByName('csrf_token')[0].value,
                cp_action_type = $('input[name="cp_action_type"]').val(),
                params = {
                    url: 'admin-cp/api_export_api_url.json',
                    data: {
                        cp_id: cp_id,
                        cp_action_id: action_id,
                        csrf_token: csrf_token,
                        cp_action_type: cp_action_type,
                        export_question_ids: export_question_ids
                    },
                    success: function(response) {
                        if (response && response.result == 'ok') {
                            $('.jsExportAPI').html('出力対象の更新');
                            $('.jsExportAPIUrl').html('URL：' + response.data.api_url);
                        } else {
                            alert('エラーが発生しました、もう一度やり直してください');
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        },
        isChecked: function() {
            var checked_flg = false;
            $('.jsQuestionnaireAnswerCheck').each(function() {
                if ($(this).prop('checked') == true) {
                    checked_flg = true;
                }
            });

            if (checked_flg == false) {
                alert('チェックしてください。');
            }
            return checked_flg;
        }
    }
})();

$(document).ready(function() {
    // Export Content API Url
    $(document).on('click', '.jsExportAPI', function() {
        QuestionnaireCampaignService.exportAPIUrl();
    });

    // Searching
    $(document).on('click', '.jsQuestionnaireAnswerSearchBtn', function() {
        QuestionnaireCampaignService.getQuestionnaireList();
    });

    // Panel hidden checking
    $(document).on('click', '.jsQuestionnairePanelHiddenConfirm', function() {
        QuestionnaireCampaignService.changeActionPanelHiddenFlg();
    });

    // Targeting Question
    $(document).on('change', '.jsTargetingQuestion', function() {
        QuestionnaireCampaignService.getQuestionnaireList();
    });

    // Paging
    $(document).on('click', '.jsCpDataListPager', function() {
        QuestionnaireCampaignService.getQuestionnaireList($(this).data('page'));
    });

    // Questionnaire Checkbox
    $(document).on('change', '.jsQuestionnaireAnswerCheckAll', function() {
        $('.' + $(this).data('questionnaire_answer_check_class')).prop('checked', this.checked);
    });

    $(document).on('change', '.jsQuestionnaireAnswerCheck', function() {
        if (!this.checked && $('.jsQuestionnaireAnswerCheckAll').is(':checked')) {
            $('.jsQuestionnaireAnswerCheckAll').prop('checked', false);
        }
    });

    // Form submit
    $(document).on('click', ('.jsQuestionnaireAnswerActionFormSubmit1'), function() {
        if (QuestionnaireCampaignService.isChecked() == false) return false;

        var submit_msg = $('input[name="multi_questionnaire_answer_approval_status_1"]:checked').val() == '1' ? '承認' : '非承認';
        if (confirm('チェック済みの投稿を' + submit_msg + 'にしますか？')) {
            document.questionnaire_answer_action_form.submit();
        }
    });

    $(document).on('click', ('.jsQuestionnaireAnswerActionFormSubmit2'), function() {
        if (QuestionnaireCampaignService.isChecked() == false) return false;

        var submit_msg = $('input[name="multi_questionnaire_answer_approval_status_2"]:checked').val() == '1' ? '承認' : '非承認';
        if (confirm('チェック済みの投稿を' + submit_msg + 'にしますか？')) {
            document.questionnaire_answer_action_form.submit();
        }
    });

    // Approval status checking
    $(document).on('change', '.jsMultiQuestionnaireAnswerApprovalStatus', function() {
        $('input[name="multi_questionnaire_answer_approval_status"]').val($(this).val());
    });

    // Reset Searching
    $(document).on('click', '.jsQuestionnaireAnswerSearchReset', function() {
        $('.jsQuestionnaireAnswerApprovalStatus[value="1"]').prop('checked', true);
        $('.jsQuestionnaireAnswerOrderType[value="1"]').prop('checked', true);
        $('.jsQuestionnaireAnswerOrderKind').val(1);
        $('.jsQuestionnaireAnswerPageLimit').val(10);

        QuestionnaireCampaignService.getQuestionnaireList();
    });
});