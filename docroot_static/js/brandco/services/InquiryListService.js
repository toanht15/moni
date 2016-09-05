if (typeof(InquiryListService) === 'undefined') {
    var InquiryListService = function () {
        return {
            executeSearchInquiry: function (src) {
                var form = $(src).parents().filter('form');
                var url = $(src).data('form_action');

                var param = {
                    data: {
                        csrf_token: $('input[name=csrf_token]', form).val(),
                        page: $('input[name=page]', form).val(),
                        total_count: $('input[name=total_count]', form).val(),
                        operator_name: $('input[name=operator_name]', form).val(),
                        category: $('select[name=category]', form).val(),
                        status: $('input[name="status[]"]:checked', form).map(function () {
                            return $(this).val();
                        }).get(),
                        mail_address: $('input[name=mail_address]', form).val(),
                        period_flg: $('input[name=period_flg]:checked', form).val(),
                        date_begin: $('input[name=date_begin]', form).val(),
                        date_end: $('input[name=date_end]', form).val(),
                        keywords: $('input[name=keywords]', form).val(),
                    },
                    url: url,
                    type: 'GET',
                    success: function (json) {
                        if (json.result == 'ok') {
                            $('.jsInquiryList').html(json.html);
                        }
                    },
                    error: function () {
                        alert('error');
                    },
                };

                Brandco.api.callAjaxWithParam(param);
            },

            executeDownloadInquiry: function (src) {
                var form = $(src).parents().filter('form');
                var url = $(src).data('form_action');

                form.attr('action', url);
                form.submit();
            }
        };
    }();
}
$(document).ready(function () {
    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker();

    // common check toggle area
    $('.jsCheckToggle').on('change', function () {
        var targetWrap = $(this).parents('.jsCheckToggleWrap')[0];
        var target = $(targetWrap).find('.jsCheckToggleTarget');
        target.find('.jsDate').val('');
        target.slideToggle(300);
    });

    $('.jsSearchForm').on('click', '.jsSearch', function () {
        InquiryListService.executeSearchInquiry(this);

        return false;
    });

    $('.jsSearchForm').on('click', '.jsDownload', function () {
        InquiryListService.executeDownloadInquiry(this);

        return false;
    });

    $('.jsInquiryList').on('click', '.jsPager', function () {
        $('input[name=page]').val($(this).attr('data-page'));
        InquiryListService.executeSearchInquiry('.jsSearch');

        return false;
    });
});
