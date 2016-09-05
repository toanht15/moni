var RssEntriesService = (function(){
    return {
        switchClick: function(tag){
            var data = tag.data('entry'),
                priority = Brandco.helper.getPriority(data.entryId),
                tsw = document.getElementById('switch'+data.entryId).className,
                csrf_token = document.getElementsByName("csrf_token")[0].value;

            if(priority == 0 || (priority == 1 && tsw == 'switch off')){
                var sw = document.getElementById('switch'+data.entryId);
                text = "優先表示";
                if(priority == 1)
                    text = "優先表示を解除";
                if (sw.className == 'switch off'){
                    document.getElementById('select'+data.entryId).innerHTML = '<option value="default">操作</option><option value="fixed">'+text+'</option><option value="edit">編集</option>';
                }else if(sw.className == 'switch on'){
                    document.getElementById('select'+data.entryId).innerHTML = '<option value="default">操作</option><option value="edit">編集</option>';
                }
                data.csrf_token = csrf_token;
                var param = {
                    data: data,
                    url: tag.data('url'),
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        console.log('tes');
                    }
                };
                Brandco.api.callAjaxWithParam(param);
            }else{
                if((priority == 1 && tsw == 'switch on'))
                    document.getElementById('switch'+data.entryId).className = 'switch off';
            }
            return false;
        },
        prioritizeChange : function(tag){
            var data = tag.data('entry'),
                csrf_token = document.getElementsByName("csrf_token")[0].value;

            if(tag.val() == 'fixed'){
                var li = document.getElementById('li'+data.entryId);
                if(li.className == 'contFixed'){
                    li.className = '';
                    document.getElementById('select'+data.entryId).innerHTML = '<option value="default">操作</option><option value="fixed">優先表示</option><option value="edit">編集</option>';
                    document.getElementById('switch'+data.entryId).setAttribute("data-priority",'0');

                }else{
                    li.className = 'contFixed';
                    document.getElementById('select'+data.entryId).innerHTML = '<option value="default">操作</option><option value="fixed">優先表示を解除</option><option value="edit">編集</option>';
                    document.getElementById('switch'+data.entryId).setAttribute("data-priority",'1');
                }

                data.csrf_token = csrf_token;
                var param = {
                    data: data,
                    url: tag.data('url')
                };
                Brandco.api.callAjaxWithParam(param);
            }else if(tag.val() == 'edit'){
                window.location = tag.data('editurl');

            }
            return false;
        }
    }
})();

$(document).ready(function(){
    $('a[href="#modal1"]').click(function(){
        Brandco.helper.showConfirm('#modal1', $(this).data('url'));
    });

    $('#delete_area').click(function(){
       var csrf_token = document.getElementsByName("csrf_token")[0].value,
           data = $(this).data('entry') + '&csrf_token='+csrf_token;
        Brandco.helper.disconnect_soclial_app(data, $(this).data('url'), $(this).data('callbackurl'));
    });

    $('.getPostManual').click(function(){
        var csrf_token = document.getElementsByName("csrf_token")[0].value,
            data = {'value':1,'stream_prefix':'RssStream','streamId': $('.getPostMethod').data('streamid') ,'csrf_token':csrf_token};
        $('.jsRadioToggleTarget').slideUp(300);
        Brandco.helper.changeHiddenFlg(data, $(this).data('url'));
    });

    $('.getPostAuto').click(function(){
        var csrf_token = document.getElementsByName("csrf_token")[0].value,
            data = {'value':0,'stream_prefix':'RssStream','streamId': $('.getPostMethod').data('streamid') ,'csrf_token':csrf_token};
        $('.jsRadioToggleTarget').slideDown(300);
        Brandco.helper.changeHiddenFlg(data, $(this).data('url'));
    });

    $('#display_panel_limit').on('change',function(){
        var csrf_token = document.getElementsByName("csrf_token")[0].value,
            data = {'value':$(this).val(),'stream_prefix':'RssStream', 'streamId': $('.getPostMethod').data('streamid'), 'csrf_token':csrf_token};
        Brandco.helper.changeHiddenFlg(data, $(this).data('url'));
    });

    $('#loadPanel').click(function(){
        var csrf_token = document.getElementsByName("csrf_token")[0].value,
            data = 'csrf_token='+csrf_token;
        Brandco.helper.loadPanel(data, $(this).data('url'), $(this).data('callbackurl'));
    });

    $(".switch").click(function(){
       RssEntriesService.switchClick($(this));
    });

    $(".prioritize").change(function(){
       RssEntriesService.prioritizeChange($(this));
    });
});