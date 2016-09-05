var BrandcoTopMainColService = (function(){
    return{
        pinToTop: function (checkbox, url){
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                data = checkbox.getAttribute('data-entry');
            data = data + '&csrf_token=' + csrf_token;
            var param = {
                data: data,
                url: url
            };
            Brandco.api.callAjaxWithParam(param, false);
        },
        showDeleteConfirm: function (a){
            var csrf_token = document.getElementsByName("csrf_token")[0].value;
            data = a.getAttribute('data-entry') + '&csrf_token=' + csrf_token;
            $("#delete_area_top").attr({"data-entry" : data});
           Brandco.unit.openModal('.modal2');
        },
        changeDisplay: function (a, url) {
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                data = a.getAttribute('data-entry') + '&csrf_token=' + csrf_token;
                param = {
                data: data,
                url: url
            };
            Brandco.api.callAjaxWithParam(param, false);
        },
        hiddenEntry: function (a, url){
            var data = a.getAttribute('data-entry'),
                param = {
                data: data,
                url: url,
                success: function(data){
                    location.reload();
                }
            };
            Brandco.api.callAjaxWithParam(param);
        },
        dragPanel: function(url){
            var firstPanelIndex,
                csrf_token = document.getElementsByName("csrf_token")[0].value,
                sortableProperties = {
                    placeholder: {
                        element: function(currentItem) {
                            return $("<section class='jsPanel contBoxMain' style='height: " + (currentItem.height()-4) + "px; width: " + (currentItem.width()-4) +"px; vertical-align: middle; text-align: center; outline: none; background-color: rgba(6,19,9,.5);'></section>")[0];
                        },
                        update: function(container, p) {
                            return;
                        }
                    },
                    distance: 5,
                    tolerance: 'intersect',
                    items: '.jsPanel',
                    containment: 'body',
                    opacity: 0.6,
                    helper: function(event, element) {
                        var clone = $(element).clone();
                        clone.removeClass('jsPanel');
                        element.removeClass('jsPanel');
                        return clone;
                    },
                    start: function(event, ui){
                        ui.item.addClass("jsPanel");
                        $list = $("#sortable .jsPanel");
                        firstPanelIndex = $list.index(ui.item);
                    },
                    stop: function(event,ui){
                        $list = $("#sortable .jsPanel");
                        $index = $list.index(ui.item);
                        $secondObj = $list.eq($index + 1);
                        BrandcoMasonryTopService.sortPanel();
                       var param = {
                            data: {'old_index':firstPanelIndex, 'next_index':$index+1,'csrf_token':csrf_token},
                            url: url
                            };
                       Brandco.api.callAjaxWithParam(param, false);
                    },
                    sort: function(){
                        BrandcoMasonryTopService.sortPanel();
                    }
                };
            $( "#jsTopSortable" ).sortable(sortableProperties).disableSelection();
            $( "#jsNormalSortable" ).sortable(sortableProperties).disableSelection();
        },
        init: function(){

            $('.jsFixed').unbind( "click" );
            $('.linkNonDisplay').unbind( "click" );
            $('.jsPanelSizing').unbind( "click" );
            $('#delete_area_top').unbind( "click" );
            $('jsPanel').unbind( "hover" );
            $('.jsOpenModal').unbind( "click" );

            $('.jsFixed').on('click', function(){
                BrandcoTopMainColService.pinToTop($(this)[0], $('#globalUrl').data('priorityurl'));
                Brandco.admin.fixedClick($(this));
            });

            $('.linkNonDisplay').click(function(){
                BrandcoTopMainColService.showDeleteConfirm($(this)[0]);
            });

            $('.jsPanelSizing').click(function(){
                BrandcoTopMainColService.changeDisplay($(this)[0], $('#globalUrl').data('chagedisplayurl'));
               Brandco.admin.jsPanelSizing($(this));
            });

            $('#delete_area_top').click(function(){
                BrandcoTopMainColService.hiddenEntry($(this)[0], $('#globalUrl').data('hiddenurl'));
            });

            $('.jsOpenModal').click(function(){
                var modalID = $(this).attr('href');
                var parameter = $(this).attr('data-option');
                Brandco.unit.openModal(modalID, parameter);
                return false;
            });

            $('.jsPanel').hover(function(){
                var editBox = $(this).find('.editBox1');
                if(editBox.length > 0){
                    editBox.stop(true, true).fadeIn(200);
                }
                $(this).find('.videoInner iframe').hide();
            },function(){
                $('.editBox1').stop(true, true).fadeOut(200);
                $(this).find('.videoInner iframe').show();
            });
        }
    };
})();

$(document).ready(function(){
    // edit mode
    // edit area
    $('.jsEditAreaWrap').hover(function(){
        $(this).find('.editArea').stop(true, true).fadeIn(200);
    },function(){
        $(this).find('.editArea').stop(true, true).fadeOut(200);
    });

    BrandcoTopMainColService.init();
    BrandcoTopMainColService.dragPanel($('#globalUrl').data('dragurl'));
});
