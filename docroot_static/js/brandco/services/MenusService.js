var MenusService = {
    isChanged: 0,
    valueChanged: function (){
        MenusService.isChanged = 1;
        $(window).unbind('beforeunload');
        $(window).on('beforeunload', function() {
            return Brandco.message.reloadMessage;
        });
    },
    initAddNewClick: function (){
        $("#iconAdd").click(function(event){
            event.preventDefault();
            if(MenusService.isChanged){
                if(confirm('変更を保存して新規メニューを追加します。') == true) {
                    $(window).unbind('beforeunload');
                    $("#update_menu_form").attr("action",$('.modalInner-cont').data('updateurl'));
                    $("#submit").click();
                }
            }else{
                window.location = this;
            }
        });
    },
    initSubmit: function (){
        MenusService.initAddNewClick();
        $("#submit").click(function (event) {
            event.preventDefault();
            $(window).unbind('beforeunload');
            var order = $("#jsSortable").sortable("toArray");
            var order_ids = [];

            $(order).each(function () {
                var list = $("#" + this);
                var target = $(".switch", list);
                var menu_id = target.attr("data-menu_id");

                order_ids.push(menu_id);

                if (target.hasClass('on')) {
                    $("#hidden_flg_" + menu_id).val(0);
                } else {
                    $("#hidden_flg_" + menu_id).val(1);
                }
            });
            $("#order").val(order_ids.join(","));

            $("#update_menu_form").submit();
            MenusService.isChanged = 0;
        });
    },
    deleteMenu: function (limit, url){
        $(".cmd-delete-menu").click(function (event) {
            event.preventDefault();
            MenusService.valueChanged();
            $(this).parents("li").remove();
            if(limit !== 'undefined' && $("#jsSortable li").size() < limit){
				$(".addNew").html('<a href="'+url+'" class="iconAdd" id="iconAdd">新規メニュー追加</a>');
                MenusService.initAddNewClick();
            }
        });
    }
};

$(document).ready(function(){
    $("#cancelChanges").click(function(event){
        event.preventDefault();
        if(MenusService.isChanged){
            if(confirm(Brandco.message.reloadMessage) == true) {
                $(window).unbind('beforeunload');
                Brandco.unit.closeModalFlame(this);
            }
        }else{
            Brandco.unit.closeModalFlame(this);
        }
    });

    $("#jsSortable").sortable({
        start: function( event, ui ) {
            MenusService.valueChanged();
        }
    });
    MenusService.initSubmit();
    MenusService.deleteMenu($('.modalInner-cont').data('menuslimit'), $('.modalInner-cont').data('createurl'));

    if($('#menuTitle')[0]) {
        $(".textLimit").html(("（")+($('#menuTitle')[0].value.length)+("文字/35文字）"));
    }

    $('#menuTitle').on('input', function(){
        $(".textLimit").html(("（")+($('#menuTitle')[0].value.length)+("文字/35文字）"));
    });

});