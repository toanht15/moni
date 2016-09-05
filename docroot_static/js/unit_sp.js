Brandco.unit = (function(){
    return {
        isSmartPhone : true,
        showNoticeBar: function(targetBar){
            var targetBarHeight = targetBar.outerHeight(),
                targetArea = $('.jsNoticeBarArea1');
            targetBar.css({
                'opacity': 0,
                'display': 'block'
            });
            targetArea.css('top', -targetBarHeight -10);
            targetArea.find(targetBar).css('opacity', 1);
            targetArea.animate({
                top: 30
            }, 1000);

            setTimeout(function(){
                if(targetBar.hasClass('jsNoticeBarClose')){
                    Brandco.unit.hideNoticeBar();
                }
            }, 2500);
            return;
        },
        hideNoticeBar: function(){
            var targetArea = $('.jsNoticeBarArea1'),
                showBar = targetArea.find(':visible'),
                targetBarHeight = showBar.outerHeight();
            targetArea.animate({
                top: -targetBarHeight -10
            }, 300, function(){
                targetArea.find('p').hide();
            });
            return;
        },
        createAndJumpToAnchor: function(isAutoLoad){
            if (isAutoLoad != true) {
                $('#newMessage').insertBefore('section.jsMessage:last');
            }

            $('#ingicatorAnchor').insertBefore('section.jsMessage:last');
            $('#pinAction').click();
        },
        disableForm: function(form) {
            var input = ['input', 'textarea', 'select'];
            input.forEach(function(entry){
                form.find(entry).each(function(){
                    $(this).attr('disabled', 'disabled');
                });
            });
        },
        changeInputNameOfForm: function(form) {
            form.find('input,textarea,select').each(function(){
                $(this).attr('name', $(this).attr('name')+'1');
            });
        },
        openModal: function(modalID, parameter){
            $(modalID + ' section iframe').contents().find('html').html('');

            $(modalID + ' section iframe').attr({'src':$(modalID + ' section iframe').attr('data-src')});
            if(parameter) {
                $(modalID + ' section iframe').attr({'src':$(modalID + ' section iframe').attr('src') + parameter});
            }
            if($(modalID).hasClass('modal1')){
                $(modalID).height($('body').height()).fadeIn(300, function(){
                    $(this).find('.jsModalCont').css({
                        display: 'block',
                        opacity: 0,
                        top: $(window).scrollTop()
                    }).animate({
                        top: $(window).scrollTop() + 30,
                        opacity: 1
                    }, 300);
                });
            }else if($(modalID).hasClass('modal2')){
                $(modalID).fadeIn(300, function(){
                    $(this).find('.jsModalCont').css({
                        display: 'block',
                        top: 30,
                        opacity: 1
                    }).fadeIn(300);
                });
            }
        },
        closeModal: function(index){
            $('#modal' + index).children().animate({
                top: -150,
                opacity: 0
            }, 200, function () {
                $(this).css('display', 'none');
                $(this).parent().fadeOut(200);
            });
        },
        windowOpenWrap: function(url,windowname,width,height) {
            var features="location=no, menubar=no, status=yes, scrollbars=yes, resizable=yes, toolbar=no";
            if (width) {
                if (window.screen.width > width)
                    features+=", left="+(window.screen.width-width)/2;
                else width=window.screen.width;
                features+=", width="+width;
            }
            if (height) {
                if (window.screen.height > height)
                    features+=", top="+(window.screen.height-height)/2;
                else height=window.screen.height;
                features+=", height="+height;
            }
            return window.open(url,windowname,features);
        }
    }
})();

