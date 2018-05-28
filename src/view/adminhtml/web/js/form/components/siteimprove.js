define([
    'underscore',
    'siteimprove',
    'Magento_Ui/js/form/components/html'
], function (_, siteimprove, Html) {
    'use strict';

    return Html.extend({
        initialize: function () {
            Html.prototype.initialize.apply(this, arguments);
            this.reloadInput();
            return this;
        },

        /**
         * Get current frontend url and reload Siteimprove "input"/overlay with it
         */
        reloadInput: function() {
            this.siteimproveInput(this.getFrontendUrl());
        },

        /**
         * @param {string} frontendUrl Url to show data about in overlay
         * @returns {boolean} Indicate if token was present when trying to send data to Siteimprove
         */
        siteimproveInput: function(frontendUrl) {
            var data = this.getData();
            if (data._siteimprove_token) {
                siteimprove({
                    'action': 'input',
                    'url': frontendUrl,
                    'token': data._siteimprove_token
                });
                return true;
            }

            siteimprove({
                'action': 'clear'
            });
            return false;
        },

        /**
         * @returns {string|null}
         */
        getFrontendUrl: function() {
            var data = this.getData();
            var frontendUrl = null;
            if (data && !_.isEmpty(data._siteimprove)) {
                if (data.current_store_id && data._siteimprove[data.current_store_id]) {
                    frontendUrl = data._siteimprove[data.current_store_id];
                } else {
                    frontendUrl = _.first(_.values(data._siteimprove));
                }
            }
            return frontendUrl;
        },

        /**
         * @returns {Object.<number, string>}
         */
        getData: function() {
            return this.source && this.source.data;
        }
    });
});
