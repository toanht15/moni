var SegmentGroupService= (function() {
    return {
    }
})();

$(document).ready(function() {

    SegmentCommonService.initSegmentData();
    SegmentCommonService.updateSegmentName();

    // Update Segment Name
    $(document).on('click', '.jsTextareaToggle', function() {
        
        var target = $(this).prev('.segmentName');
        var txt = target.text();
        target.html('<input type="text" name="spc_name" value="'+txt+'" class="jsSPNameInput" />');
        $(this).css({display:'none'});
    });

});

