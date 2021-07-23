
<div class="wrap w3eden">

<div style="clear: both;"></div>
    <form method="post" id="wdm_settings_form">
        <?php
        wp_nonce_field(NONCE_KEY, '__wpdms_nonce');
        ?>
        <div class="panel panel-default" id="wpdm-wrapper-panel">
     <div class="panel-heading"><button type="submit" class="btn btn-primary pull-right"><i class="sinc far fa-hdd"></i> &nbsp<?php _e( "Save Settings" , "download-manager" ); ?></button><h3 class="h"><i class="fa fa-cog color-purple" id="wdms_loading"></i>&nbsp;&nbsp;<?php _e( "Download Manager Settings" , "download-manager" ); ?></h3>
     </div>
<div class="panel-body settings-panel-body">
<div class="container-fluid">
<div class="row"><div class="col-md-3">
     <ul id="tabs" class="nav nav-pills nav-stacked settings-tabs">
         <?php \WPDM\admin\menus\Settings::renderMenu($tab = wpdm_query_var('tab', ['validate' => 'txt', 'default' => 'basic'])); ?>
     </ul>
        </div><div class="col-md-9">
     <div class="tab-content">
<div class="alert alert-success" style="max-width: 300px !important;display: none;position: fixed; right: 15px;top: 80px;background: #ffffff !important;cursor: pointer" id="wpdm_message"></div>
<div id="wpdm_notify"  style="position: fixed; right: 15px;top: 95px;cursor: pointer"></div>

<input type="hidden" name="task" id="task" value="wdm_save_settings" />
<input type="hidden" name="action" id="action" value="wpdm_settings" />
         <input type="hidden" name="section" id="section" value="<?php echo $tab; ?>" />
<div id="fm_settings">
<?php
global $stabs;
if(isset($stabs[$tab], $stabs[$tab]['callback']))
    call_user_func($stabs[$tab]['callback']);
else
    echo "<div class='panel panel-danger'><div class='panel-body color-red'><i class='fa fa-exclamation-triangle'></i> ".__( "Something is wrong!", "download-manager" )."</div></div>";
?>
</div>

<div class="panel panel-default" style="border-top: 1px solid #dddddd !important;">
    <div class="panel-body text-right">
        <button type="submit" style="min-width:200px" class="btn btn-info btn-lg"><i class="sinc far fa-hdd"></i> &nbsp;<?php _e( "Save Settings" , "download-manager" ); ?></button>
    </div>
</div>

<br>

</div>
    </div>

</div>
</div>
</div>

 </div>

    </form>

<script type="text/javascript">
jQuery(function($){

    jQuery('body').on('click', '#wpdm_message.alert-success', function () {
       jQuery(this).fadeOut();
    });

    jQuery('select:not(.system-ui)').chosen({disable_search_threshold: 4});
    jQuery("ul#tabs li").click(function() {

    });
    jQuery('#wpdm_message').removeClass('hide').hide();
    jQuery("ul#tabs li a").click(function() {
        ///jQuert("ul#tabs li").removeClass('active')
        jQuery("ul#tabs li").removeClass("active");
        jQuery(this).parent('li').addClass('active');
        jQuery('#wdms_loading').addClass('wpdm-spin');
        jQuery(this).append('<span class="wpdm-loading wpdm-spin pull-right" id="wpdm-lsp"></span>')
        var section = this.id;
        jQuery.post(ajaxurl,{action:'wpdm_settings',section:this.id},function(res){
            jQuery('#fm_settings').html(res);
            jQuery('#section').val(section)
            jQuery('#wdms_loading').removeClass('wpdm-spin');
            jQuery('select:not(.system-ui)').chosen({disable_search_threshold: 4});
            window.history.pushState({"html":res,"pageTitle":"response.pageTitle"},"", "edit.php?post_type=wpdmpro&page=settings&tab="+section);
            jQuery('#wpdm-lsp').fadeOut(function(){
                jQuery(this).remove();
            });
        });
        return false;
    });

    window.onpopstate = function(e){
    if(e.state){
        jQuery("#fm_settings").html(e.state.html);
        //document.title = e.state.pageTitle;
    }
    };

    <?php /* if(isset($_GET['tab'])&&$_GET['tab']!=''){ ?>
        jQuery("ul#tabs li").removeClass("active");
        jQuery('#wdms_loading').addClass('wpdm-spin');
        jQuery('#<?php echo esc_attr($_GET['tab']); ?>').parents().addClass("active");
        var section = '<?php echo esc_attr($_GET['tab']);?>';
        jQuery.post(ajaxurl,{action:'wdm_settings',section:section},function(res){
            jQuery('#fm_settings').html(res);
            jQuery('#section').val(section)
            jQuery('#wdms_loading').removeClass('wpdm-spin');
        });
    <?php } */ ?>

    jQuery('#wdm_settings_form').submit(function(){

        jQuery('.sinc').removeClass('far fa-hdd').addClass('fas fa-sun fa-spin');

       jQuery(this).ajaxSubmit({
        url:ajaxurl,
        beforeSubmit: function(formData, jqForm, options){
          jQuery('.wpdm-ssb').addClass('wpdm-spin');
          jQuery('#wdms_loading').addClass('wpdm-spin');
        },
        success: function(responseText, statusText, xhr, $form){
            var section = $('input#section').val();
            WPDM.notify("<div style='margin-bottom: 5px;text-transform: uppercase'><strong>"+$('#'+section).html()+":</strong></div>"+responseText, 'info', '#wpdm_notify', 10000);
            jQuery('.wpdm-ssb').removeClass('wpdm-spin');
            jQuery('.sinc').removeClass('fas fa-sun fa-spin').addClass('far fa-hdd');
            jQuery('#wdms_loading').removeClass('wpdm-spin');
        }
       });

       return false;
    });

    jQuery('body').on("click",'.nav-tabs a', function (e) {
        e.preventDefault();
        jQuery(this).tab('show');
    });



});

</script>

<style>
    .w3eden .alert.alert-success::before{
        padding-top: 17px;
    }
</style>
