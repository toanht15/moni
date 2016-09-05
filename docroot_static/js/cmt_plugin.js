try {
    (function (window) {
        var document = window.document,
            pluginClass = 'jsMoniPlugin',
            pluginIdPrefix = 'monipla-plugin-container-',
            pluginContainerClass = 'jsMoniPluginContainer';

        function getCurrentUrl() {
            return window.location.href;
        }

        function fetchPluginUrl(pluginContainer) {
            var pluginUrl = pluginContainer.getAttribute('data-href');
            pluginUrl += '?u=' + encodeURI(getCurrentUrl());
            return pluginUrl;
        }

        function handleEvent(event, handler) {
            if (window.addEventListener) {
                window.addEventListener(event, handler, false);
            } else if (window.attachEvent) {
                window.attachEvent('on' + event, handler);
            } else {
                window['on' + event] = handler;
            }
        }

        function appendPlugin(pluginContainer, pluginUrl, index) {
            var commentPlugin,
                commentPluginId = pluginIdPrefix + index;

            commentPlugin = document.createElement('iframe');
            commentPlugin.className = pluginClass;
            commentPlugin.id = commentPluginId;
            commentPlugin.src = pluginUrl;
            commentPlugin.setAttribute('frameBorder', 0);
            commentPlugin.setAttribute('height', 0);
            commentPlugin.setAttribute('width', '100%');
            commentPlugin.setAttribute('scrolling', 'no');
            pluginContainer.appendChild(commentPlugin);
        }

        function parsePluginContainers() {
            var pluginContainers = document.getElementsByClassName(pluginContainerClass),
                index;

            for (index = 0; index < pluginContainers.length; index++) {
                var pluginContainer = pluginContainers[index],
                    pluginUrl = fetchPluginUrl(pluginContainer);

                if (pluginUrl === null) {
                    continue;
                }

                appendPlugin(pluginContainer, pluginUrl, index + 1);

                var eventHandler = handleEvent;
                eventHandler('load', function() {
                    initIframe();
                }, false);
            }
        }

        function createPluginMessage(pluginId, currentUrl) {
            var pluginMessage = {};
            pluginMessage.pluginId = pluginId;
            pluginMessage.origin = currentUrl;

            return pluginMessage;
        }

        function initIframe() {
            var pluginContainers = document.getElementsByClassName(pluginClass),
                currentUrl = getCurrentUrl();

            for (var index = 0; index < pluginContainers.length; index++) {
                var pluginContainer = pluginContainers[index],
                    pluginMessage = createPluginMessage(pluginContainer.id, currentUrl);

                pluginContainer.contentWindow.postMessage(pluginMessage, '*');
            }
        }

        function scrollTo(element, toPosition, duration) {
            if (duration <= 0) return;

            var diff = toPosition - element.scrollTop,
                perTick = diff / duration * 10;

            setTimeout(function () {
                element.scrollTop = element.scrollTop + perTick;
                if (element.scrollTop == toPosition) return;
                scrollTo(element, toPosition, duration - 10);
            }, 10);
        }

        function resizeIframe(data) {
            if (!data.pluginId) return;
            var pluginContainer = document.getElementById(data.pluginId);
            pluginContainer.height = parseInt(data.messageContent, 10) + 'px';
        }

        function getTopOffset(element) {
            var bodyRect = window.document.body.getBoundingClientRect(),
                elemRect = element.getBoundingClientRect();

            return elemRect.top - bodyRect.top;
        }

        function autoScroll(data) {
            if (!data.pluginId) return;
            var pluginContainer = document.getElementById(data.pluginId),
                offset = getTopOffset(pluginContainer);

            scrollTo(window.document.body, offset + data.messageContent, 500);
        }

        function messageEventHandler(event) {
            switch (event.data.actionType) {
                case 1:
                    resizeIframe(event.data);
                    break;
                case 2:
                    autoScroll(event.data);
                    break;
            }
        }

        function initPlugin() {
            parsePluginContainers();

            var eventHandler = handleEvent;
            eventHandler('message', function (event) {
                messageEventHandler(event);
            }, false);
        }

        initPlugin();
    })(window.inDapIF ? parent.window : window);
} catch (e) {
    console.warn('Failed to load comment plugin');
}
