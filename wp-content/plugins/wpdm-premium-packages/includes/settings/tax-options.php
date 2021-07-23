<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

$us_states = array(
    'AL' => "Alabama",
    'AK' => "Alaska",
    'AZ' => "Arizona",
    'AR' => "Arkansas",
    'CA' => "California",
    'CO' => "Colorado",
    'CT' => "Connecticut",
    'DE' => "Delaware",
    'DC' => "District Of Columbia",
    'FL' => "Florida",
    'GA' => "Georgia",
    'HI' => "Hawaii",
    'ID' => "Idaho",
    'IL' => "Illinois",
    'IN' => "Indiana",
    'IA' => "Iowa",
    'KS' => "Kansas",
    'KY' => "Kentucky",
    'LA' => "Louisiana",
    'ME' => "Maine",
    'MD' => "Maryland",
    'MA' => "Massachusetts",
    'MI' => "Michigan",
    'MN' => "Minnesota",
    'MS' => "Mississippi",
    'MO' => "Missouri",
    'MT' => "Montana",
    'NE' => "Nebraska",
    'NV' => "Nevada",
    'NH' => "New Hampshire",
    'NJ' => "New Jersey",
    'NM' => "New Mexico",
    'NY' => "New York",
    'NC' => "North Carolina",
    'ND' => "North Dakota",
    'OH' => "Ohio",
    'OK' => "Oklahoma",
    'OR' => "Oregon",
    'PA' => "Pennsylvania",
    'RI' => "Rhode Island",
    'SC' => "South Carolina",
    'SD' => "South Dakota",
    'TN' => "Tennessee",
    'TX' => "Texas",
    'UT' => "Utah",
    'VT' => "Vermont",
    'VA' => "Virginia",
    'WA' => "Washington",
    'WV' => "West Virginia",
    'WI' => "Wisconsin",
    'WY' => "Wyoming");

$ca_states = array(
    "BC" => "British Columbia",
    "ON" => "Ontario",
    "NL" => "Newfoundland and Labrador",
    "NS" => "Nova Scotia",
    "PE" => "Prince Edward Island",
    "NB" => "New Brunswick",
    "QC" => "Quebec",
    "MB" => "Manitoba",
    "SK" => "Saskatchewan",
    "AB" => "Alberta",
    "NT" => "Northwest Territories",
    "NU" => "Nunavut",
    "YT" => "Yukon Territory");
