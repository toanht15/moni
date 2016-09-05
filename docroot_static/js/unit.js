jQuery(function($){

    if (window.addEventListener) {
        function aa_submit(event) {

            if (!$(this).data('submitted')) {
                this._submit();
            }

            $(this).data('submitted', true);
        }

        // onsubmitイベント対応
        window.addEventListener("submit", aa_submit, false);

        //.submit()対応
        HTMLFormElement.prototype._submit = HTMLFormElement.prototype.submit;
        HTMLFormElement.prototype.submit = aa_submit;
    }

    //body background image position
    var bgTop = 0;
    var headerParts = ['header .account', '.privateMode', '.editHead'];
    for (var i = headerParts.length - 1; i >= 0; i--) {
        if($('body').find(headerParts[i]).length){
            bgTop += $('body').find(headerParts[i]).outerHeight();
        }
    };
    $('body').css('background-position', '0 '+bgTop+'px');

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

    // module preview
    $('.jsModulePreviewSwitch').click(function(){
        if($(this).hasClass('left')){
            $(this).parents('header').nextAll('.jsModulePreviewArea').removeClass('displayPC').addClass('displaySP');
        }else if($(this).hasClass('right')){
            $(this).parents('header').nextAll('.jsModulePreviewArea').removeClass('displaySP').addClass('displayPC');
        }
        return false;
    });

    //all cheaked
    $('.jsAllCheck').on('change',function(){
        $(this).parents('dt').next('dd').find('input').prop('checked', $(this).prop('checked'));
    });

    // modal
  $('.jsOpenModal').unbind( "click" );
  $('.jsOpenModal').click(function(){
      Brandco.unit.showModal(this);
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
  $('a[href="#closeModalFrame"]').click(function(){
    Brandco.unit.closeModalFlame(this);
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

  $(document).on('click', '#markdown_rule_popup', function() {
      Brandco.unit.windowOpenWrap($(this).data('link'), 'title', '820', '745');
  });

  $(document).on('click', '.jsFileUploaderPopup', function() {
      Brandco.unit.windowOpenWrap($(this).data('link'), 'ファイル管理', '1000', '745');
  });

    // インジケーター
    var bar = $('.barIndicator');
    if (bar.length !== 0) {
        var opt = {
            labelVisibility: 'hidden',
            backColor: '#EAEAEA',
            foreColor: '#FFC437',
            counterStep: 1,
            milestones: false,
            horBarHeight: 8,
            animTime: 500
        };
        bar.barIndicator(opt);
        bar.barIndicator('loadNewData', 0);
    }
});

Brandco.unit = (function(){
    return {
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
                    }, 300, function() {
                        var modal_height = $(modalID).find('.jsModalCont').position().top + $(modalID).find('.jsModalCont').outerHeight(true);
                        var body_height = $('body').outerHeight(true);

                        if (body_height < modal_height) {
                            $('body').data('prev_height', body_height);
                            $('body').height(modal_height + 10);
                            $(modalID).height($('body').height());
                        } else {
                            $('body').data('prev_height', 0);
                        }
                    });
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
        //close modal with id = modal+index
        closeModal: function(index){
            $('#modal' + index).children().animate({
                top: -150,
                opacity: 0
            }, 200, function () {
                $(this).css('display', 'none');
                $(this).parent().fadeOut(200);
            });
        },
        closeModalFlame: function(a){

            $('.jsModalCont', parent.document).animate({
                top: -150,
                opacity: 0
            }, 300, function(){
                $(this).css('display', 'none');
                $(".jsModal", parent.document).css('display', 'none');

                var prev_height = parent.top.$('body').data('prev_height');
                if (prev_height && prev_height > 0) {
                    $('body', parent.document).height(prev_height);
                }
            });

            if($(a).attr('data-type') == 'refreshTop'){
                parent.Brandco.helper.brandcoBlockUI();
                window.top.location.replace(window.top.location.href.split(/#|\?/, 1));
            }
            return false;
        },
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
        showModal: function(clickObj){
            var target = $(clickObj);
            if(target.attr('href')) {
                var modalID = target.attr('href');
            } else {
                var modalID = target.attr('data-modal_id');
            }
            var parameter = target.attr('data-option');
            Brandco.unit.openModal(modalID, parameter);
            return false;
        },
        createAndJumpToAnchor: function(isAutoLoad){
            if (isAutoLoad != true) {
                $('#newMessage').insertBefore('section.jsMessage:last');
            }

            var target = $('section.jsMessage:last');
            if (target.parents('section').length > 1) {
                target = target.parents('section').get(1);
            }

            $('#indicatorAnchor').insertBefore(target);
            $('#pinAction').click();
        },
        disableForm: function(form) {
            var input = ['input', 'textarea', 'select'];
            $.each(input, function(i,v){
               form.find(v).each(function(){
                   $(this).attr('disabled', 'disabled');
               });
            });
        },
        changeInputNameOfForm: function(form) {
            form.find('input,textarea,select').each(function(){
                $(this).attr('name', $(this).attr('name')+'1');
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
