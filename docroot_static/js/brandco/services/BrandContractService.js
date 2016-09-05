var BrandContractService = (function() {
    return {
        openPreview: function() {
            var param = {
                data: {
                    'brand_id': $('input[name="brand_id"]').val(),
                    'closed_title': $('input[name="closed_title"]').val(),
                    'closed_description': CKEDITOR.instances.closed_description.getData()
                },
                url: '/dashboard/api_write_preview.json',
                success: function(response) {
                    window.open(response.data.preview_url, '_blank');
                }
            };
            Brandco.api.callAjaxWithParam(param, false, false);
        }
    }
})();

$(document).ready(function() {
    CKEDITOR.config.coreStyles_strike = {element:"del",overrides:"strike"};
    CKEDITOR.config.height = '500px';
    CKEDITOR.on('instanceCreated', function (e) {
        e.editor.on('change', function (ev) {
            $(window).unbind('beforeunload');
            $(window).on('beforeunload', function() {
                return Brandco.message.reloadMessage;
            });
        });
    });

    CKEDITOR.replace( 'closed_description', {
        filebrowserUploadUrl: $('#display').data('uploadurl'),
        filebrowserBrowseUrl: $('#display').data('listurl')
    });

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker();

    $('.jsClosedBrandPreview').on('click', function() {
        BrandContractService.openPreview();
    });
})
