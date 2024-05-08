define(['jquery'], function($) {
    'use strict';

    return function(configData) {
        console.log("sales email : ", configData.emailTemplate);
        console.log("payment methods : ", configData.paymentMethods);
    }
})