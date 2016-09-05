jQuery(function($){
  // editHead menu
  $('.otherLink a').hover(function(){
    $(this).next('span').stop(true, true).fadeIn(300);
  },function(){
    $(this).next('span').stop(true, true).fadeOut(100);
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


    // menuTitle
    $('.menuTitle').click(function(){
        $(this).closest('li').find('.jsOpenAction').click();
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
        //return false;
    });

  // notice bar
  $('.jsNoticeBarOpen1').click(function(){
    var targetBar = $($(this).attr('href')),
        targetArea = $('.jsNoticeBarArea1');

    if(targetArea.position().top < 0){
     Brandco.unit.showNoticeBar(targetBar);
    }else{
     Brandco.unit.hideNoticeBar();
    }
    return false;
  });
  $('.jsNoticeBarClose1').click(function(){
   Brandco.unit.hideNoticeBar();
    return false;
  });

  // skeleton select
  $('[name="makeStepTypeRadio"]').click(function(){
    var target = $(this).parents('.makeStepType').find('.makeStepTypeCont');
    $('.makeStepTypeCont').not(target).slideUp(500);
      if(target.css('display') == 'none') {
          target.slideDown(500);
      } else {
          target.slideUp(500);
      }
  });

  $('.jsSortItem').click(function(){
    $('.jsSortItemTarget').toggle();
    return false;
  });

  // step group carousel
  if($('.campaignFlowCont').length>0){
    var flow = $('.campaignFlowCont');
    for (var i = flow.length - 1; i >= 0; i--) {
      var target = $(flow[i]);
      var target = $(flow[i]);
      var targetWidth = 111*target.find('.campaignModule1').length + 1 + 110*target.find('.addModule').length;
      if(targetWidth > 960){
        target.parents('.campaignFlow').width(target.parents('.campaignFlow').width()-40);
        target.parents('.campaignFlowWrap').find('.campaignFlowScroll').show();
        target.width(targetWidth+1);
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

  Brandco.admin.executeStepListWith();

});

// Binding input event to dynamically created elements
$(document).on('input', '.jsReplaceLbComma', function() {
    var txt = $(this).val();
    $(this).val(txt.replace(/\r?\n/g,','));
});

Brandco.admin = (function(){
    return {
        executeStepListWith: function() {
            if($('.stepList').length>0){
                var skeleton = $('.stepList');
                for (var i = skeleton.length - 1; i >= 0; i--) {
                    var target = $(skeleton[i]);
                    var targetWidth = 54 * target.find('.moduleDetail1').length + 21 * target.children('li').length;
                    if(target.find('.addModuleDetail1').length >0){
                        targetWidth += 54 * target.find('.addModuleDetail1').length
                    }
                    if(target.find('.stepDetail_base').length <= 0){
                        targetWidth -= 20;
                    }
                    // 限定キャンペーンの時
                    if(target.find('.stepDetail_base').length > 0 && $('.jsAttractModule').length <= 0) {
                        targetWidth += 11;
                    }
                    if($('.deleteModule').length > 0) {
                        targetWidth += $('.deleteModule').width();
                    }
                    target.width(targetWidth);
                };
            }
        },
        fixedClick: function(param){
            var movePanel = param.parents('.jsPanel');
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
            param.text(innerText);
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
            BrandcoMasonryTopService.sortPanel();
            return false;
        },
        jsPanelSizing: function(param){
            var classes = ['boxSizeSmall', 'boxSizeMiddle', 'boxSizeLarge'],
                targetPanel = param.parents('.jsPanel'),
                panelSize = 0,
                classesLength = classes.length;
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

            BrandcoMasonryTopService.goMason();
        },
        adminCpInit: function(){
            // 公開ボタンクリックイベント
            $("#scheduleCp").click(function(){
               Brandco.unit.closeModal(1);

                var param = {
                    data: "cp_id="+$(this).data('cp')+'&csrf_token='+document.getElementsByName("csrf_token")[0].value,
                    url: $(this).data('url'),
                    success: function(data){
                        if(data.result == 'ok'){
                            // open message
                            window.location.href = $('base').attr('href')+'admin-cp/public_cps';
                        }
                    }
                }
                Brandco.api.callAjaxWithParam(param);

            });

            $('#demoConfirmButton').click(function() {
                Brandco.unit.closeModal(2);
                var param = {
                    data: "cp_id="+$(this).data('cp')+'&csrf_token='+document.getElementsByName("csrf_token")[0].value,
                    url: $(this).data('url'),
                    success: function(data){
                        if(data.result == 'ok'){
                            // open message
                            window.location.href = $('base').attr('href')+'admin-cp/public_cps';
                        }
                    }
                }
                Brandco.api.callAjaxWithParam(param);
            });

            // 送信対象ボタンクリックイベント
            $("#scheduleLimitedCp").click(function(){
                Brandco.unit.closeModal(1);

                cp_id = $(this).data('cp');
                cp_action_id = $(this).data('cp_action');

                var param = {
                    data: "cp_id="+cp_id+'&csrf_token='+document.getElementsByName("csrf_token")[0].value,
                    url: $(this).data('url'),
                    success: function(data){
                        if(data.result == 'ok'){
                            // open message
                            window.location.href = $('base').attr('href')+'admin-cp/edit_action/' + cp_id + "/" + cp_action_id;
                        }
                    }
                }
                Brandco.api.callAjaxWithParam(param);

            });

            $('.licenceBox').click(function(){
                var licence_count = $('.licenceBox').length;
                var agreeAllCondition = true;
                for(var i = 1; i <= licence_count; i++) {
                    if(!$('#condition'+i+'_1').is(':checked')){
                        agreeAllCondition = false;
                        break;
                    }
                }
                if (agreeAllCondition) {
                    $('.disableButton').hide();
                    $('.enableButton').show();
                } else {
                    $('.disableButton').show();
                    $('.enableButton').hide();
                }
            });

            $('.demoLicence').click(function(){
                var agreeAllCondition = true;
                for(var i = 1; i <= 3; i++) {
                    if(!$('#demo_condition_'+i).is(':checked')){
                        agreeAllCondition = false;
                        break;
                    }
                }
                if (agreeAllCondition) {
                    $('.demoDisableButton').hide();
                    $('.demoEnableButton').show();
                } else {
                    $('.demoDisableButton').show();
                    $('.demoEnableButton').hide();
                }
            });
        }

    }
})();