var DrawPieChartService = (function(){
    return{
        displayPieChart: function(data, dashboard_type){
            if($('div[data-dashboard_type="' + dashboard_type + '"]').children('svg').length) {
                var svg = d3.select('div[data-dashboard_type="' + dashboard_type + '"]').select('svg');
            } else {
                var svg = DashboardListService.getSvgElement(dashboard_type);
            }
            var data_list = [];
            var max_value = 0;
            var max_ratio = 0;
            var svg_width = $('div[data-dashboard_type="' + dashboard_type + '"]').children('svg').width();
            var svg_height = $('div[data-dashboard_type="' + dashboard_type + '"]').children('svg').height();
            var circle_center_x = svg_width*0.8;
            var circle_center_y = svg_height/2;

            // 最も割合が大きい値を取得する
            $.each(data, function(i, value){
                // 未登録の場合があり、それは最後のキーに来ることを前提としている
                if(i != -1) {
                    data_list.push(value['cnt']);
                    if(max_value < value['cnt']) {
                        max_value = value['cnt'];
                        max_ratio = value['ratio'];
                    }
                }
            });

            var y_cordinate = 0;
            if(dashboard_type == 5) {
                var each_y_cordinate = 20;
            } else {
                var each_y_cordinate = 25;
            }
            $.each(data, function(i,value) {
                var text_g = svg.append('g')
                    .attr("transform", "translate(" + 0 + "," + 15 + ")")
                    .attr("data-code", i)
                    .on("mouseover", function(){
                        if(max_value != 0) {
                            $(this).parent('svg').children('text').remove();
                            svg.append("text")
                                .attr("x", circle_center_x)
                                .attr("y", circle_center_y + 10)
                                .style("fill", '#2CB395')
                                .text(value['ratio'] + '%')
                                .attr("text-anchor", "middle")
                                .attr("font-family", "sans-serif")
                                .attr("font-size", "26px");

                            svg.selectAll('g')
                                .selectAll('text')
                                .style("fill", '#333333');

                            d3.select(this)
                                .selectAll('text')
                                .style("fill", '#2CB395');

                            svg.selectAll('g[data-code]')
                                .selectAll('path')
                                .style("fill", '#E6E6E6');

                            svg.selectAll('g[data-code="' + $(this).attr('data-code') + '"]')
                                .selectAll('path')
                                .style("fill", '#2CB395');
                        }
                    });

                // 選択肢表記
                text_g.append("text")
                    .attr("x", 5)
                    .attr("y", y_cordinate)
                    .style("fill", function(){
                        if(value['cnt'] == max_value && max_value > 0 && i != -1) {
                            return '#2CB395';
                        } else {
                            return '#333333';
                        }
                    })
                    .text(value['summary_name'])
                    .attr("text-anchor", "start")
                    .attr("font-family", "sans-serif")
                    .attr("font-size", "11px")
                    .append('title')
                    .text(value['name']);

                // 割合表記
                text_g.append("text")
                    .attr("x", svg_width*0.55)
                    .attr("y", y_cordinate)
                    .style("fill", function(){
                        if(value['cnt'] == max_value && max_value > 0 && i != -1) {
                            return '#2CB395';
                        } else {
                            return '#333333';
                        }
                    })
                    .text(Brandco.helper.conversion_comma3(value['cnt']) + '(' + value['ratio'] + '%)')
                    .attr("text-anchor", "end")
                    .attr("font-family", "sans-serif")
                    .attr("font-size", "11px");
                y_cordinate += each_y_cordinate;
            });

            // 全部が0の時は暫定で値を入れる
            if(max_value == 0 && data[-1]['cnt'] == 0) {
                data[-1]['cnt'] = 1;
            }

            var arc_data_list = [];
            $.each(data, function(i,value) {
                arc_data_list.push({legend:i, value:value['cnt']});
            });

            var arc = d3.svg.arc()
                .outerRadius(55)
                .innerRadius(45);

            var pie = d3.layout.pie()
                .sort(null)
                .value(function(d) { return d.value; });

            // 円グラフの描画
            var g = svg
                .append('g')
                .attr("transform", "translate(" + circle_center_x + "," + circle_center_y + ")")
                .selectAll('.arc')
                .data(pie(arc_data_list))
                .enter()
                .append('g')
                .attr('class', 'arc')
                .attr('data-code', function(d) {
                    return d.data.legend;
                });

            // 円グラフの色分け
            g.append("path")
                .attr("d", arc)
                .style("fill", function(d, i){
                    if(d.value == max_value && arc_data_list[i].legend != -1) {
                        return '#2CB395';
                    } else {
                        return '#E6E6E6';
                    }
                });

            // 円グラフの中心の割合表記
            if(max_value > 0) {
                svg.append("text")
                    .attr("x", circle_center_x)
                    .attr("y", circle_center_y + 10)
                    .style("fill", '#2CB395')
                    .text(max_ratio + '%')
                    .attr("text-anchor", "middle")
                    .attr("font-family", "sans-serif")
                    .attr("font-size", "26px");
            }
        },
        displaySexPieChart: function(data, dashboard_type){
            // 対象人数が0人の場合は、円グラフへの描画のために未登録人数に暫定値を入れる
            if(data['f']['cnt'] == 0 && data['m']['cnt'] == 0 && data['n']['cnt'] == 0) {
                data['n']['cnt'] = 1;
            }

            var data_list = [
                {legend:'f', value:data['f']['cnt'], color:'#F58C8C'},
                {legend:'m', value:data['m']['cnt'], color:'#74A8E4'},
                {legend:'n', value:data['n']['cnt'], color:'#E6E6E6'}
            ];
            var svg = DashboardListService.getSvgElement(dashboard_type);
            var arc = d3.svg.arc()
                .outerRadius(55)
                .innerRadius(45);

            var pie = d3.layout.pie()
                .sort(null)
                .value(function(d) { return d.value; });
            // 円の中心
            var center_x = 144,
                center_y = 59;
            var g = svg
                .append('g')
                .attr("transform", "translate(" + center_x + "," + center_y + ")")
                .selectAll('.arc')
                .data(pie(data_list))
                .enter()
                .append('g')
                .attr('class', 'arc');

            g.append("path")
                .attr("d", arc)
                .style("fill", function(d) { return d.data.color; });

            // 円の中心の割合
            if(data['f']['cnt'] > data['m']['cnt']) {
                svg.append("text")
                    .attr("x", center_x)
                    .attr("y", center_y + 10)
                    .style("fill", '#F58C8C')
                    .text(data['f']['ratio'] + '%')
                    .attr("text-anchor", "middle")
                    .attr("font-family", "sans-serif")
                    .attr("font-size", "26px")
                    .attr("class", "display_f_ratio");
            } else if(data['m']['cnt'] >= data['f']['cnt'] && data['m']['cnt'] > 0) {
                svg.append("text")
                    .attr("x", center_x)
                    .attr("y", center_y + 10)
                    .style("fill", '#74A8E4')
                    .text(data['m']['ratio'] + '%')
                    .attr("text-anchor", "middle")
                    .attr("font-family", "sans-serif")
                    .attr("font-size", "26px")
                    .attr("class", "display_m_ratio");
            }

            // 男性の人数、前日比を出力するグループ
            var m_g = svg
                .append('g')
                .attr("transform", "translate(" + 10 + "," + 0 + ")")
                .on("mouseover", function(){
                    if($('.display_f_ratio').length) {
                        $('.display_f_ratio').remove();
                        svg.append("text")
                            .attr("x", center_x)
                            .attr("y", center_y + 10)
                            .style("fill", '#74A8E4')
                            .text(data['m']['ratio'] + '%')
                            .attr("text-anchor", "middle")
                            .attr("font-family", "sans-serif")
                            .attr("font-size", "25px")
                            .attr("class", "display_m_ratio");
                    }
                });

            m_g.append("text")
                .attr("x", 25)
                .attr("y", 100)
                .style("fill", '#74A8E4')
                .text(Brandco.helper.conversion_comma3(data['m']['cnt']))
                .attr("text-anchor", "middle")
                .attr("font-family", "sans-serif")
                .attr("font-size", "18px");

            m_g.append("image")
                .attr("x", 0)
                .attr("y", 20)
                .attr("width", 50)
                .attr("height", 50)
                .attr("text-anchor", "middle")
                .attr("xlink:href", $('input[name="static_url"]').val() + "/img/dashboard/iconMale.png");

            // 女性の人数、前日比を出力するグループ
            var f_g = svg
                .append('g')
                .attr("transform", "translate(" + 225 + "," + 0 + ")")
                .on("mouseover", function(){
                    if($('.display_m_ratio').length) {
                        $('.display_m_ratio').remove();
                        svg.append("text")
                            .attr("x", center_x)
                            .attr("y", center_y + 10)
                            .style("fill", '#F58C8C')
                            .text(data['f']['ratio'] + '%')
                            .attr("text-anchor", "middle")
                            .attr("font-family", "sans-serif")
                            .attr("font-size", "25px")
                            .attr("class", "display_f_ratio");
                    }
                });

            f_g.append("text")
                .attr("x", 25)
                .attr("y", 100)
                .style("fill", '#F58C8C')
                .text(Brandco.helper.conversion_comma3(data['f']['cnt']))
                .attr("text-anchor", "middle")
                .attr("font-family", "sans-serif")
                .attr("font-size", "18px");

            f_g.append("image")
                .attr("x", 0)
                .attr("y", 20)
                .attr("width", 50)
                .attr("height", 50)
                .attr("xlink:href", $('input[name="static_url"]').val() + "/img/dashboard/iconFemale.png");
        }
    }
})();
