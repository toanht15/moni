var EditInstantWinActionService = (function(){
    return{
        resetAnimation: function () {
            $("#instantWinImage").find('img').attr('src', $('#animationData').data('img'));
            $("#previewNow").parent('li').html('<a href="javascript:void(0)" class="large1" id="btnPreview">チャレンジする</a>');
            $("#throughPreview").removeClass('playing');
        },

        previewWinImage: function () {
            var img_url = $('#animationData').data('win');
            $("#winImagePreview").attr('src', img_url + '?' + (new Date).getTime());
            $('#winPreview').show().slideDown();
            $("#animationPreview").hide().slideUp();
            $("#throughPreview").removeClass('playing');
        },

        previewLoseImage: function () {
            var img_url = $('#animationData').data('lose');
            $("#loseImagePreview").attr('src', img_url + '?' + (new Date).getTime());
            $('#losePreview').show().slideDown();
            $("#animationPreview").hide().slideUp();
            $("#throughPreview").removeClass('playing');
        },

        initTextPreview: function () {
            if ($('#text_area_0').val()) {
                var text_content = $('#text_area_0').val();
                var param = {
                    data: {
                        text_content: text_content
                    },
                    url: 'admin-cp/parse_markdown',
                    success: function(response) {
                        if (response.result == 'ok') {
                            $("#loseTextPreview").html(response.data.html_content);
                        }
                    }
                };
                Brandco.api.callAjaxWithParam(param, false,  false);
            }

            if ($('#text_area_1').val()) {
                var text_content = $('#text_area_1').val();
                var param = {
                    data: {
                        text_content: text_content
                    },
                    url: 'admin-cp/parse_markdown',
                    success: function(response) {
                        if (response.result == 'ok') {
                            $("#winTextPreview").html(response.data.html_content);
                        }
                    }
                };
                Brandco.api.callAjaxWithParam(param, false,  false);
            }
        }
    };
})();

