var PhotoEntriesService = (function(){
    return {
        switchClick : function(sw){
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                hidden_url = sw.data('hidden_url'),
                data = sw.data('entry'),
                priority = sw.data('priority');

            if (priority == 0 || (priority == 1 && sw.className == 'switch off')) {
                var text = priority == 1 ? "優先表示を解除" : "優先表示";
                var pr = sw.closest('.action').find('.prioritize');

                if (sw.attr('class') == 'switch off') {
                    $(pr).html('<option value="default">操作</option><option value="fixed">' + text + '</option>');
                } else if (sw.attr('class') == 'switch on') {
                    var li = sw.closest('li');
                    if ($(li).attr('class') == 'contFixed') $(li).attr('class', '');
                    $(pr).html('<option value="default">操作</option>');
                }

                data.csrf_token = csrf_token;
                var params = {
                    data: data,
                    url: hidden_url,
                    success: function(response) {
                        if (response.result == 'ok' && response.data.entry_id != null) {
                            var entry_data = sw.data('entry');
                            entry_data = {
                                entryId: response.data.entry_id,
                                service_prefix: entry_data.service_prefix
                            };
                            var entry_link = sw.closest('li').find('.jsEntryLink');
                            var link = entry_link.data('default_url') + '/' + response.data.entry_id + '?p=' + entry_link.data('page_no');

                            entry_link.attr('href', link);
                            sw.data('entry', entry_data);
                            $(pr).data('entry', entry_data);
                        }
                    }
                };
                Brandco.api.callAjaxWithParam(params);

            } else if ((priority == 1 && sw.attr('class') == 'switch on')) {
                sw.attr('class', 'switch off');
            }
            return false;
        },
        prioritizeChange : function(pr){
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                data = pr.data('entry'),
                prioritize_url = pr.data('prioritize_url');

            data.csrf_token = csrf_token;

            if(pr.val() == "fixed"){
                var li = pr.closest('li');
                var sw = pr.closest('.action').find('switch');

                if ($(li).attr('class') == 'contFixed') {
                    $(li).attr('class', "");
                    $(pr).html('<option value="default">操作</option><option value="fixed">優先表示</option>');
                    $(sw).data('priority', '0');
                } else {
                    $(li).attr('class', 'contFixed');
                    $(pr).html('<option value="default">操作</option><option value="fixed">優先表示を解除</option>');
                    $(sw).data('priority', '1');
                }
                Brandco.api.callAjax(data, prioritize_url, null, null);
            }
            return false;
        }
    }
})();


$(document).ready(function () {
    $(".switch").click(function(){
        PhotoEntriesService.switchClick($(this));
    });

    $(".prioritize").change(function(){
        PhotoEntriesService.prioritizeChange($(this));
    });

    $('.getPostManual').click(function(){
        var csrf_token = document.getElementsByName("csrf_token")[0].value,
            data = {
                'value': 1,
                'streamId': $('.getPostMethod').data('stream_id'),
                'stream_prefix': 'PhotoStream',
                'csrf_token': csrf_token
            };
        $('.jsRadioToggleTarget').slideUp(300);
        Brandco.helper.changeHiddenFlg(data, $(this).data('url'));
    });

    $('.getPostAuto').click(function(){
        var csrf_token = document.getElementsByName("csrf_token")[0].value,
            data = {
                'value': 0,
                'streamId': $('.getPostMethod').data('stream_id'),
                'stream_prefix': 'PhotoStream',
                'csrf_token': csrf_token
            };
        $('.jsRadioToggleTarget').slideDown(300);
        Brandco.helper.changeHiddenFlg(data, $(this).data('url'));
    });

    $('#display_panel_limit').on('change',function(){
        var csrf_token = document.getElementsByName("csrf_token")[0].value,
            data = {
                'value': $(this).val(),
                'streamId': $('.getPostMethod').data('stream_id'),
                'stream_prefix': 'PhotoStream',
                'csrf_token': csrf_token
            };
        Brandco.helper.changeHiddenFlg(data, $(this).data('url'));
    });
});