var ConversionSettingsFormService = (function(){
    return {
        changeTagValue: function(order_no, order_price, order_count, free1, free2, free3, free4){
            var data = $('#data-container');
            var script = '<script type="text/javascript">\n';
            script += ' var __btr = {\n    brand_id : '+data.data('brand-id')+',\n    conversion_id : '+data.data('conversion-id');
            script += ',\n    order_no : "'+ (order_no ? order_no : '[ここに入力してください]') + '"';
            script += ',\n    order_price : "'+ (order_price ? order_price : '[ここに入力してください]') + '"';
            script += ',\n    order_count : "'+ (order_count ? order_count : '[ここに入力してください]') + '"';
            script += ',\n    free1 : '+ (free1 ? '"'+free1+'"' : '""');
            script += ',\n    free2 : '+ (free2 ? '"'+free2+'"' : '""');
            script += ',\n    free3 : '+ (free3 ? '"'+free3+'"' : '""');
            script += ',\n    free4 : '+ (free4 ? '"'+free4+'"' : '""');
            script += ',\n    tracker : "'+ data.data('tracker-domain')+'"';
            script += '\n};';
            script += '\n(function(){\n    var s = document.createElement(\'script\');';
            script += '\n    s.src= location.protocol + "//'+data.data('static-track-domain')+'/launch.js";';
            script += '\n    var x =document.getElementsByTagName(\'script\')[0];';
            script += '\n    x.parentNode.insertBefore(s,x);\n})();\n</script>';
            $('#js_tag').val(script);
        },
        changeCart: function(cart_type) {
            switch (cart_type) {
                case "1":
                    ConversionSettingsFormService.changeTagValue('$ORDER_ID$', '$TOTAL_GOODS_AMOUNT$', '$TOTAL_BUY_COUNT_GOODS$');
                    break;
                case "2":
                    ConversionSettingsFormService.changeTagValue('[ORDER_NUM]', '[TOTAL_AMOUNT]', '[TOTAL_ITEM_COUNT]', '[USER_ID]');
                    break;
                case "3":
                    ConversionSettingsFormService.changeTagValue('%%ORDER_NO%%', '%%SYOUKEI%%', '%%ITEM_NUMBER%%');
                    break;
                default:
                    ConversionSettingsFormService.changeTagValue();
                    break
            }
        }
    }
})();
$(document).ready(function(){

   ConversionSettingsFormService.changeCart($('#cartSelector').val());

   $('#save_conversion').click(function(){
      document.editConversion.submit();
   });

   $('#cartSelector').on('change', function() {
       var csrf_token = document.getElementsByName("csrf_token")[0].value,
       param = {
           data: 'cart_type='+$(this).val()+'&csrf_token=' + csrf_token,
           url: 'admin-settings/api_change_cart_type.json',
           success: function(data){
               if(data && data.result == 'ok'){
                   ConversionSettingsFormService.changeCart($('#cartSelector').val());
               } else {
                   ConversionSettingsFormService.changeCart();
               }
           }
       };
       Brandco.api.callAjaxWithParam(param);

   });
});
