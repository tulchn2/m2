/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/region'
], function (Region) {
    'use strict';

    return Region.extend({
        defaults: {
            regionScope: 'data.region',
            regionNameScope: 'data.default_name',
            regionCodeScope: 'data.code'
        },

        /**
         * Set region to source form
         *
         * @param {String} value - region
         */
        setDifferedFromDefault: function (value) {
            this._super();
            if (parseFloat(value)) {
                this.source.set(this.regionScope, this.indexedOptions[value].label);
                this.source.set(this.regionNameScope, this.indexedOptions[value].label);
                this.source.set(this.regionCodeScope, this.indexedOptions[value].code);
            }
        }
    });
});
