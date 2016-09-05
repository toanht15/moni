if (typeof(SynService) === 'undefined') {
    var SynService = function() {
        return {
            generateSynExtensionCode: function() {
                var _lgy_lw = document.createElement("script");
                _lgy_lw.type = "text/javascript";
                _lgy_lw.charset = "UTF-8";
                _lgy_lw.async = true;
                _lgy_lw.src= (("https:" == document.location.protocol) ? "https://" : "http://")+"l.logly.co.jp/lift_widget.js?adspot_id=4088597";
                var _lgy_lw_0 = document.getElementsByTagName("script")[0];
                _lgy_lw_0.parentNode.insertBefore(_lgy_lw, _lgy_lw_0);
            }
        };
    }();
}
