jQuery(function ($) {

    $('select').chosen( { width: '100%' } );

    $('#cashpay').on('change', function (event) {
        if( $("#cashpay").prop('checked') == true){
            $('.gateway-settings-cashpay').removeClass('hidden');
        }else {
            $('.gateway-settings-cashpay').addClass('hidden');
        }
    });

    $('#paypal').on('change', function (event) {
        if( $("#paypal").prop('checked') == true){
            $('.gateway-settings-paypal').removeClass('hidden');
        }else {
            $('.gateway-settings-paypal').addClass('hidden');
        }
    });

    $('#testpay').on('change', function (event) {
        if( $("#testpay").prop('checked') == true){
            $('.gateway-settings-testpay').removeClass('hidden');
        }else {
            $('.gateway-settings-testpay').addClass('hidden');
        }
    });

    $('#chequepay').on('change', function (event) {
        if( $("#chequepay").prop('checked') == true){
            $('.gateway-settings-chequepay').removeClass('hidden');
        }else {
            $('.gateway-settings-chequepay').addClass('hidden');
        }
    });

});