jQuery(function($){
  // sp menu toggle
  $('.jsSpOpenLink').click(function(){
    // $(this).parents('.jsSpGnavBtn').toggleClass('spGnavBtn_open').toggleClass('spGnavBtn_close')
    var display = $('.jsGnavi').css('display');
    $('.jsGnavi').slideToggle(280);

    var tracker_name = $(this).attr('data-tracker_name');
    var brand_name = $(this).attr('data-brand_name');
    var brand_url = $(this).attr('data-brand_url');

    if (typeof(ga) !== 'undefined' && display === 'none') {
      ga(tracker_name + '.send', 'event', 'menu-click', brand_name, location.href, {'page': brand_url});
    }

    return false;
  });

    //switch
    $(document).on('click','.switch',function(){
        if(!$(this).attr('data-disabled')){
            if($(this).hasClass('on')){
                $(this).removeClass('on').addClass('off');
            }else{
                $(this).removeClass('off').addClass('on');
            }
        }
        return false;
    });

    //toggle_switch
    $('.toggle_switch').click(function(){
        if($(this).hasClass('right')){
            $(this).removeClass('right').addClass('left');
        }else{
            $(this).removeClass('left').addClass('right');
        }
        return false;
    });

    // message toggle
  $('.jsMessageToggle').click(function(){
    $(this).parents('.messageListInner').find('.readed').toggle();
    $(this).parents('.showAll').remove();
    return false;
  });

  // message setting toggle
  $('.jsMessageSetteing').click(function(){
    $('.jsMessageSetteingTarget').slideToggle(300);
    return false;
  });

  // pagetop scroll
  $('[href="#top"]').click(function(){
      $('body,html').animate({
          scrollTop: 0
      }, 500);
      return false;
  });

    //menu toggle
    var naviWrap = $('.sideNavi');
    naviWrap.find('.sideNaviInner').hide();
    naviWrap.find('span').on('click', function(){
        $(this).next('.sideNaviInner').slideToggle(300);
    })

    // modal
    $('.jsOpenModal').unbind( "click" );
    $('.jsOpenModal').click(function(){
        var modalID = $(this).attr('href');
        var parameter = $(this).attr('data-option');
        openModal(modalID, parameter);
        return false;
    });
    $('a[href="#closeModal"]').click(function(){
        var modalID = $(this).parents('.jsModal');
        if($(modalID).hasClass('modal1')){
            $('.jsModalCont').animate({
                top: -150,
                opacity: 0
            }, 200, function(){
                $(this).css('display', 'none');
                $(this).parents('.jsModal').fadeOut(200);
            });
        }else if($(modalID).hasClass('modal2')){
            $('.jsModalCont').fadeOut(200, function(){
                $(this).parents('.jsModal').fadeOut(200);
            });
        }
        return false;
    });
    $('a[href="#closeModalFrame"]').click(function(){
        closeModalFlame(this);
    });

    // Close Modal
    $('a[href="#closeModal"]').click(function(){
        var modalID = $(this).parents('.jsModal');

        if($(modalID).hasClass('modal1')){
            $('.jsModalCont').animate({
                top: -150,
                opacity: 0
            }, 300, function(){
                $(this).css('display', 'none');
                $(this).parents('.jsModal').fadeOut(300);
                
                var prev_height = $('body').data('prev_height');
                if (prev_height && prev_height > 0) {
                    $('body').height(prev_height);
                }
            });
        }else if($(modalID).hasClass('modal2')){
            $('.jsModalCont').fadeOut(200, function(){
                $(this).parents('.jsModal').fadeOut(200);
            });
        }

        return false;
    });

    // header fixed
    if($('.demoMode').length == 0) {
        var elem = [$('header .account'), $('.demoMode'), $('.privateMode')];
        for (var i = 0; i <= elem.length - 1; i++) {
            if(elem[i].length) {
                elem[i].css({
                    position: 'fixed',
                    top: elem[i].offset().top
                }).parent().css({
                    'padding-top': elem[i].outerHeight()
                });
            }
        };
    }

    // インジケーター
    var bar = $('.barIndicator');
    if (bar.length !== 0) {
        var opt = {
            labelVisibility: 'hidden',
            counterStep: 1,
            milestones: false,
            // horBarHeight:15,
            horBarHeight: 6,
            animTime: 500,
            backColor: '#EAEAEA',
            foreColor: '#FFC437'
        };

        bar.barIndicator(opt);
        bar.barIndicator('loadNewData', 0);
    }
});

