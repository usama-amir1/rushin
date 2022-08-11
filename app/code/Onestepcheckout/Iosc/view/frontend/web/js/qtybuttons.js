/**
 * OneStepCheckout
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to One Step Checkout AS software license.
 *
 * License is available through the world-wide-web at this URL:
 * https://www.onestepcheckout.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mail@onestepcheckout.com so we can send you a copy immediately.
 *
 * @category   onestepcheckout
 * @package    onestepcheckout_iosc
 * @copyright  Copyright (c) 2017 OneStepCheckout  (https://www.onestepcheckout.com/)
 * @license    https://www.onestepcheckout.com/LICENSE.txt
 */
define(
    [
        'uiComponent',
        'uiRegistry',
        'jquery',
        'ko',
        'PHPStudios_DualCheckout/js/view/payment/method-renderer/authnetcim',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/get-totals',
        'Magento_Ui/js/modal/confirm',
        'Magento_Checkout/js/action/redirect-on-success'
    ],
    function (
        uiComponent,
        uiRegistry,
        $,
        ko,
        authnetcim,
        quote,
        getTotalsAction,
        confirmation,
        redirectOnSuccessAction
    ) {
        "use strict";
        return uiComponent.extend({

            initialize: function () {
                this._super();
                this.updateItem = ko.observable(null);
                uiRegistry.async('checkout.iosc.ajax')(
                    function (ajax) {
                        ajax.addMethod('params', 'updateqty', this.paramsHandler.bind(this));
                        ajax.addMethod('success', 'updateqty', this.successHandler.bind(this));
                    }.bind(this)
                )
            },

            paramsHandler: function() {
                var data = this.updateItem();
                if(data !== null) {
                    data = {'item_id': data.item_id, 'qty': data.qty};
                }
                return data ;
            },

            successHandler: function(response) {
                if (this._has(response , "data.updateqty.cart_items")) {
                    if(response.data.updateqty.cart_items <= 0) {
                        redirectOnSuccessAction.redirectUrl = window.checkoutConfig.cartUrl;
                        redirectOnSuccessAction.execute()
                    }
                }
            },

            _has: function (obj, key) {
                return key.split(".").every(
                    function (x) {
                        if (typeof obj !== "object" || obj === null || !(x in obj)) {
                            return false;
                        }
                        obj = obj[x];
                        return true;
                    }
                );
            },

            updateCart: function(elem, qty) {
                uiRegistry.async('checkout.iosc.ajax')(
                    function (ajax) {
                       elem.qty = qty;
                       this.updateItem(elem);
                       ajax.update();
                       this.updateItem(null);
                    }.bind(this)
                );
                this.saveData();
            },

            saveData: function () {
                if (!($('input.emailField').val() && $('input.percentageField').val())) {
                    return;
                }
                var arr = [], emailArr = [], percentArr = [], my_percent = $('.payment_percent').val();
                $(".add-info-msg").text("");
                $('input.emailField').each(function () {
                    var email = $(this).val();
                    if (!email) {
                        $(".add-info-msg").append("One of the email is empty <br>");
                        // $('.add-info-msg').css('display', 'block');
                        $('.add-info-msg').show();
                        setTimeout(function () {
                            // $('.add-info-msg').css('display', 'none');
                            $('.add-info-msg').hide();
                        }, 4000);
                        return;
                    }
                    emailArr.push(email);
                });
                $('input.percentageField').each(function () {
                    var percent = $(this).val();
                    if (!percent) {
                        $(".add-info-msg").append("One of the percentage field is empty <br>");
                        // $('.add-info-msg').css('display', 'block');
                        $('.add-info-msg').show();
                        setTimeout(function () {
                            // $('.add-info-msg').css('display', 'none');
                            $('.add-info-msg').hide();
                        }, 4000);
                        return;
                    }
                    percentArr.push(percent);
                });

                arr.push(emailArr);
                arr.push(percentArr);
                arr.push(my_percent);
                arr.push(quote.getTotals()._latestValue.subtotal);
                window.payees = arr;
                window.checkoutConfig.payee = arr;

                $.ajax({
                    url: "/rest/V1/ajax/save/",
                    data: {
                        payeeData: JSON.stringify(arr)
                    },
                    type: 'POST',
                    contentType: "application/json",
                    dataType: "json",
                    showLoader: true,
                    success: function(data, status, xhr) {
                        console.log("Success");
                        var result = JSON.parse(data)
                        $(".add-info-msg").append(result.message + '<br>');
                        $('.add-info-msg').show();
                        setTimeout(function () {
                            $('.add-info-msg').hide();
                        }, 4000);
                        window.checkoutConfig.anp = result.ANP;
                        window.checkoutConfig.ra = result.RA;
                        var deferred = $.Deferred();
                        getTotalsAction([], deferred);
                    },
                    error: function (xhr, status, errorThrown) {
                        console.log('Error happens. Try again.');
                        console.log(errorThrown);
                    }
                });

                // var payee_percent = percentArr.reduce(function (a, b) {
                //     return a + b;
                // }, 0);
                //
                // if (!(Number(my_percent) + Number(payee_percent) == 100)) {
                //     return;
                // }

                // $('input.save').on('click', function () {
                //     if ($('input.save').val()) {
                //         ajaxValue = {
                //             'ajaxvalue': $('input.save').val();
                //         };
                //         //#ajaxcallvalue this text box id seletor
                //         var serviceUrlCreate, serviceUrl, payload;
                //         /**
                //          * Save  values .
                //          */
                //         serviceUrl = 'rest/V1/ajax/save';
                //         payload = {
                //             optionval: JSON.stringify(ajaxValue),
                //         };
                //         storage.post(
                //             serviceUrl,
                //             JSON.stringify(payload)
                //         ).done(function (response) {
                //             alert({
                //                 content: $t('Action Successfully completed.')
                //             });
                //         }).fail(function (response) {
                //             alert({
                //                 content: $t('There was error during saving data')
                //             });
                //         });
                //     }
                // });

                // var deferred = $.Deferred();
                // getTotalsAction([], deferred);

                // var sections = ['checkout-data'];
                // // customerData.invalidate(sections);
                // customerData.reload(sections, true);
                //
                // var newUrl = baseUrl + '/loadSummary';
                // $.ajax({
                //     url: newUrl,
                //
                //     data: {},
                //     type: "post",
                //     cache: false,
                //     success: function (data) {
                //         var deferred = $.Deferred();
                //         getTotalsAction([], deferred); //this will reload the order summary section I have used it in my custom module
                //     }
                // });


                // $(".add-info-msg").append(text);
                // $('.add-info-msg').css('display','block');

                // var email1 = $("#field1 .email-field input.emailField").value;
                // var percent1 = $("#field1 .percentage-field input.percentageField").value;
            },

            add: function (elem) {
                if(elem.qty <= 0) {
                    elem.qty = 0;
                }
                var qty = parseInt(elem.qty) + 1;
                this.updateCart(elem, qty);
            },

            sub: function (elem) {
                if(elem.qty <= 0) {
                    elem.qty = 1;
                }
                var qty = parseInt(elem.qty) - 1;
                if(qty === 0){
                    this.delConfirm(elem, qty);
                } else {
                    this.updateCart(elem, qty);
                }
            },

            delConfirm: function(elem) {
                var qty = 0;
                var self = this;
                confirmation({
                    title: $.mage.__('Deleting from cart!'),
                    content: $.mage.__('Are you sure you want to remove this item from your cart?'),
                    actions: {
                        confirm: function() {this.del(elem, qty)}.bind(self, elem, qty),
                        cancel: function(){},
                        always: function(){}
                    }
                });
            },

            del: function(elem, qty) {
                this.updateCart(elem, qty);
            }
        });
    }
);
