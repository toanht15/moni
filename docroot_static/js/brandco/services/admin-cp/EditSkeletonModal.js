$('#jsSkeletonModalIframe').load(function() {
    // 15はスクロールバーの表示分
    $(this).height($(this).contents().find('body').height() + 15);
});