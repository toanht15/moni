var DrawLineChartService = (function(){
    return{
        displayLineChart: function (data, dashboard_type) {
            var data_list = [];
            $.each(data, function (i, value) {
                // x:日付 y:人数 x:増分(減分)
                data_list.push({x: i, y: value[0], z: value[1]});
            });

            var svg = DashboardListService.getSvgElement(dashboard_type);
            // 固定値でグラフのX座標とY座標を取る
            var line_left_x = 185,
                line_right_x = 920,
                line_top_y = 50,
                line_bottom_y = 150;

            if(data_list.length < 9 && data_list.length > 1) {
                var line_width = ((line_right_x - line_left_x) / 8) * data_list.length;
            } else {
                var line_width = line_right_x - line_left_x;
            }
            var line_height = line_bottom_y - line_top_y;
            var x_start = line_left_x + (line_right_x - line_left_x - line_width) / 2;
            var rect_g = svg.append("g")
                .attr({
                    transform: "translate(" + x_start + "," + line_top_y + ")"
                })
                .attr("class","d3GraphGroup");

            var x = d3.time.scale()
                .range([0, line_width]);

            var y = d3.scale.linear()
                .range([line_bottom_y, line_top_y]);

            x.domain([new Date(data_list[0].x), new Date(data_list[data_list.length - 1].x)]);
            y.domain(d3.extent(data_list, function(d) { return d.y; }));

            var line = high_line = d3.svg.line()
                .x(function (d) {
                    return d[0];
                })
                .y(function (d) {
                    return d[1];
                });

            var bisectDate = d3.bisector(function(d) {
                return new Date(d.x); }).left;
            if(data_list.length > 1) {
                rect_g.append("rect")
                    .attr("width", line_width)
                    .attr("height", line_height)
                    .attr("fill", "#ffffff")
                    .on("mousemove", function () {
                        $('[data-date]').hide();
                        var x0 = x.invert(d3.mouse($('.d3GraphGroup')[0])[0]),
                            i = bisectDate(data_list, x0, 1),
                            d0 = data_list[i - 1],
                            d1 = data_list[i],
                            d = x0 - new Date(d0.x) > new Date(d1.x) - x0 ? d1 : d0;
                        $('[data-date="' + d.x + '"][data-type="' + dashboard_type + '"]').show();
                    })
                    .on("mouseout", function () {
                        $('[data-date]').hide();
                    });
            } else {
                rect_g.append("rect")
                    .attr("width", line_width)
                    .attr("height", line_height)
                    .attr("fill", "#ffffff")
                    .on("mousemove", function () {
                        if(d3.mouse($('.d3GraphGroup')[0])[0] >= 350 && d3.mouse($('.d3GraphGroup')[0])[0] <= 372) {
                                $('[data-date][data-type="' + dashboard_type + '"]').show();
                        } else {
                            $('[data-date]').hide();
                        }
                    })
                    .on("mouseout", function () {
                        $('[data-date]').hide();
                    });
            }

            // 点線(上部)
            svg.append("path")
                .attr("d", line(
                    [
                        [line_left_x, line_top_y],
                        [line_right_x, line_top_y]
                    ]))
                .style("stroke", "#DDD")
                .attr("stroke-width", "2px")
                .attr("stroke-dasharray", "1, 2");

            // 点線(下部)
            svg.append("path")
                .attr("d", line(
                    [
                        [line_left_x, line_bottom_y],
                        [line_right_x, line_bottom_y]
                    ]))
                .style("stroke", "#DDD")
                .attr("stroke-width", "2px")
                .attr("stroke-dasharray", "1, 2");

            var yScale = d3.scale.linear()
                .domain([
                    d3.min(data_list, function (d) {
                        return d.y;
                    }),
                    d3.max(data_list, function (d) {
                        return d.y;
                    })])
                .range([line_bottom_y - 10, line_top_y + 10]);

            if (data_list.length > 1) {
                var dayWidth = line_width / (data_list.length - 1);
                var line = d3.svg.line()
                    .x(function (d, i) {
                        return i * dayWidth + x_start;
                    })
                    .y(function (d) {
                        return yScale(d.y);
                    });
                svg.append("path")
                    .datum(data_list)
                    .attr("class", "line")
                    .attr("d", line)
                    .style("fill", 'none')
                    .style("stroke", '#2CB395')
                    .style("stroke-width", '2px');
            } else {
                svg.append("circle")
                    .attr("cx", function () {
                        return line_width / 2 + x_start;
                    })
                    .attr("cy", function () {
                        return line_height;
                    })
                    .attr("fill", "#2CB395")
                    .attr("r", 5);
            }

            var high_line_x = 0;
            rect_g.selectAll(".high_line")
                .data(data_list)
                .enter()
                .append("path")
                .attr("class", "high_line")
                .attr("d", function(d,i) {
                    if (data_list.length > 1) {
                        high_line_x = i * dayWidth;
                    } else {
                        high_line_x = line_width / 2;
                    }
                    return high_line(
                        [
                            [high_line_x, 0],
                            [high_line_x, line_height]
                        ]);
                })
                .style("stroke", "#CCCCCC")
                .attr("stroke-width", "1px")
                .style("display","none")
                .attr("data-date", function (d) {
                    return d.x;
                })
                .attr("data-type", dashboard_type);

            var target_graph = $('.grafData[data-dashboard_type=' + dashboard_type + ']').prev('h1').html().split("(")[0];

            var userAgent = window.navigator.userAgent.toLowerCase();
            svg.selectAll(".high_text")
                .data(data_list)
                .enter()
                .append("text")
                .attr("class", "high_text")
                .style("display","none")
                .style("fill", '#666')
                .text(function(d) {
                    return "日付:" + d.x + "　" + target_graph + ":" + Brandco.helper.conversion_comma3(d.y) + "　前日比:" + d.z;
                })
                .attr("x", function(d,i) {
                    if(userAgent.indexOf("firefox") == -1) {
                        if(data_list.length > 1) {
                            var head_x = i * dayWidth + x_start;
                            if(head_x <= line_left_x + $(this).width()/2) {
                                return line_left_x + 7.5;
                            } else if(head_x + $(this).width()/2 > line_right_x) {
                                return line_right_x - $(this).width() + 37;
                            } else {
                                return head_x - $(this).width()/2 + 15;
                            }
                        } else {
                            return line_width / 2 + x_start - $(this).width()/2 + 15;
                        }
                    } else {
                        return 430;
                    }
                })
                .attr("y", line_top_y - 10)
                .attr("text-anchor", "start")
                .attr("font-family", "sans-serif")
                .attr("font-size", "11px")
                .style("background", "#eee")
                .attr("data-date", function (d) {
                    return d.x;
                })
                .attr("data-type", dashboard_type);
            if(userAgent.indexOf("chrome") != -1) {
                svg.selectAll(".high_back")
                    .data(data_list)
                    .enter()
                    .append("rect")
                    .attr("class","high_back")
                    .style("display","none")
                    .attr("width", function(d) {
                        return $('.high_text[data-date="' + d.x + '"][data-type="' + dashboard_type + '"]').width() + 15;
                    })
                    .attr("height", 21)
                    .attr("x", function(d) {
                        return $('.high_text[data-date="' + d.x + '"][data-type="' + dashboard_type + '"]').attr('x') - 7.5;
                    })
                    .attr("y", line_top_y - 25)
                    .attr("fill", "none")
                    .attr("stroke-width", 0.2)
                    .attr("stroke","#666")
                    .attr("data-date", function (d) {
                        return d.x;
                    })
                    .attr("data-type", dashboard_type);
            }


            svg.selectAll(".high_circle")
                .data(data_list)
                .enter()
                .append("circle")
                .attr("class", "high_circle")
                .attr("cx", function (d, i) {
                    if(data_list.length > 1) {
                        return i * dayWidth + x_start;
                    } else {
                        return line_width / 2 + x_start;
                    }
                })
                .attr("cy", function (d) {
                    if(data_list.length > 1) {
                        return yScale(d.y);
                    } else {
                        return line_height;
                    }
                })
                .attr("fill", "#777")
                .attr("r", 7)
                .attr("display", 'none')
                .attr("data-date", function (d) {
                    return d.x;
                })
                .attr("data-type", dashboard_type)
                .attr("stroke-width",2)
                .attr("stroke","#ffffff");

            // 累計・期間の日付
            svg.append("text")
                .attr("x", 10)
                .attr("y", 53)
                .style("fill", '#000000')
                .text($('.dashboardFliter').children('[name="title_date"]').val())
                .attr("text-anchor", "start")
                .attr("font-family", "sans-serif")
                .attr("font-size", "11px");

            if (dashboard_type == 1) {
                if ($('input[name="date_type"]:checked').val() == 2) {
                    if(data_list.length > 1) {
                        var difference = data_list[data_list.length - 1].y - data_list[0].y + Number(data_list[0].z);
                        var count_text = (difference >= 0 ? '+' : '-') + Brandco.helper.conversion_comma3(difference);
                    } else {
                        var count_text = data_list[0].z;
                    }
                } else {
                    var count_text = Brandco.helper.conversion_comma3(data_list[data_list.length - 1].y);
                }
            } else {
                var sum_pv_count = 0;
                $.each(data_list, function (i,value) {
                    sum_pv_count += value.y;
                });
                if ($('input[name="date_type"]:checked').val() == 2) {
                    var count_text = '+' + Brandco.helper.conversion_comma3(sum_pv_count);
                } else {
                    var count_text = Brandco.helper.conversion_comma3(sum_pv_count);
                }
            }
            svg.append("text")
                .attr("x", 10)
                .attr("y", 85)
                .style("fill", '#000000')
                .text(count_text)
                .attr("text-anchor", "start")
                .attr("font-family", "sans-serif")
                .attr("font-size", "25px");

            // 暫定if文
            if ($('input[name="date_type"]:checked').val() == 1) {
                var difference = data_list.length > 1 ? data_list[data_list.length - 1].y - data_list[data_list.length - 2].y : data_list[0].y;
                var compare = (difference >= 0 ? '+' : '-') + Brandco.helper.conversion_comma3(Number(difference >= 0 ? difference : difference * (-1)));
                var compare_text = '※前日比：';
                svg.append("text")
                    .attr("x", 10)
                    .attr("y", 105)
                    .style("fill", '#333333')
                    .text(compare_text)
                    .attr("text-anchor", "start")
                    .attr("font-family", "sans-serif")
                    .attr("font-size", "12px");

                svg.append("text")
                    .attr("x", 73)
                    .attr("y", 105)
                    .style("fill", function () {
                        return difference >= 0 ? '#D80000' : '#006DD9';
                    })
                    .text(compare)
                    .attr("text-anchor", "start")
                    .attr("font-family", "sans-serif")
                    .attr("font-size", "12px");
            }
            var span = data_list.length <= 15 ? 1 : Math.floor((data_list.length + 1) / 6);
            $.each(data_list, function (i, value) {
                if (i % span == 0) {
                    var display_date = new Date(value.x);
                    svg.append("text")
                        .attr("x", function () {
                            if (data_list.length > 1) {
                                return i * dayWidth + x_start;
                            } else {
                                return line_width / 2 + line_left_x;
                            }
                        })
                        .attr("y", line_bottom_y + 20)
                        .style("fill", '#333')
                        .text((display_date.getMonth() + 1) + '/' + display_date.getDate())
                        .attr("text-anchor", "middle")
                        .attr("font-family", "sans-serif")
                        .attr("font-size", "11px");
                }
            });
        },
    }
})();
