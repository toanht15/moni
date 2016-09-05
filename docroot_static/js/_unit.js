jQuery(function($){
  //body background image position
  var bgTop = 0;
  if($('.company').length) {
    bgTop = $('.company').offset().top;
  }
  $('body').css('background-position', 'center '+bgTop+'px');

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

  //all cheaked
  $('.jsAllCheack').on('change',function(){
    $(this).parents('dt').next('dd').find('input').prop('checked', $(this).prop('checked'));
  });

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
          // $('.barIndicatorWrap').fadeIn();
      } else {
          topBtn.fadeOut();
          // $('.barIndicatorWrap').fadeOut();
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
  userAgent.match(/firefox\//i);
  if (userAgent.indexOf('firefox') != -1 && parseInt(RegExp.rightContext) < 41) {
    $('.tablink2').css({
      width: 'auto'
    }).find('.current>*').css({
      width: 'auto',
      height: 29
    });
    var tabs = $('.tablink2').find('li').children();
    tabs.css({
      width: 'auto',
      height: 30,
      padding: '0 12px',
      'border-radius': '2px 2px 0 0',
      'transform-origin': '0 0',
      'margin-left': 30
    });
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

  // tweet text count
  var $textarea = $('.jsTweetText');
  var $counter = $('.jsTweetCounter');
  $textarea.on('change keyup', function () {
      var value = $textarea.val();
      var count = twttr.txt.getTweetLength(value);

      $counter.text(count).toggleClass('attention1', count > 140);
  }).change();

  // photo page category
  $('.jsPhotoCategoryToggle').click(function(){
    $('.jsPhotoPageCategory').stop(true, true).slideToggle(300);
    return false;
  });

  // gift card
  if($('.jsGiftcardSlider').length >=1) {
    var sliders = {};
    $('.jsGiftcardSlider').flexslider({
      animation: "slide",
      controlNav: false,
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

//barIndicator
$(function(){
  var bar = $('.barIndicator');
  var opt = {
    labelVisibility:'hidden',
    backColor:'#EAEAEA',
    foreColor:'#FFC437',
    counterStep:1,
    milestones:false,
    horBarHeight:8,
    animTime:500
};
  // bar.barIndicator(opt);

  $('#reanimateBtn').on('click', function() {
    var newData = 50;
    bar.barIndicator('loadNewData', [newData])
  });


  $('.satoclik').on('click', function () {
    scrollBi($('.barIndicatorWrap'));
  });

  function scrollBi(target){
    if($('#newMessage').length){
      var height4 = $('#newMessage').get( 0 ).offsetTop;
      target.animate({top: height4}, 1000, 'swing');
      $('html,body').animate({scrollTop:height4}, 1000, 'swing');
    }
  }
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