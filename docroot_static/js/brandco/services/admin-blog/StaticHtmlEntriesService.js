var StaticHtmlEntriesService = (function () {
    return{
        isChecked: function () {
            var checkedFlg = false;
            $('.page_group').each(function () {
                if ($(this).prop('checked') == true) {
                    checkedFlg = true;
                }
            })
            if (checkedFlg == false) alert('チェックしてください。');
            return checkedFlg;
        }
    };
})();

$(document).ready(function () {
    $('.selectAction').change(function () {
        var url = this.getAttribute('data-url'),
            callback = this.getAttribute('data-callback');
        Brandco.helper.selectActionChange(this, url, callback, 'staticHTML');
    });

    $('#delete_area').click(function () {
        var url = this.getAttribute('data-url'),
            callback = this.getAttribute('data-callback');
        Brandco.helper.deleteEntry(this, url, callback);
    });

    $('.checkAll').on('change', function () {
        $('.' + this.id).prop('checked', this.checked);
    });

    $('#post_save').on('click', function () {
        switch($('#selectMenu option:selected').val()){
            case 'public':
                if (StaticHtmlEntriesService.isChecked() == false) break;
                if (confirm('チェック済みのページを公開にしますか？')) {
                    document.actionForm.action = 'admin-blog/public_static_html_entries';
                    document.actionForm.submit();
                }
                break;
            case 'draft':
                if (StaticHtmlEntriesService.isChecked() == false) break;
                if (confirm('チェック済みのページを下書きにしますか？')) {
                    document.actionForm.action = 'admin-blog/draft_static_html_entries';
                    document.actionForm.submit();
                }
                break;
            case 'delete':
                if (StaticHtmlEntriesService.isChecked() == false) break;
                if (confirm('チェック済みのページを削除しますか？')) {
                    document.actionForm.action = 'admin-blog/delete_static_html_entries';
                    document.actionForm.submit();
                }
                break;
            case 'default':
                break;
        }
    });
});
