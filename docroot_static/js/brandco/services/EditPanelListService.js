var EditPanelListService = (function(){
    return {
        switchClick : function(tag){
            var data = tag.data('entry'),
                csrf_token = document.getElementsByName("csrf_token")[0].value,
                priority = Brandco.helper.getPriority(data.entryId),
                tsw = document.getElementById('switch'+data.entryId).className;

            if(priority == 0 || (priority == 1 && tsw == 'switch off')){
                var sw = document.getElementById('switch'+data.entryId);
                var text = "優先表示";
                if(priority == 1)
                    text = "優先表示を解除";
                if (sw.className == 'switch off'){
                    document.getElementById('select'+data.entryId).innerHTML = '<option value="default">操作</option><option value="fixed">'+text+'</option><option value="repossession">再取得</option><option value="edit">編集</option>';
                }else if(sw.className == 'switch on'){
                    document.getElementById('select'+data.entryId).innerHTML = '<option value="default">操作</option><option value="repossession">再取得</option><option value="edit">編集</option>';
                }
                data.csrf_token = csrf_token;
                var param = {
                    data: data,
                    url: tag.data('url')
                };
                Brandco.api.callAjaxWithParam(param);

            }else{
                if((priority == 1 && tsw == 'switch on'))
                    document.getElementById('switch'+data.entryId).className = 'switch off';
            }
        },
        prioritizeClick : function(tag){
            var data = tag.data('entry'),
                csrf_token = document.getElementsByName("csrf_token")[0].value;
            if(tag.val() == 'fixed'){
                var li = document.getElementById('li'+data.entryId);
                if(li.className == 'contFixed'){
                    li.className = '';
                    document.getElementById('select'+data.entryId).innerHTML = '<option value="default">操作</option><option value="fixed">優先表示</option><option value="repossession">再取得</option><option value="edit">編集</option>';
                    document.getElementById('switch'+data.entryId).setAttribute("data-priority",'0');

                }else{
                    li.className = 'contFixed';
                    document.getElementById('select'+data.entryId).innerHTML = '<option value="default">操作</option><option value="fixed">優先表示を解除</option><option value="repossession">再取得</option><option value="edit">編集</option>';
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

            }else if(tag.val() == 'repossession'){
                data.csrf_token = csrf_token;
                var request_url = tag.data('requesturl'),
                    callbackurl = tag.data('callbackurl'),
                    param = {
                        data: data,
                        url: request_url,
                        success: function(data){
                            if (data.result == 'ok') {
                                document.location = callbackurl;
                            } else if (data.result == 'ng') {
                                if (data.errors.message) {
                                    $('#modal4 #ajaxMessage #message').html(data.errors.message);
                                }
                                $.unblockUI();
                                Brandco.unit.openModal('#modal4');
                            }
                        }
                    };
                Brandco.api.callAjaxWithParam(param, true, false);
            }
        },
        updateSnsProfile: function(url, brandSnsAccountId){

            var data = {
                brandSnsAccountId : brandSnsAccountId
            };

            var param = {
                data: data,
                type: 'GET',
                url: url,
                success: function (json) {
                    if (json.result == 'ok') {
                        $('#sns_profile_image').attr('src',json.data['profile_image']);
                    } else if (json.result == 'ng') {
                        alert('操作が失敗しました！ ↓ アカウント情報の更新に失敗しました。 再度お試し頂くか、事務局までお問い合わせ下さい。');
                    }
                }
            };

            Brandco.api.callAjaxWithParam(param);
        }
    }
})();

$(document).ready(function(){
    $('#delete_area').click(function(){
        var csrf_token = document.getElementsByName("csrf_token")[0].value,
            data = $(this).data('entry') + '&csrf_token='+csrf_token;
        Brandco.helper.disconnect_soclial_app(data, $(this).data('url'), $(this).data('callbackurl'));
    });

    $('.getPostManual').click(function(){
        var csrf_token = document.getElementsByName("csrf_token")[0].value,
            data = {'value':1,'brandSocialAccountId':$('.getPostMethod').data('brand-social-account-id'), 'csrf_token':csrf_token};
        $('.jsRadioToggleTarget').slideUp(300);
        Brandco.helper.changeHiddenFlg(data, $(this).data('url'));
    });

    $('.getPostAuto').click(function(){
        var csrf_token = document.getElementsByName("csrf_token")[0].value,
            data = {'value':0,'brandSocialAccountId':$('.getPostMethod').data('brand-social-account-id'), 'csrf_token':csrf_token};
        $('.jsRadioToggleTarget').slideDown(300);
        Brandco.helper.changeHiddenFlg(data, $(this).data('url'));
    });

    $('#display_panel_limit').on('change',function(){
        var csrf_token = document.getElementsByName("csrf_token")[0].value,
            data = {'value':$(this).val(),'brandSocialAccountId':$('.getPostMethod').data('brand-social-account-id'), 'csrf_token':csrf_token};
        Brandco.helper.changeHiddenFlg(data, $(this).data('url'));
    });

    $('#loadPanel').click(function(){
        var csrf_token = document.getElementsByName("csrf_token")[0].value,
            data = 'csrf_token='+csrf_token;
        Brandco.helper.loadPanel(data, $(this).data('url'), $(this).data('callbackurl'));
    });

    $(".switch").click(function(){
       EditPanelListService.switchClick($(this));
    });

    $(".prioritize").change(function(){
        EditPanelListService.prioritizeClick($(this));
    });

    $('.jsGetProfile').click(function(){

        var snsAccountId = $(this).data('sns_account_id');
        var url = $(this).data('url');

        EditPanelListService.updateSnsProfile(url, snsAccountId);
    });
});