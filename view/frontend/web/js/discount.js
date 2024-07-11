define([
    'ko',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/quote'
], function (ko, Component, totals, quote) {
        "use strict";

    return Component.extend({
        defaults: {
            segmentlIndex: ko.observable(0),
            boxplosiveTotals: ko.observableArray([])
        },

        initialize() {
            this._super();

            this.getTotals();
            quote.totals.subscribe(() => this.getTotals());

            return this;
        },

        getDiscount() {
            return totals.getSegment(`boxplosive_discount_${this.segmentlIndex()}`);
        },

        getTotals() {
            this.segmentlIndex(0);
            this.boxplosiveTotals([]);

            while (this.getDiscount()) {
                this.boxplosiveTotals.push(this.getDiscount());
                this.segmentlIndex(this.segmentlIndex() + 1);
            }
        },

        formatPrice(price) {
            return this.getFormattedPrice(price);
        },
    });
});
