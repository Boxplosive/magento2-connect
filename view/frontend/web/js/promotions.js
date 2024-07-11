define([
    'ko',
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote',
    'uiComponent'
],function(ko, $, modal, customer, quote, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            resultText: ko.observable(''),
            isShow: ko.observable(false),
            hideLoader: ko.observable(false),
        },

        initialize() {
            this._super();

            this.getPoints();
            return this;
        },

        initModal() {
            const boxplosiveButton = document.querySelector('[data-element="boxplosive-show-modal"]');
            const boxplosiveModal = $('[data-element="boxplosive-modal"]');

            const options = {
                type: 'popup',
                modalClass: 'promotions-modal',
                buttons: [],
            }

            const content = modal(options, boxplosiveModal);

            boxplosiveButton.addEventListener('click', () => {
                boxplosiveModal.modal('openModal');
            });

            content.afterOpen = function () {
                ko.applyBindings({}, document.querySelector('[data-element="boxplosive-modal"]'));
            };
        },

        getPoints() {
            let serviceUrl = '';
            const baseUrl = window.location.origin;

            if (customer.isLoggedIn()) {
                serviceUrl = '/rest/V1/boxplosive/transaction/get-subtotal/' + quote.getQuoteId()
            } else {
                serviceUrl = '/rest/V1/boxplosive/transaction/guest-cart/' + quote.getQuoteId() + '/get-subtotal'
            }

            fetch(`${baseUrl}${serviceUrl}`)
                .then(resp => resp.json())
                .then(json => {
                    if (json[0].success) {
                        if (this.displayPromotions) {
                            this.resultText(this.loyaltyText.replace('{{points}}', json[0].points));
                            this.initModal();
                        } else {
                            this.resultText(this.nonLoyaltyText.replace('{{points}}', json[0].points));
                        }

                        this.isShow(true);
                    } else {
                        this.hideLoader(true);
                    }
                })
                .catch(err => {
                    console.error(err);
                });
        }
    });
});
