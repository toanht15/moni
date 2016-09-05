jQuery(function($){
  // editHead menu
  $('.otherLink a').hover(function(){
    $(this).next('span').stop(true, true).fadeIn(300);
  },function(){
    $(this).next('span').stop(true, true).fadeOut(100);
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

  // edit mode
  // edit area
  $('.jsEditAreaWrap').hover(function(){
    $(this).find('.editArea').stop(true, true).fadeIn(200);
  },function(){
    $(this).find('.editArea').stop(true, true).fadeOut(200);
  });

  // edit menu
  $('.jsMenuToggle').click(function(){
    var target = $(this).attr('href');
    if($(target).is(':hidden')){
      if($('.jsMenuArea').is(':visible')){
        $('.jsMenuArea').slideUp(300);
        $(target).delay(300).slideDown(300);
      }else{
        $(target).slideDown(300);
      }
    }else{
      $(target).slideUp(300);
    }
    return false;
  });

  // menu title
  $('.jsMenuTitle').change(function(){
    $('.jsMenuTitleInput').attr('disabled', 'disabled');
    $(this).parent('label').next('.jsMenuTitleInput').removeAttr('disabled');
  });

  //panel cont edit
  $('.jsPanelImage').change(function(){
    $('.jsPanelImageInput').attr('disabled', 'disabled');
    $(this).parents('td').find('.jsPanelImageInput').removeAttr('disabled');
  });

  // add panles
  $('.jsPostPanel').hover(function(){
    $(this).find('.jsPostData').stop(true, true).fadeIn(200);
  }, function(){
    $(this).find('.jsPostData').stop(true, true).fadeOut(200);
  });

  //toggle more link
  $('.jsOpenAction').click(function(){
    if($(this).hasClass('openAction')){
      $(this).removeClass('openAction').addClass('closeAction');
      $(this).parents('li').find('.jsOpenActionArea').show().css('display', 'inline-block');
      $(this).parents('li').find('.jsOpenActionArea_contrary').hide();
    }else if($(this).hasClass('closeAction')){
      var linkTitleText = $(this).parents('li').find('.jsLinkTitle').val();

      $(this).removeClass('closeAction').addClass('openAction');
      $(this).parents('li').find('.jsOpenActionArea').hide();
      $(this).parents('li').find('.jsOpenActionArea_contrary').show().css('display', 'inline-block');
      $(this).parents('li').find('.jsOpenActionArea_contrary a').show().css('display', 'inline-block').html(linkTitleText);
    }
    return false;
  });

  // module cont
  $('.jsModuleContTile').click(function(){
    var trigger = $(this);
    var target = trigger.next('.jsModuleContTarget');
    if(trigger.hasClass('close')) {
      target.slideDown(200, function() {
        trigger.removeClass('close');
      });
    }else{
      target.slideUp(200, function() {
        trigger.addClass('close');
      });
    }
    // return false;
  });

  // notice bar
  var targetArea = $('.jsNoticeBarArea1');
  $('.jsNoticeBarOpen1').click(function(){
    var targetBar = $($(this).attr('href'));

    if(targetArea.position().top < 0){
      showNoticeBar(targetBar);
    }else{
      hideNoticeBar();
    }
    return false;
  });
  $('.jsNoticeBarClose1').click(function(){
    hideNoticeBar();
    return false;
  });

  function showNoticeBar(targetBar) {
    var targetBarHeight = targetBar.outerHeight();
    targetBar.css({
      'opacity': 0,
      'display': 'block'
    });
    targetArea.css('top', -targetBarHeight -10);
    targetArea.find(targetBar).css('opacity', 1);
    targetArea.animate({
      top: 0
    }, 300);

    setTimeout(function(){
      if(targetBar.hasClass('jsNoticeBarClose')){
        hideNoticeBar();
      }
    }, 3000);
    return;
  }
  function hideNoticeBar() {
    var showBar = targetArea.find(':visible');
    var targetBarHeight = showBar.outerHeight();
    targetArea.animate({
      top: -targetBarHeight -10
    }, 300, function(){
      targetArea.find('p').hide();
    });
    return;
  }

  // skeleton select
  $('[name="makeStepTypeRadio"]').change(function(){
    var target = $(this).parents('.makeStepType').find('.makeStepTypeCont');
    $('.makeStepTypeCont').not(target).slideUp(500);
    target.slideDown(500);
  });

  $('[href="#jsSortBoxToggle"]').click(function(){
    var target = $(this).parents('th').find('.sortBox');
    $('.sortBox').not(target).fadeOut(300);
    target.fadeToggle(300);
    return false;
  });

  // module preview
  $('.jsModulePreviewSwitch').click(function(){
    if($(this).hasClass('left')){
      $(this).parents('.modulePreview1').find('.jsModulePreviewArea').removeClass('displayPC').addClass('displaySP');
    }else if($(this).hasClass('right')){
      $(this).parents('.modulePreview1').find('.jsModulePreviewArea').removeClass('displaySP').addClass('displayPC');
    }
    return false;
  });

  // commone toggle area
  $('.jsAreaToggle').click(function(){
    $(this).parents('.jsAreaToggleWrap').find('.jsAreaToggleTarget').stop(true, true).fadeToggle(200);
    return false;
  });

  // commone check toggle area
  $('.jsCheckToggle').on('change', function(){
    var targetWrap = $(this).parents('.jsCheckToggleWrap')[0];
    $(targetWrap).find('.jsCheckToggleTarget').slideToggle(300);
  });

  $('.jsSortItem').click(function(){
    $('.jsSortItemTarget').toggle();
    return false;
  });

  // jsCount
  $('.jsCountTarget').on('change', function(){
    var targetGroup = $(this).attr('name');
    countCheckbox(targetGroup);
  });
  $('.jsCountAll').on('change', function(){
    var targetGroup = $(this).attr('name');
    if($(this).prop('checked')){
      $('[name="'+targetGroup+'"]').prop('checked', true);
    }else{
      $('[name="'+targetGroup+'"]').prop('checked', false);
    }
    countCheckbox(targetGroup);
  });

  function countCheckbox(targetGroup) {
    var countNum = $('.jsCountTarget:checked[name="'+targetGroup+'"]').length;
    $('.jsCountArea').html(countNum);
  };

  // step group carousel
  if($('.campaignFlowCont').length>0){
    var flow = $('.campaignFlowCont');

    for (var i = flow.length - 1; i >= 0; i--) {
      var target = $(flow[i]);
      var targetWidth = 111*target.find('.campaignModule1').length + 1 + 110*target.find('.addModule').length;
      if(targetWidth > 960){
        target.parents('.campaignFlow').width(target.parents('.campaignFlow').width()-40);
        target.parents('.campaignFlowWrap').find('.campaignFlowScroll').show();
        target.width(targetWidth);
      }
    };
    var flowScroll;
    function flowScrollStart(btn, i){
      var target = $(btn).parents('.campaignFlowWrap').find('.campaignFlow');
      flowScroll = setInterval(function(){
        var leftPosition = target.scrollLeft() + i;
        target.scrollLeft(leftPosition);
      }, 1);
    }
    function flowScrollStop(){
      clearInterval(flowScroll);
    }
    $('.flowPrev').hover(function(){
      flowScrollStart(this, -1);
    }, function(){
      flowScrollStop(this, 1);
    });

    $('.flowNext').hover(function(){
      flowScrollStart(this, 1);
    }, function(){
      flowScrollStop(this, 1);
    });
  }

  if($('.stepList').length>0){
    var skeleton = $('.stepList');
    for (var i = skeleton.length - 1; i >= 0; i--) {
      var target = $(skeleton[i]);
      var targetWidth = 0;
      targetWidth += 55 * target.find('.moduleDetail1, .addModuleDetail1').length;
      targetWidth += 20 * (target.children('li').length - 1);
      if(target.find('.stepDetail_base').length>0){
        targetWidth += 20;
      }
      if(target.parents('.stepListEdit').find('.deleteModule').length>0){
        targetWidth += 85;
      }
      if(targetWidth > target.parent().width()){
        target.width(targetWidth);
      }
    };
  }

  init();

  //all cheaked
  $('.jsClusteCheack').on('change',function(){
    var anchor = $(this).parents('.jsClusteToggleWrap');
    anchor.find('input').prop('checked', $(this).prop('checked'));
    anchor.find('.jsClusteToggleTarget').stop(true, true).slideDown(200);
    anchor.find('.jsClusteToggle').removeClass('close').slideDown(200);
  });

   //cheaked decision
  $('.jsClusteToggleTarget input').on('click',function(){
    var target = $(this).parents('.jsClusteToggleTarget').find('input');
    if(target.length == target.filter(':checked').length){
      $(this).parents('.jsClusteToggleWrap').find('.jsClusteCheack').prop('checked', 'checked');
    } else {
      $(this).parents('.jsClusteToggleWrap').find('.jsClusteCheack').prop('checked', false);
    }
  });

  // commone toggle area
  $('.jsClusteToggle').click(function(){
    var trigger = $(this);
    var target = trigger.parents('.jsClusteToggleWrap').find('.jsClusteToggleTarget');

    if(trigger.hasClass('close')) {
      target.slideDown(200, function() {
        trigger.removeClass('close');
      });
    }else{
      target.slideUp(200, function() {
        trigger.addClass('close');
      });
    }
  });
});
function init(){
  $('jsPanel').unbind( "hover" );
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

  $('.jsFixed').unbind( "click" );
  $('.jsFixed').click(function(){
    var movePanel = $(this).parents('.jsPanel');
    var parentId = movePanel.parents('.jsPanelWrap').attr('id');
    var innerText = '';

    if(movePanel.hasClass('contFixed')){
      innerText = '優先表示';
    }else{
      innerText = '優先表示を解除';
    }

    var targetId = '';
    if(parentId === 'jsTopSortable'){
      targetId = '#jsNormalSortable';
    }else if(parentId === 'jsNormalSortable'){
      targetId ='#jsTopSortable';
    }
    $(this).text(innerText);
    movePanel.toggleClass('contFixed');
    $(targetId).prepend(movePanel);
    //check top panel > 3 --> move to normal panel
    if(targetId == '#jsTopSortable') {
      $list = $(targetId).children('.jsPanel');
      if($list.length > 3){
        var delPanel = $list[3];
        delPanel.classList.toggle('contFixed', false);
        delPanel.getElementsByClassName('linkFix')[0].innerHTML = '優先表示';
        $('#jsNormalSortable').prepend(delPanel);
      }
    }
    goMason();
    return false;
  });

  // panel edit
  var classes = ['boxSizeSmall', 'boxSizeMiddle', 'boxSizeLarge'];
  $('.jsPanelSizing').unbind( "click" );
  $('.jsPanelSizing').click(function(){
    var targetPanel = $(this).parents('.jsPanel');
    var panelSize = 0;
    var classesLength = classes.length;
    for (var i = 0; i < classesLength; i++) {
      if(targetPanel.hasClass(classes[i])){
        targetPanel.removeClass(classes[i]);
        if(i >= classesLength -1){
          panelSize = 0;
        }else{
          panelSize = i + 1;
        }
        targetPanel.addClass(classes[panelSize]);
        break;
      }
    };

    goMason();
  });

}