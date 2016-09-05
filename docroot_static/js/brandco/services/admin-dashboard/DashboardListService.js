var DashboardListService = (function(){
    return{
        goMason: function(){
            $('.jsMasonry').masonry({
                // options
                itemSelector: '.jsPanel',
                gutter: 12,
                columnWidth: 150
            });
        },
        displayDashboard: function(dashboard_type){
            var data = {date_type:$('input[name="date_type"]:checked').val(),
                dashboard_type:dashboard_type,
                all_fan_count:$('input[name="all_fan_count"]').val(),
                summary_date_type:$('#selectSummaryDate').val(),
                term_date_type:$('#selectTermDate').val(),
                summary_date:$('input[name="summary_date"]').val(),
                from_date:$('input[name="from_date"]').val(),
                to_date:$('input[name="to_date"]').val()
            };
            var param = {
                data: data,
                type: 'GET',
                url: $('input[name="get_dashboard_info_url"]').val(),
                async: true,
                success: function(json) {
                    if (json.result === "ok") {
                        $.each(json.data, function (d_type, value) {
                            var target_dashboard_type = $('div[data-dashboard_type="' + d_type + '"]');
                            if(target_dashboard_type.children('.loading').length > 0) {
                                target_dashboard_type.children('.loading').remove();
                            }
                            if(d_type == 1) {
                                DrawLineChartService.displayLineChart(value, d_type);
                            }
                            if(d_type == 2) {
                                DrawBarChartService.displaySnsBarChart($('input[name="all_fan_count"]').val(), value, d_type);
                            }
                            if(d_type == 3) {
                                DrawPieChartService.displaySexPieChart(value, d_type);
                            }
                            if(d_type == 4) {
                                DrawBarChartService.displayAreaBarChart($('input[name="all_fan_count"]').val(), value, d_type);
                                target_dashboard_type.next().children('a').show();
                            }
                            if(d_type == 5) {
                                DrawPieChartService.displayPieChart(value, d_type);
                            }
                            if(String(d_type).match(/^6\//)) {
                                if(target_dashboard_type.data('multi_answer') == 1) {
                                    DrawBarChartService.displayBarChart($('input[name="all_fan_count"]').val(), value, d_type);
                                } else {
                                    DrawPieChartService.displayPieChart(value, d_type);
                                }
                            }
                            if(d_type == 7) {
                                // PVはまだ入っていないパターンがある
                                if(value) {
                                    DrawLineChartService.displayLineChart(value, d_type);
                                }
                            }
                            target_dashboard_type.removeClass('jsDisplaySubject');
                        });
                    } else {
                        alert("エラーが発生しました");
                        $('div[data-dashboard_type]').children('div').remove();
                        $('div[data-dashboard_type]').removeClass('jsDisplaySubject');
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param, false);
        },
        getSvgElement :function(dashboard_type) {
            var target = $('div[data-dashboard_type="' + dashboard_type + '"]');
            var parent_section = target.parent('section');
            var padding_width = Number(target.css('margin-left').replace('px','')) + Number(target.css('margin-right').replace('px',''));
            var padding_height = Number(target.css('margin-left').replace('px','')) + Number(target.css('margin-right').replace('px',''));
            if(target.next('.moreData').length) {
                var moreData_heght = target.next('.moreData').height() + Number(target.next('.moreData').css('margin-top').replace('px','')) + Number(target.next('.moreData').css('margin-bottom').replace('px',''));
            } else {
                var moreData_heght = 0;
            }
            var svg = d3.select('div[data-dashboard_type="' + dashboard_type + '"]')
                .append('svg')
                .attr('width',parent_section.width() - padding_width)
                .attr('height',parent_section.height() - target.prev('h1').outerHeight() - padding_height - moreData_heght);
            return svg;
        },
        getModalSvgElement :function(target) {
            var svg = d3.select('div[data-modal="' + target + '"]')
                .append('svg')
                .attr('width',860)
                .attr('height',460);
            return svg;
        },
        getDataList :function(data) {
            var data_list = [];
            $.each(data, function (i, value) {
                data_list.push([i, value['cnt']]);
            });
            return data_list;
        }
    };
})();

$(document).ready(function(){
    DashboardListService.goMason(true);

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsSummaryDate").datepicker({minDate:$('input[name="min_date"]').val(), maxDate:$('input[name="max_date"]').val()});
    $(".jsTermDate").datepicker({minDate:$('input[name="min_date"]').val(), maxDate:$('input[name="max_date"]').val()});

    $('.jsConditionApply').on('click', function() {
        var url = $(this).data('redirect_url') + '?date_type=' + $('input[name="date_type"]:checked').val();
        if($('input[name="date_type"]:checked').val() == 1) {
            url += '&summary_date_type=' + $('#selectSummaryDate').val();
            if($('#selectSummaryDate').val() == 3) {
                url += '&summary_date=' + $('input[name="summary_date"]').val();
            }
        } else {
            url += '&term_date_type=' + $('#selectTermDate').val();
            if($('#selectTermDate').val() == 7) {
                url += '&from_date=' + $('input[name="from_date"]').val() + '&to_date=' + $('input[name="to_date"]').val();
            }
        }
        window.location = url;
    });

    $('input[name="date_type"]').on('change',function() {
        if($(this).val() == 1) {
            $('.jsSummaryDate').show();
            $('.jsTermDate').hide();
            if($('#selectSummaryDate').val() == 3) {
                $('input[name="summary_date"]').parent('span').show();
            } else {
                $('input[name="summary_date"]').parent('span').hide();
            }
        } else {
            $('.jsSummaryDate').hide();
            $('.jsTermDate').show();
            if($('#selectTermDate').val() == 7) {
                $('input[name="from_date"]').parent('span').show();
                $('.jsFromToMark').show();
            } else {
                $('input[name="from_date"]').parent('span').hide();
                $('.jsFromToMark').hide();
            }
        }
    });

    $('#selectSummaryDate').on('change',function() {
        if($(this).val() == 3) {
            $('input[name="summary_date"]').parent('span').show();
        } else {
            $('input[name="summary_date"]').parent('span').hide();
        }
    });

    $('#selectTermDate').on('change',function() {
        if($(this).val() == 7) {
            $('input[name="from_date"]').parent('span').show();
        } else {
            $('input[name="from_date"]').parent('span').hide();
        }
    });

    if(!$('input[name="date_error"]').val()) {
        $('div.jsDisplaySubject').each(function() {
            DashboardListService.displayDashboard($(this).data('dashboard_type'));
        });
    } else {
        $('div.jsDisplaySubject').each(function() {
            $(this).children('.loading').remove();
        });
    }

    $('.jsOpenModal').click(function() {
        Brandco.unit.showModal(this);
    })
});
