if (typeof(GoogleAnalyticsTrackingService) === 'undefined') {
    var GoogleAnalyticsTrackingService = function() {
        return {
            generate: function(id, tracker_name, ga_param) {
                ga('create', id, 'auto', {'name': tracker_name});
                ga(tracker_name + '.require', 'ec');

                $('.jsGoogleAnalyticsTrackingAction').each(function() {
                    GoogleAnalyticsTrackingService.setAction(this, tracker_name);
                    $(this).remove();
                });

                ga(tracker_name + '.send', 'pageview', ga_param);
            },

            setAction: function(target, tracker_name) {
                var product = !$(target).data('product') ? {} : $(target).data('product');
                var action = $(target).data('action');
                var action_params = !$(target).data('action_params') ? {} : $(target).data('action_params');

                ga(tracker_name + '.ec:addProduct', product);
                if (!action_params) {
                    ga(tracker_name + '.ec:setAction', action);
                } else {
                    ga(tracker_name + '.ec:setAction', action, action_params);
                }
            }
        };
    }();
}
