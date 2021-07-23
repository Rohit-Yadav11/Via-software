<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 15/12/19 18:50
 * Updated: 24/05/20
 */
if (!defined("ABSPATH")) die();
?>
<div class="panel panel-default" id="categories-section"
     <?php if (isset($hide) && is_array($hide) && in_array('cats', $hide)) { ?>style="display: none"<?php } ?>>
    <div class="panel-heading"><b><?php _e("Categories", "download-manager"); ?></b></div>
    <div class="panel-header p-2"><input placeholder="<?php echo __( "Search...", "download-manager" ) ?>" type="search" class="form-control form-control-sm" id="cat_src" /></div>
    <div class="panel-body cat-panel">
        <?php

        $term_list = wp_get_post_terms($post->ID, 'wpdmcategory', array("fields" => "all"));
        $selected = array();
        foreach ($term_list as $__term) {
            $selected[] = $__term->term_id;
        }

        WPDM()->categories->checkboxTree('cats', $selected, $params);


        ?>
    </div>
</div>

<script>
    jQuery(function($){
        $("#cat_src").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#wpdmcat-tree li").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