$(document).ready(function () {
    EditInstantWinActionService.initTextPreview();

    $('#winning_rate').on('blur', function() {
        var winnerCount = $("#expectedChallenge").data('count');
        var totalChallenge = Math.round(winnerCount / $('#winning_rate').val() * 100);
        if(isFinite(totalChallenge) && totalChallenge !== 0) {
            $('#expectedChallenge').text(('約') + totalChallenge.toLocaleString() + ('回のチャレンジで予定当選者数に達する見込みです。'));
        } else {
            $('#expectedChallenge').text(('約 - 回のチャレンジで予定当選者数に達する見込みです。'));
        }
    });

    $('#text_area_0').on('input', function() {
        var text_content = $(this).val();
        var param = {
            data: {
                text_content: text_content
            },
            url: 'admin-cp/parse_markdown',
            success: function(response) {
                if (response.result == 'ok') {
                    $("#loseTextPreview").html(response.data.html_content);
                }
            }
        };
        Brandco.api.callAjaxWithParam(param, false,  false);
    });

    $('#text_area_1').on('input', function() {
        var text_content = $(this).val();
        var param = {
            data: {
                text_content: text_content
            },
            url: 'admin-cp/parse_markdown',
            success: function(response) {
                if (response.result == 'ok') {
                    $("#winTextPreview").html(response.data.html_content);
                }
            }
        };
        Brandco.api.callAjaxWithParam(param, false,  false);
    });

    $('#image_file_0').on('change', function() {
        var input = $(this)[0];
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#loseImagePreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
            $('#loseImagePreview').parent().show();
        }else{
            $('#loseImagePreview').parent().hide();
        }
    });
    $('#image_file_1').on('change', function(){
        var input = $(this)[0];
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#winImagePreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
            $('#winImagePreview').parent().show();
        }else{
            $('#winImagePreview').parent().hide();
        }
    });

    if ($('#challengeTimeValue').val() || $('#challengeTimeMeasurement').val()) {
        $("#challengeTimes").html(($('#challengeTimeValue').val())+($('#challengeTimeMeasurement option:selected').text()));
    }

    $('#challengeTimeValue').on('input', function() {
        $("#challengeTimes").html(($('#challengeTimeValue').val())+($('#challengeTimeMeasurement option:selected').text()));
    });

    $('#challengeTimeMeasurement').on('change', function() {
        $("#challengeTimes").html(($('#challengeTimeValue').val())+($('#challengeTimeMeasurement option:selected').text()));
    });

    if ($("[name='once_flg']:checked").val() == 1) {
        $("#challengeTimes").parent('p').hide();
        $("#challengeTimeValue").attr('disabled', 'disabled');
        $("#challengeTimeMeasurement").attr('disabled', 'disabled');
    } else {
        $("#challengeTimes").parent('p').show();
    }

    $("[name='once_flg']").click(function(){
        var num = $("[name='once_flg']").index(this);
        if(num == 1){
            $("#challengeTimes").parent('p').hide();
            $("#challengeTimeValue").attr('disabled', 'disabled');
            $("#challengeTimeMeasurement").attr('disabled', 'disabled');
        } else {
            $("#challengeTimes").parent('p').show();
            $("#challengeTimeValue").removeAttr('disabled');
            $("#challengeTimeMeasurement").removeAttr('disabled');
        }
    });

    if ($("[name='logic_type']:checked").val() == 2) {
        $("#winning_rate").attr('disabled', 'disabled');
    }

    $("[name='logic_type']").click(function(){
        var num = $("[name='logic_type']").index(this);
        if(num == 1){
            $("#winning_rate").removeAttr('disabled');
        } else {
            $("#winning_rate").attr('disabled', 'disabled');
        }
    });

    $("[name='image_type_0']").click(function() {
        var num = $(this).val();
        if(num == 1) {
            $('#loseImagePreview').attr('src', $('#animationData').data('lose'));
            $('#loseImagePreview').parent('p').show();
        } else {
            $('#image_file_0').removeAttr('disabled');
            var input = $('#image_file_0')[0];
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#loseImagePreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
                $('#loseImagePreview').parent().show();
            }else{
                $('#loseImagePreview').parent().hide();
            }
        }
    });

    $("[name='image_type_1']").click(function() {
        var num = $(this).val();
        if (num == 1) {
            $('#winImagePreview').attr('src', $('#animationData').data('win'));
            $('#winImagePreview').parent('p').show();
        } else {
            $('#image_file_1').removeAttr('disabled');
            var input = $('#image_file_1')[0];
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#winImagePreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
                $('#winImagePreview').parent().show();
            } else {
                $('#winImagePreview').parent().hide();
            }
        }
    });

    $('#animationTab').click(function() {
        if (!$('#throughPreview').hasClass('playing')) {
            $('#instantTab li').removeClass('current');
            $('#animationTab').addClass('current');
            $(".message").hide();
            $('#animationPreview').show();
        }
    });

    $('#loseTab').click(function() {
        if (!$('#throughPreview').hasClass('playing')) {
            $('#instantTab li').removeClass('current');
            $('#loseTab').addClass('current');
            $(".message").hide();
            $('#losePreview').show();
        }
    });

    $('#winTab').click(function() {
        if (!$('#throughPreview').hasClass('playing')) {
            $('#instantTab li').removeClass('current');
            $('#winTab').addClass('current');
            $(".message").hide();
            $('#winPreview').show();
        }
    });

    $('.jsAnimationSetting').click(function() {
        if (!$('#throughPreview').hasClass('playing')) {
            $('#instantTab li').removeClass('current');
            $('#animationTab').addClass('current');
            $(".message").hide();
            $('#animationPreview').show();
        }
    });

    $('.jsLoseSetting').click(function() {
        if (!$('#throughPreview').hasClass('playing')) {
            $('#instantTab li').removeClass('current');
            $('#loseTab').addClass('current');
            $(".message").hide();
            $('#losePreview').show();
        }
    });

    $('.jsWinSetting').click(function() {
        if (!$('#throughPreview').hasClass('playing')) {
            $('#instantTab li').removeClass('current');
            $('#winTab').addClass('current');
            $(".message").hide();
            $('#winPreview').show();
        }
    });

    $(document).on('click', '#btnPreview', function() {
        if (!$('#throughPreview').hasClass('playing')) {
            $("#throughPreview").addClass('playing');
            $(this).parent('li').html('<span class="large1" id="previewNow">チャレンジする</span>');
            $("#instantWinImage").find('img').attr('src', $('#animationData').data('ani'));
            setTimeout(EditInstantWinActionService.resetAnimation, 6300);
        }
    });

    $(document).on('click', '#throughPreview', function() {
        if (!$(this).hasClass('playing')) {
            $(this).addClass('playing');
            $(".message").hide();
            $('#animationPreview').show();
            $("#btnPreview").parent('li').html('<span class="large1" id="previewNow">チャレンジする</span>');
            $("#instantWinImage").find('img').attr('src', $('#animationData').data('ani'));
            setTimeout(EditInstantWinActionService.resetAnimation, 6300);
            if ($('#winTab').hasClass('current')) {
                setTimeout(EditInstantWinActionService.previewWinImage, 6300);
            }
            if ($('#loseTab').hasClass('current')) {
                setTimeout(EditInstantWinActionService.previewLoseImage, 6300);
            }
        }
    });

    $('.labelTitleWin').change(function(){
        $(this).parents('li').find('.actionImageWin').removeAttr('disabled');
    });

    $('.labelTitleLose').change(function(){
        $(this).parents('li').find('.actionImageLose').removeAttr('disabled');
    });

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $('.jsDate').datepicker({
        minDate: new Date()
    });
});
