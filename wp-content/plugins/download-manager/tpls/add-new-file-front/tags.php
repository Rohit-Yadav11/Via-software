<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 15/12/19 13:26
 */
if(!defined("ABSPATH")) die();
?>

<div class="card wpdmap-card-filter mb-3"  id="tags-section" <?php if (in_array('tags', $hide)) { ?>style="display: none"<?php } ?>>
    <div class="card-header">
        <?=__('Tags', 'download-manager'); ?>
    </div>
    <div class="card-header p-2 filter-header"><input placeholder="<?php echo __( "Search...", "download-manager" ) ?>" type="search" class="form-control form-control-sm bg-white input-sm" id="tag_src" /></div>
    <div class="card-body tag-card">
        <ul  id="wpdm-tags">
            <?php
            $term_list = wp_get_post_terms($post->ID, 'wpdmtag', array("fields" => "all"));
            $selectedtags = array();
            foreach ($term_list as $__term) {
                $selectedtags[] = $__term->term_id;
            }
            $terms = get_terms(['taxonomy' => 'wpdmtag', 'hide_empty' => false]);
            foreach($terms as $term){
                echo "<li class='wpdm-tag'><label><input type='checkbox' name='wpdmtags[]' ".checked(in_array($term->term_id, $selectedtags), true, false)." class='wpdmtag' value='{$term->term_id}'> <span class='tagname'>{$term->name}</span></label></li>";
            }

            ?>
        </ul>
    </div>
</div>


<style>
    #wpdm-tags, #wpdm-tags li{
        list-style: none;
        margin: 0;
        padding: 0;
        font-size: 9pt;
    }
    #wpdm-tags li label{
        font-size: 9pt;
    }
</style>
<script>
    jQuery(function ($) {
        $("#tag_src").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#wpdm-tags li").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

    });
</script>
