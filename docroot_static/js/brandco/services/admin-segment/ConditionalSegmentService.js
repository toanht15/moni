var ConditionalSegmentService= (function() {
    return {
        initSegmentData: function() {
            var cur_sp_no = 0;
            $('.jsSPContainer').each(function() {
                SegmentCommonService.segment_data[cur_sp_no] = [];
                SegmentCommonService.initSPCComponentData(this, cur_sp_no);
                cur_sp_no += 1;
            });

            //排除container追加
            SegmentCommonService.segment_data[cur_sp_no] = [];

            SegmentCommonService.is_show_sp_creator_confirm_box = $('input[name="segment_id"]').val().length == 0;
        },
        updateSegmentName: function() {
            $('.jsSPNameInput').each(function() {
                if (SegmentCommonService.isEmpty(this)) {
                    $(this).attr('placeholder', 'セグメント');
                }
            });
        }
    }
})();

$(document).ready(function() {

    //Init SP CONTAINER
    ConditionalSegmentService.initSegmentData();

    //Init SPC Name
    ConditionalSegmentService.updateSegmentName();

});
