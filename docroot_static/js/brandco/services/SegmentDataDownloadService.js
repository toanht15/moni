$(document).ready(function() {
    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker();

    $('.jsCurrentActiveSegment').on('change', function() {
        var segment_id = $(this).val();

        $('.jsCurrentSegmentProvision')
            .find('option').remove().end()
            .append('<option value="0">未設定</option>')
            .val(0);

        if (segment_id == 0) {
            return;
        }

        var params = {
                data: {
                    'segment_id' : segment_id
                },
                url: '/segment/api_fetch_segment_provisions.json',
                success: function(response) {
                    if (response && response.result == 'ok') {
                        $.each(response.data.segment_provisions, function(index, value) {
                            $('.jsCurrentSegmentProvision').append($('<option value="' + index + '">' + value + '</option>'));
                        })
                    }
                }
            };
        Brandco.api.callAjaxWithParam(params, false, false);
    });

    $('.jsProvisionDataDownloadBtn').on('click', function() {
        var segment_id = $('.jsCurrentActiveSegment option:selected').val(),
            segment_provision_id = $('.jsCurrentSegmentProvision option:selected').val();

        if (segment_provision_id == 0) {
            alert('Missing Provision Id');
            return;
        }

        window.location.href = $(this).data('href') + '/' + segment_id +  '/' + segment_provision_id + '?' + 'targeted_date=' + encodeURIComponent($('.jsDate').val());
    });
});