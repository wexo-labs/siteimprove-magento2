define([
    'underscore',
    'siteimprove',
    'Magento_Ui/js/form/provider'
], function (_, siteimprove, Provider) {
    'use strict';

    return Provider.extend({
        initialize: function () {
            Provider.prototype.initialize.apply(this, arguments);
            this.updateSiteimproveInput();
            return this;
        },

        updateSiteimproveInput: function () {
            if (this.data) {
                if (this.data.product && this.data.product._siteimprove_token) {
                    siteimprove({
                        'action': 'input',
                        'url': this.data.product._siteimprove_url,
                        'token': this.data.product._siteimprove_token
                    })
                } else if (this.data._siteimprove_token) {
                    if (this.data._siteimprove && this.data._siteimprove.length) {
                        var frontendUrl;
                        if (this.data.current_store_id && this.data._siteimprove[this.data.current_store_id]) {
                            frontendUrl = this.data._siteimprove[this.data.current_store_id];
                        } else {
                            frontendUrl = _.first(_.values(this.data._siteimprove));
                        }
                        siteimprove({
                            'action': 'input',
                            'url': frontendUrl,
                            'token': this.data._siteimprove_token
                        })
                    } else {
                        siteimprove({
                            'action': 'input',
                            'token': this.data._siteimprove_token
                        })
                    }
                }
            }
        }
    });
});
