$(document).ready(function() {
    $( ".socialPanelKinds" ).sortable({
        update: function (event, ui) {
            var list='';
            $(ui.item.parent().find('li')).each (function() {
                if ($(this).data('brand-social-account-id')) {
                    list = list +$(this).data('brand-social-account-id') + ',';
                } else if ($(this).data('rssid')){
                    list = list + 'rss:' + $(this).data('rssid') + ',';
                }
            });
            var csrf_token = document.getElementsByName("csrf_token")[0].value;
            var param = {
                data: 'order='+list+'&csrf_token='+csrf_token,
                url: 'admin-top/api_sort_side_col.json'
            };
            Brandco.api.callAjaxWithParam(param);
        }
    });
    $( ".socialPanelKinds" ).disableSelection();
});