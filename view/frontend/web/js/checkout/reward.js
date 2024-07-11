define([
    'ko',
    'uiComponent',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote',
    'Magento_SalesRule/js/action/set-coupon-code',
    'Magento_SalesRule/js/model/coupon',
    'Magento_Checkout/js/action/get-totals'
], function (ko, Component, customer, quote, setCouponCodeAction, coupon, getTotalsAction) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Boxplosive_Connect/checkout/reward',

            isEnabled: ko.observable(false),
            isSubscribed: ko.observable(''),
            optInText: ko.observable(''),
            formUrl: ko.observable(''),
            isChecked: ko.observable(false),
            resultText: ko.observable(''),
            promotions: ko.observableArray([]),
        },

        initialize() {
            this._super();

            this.setData();
            this.getPoints();

            this.isCheckedComputed = ko.pureComputed({
                read: () => this.isChecked(),
                write: (value) => {
                    this.isChecked(value);
                    this.subscribe();
                },
                owner: this
            });

            return this;
        },

        setData() {
            const config = window.checkoutConfig.boxplosiveConfig;

            if (config) {
                this.isEnabled(config.isEnabled);
                this.isSubscribed(config.isSubscribed);
                this.optInText(config.optInText);
                this.formUrl(config.formUrl);
                this.promotions(config.promotions);
            }
        },

        applyDiscount(form) {
            const baseUrl = window.location.origin;
            const serviceUrl = `/rest/V1/boxplosive/discount/quote_id/${quote.getQuoteId()}`;
            const checkedInputs = Array.from(form.querySelectorAll('input[type="checkbox"]:checked'));
            const data = {};

            checkedInputs.forEach((checkbox) => {
                data[checkbox.name] = checkbox.value;
            });

            fetch(`${baseUrl}${serviceUrl}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            }).then(resp => {
                if (resp === true) {
                    var deferred = $.Deferred();
                    getTotalsAction([], deferred);
                } else {
                    throw new Error('Response was false');
                }
            }).catch(err => {
                console.error(err);
            });
        },

        getPoints() {
            let serviceUrl = '';
            const baseUrl = window.location.origin;
            const config = window.checkoutConfig.boxplosiveConfig;

            if (customer.isLoggedIn()) {
                serviceUrl = '/rest/V1/boxplosive/transaction/get-subtotal/' + quote.getQuoteId()
            } else {
                serviceUrl = '/rest/V1/boxplosive/transaction/guest-cart/' + quote.getQuoteId() + '/get-subtotal'
            }

            fetch(`${baseUrl}${serviceUrl}`)
                .then(resp => resp.json())
                .then(json => {
                    if (json[0].success) {
                        if (this.isSubscribed() === 'subscribed') {
                            this.resultText(config.loyaltyText.replace('{{points}}', json[0].points));
                        } else {
                            this.resultText(config.nonLoyaltyText.replace('{{points}}', json[0].points));
                        }
                    }
                })
            .catch(err => {
                console.error(err);
            });
        },

        subscribe() {
            const baseUrl = window.location.origin;
            const subscribePath = 'rest/V1/boxplosive/subscribe';
            const quotePath = `quote_id/${quote.getQuoteId()}`;
            const checked = this.isChecked() ? '1' : '0';

            fetch(`${baseUrl}/${subscribePath}/${checked}/${quotePath}`, { method: 'POST'});
        }
    });
});
