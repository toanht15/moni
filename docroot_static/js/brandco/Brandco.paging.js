Brandco.paging = function (startPage, currentPage, endPage, target, pageContainer, itemClass) {
    if (!(this instanceof Brandco.paging)) return new Brandco.paging(startPage, currentPage, endPage, target, pageContainer, itemClass);
    this.startPage = startPage;
    this.currentPage = currentPage;
    this.target = target;
    this.pageContainer = pageContainer;
    this.itemClass = itemClass;
    this.totalPage = $('#'+target).data('totalpage');
    this.endPage = (this.totalPage>endPage)?endPage:this.totalPage;
    this.pageNum = this.endPage - this.startPage;
    this.pageLimit = $('#'+target).data('pagelimit');
    this.totalItem = $('#'+target).data('totalitem');
    this.writeHTML = function() {
        var endItem = (this.currentPage*this.pageLimit>this.totalItem)?this.totalItem:this.currentPage*this.pageLimit,
            html = '<p>'+this.totalItem+'件中'+((this.currentPage-1)*this.pageLimit+1)+'件～'+endItem+'件表示しています</p><ul id="'+this.target+'_ul">';
        if (this.startPage > 1) {
            html = html + '<li class="first" id="'+this.target+'_first"><a href="javascript:void(0)">最初のページヘ</a></li><li class="prev" id="'+this.target+'_prev"><a href="javascript:void(0)">前のページへ</a></li>';
        }
        for (var i = this.startPage; i <= this.endPage; i++) {
            if (i == this.currentPage) {
                html = html + '<li id="'+this.target+'_'+i+'" ><span class="'+this.target+'_class">'+i+'</span></li>';
            } else {
                html = html +'<li id="'+this.target+'_'+i+'" ><a href="javascript:void(0)" class="'+this.target+'_class">'+i+'</a></li>';
            }
        }
        if (this.endPage < this.totalPage) {
            html = html + '<li class="next" id="'+this.target+'_next"><a href="javascript:void(0)">次のページへ</a></li><li class="last" id="'+this.target+'_last"><a href="javascript:void(0)">最後のページヘ</a></li>';
        }
        html = html + '</ul>';
        $('#'+this.target).html(html);
    };
    this.setPageWithOffset = function (offset) {
        this.currentPage += offset;

            if (this.currentPage != (this.endPage + this.startPage)/2
                && (this.pageNum + 1)<this.totalPage) {
                
                if ((this.currentPage-this.pageNum/2)<1) {
                    this.startPage = 1;
                    this.endPage = (1 + this.pageNum)>this.totalPage?this.totalPage:(1 + this.pageNum);
                }else if ((this.currentPage+this.pageNum/2)>this.totalPage) {
                    this.endPage = this.totalPage;
                    this.startPage = (this.totalPage-this.pageNum)>1?(this.totalPage-this.pageNum):1;
                } else {
                    this.startPage = this.currentPage-this.pageNum/2;
                    this.endPage = this.currentPage+this.pageNum/2;
                }

            }

            this.writeHTML();
    };
    this.initPageClickEvent = function (pageObject, finishFunction) {
        $('.'+pageObject.target+'_class').unbind('click');
        $('.'+pageObject.target+'_class').click(function() {
            if ($(this).html() == pageObject.currentPage) {
                return;
            }
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                param = {
                    data: 'p=' + $(this).html() + '&csrf_token=' + csrf_token,
                    url: $('#'+pageObject.target).data('url'),
                    success: function (data) {
                        $(pageObject.pageContainer).html(data.html);
                        pageObject.initPageClickEvent(pageObject, finishFunction);
                        $(pageObject.itemClass).show('slow');
                    }
                },
                offset = $(this).html() - pageObject.currentPage;
            $(pageObject.itemClass).hide('slow', function () {
                $(this).remove();
            });
            pageObject.setPageWithOffset(offset, finishFunction);
            Brandco.api.callAjaxWithParam(param);
        });
        $('#'+pageObject.target+'_next').unbind('click');
        $('#'+pageObject.target+'_next').click(function() {
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                param = {
                    data: 'p=' + (pageObject.currentPage+1) + '&csrf_token=' + csrf_token,
                    url: $('#'+pageObject.target).data('url'),
                    success: function (data) {
                        $(pageObject.pageContainer).html(data.html);
                        $(pageObject.itemClass).show('slow');
                        pageObject.initPageClickEvent(pageObject, finishFunction);
                    }
                };
            $(pageObject.itemClass).hide('slow', function () {
                $(this).remove();
            });
            pageObject.setPageWithOffset(1, finishFunction);
            Brandco.api.callAjaxWithParam(param);
        });
        $('#'+pageObject.target+'_prev').unbind('click');
        $('#'+pageObject.target+'_prev').click(function() {
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                param = {
                    data: 'p=' + (pageObject.currentPage-1) + '&csrf_token=' + csrf_token,
                    url: $('#'+pageObject.target).data('url'),
                    success: function (data) {
                        $(pageObject.pageContainer).html(data.html);
                        $(pageObject.itemClass).show('slow');
                        pageObject.initPageClickEvent(pageObject, finishFunction);
                    }
                };
            $(pageObject.itemClass).hide('slow', function () {
                $(this).remove();
            });
            pageObject.setPageWithOffset(-1,finishFunction);
            Brandco.api.callAjaxWithParam(param);
        });
        $('#'+pageObject.target+'_first').unbind('click');
        $('#'+pageObject.target+'_first').click(function() {
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                param = {
                    data: 'p=1&csrf_token=' + csrf_token,
                    url: $('#'+pageObject.target).data('url'),
                    success: function (data) {
                        $(pageObject.pageContainer).html(data.html);
                        $(pageObject.itemClass).show('slow');
                        pageObject.initPageClickEvent(pageObject, finishFunction);
                    }
                };
            $(pageObject.itemClass).hide('slow', function () {
                $(this).remove();
            });
            pageObject.startPage = 1;
            pageObject.currentPage = 1;
            pageObject.endPage = 1 + pageObject.pageNum;
            pageObject.writeHTML();
            Brandco.api.callAjaxWithParam(param);
        });
        $('#'+pageObject.target+'_last').unbind('click');
        $('#'+pageObject.target+'_last').click(function() {
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                param = {
                    data: 'p='+pageObject.totalPage+'&csrf_token=' + csrf_token,
                    url: $('#'+pageObject.target).data('url'),
                    success: function (data) {
                        $(pageObject.pageContainer).html(data.html);
                        $(pageObject.itemClass).show('slow');
                        pageObject.initPageClickEvent(pageObject, finishFunction);
                    }
                };
            $(pageObject.itemClass).hide('slow', function () {
                $(this).remove();
            });
            pageObject.endPage = pageObject.totalPage;
            pageObject.startPage = pageObject.totalPage - pageObject.pageNum;
            pageObject.currentPage = pageObject.totalPage;
            pageObject.writeHTML();
            Brandco.api.callAjaxWithParam(param);
        });

        if (typeof finishFunction === 'function') {
            finishFunction();
        }

    };
    this.writeHTML();
}
