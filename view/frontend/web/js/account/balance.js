define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/alert'
], function (ko, $, Component, alert) {
    'use strict';

    return Component.extend({
        defaults: {
            balance: ko.observableArray([]),
            hideLoader: ko.observable(false),
        },

        initialize() {
            this._super();
            this.getBalance();

            return this;
        },

        getBalance() {
            const baseUrl = window.location.origin;
            let serviceUrl = `/rest/V1/boxplosive/balance/customer_id/${this.customerId}`;

            fetch(`${baseUrl}${serviceUrl}`)
            .then((resp) => resp.json())
            .then((json) => {
                this.balance(json);
                this.hideLoader(true);
            })
        },

        unsubscribe() {
            const unsubscribeUrl = this.unsubscribeUrl;
            const optOutText = this.optOutText;

            alert({
                title: $.mage.__('Unsubscribe'),
                content: optOutText,
                buttons: [{
                    text: $.mage.__('Confirm'),
                    class: 'action primary',
                    click() {
                        window.location.replace(unsubscribeUrl);
                        this.closeModal(true);
                    }
                }]
            });
        }
    });
});
