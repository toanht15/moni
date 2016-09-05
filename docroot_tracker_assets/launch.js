(function(d,l,e,ev){
    ev(window,'load',function(){
        if (!__btr || !__btr.brand_id || !__btr.conversion_id || !__btr.tracker) return ;

        var docCookies = {
            getItem: function (sKey) {
                if (!sKey || !this.hasItem(sKey)) { return null; }
                return decodeURIComponent(document.cookie.replace(new RegExp("(?:^|.*;\\s*)" + encodeURIComponent(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*((?:[^;](?!;))*[^;]?).*"), "$1"));
            },
            setItem: function (sKey, sValue, vEnd, sPath, sDomain, bSecure) {
                if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/i.test(sKey)) { return; }
                var sExpires = "";
                if (vEnd) {
                    sExpires = vEnd === Infinity ? "; expires=Tue, 19 Jan 2038 03:14:07 GMT" : "; max-age=" + vEnd;
                }
                document.cookie = encodeURIComponent(sKey) + "=" + encodeURIComponent(sValue) + sExpires + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "") + (bSecure ? "; secure" : "");
            },
            hasItem: function (sKey) {
                return (new RegExp("(?:^|;\\s*)" + encodeURIComponent(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=")).test(document.cookie);
            }
        };
        function getUrlVars(){
            var vars = {};
            var param = location.search.substring(1).split('&');
            for(var i = 0; i < param.length; i++) {
                var keySearch = param[i].search(/=/);
                var key = '';
                if(keySearch != -1) key = param[i].slice(0, keySearch);
                var val = param[i].slice(param[i].indexOf('=', 0) + 1);
                if(key != '') vars[key] = decodeURI(val);
            }
            return vars;
        }
        function getMpUid() {
            _mp_uid = docCookies.getItem('_mp_uid');
            if(_mp_uid==null && getUrlVars()['_mp_uid']){
                _mp_uid = getUrlVars()['_mp_uid'];
            }
            if(_mp_uid){
                docCookies.setItem('_mp_uid',_mp_uid,60*60*24*365);
            }

            return _mp_uid;
        }
        var i = d.createElement('img');
        i.src = 'https://'+__btr.tracker+'/tracker?';
        i.src += 'request_uri=' + e(l.href) + '&referrer=' + e(d.referrer);
        if(!__btr['_mp_uid']) {
            __btr['_mp_uid'] = getMpUid();
        }
        for(var key in __btr){
            if (key == 'tracker') {
                continue;
            }
            i.src += '&' + key + '=' + e(__btr[key]);
        }

        var s = d.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(i,s);
        ev(i,'load',function(){
            i.style.display = 'none';
        });
    });
})(document,location,encodeURIComponent,(function(){
    if ( window.addEventListener ) return function(el, type, fn) {el.addEventListener(type, fn, false);}
    else if ( window.attachEvent ) return function(el, type, fn) {el.attachEvent('on'+type, function() {fn.call(el, window.event);}); }
    else return function(el, type, fn) {el['on'+type] = fn;}
})());