?>
<div style="clear: both;margin-top:20px ;"></div>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo __("Tax Options", "wpdm-premium-packages"); ?></div>
    <div class="panel-body">
        <b><?php echo __("Tax Calculation", "wpdm-premium-packages"); ?></b>
        <div style="clear: both;margin-top:20px ;"></div>
        <label><input type="checkbox" value="1" <?php if (isset($settings['tax']['enable']) && $settings['tax']['enable'] == 1) echo "checked='checked'"; ?> id="tax_calculation" name="_wpdmpp_settings[tax][enable]"> <?php echo __("Enable tax calculation", "wpdm-premium-packages"); ?></label><br/>

        <div style="clear: both;margin-top:20px ;"></div>
                     
        <b><?php echo __("Tax Rates", "wpdm-premium-packages"); ?> </b>
        <div style="clear: both;margin-top:20px ;"></div>

        <table class="dtable table table-striped">
            <thead>
                <tr> 
                    <th><?php echo __("Country", "wpdm-premium-packages"); ?></th>
                    <th><?php echo __("State", "wpdm-premium-packages"); ?></th>
                    <th><?php echo __("Rate(%)", "wpdm-premium-packages"); ?></th>
                    <th><?php echo __("Action", "wpdm-premium-packages"); ?></th>
                </tr>
            </thead>   
            <tbody id="intr_rate">
        <?php

        $dataurl = WPDMPP_BASE_DIR.'assets/js/data/countries.json';
        $data = file_get_contents($dataurl);
        $data = json_decode($data);
        foreach($data as $index => $taxcountry){
            $taxcountries[$taxcountry->code] = $taxcountry;
        }

        if (isset($settings['tax']['tax_rate'])) {

            foreach ($settings['tax']['tax_rate'] as $key => $rate) {
                ?>
                <tr id="r_<?php echo $key; ?>">
                    <td>
                        <select class="taxcountry" rel="<?php echo $key; ?>" name="_wpdmpp_settings[tax][tax_rate][<?php echo $key; ?>][country]">
                            <?php
                            foreach ($taxcountries as $country) {
                                ?>
                                <option <?php if ($settings['tax']['tax_rate'][$key]['country'] == $country->code) echo 'selected=selected' ?> value="<?php echo $country->code; ?>"><?php echo ucwords($country->name); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td id="cahngestates_<?php echo $key; ?>">
                        <?php
                        $country_code = $settings['tax']['tax_rate'][$key]['country'];
                        $has_states = TRUE;

                        if( isset( $taxcountries[$country_code]->filename ) )
                        {
                            $states = array();
                            $dataurl = WPDMPP_BASE_DIR.'assets/js/data/countries/'.$taxcountries[$country_code]->filename.'.json';
                            $states_obg = json_decode(file_get_contents($dataurl));

                            foreach($states_obg as $index => $state){
                                $state->code = str_replace($country_code.'-','',$state->code);
                                $states[$state->code] = $state->name;
                            }
                        }
                        else{ $has_states = FALSE; } ?>

                        <select class="taxstate" name="_wpdmpp_settings[tax][tax_rate][<?php echo $key; ?>][state]" <?php echo $has_states ? '' : 'disabled="disabled" style="display:none"'; ?> >
                            <option <?php if ($settings['tax']['tax_rate'][$key]['state'] == 'ALL-STATES') echo 'selected=selected' ?> value="ALL-STATES"><?php _e('All States','wpdm-premium-packages'); ?></option>
                            <?php foreach ($states as $s_code => $s_name) { ?>
                                <option <?php if ($settings['tax']['tax_rate'][$key]['state'] == $s_code) echo 'selected=selected' ?> value="<?php echo $s_code; ?>"><?php echo ucwords($s_name); ?></option>
                            <?php } ?>
                        </select>
                        <?php

                        if($has_states)
                            echo "<input style='width:200px;display:none;' disabled class='form-control input-sm taxstate-text' type='text' name='_wpdmpp_settings[tax][tax_rate][$key][state]' value='' />";
                        else
                            echo "<input style='width:200px;' class='form-control input-sm taxstate-text' type='text' name='_wpdmpp_settings[tax][tax_rate][$key][state]' value=".$settings['tax']['tax_rate'][$key]['state']." />";

                       ?>
                    </td>
                    <td><input size="4" class='form-control input-sm' type="text" name="_wpdmpp_settings[tax][tax_rate][<?php echo $key; ?>][rate]" value="<?php echo $rate['rate']; ?>"></td>
                    <td><a href="#" class="del_rate" rel="<?php echo $key; ?>"><i class="fa fa-trash"></i></a></td>
                </tr>
                <?php
            }
        }
        ?>
            </tbody>
         </table> 
        
        <div style="clear: both;margin-top:20px ;"></div>
        <input class="btn btn-default" type="button" id="add_tax_rate" value="<?php _e('Add Tax Rate','wpdm-premium-packages'); ?>">
     </div>
</div>

<style>
    #intr_rate .chosen-disabled{display: none;}
    .del_rate{line-height: 2;padding: 8px;}
    .del_rate i{color: #ff1d1b;}
</style>
<script>
jQuery(function() {
                            
      jQuery('body').on("click",'.del_rate',function(){
          if(confirm("<?php _e('Are you sure to delete?', 'wpdm-premium-packages'); ?>")){
              jQuery('#r_'+jQuery(this).attr("rel")).remove();
          }
          return false;
      });

    //New
    jQuery('#add_tax_rate').click(function() {
        var tmy = new Date().getTime();
        var row_id = "r_" + tmy;

        jQuery('#intr_rate').append('<tr id="r_'+tmy+'">' +
            '<td>' +
            '<select style="max-width:200px;width:200px;" class="form-control taxcountry" rel="'+tmy+'" name="_wpdmpp_settings[tax][tax_rate]['+tmy+'][country]"></select>' +
            '</td>' +
            '<td id="states_'+tmy+'">' +
            '<select style="max-width:200px;width:200px;" class="form-control taxstate" name="_wpdmpp_settings[tax][tax_rate]['+tmy+'][state]"></select>'+
            '<input style="display:none; max-width:200px;width:200px;" class="form-control input-sm taxstate-text" type="text" name="_wpdmpp_settings[tax][tax_rate]['+tmy+'][state]" value="">'+
            '</td>'+
            '<td><input type="text" size="4" class="form-control input-sm" name="_wpdmpp_settings[tax][tax_rate]['+tmy+'][rate]" value=""></td>' +
            '<td><a href="#" class="del_rate" rel="'+tmy+'"><i class="fa fa-trash"></i></a></td>' +
            '</tr>');

            populateCountryStateAdmin(tmy);
    });
});
</script> 

    