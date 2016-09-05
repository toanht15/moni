$('document').ready(function(){
  if (!syn_menu_is_production) {
    Synapse.endpoint = 'https://synapse-api.stg.bitcellar.net';
    Synapse.Logger.log_level = Synapse.Logger.DEBUG
  }

  menu = new Synapse.Menu(syn_menu_menu_name);
  syn_menu = menu;

  menu.addListener('service_list_load', function(){
    menu.serviceList.serviceListItems.forEach(function(item){
      listElement = $('<li>');
      itemElement = item.toHTMLElement();
      var firstInView = false;

      $(itemElement).on('inview', function(event, isInView){
        if (isInView) {
          if (!firstInView) {
            item.trackShowEvent();
          }
          firstInView = true;
        } else {
          firstInView = false;
        }
      });
      $(itemElement).find('a').attr('class','jsClickMenu');
      listElement.append(itemElement);
      $('#synapse-service-list').append(listElement);
    });

    if (menu.serviceList.serviceListItems != undefined && menu.serviceList.serviceListItems.length > 0) {
      $('#synapse-service-list-outer-box').css('display', 'block');
      $('#synapse-logo-box').css('display', 'block');
    }

  });

  menu.addListener('service_notification_load', function(){
    if (menu.serviceNotification.serviceNotificationItems.length > 0) {
      $('.syn-notice').addClass('has-notification');
    } else {
      $('.syn-notice').removeClass('has-notification');
    }
  });

  menu.init();

  $(document).on('click','.jsClickMenu',function() {
    csrf_token = $('#synapse-service-list-outer-box [name=csrf_token]').val();
    cp_id = $('#synapse-service-list-outer-box [name=cp_id]').val();

    var param = {
      async:false,
      timeout: 2000,
      data: {
        "cp_id" : cp_id,
        "csrf_token": csrf_token
      },
          url: '/syn_campaign/api_click_menu.json'
    };
      Brandco.api.callAjaxWithParam(param, false, false);
  });
});
