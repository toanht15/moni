$(document).ready(function() {
    $('.jsModulePreviewSwitch').on('click', function() {
        var preview_url = $(this).data('preview_url');

        if ($(this).hasClass('left')) {
            $('.jsModulePreviewArea').removeClass('adminPrev_sp').addClass('adminPrev_pc');
        } else if($(this).hasClass('right')) {
            $('.jsModulePreviewArea').removeClass('adminPrev_pc').addClass('adminPrev_sp');
            preview_url += '&sp_mode=on';
        }

        $('.jsPreviewFrame').attr('src', preview_url);
    });
});