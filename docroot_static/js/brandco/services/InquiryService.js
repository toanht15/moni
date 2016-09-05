if (typeof(InquiryService) === 'undefined') {
    var InquiryService = function() {
        return {
            executeSubmitInquiry: function(src) {
                var form = $(src).parents().filter('form');
                var submit_flg = $(src).attr('data-submit_flg');

                form.append($('<input/>').attr({
                    type:   'hidden',
                    name:   'submit_flg',
                    value:  submit_flg,
                }));
                form.submit();
            }
        };
    }();
}
$(document).ready(function(){
    $('.jsInquirySubmit').one('click', function() {
        InquiryService.executeSubmitInquiry(this);

        return false;
    });
});
