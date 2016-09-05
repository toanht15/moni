$(document).ready(function(){
    $('#selectIgEntry').click(function(){
        var id = $("#igEntryList :checked").attr('id');
        var url = $("#entryImg_" + id).attr('src');
        var link = $("#entryImg_" + id).data('link');
        if (id) {
            $('#currentEntryImg', window.parent.document).children('img').attr({'src': url});
            $('#currentEntryId', window.parent.document).attr({'value': id});
            $('#currentEntryImg', window.parent.document).show();
            $(".jsIgSelectEntry", window.parent.document).replaceWith(
                        '<div class="engagementInner followIg jsIgSelectEntry">'
                        + '<div class="engagementIg">'
                        + '<p class="postDummy_ig">post dummy</p>'
                        + '</div></div>');
        }
    });

    $('#loadPanel').click(function(){
        var csrf_token = document.getElementsByName("csrf_token")[0].value,
            data = 'csrf_token='+csrf_token;
        Brandco.helper.loadPanel(data, $(this).data('url'), $(this).data('callbackurl'));
    });
});