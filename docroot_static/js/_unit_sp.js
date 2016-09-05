jQuery(function($){
  //body background image position
  var bgTop = 0;
  if($('.company').length) {
    bgTop = $('.company').offset().top;
  }
  $('body').css('background-position', 'center '+bgTop+'px');

  // header fixed
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

  // sp menu toggle
  $('.jsSpOpenLink').click(function(){
    $('.jsGnavi').slideToggle(280);
    return false;
  });

  // gnavi links
  // more links number
  if($('.gnavi li').length > 0){
    var gnavi = $('.gnavi li');
    var naviWidth = 0;
    var naviWrapWidth = $('.gnavi ul').width();
    var i = 0;
    var naviCount;
    while(i < gnavi.length){
      naviWidth += $(gnavi[i]).width() + 1;
      if(naviWidth >= naviWrapWidth - 1){
        naviCount = i;
        break;
        naviCount = i;
        break;
      }
      naviCount = 0;
      i++;
    }
    if(naviCount == 0){
      $('.gnavi .openLink').hide();
    }else{
      var naviHidden = gnavi.length - naviCount;
      $('.gnavi .openLink').html(naviHidden);
    }
  } else{
      $('.gnavi .openLink').hide();
  }

  //toggle more link
  $('.jsOpenLink').click(function(){
    $(this).toggleClass('openLink').toggleClass('closeLink');
    $(this).next('.jsOpenLinkAera').slideToggle(200);
    return false;
  });

  //toggle gnavi more links
  if($('.gnavi ul').length > 0){
    var naviHeight = $('.gnavi ul').height();
    var naviInnerHeight = $('.gnavi ul').get(0).scrollHeight;
    $(document).on('click', '.gnavi .openLink', function(){
      $(this).removeClass('openLink').addClass('closeLink');
      $(this).prev('ul').animate({
        height: naviInnerHeight
      }, 300);
      return false;
    });
    $(document).on('click', '.gnavi .closeLink', function(){
      $(this).removeClass('closeLink').addClass('openLink');
      $(this).prev('ul').animate({
        height: naviHeight
      }, 300);
      return false;
    });
  }

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

  //switch
  $('.switch').click(function(){
    if($(this).hasClass('on')){
      $(this).removeClass('on').addClass('off');
    }else{
      $(this).removeClass('off').addClass('on');
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
    return false;
  });

  // message setting toggle
  $('.jsMypageSetteing').click(function(){
    $('.jsMypageSetteingTarget').slideToggle(300);
    return false;
  });

  // campaign join address
  $('.jsJoinAddress').click(function(){
    $('.jsJoinAddressTarget').slideToggle(300);
    return false;
  });

  // pagetop scroll
  var topBtn = $('.pageTop');
  $(window).scroll(function () {
      if ($(this).scrollTop() > 100) {
          topBtn.fadeIn();
      } else {
          topBtn.fadeOut();
      }
  });
  $('[href="#top"]').click(function(){
      $('body,html').animate({
          scrollTop: 0
      }, 500);
      return false;
  });

  // firefox tab vertical
  var userAgent = window.navigator.userAgent.toLowerCase();
  var appVersion = window.navigator.appVersion.toLowerCase();
  if (userAgent.indexOf('firefox') != -1) {
    var tabs = $('.tablink2').find('li').children();
    for(var i = 0; i<tabs.length; i++){
      var tab = $(tabs[i]);
      var tabWid = tab.outerWidth() + 1;
      var tabHei = tab.outerHeight();
      tab.css({
        'transform': 'rotate(90deg)'
      }).outerWidth(tabWid);
      tab.parent('li').width(tabHei).height(tabWid);
    }
    $('.tablink2').width(30);
  }

  //menu toggle
  var naviWrap = $('.sideNavi');
  naviWrap.find('.sideNaviInner').hide();
  naviWrap.find('span').on('click', function(){
    $(this).next('.sideNaviInner').slideToggle(300);
  });

  // gift card
  if($('.jsGiftcardSlider').length >=1) {
    var sliders = {};
    $('.jsGiftcardSlider').flexslider({
      animation: "slide",
      controlNav: false,
      directionNav: false,
      animationLoop: false,
      slideshow: false,
      itemWidth: 100,
      itemMargin: 5,
      start: function(carousel) {
        var id = carousel.attr('id');
        sliders[id] = carousel;

        var cardWrap = carousel.parents('.messageGift');
        var card = cardWrap.find('.giftCard');
        if(card.find('.cardBackground').outerHeight() > card.outerHeight()) {
          card.css({
            'padding-top': card.find('.cardBackground').outerHeight()
          });
        }

        // android fit
        if(androidVersion() < 4.4 && (browserType() == 'others' || browserType() == 'safari')){
            card.find('input, textarea').each(function(i, elem) {
              var elemClass = $(elem).attr('class');
              var elemValue = $(elem).val();
              if($(elem).prop("tagName") == 'textarea') {
                elemValue = $(elem).text();
              }
              $(elem).replaceWith(function() {
                return '<span class="'+elemClass+'">'+elemValue+'</span>';
              });
            });
            card.on('click', function(event) {
              openModal('#modalGiftCard');
            });
        }

        var winWid;
        var scale;
        if($(window).outerWidth() > 320) {
          winWid = $(window).outerWidth() - 40;
        } else {
          winWid = 320 - 40;
        }
        scale = winWid / 580; // card width fixed 580px
        card.css({
          'transform': 'scale('+scale+')'
        });
        cardWrap.css({
          'height': cardWrap.outerHeight() + card.outerHeight() * (scale - 1)
        });
      }
    });
    $(document).on('click', '.jsGiftCardCange', function(event) {
      var imgAttr = $(this).attr('src');
      var card =  $(this).parents('.messageGift').find('.cardBackground');

      if(card.find('img').length < 1) {
        card.append('<img>');
      }
      card.find('img').attr('src', imgAttr);
    });
  }
});

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
  $("a").not(modalID+" a").css({
    '-webkit-tap-highlight-color': 'rgba(0, 0, 0, 0)',
    'pointer-events': 'none'
  });
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
  $("a").not(modalID+" a").css({
    '-webkit-tap-highlight-color': '',
    'pointer-events': 'auto'
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
    $('.jsModalBase').height($('body').height() + 31).css({
        'z-index': indexNum,
        'margin-top': '-31px'
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

//passView
$(function() {
  var flag = 'hidden';
  var btn = $('.jsPassViewBtn');
  var traget = $('.jsPassView');

  $('.jsPassViewBtn').click(function(){
    if ( flag == 'hidden' ) {
      btn.find('a').text('非表示');
      traget.attr('type','text');
      flag = 'view';
    } else {
      btn.find('a').text('表示');
      traget.attr('type','password');
      flag = 'hidden';
    }
    return false;
  });
});

// barIndicator
$(function(){
  var bar = $('.barIndicator');
  var opt = {
    labelVisibility:'hidden',
    counterStep:1,
    milestones:false,
    // horBarHeight:15,
    horBarHeight:6,
    animTime:500,
    backColor:'#EAEAEA',
    foreColor:'#FFC437'
};
  // bar.barIndicator(opt);

  $('#reanimateBtn').on('click', function() {
    var newData = 100;
    bar.barIndicator('loadNewData', [newData])
  });
});


$(function(){
  $('.jsModuleContText').click(function(){
    var trigger = $(this).parents('.jsModuleContWrap');
    var target = trigger.find('.jsModuleContTarget');

    if(trigger.hasClass('close')) {
      target.slideDown(200, function() {
        trigger.removeClass('close');
      });
    }else{
      target.slideUp(200, function() {
        trigger.addClass('close');
      });
    }
    return false;
  });
});
