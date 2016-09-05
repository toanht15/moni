if (typeof(CommentPluginUtil) === 'undefined') {
    var $moniJq = $moniJq || jQuery.noConflict(true);
    var CommentPluginUtil = (function (window) {
        return {
            isFramed: function () {
                return window.opener == null && (!!window.top && window != window.top || !!window.parent && window != window.parent);
            },
            updateCounter: function (target, toValue, duration) {
                $moniJq(target).prop('Counter', $moniJq(target).html())
                    .animate({
                        Counter: toValue
                    }, {
                        duration: typeof duration !== 'undefined' ? duration : 100,
                        easing: 'swing',
                        step: function (now) {
                            $moniJq(this).text(Math.ceil(now));
                        }
                    });
            },
            fetchLocationHash: function () {
                if (document.location.hash && document.location.hash !== '#_=_') {
                    return document.location.hash;
                }

                return '';
            },
            isEmpty: function (string) {
                var trimming = string.trim();

                if (trimming.length === 0) return true;
                if (trimming === '') return true;
                if (trimming.replace(/\s/g, '') === '') return true;
                if (/^\s*$/.test(trimming)) return true;

                return false;
            },
            validateText: function (commentText) {
                if (!commentText || commentText.length === 0) { return false; }

                if (CommentPluginUtil.isEmpty(commentText)) { return false; }

                var tempContainer = $moniJq('<div/>').html(commentText);
                if (CommentPluginUtil.isEmpty(tempContainer.get(0).textContent)) { return false; }

                if (tempContainer.children().length === 0) { return true; }

                tempContainer.children().filter(function () {
                    return CommentPluginUtil.isEmpty($moniJq(this).get(0).textContent);
                }).remove();

                return tempContainer.children().length !== 0;
            },
            parseText: function (target) {
                var cloneElement = $moniJq(target).clone();

                cloneElement.children().filter(function () {
                    var nodeName = $moniJq(this).get(0).nodeName.toLowerCase();

                    if (nodeName !== 'p' && nodeName !== 'div') {
                        return true;
                    }

                    var innerHTML = $moniJq(this).get(0).innerHTML;
                    return innerHTML === '<br>' || innerHTML === '' || innerHTML === 'undefined';
                }).remove();

                return $moniJq(cloneElement).html();
            },
            getDocHeight: function () {
                var D = document;
                return Math.max(
                    Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
                    Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
                    Math.max(D.body.clientHeight, D.documentElement.clientHeight)
                );
            },
            handleEvent: function (event, handler) {
                if (window.addEventListener) {
                    window.addEventListener(event, handler, false);
                } else if (window.attachEvent) {
                    window.attachEvent('on' + event, handler);
                } else {
                    window['on' + event] = handler;
                }
            },
            showFrameLoading: function (target, isBefore) {
                var loading = $moniJq('#loading');
                if (isBefore) { loading.insertBefore(target); }
                else { loading.insertAfter(target); }
                loading.show();
            },
            showLoading: function (target, isBefore) {
                if (CommentPluginUtil.isFramed()) {
                    CommentPluginUtil.showFrameLoading(target, isBefore);
                    CommentPluginService.sendIframeSize();
                } else {
                    Brandco.helper.showLoading(target);
                }
            },
            hideLoading: function () {
                Brandco.helper.hideLoading();
            },
            callAjaxWithParam: function (params) {
                if (CommentPluginUtil.isFramed()) {
                    Brandco.api.callAjaxWithParam(params, false, false);
                } else {
                    Brandco.api.callAjaxWithParam(params);
                }
            }
        };
    })(window);
}