function androidVersion() {
    var ua = navigator.userAgent;
    if( ua.indexOf('Android') != -1) {
        var version = parseFloat(ua.slice(ua.indexOf('Android')+8));
        return version;
    }
}
function browserType() {
    var ua = navigator.userAgent;
    var browser = '';
    if(ua.toLowerCase().indexOf('chrome') != -1) {
        browser = 'chrome';
    } else if(ua.toLowerCase().indexOf('firefox') != -1) {
        browser = 'firefox';
    } else if(ua.toLowerCase().indexOf('safari') != -1) {
        browser = 'safari';
    } else {
        browser = 'others';
    }
    return browser;
}
function openModal(modalID, parameter) {
    $(modalID + ' section iframe').attr({'src':$(modalID + ' section iframe').attr('data-src')});
    if(parameter) {
        $(modalID + ' section iframe').attr({'src':$(modalID + ' section iframe').attr('src') + parameter});
    }
    if($(modalID).hasClass('modal1')){
        $(modalID).height($('body').height()).fadeIn(300, function(){
            $(this).find('.jsModalCont').css({
                display: 'block',
                opacity: 0,
                top: $(window).scrollTop()
            }).animate({
                top: $(window).scrollTop() + 30,
                opacity: 1
            }, 300);
        });
    }else if($(modalID).hasClass('modal2')){
        $(modalID).fadeIn(300, function(){
            $(this).find('.jsModalCont').css({
                display: 'block',
                top: 30,
                opacity: 1
            }).fadeIn(300);
        });
    }
}
//close modal with id = modal+index
function closeModal(index){
    $('#modal' + index).children().animate({
        top: -150,
        opacity: 0
    }, 200, function () {
        $(this).css('display', 'none');
        $(this).parent().fadeOut(200);
    });
}
function closeModalFlame(a){
    $('.jsModalCont', parent.document).animate({
        top: -150,
        opacity: 0
    }, 300, function(){
        $(this).css('display', 'none');
        $(".jsModal", parent.document).css('display', 'none');
    });

    if($(a).attr('data-type') == 'refreshTop'){
        window.top.location.replace(window.top.location.href.split('?', 1));
    }
    return false;
}

//side menu
$(document).on('click', '.jsMenuTrigger', function(event) {
    var target = $(this).attr('href');
    if($(target).hasClass('close')){ // open modal
        openMenu(target);
        openModalBase(target);
        $('header .account').css("","");
    } else if($(target).hasClass('open')) { // close modal
        closeMenu($('.jsMenuTrigger'));
        closeModalBase();
        $('header .account').css("","");
    }
    return false;
});
function openMenu(target) {
    if (typeof syn_menu !== 'undefined' && syn_menu) {
        syn_menu.trackShowEvent();
    }
    var scrollY = $(window).scrollTop();
    $(target).removeClass('close').addClass('open');
    $(target).scrollTop(0);
}
function closeMenu(targets) {
    targets.each(function(i, target) {
        $(target).removeClass('open').addClass('close');
    });
}
function openModalBase(target) {
    var indexNum;
    if($(target).hasClass('jsModalTarget-search')){
        indexNum = 100;
    } else {
        indexNum = 210;
    }
    $('.jsModalBase').height($('body').height()).css({
        'z-index': indexNum
    }).fadeIn(250);
}
function closeModalBase() {
    $('.jsModalBase').fadeOut(250);
}

// modal base click
$(document).on('click', '.jsModalBase', function(event) {
    closeMenu($('.jsModalTarget-menu'));
    closeModalBase();
    return false;
});
