var PopularVoteCampaignService = (function() {
    return {
        exportAPIUrl: function() {

            var cp_id = $('input[name="cp_id"]').val(),
                action_id = $('input[name="action_id"]').val(),
                csrf_token = document.getElementsByName('csrf_token')[0].value,
                cp_action_type = $('input[name="cp_action_type"]').val(),
                cp_action_id = $('input[name="cp_action_id"]').val(),
                params = {
                    url: 'admin-cp/api_export_api_url.json',
                    data: {
                        cp_id: cp_id,
                        csrf_token: csrf_token,
                        cp_action_type: cp_action_type,
                        cp_action_id: cp_action_id
                    },
                    success: function(response) {
                        if (response && response.result == 'ok') {
                            $('.jsExportAPIBtn').html('<span class="large2">外部出力APIのURL作成</span>');
                            $('.jsExportAPIUrl').html('URL：' + response.data.api_url);
                        } else {
                            alert('エラーが発生しました、もう一度やり直してください');
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        }
    }
})();

$(document).ready(function() {
    // Export Content API Url
    $(document).on('click', '.jsExportAPI', function() {
        PopularVoteCampaignService.exportAPIUrl();
    });
});