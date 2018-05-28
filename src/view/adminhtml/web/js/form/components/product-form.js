define([
    'underscore',
    'siteimprove',
    'Siteimprove_Magento/js/form/components/siteimprove'
], function (_, siteimprove, SiComponent) {
    'use strict';

    return SiComponent.extend({

        /**
         * @returns {Object.<number, string>}
         */
        getData: function () {
            return this.source && this.source.data && this.source.data.product;
        }
    });
});
