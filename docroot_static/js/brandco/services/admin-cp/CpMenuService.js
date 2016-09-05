var CpMenuService = (function(){
    return{
        checkAnnounceStatus: function (cp_id, action_id) {
            var param = {
                data: {cp_id:cp_id, action_id:action_id},
                url: 'admin-cp/api_check_fix_target.json',
                type: 'GET',
                success: function(data){
                    if(data.result == 'ok'){
                        window.location.href = $('base').attr('href') + 'admin-cp/csv_winner_shipping_list/' + cp_id + '/' + action_id;
                        Brandco.unit.closeModal('_download_modal_' + cp_id);
                    } else {
                        if (data.errors.message) {
                            $('#modal_not_dl_announce').find('.attention1').html(data.errors.message);
                            Brandco.unit.openModal("#modal_not_dl_announce");
                        } else {
                            alert('操作が失敗しました。');
                        }
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param);
        },
        checkExistCpUser: function(target) {
            var data = {cp_id: target.data('cp-id')};
            var param = {
                data: data,
                type: 'GET',
                url: 'admin-cp/api_check_joined_user.json',
                success: function(json) {
                    if(json.result === "ok") {
                        if(json.data['alert_message']) {
                            $('#modal_disable_edit_action').find('.attention1').html(json.data['alert_message']);
                            Brandco.unit.openModal('#modal_disable_edit_action');
                        } else {
                            Brandco.unit.openModal('#SkeletonModal');
                        }
                    } else {
                        alert("操作が失敗しました");
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param, true);
        },
        downLoadDataDownLoadModalTemplate:function (cp_id) {
            var param = {
                data: {
                    cp_id: cp_id,
                    isFromPublicCp: $('.jsPublicCpPage').is('*')
                },
                type: 'GET',
                dataType: 'html',
                url: 'admin-cp/api_get_cp_download_modal_template',
                success: function(html) {
                    $('#downloadModalArea').html(html);
                    Brandco.unit.openModal('#modal_download_modal_'+cp_id);
                }
            };
            Brandco.api.callAjaxWithParam(param, true);
        }
        
    };
})();

var CpHeaderActionListService = (function(){
    return {
        countActionMessages: function (cp_id,callback) {
            var param = {
                type: "GET",
                data: { cp_id: cp_id},
                url:  "admin-cp/api_count_cp_user_action_messages.json"
            };
            param.success = function(data){
                callback(data['data']);
            };
            Brandco.net.ajaxSetup();
            Brandco.net.callAjax(param,false,true);
        }
    }
})();

$(document).ready(function() {
    $('.font-win,.font-images,.font-download').children('select').on('change', function() {
        var url = $(this).val();
        if(!url) {
            return;
        }
        $(window).unbind('beforeunload');
        window.location.href = $(this).val();
    });
    downloadModalArea = $('#downloadModalArea');
    downloadModalArea.on('click','a.download', function () {
        if($(this).hasClass('jsAnnounceDL')) {
            return;
        }
        var cp_id = $(this).closest('.jsModal').data('cp_id');
        Brandco.unit.closeModal('_download_modal_' + cp_id);
    });

    downloadModalArea.on('change','select.jsAnnounceDL', function () {
        var cp_id = $(this).closest('.jsModal').data('cp_id');
        var action_id = $(this).val();
        CpMenuService.checkAnnounceStatus(cp_id, action_id);
    });

    downloadModalArea.on('click','a.jsAnnounceDL', function () {
        var cp_id = $(this).closest('.jsModal').data('cp_id');
        var action_id = $(this).data('action_id');
        CpMenuService.checkAnnounceStatus(cp_id, action_id);
    });

    $('#modal_not_dl_announce').find('a').on('click', function() {
        Brandco.unit.closeModal('_not_dl_announce');
    });

    $('#checkExistUser').on('click', function () {
        CpMenuService.checkExistCpUser($(this));
    });

    $('#openSkeletonModal').on('click', function () {
        Brandco.unit.closeModal('_disable_edit_action');
        Brandco.unit.openModal('#SkeletonModal');
    });

});