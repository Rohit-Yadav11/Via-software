jQuery(function($){

    /**
     * License
     */

    $('#licreq').on('click', function () {
        if($(this).is(":checked")) {
            $('.file-price-field').hide();
            $('.file-price-table').show();
            $('#licopt').slideDown();
        }
        else {
            $('.file-price-field').show();
            $('.file-price-table').hide();
            $('#licopt').slideUp();
        }

    });
    $('.lic-enable').each(function () {
        if($(this).is(":checked") && !$(this).is(":disabled")) {
            $("#lic-price-" + $(this).data('lic')).removeAttr('disabled');
            $(".lic-file-price-" + $(this).data('lic')).removeAttr('disabled');

        }
        else {
            $("#lic-price-" + $(this).data('lic')).attr('disabled', 'disabled');
            if(!$(this).is(":checked"))
                $(".lic-file-price-" + $(this).data('lic')).attr('disabled', 'disabled');
        }
    });
    $('.lic-enable').on('click', function () {
        if($(this).is(":checked") && !$(this).is(":disabled")) {
            $("#lic-price-" + $(this).data('lic')).removeAttr('disabled');
            $(".lic-file-price-" + $(this).data('lic')).removeAttr('disabled');
        }
        else {
            $("#lic-price-" + $(this).data('lic')).attr('disabled', 'disabled');
            if(!$(this).is(":checked"))
                $(".lic-file-price-" + $(this).data('lic')).attr('disabled', 'disabled');
        }
    });

    $('#sales-price-expire-field, .coupon_expire').datetimepicker({dateFormat:"yy-mm-dd", timeFormat: "hh:mm tt"});

    /**
     *  Re-calculate Sales Amount of a product
     */
    $('.recal-sa').on('click', function(e){
        e.preventDefault();
        var $this = $(this);
        var id = $(this).attr('rel');
        $this.html("<i class='fa fa-spinner fa-spin'></i>");
        $.post(ajaxurl, {action: 'RecalculateSales', id: $(this).attr('rel')}, function(res){
            $this.html(res.sales_amount);
            $('#sc-'+id).html(res.sales_quantity);
        });
    });


    /**
     *  Settings >> Premium Package >> Basic Settings >> License Settings
     */

    $('body').on('click', '.del-lic', function () {
        if(!confirm('Are you sure?')) return false;
        $($(this).data('rowid')).remove();
    });
    $('body').on('click', '#addlicenses', function () {
        var licname = prompt("Enter License Name:");
        if(!licname) return false;
        var licid = licname.toLowerCase().replace(/[^a-z0-9]+/ig,"_");
        var tpl = '<tr id="tr_##licid##"> <td><input class="form-control" disabled="disabled" value="##licid##" type="text"></td> <td><input class="form-control" name="_wpdmpp_settings[licenses][##licid##][name]" value="##licname##" type="text"></td><td><textarea class="form-control" name="_wpdmpp_settings[licenses][##licid##][description]">##licname## License</textarea></td><td><input class="form-control" name="_wpdmpp_settings[licenses][##licid##][use]" value="9" type="number"></td> <td><button type="button" data-rowid="#row_##licid##" class="btn btn-danger del-lic"><i class="fa fa-trash-o"></i></button></td> </tr>';
        tpl = tpl.replace(/##licid##/ig, licid).replace(/##licname##/ig, licname);
        $('#licenses').append(tpl);

    });

    /**
     *  Settings >> Premium Package >> Basic Settings
     */

    $('body').on('click', '#allowed_cn', function () {
        $('.ccb').prop('checked', this.checked);
    });

    /**
     *  Settings >> Premium Package >> Tax
     */

    $('.taxstate,.taxcountry,.wpdmpp-currecy-dropdown').chosen({width:'200px'});

    $('.taxcountry').on('change', function(){
        var row_id = $(this).attr('rel');
        WpdmppPopulateStates(row_id);
    });

});


/* For Adding New Tax Rate */
function populateCountryStateAdmin(row_id) {
    var $ = jQuery;

    var dataurl = wpdmpp_base_url + 'assets/js/data/';

    var countries = [], states = [], countryOptions ="",  stateOptions ="", countrySelect = $('#r_'+ row_id +' .taxcountry'), stateSelect = $('#r_'+ row_id +' .taxstate');

    $.getJSON(dataurl+'countries.json', function(data){
        $.each(data, function(i, country){
            countries[""+country.code] = country.filename;
            countryOptions += "<option value='"+country.code+"'>"+country.name+"</option>";
        });
        countrySelect.html(countryOptions);
        countrySelect.chosen();
    });
    countrySelect.change(function() {
        var countryCode = $(this).val();
        loadStates(countryCode);
    });

    function loadStates(countryCode){
        console.log('populateCountryStateAdmin loadStates');
        var filename = countries[countryCode];
        if(filename != undefined) {
            $('#r_' + row_id + ' .taxstate-text').attr('disabled','disabled').hide();
            stateSelect.removeAttr('disabled').show();
            $.getJSON(dataurl + 'countries/' + filename + '.json', function (data) {
                stateOptions = "";
                stateOptions += "<option value='ALL-STATES'>All States</option>";
                $.each(data, function (i, state) {
                    states["" + state.code] = state;
                    var scode = state.code.replace(countryCode + "-", "");
                    stateOptions += "<option value='" + scode + "'>" + state.name + "</option>";
                });
                stateSelect.html(stateOptions).chosen().addClass('hidden').trigger("chosen:updated");
            });
        } else {
            stateSelect.attr('disabled','disabled').hide();
            $('#states_'+row_id+' .chosen-container').addClass('chosen-disabled');
            $('#r_' + row_id + ' .taxstate-text').removeAttr('disabled').show();
        }

    }
}

/* For Updating Old Tax Rate */
function WpdmppPopulateStates(row_id) {
    var $ = jQuery;

    var dataurl = wpdmpp_base_url + 'assets/js/data/';

    var countries = [], states = [], countryOptions ="",  stateOptions ="", countrySelect = $('#r_'+ row_id +' .taxcountry'), stateSelect = $('#r_'+ row_id +' .taxstate');

    $.getJSON(dataurl+'countries.json', function(data){
        $.each(data, function(i, country){
            countries[""+country.code] = country.filename;
        });

        var countryCode = countrySelect.val();
        loadStates(countryCode);
    });


    function loadStates(countryCode){
        console.log('populateStates loadStates');
        var filename = countries[""+countryCode];
        if(filename != undefined) {
            $('#r_' + row_id + ' .taxstate-text').attr('disabled','disabled').hide();
            stateSelect.removeAttr('disabled').show();
            $.getJSON(dataurl + 'countries/' + filename + '.json', function (data) {
                stateOptions = "";
                stateOptions += "<option value='ALL-STATES'>All States</option>";
                $.each(data, function (i, state) {
                    states["" + state.code] = state;
                    var scode = state.code.replace(countryCode + "-", "");
                    stateOptions += "<option value='" + scode + "'>" + state.name + "</option>";
                });

                stateSelect.html(stateOptions).addClass('hidden').trigger("chosen:updated");
            });
        } else {
            stateSelect.attr('disabled','disabled').hide();
            $('#cahngestates_'+row_id+' .chosen-container').addClass('chosen-disabled');
            $('#r_' + row_id + ' .taxstate-text').removeAttr('disabled').show();
        }

    }
}


function delete_renew_entry(id, nonce) {
    var $ = jQuery;
    //if(!confirm('Are you sure?')) return false;
    wpdm_boot_popup("Are You Sure?", "Deleting an order renew entry!",
        [{
            label: 'Yes',
            class: 'btn btn-danger',
            callback: function () {
                var popup = this;
                $(this).find('.modal-body').html('<i class="fa fa-refresh fa-spin"></i> Deleting...');
                $.post(ajaxurl, {action: 'delete_renew_entry', id: id, _dre: nonce}, function (res) {
                    $('#renew_row_'+id).hide();
                    popup.modal('hide');
                });
            }
        },
        {
            label: 'No',
            class: 'btn btn-default',
            callback: function () {
                this.modal('hide');
                return false;
            }
        }]
    );

    return false;
}

function getkey(file, order_id, btn_id){

    jQuery(btn_id).html("<i class='fas fa-sun fa-spin white'></i>");
    jQuery.post(ajaxurl,{execute:'getlicensekey',fileid:file,orderid:order_id},function(_res){
        var res;
        res = "<input class='form-control input-lg' style='cursor:copy;font-weight: bold;margin: 0' onfocus='this.select()' type=text readonly=readonly value='"+_res.key+"' />";

        jQuery(btn_id).html("<i class='fa fa-key white'></i>");

        if(_res.domains.length > 0) {
            res += "<div class='panel panel-default card card-default' id='lpp' style='margin-top: 15px;margin-bottom: 0;overflow: hidden'><div class='panel-heading card-header text-left' style='text-transform: unset;background: #f5f5f5 !important;' >Linked Sites</div><div style='max-height: 300px;overflow: auto;'><ul class='list-group text-left' style='margin-top: -1px;margin-bottom: 0'>";
            jQuery.each(_res.domains, function (i, domain) {
                res += "<li class='list-group-item lci'>" + domain + "</li>";
            });
            res += "</ul></div></div><style>#lpp .lci{ border-radius: 0 !important;;border: 0 !important;border-top: 1px solid #dddddd !important;; }</style>";
        }

        WPDM.bootAlert("License Key", res, 400);

    });
    return false;
}
