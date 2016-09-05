var DrawBarChartService = (function(){
    return{
        displayBarChart: function(all_fan_count, data, dashboard_type){
            if($('div[data-dashboard_type="' + dashboard_type + '"]').children('svg').length) {
                var svg = d3.select('div[data-dashboard_type="' + dashboard_type + '"]').select('svg');
            } else {
                var svg = DashboardListService.getSvgElement(dashboard_type);
            }
            var data_list = DashboardListService.getDataList(data);
            var barWidth = 250;
            var barScale = barWidth / (all_fan_count == 0 ? 1 : all_fan_count);
            var yBarSize = 31; // 棒グラフの縦幅
            var y_cordinate = 35;

            svg.append('g')
                .selectAll('rect')
                .data(data_list)
                .enter()
                .append('rect')
                .attr('x', 165)
                .attr('y', function(d,i){
                    return i * yBarSize + 24.2;
                })
                .attr('width', function(d){
                    return (d[1] * barScale) + 'px';
                })
                .attr('height', '15')
                .style('fill', '#2CB395');

            svg.append("g")
                .selectAll('rect')
                .data(data_list)
                .enter()
                .append('rect')
                .attr('x', function(d){
                    return 165 + d[1] * barScale;
                })
                .attr('y', function(d,i){
                    return i * yBarSize + 24.2;
                })
                .attr('width', function(d){
                    return (barWidth - d[1] * barScale) + 'px';
                })
                .attr('height', '15')
                .style('fill', '#E6E6E6');

            $.each(data, function(i,value) {
                svg.append("text")
                    .attr("x", 10)
                    .attr("y", y_cordinate)
                    .style("fill", '#333333')
                    .text(value['summary_name'])
                    .attr("text-anchor", "start")
                    .attr("font-family", "sans-serif")
                    .attr("font-size", "11px")
                    .append('title')
                    .text(value['name']);

                svg.append("text")
                    .attr("x", 415)
                    .attr("y", y_cordinate - 15)
                    .style("fill", '#333333')
                    .text(Brandco.helper.conversion_comma3(value['cnt']) + '(' + value['ratio'] + '%)')
                    .attr("text-anchor", "end")
                    .attr("font-family", "sans-serif")
                    .attr("font-size", "10px");
                y_cordinate += yBarSize;
            });
        },
        displayAreaBarChart: function(all_fan_count, data, dashboard_type){
            var svg = DashboardListService.getSvgElement(dashboard_type);
            var barWidth = 228;
            var data_list = DashboardListService.getDataList(data);
            var barScale = barWidth / (all_fan_count == 0 ? 1 : all_fan_count);
            var yBarSize = 31; // 棒グラフの縦幅

            var data_list_small = [];
            $.each(data_list, function(i, value) {
                data_list_small[i] = value;
                if(i == 7) {
                    return false;
                }
            });

            svg.append("g")
                .selectAll('rect')
                .data(data_list_small)
                .enter()
                .append('rect')
                .attr('x', 60)
                .attr('y', function(d,i){
                    return i * yBarSize + 24.2;
                })
                .attr('width', function(d){
                    return (d[1] * barScale) + 'px';
                })
                .attr('height', '15')
                .style('fill', '#2CB395');

            svg.append("g")
                .selectAll('rect')
                .data(data_list_small)
                .enter()
                .append('rect')
                .attr('x', function(d){
                    return 60 + d[1] * barScale;
                })
                .attr('y', function(d,i){
                    return i * yBarSize + 24.2;
                })
                .attr('width', function(d){
                    return (barWidth - d[1] * barScale) + 'px';
                })
                .attr('height', '15')
                .style('fill', '#E6E6E6');

            var y_cordinate = 35;
            $.each(data, function(i,value) {
                svg.append("text")
                    .attr("x", 10)
                    .attr("y", y_cordinate)
                    .style("fill", '#333333')
                    .text(value['name'])
                    .attr("text-anchor", "start")
                    .attr("font-family", "sans-serif")
                    .attr("font-size", "11px");

                svg.append("text")
                    .attr("x", 288)
                    .attr("y", y_cordinate - 13)
                    .style("fill", '#333333')
                    .text(Brandco.helper.conversion_comma3(value['cnt']) + '(' + value['ratio'] + '%)')
                    .attr("text-anchor", "end")
                    .attr("font-family", "sans-serif")
                    .attr("font-size", "10px");
                y_cordinate += yBarSize;
                if(i == 7) {
                    return false;
                }
            });

            // 以下はモーダル側の処理
            var modal_svg = DashboardListService.getModalSvgElement('prefecture_modal');
            var modalBarScale = 200 / (all_fan_count == 0 ? 1 : all_fan_count);
            var yModalBarSize = 27;
            var shift_x_position = 290;
            modal_svg.append("g")
                .selectAll('rect')
                .data(data_list)
                .enter()
                .append('rect')
                .attr('x', function(d,i) {
                    return Math.floor(i/16) * shift_x_position + 60;
                })
                .attr('y', function(d,i){
                    return i * yModalBarSize - Math.floor(i/16) * 432 + 24.2;
                })
                .attr('width', function(d){
                    return (d[1] * modalBarScale) + 'px';
                })
                .attr('height', '13')
                .style('fill', '#2CB395');

            modal_svg.append("g")
                .selectAll('rect')
                .data(data_list)
                .enter()
                .append('rect')
                .attr('x', function(d,i){
                    return (Math.floor(i/16) * shift_x_position + 60) + d[1] * modalBarScale;
                })
                .attr('y', function(d,i){
                    return i * yModalBarSize - Math.floor(i/16) * 432 + 24.2;
                })
                .attr('width', function(d){
                    return (200 - d[1] * modalBarScale) + 'px';
                })
                .attr('height', '13')
                .style('fill', '#E6E6E6');

            y_cordinate = 35;
            $.each(data, function(i,value) {
                modal_svg.append("text")
                    .attr("x", function() {
                        return Math.floor(i/16) * shift_x_position + 10;
                    })
                    .attr("y", function() {
                        return y_cordinate - Math.floor(i/16) * 432;
                    })
                    .style("fill", '#333333')
                    .text(value['name'])
                    .attr("text-anchor", "start")
                    .attr("font-family", "sans-serif")
                    .attr("font-size", "11px");

                modal_svg.append("text")
                    .attr("x", function() {
                        return Math.floor(i/16) * shift_x_position + 260;
                    })
                    .attr("y", function() {
                        return y_cordinate - 13 - Math.floor(i/16) * 432;
                    })
                    .style("fill", '#333333')
                    .text(Brandco.helper.conversion_comma3(value['cnt']) + '(' + value['ratio'] + '%)')
                    .attr("text-anchor", "end")
                    .attr("font-family", "sans-serif")
                    .attr("font-size", "10px");
                y_cordinate += yModalBarSize;
            });
        },
        displaySnsBarChart: function(all_fan_count, data, dashboard_type){
            var svg = DashboardListService.getSvgElement(dashboard_type);
            
            var data_list = DashboardListService.getDataList(data);
            var y_cordinate = 22.5;
            var barScale = 243 / (all_fan_count == 0 ? 1 : all_fan_count);

            if(data_list.length == 7) {
                var yBarSize = 39; // 棒グラフの縦幅
            } else {
                var yBarSize = 33; // 棒グラフの縦幅
            }

            // social_media_idに対応するようにアイコンのパスを指定
            var color = ["",
                "#305097",
                "#55ACEE",
                "#00C300",
                "#3F729B",
                "#FF0033",
                "#DD4B39",
                "#3695D6",
                "#0077b5"
            ];

            var icon = ["",
                "/img/sns/iconSnsFB2.png",
                "/img/sns/iconSnsTW2.png",
                "/img/sns/iconSnsLN2.png",
                "/img/sns/iconSnsIG2.png",
                "/img/sns/iconSnsYH2.png",
                "/img/sns/iconSnsGP2.png",
                "/img/thirdParty/iconGdo2.png",
                "/img/sns/iconSnsIN2.png"
            ];

            var mail_color = "#A3A3A3";
            var mail_icon = "/img/dashboard/iconMail.png";
            var mail_y = 270;

            svg.append("g")
                .selectAll('rect')
                .data(data_list)
                .enter()
                .append('rect')
                .attr('x', 45)
                .attr('y', function(d,i){
                    return d[0] == -1 ? mail_y : i * yBarSize + y_cordinate;
                })
                .attr('width', function(d){
                    return (d[1] * barScale) + 'px';
                })
                .attr('height', '15')
                .attr("fill", function(d,i){
                    return d[0] == -1 ? mail_color : color[d[0]];
                });

            svg.append("g")
                .attr("fill", "#E6E6E6")
                .selectAll('rect')
                .data(data_list)
                .enter()
                .append('rect')
                .attr('x', function(d){
                    return 45 + d[1] * barScale;
                })
                .attr('y', function(d,i){
                    return d[0] == -1 ? mail_y : i * yBarSize + y_cordinate;
                })
                .attr('width', function(d){
                    return (243 - d[1] * barScale) + 'px';
                })
                .attr('height', '15');

            $.each(data, function(i,value) {
                svg.append("image")
                    .attr("x", 10)
                    .attr("y", function() {
                        return i == -1 ? mail_y : y_cordinate;
                    })
                    .attr("width", 15)
                    .attr("height", 15)
                    .attr("xlink:href", function(){
                        return i == -1 ? ($('input[name="static_url"]').val() + mail_icon) : ($('input[name="static_url"]').val() + icon[i]);
                    });

                svg.append("text")
                    .attr("x", 288)
                    .attr("y", function() {
                        return i == -1 ? mail_y - 4 : y_cordinate - 4;
                    })
                    .style("fill", '#333333')
                    .text(Brandco.helper.conversion_comma3(value['cnt']) + '(' + value['ratio'] + '%)')
                    .attr("text-anchor", "end")
                    .attr("font-family", "sans-serif")
                    .attr("font-size", "11px");
                y_cordinate += yBarSize;
            });

            svg.append("text")
                .attr("x", 2)
                .attr("y", 260)
                .style("fill", '#333333')
                .text('未連携')
                .attr("text-anchor", "start")
                .attr("font-family", "sans-serif")
                .attr("font-size", "11px");

        }
    }
})();
