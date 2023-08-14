define([
    'ko',
    'uiComponent'
], function (ko, Component) {
    'use strict';

    return Component.extend({
        initialize: function () {
            //initialize parent Component
            this._super();
            this.qty = ko.observable(this.defaultQty);
        },

        decreaseQty: function() {
            let newQty = this.qty() <= 1 ? 1 : this.qty() - 1;
            this.qty(newQty);
        },

        increaseQty: function() {
            let newQty = this.qty() + 1;
            this.qty(newQty);
        }
    });
});

