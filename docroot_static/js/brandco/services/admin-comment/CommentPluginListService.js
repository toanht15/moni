var CommentPluginListService = (function() {
    return {
        cur_page: 1,
        loadPluginList: function() {
            var pager = $('.jsListPager'),
                type = $('input[name="type"]:checked').val(),
                limit = $('select[name="item_limit"]').val(),
                order_type = $('select[name="order_type"]').val(),
                params = {
                    data: {
                        order_type: order_type,
                        page_limit: limit,
                        type: type,
                        page: CommentPluginListService.cur_page
                    },
                    url: 'admin-comment/api_load_comment_plugin_list.json',
                    type: 'GET',
                    success: function(response) {
                        if (response.result == 'ok') {
                            $(pager).replaceWith(response.html.pager);
                            $('.jsCommentPluginList').replaceWith(response.html.plugin_list);
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        }
    }
})();

$(document).ready(function() {
    CommentPluginListService.loadPluginList();

    $(document).on('click', '.jsPager', function() {
        CommentPluginListService.cur_page = $(this).attr('data-page');
        CommentPluginListService.loadPluginList();
    });

    $(document).on('click', '.jsUpdateItemList', function() {
        CommentPluginListService.loadPluginList();
    });

    $(document).on('change', '.jsApplySearchCondition', function() {
        CommentPluginListService.loadPluginList();
    });
});